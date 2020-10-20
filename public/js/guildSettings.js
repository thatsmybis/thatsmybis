$(".js-show-next").change(function() {
    showNext(this);
}).change();

$(".js-show-next").keyup(function() {
    showNext(this);
});

$("[name=show_message]").change(function () {
    if (this.checked) {
        $("#message").show();
    } else {
        $("#message").hide();
    }
}).change();

// If the current element has a value, show it and the next element that is hidden because it is empty
function showNext(currentElement) {
    if ($(currentElement).val() != "") {
        $(currentElement).show();
        $(currentElement).parent().next(".js-hide-empty").show();
    }
}
