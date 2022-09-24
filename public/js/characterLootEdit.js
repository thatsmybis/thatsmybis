// CSV import/parsing code ripped (and edited) from https://www.papaparse.com/demo
var inputType = "string";
var firstRun  = true;
var addedCount     = 0;
var skippedCount   = 0;
var overLimitCount = 0;
var skippedItems   = [];
var errorCount = 0;
var firstError = undefined;

$(document).ready(function () {
    let initializing = true;

    warnBeforeLeaving("#itemForm");
    addSortHandlers();

    $(".js-toggle-import").click(function() {
        $("#toggleImport").toggle()
        $("#importArea").toggle();
    });

    $("[name=import_textarea]").bind('input change', function () {
        if ($(this).val()) {
            $("#submitImport").prop('disabled', false);
        } else {
            $("#submitImport").prop('disabled', true);
        }
    });

    $("#submitImport").click(function () {
        parseUpgradesImport(this);
    });

    // Show/hide note input
    $(".js-toggle-note").change(function () {
        const index = $(this).data('index');
        const type = $(this).data('type');

        if ($(this).prop("checked")) {
            $(".js-note[data-index=" + index + "][data-type=" + type + "]").parent().show();
        } else {
            $(".js-note[data-index=" + index + "][data-type=" + type + "]").parent().hide();
        }
    });

    initializing = false;
    $(".loadingBarContainer").removeClass("d-flex").hide();
    $("#itemForm").show();
});

function parseUpgradesImport($this) {
    if ($($this).prop('disabled') == "true") {
        return;
    }
    disableForm();
    $("#status-message").html("").hide();

    errorCount = 0;
    firstError = undefined;

    addedCount     = 0;
    overLimitCount = 0;
    skippedItems   = [];

    if (!firstRun) {
        console.log("--------------------------------------------------");
    } else {
        firstRun = false;
    }

    console.log('Input accepts JSON. eg. { "items": [ { "name": "Azuresong Mageblade", "id": 17103 }, { "name": "Staff of Dominance", "id": 18842 } ] }');

    console.log("Importing wishlist items...");

    var input = $('#importTextarea').val();

    if (!input) {
        alert("Please enter a valid JSON string. Re-copy the export from 60/70/80 upgrades.com");
        enableForm();
        return 0;
    }

    var json = null;
    try {
        json = JSON.parse(input);
    } catch (error) {
        console.log("Invalid JSON input.");
        console.log("Clearing input... Old input:", $("[name=import_textarea]").val());
        clearInput()
        errorCount++;
        firstError = "Invalid input. Try again.";
    }

    if (!errorCount) {
        console.log(json);

        setTimeout(function(){}, 250); // hack

        if (json.items && json.items.length > 0) {
            // Load the items into the form
            for (let i = 0; i < json.items.length; i++) {
                let item = json.items[i];
                if (i >= maxItems) {
                    overLimitCount++;
                    skippedItems.push(item.name);
                    continue;
                } else {
                    loadItemToForm(item);
                    addedCount++;
                }
            }
            // Load wowhead links for any items we just populated.
            makeWowheadLinks();
        } else {
            console.log("No items found");
        }
    }

    let statusMessages = "";

    if (firstError) {
        statusMessages += `<li class="text-danger">Error: ${ firstError }</li>`;
    }

    if (addedCount) {
        statusMessages += `<li class="text-success">${ addedCount } item${ addedCount > 1 ? 's' : '' } loaded into form</li>`;
    }

    if (overLimitCount) {
        statusMessages += `<li class="text-warning">${ overLimitCount ? "" + overLimitCount + " items over the limit of " + maxItems  : ""}</li>`;
    }

    if (skippedItems.length) {
        let length = skippedItems.length;
        skippedItems = skippedItems.join(", ");
        statusMessages += `<li class="text-danger">${ length } item${ length > 1 ? 's' : '' } skipped (no room): ${ skippedItems }</li>`;
    }

    // statusMessages += `<li class="text-muted">More details in console (usually F12)</li>`;

    if (statusMessages) {
        $("#status-message").html(`<ul>${statusMessages}</ul>`).show();
    }

    clearInput();

    // icky hack
    setTimeout(function(){enableForm();}, 100);
}

function clearInput() {
    $("[name=import_textarea]").val("");
}

function enableForm() {
    setTimeout(function () {
        $("#itemForm fieldset").prop("disabled", false);

        $("#loading-indicator").hide();
        $("#loaded-indicator").show();
        setTimeout(function () {
            $("#submitImport").prop('disabled', false);
            $(".js-toggle-import").prop("disabled", false);
            $("#loaded-indicator").hide();
        }, 5000); // Let this drag on a bit longer, otherwise it can disappear too quickly to notice
    }, 1000);
}

function disableForm() {
    $("#loading-indicator").show();
    $("#submitImport").prop('disabled', true);
    $("#itemForm fieldset").prop("disabled", true);
    $(".js-toggle-import").prop("disabled", true);
}

/**
 * Takes in an object that has a `name` and `id` property.
 * Loads that into the next available wishlist input.
 *
 * @param item Object
 *
 * @return void
 */
function loadItemToForm(item) {
    const wishlistInput = $("#wishlist");

    addTag(wishlistInput, item.id, item.name);
}
