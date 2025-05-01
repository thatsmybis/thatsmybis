@include('partials/loadingBars')
<div class="pr-2 pl-2" style="display:none;" id="characterStatsTableFilters">
    <ul class="list-inline mb-3 mt-3">
        <li class="list-inline-item">
            <span class="js-show-slot-cols js-toggle-column-set btn btn-success font-weight-bold">
                <span class="text-epic fas fa-fw fa-tshirt"></span>
                {{ __("Item Slots") }}
            </span>
        </li>
        @if (!$guild->is_prio_disabled)
            <li class="list-inline-item">
                <span class="js-show-prio-cols js-toggle-column-set btn btn-success font-weight-bold">
                    <span class="text-gold fas fa-fw fa-sort-amount-down"></span>
                    {{ __("Prios") }}
                </span>
            </li>
        @endif
        <li class="list-inline-item">
            <span class="js-show-received-cols js-toggle-column-set btn btn-success font-weight-bold">
                <span class="text-white fas fa-fw fa-sack"></span>
                {{ __("Received") }}
            </span>
        </li>
        @if (!$guild->is_wishlist_disabled)
            <li class="list-inline-item">
                <span class="js-show-wishlist-cols js-toggle-column-set btn btn-success font-weight-bold">
                    <span class="text-legendary fas fa-fw fa-scroll-old"></span>
                    {{ __("Wishlist") }}
                </span>
            </li>
        @endif
    </ul>

    <ul class="list-inline mb-0">
        <li class="list-inline-item">
            <label for="raid_group_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-helmet-battle"></span>
                {{ __("Raid Group") }}
            </label>
            <select id="raid_group_filter" class="form-control dark selectpicker">
                <option value="">—</option>
                @foreach ($raidGroups->whereNull('disabled_at') as $raidGroup)
                    <option value="{{ $raidGroup->id }}" style="color:{{ $raidGroup->getColor() }};">
                        {{ $raidGroup->name }}
                    </option>
                @endforeach
            </select>
        </li>
        <li class="list-inline-item">
            <label for="class_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-axe-battle"></span>
                {{ __("Class") }}
            </label>
            <select id="class_filter" class="form-control dark selectpicker">
                <option value="">—</option>
                @foreach (App\Character::classes($guild->expansion_id) as $key => $class)
                    <option value="{{ $key }}" class="text-{{ strtolower($key) }}-important">
                        {{ $class }}
                    </option>
                @endforeach
            </select>
        </li>
        <li class="list-inline-item">
            <label for="archetype_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-chess"></span>
                {{ __("Role") }}
            </label>
            <select id="archetype_filter" class="form-control dark selectpicker">
                <option value="">—</option>
                @foreach (App\Character::extendedArchetypes() as $key => $role)
                    <option value="{{ $key }}" class="text-{{ strtolower($key) }}-important font-weight-medium">
                        {{ $role }}
                    </option>
                @endforeach
            </select>
        </li>
        <li class="list-inline-item">
            <label for="archetype_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-chess"></span>
                {{ __("Role") }}
            </label>
            <select id="archetype_filter" class="form-control dark selectpicker">
                <option value="">—</option>@foreach (App\Character::extendedArchetypes() as $key => $role)<option value="{{ $key }}" class="text-{{ strtolower($key) }}-important font-weight-medium">{{ $role }}</option>
                @endforeach
            </select>
        </li>
        <li class="list-inline-item">
            <label for="instance_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-dungeon"></span>
                {{ __("Dungeon Filter") }}
            </label>
            <select id="instance_filter"
                multiple
                autocomplete="off"
                class="form-control dark selectpicker"
                data-actions-box="true"
                data-none-selected-text="—"
                data-deselect-all-text="{{ __('Reset') }}"
                data-select-all-text="{{ __('All') }}"
            >
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
                    <option value="77">{{ __("Seige of Orgrimmar Flex") }}</option>
                    <option value="78">{{ __("Seige of Orgrimmar Normal") }}</option>
                    <option value="79">{{ __("Seige of Orgrimmar Heroic") }}</option>
                @endif
            </select>
        </li>
        <li id="loot_type_container" class="list-inline-item">
            <label for="loot_type" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-sack"></span>
                {{ __("Loot Type") }}
            </label>
            <select id="loot_type" class="form-control dark selectpicker" autocomplete="off">
                <option value="received">{{ __("Received") }}</option>
                <option value="prios">{{ __("Prios") }}</option>
                <option value="wishlist">{{ __("Wishlist") }}</option>
            </select>
        </li>
        @if (isset($receivedLootDateFilter) && $receivedLootDateFilter && isset($minReceivedLootDate))
            <li class="list-inline-item">
                <label for="min_date" class="font-weight-light">
                    <span class="text-muted fas fa-fw fa-sack"></span>
                    {{ __("Received Loot Filter") }}
                </label>
                <div class="small">
                    @include('partials/receivedLootDateFilter')
                </div>
            </li>
        @endif
    </ul>
    <ul class="list-inline mb-0 mt-3">
        <li class="list-inline-item">
            <span class="js-toggle-column text-link text-unselectable cursor-pointer font-weight-light" data-column="4">
                <span class="text-muted fal fa-fw fa-comment-alt-lines"></span>
                {{ __("Notes") }}
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="js-hide-strikethrough-items text-link text-unselectable cursor-pointer font-weight-light">
                <span class="text-muted fal fa-fw fa-strikethrough"></span>
                <span id="hide_received_label">{{ __("Hide received") }}</span>
                <span id="show_received_label" style="display:none;">{{ __("Show received") }}</span>
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="js-hide-offspec-items text-link text-unselectable cursor-pointer font-weight-light">
                <span class="text-muted fal fa-fw fa-trash"></span>
                <span id="hide_os_label">{{ __("Hide OS") }}</span>
                <span id="show_os_label" style="display:none;">{{ __("Show OS") }}</span>
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="js-show-all-items text-link text-unselectable cursor-pointer font-weight-light">
                <span class="text-muted fal fa-fw fa-eye"></span>
                <span id="show_items_label">{{ __("Show items") }}</span>
                <span id="hide_items_label" style="display:none;">{{ __("Hide items") }}</span>
            </span>
        </li>
    </ul>
</div>

<div class="col-12 pb-3 pr-2 pl-2 rounded">
    <table id="characterStatsTable" class="table table-border table-hover stripe col-borders th-bottom-border">
    </table>
</div>
