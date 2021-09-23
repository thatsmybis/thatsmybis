$(document).ready(function() {
    var firstLoad = true;

    // When a class is selected, enable+show relevant specs and disabled+hide irrelevant specs
    $("[name=class]").change(function () {
        const className = $(this).val();
        if (className) {
            const oldClassName = $("[name=spec] option:selected").data("class");

            // Class name changed; reset spec to unselected
            if (oldClassName != className) {
                $("[name=spec]").val("");
            }

            $("[name=spec]").prop("disabled", false);
            $("[name=spec] option").prop("disabled", false).show();
            $("[name=spec] option").not("[value='']").not("[data-class='" + className + "']").prop("disabled", true).hide();

            $("[name=spec_label]").prop("disabled", false);
        } else {
            $("[name=spec]").val("");
            $("[name=spec]").prop("disabled", true);
            $("[name=spec_label]").val("");
            $("[name=spec_label]").prop("disabled", true);
        }
    }).change();

    // Ensure current archetype makes sense for current selection
    $("[name=spec]").change(function () {
        const oldArchetype = $("[name=archetype]").val();
        const newArchetype = $(this).find(":selected").data('archetype');

        if (!firstLoad && newArchetype && oldArchetype != newArchetype) {
            $("[name=archetype]").val(newArchetype);
            flashElement($("[name=archetype]"));
        }
    }).change();

    firstLoad = false;
});
