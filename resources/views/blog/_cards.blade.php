@foreach ($posts as $post)
    @include('blog._card', ['post' => $post])
@endforeach

{{-- Carries the next page URL back to the front-end loader --}}
<div class="scroll-sentinel-data" data-next-page="{{ $posts->nextPageUrl() }}" hidden></div>
