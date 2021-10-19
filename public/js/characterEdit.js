$(document).ready(function() {
    var firstLoad = true;


    // When a class is selected, enable+show relevant specs and disabled+hide irrelevant specs
    $(".js-class").change(function () {
        const index = $(this).data('index');
        const className = $(this).val();
        if (className) {
            const oldClassName = $(`.js-spec[data-index=${index}] option:selected`).data("class");

            // Class name changed; reset spec to unselected
            if (!firstLoad && oldClassName != className) {
                $(`.js-spec[data-index=${index}]`).val("");
            }

            $(`.js-spec[data-index=${index}]`).prop("disabled", false);
            $(`.js-spec[data-index=${index}] option`).prop("disabled", false).show();
            $(`.js-spec[data-index=${index}] option`).not("[value='']").not("[data-class='" + className + "']").prop("disabled", true).hide();

            $(`.js-spec-label[data-index=${index}]`).prop("disabled", false);
        } else {
            $(`.js-spec[data-index=${index}]`).val("");
            $(`.js-spec[data-index=${index}]`).prop("disabled", true);
            $(`.js-spec-label[data-index=${index}]`).val("");
            $(`.js-spec-label[data-index=${index}]`).prop("disabled", true);
        }
    }).change();

    // Ensure current archetype makes sense for current selection
    $(`.js-spec`).change(function () {
        const index = $(this).data("index");
        const oldArchetype = $(`.js-archetype[data-index=${index}]`).val();
        const newArchetype = $(this).find(":selected").data('archetype');

        if (!firstLoad && newArchetype && oldArchetype != newArchetype) {
            $(`.js-archetype[data-index=${index}]`).val(newArchetype);
            flashElement($(`.js-archetype[data-index=${index}]`));
        }
    }).change();

    firstLoad = false;
});
