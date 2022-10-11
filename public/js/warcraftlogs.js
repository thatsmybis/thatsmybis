const WARCRAFTLOGS_MODE_NEW = 'getNew';
const WARCRAFTLOGS_MODE_EXISTING = 'getExisting';

/**
 * @param function callback A function to execute on each character retrieved from Warcraft Logs.
 * @param string mode ['getNew', 'getExisting'] Whether we want to get characters that already exist in the guild, or ones that do not.
 */
function getWarcraftlogsRankedCharacters(callback, mode) {
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

    $(".js-warcraftlogs-attendees-loading-spinner").show();
    $("#warcraftlogsLoadingbar").addClass("d-flex").show();

    let success = false;

    // Request characters
    $.ajax({
        method: "get",
        data: {
            codes: validCodes,
            guild_id: guild.id
        },
        dataType: "json",
        url: "/api/warcraftlogs/attendees",
        success: function (data) {
            $("#warcraftlogsLoadingbar").removeClass("d-flex").hide();
            if (data.length <= 0) {
                $(".js-warcraftlogs-attendees-message").html('No attendance data found').show();
                setTimeout(() => $(".js-warcraftlogs-attendees-message").hide(), 7500);
            } else {
                let reportCharacters = [];

                // Report on the data we got back, and compile a list of the characters
                let message = `Received the following from Warcraft Logs:`;
                for (const [key, report] of Object.entries(data)) {
                    if (report) {
                        // This log is INTENTIONAL; do not remove
                        console.log(`Report ${ report.code }:`, report);

                        if (report.rankedCharacters) {
                            for (const [key, rankedCharacter] of Object.entries(report.rankedCharacters)) {
                                // Duplicate prevention
                                if (!reportCharacters.find(reportCharacter => reportCharacter[0] === rankedCharacter.name)) {
                                    // Doing it this way so that I can sort it later...
                                    // If it were a pure object, I wouldn't be able to sort it.
                                    reportCharacters.push([(rankedCharacter.name ? rankedCharacter.name : 'unknown'), rankedCharacter]);
                                }
                            }
                        }
                        message += `<ul class="mt-3">
                            <li class="font-weight-bold">
                                ${ report.title }
                            </li>
                            <li>
                                ${ report.rankedCharacters ? `${ report.rankedCharacters.length } characters` : `0 characters` }
                            </li>
                            ${ report.endTime ? `<li class="small text-muted font-weight-normal">${ moment.utc(report.endTime).local().format("ddd, MMM Do YYYY @ h:mm a") }</li>` : '' }
                            ${ report.zone && report.zone.name ? `<li class="small text-muted font-weight-normal">${ report.zone.name }</li>` : `` }
                            <li class="small text-muted font-weight-normal">
                                ID ${ report.code }
                            </li>
                        </ul>`;
                    } else {
                        console.log(`No report found`);
                        message += `<ul class="mt-3">
                            <li class="font-weight-bold text-danger">
                                No report found. Did you connect your Warcraftlogs account in guild settings?
                            </li>
                        </ul>`;
                    }
                }

                printWarcraftlogsRankedCharacters(reportCharacters, callback, mode, message);
            }
        },
        error: function (data) {
            console.log('error');
        }
    });
}

// Pass in a Warcraftlogs Character, get back a pretty format for their name
function getWarcraftlogsCharacterHtml(character) {
    return `<span class="text-${ character.classID && WARCRAFTLOGS_CLASSES[character.classID] ? WARCRAFTLOGS_CLASSES[character.classID].slug : '' }" title="${ character.classID && WARCRAFTLOGS_CLASSES[character.classID] ? WARCRAFTLOGS_CLASSES[character.classID].name : '' }">${ character.name }</span>`;
}

/**
 *  Prints the list of rankedCharacters in a Warcraft Logs report...
 *  Or a list of characters made to emulate it.
 *
 *  Relies on a `characters` array with all existing characters for the guild in order to provide duplicate checks.
 *
 * @param reportCharacters array[object] eg. [['xyz', {name: 'xyz', classID: 9}], ['abc', [{name: 'abc', classID: 10}]]
 * @param function callback A function to execute on each character retrieved from Warcraft Logs.
 * @param string mode ['getNew', 'getExisting'] Whether we want to get characters that already exist in the guild, or ones that do not.
 * @param string message An optional prepend to the message that gets printed.
 */
function printWarcraftlogsRankedCharacters(reportCharacters, callback, mode, message = '') {
    let successfulCharactersHtml = [];
    let unsuccessfulCharactersHtml = [];
    let addedCount = 0;
    let alreadyAddedCount = 0;

    // Sort by name
    reportCharacters.sort(function (a, b) { return a[0] > b[0]; });

    // Add the characters to the raid
    reportCharacters.forEach(function (reportCharacter) {
        // 0 is name, 1 is the actual object we got from WCL
        reportCharacter = reportCharacter[1];
        let character = characters.find(character => character.name.toLowerCase() === reportCharacter.name.toLowerCase());
        if (mode === 'getExisting') {
            if (character) {
                if (callback(character)) {
                    addedCount++;
                    successfulCharactersHtml = [...successfulCharactersHtml, getWarcraftlogsCharacterHtml(reportCharacter)];
                }
            } else {
                unsuccessfulCharactersHtml = [...unsuccessfulCharactersHtml, getWarcraftlogsCharacterHtml(reportCharacter)];
            }
        } else if (mode === 'getNew') {
            if (character) {
                unsuccessfulCharactersHtml = [...unsuccessfulCharactersHtml, getWarcraftlogsCharacterHtml(reportCharacter)];
            } else {
                if (callback(reportCharacter)) {
                    addedCount++;
                    successfulCharactersHtml = [...successfulCharactersHtml, getWarcraftlogsCharacterHtml(reportCharacter)];
                }
            }
        }
        i++;
    });

    // Report on who we added and who we couldn't add
    message += `<ul>
        <li>${ addedCount } added</li>
        ${ successfulCharactersHtml.length ? `<li class="text-white"><span class="font-weight-bold">Successful:</span> ${ successfulCharactersHtml.join(', ')}</li>` : `` }
        ${ unsuccessfulCharactersHtml.length
            ? `<li class="text-white">
                    <span class="font-weight-bold">${ mode === 'getExisting' ? 'Not found:' : 'Already exists:' }</span> ${ unsuccessfulCharactersHtml.join(', ') }
                </li>
                ${ mode === 'getExisting' ? `<li class="text-white">To add these characters, add them to the guild and reload this page</li>` : `` }`
            : `` }
    </ul>`;

    $(".js-warcraftlogs-attendees-message").html(message).show();
    addTooltips();
}
