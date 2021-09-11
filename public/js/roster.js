var table = null;

var colName      = 0;
var colPrios     = 1;
var colWishlist  = 2;
var colLoot      = 3;
var colRecipes   = 4;
var colRoles     = 5;
var colNotes     = 6;
var colClass     = 7;
var colRaidGroup = 8;
var colRaidsAttended = 11;

var allItemsVisible = false;

$(document).ready( function () {
   var table = createTable();

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
        parseMarkdown();
    });

    // Toggle visiblity for all of the clipped/hidden items on the page
    $(".js-show-all-clipped-items").click(function () {
        if (allItemsVisible) {
            allItemsVisible = false;
            resetItemVisibility();
        } else {
            allItemsVisible = true;
            showAllItems();
        }
    });

    $(".js-sort-by-raids-attended").click(function() {
        table.order(colRaidsAttended, 'desc').draw();
    });

    addClippedItemHandlers();
    addInstanceFilterHandlers();
    addWishlistFilterHandlers();
    trackTimestamps();
});

function createTable() {
    memberTable = $("#characterTable").DataTable({
        autoWidth : false,
        data      : characters,
        columns   : [
            {
                title  : `<span class="fas fa-fw fa-user"></span> ${headerCharacter} <span class="text-muted small">(${characters.length})</span>`,
                data   : "character",
                render : function (data, type, row) {
                    return `
                    <ul class="no-bullet no-indent mb-2">
                        <li>
                            <div class="dropdown text-${ row.class ? row.class.toLowerCase() : '' }">
                                <a class="dropdown-toggle text-4 font-weight-bold text-${ row.class ? row.class.toLowerCase() : '' }"
                                    id="character${ row.id }Dropdown"
                                    role="button"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    title="${ row.username ? row.username : '' }">
                                    ${ row.name }
                                </a>
                                <div class="dropdown-menu" aria-labelledby="character${ row.id }Dropdown">
                                    <a class="dropdown-item" href="/${ guild.id }/${ guild.slug }/c/${ row.id }/${ row.slug }">Profile</a>
                                    <a class="dropdown-item" href="/${ guild.id }/${ guild.slug }/audit-log?character_id=${ row.id }">History</a>
                                    ${ showEdit ?
                                        `<a class="dropdown-item" href="/${ guild.id }/${ guild.slug }/c/${ row.id }/${ row.slug }/edit">Edit</a>
                                        <a class="dropdown-item" href="/${ guild.id }/${ guild.slug }/c/${ row.id }/${ row.slug }/loot">Wishlist & Loot</a>`
                                        : `` }
                                    ${ row.member_id ?
                                        `<a class="dropdown-item" href="/${ guild.id }/${ guild.slug }/u/${ row.member_id }/${ row.username ? row.username.toLowerCase() : 'view member' }">${ row.username ? row.username : 'view member' }</a>`
                                    : `` }
                                </div>
                            </div>
                        </li>
                        ${ row.is_alt || row.raid_group_name || row.class ? `
                            <li>
                                ${ row.is_alt ? `
                                    <span class="text-warning font-weight-bold">${localeAlt}</span>&nbsp;
                                ` : '' }
                                ${ row.raid_group_name ? `
                                    <span class="font-weight-bold d-inline tag">
                                        <span class="role-circle" style="background-color:${ getColorFromDec(parseInt(row.raid_group_color)) }"></span>
                                        ${ row.raid_group_name ? row.raid_group_name : '' }
                                    </span>&nbsp;
                                ` : ``}
                                ${ row.class ? row.class : '' }
                            </li>` : `` }

                        ${ !guild.is_attendance_hidden && (row.attendance_percentage || row.raid_count) ?
                            `<li>
                                ${ row.raid_count && typeof row.attendance_percentage === 'number' ? `<span title="attendance" class="${ getAttendanceColor(row.attendance_percentage) }">${ Math.round(row.attendance_percentage * 100) }%</span>` : '' }
                                ${ row.raid_count ? `<span class="small text-muted">${ row.raid_count } raid${ row.raid_count > 1 ? 's' : '' }</span>` : ``}
                            </li>` : `` }

                        ${ row.level || row.race || row.spec ? `
                            <li>
                                <span class="small text-muted">
                                    ${ row.level ? row.level : '' }
                                    <span class="font-weight-bold">
                                        ${ row.race  ? row.race : '' }
                                        ${ row.spec  ? row.spec : '' }
                                    </span>
                                </span>
                            </li>` : `` }

                        ${ row.rank || row.profession_1 || row.profession_2 ? `
                            <li>
                                <span class="small text-muted">
                                    ${ row.rank         ? 'Rank ' + row.rank + (row.profession_1 || row.profession_2 ? ',' : '') : '' }
                                    ${ row.profession_1 ? row.profession_1 + (row.profession_2 ? ',' : '') : '' }
                                    ${ row.profession_2 ? row.profession_2 : '' }
                                </span>
                            </li>` : `` }
                        ${ showEdit ?
                            `
                            ${ row.is_received_unlocked ? `<li class="list-inline-item small text-warning" title="To lock, edit the member that owns this character">loot unlocked</li>` : `` }
                            ${ row.is_wishlist_unlocked ? `<li class="list-inline-item small text-warning" title="To lock, edit the member that owns this character">wishlist unlocked</li>` : `` }
                            ` : `` }
                    </ul>`;
                },
                visible : true,
                width   : "250px",
                className : "width-250",
            },
            {
                title  : `<span class="text-gold fas fa-fw fa-sort-amount-down"></span> ${headerPrios}`,
                data   : "prios",
                render : function (data, type, row) {
                    return data && data.length ? getItemListHtml(data, 'prio', row.id, true) : '—';
                },
                orderable : false,
                visible : showPrios ? true : false,
                width   : "280px",
                className : "width-280",
            },
            {
                title  : `<span class="text-legendary fas fa-fw fa-scroll-old"></span> ${headerWishlist}
                    <span class="js-sort-wishlists text-link">
                        <span class="fas fa-fw fa-exchange cursor-pointer"></span>
                    </span>`,
                data   : "all_wishlists",
                render : function (data, type, row) {
                    // This function only gets used inside this render
                    function createWishlistHtml(data, type, row, list, wishlistNumber, showListHeader) {
                        // Only use the currently selected wishlist
                        data = data.filter(item => item.list_number == wishlistNumber);

                        if (data.length) {
                            // Create a copy of data, then sort it by instance_order DESC, user chosen order ASC
                            let dataSorted = data.slice().sort((a, b) => a.instance_order - b.instance_order || a.pivot.order - b.pivot.order);
                            list += getItemListHtml(
                                dataSorted,
                                'wishlist',
                                row.id,
                                true,
                                true,
                                'js-wishlist-sorted',
                                (guild.do_sort_items_by_instance ? true : false),
                                (showListHeader ? headerWishlist + ' ' + wishlistNumber : null)
                            );
                            list += getItemListHtml(
                                data,
                                'wishlist',
                                row.id,
                                true,
                                false,
                                'js-wishlist-unsorted',
                                (guild.do_sort_items_by_instance ? false : true),
                                (showListHeader ? headerWishlist + ' ' + wishlistNumber : null)
                            );
                        }

                        return list;
                    }

                    if (data && data.length) {
                        let list = '';
                        // List number is a single number
                        if (currentWishlistNumber) {
                            list = createWishlistHtml(data, type, row, list, currentWishlistNumber, false);
                        } else { // Show all wishlists
                            // Create a new list for each wishlist number
                            for (i = 1; i <= maxWishlistLists; i++) {
                                list = createWishlistHtml(data, type, row, list, i, true);
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
                width   : "280px",
                className : "width-280",
            },
            {
                title  : `<span class="text-success fas fa-fw fa-sack"></span> ${headerReceived}`,
                data   : "received",
                render : function (data, type, row) {
                    return data && data.length ? getItemListHtml(data, 'received', row.id) : '—';
                },
                orderable : false,
                visible : true,
                width   : "280px",
                className : "width-280",
            },
            {
                title  : `<span class="text-gold fas fa-fw fa-book"></span> ${headerRecipes}`,
                data   : "recipes",
                render : function (data, type, row) {
                    return data && data.length ? getItemListHtml(data, 'recipes', row.id) : '—';
                },
                orderable : false,
                visible : false,
                width   : "280px",
                className : "width-280",
            },
            {
                /* this feature has been cut */
                title  : "Roles",
                data   : "user.roles",
                render : function (data, type, row) {
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
                orderable : false,
                visible : false,
            },
            {
                title  : `<span class="fas fa-fw fa-comment-alt-lines"></span> ${headerNotes}`,
                data   : "public_note",
                render : function (data, type, row) {
                    return getNotes(data, type, row);
                },
                orderable : false,
                visible : true,
                width   : "280px",
                className : "width-280",
            },
            {
                title  : "Class",
                data   : "class",
                render : function (data, type, row) {
                    return (row.class ? row.class : null);
                },
                visible : false,
            },
            {
                title  : "Raid Group",
                data   : "raid_group",
                render : function (data, type, row) {
                    let contents = '' + (row.raid_group_id ? row.raid_group_id : '');
                    if (row.secondary_raid_groups && row.secondary_raid_groups.length) {
                        row.secondary_raid_groups.forEach(function (raidGroup, index) {
                            contents += `${raidGroup.id} `;
                        });
                    }
                    return contents;
                },
                visible : false,
            },
            {
                title  : "Username",
                data   : "username",
                render : function (data, type, row) {
                    return (row.username ? row.username : null);
                },
                visible : false,
            },
            {
                title  : "Discord Username",
                data   : "discord_username",
                render : function (data, type, row) {
                    return (row.discord_username ? row.discord_username : null);
                },
                visible : false,
            },
            {
                title  : "Raids Attended",
                data   : "raid_count",
                render : function (data, type, row) {
                    return (row.raid_count ? row.raid_count : null);
                },
                visible    : false,
                searchable : false,
            },
        ],
        order  : [], // Disable initial auto-sort; relies on server-side sorting
        paging : false,
        fixedHeader : true, // Header row sticks to top of window when scrolling down
        drawCallback : function () {
            makeWowheadLinks();
            addClippedItemHandlers();
            addItemAutocompleteHandler();
            addTagInputHandlers();
            addWishlistSortHandlers();
            parseMarkdown();
            trackTimestamps();

            // Table was redrawn and item visibility was reset;
            // We should set visibility based on the previous setting.
            if (allItemsVisible) {
                showAllItems();
            } else {
                resetItemVisibility();
            }
        },
        initComplete: function () {
            // Columns that we want to filter by.
            const filterColumns = [colClass, colRaidGroup];

            // For each column, set up a filter
            this.api().columns().every(function (index) {
                var column = this;

                // select1 is the first filter for this column
                let select1 = null;
                // select2 is the second filter for this column
                let select2 = null; // Initialize this beside select1 if we want a secondary sort for the same column

                // Based on the current column, identify the relevant filter input
                if (index == colClass) {
                    select1 = $("#class_filter");
                    select2 = null;
                } else if (index == colRaidGroup) {
                    select1 = $("#raid_group_filter");
                    select2 = null;
                }

                if (filterColumns.includes(index)) {
                    select1.on('change', function () {
                        const val = $.fn.dataTable.util.escapeRegex($(this).val());

                        // Only IF we are using the second select
                        if (select2 && select2.val()) {
                            // Must contain both
                            val = "(?=.*" + val + ")(?=.*" + $.fn.dataTable.util.escapeRegex(select2.val()) + ")";
                        }

                        column.search(val ? val : '', true, false).draw();
                    }).change();

                    if (select2) {
                        select2.on('change', function () {
                            const val = $.fn.dataTable.util.escapeRegex($(this).val());

                            if (select1 && select1.val()) {
                                // Must contain both
                                val = "(?=.*" + val + ")(?=.*" + $.fn.dataTable.util.escapeRegex(select1.val()) + ")";
                            }

                            column.search(val ? val : '', true, false).draw();
                        }).change();
                    }
                }

                // Only show rows that have the desired item in the wishlist
                if (typeof filterWishlistsByItemName !== 'undefined' && filterWishlistsByItemName && index == colWishlist) {
                    $("#wishlist_filter").on('change', function () {
                        // If we filter wishlists to decide whether or not it contains the name(s) of given item(s)
                        const regex = "(" + itemNames.join("|") + ")";
                        column.search(regex, true, false).draw();
                    });
                }
            } );
            makeWowheadLinks();
            addItemAutocompleteHandler();
            addTagInputHandlers();
            addWishlistSortHandlers();
            parseMarkdown();
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

function addInstanceFilterHandlers() {
    $("#instance_filter").change(function () {
        let instanceId = $("#instance_filter").val();
        if (instanceId) {
            // Show all items, then remove the visible/hidden filters; they interfere with this filter.
            allItemsVisible = false;
            $(".js-show-all-clipped-items").click();
            $(".js-show-all-clipped-items").hide();
            $(".js-show-clipped-items").hide();
            $(".js-hide-clipped-items").hide();

            // hide all other instance's items
            $("li.js-has-instance[data-instance-id='" + instanceId + "']").show();
            $("li.js-has-instance[data-instance-id!='" + instanceId + "']").hide();
        } else {
            // show all instance's items
            $("li.js-has-instance[data-instance-id]").show();

            // Reset the visible/hidden filters to their default state.
            allItemsVisible = true;
            $(".js-show-all-clipped-items").click();
            $(".js-show-all-clipped-items").show();
            $(".js-show-clipped-items").show();
            $(".js-hide-clipped-items").hide();
        }
    });
}

function addWishlistFilterHandlers() {
    $("#wishlist_filter").on('change', function () {
        currentWishlistNumber = $(this).val();
        memberTable.rows().invalidate().draw();
    }).change();
}

// Gets an HTML list of items with pretty wowhead formatting
function getItemListHtml(data, type, characterId, useOrder = false, showInstances = false, listClass = null, isVisible = true, header = null) {
    let items = `<ol class="no-indent js-item-list mb-2 ${ listClass }" data-type="${ type }" data-id="${ characterId }" style="${ isVisible ? '' : 'display:none;' }">`;

    if (header) {
        items += `<li class="small text-muted no-bullet " data-type="${ type }" data-id="${ characterId }">${header}</li>`;
    }

    let initialLimit = 4;

    let lastInstanceId = null;
    let lastRaidGroupId = null;
    $.each(data, function (index, item) {

        // Skip prio item if raid group is disabled or not found in the guild
        // if (type == 'prio' && item.pivot.raid_group_id && guild.raidGroups.filter(raidGroup => (raidGroup.id == item.pivot.raid_group_id)).length < 1) {
        //     console.log(item.pivot.raid_group_id + ' not found');
        //     return false;
        // }

        let clipItem = false;
        if (index >= initialLimit) {
            clipItem = true;
            if (index == initialLimit) {
                items += `<li class="js-show-clipped-items small cursor-pointer no-bullet " data-type="${ type }" data-id="${ characterId }">show ${ data.length - initialLimit } more…</li>`;
            }
        }

        if (type == 'prio' && item.pivot.raid_group_id && item.pivot.raid_group_id != lastRaidGroupId) {
            lastRaidGroupId = item.pivot.raid_group_id;
            let raidGroupName = '';
            if (raidGroups.length) {
                let raidGroup = raidGroups.find(raidGroup => raidGroup.id === item.pivot.raid_group_id);
                 if (raidGroup) {
                    raidGroupName = raidGroup.name;
                }
            }
            items += `
                <li data-raid-group-id="" class="${ clipItem ? 'js-clipped-item' : '' } js-item-wishlist-character no-bullet font-weight-normal font-italic text-muted small"
                    style="${ clipItem ? 'display:none;' : '' }"
                    data-type="${ type }"
                    data-id="${ characterId }">
                    ${ raidGroupName }
                </li>
            `;
        }

        if (showInstances && item.instance_id && item.instance_id != lastInstanceId) {
            lastInstanceId = item.instance_id;
            items += `
                <li class="js-has-instance ${ clipItem ? 'js-clipped-item' : '' } no-bullet font-weight-normal font-italic text-muted small"
                    style="${ clipItem ? 'display:none;' : '' }"
                    data-type="${ type }"
                    data-id="${ characterId }"
                    data-instance-id="${ item.instance_id }">
                    ${ item.instance_name }
                </li>
            `;
        }

        let wowheadData = `data-wowhead-link="https://${ wowheadLocale + wowheadSubdomain }.wowhead.com/item=${ item.item_id }"
            data-wowhead="item=${ item.item_id }?domain=${ wowheadLocale + wowheadSubdomain }"`;

        items += `
            <li class="js-has-instance font-weight-normal ${ clipItem ? 'js-clipped-item' : '' }"
                data-type="${ type }"
                data-id="${ characterId }"
                data-instance-id="${ item.instance_id }"
                data-wishlist-number="${item.list_number}"
                value="${ useOrder ? item.pivot.order : '' }"
                style="${ clipItem ? 'display:none;' : '' }">
                ${ guild.tier_mode ?
                    `<span class="text-monospace font-weight-medium text-tier-${ item.guild_tier ? item.guild_tier : '' }">${ item.guild_tier ? getItemTierLabel(item, guild.tier_mode) : '&nbsp;' }</span>`
                : `` }
                <a href="/${ guild.id }/${ guild.slug }/i/${ item.item_id }/${ slug(item.name) }"
                    class="${ item.quality ? 'q' + item.quality : '' } ${ item.pivot.is_received && (item.pivot.type == 'wishlist' || item.pivot.type == 'prio') ? 'font-strikethrough' : '' }"
                    ${ wowheadData }>
                    ${ item.name }
                </a>
                ${ item.pivot.is_offspec ? '<span title="offspec item" class="small font-weight-bold text-muted">OS</span>' : '' }
                <span class="js-watchable-timestamp js-timestamp-title smaller text-muted"
                    data-timestamp="${ item.pivot.received_at ? item.pivot.received_at : item.pivot.created_at }"
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

function getNotes(data, type, row) {
    let secondaryRaidGroups = '';
    if (row.secondary_raid_groups && row.secondary_raid_groups.length) {
        secondaryRaidGroups = `<ul class="list-inline">`;
        row.secondary_raid_groups.forEach(function (raidGroup, index) {
            secondaryRaidGroups += `<li class="list-inline-item small"><span class="tag text-muted"><span class="role-circle align-fix" style="background-color:${ getColorFromDec(parseInt(raidGroup.color)) }"></span>${raidGroup.name}</span></li>`;
        });
        secondaryRaidGroups += `</ul>`;
    }
    return (row.public_note ? `<span class="js-markdown-inline">${ DOMPurify.sanitize(nl2br(row.public_note)) }</span>` : '—')
        + (row.officer_note ? `<br><small class="font-weight-bold font-italic text-gold">Officer\'s Note</small><br><span class="js-markdown-inline">${ DOMPurify.sanitize(nl2br(row.officer_note)) }</span>` : '')
        + (secondaryRaidGroups ? `<br>${secondaryRaidGroups}` : ``);
}

function resetItemVisibility() {
    $(".js-clipped-item").hide();
    $(".js-show-clipped-items").show();
    $(".js-hide-clipped-items").hide();
}

function showAllItems() {
    $(".js-clipped-item").show();
    $(".js-show-clipped-items").hide();
    $(".js-hide-clipped-items").hide();
}
