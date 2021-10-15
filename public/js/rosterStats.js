var table = null;

var colCharacter = 1;
var colRole = 2;
var colSpec = 3;
var colAttendance = 4;
var colMainRaidGroups = 5;
var colSecondaryRaidGroups = 6;
var colNotes = 7;
var colTotal = 8;
var colDungeons = 13;

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

        table.column(colCharacter)          .visible(true);
        table.column(colRole)               .visible(true);
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

    // Dungeon multiselect could get stuck if clicked too soon
    $(".selectpicker").selectpicker("refresh");

    $(".loadingBarContainer").removeClass("d-flex").hide();
    $("#characterDatatable").show();

    callRosterStatHandlers();
});

function createRosterStatsTable() {
    if ($.fn.DataTable.isDataTable('#characterStatsTable')) {
        $("#characterStatsTable").destroy();
    }

    let rosterStatsTableColumns = [
        {
            title  : `<span class="fas fa-fw fa-user"></span> ${headerCharacter} <span class="text-muted small">(${characters.length})</span>`,
            data   : "character",
            render : function (data, type, row) {
                return `<ul class="no-bullet no-indent">
                    <li>
                        <div class="dropdown text-${ row.class ? row.class.toLowerCase() : '' }">
                            <a class="dropdown-toggle text-4 font-weight-bold text-${ row.class ? row.class.toLowerCase() : '' }"
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

                    ${ !guild.is_attendance_hidden && (row.attendance_percentage || row.raid_count || row.benched_count) ?
                        `<li>
                            <ul class="list-inline">
                            ${ row.raid_count && typeof row.attendance_percentage === 'number' ? `<li class="list-inline-item ${ getAttendanceColor(row.attendance_percentage) }" title="attendance">${ Math.round(row.attendance_percentage * 100) }%</li>` : '' }
                            ${ row.raid_count ? `<li class="list-inline-item small text-muted">${ row.raid_count } raid${ row.raid_count > 1 ? 's' : '' }</li>` : ``}
                            ${ row.benched_count ? `<li class="list-inline-item small text-muted">benched ${ row.benched_count }x</li>` : ``}
                        </li>` : `` }
                </ul>`;
            },
            visible : true,
            width   : "250px",
            className : "width-250",
        },
        {
            title  : "Role",
            data   : "character",
            render : function (data, type, row) {
                return `<span class="text-${ row.class ? row.class.toLowerCase() : '' }">${row.archetype ? row.archetype : ''}</span>`;
            },
            visible : true,
            width   : "100px",
            className : "width-100",
        },
        {
            title  : "Spec",
            data   : "character",
            render : function (data, type, row) {
                return `<span class="text-${ row.class ? row.class.toLowerCase() : '' }">${row.spec_label ? row.spec_label : (row.spec ? row.spec : '')}</span>`;
            },
            visible : true,
            width   : "100px",
            className : "width-100",
        },
        {
            title  : "Raid Group",
            data   : "character",
            render : function (data, type, row) {
                return row.raid_group_name
                    ? `<span class="font-weight-bold d-inline tag">
                        <span class="role-circle" style="background-color:${ getColorFromDec(parseInt(row.raid_group_color)) }"></span>
                        ${ row.raid_group_name ? row.raid_group_name : '' }
                    </span>`
                    : ``;
            },
            visible : true,
            width   : "100px",
            className : "width-100",
        },
        {
            title  : `<span class="fas fa-fw fa-comment-alt-lines"></span> ${headerNotes}`,
            data   : "notes",
            render : function (data, type, row) {
                return getNotes(data, type, row);
            },
            orderable : false,
            visible : true,
            width   : "200px",
            className : "width-200",
        },
        {
            title  : "Username",
            data   : "username",
            render : function (data, type, row) {
                return (row.username ? row.username : null);
            },
            visible : false,
        },
        {
            title  : "Discord Username",
            data   : "discord_username",
            render : function (data, type, row) {
                return (row.discord_username ? row.discord_username : null);
            },
            visible : false,
        },
        {
            title  : "Raids Attended",
            data   : "raid_count",
            render : function (data, type, row) {
                return (row.raid_count ? row.raid_count : null);
            },
            visible    : false,
            searchable : false,
        },
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

    if ('wishlist') {
        rosterStatsTableColumns.push(
            {
                title  : "Total",
                data   : "all_wishlists",
                render : function (data, type, row) {
                    return 'wishlist stats';
                },
                visible    : true,
                searchable : true,
            }
        );
        instances.forEach(function (instance, index) {
            rosterStatsTableColumns.push(
                {
                    title  : instance.short_name,
                    data   : "all_wishlists",
                    render : function (data, type, row) {
                        return 'wishlist stats';
                    },
                    visible    : true,
                    searchable : true,
                }
            );
        });
    } else if ('prios') {

    } else if ('received') {

    } else if ('slots') {

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
        fixedHeader : true, // Header row sticks to top of window when scrolling down
        drawCallback : function () {
            callRosterStatHandlers();
        },
        initComplete: function () {
            // Columns that we want to filter by.
            const filterColumns = [colClass, colRaidGroup];

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
                } else if (index == colRaidGroup) {
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

// Gets an HTML list of items with pretty wowhead formatting
function getItemListHtml(data, type, characterId, useOrder = false, showInstances = false, listClass = null, isVisible = true, header = null) {
    let items = `<ol class="no-indent js-item-list mb-2 ${ listClass }" data-type="${ type }" data-id="${ characterId }" style="${ isVisible ? '' : 'display:none;' }">`;

    if (header) {
        items += `<li class="small text-muted no-bullet " data-type="${ type }" data-id="${ characterId }">${header}</li>`;
    }

    let initialLimit = 4;

    let lastInstanceId = null;
    let lastRaidGroupId = null;
    $.each(data, function (index, item) {

        // Skip prio item if raid group is disabled or not found in the guild
        // if (type == 'prio' && item.pivot.raid_group_id && guild.raidGroups.filter(raidGroup => (raidGroup.id == item.pivot.raid_group_id)).length < 1) {
        //     console.log(item.pivot.raid_group_id + ' not found');
        //     return false;
        // }

        let clipItem = false;
        if (index >= initialLimit) {
            clipItem = true;
            if (index == initialLimit) {
                items += `<li class="js-show-clipped-items small cursor-pointer no-bullet " data-type="${ type }" data-id="${ characterId }">show ${ data.length - initialLimit } more…</li>`;
            }
        }

        if (type == 'prio' && item.pivot.raid_group_id && item.pivot.raid_group_id != lastRaidGroupId) {
            lastRaidGroupId = item.pivot.raid_group_id;
            let raidGroupName = '';
            if (raidGroups.length) {
                let raidGroup = raidGroups.find(raidGroup => raidGroup.id === item.pivot.raid_group_id);
                 if (raidGroup) {
                    raidGroupName = raidGroup.name;
                }
            }
            items += `
                <li data-raid-group-id="" class="${ clipItem ? 'js-clipped-item' : '' } js-item-wishlist-character no-bullet font-weight-normal font-italic text-muted small"
                    style="${ clipItem ? 'display:none;' : '' }"
                    data-type="${ type }"
                    data-id="${ characterId }">
                    ${ raidGroupName }
                </li>
            `;
        }

        if (showInstances && item.instance_id && item.instance_id != lastInstanceId) {
            lastInstanceId = item.instance_id;
            items += `
                <li class="js-has-instance ${ clipItem ? 'js-clipped-item' : '' } no-bullet font-weight-normal font-italic text-muted small"
                    style="${ clipItem ? 'display:none;' : '' }"
                    data-type="${ type }"
                    data-id="${ characterId }"
                    data-instance-id="${ item.instance_id }">
                    ${ item.instance_name }
                </li>
            `;
        }

        let wowheadData = `data-wowhead-link="https://${ wowheadLocale + wowheadSubdomain }.wowhead.com/item=${ item.item_id }"
            data-wowhead="item=${ item.item_id }?domain=${ wowheadLocale + wowheadSubdomain }"`;

        items += `
            <li class="js-has-instance font-weight-normal ${ clipItem ? 'js-clipped-item' : '' }"
                data-type="${ type }"
                data-id="${ characterId }"
                data-offspec="${ item.pivot.is_offspec ? 1 : 0 }"
                data-instance-id="${ item.instance_id }"
                data-wishlist-number="${item.list_number}"
                value="${ useOrder ? item.pivot.order : '' }"
                style="${ clipItem ? 'display:none;' : '' }">
                ${ guild.tier_mode ?
                    `<span class="text-monospace font-weight-medium text-tier-${ item.guild_tier ? item.guild_tier : '' }">${ item.guild_tier ? getItemTierLabel(item, guild.tier_mode) : '&nbsp;' }</span>`
                : `` }
                <a href="/${ guild.id }/${ guild.slug }/i/${ item.item_id }/${ slug(item.name) }"
                    class="${ item.quality ? 'q' + item.quality : '' } ${ item.pivot.is_received && (item.pivot.type == 'wishlist' || item.pivot.type == 'prio') ? 'font-strikethrough' : '' }"
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

    if (data.length > initialLimit) {
        items += `<li class="js-hide-clipped-items small cursor-pointer no-bullet" style="display:none;" data-type="${ type }" data-id="${ characterId }">show less</li>`;
    }

    items += `</ol>`;
    return items;
}

function getNotes(data, type, row) {
    let secondaryRaidGroups = '';
    if (row.secondary_raid_groups && row.secondary_raid_groups.length) {
        secondaryRaidGroups = `<ul class="list-inline">`;
        row.secondary_raid_groups.forEach(function (raidGroup, index) {
            secondaryRaidGroups += `<li class="list-inline-item small"><span class="tag text-muted"><span class="role-circle align-fix" style="background-color:${ getColorFromDec(parseInt(raidGroup.color)) }"></span>${raidGroup.name}</span></li>`;
        });
        secondaryRaidGroups += `</ul>`;
    }
    return (row.public_note ? `<span class="js-markdown-inline">${ DOMPurify.sanitize(nl2br(row.public_note)) }</span>` : '—')
        + (row.officer_note ? `<br><small class="font-weight-bold font-italic text-gold">Officer\'s Note</small><br><span class="js-markdown-inline">${ DOMPurify.sanitize(nl2br(row.officer_note)) }</span>` : '')
        + (secondaryRaidGroups ? `<br>${secondaryRaidGroups}` : ``);
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
