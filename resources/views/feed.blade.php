@php echo '<'.'?xml version="1.0" encoding="UTF-8"?>'; @endphp
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <channel>
        <title>{{ config('app.name') }}{{ $category === null ? ' - Báo điện tử' : ' - '.$category->name }}</title>
        <link>{{ $category === null ? route('home') : route('news.index', ['category' => $category->slug]) }}</link>
        <description>{{ $category === null ? 'Cập nhật tin tức thời sự, công nghệ, kinh doanh 24/7' : 'Tin mới nhất trong chuyên mục '.$category->name }}</description>
        <language>vi</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <atom:link href="{{ $category === null ? route('feed.index') : route('feed.category', $category) }}" rel="self" type="application/rss+xml" />
        @foreach ($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ route('news.show', $post->slug) }}</link>
                <description>{{ $post->summary }}</description>
                <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
                <guid isPermaLink="true">{{ route('news.show', $post->slug) }}</guid>
                <dc:creator>{{ $post->author->name }}</dc:creator>
                <category>{{ $post->category->name }}</category>
            </item>
        @endforeach
    </channel>
</rss>
