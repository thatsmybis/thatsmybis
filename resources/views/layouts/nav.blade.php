<nav class="navbar navbar-expand-md navbar-dark">
    <span class="navbar-brand" href="{{ route('home') }}">
        <span class="font-weight-bold">
            <a href="{{ route('home') }}" class="text-white">
                {!! isset($guild) && $guild->name ? $guild->name : env('APP_NAME') !!}
            </a>
            <a href="{{ route('toggleStreamerMode') }}" class="text-white">
                <span class="fa-fw {!! isStreamerMode() ? 'fas fa-shield-alt' : 'fal fa-shield' !!}"></span>
            </a>
        </span>
    </span>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            @if (Auth::user() && isset($guild) && $guild)

                @if (isset($currentMember) && $currentMember)
                    <li class="nav-item {{ in_array(Route::currentRouteName(), ['member.edit', 'member.show']) && $currentMember->id == (isset($member) ? $member->id : null) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]) }}">
                            Profile
                        </a>
                    </li>
                @endif

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ in_array(Route::currentRouteName(), ['guild.item.list']) ? 'active font-weight-bold' : '' }}" href="#" id="lootNavDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Loot
                    </a>
                    <div class="dropdown-menu" aria-labelledby="lootNavDropdown">
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
                        <span class="dropdown-item text-muted" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas']) }}">
                            Naxxramas
                        </span>
                    </div>
                </li>

                <li class="nav-item {{ in_array(Route::currentRouteName(), ['guild.roster']) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('guild.roster', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">Roster</a>
                </li>

                {{-- Why hello there... yes. Yes, there is a 'news' page. No, I don't quite think it's ready for the mainstream yet.
                <li class="nav-item {{ in_array(Route::currentRouteName(), ['guild.news']) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('guild.news', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">News</a>
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
                    <a class="nav-link" href="{{ route('contentIndex', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">Resources</a>
                </li>
                --}}

                @if ($currentMember->hasPermission('edit.raid-loot'))
                    <li class="nav-item {{ in_array(Route::currentRouteName(), ['item.massInput']) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('item.massInput', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">Assign Loot</a>
                    </li>
                @endif

                <li class="nav-item {{ in_array(Route::currentRouteName(), ['guild.auditLog']) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">Audit</a>
                </li>

                @php
                    $viewRoles      = $currentMember->hasPermission('view.discord-roles');
                    $viewRaids      = $currentMember->hasPermission('view.raids');
                    $editCharacters = $currentMember->hasPermission('edit.characters');
                    $editGuild      = $currentMember->hasPermission('edit.guild');
                    $editItems      = $currentMember->hasPermission('edit.items');
                    $editPrios      = $currentMember->hasPermission('edit.prios');
                @endphp

                @if ($viewRoles || $viewRaids || $editGuild || $editPrios)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminNavDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Admin
                        </a>
                        <div class="dropdown-menu" aria-labelledby="adminNavDropdown">


                            @if ($editGuild)
                                <a class="dropdown-item" href="{{ route('guild.settings', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                    Settings
                                </a>
                            @endif

                            <a class="dropdown-item" href="{{ route('guild.members.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                Members
                            </a>

                            @if ($viewRaids)
                                <a class="dropdown-item" href="{{ route('guild.raids', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                    Raids
                                </a>
                            @endif

                            @if ($viewRoles)
                                <a class="dropdown-item" href="{{ route('guild.roles', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                    Roles
                                </a>
                            @endif

                            @if ($editCharacters)
                                <a class="dropdown-item" href="{{ route('character.showCreate', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'create_more' => 1]) }}">
                                    Create Characters
                                </a>
                            @endif

                            @if ($editItems)
                                <div class="dropdown dropright">
                                    <a class="dropdown-item dropdown-toggle" href="#" id="adminItemNotes" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Item Notes
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="adminItemNotes">
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
                                        <span class="dropdown-item text-muted" href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas']) }}">
                                            Naxxramas
                                        </span>
                                    </div>
                                </div>
                            @endif

                            @if ($editPrios)
                                <div class="dropdown dropright">
                                    <a class="dropdown-item dropdown-toggle" href="#" id="adminPrioDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Item Prios
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="adminPrioDropdown">
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaid', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'zulgurub']) }}">
                                            Zul'Gurub
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaid', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'ruins-of-ahnqiraj']) }}">
                                            Ruins of Ahn'Qiraj
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaid', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'world-bosses']) }}">
                                            World Bosses
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaid', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'molten-core']) }}">
                                            Molten Core
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaid', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'onyxias-lair']) }}">
                                            Onyxia's Lair
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaid', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'blackwing-lair']) }}">
                                            Blackwing Lair
                                        </a>
                                        <a class="dropdown-item" href="{{ route('guild.prios.chooseRaid', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'temple-of-ahnqiraj']) }}">
                                            Temple of Ahn'Qiraj
                                        </a>
                                        <span class="dropdown-item text-muted" href="{{ route('guild.prios.chooseRaid', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'naxxramas']) }}">
                                            Naxxramas
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </li>
                @endif
            @endif
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item mr-3">
                <a href="{{ env('LINK_GITHUB') }}" target="_blank" class="nav-link active font-weight-bold">
                    <span class="fab fa-github"></span>
                    Contribute
                </a>
            </li>
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
