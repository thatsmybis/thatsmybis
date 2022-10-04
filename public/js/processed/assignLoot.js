var inputType="string",firstRun=!0,addedCount=0,skippedCount=0,disenchantCount=0,overLimitCount=0,offspecCount=0,missingCharacters=[],missingCharacterCount=0,errorCount=0,firstError=void 0,preventDuplicates=!0;function clearRow(e){var t=$("#item"+e);t.find("input[name^=item][name$=\\[note\\]]").val(""),t.find("input[name^=item][name$=\\[officer_note\\]]").val(""),t.find("input[name^=item][name$=\\[import_id\\]]").val(""),t.find("select[name^=item][name$=\\[character_id\\]] option").prop("selected",!1),t.find("input[name^=item][name$=\\[is_offspec\\]]").prop("checked",!1),t.find(".js-item-autocomplete").val(""),t.find("input[name^=item][name$=\\[id\\]]").val(""),t.find("input[name^=item][name$=\\[label\\]]").val(""),t.find(".js-input-label").empty(),t.find(".input-item").removeClass("d-flex").hide(),t.find(".js-item-autocomplete").show(),t.find(".selectpicker").selectpicker("refresh")}function parseCsv(e){if("true"!=$(e).prop("disabled")){disableForm(),$("#status-message").html("").hide(),errorCount=0,firstError=void 0,addedCount=0,skippedCount=0,disenchantCount=0,overLimitCount=0,offspecCount=0,missingCharacterCount=0,missingCharacters=[];var t={delimiter:"",header:!0,dynamicTyping:!1,skipEmptyLines:!0,preview:0,step:void 0,encoding:"",worker:!1,comments:!1,complete:completeCsvImport,error:errorCsvImport,download:"remote"==inputType},o=$("#importTextarea").val();if("remote"==inputType?o=$("#url").val():"json"==inputType&&(o=$("#json").val()),firstRun?firstRun=!1:console.log("--------------------------------------------------"),"local"==inputType){if(!$("#files")[0].files.length)return alert("Please choose at least one file to parse."),enableForm(),0;$("#files").parse({config:t,before:function before(e,t){console.log("Parsing file...",e)},error:function error(e,t){console.log("ERROR:",e,t)},complete:function complete(){}})}else if("json"==inputType){if(!o)return alert("Please enter a valid JSON string to convert to CSV."),enableForm(),0;var i=Papa.unparse(o,t);console.log("Unparse complete"),i.length>maxUnparseLength&&(i=i.substr(0,maxUnparseLength),console.log("(Results truncated for brevity)")),console.log(i),setTimeout(function(){enableForm()},100)}else{if("remote"==inputType&&!o)return alert("Please enter the URL of a file to download and parse."),enableForm(),0;var n=Papa.parse(o,t);(t.worker||t.download)&&console.log("Running...")}}}function showNext(e){if(""!=$(e).val()){$(e).show();var t=$(e).closest(".row").next(".js-hide-empty");t.show(),t.find("select[name^=item][name$=\\[character_id\\]]").addClass("selectpicker").selectpicker()}}function stepCsvImport(e,t){}function completeCsvImport(e){setTimeout(function(){},250);var t=0;if(e&&e.errors&&(e.errors&&(errorCount=e.errors.length,firstError=e.errors[0]),e.data&&e.data.length>0&&(t=e.data.length)),console.log("Parse complete"),console.log("    Rows:",t),console.log("    Errors:",errorCount),console.log("    First Error:",firstError),console.log("    Meta:",e.meta),console.log("    Results:",e),console.log("Loading data into form..."),e.data.length>0){prepareForm();for(var o=0,i=0;i<e.data.length;i++){var n=e.data[i],a=["de","disenchant","vendor","vendored"];i>=maxItems?(skippedCount++,overLimitCount++):null!=n.response&&a.includes(n.response.toLowerCase())?(console.log("Skipping row ".concat(i+1,": Disenchant/vendored ").concat(n.item?n.item:n.item_id?n.item_id:"")),skippedCount++,disenchantCount++):(loadItemToForm(n,o),o++,addedCount++)}makeWowheadLinks(),$(".selectpicker").selectpicker("refresh")}var r="";firstError&&(r+='<li class="text-danger">Error: '.concat(firstError.message,"</li>")),addedCount&&(r+='<li class="text-success">'.concat(addedCount," item").concat(addedCount>1?"s":""," loaded into form</li>")),skippedCount&&(r+='<li class="text-warning">Skipped '.concat(skippedCount," item").concat(skippedCount>1?"s":""," ").concat(disenchantCount?"("+disenchantCount+" items disenchanted)":""," ").concat(overLimitCount?"("+overLimitCount+" items over the limit of "+maxItems+")":"","</li>")),offspecCount&&(r+='<li class="text-warning">'.concat(offspecCount," item").concat(offspecCount>1?"s":""," flagged as offspec</li>")),missingCharacterCount&&(missingCharacters=missingCharacters.join(", "),r+='<li class="text-danger">'.concat(missingCharacterCount," character").concat(missingCharacterCount>1?"s":""," not found: ").concat(missingCharacters,"</li>")),(r+='<li class="text-muted">More details in console (usually F12)</li>')&&$("#status-message").html("<ul>".concat(r,"</ul>")).show(),setTimeout(function(){enableForm()},100)}function errorCsvImport(e,t){console.log("ERROR:",e,t),$("#status-message").html("ERROR: "+e).show(),enableForm()}function loadItemToForm(e,t){var o=null,i=null,n=null,a=null,r=null,s=0,c="",l="",m=null,d=null,p=null;if(e.player?a=e.player.split("-")[0].trim():e.character?a=e.character.trim():e.name&&(a=e.name.trim()),e.id){o=(e.id+"-"+a).substring(0,20);var u=e.id.split("-").map(function(e){return e.trim()});u.length>0&&(u=moment(Number(u[0]+"000"))).isValid()&&(r=u.format("YYYY-MM-DD"))}e.itemID?i=e.itemID.trim():e.item_id&&(i=e.item_id.trim()),i&&18423==i&&(i=18422),i&&19003==i&&(i=19002),i&&32386==i&&(i=32385),i&&43959==i&&(i=44083),e.item?n=e.item.trim():e.itemName?n=e.itemName.trim():e.item_name&&(n=e.item_name.trim()),e.date&&!r?(rclcDate=moment(e.date.trim(),"DD/MM/YY"),r=rclcDate.isValid()?rclcDate.format("YYYY-MM-DD"):moment(e.date.trim()).format("YYYY-MM-DD")):e.dateTime?r=moment(e.dateTime.trim()).format("YYYY-MM-DD"):e.date_time&&(r=moment(e.date_time.trim()).format("YYYY-MM-DD")),e.publicNote?c=e.publicNote.trim():e.public_note&&(c=e.public_note.trim()),c=c.substr(0,140),e.officerNote?l=e.officerNote.trim():e.officer_note&&(l=e.officer_note.trim()),e.note&&(m=e.note.trim()),e.response&&(d=e.response.trim()),e.votes&&(p=e.votes.trim()),l=(l=(p?"Votes: "+p+" ":"")+(l?l+" ":"")+(d?'"'+d+'" ':"")+(m?'"'+m+'" ':"")).substr(0,140);var f=["os","offspec"];if(e.offspec&&1==e.offspec?s=1:(null==e.offspec||0!==e.offspec&&"false"!==e.offspec.toLowerCase()&&"no"!==e.offspec.toLowerCase())&&(e.note&&f.includes(e.note.toLowerCase())?s=1:e.response&&f.includes(e.response.toLowerCase())?s=1:e.publicNote&&f.includes(e.publicNote.toLowerCase())?s=1:e.officerNote&&f.includes(e.officerNote.toLowerCase())?s=1:e.officer_note&&f.includes(e.officer_note.toLowerCase())&&(s=1)),i){var h=$(".js-item-autocomplete[data-id="+t+"]").change();addTag(h,i,n)}if(a){var g=$("[name=item\\["+t+"\\]\\[character_id\\]]"),v=g.find('option[data-name="'+a+'"i]');v.val()?(v.prop("selected",!0).change(),g.parent().removeClass("form-danger")):-1==missingCharacters.indexOf(a)?(console.log("Character not found for row "+(t+1)+": "+a),missingCharacterCount++,missingCharacters.push(a),g.parent().addClass("form-danger")):g.parent().addClass("form-danger")}return 1==s&&($("[name=item\\["+t+"\\]\\[is_offspec\\]]").prop("checked",!0).change(),console.log("Flagged row ".concat(t+1," as offspec ").concat(n||(i||"")).concat(a?" on "+a:"")),offspecCount++),c&&$("[name=item\\["+t+"\\]\\[note\\]]").val(c).change(),l&&$("[name=item\\["+t+"\\]\\[officer_note\\]]").val(l).change(),r&&$("[name=item\\["+t+"\\]\\[received_at\\]]").val(r).change(),preventDuplicates&&o&&$("[name=item\\["+t+"\\]\\[import_id\\]]").val(o).change(),e}function prepareForm(){$("[name=toggle_notes]").prop("checked",!0).change(),$("[name=toggle_dates]").prop("checked",!0).change(),$("[name=date_default]").val("").change(),$("[name=raid_group_filter]").val("").change(),$("input[name^=item][name$=\\[note\\]]").val(""),$("input[name^=item][name$=\\[officer_note\\]]").val(""),$("input[name^=item][name$=\\[import_id\\]]").val(""),$("select[name^=item][name$=\\[character_id\\]] option").prop("selected",!1),$("input[name^=item][name$=\\[is_offspec\\]]").prop("checked",!1),$(".js-item-autocomplete").val(""),$("input[name^=item][name$=\\[id\\]]").val(""),$("input[name^=item][name$=\\[label\\]]").val(""),$(".js-input-label").empty(),$(".input-item").removeClass("d-flex").hide(),$(".js-item-autocomplete").show(),$(".selectpicker").selectpicker("refresh")}function disableForm(){$("#loading-indicator").show(),$("#submitImport").prop("disabled",!0),$("#itemForm fieldset").prop("disabled",!0),$(".js-toggle-import").prop("disabled",!0)}function enableForm(){setTimeout(function(){$("#itemForm fieldset").prop("disabled",!1),$("#loading-indicator").hide(),$("#loaded-indicator").show(),setTimeout(function(){$("#submitImport").prop("disabled",!1),$(".js-toggle-import").prop("disabled",!1),$("#loaded-indicator").hide()},5e3)},1e3)}$(document).ready(function(){$(".js-remove-item").click(function(){var e;clearRow($(this).data("index"))}),$(".js-show-next").change(function(){showNext(this)}),$(".js-show-next").keyup(function(){showNext(this)}),$("select[name^=item][name$=\\[character_id\\]]:visible").addClass("selectpicker").selectpicker(),$("[name=raid_group_id]").on("change",function(){$(this).val()?$("#raidGroupWarning").hide():$("#raidGroupWarning").show()}).change(),$("#raid_group_filter").on("change",function(){var e=$(this).val();e?($(".js-character-option[data-raid-group-id!='"+e+"']").hide(),$(".js-character-option[data-raid-group-id='"+e+"']").show()):$(".js-character-option").show(),$(".selectpicker").selectpicker("refresh")}).change(),$("[name=toggle_notes]").on("change",function(){this.checked?$(".js-note").show():$(".js-note").hide()}).change(),$("[name=toggle_dates]").on("change",function(){this.checked?($(".js-date").show(),$("#default_datepicker").show()):($(".js-date").hide(),$("#default_datepicker").hide())}).change(),$("[name=date_default]").on("change",function(){$("[name*='received_at']").val($(this).val())}),$(".js-toggle-import").click(function(){$("#toggleImportArea").toggle(),$("#importArea").toggle()}),$("#tab-string").click(function(){$(".tab").removeClass("active"),$(this).addClass("active"),$(".input-area").hide(),$("#input-string").show(),$("#submit").text("Parse"),inputType="string"}),$("#tab-local").click(function(){$(".tab").removeClass("active"),$(this).addClass("active"),$(".input-area").hide(),$("#input-local").show(),$("#submit").text("Parse"),inputType="local"}),$("#tab-remote").click(function(){$(".tab").removeClass("active"),$(this).addClass("active"),$(".input-area").hide(),$("#input-remote").show(),$("#submit").text("Parse"),inputType="remote"}),$("#tab-unparse").click(function(){$(".tab").removeClass("active"),$(this).addClass("active"),$(".input-area").hide(),$("#input-unparse").show(),$("#submit").text("Unparse"),inputType="json"}),$("#submitImport").click(function(){parseCsv(this)})});
