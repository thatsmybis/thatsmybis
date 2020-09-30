$(document).ready(function () {
    $(".js-show-next").change(function() {
        showNext(this);
    });
    $(".js-show-next").keyup(function() {
        showNext(this);
    });

    // If the current element has a value, show it and the next element that is hidden because it is empty
    function showNext(currentElement) {
        if ($(currentElement).val() != "") {
            $(currentElement).show();
            $(currentElement).closest(".row").next(".js-hide-empty").show();
        }
    }

    // Filter out characters based on the raid they are in
    $("#raid_filter").on('change', function () {
        let raidId = $(this).val();

        if (raidId) {
            $(".js-character-option[data-raid-id!='" + raidId + "']").hide();
            $(".js-character-option[data-raid-id='" + raidId + "']").show();
        } else {
            $(".js-character-option").show();
        }
        $(".selectpicker").selectpicker("refresh");
    }).change();

    $("[name=toggle_notes]").on('change', function () {
        if (this.checked) {
            $(".js-note").show();
        } else {
            $(".js-note").hide();
        }
    }).change();

    $("[name=toggle_dates]").on('change', function () {
        if (this.checked) {
            $(".js-date").show();
        } else {
            $(".js-date").hide();
        }
    }).change();
});
