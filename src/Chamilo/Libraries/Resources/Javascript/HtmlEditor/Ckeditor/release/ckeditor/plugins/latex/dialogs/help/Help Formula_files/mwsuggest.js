﻿var os_map={},os_cache={},os_cur_keypressed=0,os_keypressed_count=0,os_timer=null,os_mouse_pressed=!1,os_mouse_num=-1,os_mouse_moved=!1,os_search_timeout=250,os_autoload_inputs=["searchInput","searchInput2","powerSearchText","searchText"],os_autoload_forms=["searchform","searchform2","powersearch","search"],os_is_stopped=!1,os_max_lines_per_suggest=7,os_animation_steps=6,os_animation_min_step=2,os_animation_delay=30,os_container_max_width=2,os_animation_timer=null;
function os_Timer(a,b,c){this.id=a;this.r=b;this.query=c}function os_AnimationTimer(a,b){this.r=a;var c=document.getElementById(a.container).offsetWidth;this.inc=Math.round((b-c)/os_animation_steps);this.inc<os_animation_min_step&&0<=this.inc&&(this.inc=os_animation_min_step);this.inc>-os_animation_min_step&&0>this.inc&&(this.inc=-os_animation_min_step);this.target=b}
function os_Results(a,b){this.searchform=b;this.searchbox=a;this.container=a+"Suggest";this.resultTable=a+"Result";this.resultText=a+"ResultText";this.toggle=a+"Toggle";this.results=this.query=null;this.resultCount=0;this.original=null;this.selected=-1;this.containerTotal=this.containerRow=this.containerCount=0;this.stayHidden=this.visible=!1}function os_hideResults(a){var b=document.getElementById(a.container);null!=b&&(b.style.visibility="hidden");a.visible=!1;a.selected=-1}
function os_showResults(a){if(!os_is_stopped&&!a.stayHidden){os_fitContainer(a);var b=document.getElementById(a.container);a.selected=-1;null!=b&&(b.scrollTop=0,b.style.visibility="visible",a.visible=!0)}}function os_operaWidthFix(){return"string"!=typeof document.body.style.overflowX?30:0}function os_encodeQuery(a){return encodeURIComponent?encodeURIComponent(a):escape?escape(a):null}function os_decodeValue(a){return decodeURIComponent?decodeURIComponent(a):unescape?unescape(a):null}
function f_clientWidth(){return f_filterResults(window.innerWidth?window.innerWidth:0,document.documentElement?document.documentElement.clientWidth:0,document.body?document.body.clientWidth:0)}function f_clientHeight(){return f_filterResults(window.innerHeight?window.innerHeight:0,document.documentElement?document.documentElement.clientHeight:0,document.body?document.body.clientHeight:0)}
function f_scrollLeft(){return f_filterResults(window.pageXOffset?window.pageXOffset:0,document.documentElement?document.documentElement.scrollLeft:0,document.body?document.body.scrollLeft:0)}function f_scrollTop(){return f_filterResults(window.pageYOffset?window.pageYOffset:0,document.documentElement?document.documentElement.scrollTop:0,document.body?document.body.scrollTop:0)}function f_filterResults(a,b,c){a=a?a:0;if(b&&(!a||a>b))a=b;return c&&(!a||a>c)?c:a}
function os_availableHeight(a){var a=document.getElementById(a.container).style.top,b=a.lastIndexOf("px");0<b&&(a=a.substring(0,b));return f_clientHeight()-(a-f_scrollTop())}function os_getElementPosition(a){for(var a=document.getElementById(a),b=0,c=0;a;)b+=a.offsetLeft,c+=a.offsetTop,a=a.offsetParent;-1!=navigator.userAgent.indexOf("Mac")&&"undefined"!=typeof document.body.leftMargin&&(b+=document.body.leftMargin,c+=document.body.topMargin);return{left:b,top:c}}
function os_createContainer(a){var b=document.createElement("div"),c=document.getElementById(a.searchbox),d=os_getElementPosition(a.searchbox),e=d.left,d=d.top+c.offsetHeight;b.className="os-suggest";b.setAttribute("id",a.container);document.body.appendChild(b);b=document.getElementById(a.container);b.style.top=d+"px";b.style.left=e+"px";b.style.width=c.offsetWidth+"px";b.onmouseover=function(b){os_eventMouseover(a.searchbox,b)};b.onmousemove=function(b){os_eventMousemove(a.searchbox,b)};b.onmousedown=
function(b){return os_eventMousedown(a.searchbox,b)};b.onmouseup=function(b){os_eventMouseup(a.searchbox,b)};return b}function os_fitContainer(a){var b=document.getElementById(a.container),c=os_availableHeight(a)-20,d=a.containerRow,c=parseInt(c/d)*d;c<2*d&&1<a.resultCount&&(c=2*d);c/d>os_max_lines_per_suggest&&(c=d*os_max_lines_per_suggest);c<a.containerTotal?(b.style.height=c+"px",a.containerCount=parseInt(Math.round(c/d))):(b.style.height=a.containerTotal+"px",a.containerCount=a.resultCount)}
function os_trimResultText(a){for(var b=0,c=0;c<a.resultCount;c++){var d=document.getElementById(a.resultText+c);d.offsetWidth>b&&(b=d.offsetWidth)}var e=document.getElementById(a.container).offsetWidth,c=0,c=a.containerCount<a.resultCount?20:os_operaWidthFix(e);4>c&&(c=4);b+=c;d=document.getElementById(a.searchbox).offsetWidth;b/=d;b>os_container_max_width?b=os_container_max_width:1>b&&(b=1);d=Math.round(d*b);e!=d&&(e=d,null!=os_animation_timer&&clearInterval(os_animation_timer.id),os_animation_timer=
new os_AnimationTimer(a,e),os_animation_timer.id=setInterval("os_animateChangeWidth()",os_animation_delay),e-=c);if(!(10>e))for(c=0;c<a.resultCount;c++){for(var d=document.getElementById(a.resultText+c),b=1,f=d.offsetWidth+1,g=0,h=!1;d.offsetWidth>e&&(d.offsetWidth<f||2>g);){var h=!0,f=d.offsetWidth,j=d.innerHTML;d.innerHTML=j.substring(0,j.length-b)+"...";g++;b=4}h&&document.getElementById(a.resultTable+c).setAttribute("title",a.results[c])}}
function os_animateChangeWidth(){var a=os_animation_timer.r,b=document.getElementById(a.container),c=b.offsetWidth,d=document.getElementById(a.searchbox).offsetWidth,a=os_getElementPosition(a.searchbox).left,e=os_animation_timer.inc,f=os_animation_timer.target,c=c+e;0<e&&c>=f||0>=e&&c<=f?(b.style.width=f+"px",clearInterval(os_animation_timer.id),os_animation_timer=null):(b.style.width=c+"px","rtl"==document.documentElement.dir&&(b.style.left=a+d+(f-c)-os_animation_timer.target-1+"px"))}
function os_updateResults(a,b,c,d){os_cache[d]=c;a.query=b;a.original=b;if(""==c)a.results=null,a.resultCount=0,os_hideResults(a);else try{var e=eval("("+c+")");if(2>e.length||0==e[1].length)a.results=null,a.resultCount=0,os_hideResults(a);else{var f=document.getElementById(a.container);null==f&&(f=os_createContainer(a));f.innerHTML=os_createResultTable(a,e[1]);var g=document.getElementById(a.resultTable);a.containerTotal=g.offsetHeight;a.containerRow=g.offsetHeight/a.resultCount;os_fitContainer(a);
os_trimResultText(a);os_showResults(a)}}catch(h){os_hideResults(a),os_cache[d]=null}}
function os_createResultTable(a,b){var c=document.getElementById(a.container),c=c.offsetWidth-os_operaWidthFix(c.offsetWidth),c='<table class="os-suggest-results" id="'+a.resultTable+'" style="width: '+c+'px;">';a.results=[];a.resultCount=b.length;for(i=0;i<b.length;i++){var d=os_decodeValue(b[i]);a.results[i]=d;c+='<tr><td class="os-suggest-result" id="'+a.resultTable+i+'"><span id="'+a.resultText+i+'">'+d+"</span></td></tr>"}return c+"</table>"}
function os_getNamespaces(a){var b="",a=document.forms[a.searchform].elements;for(i=0;i<a.length;i++){var c=a[i].name;if("undefined"!=typeof c&&2<c.length&&"n"==c[0]&&"s"==c[1]&&("checkbox"==a[i].type&&a[i].checked||"hidden"==a[i].type&&"1"==a[i].value))""!=b&&(b+="|"),b+=c.substring(2)}""==b&&(b=wgSearchNamespaces.join("|"));return b}function os_updateIfRelevant(a,b,c,d){var e=document.getElementById(a.searchbox);null!=e&&e.value==b&&os_updateResults(a,b,c,d);a.query=b}
function os_delayedFetch(){if(null!=os_timer){var a=os_timer.r,b=os_timer.query;os_timer=null;var c=wgMWSuggestTemplate.replace("{namespaces}",os_getNamespaces(a)).replace("{dbname}",wgDBname).replace("{searchTerms}",os_encodeQuery(b)),d=os_cache[c];if(null!=d)os_updateIfRelevant(a,b,d,c);else{var e=sajax_init_object();if(e)try{e.open("GET",c,!0),e.onreadystatechange=function(){4==e.readyState&&"function"==typeof os_updateIfRelevant&&os_updateIfRelevant(a,b,e.responseText,c)},e.send(null)}catch(f){throw"localhost"==
window.location.hostname&&alert("Your browser blocks XMLHttpRequest to 'localhost', try using a real hostname for development/testing."),f;}}}}function os_fetchResults(a,b,c){""==b?(a.query="",os_hideResults(a)):b!=a.query&&(os_is_stopped=!1,null!=os_timer&&null!=os_timer.id&&clearTimeout(os_timer.id),0!=c?os_timer=new os_Timer(setTimeout("os_delayedFetch()",c),a,b):(os_timer=new os_Timer(null,a,b),os_delayedFetch()))}
function os_changeHighlight(a,b,c,d){c>=a.resultCount&&(c=a.resultCount-1);-1>c&&(c=-1);a.selected=c;if(b!=c){0<=b&&(b=document.getElementById(a.resultTable+b),null!=b&&(b.className="os-suggest-result"));0<=c?(b=document.getElementById(a.resultTable+c),null!=b&&(b.className=os_HighlightClass()),b=a.results[c]):b=a.original;if(a.containerCount<a.resultCount){var e=document.getElementById(a.container),f=e.scrollTop/a.containerRow,g=f+a.containerCount;c<f?e.scrollTop=c*a.containerRow:c>=g&&(e.scrollTop=
(c-a.containerCount+1)*a.containerRow)}d&&os_updateSearchQuery(a,b)}}function os_HighlightClass(){var a=navigator.userAgent.match(/AppleWebKit\/(\d+)/);return a&&523>parseInt(a[1])?"os-suggest-result-hl-webkit":"os-suggest-result-hl"}function os_updateSearchQuery(a,b){document.getElementById(a.searchbox).value=b;a.query=b}function os_getTarget(a){a||(a=window.event);return a.target?a.target:a.srcElement?a.srcElement:null}
function os_eventKeyup(a){var a=os_getTarget(a),b=os_map[a.id];null!=b&&(0==os_keypressed_count&&os_processKey(b,os_cur_keypressed,a),os_fetchResults(b,a.value,os_search_timeout))}
function os_processKey(a,b,c){40==b?a.visible?os_changeHighlight(a,a.selected,a.selected+1,!0):null==os_timer&&(a.query="",os_fetchResults(a,c.value,0)):38==b?a.visible&&os_changeHighlight(a,a.selected,a.selected-1,!0):27==b?(document.getElementById(a.searchbox).value=a.original,a.query=a.original,os_hideResults(a)):document.getElementById(a.searchbox)}
function os_eventKeypress(a){var a=os_getTarget(a),b=os_map[a.id];if(null!=b){var c=os_cur_keypressed;os_keypressed_count++;os_processKey(b,c,a)}}function os_eventKeydown(a){a||(a=window.event);var b=os_getTarget(a);null!=os_map[b.id]&&(os_mouse_moved=!1,os_cur_keypressed=void 0==a.keyCode?a.which:a.keyCode,os_keypressed_count=0)}
function os_eventBlur(a){a=os_getTarget(a);a=os_map[a.id];null!=a&&!os_mouse_pressed&&(os_hideResults(a),a.stayHidden=!0,null!=os_timer&&null!=os_timer.id&&clearTimeout(os_timer.id),os_timer=null)}function os_eventFocus(a){a=os_getTarget(a);a=os_map[a.id];null!=a&&(a.stayHidden=!1)}function os_eventMouseover(a,b){var c=os_getTarget(b),d=os_map[a];null!=d&&os_mouse_moved&&(c=os_getNumberSuffix(c.id),0<=c&&os_changeHighlight(d,d.selected,c,!1))}
function os_getNumberSuffix(a){a=a.substring(a.length-2);"0"<=a.charAt(0)&&"9">=a.charAt(0)||(a=a.substring(1));return os_isNumber(a)?parseInt(a):-1}function os_eventMousemove(){os_mouse_moved=!0}function os_eventMousedown(a,b){var c=os_getTarget(b),d=os_map[a];if(null!=d)return c=os_getNumberSuffix(c.id),os_mouse_pressed=!0,0<=c&&(os_mouse_num=c),document.getElementById(d.searchbox).focus(),!1}
function os_eventMouseup(a,b){var c=os_getTarget(b),d=os_map[a];null!=d&&(c=os_getNumberSuffix(c.id),0<=c&&os_mouse_num==c&&(os_updateSearchQuery(d,d.results[c]),os_hideResults(d),document.getElementById(d.searchform).submit()),os_mouse_pressed=!1,document.getElementById(d.searchbox).focus())}function os_isNumber(a){if(""==a||isNaN(a))return!1;for(var b=0;b<a.length;b++){var c=a.charAt(b);if(!("0"<=c&&"9">=c))return!1}return!0}
function os_eventOnsubmit(a){a=os_getTarget(a);os_is_stopped=!0;null!=os_timer&&null!=os_timer.id&&(clearTimeout(os_timer.id),os_timer=null);for(i=0;i<os_autoload_inputs.length;i++){var b=os_map[os_autoload_inputs[i]];if(null!=b){var c=document.getElementById(b.searchform);null!=c&&c==a&&(b.query=document.getElementById(b.searchbox).value);os_hideResults(b)}}return!0}function os_hookEvent(a,b,c){a.addEventListener?a.addEventListener(b,c,!1):window.attachEvent&&a.attachEvent("on"+b,c)}
function os_initHandlers(a,b,c){var d=new os_Results(a,b);os_hookEvent(c,"keyup",function(a){os_eventKeyup(a)});os_hookEvent(c,"keydown",function(a){os_eventKeydown(a)});os_hookEvent(c,"keypress",function(a){os_eventKeypress(a)});os_hookEvent(c,"blur",function(a){os_eventBlur(a)});os_hookEvent(c,"focus",function(a){os_eventFocus(a)});c.setAttribute("autocomplete","off");os_hookEvent(document.getElementById(b),"submit",function(a){return os_eventOnsubmit(a)});os_map[a]=d;document.getElementById(d.toggle)}
function os_createToggle(a,b){var c=document.createElement("span");c.className=b;c.setAttribute("id",a.toggle);var d=document.createElement("a");d.setAttribute("href","javascript:void(0);");d.onclick=function(){os_toggle(a.searchbox,a.searchform)};var e=document.createTextNode(wgMWSuggestMessages[0]);d.appendChild(e);c.appendChild(d);return c}
function os_toggle(a,b){r=os_map[a];var c="";null==r?(os_enableSuggestionsOn(a,b),r=os_map[a],c=wgMWSuggestMessages[0]):(os_disableSuggestionsOn(a,b),c=wgMWSuggestMessages[1]);var d=document.getElementById(r.toggle).firstChild;d.replaceChild(document.createTextNode(c),d.firstChild)}function os_enableSuggestionsOn(a,b){os_initHandlers(a,b,document.getElementById(a))}
function os_disableSuggestionsOn(a){r=os_map[a];null!=r&&(os_timer=null,os_hideResults(r),document.getElementById(a).setAttribute("autocomplete","on"),os_map[a]=null);a=os_autoload_inputs.indexOf(a);0<=a&&(os_autoload_inputs[a]=os_autoload_forms[a]="")}function os_MWSuggestInit(){for(i=0;i<os_autoload_inputs.length;i++){var a=os_autoload_inputs[i],b=os_autoload_forms[i];element=document.getElementById(a);null!=element&&os_initHandlers(a,b,element)}}hookEvent("load",os_MWSuggestInit);