
<!-- Classic Loot Tables -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-sack text-success"></span>
        <span class="font-weight-normal text-gold">Classic</span> Loot Tables
    </h2>
    <p>
        The raid/boss/loot table data for all Classic WoW raids.
    </p>
    <ul class="list-inline">
        <li class="list-inline-item">
            <a href="{{ route('loot.table', 'classic') }}" target="_blank" class="tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                Download CSV
            </a>
        </li>
    </ul>
</li>
<!-- Burning Crusade Loot Tables -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-sack text-success"></span>
        <span class="font-weight-normal text-uncommon">Burning Crusade</span> Loot Tables
    </h2>
    <p>
        The raid/boss/loot table data for all Burning Crusade raids. (includes Classic loot tables)
    </p>
    <ul class="list-inline">
        <li class="list-inline-item">
            <a href="{{ route('loot.table', 'burning-crusade') }}" target="_blank" class="tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                Download CSV
            </a>
        </li>
    </ul>
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
        <a href="https://github.com/thatsmybis/classic-wow-item-db/tree/master/thatsmybis" target="_blank">https://github.com/thatsmybis/classic-wow-item-db</a>
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
        <a href="https://github.com/thatsmybis/burning-crusade-item-db/tree/main/thatsmybis" target="_blank">https://github.com/thatsmybis/burning-crusade-item-db</a>
    </p>
</li>
