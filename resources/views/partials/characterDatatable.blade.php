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
                @foreach (App\Character::classes() as $class)
                    <option value="{{ $class }}" class="text-{{ strtolower($class) }}-important">
                        {{ $class }}
                    </option>
                @endforeach
            </select>
        </li>

        <li class="list-inline-item font-weight-light">
            <span class="text-muted fas fa-fw fa-eye-slash"></span>
            Columns
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column-default text-link cursor-pointer" href="">
                Defaults
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="1" href="">
                <span class="text-muted fas fa-fw fa-sack"></span>
                Loot Received
            </span>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="2" href="">
                <span class="text-muted fas fa-fw fa-scroll-old"></span>
                Wishlist
            </span>
        </li>
        <!--
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="3" href="">
                <span class="text-muted fas fa-fw fa-book"></span>
                Recipes
            </span>
        </li>
        -->
        <!--
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="4" href="">
                <span class="text-muted fab fa-fw fa-discord"></span>
                Roles
            </span>
        </li>
        -->
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <span class="toggle-column text-link cursor-pointer font-weight-light" data-column="5" href="">
                <span class="text-muted fas fa-fw fa-comment-alt-lines"></span>
                Notes
            </span>
        </li>
    </ul>
</div>

<div class="col-12 pb-3 pr-2 pl-2 rounded">
    <table id="characterTable" class="col-xs-12 table table-border table-hover stripe">
    </table>
</div>
