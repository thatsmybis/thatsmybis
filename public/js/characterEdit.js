var firstLoad = true;

// For some reason I was having issue with [value=''] in jQuery actually getting empty inputs...
// So I am doing this instead.
var characterLoadIndex = 0;

$(document).ready(function() {
    $(".js-character").click(function () {
        $(this).next(".js-character").first().show();
    });
    $(".js-character").change(function () {
        if (!firstLoad) {
            $(this).next(".js-character").first().show();
        }
    });

    // When a class is selected, enable+show relevant specs and disabled+hide irrelevant specs
    $(".js-class").change(function () {
        const index = $(this).data('index');
        const className = $(this).val();
        if (className) {
            const oldClassName = $(`.js-spec[data-index=${index}] option:selected`).data("class");

            // Class name changed; reset spec to unselected
            if (!firstLoad && oldClassName != className) {
                $(`.js-spec[data-index=${index}]`).val("");
            }

            $(`.js-spec[data-index=${index}]`).prop("disabled", false);
            $(`.js-spec[data-index=${index}] option`).prop("disabled", false).show();
            $(`.js-spec[data-index=${index}] option`).not("[value='']").not("[data-class='" + className + "']").prop("disabled", true).hide();

            $(`.js-spec-label[data-index=${index}]`).prop("disabled", false);
        } else {
            $(`.js-spec[data-index=${index}]`).val("");
            $(`.js-spec[data-index=${index}]`).prop("disabled", true);
            $(`.js-spec-label[data-index=${index}]`).val("");
            $(`.js-spec-label[data-index=${index}]`).prop("disabled", true);
        }
    }).change();

    // Ensure current archetype makes sense for current selection
    $(`.js-spec`).change(function () {
        const index = $(this).data("index");
        const oldArchetype = $(`.js-archetype[data-index=${index}]`).val();
        const newArchetype = $(this).find(":selected").data('archetype');

        if (!firstLoad && newArchetype && oldArchetype != newArchetype) {
            $(`.js-archetype[data-index=${index}]`).val(newArchetype);
            flashElement($(`.js-archetype[data-index=${index}]`));
        }
    }).change();


let logs = $("[name^=logs]:visible");

    let validCodes = [];

    // Extract report codes from the URLs
    logs.each(function () {
        if ($(this).val()) {
            log = new URL($(this).val());
            log.pathname.split('/').forEach(function (pathPart) {
                // 16 characters looks valid enough to go from here...
                if (pathPart.length === 16) {
                    validCodes.push(pathPart);
                    return;
                }
            });
        }
    });

    $("#addWarcraftlogsCharacters").click(function () {
        if (confirm("This WILL OVERWRITE any existing inputs in this form. Continue?")) {
            $(".js-name").val("");
            $(".js-class").val("");
            $(".js-archetype").val("");
            $(".js-spec-label").val("");
            $(".js-spec").val("");

            let input = $("[name^=logs]:visible");

            characterLoadIndex = 0;
            if (input.val().includes("warcraftlogs.com")) {
                if (hasLinkedWarcraftLogs) {
                    getWarcraftlogsRankedCharacters(addCharacter, WARCRAFTLOGS_MODE_NEW);
                } else {
                    $(".js-warcraftlogs-attendees-message").html(`<span class="font-weight-bold text-warning">Connect your Warcraft Logs account in guild settings to import</span>`).show();
                }
            } else {
                let characters = parseCharacterList(input.val());
                printWarcraftlogsRankedCharacters(characters, addCharacter, WARCRAFTLOGS_MODE_NEW);
            }
            characterLoadIndex = 0;
        }
    });

    // If the page loads and the browser autocompletes hidden inputs... show them.
    $('.js-name').each(function () {
        $(this).val() ? $(this).click() : null;
    });

    addInputAntiSubmitHandler();

    firstLoad = false;
});

// Load character name and spec (if available) into inputs.
// Starts at first input and increases. Will overwrite existing values.
function addCharacter(character) {
    if ($('.js-name').eq(characterLoadIndex).length && character.name) {
        let classSlug = ucfirst(character.classID && WARCRAFTLOGS_CLASSES[character.classID] ? WARCRAFTLOGS_CLASSES[character.classID].slug : '');
        $('.js-name').eq(characterLoadIndex).val(character.name);
        $('.js-name').eq(characterLoadIndex).change();
        if (classSlug) {
            $('.js-class').eq(characterLoadIndex).val(classSlug);
            $('.js-class').eq(characterLoadIndex).change();
        }
        characterLoadIndex++;
        return true;
    } else {
        return false;
    }
}

function parseCharacterList(list) {
    let parseResults = null;
    // Auto-detect delimiter
    parseResults = Papa.parse(list, {delimiter: ''});

    // Didn't get back a list of names... try again with a space as a delimiter
    if (parseResults.errors && parseResults.errors.length && !(parseResults.data && parseResults.data[0].length > 1)) {
        // Try using space as the delimiter
        parseResults = Papa.parse(list, {delimiter: ' '});
    }

    parseResults = (parseResults.data && parseResults.data[0].length > 1) ? parseResults.data[0] : [];

    let characters = [];

    if (parseResults.length) {
        parseResults.forEach(function (result) {
            characters.push([result, {name: result, classID: null}]);
        });
    }
    return characters;
}
