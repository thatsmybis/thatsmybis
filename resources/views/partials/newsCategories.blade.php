<li class="list-inline-item {{ isset($category) && $category && $category == 'my-feed' ? 'font-weight-bold' : '' }}">
    <a class="text-muted" href="{{ route('news') }}">#my-feed</a>
</li>
<li class="list-inline-item">&sdot;</li>
<li class="list-inline-item {{ isset($category) && $category && $category == 'news' ? 'font-weight-bold' : '' }}">
    <a class="text-muted" href="{{ route('news', ['category' => 'news']) }}">#news</a>
</li>
@foreach ($raids as $raid)
    <li class="list-inline-item">&sdot;</li>
    <li class="list-inline-item {{ isset($category) && $category && $category == $raid->slug ? 'font-weight-bold' : '' }}">
        <a class="text-muted" href="{{ route('news', ['category' => $raid->slug]) }}">#{{ $raid->slug }}</a>
    </li>
@endforeach
