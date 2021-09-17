<div class="pr-2 pl-2">
    <ul class="list-inline mb-0">
        <li class="list-inline-item">
            <label for="raid_group_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-helmet-battle"></span>
                {{ __("Raid Group") }}
            </label>
            <select id="raid_group_filter" class="form-control dark">
                <option value="">—</option>
                @foreach ($raidGroups->whereNull('disabled_at') as $raidGroup)
                    <option value="{{ $raidGroup->id }}" style="color:{{ $raidGroup->getColor() }};">
                        {{ $raidGroup->name }}
                    </option>
                @endforeach
            </select>
        </li>
        <li class=" list-inline-item">
            <label for="class_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-axe-battle"></span>
                {{ __("Class") }}
            </label>
            <select id="class_filter" class="form-control dark">
                <option value="">—</option>
                @foreach (App\Character::classes($guild->expansion_id) as $key => $class)
                    <option value="{{ $class }}" class="text-{{ strtolower($key) }}-important">
                        {{ $class }}
                    </option>
                @endforeach
            </select>
        </li>
        <li class="list-inline-item">
            <label for="instance_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-sack"></span>
                {{ __("Dungeon") }}
            </label>
            <select id="instance_filter" class="form-control dark">
                <option value="">—</option>
                @if ($guild->expansion_id == 1)
                    <option value="4">
                        {{ __("Zul'Gurub") }}
                    </option>
                    <option value="5">
                        {{ __("Ruins of Ahn'Qiraj") }}
                    </option>
                    <option value="8">
                        {{ __("World Bosses") }}
                    </option>
                    <option value="1">
                        {{ __("Molten Core") }}
                    </option>
                    <option value="2">
                        {{ __("Onyxia's Lair") }}
                    </option>
                    <option value="3">
                        {{ __("Blackwing Lair") }}
                    </option>
                    <option value="6">
                        {{ __("Temple of Ahn'Qiraj") }}
                    </option>
                    <option value="7">
                        {{ __("Naxxramas") }}
                    </option>
                @elseif ($guild->expansion_id == 2)
                    <option value="9">
                        {{ __("Karazhan") }}
                    </option>
                    <option value="10">
                        {{ __("Gruul's Lair") }}
                    </option>
                    <option value="11">
                        {{ __("Magtheridon's Lair") }}
                    </option>
                    <option value="12">
                        {{ __("Serpentshrine Cavern") }}
                    </option>
                    <option value="13">
                        {{ __("Hyjal Summit") }}
                    </option>
                    <option value="14">
                        {{ __("Tempest Keep") }}
                    </option>
                    <option value="15">
                        {{ __("Black Temple") }}
                    </option>
                    <option value="16">
                        {{ __("Zul'Aman") }}
                    </option>
                    <option value="17">
                        {{ __("Sunwell Plateau") }}
                    </option>
                    <option value="18">
                        {{ __("World Bosses") }}
                    </option>
                @endif
            </select>
        </li>
        <li class="list-inline-item">
            @php
                $wishlistNames = $guild->getWishlistNames();
            @endphp
            <label for="wishlist_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-scroll-old"></span>
                {{ __("Wishlist") }}
            </label>
            <select id="wishlist_filter" class="form-control dark">
                @for ($i = 1; $i <= App\Http\Controllers\CharacterLootController::MAX_WISHLIST_LISTS; $i++)
                    <option value="{{ $i }}" {{ $guild->current_wishlist_number === $i ? 'selected' : '' }}>
                        @if ($wishlistNames && $wishlistNames[$i - 1])
                            {{ $wishlistNames[$i - 1] }}{{ $guild->current_wishlist_number === $i ? '*' : '' }}
                        @else
                            {{ $i }}{{ $guild->current_wishlist_number === $i ? '*' : '' }}
                        @endif
                    </option>
                @endfor
                <option value="">
                    {{ __("All") }}
                </option>
            </select>
        </li>
        <li class="list-inline-item font-weight-light">
            <span class="text-muted fas fa-fw fa-eye-slash"></span>
            {{ __("Columns") }}
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column-default text-link cursor-pointer">
                {{ __("Defaults") }}
            </span>
        </li>
        @if ($showPrios)
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="1">
                    <span class="text-muted fal fa-fw fa-sort-amount-down"></span>
                    {{ __("Prios") }}
                </span>
            </li>
        @endif
        @if ($showWishlist)
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="2">
                    <span class="text-muted fal fa-fw fa-scroll-old"></span>
                    {{ __("Wishlist") }}
                </span>
            </li>
        @endif
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="3">
                <span class="text-muted fal fa-fw fa-sack"></span>
                {{ __("Received") }}
            </span>
        </li>
        <!--
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="3">
                <span class="text-muted fas fa-fw fa-book"></span>
                {{ __("Recipes") }}
            </span>
        </li>
        -->
        <!--
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="4">
                <span class="text-muted fab fa-fw fa-discord"></span>
                {{ __("Roles") }}
            </span>
        </li>
        -->
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="6">
                <span class="text-muted fal fa-fw fa-comment-alt-lines"></span>
                {{ __("Notes") }}
            </span>
        </li>
        @if (!$guild->is_attendance_hidden)
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <span class="js-sort-by-raids-attended text-link cursor-pointer font-weight-light" data-column="11">
                    <span class="text-muted fal fa-fw fa-helmet-battle"></span>
                    {{ __("Sort By Raid Count") }}
                </span>
            </li>
        @endif
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="js-hide-strikethrough-items text-link cursor-pointer font-weight-light" data-column="6">
                <span class="text-muted fal fa-fw fa-strikethrough"></span>
                {{ __("Hide") }}
                <span class="font-strikethrough">{{ __("received") }}</span>
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="js-show-all-clipped-items text-link cursor-pointer font-weight-light" data-column="6">
                <span class="text-muted fal fa-fw fa-eye"></span>
                {{ __("Show all loot") }}
            </span>
        </li>
    </ul>
</div>

<div class="col-12 pb-3 pr-2 pl-2 rounded">
    <table id="characterTable" class="table table-border table-hover stripe">
    </table>
</div>
