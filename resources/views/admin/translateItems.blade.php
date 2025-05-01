@extends('layouts.app')
@section('title', 'Translate Item Names - ' . config('app.name'))

@section('wowheadShowIcons', 'false')

@section('content')

    <p>
        This page was made for development purposes. It requires fetching each language, one at a time, and then updating the `items` table to use the translated names. For future use: Consider further developing this page to load multiple languages at once, and possibly in a format that makes it easier to put into a SQL `UPDATE` statement.
    </p>
    <p>
        I was loading ~4000 items at a time.
    </p>
    <ul>
        <li>
            <a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => $lang, 'minId' => $minId, 'maxId' => $maxId]) }}">get link for editing</a>
            <span class="small text-muted">(click this, then edit URL to your needs)</span>
        </li>
        <li>
            <ul class="list-inline">
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'cn', 'minId' => $minId, 'maxId' => $maxId]) }}">cn</a></li>
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'de', 'minId' => $minId, 'maxId' => $maxId]) }}">de</a></li>
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'es', 'minId' => $minId, 'maxId' => $maxId]) }}">es</a></li>
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'fr', 'minId' => $minId, 'maxId' => $maxId]) }}">fr</a></li>
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'ko', 'minId' => $minId, 'maxId' => $maxId]) }}">ko</a></li>
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'pt', 'minId' => $minId, 'maxId' => $maxId]) }}">pt</a></li>
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'ru', 'minId' => $minId, 'maxId' => $maxId]) }}">ru</a></li>
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'it', 'minId' => $minId, 'maxId' => $maxId]) }}">it</a> <span class="small text-muted">(missing translations)</span></li>
            </ul>
        </li>
        <li>
            <ul class="list-inline">
                @php
                    $diff = $maxId - $minId;
                @endphp
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'cn', 'minId' => $minId + $diff + 1, 'maxId' => $maxId + $diff + 1]) }}">cn {{ $minId + $diff + 1 }}-{{ $maxId + $diff + 1 }}</a></li>
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'de', 'minId' => $minId + $diff + 1, 'maxId' => $maxId + $diff + 1]) }}">de {{ $minId + $diff + 1 }}-{{ $maxId + $diff + 1 }}</a></li>
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'es', 'minId' => $minId + $diff + 1, 'maxId' => $maxId + $diff + 1]) }}">es {{ $minId + $diff + 1 }}-{{ $maxId + $diff + 1 }}</a></li>
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'fr', 'minId' => $minId + $diff + 1, 'maxId' => $maxId + $diff + 1]) }}">fr {{ $minId + $diff + 1 }}-{{ $maxId + $diff + 1 }}</a></li>
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'ko', 'minId' => $minId + $diff + 1, 'maxId' => $maxId + $diff + 1]) }}">ko {{ $minId + $diff + 1 }}-{{ $maxId + $diff + 1 }}</a></li>
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'pt', 'minId' => $minId + $diff + 1, 'maxId' => $maxId + $diff + 1]) }}">pt {{ $minId + $diff + 1 }}-{{ $maxId + $diff + 1 }}</a></li>
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'ru', 'minId' => $minId + $diff + 1, 'maxId' => $maxId + $diff + 1]) }}">ru {{ $minId + $diff + 1 }}-{{ $maxId + $diff + 1 }}</a></li>
                <li class="list-inline-item"><a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => 'it', 'minId' => $minId + $diff + 1, 'maxId' => $maxId + $diff + 1]) }}">it {{ $minId + $diff + 1 }}-{{ $maxId + $diff + 1 }}</a> <span class="small text-muted">(missing translations)</span></li>
            </ul>
        </li>
        <li>
            expansion: <strong>{{ $expansion }}</strong>
            <span class="small text-muted">
                (<a href="{{ route('admin.translateItems', ['expansion' => 'classic', 'lang' => $lang, 'minId' => $minId, 'maxId' => $maxId]) }}">classic</a>,
                <a href="{{ route('admin.translateItems', ['expansion' => 'tbc', 'lang' => $lang, 'minId' => $minId, 'maxId' => $maxId]) }}">tbc</a>,
                <a href="{{ route('admin.translateItems', ['expansion' => 'wotlk', 'lang' => $lang, 'minId' => $minId, 'maxId' => $maxId]) }}">wotlk</a>,
                <a href="{{ route('admin.translateItems', ['expansion' => 'cata', 'lang' => $lang, 'minId' => $minId, 'maxId' => $maxId]) }}">cata</a>,
                <a href="{{ route('admin.translateItems', ['expansion' => 'mop', 'lang' => $lang, 'minId' => $minId, 'maxId' => $maxId]) }}">mop</a>)
            </span>
        </li>
        <li>
            lang: <strong>{{ $lang }}</strong>
            <span class="small text-muted">(cn, de, es, fr, it, ko, pt, ru)</span>
        </li>
        <li>
            minId: <strong>{{ $minId }}</strong>
            <span class="small text-muted">(first item to show)</span>
        </li>
        <li>
            maxId: <strong>{{ $maxId }}</strong>
            <span class="small text-muted">(last item to show)</span>
        </li>
    </ul>
    <div class="btn btn-success ml-3 mb-3" onclick="copyItemsToClipboard()">
        <span class="fas fa-fw fa-clipboard-list-check"></span>
        Copy Items
    </div>

{{-- Not indented so that spaces won't exist while copying --}}
<div id="items" class="ml-3 no-bullet no-indent">@foreach ($items as $item)
{!! !$loop->first ? "<br>" : "" !!}{{ $item->item_id }} @include('partials/item', ['itemExpansionId' => $expansionId, 'wowheadLocale' => $lang, 'wowheadLink' => true])
@endforeach
</div>

@endsection

@section('scripts')
<script>
function copyItemsToClipboard() {
    var range = document.createRange();
    range.selectNode(document.getElementById("items"));
    window.getSelection().removeAllRanges(); // clear current selection
    window.getSelection().addRange(range); // to select text
    document.execCommand("copy");
    window.getSelection().removeAllRanges();// to deselect
}
</script>
@endsection
