var PINNED_ITEMS="thatsmybis.pinnedItems";function makeIconLight(t){t.removeClass("fas"),t.addClass("fal")}function makeIconSolid(t){t.removeClass("fal"),t.addClass("fas")}function sortItems(){var t=[],e=Cookies.get(PINNED_ITEMS);e&&((t=JSON.parse(e))||(t=[])),$(".js-pin-sortable").each(function(){var e=$(this).data("itemId");if(t.find(function(t){return t===e})){var n=-1e6+t.indexOf(e);$(this).attr("data-user-order",n),makeIconSolid($(this).find(".js-pin-item"))}else{var i=$(this).attr("data-original-order");$(this).attr("data-user-order",i),makeIconLight($(this).find(".js-pin-item"))}}),$("#pinnableList").children(".js-pin-sortable").sort(function(t,e){var n,i;return parseInt(t.getAttribute("data-user-order"))>parseInt(e.getAttribute("data-user-order"))?1:-1}).appendTo($("#pinnableList")),setTimeout(function(){$(".tooltip").remove()},100)}function updatePlaceholders(){$("ol.js-sortable-lazy").each(function(){var t=0;$(this).find('input[name$="[order]"]').each(function(){t++,$(this).attr("placeholder",t)})})}$(document).ready(function(){warnBeforeLeaving("#editForm"),$(".js-sortable-lazy").one("mouseenter",function(){$(this).sortable({handle:".js-sort-handle",stop:function stop(t,e){updatePlaceholders()}})}),$(".js-pin-item").click(function(){var t=$(this).data("itemId"),e=Cookies.get(PINNED_ITEMS);if(e){var n=JSON.parse(e);n.find(function(e){return e===t})?(n=n.filter(function(e){return e!==t}),Cookies.set(PINNED_ITEMS,JSON.stringify(n))):(n.push(t),Cookies.set(PINNED_ITEMS,JSON.stringify(n)))}else{var i=[];i.push(t),Cookies.set(PINNED_ITEMS,JSON.stringify(i))}sortItems()}),sortItems(),$(".js-reset-ranks").click(function(){var t=$(this).data("index");$("input.js-rank[data-index='".concat(t,"']")).val("")}),$(".js-reset-all-ranks").click(function(){confirm("This will remove any manually input numbers for prio ranks. You can review this before you submit the page. Continue?")&&$("input.js-rank").val("")}),$(".js-toggle-note").change(function(){var t=$(this).data("index");$(this).prop("checked")?$(".js-note[data-index="+t+"]").parent().show():$(".js-note[data-index="+t+"]").parent().hide()}),$(".loadingBarContainer").removeClass("d-flex").hide(),$("#editForm").show()});
