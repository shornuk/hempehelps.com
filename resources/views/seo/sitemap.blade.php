{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach ($products as $product)
    <url>
        <loc>{{ url('/products/'.$product->handle) }}</loc>
        @foreach ($markets as $market)
            <xhtml:link rel="alternate" hreflang="{{ $market->locale }}" href="{{ url(($market->path_prefix ? '/'.$market->path_prefix : '').'/products/'.$product->handle) }}" />
        @endforeach
    </url>
@endforeach
@foreach ($pages as $page)
    <url>
        <loc>{{ url('/pages/'.$page->slug) }}</loc>
    </url>
@endforeach
@foreach ($articles as $article)
    <url>
        <loc>{{ url('/guides/'.$article->slug) }}</loc>
    </url>
@endforeach
    <url><loc>{{ url('/shop') }}</loc></url>
    <url><loc>{{ url('/faq') }}</loc></url>
</urlset>
