function addCharacter(e){var a=e.id,t=!1,n;if(!findExistingCharacter(a).length){var r=$('select[name^=characters][name$=\\[character_id\\]] option:selected[value=""]').first().parent(),c=r.find('option[value="'+a+'"i]');c.val()&&(c.prop("selected",!0).change(),t=!0);var s=r.parent().closest(".js-row");$(s).find("[name^=characters][name$=\\[is_exempt\\]]").prop("checked",!1).change(),$(s).find("[name^=characters][name$=\\[remark_id\\]]").val("").change(),$(s).find("[name^=characters][name$=\\[credit\\]]").bootstrapSlider("setValue",1)}return t}function findExistingCharacter(e){var a=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;return a?$('select[name^=characters][name$=\\[character_id\\]] option:selected[value="'.concat(e,'"]')).not(a).first():$('select[name^=characters][name$=\\[character_id\\]] option:selected[value="'.concat(e,'"]')).first()}function fillCharactersFromRaid(e){var a=characters.filter(function(a){return a.raid_group_id==e}),t=characters.filter(function(a){return a.secondary_raid_groups.filter(function(a){return a.id==e}).length>0}),n=a.concat(t).sort(function(e,a){return e.name>a.name?1:a.name>e.name?-1:0}),r=0,c=!0,s=!1,i=void 0;try{for(var o=n[Symbol.iterator](),d;!(c=(d=o.next()).done);c=!0){var h;addCharacter(d.value),r++}}catch(e){s=!0,i=e}finally{try{c||null==o.return||o.return()}finally{if(s)throw i}}$(".js-raid-group-message").html("".concat(r," attendees added")).show(),setTimeout(function(){return $(".js-raid-group-message").hide()},7500)}function fixSliderLabels(){window.dispatchEvent(new Event("resize"))}function resetAttendees(){confirm("Are you sure you want to empty and reset the attendee list?")&&($("select[name^=raid_group_id]").val("").change(),$("select[name^=characters][name$=\\[character_id\\]]").val("").change(),$(".js-attendance-skip").prop("checked",!1).change(),$("select[name^=characters][name$=\\[remark_id\\]]").val("").change(),$("[name^=characters][name$=\\[credit\\]]").bootstrapSlider("setValue",1),$("[name^=characters][name$=\\[public_note\\]]").val(""),$("[name^=characters][name$=\\[officer_note\\]]").val(""),$(".js-show-notes").show(),$(".js-notes").hide())}function showNext(e){""!=$(e).val()&&($(e).show(),$(e).parent().next(".js-hide-empty").show())}function showNextCharacter(e){if(""!=$(e).val()){$(e).show();var a=$(e).closest(".js-row").next(".js-hide-empty");a.show(),a.find("select[name^=characters][name$=\\[character_id\\]]").addClass("selectpicker").selectpicker(),fixSliderLabels()}}$(document).ready(function(){var e=!0;warnBeforeLeaving("#editForm"),$.datetimepicker.setLocale(locale||"en"),$(".js-date-input").datetimepicker({format:"Y-m-d H:i:s",inline:!0,step:30,theme:"dark",value:date?moment.utc(date).local().format("YYYY-MM-DD HH:mm:ss"):moment().format("YYYY-MM-DD HH:mm:ss")}),$(".js-date-input").change(),$("[name=raid_group_id\\[\\]]").change(function(){e||$("[name=add_raiders]").prop("checked")&&$(this).val()&&fillCharactersFromRaid($(this).val())}),$("[name^=characters][name$=\\[character_id\\]").change(function(){var a;e||findExistingCharacter($(this).val(),$(this).find(":selected")).length&&$(this).selectpicker("val","").selectpicker("refresh")}),$(".js-show-next").change(function(){showNext(this)}).change(),$(".js-show-next").keyup(function(){showNext(this)}),$(".js-show-next-character").change(function(){showNextCharacter(this)}).change(),$(".js-show-next-character").keyup(function(){showNextCharacter(this)}),$("#addWarcraftlogsAttendees").click(function(){getWarcraftlogsRankedCharacters(addCharacter,WARCRAFTLOGS_MODE_EXISTING)}),$(".js-show-notes").click(function(){var e=$(this).data("index");$(this).hide(),$('.js-notes[data-index="'.concat(e,'"]')).show()}),$(".js-attendance-skip").on("change",function(){var e=$(this).data("index");this.checked?($('[data-attendance-input="'.concat(e,'"]')).addClass("disabled").hide(),$('[data-attendance-skip-note="'.concat(e,'"]')).show()):($('[data-attendance-input="'.concat(e,'"]')).removeClass("disabled").show(),$('[data-attendance-skip-note="'.concat(e,'"]')).hide())}).change(),$(".js-clear-attendees").click(function(){resetAttendees()}),e=!1,$(".loadingBarContainer").removeClass("d-flex").hide(),$("#editForm").show(),fixSliderLabels()});
