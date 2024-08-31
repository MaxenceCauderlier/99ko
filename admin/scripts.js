/**
 * @copyright (C) 2022, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * @author ShevAbam <me@shevarezo.fr>
 *
 * @package 299Ko https://github.com/299Ko/299ko
 */

/*!
 * Toastify js 1.12.0
 * https://github.com/apvarun/toastify-js
 * @license MIT licensed
 *
 * Copyright (C) 2018 Varun A P
 */
!function(t,o){"object"==typeof module&&module.exports?module.exports=o():t.Toastify=o()}(this,function(t){var o=function(t){return new o.lib.init(t)};function i(t,o){return o.offset[t]?isNaN(o.offset[t])?o.offset[t]:o.offset[t]+"px":"0px"}function s(t,o){return!!t&&"string"==typeof o&&!!(t.className&&t.className.trim().split(/\s+/gi).indexOf(o)>-1)}return o.defaults={oldestFirst:!0,text:"Toastify is awesome!",node:void 0,duration:4e3,selector:void 0,callback:function(){},destination:void 0,newWindow:!1,close:true,gravity:"toastify-top",positionLeft:!1,position:"",backgroundColor:"",avatar:"",className:"",stopOnFocus:!0,onClick:function(){},offset:{x:0,y:0},escapeMarkup:!0,ariaLive:"polite",style:{background:""}},o.lib=o.prototype={toastify:"1.12.0",constructor:o,init:function(t){return t||(t={}),this.options={},this.toastElement=null,this.options.text=t.text||o.defaults.text,this.options.node=t.node||o.defaults.node,this.options.duration=0===t.duration?0:t.duration||o.defaults.duration,this.options.selector=t.selector||o.defaults.selector,this.options.callback=t.callback||o.defaults.callback,this.options.destination=t.destination||o.defaults.destination,this.options.newWindow=t.newWindow||o.defaults.newWindow,this.options.close=t.close||o.defaults.close,this.options.gravity="bottom"===t.gravity?"toastify-bottom":o.defaults.gravity,this.options.positionLeft=t.positionLeft||o.defaults.positionLeft,this.options.position=t.position||o.defaults.position,this.options.backgroundColor=t.backgroundColor||o.defaults.backgroundColor,this.options.avatar=t.avatar||o.defaults.avatar,this.options.className=t.className||o.defaults.className,this.options.stopOnFocus=void 0===t.stopOnFocus?o.defaults.stopOnFocus:t.stopOnFocus,this.options.onClick=t.onClick||o.defaults.onClick,this.options.offset=t.offset||o.defaults.offset,this.options.escapeMarkup=void 0!==t.escapeMarkup?t.escapeMarkup:o.defaults.escapeMarkup,this.options.ariaLive=t.ariaLive||o.defaults.ariaLive,this.options.style=t.style||o.defaults.style,t.backgroundColor&&(this.options.style.background=t.backgroundColor),this},buildToast:function(){if(!this.options)throw"Toastify is not initialized";var t=document.createElement("div");for(var o in t.className="toastify on "+this.options.className,this.options.position?t.className+=" toastify-"+this.options.position:!0===this.options.positionLeft?(t.className+=" toastify-left",console.warn("Property `positionLeft` will be depreciated in further versions. Please use `position` instead.")):t.className+=" toastify-right",t.className+=" "+this.options.gravity,this.options.backgroundColor&&console.warn('DEPRECATION NOTICE: "backgroundColor" is being deprecated. Please use the "style.background" property.'),this.options.style)t.style[o]=this.options.style[o];if(this.options.ariaLive&&t.setAttribute("aria-live",this.options.ariaLive),this.options.node&&this.options.node.nodeType===Node.ELEMENT_NODE)t.appendChild(this.options.node);else if(this.options.escapeMarkup?t.innerText=this.options.text:t.innerHTML=this.options.text,""!==this.options.avatar){var s=document.createElement("img");s.src=this.options.avatar,s.className="toastify-avatar","left"==this.options.position||!0===this.options.positionLeft?t.appendChild(s):t.insertAdjacentElement("afterbegin",s)}if(!0===this.options.close){var e=document.createElement("button");e.type="button",e.setAttribute("aria-label","Close"),e.className="toast-close",e.innerHTML="<i class='fa-solid fa-xmark'></i>",e.addEventListener("click",(function(t){t.stopPropagation(),this.removeElement(this.toastElement),window.clearTimeout(this.toastElement.timeOutValue)}).bind(this));var n=window.innerWidth>0?window.innerWidth:screen.width;("left"==this.options.position||!0===this.options.positionLeft)&&n>360?t.insertAdjacentElement("afterbegin",e):t.appendChild(e)}if(this.options.stopOnFocus&&this.options.duration>0){var a=this;t.addEventListener("mouseover",function(o){window.clearTimeout(t.timeOutValue)}),t.addEventListener("mouseleave",function(){t.timeOutValue=window.setTimeout(function(){a.removeElement(t)},a.options.duration)})}if(void 0!==this.options.destination&&t.addEventListener("click",(function(t){t.stopPropagation(),!0===this.options.newWindow?window.open(this.options.destination,"_blank"):window.location=this.options.destination}).bind(this)),"function"==typeof this.options.onClick&&void 0===this.options.destination&&t.addEventListener("click",(function(t){t.stopPropagation(),this.options.onClick()}).bind(this)),"object"==typeof this.options.offset){var l=i("x",this.options),r=i("y",this.options),p="left"==this.options.position?l:"-"+l,d="toastify-top"==this.options.gravity?r:"-"+r;t.style.transform="translate("+p+","+d+")"}return t},showToast:function(){if(this.toastElement=this.buildToast(),!(t="string"==typeof this.options.selector?document.getElementById(this.options.selector):this.options.selector instanceof HTMLElement||"undefined"!=typeof ShadowRoot&&this.options.selector instanceof ShadowRoot?this.options.selector:document.body))throw"Root element is not defined";var t,i=o.defaults.oldestFirst?t.firstChild:t.lastChild;return t.insertBefore(this.toastElement,i),o.reposition(),this.options.duration>0&&(this.toastElement.timeOutValue=window.setTimeout((function(){this.removeElement(this.toastElement)}).bind(this),this.options.duration)),this},hideToast:function(){this.toastElement.timeOutValue&&clearTimeout(this.toastElement.timeOutValue),this.removeElement(this.toastElement)},removeElement:function(t){t.className=t.className.replace(" on",""),window.setTimeout((function(){this.options.node&&this.options.node.parentNode&&this.options.node.parentNode.removeChild(this.options.node),t.parentNode&&t.parentNode.removeChild(t),this.options.callback.call(t),o.reposition()}).bind(this),400)}},o.reposition=function(){for(var t,o={top:15,bottom:15},i={top:15,bottom:15},e={top:15,bottom:15},n=document.getElementsByClassName("toastify"),a=0;a<n.length;a++){t=!0===s(n[a],"toastify-top")?"toastify-top":"toastify-bottom";var l=n[a].offsetHeight;t=t.substr(9,t.length-1),(window.innerWidth>0?window.innerWidth:screen.width)<=360?(n[a].style[t]=e[t]+"px",e[t]+=l+15):!0===s(n[a],"toastify-left")?(n[a].style[t]=o[t]+"px",o[t]+=l+15):(n[a].style[t]=i[t]+"px",i[t]+=l+15)}return this},o.lib.init.prototype=o.lib,o});

document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll('.msg').forEach(function (item, index) {
        item.querySelector('.msg-button-close').addEventListener('click', function () {
            fadeOut(item);
        });

        setTimeout(function () {
            fadeOut(item);
        }, 8000 + index * 8000);

    });

    document.querySelectorAll('.tabs-container').forEach(x => tabify(x));

    // Login : btn Quitter redirection
    if (document.querySelector('#login input.alert')) {
        document.querySelector('#login input.alert').addEventListener('click', function () {
            document.location.href = this.getAttribute('rel');
        });
    }

    window.addEventListener("resize", resizeListener);
    resizeListener();

    document.querySelectorAll(".list-item-list li i").forEach(function (item) {
        item.addEventListener('click', function () {
            item.classList.toggle('rotate-180');
            getNextSibling(item, 'ul.list-item-list-sub').slideToggle(400);
        });
    });
});

function resizeListener() {
    var nav = document.querySelector('#adminNav');
    if (!nav.classList.contains('manuallyChanged')) {
        if (window.innerWidth > 1279) {
            nav.classList.remove('withoutText');
        } else {
            nav.classList.add('withoutText');
        }
    }
}

function changeMainNav() {
    var nav = document.querySelector('#adminNav');
    nav.classList.toggle('withoutText');
    nav.classList.add('manuallyChanged');
    return false;
}

function fadeOut(el) {
    el.style.opacity = 1;
    (function fade() {
        if ((el.style.opacity -= .03) < 0) {
            el.style.display = "none";
        } else {
            requestAnimationFrame(fade);
        }
    })();
};

function fadeIn(el, display) {
    el.style.opacity = 0;
    el.style.display = display || "block";
    (function fade() {
        var val = parseFloat(el.style.opacity);
        if (!((val += .03) > 1)) {
            el.style.opacity = val;
            requestAnimationFrame(fade);
        }
    })();
};

function tabify(element){
    const header = element.querySelector('.tabs-header');
        const content = element.querySelector('.tabs');
        const tab_headers = [...header.children];
        const tab_contents = [...content.children];
        tab_contents.forEach(x => x.style.display = 'none');
        let current_tab_index = - 1;
        function setTab(index){
            if (current_tab_index > - 1){
                tab_headers[ current_tab_index ].classList.remove("active");
                tab_contents[ current_tab_index ].style.display = 'none';
            }
            tab_headers[ index ].classList.add("active");
            tab_contents[ index ].style.display = 'block';
            current_tab_index = index;
        }
    default_tab_index = tab_headers.findIndex(x => {
        return [...x.classList].indexOf('default-tab') > - 1;
    });
    default_tab_index = default_tab_index === - 1 ? 0 : default_tab_index;
    setTab(default_tab_index);
    tab_headers.forEach((x, i) => x.onclick = event => setTab(i));
}


/* plain JS slideToggle https://github.com/ericbutler555/plain-js-slidetoggle
 *
 * var sidebar = document.querySelector('#sidebar');
 * sidebar.slideToggle(400);
 */
function _s(o, i, p, l) {
    void 0 === i && (i = 400), void 0 === l && (l = !1), o.style.overflow = "hidden", l && (o.style.display = "block");
    var n, t = window.getComputedStyle(o), s = parseFloat(t.getPropertyValue("height")), a = parseFloat(t.getPropertyValue("padding-top")), r = parseFloat(t.getPropertyValue("padding-bottom")), y = parseFloat(t.getPropertyValue("margin-top")), d = parseFloat(t.getPropertyValue("margin-bottom")), g = s / i, m = a / i, h = r / i, u = y / i, x = d / i;
    window.requestAnimationFrame(function t(e) {
        void 0 === n && (n = e);
        e -= n;
        l ? (o.style.height = g * e + "px", o.style.paddingTop = m * e + "px", o.style.paddingBottom = h * e + "px", o.style.marginTop = u * e + "px", o.style.marginBottom = x * e + "px") : (o.style.height = s - g * e + "px", o.style.paddingTop = a - m * e + "px", o.style.paddingBottom = r - h * e + "px", o.style.marginTop = y - u * e + "px", o.style.marginBottom = d - x * e + "px"), i <= e ? (o.style.height = "", o.style.paddingTop = "", o.style.paddingBottom = "", o.style.marginTop = "", o.style.marginBottom = "", o.style.overflow = "", l || (o.style.display = "none"), "function" == typeof p && p()) : window.requestAnimationFrame(t)
    })
}
HTMLElement.prototype.slideToggle = function (t, e) {
    0 === this.clientHeight ? _s(this, t, e, !0) : _s(this, t, e)
}, HTMLElement.prototype.slideUp = function (t, e) {
    _s(this, t, e)
}, HTMLElement.prototype.slideDown = function (t, e) {
    _s(this, t, e, !0)
};

/*
 * Get Next Sibling of an element, with or without selector
 *
 * var el = getNextSibling(element, 'div.class');
 */

var getNextSibling = function (elem, selector) {
    // Get the next sibling element
    var sibling = elem.nextElementSibling;
    // If there's no selector, return the first sibling
    if (!selector)
        return sibling;
    // If the sibling matches our selector, use it
    // If not, jump to the next sibling and continue the loop
    while (sibling) {
        if (sibling.matches(selector))
            return sibling;
        sibling = sibling.nextElementSibling
    }
};
