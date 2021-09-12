var table=null,colSource=0,colName=1,colPrios=2,colWishlist=3,colReceived=4,colNotes=5,colPriority=6,lastSource=null;function createTable(a){return itemTable=$("#itemTable").DataTable({autoWidth:!1,data:items,columns:[{title:'<span class="fas fa-fw fa-skull-crossbones"></span> '.concat(headerBoss),data:"",render:function render(a,t,e){return e.source_name&&(thisSource=e.source_name),'\n                    <ul class="no-bullet no-indent mb-0">\n                        '.concat(e.source_name?'\n                            <li>\n                                <span class="font-weight-bold">\n                                    '.concat(e.source_name,"\n                                </span>\n                            </li>"):"","\n                    </ul>")},visible:!0,width:"130px",className:"text-right width-130"},{title:'<span class="fas fa-fw fa-sack text-success"></span> '.concat(headerLoot,' <span class="text-muted small">(').concat(items.length,")</span>"),data:"",render:function render(a,t,e){return getItemLink(e)},visible:!0,width:"330px",className:"width-330"},{title:'<span class="fas fa-fw fa-sort-amount-down text-gold"></span> '.concat(headerPrios),data:guild.is_attendance_hidden?"priod_characters":"priod_characters_with_attendance",render:function render(a,t,e){return a&&a.length?createCharacterListHtml(a,"prio",e.item_id,null):"—"},orderable:!1,visible:!!showPrios,width:"300px",className:"width-300"},{title:'<span class="text-legendary fas fa-fw fa-scroll-old"></span> '.concat(headerWishlist),data:guild.is_attendance_hidden?"wishlist_characters":"wishlist_characters_with_attendance",render:function render(a,t,e){if(a&&a.length){var n="";if(currentWishlistNumber){var c=a.filter(function(a){return a.pivot.list_number==currentWishlistNumber});c.length&&(n=createCharacterListHtml(c,"wishlist",e.item_id,null))}else for(i=1;i<=maxWishlistLists;i++){var s=a.filter(function(a){return a.pivot.list_number==i});if(s.length){var r=headerWishlist+" "+i;n+=createCharacterListHtml(s,"wishlist",e.item_id,r)}}return""==n&&(n="—"),n}return"—"},orderable:!1,visible:!!showWishlist,width:"400px",className:"width-400"},{title:'<span class="text-success fas fa-fw fa-sack"></span> '.concat(headerReceived),data:"received_and_recipe_characters",render:function render(a,t,e){return a&&a.length?createCharacterListHtml(a,"received",e.item_id,null):"—"},orderable:!1,visible:!0,width:"300px",className:"width-300"},{title:'<span class="fas fa-fw fa-comment-alt-lines"></span> '.concat(headerNotes),data:"guild_note",render:function render(a,t,e){return getNotes(e,a)},orderable:!1,visible:!!showNotes,width:"200px",className:"width-200"},{title:'<span class="fas fa-fw fa-comment-alt-lines"></span> '.concat(headerPrioNotes),data:"guild_priority",render:function render(a,t,e){return a?'<span class="js-markdown-inline">'.concat(nl2br(a),"</span>"):"—"},orderable:!1,visible:!!showNotes,width:"200px",className:"width-200"}],order:[],paging:!1,fixedHeader:!0,drawCallback:function drawCallback(){makeWowheadLinks(),parseMarkdown(),trackTimestamps()},initComplete:function initComplete(){makeWowheadLinks(),parseMarkdown()},createdRow:function createdRow(t,e,n){0!=n&&null!=a||(a=e.source_name),e.source_name!=a&&($(t).addClass("top-border"),a=e.source_name)}}),itemTable}function addWishlistFilterHandlers(){$("#wishlist_filter").on("change",function(){currentWishlistNumber=$(this).val(),itemTable.rows().invalidate().draw()}).change()}function createCharacterListHtml(a,t,e){var n=arguments.length>3&&void 0!==arguments[3]?arguments[3]:null,i='<ul class="list-inline js-item-list mb-0" data-type="'.concat(t,'" data-id="').concat(e,'">');n&&(i+='<li class="js-item-wishlist-character no-bullet font-weight-bold text-muted small">\n            '.concat(n,"\n        </li>"));var c=4,s=null;return $.each(a,function(a,e){if("prio"==t&&e.pivot.raid_group_id&&e.pivot.raid_group_id!=s){s=e.pivot.raid_group_id;var n="";if(raidGroups.length){var c=raidGroups.find(function(a){return a.id===e.pivot.raid_group_id});c&&(n=c.name)}i+='\n                <li data-raid-group-id="" class="js-item-wishlist-character no-bullet font-weight-normal text-muted small">\n                    '.concat(n,"\n                </li>\n            ")}if("wishlist"==t&&(e.raid_group_id&&e.raid_group_id!=s||!e.raid_group_id&&s)){var r="";if(!e.raid_group_id&&s)r="no raid group",s=null;else if(s=e.raid_group_id,raidGroups.length){var l=raidGroups.find(function(a){return a.id===e.raid_group_id});l&&(r=l.name)}i+='\n                <li data-raid-group-id="" class="js-item-wishlist-character no-bullet font-weight-normal text-muted small">\n                    '.concat(r,"\n                </li>\n            ")}i+='\n            <li data-raid-group-id="'.concat("prio"==t?e.pivot.raid_group_id:e.raid_group_id,'"\n                value="').concat("prio"==t?e.pivot.order:"",'"\n                class="js-item-wishlist-character list-inline-item font-weight-normal mb-1 mr-0 ').concat("received"!=e.pivot.type&&e.pivot.received_at?"font-strikethrough":"",'">\n                <a href="/').concat(guild.id,"/").concat(guild.slug,"/c/").concat(e.id,"/").concat(e.slug,'"\n                    title="').concat(e.raid_group_name?e.raid_group_name+" -":""," ").concat(e.level?e.level:""," ").concat(e.race?e.race:""," ").concat(e.spec?e.spec:""," ").concat(e.class?e.class:""," ").concat(e.raid_count?"(".concat(e.raid_count," raid").concat(e.raid_count>1?"s":""," attended)"):""," ").concat(e.username?"("+e.username+")":"",'"\n                    class="tag text-muted d-inline">\n                    <span class="">').concat("received"!==t&&e.pivot.order?e.pivot.order:"",'</span>\n                    <span class="small font-weight-bold">').concat(e.pivot.is_offspec?"OS":"",'</span>\n                    <span class="role-circle" style="background-color:').concat(getColorFromDec(e.raid_group_color),'"></span>\n                    <span class="text-').concat(e.class?e.class.toLowerCase():"",'-important">').concat(e.name,"</span>\n                    ").concat(e.is_alt?'\n                        <span class="text-warning">'.concat(localeAlt,"</span>\n                    "):"","\n                    ").concat(guild.is_attendance_hidden||!e.attendance_percentage&&!e.raid_count?"":"".concat(e.raid_count&&"number"==typeof e.attendance_percentage?'<span title="attendance" class="smaller '.concat(getAttendanceColor(e.attendance_percentage),'">').concat(Math.round(100*e.attendance_percentage),"%</span>"):"").concat(e.raid_count?'<span class="smaller"> '.concat(e.raid_count,"r</span>"):"","\n                    "),'\n                    <span class="js-watchable-timestamp smaller"\n                        data-timestamp="').concat(e.pivot.created_at,'"\n                        data-is-short="1">\n                    </span>\n                    <span style="display:none;">').concat(e.discord_username," ").concat(e.username,"</span>\n                </a>\n            </li>")}),i+="</ul>"}function getNotes(a,t){var e=null;return t=t?'<span class="js-markdown-inline">'.concat(t?DOMPurify.sanitize(nl2br(t)):"","</span>").concat(""):"—"}function getItemLink(a){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null,e='data-wowhead-link="https://'.concat(wowheadLocale+wowheadSubdomain,".wowhead.com/item=").concat(a.item_id,'"\n    data-wowhead="item=').concat(a.item_id,"?domain=").concat(wowheadLocale+wowheadSubdomain,'"');t&&(e+=' data-wh-icon-size="'.concat(t,'"'));var n="";return n=guild?"/".concat(guild.id,"/").concat(guild.slug,"/i/").concat(a.item_id,"/").concat(slug(a.name)):"",'\n    <ul class="no-bullet no-indent mb-0">\n        <li>\n            '.concat(guild.tier_mode?'<span class="text-monospace font-weight-medium text-tier-'.concat(a.guild_tier?a.guild_tier:"",'">').concat(a.guild_tier?getItemTierLabel(a,guild.tier_mode):"&nbsp;","</span>"):"",'\n            <a href="').concat(n,'"\n                class="').concat(a.quality?"q"+a.quality:"",'"\n                ').concat(e,">\n                ").concat(a.name,"\n            </a>\n        </li>\n    </ul>")}$(document).ready(function(){table=createTable(),$(".toggle-column").click(function(a){a.preventDefault();var t=table.column($(this).attr("data-column"));t.visible(!t.visible())}),$(".toggle-column-default").click(function(a){a.preventDefault(),table.column(colName).visible(!0),table.column(colPrios).visible(!0),table.column(colWishlist).visible(!0),table.column(colReceived).visible(!0),table.column(colNotes).visible(!0),table.column(colPriority).visible(!0)}),table.on("column-visibility.dt",function(a,t,e,n){makeWowheadLinks(),trackTimestamps(),parseMarkdown()}),$("#raid_group_filter").on("change",function(){var a=$(this).val();a?($(".js-item-wishlist-character[data-raid-group-id!='"+a+"']").hide(),$(".js-item-wishlist-character[data-raid-group-id='"+a+"']").show()):$(".js-item-wishlist-character").show()}).change(),addWishlistFilterHandlers(),trackTimestamps()});
