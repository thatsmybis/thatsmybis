var table = null;

var colName = 0;
var colRoles = 1;
var colRecipes = 2;
var colAlts = 3;
var colRank = 4;
var colRankGoal = 5;
var colWishlist = 6;
var colLoot = 7;
var colNotes = 8;
var colOfficerNotes = 9;

$(document).ready( function () {
   table = createTable();

   $(".toggle-column").click(function(e) {
        e.preventDefault();
        let column = table.column($(this).attr("data-column"));
        column.visible(!column.visible());
   });

   $(".toggle-column-default").click(function(e) {
        e.preventDefault();

        table.column(colName).        visible(true);
        table.column(colRoles).       visible(true);
        table.column(colRecipes).     visible(false);
        table.column(colAlts).        visible(false);
        table.column(colRank).        visible(false);
        table.column(colRankGoal).    visible(false);
        table.column(colWishlist).    visible(false);
        table.column(colLoot).        visible(false);
        table.column(colNotes).       visible(true);
        table.column(colOfficerNotes).visible(false);
   });

   $(".toggle-column-loot-council").click(function(e) {
        e.preventDefault();

        table.column(colName).        visible(true);
        table.column(colRoles).       visible(true);
        table.column(colRecipes).     visible(false);
        table.column(colAlts).        visible(false);
        table.column(colRank).        visible(false);
        table.column(colRankGoal).    visible(false);
        table.column(colWishlist).    visible(true);
        table.column(colLoot).        visible(true);
        table.column(colNotes).       visible(true);
        table.column(colOfficerNotes).visible(true);
   });

   $(".toggle-column-bare").click(function(e) {
        e.preventDefault();

        table.column(colName).        visible(true);
        table.column(colRoles).       visible(true);
        table.column(colRecipes).     visible(false);
        table.column(colAlts).        visible(false);
        table.column(colRank).        visible(false);
        table.column(colRankGoal).    visible(false);
        table.column(colWishlist).    visible(false);
        table.column(colLoot).        visible(false);
        table.column(colNotes).       visible(false);
        table.column(colOfficerNotes).visible(false);
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
        "data"      : members,
        "columns"   : [
            {
                "title"  : "Name",
                "data"   : "username",
                "render" : function (data, type, row) {
                    return `<a href="${row.id}/${row.username}" class="text-${row.class ? row.class.toLowerCase() : ''} font-weight-bold">
                        ${row.username}
                    </a>
                    <small>${row.spec ? row.spec : '—'}<small>`;
                }
            },
            {
                "title"  : "Roles",
                "data"   : "roles",
                "render" : function (data, type, row) {
                    let roles = "";
                    if (data.length > 0) {
                        data.forEach(function (item, index) {
                            let color = item.color != 0 ? '#' + rgbToHex(item.color) : "#FFFFFF";
                            roles += `<span class="tag" style="border-color:${ color };"><span class="role-circle" style="background-color:${ color }"></span>${ item.name }</span>`;
                        });
                    } else {
                        roles = '—';
                    }
                    return roles;
                }
            },
            {
                "title"  : "Rare Recipes",
                "data"   : "recipes",
                "render" : function (data, type, row) {
                    return data ? getItemList(data) : '—';
                },
                "visible" : false
            },
            {
                "title"  : "Alts",
                "data"   : "alts",
                "render" : function (data, type, row) {
                    return row.alts ? nl2br(row.alts) : '—';
                },
                "visible" : false
            },
            {
                "title"  : "Rank",
                "data"   : "rank",
                "render" : function (data, type, row) {
                    return row.rank ? row.rank : '—';
                },
                "visible" : false
            },
            {
                "title"  : "Rank Goal",
                "data"   : "rank_goal",
                "render" : function (data, type, row) {
                    return row.rank_goal ? row.rank_goal : '—';
                },
                "visible" : false
            },
            {
                "title"  : "Wishlist",
                "data"   : "wishlist",
                "render" : function (data, type, row) {
                    return data ? getItemList(data) : '—';
                },
                "visible" : false
            },
            {
                "title"  : "Raid Loot",
                "data"   : "loot_recieved",
                "render" : function (data, type, row) {
                    return data ? getItemList(data) : '—';
                },
                "visible" : false
            },
            {
                "title"  : "Notes",
                "data"   : "note",
                "render" : function (data, type, row) {
                    return row.note ? nl2br(row.note) : '—';
                }
            },
            {
                "title"  : "Officer Notes",
                "data"   : "officer_note",
                "render" : function (data, type, row) {
                    return row.officer_note ? nl2br(row.officer_note) : '—';
                },
                "visible" : false
            },
        ],
        "order"  : [], // Disable initial auto-sort; relies on server-side sorting
        "paging" : false,
        initComplete: function () {
            let sortColumns = [colRoles];
            this.api().columns().every(function (index) {
                var column = this;

                let select1 = null;
                let select2 = null;

                if (index == colRoles) {
                    select1 = $("#roleFilter1");
                    select2 = $("#roleFilter2");
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
        }
    });
    return memberTable;
}

// Gets an HTML list of items with pretty wowhead formatting
function getItemList(data) {
    let items = `<ul class="no-indent no-bullet">`;
    $.each(data, function (index, item) {
        items += `<li class="font-weight-medium"><a href="/item/${ item.item_id }" data-wowhead="item=${ item.item_id }">${ item.name }</a></li>`;
    });
    items += `</ul>`;
    return items;
}
