/*! For license information please see page.js.LICENSE.txt */
(globalThis.webpackChunksakurairo_scripts=globalThis.webpackChunksakurairo_scripts||[]).push([[9768],{9508:()=>{const e=[".bili",".menhera",".tieba",".custom"];window.motionSwitch=function(t){for(const n of e){if(n===t)continue;const e=document.querySelector(n+"-bar");null!==e&&(e.classList.remove("on-hover"),document.querySelector(n+"-container").style.display="none")}document.querySelector(t+"-bar").classList.add("on-hover"),document.querySelector(t+"-container").style.display="block"},window.grin=function(e,t,n,o){let a;switch(t){case"custom":e=n+e+o;break;case"Img":e="[img]"+e+"[/img]";break;case"Math":e=" {{"+e+"}} ";break;case"tieba":e=" ::"+e+":: ";break;default:e=" :"+e+": "}if(!document.getElementById("comment")||"textarea"!=document.getElementById("comment").type)return!1;if(a=document.getElementById("comment"),document.selection){a.focus();document.selection.createRange().text=e,a.focus()}else if(a.selectionStart||"0"==a.selectionStart){let t=a.selectionStart,n=a.selectionEnd,o=n;a.value=a.value.substring(0,t)+e+a.value.substring(n,a.value.length),o+=e.length,a.focus(),a.selectionStart=o,a.selectionEnd=o}else a.value+=e,a.focus()}},7791:(e,t,n)=>{var o=n(9966),a=n(9618),r=n(9509);let s;function i(){!function(){const e=document.getElementsByClassName("collapseButton");if(e.length>0){const t=e=>{(0,a.wy)(e.target.parentNode.parentNode.parentNode.querySelector(".xContent"))};for(const n of e)n.addEventListener("click",t)}}(),async function(){if(_iro.baguetteBoxON){s||(s=(0,o.loadCSS)((0,r.i)("dist/baguetteBox.min.css","baguettebox.js","1.11.1")));const{default:e}=await n.e(1541).then(n.t.bind(n,1541,23));e.run(".entry-content",{captions:function(e){return e.getElementsByTagName("img")[0].alt},ignoreClass:"fancybox"})}else if(_iro.fancybox){if(s||(s=(0,o.loadCSS)((0,r.i)("dist/jquery.fancybox.min.css","@fancyapps/fancybox","3.5.7"))),!(window.jQuery instanceof Function||window.$ instanceof Function))if(_iro.ext_shared_lib)(0,r.V)("dist/jquery.slim.min.js","jquery");else{const{default:e}=await n.e(2692).then(n.t.bind(n,2692,23));window.$=e,window.jQuery=e}_iro.ext_shared_lib?(0,r.V)("dist/jquery.fancybox.min.js","@fancyapps/fancybox"):n.e(245).then(n.t.bind(n,245,23))}else if(_iro.lightGallery){const{default:e}=await Promise.all([n.e(6104),n.e(2585)]).then(n.bind(n,6104));e()}}(),async function(){document.getElementsByTagName("math").length>0&&("MathJax"in window||(window.MathJax={svg:{fontCache:"global"},startup:{typeset:!1},chtml:{fontURL:(0,r.i)("es5/output/chtml/fonts/woff-v2","mathjax","2.3.0"),mathmlSpacing:!0}}),await n.e(9706).then(n.t.bind(n,9706,23)),window.MathJax.typeset())}()}var l=n(5153),c=n(7513),u=(n(9508),n(6706)),m=n.n(u);const d=(e,t=80)=>"https://"+_iro.gravatar_url+"/"+m()(e)+".jpg?s="+t+"&d=mm";function g(){let e=!1;const t=document.querySelector("input#author"),n=document.querySelector("input#qq"),o=document.querySelector("input#email"),r=document.querySelector("input#url"),s=document.querySelector(".qq-check"),i=document.querySelector(".gravatar-check"),l=document.querySelector("div.comment-user-avatar img");if(null==t)return;localStorage.getItem("user_qq")||localStorage.getItem("user_qq_email")||localStorage.getItem("user_author")||(n.value=t.value=o.value=r.value=""),localStorage.getItem("user_avatar")&&localStorage.getItem("user_qq")&&localStorage.getItem("user_qq_email")&&(l.setAttribute("src",localStorage.getItem("user_avatar")),t.value=localStorage.getItem("user_author"),o.value=localStorage.getItem("user_qq")+"@qq.com",n.value=localStorage.getItem("user_qq"),_iro.qzone_autocomplete&&(r.value="https://user.qzone.qq.com/"+localStorage.getItem("user_qq")),n.value&&(s.style.display="block",i.style.display="none"));let c=o.value;t.addEventListener("blur",(()=>{const u=t.value;if(/^[1-9]\d{4,9}$/.test(u)){const m=()=>{n.value="",s.style.display="none",i.style.display="block",l.setAttribute("src",d(o.value,80)),localStorage.setItem("user_qq",""),localStorage.setItem("user_email",o.value),localStorage.setItem("user_avatar",d(o.value,80))};fetch((0,a.bf)(_iro.qq_api_url,{qq:u})).then((async a=>{if(a.ok)try{const m=await a.json();t.value=m.name,o.value=u.trim()+"@qq.com",_iro.qzone_autocomplete&&(r.value="https://user.qzone.qq.com/"+u.trim()),l.setAttribute("src","https://q2.qlogo.cn/headimg_dl?dst_uin="+u+"&spec=100"),e=!0,n.value=u.trim(),n.value&&(s.style.display="block",i.style.display="none"),localStorage.setItem("user_author",m.name),localStorage.setItem("user_qq",u),localStorage.setItem("is_user_qq","yes"),localStorage.setItem("user_qq_email",u+"@qq.com"),localStorage.setItem("user_email",u+"@qq.com"),c=o.value,l.setAttribute("src",m.avatar),localStorage.setItem("user_avatar",m.avatar)}catch(e){console.warn(e),m()}else m()}))}})),localStorage.getItem("user_avatar")&&localStorage.getItem("user_email")&&"no"==localStorage.getItem("is_user_qq")&&!localStorage.getItem("user_qq_email")&&(l.setAttribute("src",localStorage.getItem("user_avatar")),o.value=localStorage.getItem("user_mail"),n.value="",n.value||(s.style.display="none",i.style.display="block")),o.addEventListener("blur",(function(){let t=o.value;0!=e&&c==t||""==t||(l.setAttribute("src",d(t,80)),localStorage.setItem("user_avatar",d(t,80)),localStorage.setItem("user_email",t),localStorage.setItem("user_qq_email",""),localStorage.setItem("is_user_qq","no"),n.value="",n.value||(s.style.display="none",i.style.display="block"))})),localStorage.getItem("user_url")&&(r.value=localStorage.getItem("user_url")),r.addEventListener("blur",(function(){let e=r.value;r.value=e,localStorage.setItem("user_url",e)})),localStorage.getItem("user_author")&&(t.value=localStorage.getItem("user_author")),t.addEventListener("blur",(function(){let e=t.value;t.value=e,localStorage.setItem("user_author",e)}))}const f={respondId:"",moveForm:function(e,t,n){const o=document.getElementById(e),a=document.getElementById(n),r=document.getElementById("cancel-comment-reply-link"),s=document.getElementById("comment_parent");if(this.respondId=n,!document.getElementById("wp-temp-form-div")){const e=document.createElement("div");e.id="wp-temp-form-div",e.style.display="none",a.parentNode.insertBefore(e,a)}if(o)o.parentNode.insertBefore(a,o.nextSibling);else{const e=document.getElementById("wp-temp-form-div");document.getElementById("comment_parent").value="0",e.parentNode.insertBefore(a,e),e.remove()}const i=document.getElementById("respond");window.scrollTo({top:i.getBoundingClientRect().top+window.pageYOffset-i.clientTop-100,behavior:"smooth"}),s.value=t,r.style.display="",r.onclick=function(e){const t=f,n=document.getElementById("wp-temp-form-div"),o=document.getElementById(t.respondId);return document.getElementById("comment_parent").value="0",n&&o&&(n.parentNode.insertBefore(o,n),n.remove()),this.style.display="none",this.onclick=null,!1};try{document.getElementById("comment").focus()}catch(e){}return!1}},p=f;var y=n(2159);const h=e=>{const t=e.target;if(t===document.querySelector("#bangumi-pagination a")){if(e.preventDefault(),e.stopPropagation(),t.classList.contains("loading"))return;t.classList.add("loading"),t.textContent="",fetch(t.dataset.href+"&_wpnonce="+_iro.nonce,{method:"POST"}).then((async e=>{const t=await e.json();if(!e.ok)throw Error(`Error，Status：${e.status}`);document.getElementById("bangumi-pagination").remove(),document.querySelector(".row").insertAdjacentHTML("beforeend",t),(0,l.m)()})).catch((e=>{console.error(e),(0,c.f)(e),t.classList.remove("loading"),t.innerHTML='<i class="fa-solid fa-triangle-exclamation"></i> ERROR '}))}};function v(){const e=document.getElementsByTagName("section");let t=!1;for(let n=0;n<e.length;n++)if(e[n].classList.contains("bangumi")){t=!0;break}t&&document.addEventListener("click",h)}var _=n(3062);function w(){document.getElementsByClassName("comment_inline_img").length&&document.getElementsByClassName("comments-main")[0].addEventListener("click",(function(e){e.target.classList.contains("comment_inline_img")&&window.open(e.target.src)}))}function b(){document.getElementsByClassName("emoji-item").length&&document.querySelector(".menhera-container").addEventListener("click",(function(e){e.target.classList.contains("emoji-item")&&grin(e.target.innerText,"custom","`","` ")}))}function S(){const e="commentwrap",t=document.getElementById("commentform");if(t){let n=!1;t.addEventListener("submit",(function(t){if(t.stopPropagation(),t.preventDefault(),n)return;const o=(0,c.f)((0,y.__)("提交中...."),!0),a=new FormData(this);a.append("action","ajax_comment"),n=!0,fetch(_iro.ajaxurl,{method:this.attributes.method.value,body:a}).then((async t=>{const n=await t.text();if(t.ok){Array.from(document.getElementsByTagName("textarea")).forEach((e=>e.value=""));const t=document.getElementById("cancel-comment-reply-link"),o=document.getElementById("wp-temp-form-div"),a=document.getElementById(p.respondId);"0"!=document.getElementById("comment_parent").value?document.getElementById("respond").insertAdjacentHTML("beforebegin",'<ol class="children">'+n+"</ol>"):document.getElementsByClassName(e).length?"asc"==_iro.order?document.getElementsByClassName("commentwrap")[1].insertAdjacentHTML("beforeend",n):document.getElementsByClassName("commentwrap")[1].insertAdjacentHTML("afterbegin",n):"bottom"==_iro.formpostion?document.getElementById("respond").insertAdjacentHTML("beforebegin",'<ol class="'+e+'">'+n+"</ol>"):document.getElementById("respond").insertAdjacentHTML("afterend",'<ol class="'+e+'">'+n+"</ol>"),(0,c.f)((0,y.__)("提交成功")),document.dispatchEvent(new CustomEvent("ajax_comment_complete")),t.style.display="none",t.onclick=null,document.getElementById("comment_parent").value="0",o&&a&&(o.parentNode.insertBefore(a,o),o.remove())}else(0,c.f)(n||"HTTP"+t.status+":"+t.statusText)})).catch((e=>{(0,c.f)(e)})).finally((()=>{o.remove(),n=!1}))}))}}function q(){let e=document.getElementsByClassName("sm"),t=document.querySelector(".comments-main");e.length&&t&&(t.addEventListener("click",(function(e){if(e.target.classList.contains("comment-reply-link")){e.preventDefault(),e.stopPropagation();let t=e.target.getAttribute("data-commentid");p.moveForm("comment-"+t,t,"respond",this.getAttribute("data-postid"))}})),t.addEventListener("click",(e=>{let t=e.target.parentNode;if(t.classList.contains("sm")){let e=(0,y.__)("您真的要设为私密吗？");if(1==confirm(e)){if(t.classList.contains("private_now"))return alert((0,y.__)("您之前已设过私密评论")),!1;{t.classList.add("private_now");let e=t.getAttribute("data-idp"),n=t.getAttribute("data-actionp"),o=t.getElementsByClassName("has_set_private")[0],a="action=siren_private&p_id="+e+"&p_action="+n,r=new XMLHttpRequest;return r.onreadystatechange=function(){4==this.readyState&&200==this.status&&(o.innerHTML=r.responseText)},r.open("POST",_iro.ajaxurl,!0),r.setRequestHeader("Content-type","application/x-www-form-urlencoded"),r.send(a),!1}}}})))}function E(){const e=document.querySelector(".toc-container"),t=document.querySelector(".site-content");if(e&&t){const n=()=>{e.style.height=Math.min(t.getBoundingClientRect().height,document.documentElement.offsetHeight-e.offsetTop)+"px"};n(),window.addEventListener("resize",function(e,t=166){let n;function o(...o){clearTimeout(n),n=setTimeout((()=>{e.apply(this,o)}),t)}return o.clear=()=>{clearTimeout(n)},o}(n),{passive:!0}),document.addEventListener("scroll",function(e,t,n){let o=null,a=Date.parse(new Date);return function(){null!==o&&clearTimeout(o);let r=Date.parse(new Date);r-a>=n?(e(),a=r):o=setTimeout(e,t)}}(n,200,1e3))}}function I(e){if(!(document.body.clientWidth<=1200))if(document.querySelector("div.have-toc")||document.querySelector("div.has-toc")){if(e&&document.getElementsByClassName("toc").length>0){n.e(1812).then(n.t.bind(n,1812,23)).then((({default:t})=>{t.init({tocSelector:".toc",contentSelector:[".entry-content",".links"],headingSelector:"h1,h2,h3,h4,h5",headingsOffset:e-window.innerHeight/2,onClick:null})}));const e="sakurairo"==_iro.entry_content_style?document.querySelector("article.type-post")?document.querySelector("div.pattern-attachment")?-75:200:375:window.innerHeight/2,t=document.querySelectorAll(".entry-content,.links"),o=new Set;let a=1;for(let e=0;e<t.length;e++){let n=t[e].querySelectorAll("h1,h2,h3,h4,h5");for(const e of n){const t=encodeURIComponent(e.innerText.replace(" ","-"));o.has(t)?e.id="toc-head-"+a++:(e.id=t,o.add(t))}}}}else{let e=document.getElementsByClassName("toc-container")[0];e&&(e.remove(),e=null)}}function B(){let e=document.getElementById("emotion-toggle");e&&e.addEventListener("click",(function(){document.querySelector(".emotion-toggle-off").classList.toggle("emotion-hide"),document.querySelector(".emotion-toggle-on").classList.toggle("emotion-show"),document.querySelector(".emotion-box").classList.toggle("emotion-box-show")}))}function T(){const e=document.querySelector(".form-submit #submit");if(null==e)return;if(!_iro.comment_upload_img)return void(e.style.width="100%");e.insertAdjacentHTML("afterend",'<div class="insert-image-tips popup"><i class="fa-regular fa-image"></i><span class="insert-img-popuptext" id="uploadTipPopup">上传图片</span></div><input id="upload-img-file" type="file" accept="image/*" multiple="multiple" class="insert-image-button">'),function(){let e=document.getElementsByClassName("insert-image-tips")[0],t=document.getElementById("upload-img-file");t&&t.addEventListener("change",(function(){if(this.files.length>10)return(0,c.f)((0,y.__)("每次上传上限为10张")),0;for(let e=0;e<this.files.length;e++)if(this.files[e].size>=5242880)return void alert((0,y._$)("图片上传大小限制为5 MB\n\n「{0}」\n\n这张图太大啦~请重新上传噢！",this.files[e].name));for(let t=0;t<this.files.length;t++){let n=this.files[t],o=new FormData,r=new XMLHttpRequest;o.append("cmt_img_file",n),r.addEventListener("loadstart",(function(){e.innerHTML='<i class="fa-solid fa-circle-notch fa-spin"></i>',(0,c.f)((0,y.__)("上传中..."))})),r.open("POST",(0,a.bf)(_iro.api+"sakura/v1/image/upload"),!0),r.send(o),r.onreadystatechange=function(){if(4!=r.readyState||200!=r.status&&304!=r.status)4==r.readyState&&(e.innerHTML='<i class="fa-regular fa-clock" aria-hidden="true" style="color:red"></i>',alert((0,y.__)("上传失败，请重试.")),setTimeout((function(){e.innerHTML='<i class="fa-regular fa-image"></i>'}),1e3));else{e.innerHTML='<i class="fa-solid fa-check"></i>',setTimeout((function(){e.innerHTML='<i class="fa-regular fa-image"></i>'}),1e3);let t=JSON.parse(r.responseText);if(200==t.status){let e=t.proxy;document.getElementById("upload-img-show").insertAdjacentHTML("afterend",'<img class="lazyload upload-image-preview" src="'+_iro.loading_ph+'" data-src="'+e+'" onclick="window.open(\''+e+'\')" onerror="imgError(this)" />'),(0,l.m)(),(0,c.f)((0,y.__)("图片上传成功~")),grin(e,type="Img")}else(0,c.f)((0,y._$)("上传失败！\n文件名: {0}\ncode: {1}\n{2}",n.name,t.status,t.message),3e3)}}}}))}();const t=document.getElementById("upload-img-file"),n=document.getElementsByClassName("insert-image-tips")[0],o=document.getElementById("uploadTipPopup");t&&(t.addEventListener("mouseenter",(function(){n.classList.toggle("insert-image-tips-hover"),o.classList.toggle("show")})),t.addEventListener("mouseleave",(function(){n.classList.toggle("insert-image-tips-hover"),o.classList.toggle("show")})))}function L(){(0,l.m)(),w(),document.getElementById("upload-img-show").innerHTML=""}function k(){i(),q()}window.addEventListener("popstate",k),w(),(0,_.Ui)(),v(),q(),b(),B(),I(!0),document.querySelectorAll(".comt-addsmilies").forEach((e=>{e.addEventListener("click",(()=>{"block"==e.stlye.display?e.style.display="none":e.style.display="block"}))})),document.querySelectorAll(".comt-smilies a").forEach((e=>{e.addEventListener("click",(()=>{e.parentNode.style.display="none"}))})),document.addEventListener("ajax_comment_complete",L),document.addEventListener("pjax:complete",(function(){try{T(),i(),v(),I(!0),w(),g(),q(),b(),(0,_.Ui)(),B(),S(),E()}catch(e){console.warn(e)}})),(0,a.Cd)((function(){i(),S(),document.body.addEventListener("click",(function(e){if(e.target.parentNode==document.getElementById("comments-navi")&&"a"==e.target.nodeName.toLowerCase()){e.preventDefault(),e.stopPropagation();let t=e.target,n=t.pathname,o=new XMLHttpRequest;o.open("GET",t.getAttribute("href"),!0),o.responseType="document",o.onloadstart=()=>{let e=document.getElementById("comments-navi"),t=document.querySelector("ul.commentwrap"),n=document.getElementById("loading-comments"),o=document.getElementById("comments-list-title");e.remove(),t.remove(),n.style.display="block",(0,a.wy)(n,500,"show"),window.scrollTo({top:o.getBoundingClientRect().top+window.pageYOffset-o.clientTop-65,behavior:"smooth"})},o.onreadystatechange=function(){if(4==o.readyState&&200==o.status){let e=o.response,t=e.querySelector("ul.commentwrap"),r=e.getElementById("comments-navi"),s=document.getElementById("loading-comments");(0,a.wy)(s,200,"hide"),document.getElementById("loading-comments").insertAdjacentHTML("afterend",t.outerHTML),document.querySelector("ul.commentwrap").insertAdjacentHTML("afterend",r.outerHTML),(0,l.m)(),window.gtag&&gtag("config",_iro.google_analytics_id,{page_path:n}),(0,_.Ui)(),w();let i=document.querySelector("ul.commentwrap");window.scrollTo({top:i&&i.getBoundingClientRect().top+window.pageYOffset-i.clientTop-200,behavior:"smooth"})}},o.send()}})),g(),T(),E()}))},6636:e=>{var t={utf8:{stringToBytes:function(e){return t.bin.stringToBytes(unescape(encodeURIComponent(e)))},bytesToString:function(e){return decodeURIComponent(escape(t.bin.bytesToString(e)))}},bin:{stringToBytes:function(e){for(var t=[],n=0;n<e.length;n++)t.push(255&e.charCodeAt(n));return t},bytesToString:function(e){for(var t=[],n=0;n<e.length;n++)t.push(String.fromCharCode(e[n]));return t.join("")}}};e.exports=t},1048:e=>{var t,n;t="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",n={rotl:function(e,t){return e<<t|e>>>32-t},rotr:function(e,t){return e<<32-t|e>>>t},endian:function(e){if(e.constructor==Number)return 16711935&n.rotl(e,8)|4278255360&n.rotl(e,24);for(var t=0;t<e.length;t++)e[t]=n.endian(e[t]);return e},randomBytes:function(e){for(var t=[];e>0;e--)t.push(Math.floor(256*Math.random()));return t},bytesToWords:function(e){for(var t=[],n=0,o=0;n<e.length;n++,o+=8)t[o>>>5]|=e[n]<<24-o%32;return t},wordsToBytes:function(e){for(var t=[],n=0;n<32*e.length;n+=8)t.push(e[n>>>5]>>>24-n%32&255);return t},bytesToHex:function(e){for(var t=[],n=0;n<e.length;n++)t.push((e[n]>>>4).toString(16)),t.push((15&e[n]).toString(16));return t.join("")},hexToBytes:function(e){for(var t=[],n=0;n<e.length;n+=2)t.push(parseInt(e.substr(n,2),16));return t},bytesToBase64:function(e){for(var n=[],o=0;o<e.length;o+=3)for(var a=e[o]<<16|e[o+1]<<8|e[o+2],r=0;r<4;r++)8*o+6*r<=8*e.length?n.push(t.charAt(a>>>6*(3-r)&63)):n.push("=");return n.join("")},base64ToBytes:function(e){e=e.replace(/[^A-Z0-9+\/]/gi,"");for(var n=[],o=0,a=0;o<e.length;a=++o%4)0!=a&&n.push((t.indexOf(e.charAt(o-1))&Math.pow(2,-2*a+8)-1)<<2*a|t.indexOf(e.charAt(o))>>>6-2*a);return n}},e.exports=n},3149:e=>{function t(e){return!!e.constructor&&"function"==typeof e.constructor.isBuffer&&e.constructor.isBuffer(e)}e.exports=function(e){return null!=e&&(t(e)||function(e){return"function"==typeof e.readFloatLE&&"function"==typeof e.slice&&t(e.slice(0,0))}(e)||!!e._isBuffer)}},6706:(e,t,n)=>{var o,a,r,s,i;o=n(1048),a=n(6636).utf8,r=n(3149),s=n(6636).bin,(i=function(e,t){e.constructor==String?e=t&&"binary"===t.encoding?s.stringToBytes(e):a.stringToBytes(e):r(e)?e=Array.prototype.slice.call(e,0):Array.isArray(e)||e.constructor===Uint8Array||(e=e.toString());for(var n=o.bytesToWords(e),l=8*e.length,c=1732584193,u=-271733879,m=-1732584194,d=271733878,g=0;g<n.length;g++)n[g]=16711935&(n[g]<<8|n[g]>>>24)|4278255360&(n[g]<<24|n[g]>>>8);n[l>>>5]|=128<<l%32,n[14+(l+64>>>9<<4)]=l;var f=i._ff,p=i._gg,y=i._hh,h=i._ii;for(g=0;g<n.length;g+=16){var v=c,_=u,w=m,b=d;c=f(c,u,m,d,n[g+0],7,-680876936),d=f(d,c,u,m,n[g+1],12,-389564586),m=f(m,d,c,u,n[g+2],17,606105819),u=f(u,m,d,c,n[g+3],22,-1044525330),c=f(c,u,m,d,n[g+4],7,-176418897),d=f(d,c,u,m,n[g+5],12,1200080426),m=f(m,d,c,u,n[g+6],17,-1473231341),u=f(u,m,d,c,n[g+7],22,-45705983),c=f(c,u,m,d,n[g+8],7,1770035416),d=f(d,c,u,m,n[g+9],12,-1958414417),m=f(m,d,c,u,n[g+10],17,-42063),u=f(u,m,d,c,n[g+11],22,-1990404162),c=f(c,u,m,d,n[g+12],7,1804603682),d=f(d,c,u,m,n[g+13],12,-40341101),m=f(m,d,c,u,n[g+14],17,-1502002290),c=p(c,u=f(u,m,d,c,n[g+15],22,1236535329),m,d,n[g+1],5,-165796510),d=p(d,c,u,m,n[g+6],9,-1069501632),m=p(m,d,c,u,n[g+11],14,643717713),u=p(u,m,d,c,n[g+0],20,-373897302),c=p(c,u,m,d,n[g+5],5,-701558691),d=p(d,c,u,m,n[g+10],9,38016083),m=p(m,d,c,u,n[g+15],14,-660478335),u=p(u,m,d,c,n[g+4],20,-405537848),c=p(c,u,m,d,n[g+9],5,568446438),d=p(d,c,u,m,n[g+14],9,-1019803690),m=p(m,d,c,u,n[g+3],14,-187363961),u=p(u,m,d,c,n[g+8],20,1163531501),c=p(c,u,m,d,n[g+13],5,-1444681467),d=p(d,c,u,m,n[g+2],9,-51403784),m=p(m,d,c,u,n[g+7],14,1735328473),c=y(c,u=p(u,m,d,c,n[g+12],20,-1926607734),m,d,n[g+5],4,-378558),d=y(d,c,u,m,n[g+8],11,-2022574463),m=y(m,d,c,u,n[g+11],16,1839030562),u=y(u,m,d,c,n[g+14],23,-35309556),c=y(c,u,m,d,n[g+1],4,-1530992060),d=y(d,c,u,m,n[g+4],11,1272893353),m=y(m,d,c,u,n[g+7],16,-155497632),u=y(u,m,d,c,n[g+10],23,-1094730640),c=y(c,u,m,d,n[g+13],4,681279174),d=y(d,c,u,m,n[g+0],11,-358537222),m=y(m,d,c,u,n[g+3],16,-722521979),u=y(u,m,d,c,n[g+6],23,76029189),c=y(c,u,m,d,n[g+9],4,-640364487),d=y(d,c,u,m,n[g+12],11,-421815835),m=y(m,d,c,u,n[g+15],16,530742520),c=h(c,u=y(u,m,d,c,n[g+2],23,-995338651),m,d,n[g+0],6,-198630844),d=h(d,c,u,m,n[g+7],10,1126891415),m=h(m,d,c,u,n[g+14],15,-1416354905),u=h(u,m,d,c,n[g+5],21,-57434055),c=h(c,u,m,d,n[g+12],6,1700485571),d=h(d,c,u,m,n[g+3],10,-1894986606),m=h(m,d,c,u,n[g+10],15,-1051523),u=h(u,m,d,c,n[g+1],21,-2054922799),c=h(c,u,m,d,n[g+8],6,1873313359),d=h(d,c,u,m,n[g+15],10,-30611744),m=h(m,d,c,u,n[g+6],15,-1560198380),u=h(u,m,d,c,n[g+13],21,1309151649),c=h(c,u,m,d,n[g+4],6,-145523070),d=h(d,c,u,m,n[g+11],10,-1120210379),m=h(m,d,c,u,n[g+2],15,718787259),u=h(u,m,d,c,n[g+9],21,-343485551),c=c+v>>>0,u=u+_>>>0,m=m+w>>>0,d=d+b>>>0}return o.endian([c,u,m,d])})._ff=function(e,t,n,o,a,r,s){var i=e+(t&n|~t&o)+(a>>>0)+s;return(i<<r|i>>>32-r)+t},i._gg=function(e,t,n,o,a,r,s){var i=e+(t&o|n&~o)+(a>>>0)+s;return(i<<r|i>>>32-r)+t},i._hh=function(e,t,n,o,a,r,s){var i=e+(t^n^o)+(a>>>0)+s;return(i<<r|i>>>32-r)+t},i._ii=function(e,t,n,o,a,r,s){var i=e+(n^(t|~o))+(a>>>0)+s;return(i<<r|i>>>32-r)+t},i._blocksize=16,i._digestsize=16,e.exports=function(e,t){if(null==e)throw new Error("Illegal argument "+e);var n=o.wordsToBytes(i(e,t));return t&&t.asBytes?n:t&&t.asString?s.bytesToString(n):o.bytesToHex(n)}}},e=>{e.O(0,[1920],(()=>{return t=7791,e(e.s=t);var t}));e.O()}]);
//# sourceMappingURL=page.js.map