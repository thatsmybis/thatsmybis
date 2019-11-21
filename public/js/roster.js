var table = null;

var colName = 0;
var colClass = 1;
var colSpec = 2;
var colProfessions = 3;
var colRecipes = 4;
var colAlts = 5;
var colRank = 6;
var colRankGoal = 7;
var colRaidGroup = 8;
var colWishlist = 9;
var colLoot = 10;
var colNotes = 11;
var colOfficerNotes = 12;

$(document).ready( function () {
   table = createTable();

   $(".toggle-column").click(function(e) {
        e.preventDefault();
        let column = table.column($(this).attr("data-column"));
        column.visible(!column.visible());
   });

   $(".toggle-column-default").click(function(e) {
        e.preventDefault();

        table.column(colName).visible(true);
        table.column(colClass).visible(true);
        table.column(colSpec).visible(true);
        table.column(colProfessions).visible(false);
        table.column(colRecipes).visible(true);
        table.column(colAlts).visible(false);
        table.column(colRank).visible(false);
        table.column(colRankGoal).visible(false);
        table.column(colRaidGroup).visible(true);
        table.column(colWishlist).visible(false);
        table.column(colLoot).visible(false);
        table.column(colNotes).visible(true);
        table.column(colOfficerNotes).visible(true);
   });

   $(".toggle-column-loot-council").click(function(e) {
        e.preventDefault();

        table.column(colName).visible(true);
        table.column(colClass).visible(true);
        table.column(colSpec).visible(true);
        table.column(colProfessions).visible(true);
        table.column(colRecipes).visible(true);
        table.column(colAlts).visible(false);
        table.column(colRank).visible(false);
        table.column(colRankGoal).visible(false);
        table.column(colRaidGroup).visible(true);
        table.column(colWishlist).visible(true);
        table.column(colLoot).visible(true);
        table.column(colNotes).visible(true);
        table.column(colOfficerNotes).visible(true);
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
                    return `<a href="${row.id}/${row.username}" class="text-${row.class.toLowerCase()} font-weight-bold">${row.username}</a>`;
                }
            },
            {
                "title"  : "Class",
                "data"   : "class",
                "render" : function (data, type, row) {
                    return row.class;
                }
            },
            {
                "title"  : "Spec",
                "data"   : "spec",
                "render" : function (data, type, row) {
                    return row.spec;
                }
            },
            {
                "title"  : "Professions",
                "data"   : "professions",
                "render" : function (data, type, row) {
                    return nl2br(row.professions);
                },
                "visible" : false
            },
            {
                "title"  : "Rare Recipes",
                "data"   : "recipes",
                "render" : function (data, type, row) {
                    return nl2br(row.recipes);
                }
            },
            {
                "title"  : "Alts",
                "data"   : "alts",
                "render" : function (data, type, row) {
                    return nl2br(row.alts);
                },
                "visible" : false
            },
            {
                "title"  : "Rank",
                "data"   : "rank",
                "render" : function (data, type, row) {
                    return row.rank;
                },
                "visible" : false
            },
            {
                "title"  : "Rank Goal",
                "data"   : "rank_goal",
                "render" : function (data, type, row) {
                    return row.rank_goal;
                },
                "visible" : false
            },
            {
                "title"  : "Raid",
                "data"   : "raid_group",
                "render" : function (data, type, row) {
                    return row.raid_group;
                },
                "visible" : true
            },
            {
                "title"  : "Wishlist",
                "data"   : "wishlist",
                "render" : function (data, type, row) {
                    return nl2br(row.wishlist);
                },
                "visible" : false
            },
            {
                "title"  : "Raid Loot",
                "data"   : "loot_recieved",
                "render" : function (data, type, row) {
                    return nl2br(row.loot_received);
                },
                "visible" : false
            },
            {
                "title"  : "Notes",
                "data"   : "note",
                "render" : function (data, type, row) {
                    return nl2br(row.note);
                }
            },
            {
                "title"  : "Officer Notes",
                "data"   : "officer_note",
                "render" : function (data, type, row) {
                    return nl2br(row.officer_note);
                },
                "visible" : false
            },
        ],
        "order"  : [], // Disable initial auto-sort; relies on server-side sorting
        "paging" : false,
        initComplete: function () {
            let sortColumns = [colClass, colProfessions, colRaidGroup];
            this.api().columns().every( function (index) {
                var column = this;

                let select = null;

                if (index == colClass) {
                    select = $("#classFilter");
                } else if (index == colProfessions) {
                    select = $("#professionFilter");
                } else if (index == colRaidGroup) {
                    select = $("#raidFilter");
                }

                if (sortColumns.includes(index)) {
                    select.on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search(val ? val : '', true, false)
                            .draw();
                    });

                    column.data().unique().sort().each(function (d, j) {
                        select.append('<option value="'+d+'">'+d+'</option>');
                    });
                }
            } );
        }
    });
    return memberTable;
}
