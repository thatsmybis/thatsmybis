// For keeping track of the intervals updating times
var timestampUpdateInterval = null;

$(document).ready(function () {
    // Format any markdown fields
    parseMarkdown();

    // Watch any watchable times on the page
    trackTimestamps();
});

/**
 * Tracks any timestamps on the page and prints how long since/until each timestamp's date.
 *
 * @param rate How frequently the timestamps should be updated.
 */
function trackTimestamps(rate = 15000) {
    $(".js-watchable-timestamp").each(function () {
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
            since = moment(timestamp).fromNow(true);
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
        let format    = ($(this).data("format") ? $(this).data("format") : "dddd, MMMM Do YYYY, h:mm a");
        let since     = moment(timestamp).format(format);
        if ($(this).is("abbr")) {
            $(this).prop("title", since);
        } else {
            $(this).html(since);
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
                since = moment(timestamp).fromNow(true);
            }

            if ($(this).is("abbr")) {
                $(this).prop("title", (future ? "in " : "") + since + (!future ? " ago" : ""));
            } else {
                $(this).html(since);
            }
        });
    }, rate);
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
        element.html(marked(element.html(), {renderer: render}));
        element.addClass("js-markdown-parsed"); // To avoid going over the same element twice
    } else {
        $(".js-markdown").each(function () {
            if (!$(this).hasClass("js-markdown-parsed")) {
                $(this).html(marked($.trim($(this).text()), {renderer: render}));
                $(this).addClass("js-markdown-parsed");
            }
        });
    }
}
