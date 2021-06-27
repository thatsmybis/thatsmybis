@php
    if (isset($currentMember)) {
        $viewRoles      = $currentMember->hasPermission('view.discord-roles');
        $viewRaids      = $currentMember->hasPermission('view.raids');
        $editCharacters = $currentMember->hasPermission('edit.characters');
        $editGuild      = $currentMember->hasPermission('edit.guild');
        $editItems      = $currentMember->hasPermission('edit.items');
        $editPrios      = !$guild->is_prio_disabled && $currentMember->hasPermission('edit.prios');
    }
@endphp

<nav class="navbar navbar-expand-md navbar-dark">
    <span class="{{ isset($guild) ? 'navbar-brand-guild' : 'navbar-brand' }}" href="{{ route('home') }}">
        <span class="font-weight-bold">
            @php
                $logoColor = 'white';
                if (isset($guild)) {
                    if ($guild->disabled_at) {
                        $logoColor = 'danger';
                    } else{
                        $logoColor = getExpansionColor($guild->expansion_id);
                    }
                }
            @endphp
            <a href="{{ route('home') }}" class="text-{{ $logoColor }}"
                title="{{ isset($guild) && $guild->disabled_at ? 'guild is disabled' : '' }}">
                {!! isset($guild) && $guild->name ? $guild->name : env('APP_NAME') !!}
            </a>
            <a href="{{ route('toggleStreamerMode') }}" class="text-muted small">
                <span class="fa-fw {!! isStreamerMode() ? 'fas fa-lock' : 'fal fa-unlock' !!}"></span>
            </a>
        </span>
    </span>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            @if (Auth::user() && isset($guild) && $guild)

                @php
                    $menuColor = ($currentMember->guild_id != $guild->id ? 'text-danger' : '' );
                @endphp

                @if ($currentMember->guild_id == $guild->id)
                    <li class="nav-item {{ in_array(Route::currentRouteName(), ['member.edit', 'member.show']) && $currentMember->id == (isset($member) ? $member->id : null) ? 'active' : '' }}">
                        <a class="nav-link {{ $menuColor }}" href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]) }}">
                            Profile
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ $menuColor }} {{ in_array(Route::currentRouteName(), ['character.loot']) ? 'active font-weight-bold' : '' }}" href="#" id="wishlistNavDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Wishlist
                        </a>
                        <div class="dropdown-menu" aria-labelledby="wishlistNavDropdown">
                            @foreach ($currentMember->characters as $character)
                                <a class="dropdown-item text-{{ strtolower($character->class) }}-important" href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                                    {{ $character->name }}
                                </a>
                            @endforeach

                            @if ($currentMember->characters->count())
                                <div class="dropdown-divider"></div>
                            @endif

                            <a class="dropdown-item text-muted" href="{{ route('guild.loot.wishlist', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                Sitewide wishlists
                            </a>
                        </div>
                    </li>
                </li>
                @elseif ($currentMember->guild_id != $guild->id)
                    <li class="nav-item">
                        <a class="nav-link">
                            <span class="fa-fw fas fa-exclamation-triangle text-danger"></span>
                        </a>
                    </li>
                @endif

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ $menuColor }} {{ in_array(Route::currentRouteName(), ['guild.item.list']) ? 'active font-weight-bold' : '' }}" href="#" id="lootNavDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Loot
                    </a>
                    <div class="dropdown-menu" aria-labelledby="lootNavDropdown">
                        @if ($editItems)
                            <div class="dropdown dropright">
                                <a class="dropdown-item dropdown-toggle" href="#" id="adminItemNotes" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Notes
                                </a>
                                <div class="dropdown-menu" aria-labelledby="adminItemNotes">
                                    @if ($guild->expansion_id == 1)
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'zulgurub']) }}">
                                            Zul'Gurub
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruins-of-ahnqiraj']) }}">
                                            Ruins of Ahn'Qiraj
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'world-bosses']) }}">
                                            World Bosses
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'molten-core']) }}">
                                            Molten Core
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'onyxias-lair']) }}">
                                            Onyxia's Lair
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackwing-lair']) }}">
                                            Blackwing Lair
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'temple-of-ahnqiraj']) }}">
                                            Temple of Ahn'Qiraj
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas']) }}">
                                            Naxxramas
                                        </a>
                                    @elseif ($guild->expansion_id == 2)
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'karazhan']) }}">
                                            Karazhan
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'gruuls-lair']) }}">
                                            Gruul's Lair
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'magtheridons-lair']) }}">
                                            Magtheridon's Lair
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'serpentshrine-cavern']) }}">
                                            Serpentshrine Cavern
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'hyjal-summit']) }}">
                                            Hyjal Summit
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'tempest-keep']) }}">
                                            Tempest Keep
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'black-temple']) }}">
                                            Black Temple
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'zulaman']) }}">
                                            Zul'Aman
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'sunwell-plateau']) }}">
                                            Sunwell Plateau
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'bc-world-bosses']) }}">
                                            World Bosses
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($editPrios)
                            <div class="dropdown dropright">
                                <a class="dropdown-item dropdown-toggle" href="#" id="adminPrioDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Prios
                                </a>
                                <div class="dropdown-menu" aria-labelledby="adminPrioDropdown">
                                    @if ($guild->expansion_id == 1)
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'zulgurub']) }}">
                                            Zul'Gurub
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruins-of-ahnqiraj']) }}">
                                            Ruins of Ahn'Qiraj
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'world-bosses']) }}">
                                            World Bosses
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'molten-core']) }}">
                                            Molten Core
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'onyxias-lair']) }}">
                                            Onyxia's Lair
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackwing-lair']) }}">
                                            Blackwing Lair
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'temple-of-ahnqiraj']) }}">
                                            Temple of Ahn'Qiraj
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas']) }}">
                                            Naxxramas
                                        </a>
                                    @elseif ($guild->expansion_id == 2)
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'karazhan']) }}">
                                            Karazhan
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'gruuls-lair']) }}">
                                            Gruul's Lair
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'magtheridons-lair']) }}">
                                            Magtheridon's Lair
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'serpentshrine-cavern']) }}">
                                            Serpentshrine Cavern
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'hyjal-summit']) }}">
                                            Hyjal Summit
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'tempest-keep']) }}">
                                            Tempest Keep
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'black-temple']) }}">
                                            Black Temple
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'zulaman']) }}">
                                            Zul'Aman
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'sunwell-plateau']) }}">
                                            Sunwell Plateau
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'bc-world-bosses']) }}">
                                            World Bosses
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($editPrios || $editItems)
                            <div class="dropdown-divider"></div>
                        @endif

                        @if ($guild->expansion_id == 1)
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'zulgurub']) }}">
                                Zul'Gurub
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruins-of-ahnqiraj']) }}">
                                Ruins of Ahn'Qiraj
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'world-bosses']) }}">
                                World Bosses
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'molten-core']) }}">
                                Molten Core
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'onyxias-lair']) }}">
                                Onyxia's Lair
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackwing-lair']) }}">
                                Blackwing Lair
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'temple-of-ahnqiraj']) }}">
                                Temple of Ahn'Qiraj
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas']) }}">
                                Naxxramas
                            </a>
                        @elseif ($guild->expansion_id == 2)
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'karazhan']) }}">
                                Karazhan
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'gruuls-lair']) }}">
                                Gruul's Lair
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'magtheridons-lair']) }}">
                                Magtheridon's Lair
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'serpentshrine-cavern']) }}">
                                Serpentshrine Cavern
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'hyjal-summit']) }}">
                                Hyjal Summit
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'tempest-keep']) }}">
                                Tempest Keep
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'black-temple']) }}">
                                Black Temple
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'zulaman']) }}">
                                Zul'Aman
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'sunwell-plateau']) }}">
                                Sunwell Plateau
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'bc-world-bosses']) }}">
                                World Bosses
                            </a>
                        @endif
                    </div>
                </li>

                <li class="nav-item {{ in_array(Route::currentRouteName(), ['guild.roster']) ? 'active' : '' }}">
                    <a class="nav-link {{ $menuColor }}" href="{{ route('guild.roster', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">Roster</a>
                </li>

                {{-- Why hello there... yes. Yes, there is a 'news' page. No, I don't quite think it's ready for the mainstream yet.
                <li class="nav-item {{ in_array(Route::currentRouteName(), ['guild.news']) ? 'active' : '' }}">
                    <a class="nav-link {{ $menuColor }}" href="{{ route('guild.news', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">News</a>
                </li>
                --}}

                {{-- Yep, there's support for a calendar...
                @if ($guild->calendar_link)
                    <li class="nav-item {{ in_array(Route::currentRouteName(), ['guild.calendar']) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('guild.calendar', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">Calendar</a>
                    </li>
                @endif
                --}}
                {{-- Why yes, there's a section for hosting resources such as guides... but it's just not time yet!
                <li class="nav-item {{ in_array(Route::currentRouteName(), ['contentIndex', 'showContent']) ? 'active' : '' }}">
                    <a class="nav-link {{ $menuColor }}" href="{{ route('contentIndex', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">Resources</a>
                </li>
                --}}

                @if ($currentMember->hasPermission('edit.raid-loot'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ $menuColor }} {{ in_array(Route::currentRouteName(), ['guild.raids.edit', 'guild.raids.list', 'guild.raids.new', 'guild.raids.show', 'item.assignLoot', 'item.assignLoot.list']) ? 'active font-weight-bold' : '' }}" href="#" id="raidNavDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Raids
                        </a>
                        <div class="dropdown-menu" aria-labelledby="raidNavDropdown">
                            <a class="dropdown-item" href="{{ route('item.assignLoot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                Assign Loot
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.raids.create', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                Create Raid
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.raids.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                List Raids
                            </a>
                            <a class="dropdown-item" href="{{ route('item.assignLoot.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                Past Loot
                            </a>
                            @if ($viewRaids)
                                <a class="dropdown-item" href="{{ route('guild.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                    Raid Groups
                                </a>
                            @endif
                        </div>
                    </li>
                @else
                    <li class="nav-item {{ in_array(Route::currentRouteName(), ['guild.raids.list']) ? 'active' : '' }}">
                        <a class="nav-link {{ $menuColor }}" href="{{ route('guild.raids.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">Raids</a>
                    </li>
                @endif

                <li class="nav-item dropdown">
                    <a class="nav-link {{ $menuColor }} dropdown-toggle" href="#" id="adminNavDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Guild
                    </a>
                    <div class="dropdown-menu" aria-labelledby="adminNavDropdown">
                        @if ($editGuild)
                            <a class="dropdown-item" href="{{ route('guild.settings', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                Settings
                            </a>
                        @endif

                        <a class="dropdown-item" href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            Audit Log
                        </a>

                        @if ($editCharacters)
                            <a class="dropdown-item" href="{{ route('character.showCreate', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'create_more' => 1]) }}">
                                Create Characters
                            </a>
                        @endif

                        <a class="dropdown-item" target="_blank" href="{{ route('guild.export.addonItems', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'html']) }}">
                            Export for TMB Tooltips
                        </a>
                        <a class="dropdown-item" href="{{ route('guild.exports', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            Exports
                        </a>
                        <a class="dropdown-item" href="{{ route('guild.members.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            Members
                        </a>

                        @if ($viewRaids)
                            <span class="dropdown-item disabled">
                                Raid Groups moved to <strong>Raids</strong>
                            </span>
                        @endif

                        @if ($editItems || $editPrios)
                            <span class="dropdown-item disabled">
                                Prios/Notes moved to <strong>Loot</strong>
                            </span>
                        @endif

                        <a class="dropdown-item" href="{{ route('guild.recipe.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            Recipes
                        </a>

                        @if ($viewRoles)
                            <a class="dropdown-item" href="{{ route('guild.roles', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                Roles
                            </a>
                        @endif
                    </div>
                </li>
            @endif
        </ul>
        <ul class="navbar-nav">
            @if (isset($guild))
                <li class="nav-item mr-3 d-patreon-block">
                    <a class="dropdown-item text-4 text-patreon" href="https://www.patreon.com/lemmings19" target="_blank" title="Patreon donations">
                    <a href="https://www.patreon.com/lemmings19" target="_blank" class="nav-link active small font-weight-bold text-patreon"
                        title="Toss a coin to your web dev">
                        <span class="fas fa-fw fa-sack"></span>
                        Support TMB
                    </a>
                </li>
            @endif
        </ul>
        <div class="my-2 my-lg-0">
            @if (Auth::guest())
                <a class="text-white" href="{{ route('discordLogin') }}" title="Sign in with Discord" rel="nofollow">
                    <span class="fal fa-fw fa-sign-in-alt"></span>
                    Sign In
                </a>
            @else
                <a href="{{ route('logout') }}"
                    class="text-white"
                    onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                    <span class="text-grey fal fa-fw fa-sign-out"></span> Sign Out ({{ Auth::user()->username }})
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            @endif
        </div>
    </div>
</nav>
