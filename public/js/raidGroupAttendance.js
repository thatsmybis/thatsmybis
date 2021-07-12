var allLootVisible = false;

$(document).ready(function () {
    $(".js-show-loot").click(function () {
        $(this).hide();
        $(".js-loot[data-raid-id='" + $(this).data('raidId') + "'][data-character-id='" + $(this).data('characterId') + "']").show();
    });

    $(".js-show-all-loot").click(function () {
        if (allLootVisible) {
            $(".js-show-loot").show();
            $(".js-show-character-loot").show();
            $(".js-show-raid-loot").show();
            $(".js-loot").hide();
            allLootVisible = false;
        } else {
            $(".js-show-loot").hide();
            $(".js-show-character-loot").hide();
            $(".js-show-raid-loot").hide();
            $(".js-loot").show();
            allLootVisible = true;
        }
    });

    $(".js-show-character-loot").click(function () {
        $(this).hide();
        $(".js-show-loot[data-character-id='" + $(this).data('characterId') + "']").hide();
        $(".js-loot[data-character-id='" + $(this).data('characterId') + "']").show();
    });

    $(".js-show-raid-loot").click(function () {
        $(this).hide();
        $(".js-show-loot[data-raid-id='" + $(this).data('raidId') + "']").hide();
        $(".js-loot[data-raid-id='" + $(this).data('raidId') + "']").show();
    });
});
