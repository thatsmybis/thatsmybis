
<!-- Classic Loot Tables -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-sack text-muted"></span>
        <span class="font-weight-normal text-gold">{{ __("Classic") }}</span> {{ __("Loot Tables") }}
    </h2>
    <ul class="list-inline">
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'classic', 'type' => 'csv']) }}" target="_blank" class="text-4 tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                {{ __("Download CSV") }}
            </a>
        </li>
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'classic', 'type' => 'html']) }}" target="_blank" class="text-4 tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                {{ __("View CSV") }}
            </a>
        </li>
    </ul>
</li>
<!-- Burning Crusade Loot Tables -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-sack text-muted"></span>
        <span class="font-weight-normal text-uncommon">{{ __("Burning Crusade") }}</span> {{ __("Loot Tables") }}
    </h2>
    <p>
       {{ __("(includes Classic too)") }}
    </p>
    <ul class="list-inline">
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'burning-crusade', 'type' => 'csv']) }}" target="_blank" class="text-4 tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                {{ __("Download CSV") }}
            </a>
        </li>
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'burning-crusade', 'type' => 'html']) }}" target="_blank" class="text-4 tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                {{ __("View CSV") }}
            </a>
        </li>
    </ul>
</li>
<!-- Burning Crusade Loot Tables -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-dungeon text-muted"></span>
        {{ __("Pretty Loot Tables") }}
    </h2>
    <div class="row">
        <div class="col-sm-6 p-4">
            <h2 class="font-weight-bold text-gold">{{ __("Classic Raids") }}</h2>
            <ul class="no-bullet no-indent">
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'zulgurub']) }}">
                        {{ __("Zul'Gurub") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'ruins-of-ahnqiraj']) }}">
                        {{ __("Ruins of Ahn'Qiraj") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'world-bosses']) }}">
                        {{ __("World Bosses") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'molten-core']) }}">
                        {{ __("Molten Core") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'onyxias-lair']) }}">
                        {{ __("Onyxia's Lair") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'blackwing-lair']) }}">
                        {{ __("Blackwing Lair") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'temple-of-ahnqiraj']) }}">
                        {{ __("Temple of Ahn'Qiraj") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'naxxramas']) }}">
                        {{ __("Naxxramas") }}
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-sm-6 p-4">
            <h2 class="font-weight-bold text-uncommon">{{ __("Burning Crusade Raids") }}</h2>
            <ul class="no-bullet no-indent">
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'karazhan']) }}">
                        {{ __("Karazhan") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'gruuls-lair']) }}">
                        {{ __("Gruul's Lair") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'magtheridons-lair']) }}">
                        {{ __("Magtheridon's Lair") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'serpentshrine-cavern']) }}">
                        {{ __("Serpentshrine Cavern") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'hyjal-summit']) }}">
                        {{ __("Hyjal Summit") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'tempest-keep']) }}">
                        {{ __("Tempest Keep") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'black-temple']) }}">
                        {{ __("Black Temple") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'zulaman']) }}">
                        {{ __("Zul'Aman") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'sunwell-plateau']) }}">
                        {{ __("Sunwell Plateau") }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</li>
<!-- Classic Item Database -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-database text-muted"></span>
        <span class="font-weight-normal text-gold">{{ __("Classic") }}</span> {{ __("MySQL Item Database") }}
    </h2>
    <p>
        {{ __("The MySQL item database used by :appName for Classic WoW only.", ['appName' => env('APP_NAME')]) }}
    </p>
    <p>
        <a href="https://github.com/thatsmybis/classic-wow-item-db/tree/master/thatsmybis" class="text-5" target="_blank">https://github.com/thatsmybis/classic-wow-item-db</a>
    </p>
</li>
<!-- TBC Item Database -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-database text-muted"></span>
        <span class="font-weight-normal text-uncommon">Burning Crusade</span> MySQL Item Database
    </h2>
    <p>
        {{ __("The MySQL WoW item database used by :appName for TBC (export includes Classic data too).", ['appName' => env('APP_NAME')]) }}
    </p>
    <p>
        <a href="https://github.com/thatsmybis/burning-crusade-item-db/tree/main/thatsmybis" class="text-5" target="_blank">https://github.com/thatsmybis/burning-crusade-item-db</a>
    </p>
</li>

<li>
    <h2>{{ __("Want more?") }}</h2>

    <p>
        {{ __("View the") }} <a href="{{ route('loot.wishlist', ['expansionId' => 'tbc']) }}">{{ __("live data") }}</a> {{ __("for what people are wishlisting in") }}
        <a href="{{ route('loot.wishlist', ['expansionId' => 'tbc']) }}" class="text-{{ getExpansionColor(2) }}">{{ __("TBC") }}</a>
        {{ __("and") }}
        <a href="{{ route('loot.wishlist', ['expansionId' => 'classic']) }}" class="text-{{ getExpansionColor(1) }}">{{ __("Classic") }}</a>
    </p>
</li>
