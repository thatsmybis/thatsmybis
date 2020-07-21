var table = null;

var colName = 0;
var colWishlist = 1;
var colNotes = 2;
var colPriority = 3;

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
    });
});

function createTable() {
    memberTable = $("#itemTable").DataTable({
        "autoWidth" : false,
        "data"      : items,
        "columns"   : [
            {
                "title"  : '<span class="fas fa-fw fa-sword"></span> Item',
                "data"   : "item",
                "render" : function (data, type, row) {
                    return `
                    <ul class="no-bullet no-indent mb-2">
                        <li>
                            <a href="/${ guild.slug }/item/${ item.item_id }/${ slug(item.name) }"
                                class="text-4 font-weight-bold"
                                data-wowhead-link="https://classic.wowhead.com/item=${ item.item_id }"
                                data-wowhead="item=${ item.item_id }?domain=classic">
                                ${ item.name }
                            </a>
                        </li>

                        ${ row.itemSource ? `
                            <li>
                                <span class="font-weight-bold">
                                    ${ row.itemSource.name }
                                </span>
                            </li>` : `` }

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
            },
            {
                "title"  : '<span class="text-legendary fas fa-fw fa-scroll-old"></span> Wishlist',
                "data"   : "wishlist",
                "render" : function (data, type, row) {
                    return data.length ? getCharacterList(data, 'wishlist', row.id) : '—';
                },
                "orderable" : false,
                "visible" : true,
            },
            {
                "title"  : '<span class="fas fa-fw fa-comment-alt-lines"></span> Notes',
                "data"   : "note",
                "render" : function (data, type, row) {
                    return (row.pivot.note ? nl2br(row.pivot.note) : '—');
                },
                "orderable" : false,
                "visible" : true,
            },
            {
                "title"  : '<span class="fas fa-fw fa-comment-alt-lines"></span> Priority',
                "data"   : "priority",
                "render" : function (data, type, row) {
                    return (row.pivot.priority ? nl2br(row.pivot.priority) : '—');
                },
                "orderable" : false,
                "visible" : true,
            },
        ],
        "order"  : [], // Disable initial auto-sort; relies on server-side sorting
        "paging" : false,
        initComplete: function () {
            let sortColumns = [colWishlist];
            this.api().columns().every(function (index) {
                var column = this;

                let select1 = null;
                let select2 = null; // Iniitalize this beside select1 if we want a secondary sort

                if (index == colWishlist) {
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

// Gets an HTML list of characters
function getCharacterList(data, type, itemId) {
    let characters = `<ol class="no-indent js-item-list mb-2" data-type="${ type }" data-id="${ itemId }">`;
    let initialLimit = 4;

    $.each(data, function (index, item) {
        let clipItem = false;

        if (index >= initialLimit) {
            clipCharacter = true;
            if (index == initialLimit) {
                characters += `<li class="js-show-clipped-items no-bullet font-weight-light"><small>show ${ data.length - initialLimit } more…</small></li>`;
            }
        }

        characters += `
            <li class="font-weight-normal ${ clipCharacter ? 'js-clipped-item' : '' }"
                style="${ clipCharacter ? 'display:none;' : '' }">

                <a href="/${ guild.slug }/character/${ character.name }"
                    class="text-4 text-${row.class ? row.class.toLowerCase() : ''} font-weight-bold">
                    ${ item.name }
                </a>
            </li>`;
    });

    if (data.length > initialLimit) {
        characters += `<li class="js-show-clipped-items font-weight-light no-bullet" style="display:none;"><small>show less…</small></li>`;
    }

    characters += `</ol>`;
    return characters;
}
