var table = null;

var colName = 0;
var colLoot = 1;
var colWishlist = 2;
var colPrios = 3;
var colRecipes = 4;
var colRoles = 5;
var colNotes = 6;
var colClass = 7;
var colRaid = 8;

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
        table.column(colRoles)   .visible(false);
        table.column(colLoot)    .visible(true);
        table.column(colWishlist).visible(true);
        table.column(colRecipes) .visible(false);
        table.column(colNotes)   .visible(true);
   });

    // Triggered when a column is made visible
    table.on('column-visibility.dt', function (e, settings, column, state) {
        // Refresh wowhead links to show stlying.
        // wowhead's script previously ignored these links if they weren't visible
        makeWowheadLinks();
        addClippedItemHandlers();
        trackTimestamps();
    });

    addClippedItemHandlers();
    trackTimestamps();
});

function createTable() {
    memberTable = $("#characterTable").DataTable({
        "autoWidth" : false,
        "data"      : characters,
        "columns"   : [
            {
                "title"  : '<span class="fas fa-fw fa-user"></span> Character',
                "data"   : "character",
                "render" : function (data, type, row) {
                    return `
                    <ul class="no-bullet no-indent mb-2">
                        <li>
                            <div class="dropdown text-${ row.class ? row.class.toLowerCase() : ''}">
                                <a class="dropdown-toggle text-4 font-weight-bold"
                                    id="character${ row.id }Dropdown"
                                    role="button"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    title="${ row.member ? row.member.username : '' }">
                                    ${ row.name }
                                </a>
                                <div class="dropdown-menu" aria-labelledby="character${ row.id }Dropdown">
                                    <a class="dropdown-item" href="/${ guild.id }/${ guild.slug }/c/${ row.id }/${ row.slug }" target="_blank">
                                        Profile
                                    </a>
                                    <a class="dropdown-item" href="/${ guild.id }/${ guild.slug }/audit-log?character_id=${ row.id }" target="_blank">
                                        Logs
                                    </a>
                                    ${ showEdit ?
                                        `<a class="dropdown-item" href="/${ guild.id }/${ guild.slug }/c/${ row.id }/${ row.slug }/edit" target="_blank">
                                            Edit
                                        </a>
                                        <a class="dropdown-item" href="/${ guild.id }/${ guild.slug }/c/${ row.id }/${ row.slug }/loot" target="_blank">
                                            Loot
                                        </a>`
                                        : `` }
                                </div>
                            </div>
                        </li>
                        ${ row.is_alt || row.raid_name || row.class ? `
                            <li>
                                ${ row.is_alt ? `
                                    <span class="text-legendary font-weight-bold">Alt</span>&nbsp;
                                ` : '' }
                                ${ row.raid_name ? `
                                    <span class="font-weight-bold">
                                        <span class="role-circle" style="background-color:${ row.raid_color ? getColorFromDec(parseInt(row.raid_color)) : '' }"></span>
                                        ${ row.raid_name ? row.raid_name : '' }
                                    </span>
                                ` : ``}
                                ${ row.class ? row.class : '' }
                            </li>` : `` }

                        ${ row.level || row.race || row.spec ? `
                            <li>
                                <small>
                                    ${ row.level ? row.level : '' }
                                    ${ row.race  ? row.race : '' }
                                    ${ row.spec  ? row.spec : '' }
                                </small>
                            </li>` : `` }

                        ${ row.rank || row.profession_1 || row.profession_2 ? `
                            <li>
                                <small>
                                    ${ row.rank         ? 'Rank ' + row.rank + (row.profession_1 || row.profession_2 ? ',' : '') : '' }
                                    ${ row.profession_1 ? row.profession_1 + (row.profession_2 ? ',' : '') : '' }
                                    ${ row.profession_2 ? row.profession_2 : '' }
                                </small>
                            </li>` : `` }
                    </ul>`;
                },
                "visible" : true,
                "width"   : "250px",
            },
            {
                "title"  : '<span class="text-success fas fa-fw fa-sack"></span> Loot Received',
                "data"   : "received",
                "render" : function (data, type, row) {
                    return data && data.length ? getItemList(data, 'received', row.id) : '—';
                },
                "orderable" : false,
                "visible" : true,
                "width"   : "280px",
            },
            {
                "title"  : '<span class="text-legendary fas fa-fw fa-scroll-old"></span> Wishlist',
                "data"   : "wishlist",
                "render" : function (data, type, row) {
                    return data && data.length ? getItemList(data, 'wishlist', row.id) : '—';
                },
                "orderable" : false,
                "visible" : showWishlist ? true : false,
                "width"   : "280px",
            },
            {
                "title"  : '<span class="text-gold fas fa-fw fa-sort-amount-down"></span> Prio\'s',
                "data"   : "prios",
                "render" : function (data, type, row) {
                    return data && data.length ? getItemList(data, 'prio', row.id, true) : '—';
                },
                "orderable" : false,
                "visible" : showPrios ? true : false,
                "width"   : "280px",
            },
            {
                "title"  : '<span class="text-gold fas fa-fw fa-book"></span> Recipes',
                "data"   : "recipes",
                "render" : function (data, type, row) {
                    return data && data.length ? getItemList(data, 'recipes', row.id) : '—';
                },
                "orderable" : false,
                "visible" : false,
                "width"   : "280px",
            },
            {
                /* this feature has been cut */
                "title"  : "Roles",
                "data"   : "user.roles",
                "render" : function (data, type, row) {
                    let roles = "";
                    if (data && data.length > 0) {
                        roles = '<ul class="list-inline">';
                        data.forEach(function (item, index) {
                            let color = item.color != 0 ? '#' + rgbToHex(item.color) : "#FFFFFF";
                            roles += `<li class="list-inline-item"><span class="tag" style="border-color:${ color };"><span class="role-circle" style="background-color:${ color }"></span>${ item.name }</span></li>`;
                        });
                        roles += "</ul>";
                    } else {
                        roles = '—';
                    }
                    return roles;
                },
                "orderable" : false,
                "visible" : false,
            },
            {
                "title"  : '<span class="fas fa-fw fa-comment-alt-lines"></span> Notes',
                "data"   : "public_note",
                "render" : function (data, type, row) {
                    return (row.public_note ? nl2br(row.public_note) : '—')
                        + (row.officer_note ? '<br><small class="font-weight-bold">Officer\'s Note</small><br><em>' + nl2br(row.officer_note) + '</em>' : '');
                },
                "orderable" : false,
                "visible" : true,
                "width"   : "280px",
            },
            {
                "title"  : "Class",
                "data"   : "class",
                "render" : function (data, type, row) {
                    return (row.class ? row.class : null);
                },
                "visible" : false,
            },
            {
                "title"  : "Raid",
                "data"   : "raid",
                "render" : function (data, type, row) {
                    return (row.raid_name ? row.raid_name : null);
                },
                "visible" : false,
            },
        ],
        "order"  : [], // Disable initial auto-sort; relies on server-side sorting
        "paging" : false,
        initComplete: function () {
            let sortColumns = [colClass, colRaid];
            this.api().columns().every(function (index) {
                var column = this;

                let select1 = null;
                let select2 = null; // Iniitalize this beside select1 if we want a secondary sort

                if (index == colClass) {
                    select1 = $("#class_filter");
                    select2 = null;
                }

                if (index == colRaid) {
                    select1 = $("#raid_filter");
                    select2 = null;
                }

                if (sortColumns.includes(index)) {
                    select1.on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        if (select2 && select2.val()) {
                            // Must contain both
                            val = "(?=.*" + val + ")(?=.*" + $.fn.dataTable.util.escapeRegex(select2.val()) + ")";
                        }
                        column.search(val ? val : '', true, false).draw();
                    });

                    if (select2) {
                        select2.on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            if (select1 && select1.val()) {
                                // Must contain both
                                val = "(?=.*" + val + ")(?=.*" + $.fn.dataTable.util.escapeRegex(select1.val()) + ")";
                            }
                            column.search(val ? val : '', true, false).draw();
                        });
                    }
                }
            } );
            makeWowheadLinks();
            addItemAutocompleteHandler();
            addTagInputHandlers();
        }
    });
    return memberTable;
}

function addClippedItemHandlers() {
    $(".js-show-clipped-items").click(function () {
        let id = $(this).data("id");
        let type = $(this).data("type");
        $(".js-clipped-item[data-id='" + id + "'][data-type='" + type + "']").show();
        $(".js-show-clipped-items[data-id='" + id + "'][data-type='" + type + "']").hide();
        $(".js-hide-clipped-items[data-id='" + id + "'][data-type='" + type + "']").show();
    });
    $(".js-hide-clipped-items").click(function () {
        let id = $(this).data("id");
        let type = $(this).data("type");
        $(".js-clipped-item[data-id='" + id + "'][data-type='" + type + "']").hide();
        $(".js-show-clipped-items[data-id='" + id + "'][data-type='" + type + "']").show();
        $(".js-hide-clipped-items[data-id='" + id + "'][data-type='" + type + "']").hide();
    });
}

// Gets an HTML list of items with pretty wowhead formatting
function getItemList(data, type, characterId, useOrder = false) {
    let items = `<ol class="no-indent js-item-list mb-2" data-type="${ type }" data-id="${ characterId }">`;
    let initialLimit = 4;

    let lastRaidId = null;
    $.each(data, function (index, item) {
        let clipItem = false;
        if (index >= initialLimit) {
            clipItem = true;
            if (index == initialLimit) {
                items += `<li class="js-show-clipped-items small cursor-pointer no-bullet " data-type="${ type }" data-id="${ characterId }">show ${ data.length - initialLimit } more…</li>`;
            }
        }

        if (type == 'prio' && item.pivot.raid_id && item.pivot.raid_id != lastRaidId) {
            lastRaidId = item.pivot.raid_id;
            items += `
                <li data-raid-id="" class="js-item-wishlist-character no-bullet font-weight-normal font-italic text-muted small">
                    ${ raids.find(val => val.id === item.pivot.raid_id).name }
                </li>
            `;
        }

        items += `
            <li class="font-weight-normal ${ clipItem ? 'js-clipped-item' : '' }" data-type="${ type }" data-id="${ characterId }"
                value="${ useOrder ? item.pivot.order : '' }"
                style="${ clipItem ? 'display:none;' : '' }">
                <a href="/${ guild.id }/${ guild.slug }/i/${ item.item_id }/${ slug(item.name) }"
                    data-wowhead-link="https://classic.wowhead.com/item=${ item.item_id }"
                    data-wowhead="item=${ item.item_id }?domain=classic">
                    ${ item.name }
                </a>
                <span class="js-watchable-timestamp js-timestamp-title smaller text-muted"
                    data-timestamp="${ item.pivot.created_at }"
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
