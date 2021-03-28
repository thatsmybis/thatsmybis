<div class="pr-2 pl-2">
    <ul class="list-inline mb-0">
        <li class="list-inline-item">
            <label for="raid_filter font-weight-light">
                <span class="text-muted fas fa-fw fa-helmet-battle"></span>
                Raid
            </label>
            <select id="raid_filter" class="form-control dark">
                <option value="">—</option>
                @foreach ($raids as $raid)
                    <option value="{{ $raid->name }}" style="color:{{ $raid->getColor() }};">
                        {{ $raid->name }}
                    </option>
                @endforeach
            </select>
        </li>
        <li class=" list-inline-item">
            <label for="class_filter font-weight-light">
                <span class="text-muted fas fa-fw fa-axe-battle"></span>
                Class
            </label>
            <select id="class_filter" class="form-control dark">
                <option value="">—</option>
                @foreach (App\Character::classes($guild->expansion_id) as $class)
                    <option value="{{ $class }}" class="text-{{ strtolower($class) }}-important">
                        {{ $class }}
                    </option>
                @endforeach
            </select>
        </li>
        <li class="list-inline-item">
            <label for="raid_filter font-weight-light">
                <span class="text-muted fas fa-fw fa-sack"></span>
                Dungeon
            </label>
            <select id="instance_filter" class="form-control dark">
                <option value="">—</option>
                @if ($guild->expansion_id == 1)
                    <option value="4">
                        Zul'Gurub
                    </option>
                    <option value="5">
                        Ruins of Ahn'Qiraj
                    </option>
                    <option value="8">
                        World Bosses
                    </option>
                    <option value="1">
                        Molten Core
                    </option>
                    <option value="2">
                        Onyxia's Lair
                    </option>
                    <option value="3">
                        Blackwing Lair
                    </option>
                    <option value="6">
                        Temple of Ahn'Qiraj
                    </option>
                    <option value="7">
                        Naxxramas
                    </option>
                @elseif ($guild->expansion_id == 2)
                    <option value="9">
                        Karazhan
                    </option>
                    <option value="10">
                        Gruul's Lair
                    </option>
                    <option value="11">
                        Magtheridon's Lair
                    </option>
                    <option value="12">
                        Serpentshrine Cavern
                    </option>
                    <option value="13">
                        Hyjal Summit
                    </option>
                    <option value="14">
                        Tempest Keep
                    </option>
                    <option value="15">
                        Black Temple
                    </option>
                    <option value="16">
                        Zul'Aman
                    </option>
                    <option value="17">
                        Sunwell Plateau
                    </option>
                    <option value="18">
                        World Bosses
                    </option>
                @endif
            </select>
        </li>

        <li class="list-inline-item font-weight-light">
            <span class="text-muted fas fa-fw fa-eye-slash"></span>
            Columns
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column-default text-link cursor-pointer">
                Defaults
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="1">
                <span class="text-muted fal fa-fw fa-sack"></span>
                Loot Received
            </span>
        </li>
        @if ($showWishlist)
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="2">
                    <span class="text-muted fal fa-fw fa-scroll-old"></span>
                    Wishlist
                </span>
            </li>
        @endif
        @if ($showPrios)
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="3">
                    <span class="text-muted fal fa-fw fa-sort-amount-down"></span>
                    Prio's
                </span>
            </li>
        @endif
        <!--
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="3">
                <span class="text-muted fas fa-fw fa-book"></span>
                Recipes
            </span>
        </li>
        -->
        <!--
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="4">
                <span class="text-muted fab fa-fw fa-discord"></span>
                Roles
            </span>
        </li>
        -->
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="6">
                <span class="text-muted fal fa-fw fa-comment-alt-lines"></span>
                Notes
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="js-show-all-clipped-items text-link cursor-pointer font-weight-light" data-column="6">
                <span class="text-muted fal fa-fw fa-eye"></span>
                Show all loot
            </span>
        </li>
    </ul>
</div>

<div class="col-12 pb-3 pr-2 pl-2 rounded">
    <table id="characterTable" class="table table-border table-hover stripe">
    </table>
</div>
