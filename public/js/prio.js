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

    $(".loadingBarContainer").removeClass("d-flex").hide();
    $("#editForm").show();
});

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
