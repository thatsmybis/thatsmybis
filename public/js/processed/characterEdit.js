$(document).ready(function(){var e=!0;$("[name=class]").change(function(){var e=$(this).val(),a;e?($("[name=spec] option:selected").data("class")!=e&&$("[name=spec]").val(""),$("[name=spec]").prop("disabled",!1),$("[name=spec] option").prop("disabled",!1).show(),$("[name=spec] option").not("[value='']").not("[data-class='"+e+"']").prop("disabled",!0).hide(),$("[name=spec_label]").prop("disabled",!1)):($("[name=spec]").val(""),$("[name=spec]").prop("disabled",!0),$("[name=spec_label]").val(""),$("[name=spec_label]").prop("disabled",!0))}).change(),$("[name=spec]").change(function(){var a=$("[name=archetype]").val(),n=$(this).find(":selected").data("archetype");!e&&n&&a!=n&&($("[name=archetype]").val(n),flashElement($("[name=archetype]")))}).change(),e=!1});