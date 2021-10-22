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
            <label for="instance_filter" class="font-weight-light">
                <span class="text-muted fas fa-fw fa-sack"></span>
                {{ __("Item Filter") }}
            </label>
            <select id="instance_filter"
                multiple
                class="form-control dark selectpicker"
                data-actions-box="true"
                data-none-selected-text="—"
                data-deselect-all-text="{{ __('Reset') }}"
                data-select-all-text="{{ __('All') }}">
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
                    <option value="14">
                        {{ __("Tempest Keep") }}
                    </option>
                    <option value="13">
                        {{ __("Hyjal Summit") }}
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
            <span class="js-show-all-items text-link text-unselectable cursor-pointer font-weight-light">
                <span class="text-muted fal fa-fw fa-eye"></span>
                {{ __("Show all loot") }}
            </span>
        </li>
    </ul>
</div>

<div class="col-12 pb-3 pr-2 pl-2 rounded">
    <table id="characterStatsTable" class="table table-border table-hover stripe">
    </table>
</div>
