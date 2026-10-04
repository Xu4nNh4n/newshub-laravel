@php echo '<'.'?xml version="1.0" encoding="UTF-8"?>'; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ route('home') }}</loc></url>
    <url><loc>{{ route('news.index') }}</loc></url>
    @foreach ($posts as $post)
        <url>
            <loc>{{ route('news.show', $post->slug) }}</loc>
            <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
        </url>
    @endforeach
    @foreach ($categories as $category)
        <url>
            <loc>{{ route('news.index', ['category' => $category->slug]) }}</loc>
            <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
        </url>
    @endforeach
    @foreach ($tags as $tag)
        <url>
            <loc>{{ route('news.index', ['tag' => $tag->slug]) }}</loc>
            <lastmod>{{ $tag->updated_at->toAtomString() }}</lastmod>
        </url>
    @endforeach
</urlset>
