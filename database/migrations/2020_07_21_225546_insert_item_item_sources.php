<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertItemItemSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tie MC loot to MC bosses
        DB::insert("INSERT INTO `item_item_sources` (`item_source_id`, `item_id`)
            VALUES
                -- 1 Recipes
                (1, 18260), -- formula-enchant-weapon-healing-power
                (1, 18259), -- formula-enchant-weapon-spell-power
                (1, 18252), -- pattern-core-armor-kit
                (1, 21371), -- pattern-core-felcloth-bag
                (1, 18265), -- pattern-flarecore-wraps
                (1, 18264), -- plans-elemental-sharpening-stone
                (1, 18257), -- recipe-major-rejuvenation-potion
                (1, 18290), -- schematic-biznicks-247x128-accurascope
                (1, 18292), -- schematic-core-marksman-rifle
                (1, 18291), -- schematic-force-reactive-disk

                -- 2 Trash
                (2, 16802), -- arcanist-belt
                (2, 16799), -- arcanist-bindings
                (2, 16864), -- belt-of-might
                (2, 16861), -- bracers-of-might
                (2, 16828), -- cenarion-belt
                (2, 16830), -- cenarion-bracers
                (2, 16838), -- earthfury-belt
                (2, 16840), -- earthfury-bracers
                (2, 16806), -- felheart-belt
                (2, 16804), -- felheart-bracers
                (2, 16851), -- giantstalkers-belt
                (2, 16850), -- giantstalkers-bracers
                (2, 16817), -- girdle-of-prophecy
                (2, 16858), -- lawbringer-belt
                (2, 16857), -- lawbringer-bracers
                (2, 16827), -- nightslayer-belt
                (2, 16825), -- nightslayer-bracelets
                (2, 16819), -- vambraces-of-prophecy

                -- 3 Shared
                (3, 18823), -- aged-core-leather-gloves
                (3, 17077), -- crimson-shocker
                (3, 18829), -- deep-earth-spaulders
                (3, 19142), -- fire-runed-grimoire
                (3, 19143), -- flameguard-gauntlets
                (3, 18861), -- flamewaker-legplates
                (3, 18879), -- heavy-dark-iron-ring
                (3, 18824), -- magma-tempered-boots
                (3, 19136), -- mana-igniting-cord
                (3, 18872), -- manastorm-leggings
                (3, 18822), -- obsidian-edged-blade
                (3, 18821), -- quick-strike-ring
                (3, 19147), -- ring-of-spell-power
                (3, 19145), -- robe-of-volatile-power
                (3, 19144), -- sabatons-of-the-flamewalker
                (3, 18875), -- salamander-scale-pants
                (3, 18878), -- sorcerous-dagger
                (3, 18820), -- talisman-of-ephemeral-power
                (3, 19146), -- wristguards-of-stability

                -- 4 Lucifron
                (4, 16800), -- arcanist-boots
                (4, 16829), -- cenarion-boots
                (4, 17109), -- choker-of-enlightenment
                (4, 16837), -- earthfury-boots
                (4, 16805), -- felheart-gloves
                (4, 16863), -- gauntlets-of-might
                (4, 16859), -- lawbringer-boots
                (4, 16665), -- tome-of-tranquilizing-shot

                -- 5 Magmadar
                (5, 16796), -- arcanist-leggings
                (5, 16835), -- cenarion-leggings
                (5, 16843), -- earthfury-legguards
                (5, 17073), -- earthshaker
                (5, 18203), -- eskhandars-right-claw
                (5, 16810), -- felheart-pants
                (5, 16847), -- giantstalkers-leggings
                (5, 16855), -- lawbringer-legplates
                (5, 16867), -- legplates-of-might
                (5, 17065), -- medallion-of-steadfast-might
                (5, 16822), -- nightslayer-pants
                (5, 16814), -- pants-of-prophecy
                (5, 17069), -- strikers-mark

                -- 6 Gehennas
                (6, 16839), -- earthfury-gauntlets
                (6, 16849), -- giantstalkers-boots
                (6, 16812), -- gloves-of-prophecy
                (6, 16860), -- lawbringer-gauntlets
                (6, 16826), -- nightslayer-gloves
                (6, 16862), -- sabatons-of-might

                -- 7 Garr
                (7, 16795), -- arcanist-crown
                (7, 17105), -- aurastone-hammer
                (7, 18564), -- bindings-of-the-windseeker
                (7, 18832), -- brutality-blade
                (7, 16834), -- cenarion-helm
                (7, 16813), -- circlet-of-prophecy
                (7, 17066), -- drillborer-disk
                (7, 16842), -- earthfury-helmet
                (7, 16808), -- felheart-horns
                (7, 16846), -- giantstalkers-helmet
                (7, 17071), -- gutgore-ripper
                (7, 16866), -- helm-of-might
                (7, 16854), -- lawbringer-helm
                (7, 16821), -- nightslayer-cover

                -- 8 Shazzrah
                (8, 16801), -- arcanist-gloves
                (8, 16811), -- boots-of-prophecy
                (8, 16831), -- cenarion-gloves
                (8, 16803), -- felheart-slippers
                (8, 16852), -- giantstalkers-gloves
                (8, 16824), -- nightslayer-boots

                -- 9 Baron Geddon
                (9, 16797), -- arcanist-mantle
                (9, 18563), -- bindings-of-the-windseeker
                (9, 16836), -- cenarion-spaulders
                (9, 16844), -- earthfury-epaulets
                (9, 16807), -- felheart-shoulder-pads
                (9, 16856), -- lawbringer-spaulders
                (9, 17110), -- seal-of-the-archmagus
                (9, 17782), -- talisman-of-binding-shard

                -- 10 Golemagg the Incinerator
                (10, 16798), -- arcanist-robes
                (10, 17103), -- azuresong-mageblade
                (10, 17072), -- blastershot-launcher
                (10, 16865), -- breastplate-of-might
                (10, 16833), -- cenarion-vestments
                (10, 16841), -- earthfury-vestments
                (10, 16809), -- felheart-robes
                (10, 16845), -- giantstalkers-breastplate
                (10, 16853), -- lawbringer-chestguard
                (10, 16820), -- nightslayer-chestpiece
                (10, 16820), -- nightslayer-chestpiece
                (10, 16815), -- robes-of-prophecy
                (10, 18842), -- staff-of-dominance

                -- 11 Sulfuron Harbinger
                (11, 16848), -- giantstalkers-epaulets
                (11, 16816), -- mantle-of-prophecy
                (11, 16823), -- nightslayer-shoulder-pads
                (11, 16868), -- pauldrons-of-might
                (11, 17074), -- shadowstrike

                -- 12 Majordomo Executus
                (12, 18703), -- ancient-petrified-leaf
                (12, 19140), -- cauterizing-band
                (12, 18806), -- core-forged-greaves
                (12, 18805), -- core-hound-tooth
                (12, 18803), -- finkles-lava-dredger
                (12, 19139), -- fireguard-shoulders
                (12, 18811), -- fireproof-cloak
                (12, 18808), -- gloves-of-the-hypnotic-flame
                (12, 18809), -- sash-of-whispered-secrets
                (12, 18646), -- the-eye-of-divinity
                (12, 18810), -- wild-growth-spaulders
                (12, 18812), -- wristguards-of-true-flight

                -- 13 Ragnaros
                (13, 17063), -- band-of-accuria
                (13, 19138), -- band-of-sulfuras
                (13, 16909), -- bloodfang-pants
                (13, 17076), -- bonereavers-edge
                (13, 18814), -- choker-of-the-fire-lord
                (13, 17102), -- cloak-of-the-shrouded-mists
                (13, 18817), -- crown-of-destruction
                (13, 17107), -- dragons-blood-cape
                (13, 16938), -- dragonstalkers-legguards
                (13, 18815), -- essence-of-the-pure-flame
                (13, 17204), -- eye-of-sulfuras
                (13, 16954), -- judgement-legplates
                (13, 16922), -- leggings-of-transcendence
                (13, 16946), -- legplates-of-ten-storms
                (13, 16962), -- legplates-of-wrath
                (13, 17106), -- malistars-defender
                (13, 16930), -- nemesis-leggings
                (13, 16915), -- netherwind-pants
                (13, 19137), -- onslaught-girdle
                (13, 18816), -- perditions-blade
                (13, 17082), -- shard-of-the-flame
                (13, 17104), -- spinal-reaper
                (13, 16901); -- stormrage-legguards");

        // Tie Onyxia loot to Onyxia
        DB::insert("INSERT INTO `item_item_sources` (`item_source_id`, `item_id`)
            VALUES
                (14, 17067), -- ancient-cornerstone-grimoire
                (14, 16908), -- bloodfang-hood
                (14, 17068), -- deathbringer
                (14, 16939), -- dragonstalkers-helm
                (14, 18205), -- eskhandars-collar
                (14, 16921), -- halo-of-transcendence
                (14, 18422), -- head-of-onyxia
                (14, 16963), -- helm-of-wrath
                (14, 16947), -- helmet-of-ten-storms
                (14, 16955), -- judgement-crown
                (14, 18705), -- mature-black-dragon-sinew
                (14, 16929), -- nemesis-skullcap
                (14, 16914), -- netherwind-crown
                (14, 17966), -- onyxia-hide-backpack
                (14, 18813), -- ring-of-binding
                (14, 17078), -- sapphiron-drape
                (14, 17064), -- shard-of-the-scale
                (14, 16900), -- stormrage-cover
                (14, 17075); -- viskag-the-bloodletter");

        // Tie BWL loot to BWL bosses
        DB::insert("INSERT INTO `item_item_sources` (`item_source_id`, `item_id`)
            VALUES
                -- 15 Trash
                (15, 19434), -- band-of-dark-dominion
                (15, 19437), -- boots-of-pure-thought
                (15, 19436), -- cloak-of-draconic-might
                (15, 19362), -- dooms-edge
                (15, 19354), -- draconic-avenger
                (15, 19358), -- draconic-maul
                (15, 19435), -- essence-gatherer
                (15, 19439), -- interlaced-shadow-jerkin
                (15, 19438), -- ringos-blizzard-boots

                -- 16 Razorgore
                (16, 19336), -- arcane-infused-gem
                (16, 16926), -- bindings-of-transcendence
                (16, 16911), -- bloodfang-bracers
                (16, 16959), -- bracelets-of-wrath
                (16, 16943), -- bracers-of-ten-storms
                (16, 16935), -- dragonstalkers-bracers
                (16, 19369), -- gloves-of-rapid-evolution
                (16, 16951), -- judgement-bindings
                (16, 19370), -- mantle-of-the-blackwing-cabal
                (16, 16934), -- nemesis-bracers
                (16, 16918), -- netherwind-bindings
                (16, 19335), -- spineshatter
                (16, 16904), -- stormrage-bracers
                (16, 19337), -- the-black-book
                (16, 19334), -- the-untamed-blade

                -- 17 Vael
                (17, 16944), -- belt-of-ten-storms
                (17, 16925), -- belt-of-transcendence
                (17, 16910), -- bloodfang-belt
                (17, 19346), -- dragonfang-blade
                (17, 16936), -- dragonstalkers-belt
                (17, 19372), -- helm-of-endless-rage
                (17, 16952), -- judgement-belt
                (17, 19339), -- mind-quickening-gem
                (17, 16933), -- nemesis-belt
                (17, 16818), -- netherwind-belt
                (17, 19371), -- pendant-of-the-fallen-dragon
                (17, 19348), -- red-dragonscale-protector
                (17, 19340), -- rune-of-metamorphosis
                (17, 16903), -- stormrage-belt
                (17, 16960), -- waistband-of-wrath

                -- 18 Broodlord
                (18, 19373), -- black-brood-pauldrons
                (18, 16906), -- bloodfang-boots
                (18, 16919), -- boots-of-transcendence
                (18, 19374), -- bracers-of-arcane-accuracy
                (18, 16941), -- dragonstalkers-greaves
                (18, 16949), -- greaves-of-ten-storms
                (18, 19350), -- heartstriker
                (18, 16957), -- judgement-sabatons
                (18, 19341), -- lifegiving-gem
                (18, 19351), -- maladath-runed-blade-of-the-black-flight
                (18, 16927), -- nemesis-boots
                (18, 16912), -- netherwind-boots
                (18, 16965), -- sabatons-of-wrath
                (18, 16898), -- stormrage-boots
                (18, 19342), -- venomous-totem

                -- 19 Firemaw
                (19, 19399), -- black-ash-robe
                (19, 19365), -- claw-of-the-black-drake
                (19, 19398), -- cloak-of-firemaw
                (19, 19400), -- firemaws-clutch
                (19, 19402), -- legguards-of-the-fallen-crusader
                (19, 19344), -- natural-alignment-crystal
                (19, 19401), -- primalists-linked-legguards
                (19, 19343), -- scrolls-of-blinding-light

                -- 20 Ebonroc
                (20, 19345), -- aegis-of-preservation
                (20, 19403), -- band-of-forced-concentration
                (20, 19368), -- dragonbreath-hand-cannon
                (20, 19406), -- drake-fang-talisman
                (20, 19407), -- ebony-flame-gloves
                (20, 19405), -- malfurions-blessed-bulwark

                -- 21 Flamegor
                (21, 19432), -- circle-of-applied-force
                (21, 19367), -- dragons-touch
                (21, 19433), -- emberweave-leggings
                (21, 19357), -- herald-of-woe
                (21, 19430), -- shroud-of-pure-thought
                (21, 19431), -- styleens-impeding-scarab

                -- 22 Drakes Shared Loot
                (22, 16907), -- bloodfang-gloves
                (22, 16940), -- dragonstalkers-gauntlets
                (22, 19353), -- drake-talon-cleaver
                (22, 19394), -- drake-talon-pauldrons
                (22, 16948), -- gauntlets-of-ten-storms
                (22, 16964), -- gauntlets-of-wrath
                (22, 16920), -- handguards-of-transcendence
                (22, 16956), -- judgement-gauntlets
                (22, 16928), -- nemesis-gloves
                (22, 16913), -- netherwind-gloves
                (22, 19395), -- rejuvenating-gem
                (22, 19397), -- ring-of-blackrock
                (22, 19355), -- shadow-wing-focus-staff
                (22, 16899), -- stormrage-handguards
                (22, 19396), -- taut-dragonhide-belt

                -- 23 Chromaggus
                (23, 19388), -- angelistas-grasp
                (23, 19361), -- ashjrethul-crossbow-of-smiting
                (23, 16832), -- bloodfang-spaulders
                (23, 19387), -- chromatic-boots
                (23, 19352), -- chromatically-tempered-sword
                (23, 19347), -- claw-of-chromaggus
                (23, 16937), -- dragonstalkers-spaulders
                (23, 19349), -- elementium-reinforced-bulwark
                (23, 19386), -- elementium-threaded-cloak
                (23, 19385), -- empowered-leggings
                (23, 16945), -- epaulets-of-ten-storms
                (23, 19392), -- girdle-of-the-fallen-crusader
                (23, 16953), -- judgement-spaulders
                (23, 16932), -- nemesis-spaulders
                (23, 16917), -- netherwind-mantle
                (23, 16924), -- pauldrons-of-transcendence
                (23, 16961), -- pauldrons-of-wrath
                (23, 19393), -- primalists-linked-waistguard
                (23, 19391), -- shimmering-geta
                (23, 16902), -- stormrage-pauldrons
                (23, 19390), -- taut-dragonhide-gloves
                (23, 19389), -- taut-dragonhide-shoulderpads

                -- 24 Nefarian
                (24, 19376), -- archimtiros-ring-of-reckoning
                (24, 19364), -- ashkandi-greatsword-of-the-brotherhood
                (24, 16905), -- bloodfang-chestpiece
                (24, 19381), -- boots-of-the-shadow-flame
                (24, 16950), -- breastplate-of-ten-storms
                (24, 16966), -- breastplate-of-wrath
                (24, 19378), -- cloak-of-the-brood-lord
                (24, 19363), -- crulshorukh-edge-of-chaos
                (24, 16942), -- dragonstalkers-breastplate
                (24, 19002), -- head-of-nefarian
                -- (24, 19003), -- head-of-nefarian
                (24, 16958), -- judgement-breastplate
                (24, 19360), -- lokamir-il-romathis
                (24, 19375), -- mishundare-circlet-of-the-mind-flayer
                (24, 19379), -- neltharions-tear
                (24, 16931), -- nemesis-robes
                (24, 16916), -- netherwind-robes
                (24, 19377), -- prestors-talisman-of-connivery
                (24, 19382), -- pure-elementium-band
                (24, 16923), -- robes-of-transcendence
                (24, 19356), -- staff-of-the-shadow-flame
                (24, 16897), -- stormrage-chestguard
                (24, 19380); -- therazanes-link");

        // Tie ZG loot to ZG bosses
        DB::insert("INSERT INTO `item_item_sources` (`item_source_id`, `item_id`)
            VALUES
                -- Tokens 25
                (25, 19724), -- primal-hakkari-aegis
                (25, 19717), -- primal-hakkari-armsplint
                (25, 19716), -- primal-hakkari-bindings
                (25, 19719), -- primal-hakkari-girdle
                (25, 22637), -- primal-hakkari-idol
                (25, 19723), -- primal-hakkari-kossack
                (25, 19720), -- primal-hakkari-sash
                (25, 19721), -- primal-hakkari-shawl
                (25, 19718), -- primal-hakkari-stanchion
                (25, 19722), -- primal-hakkari-tabard

                -- Trash 26
                (26, 20263), -- gurubashi-helm
                (26, 19908), -- sceptre-of-smiting
                (26, 20259), -- shadow-panther-hide-gloves
                (26, 20261), -- shadow-panther-hide-belt
                (26, 20258), -- zulian-ceremonial-staff
                (26, 19921), -- zulian-hacker

                -- Shared 27
                (27, 22721), -- band-of-servitude
                (27, 22716), -- belt-of-untapped-power
                (27, 22718), -- blooddrenched-mask
                (27, 22711), -- cloak-of-the-hakkari-worshipers
                (27, 22715), -- gloves-of-the-tormented
                (27, 22712), -- might-of-the-tribe
                (27, 22714), -- sacrificial-gauntlets
                (27, 22722), -- seal-of-the-gurubashi-berserker
                (27, 22720), -- zulian-headdress
                (27, 22713), -- zulian-scepter-of-rites

                -- High Priestess Jeklik 28
                (28, 19928), -- animists-spaulders
                (28, 19918), -- jekliks-crusher
                (28, 19923), -- jekliks-opaline-talisman
                (28, 20265), -- peacekeeper-boots
                (28, 19920), -- primalists-band
                (28, 20262), -- seafury-boots
                (28, 19915), -- zulian-defender

                -- High Priest Venoxis 29
                (29, 19906), -- blooddrenched-footpads
                (29, 19903), -- fang-of-venoxis
                (29, 19904), -- runed-bloodstained-hauberk
                (29, 19905), -- zanzils-band
                (29, 19900), -- zulian-stone-axe
                (29, 19907), -- zulian-tigerhide-cloak

                -- High Priestess Mar'li 30
                (30, 19925), -- band-of-jin
                (30, 19919), -- bloodstained-greaves
                (30, 20032), -- flowing-ritual-robes
                (30, 19930), -- marlis-eye
                (30, 19927), -- marlis-touch
                (30, 19871), -- talisman-of-protection

                -- Bloodlord Mandokir 31
                (31, 19877), -- animists-leggings
                (31, 19869), -- blooddrenched-grips
                (31, 19867), -- bloodlords-defender
                (31, 19878), -- bloodsoaked-pauldrons
                (31, 19895), -- bloodtinged-kilt
                (31, 19870), -- hakkari-loa-cloak
                (31, 19874), -- halberd-of-smiting
                (31, 20038), -- mandokirs-sting
                (31, 19873), -- overlords-crimson-band
                (31, 19863), -- primalists-seal
                (31, 19872), -- swift-razzashi-raptor
                (31, 19893), -- zanzils-seal
                (31, 19866), -- warblade-of-the-hakkari

                -- Edge of Madness 32
                (32, 19968), -- fiery-retributer
                (32, 19939), -- grileks-blood
                (32, 19962), -- grileks-carver
                (32, 19961), -- grileks-grinder
                (32, 19942), -- hazzarahs-dream-thread
                (32, 19993), -- hoodoo-hunting-bow
                (32, 19963), -- pitchfork-of-madness
                (32, 19940), -- renatakis-tooth
                (32, 19964), -- renatakis-soul-conduit
                (32, 19967), -- thoughtblighter
                (32, 19941), -- wushoolays-mane
                (32, 19965), -- wushoolays-poker

                -- High Priest Thekal 33
                (33, 19897), -- betrayers-boots
                (33, 20266), -- peacekeeper-leggings
                (33, 19899), -- ritualistic-legguards
                (33, 20260), -- seafury-leggings
                (33, 19898), -- seal-of-jin
                (33, 19902), -- swift-zulian-tiger
                (33, 19896), -- thekals-grasp
                (33, 19901), -- zulian-slicer

                -- Gahz'ranka 34
                (34, 19945), -- forors-eyepatch
                (34, 19947), -- nat-pagles-broken-reel
                (34, 19944), -- nat-pagles-fish-terminator
                (34, 19946), -- tigules-harpoon
                (34, 22739), -- tome-of-polymorph-turtle

                -- High Priestess Arlokk 35
                (35, 19910), -- arlokks-grasp
                (35, 19922), -- arlokks-hoodoo-stick
                (35, 19913), -- bloodsoaked-greaves
                (35, 19912), -- overlords-onyx-band
                (35, 19914), -- panther-hide-sack
                (35, 19909), -- will-of-arlokk

                -- Jin'do the Hexxer 36
                (36, 19892), -- animists-boots
                (36, 19889), -- blooddrenched-leggings
                (36, 19894), -- bloodsoaked-gauntlets
                (36, 19875), -- bloodstained-coif
                (36, 19887), -- bloodstained-legplates
                (36, 19929), -- bloodtinged-gloves
                (36, 19891), -- jindos-bag-of-whammies
                (36, 19885), -- jindos-evil-eye
                (36, 19890), -- jindos-hexxer
                (36, 19884), -- jindos-judgement
                (36, 19888), -- overlords-embrace
                (36, 19886), -- the-hexxers-cover

                -- Hakkar 37
                (37, 19862), -- aegis-of-the-blood-god
                (37, 19852), -- ancient-hakkari-manslayer
                (37, 19864), -- bloodcaller
                (37, 19855), -- bloodsoaked-legplates
                (37, 19857), -- cloak-of-consumption
                (37, 19859), -- fang-of-the-faceless
                (37, 19853), -- gurubashi-dwarf-destroyer
                (37, 19802), -- heart-of-hakkar
                (37, 20264), -- peacekeeper-gauntlets
                (37, 20257), -- seafury-gauntlets
                (37, 19876), -- soul-corrupters-necklace
                (37, 19856), -- the-eye-of-hakkar
                (37, 19861), -- touch-of-chaos
                (37, 19865), -- warblade-of-the-hakkari
                (37, 19854); -- zinrokh-destroyer-of-worlds");
        // Tie ZG loot to ZG bosses
        DB::insert("INSERT INTO `item_item_sources` (`item_source_id`, `item_id`)
            VALUES
                -- Enchants 38
                (38, 20734), -- formula-enchant-cloak-stealth
                (38, 20728), -- formula-enchant-gloves-frost-power
                (38, 20730), -- formula-enchant-gloves-healing-power
                (38, 20731), -- formula-enchant-gloves-superior-agility

                -- Tokens 39
                (39, 20888), -- qiraji-ceremonial-ring
                (39, 20884), -- qiraji-magisterial-ring
                (39, 20885), -- qiraji-martial-drape
                (39, 20890), -- qiraji-ornate-hilt
                (39, 20889), -- qiraji-regal-drape
                (39, 20886), -- qiraji-spiked-hilt

                -- Trash 40
                (40, 21801), -- antenna-of-invigoration
                (40, 21804), -- coif-of-elemental-fury
                (40, 21809), -- fury-of-the-forgotten-swarm
                (40, 21806), -- gavel-of-qiraji-authority
                (40, 21803), -- helm-of-the-holy-avenger
                (40, 21800), -- silithid-husked-launcher
                (40, 21802), -- the-lost-kris-of-zedd
                (40, 21810), -- treads-of-the-wandering-nomad

                -- Kurinnaxx 41
                (41, 21500), -- belt-of-the-inquisition
                (41, 21503), -- belt-of-the-sand-reaver
                (41, 21498), -- qiraji-sacrificial-dagger
                (41, 21502), -- sand-reaver-wristguards
                (41, 21501), -- toughened-silithid-hide-gloves
                (41, 21499), -- vestments-of-the-shifting-sands

                -- General Rajaxx 42
                (42, 21497), -- boots-of-the-qiraji-general
                (42, 21493), -- boots-of-the-vanguard
                (42, 21496), -- bracers-of-qiraji-command
                (42, 21495), -- legplates-of-the-qiraji-command
                (42, 21492), -- manslayer-of-the-qiraji
                (42, 21494), -- southwinds-grasp

                -- Moam 43
                (43, 21474), -- chitinous-shoulderguards
                (43, 21470), -- cloak-of-the-savior
                (43, 21472), -- dustwind-turban
                (43, 21473), -- eye-of-moam
                (43, 21469), -- gauntlets-of-southwind
                (43, 21479), -- gauntlets-of-the-immovable
                (43, 21475), -- legplates-of-the-destroyer
                (43, 21468), -- mantle-of-maznadir
                (43, 21476), -- obsidian-scaled-leggings
                (43, 22220), -- plans-black-grasp-of-the-destroyer
                (43, 21477), -- ring-of-fury
                (43, 21455), -- southwind-helm
                (43, 21471), -- talon-of-furious-concentration
                (43, 21467), -- thick-silithid-chestguard

                -- Buru the Gorger 44
                (44, 21485), -- burus-skull-fragment
                (44, 21488), -- fetish-of-chitinous-spikes
                (44, 21486), -- gloves-of-the-swarm
                (44, 21489), -- quicksand-waders
                (44, 21491), -- scaled-bracers-of-the-gorger
                (44, 21490), -- slime-kickers
                (44, 21487), -- slimy-scaled-gauntlets

                -- Ayamiss the Hunter 45
                (45, 21479), -- gauntlets-of-the-immovable
                (45, 21481), -- boots-of-the-desert-protector
                (45, 21482), -- boots-of-the-fiery-sands
                (45, 21478), -- bow-of-taut-sinew
                (45, 21484), -- helm-of-regrowth
                (45, 21483), -- ring-of-the-desert-winds
                (45, 21480), -- scaled-silithid-gauntlets
                (45, 21466), -- stinger-of-ayamiss

                -- Ossirian the Unscarred 46
                (46, 21457), -- bracers-of-brutality
                (46, 21459), -- crossbow-of-imminent-doom
                (46, 21458), -- gauntlets-of-new-life
                (46, 21462), -- gloves-of-dark-wisdom
                (46, 21220), -- head-of-ossirian-the-unscarred
                (46, 21460), -- helm-of-domination
                (46, 21461), -- leggings-of-the-black-blizzard
                (46, 21453), -- mantle-of-the-horusath
                (46, 21463), -- ossirians-binding
                (46, 21454), -- runic-stone-shoulders
                (46, 21715), -- sand-polished-hammer
                (46, 21456), -- sandstorm-cloak
                (46, 21464), -- shackles-of-the-unscarred
                (46, 21452); -- staff-of-the-ruins");

        // Tie AQ40 loot to AQ40 bosses
        DB::insert("INSERT INTO `item_item_sources` (`item_source_id`, `item_id`)
            VALUES
                -- Mounts 47
                (47, 21218), -- blue-qiraji-resonating-crystal
                (47, 21323), -- green-qiraji-resonating-crystal
                (47, 21324), -- yellow-qiraji-resonating-crystal
                (47, 21321), -- red-qiraji-resonating-crystal

                -- Enchants 48
                (48, 20736), -- formula-enchant-cloak-dodge
                (48, 20734), -- formula-enchant-cloak-stealth
                (48, 20729), -- formula-enchant-gloves-fire-power
                (48, 20728), -- formula-enchant-gloves-frost-power
                (48, 20730), -- formula-enchant-gloves-healing-power
                (48, 20727), -- formula-enchant-gloves-shadow-power
                (48, 20731), -- formula-enchant-gloves-superior-agility

                -- Tokens 49
                (49, 20929), -- carapace-of-the-old-god
                (49, 20933), -- husk-of-the-old-god
                (49, 21232), -- imperial-qiraji-armaments
                (49, 21237), -- imperial-qiraji-regalia
                (49, 20927), -- ouros-intact-hide
                (49, 20928), -- qiraji-bindings-of-command
                (49, 20932), -- qiraji-bindings-of-dominance
                (49, 20931), -- skin-of-the-great-sandworm
                (49, 20930), -- veklors-diadem
                (49, 20926), -- veknilashs-circlet

                -- Trash 50
                (50, 21837), -- anubisath-warhammer
                (50, 21838), -- garb-of-royal-ascension
                (50, 21888), -- gloves-of-the-immortal
                (50, 21889), -- gloves-of-the-redeemed-prophecy
                (50, 21856), -- neretzek-the-blood-drinker
                (50, 21836), -- ritssyns-ring-of-chaos
                (50, 21891), -- shard-of-the-fallen-star

                -- The Prophet Skeram 51
                (51, 21702), -- amulet-of-foul-warding
                (51, 21699), -- barrage-shoulders
                (51, 21708), -- beetle-scaled-wristguards
                (51, 21705), -- boots-of-the-fallen-prophet
                (51, 21704), -- boots-of-the-redeemed-prophecy
                (51, 21706), -- boots-of-the-unwavering-will
                (51, 21814), -- breastplate-of-annihilation
                (51, 21701), -- cloak-of-concentrated-hatred
                (51, 21703), -- hammer-of-jizhi
                (51, 21698), -- leggings-of-immersion
                (51, 21700), -- pendant-of-the-qiraji-guardian
                (51, 22222), -- plans-thick-obsidian-breastplate
                (51, 21707), -- ring-of-swarming-thought
                (51, 21128), -- staff-of-the-qiraji-prophets

                -- Bug Trio 52
                (52, 21690), -- angelistas-charm
                (52, 21695), -- angelistas-touch
                (52, 21682), -- bile-covered-gauntlets
                (52, 21688), -- boots-of-the-fallen-hero
                (52, 21697), -- cape-of-the-trinity
                (52, 21689), -- gloves-of-ebru
                (52, 21693), -- guise-of-the-devourer
                (52, 21686), -- mantle-of-phrenic-power
                (52, 21683), -- mantle-of-the-desert-crusade
                (52, 21684), -- mantle-of-the-deserts-fury
                (52, 21691), -- ooze-ridden-gauntlets
                (52, 21685), -- petrified-scarab
                (52, 21681), -- ring-of-the-devoured
                (52, 21696), -- robes-of-the-triumvirate
                (52, 21694), -- ternary-mantle
                (52, 21692), -- triad-girdle
                (52, 21687), -- ukkos-ring-of-darkness
                (52, 21680), -- vest-of-swift-execution
                (52, 21603), -- wand-of-qiraji-nobility

                -- Battleguard Sartura 53
                (53, 21670), -- badge-of-the-swarmguard
                (53, 21669), -- creeping-vine-helm
                (53, 21674), -- gauntlets-of-steadfast-determination
                (53, 21672), -- gloves-of-enforcement
                (53, 21676), -- leggings-of-the-festering-swarm
                (53, 21667), -- legplates-of-blazing-light
                (53, 21678), -- necklace-of-purity
                (53, 21648), -- recomposed-boots
                (53, 21671), -- robes-of-the-battleguard
                (53, 21666), -- sarturas-might
                (53, 21668), -- scaled-leggings-of-qiraji-fury
                (53, 21673), -- silithid-claw
                (53, 21675), -- thick-qirajihide-belt

                -- Fankriss the Unyielding 54
                (54, 21650), -- ancient-qiraji-ripper
                (54, 21635), -- barb-of-the-sand-reaver
                (54, 21664), -- barbed-choker
                (54, 21627), -- cloak-of-untold-secrets
                (54, 21647), -- fetish-of-the-sand-reaver
                (54, 21645), -- hive-tunnelers-boots
                (54, 22402), -- libram-of-grace
                (54, 21665), -- mantle-of-wicked-revenge
                (54, 21639), -- pauldrons-of-the-unrelenting
                (54, 21663), -- robes-of-the-guardian-saint
                (54, 21651), -- scaled-sand-reaver-leggings
                (54, 21652), -- silithid-carapace-chestguard
                (54, 22396), -- totem-of-life

                -- Viscidus 55
                (55, 21624), -- gauntlets-of-kalimdor
                (55, 21623), -- gauntlets-of-the-righteous-champion
                (55, 22399), -- idol-of-health
                (55, 20928), -- qiraji-bindings-of-command
                (55, 20932), -- qiraji-bindings-of-dominance
                (55, 21677), -- ring-of-the-qiraji-fury
                (55, 21625), -- scarab-brooch
                (55, 21622), -- sharpened-silithid-femur
                (55, 21626), -- slime-coated-leggings

                -- Princess Huhuran 56
                (56, 21621), -- cloak-of-the-golden-hive
                (56, 21619), -- gloves-of-the-messiah
                (56, 21618), -- hive-defiler-wristguards
                (56, 21616), -- huhurans-stinger
                (56, 20928), -- qiraji-bindings-of-command
                (56, 20932), -- qiraji-bindings-of-dominance
                (56, 21620), -- ring-of-the-martyr
                (56, 21617), -- wasphide-gauntlets

                -- Twin Emperors 57
                (57, 21608), -- amulet-of-veknilash
                (57, 21606), -- belt-of-the-fallen-emperor
                (57, 21600), -- boots-of-epiphany
                (57, 21604), -- bracelets-of-royal-redemption
                (57, 21605), -- gloves-of-the-hidden-temple
                (57, 21607), -- grasp-of-the-fallen-emperor
                (57, 21679), -- kalimdors-revenge
                (57, 21602), -- qiraji-execution-bracers
                (57, 21609), -- regenerating-belt-of-veknilash
                (57, 21601), -- ring-of-emperor-veklor
                (57, 21598), -- royal-qiraji-belt
                (57, 21597), -- royal-scepter-of-veklor
                (57, 20930), -- veklors-diadem
                (57, 21599), -- veklors-gloves-of-devastation
                (57, 20926), -- veknilashs-circlet

                -- Ouro 58
                (58, 21611), -- burrower-bracers
                (58, 21615), -- don-rigobertos-lost-hat
                (58, 23570), -- jom-gabbar
                (58, 23557), -- larvae-of-the-great-worm
                (58, 20927), -- ouros-intact-hide
                (58, 20931), -- skin-of-the-great-sandworm
                (58, 23558), -- the-burrowers-shell
                (58, 21610), -- wormscale-blocker

                -- C'Thun 59
                (59, 21586), -- belt-of-never-ending-agony
                (59, 20929), -- carapace-of-the-old-god
                (59, 21583), -- cloak-of-clarity
                (59, 22731), -- cloak-of-the-devoured
                (59, 21134), -- dark-edge-of-insanity
                (59, 21585), -- dark-storm-gauntlets
                (59, 21126), -- deaths-sting
                (59, 21221), -- eye-of-cthun
                (59, 22730), -- eyestalk-waist-cord
                (59, 21581), -- gauntlets-of-annihilation
                (59, 21582), -- grasp-of-the-old-god
                (59, 20933), -- husk-of-the-old-god
                (59, 22732), -- mark-of-cthun
                (59, 21596), -- ring-of-the-godslayer
                (59, 21839), -- scepter-of-the-false-prophet
                (59, 21579); -- vanquished-tentacle-of-cthun");
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
