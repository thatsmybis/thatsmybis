@extends('layouts.app')
@section('title', 'Audit Log - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-wight-medium">
                        <span class="fas fa-fw fa-clipboard-list-check text-gold"></span>
                        Audit Log
                        @if ($resourceName)
                            for {{ $resourceName }}
                        @endif
                    </h1>
                    <ul>
                        <li class="small no-bullet font-italic">
                            Whodunit?
                        </li>
                        @if (!$showPrios)
                            <li class="small text-danger">
                                Prios are hidden by your guild master(s)
                            </li>
                        @elseif ($guild->is_prio_private)
                            <li class="small text-warning">
                                Prios are hidden from raiders
                            </li>
                        @endif
                        @if (!$showWishlist)
                            <li class="small text-danger">
                                Wishlists are hidden by your guild master(s)
                            </li>
                        @elseif ($guild->is_wishlist_private)
                            <li class="small text-warning">
                                Wishlists are hidden from raiders
                            </li>
                        @endif
                    </ul>
                </div>

                @if ($resource)
                    <div class="col-12 pb-3">
                        <div class="bg-light rounded pt-1 pb-1 pl-3 pr-3">
                            @if($resource instanceof \App\Character)
                                @include('character/partials/header', ['character' => $resource, 'headerSize' => 1, 'showEdit' => false, 'showIcon' => false])
                            @elseif($resource instanceof \App\Item)
                                @include('partials/item', ['item' => $resource, 'wowheadLink' => false])
                            @elseif($resource instanceof \App\Member)
                                @include('member/partials/header', ['member' => $resource, 'discordUsername' => $resource->user->discord_username, 'headerSize' => 1, 'showEdit' => false, 'titlePrefix' => null])
                            @elseif($resource instanceof \App\Raid)
                                <span class="font-weight-bold">
                                    @include('partials/raid', ['raid' => $resource, 'raidColor' => $resource->getColor()])
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="col-12">
                    <ol class="no-bullet no-indent striped">
                        @foreach ($logs as $log)
                            <li class="p-1 pl-3 rounded">
                                <div class="row">
                                    <div class="col-md-2 col-12 text-muted small">
                                        @if ($log->member_id)
                                            <!-- <a href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $log->member_id, 'usernameSlug' => $log->member_slug]) }}" class="text-muted"> -->
                                            <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'member_id' => $log->member_id]) }}" class="text-muted">
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
                                                    @include('partials/item', ['wowheadLink' => false, 'auditLink' => true, 'itemId' => $log->item_id, 'itemName' => $log->item_name, 'fontWeight' => 'light'])
                                                </li>
                                            @endif
                                            @if ($log->other_member_id)
                                                <li class="list-inline-item">
                                                    <!-- <a href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $log->member_id, 'usernameSlug' => $log->other_member_slug]) }}" class="text-muted"> -->
                                                    <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'member_id' => $log->other_member_id]) }}" class="text-muted">
                                                        {{ $log->other_member_username }}
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($log->character_id)
                                                <li class="list-inline-item">
                                                    <!-- <a href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $log->character_id, 'nameSlug' => $log->character_slug]) }}" class="text-muted"> -->
                                                    <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'character_id' => $log->character_id]) }}" class="text-muted">
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
                                                    <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_id' => $log->raid_id]) }}" class="text-muted">
                                                        {{ $log->raid_name }}
                                                    </a>
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

                <div class="col-12 mt-3">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
