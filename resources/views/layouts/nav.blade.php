@php
    if (isset($currentMember)) {
        $viewRoles      = $currentMember->hasPermission('view.discord-roles');
        $viewRaids      = $currentMember->hasPermission('view.raids');
        $editCharacters = $currentMember->hasPermission('edit.characters');
        $editGuild      = $currentMember->hasPermission('edit.guild');
        $editItems      = $currentMember->hasPermission('edit.items');
        $editRaidLoot   = $currentMember->hasPermission('edit.raid-loot');
        $editRaidGroups = $currentMember->hasPermission('edit.raids');
        $editPrios      = !$guild->is_prio_disabled && $currentMember->hasPermission('edit.prios');
    }
@endphp

<nav class="navbar navbar-expand-md navbar-dark sticky-top bg-navbar-blur">
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
                            {{ __("Profile") }}
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ $menuColor }} {{ in_array(Route::currentRouteName(), ['character.loot']) ? 'active font-weight-bold' : '' }}" href="javascript:void(0)" id="wishlistNavDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @if ($guild->is_wishlist_disabled)
                                {{ __("Character") }}
                            @else
                                {{ __("Wishlist") }}
                            @endif
                        </a>
                        <div class="dropdown-menu" aria-labelledby="wishlistNavDropdown">
                            @foreach ($currentMember->characters->whereNull('inactive_at') as $character)
                                <a class="dropdown-item text-{{ slug($character->class) }}-important" href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                                    {{ $character->name }}
                                </a>
                            @endforeach

                            @if ($currentMember->characters->whereNull('inactive_at')->count())
                                <div class="dropdown-divider"></div>
                            @endif

                            <a class="dropdown-item text-muted" href="{{ route('character.showCreate', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'member_id' => $currentMember->id]) }}">
                                <span class="fas fa-user-plus text-muted"></span>
                                {{ __("Create character") }}
                            </a>

                            <a class="dropdown-item text-muted" href="{{ route('guild.loot.wishlist', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                <span class="fas fa-fw fa-scroll-old text-muted"></span>
                                {{ __("Sitewide wishlists") }}
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
                    <a class="nav-link dropdown-toggle {{ $menuColor }} {{ in_array(Route::currentRouteName(), ['guild.item.list', 'guild.item.list.edit', 'guild.prios.chooseRaidGroup']) ? 'active font-weight-bold' : '' }}" href="javascript:void(0)" id="lootNavDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __("Loot") }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="lootNavDropdown">
                        @if ($editRaidLoot)
                            <a class="dropdown-item" href="{{ route('item.assignLoot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                <span class="fas fa-fw fa-sack text-muted"></span>
                                {{ __("Assign Loot") }}
                            </a>
                        @endif
                        @if ($editItems)
                            <div class="dropdown dropright">
                                <a class="dropdown-item dropdown-toggle" href="javascript:void(0)" id="adminItemNotes" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="fas fa-fw fa-sticky-note text-muted"></span>
                                    {{ __("Notes") }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="adminItemNotes">
                                    @if ($guild->expansion_id == 1)
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'zulgurub']) }}">
                                            {{ __("Zul'Gurub") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruins-of-ahnqiraj']) }}">
                                            {{ __("Ruins of Ahn'Qiraj") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'world-bosses']) }}">
                                            {{ __("World Bosses") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'molten-core']) }}">
                                            {{ __("Molten Core") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'onyxias-lair']) }}">
                                            {{ __("Onyxia's Lair") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackwing-lair']) }}">
                                            {{ __("Blackwing Lair") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'temple-of-ahnqiraj']) }}">
                                            {{ __("Temple of Ahn'Qiraj") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas']) }}">
                                            {{ __("Naxxramas") }}
                                        </a>
                                    @elseif ($guild->expansion_id == 2)
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'karazhan']) }}">
                                            {{ __("Karazhan") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'gruuls-lair']) }}">
                                            {{ __("Gruul's Lair") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'magtheridons-lair']) }}">
                                            {{ __("Magtheridon's Lair") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'serpentshrine-cavern']) }}">
                                            {{ __("Serpentshrine Cavern") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'tempest-keep']) }}">
                                            {{ __("Tempest Keep") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'hyjal-summit']) }}">
                                            {{ __("Hyjal Summit") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'black-temple']) }}">
                                            {{ __("Black Temple") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'zulaman']) }}">
                                            {{ __("Zul'Aman") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'sunwell-plateau']) }}">
                                            {{ __("Sunwell Plateau") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'bc-world-bosses']) }}">
                                            {{ __("World Bosses") }}
                                        </a>
                                    @elseif ($guild->expansion_id == 3)
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas-n10']) }}">
                                            {{ __("Naxxramas N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas-n25']) }}">
                                            {{ __("Naxxramas N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'eye-of-eternity-n10']) }}">
                                            {{ __("Eye of Eternity N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'eye-of-eternity-n25']) }}">
                                            {{ __("Eye of Eternity N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'obsidian-sanctum-n10']) }}">
                                            {{ __("Obsidian Sanctum N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'obsidian-sanctum-n25']) }}">
                                            {{ __("Obsidian Sanctum N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'vault-of-archavon-n10']) }}">
                                            {{ __("Vault of Archavon N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'vault-of-archavon-n25']) }}">
                                            {{ __("Vault of Archavon N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ulduar-n10']) }}">
                                            {{ __("Ulduar N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ulduar-n25']) }}">
                                            {{ __("Ulduar N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'trial-of-the-crusader-n10']) }}">
                                            {{ __("Trial of the Crusader N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'trial-of-the-crusader-25']) }}">
                                            {{ __("Trial of the Crusader N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'trial-of-the-crusader-h10']) }}">
                                            {{ __("Trial of the Crusader H10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'trial-of-the-crusader-h25']) }}">
                                            {{ __("Trial of the Crusader H25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'onyxias-lair-n10']) }}">
                                            {{ __("Onyxia's Lair N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'onyxias-lair-n25']) }}">
                                            {{ __("Onyxia's Lair N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'icecrown-citadel-n10']) }}">
                                            {{ __("Icecrown Citadel N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'icecrown-citadel-n25']) }}">
                                            {{ __("Icecrown Citadel N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'icecrown-citadel-h10']) }}">
                                            {{ __("Icecrown Citadel H10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'icecrown-citadel-h25']) }}">
                                            {{ __("Icecrown Citadel H25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruby-sanctum-n10']) }}">
                                            {{ __("Ruby Sanctum N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruby-sanctum-n25']) }}">
                                            {{ __("Ruby Sanctum N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruby-sanctum-h10']) }}">
                                            {{ __("Ruby Sanctum H10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruby-sanctum-h25']) }}">
                                            {{ __("Ruby Sanctum H25") }}
                                        <a>
                                    @elseif ($guild->expansion_id == 4)
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'world']) }}">
                                            {{ __("World") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackfathom-depths']) }}">
                                            {{ __("BFD") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'gnomeregan']) }}">
                                            {{ __("Gnomeregan") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'sunken-temple']) }}">
                                            {{ __("Sunken Temple") }}
                                        </a>
                                    @elseif ($guild->expansion_id == 5)
                                        <!-- <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'baradin-hold-n']) }}">
                                            {{ __("Baradin Hold Normal") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'baradin-hold-h']) }}">
                                            {{ __("Baradin Hold Heroic") }}
                                        </a> -->
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'throne-of-the-four-winds-n']) }}">
                                            {{ __("Throne of the Four Winds Normal") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'throne-of-the-four-winds-h']) }}">
                                            {{ __("Throne of the Four Winds Heroic") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackwing-descent-n']) }}">
                                            {{ __("Blackwing Descent Normal") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackwing-descent-h']) }}">
                                            {{ __("Blackwing Descent Heroic") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'the-bastion-of-twilight-n']) }}">
                                            {{ __("The Bastion of Twilight Normal") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'the-bastion-of-twilight-h']) }}">
                                            {{ __("The Bastion of Twilight Heroic") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'firelands-n']) }}">
                                            {{ __("Firelands Normal") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'firelands-h']) }}">
                                            {{ __("Firelands Heroic") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'dragon-soul-n']) }}">
                                            {{ __("Dragon Soul Normal") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'dragon-soul-h']) }}">
                                            {{ __("Dragon Soul Heroic") }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($editPrios)
                            <div class="dropdown dropright">
                                <a class="dropdown-item dropdown-toggle" href="javascript:void(0)" id="adminPrioDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="fas fa-fw fa-sort-amount-down text-muted"></span>
                                    {{ __("Prios") }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="adminPrioDropdown">
                                    @if ($guild->expansion_id == 1)
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'zulgurub']) }}">
                                            {{ __("Zul'Gurub") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruins-of-ahnqiraj']) }}">
                                            {{ __("Ruins of Ahn'Qiraj") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'world-bosses']) }}">
                                            {{ __("World Bosses") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'molten-core']) }}">
                                            {{ __("Molten Core") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'onyxias-lair']) }}">
                                            {{ __("Onyxia's Lair") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackwing-lair']) }}">
                                            {{ __("Blackwing Lair") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'temple-of-ahnqiraj']) }}">
                                            {{ __("Temple of Ahn'Qiraj") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas']) }}">
                                            {{ __("Naxxramas") }}
                                        </a>
                                    @elseif ($guild->expansion_id == 2)
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'karazhan']) }}">
                                            {{ __("Karazhan") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'gruuls-lair']) }}">
                                            {{ __("Gruul's Lair") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'magtheridons-lair']) }}">
                                            {{ __("Magtheridon's Lair") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'serpentshrine-cavern']) }}">
                                            {{ __("Serpentshrine Cavern") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'tempest-keep']) }}">
                                            {{ __("Tempest Keep") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'hyjal-summit']) }}">
                                            {{ __("Hyjal Summit") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'black-temple']) }}">
                                            {{ __("Black Temple") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'zulaman']) }}">
                                            {{ __("Zul'Aman") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'sunwell-plateau']) }}">
                                            {{ __("Sunwell Plateau") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'bc-world-bosses']) }}">
                                            {{ __("World Bosses") }}
                                        </a>
                                    @elseif ($guild->expansion_id == 3)
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas-n10']) }}">
                                            {{ __("Naxxramas N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas-n25']) }}">
                                            {{ __("Naxxramas N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'eye-of-eternity-n10']) }}">
                                            {{ __("Eye of Eternity N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'eye-of-eternity-n25']) }}">
                                            {{ __("Eye of Eternity N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'obsidian-sanctum-n10']) }}">
                                            {{ __("Obsidian Sanctum N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'obsidian-sanctum-n25']) }}">
                                            {{ __("Obsidian Sanctum N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'vault-of-archavon-n10']) }}">
                                            {{ __("Vault of Archavon N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'vault-of-archavon-n25']) }}">
                                            {{ __("Vault of Archavon N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ulduar-n10']) }}">
                                            {{ __("Ulduar N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ulduar-n25']) }}">
                                            {{ __("Ulduar N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'trial-of-the-crusader-n10']) }}">
                                            {{ __("Trial of the Crusader N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'trial-of-the-crusader-25']) }}">
                                            {{ __("Trial of the Crusader N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'trial-of-the-crusader-h10']) }}">
                                            {{ __("Trial of the Crusader H10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'trial-of-the-crusader-h25']) }}">
                                            {{ __("Trial of the Crusader H25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'onyxias-lair-n10']) }}">
                                            {{ __("Onyxia's Lair N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'onyxias-lair-n25']) }}">
                                            {{ __("Onyxia's Lair N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'icecrown-citadel-n10']) }}">
                                            {{ __("Icecrown Citadel N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'icecrown-citadel-n25']) }}">
                                            {{ __("Icecrown Citadel N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'icecrown-citadel-h10']) }}">
                                            {{ __("Icecrown Citadel H10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'icecrown-citadel-h25']) }}">
                                            {{ __("Icecrown Citadel H25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruby-sanctum-n10']) }}">
                                            {{ __("Ruby Sanctum N10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruby-sanctum-n25']) }}">
                                            {{ __("Ruby Sanctum N25") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruby-sanctum-h10']) }}">
                                            {{ __("Ruby Sanctum H10") }}
                                        <a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruby-sanctum-h25']) }}">
                                            {{ __("Ruby Sanctum H25") }}
                                        <a>
                                    @elseif ($guild->expansion_id == 4)
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'world']) }}">
                                            {{ __("World") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackfathom-depths']) }}">
                                            {{ __("BFD") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'gnomeregan']) }}">
                                            {{ __("Gnomeregan") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'sunken-temple']) }}">
                                            {{ __("Sunken Temple") }}
                                        </a>
                                    @elseif ($guild->expansion_id == 5)
                                        <!-- <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'baradin-hold-n']) }}">
                                            {{ __("Baradin Hold Normal") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'baradin-hold-h']) }}">
                                            {{ __("Baradin Hold Heroic") }}
                                        </a> -->
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'throne-of-the-four-winds-n']) }}">
                                            {{ __("Throne of the Four Winds Normal") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'throne-of-the-four-winds-h']) }}">
                                            {{ __("Throne of the Four Winds Heroic") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackwing-descent-n']) }}">
                                            {{ __("Blackwing Descent Normal") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackwing-descent-h']) }}">
                                            {{ __("Blackwing Descent Heroic") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'the-bastion-of-twilight-n']) }}">
                                            {{ __("The Bastion of Twilight Normal") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'the-bastion-of-twilight-h']) }}">
                                            {{ __("The Bastion of Twilight Heroic") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'firelands-n']) }}">
                                            {{ __("Firelands Normal") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'firelands-h']) }}">
                                            {{ __("Firelands Heroic") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'dragon-soul-n']) }}">
                                            {{ __("Dragon Soul Normal") }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'dragon-soul-h']) }}">
                                            {{ __("Dragon Soul Heroic") }}
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
                                {{ __("Zul'Gurub") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruins-of-ahnqiraj']) }}">
                                {{ __("Ruins of Ahn'Qiraj") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'world-bosses']) }}">
                                {{ __("World Bosses") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'molten-core']) }}">
                                {{ __("Molten Core") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'onyxias-lair']) }}">
                                {{ __("Onyxia's Lair") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackwing-lair']) }}">
                                {{ __("Blackwing Lair") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'temple-of-ahnqiraj']) }}">
                                {{ __("Temple of Ahn'Qiraj") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas']) }}">
                                {{ __("Naxxramas") }}
                            </a>
                        @elseif ($guild->expansion_id == 2)
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'karazhan']) }}">
                                {{ __("Karazhan") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'gruuls-lair']) }}">
                                {{ __("Gruul's Lair") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'magtheridons-lair']) }}">
                                {{ __("Magtheridon's Lair") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'serpentshrine-cavern']) }}">
                                {{ __("Serpentshrine Cavern") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'tempest-keep']) }}">
                                {{ __("Tempest Keep") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'hyjal-summit']) }}">
                                {{ __("Hyjal Summit") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'black-temple']) }}">
                                {{ __("Black Temple") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'zulaman']) }}">
                                {{ __("Zul'Aman") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'sunwell-plateau']) }}">
                                {{ __("Sunwell Plateau") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'bc-world-bosses']) }}">
                                {{ __("World Bosses") }}
                            </a>
                        @elseif ($guild->expansion_id == 3)
                            <div class="dropdown dropright">
                                <a class="dropdown-item dropdown-toggle" href="javascript:void(0)" id="naxx" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ __("Naxxramas") }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="naxx">
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas-n10']) }}">
                                        {{ __("Naxxramas N10") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas-n25']) }}">
                                        {{ __("Naxxramas N25") }}
                                    <a>
                                </div>
                            </div>

                            <div class="dropdown dropright">
                                <a class="dropdown-item dropdown-toggle" href="javascript:void(0)" id="eoe" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ __("Eye of Eternity") }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="eoe">
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'eye-of-eternity-n10']) }}">
                                        {{ __("Eye of Eternity N10") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'eye-of-eternity-n25']) }}">
                                        {{ __("Eye of Eternity N25") }}
                                    <a>
                                </div>
                            </div>

                            <div class="dropdown dropright">
                                <a class="dropdown-item dropdown-toggle" href="javascript:void(0)" id="os" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ __("Obsidian Sanctum") }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="os">
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'obsidian-sanctum-n10']) }}">
                                        {{ __("Obsidian Sanctum N10") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'obsidian-sanctum-n25']) }}">
                                        {{ __("Obsidian Sanctum N25") }}
                                    <a>
                                </div>
                            </div>

                            <div class="dropdown dropright">
                                <a class="dropdown-item dropdown-toggle" href="javascript:void(0)" id="voa" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ __("Vault of Archavon") }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="voa">
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'vault-of-archavon-n10']) }}">
                                        {{ __("Vault of Archavon N10") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'vault-of-archavon-n25']) }}">
                                        {{ __("Vault of Archavon N25") }}
                                    <a>
                                </div>
                            </div>

                            <div class="dropdown dropright">
                                <a class="dropdown-item dropdown-toggle" href="javascript:void(0)" id="uld" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ __("Ulduar") }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="uld">
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ulduar-n10']) }}">
                                        {{ __("Ulduar N10") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ulduar-n25']) }}">
                                        {{ __("Ulduar N25") }}
                                    <a>
                                </div>
                            </div>

                            <div class="dropdown dropright">
                                <a class="dropdown-item dropdown-toggle" href="javascript:void(0)" id="totc" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ __("Trial of the Crusader") }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="totc">
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'trial-of-the-crusader-n10']) }}">
                                        {{ __("Trial of the Crusader N10") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'trial-of-the-crusader-25']) }}">
                                        {{ __("Trial of the Crusader N25") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'trial-of-the-crusader-h10']) }}">
                                        {{ __("Trial of the Crusader H10") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'trial-of-the-crusader-h25']) }}">
                                        {{ __("Trial of the Crusader H25") }}
                                    <a>
                                </div>
                            </div>

                            <div class="dropdown dropright">
                                <a class="dropdown-item dropdown-toggle" href="javascript:void(0)" id="ony" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ __("Onyxia's Lair") }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="ony">
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'onyxias-lair-n10']) }}">
                                        {{ __("Onyxia's Lair N10") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'onyxias-lair-n25']) }}">
                                        {{ __("Onyxia's Lair N25") }}
                                    <a>
                                </div>
                            </div>

                            <div class="dropdown dropright">
                                <a class="dropdown-item dropdown-toggle" href="javascript:void(0)" id="icc" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ __("Icecrown Citadel") }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="icc">
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'icecrown-citadel-n10']) }}">
                                        {{ __("Icecrown Citadel N10") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'icecrown-citadel-n25']) }}">
                                        {{ __("Icecrown Citadel N25") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'icecrown-citadel-h10']) }}">
                                        {{ __("Icecrown Citadel H10") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'icecrown-citadel-h25']) }}">
                                        {{ __("Icecrown Citadel H25") }}
                                    <a>
                                </div>
                            </div>

                            <div class="dropdown dropright">
                                <a class="dropdown-item dropdown-toggle" href="javascript:void(0)" id="rs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ __("Ruby Sanctum") }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="rs">
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruby-sanctum-n10']) }}">
                                        {{ __("Ruby Sanctum N10") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruby-sanctum-n25']) }}">
                                        {{ __("Ruby Sanctum N25") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruby-sanctum-h10']) }}">
                                        {{ __("Ruby Sanctum H10") }}
                                    <a>
                                    <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruby-sanctum-h25']) }}">
                                        {{ __("Ruby Sanctum H25") }}
                                    <a>
                                </div>
                            </div>

                        @elseif ($guild->expansion_id == 4)
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'world']) }}">
                                {{ __("World") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackfathom-depths']) }}">
                                {{ __("Blackfathom Depths") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'gnomeregan']) }}">
                                {{ __("Gnomeregan") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'sunken-temple']) }}">
                                {{ __("Sunken Temple") }}
                            </a>
                        @elseif ($guild->expansion_id == 5)
                            <!-- <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'baradin-hold-n']) }}">
                                {{ __("Baradin Hold Normal") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'baradin-hold-h']) }}">
                                {{ __("Baradin Hold Heroic") }}
                            </a> -->
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'throne-of-the-four-winds-n']) }}">
                                {{ __("Throne of the Four Winds Normal") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'throne-of-the-four-winds-h']) }}">
                                {{ __("Throne of the Four Winds Heroic") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackwing-descent-n']) }}">
                                {{ __("Blackwing Descent Normal") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackwing-descent-h']) }}">
                                {{ __("Blackwing Descent Heroic") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'the-bastion-of-twilight-n']) }}">
                                {{ __("The Bastion of Twilight Normal") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'the-bastion-of-twilight-h']) }}">
                                {{ __("The Bastion of Twilight Heroic") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'firelands-n']) }}">
                                {{ __("Firelands Normal") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'firelands-h']) }}">
                                {{ __("Firelands Heroic") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'dragon-soul-n']) }}">
                                {{ __("Dragon Soul Normal") }}
                            </a>
                            <a class="dropdown-item" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'dragon-soul-h']) }}">
                                {{ __("Dragon Soul Heroic") }}
                            </a>
                        @endif
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ $menuColor }} {{ in_array(Route::currentRouteName(), [
                            'guild.roster',
                            'guild.rosterStats',
                        ]) ? 'active font-weight-bold' : '' }}" href="javascript:void(0)" id="rosterNavDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __("Roster") }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="rosterNavDropdown">
                        <a class="dropdown-item" href="{{ route('guild.roster', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <span class="fas fa-fw fa-users text-muted"></span>
                            {{ __("Roster") }}
                        </a>
                        <a class="dropdown-item" href="{{ route('guild.rosterStats', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <span class="fas fa-fw fa-user-chart text-muted"></span>
                            {{ __("Item Distribution") }}
                        </a>
                        <a class="dropdown-item" href="{{ route('guild.rosterBreakdown', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <span class="fas fa-fw fa-users-class text-muted"></span>
                            {{ __("Class Breakdown") }}
                        </a>
                    </div>
                </li>

                {{-- Why hello there... yes. Yes, there is a 'news' page. No, I don't quite think it's ready for the mainstream yet.
                <li class="nav-item {{ in_array(Route::currentRouteName(), ['guild.news']) ? 'active' : '' }}">
                    <a class="nav-link {{ $menuColor }}" href="{{ route('guild.news', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                        {{ __("News") }}
                    </a>
                </li>
                --}}

                {{-- Yep, there's support for a calendar...
                @if ($guild->calendar_link)
                    <li class="nav-item {{ in_array(Route::currentRouteName(), ['guild.calendar']) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('guild.calendar', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            {{ __("Calendar") }}
                        </a>
                    </li>
                @endif
                --}}
                {{-- Why yes, there's a section for hosting resources such as guides... but it's just not time yet!
                <li class="nav-item {{ in_array(Route::currentRouteName(), ['contentIndex', 'showContent']) ? 'active' : '' }}">
                    <a class="nav-link {{ $menuColor }}" href="{{ route('contentIndex', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                        {{ __("Resources") }}
                    </a>
                </li>
                --}}


                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ $menuColor }} {{ in_array(Route::currentRouteName(), [
                            'guild.raidGroups',
                            'guild.raidGroup.create',
                            'guild.raidGroup.edit',
                            'guild.raidGroup.mainCharacters',
                            'guild.raidGroup.secondaryCharacters',
                            'guild.raids.edit',
                            'guild.raids.list',
                            'guild.raids.new',
                            'guild.raids.show',
                            'item.assignLoot',
                            'item.assignLoot.list'
                        ]) ? 'active font-weight-bold' : '' }}" href="javascript:void(0)" id="raidNavDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __("Raids") }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="raidNavDropdown">
                        <a class="dropdown-item" href="{{ route('guild.raids.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <span class="fas fa-fw fa-list text-muted"></span>
                            {{ __("List Raids") }}
                        </a>
                        <a class="dropdown-item" href="{{ route('guild.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <span class="fas fa-fw fa-helmet-battle text-muted"></span>
                            {{ __("Raid Groups") }}
                        </a>
                        @if ($viewRaids)
                            <a class="dropdown-item" href="{{ route('guild.raids.create', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <span class="fas fa-fw fa-calendar-plus text-muted"></span>
                                {{ __("Create Raid") }}
                            </a>
                        @endif
                        @if ($editRaidGroups)
                            <a class="dropdown-item" href="{{ route('guild.raidGroup.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <span class="fas fa-fw fa-plus text-muted"></span>
                                {{ __("Create Raid Group") }}
                            </a>
                        @endif
                        <a class="dropdown-item" href="{{ route('item.assignLoot.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <span class="fas fa-fw fa-history text-muted"></span>
                            {{ __("Old Loot Assignments") }}
                        </a>
                        @if (!$guild->is_attendance_hidden && $guild->raidGroups->count())
                            <div class="dropdown dropright">
                                <a title="{{ __("Only show attendance for a specific raid group") }}" class="dropdown-item dropdown-toggle" href="javascript:void(0)" id="raidGroupAttendanceFilter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="fas fa-fw fa-filter text-muted"></span>
                                    {{ __("Attendance Filter") }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="raidGroupAttendanceFilter">
                                    @foreach ($guild->raidGroups as $raidGroup)
                                        <form class="dropdown-item" role="form" method="POST" action="{{ route('setRaidGroupFilter', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                            {{ csrf_field() }}
                                            <input hidden name="raid_group_id" value="{{ $raidGroup->id }}" />
                                            <button class="link text-white">
                                                <span class="fas fa-fw fa-helmet-battle text-muted"></span>
                                                {{ $raidGroup->name }}
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link {{ $menuColor }} {{ in_array(Route::currentRouteName(), [
                            'guild.settings',
                            'guild.auditLog',
                            'guild.exports',
                            'guild.members.list',
                            'guild.recipe.list',
                            'guild.roles',
                        ]) ? 'active font-weight-bold' : '' }}
                        dropdown-toggle" href="javascript:void(0)" id="adminNavDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __("Guild") }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="adminNavDropdown">
                        @if ($editGuild)
                            <a class="dropdown-item" href="{{ route('guild.settings', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                <span class="fas fa-fw fa-cog text-muted"></span>
                                {{ __("Settings") }}
                            </a>
                        @endif

                        <a class="dropdown-item" href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <span class="fas fa-fw fa-clipboard-list-check text-muted"></span>
                            {{ __("Audit Log") }}
                        </a>

                        @if ($editCharacters)
                            <a class="dropdown-item" href="{{ route('character.showCreateMany', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <span class="fas fa-fw fa-user-plus text-muted"></span>
                                {{ __("Create Characters") }}
                            </a>
                        @endif

                        <a class="dropdown-item" href="{{ route('guild.exports', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <span class="fas fa-fw fa-file-export text-muted"></span>
                            {{ __("Exports") }}
                        </a>
                        <a class="dropdown-item" href="{{ route('guild.export.gargul', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <img class="inline-image-icon" src="{{ asset('images/gargul_monotone.png') }}"></img>
                            {{ __("Export Gargul") }}
                        </a>
                        <a class="dropdown-item" target="_blank" href="{{ route('guild.export.addonItems', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'html']) }}">
                            <span class="fas fa-fw fa- text-muted"></span>
                            {{ __("Export TMB Tooltips") }}
                        </a>
                        <a class="dropdown-item" href="{{ route('guild.members.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <span class="fas fa-fw fa-users text-muted"></span>
                            {{ __("Members") }}
                        </a>

                        <a class="dropdown-item" href="{{ route('guild.recipe.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <span class="fas fa-fw fa-book text-muted"></span>
                            {{ __("Recipes") }}
                        </a>

                        @if ($viewRoles)
                            <a class="dropdown-item" href="{{ route('guild.roles', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                            <span class="fab fa-fw fa-discord text-muted"></span>
                                {{ __("Roles") }}
                            </a>
                        @endif
                    </div>
                </li>
            @endif
        </ul>
        <ul class="navbar-nav">
            @if (isset($guild) && !request()->get('hideAds'))
                <li class="nav-item mr-3 d-patreon-block">
                    <a href="{{ route('donate') }}" target="_blank" class="nav-link active small font-weight-bold text-patreon mt-0"
                        title="Toss a coin to your web dev">
                        <span class="fas fa-fw fa-sack"></span>
                        {{ __("Donate") }}
                    </a>
                </li>
            @endif
        </ul>
        <div class="my-2 my-lg-0">
            @if (Auth::guest())
                <a class="text-white" href="{{ route('discordLogin') }}" title="Sign in with Discord" rel="nofollow">
                    <span class="fal fa-fw fa-sign-in-alt"></span>
                    {{ __("Sign In") }}
                </a>
            @else
                <a href="{{ route('logout') }}"
                    class="text-white"
                    onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                    <span class="text-grey fal fa-fw fa-sign-out"></span>
                    {{ __("Sign Out") }}
                    <span class="small text-muted">({{ Auth::user()->username }})</span>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            @endif
        </div>
    </div>
</nav>
