var table = null;

const VIEW_PRIOS    = 'prios';
const VIEW_RECEIVED = 'received';
const VIEW_SLOTS    = 'slots';
const VIEW_WISHLIST = 'wishlist';

// Defaults to slots...
var view = '';

var colCharacter = 0;
var colArchetype = 1;
var colAttendance = 2;
var colMainRaidGroup = 3;
var colNotes = 4;
var colClass = 5;
var colSlotTotal     = 10;
var colSlotHead      = 11;
var colSlotNeck      = 12;
var colSlotShoulder  = 13;
var colSlotBack      = 14;
var colSlotChest     = 15;
var colSlotWrists    = 16;
var colSlotHands     = 17;
var colSlotWaist     = 18;
var colSlotLegs      = 19;
var colSlotFeet      = 20;
var colSlotFinger    = 21;
var colSlotTrinket   = 22;
var colSlotWeapon    = 23;
var colSlotOffhand   = 24;
var colSlotRanged    = 25;
var colSlotOther     = 26;
var colInstanceTotal = 27;
var colInstance1     = 28;
var colInstance2     = 29;
var colInstance3     = 30;
var colInstance4     = 31;
var colInstance5     = 32;
var colInstance6     = 33;
var colInstance7     = 34;
var colInstance8     = 35;
var colInstance9     = 36;
var colInstance10    = 37;
var colInstance11    = 38;
var colInstance12    = 39;
var colInstance13    = 40;
var colInstance14    = 41;
var colInstance15    = 42;
var colInstance16    = 43;
var colInstance17    = 44;
var colInstance18    = 45;
var colInstance19    = 46;
var colInstance20    = 47;
var colInstance21    = 48;
var colInstance22    = 49;
var colInstance23    = 50;
var colInstance24    = 51;
var colInstance25    = 52;

var allItemsVisible = false;
var offspecVisible = true;
var strikethroughVisible = true;

// Only show items from these instances.
var instanceIdsToShow = [];
var lootTypeToShow = 'received';

// For making sure we don't spam request handlers to be added.
var rosterHandlersTimeout = null;

$(document).ready( function () {
    // Get the desired view from the URL on page load.
    view = window.location.hash.substring(1);

    if (![VIEW_PRIOS, VIEW_RECEIVED, VIEW_SLOTS, VIEW_WISHLIST].includes(view)) {
        view = VIEW_SLOTS;
    }

    initializeTable();

    $(".js-toggle-column").click(function(e) {
        e.preventDefault();
        let column = table.column($(this).attr("data-column"));
        column.visible(!column.visible());
    });

    $(".js-show-prio-cols").click(function(e) {
        e.preventDefault();
        view = VIEW_PRIOS;
        window.location = "#" + VIEW_PRIOS;

        $(".js-toggle-column-set").removeClass("disabled");
        $(this).addClass('disabled');

        toggleInstanceCols(true);
        toggleSlotCols(false);

        $("#loot_type_container").hide();

        $(".js-prio-items").show();
        $(".js-received-items").hide();
        $(".js-wishlist-items").hide();
    });

    $(".js-show-received-cols").click(function(e) {
        e.preventDefault();
        view = VIEW_RECEIVED;
        window.location = "#" + VIEW_RECEIVED;

        $(".js-toggle-column-set").removeClass("disabled");
        $(this).addClass('disabled');
        toggleInstanceCols(true);
        toggleSlotCols(false);
        $("#loot_type_container").hide();

        $(".js-prio-items").hide();
        $(".js-received-items").show();
        $(".js-wishlist-items").hide();
    });

    $(".js-show-slot-cols").click(function(e) {
        e.preventDefault();
        view = VIEW_SLOTS;
        window.location = "#" + VIEW_SLOTS;

        $(".js-toggle-column-set").removeClass("disabled");
        $(this).addClass('disabled');

        $("#loot_type_container").show();

        toggleInstanceCols(false);
        toggleSlotCols(true);
    });

    $(".js-show-wishlist-cols").click(function(e) {
        e.preventDefault();
        view = VIEW_WISHLIST;
        window.location = "#" + VIEW_WISHLIST;

        $(".js-toggle-column-set").removeClass("disabled");
        $(this).addClass('disabled');

        toggleInstanceCols(true);
        toggleSlotCols(false);

        $("#loot_type_container").hide();

        $(".js-prio-items").hide();
        $(".js-received-items").hide();
        $(".js-wishlist-items").show();
    });

    $(".js-hide-strikethrough-items").click(function() {
        if (strikethroughVisible) {
            strikethroughVisible = false;
            $("#hide_received_label").hide();
            $("#show_received_label").show();
        } else {
            strikethroughVisible = true;
            $("#hide_received_label").show();
            $("#show_received_label").hide();
        }
        initializeTable();
    });

    // Triggered when a column is made visible
    table.on('column-visibility.dt', function (e, settings, column, state) {
        // Refresh wowhead links to show stlying.
        // wowhead's script previously ignored these links if they weren't visible
        callRosterStatHandlers();
    });

    $(".js-hide-offspec-items").click(function() {
        if (offspecVisible) {
            offspecVisible = false;
            $("#hide_os_label").hide();
            $("#show_os_label").show();
        } else {
            offspecVisible = true;
            $("#hide_os_label").show();
            $("#show_os_label").hide();
        }
        initializeTable();
    });

    $(".js-show-all-items").click(function () {
        if (allItemsVisible) {
            $(".js-item-list").hide();
            allItemsVisible = false;
            $("#hide_items_label").hide();
            $("#show_items_label").show();
        } else {
            $(".js-item-list").show();
            allItemsVisible = true;
            $("#hide_items_label").show();
            $("#show_items_label").hide();
        }
    });

    $("#loot_type").change(function () {
        if ($(this).val() != lootTypeToShow) {
            lootTypeToShow = $(this).val();
            initializeTable();
        }
    });

    // Dungeon multiselect could get stuck if clicked too soon
    $(".selectpicker").selectpicker("refresh");

    $(".loadingBarContainer").removeClass("d-flex").hide();
    $("#characterStatsTable").show();
    $("#characterStatsTableFilters").show();

    callRosterStatHandlers();
    addInstanceFilterHandlers();
});

function initializeTable() {
    if (table != null) {
        table.clear().destroy();
        $("#characterStatsTable tbody").empty();
        $("#characterStatsTable thead").empty();
    }

    table = createRosterStatsTable();

    showOrHideSomeStuffBasedOnView();
}

function showOrHideSomeStuffBasedOnView() {
    if (view == VIEW_SLOTS) {
        $(".js-show-slot-cols").addClass("disabled");
    } else if (view == VIEW_PRIOS) {
        $(".js-show-prio-cols").addClass("disabled");
        $(".js-received-items").hide();
        $(".js-wishlist-items").hide();
        $("#loot_type_container").hide();
    } else if (view == VIEW_RECEIVED) {
        $(".js-show-received-cols").addClass("disabled");
        $(".js-prio-items").hide();
        $(".js-wishlist-items").hide();
        $("#loot_type_container").hide();
    } else if (view == VIEW_WISHLIST) {
        $(".js-show-wishlist-cols").addClass("disabled");
        $(".js-prio-items").hide();
        $(".js-received-items").hide();
        $("#loot_type_container").hide();
    }
}

function createRosterStatsTable() {
    if ($.fn.DataTable.isDataTable('#characterStatsTable')) {
        $("#characterStatsTable").destroy();
    }

    let rosterStatsTableColumns = [
        // Character name
        {
            title  : `<span class="fas fa-fw fa-user"></span> ${headerCharacter} <span class="text-muted small">(${characters.length})</span>`,
            data   : "name",
            render : {
                // Underscore property gets used for the visible render of each cell
                _: function (data, type, row) {
                    return `<ul class="no-bullet no-indent">
                        <li>
                            <div class="dropdown text-${ row.class ? slug(row.class) : '' }">
                                <a class="dropdown-toggle font-weight-bold text-${ row.class ? slug(row.class) : '' }"
                                    id="character${ row.id }Dropdown"
                                    role="button"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    title="${ row.username ? row.username : '' }">
                                    ${ row.name }
                                </a>
                                <div class="dropdown-menu" aria-labelledby="character${ row.id }Dropdown">
                                    <a class="dropdown-item" href="/${ guild.id }/${ guild.slug }/c/${ row.id }/${ row.slug }">Profile</a>
                                    <a class="dropdown-item" href="/${ guild.id }/${ guild.slug }/audit-log?character_id=${ row.id }">History</a>
                                    ${ showEdit ?
                                        `<a class="dropdown-item" href="/${ guild.id }/${ guild.slug }/c/${ row.id }/${ row.slug }/edit">Edit</a>
                                        <a class="dropdown-item" href="/${ guild.id }/${ guild.slug }/c/${ row.id }/${ row.slug }/loot">Wishlist & Loot</a>`
                                        : `` }
                                    ${ row.member_id ?
                                        `<a class="dropdown-item" href="/${ guild.id }/${ guild.slug }/u/${ row.member_id }/${ row.username ? row.username.toLowerCase() : 'view member' }">${ row.username ? row.username : 'view member' }</a>`
                                    : `` }
                                </div>
                            </div>
                        </li>
                        ${ row.spec || row.display_spec || row.archetype
                            ? `<li class="small font-weight-light">
                                <span class="${ row.archetype ?  getArchetypeIcon(row.archetype) : '' }"></span>
                                <span class="">
                                    ${ row.spec_label ? row.spec_label : (row.spec ? row.display_spec : '') }
                                </span>
                            </li>`
                            : `` }
                        ${ row.raid_group_name ? `<li>${ getRaidGroupHtml(row.raid_group_name, row.raid_group_color) }</li>` : `` }
                    </ul>`;
                },
                // Sort by the value in data; not the render
                sort: function (data, type, row) {return data;},
            },
            visible : true,
            width   : "50px",
            className : "width-50 fixed-width",
        },
        // Archetype
        {
            title  : "Role",
            data   : "character",
            render : function (data, type, row) {
                return `<span class="smaller">${row.archetype ? row.archetype : ''} ${row.sub_archetype ? row.sub_archetype : ''}</span>`;
            },
            visible : true,
            width   : "20px",
            className : "width-20 fixed-width",
        },
        // Attendance
        {
            title  : "Att.",
            data   : "raid_count",
            render : {
                _: function (data, type, row) {
                    if (!guild.is_attendance_hidden && (row.attendance_percentage || row.raid_count || row.benched_count)) {
                        return `<ul class="list-inline small">
                                ${ row.raid_count && typeof row.attendance_percentage === 'number' ? `<li class="list-inline-item mr-0 ${ getAttendanceColor(row.attendance_percentage) }" title="attendance">${ Math.round(row.attendance_percentage * 100) }%</li>` : '' }
                                ${ row.raid_count ? `<li class="list-inline-item text-muted mr-0">${ row.raid_count }r</li>` : ``}
                                ${ row.benched_count ? `<li class="list-inline-item text-muted mr-0">bench ${ row.benched_count }x</li>` : ``}
                            </ul>`;
                    } else {
                        return '';
                    }
                },
                sort: function (data, type, row) {return data;},
            },
            visible : true,
            width   : "10px",
            className : "width-10 fixed-width",
            type: 'num',
        },
        // Raid Groups (for filtering)
        {
            title  : "Raid",
            data   : "character",
            render : function (data, type, row) {
                if (row.raid_group_name || (row.secondary_raid_groups && row.secondary_raid_groups.length)) {
                    let secondaryRaidGroups = '';
                    if (row.secondary_raid_groups && row.secondary_raid_groups.length) {
                        secondaryRaidGroups = `<ul class="list-inline">`;
                        row.secondary_raid_groups.forEach(function (raidGroup, index) {
                            secondaryRaidGroups += `${ raidGroup.id } <li class="list-inline-item small"><span class="text-muted"><span class="role-circle align-fix" style="background-color:${ getColorFromDec(parseInt(raidGroup.color)) }"></span>${raidGroup.name}</span></li>`;
                        });
                        secondaryRaidGroups += `</ul>`;
                    }
                    return row.raid_group_id + getRaidGroupHtml(row.raid_group_name, row.raid_group_color) + secondaryRaidGroups;
                } else {
                    return '';
                }
            },
            visible : false,
            width   : "50px",
            className : "width-50 fixed-width",
        },
        // Notes
        {
            title  : `<span class="fas fa-fw fa-comment-alt-lines"></span> ${headerNotes}`,
            data   : "notes",
            render : function (data, type, row) {
                return getNotes(data, type, row);
            },
            orderable : false,
            visible : false,
            width   : "100px",
            className : "width-100 fixed-width",
        },
        // Class (for filtering)
        {
            title  : "Class",
            data   : "class",
            render : function (data, type, row) {
                return (row.class ? row.class : null);
            },
            visible : false,
        },
        // Username (for searching)
        {
            title  : "Username",
            data   : "username",
            render : function (data, type, row) {
                return (row.username ? row.username : null);
            },
            visible : false,
        },
        // Discord Username (for searching)
        {
            title  : "Discord Username",
            data   : "discord_username",
            render : function (data, type, row) {
                return (row.discord_username ? row.discord_username : null);
            },
            visible : false,
        },
        // Raid attend count (for sorting)
        {
            title  : "Raids Attended",
            data   : "raid_count",
            render : function (data, type, row) {
                return (row.raid_count ? row.raid_count : null);
            },
            visible    : false,
            searchable : false,
        },
        // Benched count (for sorting)
        {
            title  : "Benched Count",
            data   : "benched_count",
            render : function (data, type, row) {
                return (row.benched_count ? row.benched_count : null);
            },
            visible    : false,
            searchable : false,
        }
    ];

    let isVisible = false;

    if (view === VIEW_SLOTS) {
        isVisible = true;
    }

    // Add the columns for each item slot
    rosterStatsTableColumns.push(
        createItemSlotColumn("Total",         null,                         isVisible),
        createItemSlotColumn("Head",          [SLOT_HEAD],                  isVisible),
        createItemSlotColumn("Neck",          [SLOT_NECK],                  isVisible),
        createItemSlotColumn("Shoulders",     [SLOT_SHOULDERS],             isVisible),
        createItemSlotColumn("Back",          [SLOT_BACK],                  isVisible),
        createItemSlotColumn("Chest",         [SLOT_CHEST_1, SLOT_CHEST_2], isVisible),
        createItemSlotColumn("Wrist",         [SLOT_WRIST],                 isVisible),
        createItemSlotColumn("Waist",         [SLOT_WAIST],                 isVisible),
        createItemSlotColumn("Hands",         [SLOT_HANDS],                 isVisible),
        createItemSlotColumn("Legs",          [SLOT_LEGS],                  isVisible),
        createItemSlotColumn("Feet",          [SLOT_FEET],                  isVisible),
        createItemSlotColumn("Finger",        [SLOT_FINGER],                isVisible),
        createItemSlotColumn("Trinket",       [SLOT_TRINKET],               isVisible),
        createItemSlotColumn("Weapon",        [SLOT_WEAPON_MAIN_HAND, SLOT_WEAPON_TWO_HAND, SLOT_WEAPON_ONE_HAND, SLOT_WEAPON_OFF_HAND], isVisible),
        createItemSlotColumn("Offhand",       [SLOT_SHIELD, SLOT_OFFHAND],                             isVisible),
        createItemSlotColumn("Ranged /Relic", [SLOT_RANGED_1, SLOT_RANGED_2, SLOT_THROWN, SLOT_RELIC], isVisible),
        createItemSlotColumn("Misc",          [SLOT_MISC, SLOT_SHIRT, SLOT_BAG, SLOT_AMMO],            isVisible)
    );

    if ([VIEW_PRIOS, VIEW_RECEIVED, VIEW_WISHLIST].includes(view)) {
        isVisible = true;
    } else {
        isVisible = false;
    }

    rosterStatsTableColumns.push(
        createInstanceTotalsColumn(isVisible)
    );

    if (guild && guild.expansion_id === 1) { // Classic
        rosterStatsTableColumns.push(createInstanceColumn("MC",           1, (isVisible && (instanceIdsToShow.includes(1) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Ony",          2, (isVisible && (instanceIdsToShow.includes(2) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("BWL",          3, (isVisible && (instanceIdsToShow.includes(3) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("ZG",           4, (isVisible && (instanceIdsToShow.includes(4) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("AQ20",         5, (isVisible && (instanceIdsToShow.includes(5) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("AQ40",         6, (isVisible && (instanceIdsToShow.includes(6) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Naxx",         7, (isVisible && (instanceIdsToShow.includes(7) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("World Bosses", 8, (isVisible && (instanceIdsToShow.includes(8) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Other", null, (instanceIdsToShow.length === 0)));
    } else if (guild && guild.expansion_id === 2) { // TBC
        rosterStatsTableColumns.push(createInstanceColumn("Kara",         9,  (isVisible && (instanceIdsToShow.includes(9) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Gruul",        10, (isVisible && (instanceIdsToShow.includes(10) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Mag",          11, (isVisible && (instanceIdsToShow.includes(11) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("SSC",          12, (isVisible && (instanceIdsToShow.includes(12) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Hyjal",        13, (isVisible && (instanceIdsToShow.includes(13) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("TK",           14, (isVisible && (instanceIdsToShow.includes(14) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("BT",           15, (isVisible && (instanceIdsToShow.includes(15) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("ZA",           16, (isVisible && (instanceIdsToShow.includes(16) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Sunwell",      17, (isVisible && (instanceIdsToShow.includes(17) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("World Bosses", 18, (isVisible && (instanceIdsToShow.includes(18) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Other", null, (instanceIdsToShow.length === 0)));
    } else if (guild && guild.expansion_id === 3) { // WoTLK
        rosterStatsTableColumns.push(createInstanceColumn("Naxx N10", 19, (isVisible && (instanceIdsToShow.includes(19) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Naxx N25", 20, (isVisible && (instanceIdsToShow.includes(20) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("EoE N10",  21, (isVisible && (instanceIdsToShow.includes(21) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("EoE N25",  22, (isVisible && (instanceIdsToShow.includes(22) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("OS N10",   23, (isVisible && (instanceIdsToShow.includes(23) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("OS N25",   24, (isVisible && (instanceIdsToShow.includes(24) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Arch N10", 25, (isVisible && (instanceIdsToShow.includes(25) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Arch N25", 26, (isVisible && (instanceIdsToShow.includes(26) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Uld N10",  27, (isVisible && (instanceIdsToShow.includes(27) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Uld N25",  28, (isVisible && (instanceIdsToShow.includes(28) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("TotC N10", 29, (isVisible && (instanceIdsToShow.includes(29) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("TotC N25", 30, (isVisible && (instanceIdsToShow.includes(30) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("TotC H10", 31, (isVisible && (instanceIdsToShow.includes(31) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("TotC H25", 32, (isVisible && (instanceIdsToShow.includes(32) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Ony N10",  33, (isVisible && (instanceIdsToShow.includes(33) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Ony N25",  34, (isVisible && (instanceIdsToShow.includes(34) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("ICC N10",  35, (isVisible && (instanceIdsToShow.includes(35) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("ICC N25",  36, (isVisible && (instanceIdsToShow.includes(36) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("ICC H10",  37, (isVisible && (instanceIdsToShow.includes(37) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("ICC H25",  38, (isVisible && (instanceIdsToShow.includes(38) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("RS N10",   39, (isVisible && (instanceIdsToShow.includes(39) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("RS N25",   40, (isVisible && (instanceIdsToShow.includes(40) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("RS H10",   41, (isVisible && (instanceIdsToShow.includes(41) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("RS H25",   42, (isVisible && (instanceIdsToShow.includes(42) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("Other", null, (instanceIdsToShow.length === 0)));
    } else if (guild && guild.expansion_id === 4) { // SOD
        rosterStatsTableColumns.push(createInstanceColumn("World",    43, (isVisible && (instanceIdsToShow.includes(43) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("World",    44, (isVisible && (instanceIdsToShow.includes(44) || instanceIdsToShow.length === 0))));
        rosterStatsTableColumns.push(createInstanceColumn("World",    45, (isVisible && (instanceIdsToShow.includes(45) || instanceIdsToShow.length === 0))));
    } else if (guild && guild.expansion_id === 5) { // Cata
        //
    }

    rosterStatsTable = $("#characterStatsTable").DataTable({
        autoWidth : false,
        data      : characters,
        // To disable fuzzy search:
        // search: {
        //     smart: false
        // },
        oLanguage: {
            sSearch: "<abbr title='Fuzzy searching is ON. To search exact text, wrap your search in \"quotes\"'>Search</abbr>"
        },
        columns : rosterStatsTableColumns,
        order  : [], // Disable initial auto-sort; relies on server-side sorting
        paging : false,
        fixedHeader : { // Header row sticks to top of window when scrolling down
            headerOffset : 43,
        },
        drawCallback : function () {
            callRosterStatHandlers();
        },
        initComplete: function () {
            // Columns that we want to filter by.
            const filterColumns = [
                colClass,
                colArchetype,
                colMainRaidGroup,
            ];

            // For each column, set up a filter
            this.api().columns().every(function (index) {
                var column = this;

                // select1 is the first filter for this column
                let select1 = null;
                // select2 is the second filter for this column
                let select2 = null; // Initialize this beside select1 if we want a secondary sort for the same column

                // Based on the current column, identify the relevant filter input
                if (index == colArchetype) {
                    select1 = $("#archetype_filter");
                    select2 = null;
                } else if (index == colClass) {
                    select1 = $("#class_filter");
                    select2 = null;
                } else if (index == colMainRaidGroup) {
                    select1 = $("#raid_group_filter");
                    select2 = null;
                }

                if (filterColumns.includes(index)) {
                    select1.on('change', function () {
                        const val = $.fn.dataTable.util.escapeRegex($(this).val());

                        // Only IF we are using the second select
                        if (select2 && select2.val()) {
                            // Must contain both
                            val = "(?=.*" + val + ")(?=.*" + $.fn.dataTable.util.escapeRegex(select2.val()) + ")";
                        }

                        column.search(val ? val : '', true, false).draw();
                    }).change();

                    if (select2) {
                        select2.on('change', function () {
                            const val = $.fn.dataTable.util.escapeRegex($(this).val());

                            if (select1 && select1.val()) {
                                // Must contain both
                                val = "(?=.*" + val + ")(?=.*" + $.fn.dataTable.util.escapeRegex(select1.val()) + ")";
                            }

                            column.search(val ? val : '', true, false).draw();
                        }).change();
                    }
                }
            });
            callRosterStatHandlers();
        }
    });
    return rosterStatsTable;
}

function addInstanceFilterHandlers() {
    $("#instance_filter").change(function () {
        let instanceIds = $("#instance_filter").val();
        // Cast from string to int
        for (i = 0; i < instanceIds.length; i++) {
            instanceIds[i] = parseInt(instanceIds[i]);
        }
        instanceIdsToShow = instanceIds;
        initializeTable();
    });
}

// Take in an array of items, return the average tier number.
// Only count items that have tiers associated with them.
function getAverageTier(items, showColor) {
    const filteredItems = items.filter(item => item.guild_tier);
    const average = filteredItems.reduce((ac, a) => a.guild_tier + ac, 0) / filteredItems.length;
    const mockItem = {'guild_tier': average};
    return getTierHtml(mockItem, showColor);
}

/**
 * Get an object that represents a column for an instance in Datatables.
 *
 * @param {string} name The name of the instance.
 * @param {number} instanceId   The integer that matches the instance's ID in the database.
 * @param {bool}   isVisible    Whether or not this column should be visible by default.
 *
 * @return {object}
 */
function createInstanceColumn(name, instanceId, isVisible) {
    return {
        className : "width-50 fixed-width pt-0 pl-0 pb-0 pr-1",
        title  : name,
        data   : "name",
        render : {
            _: function (data, type, row) {
                let html = '';

                let prioItems = row.prios ? row.prios.filter(item => item.instance_id === instanceId) : [];
                let receivedItems = row.received ? row.received.filter(item => item.instance_id === instanceId) : [];
                let wishlistItems = row.all_wishlists ? row.all_wishlists.filter(item => (item.instance_id === instanceId && item.list_number === guild.current_wishlist_number)) : [];
                if (!offspecVisible) {
                    prioItems = prioItems.filter(item => item.pivot.is_offspec != 1);
                    receivedItems = receivedItems.filter(item => item.pivot.is_offspec != 1);
                    wishlistItems = wishlistItems.filter(item => item.pivot.is_offspec != 1);
                }
                if (!strikethroughVisible) {
                    prioItems = prioItems.filter(item => item.pivot.is_received != 1);
                    receivedItems = receivedItems.filter(item => item.pivot.is_received != 1);
                    wishlistItems = wishlistItems.filter(item => item.pivot.is_received != 1);
                }

                // Prios
                const prioOffspecCount = prioItems.filter(item => item.is_offspec).length;
                if (prioItems && prioItems.length) {
                    html += `<div class="js-prio-items">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item mr-1 font-weight-bold">${ prioItems.length }</li>
                                ${ guild.tier_mode ? `<li class="list-inline-item mr-1">${ getAverageTier(prioItems, false) }</li>` : '' }
                                ${ prioOffspecCount ? `<li class="list-inline-item small mr-1 text-muted">${ prioOffspecCount }os</li>` : `` }
                                ${ prioItems.length ? `<li class="list-inline-item">${ getItemListHtml(prioItems, 'prio', row.id, false, false) }</li>` : `` }
                            </ul>
                        </div>`;
                } else {
                    html += `<div class="js-prio-items text-muted">—</div>`;
                }

                // Received
                const receivedOffspecCount = receivedItems.filter(item => item.is_offspec).length;
                if (receivedItems && receivedItems.length) {
                    html += `<div class="js-received-items">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item mr-1 font-weight-bold">${ receivedItems.length }</li>
                                ${ guild.tier_mode ? `<li class="list-inline-item mr-1">${ getAverageTier(receivedItems, false) }</li>` : '' }
                                ${ receivedOffspecCount ? `<li class="list-inline-item mr-1 small text-muted">${ receivedOffspecCount }os</li>` : `` }
                                ${ receivedItems.length ? `<li class="list-inline-item">${ getItemListHtml(receivedItems, 'received', row.id, false, false) }</li>` : `` }
                            </ul>
                        </div>`;
                } else {
                    html += `<div class="js-received-items text-muted">—</div>`;
                }

                // Wishlist (current only)
                const wishlistOffspecCount = wishlistItems.filter(item => item.is_offspec).length;
                if (wishlistItems && wishlistItems.length) {
                    html += `<div class="js-wishlist-items">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item mr-1 font-weight-bold">${ wishlistItems.length }</li>
                                ${ guild.tier_mode ? `<li class="list-inline-item mr-1">${ getAverageTier(wishlistItems, false) }</li>` : '' }
                                ${ wishlistOffspecCount ? `<li class="list-inline-item mr-1 small text-muted">${ wishlistOffspecCount }os</li>` : `` }
                                ${ wishlistItems.length ? `<li class="list-inline-item">${ getItemListHtml(wishlistItems, 'prio', row.id, false, false) }</li>` : `` }
                            </ul>
                        </div>`;
                } else {
                    html += `<div class="js-wishlist-items text-muted">—</div>`;
                }

                return html;
            },
            sort: function (data, type, row) {
                let filteredItems = [];
                if (view === VIEW_PRIOS) {
                    filteredItems = row.prios ? row.prios.slice() : [];
                } else if (view === VIEW_RECEIVED) {
                    filteredItems = row.received ? row.received.slice() : [];
                } else if (view === VIEW_WISHLIST) {
                    filteredItems = row.all_wishlists ? row.all_wishlists.filter(item => item.list_number === guild.current_wishlist_number) : [];
                }
                // Filter out items not belonging in the selected instance(s)
                if (instanceIdsToShow.length > 0) {
                    filteredItems = filteredItems.filter(item => instanceIdsToShow.includes(item.instance_id));
                }
                if (!offspecVisible) {
                    filteredItems = filteredItems.filter(item => item.pivot.is_offspec != 1);
                }
                if (!strikethroughVisible) {
                    filteredItems = filteredItems.filter(item => item.pivot.is_received != 1);
                }
                return filteredItems.length;
            },
        },
        orderable : true,
        visible    : isVisible,
        searchable : true,
        type: 'num',
    };
}

/**
 * Shows the aggregate stats for the instances columns.
 *
 * @param {bool}   isVisible    Whether or not this column should be visible by default.
 *
 * @return {object}
 */
function createInstanceTotalsColumn(isVisible) {
    return {
        className : "width-50 fixed-width pt-0 pl-0 pb-0 pr-1",
        title  : "Total",
        data   : "name",
        render : {
            _: function (data, type, row) {
                let html = '';

                // Clone arrays, or make empty ones. slice() is used to clone rather than copy by reference.
                let prioItems = row.prios ? row.prios.slice() : [];
                let receivedItems = row.received ? row.received.slice() : [];
                let wishlistItems = row.all_wishlists ? row.all_wishlists.filter(item => item.list_number === guild.current_wishlist_number) : [];
                // Filter out items not belonging in the selected instance(s)
                if (instanceIdsToShow.length > 0) {
                    prioItems = prioItems.filter(item => instanceIdsToShow.includes(item.instance_id));
                    receivedItems = receivedItems.filter(item => instanceIdsToShow.includes(item.instance_id));
                    wishlistItems = wishlistItems.filter(item => instanceIdsToShow.includes(item.instance_id));
                }
                if (!offspecVisible) {
                    prioItems = prioItems.filter(item => item.pivot.is_offspec != 1);
                    receivedItems = receivedItems.filter(item => item.pivot.is_offspec != 1);
                    wishlistItems = wishlistItems.filter(item => item.pivot.is_offspec != 1);
                }
                if (!strikethroughVisible) {
                    prioItems = prioItems.filter(item => item.pivot.is_received != 1);
                    receivedItems = receivedItems.filter(item => item.pivot.is_received != 1);
                    wishlistItems = wishlistItems.filter(item => item.pivot.is_received != 1);
                }

                // Prios
                const prioOffspecCount = prioItems.filter(item => item.is_offspec).length;
                if (prioItems && prioItems.length) {
                    html += `<div class="js-prio-items">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item mr-1 font-weight-bold">${ prioItems.length }</li>
                                ${ guild.tier_mode ? `<li class="list-inline-item mr-1">${ getAverageTier(prioItems, false) }</li>` : '' }
                                ${ prioOffspecCount ? `<li class="list-inline-item small mr-1 text-muted">${ prioOffspecCount }os</li>` : `` }
                            </ul>
                        </div>`;
                } else {
                    html += `<div class="js-prio-items text-muted">—</div>`;
                }

                // Received
                const receivedOffspecCount = receivedItems.filter(item => item.is_offspec).length;
                if (receivedItems && receivedItems.length) {
                    html += `<div class="js-received-items">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item mr-1 font-weight-bold">${ receivedItems.length }</li>
                                ${ guild.tier_mode ? `<li class="list-inline-item mr-1">${ getAverageTier(receivedItems, false) }</li>` : '' }
                                ${ receivedOffspecCount ? `<li class="list-inline-item mr-1 small text-muted">${ receivedOffspecCount }os</li>` : `` }
                            </ul>
                        </div>`;
                } else {
                    html += `<div class="js-received-items text-muted">—</div>`;
                }

                // Wishlist (current only)
                const wishlistOffspecCount = wishlistItems.filter(item => item.is_offspec).length;
                if (wishlistItems && wishlistItems.length) {
                    html += `<div class="js-wishlist-items">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item mr-1 font-weight-bold">${ wishlistItems.length }</li>
                                ${ guild.tier_mode ? `<li class="list-inline-item mr-1">${ getAverageTier(wishlistItems, false) }</li>` : '' }
                                ${ wishlistOffspecCount ? `<li class="list-inline-item mr-1 small text-muted">${ wishlistOffspecCount }os</li>` : `` }
                            </ul>
                        </div>`;
                } else {
                    html += `<div class="js-wishlist-items text-muted">—</div>`;
                }
                return html;
            },
            sort: function (data, type, row) {
                let filteredItems = [];
                if (view === VIEW_PRIOS) {
                    filteredItems = row.prios ? row.prios.slice() : [];
                } else if (view === VIEW_RECEIVED) {
                    filteredItems = row.received ? row.received.slice() : [];
                } else if (view === VIEW_WISHLIST) {
                    filteredItems = row.all_wishlists ? row.all_wishlists.filter(item => item.list_number === guild.current_wishlist_number) : [];
                }
                // Filter out items not belonging in the selected instance(s)
                if (instanceIdsToShow.length > 0) {
                    filteredItems = filteredItems.filter(item => instanceIdsToShow.includes(item.instance_id));
                }
                if (!offspecVisible) {
                    filteredItems = filteredItems.filter(item => item.pivot.is_offspec != 1);
                }
                if (!strikethroughVisible) {
                    filteredItems = filteredItems.filter(item => item.pivot.is_received != 1);
                }
                return filteredItems.length;
            },
        },
        orderable : true,
        visible    : isVisible,
        searchable : true,
        type: 'num',
    };
}

/**
 * Get an object that represents a column for an item slot in Datatables.
 *
 * @param {string} name The name of the slot.
 * @param {array} slots The integer values that represent valid item inventory_types for this slot.
 *
 * @return object
 */
function createItemSlotColumn(name, slots) {
    return {
        className : "width-50 fixed-width pt-0 pl-0 pb-0 pr-1",
        title  : name,
        data   : function (row, type, val, meta) {
            let data = [];

            // Filter data by loot type
            if (lootTypeToShow === 'prios') {
                data = row.prios ? row.prios.slice() : [];
            } else if (lootTypeToShow === 'wishlist') {
                data = row.all_wishlists ? row.all_wishlists.slice() : [];
            } else {
                data = row.received ? row.received.slice() : [];
            }

            // Filter data by instance
            if (instanceIdsToShow.length > 0 && data) {
                data = data.filter(item => instanceIdsToShow.includes(item.instance_id));
            }

            return data;
        },
        render : {
            _: function (data, type, row) {
                let filteredItems = (slots ? data.filter(item => slots.includes(item.inventory_type)) : data);
                if (!offspecVisible) {
                    filteredItems = filteredItems.filter(item => item.pivot.is_offspec != 1);
                }
                if (!strikethroughVisible) {
                    filteredItems = filteredItems.filter(item => item.pivot.is_received != 1);
                }

                const offspecCount = filteredItems.filter(item => item.is_offspec).length;

                if (filteredItems && filteredItems.length) {
                    return `<div class="ml-1">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item mr-1 font-weight-bold">${ filteredItems.length }</li>
                                ${ guild.tier_mode ? `<li class="list-inline-item mr-1">${ getAverageTier(filteredItems, false) }</li>` : `` }
                                ${ offspecCount ? `<li class="list-inline-item mr-1 small text-muted">${ offspecCount }os</li>` : `` }
                                ${ slots && filteredItems.length ? `<li class="list-inline-item mr-1">${ getItemListHtml(filteredItems, 'received', row.id, false, false) }</li>` : '' }
                            </ul>
                        </div>`;
                } else {
                    return `<span class="text-muted">—</span>`;
                }
            },
            sort: function (data, type, row) {
                let filteredItems = (slots ? data.filter(item => slots.includes(item.inventory_type)) : data);
                if (!offspecVisible) {
                    filteredItems = filteredItems.filter(item => item.pivot.is_offspec != 1);
                }
                if (!strikethroughVisible) {
                    filteredItems = filteredItems.filter(item => item.pivot.is_received != 1);
                }
                return filteredItems.length;
            },
        },
        orderable : true,
        visible    : (view === 'slots' ? true : false),
        searchable : true,
        type: 'num',
    };
}

// Gets an HTML list of items with pretty wowhead formatting
function getItemListHtml(data, type, characterId, useOrder = false, isVisible = true) {
    let items = `<ol class="js-item-list list-inline mb-0" data-type="${ type }" data-id="${ characterId }" style="${ isVisible ? '' : 'display:none;' }">`;

    $.each(data, function (index, item) {
        let wowheadData = `data-wowhead-link="https://${ wowheadLocale + wowheadSubdomain }.wowhead.com/item=${ item.item_id }"
            data-wowhead="item=${ item.item_id }?domain=${ wowheadLocale + wowheadSubdomain }"`;

        items += `
            <li class="js-has-instance font-weight-normal"
                data-type="${ type }"
                data-id="${ characterId }"
                data-offspec="${ item.pivot.is_offspec ? 1 : 0 }"
                data-instance-id="${ item.instance_id }"
                data-wishlist-number="${item.list_number}"
                value="${ useOrder ? item.pivot.order : '' }">
                ${ guild.tier_mode && item.guild_tier
                    ? getTierHtml(item, true)
                    : ``
                }
                <a href="/${ guild.id }/${ guild.slug }/i/${ item.item_id }/${ slug(item.name) }"
                    class="small ${ item.quality ? 'q' + item.quality : '' } ${ item.pivot.is_received && (item.pivot.type == 'wishlist' || item.pivot.type == 'prio') ? 'font-strikethrough' : '' }"
                    ${ wowheadData }>
                    ${ item.name }
                </a>
                ${ item.pivot.is_offspec ? '<span title="offspec item" class="small font-weight-bold text-muted">OS</span>' : '' }
                <span class="js-watchable-timestamp js-timestamp-title smaller text-muted"
                    data-timestamp="${ item.pivot.received_at ? item.pivot.received_at : item.pivot.created_at }"
                    data-title="added by ${ item.added_by_username } at"
                    data-is-short="1">
                </span>
            </li>`;
    });

    items += `</ol>`;
    return items;
}

function getNotes(data, type, row) {
    return (row.public_note ? `<span class="js-markdown-inline small">${ DOMPurify.sanitize(nl2br(row.public_note)) }</span>` : '—')
        + (row.officer_note ? `<br><small class="font-weight-medium font-italic text-gold">Officer\'s Note</small><br><span class="js-markdown-inline small">${ DOMPurify.sanitize(nl2br(row.officer_note)) }</span>` : '');
}

// Just a simple pretty printout of the raid group's name and color
function getRaidGroupHtml(name, color) {
    if (name) {
        return `<span class="small font-weight-light d-inline">
            <span class="role-circle-small" style="background-color:${ getColorFromDec(parseInt(color)) }"></span>
                ${ name }
            </span>`;
    } else {
        return '';
    }

}

function getTierHtml(item, showColor) {
    return `<span class="text-monospace small font-weight-normal text-${ showColor && item.guild_tier ? 'tier-' + Math.ceil(item.guild_tier) : 'muted' }">${ item.guild_tier ? getItemTierLabel(item, guild.tier_mode) : '&nbsp;' }</span>`;
}

/**
 * Toggles visibility for all of the columns for instances.
 * These contain the data for prios, received loot, and wishlists.
 *
 * @param {bool} isVisible Should we show or hide the columns?
 */
function toggleInstanceCols(isVisible) {
    if (guild.expansion_id === 1) { // Classic
        // FixedHeader extension has a bug and it makes `.visible(true)` slow.
        // So, we're calling `visible(true)` just once.
        // Source: https://datatables.net/forums/discussion/comment/123339/#Comment_123339
        const columnsToToggle = [colInstanceTotal];
        if (instanceIdsToShow.includes(colInstance1) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance1);
        if (instanceIdsToShow.includes(colInstance2) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance2);
        if (instanceIdsToShow.includes(colInstance3) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance3);
        if (instanceIdsToShow.includes(colInstance4) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance4);
        if (instanceIdsToShow.includes(colInstance5) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance5);
        if (instanceIdsToShow.includes(colInstance6) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance6);
        if (instanceIdsToShow.includes(colInstance7) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance7);
        if (instanceIdsToShow.includes(colInstance8) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance8);
        if (instanceIdsToShow.includes(colInstance9) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance9);

        table.columns(columnsToToggle).visible(isVisible);
    } else if (guild.expansion_id === 2) { // TBC
        const columnsToToggle = [colInstanceTotal];
        if (instanceIdsToShow.includes(colInstance1) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance1);
        if (instanceIdsToShow.includes(colInstance2) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance2);
        if (instanceIdsToShow.includes(colInstance3) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance3);
        if (instanceIdsToShow.includes(colInstance4) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance4);
        if (instanceIdsToShow.includes(colInstance5) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance5);
        if (instanceIdsToShow.includes(colInstance6) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance6);
        if (instanceIdsToShow.includes(colInstance7) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance7);
        if (instanceIdsToShow.includes(colInstance8) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance8);
        if (instanceIdsToShow.includes(colInstance9) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance9);
        if (instanceIdsToShow.includes(colInstance10) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance10);
        if (instanceIdsToShow.includes(colInstance11) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance11);

        table.columns(columnsToToggle).visible(isVisible);
    } else if (guild.expansion_id === 3) { // WoTLK
        const columnsToToggle = [colInstanceTotal];

        if (instanceIdsToShow.includes(19) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance1);
        if (instanceIdsToShow.includes(20) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance2);
        if (instanceIdsToShow.includes(21) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance3);
        if (instanceIdsToShow.includes(22) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance4);
        if (instanceIdsToShow.includes(23) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance5);
        if (instanceIdsToShow.includes(24) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance6);
        if (instanceIdsToShow.includes(25) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance7);
        if (instanceIdsToShow.includes(26) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance8);
        if (instanceIdsToShow.includes(27) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance9);
        if (instanceIdsToShow.includes(28) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance10);
        if (instanceIdsToShow.includes(29) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance11);
        if (instanceIdsToShow.includes(30) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance12);
        if (instanceIdsToShow.includes(31) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance13);
        if (instanceIdsToShow.includes(32) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance14);
        if (instanceIdsToShow.includes(33) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance15);
        if (instanceIdsToShow.includes(34) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance16);
        if (instanceIdsToShow.includes(35) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance17);
        if (instanceIdsToShow.includes(36) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance18);
        if (instanceIdsToShow.includes(37) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance19);
        if (instanceIdsToShow.includes(38) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance20);
        if (instanceIdsToShow.includes(39) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance21);
        if (instanceIdsToShow.includes(40) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance22);
        if (instanceIdsToShow.includes(41) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance23);
        if (instanceIdsToShow.includes(42) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance24);

        table.columns(columnsToToggle).visible(isVisible);
    } else if (guild.expansion_id === 4) { // SOD
        const columnsToToggle = [colInstanceTotal];

        if (instanceIdsToShow.includes(43) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance1);
        if (instanceIdsToShow.includes(44) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance2);
        if (instanceIdsToShow.includes(45) || instanceIdsToShow.length === 0) columnsToToggle.push(colInstance3);
        // TODO ADD INSTANCES

        table.columns(columnsToToggle).visible(isVisible);
    } else if (guild.expansion_id === 5) { // Cata
        const columnsToToggle = [colInstanceTotal];

        // TODO add columns

        table.columns(columnsToToggle).visible(isVisible);
    }
}

/**
 * Toggles visibility for all of the columns in the table for inventory slots.
 *
 * @param {bool} isVisible Should we show or hide the columns?
 */
function toggleSlotCols(isVisible) {
    table.columns([
            colSlotTotal,
            colSlotHead,
            colSlotNeck,
            colSlotShoulder,
            colSlotBack,
            colSlotChest,
            colSlotWrists,
            colSlotHands,
            colSlotWaist,
            colSlotLegs,
            colSlotFeet,
            colSlotFinger,
            colSlotTrinket,
            colSlotWeapon,
            colSlotOffhand,
            colSlotRanged,
            colSlotOther,
        ]).visible(isVisible);
}

// In order to prevent these handlers from accidentally being called over and over,
// add them on a timeout. If the timeout hasn't reached 0, and this function is called
// again, the timer will start over and the contained scripts will never have been run.
function callRosterStatHandlers() {
    rosterHandlersTimeout ? clearTimeout(rosterHandlersTimeout) : null;
    rosterHandlersTimeout = setTimeout(function () {
        makeWowheadLinks();
        parseMarkdown();
        trackTimestamps();
    }, 500); // 0.5s delay
}
