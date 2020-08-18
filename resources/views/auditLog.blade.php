@extends('layouts.app')
@section('title', 'Audit Log - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-wight-medium">
                        <span class="fas fa-fw fa-scroll-old text-gold"></span>
                        Audit Log
                    </h1>
                    <p>
                        Whodunit?
                    </p>
                </div>

                <div class="col-12">
                    <ol class="no-bullet no-indent striped">
                        @foreach ($logs as $log)
                            <li class="p-1 pl-3 rounded">
                                <div class="row">
                                    <div class="col-md-2 col-12 text-muted small">
                                        @if ($log->member_id)
                                            <a href="{{ route('member.show', ['guildSlug' => $guild->slug, 'usernameSlug' => $log->member_slug]) }}"
                                                class="text-muted">
                                                <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $log->created_at }}"></span> ago
                                            </a>
                                        @else
                                            <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $log->created_at }}"></span> ago
                                        @endif
                                    </div>

                                    <div class="col-md-10 col-12">
                                        <ul class="list-inline">
                                            <li class="list-inline-item">
                                                {{ $log->description }}
                                            </li>

                                            @if ($log->item_id)
                                                <li class="list-inline-item">
                                                    @include('partials/item', ['wowheadLink' => false, 'itemId' => $log->item_id, 'itemName' => $log->item_name, 'fontWeight' => 'light'])
                                                </li>
                                            @endif
                                            @if ($log->other_member_id)
                                                <li class="list-inline-item">
                                                    <a href="{{ route('member.show', ['guildSlug' => $guild->slug, 'usernameSlug' => $log->other_member_slug]) }}" class="text-muted">
                                                        {{ $log->other_member_username }}
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($log->character_id)
                                                <li class="list-inline-item">
                                                    <a href="{{ route('character.show', ['guildSlug' => $guild->slug, 'nameSlug' => $log->character_slug]) }}" class="text-muted">
                                                        {{ $log->character_name }}
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($log->instance_id)
                                                <li class="list-inline-item text-muted">
                                                    {{ $log->instance_name }}
                                                </li>
                                            @endif
                                            @if ($log->item_source_id)
                                                <li class="list-inline-item text-muted">
                                                    {{ $log->item_source_name }}
                                                </li>
                                            @endif
                                            @if ($log->raid_id)
                                                <li class="list-inline-item text-muted">
                                                    {{ $log->raid_name }}
                                                </li>
                                            @endif
                                            @if ($log->role_id)
                                                <li class="list-inline-item text-muted">
                                                    {{ $log->role_name }}
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </div>

                <div class="col-xs-12 mt-3">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
