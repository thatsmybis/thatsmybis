const PINNED_ITEMS = "thatsmybis.pinnedItems";
$(document).ready(function () {
    warnBeforeLeaving("#editForm")

    // sortable() is slow to initialize when applied to hundreds of elements, so this solves for that scenario
    $(".js-sortable-lazy").one("mouseenter", function() {
        $(this).sortable({
            handle: ".js-sort-handle",
            // Update the order palceholders once sorting finishes
            stop: function(event, ui) {
                updatePlaceholders();
            },
        });
    });

    $(".js-pin-item").click(function () {
        const itemId = $(this).data("itemId");
        const pinnedItemsCookie = Cookies.get(PINNED_ITEMS);

        // Add/remove item from cookie
        if (!pinnedItemsCookie) {
            const pinnedItems = [];
            pinnedItems.push(itemId);
            Cookies.set(PINNED_ITEMS, JSON.stringify(pinnedItems));
        } else {
            let pinnedItems = JSON.parse(pinnedItemsCookie);
            if (pinnedItems.find(element => element === itemId)) {
                pinnedItems = pinnedItems.filter(element => element !== itemId);
                Cookies.set(PINNED_ITEMS, JSON.stringify(pinnedItems));
            } else {
                pinnedItems.push(itemId);
                Cookies.set(PINNED_ITEMS, JSON.stringify(pinnedItems));
            }
        }

        sortItems();
    });

    // If the user has pinned items in the cookies, apply their ordering
    sortItems();

    $(".js-reset-ranks").click(function () {
        const index = $(this).data("index");
        $(`input.js-rank[data-index='${index}']`).val("");
    });

    $(".js-reset-all-ranks").click(function () {
        if (confirm("This will remove any manually input numbers for prio ranks. You can review this before you submit the page. Continue?")) {
            $(`input.js-rank`).val("");
        }
    });

    // Show/hide note input
    applyNoteToggleHandlers();
    // When a character is added to the prio list and a new entry is populated, reapply the handlers for notes
    $("select.js-input-select").change(function () {
        console.log('applying handlers');
        applyNoteToggleHandlers();
    });

    $(".loadingBarContainer").removeClass("d-flex").hide();
    $("#editForm").show();
});

// When the note toggle is changed, show/hide the note input
function applyNoteToggleHandlers() {
    $(".js-toggle-note").off();
    // Show/hide note input
    $(".js-toggle-note").change(function () {
        const index = $(this).data('index');

        if ($(this).prop("checked")) {
            $(".js-note[data-index=" + index + "]").parent().show();
        } else {
            $(".js-note[data-index=" + index + "]").parent().hide();
        }
    });
}

// icon is a jquery element that contains a font awesome class (fas or fal)
function makeIconLight(icon) {
    icon.removeClass("fas");
    icon.addClass("fal");
}
function makeIconSolid(icon) {
    icon.removeClass("fal");
    icon.addClass("fas");
}

// Sort the items based on the pins stored in cookies; if any
function sortItems() {
    let pinnedItems = [];
    const pinnedItemsCookie = Cookies.get(PINNED_ITEMS);
    if (pinnedItemsCookie) {
        pinnedItems = JSON.parse(pinnedItemsCookie);
        // If the cookie is corrupt or something, ensure an empty array is still used
        if (!pinnedItems) {
            pinnedItems = [];
        }
    }

    $(".js-pin-sortable").each(function () {
        const itemId = $(this).data("itemId");
        if (pinnedItems.find(element => element === itemId)) {
            // Give it a negative index so it shows up at the start of the list
            const newOrder = -1000000 + pinnedItems.indexOf(itemId);
            $(this).attr('data-user-order', newOrder);
            makeIconSolid($(this).find(".js-pin-item"));
        } else {
            const oldOrder = $(this).attr('data-original-order');
            $(this).attr('data-user-order', oldOrder);
            makeIconLight($(this).find(".js-pin-item"));
        }
    });

    // Sort based on user pins
    $("#pinnableList").children(".js-pin-sortable").sort(function (a, b) {
        const aOrder = parseInt(a.getAttribute('data-user-order'));
        const bOrder = parseInt(b.getAttribute('data-user-order'));
        return (aOrder > bOrder) ? 1 : -1;
    }).appendTo($("#pinnableList"));


    // What we just did can bug out the tooltips.
    setTimeout(function(){$(".tooltip").remove();}, 100); // hackity-hack
}

// Update the placeholder values of anything with the given name.
// Good for resetting a list's indexed placeholders after the list is reordered.
function updatePlaceholders() {
    $("ol.js-sortable-lazy").each(function () {
        let index = 0;
        $(this).find(`input[name$="[order]"]`).each(function () {
            index++;
            $(this).attr("placeholder", index);
        });
    });
}
