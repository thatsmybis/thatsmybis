@include('partials/loadingBars')
<div class="pr-2 pl-2" style="display:none;" id="itemDatatable">
    <div class="row mb-0 mx-0">
        <div class="mr-3">
            <label for="raid_group_filter font-weight-light">
                <span class="text-muted fas fa-fw fa-helmet-battle"></span>
                {{ __("Raid Group") }}
            </label>
            <select id="raid_group_filter" class="form-control dark">
                <option value="">—</option>
                @if ($raidGroups)
                    @foreach ($raidGroups as $raidGroup)
                        <option value="{{ $raidGroup->id }}" style="color:{{ $raidGroup->getColor() }};">
                            {{ $raidGroup->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
        @if ($guild)
            <div class="mr-3">
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
            </div>
        @endif

        @if (isset($receivedLootDateFilter) && $receivedLootDateFilter && isset($minReceivedLootDate))
            <div class="">
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
        @if ($showPrios)
            <li class="list-inline-item">
                <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="2" href="">
                    <span class="text-muted fal fa-fw fa-eye-slash"></span>
                    {{ __("Prios") }}
                </span>
            </li>
        @endif
        @if ($showWishlist)
            @if ($showPrios)
                <li class="list-inline-item">&sdot;</li>
            @endif
            <li class="list-inline-item">
                <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="3" href="">
                    <span class="text-muted fal fa-fw fa-eye-slash"></span>
                    {{ __("Wishlist") }}
                </span>
            </li>
        @endif
        @if ($showWishlist || $showPrios)
            <li class="list-inline-item">&sdot;</li>
        @endif
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="4" href="">
                <span class="text-muted fal fa-fw fa-eye-slash"></span>
                {{ __("Received") }}
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="5" href="">
                <span class="text-muted fal fa-fw fa-eye-slash"></span>
                {{ __("Notes") }}
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="6" href="">
                <span class="text-muted fal fa-fw fa-eye-slash"></span>
                {{ __("Prio Notes") }}
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="js-hide-offspec-items text-link cursor-pointer font-weight-light" data-column="6">
                <span class="text-muted fal fa-fw fa-eye-slash"></span>
                {{ __("Hide OS") }}
            </span>
        </li>
    </ul>
</div>

<div class="col-12 pb-3 pr-2 pl-2 rounded">
    <table id="itemTable" class="table table-border table-hover stripe">
    </table>
</div>
