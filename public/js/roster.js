var table = null;
$(document).ready( function () {
   table = createTable();

   $(".toggle-column").click(function(e) {
        e.preventDefault();
        let column = table.column($(this).attr("data-column"));
        column.visible(!column.visible());
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
                    return `<a href="${row.id}/${row.username}" class="text-${row.class} font-weight-bold">${row.username}</a>`;
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
                    return row.professions;
                },
                "visible" : false
            },
            {
                "title"  : "Rare Recipes",
                "data"   : "recipes",
                "render" : function (data, type, row) {
                    return row.recipes;
                }
            },
            {
                "title"  : "Alts",
                "data"   : "alts",
                "render" : function (data, type, row) {
                    return row.alts;
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
                "title"  : "Wishlist",
                "data"   : "wishlist",
                "render" : function (data, type, row) {
                    return row.wishlist;
                }
            },
            {
                "title"  : "Raid Loot",
                "data"   : "loot_recieved",
                "render" : function (data, type, row) {
                    return row.loot_received;
                },
                "visible" : false
            },
            {
                "title"  : "Notes",
                "data"   : "note",
                "render" : function (data, type, row) {
                    return row.note;
                }
            },
            {
                "title"  : "Officer Notes",
                "data"   : "officer_note",
                "render" : function (data, type, row) {
                    return row.officer_note;
                }
            },
        ],
        "order"  : [], // Disable initial auto-sort; relies on server-side sorting
        "paging" : false,
    });
    return memberTable;
}
