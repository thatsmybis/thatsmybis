// CSV import/parsing code ripped (and edited) from https://www.papaparse.com/demo
var inputType = "string";
var firstRun  = true;
var addedCount      = 0;
var skippedCount    = 0;
var disenchantCount = 0;
var overLimitCount  = 0;
var offspecCount    = 0;
var missingCharacters     = [];
var missingCharacterCount = 0;
var errorCount = 0;
var firstError = undefined;
var preventDuplicates = true;

$(document).ready(function () {
    $(".js-show-next").change(function() {
        showNext(this);
    });
    $(".js-show-next").keyup(function() {
        showNext(this);
    });

    // Load only the visible pickers so that we don't have to initialize ALL of them on page load.
    // The rest get lazy loaded when they're shown.
    $("select[name^=item][name$=\\[character_id\\]]:visible").addClass("selectpicker").selectpicker();

    // If the current element has a value, show it and the next element that is hidden because it is empty
    function showNext(currentElement) {
        if ($(currentElement).val() != "") {
            $(currentElement).show();
            let nextElement = $(currentElement).closest(".row").next(".js-hide-empty");
            nextElement.show();
            nextElement.find("select[name^=item][name$=\\[character_id\\]]").addClass("selectpicker").selectpicker();
        }
    }

    $("[name=raid_group_id]").on('change', function () {
        if ($(this).val()) {
            $("#raidGroupWarning").hide();
        } else {
            $("#raidGroupWarning").show();
        }
    }).change();

    // Filter out characters based on the raid group they are in
    $("#raid_group_filter").on('change', function () {
        let raidGroupId = $(this).val();

        if (raidGroupId) {
            $(".js-character-option[data-raid-group-id!='" + raidGroupId + "']").hide();
            $(".js-character-option[data-raid-group-id='" + raidGroupId + "']").show();
        } else {
            $(".js-character-option").show();
        }
        $(".selectpicker").selectpicker("refresh");
    }).change();

    // Toggles visibility on the Note inputs
    $("[name=toggle_notes]").on('change', function () {
        if (this.checked) {
            $(".js-note").show();
        } else {
            $(".js-note").hide();
        }
    }).change();

    // Toggles visibility on the Date inputs
    $("[name=toggle_dates]").on('change', function () {
        if (this.checked) {
            $(".js-date").show();
            $("#default_datepicker").show();
        } else {
            $(".js-date").hide();
            $("#default_datepicker").hide();
        }
    }).change();

    // Sets all dates to the chosen date
    $("[name=date_default]").on('change', function () {
        $("[name*='received_at']").val($(this).val());
    });

    $(".js-toggle-import").click(function() {
        $("#toggleImportArea").toggle();
        $("#importArea").toggle();
    });

    // Tabs for changing between what to parse for the CSV parser.
    // Implmented
    $('#tab-string').click(function()
    {
        $('.tab').removeClass('active');
        $(this).addClass('active');
        $('.input-area').hide();
        $('#input-string').show();
        $('#submit').text("Parse");
        inputType = "string";
    });
    // Unused, but parseCsv() should be 80% implemented
    // Reference https://www.papaparse.com/demo if you want to implement the front-end for any of these
    $('#tab-local').click(function()
    {
        $('.tab').removeClass('active');
        $(this).addClass('active');
        $('.input-area').hide();
        $('#input-local').show();
        $('#submit').text("Parse");
        inputType = "local";
    });
    // Unused, but parseCsv() should be 80% implemented
    $('#tab-remote').click(function()
    {
        $('.tab').removeClass('active');
        $(this).addClass('active');
        $('.input-area').hide();
        $('#input-remote').show();
        $('#submit').text("Parse");
        inputType = "remote";
    });
    // Unused, but parseCsv() should be 80% implemented
    $('#tab-unparse').click(function()
    {
        $('.tab').removeClass('active');
        $(this).addClass('active');
        $('.input-area').hide();
        $('#input-unparse').show();
        $('#submit').text("Unparse");
        inputType = "json";
    });

    $("#submitImport").click(function () {
        parseCsv(this);
    });
});

/**
 * Parse a CSV and load it into the form.
 *
 * Depending on the user input different sources will be parsed. (textarea, file, url, etc.)
 *
 * @var $this  object The object calling this function
 *
 * @return     bool   True on success.
 */
function parseCsv($this) {
    if ($($this).prop('disabled') == "true") {
        return;
    }

    // Allow only one parse at a time
    disableForm();
    $("#status-message").html("").hide();

    errorCount = 0;
    firstError = undefined;

    addedCount        = 0;
    skippedCount      = 0;
    disenchantCount   = 0;
    overLimitCount    = 0;
    offspecCount      = 0;
    missingCharacterCount = 0
    missingCharacters     = [];

    var config = {
        delimiter:      "",
        header:         true,
        dynamicTyping:  false,
        skipEmptyLines: true,
        preview:        0,
        step:           undefined, // stepCsvImport
        encoding:       "",
        worker:         false,
        comments:       false,
        complete:       completeCsvImport,
        error:          errorCsvImport,
        download:       (inputType == "remote")
    };

    var input = $('#importTextarea').val();

    if (inputType == "remote") {
        input = $('#url').val();
    } else if (inputType == "json") {
        input = $('#json').val();
    }

    if (!firstRun) {
        console.log("--------------------------------------------------");
    } else {
        firstRun = false;
    }

    if (inputType == "local") { // untested
        if (!$('#files')[0].files.length) {
            alert("Please choose at least one file to parse.");
            enableForm();
            return 0;
        }

        $('#files').parse({
            config: config,
            before: function(file, inputElem) {
                console.log("Parsing file...", file);
            },
            error: function(err, file) {
                console.log("ERROR:", err, file);
            },
            complete: function() {
            }
        });
    } else if (inputType == "json") { // untested
        if (!input) {
            alert("Please enter a valid JSON string to convert to CSV.");
            enableForm();
            return 0;
        }

        var csv = Papa.unparse(input, config);

        console.log("Unparse complete");

        if (csv.length > maxUnparseLength) {
            csv = csv.substr(0, maxUnparseLength);
            console.log("(Results truncated for brevity)");
        }

        console.log(csv);

        setTimeout(function(){enableForm();}, 100); // hackity-hack
    } else if (inputType == "remote" && !input) { // untested
        alert("Please enter the URL of a file to download and parse.");
        enableForm();
        return 0;
    } else {
        var results = Papa.parse(input, config);

        if (config.worker || config.download) {
            console.log("Running...");
        }
    }
}

// Optional function that gets called on each row being parsed
function stepCsvImport(results, parser)
{
    //
}

// Called when the CSV import is completed.
function completeCsvImport(results)
{
    setTimeout(function(){}, 250); // hack

    let rowCount = 0;

    if (results && results.errors)
    {
        if (results.errors)
        {
            errorCount = results.errors.length;
            firstError = results.errors[0];
        }
        if (results.data && results.data.length > 0) {
            rowCount = results.data.length;
        }
    }

    console.log("Parse complete");
    console.log("    Rows:", rowCount);
    console.log("    Errors:", errorCount);
    console.log("    First Error:", firstError);
    console.log("    Meta:", results.meta);
    console.log("    Results:", results);
    console.log("Loading data into form...");

    // Load the results into the form
    if (results.data.length > 0) {
        // Clear the form
        prepareForm();

        // Load the parsed items into the form
        let inputRow = 0;
        for (let i = 0; i < results.data.length; i++) {
            let item = results.data[i];
            // Skip over requests to disenchant an item
            let disenchantFlags = ['de', 'disenchant'];
            if (i >= maxItems) {
                skippedCount++;
                overLimitCount++;
                continue;
            } else if (item['response'] != undefined && disenchantFlags.includes(item['response'].toLowerCase())) {
                console.log(`Skipping row ${ (i + 1) }: Disenchant ${ item.item ? item.item : (item.item_id ? item.item_id : '') }`);
                skippedCount++;
                disenchantCount++;
                continue;
            } else {
                loadItemToForm(item, inputRow);
                inputRow++;
                addedCount++;
            }
        }
        // Load wowhead links for any items we just populated.
        makeWowheadLinks();
        // Update the fancy select for any select options we just chose.
        $(".selectpicker").selectpicker("refresh");
    }

    let statusMessages = "";

    if (firstError) {
        statusMessages += `<li class="text-danger">Error: ${ firstError.message }</li>`;
    }

    if (addedCount) {
        statusMessages += `<li class="text-success">${ addedCount } item${ addedCount > 1 ? 's' : '' } loaded into form</li>`;
    }

    if (skippedCount) {
        statusMessages += `<li class="text-warning">Skipped ${ skippedCount } item${ skippedCount > 1 ? 's' : '' } ${ disenchantCount ? "(" + disenchantCount + " items disenchanted)" : "" } ${ overLimitCount ? "(" + overLimitCount + " items over the limit of " + maxItems + ")" : ""}</li>`;
    }

    if (offspecCount) {
        statusMessages += `<li class="text-warning">${ offspecCount } item${ offspecCount > 1 ? 's' : '' } flagged as offspec</li>`;
    }

    if (missingCharacterCount) {
        missingCharacters = missingCharacters.join(", ");
        statusMessages += `<li class="text-danger">${ missingCharacterCount } character${ missingCharacterCount > 1 ? 's' : '' } not found: ${ missingCharacters }</li>`;
    }

    statusMessages += `<li class="text-muted">More details in console (usually F12)</li>`;

    if (statusMessages) {
        $("#status-message").html(`<ul>${statusMessages}</ul>`).show();
    }



    // icky hack
    setTimeout(function(){enableForm();}, 100);
}

// Called when the CSV import runs into an error
function errorCsvImport(err, file)
{
    console.log("ERROR:", err, file);
    $("#status-message").html("ERROR: " + err).show();
    enableForm();
}

/**
 * Takes in an item object with variable fields. Parses through it to gets the fields we want.
 * Loads those fields into the form.
 * RCLootCouncil addon formatting docs: https://github.com/evil-morfar/RCLootCouncil2/wiki/CSV-Import
 * (some of these variables are based on the RCLC fields)
 *
 * @param item Array|Object An array/object containing keys for the object's properties.
 * @param i    int          An integer indicating which input field this should correspond to
 *
 * @return item Array|Object The same item that was passed in.
 */
function loadItemToForm(item, i) {
    let importId      = null;
    let itemId        = null;
    let itemName      = null;
    let characterName = null;
    let date          = null;
    // let time          = null;
    let offspec       = 0;
    let publicNote    = "";
    let officerNote   = "";

    let note          = null;
    let response      = null;
    let votes         = null;

    if (item['player']) { // RCLC name for character, formatted like so: "Playername-Servername"
        characterName = item['player'].split("-")[0].trim(); // Split the character name and server name up, get just the character name.
    } else if (item['character']) {
        characterName = item['character'].trim();
    }

    if (item['id']) { // RCLC value
        // RCLC id is only a unix timestamp to the second.
        // To make it really unique, append the character's name.
        importId = (item['id'] + "-" + characterName).substring(0, 20);

        // Check if the ID is a RCLC ID that contains the unix time of the transaction.
        // If it is, use that for the date instead.
        let possibleUnixTimestamp = item['id'].split('-').map(item => item.trim());

        if (possibleUnixTimestamp.length > 0) {
            possibleUnixTimestamp = moment(Number(possibleUnixTimestamp[0] + '000')); // Add milliseconds so that moment() will accept it.

            if (possibleUnixTimestamp.isValid()) {
                date = possibleUnixTimestamp.format('YYYY-MM-DD');
            }
        }
    }

    if (item['itemID']) { // RCLC value
        itemId = item['itemID'].trim();
    } else if (item['item_id']) {
        itemId = item['item_id'].trim();
    }

    // Convert item ID to the ID that we use, because having two of the same item is stupid.
    if (itemId && itemId == 18423) { // 18423 = Head of Onyxia, Alliance version
        itemId = 18422; // Head of Onyxia, Horde version
    }

    // Convert item ID to the ID that we use
    if (itemId && itemId == 19003) { // 19003 = Head of Nefarian, Alliance version
        itemId = 19002; // Head of Nefarian, Horde version
    }

    // Convert item ID to the ID that we use
    if (itemId && itemId == 32386) { // 32385 = Magtheridon's Head, Alliance version
        itemId = 32385; // Head of Nefarian, Horde version
    }

    if (item['item']) { // RCLC value
        itemName = item['item'].trim();
    } else if (item['itemName']) {
        itemName = item['itemName'].trim();
    } else if (item['item_name']) {
        itemName = item['item_name'].trim();
    }

    if (item['date'] && !date) { // If RCLC value; can instead default to the date in the ID
        rclcDate = moment(item['date'].trim(), 'DD/MM/YY');
        if (rclcDate.isValid()) {
            // Accept insane DD/MM/YY format
            date = rclcDate.format('YYYY-MM-DD');
        } else {
            // Accept NORMAL SANE YYYY-MM-DD FORMAT
            date = moment(item['date'].trim()).format('YYYY-MM-DD');
        }
    } else if (item['dateTime']) {
        date = moment(item['dateTime'].trim()).format('YYYY-MM-DD');
    } else if (item['date_time']) {
        date = moment(item['date_time'].trim()).format('YYYY-MM-DD');
    }

    if (item['publicNote']) {
        publicNote = item['publicNote'].trim();
    } else if (item['public_note']) {
        publicNote = item['public_note'].trim();
    }

    publicNote = publicNote.substr(0, 140);

    if (item['officerNote']) {
        officerNote = item['officerNote'].trim();
    } else if (item['officer_note']) {
        officerNote = item['officer_note'].trim();
    }
    if (item['note']) { // RCLC value
        note = item['note'].trim();
    }
    if (item['response']) { // RCLC value
        response = item['response'].trim();
    }
    if (item['votes']) { // RCLC value
        votes = item['votes'].trim();
    }

    officerNote = (votes ? "Votes: " + votes + " " : "") + (officerNote ? officerNote + " " : "") + (response ? '"' + response + '" ' : "") + (note ? '"' + note + '" ' : "");
    officerNote = officerNote.substr(0, 140);

    let offspecFlags = ['os', 'offspec'];

    if ((item['offspec'] && item['offspec'] == 1)) {
        offspec = 1;
    } else if (item['offspec'] == undefined || (item['offspec'] !== 0 && item['offspec'].toLowerCase() !== 'false' && item['offspec'].toLowerCase() !== 'no')) {
        if (item['note'] && offspecFlags.includes(item['note'].toLowerCase())) {
            offspec = 1;
        } else if (item['response'] && offspecFlags.includes(item['response'].toLowerCase())) {
            offspec = 1;
        } else if (item['publicNote'] && offspecFlags.includes(item['publicNote'].toLowerCase())) {
            offspec = 1;
        } else if (item['officerNote'] && offspecFlags.includes(item['officerNote'].toLowerCase())) {
            offspec = 1;
        } else if (item['officer_note'] && offspecFlags.includes(item['officer_note'].toLowerCase())) {
            offspec = 1;
        }
    }

    if (itemId) {
        // Input the item
        let itemInput = $(".js-item-autocomplete[data-id=" + i + "]").change();
        addTag(itemInput, itemId, itemName);
    }

    if (characterName) {
        // Select the character, 'i' should make it case INsensitve
        let characterSelect = $("[name=item\\[" + i + "\\]\\[character_id\\]]");
        let characterOption = characterSelect.find('option[data-name="' + characterName + '"i]');

        if (characterOption.val()) {
            characterOption.prop("selected", true).change();
            characterSelect.parent().removeClass("form-danger");
        } else if (missingCharacters.indexOf(characterName) == -1) {
            console.log("Character not found for row " + (i + 1) + ": " + characterName);
            missingCharacterCount++;
            missingCharacters.push(characterName);

            characterSelect.parent().addClass("form-danger");
        } else {
            characterSelect.parent().addClass("form-danger");
        }
    }

    if (offspec == 1) {
        // Check the checkbox
        $("[name=item\\[" + i + "\\]\\[is_offspec\\]]").prop("checked", true).change();

        console.log(`Flagged row ${ (i + 1) } as offspec ${ itemName ? itemName : (itemId ? itemId : '') }${ characterName ? ' on ' + characterName : '' }`);
        offspecCount++;
    }

    if (publicNote) {
        $("[name=item\\[" + i + "\\]\\[note\\]]").val(publicNote).change();
    }

    if (officerNote) {
        $("[name=item\\[" + i + "\\]\\[officer_note\\]]").val(officerNote).change();
    }

    if (date) {
        $("[name=item\\[" + i + "\\]\\[received_at\\]]").val(date).change();
    }

    if (preventDuplicates && importId) {
        $("[name=item\\[" + i + "\\]\\[import_id\\]]").val(importId).change();
    }

    return item;
}

// Prepares the form for a data import.
// Clears out all old values.
function prepareForm() {
    // Set these to the values we want then trigger their change event.
    $("[name=toggle_notes]").prop("checked", true).change();
    $("[name=toggle_dates]").prop("checked", true).change();
    $("[name=date_default]").val("").change();
    $("[name=raid_group_filter]").val("").change();

    if (preventDuplicates) {
        // $(".js-import-id").show();
    }

    // Reset all native form elements
    // $("#itemForm")[0].reset();

    // Reset fields to empty...
    $("input[name^=item][name$=\\[note\\]]").val("");
    $("input[name^=item][name$=\\[officer_note\\]]").val("");
    $("input[name^=item][name$=\\[import_id\\]]").val("");
    $("select[name^=item][name$=\\[character_id\\]] option").prop("selected", false);
    $("input[name^=item][name$=\\[is_offspec\\]]").prop("checked", false);
    $(".js-item-autocomplete").val("");

    // Clear hidden item checkbox values...
    $("input[name^=item][name$=\\[id\\]]").val("");
    $("input[name^=item][name$=\\[label\\]]").val("");
    // Remove fancy links for items...
    $(".js-input-label").empty();
    // Now hide old items...
    $(".input-item").hide();
    // And show their old inputs...
    $(".js-item-autocomplete").show();

    // Refresh our fancypants select inputs.
    $(".selectpicker").selectpicker("refresh");
}

function disableForm() {
    $("#loading-indicator").show();
    $("#submitImport").prop('disabled', true);
    $("#itemForm fieldset").prop("disabled", true);
    $(".js-toggle-import").prop("disabled", true);
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
