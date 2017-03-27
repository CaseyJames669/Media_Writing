/*
 * Shadowbox.js, version @VERSION
 * http://shadowbox-js.com/
 *
 * Copyright 2007-2010, Michael J. I. Jackson
 * @DATE
 */
(function(window,undefined){;var S={version:"3.0.3"}
var ua=navigator.userAgent.toLowerCase();if(ua.indexOf('windows')>-1||ua.indexOf('win32')>-1){S.isWindows=true;}else if(ua.indexOf('macintosh')>-1||ua.indexOf('mac os x')>-1){S.isMac=true;}else if(ua.indexOf('linux')>-1){S.isLinux=true;}
S.isIE=ua.indexOf('msie')>-1;S.isIE6=ua.indexOf('msie 6')>-1;S.isIE7=ua.indexOf('msie 7')>-1;S.isGecko=ua.indexOf('gecko')>-1&&ua.indexOf('safari')==-1;S.isWebKit=ua.indexOf('applewebkit/')>-1;var inlineId=/#(.+)$/,galleryName=/^(light|shadow)box\[(.*?)\]/i,inlineParam=/\s*([a-z_]*?)\s*=\s*(.+)\s*/,fileExtension=/[0-9a-z]+$/i,scriptPath=/(.+\/)shadowbox\.js/i;var open=false,initialized=false,lastOptions={},slideDelay=0,slideStart,slideTimer;S.current=-1;S.dimensions=null;S.ease=function(state){return 1+Math.pow(state-1,3);}
S.errorInfo={fla:{name:"Flash",url:"http://www.adobe.com/products/flashplayer/"},qt:{name:"QuickTime",url:"http://www.apple.com/quicktime/download/"},wmp:{name:"Windows Media Player",url:"http://www.microsoft.com/windows/windowsmedia/"},f4m:{name:"Flip4Mac",url:"http://www.flip4mac.com/wmv_download.htm"}};S.gallery=[];S.onReady=noop;S.path=null;S.player=null;S.playerId="sb-player";S.options={animate:true,animateFade:true,autoplayMovies:true,continuous:false,enableKeys:true,flashParams:{bgcolor:"#000000",allowfullscreen:true},flashVars:{},flashVersion:"9.0.115",handleOversize:"resize",handleUnsupported:"link",onChange:noop,onClose:noop,onFinish:noop,onOpen:noop,showMovieControls:true,skipSetup:false,slideshowDelay:0,viewportPadding:20};S.getCurrent=function(){return S.current>-1?S.gallery[S.current]:null;}
S.hasNext=function(){return S.gallery.length>1&&(S.current!=S.gallery.length-1||S.options.continuous);}
S.isOpen=function(){return open;}
S.isPaused=function(){return slideTimer=="pause";}
S.applyOptions=function(options){lastOptions=apply({},S.options);apply(S.options,options);}
S.revertOptions=function(){apply(S.options,lastOptions);}
S.init=function(options,callback){if(initialized)
return;initialized=true;if(S.skin.options)
apply(S.options,S.skin.options);if(options)
apply(S.options,options);if(!S.path){var path,scripts=document.getElementsByTagName("script");for(var i=0,len=scripts.length;i<len;++i){path=scriptPath.exec(scripts[i].src);if(path){S.path=path[1];break;}}}
if(callback)
S.onReady=callback;bindLoad();}
S.open=function(obj){if(open)
return;var gc=S.makeGallery(obj);S.gallery=gc[0];S.current=gc[1];obj=S.getCurrent();if(obj==null)
return;S.applyOptions(obj.options||{});filterGallery();if(S.gallery.length){obj=S.getCurrent();if(S.options.onOpen(obj)===false)
return;open=true;S.skin.onOpen(obj,load);}}
S.close=function(){if(!open)
return;open=false;if(S.player){S.player.remove();S.player=null;}
if(typeof slideTimer=="number"){clearTimeout(slideTimer);slideTimer=null;}
slideDelay=0;listenKeys(false);S.options.onClose(S.getCurrent());S.skin.onClose();S.revertOptions();}
S.play=function(){if(!S.hasNext())
return;if(!slideDelay)
slideDelay=S.options.slideshowDelay*1000;if(slideDelay){slideStart=now();slideTimer=setTimeout(function(){slideDelay=slideStart=0;S.next();},slideDelay);if(S.skin.onPlay)
S.skin.onPlay();}}
S.pause=function(){if(typeof slideTimer!="number")
return;slideDelay=Math.max(0,slideDelay-(now()-slideStart));if(slideDelay){clearTimeout(slideTimer);slideTimer="pause";if(S.skin.onPause)
S.skin.onPause();}}
S.change=function(index){if(!(index in S.gallery)){if(S.options.continuous){index=(index<0?S.gallery.length+index:0);if(!(index in S.gallery))
return;}else{return;}}
S.current=index;if(typeof slideTimer=="number"){clearTimeout(slideTimer);slideTimer=null;slideDelay=slideStart=0;}
S.options.onChange(S.getCurrent());load(true);}
S.next=function(){S.change(S.current+1);}
S.previous=function(){S.change(S.current-1);}
S.setDimensions=function(height,width,maxHeight,maxWidth,topBottom,leftRight,padding,preserveAspect){var originalHeight=height,originalWidth=width;var extraHeight=2*padding+topBottom;if(height+extraHeight>maxHeight)
height=maxHeight-extraHeight;var extraWidth=2*padding+leftRight;if(width+extraWidth>maxWidth)
width=maxWidth-extraWidth;var changeHeight=(originalHeight-height)/originalHeight,changeWidth=(originalWidth-width)/originalWidth,oversized=(changeHeight>0||changeWidth>0);if(preserveAspect&&oversized){if(changeHeight>changeWidth){width=Math.round((originalWidth/originalHeight)*height);}else if(changeWidth>changeHeight){height=Math.round((originalHeight/originalWidth)*width);}}
S.dimensions={height:height+topBottom,width:width+leftRight,innerHeight:height,innerWidth:width,top:Math.floor((maxHeight-(height+extraHeight))/2+padding),left:Math.floor((maxWidth-(width+extraWidth))/2+padding),oversized:oversized};return S.dimensions;}
S.makeGallery=function(obj){var gallery=[],current=-1;if(typeof obj=="string")
obj=[obj];if(typeof obj.length=="number"){each(obj,function(i,o){if(o.content){gallery[i]=o;}else{gallery[i]={content:o};}});current=0;}else{if(obj.tagName){var cacheObj=S.getCache(obj);obj=cacheObj?cacheObj:S.makeObject(obj);}
if(obj.gallery){gallery=[];var o;for(var key in S.cache){o=S.cache[key];if(o.gallery&&o.gallery==obj.gallery){if(current==-1&&o.content==obj.content)
current=gallery.length;gallery.push(o);}}
if(current==-1){gallery.unshift(obj);current=0;}}else{gallery=[obj];current=0;}}
each(gallery,function(i,o){gallery[i]=apply({},o);});return[gallery,current];}
S.makeObject=function(link,options){var obj={content:link.href,title:link.getAttribute("title")||"",link:link};if(options){options=apply({},options);each(["player","title","height","width","gallery"],function(i,o){if(typeof options[o]!="undefined"){obj[o]=options[o];delete options[o];}});obj.options=options;}else{obj.options={};}
if(!obj.player)
obj.player=S.getPlayer(obj.content);var rel=link.getAttribute("rel");if(rel){var match=rel.match(galleryName);if(match)
obj.gallery=escape(match[2]);each(rel.split(';'),function(i,p){match=p.match(inlineParam);if(match)
obj[match[1]]=match[2];});}
return obj;}
S.getPlayer=function(content){if(content.indexOf("#")>-1&&content.indexOf(document.location.href)==0)
return"inline";var q=content.indexOf("?");if(q>-1)
content=content.substring(0,q);var ext,m=content.match(fileExtension);if(m)
ext=m[0].toLowerCase();if(ext){if(S.img&&S.img.ext.indexOf(ext)>-1)
return"img";if(S.swf&&S.swf.ext.indexOf(ext)>-1)
return"swf";if(S.flv&&S.flv.ext.indexOf(ext)>-1)
return"flv";if(S.qt&&S.qt.ext.indexOf(ext)>-1){if(S.wmp&&S.wmp.ext.indexOf(ext)>-1){return"qtwmp";}else{return"qt";}}
if(S.wmp&&S.wmp.ext.indexOf(ext)>-1)
return"wmp";}
return"iframe";}
function filterGallery(){var err=S.errorInfo,plugins=S.plugins,obj,remove,needed,m,format,replace,inlineEl,flashVersion;for(var i=0;i<S.gallery.length;++i){obj=S.gallery[i]
remove=false;needed=null;switch(obj.player){case"flv":case"swf":if(!plugins.fla)
needed="fla";break;case"qt":if(!plugins.qt)
needed="qt";break;case"wmp":if(S.isMac){if(plugins.qt&&plugins.f4m){obj.player="qt";}else{needed="qtf4m";}}else if(!plugins.wmp){needed="wmp";}
break;case"qtwmp":if(plugins.qt){obj.player="qt";}else if(plugins.wmp){obj.player="wmp";}else{needed="qtwmp";}
break;}
if(needed){if(S.options.handleUnsupported=="link"){switch(needed){case"qtf4m":format="shared";replace=[err.qt.url,err.qt.name,err.f4m.url,err.f4m.name];break;case"qtwmp":format="either";replace=[err.qt.url,err.qt.name,err.wmp.url,err.wmp.name];break;default:format="single";replace=[err[needed].url,err[needed].name];}
obj.player="html";obj.content='<div class="sb-message">'+sprintf(S.lang.errors[format],replace)+'</div>';}else{remove=true;}}else if(obj.player=="inline"){m=inlineId.exec(obj.content);if(m){inlineEl=get(m[1]);if(inlineEl){obj.content=inlineEl.innerHTML;}else{remove=true;}}else{remove=true;}}else if(obj.player=="swf"||obj.player=="flv"){flashVersion=(obj.options&&obj.options.flashVersion)||S.options.flashVersion;if(S.flash&&!S.flash.hasFlashPlayerVersion(flashVersion)){obj.width=310;obj.height=177;}}
if(remove){S.gallery.splice(i,1);if(i<S.current){--S.current;}else if(i==S.current){S.current=i>0?i-1:i;}
--i;}}}
function listenKeys(on){if(!S.options.enableKeys)
return;(on?addEvent:removeEvent)(document,"keydown",handleKey);}
function handleKey(e){if(e.metaKey||e.shiftKey||e.altKey||e.ctrlKey)
return;var code=keyCode(e),handler;switch(code){case 81:case 88:case 27:handler=S.close;break;case 37:handler=S.previous;break;case 39:handler=S.next;break;case 32:handler=typeof slideTimer=="number"?S.pause:S.play;break;}
if(handler){preventDefault(e);handler();}}
function load(changing){listenKeys(false);var obj=S.getCurrent();var player=(obj.player=="inline"?"html":obj.player);if(typeof S[player]!="function")
throw"unknown player "+player;if(changing){S.player.remove();S.revertOptions();S.applyOptions(obj.options||{});}
S.player=new S[player](obj,S.playerId);if(S.gallery.length>1){var next=S.gallery[S.current+1]||S.gallery[0];if(next.player=="img"){var a=new Image();a.src=next.content;}
var prev=S.gallery[S.current-1]||S.gallery[S.gallery.length-1];if(prev.player=="img"){var b=new Image();b.src=prev.content;}}
S.skin.onLoad(changing,waitReady);}
function waitReady(){if(!open)
return;if(typeof S.player.ready!="undefined"){var timer=setInterval(function(){if(open){if(S.player.ready){clearInterval(timer);timer=null;S.skin.onReady(show);}}else{clearInterval(timer);timer=null;}},10);}else{S.skin.onReady(show);}}
function show(){if(!open)
return;S.player.append(S.skin.body,S.dimensions);S.skin.onShow(finish);}
function finish(){if(!open)
return;if(S.player.onLoad)
S.player.onLoad();S.options.onFinish(S.getCurrent());if(!S.isPaused())
S.play();listenKeys(true);};if(!Array.prototype.indexOf){Array.prototype.indexOf=function(obj,from){var len=this.length>>>0;from=from||0;if(from<0)
from+=len;for(;from<len;++from){if(from in this&&this[from]===obj)
return from;}
return-1;}}
function now(){return(new Date).getTime();}
function apply(original,extension){for(var property in extension)
original[property]=extension[property];return original;}
function each(obj,callback){var i=0,len=obj.length;for(var value=obj[0];i<len&&callback.call(value,i,value)!==false;value=obj[++i]){}}
function sprintf(str,replace){return str.replace(/\{(\w+?)\}/g,function(match,i){return replace[i];});}
function noop(){}
function get(id){return document.getElementById(id);}
function remove(el){el.parentNode.removeChild(el);}
var supportsOpacity=true,supportsFixed=true;function checkSupport(){var body=document.body,div=document.createElement("div");supportsOpacity=typeof div.style.opacity==="string";div.style.position="fixed";div.style.margin=0;div.style.top="20px";body.appendChild(div,body.firstChild);supportsFixed=div.offsetTop==20;body.removeChild(div);}
S.getStyle=(function(){var opacity=/opacity=([^)]*)/,getComputedStyle=document.defaultView&&document.defaultView.getComputedStyle;return function(el,style){var ret;if(!supportsOpacity&&style=="opacity"&&el.currentStyle){ret=opacity.test(el.currentStyle.filter||"")?(parseFloat(RegExp.$1)/100)+"":"";return ret===""?"1":ret;}
if(getComputedStyle){var computedStyle=getComputedStyle(el,null);if(computedStyle)
ret=computedStyle[style];if(style=="opacity"&&ret=="")
ret="1";}else{ret=el.currentStyle[style];}
return ret;}})();S.appendHTML=function(el,html){if(el.insertAdjacentHTML){el.insertAdjacentHTML("BeforeEnd",html);}else if(el.lastChild){var range=el.ownerDocument.createRange();range.setStartAfter(el.lastChild);var frag=range.createContextualFragment(html);el.appendChild(frag);}else{el.innerHTML=html;}}
S.getWindowSize=function(dimension){if(document.compatMode==="CSS1Compat")
return document.documentElement["client"+dimension];return document.body["client"+dimension];}
S.setOpacity=function(el,opacity){var style=el.style;if(supportsOpacity){style.opacity=(opacity==1?"":opacity);}else{style.zoom=1;if(opacity==1){if(typeof style.filter=="string"&&(/alpha/i).test(style.filter))
style.filter=style.filter.replace(/\s*[\w\.]*alpha\([^\)]*\);?/gi,"");}else{style.filter=(style.filter||"").replace(/\s*[\w\.]*alpha\([^\)]*\)/gi,"")+" alpha(opacity="+(opacity*100)+")";}}}
S.clearOpacity=function(el){S.setOpacity(el,1);};function getTarget(e){var target=e.target?e.target:e.srcElement;return target.nodeType==3?target.parentNode:target;}
function getPageXY(e){var x=e.pageX||(e.clientX+(document.documentElement.scrollLeft||document.body.scrollLeft)),y=e.pageY||(e.clientY+(document.documentElement.scrollTop||document.body.scrollTop));return[x,y];}
function preventDefault(e){e.preventDefault();}
function keyCode(e){return e.which?e.which:e.keyCode;}
function addEvent(el,type,handler){if(el.addEventListener){el.addEventListener(type,handler,false);}else{if(el.nodeType===3||el.nodeType===8)
return;if(el.setInterval&&(el!==window&&!el.frameElement))
el=window;if(!handler.__guid)
handler.__guid=addEvent.guid++;if(!el.events)
el.events={};var handlers=el.events[type];if(!handlers){handlers=el.events[type]={};if(el["on"+type])
handlers[0]=el["on"+type];}
handlers[handler.__guid]=handler;el["on"+type]=addEvent.handleEvent;}}
addEvent.guid=1;addEvent.handleEvent=function(event){var result=true;event=event||addEvent.fixEvent(((this.ownerDocument||this.document||this).parentWindow||window).event);var handlers=this.events[event.type];for(var i in handlers){this.__handleEvent=handlers[i];if(this.__handleEvent(event)===false)
result=false;}
return result;}
addEvent.preventDefault=function(){this.returnValue=false;}
addEvent.stopPropagation=function(){this.cancelBubble=true;}
addEvent.fixEvent=function(e){e.preventDefault=addEvent.preventDefault;e.stopPropagation=addEvent.stopPropagation;return e;}
function removeEvent(el,type,handler){if(el.removeEventListener){el.removeEventListener(type,handler,false);}else{if(el.events&&el.events[type])
delete el.events[type][handler.__guid];}};var loaded=false,DOMContentLoaded;if(document.addEventListener){DOMContentLoaded=function(){document.removeEventListener("DOMContentLoaded",DOMContentLoaded,false);S.load();}}else if(document.attachEvent){DOMContentLoaded=function(){if(document.readyState==="complete"){document.detachEvent("onreadystatechange",DOMContentLoaded);S.load();}}}
function doScrollCheck(){if(loaded)
return;try{document.documentElement.doScroll("left");}catch(e){setTimeout(doScrollCheck,1);return;}
S.load();}
function bindLoad(){if(document.readyState==="complete")
return S.load();if(document.addEventListener){document.addEventListener("DOMContentLoaded",DOMContentLoaded,false);window.addEventListener("load",S.load,false);}else if(document.attachEvent){document.attachEvent("onreadystatechange",DOMContentLoaded);window.attachEvent("onload",S.load);var topLevel=false;try{topLevel=window.frameElement===null;}catch(e){}
if(document.documentElement.doScroll&&topLevel)
doScrollCheck();}}
S.load=function(){if(loaded)
return;if(!document.body)
return setTimeout(S.load,13);loaded=true;checkSupport();S.onReady();if(!S.options.skipSetup)
S.setup();S.skin.init();};S.plugins={};if(navigator.plugins&&navigator.plugins.length){var names=[];each(navigator.plugins,function(i,p){names.push(p.name);});names=names.join(',');var f4m=names.indexOf('Flip4Mac')>-1;S.plugins={fla:names.indexOf('Shockwave Flash')>-1,qt:names.indexOf('QuickTime')>-1,wmp:!f4m&&names.indexOf('Windows Media')>-1,f4m:f4m};}else{var detectPlugin=function(name){var axo;try{axo=new ActiveXObject(name);}catch(e){}
return!!axo;}
S.plugins={fla:detectPlugin('ShockwaveFlash.ShockwaveFlash'),qt:detectPlugin('QuickTime.QuickTime'),wmp:detectPlugin('wmplayer.ocx'),f4m:false};};var relAttr=/^(light|shadow)box/i,expando="shadowboxCacheKey",cacheKey=1;S.cache={};S.select=function(selector){var links=[];if(!selector){var rel;each(document.getElementsByTagName("a"),function(i,el){rel=el.getAttribute("rel");if(rel&&relAttr.test(rel))
links.push(el);});}else{var length=selector.length;if(length){if(typeof selector=="string"){if(S.find)
links=S.find(selector);}else if(length==2&&typeof selector[0]=="string"&&selector[1].nodeType){if(S.find)
links=S.find(selector[0],selector[1]);}else{for(var i=0;i<length;++i)
links[i]=selector[i];}}else{links.push(selector);}}
return links;}
S.setup=function(selector,options){each(S.select(selector),function(i,link){S.addCache(link,options);});}
S.teardown=function(selector){each(S.select(selector),function(i,link){S.removeCache(link);});}
S.addCache=function(link,options){var key=link[expando];if(key==undefined){key=cacheKey++;link[expando]=key;addEvent(link,"click",handleClick);}
S.cache[key]=S.makeObject(link,options);}
S.removeCache=function(link){removeEvent(link,"click",handleClick);delete S.cache[link[expando]];link[expando]=null;}
S.getCache=function(link){var key=link[expando];return(key in S.cache&&S.cache[key]);}
S.clearCache=function(){for(var key in S.cache)
S.removeCache(S.cache[key].link);S.cache={};}
function handleClick(e){S.open(this);if(S.gallery.length)
preventDefault(e);};S.lang={code:'en',of:'of',loading:'loading',cancel:'Cancel',next:'Next',previous:'Previous',play:'Play',pause:'Pause',close:'Close',errors:{single:'You must install the <a href="{0}">{1}</a> browser plugin to view this content.',shared:'You must install both the <a href="{0}">{1}</a> and <a href="{2}">{3}</a> browser plugins to view this content.',either:'You must install either the <a href="{0}">{1}</a> or the <a href="{2}">{3}</a> browser plugin to view this content.'}};var pre,proxyId="sb-drag-proxy",dragData,dragProxy,dragTarget;function resetDrag(){dragData={x:0,y:0,startX:null,startY:null};}
function updateProxy(){var dims=S.dimensions;apply(dragProxy.style,{height:dims.innerHeight+"px",width:dims.innerWidth+"px"});}
function enableDrag(){resetDrag();var style=["position:absolute","cursor:"+(S.isGecko?"-moz-grab":"move"),"background-color:"+(S.isIE?"#fff;filter:alpha(opacity=0)":"transparent")].join(";");S.appendHTML(S.skin.body,'<div id="'+proxyId+'" style="'+style+'"></div>');dragProxy=get(proxyId);updateProxy();addEvent(dragProxy,"mousedown",startDrag);}
function disableDrag(){if(dragProxy){removeEvent(dragProxy,"mousedown",startDrag);remove(dragProxy);dragProxy=null;}
dragTarget=null;}
function startDrag(e){preventDefault(e);var xy=getPageXY(e);dragData.startX=xy[0];dragData.startY=xy[1];dragTarget=get(S.player.id);addEvent(document,"mousemove",positionDrag);addEvent(document,"mouseup",endDrag);if(S.isGecko)
dragProxy.style.cursor="-moz-grabbing";}
function positionDrag(e){var player=S.player,dims=S.dimensions,xy=getPageXY(e);var moveX=xy[0]-dragData.startX;dragData.startX+=moveX;dragData.x=Math.max(Math.min(0,dragData.x+moveX),dims.innerWidth-player.width);var moveY=xy[1]-dragData.startY;dragData.startY+=moveY;dragData.y=Math.max(Math.min(0,dragData.y+moveY),dims.innerHeight-player.height);apply(dragTarget.style,{left:dragData.x+"px",top:dragData.y+"px"});}
function endDrag(){removeEvent(document,"mousemove",positionDrag);removeEvent(document,"mouseup",endDrag);if(S.isGecko)
dragProxy.style.cursor="-moz-grab";}
S.img=function(obj,id){this.obj=obj;this.id=id;this.ready=false;var self=this;pre=new Image();pre.onload=function(){self.height=obj.height?parseInt(obj.height,10):pre.height;self.width=obj.width?parseInt(obj.width,10):pre.width;self.ready=true;pre.onload=null;pre=null;}
pre.src=obj.content;}
S.img.ext=["bmp","gif","jpg","jpeg","png"];S.img.prototype={append:function(body,dims){var img=document.createElement("img");img.id=this.id;img.src=this.obj.content;img.style.position="absolute";var height,width;if(dims.oversized&&S.options.handleOversize=="resize"){height=dims.innerHeight;width=dims.innerWidth;}else{height=this.height;width=this.width;}
img.setAttribute("height",height);img.setAttribute("width",width);body.appendChild(img);},remove:function(){var el=get(this.id);if(el)
remove(el);disableDrag();if(pre){pre.onload=null;pre=null;}},onLoad:function(){var dims=S.dimensions;if(dims.oversized&&S.options.handleOversize=="drag")
enableDrag();},onWindowResize:function(){var dims=S.dimensions;switch(S.options.handleOversize){case"resize":var el=get(this.id);el.height=dims.innerHeight;el.width=dims.innerWidth;break;case"drag":if(dragTarget){var top=parseInt(S.getStyle(dragTarget,"top")),left=parseInt(S.getStyle(dragTarget,"left"));if(top+this.height<dims.innerHeight)
dragTarget.style.top=dims.innerHeight-this.height+"px";if(left+this.width<dims.innerWidth)
dragTarget.style.left=dims.innerWidth-this.width+"px";updateProxy();}
break;}}};var overlayOn=false,visibilityCache=[],pngIds=["sb-nav-close","sb-nav-next","sb-nav-play","sb-nav-pause","sb-nav-previous"],container,overlay,wrapper,doWindowResize=true;function animate(el,property,to,duration,callback){var isOpacity=(property=="opacity"),anim=isOpacity?S.setOpacity:function(el,value){el.style[property]=""+
value+"px";};if(duration==0||(!isOpacity&&!S.options.animate)||(isOpacity&&!S.options.animateFade)){anim(el,to);if(callback)
callback();return;}
var from=parseFloat(S.getStyle(el,property))||0;var delta=to-from;if(delta==0){if(callback)
callback();return;}
duration*=1000;var begin=now(),ease=S.ease,end=begin+duration,time;var interval=setInterval(function(){time=now();if(time>=end){clearInterval(interval);interval=null;anim(el,to);if(callback)
callback();}else{anim(el,from+ease((time-begin)/duration)*delta);}},10);}
function setSize(){container.style.height=S.getWindowSize("Height")+"px";container.style.width=S.getWindowSize("Width")+"px";}
function setPosition(){container.style.top=document.documentElement.scrollTop+"px";container.style.left=document.documentElement.scrollLeft+"px";}
function toggleTroubleElements(on){if(on){each(visibilityCache,function(i,el){el[0].style.visibility=el[1]||'';});}else{visibilityCache=[];each(S.options.troubleElements,function(i,tag){each(document.getElementsByTagName(tag),function(j,el){visibilityCache.push([el,el.style.visibility]);el.style.visibility="hidden";});});}}
function toggleNav(id,on){var el=get("sb-nav-"+id);if(el)
el.style.display=on?"":"none";}
function toggleLoading(on,callback){var loading=get("sb-loading"),playerName=S.getCurrent().player,anim=(playerName=="img"||playerName=="html");if(on){S.setOpacity(loading,0);loading.style.display="block";var wrapped=function(){S.clearOpacity(loading);if(callback)
callback();}
if(anim){animate(loading,"opacity",1,S.options.fadeDuration,wrapped);}else{wrapped();}}else{var wrapped=function(){loading.style.display="none";S.clearOpacity(loading);if(callback)
callback();}
if(anim){animate(loading,"opacity",0,S.options.fadeDuration,wrapped);}else{wrapped();}}}
function buildBars(callback){var obj=S.getCurrent();get("sb-title-inner").innerHTML=obj.title||"";var close,next,play,pause,previous;if(S.options.displayNav){close=true;var len=S.gallery.length;if(len>1){if(S.options.continuous){next=previous=true;}else{next=(len-1)>S.current;previous=S.current>0;}}
if(S.options.slideshowDelay>0&&S.hasNext()){pause=!S.isPaused();play=!pause;}}else{close=next=play=pause=previous=false;}
toggleNav("close",close);toggleNav("next",next);toggleNav("play",play);toggleNav("pause",pause);toggleNav("previous",previous);var counter="";if(S.options.displayCounter&&S.gallery.length>1){var len=S.gallery.length;if(S.options.counterType=="skip"){var i=0,end=len,limit=parseInt(S.options.counterLimit)||0;if(limit<len&&limit>2){var h=Math.floor(limit/2);i=S.current-h;if(i<0)
i+=len;end=S.current+(limit-h);if(end>len)
end-=len;}
while(i!=end){if(i==len)
i=0;counter+='<a onclick="Shadowbox.change('+i+');"'
if(i==S.current)
counter+=' class="sb-counter-current"';counter+=">"+(++i)+"</a>";}}else{counter=[S.current+1,S.lang.of,len].join(' ');}}
get("sb-counter").innerHTML=counter;callback();}
function showBars(callback){var titleInner=get("sb-title-inner"),infoInner=get("sb-info-inner"),duration=0.35;titleInner.style.visibility=infoInner.style.visibility="";if(titleInner.innerHTML!="")
animate(titleInner,"marginTop",0,duration);animate(infoInner,"marginTop",0,duration,callback);}
function hideBars(anim,callback){var title=get("sb-title"),info=get("sb-info"),titleHeight=title.offsetHeight,infoHeight=info.offsetHeight,titleInner=get("sb-title-inner"),infoInner=get("sb-info-inner"),duration=(anim?0.35:0);animate(titleInner,"marginTop",titleHeight,duration);animate(infoInner,"marginTop",infoHeight*-1,duration,function(){titleInner.style.visibility=infoInner.style.visibility="hidden";callback();});}
function adjustHeight(height,top,anim,callback){var wrapperInner=get("sb-wrapper-inner"),duration=(anim?S.options.resizeDuration:0);animate(wrapper,"top",top,duration);animate(wrapperInner,"height",height,duration,callback);}
function adjustWidth(width,left,anim,callback){var duration=(anim?S.options.resizeDuration:0);animate(wrapper,"left",left,duration);animate(wrapper,"width",width,duration,callback);}
function setDimensions(height,width){var bodyInner=get("sb-body-inner"),height=parseInt(height),width=parseInt(width),topBottom=wrapper.offsetHeight-bodyInner.offsetHeight,leftRight=wrapper.offsetWidth-bodyInner.offsetWidth,maxHeight=overlay.offsetHeight,maxWidth=overlay.offsetWidth,padding=parseInt(S.options.viewportPadding)||20,preserveAspect=(S.player&&S.options.handleOversize!="drag");return S.setDimensions(height,width,maxHeight,maxWidth,topBottom,leftRight,padding,preserveAspect);}
var K={};K.markup=""+'<div id="sb-container">'+'<div id="sb-overlay"></div>'+'<div id="sb-wrapper">'+'<div id="sb-title">'+'<div id="sb-title-inner"></div>'+'</div>'+'<div id="sb-wrapper-inner">'+'<div id="sb-body">'+'<div id="sb-body-inner"></div>'+'<div id="sb-loading">'+'<div id="sb-loading-inner"><span>{loading}</span></div>'+'</div>'+'</div>'+'</div>'+'<div id="sb-info">'+'<div id="sb-info-inner">'+'<div id="sb-counter"></div>'+'<div id="sb-nav">'+'<a id="sb-nav-close" title="{close}" onclick="Shadowbox.close()"></a>'+'<a id="sb-nav-next" title="{next}" onclick="Shadowbox.next()"></a>'+'<a id="sb-nav-play" title="{play}" onclick="Shadowbox.play()"></a>'+'<a id="sb-nav-pause" title="{pause}" onclick="Shadowbox.pause()"></a>'+'<a id="sb-nav-previous" title="{previous}" onclick="Shadowbox.previous()"></a>'+'</div>'+'</div>'+'</div>'+'</div>'+'</div>';K.options={animSequence:"sync",counterLimit:10,counterType:"default",displayCounter:true,displayNav:true,fadeDuration:0.35,initialHeight:160,initialWidth:320,modal:false,overlayColor:"#000",overlayOpacity:0.5,resizeDuration:0.35,showOverlay:true,troubleElements:["select","object","embed","canvas"]};K.init=function(){S.appendHTML(document.body,sprintf(K.markup,S.lang));K.body=get("sb-body-inner");container=get("sb-container");overlay=get("sb-overlay");wrapper=get("sb-wrapper");if(!supportsFixed)
container.style.position="absolute";if(!supportsOpacity){var el,m,re=/url\("(.*\.png)"\)/;each(pngIds,function(i,id){el=get(id);if(el){m=S.getStyle(el,"backgroundImage").match(re);if(m){el.style.backgroundImage="none";el.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,src="+
m[1]+",sizingMethod=scale);";}}});}
var timer;addEvent(window,"resize",function(){if(timer){clearTimeout(timer);timer=null;}
if(open)
timer=setTimeout(K.onWindowResize,10);});}
K.onOpen=function(obj,callback){doWindowResize=false;container.style.display="block";setSize();var dims=setDimensions(S.options.initialHeight,S.options.initialWidth);adjustHeight(dims.innerHeight,dims.top);adjustWidth(dims.width,dims.left);if(S.options.showOverlay){overlay.style.backgroundColor=S.options.overlayColor;S.setOpacity(overlay,0);if(!S.options.modal)
addEvent(overlay,"click",S.close);overlayOn=true;}
if(!supportsFixed){setPosition();addEvent(window,"scroll",setPosition);}
toggleTroubleElements();container.style.visibility="visible";if(overlayOn){animate(overlay,"opacity",S.options.overlayOpacity,S.options.fadeDuration,callback);}else{callback();}}
K.onLoad=function(changing,callback){toggleLoading(true);while(K.body.firstChild)
remove(K.body.firstChild);hideBars(changing,function(){if(!open)
return;if(!changing)
wrapper.style.visibility="visible";buildBars(callback);});}
K.onReady=function(callback){if(!open)
return;var player=S.player,dims=setDimensions(player.height,player.width);var wrapped=function(){showBars(callback);}
switch(S.options.animSequence){case"hw":adjustHeight(dims.innerHeight,dims.top,true,function(){adjustWidth(dims.width,dims.left,true,wrapped);});break;case"wh":adjustWidth(dims.width,dims.left,true,function(){adjustHeight(dims.innerHeight,dims.top,true,wrapped);});break;default:adjustWidth(dims.width,dims.left,true);adjustHeight(dims.innerHeight,dims.top,true,wrapped);}}
K.onShow=function(callback){toggleLoading(false,callback);doWindowResize=true;}
K.onClose=function(){if(!supportsFixed)
removeEvent(window,"scroll",setPosition);removeEvent(overlay,"click",S.close);wrapper.style.visibility="hidden";var callback=function(){container.style.visibility="hidden";container.style.display="none";toggleTroubleElements(true);}
if(overlayOn){animate(overlay,"opacity",0,S.options.fadeDuration,callback);}else{callback();}}
K.onPlay=function(){toggleNav("play",false);toggleNav("pause",true);}
K.onPause=function(){toggleNav("pause",false);toggleNav("play",true);}
K.onWindowResize=function(){if(!doWindowResize)
return;setSize();var player=S.player,dims=setDimensions(player.height,player.width);adjustWidth(dims.width,dims.left);adjustHeight(dims.innerHeight,dims.top);if(player.onWindowResize)
player.onWindowResize();}
S.skin=K;;window['Shadowbox']=S;})(window);