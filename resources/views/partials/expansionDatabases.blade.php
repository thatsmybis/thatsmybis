
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
<!-- WoTLK Loot Tables -->
<li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-sack text-muted"></span>
        <span class="font-weight-normal text-mage">{{ __("Wrath of The Lich King") }}</span> {{ __("Loot Tables") }}
    </h2>
    <p class="text-warning">
        Loot tables are also not guaranteed 100%; but are a best estimate.
    </p>
    <ul class="list-inline">
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'wotlk', 'type' => 'csv']) }}" target="_blank" class="text-4 tag">
                <span class="fas fa-fw fa-file-csv text-muted"></span>
                {{ __("Download CSV") }}
            </a>
        </li>
        <li class="list-inline-item">
            <a href="{{ route('loot.table', ['expansionSlug' => 'wotlk', 'type' => 'html']) }}" target="_blank" class="text-4 tag">
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
        <span class="font-weight-normal text-mage">{{ __("Season of Discovery") }}</span> {{ __("Loot Tables") }}
    </h2>
    <p class="text-warning">
        Loot tables are also not guaranteed 100%; but are a best estimate.
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
<!-- Catacylsm Loot Tables -->
<!-- <li class="p-3 mb-3 rounded">
    <h2>
        <span class="fas fa-fw fa-sack text-muted"></span>
        <span class="font-weight-normal text-mage">{{ __("Cataclysm) }}</span> {{ __("Loot Tables") }}
    </h2>
    <p class="text-warning">
        Loot tables are also not guaranteed 100%; but are a best estimate.
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
</li> -->
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
                <!-- <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'zulgurub']) }}">
                        {{ __("Zul'Gurub") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'ruins-of-ahnqiraj']) }}">
                        {{ __("Ruins of Ahn'Qiraj") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'world-bosses']) }}">
                        {{ __("World Bosses") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'molten-core']) }}">
                        {{ __("Molten Core") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'onyxias-lair']) }}">
                        {{ __("Onyxia's Lair") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'blackwing-lair']) }}">
                        {{ __("Blackwing Lair") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'temple-of-ahnqiraj']) }}">
                        {{ __("Temple of Ahn'Qiraj") }}
                    </a>
                </li>
                <li class="">
                    <a class="text-4 tag" href="{{ route('loot.list', ['expansionId' => 4, 'instanceSlug' => 'naxxramas']) }}">
                        {{ __("Naxxramas") }}
                    </a>
                </li> -->
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
        <a href="https://github.com/thatsmybis/classic-wow-item-db/" class="text-5" target="_blank">https://github.com/thatsmybis/classic-wow-item-db</a>
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

<li>
    <h2>{{ __("Want more?") }}</h2>

    <p>
        {{ __("View the") }} <a href="{{ route('loot.wishlist', ['expansionName' => 'tbc']) }}">{{ __("live data") }}</a> {{ __("for what people are wishlisting in") }}
        <a href="{{ route('loot.wishlist', ['expansionName' => 'wotlk']) }}" class="text-{{ getExpansionColor(3) }}">{{ __("WoTLK") }}</a>,
        <a href="{{ route('loot.wishlist', ['expansionName' => 'tbc']) }}" class="text-{{ getExpansionColor(2) }}">{{ __("TBC") }}</a>,
        <a href="{{ route('loot.wishlist', ['expansionName' => 'classic']) }}" class="text-{{ getExpansionColor(1) }}">{{ __("Classic") }}</a>,
        {{ __("and") }}
        <a href="{{ route('loot.wishlist', ['expansionName' => 'season-of-discovery']) }}" class="text-{{ getExpansionColor(4) }}">{{ __("Season of Discovery") }}</a>
        <!-- <a href="{{ route('loot.wishlist', ['expansionName' => 'cataclysm']) }}" class="text-{{ getExpansionColor(5) }}">{{ __("Classic") }}</a> -->

    </p>
</li>
