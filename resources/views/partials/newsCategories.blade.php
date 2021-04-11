<li class="list-inline-item {{ isset($category) && $category && $category == 'my-feed' ? 'font-weight-bold' : '' }}">
    <a class="text-muted" href="{{ route('guild.news', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">#my-feed</a>
</li>
<li class="list-inline-item">&sdot;</li>
<li class="list-inline-item {{ isset($category) && $category && $category == 'news' ? 'font-weight-bold' : '' }}">
    <a class="text-muted" href="{{ route('guild.news', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'category' => 'news']) }}">#news</a>
</li>
@foreach ($raidGroups as $raidGroup)
    <li class="list-inline-item">&sdot;</li>
    <li class="list-inline-item {{ isset($category) && $category && $category == $raidGroup->slug ? 'font-weight-bold' : '' }}">
        <a class="text-muted" href="{{ route('guild.news', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'category' => $raidGroup->slug]) }}">#{{ $raidGroup->slug }}</a>
    </li>
@endforeach
