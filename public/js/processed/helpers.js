var ARCHETYPE_DPS="DPS",ARCHETYPE_HEAL="Heal",ARCHETYPE_TANK="Tank",TIER_MODE_NUM="num",TIER_MODE_S="s",TIERS={1:"S",2:"A",3:"B",4:"C",5:"D",6:"F"},SLOT_MISC=0,SLOT_HEAD=1,SLOT_NECK=2,SLOT_SHOULDERS=3,SLOT_SHIRT=4,SLOT_CHEST_1=5,SLOT_WAIST=6,SLOT_LEGS=7,SLOT_FEET=8,SLOT_WRIST=9,SLOT_HANDS=10,SLOT_FINGER=11,SLOT_TRINKET=12,SLOT_WEAPON_MAIN_HAND=13,SLOT_SHIELD=14,SLOT_RANGED_1=15,SLOT_BACK=16,SLOT_WEAPON_TWO_HAND=17,SLOT_BAG=18,SLOT_CHEST_2=20,SLOT_WEAPON_ONE_HAND=21,SLOT_WEAPON_OFF_HAND=22,SLOT_OFFHAND=23,SLOT_AMMO=24,SLOT_THROWN=25,SLOT_RANGED_2=26,SLOT_RELIC=28,WARCRAFTLOGS_CLASSES={1:{id:1,name:"Death Knight",slug:"dk",specs:{1:{id:1,name:"Blood"},2:{id:2,name:"Frost"},3:{id:3,name:"Unholy"}}},2:{id:2,name:"Druid",slug:"druid",specs:{1:{id:1,name:"Balance"},2:{id:2,name:"Feral"},3:{id:3,name:"Guardian"},4:{id:4,name:"Restoration"}}},3:{id:3,name:"Hunter",slug:"hunter",specs:{1:{id:1,name:"Beast Mastery"},2:{id:2,name:"Marksmanship"},3:{id:3,name:"Survival"}}},4:{id:4,name:"Mage",slug:"mage",specs:{1:{id:1,name:"Arcane"},2:{id:2,name:"Fire"},3:{id:3,name:"Frost"}}},5:{id:5,name:"Monk",slug:"monk",specs:{1:{id:1,name:"Brewmaster"},2:{id:2,name:"Mistweaver"},3:{id:3,name:"Windwalker"}}},6:{id:6,name:"Paladin",slug:"paladin",specs:{1:{id:1,name:"Holy"},2:{id:2,name:"Protection"},3:{id:3,name:"Retribution"}}},7:{id:7,name:"Priest",slug:"priest",specs:{1:{id:1,name:"Discipline"},2:{id:2,name:"Holy"},3:{id:3,name:"Shadow"}}},8:{id:8,name:"Rogue",slug:"rogue",specs:{1:{id:1,name:"Assassination"},2:{id:2,name:"Combat"},3:{id:3,name:"Subtlety"},4:{id:4,name:"Outlaw"}}},9:{id:9,name:"Shaman",slug:"shaman",specs:{1:{id:1,name:"Elemental"},2:{id:2,name:"Enhancement"},3:{id:3,name:"Restoration"}}},10:{id:10,name:"Warlock",slug:"warlock",specs:{1:{id:1,name:"Affliction"},2:{id:2,name:"Demonology"},3:{id:3,name:"Destruction"}}},11:{id:11,name:"Warrior",slug:"warrior",specs:{1:{id:1,name:"Arms"},2:{id:2,name:"Fury"},3:{id:3,name:"Protection"},4:{id:4,name:"Gladiator"}}},12:{id:12,name:"Demon Hunter",slug:"dh",specs:{1:{id:1,name:"Havoc"},2:{id:2,name:"Vengeance"}}}},timestampCheckRate=3e4,timestampUpdateInterval=null;function addDateInputHandlers(){$(".js-date-input").change(function(){var e=$(this).prev(".js-date");$(this).val()?e.val(moment($(this).val()).utc().format("YYYY-MM-DD HH:mm:ss")):(e.val(date),$(this).val(moment.utc(date).local().format("YYYY-MM-DD HH:mm:ss")))})}function addInputAntiSubmitHandler(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:":text";$(e).on("keypress keyup",function(e){if(13==e.which)return!1})}function addNestedDropdownSupport(){var e;$.fn.dropdown=(e=$.fn.dropdown,function(t){"string"==typeof t&&"toggle"===t&&($(".has-child-dropdown-show").removeClass("has-child-dropdown-show"),$(this).closest(".dropdown").parents(".dropdown").addClass("has-child-dropdown-show"));var a=e.call($(this),t);return $(this).off("click.bs.dropdown"),a}),$(function(){$('.dropdown [data-toggle="dropdown"]').on("click",function(e){$(this).dropdown("toggle"),e.stopPropagation()}),$(".dropdown").on("hide.bs.dropdown",function(e){$(this).is(".has-child-dropdown-show")&&($(this).removeClass("has-child-dropdown-show"),e.preventDefault()),e.stopPropagation()})})}function addNoteHandlers(){$(".js-show-note-edit").click(function(){$(".js-note-input").toggle()})}function addSortHandlers(){$(".js-sortable").sortable({handle:".js-sort-handle"}),$(".js-sortable-lazy").one("mouseenter",function(){$(this).sortable({handle:".js-sort-handle"})})}function addTooltips(){$("span").tooltip(),$("abbr").tooltip(),$("a").tooltip()}function addWishlistSortHandlers(){$(".js-sort-wishlists").click(function(){$(".js-wishlist-unsorted").toggle(),$(".js-wishlist-sorted").toggle()})}function configureMoment(){moment.relativeTimeThreshold("ss",40),moment.relativeTimeThreshold("s",60),moment.relativeTimeThreshold("m",60),moment.relativeTimeThreshold("h",49),moment.relativeTimeThreshold("d",93),moment.relativeTimeThreshold("M",25),moment.relativeTimeRounding(Math.floor)}function cleanUrl(e,t,a){if(e){try{var n=decodeURIComponent(unescape(a)).replace(/[^\w:]/g,"").toLowerCase()}catch(e){return null}if(0===n.indexOf("javascript:")||0===n.indexOf("vbscript:")||0===n.indexOf("data:"))return null}t&&!originIndependentUrl.test(a)&&(a=resolveUrl(t,a));try{a=encodeURI(a).replace(/%25/g,"%")}catch(e){return null}return a}function decToHex(e){return parseInt(e).toString(16)}function flashElement(e){e.fadeTo(100,.3,function(){$(this).fadeTo(500,1)})}function getArchetypeIcon(e){return e===ARCHETYPE_DPS?"fas fa-fw fa-bow-arrow text-dps":e===ARCHETYPE_HEAL?"fas fa-fw fa-plus-circle text-healer":e===ARCHETYPE_TANK?"fas fa-fw fa-shield text-tank":"fas fa-fw fa-map-marker-question text-muted"}function getAttendanceColor(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,t="";return t=e>=.95?"text-tier-1":e>=.9?"text-tier-2":e>=.8?"text-tier-3":e>=.7?"text-tier-4":"text-tier-5"}function getColorFromDec(e){if(e=parseInt(e))for(e=decToHex(e);e.length<6;)e="0"+e;else e="FFF";return"#"+e}function getItemTierLabel(e,t){return e.guild_tier?t==TIER_MODE_S?numToSTier(e.guild_tier):e.guild_tier:""}function makeWowheadLinks(){try{$WowheadPower.refreshLinks()}catch(e){console.log("Failed to refresh wowhead links.")}}function nl2br(e){return e?e.replace(/\n/g,"<br>"):""}function numToSTier(e){return e>0?(tiers=TIERS,whole=Math.floor(e),decimal=e-whole,affix="",decimal>.66?affix="++":decimal>.33&&(affix="+"),tiers[Math.ceil(e)]+affix):""}function parseMarkdown(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null,t=new marked.Renderer;t.link=function(e,t,a){var n="";return null===(e=cleanUrl(this.options.sanitize,this.options.baseUrl,e))?a:(n+='<a target="_blank" href="'+e+'"',t&&(n+=' title="'+t+'"'),n+=">"+a+"</a>")},e&&!e.hasClass("js-markdown-parsed")?(e.html(marked(DOMPurify.sanitize(e.html()),{renderer:t})),e.addClass("js-markdown-parsed")):($(".js-markdown").each(function(){if(!$(this).hasClass("js-markdown-parsed")){var e=DOMPurify.sanitize($.trim($(this).text()));$(this).html(marked(e),{renderer:t}),$(this).addClass("js-markdown-parsed")}}),$(".js-markdown-inline").each(function(){if(!$(this).hasClass("js-markdown-parsed")){var e=DOMPurify.sanitize($.trim($(this).text()));$(this).html(marked.parseInline(e),{renderer:t}),$(this).addClass("js-markdown-parsed")}}))}function rgbToHex(e){for(var t=Number(e).toString(16);t.length<6;)t="0"+t;return t}function slug(e){var t="-",a="àáäâãåăæçèéëêǵḧìíïîḿńǹñòóöôœṕŕßśșțùúüûǘẃẍÿź·/_,:;",n="aaaaaaaaceeeeghiiiimnnnoooooprssstuuuuuwxyz------",i=new RegExp(a.split("").join("|"),"g"),o=e.toString().toLowerCase().replace(/\s+/g,"-").replace(i,function(e){return n.charAt(a.indexOf(e))}).replace(/&/g,"").replace(/[^\w\-]+/g,"").replace(/\-\-+/g,"-").replace(/^-+/,"").replace(/-+$/,"").replace(/-+/g,"-").substr(0,50);return o||"-"}function trackTimestamps(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:timestampCheckRate;$(".js-watchable-timestamp").each(function(){var e=$(this).data("isShort");locale&&moment.locale(locale),!e||locale&&"en"!==locale||moment.locale("en",{relativeTime:{past:"%s ago",s:"just now",ss:"%ss",m:"%dm",mm:"%dm",h:"%dh",hh:"%dh",d:"%dd",dd:"%dd",M:"%dmo",MM:"%dmo",y:"%dy",yy:"%dy"}});var t=$(this).data("timestamp");t<1e12&&(t*=1e3);var a=!1;t>Date.now()&&(a=!0);var n=null,i=$(this).data("maxDays");n=i&&t<moment().valueOf()-864e5*i?"over 2 weeks":moment.utc(t).fromNow(!0),$(this).is("abbr")?$(this).prop("title",(a?"in ":"")+n+(a?"":" ago")):$(this).html(n)}),$(".js-timestamp").each(function(){var e=$(this).data("timestamp");e<1e12&&(e*=1e3);var t=$(this).data("format")?$(this).data("format"):"ddd, MMM Do YYYY @ h:mm a",a=moment.utc(e).local().format(t);$(this).is("abbr")?$(this).prop("title",a):$(this).html(a)}),$(".js-timestamp-title").each(function(){var e=$(this).data("title"),t=$(this).data("timestamp");t<1e12&&(t*=1e3);var a=moment.utc(t).local().format("ddd, MMM Do YYYY @ h:mm a");e?$(this).prop("title",e+" "+a):$(this).prop("title",a)}),timestampUpdateInterval&&clearInterval(timestampUpdateInterval),timestampUpdateInterval=setInterval(function(){$(".js-watchable-timestamp").each(function(){var e=$(this).data("timestamp");e<1e12&&(e*=1e3);var t=!1;e>Date.now()&&(t=!0);var a=null,n=$(this).data("maxDays");a=n&&e<moment().valueOf()-864e5*n?"over 2 weeks":moment.utc(e).fromNow(!0),$(this).is("abbr")?$(this).prop("title",(t?"in ":"")+a+(t?"":" ago")):$(this).html(a)})},e)}function ucfirst(e){return e.charAt(0).toUpperCase()+e.slice(1)}function warnBeforeLeaving(e){$(e).one("change",function(){window.onbeforeunload=function(){return!0}}),$(e).one("submit",function(){window.onbeforeunload=function(){}})}$.ajaxSetup({headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}}),marked.setOptions({gfm:!0,breaks:!0}),configureMoment(),$(document).ready(function(){addNestedDropdownSupport(),parseMarkdown(),makeWowheadLinks(),trackTimestamps(),addDateInputHandlers(),addInputAntiSubmitHandler(),addNoteHandlers(),addWishlistSortHandlers(),$(".js-edit-content").click(function(e){e.preventDefault();var t=$(this).data("id");$(".js-content[data-id="+t+"]").toggle()}),addTooltips()});
