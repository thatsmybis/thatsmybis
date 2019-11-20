<ul class="no-bullet text-center">
    @foreach($content as $content)
        <li class="font-weight-bold text-lead lead">
            <a href="{{ route('showContent', $content->slug) }}" class="text-{{ $content->slug }}">{{ $content->title }}</a>
        </li>
    @endforeach
</ul>
