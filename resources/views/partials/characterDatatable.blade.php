<div>
    <ul class="list-inline">
        <li class=" list-inline-item">
            <label for="raid_filter font-weight-light">
                <span class="text-muted fas fa-fw fa-users-crown"></span>
                Raid
            </label>
            <select id="raid_filter" class="form-control">
                <option value="" class="bg-tag">—</option>
                @foreach ($raids as $raid)
                    <option value="{{ $raid->name }}" class="bg-tag" style="color:{{ $raid->getColor() }};">
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
            <select id="class_filter" class="form-control">
                <option value="" class="bg-tag">—</option>
                @foreach (App\Character::classes() as $class)
                    <option value="{{ $class }}" class="bg-tag text-{{ strtolower($class) }}-important">
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
            <a class="toggle-column-default cursor-pointer" href="">Defaults</a>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <a class="toggle-column cursor-pointer font-weight-light" data-column="1" href="">
                <span class="text-muted fas fa-fw fa-sack"></span>
                Loot Received
            </a>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <a class="toggle-column cursor-pointer font-weight-light" data-column="2" href="">
                <span class="text-muted fas fa-fw fa-scroll-old"></span>
                Wishlist
            </a>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <a class="toggle-column cursor-pointer font-weight-light" data-column="3" href="">
                <span class="text-muted fas fa-fw fa-book"></span>
                Recipes
            </a>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <a class="toggle-column cursor-pointer font-weight-light" data-column="4" href="">
                <span class="text-muted fab fa-fw fa-discord"></span>
                Roles
            </a>
        </li>
        <li class="list-inline-item">&sdot;</li>
        <li class="list-inline-item">
            <a class="toggle-column cursor-pointer font-weight-light" data-column="5" href="">
                <span class="text-muted fas fa-fw fa-comment-alt-lines"></span>
                Notes
            </a>
        </li>
    </ul>
</div>

<div class="col-12 pb-3 pr-3 pl-3 rounded">
    <table id="characterTable" class="col-xs-12 table table-border table-hover stripe">
    </table>
</div>
