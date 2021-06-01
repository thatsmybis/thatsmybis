$(document).ready(function () {
    addItemAutocompleteHandler();
    addItemListSelectHandler();
    addItemRemoveHandler();
    addTagInputHandlers();
});

// Adds autocomplete for items!
function addItemAutocompleteHandler() {
    $(".js-item-autocomplete").each(function () {
        var self = this; // Allows callback functions to access `this`
        $(this).autocomplete({
            source: function (request, response) {
                $.ajax({
                    method: "get",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    url: "/api/items/query/" + expansionId + "/" + request.term,
                    success: function (data) {
                        response(data);
                        if (data.length <= 0) {
                            $(self).nextAll(".js-status-indicator").show();
                            $(self).nextAll(".js-status-indicator").html("<span class=\"bg-danger\">&nbsp;" + request.term + " not found&nbsp;</span>");
                        }
                    },
                    error: function () {}
                });
            },
            search: function () {
                $(this).nextAll(".js-status-indicator").hide();
                $(this).nextAll(".js-status-indicator").empty();
                $(this).nextAll(".js-loading-indicator").show();
            },
            response: function () {
                $(this).nextAll(".js-loading-indicator").hide();
            },
            select: function (event, ui) {
                if (ui.item.value) {
                    // Put the value into a tag below the input
                    value = ui.item.value;
                    label = ui.item.label;

                    // Only allow numbers (an item ID must be found)
                    if (Number.isInteger(value)) {
                        addTag(this, value, label);
                        makeWowheadLinks();
                    }

                    // prevent autocomplete from autofilling this.val()
                    return false;
                }
            },
            minLength: 1,
            delay: 400
        });
    });
}

// Adds the current input to the list below the input
function addItemListSelectHandler() {
    /**
     * Move the selected value to the list under the select.
     * Change the selected value back to the default value.
     **/
    $(".js-input-item").change(function () {
        $(this).find(":selected").val();
        $(this).find(":selected").html().trim();

        value = $(this).find(":selected").val();
        label = $(this).find(":selected").html().trim();
        $nextInput = $(this).next("ul").children("li").children("input[value='']").first();
        if ($nextInput.val() == "") {
        // Add the item.
            $nextInput.parent("li").show();
            $nextInput.val(value);
            // Populate the hidden input's sibling that holds onto the label
            // Useful if submission fails on the server side and the server wants to send the label back
            $nextInput.siblings("input[value='']").first().val(label);
            $nextInput.siblings(".js-input-label").html(" " + label);
            $(this).val("");
            $(this).find("option:first").text("—");
        } else {
        // Can't add any more.
            $(this).val("");
            // If a select input triggered this
            $(this).find("option:first").text("maximum added");
        }
    });

    /**
     * Move the selected value to the list under the select.
     * Change the selected value back to the default value.
     **/
    $(".js-input-select").change(function () { // TODO: Why is this triggering four times? This bug has always been here. Change trigger from change() to the fancy select event.
        const value = $(this).find(":selected").val();
        const label = $(this).find(":selected").html().trim();
        let $nextInput = $(this).parent().next("ol").children("li").children("input[value='']").first(); // TODO: Check only against the input we care about (id/name/whatever)

        // Couldn't find an open input. Check and see if there are any potential inputs that
        // are waiting for a template instead. (template should be defined on the page)
        if ($nextInput.val() != "") {
            $nextInput = $(this).parent().next("ol").children("li[data-needs-template='1']").first();
            $nextInput.html(inputTemplate);

            const prefix = $nextInput.data("input-prefix");

            if (prefix) {
                // The template doesn't have fully populated names for inputs... set the input names.
                $nextInput.find("input").each(function () {
                    $(this).attr("name", prefix + $(this).attr('name'));
                });
            }

            $nextInput.data("needs-template", 0);

            $nextInput = $nextInput.children("input[value='']").first();
        }


        if ($nextInput.val() == "") {
        // Add the item.
            $nextInput.parent("li").show();

            // Populate the ID
            $nextInput.val(value);
            $nextInput.siblings(".js-input-label").html(" " + label);
            // Populate the label
            $label = $nextInput.next("input").first();
            $label.val(label);

            // Reset the select
            $(this).val("");
            $(this).find("option:first").text("—");
        } else {
        // Can't add any more.
            $(this).val("");
            // If a select input triggered this
            $(this).find("option:first").text("maximum added");
        }
    });
}

/**
 * When typing a tag and NOT using autocomplete, handle what happens
 * when the user presses enter, space, or comma.
 *
 * @return void
 */
function addTagInputHandlers() {
    $(".js-item-autocomplete").keyup(function (e) {
        keys = [
            13, // enter
            // 32, // space
            // 188 // comma
        ];

        if ($.inArray(e.keyCode, keys) >= 0) {
            e.preventDefault();

            // Put the value into a tag below the input
            value = this.value ;
            label = value;

            // Only allow numbers (an item ID must be found)
            if (Number.isInteger(value)) {
                addTag(this, value, label);
                makeWowheadLinks();
            }
        }
    });

    // When the element loses focus, submit whatever was in it
    $(".js-item-autocomplete").focusout(function () {
        if ($(this).val()) {
            // Put the value into a tag below the input
            value = this.value;
            label = value;

            // Only allow numbers (an item ID must be found)
            if (Number.isInteger(value)) {
                addTag(this, value, label);
                makeWowheadLinks();
            }
        }
    });
}

function addItemRemoveHandler() {
    /**
     * Remove the chosen tag from the list that appears below the select.
     */
    $(".js-input-button").click(function () {
        $(this).prev("input").val(""); // Clear the pivot input (note: doesn't always exist)
        $(this).prev("input").prev("input").val(""); // Clear the label input
        $(this).prev("input").prev("input").prev("input").val(""); // Clear the value input
        $(this).parent("li").removeClass("d-flex").hide();

        // Remove the select's warning message.
        let select = $(this).parent("li").parent("ul").siblings(".js-input-select");
        select.find("option:first").text("—");
        select.show();

        // Remove the input's warning message, only if it is present.
        let textInput = $(this).parent("li").parent("ul").siblings(".js-input-text");
        if (textInput.val() && textInput.val().match("^maximum") && textInput.val().match("added$")) {
            textInput.val("");
        }
        textInput.show();
    });
}

/**
 * Take the given value and plop it into the next available input, provided it's in a list.
 *
 * @var $this         object The object that you want to add the tag after.
 * @var value         string The tag to add.
 * @var label         string The visible name of the tag to add.
 *
 * @return            bool   True on success.
 */
function addTag($this, value, label) {
    if ($this && value) {
        if (!label) {
            label = "";
        }

        // Find the hidden input
        $nextInput = $($this).next().next("ul").children("li").children("input:first-child[value='']").first();

        if ($nextInput.val() == "") {
            $nextInput.parent("li").show().addClass("d-flex");
            $nextInput.val(value);
            // Populate the hidden input's sibling that holds onto the label
            // Useful if submission fails on the server side and the server wants to send the label back
            $nextInput.siblings("input[value='']").first().val(label);

            let link = "";
            if (guild) {
                let wowheadData = `data-wowhead-link="https://${ wowheadSubdomain }.wowhead.com/item=${ value }"
                        data-wowhead="item=${ value }?domain=${ wowheadSubdomain }"`;

                link = ` <a href="/${ guild.id }/${ guild.slug }/i/${ value }/${ slug(label) }"
                    target="_blank"
                    class="font-weight-medium"
                    ${ wowheadData }>
                    ${ label }
                </a>`;
            } else {
                link = `<a href="https://${ wowheadSubdomain }.wowhead.com/item=${ value }" target="_blank" class="font-weight-medium">${ label }</a>`;
            }

            let inputLabel  = $nextInput.siblings(".js-input-label");
            let itemDisplay = inputLabel.children(".js-item-display");

            if (itemDisplay.length > 0) {
                // Only replace the display
                itemDisplay.html(link);
            } else {
                // Replace the whole thing
                inputLabel.html(link);
            }

            $($this).val("");
            if ($($this).data("isSingleInput")) {
                $($this).hide();
            }

            // Trigger any onChange listeners
            $($this).change();

            return true;
        } else {
            $($this).val("maximum items added");
            return false;
        }
    }
}
