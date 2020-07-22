<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertAq40ItemItemSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tie AQ40 loot to AQ40 bosses
        DB::insert("INSERT INTO `item_item_sources` (`item_source_id`, `item_id`)
            VALUES
                -- Mounts 45
                (45, 21218), -- blue-qiraji-resonating-crystal
                (45, 21323), -- green-qiraji-resonating-crystal
                (45, 21324), -- yellow-qiraji-resonating-crystal
                (45, 21321), -- red-qiraji-resonating-crystal

                -- Enchants 46
                (46, 20736), -- formula-enchant-cloak-dodge
                (46, 20734), -- formula-enchant-cloak-stealth
                (46, 20729), -- formula-enchant-gloves-fire-power
                (46, 20728), -- formula-enchant-gloves-frost-power
                (46, 20730), -- formula-enchant-gloves-healing-power
                (46, 20727), -- formula-enchant-gloves-shadow-power
                (46, 20731), -- formula-enchant-gloves-superior-agility

                -- Trash 47
                (47, 21837), -- anubisath-warhammer
                (47, 21838), -- garb-of-royal-ascension
                (47, 21888), -- gloves-of-the-immortal
                (47, 21889), -- gloves-of-the-redeemed-prophecy
                (47, 21856), -- neretzek-the-blood-drinker
                (47, 21836), -- ritssyns-ring-of-chaos
                (47, 21891), -- shard-of-the-fallen-star

                -- Tokens 48
                (48, 20929), -- carapace-of-the-old-god
                (48, 20933), -- husk-of-the-old-god
                (48, 21232), -- imperial-qiraji-armaments
                (48, 21237), -- imperial-qiraji-regalia
                (48, 20927), -- ouros-intact-hide
                (48, 20928), -- qiraji-bindings-of-command
                (48, 20932), -- qiraji-bindings-of-dominance
                (48, 20931), -- skin-of-the-great-sandworm
                (48, 20930), -- veklors-diadem
                (48, 20926), -- veknilashs-circlet

                -- The Prophet Skeram 49
                (49, 21702), -- amulet-of-foul-warding
                (49, 21699), -- barrage-shoulders
                (49, 21708), -- beetle-scaled-wristguards
                (49, 21705), -- boots-of-the-fallen-prophet
                (49, 21704), -- boots-of-the-redeemed-prophecy
                (49, 21706), -- boots-of-the-unwavering-will
                (49, 21814), -- breastplate-of-annihilation
                (49, 21701), -- cloak-of-concentrated-hatred
                (49, 21703), -- hammer-of-jizhi
                (49, 21698), -- leggings-of-immersion
                (49, 21700), -- pendant-of-the-qiraji-guardian
                (49, 22222), -- plans-thick-obsidian-breastplate
                (49, 21707), -- ring-of-swarming-thought
                (49, 21128), -- staff-of-the-qiraji-prophets

                -- Bug Trio 50
                (50, 21690), -- angelistas-charm
                (50, 21695), -- angelistas-touch
                (50, 21682), -- bile-covered-gauntlets
                (50, 21688), -- boots-of-the-fallen-hero
                (50, 21697), -- cape-of-the-trinity
                (50, 21689), -- gloves-of-ebru
                (50, 21693), -- guise-of-the-devourer
                (50, 21686), -- mantle-of-phrenic-power
                (50, 21683), -- mantle-of-the-desert-crusade
                (50, 21684), -- mantle-of-the-deserts-fury
                (50, 21691), -- ooze-ridden-gauntlets
                (50, 21685), -- petrified-scarab
                (50, 21681), -- ring-of-the-devoured
                (50, 21696), -- robes-of-the-triumvirate
                (50, 21694), -- ternary-mantle
                (50, 21692), -- triad-girdle
                (50, 21687), -- ukkos-ring-of-darkness
                (50, 21680), -- vest-of-swift-execution
                (50, 21603), -- wand-of-qiraji-nobility

                -- Battleguard Sartura 51
                (51, 21670), -- badge-of-the-swarmguard
                (51, 21669), -- creeping-vine-helm
                (51, 21674), -- gauntlets-of-steadfast-determination
                (51, 21672), -- gloves-of-enforcement
                (51, 21676), -- leggings-of-the-festering-swarm
                (51, 21667), -- legplates-of-blazing-light
                (51, 21678), -- necklace-of-purity
                (51, 21648), -- recomposed-boots
                (51, 21671), -- robes-of-the-battleguard
                (51, 21666), -- sarturas-might
                (51, 21668), -- scaled-leggings-of-qiraji-fury
                (51, 21673), -- silithid-claw
                (51, 21675), -- thick-qirajihide-belt

                -- Fankriss the Unyielding 52
                (52, 21650), -- ancient-qiraji-ripper
                (52, 21635), -- barb-of-the-sand-reaver
                (52, 21664), -- barbed-choker
                (52, 21627), -- cloak-of-untold-secrets
                (52, 21647), -- fetish-of-the-sand-reaver
                (52, 21645), -- hive-tunnelers-boots
                (52, 22402), -- libram-of-grace
                (52, 21665), -- mantle-of-wicked-revenge
                (52, 21639), -- pauldrons-of-the-unrelenting
                (52, 21663), -- robes-of-the-guardian-saint
                (52, 21651), -- scaled-sand-reaver-leggings
                (52, 21652), -- silithid-carapace-chestguard
                (52, 22396), -- totem-of-life

                -- Viscidus 53
                (53, 21624), -- gauntlets-of-kalimdor
                (53, 21623), -- gauntlets-of-the-righteous-champion
                (53, 22399), -- idol-of-health
                (53, 20928), -- qiraji-bindings-of-command
                (53, 20932), -- qiraji-bindings-of-dominance
                (53, 21677), -- ring-of-the-qiraji-fury
                (53, 21625), -- scarab-brooch
                (53, 21622), -- sharpened-silithid-femur
                (53, 21626), -- slime-coated-leggings

                -- Princess Huhuran 54
                (54, 21621), -- cloak-of-the-golden-hive
                (54, 21619), -- gloves-of-the-messiah
                (54, 21618), -- hive-defiler-wristguards
                (54, 21616), -- huhurans-stinger
                (54, 20928), -- qiraji-bindings-of-command
                (54, 20932), -- qiraji-bindings-of-dominance
                (54, 21620), -- ring-of-the-martyr
                (54, 21617), -- wasphide-gauntlets

                -- Twin Emperors 55
                (55, 21608), -- amulet-of-veknilash
                (55, 21606), -- belt-of-the-fallen-emperor
                (55, 21600), -- boots-of-epiphany
                (55, 21604), -- bracelets-of-royal-redemption
                (55, 21605), -- gloves-of-the-hidden-temple
                (55, 21607), -- grasp-of-the-fallen-emperor
                (55, 21679), -- kalimdors-revenge
                (55, 21602), -- qiraji-execution-bracers
                (55, 21609), -- regenerating-belt-of-veknilash
                (55, 21601), -- ring-of-emperor-veklor
                (55, 21598), -- royal-qiraji-belt
                (55, 21597), -- royal-scepter-of-veklor
                (55, 20930), -- veklors-diadem
                (55, 21599), -- veklors-gloves-of-devastation
                (55, 20926), -- veknilashs-circlet

                -- Ouro 56
                (56, 21611), -- burrower-bracers
                (56, 21615), -- don-rigobertos-lost-hat
                (56, 23570), -- jom-gabbar
                (56, 23557), -- larvae-of-the-great-worm
                (56, 20927), -- ouros-intact-hide
                (56, 20931), -- skin-of-the-great-sandworm
                (56, 23558), -- the-burrowers-shell
                (56, 21610), -- wormscale-blocker

                -- C'Thun 57
                (57, 21586), -- belt-of-never-ending-agony
                (57, 20929), -- carapace-of-the-old-god
                (57, 21583), -- cloak-of-clarity
                (57, 22731), -- cloak-of-the-devoured
                (57, 21134), -- dark-edge-of-insanity
                (57, 21585), -- dark-storm-gauntlets
                (57, 21126), -- deaths-sting
                (57, 21221), -- eye-of-cthun
                (57, 22730), -- eyestalk-waist-cord
                (57, 21581), -- gauntlets-of-annihilation
                (57, 21582), -- grasp-of-the-old-god
                (57, 20933), -- husk-of-the-old-god
                (57, 22732), -- mark-of-cthun
                (57, 21596), -- ring-of-the-godslayer
                (57, 21839), -- scepter-of-the-false-prophet
                (57, 21579); -- vanquished-tentacle-of-cthun
            ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
