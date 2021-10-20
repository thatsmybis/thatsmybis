$(document).ready(function () {
    let initializing = true;

    warnBeforeLeaving("#editForm");

    // Always initialize to null, let the javascript convert from UTC time to local browser time,
    // then it will populate the date.
    $.datetimepicker.setLocale((locale ? locale : 'en'));
    $(".js-date-input").datetimepicker({
        format: 'Y-m-d H:i:s',
        inline: true,
        step: 30,
        theme: 'dark',
        value: date ? moment.utc(date).local().format("YYYY-MM-DD HH:mm:ss") : moment().format("YYYY-MM-DD HH:mm:ss"),
    });

    // Trigger a date change to make it convert to UTC and stuff.
    $(".js-date-input").change();

    $("[name=raid_group_id\\[\\]]").change(function () {
        if (!initializing) {
            if ($("[name=add_raiders]").prop("checked") && $(this).val()) {
                fillCharactersFromRaid($(this).val());
            }
        }
    });

    // Don't allow for picking the same character in multiple inputs.
    $("[name^=characters][name$=\\[character_id\\]").change(function () {
        if (!initializing) {
            const existing = findExistingCharacter($(this).val(), $(this).find(":selected"));
            if (existing.length) {
                $(this).selectpicker("val", "").selectpicker("refresh");
            }
        }
    });

    // Show the next input
    $(".js-show-next").change(function() {
        showNext(this);
    }).change();
    $(".js-show-next").keyup(function() {
        showNext(this);
    });
    $(".js-show-next-character").change(function() {
        showNextCharacter(this);
    }).change();
    $(".js-show-next-character").keyup(function() {
        showNextCharacter(this);
    });

    $("#addWarcraftlogsAttendees").click(function () {
        getWarcraftlogsRankedCharacters(addCharacter, WARCRAFTLOGS_MODE_EXISTING);
    });

    $(".js-show-notes").click(function () {
        const index = $(this).data('index');
        $(this).hide();
        $(`.js-notes[data-index="${index}"]`).show();
    });

    $(".js-attendance-skip").on('change', function () {
        const index = $(this).data('index');
        if (this.checked) {
            $(`[data-attendance-input="${index}"]`).addClass("disabled").hide();
            $(`[data-attendance-skip-note="${index}"]`).show();
        } else {
            $(`[data-attendance-input="${index}"]`).removeClass("disabled").show();
            $(`[data-attendance-skip-note="${index}"]`).hide();
        }
    }).change();

    $(".js-clear-attendees").click(function () {
        resetAttendees()
    });

    initializing = false;
    $(".loadingBarContainer").removeClass("d-flex").hide();
    $("#editForm").show();
    fixSliderLabels();
});

function addCharacter(character) {
    const characterId = character.id;
    let isAdded = false;
    const existing = findExistingCharacter(characterId);
    if (!existing.length) {
        let emptyCharacterSelect = $('select[name^=characters][name$=\\[character_id\\]] option:selected[value=""]').first().parent();
        let characterOption = emptyCharacterSelect.find('option[value="' + characterId + '"i]');
        if (characterOption.val()) {
            characterOption.prop("selected", true).change();
            isAdded = true;
        }

        // Reset associated inputs
        const row = emptyCharacterSelect.parent().closest(".js-row");
        $(row).find("[name^=characters][name$=\\[is_exempt\\]]").prop("checked", false).change();
        $(row).find("[name^=characters][name$=\\[remark_id\\]]").val("").change();
        $(row).find("[name^=characters][name$=\\[credit\\]]").bootstrapSlider('setValue', 1);
    }
    return isAdded;
}

function findExistingCharacter(characterId, except = null) {
    if (except) {
        return $(`select[name^=characters][name$=\\[character_id\\]] option:selected[value="${characterId}"]`).not(except).first();
    } else {
        return $(`select[name^=characters][name$=\\[character_id\\]] option:selected[value="${characterId}"]`).first();
    }
}

// Add characters belonging to the given raid group to the character list if they're not already in it
function fillCharactersFromRaid(raidGroupId) {
    const mainRaidGroupCharacters = characters.filter(character => character.raid_group_id == raidGroupId);
    const secondaryRaidGroupCharacters = characters.filter(character =>
        character.secondary_raid_groups.filter(raidGroup => raidGroup.id == raidGroupId).length > 0
    );

    // Combine the two arrays and sort by name
    const raidGroupCharacters = mainRaidGroupCharacters
        .concat(secondaryRaidGroupCharacters)
        .sort((a,b) => (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0));

    let addedCount = 0;

    for (const character of raidGroupCharacters) {
        addCharacter(character);
        addedCount++;
    }

    $(".js-raid-group-message").html(`${addedCount} attendees added`).show();
    setTimeout(() => $(".js-raid-group-message").hide(), 7500);
}

// Hack to get the slider's labels to refresh: https://github.com/seiyria/bootstrap-slider/issues/396#issuecomment-310415503
function fixSliderLabels() {
    window.dispatchEvent(new Event('resize'));
}

// Reset and empty the attendee list.
function resetAttendees () {
    if (confirm("Are you sure you want to empty and reset the attendee list?")) {
        // Raid group selects
        $('select[name^=raid_group_id]').val('').change();
        // Char select
        $('select[name^=characters][name$=\\[character_id\\]]').val('').change();
        // Excused
        $(".js-attendance-skip").prop("checked", false).change();
        // Note / remark
        $('select[name^=characters][name$=\\[remark_id\\]]').val('').change();
        // Credit slider
        $("[name^=characters][name$=\\[credit\\]]").bootstrapSlider('setValue', 1);
        // Public note
        $('[name^=characters][name$=\\[public_note\\]]').val('');
        // Officer note
        $('[name^=characters][name$=\\[officer_note\\]]').val('');
        // Show the custom note toggle
        $(".js-show-notes").show();
        // Hide the custom notes
        $(`.js-notes`).hide();
    }
}

// If the current element has a value, show it and the next element that is hidden because it is empty
function showNext(currentElement) {
    if ($(currentElement).val() != "") {
        $(currentElement).show();
        $(currentElement).parent().next(".js-hide-empty").show();
    }
}

// If the current element has a value, show it and the next element that is hidden because it is empty
function showNextCharacter(currentElement) {
    if ($(currentElement).val() != "") {
        $(currentElement).show();
        let nextElement = $(currentElement).closest(".js-row").next(".js-hide-empty");
        nextElement.show();
        nextElement.find("select[name^=characters][name$=\\[character_id\\]]").addClass("selectpicker").selectpicker();
        fixSliderLabels();
    }
}
