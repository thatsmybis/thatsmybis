<div class="pr-2 pl-2">
    <ul class="list-inline mb-0">
        <li class="list-inline-item">
            <label for="raid_group_filter font-weight-light">
                <span class="text-muted fas fa-fw fa-helmet-battle"></span>
                Raid Group
            </label>
            <select id="raid_filter" class="form-control dark">
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

        <li class="list-inline-item font-weight-light">
            <span class="text-muted fas fa-fw fa-eye-slash"></span>
            Columns
        </li>
        @if ($showPrios)
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="2" href="">
                    <span class="text-muted fal fa-fw fa-sort-amount-down"></span>
                    Prio's
                </span>
            </li>
        @endif
        @if ($showWishlist)
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="3" href="">
                    <span class="text-muted fal fa-fw fa-scroll-old"></span>
                    Wishlist
                </span>
            </li>
        @endif
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="4" href="">
                <span class="text-muted fal fa-fw fa-comment-alt-lines"></span>
                Notes
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="5" href="">
                <span class="text-muted fal fa-fw fa-comment-alt-lines"></span>
                Prio Notes
            </span>
        </li>
    </ul>
</div>

<div class="col-12 pb-3 pr-2 pl-2 rounded">
    <table id="itemTable" class="table table-border table-hover stripe">
    </table>
</div>
