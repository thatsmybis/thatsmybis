var table = null;

var view = "slots";

var colCharacter = 0;
var colArchetype = 1;
var colAttendance = 2;
var colMainRaidGroup = 3;
var colNotes = 4;
var colClass = 5;
var colSlotHead     = 10;
var colSlotNeck     = 11;
var colSlotShoulder = 12;
var colSlotBack     = 13;
var colSlotChest    = 14;
var colSlotWrists   = 15;
var colSlotHands    = 16;
var colSlotWaist    = 17;
var colSlotLegs     = 18;
var colSlotFeet     = 19;
var colSlotFinger   = 20;
var colSlotTrinket  = 21;
var colSlotWeapon   = 22;
var colSlotOffhand  = 23;
var colSlotRanged   = 24;
var colSlotOther    = 25;

var allItemsVisible = false;
var offspecVisible = true;

// For making sure we don't spam request handlers to be added.
var rosterHandlersTimeout = null;

$(document).ready( function () {
   var table = createRosterStatsTable();

   $(".toggle-column").click(function(e) {
        e.preventDefault();
        let column = table.column($(this).attr("data-column"));
        column.visible(!column.visible());
   });

   $(".toggle-column-default").click(function(e) {
        e.preventDefault();
// TODO: Use this for toggling between slot/wishlist/prio/received views
        table.column(colCharacter)          .visible(true);
        table.column(colArchetype)               .visible(true);
        table.column(colSpec)               .visible(true);
        table.column(colAttendance)         .visible(true);
        table.column(colMainRaidGroup)      .visible(true);
        table.column(colSecondaryRaidGroups).visible(false);
        table.column(colNotes)              .visible(false);
        table.column(colTotal)              .visible(true);
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
            hideOffspecItems();
        } else {
            offspecVisible = true;
            showOffspecItems();
        }
    });

    $(".js-show-all-items").click(function () {
        if (allItemsVisible) {
            $(".js-item-list").hide();
            allItemsVisible = false;
        } else {
            $(".js-item-list").show();
            allItemsVisible = true;
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
                            <div class="dropdown text-${ row.class ? row.class.toLowerCase() : '' }">
                                <a class="dropdown-toggle font-weight-bold text-${ row.class ? row.class.toLowerCase() : '' }"
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
                            ${ getRaidGroupHtml(row.raid_group_name, row.raid_group_color) }
                        </li>
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
                return `<span class="small text-${ row.class ? row.class.toLowerCase() : '' }">${row.display_archetype ? row.display_archetype : ''}</span>
                    <br>
                    <span class="small text-${ row.class ? row.class.toLowerCase() : '' }">${row.display_spec ? row.display_spec : ''}</span>`;
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
        },
        // Raid Groups (for filtering)
        {
            title  : "Raid",
            data   : "character",
            render : function (data, type, row) {
                if (row.raid_group_name) {
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

    // Add the columns for each item slot
    rosterStatsTableColumns.push(
        getItemColumnBySlot("Head", [SLOT_HEAD]),
        getItemColumnBySlot("Neck", [SLOT_NECK]),
        getItemColumnBySlot("Shoulders", [SLOT_SHOULDERS]),
        getItemColumnBySlot("Back", [SLOT_BACK]),
        getItemColumnBySlot("Chest", [SLOT_CHEST_1, SLOT_CHEST_2]),
        getItemColumnBySlot("Wrist", [SLOT_WRIST]),
        getItemColumnBySlot("Waist", [SLOT_WAIST]),
        getItemColumnBySlot("Hands", [SLOT_HANDS]),
        getItemColumnBySlot("Legs", [SLOT_LEGS]),
        getItemColumnBySlot("Feet", [SLOT_FEET]),
        getItemColumnBySlot("Finger", [SLOT_FINGER]),
        getItemColumnBySlot("Trinket", [SLOT_TRINKET]),
        getItemColumnBySlot("Weapon", [SLOT_WEAPON_MAIN_HAND, SLOT_WEAPON_TWO_HAND, SLOT_WEAPON_ONE_HAND, SLOT_WEAPON_OFF_HAND]),
        getItemColumnBySlot("Offhand", [SLOT_SHIELD, SLOT_OFFHAND]),
        getItemColumnBySlot("Ranged /Relic", [SLOT_RANGED_1, SLOT_RANGED_2, SLOT_THROWN, SLOT_RELIC]),
        getItemColumnBySlot("Misc", [SLOT_MISC, SLOT_SHIRT, SLOT_BAG, SLOT_AMMO])
    );

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
        fixedHeader : true, // Header row sticks to top of window when scrolling down
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
                if (index == colClass) {
                    select1 = $("#class_filter");
                    select2 = null;
                } else if (index == colArchetype) {
                    select1 = $("#archetype_filter");
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
        if (instanceIds.length) {
            // Start with all instanced items hidden
            $("li.js-has-instance").hide();

            // Create a list of the instances we want to show
            let selectorsToShow = "";
            instanceIds.forEach(instanceId => {
                selectorsToShow += "li.js-has-instance[data-instance-id='" + instanceId + "'],";
            });
            selectorsToShow = selectorsToShow.replace(/(^,)|(,$)/g, ""); // Trim trailing commas

            // Show the instances we want
            $(selectorsToShow).show();
        } else {
            // show all instanced items
            $("li.js-has-instance").show();
        }
    });
}

// Take in an array of items, return the average tier number.
// Only count items that have tiers associated with them.
function getAverageTier(items) {
    const filteredItems = items.filter(item => item.guild_tier);
    const average = filteredItems.reduce((ac, a) => a.guild_tier + ac, 0) / filteredItems.length;
    const mockItem = {'guild_tier': average};
    return getTierHtml(mockItem);
}

/**
 * Get the object that represents a column in Datatables.
 *
 * @param {string} name The name of the slot.
 * @param {array} slots The integer values that represent valid item inventory_types for this slot.
 *
 * @return string
 */
function getItemColumnBySlot(name, slots) {
    return {
        // Nisstyyy was here
        className : "width-50 fixed-width pt-0 pl-0 pb-0 pr-1",
        title  : name,
        data   : "received",
        render : function (data, type, row) {
            const filteredItems = data.filter(item => slots.includes(item.inventory_type))
            if (filteredItems && filteredItems.length) {
                return `<div class="ml-1">
                        <span class="font-weight-medium">${ filteredItems.length }</span>
                        ${ guild.tier_mode ? getAverageTier(filteredItems) : `` }
                        ${ getItemListHtml(filteredItems, 'received', row.id, false, false) }
                    </div>`;
            } else {
                return `<span class="text-muted">—</span>`;
            }
        },
        orderable : true,
        visible    : (view === 'slots' ? true : false),
        searchable : true,
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
                    ? getTierHtml(item)
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
        return `<span class="small font-weight-normal d-inline">
            <span class="role-circle" style="background-color:${ getColorFromDec(parseInt(color)) }"></span>
                ${ name }
            </span>`;
    } else {
        return '';
    }

}

function getTierHtml(item) {
    return `<span class="text-monospace small font-weight-normal text-tier-${ item.guild_tier ? Math.ceil(item.guild_tier) : '' }">${ item.guild_tier ? getItemTierLabel(item, guild.tier_mode) : '&nbsp;' }</span>`;
}

function hideOffspecItems() {
    $("[data-offspec='1']").hide();
}

function showOffspecItems() {
    $("[data-offspec='1']").show();
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
