$(document).ready(function () {
    warnBeforeLeaving("#characterForm");
    $("#selectedCharacters" ).sortable({
      connectWith: "#availableCharacters",
      handle: ".js-sort-handle",
      // Sort both lists once something has been dragged
      stop: function(event, ui) {
        sort("#availableCharacters");
        sort("#selectedCharacters");
      },
    });
    $("#availableCharacters" ).sortable({
      connectWith: "#selectedCharacters",
      handle: ".js-sort-handle",
      // Sort both lists once something has been dragged
      stop: function(event, ui) {
        sort("#availableCharacters");
        sort("#selectedCharacters");
      },
    });
});

// Sort a list by its items' data-val attribute
function sort(id) {
    var sortableList = $(id);
    var listItems = $("li", sortableList);
    listItems.sort(function (a, b) {
        return ($(a).data("val") > $(b).data("val"))  ? 1 : -1;
    });
    sortableList.append(listItems);
    // Trigger a change event
    $(id).change();
}
