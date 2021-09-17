<div class="pr-2 pl-2">
    <ul class="list-inline mb-0">
        <li class="list-inline-item">
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
        </li>
        @if ($guild)
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
        @endif

        <li class="list-inline-item font-weight-light">
            <span class="text-muted fas fa-fw fa-eye-slash"></span>
            {{ __("Columns") }}
        </li>
        @if ($showPrios)
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="2" href="">
                    <span class="text-muted fal fa-fw fa-sort-amount-down"></span>
                    {{ __("Prios") }}
                </span>
            </li>
        @endif
        @if ($showWishlist)
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="3" href="">
                    <span class="text-muted fal fa-fw fa-scroll-old"></span>
                    {{ __("Wishlist") }}
                </span>
            </li>
        @endif
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="4" href="">
                <span class="text-muted fal fa-fw fa-sack"></span>
                {{ __("Received") }}
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="5" href="">
                <span class="text-muted fal fa-fw fa-comment-alt-lines"></span>
                {{ __("Notes") }}
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="6" href="">
                <span class="text-muted fal fa-fw fa-comment-alt-lines"></span>
                {{ __("Prio Notes") }}
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="js-hide-strikethrough-items text-link cursor-pointer font-weight-light" data-column="6">
                <span class="text-muted fal fa-fw fa-strikethrough"></span>
                {{ __("Hide") }}
                <span class="font-strikethrough">{{ __("received") }}</span>
            </span>
        </li>
    </ul>
</div>

<div class="col-12 pb-3 pr-2 pl-2 rounded">
    <table id="itemTable" class="table table-border table-hover stripe">
    </table>
</div>
