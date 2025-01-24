<!-- PRETTY Loot Tables -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-dungeon text-muted"></span>
        {{ __("Pretty Loot Tables") }}
    </h2>
    <div class="row">
        <!-- Cata -->
        <div class="col-sm-6 p-4">
            <h2 class="font-weight-bold text-druid">{{ __("Cataclyms Raids") }}</h2>
            <ul class="no-bullet no-indent">
                <!-- <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 5, 'instanceSlug' => 'baradin-hold-n']) }}">
                        {{ __("Baradin Hold Normal") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 5, 'instanceSlug' => 'baradin-hold-h']) }}">
                        {{ __("Baradin Hold Heroic") }}
                    </a>
                </li> -->
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 5, 'instanceSlug' => 'throne-of-the-four-winds-n']) }}">
                        {{ __("Throne of the Four Winds Normal") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 5, 'instanceSlug' => 'throne-of-the-four-winds-h']) }}">
                        {{ __("Throne of the Four Winds Heroic") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 5, 'instanceSlug' => 'blackwing-descent-n']) }}">
                        {{ __("Blackwing Descent Normal") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 5, 'instanceSlug' => 'blackwing-descent-h']) }}">
                        {{ __("Blackwing Descent Heroic") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 5, 'instanceSlug' => 'the-bastion-of-twilight-n']) }}">
                        {{ __("The Bastion of Twilight Normal") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 5, 'instanceSlug' => 'the-bastion-of-twilight-h']) }}">
                        {{ __("The Bastion of Twilight Heroic") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 5, 'instanceSlug' => 'firelands-n']) }}">
                        {{ __("Firelands Normal") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 5, 'instanceSlug' => 'firelands-h']) }}">
                        {{ __("Firelands Heroic") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 5, 'instanceSlug' => 'dragon-soul-n']) }}">
                        {{ __("Dragon Soul Normal") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 5, 'instanceSlug' => 'dragon-soul-h']) }}">
                        {{ __("Dragon Soul Heroic") }}
                    </a>
                </li>
            </ul>
        </div>

        <!-- SoD -->
        <div class="col-sm-6 p-4">
            <h2 class="font-weight-bold text-gold">{{ __("Season of Discovery Raids") }}</h2>
            <ul class="no-bullet no-indent">
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'blackfathom-depths']) }}">
                        {{ __("Blackfathom Depths") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'gnomeregan']) }}">
                        {{ __("Gnomeregan") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'sunken-temple']) }}">
                        {{ __("Sunken Temple") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'world-bosses-sod']) }}">
                        {{ __("World Bosses") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'molten-core-sod']) }}">
                        {{ __("Molten Core") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'onyxias-lair-sod']) }}">
                        {{ __("Onyxia's Lair") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'zulgurub-sod']) }}">
                        {{ __("Zul'Gurub") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'blackwing-lair-sod']) }}">
                        {{ __("Blackwing Lair") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'ruins-of-ahnqiraj-sod']) }}">
                        {{ __("Ruins of Ahn'Qiraj") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'temple-of-ahnqiraj-sod']) }}">
                        {{ __("Temple of Ahn'Qiraj") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'naxxramas-sod']) }}">
                        {{ __("Naxxramas") }}
                    </a>
                </li>
            </ul>
        </div>

        <!-- Classic -->
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

        <!-- TBC -->
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
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'tempest-keep']) }}">
                        {{ __("Tempest Keep") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 2, 'instanceSlug' => 'hyjal-summit']) }}">
                        {{ __("Hyjal Summit") }}
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

        <!-- WoTLK -->
        <div class="col-sm-6 p-4">
            <h2 class="font-weight-bold text-mage">{{ __("Wrath of The Lich King Raids") }}</h2>
            <ul class="no-bullet no-indent">
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'naxxramas-n10']) }}">
                        {{ __("Naxxramas N10") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'naxxramas-n25']) }}">
                        {{ __("Naxxramas N25") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'eye-of-eternity-n10']) }}">
                        {{ __("Eye of Eternity N10") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'eye-of-eternity-n25']) }}">
                        {{ __("Eye of Eternity N25") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'obsidian-sanctum-n10']) }}">
                        {{ __("Obsidian Sanctum N10") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'obsidian-sanctum-n25']) }}">
                        {{ __("Obsidian Sanctum N25") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'vault-of-archavon-n10']) }}">
                        {{ __("Vault of Archavon N10") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'vault-of-archavon-n25']) }}">
                        {{ __("Vault of Archavon N25") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'ulduar-n10']) }}">
                        {{ __("Ulduar N10") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'ulduar-n25']) }}">
                        {{ __("Ulduar N25") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'trial-of-the-crusader-n10']) }}">
                        {{ __("Trial of the Crusader N10") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'trial-of-the-crusader-25']) }}">
                        {{ __("Trial of the Crusader N25") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'trial-of-the-crusader-h10']) }}">
                        {{ __("Trial of the Crusader H10") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'trial-of-the-crusader-h25']) }}">
                        {{ __("Trial of the Crusader H25") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'onyxias-lair-n10']) }}">
                        {{ __("Onyxia's Lair N10") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'onyxias-lair-n25']) }}">
                        {{ __("Onyxia's Lair N25") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'icecrown-citadel-n10']) }}">
                        {{ __("Icecrown Citadel N10") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'icecrown-citadel-n25']) }}">
                        {{ __("Icecrown Citadel N25") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'icecrown-citadel-h10']) }}">
                        {{ __("Icecrown Citadel H10") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'icecrown-citadel-h25']) }}">
                        {{ __("Icecrown Citadel H25") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'ruby-sanctum-n10']) }}">
                        {{ __("Ruby Sanctum N10") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'ruby-sanctum-n25']) }}">
                        {{ __("Ruby Sanctum N25") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'ruby-sanctum-h10']) }}">
                        {{ __("Ruby Sanctum H10") }}
                    </a>
                </li>
                <li>
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 3, 'instanceSlug' => 'ruby-sanctum-h25']) }}">
                        {{ __("Ruby Sanctum H25") }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</li>

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
<!-- SOD Loot Tables -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-sack text-muted"></span>
        <span class="font-weight-normal text-gold">{{ __("Season of Discovery") }}</span> {{ __("Loot Tables") }}
    </h2>
    <p class="text-warning">
        Loot tables are a best estimate based on available data.
    </p>
    <ul class="list-inline">
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'season-of-discovery', 'type' => 'csv']) }}" target="_blank" class="text-4 tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                {{ __("Download CSV") }}
            </a>
        </li>
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'season-of-discovery', 'type' => 'html']) }}" target="_blank" class="text-4 tag">
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
<!-- WoTLK Loot Tables -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-sack text-muted"></span>
        <span class="font-weight-normal text-mage">{{ __("Wrath of The Lich King") }}</span> {{ __("Loot Tables") }}
    </h2>
    <ul class="list-inline">
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'wrath-of-the-lich-king', 'type' => 'csv']) }}" target="_blank" class="text-4 tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                {{ __("Download CSV") }}
            </a>
        </li>
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'wrath-of-the-lich-king', 'type' => 'html']) }}" target="_blank" class="text-4 tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                {{ __("View CSV") }}
            </a>
        </li>
    </ul>
</li>
<!-- Catacylsm Loot Tables -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-sack text-muted"></span>
        <span class="font-weight-normal text-druid">{{ __("Cataclysm") }}</span> {{ __("Loot Tables") }}
    </h2>
    <p class="text-warning">
        Loot tables are a best estimate based on available data.
    </p>
    <ul class="list-inline">
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'cataclysm', 'type' => 'csv']) }}" target="_blank" class="text-4 tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                {{ __("Download CSV") }}
            </a>
        </li>
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'cataclysm', 'type' => 'html']) }}" target="_blank" class="text-4 tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                {{ __("View CSV") }}
            </a>
        </li>
    </ul>
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
        <a href="https://github.com/thatsmybis/classic-wow-item-db/" class="text-5" target="_blank">https://github.com/thatsmybis/classic-wow-item-db</a>
    </p>
</li>
<!-- Season of Discovery Item Database -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-database text-muted"></span>
        <span class="font-weight-normal text-gold">{{ __("Season of Discovery") }}</span> {{ __("MySQL Item Database") }}
    </h2>
    <p>
        {{ __("The MySQL WoW item database used by :appName for WoTLK (export includes Classic, TBC, and WoTLK data too).", ['appName' => env('APP_NAME')]) }}
    </p>
    <p>
        <a href="https://github.com/thatsmybis/sod-item-db/" class="text-5" target="_blank">https://github.com/thatsmybis/sod-item-db</a>
    </p>
</li>
<!-- TBC Item Database -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-database text-muted"></span>
        <span class="font-weight-normal text-uncommon">{{ __("Burning Crusade") }}</span> {{ __("MySQL Item Database") }}
    </h2>
    <p>
        {{ __("The MySQL WoW item database used by :appName for TBC (export includes Classic data too).", ['appName' => env('APP_NAME')]) }}
    </p>
    <p>
        <a href="https://github.com/thatsmybis/burning-crusade-item-db/" class="text-5" target="_blank">https://github.com/thatsmybis/burning-crusade-item-db</a>
    </p>
</li>
<!-- WoTLK Item Database -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-database text-muted"></span>
        <span class="font-weight-normal text-mage">{{ __("Wrath of the Lich King") }}</span> {{ __("MySQL Item Database") }}
    </h2>
    <p>
        {{ __("The MySQL WoW item database used by :appName for WoTLK (export includes Classic and TBC data too).", ['appName' => env('APP_NAME')]) }}
    </p>
    <p>
        <a href="https://github.com/thatsmybis/wotlk-item-db/" class="text-5" target="_blank">https://github.com/thatsmybis/wotlk-item-db</a>
    </p>
</li>
<!-- Cata Item Database -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-database text-muted"></span>
        <span class="font-weight-normal text-druid">{{ __("Cataclysm") }}</span> {{ __("MySQL Item Database") }}
    </h2>
    <p>
        {{ __("The MySQL WoW item database used by :appName for WoTLK (export includes Classic, TBC, WoTLK, and SOD data too).", ['appName' => env('APP_NAME')]) }}
    </p>
    <p>
        <a href="https://github.com/thatsmybis/cata-item-db/" class="text-5" target="_blank">https://github.com/thatsmybis/cata-item-db</a>
    </p>
</li>

<li>
    <p>
        {{ __("View the") }} <a href="{{ route('loot.wishlist', ['expansionName' => 'tbc']) }}">{{ __("live data") }}</a> {{ __("for what people are wishlisting in") }}
        <a href="{{ route('loot.wishlist', ['expansionName' => 'classic']) }}" class="text-{{ getExpansionColor(1) }}">{{ __("Classic") }}</a>,
        <a href="{{ route('loot.wishlist', ['expansionName' => 'sod']) }}" class="text-{{ getExpansionColor(4) }}">{{ __("Season of Discovery") }}</a>,
        <a href="{{ route('loot.wishlist', ['expansionName' => 'tbc']) }}" class="text-{{ getExpansionColor(2) }}">{{ __("TBC") }}</a>,
        <a href="{{ route('loot.wishlist', ['expansionName' => 'wotlk']) }}" class="text-{{ getExpansionColor(3) }}">{{ __("WoTLK") }}</a>,
        {{ __("and") }}
        <a href="{{ route('loot.wishlist', ['expansionName' => 'cata']) }}" class="text-{{ getExpansionColor(5) }}">{{ __("Cataclysm") }}</a>

    </p>
</li>
