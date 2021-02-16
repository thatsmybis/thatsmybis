var table = null;

var colSource = 0;
var colName = 1;
var colPrios = 2;
var colWishlist = 3;
var colNotes = 4;
var colPriority = 5;

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
        table.column(colWishlist).visible(true);
        table.column(colPrios)   .visible(true);
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

    // Filter out characters based on the raid they are in
    $("#raid_filter").on('change', function () {
        let raidId = $(this).val();

        if (raidId) {
            $(".js-item-wishlist-character[data-raid-id!='" + raidId + "']").hide();
            $(".js-item-wishlist-character[data-raid-id='" + raidId + "']").show();
        } else {
            $(".js-item-wishlist-character").show();
        }
    }).change();

    trackTimestamps();
});

function createTable(lastSource) {
    memberTable = $("#itemTable").DataTable({
        "autoWidth" : false,
        "data"      : items,
        "columns"   : [
            {
                "title"  : '<span class="fas fa-fw fa-skull-crossbones"></span> Boss',
                "data"   : "",
                "render" : function (data, type, row) {
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
                "visible"   : true,
                "width"     : "130px",
                "className" : "text-right",
            },
            {
                "title"  : '<span class="fas fa-fw fa-sack text-success"></span> Loot',
                "data"   : "",
                "render" : function (data, type, row) {
                    return `
                    <ul class="no-bullet no-indent mb-0">
                        <li>
                            <a href="/${ guild.id }/${ guild.slug }/i/${ row.item_id }/${ slug(row.name) }"
                                class="${ row.quality ? 'q' + row.quality : '' }"
                                data-wowhead-link="https://classic.wowhead.com/item=${ row.item_id }"
                                data-wowhead="item=${ row.item_id }?domain=classic">
                                ${ row.name }
                            </a>
                        </li>

                        ${ row.is_bis || row.is_bis_horde || row.is_bis_alliance ? `
                            <li>
                                <small>
                                    ${ row.is_bis ? 'BIS' : '' }
                                    ${ row.is_bis_alliance  ? '<span class="text-shaman">A</span>' : '' }
                                    ${ row.is_bis_horde  ? '<span class="text-dk">H</span>' : '' }
                                </small>
                            </li>` : `` }
                    </ul>`;
                },
                "visible" : true,
                "width"   : "330px",
            },
            {
                "title"  : '<span class="fas fa-fw fa-sort-amount-down text-gold"></span> Prio\'s',
                "data"   : "priod_characters",
                "render" : function (data, type, row) {
                    return data && data.length ? getCharacterList(data, 'prio', row.item_id) : '—';
                },
                "orderable" : false,
                "visible" : showPrios ? true : false,
                "width"   : "300px",
            },
            {
                "title"  : '<span class="text-legendary fas fa-fw fa-scroll-old"></span> Wishlist',
                "data"   : "wishlist_characters",
                "render" : function (data, type, row) {
                    return data && data.length ? getCharacterList(data, 'wishlist', row.item_id) : '—';
                },
                "orderable" : false,
                "visible" : showWishlist ? true : false,
                "width"   : "400px",
            },
            {
                "title"  : '<span class="fas fa-fw fa-comment-alt-lines"></span> Notes',
                "data"   : "guild_note",
                "render" : function (data, type, row) {
                    return (data ? `<span class="js-markdown-inline">${ nl2br(data) }</span>` : '—');
                },
                "orderable" : false,
                "visible" : true,
                "width"   : "200px",
            },
            {
                "title"  : '<span class="fas fa-fw fa-comment-alt-lines"></span> Prio Notes',
                "data"   : "guild_priority",
                "render" : function (data, type, row) {
                    return (data ? `<span class="js-markdown-inline">${ nl2br(data) }</span>` : '—');
                },
                "orderable" : false,
                "visible" : true,
                "width"   : "200px",
            },
        ],
        "order"  : [], // Disable initial auto-sort; relies on server-side sorting
        "paging" : false,
        "initComplete": function () {
            makeWowheadLinks();
            parseMarkdown();
        },
        "createdRow" : function (row, data, dataIndex) {
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
    return memberTable;
}

// Gets an HTML list of characters
function getCharacterList(data, type, itemId) {
    let characters = `<ul class="list-inline js-item-list mb-0" data-type="${ type }" data-id="${ itemId }">`;
    let initialLimit = 4;

    let lastRaidId = null;
    $.each(data, function (index, character) {
        if (type == 'prio' && character.pivot.raid_id && character.pivot.raid_id != lastRaidId) {
            lastRaidId = character.pivot.raid_id;
            characters += `
                <li data-raid-id="" class="js-item-wishlist-character no-bullet font-weight-normal font-italic  text-muted small">
                    ${ raids.find(val => val.id === character.pivot.raid_id).name }
                </li>
            `;
        }
        characters += `
            <li data-raid-id="${ type == 'prio' ? character.pivot.raid_id : character.raid_id }"
                value="${ type == 'prio' ? character.pivot.order : '' }"
                class="js-item-wishlist-character list-inline-item font-weight-normal mb-1 mr-0 ${ character.pivot.received_at ? 'font-strikethrough' : '' }">
                <a href="/${ guild.id }/${ guild.slug }/c/${ character.id }/${ character.slug }"
                    title="${ character.raid_name ? character.raid_name + ' -' : '' } ${ character.level ? character.level : '' } ${ character.race ? character.race : '' } ${ character.spec ? character.spec : '' } ${ character.class ? character.class : '' } ${ character.username ? '(' + character.username + ')' : '' }"
                    class="text-${ character.class ? character.class.toLowerCase() : ''}-important tag d-inline">
                    <span class="text-muted">${ character.pivot.order ? character.pivot.order : '' }</span>
                    <span class="role-circle" style="background-color:${ getColorFromDec(character.raid_color) }"></span>${ character.name }
                    ${ character.is_alt ? `
                        <span class="text-legendary">alt</span>
                    ` : '' }
                    <span class="js-watchable-timestamp smaller text-muted"
                        data-timestamp="${ character.pivot.created_at }"
                        data-is-short="1">
                    </span>
                </a>
            </li>`;
    });

    characters += `</ul>`;
    return characters;
}
