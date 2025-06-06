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

var offspecVisible = true;

// For making sure we don't spam request handlers to be added.
var itemListHandlersTimeout = null;

// I put this here to save time on lookups on every item
var raidGroupMap = {};
var guildCharactersMap = {};

$(document).ready( function () {
    if (raidGroups.length) {
        for (const raidGroup of raidGroups) {
            raidGroupMap[raidGroup.id] = raidGroup;
        }
    }

    if (!guild.is_attendance_hidden && Array.isArray(guildCharacters)) {
        for (const char of guildCharacters) {
            guildCharactersMap[char.id] = char;
        }
    }

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
        callItemListHandlers();
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

    $(".js-hide-offspec-items").click(function() {
        if (offspecVisible) {
            offspecVisible = false;
            hideOffspecItems();
        } else {
            offspecVisible = true;
            showOffspecItems();
        }
    });

    addWishlistFilterHandlers();

    $(".loadingBarContainer").removeClass("d-flex").hide();
    $("#itemDatatable").show();

    callItemListHandlers();
});

function createTable() {
    itemTable = $("#itemTable").DataTable({
        autoWidth : false,
        data      : items,
        // To disable fuzzy search:
        // search: {
        //     smart: false
        // },
        oLanguage: {
            sSearch: "<abbr title='Fuzzy searching is ON. To search exact text, wrap your search in \"quotes\"'>Search</abbr>"
        },
        columns   : [
            {
                title  : `<span class="fas fa-fw fa-skull-crossbones"></span> ${headerBoss}`,
                data   : "",
                render : function (data, type, row) {
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
                data   : "priod_characters",
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
                data   : "wishlist_characters",
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
                                    let header = null;
                                    if (wishlistNames && wishlistNames[i - 1]) {
                                        header = wishlistNames[i - 1];
                                    } else {
                                        header = headerWishlist + ' ' + i;
                                    }
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
                    return (data ?
                    `<span class="js-preview-text js-markdown-inline">${ DOMPurify.sanitize(nl2br(data.substring(0, 200))) }</span>
                    ${ data.length > 200 ?
                        `<span class="js-full-text js-markdown-inline" style="display:none;">${ DOMPurify.sanitize(nl2br(data)) }</span>
                        <span class="js-show-text text-link cursor-pointer">show more…</span>
                        <span class="js-hide-text text-link cursor-pointer" style="display:none;">show less…</span>` : ``}`
                    : '—');
                },
                orderable : false,
                visible : showNotes ? true : false,
                width   : "200px",
                className : "width-200",
            },
        ],
        order       : [], // Disable initial auto-sort; relies on server-side sorting
        paging      : false,
        fixedHeader : { // Header row sticks to top of window when scrolling down
            headerOffset : 43,
        },
        drawCallback : function () {
            callItemListHandlers();
        },
        initComplete: function () {
            callItemListHandlers();
        },
        createdRow : function (row, data, dataIndex) {
            // Add a top border style between different loot sources
            if (dataIndex == 0 || lastSource == null) {
                lastSource = data.source_name;
            }
            if (data.source_name != lastSource) {
                $(row).addClass("top-border padded-anchor");
                $(row).attr('id', data.source_slug ? data.source_slug.trim() : null);
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
    const characters = [];

    characters.push( `<ul class="list-inline js-item-list mb-0" data-type="${ type }" data-id="${ itemId }">`);

    if (header) {
        characters.push(`<li class="js-item-wishlist-character no-bullet font-weight-bold text-muted small">${ header }</li>`);
    }

    let initialLimit = 4;

    let lastRaidGroupId = null;
    for (let index = 0; index < data.length; index++) {
        character = data[index];

        let attendanceCharacter = null;
        if (!guild.is_attendance_hidden) {
            attendanceCharacter = guildCharactersMap[character.id];
        }

        if (type == 'prio' && character.pivot.raid_group_id && character.pivot.raid_group_id != lastRaidGroupId) {
            lastRaidGroupId = character.pivot.raid_group_id;
            const raidGroupName = raidGroupMap[character.pivot.raid_group_id]?.name || '';
            characters.push(`<li data-raid-group-id="" class="js-item-wishlist-character no-bullet font-weight-normal text-muted small">${ raidGroupName }</li>`);
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
                    let raidGroupName = raidGroupMap[character.raid_group_id]?.name;
                }
            }
            characters.push(`<li data-raid-group-id="" class="js-item-wishlist-character no-bullet font-weight-normal text-muted small">${ raidGroupName }</li>`);
        }

        characters.push(
`<li data-raid-group-id="${ type == 'prio' ? character.pivot.raid_group_id : character.raid_group_id }"
data-offspec="${ character.pivot.is_offspec ? 1 : 0}"
value="${ type == 'prio' ? character.pivot.order : '' }"
class="js-item-wishlist-character list-inline-item font-weight-normal mb-1 mr-0 ${ character.pivot.type != 'received' && character.pivot.received_at ? 'font-strikethrough' : '' }">
<span class="tag text-muted d-inline">
  <a href="/${ guild.id }/${ guild.slug }/c/${ character.id }/${ character.slug }"
    title="${ character.raid_group_name ? character.raid_group_name + ' -' : '' } ${ character.level ? character.level : '' } ${ character.race ? character.race : '' } ${ character.spec ? character.spec : '' } ${ character.class ? character.class : '' } ${ character.raid_count ? `(${ character.raid_count } raid${ character.raid_count > 1 ? 's' : '' } attended)` : `` } ${ character.username ? '(' + character.username + ')' : '' }"
    class="text-muted">
    <span class="">${ type !== 'received' && character.pivot.order ? character.pivot.order : '' }</span>
    <span class="small font-weight-bold">${ character.pivot.is_offspec ? 'OS' : '' }</span>
    <span class="role-circle" style="background-color:${ getColorFromDec(character.raid_group_color) }"></span>
    <span class="text-${ character.class ? slug(character.class) : '' }-important">${ character.name }</span>
    ${ character.is_alt ? `
        <span class="text-warning">${localeAlt}</span>
    ` : '' }
    ${ type !== 'received' && attendanceCharacter && (attendanceCharacter.attendance_percentage || attendanceCharacter.raid_count) ?
        `${ attendanceCharacter.raid_count && typeof attendanceCharacter.attendance_percentage === 'number'
            ? `<span title="attendance" class="smaller ${ getAttendanceColor(attendanceCharacter.attendance_percentage) }">${ Math.round(attendanceCharacter.attendance_percentage * 100) }%</span>`
            : '' }${ attendanceCharacter.raid_count ? `<span class="smaller"> ${ attendanceCharacter.raid_count }r</span>` : ``}
    ` : `` }
  </a>
  <span class="js-watchable-timestamp js-timestamp-title smaller"
    data-timestamp="${ character.pivot.created_at }"
    data-is-short="1">
  </span>
  <span style="display:none;">${ character.discord_username } ${ character.username }</span>
  ${ character.pivot.note ? `<span class="smaller text-muted text-underline" title="${ character.pivot.note }">note</span>` : '' }
</span>
</li>`);
    };

    characters.push(`</ul>`);

    return characters.join('');
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
    let officerNote = 'guild_officer_note' in row ? row.guild_officer_note : null;
    // console.log(row.guildofficer_note, officerNote);
    if (note || officerNote || childItems) {
        note =
            `${ note ? `<span class="js-preview-text js-markdown-inline">${ DOMPurify.sanitize(nl2br(note.substring(0, 200))) }</span>
                ${ note.length > 200 ?
                    `<span class="js-full-text js-markdown-inline" style="display:none;">${ DOMPurify.sanitize(nl2br(note)) }</span>
                    <span class="js-show-text text-link cursor-pointer">show more…</span>
                    <span class="js-hide-text text-link cursor-pointer" style="display:none;">show less…</span>` : ``}`
            : ''}
            ${ officerNote && note ? `<br>` : `` }
            ${ officerNote ?
            `<small class="font-weight-bold font-italic text-gold">Officer\'s Note</small><br>
            <span class="js-preview-text js-markdown-inline">${ DOMPurify.sanitize(nl2br(officerNote.substring(0, 200))) }</span>
            ${ officerNote.length > 200 ?
                `<span class="js-full-text js-markdown-inline" style="display:none;">${ DOMPurify.sanitize(nl2br(officerNote)) }</span>
                <span class="js-show-text text-link cursor-pointer">show more…</span>
                <span class="js-hide-text text-link cursor-pointer" style="display:none;">show less…</span>` : ``}`
            : ''}
            ${ childItems ? childItems : '' }`;
    } else {
        note = '—';
    }
    return note;
}

function getItemLink(row, iconSize = null) {
    let wowheadUrl = `https://${ wowheadLocale + wowheadSubdomain }.wowhead.com/item=${ row.item_id }`;
    let wowheadData = `data-wowhead-link="${ wowheadUrl }" data-wowhead="item=${ row.item_id }?domain=${ wowheadLocale + wowheadSubdomain }"`;

    if (iconSize) {
        wowheadData += ` data-wh-icon-size="${ iconSize }"`;
    }

    const hasGuild = guild && !$.isEmptyObject(guild);
    let url = "";
    if (hasGuild) {
        url = `/${ guild.id }/${ guild.slug }/i/${ row.item_id }/${ slug(row.name) }`;
    } else {
        console.log('no guild');
        url = wowheadUrl;
    }

    let factionHtml = ``;
    if (row.faction && !(guild && guild.faction)) {
        if (row.faction === 'h') {
            factionHtml = `<span class="text-horde font-weight-bold" title="Horde">H</span>`;
        } else if (row.faction === 'a') {
            factionHtml = `<span class="text-alliance font-weight-bold" title="Alliance">A</span>`;
        }
    }

    let heroicHtml = ``;
    if (row.is_heroic) {
        heroicHtml = `<span class="text-uncommon small" title="Heroic">Heroic</span>`;
    }

    return `
    <ul class="no-bullet no-indent mb-0">
        <li>
            ${ guild.tier_mode ?
                `<span class="text-monospace font-weight-medium text-tier-${ row.guild_tier ? row.guild_tier : '' }">${ row.guild_tier ? getItemTierLabel(row, guild.tier_mode) : '&nbsp;' }</span>`
            : `` }
            <a href="${ url }"
                target="${ !hasGuild ? "_blank" : "" }"
                class="${ row.quality ? 'q' + row.quality : '' }"
                ${ wowheadData }>
                ${ row.name }
            </a>
            ${ heroicHtml }
            ${ factionHtml }
        </li>
    </ul>`;
}

function hideOffspecItems() {
    $("[data-offspec='1']").hide();
}

// In order to prevent these handlers from accidentally being called over and over,
// add them on a timeout. If the timeout hasn't reached 0, and this function is called
// again, the timer will start over and the contained scripts will never have been run.
function callItemListHandlers() {
    itemListHandlersTimeout ? clearTimeout(itemListHandlersTimeout) : null;
    itemListHandlersTimeout = setTimeout(function () {
        makeWowheadLinks();
        parseMarkdown();
        trackTimestamps();
        addTooltips();
    }, 500); // 0.5s delay
}

function showOffspecItems() {
    $("[data-offspec='1']").show();
}
