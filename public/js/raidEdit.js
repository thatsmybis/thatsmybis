$(document).ready(function () {
    let initializing = true;

    warnBeforeLeaving("#editForm")

    $("[name=date]").datetimepicker({
        format: 'Y-m-d H:i:s',
        inline: true,
        step: 30,
        theme: 'dark',
        value: raidDate,
    });

    $("[name=raid_group_id\\[\\]]").change(function () {
        if (!initializing) {
            if ($(this).val()) {
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

    initializing = false;
});

function findExistingCharacter(characterId, except = null) {
    if (except) {
        return $(`select[name^=characters][name$=\\[character_id\\]] option:selected[value="${characterId}"]`).not(except).first();
    } else {
        return $(`select[name^=characters][name$=\\[character_id\\]] option:selected[value="${characterId}"]`).first();
    }
}

// Add characters belonging to the given raid group to the character list if they're not already in it
function fillCharactersFromRaid(raidGroupId) {
    const raidGroupCharacters = characters.filter(character => character.raid_group_id == raidGroupId);

    let addedCount = 0;
    let alreadyAddedCount = 0;

    for (const character of raidGroupCharacters) {
        const existing = findExistingCharacter(character.id);
        if (!existing.length) {
            let emptyCharacterSelect = $('select[name^=characters][name$=\\[character_id\\]] option:selected[value=""]').first().parent();
            let characterOption = emptyCharacterSelect.find('option[value="' + character.id + '"i]');
            if (characterOption.val()) {
                characterOption.prop("selected", true).change();
                addedCount++;
            }

            // Reset associated inputs
            const row = emptyCharacterSelect.parent().closest(".js-row");
            $(row).find("[name^=characters][name$=\\[is_exempt\\]]").prop("checked", false).change();
            $(row).find("[name^=characters][name$=\\[remark_id\\]]").val("").change();
            $(row).find("[name^=characters][name$=\\[credit\\]]").bootstrapSlider('setValue', 1);
        } else {
            alreadyAddedCount++;
        }
    }

    $(".js-raid-group-message").html(`${addedCount} characters added${ alreadyAddedCount ? ` (${alreadyAddedCount} already in list)` : '' }`).show();
    setTimeout(() => $(".js-raid-group-message").hide(), 7500);
}

// Hack to get the slider's labels to refresh: https://github.com/seiyria/bootstrap-slider/issues/396#issuecomment-310415503
function fixSliderLabels() {
    window.dispatchEvent(new Event('resize'));
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
