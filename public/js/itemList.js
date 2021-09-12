var table = null;

var colSource   = 0;
var colName     = 1;
var colPrios    = 2;
var colWishlist = 3;
var colReceived = 4;
var colNotes    = 5;
var colPriority = 6;

// For keeping track of the loot's source
var lastSource = null;

$(document).ready( function () {
   table = createTable();

   $(".toggle-column").click(function(e) {
        e.preventDefault();
        let column = table.column($(this).attr("data-column"));
        column.visible(!column.visible());
   });

   $(".toggle-column-default").click(function(e) {
        e.preventDefault();

        table.column(colName)    .visible(true);
        table.column(colPrios)   .visible(true);
        table.column(colWishlist).visible(true);
        table.column(colReceived).visible(true);
        table.column(colNotes)   .visible(true);
        table.column(colPriority).visible(true);
   });

    // Triggered when a column is made visible
    table.on('column-visibility.dt', function (e, settings, column, state) {
        // Refresh wowhead links to show stlying.
        // wowhead's script previously ignored these links if they weren't visible
        makeWowheadLinks();
        trackTimestamps();
        parseMarkdown();
    });

    // Filter out characters based on the raid group they are in
    $("#raid_group_filter").on('change', function () {
        let raidGroupId = $(this).val();

        if (raidGroupId) {
            $(".js-item-wishlist-character[data-raid-group-id!='" + raidGroupId + "']").hide();
            $(".js-item-wishlist-character[data-raid-group-id='" + raidGroupId + "']").show();
        } else {
            $(".js-item-wishlist-character").show();
        }
    }).change();

    addWishlistFilterHandlers();
    trackTimestamps();
});

function createTable(lastSource) {
    itemTable = $("#itemTable").DataTable({
        autoWidth : false,
        data      : items,
        columns   : [
            {
                title  : `<span class="fas fa-fw fa-skull-crossbones"></span> ${headerBoss}`,
                data   : "",
                render : function (data, type, row) {
                    if (row.source_name) {
                        thisSource = row.source_name;
                    }

                    return `
                    <ul class="no-bullet no-indent mb-0">
                        ${ row.source_name ? `
                            <li>
                                <span class="font-weight-bold">
                                    ${ row.source_name }
                                </span>
                            </li>` : `` }
                    </ul>`;
                },
                visible   : true,
                width     : "130px",
                className : "text-right width-130",
            },
            {
                title  : `<span class="fas fa-fw fa-sack text-success"></span> ${headerLoot} <span class="text-muted small">(${ items.length })</span>`,
                data   : "",
                render : function (data, type, row) {
                    return getItemLink(row);
                },
                visible : true,
                width   : "330px",
                className : "width-330",
            },
            {
                title  : `<span class="fas fa-fw fa-sort-amount-down text-gold"></span> ${headerPrios}`,
                data   : guild.is_attendance_hidden ? "priod_characters" : "priod_characters_with_attendance",
                render : function (data, type, row) {
                    return data && data.length ? createCharacterListHtml(data, 'prio', row.item_id, null) : '—';
                },
                orderable : false,
                visible : showPrios ? true : false,
                width   : "300px",
                className : "width-300",
            },
            {
                title  : `<span class="text-legendary fas fa-fw fa-scroll-old"></span> ${headerWishlist}`,
                data   : guild.is_attendance_hidden ? "wishlist_characters" : "wishlist_characters_with_attendance",
                render : function (data, type, row) {
                    if (data && data.length) {
                        let list = '';
                        if (currentWishlistNumber) { // return only the selected wishlist
                            const filteredData = data.filter(character => character.pivot.list_number == currentWishlistNumber);
                            if (filteredData.length) {
                                list = createCharacterListHtml(filteredData, 'wishlist', row.item_id, null);
                            }
                        } else { // return all wishlists
                            for (i = 1; i <= maxWishlistLists; i++) {
                                const filteredData = data.filter(character => character.pivot.list_number == i);
                                if (filteredData.length) {
                                    const header = headerWishlist + " " + i;
                                    list += createCharacterListHtml(filteredData, 'wishlist', row.item_id, header);
                                }
                            }
                        }

                        // Nothing in list... just show a dash.
                        if (list == '') {
                            list = '—';
                        }

                        return list;
                    } else {
                        return '—';
                    }
                },
                orderable : false,
                visible : showWishlist ? true : false,
                width   : "400px",
                className : "width-400",
            },
            {
                title  : `<span class="text-success fas fa-fw fa-sack"></span> ${headerReceived}`,
                data   : "received_and_recipe_characters",
                render : function (data, type, row) {
                    return data && data.length ? createCharacterListHtml(data, 'received', row.item_id, null) : '—';
                },
                orderable : false,
                visible : true,
                width   : "300px",
                className : "width-300",
            },
            {
                title  : `<span class="fas fa-fw fa-comment-alt-lines"></span> ${headerNotes}`,
                data   : "guild_note",
                render : function (data, type, row) {
                    return getNotes(row, data);
                },
                orderable : false,
                visible : showNotes ? true : false,
                width   : "200px",
                className : "width-200",
            },
            {
                title  : `<span class="fas fa-fw fa-comment-alt-lines"></span> ${headerPrioNotes}`,
                data   : "guild_priority",
                render : function (data, type, row) {
                    return (data ? `<span class="js-markdown-inline">${ nl2br(data) }</span>` : '—');
                },
                orderable : false,
                visible : showNotes ? true : false,
                width   : "200px",
                className : "width-200",
            },
        ],
        order       : [], // Disable initial auto-sort; relies on server-side sorting
        paging      : false,
        fixedHeader : true, // Header row sticks to top of window when scrolling down
        drawCallback : function () {
            makeWowheadLinks();
            parseMarkdown();
            trackTimestamps();
        },
        initComplete: function () {
            makeWowheadLinks();
            parseMarkdown();
        },
        createdRow : function (row, data, dataIndex) {
            // Add a top border style between different loot sources
            if (dataIndex == 0 || lastSource == null) {
                lastSource = data.source_name;
            }
            if (data.source_name != lastSource) {
                $(row).addClass("top-border");
                lastSource = data.source_name;
            }
        }
    });

    return itemTable;
}

function addWishlistFilterHandlers() {
    $("#wishlist_filter").on('change', function () {
        currentWishlistNumber = $(this).val();
        itemTable.rows().invalidate().draw();
    }).change();
}

// Gets an HTML list of characters
function createCharacterListHtml(data, type, itemId, header = null) {
    let characters = `<ul class="list-inline js-item-list mb-0" data-type="${ type }" data-id="${ itemId }">`;

    if (header) {
        characters += `<li class="js-item-wishlist-character no-bullet font-weight-bold text-muted small">
            ${ header }
        </li>`;
    }

    let initialLimit = 4;

    let lastRaidGroupId = null;
    $.each(data, function (index, character) {
        if (type == 'prio' && character.pivot.raid_group_id && character.pivot.raid_group_id != lastRaidGroupId) {
            lastRaidGroupId = character.pivot.raid_group_id;
            let raidGroupName = '';
            if (raidGroups.length) {
                let raidGroup = raidGroups.find(raidGroup => raidGroup.id === character.pivot.raid_group_id);
                 if (raidGroup) {
                    raidGroupName = raidGroup.name;
                }
            }
            characters += `
                <li data-raid-group-id="" class="js-item-wishlist-character no-bullet font-weight-normal text-muted small">
                    ${ raidGroupName }
                </li>
            `;
        }

        if (type == 'wishlist' && (
            (character.raid_group_id && character.raid_group_id != lastRaidGroupId) ||
            (!character.raid_group_id && lastRaidGroupId)
        )) {
            let raidGroupName = '';
            if (!character.raid_group_id && lastRaidGroupId) {
                raidGroupName = 'no raid group';
                lastRaidGroupId = null;
            } else {
                lastRaidGroupId = character.raid_group_id;
                if (raidGroups.length) {
                    let raidGroup = raidGroups.find(raidGroup => raidGroup.id === character.raid_group_id);
                     if (raidGroup) {
                        raidGroupName = raidGroup.name;
                    }
                }
            }
            characters += `
                <li data-raid-group-id="" class="js-item-wishlist-character no-bullet font-weight-normal text-muted small">
                    ${ raidGroupName }
                </li>
            `;
        }

        characters += `
            <li data-raid-group-id="${ type == 'prio' ? character.pivot.raid_group_id : character.raid_group_id }"
                value="${ type == 'prio' ? character.pivot.order : '' }"
                class="js-item-wishlist-character list-inline-item font-weight-normal mb-1 mr-0 ${ character.pivot.type != 'received' && character.pivot.received_at ? 'font-strikethrough' : '' }">
                <a href="/${ guild.id }/${ guild.slug }/c/${ character.id }/${ character.slug }"
                    title="${ character.raid_group_name ? character.raid_group_name + ' -' : '' } ${ character.level ? character.level : '' } ${ character.race ? character.race : '' } ${ character.spec ? character.spec : '' } ${ character.class ? character.class : '' } ${ character.raid_count ? `(${ character.raid_count } raid${ character.raid_count > 1 ? 's' : '' } attended)` : `` } ${ character.username ? '(' + character.username + ')' : '' }"
                    class="tag text-muted d-inline">
                    <span class="">${ type !== 'received' && character.pivot.order ? character.pivot.order : '' }</span>
                    <span class="small font-weight-bold">${ character.pivot.is_offspec ? 'OS' : '' }</span>
                    <span class="role-circle" style="background-color:${ getColorFromDec(character.raid_group_color) }"></span>
                    <span class="text-${ character.class ? character.class.toLowerCase() : '' }-important">${ character.name }</span>
                    ${ character.is_alt ? `
                        <span class="text-warning">${localeAlt}</span>
                    ` : '' }
                    ${ !guild.is_attendance_hidden && (character.attendance_percentage || character.raid_count) ?
                        `${ character.raid_count && typeof character.attendance_percentage === 'number' ? `<span title="attendance" class="smaller ${ getAttendanceColor(character.attendance_percentage) }">${ Math.round(character.attendance_percentage * 100) }%</span>` : '' }${ character.raid_count ? `<span class="smaller"> ${ character.raid_count }r</span>` : ``}
                    ` : `` }
                    <span class="js-watchable-timestamp smaller"
                        data-timestamp="${ character.pivot.created_at }"
                        data-is-short="1">
                    </span>
                    <span style="display:none;">${ character.discord_username } ${ character.username }</span>
                </a>
            </li>`;
    });

    characters += `</ul>`;
    return characters;
}

function getNotes(row, note) {
    let childItems = null;
    // Uncomment to show child items
    // if (row.child_items.length) {
    //     childItems = '<ul class="list-inline">';
    //     row.child_items.forEach(function (item, index) {
    //         item.quality = 0;
    //         childItems += `<li class="list-inline-item smaller">${ getItemLink(item, ' ') }</li>`;
    //     });
    //     childItems += '</ul>';
    // }
    if (note || childItems) {
        note = `<span class="js-markdown-inline">${ note ? DOMPurify.sanitize(nl2br(note)) : '' }</span>${ childItems ? childItems : '' }`;
    } else {
        note = '—';
    }
    return note;
}

function getItemLink(row, iconSize = null) {
    let wowheadData =
    `data-wowhead-link="https://${ wowheadLocale + wowheadSubdomain }.wowhead.com/item=${ row.item_id }"
    data-wowhead="item=${ row.item_id }?domain=${ wowheadLocale + wowheadSubdomain }"`;

    if (iconSize) {
        wowheadData += ` data-wh-icon-size="${ iconSize }"`;
    }

    let url = "";
    if (guild) {
        url = `/${ guild.id }/${ guild.slug }/i/${ row.item_id }/${ slug(row.name) }`;
    } else {
        url = "";
    }
    return `
    <ul class="no-bullet no-indent mb-0">
        <li>
            ${ guild.tier_mode ?
                `<span class="text-monospace font-weight-medium text-tier-${ row.guild_tier ? row.guild_tier : '' }">${ row.guild_tier ? getItemTierLabel(row, guild.tier_mode) : '&nbsp;' }</span>`
            : `` }
            <a href="${ url }"
                class="${ row.quality ? 'q' + row.quality : '' }"
                ${ wowheadData }>
                ${ row.name }
            </a>
        </li>
    </ul>`;
}
