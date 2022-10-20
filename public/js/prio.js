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

    sortItems();

    $(".loadingBarContainer").removeClass("d-flex").hide();
    $("#editForm").show();
});

// icon is a jquery element that contains a font awesome class (fas or fal)
function makeIconLight(icon) {
    icon.removeClass("fas");
    icon.addClass("fal");
}
function makeIconSolid(icon) {
    icon.removeClass("fal");
    icon.addClass("fas");
}

// Sort the items based on the pins stored in cookies
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
        return a.getAttribute('data-user-order') > b.getAttribute('data-user-order');
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
