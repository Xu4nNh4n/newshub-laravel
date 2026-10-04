<?php

namespace App\Services;

use DOMComment;
use DOMDocument;
use DOMElement;
use DOMNode;

final class PostContentSanitizer
{
    /** @var array<string, list<string>> */
    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
        'iframe' => ['src', 'title', 'width', 'height', 'allow', 'allowfullscreen', 'frameborder'],
    ];

    /** @var list<string> */
    private const ALLOWED_ELEMENTS = [
        'p', 'br', 'h2', 'h3', 'strong', 'em', 'u', 'blockquote',
        'ul', 'ol', 'li', 'a', 'figure', 'img', 'figcaption', 'iframe', 'div',
    ];

    /** Remove active content while retaining the editorial markup supported by the editor. */
    public function sanitize(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previousErrorHandling = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><!DOCTYPE html><html><body><div id="post-content-root">'.$html.'</div></body></html>',
            LIBXML_NOERROR | LIBXML_NOWARNING,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorHandling);

        $root = $document->getElementById('post-content-root');

        if (! $root instanceof DOMElement) {
            return '';
        }

        $this->sanitizeChildren($root);

        $sanitized = '';

        foreach ($root->childNodes as $child) {
            $sanitized .= $document->saveHTML($child) ?: '';
        }

        return trim($sanitized);
    }

    private function sanitizeChildren(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $child) {
            if ($child instanceof DOMComment) {
                $parent->removeChild($child);

                continue;
            }

            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (! in_array($tag, self::ALLOWED_ELEMENTS, true)) {
                $parent->removeChild($child);

                continue;
            }

            $this->sanitizeAttributes($child, $tag);

            if ($tag === 'iframe' && ! $this->isAllowedYouTubeUrl($child->getAttribute('src'))) {
                $parent->removeChild($child);

                continue;
            }

            $this->sanitizeChildren($child);
        }
    }

    private function sanitizeAttributes(DOMElement $element, string $tag): void
    {
        $allowedAttributes = self::ALLOWED_ATTRIBUTES[$tag] ?? [];
        $attributeNames = [];

        foreach ($element->attributes as $attribute) {
            $attributeNames[] = $attribute->name;
        }

        foreach ($attributeNames as $attributeName) {
            if (! in_array(strtolower($attributeName), $allowedAttributes, true)) {
                $element->removeAttribute($attributeName);
            }
        }

        if ($element->hasAttribute('href') && ! $this->isAllowedUrl($element->getAttribute('href'), true)) {
            $element->removeAttribute('href');
        }

        if ($element->hasAttribute('src') && $tag !== 'iframe' && ! $this->isAllowedUrl($element->getAttribute('src'))) {
            $element->removeAttribute('src');
        }

        if ($element->hasAttribute('target')) {
            $target = strtolower($element->getAttribute('target'));

            if (! in_array($target, ['_blank', '_self'], true)) {
                $element->removeAttribute('target');
            }
        }

        if ($tag === 'a' && $element->getAttribute('target') === '_blank') {
            $element->setAttribute('rel', 'noopener noreferrer');
        }

        if ($tag === 'iframe') {
            $element->setAttribute('loading', 'lazy');
            $element->setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
        }
    }

    private function isAllowedUrl(string $url, bool $allowAnchor = false): bool
    {
        $normalizedUrl = html_entity_decode(trim($url), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $normalizedUrl = preg_replace('/[\x00-\x20\x7F]+/u', '', $normalizedUrl) ?? '';

        if ($normalizedUrl === '') {
            return false;
        }

        if ($allowAnchor && str_starts_with($normalizedUrl, '#')) {
            return true;
        }

        if (str_starts_with($normalizedUrl, '/') && ! str_starts_with($normalizedUrl, '//')) {
            return true;
        }

        $scheme = strtolower((string) parse_url($normalizedUrl, PHP_URL_SCHEME));

        return in_array($scheme, $allowAnchor ? ['http', 'https', 'mailto'] : ['http', 'https'], true);
    }

    private function isAllowedYouTubeUrl(string $url): bool
    {
        if (! $this->isAllowedUrl($url)) {
            return false;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return $host === 'youtu.be'
            || $host === 'youtube.com'
            || str_ends_with($host, '.youtube.com')
            || $host === 'youtube-nocookie.com'
            || str_ends_with($host, '.youtube-nocookie.com');
    }
}
