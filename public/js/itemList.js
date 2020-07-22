var table = null;

var colSource = 0;
var colName = 1;
var colWishlist = 2;
var colPriority = 3;
var colNotes = 4;


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
    });
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
                    <ul class="no-bullet no-indent mb-2">
                        ${ row.source_name ? `
                            <li>
                                <span class="font-weight-bold">
                                    ${ row.source_name }
                                </span>
                            </li>` : `` }
                    </ul>`;
                },
                "visible"   : true,
                "width"     : "165px",
                "className" : "text-right",
            },
            {
                "title"  : '<span class="fas fa-fw fa-treasure-chest text-gold"></span> Item',
                "data"   : "",
                "render" : function (data, type, row) {
                    return `
                    <ul class="no-bullet no-indent mb-2">
                        <li>
                            <a href="/${ guild.slug }/item/${ row.item_id }/${ slug(row.name) }"
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
                "width"   : "310px",
            },
            {
                "title"  : '<span class="text-legendary fas fa-fw fa-scroll-old"></span> Wishlist',
                "data"   : "wishlist_characters",
                "render" : function (data, type, row) {
                    return data.length ? getCharacterList(data, 'wishlist', row.id) : '—';
                },
                "orderable" : false,
                "visible" : true,
                "width"   : "200px",
            },
            {
                "title"  : '<span class="fas fa-fw fa-comment-alt-lines"></span> Priority',
                "data"   : "guild_priority",
                "render" : function (data, type, row) {
                    return (data ? nl2br(data) : '—');
                },
                "orderable" : false,
                "visible" : true,
            },
            {
                "title"  : '<span class="fas fa-fw fa-comment-alt-lines"></span> Notes',
                "data"   : "guild_note",
                "render" : function (data, type, row) {
                    return (data ? nl2br(data) : '—');
                },
                "orderable" : false,
                "visible" : true,
            },
        ],
        "order"  : [], // Disable initial auto-sort; relies on server-side sorting
        "paging" : false,
        "initComplete": function () {
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
            });
            makeWowheadLinks();
            addItemAutocompleteHandler();
            addTagInputHandlers();
        },
        "createdRow" : function (row, data, dataIndex) {
            if (dataIndex == 0 || lastSource == null) {
                lastSource = data.source_name;
            }

            console.log(data.source_name, lastSource);
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
    let characters = `<ul type="a" class="no-indent js-item-list mb-2" data-type="${ type }" data-id="${ itemId }">`;
    let initialLimit = 4;

    $.each(data, function (index, character) {
        let clipCharacter = false;

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
                    class="text-${ character.class ? character.class.toLowerCase() : ''}">
                    ${ character.name }
                </a>
            </li>`;
    });

    if (data.length > initialLimit) {
        characters += `<li class="js-show-clipped-items font-weight-light no-bullet" style="display:none;"><small>show less…</small></li>`;
    }

    characters += `</ul>`;
    return characters;
}
