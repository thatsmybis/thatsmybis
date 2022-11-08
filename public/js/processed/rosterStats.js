function _readOnlyError(e){throw new Error('"'+e+'" is read-only')}var table=null,VIEW_PRIOS="prios",VIEW_RECEIVED="received",VIEW_SLOTS="slots",VIEW_WISHLIST="wishlist",view="",colCharacter=0,colArchetype=1,colAttendance=2,colMainRaidGroup=3,colNotes=4,colClass=5,colSlotTotal=10,colSlotHead=11,colSlotNeck=12,colSlotShoulder=13,colSlotBack=14,colSlotChest=15,colSlotWrists=16,colSlotHands=17,colSlotWaist=18,colSlotLegs=19,colSlotFeet=20,colSlotFinger=21,colSlotTrinket=22,colSlotWeapon=23,colSlotOffhand=24,colSlotRanged=25,colSlotOther=26,colInstanceTotal=27,colInstance1=28,colInstance2=29,colInstance3=30,colInstance4=31,colInstance5=32,colInstance6=33,colInstance7=34,colInstance8=35,colInstance9=36,colInstance10=37,colInstance11=38,colInstance12=39,colInstance13=40,colInstance14=41,colInstance15=42,colInstance16=43,colInstance17=44,colInstance18=45,colInstance19=46,colInstance20=47,colInstance21=48,colInstance22=49,colInstance23=50,colInstance24=51,colInstance25=52,allItemsVisible=!1,offspecVisible=!0,strikethroughVisible=!0,instanceIdsToShow=[],lootTypeToShow="received",rosterHandlersTimeout=null;function initializeTable(){null!=table&&(table.clear().destroy(),$("#characterStatsTable tbody").empty(),$("#characterStatsTable thead").empty()),table=createRosterStatsTable(),showOrHideSomeStuffBasedOnView()}function showOrHideSomeStuffBasedOnView(){view==VIEW_SLOTS?$(".js-show-slot-cols").addClass("disabled"):view==VIEW_PRIOS?($(".js-show-prio-cols").addClass("disabled"),$(".js-received-items").hide(),$(".js-wishlist-items").hide(),$("#loot_type_container").hide()):view==VIEW_RECEIVED?($(".js-show-received-cols").addClass("disabled"),$(".js-prio-items").hide(),$(".js-wishlist-items").hide(),$("#loot_type_container").hide()):view==VIEW_WISHLIST&&($(".js-show-wishlist-cols").addClass("disabled"),$(".js-prio-items").hide(),$(".js-received-items").hide(),$("#loot_type_container").hide())}function createRosterStatsTable(){$.fn.DataTable.isDataTable("#characterStatsTable")&&$("#characterStatsTable").destroy();var e=[{title:'<span class="fas fa-fw fa-user"></span> '.concat(headerCharacter,' <span class="text-muted small">(').concat(characters.length,")</span>"),data:"name",render:{_:function _(e,n,t){return'<ul class="no-bullet no-indent">\n                        <li>\n                            <div class="dropdown text-'.concat(t.class?slug(t.class):"",'">\n                                <a class="dropdown-toggle font-weight-bold text-').concat(t.class?slug(t.class):"",'"\n                                    id="character').concat(t.id,'Dropdown"\n                                    role="button"\n                                    data-toggle="dropdown"\n                                    aria-haspopup="true"\n                                    aria-expanded="false"\n                                    title="').concat(t.username?t.username:"",'">\n                                    ').concat(t.name,'\n                                </a>\n                                <div class="dropdown-menu" aria-labelledby="character').concat(t.id,'Dropdown">\n                                    <a class="dropdown-item" href="/').concat(guild.id,"/").concat(guild.slug,"/c/").concat(t.id,"/").concat(t.slug,'">Profile</a>\n                                    <a class="dropdown-item" href="/').concat(guild.id,"/").concat(guild.slug,"/audit-log?character_id=").concat(t.id,'">History</a>\n                                    ').concat(showEdit?'<a class="dropdown-item" href="/'.concat(guild.id,"/").concat(guild.slug,"/c/").concat(t.id,"/").concat(t.slug,'/edit">Edit</a>\n                                        <a class="dropdown-item" href="/').concat(guild.id,"/").concat(guild.slug,"/c/").concat(t.id,"/").concat(t.slug,'/loot">Wishlist & Loot</a>'):"","\n                                    ").concat(t.member_id?'<a class="dropdown-item" href="/'.concat(guild.id,"/").concat(guild.slug,"/u/").concat(t.member_id,"/").concat(t.username?t.username.toLowerCase():"view member",'">').concat(t.username?t.username:"view member","</a>"):"","\n                                </div>\n                            </div>\n                        </li>\n                        ").concat(t.spec||t.display_spec||t.archetype?'<li class="small font-weight-light">\n                                <span class="'.concat(t.archetype?getArchetypeIcon(t.archetype):"",'"></span>\n                                <span class="">\n                                    ').concat(t.spec_label?t.spec_label:t.spec?t.display_spec:"","\n                                </span>\n                            </li>"):"","\n                        ").concat(t.raid_group_name?"<li>".concat(getRaidGroupHtml(t.raid_group_name,t.raid_group_color),"</li>"):"","\n                    </ul>")},sort:function sort(e,n,t){return e}},visible:!0,width:"50px",className:"width-50 fixed-width"},{title:"Role",data:"character",render:function render(e,n,t){return'<span class="smaller">'.concat(t.archetype?t.archetype:""," ").concat(t.sub_archetype?t.sub_archetype:"","</span>")},visible:!0,width:"20px",className:"width-20 fixed-width"},{title:"Att.",data:"raid_count",render:{_:function _(e,n,t){return!guild.is_attendance_hidden&&(t.attendance_percentage||t.raid_count||t.benched_count)?'<ul class="list-inline small">\n                                '.concat(t.raid_count&&"number"==typeof t.attendance_percentage?'<li class="list-inline-item mr-0 '.concat(getAttendanceColor(t.attendance_percentage),'" title="attendance">').concat(Math.round(100*t.attendance_percentage),"%</li>"):"","\n                                ").concat(t.raid_count?'<li class="list-inline-item text-muted mr-0">'.concat(t.raid_count,"r</li>"):"","\n                                ").concat(t.benched_count?'<li class="list-inline-item text-muted mr-0">bench '.concat(t.benched_count,"x</li>"):"","\n                            </ul>"):""},sort:function sort(e,n,t){return e}},visible:!0,width:"10px",className:"width-10 fixed-width",type:"num"},{title:"Raid",data:"character",render:function render(e,n,t){if(t.raid_group_name||t.secondary_raid_groups&&t.secondary_raid_groups.length){var s="";return t.secondary_raid_groups&&t.secondary_raid_groups.length&&(s='<ul class="list-inline">',t.secondary_raid_groups.forEach(function(e,n){s+="".concat(e.id,' <li class="list-inline-item small"><span class="text-muted"><span class="role-circle align-fix" style="background-color:').concat(getColorFromDec(parseInt(e.color)),'"></span>').concat(e.name,"</span></li>")}),s+="</ul>"),t.raid_group_id+getRaidGroupHtml(t.raid_group_name,t.raid_group_color)+s}return""},visible:!1,width:"50px",className:"width-50 fixed-width"},{title:'<span class="fas fa-fw fa-comment-alt-lines"></span> '.concat(headerNotes),data:"notes",render:function render(e,n,t){return getNotes(e,n,t)},orderable:!1,visible:!1,width:"100px",className:"width-100 fixed-width"},{title:"Class",data:"class",render:function render(e,n,t){return t.class?t.class:null},visible:!1},{title:"Username",data:"username",render:function render(e,n,t){return t.username?t.username:null},visible:!1},{title:"Discord Username",data:"discord_username",render:function render(e,n,t){return t.discord_username?t.discord_username:null},visible:!1},{title:"Raids Attended",data:"raid_count",render:function render(e,n,t){return t.raid_count?t.raid_count:null},visible:!1,searchable:!1},{title:"Benched Count",data:"benched_count",render:function render(e,n,t){return t.benched_count?t.benched_count:null},visible:!1,searchable:!1}],n=!1;return view===VIEW_SLOTS&&(n=!0),e.push(createItemSlotColumn("Total",null,n),createItemSlotColumn("Head",[SLOT_HEAD],n),createItemSlotColumn("Neck",[SLOT_NECK],n),createItemSlotColumn("Shoulders",[SLOT_SHOULDERS],n),createItemSlotColumn("Back",[SLOT_BACK],n),createItemSlotColumn("Chest",[SLOT_CHEST_1,SLOT_CHEST_2],n),createItemSlotColumn("Wrist",[SLOT_WRIST],n),createItemSlotColumn("Waist",[SLOT_WAIST],n),createItemSlotColumn("Hands",[SLOT_HANDS],n),createItemSlotColumn("Legs",[SLOT_LEGS],n),createItemSlotColumn("Feet",[SLOT_FEET],n),createItemSlotColumn("Finger",[SLOT_FINGER],n),createItemSlotColumn("Trinket",[SLOT_TRINKET],n),createItemSlotColumn("Weapon",[SLOT_WEAPON_MAIN_HAND,SLOT_WEAPON_TWO_HAND,SLOT_WEAPON_ONE_HAND,SLOT_WEAPON_OFF_HAND],n),createItemSlotColumn("Offhand",[SLOT_SHIELD,SLOT_OFFHAND],n),createItemSlotColumn("Ranged /Relic",[SLOT_RANGED_1,SLOT_RANGED_2,SLOT_THROWN,SLOT_RELIC],n),createItemSlotColumn("Misc",[SLOT_MISC,SLOT_SHIRT,SLOT_BAG,SLOT_AMMO],n)),n=!![VIEW_PRIOS,VIEW_RECEIVED,VIEW_WISHLIST].includes(view),e.push(createInstanceTotalsColumn(n)),guild&&1===guild.expansion_id?(e.push(createInstanceColumn("MC",1,n&&(instanceIdsToShow.includes(1)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Ony",2,n&&(instanceIdsToShow.includes(2)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("BWL",3,n&&(instanceIdsToShow.includes(3)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("ZG",4,n&&(instanceIdsToShow.includes(4)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("AQ20",5,n&&(instanceIdsToShow.includes(5)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("AQ40",6,n&&(instanceIdsToShow.includes(6)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Naxx",7,n&&(instanceIdsToShow.includes(7)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("World Bosses",8,n&&(instanceIdsToShow.includes(8)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Other",null,0===instanceIdsToShow.length))):guild&&2===guild.expansion_id?(e.push(createInstanceColumn("Kara",9,n&&(instanceIdsToShow.includes(9)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Gruul",10,n&&(instanceIdsToShow.includes(10)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Mag",11,n&&(instanceIdsToShow.includes(11)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("SSC",12,n&&(instanceIdsToShow.includes(12)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Hyjal",13,n&&(instanceIdsToShow.includes(13)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("TK",14,n&&(instanceIdsToShow.includes(14)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("BT",15,n&&(instanceIdsToShow.includes(15)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("ZA",16,n&&(instanceIdsToShow.includes(16)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Sunwell",17,n&&(instanceIdsToShow.includes(17)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("World Bosses",18,n&&(instanceIdsToShow.includes(18)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Other",null,0===instanceIdsToShow.length))):guild&&3===guild.expansion_id&&(e.push(createInstanceColumn("Naxx N10",19,n&&(instanceIdsToShow.includes(19)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Naxx N25",20,n&&(instanceIdsToShow.includes(20)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("EoE N10",21,n&&(instanceIdsToShow.includes(21)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("EoE N25",22,n&&(instanceIdsToShow.includes(22)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("OS N10",23,n&&(instanceIdsToShow.includes(23)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("OS N25",24,n&&(instanceIdsToShow.includes(24)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Arch N10",25,n&&(instanceIdsToShow.includes(25)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Arch N25",26,n&&(instanceIdsToShow.includes(26)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Uld N10",27,n&&(instanceIdsToShow.includes(27)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Uld N25",28,n&&(instanceIdsToShow.includes(28)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("TotC N10",29,n&&(instanceIdsToShow.includes(29)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("TotC N25",30,n&&(instanceIdsToShow.includes(30)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("TotC H10",31,n&&(instanceIdsToShow.includes(31)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("TotC H25",32,n&&(instanceIdsToShow.includes(32)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Ony N10",33,n&&(instanceIdsToShow.includes(33)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Ony N25",34,n&&(instanceIdsToShow.includes(34)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("ICC N10",35,n&&(instanceIdsToShow.includes(35)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("ICC N25",36,n&&(instanceIdsToShow.includes(36)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("ICC H10",37,n&&(instanceIdsToShow.includes(37)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("ICC H25",38,n&&(instanceIdsToShow.includes(38)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("RS N10",39,n&&(instanceIdsToShow.includes(39)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("RS N25",40,n&&(instanceIdsToShow.includes(40)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("RS H10",41,n&&(instanceIdsToShow.includes(41)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("RS H25",42,n&&(instanceIdsToShow.includes(42)||0===instanceIdsToShow.length))),e.push(createInstanceColumn("Other",null,0===instanceIdsToShow.length))),rosterStatsTable=$("#characterStatsTable").DataTable({autoWidth:!1,data:characters,oLanguage:{sSearch:"<abbr title='Fuzzy searching is ON. To search exact text, wrap your search in \"quotes\"'>Search</abbr>"},columns:e,order:[],paging:!1,fixedHeader:{headerOffset:43},drawCallback:function drawCallback(){callRosterStatHandlers()},initComplete:function initComplete(){var e=[colClass,colArchetype,colMainRaidGroup];this.api().columns().every(function(n){var t=this,s=null,i=null;n==colArchetype?(s=$("#archetype_filter"),i=null):n==colClass?(s=$("#class_filter"),i=null):n==colMainRaidGroup&&(s=$("#raid_group_filter"),i=null),e.includes(n)&&(s.on("change",function(){var e=$.fn.dataTable.util.escapeRegex($(this).val());i&&i.val()&&(_readOnlyError("val"),e="(?=.*"+e+")(?=.*"+$.fn.dataTable.util.escapeRegex(i.val())+")"),t.search(e||"",!0,!1).draw()}).change(),i&&i.on("change",function(){var e=$.fn.dataTable.util.escapeRegex($(this).val());s&&s.val()&&(_readOnlyError("val"),e="(?=.*"+e+")(?=.*"+$.fn.dataTable.util.escapeRegex(s.val())+")"),t.search(e||"",!0,!1).draw()}).change())}),callRosterStatHandlers()}}),rosterStatsTable}function addInstanceFilterHandlers(){$("#instance_filter").change(function(){var e=$("#instance_filter").val();for(i=0;i<e.length;i++)e[i]=parseInt(e[i]);instanceIdsToShow=e,initializeTable()})}function getAverageTier(e,n){var t=e.filter(function(e){return e.guild_tier}),s,i;return getTierHtml({guild_tier:t.reduce(function(e,n){return n.guild_tier+e},0)/t.length},n)}function createInstanceColumn(e,n,t){return{className:"width-50 fixed-width pt-0 pl-0 pb-0 pr-1",title:e,data:"name",render:{_:function _(e,t,s){var i="",c=s.prios?s.prios.filter(function(e){return e.instance_id===n}):[],l=c.filter(function(e){return e.is_offspec}).length;c&&c.length?i+='<div class="js-prio-items">\n                            <ul class="list-inline mb-0">\n                                <li class="list-inline-item mr-1 font-weight-bold">'.concat(c.length,"</li>\n                                ").concat(guild.tier_mode?'<li class="list-inline-item mr-1">'.concat(getAverageTier(c,!1),"</li>"):"","\n                                ").concat(l?'<li class="list-inline-item small mr-1 text-muted">'.concat(l,"os</li>"):"","\n                                ").concat(c.length?'<li class="list-inline-item">'.concat(getItemListHtml(c,"prio",s.id,!1,!1),"</li>"):"","\n                            </ul>\n                        </div>"):i+='<div class="js-prio-items text-muted">—</div>';var o=s.received?s.received.filter(function(e){return e.instance_id===n}):[],a=o.filter(function(e){return e.is_offspec}).length;o&&o.length?i+='<div class="js-received-items">\n                            <ul class="list-inline mb-0">\n                                <li class="list-inline-item mr-1 font-weight-bold">'.concat(o.length,"</li>\n                                ").concat(guild.tier_mode?'<li class="list-inline-item mr-1">'.concat(getAverageTier(o,!1),"</li>"):"","\n                                ").concat(a?'<li class="list-inline-item mr-1 small text-muted">'.concat(a,"os</li>"):"","\n                                ").concat(o.length?'<li class="list-inline-item">'.concat(getItemListHtml(o,"received",s.id,!1,!1),"</li>"):"","\n                            </ul>\n                        </div>"):i+='<div class="js-received-items text-muted">—</div>';var d=s.all_wishlists?s.all_wishlists.filter(function(e){return e.instance_id===n&&e.list_number===guild.current_wishlist_number}):[],r=d.filter(function(e){return e.is_offspec}).length;return d&&d.length?i+='<div class="js-wishlist-items">\n                            <ul class="list-inline mb-0">\n                                <li class="list-inline-item mr-1 font-weight-bold">'.concat(d.length,"</li>\n                                ").concat(guild.tier_mode?'<li class="list-inline-item mr-1">'.concat(getAverageTier(d,!1),"</li>"):"","\n                                ").concat(r?'<li class="list-inline-item mr-1 small text-muted">'.concat(r,"os</li>"):"","\n                                ").concat(d.length?'<li class="list-inline-item">'.concat(getItemListHtml(d,"prio",s.id,!1,!1),"</li>"):"","\n                            </ul>\n                        </div>"):i+='<div class="js-wishlist-items text-muted">—</div>',i},sort:function sort(e,t,s){var i=[];return view===VIEW_PRIOS?i=s.prios?s.prios.filter(function(e){return e.instance_id===n}):[]:view===VIEW_RECEIVED?i=s.received?s.received.filter(function(e){return e.instance_id===n}):[]:view===VIEW_WISHLIST&&(i=s.all_wishlists?s.all_wishlists.filter(function(e){return e.instance_id===n&&e.list_number===guild.current_wishlist_number}):[]),i.length}},orderable:!0,visible:t,searchable:!0,type:"num"}}function createInstanceTotalsColumn(e){return{className:"width-50 fixed-width pt-0 pl-0 pb-0 pr-1",title:"Total",data:"name",render:{_:function _(e,n,t){var s="",i=t.prios?t.prios.slice():[],c=t.received?t.received.slice():[],l=t.all_wishlists?t.all_wishlists.filter(function(e){return e.list_number===guild.current_wishlist_number}):[];instanceIdsToShow.length>0&&(i.length>0&&(i=i.filter(function(e){return instanceIdsToShow.includes(e.instance_id)})),c.length>0&&(c=c.filter(function(e){return instanceIdsToShow.includes(e.instance_id)})),l.length>0&&(l=l.filter(function(e){return instanceIdsToShow.includes(e.instance_id)})));var o=i.filter(function(e){return e.is_offspec}).length;i&&i.length?s+='<div class="js-prio-items">\n                            <ul class="list-inline mb-0">\n                                <li class="list-inline-item mr-1 font-weight-bold">'.concat(i.length,"</li>\n                                ").concat(guild.tier_mode?'<li class="list-inline-item mr-1">'.concat(getAverageTier(i,!1),"</li>"):"","\n                                ").concat(o?'<li class="list-inline-item small mr-1 text-muted">'.concat(o,"os</li>"):"","\n                            </ul>\n                        </div>"):s+='<div class="js-prio-items text-muted">—</div>';var a=c.filter(function(e){return e.is_offspec}).length;c&&c.length?s+='<div class="js-received-items">\n                            <ul class="list-inline mb-0">\n                                <li class="list-inline-item mr-1 font-weight-bold">'.concat(c.length,"</li>\n                                ").concat(guild.tier_mode?'<li class="list-inline-item mr-1">'.concat(getAverageTier(c,!1),"</li>"):"","\n                                ").concat(a?'<li class="list-inline-item mr-1 small text-muted">'.concat(a,"os</li>"):"","\n                            </ul>\n                        </div>"):s+='<div class="js-received-items text-muted">—</div>';var d=l.filter(function(e){return e.is_offspec}).length;return l&&l.length?s+='<div class="js-wishlist-items">\n                            <ul class="list-inline mb-0">\n                                <li class="list-inline-item mr-1 font-weight-bold">'.concat(l.length,"</li>\n                                ").concat(guild.tier_mode?'<li class="list-inline-item mr-1">'.concat(getAverageTier(l,!1),"</li>"):"","\n                                ").concat(d?'<li class="list-inline-item mr-1 small text-muted">'.concat(d,"os</li>"):"","\n                            </ul>\n                        </div>"):s+='<div class="js-wishlist-items text-muted">—</div>',s},sort:function sort(e,n,t){var s=[];return view===VIEW_PRIOS?s=t.prios?t.prios:[]:view===VIEW_RECEIVED?s=t.received?t.received:[]:view===VIEW_WISHLIST&&(s=t.all_wishlists?t.all_wishlists.filter(function(e){return e.list_number===guild.current_wishlist_number}):[]),s.length}},orderable:!0,visible:e,searchable:!0,type:"num"}}function createItemSlotColumn(e,n){return{className:"width-50 fixed-width pt-0 pl-0 pb-0 pr-1",title:e,data:function data(e,n,t,s){var data=[];return data="prios"===lootTypeToShow?e.prios||[]:"wishlist"===lootTypeToShow?e.wishlist||[]:e.received||[],instanceIdsToShow.length>0&&data&&(data=data.filter(function(e){return instanceIdsToShow.includes(e.instance_id)})),data},render:{_:function _(e,t,s){var i=n?e.filter(function(e){return n.includes(e.inventory_type)}):e,c=i.filter(function(e){return e.is_offspec}).length;return i&&i.length?'<div class="ml-1">\n                            <ul class="list-inline mb-0">\n                                <li class="list-inline-item mr-1 font-weight-bold">'.concat(i.length,"</li>\n                                ").concat(guild.tier_mode?'<li class="list-inline-item mr-1">'.concat(getAverageTier(i,!1),"</li>"):"","\n                                ").concat(c?'<li class="list-inline-item mr-1 small text-muted">'.concat(c,"os</li>"):"","\n                                ").concat(n&&i.length?'<li class="list-inline-item mr-1">'.concat(getItemListHtml(i,"received",s.id,!1,!1),"</li>"):"","\n                            </ul>\n                        </div>"):'<span class="text-muted">—</span>'},sort:function sort(e,t,s){var i;return(n?e.filter(function(e){return n.includes(e.inventory_type)}):e).length}},orderable:!0,visible:"slots"===view,searchable:!0,type:"num"}}function getItemListHtml(e,n,t){var s=arguments.length>3&&void 0!==arguments[3]&&arguments[3],i=!(arguments.length>4&&void 0!==arguments[4])||arguments[4],c='<ol class="js-item-list list-inline mb-0" data-type="'.concat(n,'" data-id="').concat(t,'" style="').concat(i?"":"display:none;",'">');return $.each(e,function(e,i){var l='data-wowhead-link="https://'.concat(wowheadLocale+wowheadSubdomain,".wowhead.com/item=").concat(i.item_id,'"\n            data-wowhead="item=').concat(i.item_id,"?domain=").concat(wowheadLocale+wowheadSubdomain,'"');c+='\n            <li class="js-has-instance font-weight-normal"\n                data-type="'.concat(n,'"\n                data-id="').concat(t,'"\n                data-offspec="').concat(i.pivot.is_offspec?1:0,'"\n                data-instance-id="').concat(i.instance_id,'"\n                data-wishlist-number="').concat(i.list_number,'"\n                value="').concat(s?i.pivot.order:"",'">\n                ').concat(guild.tier_mode&&i.guild_tier?getTierHtml(i,!0):"",'\n                <a href="/').concat(guild.id,"/").concat(guild.slug,"/i/").concat(i.item_id,"/").concat(slug(i.name),'"\n                    class="small ').concat(i.quality?"q"+i.quality:""," ").concat(!i.pivot.is_received||"wishlist"!=i.pivot.type&&"prio"!=i.pivot.type?"":"font-strikethrough",'"\n                    ').concat(l,">\n                    ").concat(i.name,"\n                </a>\n                ").concat(i.pivot.is_offspec?'<span title="offspec item" class="small font-weight-bold text-muted">OS</span>':"",'\n                <span class="js-watchable-timestamp js-timestamp-title smaller text-muted"\n                    data-timestamp="').concat(i.pivot.received_at?i.pivot.received_at:i.pivot.created_at,'"\n                    data-title="added by ').concat(i.added_by_username,' at"\n                    data-is-short="1">\n                </span>\n            </li>')}),c+="</ol>"}function getNotes(e,n,t){return(t.public_note?'<span class="js-markdown-inline small">'.concat(DOMPurify.sanitize(nl2br(t.public_note)),"</span>"):"—")+(t.officer_note?'<br><small class="font-weight-medium font-italic text-gold">Officer\'s Note</small><br><span class="js-markdown-inline small">'.concat(DOMPurify.sanitize(nl2br(t.officer_note)),"</span>"):"")}function getRaidGroupHtml(e,n){return e?'<span class="small font-weight-light d-inline">\n            <span class="role-circle-small" style="background-color:'.concat(getColorFromDec(parseInt(n)),'"></span>\n                ').concat(e,"\n            </span>"):""}function getTierHtml(e,n){return'<span class="text-monospace small font-weight-normal text-'.concat(n&&e.guild_tier?"tier-"+Math.ceil(e.guild_tier):"muted",'">').concat(e.guild_tier?getItemTierLabel(e,guild.tier_mode):"&nbsp;","</span>")}function hideOffspecItems(){$("[data-offspec='1']").hide()}function showOffspecItems(){$("[data-offspec='1']").show()}function hideStrikethroughItems(){$("[data-type='prio']").children(".font-strikethrough").parent().hide(),$("[data-type='wishlist']").children(".font-strikethrough").parent().hide()}function showStrikethroughItems(){$("[data-type='prio']").children(".font-strikethrough").parent().show(),$("[data-type='wishlist']").children(".font-strikethrough").parent().show()}function toggleInstanceCols(e){if(1===guild.expansion_id){var n=[colInstanceTotal];(instanceIdsToShow.includes(colInstance1)||0===instanceIdsToShow.length)&&n.push(colInstance1),(instanceIdsToShow.includes(colInstance2)||0===instanceIdsToShow.length)&&n.push(colInstance2),(instanceIdsToShow.includes(colInstance3)||0===instanceIdsToShow.length)&&n.push(colInstance3),(instanceIdsToShow.includes(colInstance4)||0===instanceIdsToShow.length)&&n.push(colInstance4),(instanceIdsToShow.includes(colInstance5)||0===instanceIdsToShow.length)&&n.push(colInstance5),(instanceIdsToShow.includes(colInstance6)||0===instanceIdsToShow.length)&&n.push(colInstance6),(instanceIdsToShow.includes(colInstance7)||0===instanceIdsToShow.length)&&n.push(colInstance7),(instanceIdsToShow.includes(colInstance8)||0===instanceIdsToShow.length)&&n.push(colInstance8),(instanceIdsToShow.includes(colInstance9)||0===instanceIdsToShow.length)&&n.push(colInstance9),table.columns(n).visible(e)}else if(2===guild.expansion_id){var t=[colInstanceTotal];(instanceIdsToShow.includes(colInstance1)||0===instanceIdsToShow.length)&&t.push(colInstance1),(instanceIdsToShow.includes(colInstance2)||0===instanceIdsToShow.length)&&t.push(colInstance2),(instanceIdsToShow.includes(colInstance3)||0===instanceIdsToShow.length)&&t.push(colInstance3),(instanceIdsToShow.includes(colInstance4)||0===instanceIdsToShow.length)&&t.push(colInstance4),(instanceIdsToShow.includes(colInstance5)||0===instanceIdsToShow.length)&&t.push(colInstance5),(instanceIdsToShow.includes(colInstance6)||0===instanceIdsToShow.length)&&t.push(colInstance6),(instanceIdsToShow.includes(colInstance7)||0===instanceIdsToShow.length)&&t.push(colInstance7),(instanceIdsToShow.includes(colInstance8)||0===instanceIdsToShow.length)&&t.push(colInstance8),(instanceIdsToShow.includes(colInstance9)||0===instanceIdsToShow.length)&&t.push(colInstance9),(instanceIdsToShow.includes(colInstance10)||0===instanceIdsToShow.length)&&t.push(colInstance10),(instanceIdsToShow.includes(colInstance11)||0===instanceIdsToShow.length)&&t.push(colInstance11),table.columns(t).visible(e)}else if(3===guild.expansion_id){var s=[colInstanceTotal];(instanceIdsToShow.includes(19)||0===instanceIdsToShow.length)&&s.push(colInstance1),(instanceIdsToShow.includes(20)||0===instanceIdsToShow.length)&&s.push(colInstance2),(instanceIdsToShow.includes(21)||0===instanceIdsToShow.length)&&s.push(colInstance3),(instanceIdsToShow.includes(22)||0===instanceIdsToShow.length)&&s.push(colInstance4),(instanceIdsToShow.includes(23)||0===instanceIdsToShow.length)&&s.push(colInstance5),(instanceIdsToShow.includes(24)||0===instanceIdsToShow.length)&&s.push(colInstance6),(instanceIdsToShow.includes(25)||0===instanceIdsToShow.length)&&s.push(colInstance7),(instanceIdsToShow.includes(26)||0===instanceIdsToShow.length)&&s.push(colInstance8),(instanceIdsToShow.includes(27)||0===instanceIdsToShow.length)&&s.push(colInstance9),(instanceIdsToShow.includes(28)||0===instanceIdsToShow.length)&&s.push(colInstance10),(instanceIdsToShow.includes(29)||0===instanceIdsToShow.length)&&s.push(colInstance11),(instanceIdsToShow.includes(30)||0===instanceIdsToShow.length)&&s.push(colInstance12),(instanceIdsToShow.includes(31)||0===instanceIdsToShow.length)&&s.push(colInstance13),(instanceIdsToShow.includes(32)||0===instanceIdsToShow.length)&&s.push(colInstance14),(instanceIdsToShow.includes(33)||0===instanceIdsToShow.length)&&s.push(colInstance15),(instanceIdsToShow.includes(34)||0===instanceIdsToShow.length)&&s.push(colInstance16),(instanceIdsToShow.includes(35)||0===instanceIdsToShow.length)&&s.push(colInstance17),(instanceIdsToShow.includes(36)||0===instanceIdsToShow.length)&&s.push(colInstance18),(instanceIdsToShow.includes(37)||0===instanceIdsToShow.length)&&s.push(colInstance19),(instanceIdsToShow.includes(38)||0===instanceIdsToShow.length)&&s.push(colInstance20),(instanceIdsToShow.includes(39)||0===instanceIdsToShow.length)&&s.push(colInstance21),(instanceIdsToShow.includes(40)||0===instanceIdsToShow.length)&&s.push(colInstance22),(instanceIdsToShow.includes(41)||0===instanceIdsToShow.length)&&s.push(colInstance23),(instanceIdsToShow.includes(42)||0===instanceIdsToShow.length)&&s.push(colInstance24),table.columns(s).visible(e)}}function toggleSlotCols(e){table.columns([colSlotTotal,colSlotHead,colSlotNeck,colSlotShoulder,colSlotBack,colSlotChest,colSlotWrists,colSlotHands,colSlotWaist,colSlotLegs,colSlotFeet,colSlotFinger,colSlotTrinket,colSlotWeapon,colSlotOffhand,colSlotRanged,colSlotOther]).visible(e)}function callRosterStatHandlers(){rosterHandlersTimeout&&clearTimeout(rosterHandlersTimeout),rosterHandlersTimeout=setTimeout(function(){makeWowheadLinks(),parseMarkdown(),trackTimestamps()},500)}$(document).ready(function(){view=window.location.hash.substring(1),[VIEW_PRIOS,VIEW_RECEIVED,VIEW_SLOTS,VIEW_WISHLIST].includes(view)||(view=VIEW_SLOTS),initializeTable(),$(".js-toggle-column").click(function(e){e.preventDefault();var n=table.column($(this).attr("data-column"));n.visible(!n.visible())}),$(".js-show-prio-cols").click(function(e){e.preventDefault(),view=VIEW_PRIOS,window.location="#"+VIEW_PRIOS,$(".js-toggle-column-set").removeClass("disabled"),$(this).addClass("disabled"),toggleInstanceCols(!0),toggleSlotCols(!1),$("#loot_type_container").hide(),$(".js-prio-items").show(),$(".js-received-items").hide(),$(".js-wishlist-items").hide()}),$(".js-show-received-cols").click(function(e){e.preventDefault(),view=VIEW_RECEIVED,window.location="#"+VIEW_RECEIVED,$(".js-toggle-column-set").removeClass("disabled"),$(this).addClass("disabled"),toggleInstanceCols(!0),toggleSlotCols(!1),$("#loot_type_container").hide(),$(".js-prio-items").hide(),$(".js-received-items").show(),$(".js-wishlist-items").hide()}),$(".js-show-slot-cols").click(function(e){e.preventDefault(),view=VIEW_SLOTS,window.location="#"+VIEW_SLOTS,$(".js-toggle-column-set").removeClass("disabled"),$(this).addClass("disabled"),$("#loot_type_container").show(),toggleInstanceCols(!1),toggleSlotCols(!0)}),$(".js-show-wishlist-cols").click(function(e){e.preventDefault(),view=VIEW_WISHLIST,window.location="#"+VIEW_WISHLIST,$(".js-toggle-column-set").removeClass("disabled"),$(this).addClass("disabled"),toggleInstanceCols(!0),toggleSlotCols(!1),$("#loot_type_container").hide(),$(".js-prio-items").hide(),$(".js-received-items").hide(),$(".js-wishlist-items").show()}),$(".js-hide-strikethrough-items").click(function(){strikethroughVisible?(strikethroughVisible=!1,hideStrikethroughItems()):(strikethroughVisible=!0,showStrikethroughItems())}),table.on("column-visibility.dt",function(e,n,t,s){callRosterStatHandlers()}),$(".js-hide-offspec-items").click(function(){offspecVisible?(offspecVisible=!1,hideOffspecItems()):(offspecVisible=!0,showOffspecItems())}),$(".js-show-all-items").click(function(){allItemsVisible?($(".js-item-list").hide(),allItemsVisible=!1):($(".js-item-list").show(),allItemsVisible=!0)}),$("#loot_type").change(function(){$(this).val()!=lootTypeToShow&&(lootTypeToShow=$(this).val(),initializeTable())}),$(".selectpicker").selectpicker("refresh"),$(".loadingBarContainer").removeClass("d-flex").hide(),$("#characterStatsTable").show(),$("#characterStatsTableFilters").show(),callRosterStatHandlers(),addInstanceFilterHandlers()});
