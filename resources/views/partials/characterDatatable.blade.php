@include('partials/loadingBars')
<div class="pr-2 pl-2" style="display:none;" id="characterDatatable">
    <div class="row mb-0 mx-0">
        <div class="mr-3">
            <label for="raid_group_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-helmet-battle"></span>
                {{ __("Raid Group") }}
            </label>
            <select id="raid_group_filter" class="form-control dark selectpicker">
                <option value="">—</option>@foreach ($raidGroups->whereNull('disabled_at') as $raidGroup)<option value="{{ $raidGroup->id }}" style="color:{{ $raidGroup->getColor() }};">{{ $raidGroup->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mr-3">
            <label for="class_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-axe-battle"></span>
                {{ __("Class") }}
            </label>
            <select id="class_filter" class="form-control dark selectpicker">
                <option value="">—</option>@foreach (App\Character::classes($guild->expansion_id) as $key => $class)<option value="{{ $key }}" class="text-{{ strtolower($key) }}-important">{{ $class }}</option>
                @endforeach
            </select>
        </div>
        <div class="mr-3">
            <label for="archetype_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-chess"></span>
                {{ __("Role") }}
            </label>
            <select id="archetype_filter" class="form-control dark selectpicker">
                <option value="">—</option>@foreach (App\Character::extendedArchetypes() as $key => $role)<option value="{{ $key }}" class="text-{{ strtolower($key) }}-important font-weight-medium">{{ $role }}</option>
                @endforeach
            </select>
        </div>
        <div class="mr-3">
            <label for="instance_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-sack"></span>
                {{ __("Dungeon") }}
            </label>
            <select id="instance_filter"
                multiple
                class="form-control dark selectpicker"
                data-actions-box="true"
                data-none-selected-text="—"
                data-deselect-all-text="{{ __('Reset') }}"
                data-select-all-text="{{ __('All') }}">
                @if ($guild->expansion_id == 1)
                    <option value="4">{{ __("Zul'Gurub") }}</option>
                    <option value="5">{{ __("Ruins of Ahn'Qiraj") }}</option>
                    <option value="8">{{ __("World Bosses") }}</option>
                    <option value="1">{{ __("Molten Core") }}</option>
                    <option value="2">{{ __("Onyxia's Lair") }}</option>
                    <option value="3">{{ __("Blackwing Lair") }}</option>
                    <option value="6">{{ __("Temple of Ahn'Qiraj") }}</option>
                    <option value="7">{{ __("Naxxramas") }}</option>
                @elseif ($guild->expansion_id == 2)
                    <option value="9">{{ __("Karazhan") }}</option>
                    <option value="10">{{ __("Gruul's Lair") }}</option>
                    <option value="11">{{ __("Magtheridon's Lair") }}</option>
                    <option value="12">{{ __("Serpentshrine Cavern") }}</option>
                    <option value="14">{{ __("Tempest Keep") }}</option>
                    <option value="13">{{ __("Hyjal Summit") }}</option>
                    <option value="15">{{ __("Black Temple") }}</option>
                    <option value="16">{{ __("Zul'Aman") }}</option>
                    <option value="17">{{ __("Sunwell Plateau") }}</option>
                    <option value="18">{{ __("World Bosses") }}</option>
                @elseif ($guild->expansion_id == 3)
                    <option value="19">{{ __("Naxxramas N10") }}</option>
                    <option value="20">{{ __("Naxxramas N25") }}</option>
                    <option value="21">{{ __("Eye of Eternity N10") }}</option>
                    <option value="22">{{ __("Eye of Eternity N25") }}</option>
                    <option value="23">{{ __("Obsidian Sanctum N10") }}</option>
                    <option value="24">{{ __("Obsidian Sanctum N25") }}</option>
                    <option value="25">{{ __("Vault of Archavon N10") }}</option>
                    <option value="26">{{ __("Vault of Archavon N25") }}</option>
                    <option value="27">{{ __("Ulduar N10") }}</option>
                    <option value="28">{{ __("Ulduar N25") }}</option>
                    <option value="29">{{ __("Trial of the Crusader N10") }}</option>
                    <option value="30">{{ __("Trial of the Crusader N25") }}</option>
                    <option value="31">{{ __("Trial of the Crusader H10") }}</option>
                    <option value="32">{{ __("Trial of the Crusader H25") }}</option>
                    <option value="33">{{ __("Onyxia's Lair N10") }}</option>
                    <option value="34">{{ __("Onyxia's Lair N25") }}</option>
                    <option value="35">{{ __("Icecrown Citadel N10") }}</option>
                    <option value="36">{{ __("Icecrown Citadel N25") }}</option>
                    <option value="37">{{ __("Icecrown Citadel H10") }}</option>
                    <option value="38">{{ __("Icecrown Citadel H25") }}</option>
                    <option value="39">{{ __("Ruby Sanctum N10") }}</option>
                    <option value="40">{{ __("Ruby Sanctum N25") }}</option>
                    <option value="41">{{ __("Ruby Sanctum H10") }}</option>
                    <option value="42">{{ __("Ruby Sanctum H25") }}</option>
                @elseif ($guild->expansion_id == 4)
                    <option value="43">{{ __("World") }}</option>
                    <option value="44">{{ __("Blackfathom Depths") }}</option>
                    <option value="45">{{ __("Gnomregan") }}</option>
                    <option value="46">{{ __("Sunken Temple") }}</option>
                    <option value="59">{{ __("World Bosses") }}</option>
                    <option value="60">{{ __("Molten Core") }}</option>
                    <option value="61">{{ __("Onyxia's Lair") }}</option>
                    <option value="60">{{ __("Molten Core") }}</option>
                    <option value="61">{{ __("Onyxia's Lair") }}</option>
                    <option value="62">{{ __("Zul'Gurub") }}</option>
                    <option value="63">{{ __("Blackwing Lair") }}</option>
                    <option value="64">{{ __("Ruins of Ahn'Qiraj") }}</option>
                    <option value="65">{{ __("Temple of Ahn'Qiraj") }}</option>
                    <option value="66">{{ __("Naxxramas") }}</option>
                    <option value="67">{{ __("Scarlet Enclave") }}</option>
                @elseif ($guild->expansion_id == 5)
                    <option value="47">{{ __("Baradin Hold Normal") }}</option>
                    <option value="48">{{ __("Baradin Hold Heroic") }}</option>
                    <option value="49">{{ __("Throne of the Four Winds Normal") }}</option>
                    <option value="50">{{ __("Throne of the Four Winds Heroic") }}</option>
                    <option value="51">{{ __("Blackwing Descent Normal") }}</option>
                    <option value="52">{{ __("Blackwing Descent Heroic") }}</option>
                    <option value="53">{{ __("The Bastion of Twilight Normal") }}</option>
                    <option value="54">{{ __("The Bastion of Twilight Heroic") }}</option>
                    <option value="55">{{ __("Firelands Normal") }}</option>
                    <option value="56">{{ __("Firelands Heroic") }}</option>
                    <option value="57">{{ __("Dragon Soul Normal") }}</option>
                    <option value="58">{{ __("Dragon Soul Heroic") }}</option>
                @elseif ($guild->expansion_id == 6)
                    <option value="68">{{ __("World Bosses") }}</option>
                    <option value="69">{{ __("Mogu'Shan Vaults Normal") }}</option>
                    <option value="70">{{ __("Mogu'Shan Vaults Heroic") }}</option>
                    <option value="71">{{ __("Heart of Fear Normal") }}</option>
                    <option value="72">{{ __("Heart of Fear Heroic") }}</option>
                    <option value="73">{{ __("Terrace of Endless Spring Normal") }}</option>
                    <option value="74">{{ __("Terrace of Endless Spring Heroic") }}</option>
                    <option value="75">{{ __("Throne of Tunder Normal") }}</option>
                    <option value="76">{{ __("Throne of Tunder Heroic") }}</option>
                    <option value="77">{{ __("Siege of Orgrimmar Flex") }}</option>
                    <option value="78">{{ __("Siege of Orgrimmar Normal") }}</option>
                    <option value="79">{{ __("Siege of Orgrimmar Heroic") }}</option>
                @endif
            </select>
        </div>
        <div class="mr-3">
            @php
                $wishlistNames = $guild->getWishlistNames();
            @endphp
            <label for="wishlist_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-scroll-old"></span>
                {{ __("Wishlist") }}
            </label>
            <select id="wishlist_filter" class="form-control dark selectpicker">
                @for ($i = 1; $i <= App\Http\Controllers\CharacterLootController::MAX_WISHLIST_LISTS; $i++)
                    <option value="{{ $i }}" {{ $guild->current_wishlist_number === $i ? 'selected' : '' }}>@if ($wishlistNames && $wishlistNames[$i - 1]){{ $wishlistNames[$i - 1] }}{{ $guild->current_wishlist_number === $i ? '*' : '' }}
                        @else
                            {{ $i }}{{ $guild->current_wishlist_number === $i ? '*' : '' }}
                        @endif
                    </option>
                @endfor
                <option value="">{{ __("All") }}</option>
            </select>
        </div>
        @if (isset($receivedLootDateFilter) && $receivedLootDateFilter && isset($minReceivedLootDate))
            <div>
                <label for="min_date" class="font-weight-light">
                    <span class="text-muted fas fa-fw fa-sack"></span>
                    {{ __("Received Loot Filter") }}
                </label>
                <div class="small">
                    @include('partials/receivedLootDateFilter')
                </div>
            </div>
        @endif
    </div>
    <ul class="list-inline mb-0 mt-3">
        <!-- Hidden as it doesn't take into account the rest of the filters right now
        <li class="list-inline-item">
            <span class="toggle-column-default text-link cursor-pointer">
                {{ __("Defaults") }}
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        -->
        @if ($showPrios)
            <li class="list-inline-item">
                <span class="toggle-column text-link text-unselectable cursor-pointer font-weight-light" data-column="1">
                    <span class="text-muted fal fa-fw fa-sort-amount-down"></span>
                    {{ __("Prios") }}
                </span>
            </li>
            <li class="list-inline-item">&sdot;</li>
        @endif
        @if ($showWishlist)
            <li class="list-inline-item">
                <span class="toggle-column text-link text-unselectable cursor-pointer font-weight-light" data-column="2">
                    <span class="text-muted fal fa-fw fa-scroll-old"></span>
                    {{ __("Wishlist") }}
                </span>
            </li>
            <li class="list-inline-item">&sdot;</li>
        @endif
        <li class="list-inline-item">
            <span class="toggle-column text-link text-unselectable cursor-pointer font-weight-light" data-column="3">
                <span class="text-muted fal fa-fw fa-sack"></span>
                {{ __("Received") }}
            </span>
        </li>
        <!--
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link text-unselectable cursor-pointer font-weight-light" data-column="3">
                <span class="text-muted fas fa-fw fa-book"></span>
                {{ __("Recipes") }}
            </span>
        </li>
        -->
        <!--
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link text-unselectable cursor-pointer font-weight-light" data-column="4">
                <span class="text-muted fab fa-fw fa-discord"></span>
                {{ __("Roles") }}
            </span>
        </li>
        -->
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link text-unselectable cursor-pointer font-weight-light" data-column="6">
                <span class="text-muted fal fa-fw fa-comment-alt-lines"></span>
                {{ __("Notes") }}
            </span>
        </li>
        @if (!$guild->is_attendance_hidden)
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <span class="js-sort-by-raids-attended text-link text-unselectable cursor-pointer font-weight-light">
                    <span class="text-muted fal fa-fw fa-helmet-battle"></span>
                    {{ __("Sort By Raid Count") }}
                </span>
            </li>
        @endif
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="js-hide-strikethrough-items text-link text-unselectable cursor-pointer font-weight-light">
                <span class="text-muted fal fa-fw fa-strikethrough"></span>
                {{ __("Hide") }}
                <span class="font-strikethrough">{{ __("received") }}</span>
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="js-hide-offspec-items text-link text-unselectable cursor-pointer font-weight-light">
                <span class="text-muted fal fa-fw fa-trash"></span>
                {{ __("Hide OS") }}
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="js-show-all-clipped-items text-link text-unselectable cursor-pointer font-weight-light">
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
