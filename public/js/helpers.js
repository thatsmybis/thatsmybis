const ARCHETYPE_DPS  = 'DPS';
const ARCHETYPE_HEAL = 'Heal';
const ARCHETYPE_TANK = 'Tank';

const SUB_ARCHETYPE_DPS_CASTER   = 'Caster DPS';
const SUB_ARCHETYPE_DPS_PHYSICAL = 'Physical DPS';

const CLASS_DEATH_KNIGHT = 'Death Knight';
const CLASS_DRUID        = 'Druid';
const CLASS_HUNTER       = 'Hunter';
const CLASS_MAGE         = 'Mage';
const CLASS_PALADIN      = 'Paladin';
const CLASS_PRIEST       = 'Priest';
const CLASS_ROGUE        = 'Rogue';
const CLASS_SHAMAN       = 'Shaman';
const CLASS_WARLOCK      = 'Warlock';
const CLASS_WARRIOR      = 'Warrior';

const SPEC_DEATH_KNIGHT_BLOOD  = 'Blood';
const SPEC_DEATH_KNIGHT_FROST  = 'Frost (DK)';
const SPEC_DEATH_KNIGHT_UNHOLY = 'Unholy';
const SPEC_DRUID_BALANCE       = 'Balance';
const SPEC_DRUID_FERAL         = 'Feral';
const SPEC_DRUID_RESTO         = 'Resto (Druid)';
const SPEC_HUNTER_BEAST        = 'Beast';
const SPEC_HUNTER_MARKSMAN     = 'Marksman';
const SPEC_HUNTER_CUNNING      = 'Cunning';
const SPEC_HUNTER_FEROCITY     = 'Ferocity';
const SPEC_HUNTER_TENACTIY     = 'Tenacity';
const SPEC_HUNTER_SURVIVAL     = 'Survival';
const SPEC_MAGE_ARCANE         = 'Arcane';
const SPEC_MAGE_FIRE           = 'Fire';
const SPEC_MAGE_FROST          = 'Frost';
const SPEC_PALADIN_COMBAT      = 'Retribution';
const SPEC_PALADIN_HOLY        = 'Holy (Pally)';
const SPEC_PALADIN_PROTECTION  = 'Prot (Pally)';
const SPEC_PRIEST_DISCIPLINE   = 'Discipline';
const SPEC_PRIEST_HOLY         = 'Holy (Priest)';
const SPEC_PRIEST_SHADOW       = 'Shadow';
const SPEC_ROGUE_ASSASSIN      = 'Assassination';
const SPEC_ROGUE_COMBAT        = 'Combat';
const SPEC_ROGUE_SUBTLETY      = 'Subtlety';
const SPEC_SHAMAN_ELEMENTAL    = 'Elemental';
const SPEC_SHAMAN_ENHANCE      = 'Enhancement';
const SPEC_SHAMAN_RESTO        = 'Resto (Shammy)';
const SPEC_WARLOCK_AFFLICTION  = 'Affliction';
const SPEC_WARLOCK_DESTRO      = 'Destruction';
const SPEC_WARLOCK_DEMON       = 'Demonology';
const SPEC_WARRIOR_ARMS        = 'Arms';
const SPEC_WARRIOR_FURY        = 'Fury';
const SPEC_WARRIOR_PROT        = 'Prot (War)';

// For knowing what kind of tier style to display
const TIER_MODE_NUM = 'num';
const TIER_MODE_S   = 's';

const TIERS = {
    1: 'S',
    2: 'A',
    3: 'B',
    4: 'C',
    5: 'D',
    6: 'F',
};

const SLOT_MISC      = 0; // ammo, mount, book, etc
const SLOT_HEAD      = 1; // head
const SLOT_NECK      = 2; // neck
const SLOT_SHOULDERS = 3; // shoulder
const SLOT_SHIRT     = 4; // shirt
const SLOT_CHEST_1   = 5; // chest
const SLOT_WAIST     = 6; // waist
const SLOT_LEGS      = 7; // legs
const SLOT_FEET      = 8; // feet
const SLOT_WRIST     = 9; // wrist
const SLOT_HANDS     = 10; // hand
const SLOT_FINGER    = 11; // finger
const SLOT_TRINKET   = 12; // trinket
const SLOT_WEAPON_MAIN_HAND = 13; // weapon, 1 hander
const SLOT_SHIELD           = 14; // shield
const SLOT_RANGED_1         = 15; // bow
const SLOT_BACK             = 16; // cloak
const SLOT_WEAPON_TWO_HAND  = 17; // 2h weapon
const SLOT_BAG              = 18; // bag, quiver/ammo pouch
// 19; //// nothing (after my filters, I found nothing in here)
const SLOT_CHEST_2          = 20; // cloth chest
const SLOT_WEAPON_ONE_HAND  = 21; // more 1h weapons
const SLOT_WEAPON_OFF_HAND  = 22; // offhand 1h weapon
const SLOT_OFFHAND          = 23; // offhand non-weapon
const SLOT_AMMO             = 24; // ammo
const SLOT_THROWN           = 25; // thrown
const SLOT_RANGED_2         = 26; // crossbow, gun, wand
// 27; //// nothing (after my filters, I found nothing in here)
const SLOT_RELIC            = 28; // totem/idol/libram

const WARCRAFTLOGS_CLASSES = {
    1: {"id": 1, "name": "Death Knight", "slug": "dk", "specs": {1: {"id": 1, "name": "Blood"}, 2: {"id": 2, "name": "Frost"}, 3: {"id": 3, "name": "Unholy"}}},
    2: {"id": 2, "name": "Druid", "slug": "druid", "specs": {1: {"id": 1, "name": "Balance"}, 2: {"id": 2, "name": "Feral"}, 3: {"id": 3, "name": "Guardian"}, 4: {"id": 4, "name": "Restoration"}}},
    3: {"id": 3, "name": "Hunter", "slug": "hunter", "specs": {1: {"id": 1, "name": "Beast Mastery"}, 2: {"id": 2, "name": "Marksmanship"}, 3: {"id": 3, "name": "Survival"}}},
    4: {"id": 4, "name": "Mage", "slug": "mage", "specs": {1: {"id": 1, "name": "Arcane"}, 2: {"id": 2, "name": "Fire"}, 3: {"id": 3, "name": "Frost"}}},
    5: {"id": 5, "name": "Monk", "slug": "monk", "specs": {1: {"id": 1, "name": "Brewmaster"}, 2: {"id": 2, "name": "Mistweaver"}, 3: {"id": 3, "name": "Windwalker"}}},
    6: {"id": 6, "name": "Paladin", "slug": "paladin", "specs": {1: {"id": 1, "name": "Holy"}, 2: {"id": 2, "name": "Protection"}, 3: {"id": 3, "name": "Retribution"}}},
    7: {"id": 7, "name": "Priest", "slug": "priest", "specs": {1: {"id": 1, "name": "Discipline"}, 2: {"id": 2, "name": "Holy"}, 3: {"id": 3, "name": "Shadow"}}},
    8: {"id": 8, "name": "Rogue", "slug": "rogue", "specs": {1: {"id": 1, "name": "Assassination"}, 2: {"id": 2, "name": "Combat"}, 3: {"id": 3, "name": "Subtlety"}, 4: {"id": 4, "name": "Outlaw"}}},
    9: {"id": 9, "name": "Shaman", "slug": "shaman", "specs": {1: {"id": 1, "name": "Elemental"}, 2: {"id": 2, "name": "Enhancement"}, 3: {"id": 3, "name": "Restoration"}}},
    10: {"id": 10, "name": "Warlock", "slug": "warlock", "specs": {1: {"id": 1, "name": "Affliction"}, 2: {"id": 2, "name": "Demonology"}, 3: {"id": 3, "name": "Destruction"}}},
    11: {"id": 11, "name": "Warrior", "slug": "warrior", "specs": {1: {"id": 1, "name": "Arms"}, 2: {"id": 2, "name": "Fury"}, 3: {"id": 3, "name": "Protection"}, 4: {"id": 4, "name": "Gladiator"}}},
    12: {"id": 12, "name": "Demon Hunter", "slug": "dh", "specs": {1: {"id": 1, "name": "Havoc"}, 2: {"id": 2, "name": "Vengeance"}}}
};

// How often to update timestamps
var timestampCheckRate = 30000;

// For keeping track of the intervals updating times
var timestampUpdateInterval = null;


// Add CSRF token to all request headers
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// For config options: https://marked.js.org/#/USING_ADVANCED.md#options
marked.setOptions({
    gfm: true,
    breaks: true
});

// Apply custom configurations to moment.js
configureMoment();

$(document).ready(function () {
    // Add support for better nav dropdowns
    addNestedDropdownSupport();

    // Format any markdown fields
    parseMarkdown();

    // Fix wowhead links that were generated by markdown
    makeWowheadLinks();

    // Watch any watchable times on the page
    trackTimestamps();

    // For local to UTC time conversions before the date is sent to the server
    addDateInputHandlers();

    // Don't submit forms when the user presses enter in a textbox
    addInputAntiSubmitHandler();

    // For toggling hidden note inputs
    addNoteHandlers();

    // For enabling wishlist sorting
    addWishlistSortHandlers();

    $(".js-edit-content").click(function (e) {
        e.preventDefault();
        let id = $(this).data("id");
        $(".js-content[data-id=" + id + "]").toggle();
    });

    // Add mobile friendly tooltips
    addTooltips();
});

// Take the visible date input, and convert its time to UTC, update the hidden date input.
function addDateInputHandlers() {
    $(".js-date-input").change(function () {
        let actualInput = $(this).prev(".js-date");
        if ($(this).val()) {
            actualInput.val(moment($(this).val()).utc().format("YYYY-MM-DD HH:mm:ss"));
        } else {
            actualInput.val(date);
            $(this).val(moment.utc(date).local().format("YYYY-MM-DD HH:mm:ss"));
        }
    });
}

/**
 * Prevents inputs from submitting their form when enter is pressed.
 */
function addInputAntiSubmitHandler(selector = ":text") {
    $(selector).on("keypress keyup", function (e) {
        if (e.which == 13) {
            return false;
        }
    });
}

// Copied from https://stackoverflow.com/a/61222302/1196517
function addNestedDropdownSupport() {
    $.fn.dropdown = (function() {
        var $bsDropdown = $.fn.dropdown;
        return function(config) {
            if (typeof config === 'string' && config === 'toggle') { // dropdown toggle trigged
                $('.has-child-dropdown-show').removeClass('has-child-dropdown-show');
                $(this).closest('.dropdown').parents('.dropdown').addClass('has-child-dropdown-show');
            }
            var ret = $bsDropdown.call($(this), config);
            $(this).off('click.bs.dropdown'); // Turn off dropdown.js click event, it will call 'this.toggle()' internal
            return ret;
        }
    })();

    $(function() {
        $('.dropdown [data-toggle="dropdown"]').on('click', function(e) {
            $(this).dropdown('toggle');
            e.stopPropagation();
        });
        $('.dropdown').on('hide.bs.dropdown', function(e) {
            if ($(this).is('.has-child-dropdown-show')) {
                $(this).removeClass('has-child-dropdown-show');
                e.preventDefault();
            }
            e.stopPropagation();
        });
    });
}

// Add basic handlers to show edit forms for notes
function addNoteHandlers() {
    $(".js-show-note-edit").click(function () {
        $(".js-note-input").toggle();
    });
}

// For enabling sortable elements
function addSortHandlers() {
    $(".js-sortable").sortable({handle: ".js-sort-handle"});
    // sortable() is slow to initialize when applied to hundreds of elements, so this solves for that scenario
    $(".js-sortable-lazy").one("mouseenter", function() {
        $(this).sortable({handle: ".js-sort-handle"});
    });
}

// Add mobile friendly tooltips
function addTooltips() {
    $("span").tooltip();
    $("abbr").tooltip();
    $("a").tooltip();
}

// Add basic handlers to change the sorting of wishlists
function addWishlistSortHandlers() {
    $(".js-sort-wishlists").click(function () {
        $(".js-wishlist-unsorted").toggle();
        $(".js-wishlist-sorted").toggle();
    });
}

// Apply any desired custom configurations to the moment.js library
function configureMoment() {
    // Change moment.js thresholds so that it doesn't round seconds, minutes, hours, days, or months.
    // See: https://momentjs.com/docs/#/customization/relative-time-threshold/
    moment.relativeTimeThreshold('ss', 40); // Show seconds if >= # seconds; otherwise 'now'
    moment.relativeTimeThreshold('s', 60); // Show minutes if >= ## seconds
    moment.relativeTimeThreshold('m', 60); // Show hours if >= ## minutes
    moment.relativeTimeThreshold('h', 49); // Show days if >= ## hours
    moment.relativeTimeThreshold('d', 93); // Show months if >= ## days
    moment.relativeTimeThreshold('M', 25); // Show years if > ## months

    // Round relative time evaluation down
    // See: http://momentjs.com/docs/#/customization/relative-time-rounding/
    moment.relativeTimeRounding(Math.floor);
}

function cleanUrl(sanitize, base, href) {
    if (sanitize) {
        try {
            var prot = decodeURIComponent(unescape(href))
            .replace(/[^\w:]/g, '')
            .toLowerCase();
        } catch (e) {
            return null;
        }
        if (prot.indexOf('javascript:') === 0 || prot.indexOf('vbscript:') === 0 || prot.indexOf('data:') === 0) {
            return null;
        }
    }
    if (base && !originIndependentUrl.test(href)) {
        href = resolveUrl(base, href);
    }
    try {
        href = encodeURI(href).replace(/%25/g, '%');
    } catch (e) {
        return null;
    }
    return href;
}

function decToHex(number) {
    return parseInt(number).toString(16);
}

// Delete a cookie from the browser document
function deleteCookie (name, path) {
    setCookie(name, '', -1, path);
}

// Pass a string of HTML, get the inner contents out of it.
function extractHtmlContent(htmlString) {
    var span = document.createElement('span');
    span.innerHTML = htmlString;
    return span.textContent || span.innerText;
}

// Take a jquery element, make that element visually flash to get the user's attention
function flashElement(element) {
    element.fadeTo(100, 0.3, function() { $(this).fadeTo(500, 1.0); });
}

function getArchetypeIcon(archetype) {
    if (archetype === ARCHETYPE_DPS) {
        return 'fas fa-fw fa-bow-arrow text-dps';
    } else if (archetype === ARCHETYPE_HEAL) {
        return 'fas fa-fw fa-plus-circle text-healer';
    } else if (archetype === ARCHETYPE_TANK) {
        return 'fas fa-fw fa-shield text-tank';
    } else {
        return 'fas fa-fw fa-map-marker-question text-muted';
    }
}

// Based on attendance percentage, return a CSS color class
function getAttendanceColor(percentage = 0) {
    let color = '';

    if (percentage >= 0.95) {
        color = 'text-tier-1';
    } else if (percentage >= 0.9) {
        color = 'text-tier-2';
    } else if (percentage >= 0.8) {
        color = 'text-tier-3';
    } else if (percentage >= 0.7) {
        color = 'text-tier-4';
    } else {
        color = 'text-tier-5';
    }

    return color;
}

/**
 * Pass a number, get back a hex color complete with leading hash to make it HTML friendly
 */
function getColorFromDec(color) {
    color = parseInt(color);
    if (color) {
        color = decToHex(color);
        // If it's too short, keep adding prefixed zero's until it's long enough
        while (color.length < 6) {
            color = '0' + color;
        }
    } else {
        color = 'FFF';
    }
    return '#' + color;
}

// Get a cookie from the browser document
function getCookie (name) {
    return document.cookie.split('; ').reduce((r, v) => {
        const parts = v.split('=')
        return parts[0] === name ? decodeURIComponent(parts[1]) : r
    }, '');
}

// Based on the guild's tier mode setting, return a tier label
function getItemTierLabel(item, tierMode) {
    if (item.guild_tier) {
        if (tierMode == TIER_MODE_S) {
            return numToSTier(item.guild_tier);
        } else {
            return item.guild_tier;
        }
    } else {
        return '';
    }
}

// Updates any wowhead links to have a tooltip, plus other modifications.
// The configuration for this is defined in the HTML header (app.blade.php)
function makeWowheadLinks() {
    // Sometimes the error "WH.getDataEnv is not a function" appears
    // This *seems* to be due to trying to refresh the links before they've finished their inital
    // setup, but I'm not sure.
    // If the error goes through (and I don't see anything we can do to fix/handle it), it breaks the
    // javascript on the rest of our page. try/catch is a cheap fix.
    try {
        $WowheadPower.refreshLinks();
    } catch (error) {
        console.log("Failed to refresh wowhead links.");
    }
}

/**
 * Convert newlines to <br> tags. Given the odd name because that's the name PHP uses.
 *
 * @param string string The string to convert.
 *
 * @return string
 */
function nl2br(string) {
    return string ? string.replace(/\n/g,"<br>") : '';
}

/**
 * Expects a float, returns an s-tier with the decimal of the float intact.
 *
 * @param $float
 *
 * @return array
 */
function numToSTier(float) {
    if (float > 0) {
        tiers = TIERS;

        whole = Math.floor(float);
        decimal = float - whole;

        affix = '';
        if (decimal > 0.66) {
            affix = '++';
        } else if (decimal > 0.33) {
            affix = '+';
        }

        return tiers[Math.ceil(float)] + affix;
    } else {
        return '';
    }
}

/**
 * Parse markdown in the given element from markdown into HTML.
 * If no element is provided, do it for all markdown elements.
 */
function parseMarkdown(element = null) {
    var render = new marked.Renderer();

    // Disable pretty links so that users always know what link they're clicking on.
    // This is to prevent abuse.
    // Autolinks still work: 'https://nubbl.com' -> 'https://nubbl.com'
    // Pretty links format like so: '[title1](https://www.example.com)' -> 'title1 (https://www.example.com)'
    /*
        render.link = function(href, title, text) {
            var textIsDifferent = text ? ((text == href) ? false : true) : false;
            var out = '';

            if (textIsDifferent) {
                out = text + ' (';
            }

            href = cleanUrl(this.options.sanitize, this.options.baseUrl, href);

            if (href === null) {
                return text;
            }

            out += '<a target="_blank" href="' + href + '"';
            if (title) {
                out += ' title="' + title + '"';
            }
            out += '>' + href + '</a>';

            if (textIsDifferent) {
                out += ')';
            }
            return out;
        };
    */

    // Add target=_blank to links
    render.link = function(href, title, text) {
        var out = '';
        href = cleanUrl(this.options.sanitize, this.options.baseUrl, href);

        if (href === null) {
            return text;
        }

        out += '<a target="_blank" href="' + href + '"';
        if (title) {
            out += ' title="' + title + '"';
        }
        out += '>' + text + '</a>';
        return out;
    };

    // Disable embedded images. Instead, display as a link.
    // '![alt text](https://example.com/image.jpg)' -> 'alt text (https://example.com/image.jpg)'
    // '![](https://example.com/image.jpg)' -> 'https://example.com/image.jpg'
    // render.image = function(href, title, text) {
    //     var textIsDifferent = text ? ((text == href) ? false : true) : false;
    //     var out = '';

    //     if (textIsDifferent) {
    //         out = text + ' (';
    //     }

    //     href = cleanUrl(this.options.sanitize, this.options.baseUrl, href);
    //     if (href === null) {
    //         return text;
    //     }

    //     out += '<a target="_blank" href="' + href + '"';
    //     if (title) {
    //         out += ' title="' + title + '"';
    //     }
    //     out += '>' + href + '</a>';

    //     if (textIsDifferent) {
    //         out += ')';
    //     }
    //     return out;
    // };

    if (element && !element.hasClass("js-markdown-parsed")) {
        element.html(marked(DOMPurify.sanitize(element.html()), {renderer: render}));
        element.addClass("js-markdown-parsed"); // To avoid going over the same element twice
    } else {
        $(".js-markdown").each(function () {
            if (!$(this).hasClass("js-markdown-parsed")) {
                const text = DOMPurify.sanitize($.trim($(this).text()));
                $(this).html(marked(text), {renderer: render});
                $(this).addClass("js-markdown-parsed");
            }
        });

        $(".js-markdown-inline").each(function () {
            if (!$(this).hasClass("js-markdown-parsed")) {
                const text = DOMPurify.sanitize($.trim($(this).text()));
                $(this).html(marked.parseInline(text), {renderer: render});
                $(this).addClass("js-markdown-parsed");
            }
        });
    }

    // This isn't markdown but it shows at the same time as a lot of markdown
    // so I am lazily throwing it in here to hijack when parseMarkdown() gets called.
    $(".js-show-text").click(function () {
        const previewText = $(this).siblings(".js-preview-text").hide();
        const fullText = $(this).siblings(".js-full-text").show();
        const showText = $(this).siblings(".js-hide-text").show();
        $(this).hide();
    });
    $(".js-hide-text").click(function () {
        const previewText = $(this).siblings(".js-preview-text").show();
        const fullText = $(this).siblings(".js-full-text").hide();
        const showText = $(this).siblings(".js-show-text").show();
        $(this).hide();
    });
}

// Takes a numeric RGB value and turns it into a hex colour code
function rgbToHex (rgb) {
    let hex = Number(rgb).toString(16);

    // If it's too short, keep adding prefixed zero's till it's long enough
    while (hex.length < 6) {
        hex = "0" + hex;
    }
    return hex;
};

// Set a cookie in the browser document
function setCookie (name, value, days = 7, path = '/') {
    const expires = new Date(Date.now() + days * 864e5).toUTCString();
    document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + expires + '; path=' + path;
}

// Turn a url into a slug url!
function slug(string) {
    let theChosenCharacter = "-"; // You are the Chosen One

    const a = "àáäâãåăæçèéëêǵḧìíïîḿńǹñòóöôœṕŕßśșțùúüûǘẃẍÿź·/_,:;";
    const b = "aaaaaaaaceeeeghiiiimnnnoooooprssstuuuuuwxyz------";
    const p = new RegExp(a.split("").join("|"), "g");

    let slug =  string.toString().toLowerCase()
        .replace(/\s+/g, "-")                    // Replace spaces with -
        .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
        .replace(/&/g, "")                       // Remove ampersands (can optionally change to replace with '-and-)
        .replace(/[^\w\-]+/g, "")                // Remove all non-word characters
        .replace(/\-\-+/g, "-")                  // Replace multiple - with single -
        .replace(/^-+/, "")                      // Trim - from start of text
        .replace(/-+$/, "")                      // Trim - from end of text
        .replace(/-+/g, theChosenCharacter)      // Replace - with The Chosen Character
        .substr(0, 50);                          // Limit length to 50 characters

    if (slug) {
        return slug;
    } else {
        return theChosenCharacter;
    }
}

/**
 * Tracks any timestamps on the page and prints how long since/until each timestamp's date.
 *
 * @param rate How frequently the timestamps should be updated.
 */
function trackTimestamps(rate = timestampCheckRate) {
    $(".js-watchable-timestamp").each(function () {
        let isShort = $(this).data("isShort");

        if (locale) {
            moment.locale(locale);
        }

        // For English short-form dates
        if (isShort && (!locale || locale === 'en')) {
            moment
            .locale('en', {
                relativeTime: {
                    past: '%s ago',
                    s:  'just now',
                    ss: '%ss',
                    m:  '%dm',
                    mm: '%dm',
                    h:  '%dh',
                    hh: '%dh',
                    d:  '%dd',
                    dd: '%dd',
                    M:  '%dmo',
                    MM: '%dmo',
                    y:  '%dy',
                    yy: '%dy'
                }
            });
        }

        let timestamp = $(this).data("timestamp");
        if (timestamp < 1000000000000) { // <-- Potential y33.658k bug [that's a y2k joke]
            timestamp = timestamp * 1000; // convert from seconds to milliseconds
        }
        let future = false;
        if (timestamp > (Date.now())) {
            future = true;
        }

        let since = null;
        let maxDays = $(this).data("maxDays");
        if (maxDays && (timestamp < moment().valueOf() - (maxDays * 86400000))) {
        // > 2 weeks, change the message to that
            since = "over 2 weeks";
        } else {
        // < 2 weeks
            since = moment
                .utc(timestamp)
                .fromNow(true);
        }

        if ($(this).is("abbr")) {
            $(this).prop("title", (future ? "in " : "") + since + (!future ? " ago" : ""));
        } else {
            $(this).html(since);
        }
    });

    $(".js-timestamp").each(function () {
        let timestamp = $(this).data("timestamp");
        if (timestamp < 1000000000000) {
            timestamp = timestamp * 1000;
        }
        let format = ($(this).data("format") ? $(this).data("format") : "ddd, MMM Do YYYY @ h:mm a");
        let since  = moment.utc(timestamp).local().format(format);
        if ($(this).is("abbr")) {
            $(this).prop("title", since);
        } else {
            $(this).html(since);
        }
    });

    $(".js-timestamp-title").each(function () {
        let title = $(this).data("title");

        let timestamp = $(this).data("timestamp");
        if (timestamp < 1000000000000) {
            timestamp = timestamp * 1000;
        }
        let time = moment.utc(timestamp).local().format("ddd, MMM Do YYYY @ h:mm a");
        if (title) {
            $(this).prop("title", (title + ' ' + time));
        } else {
            $(this).prop("title", time);
        }
    });

    timestampUpdateInterval ? clearInterval(timestampUpdateInterval) : null;

    timestampUpdateInterval = setInterval(function () {
        $(".js-watchable-timestamp").each(function () {
            let timestamp = $(this).data("timestamp");
            if (timestamp < 1000000000000) {
                timestamp = timestamp * 1000; // convert from seconds to milliseconds
            }
            let future = false;
            if (timestamp > (Date.now())) {
                future = true;
            }

            let since = null;
            let maxDays = $(this).data("maxDays");
            if (maxDays && (timestamp < moment().valueOf() - (maxDays * 86400000))) {
            // > 2 weeks, change the message to that
                since = "over 2 weeks";
            } else {
            // < 2 weeks
                since = moment.utc(timestamp).fromNow(true);
            }

            if ($(this).is("abbr")) {
                $(this).prop("title", (future ? "in " : "") + since + (!future ? " ago" : ""));
            } else {
                $(this).html(since);
            }
        });
    }, rate);
}

function ucfirst(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

/**
 * Warn a user before leaving the page if the given element has changed.
 */
function warnBeforeLeaving(selector) {
    $(selector).one("change", () => {
        window.onbeforeunload = () => true;
    });
    // Remove the warning if submitting
    $(selector).one("submit", () => {
        window.onbeforeunload = function () {};
    });
}
