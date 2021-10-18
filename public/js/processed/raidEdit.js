function _slicedToArray(e,t){return _arrayWithHoles(e)||_iterableToArrayLimit(e,t)||_nonIterableRest()}function _nonIterableRest(){throw new TypeError("Invalid attempt to destructure non-iterable instance")}function _iterableToArrayLimit(e,t){if(Symbol.iterator in Object(e)||"[object Arguments]"===Object.prototype.toString.call(e)){var a=[],n=!0,r=!1,c=void 0;try{for(var o=e[Symbol.iterator](),s;!(n=(s=o.next()).done)&&(a.push(s.value),!t||a.length!==t);n=!0);}catch(e){r=!0,c=e}finally{try{n||null==o.return||o.return()}finally{if(r)throw c}}return a}}function _arrayWithHoles(e){if(Array.isArray(e))return e}function _toConsumableArray(e){return _arrayWithoutHoles(e)||_iterableToArray(e)||_nonIterableSpread()}function _nonIterableSpread(){throw new TypeError("Invalid attempt to spread non-iterable instance")}function _iterableToArray(e){if(Symbol.iterator in Object(e)||"[object Arguments]"===Object.prototype.toString.call(e))return Array.from(e)}function _arrayWithoutHoles(e){if(Array.isArray(e)){for(var t=0,a=new Array(e.length);t<e.length;t++)a[t]=e[t];return a}}var testReports=null;function addCharacter(e){var t=!1,a;if(!findExistingCharacter(e).length){var n=$('select[name^=characters][name$=\\[character_id\\]] option:selected[value=""]').first().parent(),r=n.find('option[value="'+e+'"i]');r.val()&&(r.prop("selected",!0).change(),t=!0);var c=n.parent().closest(".js-row");$(c).find("[name^=characters][name$=\\[is_exempt\\]]").prop("checked",!1).change(),$(c).find("[name^=characters][name$=\\[remark_id\\]]").val("").change(),$(c).find("[name^=characters][name$=\\[credit\\]]").bootstrapSlider("setValue",1)}return t}function addWarcraftlogsAttendees(){var e=$("[name^=logs]:visible"),t=[];e.each(function(){$(this).val()&&(log=new URL($(this).val()),log.pathname.split("/").forEach(function(e){16===e.length&&t.push(e)}))}),$(".js-warcraftlogs-attendees-loading-spinner").show(),$("#warcraftlogsLoadingbar").addClass("d-flex").show(),setTimeout(function(){return $("#warcraftlogsLoadingbar").removeClass("d-flex").hide()},7500),$.ajax({method:"get",data:{codes:t,guild_id:guild.id},dataType:"json",url:"/api/warcraftlogs/attendees",success:function success(e){if($("#warcraftlogsLoadingbar").removeClass("d-flex").hide(),e.length<=0)$(".js-warcraftlogs-attendees-message").html("No attendance data found").show(),setTimeout(function(){return $(".js-warcraftlogs-attendees-message").hide()},1e3);else{for(var t=[],a=[],n=[],r=0,c=0,o="Warcraft Logs sent the following:",s=0,i=Object.entries(e);s<i.length;s++){var l=_slicedToArray(i[s],2),d=l[0],h=l[1];h.rankedCharacters&&(t=[].concat(_toConsumableArray(t),_toConsumableArray(h.rankedCharacters.map(function(e){return e.name})))),o+='<ul class="mt-3">\n                        <li class="font-weight-bold">\n                            '.concat(h.title,"\n                        </li>\n                        <li>\n                            ").concat(h.rankedCharacters?"".concat(h.rankedCharacters.length," characters"):'0 characters <span class="small text-muted font-weight-normal">(blame WCL)</span>',"\n                        </li>\n                        ").concat(h.endTime?'<li class="small text-muted font-weight-normal">'.concat(moment.utc(h.endTime).local().format("ddd, MMM Do YYYY @ h:mm a"),"</li>"):"","\n                        ").concat(h.zone&&h.zone.name?'<li class="small text-muted font-weight-normal">'.concat(h.zone.name,"</li>"):"",'\n                        <li class="small text-muted font-weight-normal">\n                            ID ').concat(h.code,"\n                        </li>\n                    </ul>")}(t=_toConsumableArray(new Set(t))).sort().forEach(function(e){character=characters.find(function(t){return t.slug===e.toLowerCase()}),character?(addCharacter(character.id),r++,a=[].concat(_toConsumableArray(a),[character.name])):n=[].concat(_toConsumableArray(n),[e])}),o+="<ul>\n                    <li>".concat(r," added</li>\n                    ").concat(t.length&&a?'<li class="text-white"><span class="font-weight-bold">Successful:</span> '.concat(a.sort().join(", "),"</li>"):"","\n                    ").concat(t.length&&n?'<li class="text-white"><span class="font-weight-bold">Not found:</span> '.concat(n.sort().join(", "),'</li><li class="text-white">To add these characters, go create them and then reload this page</li>'):"","\n                </ul>"),$(".js-warcraftlogs-attendees-message").html(o).show()}}})}function findExistingCharacter(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;return t?$('select[name^=characters][name$=\\[character_id\\]] option:selected[value="'.concat(e,'"]')).not(t).first():$('select[name^=characters][name$=\\[character_id\\]] option:selected[value="'.concat(e,'"]')).first()}function fillCharactersFromRaid(e){var t=characters.filter(function(t){return t.raid_group_id==e}),a=characters.filter(function(t){return t.secondary_raid_groups.filter(function(t){return t.id==e}).length>0}),n=t.concat(a).sort(function(e,t){return e.name>t.name?1:t.name>e.name?-1:0}),r=0,c=!0,o=!1,s=void 0;try{for(var i=n[Symbol.iterator](),l;!(c=(l=i.next()).done);c=!0){var d;addCharacter(l.value.id),r++}}catch(e){o=!0,s=e}finally{try{c||null==i.return||i.return()}finally{if(o)throw s}}$(".js-raid-group-message").html("".concat(r," characters added")).show(),setTimeout(function(){return $(".js-raid-group-message").hide()},7500)}function fixSliderLabels(){window.dispatchEvent(new Event("resize"))}function resetAttendees(){confirm("Are you sure you want to empty and reset the attendee list?")&&($("select[name^=raid_group_id]").val("").change(),$("select[name^=characters][name$=\\[character_id\\]]").val("").change(),$(".js-attendance-skip").prop("checked",!1).change(),$("select[name^=characters][name$=\\[remark_id\\]]").val("").change(),$("[name^=characters][name$=\\[credit\\]]").bootstrapSlider("setValue",1),$("[name^=characters][name$=\\[public_note\\]]").val(""),$("[name^=characters][name$=\\[officer_note\\]]").val(""),$(".js-show-notes").show(),$(".js-notes").hide())}function showNext(e){""!=$(e).val()&&($(e).show(),$(e).parent().next(".js-hide-empty").show())}function showNextCharacter(e){if(""!=$(e).val()){$(e).show();var t=$(e).closest(".js-row").next(".js-hide-empty");t.show(),t.find("select[name^=characters][name$=\\[character_id\\]]").addClass("selectpicker").selectpicker(),fixSliderLabels()}}$(document).ready(function(){var e=!0;warnBeforeLeaving("#editForm"),$.datetimepicker.setLocale(locale||"en"),$(".js-date-input").datetimepicker({format:"Y-m-d H:i:s",inline:!0,step:30,theme:"dark",value:date?moment.utc(date).local().format("YYYY-MM-DD HH:mm:ss"):moment().format("YYYY-MM-DD HH:mm:ss")}),$(".js-date-input").change(),$("[name=raid_group_id\\[\\]]").change(function(){e||$("[name=add_raiders]").prop("checked")&&$(this).val()&&fillCharactersFromRaid($(this).val())}),$("[name^=characters][name$=\\[character_id\\]").change(function(){var t;e||findExistingCharacter($(this).val(),$(this).find(":selected")).length&&$(this).selectpicker("val","").selectpicker("refresh")}),$(".js-show-next").change(function(){showNext(this)}).change(),$(".js-show-next").keyup(function(){showNext(this)}),$(".js-show-next-character").change(function(){showNextCharacter(this)}).change(),$(".js-show-next-character").keyup(function(){showNextCharacter(this)}),$("#addWarcraftlogsAttendees").click(function(){addWarcraftlogsAttendees()}),$(".js-show-notes").click(function(){var e=$(this).data("index");$(this).hide(),$('.js-notes[data-index="'.concat(e,'"]')).show()}),$(".js-attendance-skip").on("change",function(){var e=$(this).data("index");this.checked?($('[data-attendance-input="'.concat(e,'"]')).addClass("disabled").hide(),$('[data-attendance-skip-note="'.concat(e,'"]')).show()):($('[data-attendance-input="'.concat(e,'"]')).removeClass("disabled").show(),$('[data-attendance-skip-note="'.concat(e,'"]')).hide())}).change(),$(".js-clear-attendees").click(function(){resetAttendees()}),e=!1,$(".loadingBarContainer").removeClass("d-flex").hide(),$("#editForm").show(),fixSliderLabels()});
