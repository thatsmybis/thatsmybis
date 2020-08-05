var table = null;

var colSource = 0;
var colName = 1;
var colWishlist = 2;
var colNotes = 3;
var colPriority = 4;

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
        table.column(colNotes)   .visible(true);
        table.column(colPriority).visible(true);
   });

    // Triggered when a column is made visible
    table.on('column-visibility.dt', function (e, settings, column, state) {
        // Refresh wowhead links to show stlying.
        // wowhead's script previously ignored these links if they weren't visible
        makeWowheadLinks();
        trackTimestamps();
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
                "title"  : '<span class="fas fa-fw fa-treasure-chest text-gold"></span> Loot',
                "data"   : "",
                "render" : function (data, type, row) {
                    return `
                    <ul class="no-bullet no-indent mb-0">
                        <li>
                            <a href="/${ guild.slug }/i/${ row.item_id }/${ slug(row.name) }"
                                class=""
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
                "title"  : '<span class="text-legendary fas fa-fw fa-scroll-old"></span> Wishlist',
                "data"   : "wishlist_characters",
                "render" : function (data, type, row) {
                    return data.length ? getCharacterList(data, 'wishlist', row.id) : '—';
                },
                "orderable" : false,
                "visible" : true,
                "width"   : "400px",
            },
            {
                "title"  : '<span class="fas fa-fw fa-comment-alt-lines"></span> Notes',
                "data"   : "guild_note",
                "render" : function (data, type, row) {
                    return (data ? nl2br(data) : '—');
                },
                "orderable" : false,
                "visible" : true,
                "width"   : "200px",
            },
            {
                "title"  : '<span class="fas fa-fw fa-sort-amount-down"></span> Priority',
                "data"   : "guild_priority",
                "render" : function (data, type, row) {
                    return (data ? nl2br(data) : '—');
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

    $.each(data, function (index, character) {
        characters += `
            <li data-raid-id="${ character.raid_id }" class="js-item-wishlist-character list-inline-item font-weight-normal mb-1">
                <a href="/${ guild.slug }/c/${ character.name }"
                    title="${ character.raid_name ? character.raid_name + ' -' : '' } ${ character.level ? character.level : '' } ${ character.race ? character.race : '' } ${ character.spec ? character.spec : '' } ${ character.class ? character.class : '' } ${ character.username ? '(' + character.username + ')' : '' }"
                    class="text-${ character.class ? character.class.toLowerCase() : ''}-important tag d-inline">
                    <span class="role-circle" style="background-color:${ getColorFromDec(character.raid_color) }"></span>${ character.name }

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
