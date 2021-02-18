
<!-- Classic Loot Tables -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-sack text-muted"></span>
        <span class="font-weight-normal text-gold">Classic</span> Loot Tables CSV
    </h2>
    <p>
        For ALL of Classic WoW.
    </p>
    <ul class="list-inline">
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'classic', 'type' => 'csv']) }}" target="_blank" class="text-4 tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                Download CSV
            </a>
        </li>
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'classic', 'type' => 'html']) }}" target="_blank" class="text-4 tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                View CSV
            </a>
        </li>
    </ul>
</li>
<!-- Burning Crusade Loot Tables -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-sack text-muted"></span>
        <span class="font-weight-normal text-uncommon">Burning Crusade</span> Loot Tables CSV
    </h2>
    <p>
       For ALL of Burning Crusade. (includes Classic too)
    </p>
    <ul class="list-inline">
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'burning-crusade', 'type' => 'csv']) }}" target="_blank" class="text-4 tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                Download CSV
            </a>
        </li>
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'burning-crusade', 'type' => 'html']) }}" target="_blank" class="text-4 tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                View CSV
            </a>
        </li>
    </ul>
</li>
<!-- Burning Crusade Loot Tables -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-dungeon text-muted"></span>
        Pretty Loot Tables
    </h2>
    <div class="row">
        <div class="col-xs-6 p-4">
            <h2 class="font-weight-bold text-gold">Classic Raids</h2>
            <ul class="no-bullet no-indent">
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'zulgurub', 'type' => 'csv']) }}" target="_blank">
                        Zul'Gurub
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'ruins-of-ahnqiraj', 'type' => 'csv']) }}" target="_blank">
                        Ruins of Ahn'Qiraj
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'world-bosses', 'type' => 'csv']) }}" target="_blank">
                        World Bosses
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'molten-core', 'type' => 'csv']) }}" target="_blank">
                        Molten Core
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'onyxias-lair', 'type' => 'csv']) }}" target="_blank">
                        Onyxia's Lair
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'blackwing-lair', 'type' => 'csv']) }}" target="_blank">
                        Blackwing Lair
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'temple-of-ahnqiraj', 'type' => 'csv']) }}" target="_blank">
                        Temple of Ahn'Qiraj
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 1, 'instanceSlug' => 'naxxramas', 'type' => 'csv']) }}" target="_blank">
                        Naxxramas
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-xs-6 p-4">
            <h2 class="font-weight-bold text-uncommon">Burning Crusade Raids</h2>
            <ul class="no-bullet no-indent">
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'karazhan', 'type' => 'csv']) }}" target="_blank">
                        Karazhan
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'gruuls-lair', 'type' => 'csv']) }}" target="_blank">
                        Gruul's Lair
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'magtheridons-lair', 'type' => 'csv']) }}" target="_blank">
                        Magtheridon's Lair
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'serpentshrine-cavern', 'type' => 'csv']) }}" target="_blank">
                        Serpentshrine Cavern
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'hyjal-summit', 'type' => 'csv']) }}" target="_blank">
                        Hyjal Summit
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'tempest-keep', 'type' => 'csv']) }}" target="_blank">
                        Tempest Keep
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'black-temple', 'type' => 'csv']) }}" target="_blank">
                        Black Temple
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'zulaman', 'type' => 'csv']) }}" target="_blank">
                        Zul'Aman
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'sunwell-plateau', 'type' => 'csv']) }}" target="_blank">
                        Sunwell Plateau
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
        <span class="font-weight-normal text-gold">Classic</span> MySQL Item Database
    </h2>
    <p>
        The MySQL item database used by {{ env('APP_NAME') }} for Classic WoW only.
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
        The MySQL WoW item database used by {{ env('APP_NAME') }} for TBC (export includes Classic data too).
    </p>
    <p>
        <a href="https://github.com/thatsmybis/burning-crusade-item-db/tree/main/thatsmybis" class="text-5" target="_blank">https://github.com/thatsmybis/burning-crusade-item-db</a>
    </p>
</li>
