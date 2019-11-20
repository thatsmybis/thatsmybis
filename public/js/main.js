$(document).ready(function () {
    $(".js-edit-content").click(function (e) {
        e.preventDefault();
        let id = $(this).data("id");
        $(".js-content[data-id=" + id + "]").toggle();
    });
});
