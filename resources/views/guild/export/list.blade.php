@extends('layouts.app')
@section('title', 'Export Data - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fas fa-fw fa-database text-muted"></span>
                        Choose Data to Export
                    </h1>
                </div>
                <div class="col-12 pt-3 pb-1 mb-2 bg-light rounded">
                    <p>
                        <strong>The format of the data being exported is subject to change.</strong> We might change a field or two as development on this project continues.
                    </p>
                    <p>
                        If you absolutely need access to some data or for a specific export format to not change, please reach out on
                        <a href="{{ env('APP_DISCORD') }}" target="_blank" alt="Join the {{ env('APP_NAME') }} Discord Server" title="Join the {{ env('APP_NAME') }} Discord Server" class="">Discord</a>.
                        We will not give you access to data you wouldn't normally have access to in your guild (such as officer notes) but we will try to help you as best we can.
                    </p>
                    <ol class="no-bullet no-indent striped">
                        <!-- Loot Received -->
                        <li class="p-3 mb-3 rounded">
                            <h2>
                                <span class="fas fa-fw fa-sack text-success"></span>
                                Loot Received
                            </h2>
                            <p>
                                All of the loot received in your guild.
                            </p>
                            <p>
                                Fields exported:
                            </p>
                            <div class="bg-dark rounded p-2">
                                <code>
                                    {{ collect(App\Http\Controllers\ExportController::LOOT_HEADERS)->implode(', ') }}
                                </code>
                            </div>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        Download CSV
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Wishlist -->
                        <li class="p-3 mb-3 rounded">
                            <h2>
                                <span class="fas fa-fw fa-scroll-old text-legendary"></span>
                                Wishlists
                            </h2>
                            <p>
                                All of the wishlisted items in your guild.
                            </p>
                            <p>
                                Fields exported:
                            </p>
                            <div class="bg-dark rounded p-2">
                                <code>
                                    {{ collect(App\Http\Controllers\ExportController::LOOT_HEADERS)->implode(', ') }}
                                </code>
                            </div>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.wishlist', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        Download CSV
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Prios -->
                        <li class="p-3 mb-3 rounded">
                            <h2>
                                <span class="fas fa-fw fa-sort-amount-down text-gold"></span>
                                Prios
                            </h2>
                            <p>
                                All of the item/character priorities in your guild.
                            </p>
                            <p>
                                Fields exported:
                            </p>
                            <div class="bg-dark rounded p-2">
                                <code>
                                    {{ collect(App\Http\Controllers\ExportController::LOOT_HEADERS)->implode(', ') }}
                                </code>
                            </div>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.prio', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        Download CSV
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Item Notes -->
                        <li class="p-3 mb-3 rounded">
                            <h2>
                                <span class="fas fa-fw fa-sword text-muted"></span>
                                All Items With Guild Notes
                            </h2>
                            <p>
                                <strong>Guild notes</strong> and <strong>prio notes</strong> are included.
                            </p>
                            <p>
                                Handy if all you want is the loot tables in a simple format...
                            </p>
                            <p>
                                Fields exported:
                            </p>
                            <div class="bg-dark rounded p-2">
                                <code>
                                    {{ collect(App\Http\Controllers\ExportController::ITEM_NOTE_HEADERS)->implode(', ') }}
                                </code>
                            </div>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.itemNotes', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        Download CSV
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Characters with everything -->
                        <li class="p-3 mb-3 rounded">
                            <h2>
                                <span class="fas fa-fw fa-users-class text-success"></span>
                                Characters With Everything
                            </h2>
                            <p>
                                All of your guild's characters with their <strong>loot received</strong>, <strong>wishlist</strong>, <strong>prios</strong>, notes, etc. It's all of the data used to populate the Roster page.
                            </p>
                            <p>
                                Format looks something like this: (provided this documentation is still up to date)
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
    "raid_id": 7,
    "is_alt": 0,
    "public_note": "test1",
    "inactive_at": null,
    "username": "Lemmings",
    "is_wishlist_unlocked": 0,
    "is_received_unlocked": 0,
    "raid_name": "Myth Raid",
    "raid_color": "10181046",
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
        "raid_name": null,
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
          "raid_id": null,
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
                                    <a href="{{ route('guild.export.charactersWithItems', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-code text-muted"></span>
                                        Download JSON
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Item Database -->
                        <li class="p-3 mb-3 rounded">
                            <h2>
                                <span class="fas fa-fw fa-database text-muted"></span>
                                Classic Item Database
                            </h2>
                            <p>
                                The Classic WoW item database used by {{ env('APP_NAME') }}!
                            </p>
                            <p>
                                <a href="https://github.com/thatsmybis/classic-wow-item-db/tree/master/thatsmybis" target="_blank">https://github.com/thatsmybis/classic-wow-item-db/tree/master/thatsmybis</a>
                            </p>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
