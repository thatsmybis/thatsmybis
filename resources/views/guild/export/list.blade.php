@extends('layouts.app')
@section('title', __("Export Data") . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fas fa-fw fa-database text-muted"></span>
                        {{ __("Choose Data to Export") }}
                    </h1>
                </div>
                <div class="col-12 pt-3 pb-1 mb-2 bg-light rounded">
                    <ol class="no-bullet no-indent striped">
                        <!-- TMB Tooltips Addon -->
                        <li class="p-3 mb-3 rounded">
                            <h2 class="text-uncommon font-weight-bold">
                                <span class="fab fa-fw fa-battle-net text-mage"></span>
                                <a href="{{ route('guild.export.addonItems', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'html']) }}" target="_blank" class="text-uncommon">
                                    {{ __("TMB Tooltips Addon") }}
                                </a>
                            </h2>
                            <p>
                                {{ __("Copy+paste this into the") }} <a href="https://www.curseforge.com/wow/addons/tmb-helper" target="_blank">{{ __("TMB Tooltips Addon") }}</a>
                            </p>
                            <!--
                            <p>
                                Fields exported:
                            </p>
                            <div class="bg-dark rounded p-2">
                                <code>
                                    {{ collect(App\Http\Controllers\ExportController::LOOT_HEADERS)->implode(', ') }}
                                </code>
                            </div>
                            -->
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.addonItems', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'csv']) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        {{ __("Download CSV") }}
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.addonItems', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'html']) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-uncommon"></span>
                                        <span class="text-uncommon font-weight-bold">View CSV</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Gargul -->
                        <li class="p-3 mb-3 rounded">
                            <h2>
                                <span class="fas fa-fw text-legendary"><img src="{{ asset('images/gargul.png') }}" style="width: 40px; height: 40px;"></img></span>
                                <a href="https://www.curseforge.com/wow/addons/gargul" target="_blank" class="text-epic">
                                    Gargul
                                </a>
                            </h2>
                            <p>
                                {!! __("Import wishlist and loot priority data into the Gargul addon. Select all ( ctrl+a ), copy ( ctrl+c ) and then paste ( ctrl+v ) in the import window (/gl wl). For more info check :curseforge_url on Curseforge. Happy lootin'!", ['curseforge_url' => "<a href='https://www.curseforge.com/wow/addons/gargul' target='_blank'>Gargul</a>"]) !!}
                            </p>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.gargul.wishlist', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-code text-muted"></span>
                                        {{ __("Wishlists") }} ( /gl wl )
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.gargul.priority', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        {{ __("Loot Priority") }} ( /gl lo )
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="p-3 mb-3 rounded">
                            <p class="text-4">
                                {{ __("Exports are") }} <span class="font-weight-bold">{{ __("CACHED") }}</span> for {{ env('EXPORT_CACHE_SECONDS', 120) / 60 }} minute{{ env('EXPORT_CACHE_SECONDS', 120) / 60 == 1 ? '' : 's' }}.
                                <abbr title="This applies across your entire guild. If you're the one running the export for the first time, expect fresh data. If your guildmate just ran an export, you will have to wait {{ env('EXPORT_CACHE_SECONDS', 120) / 60 }} minute{{ env('EXPORT_CACHE_SECONDS', 120) / 60 > 1 ? 's' : '' }} for the data to update. Officers may get a different cache than members depending on wishlist/prio visibility.">?</abbr>
                            </p>
                            <p>
                                Publicly sharable copy of the generic loot tables <a href="{{ route('loot') }}" target="_blank">here</a>. The format of the data being exported is subject to change. If you need access to data that isn't here, please reach out on
                                <a href="{{ env('APP_DISCORD') }}" target="_blank" alt="Join the {{ env('APP_NAME') }} Discord Server" title="Join the {{ env('APP_NAME') }} Discord Server" class="">Discord</a>.
                            </p>
                        </li>
                        <!-- Loot Received -->
                        <li class="p-3 mb-3 rounded">
                            <h2>
                                <span class="fas fa-fw fa-treasure-chest text-gold"></span>
                                {{ __("Loot Received, Wishlists, Prios, and Notes") }}
                            </h2>
                            <p>
                                {{ __("All of the loot in your guild, plus all of your guild's notes and tiers for items.") }}
                            </p>
                            <p>
                                {{ __("Fields exported:") }}
                            </p>
                            <div class="bg-dark rounded p-2">
                                <code>
                                    {{ collect(App\Http\Controllers\ExportController::LOOT_HEADERS)->implode(', ') }}
                                </code>
                            </div>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'csv', 'lootType' => 'all']) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        {{ __("Download CSV") }}
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'html', 'lootType' => 'all']) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        {{ __("View CSV") }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Wishlist -->
                        <li class="p-3 mb-3 rounded">
                            <h2>
                                <span class="fas fa-fw fa-scroll-old text-legendary"></span>
                                {{ __("Just Wishlists") }}
                            </h2>
                            <p>
                                {{ __("The wishlisted items in your guild.") }}
                            </p>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'csv', 'lootType' => App\Item::TYPE_WISHLIST]) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        {{ __("Download CSV") }}
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'html', 'lootType' => App\Item::TYPE_WISHLIST]) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        {{ __("View CSV") }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Prios -->
                        <li class="p-3 mb-3 rounded">
                            <h2>
                                <span class="fas fa-fw fa-sort-amount-down text-gold"></span>
                                {{ __("Just Prios") }}
                            </h2>
                            <p>
                                {{ __("The item prios in your guild.") }}
                            </p>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'csv', 'lootType' => App\Item::TYPE_PRIO]) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        {{ __("Download CSV") }}
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'html', 'lootType' => App\Item::TYPE_PRIO]) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        {{ __("View CSV") }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Loot Received -->
                        <li class="p-3 mb-3 rounded">
                            <h2>
                                <span class="fas fa-fw fa-sack text-success"></span>
                                {{ __("Just Loot Received") }}
                            </h2>
                            <p>
                                {{ __("The loot received in your guild.") }}
                            </p>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'csv', 'lootType' => App\Item::TYPE_RECEIVED]) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        {{ __("Download CSV") }}
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'html', 'lootType' => App\Item::TYPE_RECEIVED]) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        {{ __("View CSV") }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Item Notes -->
                        <li class="p-3 mb-3 rounded">
                            <h2>
                                <span class="fas fa-fw fa-sword text-muted"></span>
                                {{ __("Guild Item Notes") }}
                            </h2>
                            <p>
                                <strong>Guild notes</strong> and <strong>prio notes</strong> are included.
                            </p>
                            <p>
                                {{ __("Fields exported:") }}
                            </p>
                            <div class="bg-dark rounded p-2">
                                <code>
                                    {{ collect(App\Http\Controllers\ExportController::ITEM_NOTE_HEADERS)->implode(', ') }}
                                </code>
                            </div>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.itemNotes', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'csv']) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        {{ __("Download CSV") }}
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.itemNotes', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'html']) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        {{ __("View CSV") }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Item Notes -->
                        <li class="p-3 mb-3 rounded">
                            <h2>
                                <span class="fas fa-fw fa-helmet-battle text-dk"></span>
                                {{ __("Guild Raid Groups") }}
                            </h2>
                            <p>
                                {{ __("Fields exported:") }}
                            </p>
                            <div class="bg-dark rounded p-2">
                                <code>
                                    {{ collect(App\Http\Controllers\ExportController::RAID_GROUPS_HEADERS)->implode(', ') }}
                                </code>
                            </div>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'csv']) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        {{ __("Download CSV") }}
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'html']) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        {{ __("View CSV") }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Characters with everything -->
                        <li class="p-3 mb-3 rounded">
                            <h2>
                                <span class="fas fa-fw fa-users-class text-success"></span>
                                {{ __("Giant JSON blob") }}
                            </h2>
                            <p>
                                All of your guild's characters with their <strong>loot received</strong>, <strong>wishlist</strong>, <strong>prios</strong>, notes, etc. It's all of the data used to populate the Roster page.
                            </p>
                            <p>
                                {{ __("Format looks something like this: (provided this documentation is still up to date)") }}
                            </p>
                            <div class="bg-dark rounded p-2 code-box">
<code class="pre">[
  {
    "id": 20,
    "member_id": 1,
    "guild_id": 1,
    "name": "Ahiram",
    "slug": "ahiram",
    "level": 60,
    "race": "Orc",
    "class": "Warrior",
    "spec": "Fury",
    "profession_1": null,
    "profession_2": null,
    "rank": null,
    "rank_goal": null,
    "raid_group_id": 7,
    "is_alt": 0,
    "public_note": "test1",
    "inactive_at": null,
    "username": "Lemmings",
    "is_wishlist_unlocked": 0,
    "is_received_unlocked": 0,
    "raid_group_name": "Myth Raid",
    "raid_group_color": "10181046",
    "received": [
      {
        "item_id": 23060,
        "name": "Bonescythe Ring",
        "weight": 0.5,
        "quality": 4,
        "display_id": 35472,
        "created_at": null,
        "updated_at": null,
        "added_by_username": "Lemmings",
        "raid_group_name": null,
        "instance_id": 9,
        "guild_tier": 2,
        "pivot": {
          "character_id": 20,
          "item_id": 23060,
          "id": 1555,
          "added_by": 1,
          "type": "received",
          "order": 0,
          "note": null,
          "officer_note": null,
          "is_offspec": 0,
          "raid_group_id": null,
          "received_at": null,
          "created_at": "2020-11-18T22:12:36.000000Z",
          "updated_at": null
        }
      },
      {
        "... identical format to previous item, repeated for however many items there are"
      }
    ],
    "prios": [
      {
        "... identical format to received array"
      }
    ],
    "wishlist": [
      {
        "... identical format to received array"
      }
    ]
  },
  {
    "... identical format to previous character, repeated for however many characters there are"
  }
}
]</code>
                            </div>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.charactersWithItems', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'json']) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-code text-muted"></span>
                                        {{ __("Download JSON") }}
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.charactersWithItems', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'html']) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-code text-muted"></span>
                                        {{ __("View JSON") }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @include('partials/expansionDatabases')
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
