var table = null;

var colName = 0;
var colLoot = 1;
var colWishlist = 2;
var colRecipes = 3;
var colRoles = 4;
var colNotes = 5;
var colClass = 6;
var colRaid = 7;

/*
    member_id
    guild_id
    name
    level
    race
    class
    spec
    profession_1
    profession_2
    rank
    rank_goal
    raid_id
    public_note
    hidden_at
    removed_at
*/

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
    });
});

function createTable() {
    memberTable = $("#roster").DataTable({
        "autoWidth" : false,
        "data"      : characters,
        "columns"   : [
            {
                "title"  : "Character",
                "data"   : "character",
                "render" : function (data, type, row) {
                    return `
                    <ul class="no-bullet no-indent">
                        <li>
                            <a href="${row.guild_name}/characters/${row.name}"
                                class="text-4 text-${row.class ? row.class.toLowerCase() : ''} font-weight-bold"
                                title="${ row.member ? row.member.username : 'unknown member' }">
                                ${ row.name }
                            </a>
                        </li>

                        ${ row.raid || row.class ? `
                            <li>
                                <span class="font-weight-bold">
                                    ${ row.raid.name }
                                </span>
                                ${ row.class  ? row.class : '' }
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
            },
            {
                "title"  : "Loot Received",
                "data"   : "received",
                "render" : function (data, type, row) {
                    return data.length ? getItemList(data) : '—';
                },
                "visible" : true,
            },
            {
                "title"  : "Wishlist",
                "data"   : "wishlist",
                "render" : function (data, type, row) {
                    return data.length ? getItemList(data) : '—';
                },
                "visible" : true,
            },
            {
                "title"  : "Recipes",
                "data"   : "recipes",
                "render" : function (data, type, row) {
                    return data.length ? getItemList(data) : '—';
                },
                "visible" : false,
            },
            {
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
                "visible" : false,
            },
            {
                "title"  : "Notes",
                "data"   : "public_note",
                "render" : function (data, type, row) {
                    return (row.public_note ? nl2br(row.public_note) : '—')
                        + (row.officer_note ? "OFFICER NOTE" + nl2br(row.officer_note) : '');
                }
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
                    return (row.raid ? row.raid.name : null);
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
        }
    });
    return memberTable;
}

// Gets an HTML list of items with pretty wowhead formatting
function getItemList(data) {
    let items = `<ol class="no-indent">`;
    $.each(data, function (index, item) {
        let clipItem = false;

        if (index >= 5) {
            clipItem = true;
            if (index == 5) {
                items += `<li class="font-weight-light js-show-clipped-items"><small>show more…</small></li>`;
            }
        }

        items += `
        <li class="font-weight-normal ${ clipItem ? 'js-clipped-item' : '' }"
            style="${ clipItem ? 'display:none;' : '' }">
            <a href="https://classic.wowhead.com/item=${ item.item_id }"
                data-wowhead="item=${ item.item_id }">
                ${ item.name }
            </a>
        </li>`;
    });

    if (data.length > 5) {
        items += `<li class="font-weight-light js-show-clipped-items" style="display:none;"><small>show less…</small></li>`;
    }

    items += `</ol>`;
    return items;
}
