/* Last update: 21/05/2019 17:40:25 */(function() {
 /***
 * vnpJs - Core
 ***/
 function vnpJsCore(item, root) {
 var i;
 this.length = 0;
 if (typeof item == "string") item = vnpJs._select(this["selector"] = item, root);
 if (null == item) return this;
 if (typeof item == "function") vnpJs._closure(item, root); else if (typeof item != "object" || item.nodeType || (i = item.length) !== +i || item == item.window) this[this.length++] = item; else for (this.length = i = i > 0 ? ~~i : 0; i--; ) this[i] = item[i];
 }
 function vnpJs(item, root) {
 return new vnpJsCore(item, root);
 }
 var $ = vnpJs;
 $.fn = $.prototype = vnpJsCore.prototype;
 $._reserved = {
 reserved: 1,
 vnpJs: 1,
 expose: 1,
 noConflict: 1,
 fn: 1
 };
 $.vnpJs = function(o, chain) {
 var o2 = chain ? vnpJsCore.prototype : vnpJs;
 for (var k in o) !(k in vnpJs._reserved) && (o2[k] = o[k]);
 return o2;
 };
 $._select = function(s, r) {
 return s ? (r || document).querySelectorAll(s) : [];
 };
 $._closure = function(fn) {
 $.ready(function() {
 fn.call(document, vnpJs);
 });
 };
 $.expose = function(name, value) {
 vnpJs.expose.old[name] = window[name];
 window[name] = value;
 };
 $.expose.old = {};
 $.noConflict = function(all) {
 window["$"] = vnpJs.expose.old["$"];
 if (all) for (var k in vnpJs.expose.old) window[k] = vnpJs.expose.old[k];
 return this;
 };
 $.expose("$", vnpJs);
 $.expose("vnpJs", vnpJs);
 /***
 * vnpJs - Lib
 ***/
 var global = this;
 function require(id) {
 if ("$" + id in require._cache) return require._cache["$" + id];
 if ("$" + id in require._modules) return require._cache["$" + id] = require._modules["$" + id]._load();
 if (id in window) return window[id];
 throw new Error('Requested module "' + id + '" has not been defined.');
 }
 function provide(id, exports) {
 return require._cache["$" + id] = exports;
 }
 require._cache = {};
 require._modules = {};
 function Module(id, fn) {
 this.id = id;
 this.fn = fn;
 require._modules["$" + id] = this;
 }
 Module.prototype.require = function(id) {
 var parts, i;
 if (id.charAt(0) == ".") {
 parts = (this.id.replace(/\/.*?$/, "/") + id.replace(/\.js$/, "")).split("/");
 while (~(i = parts.indexOf("."))) parts.splice(i, 1);
 while ((i = parts.lastIndexOf("..")) > 0) parts.splice(i - 1, 2);
 id = parts.join("/");
 }
 return require(id);
 };
 Module.prototype._load = function() {
 var m = this;
 if (!m._loaded) {
 m._loaded = true;
 m.exports = {};
 m.fn.call(global, m, m.exports, function(id) {
 return m.require(id);
 }, global);
 }
 return m.exports;
 };
 Module.createPackage = function(id, modules, main) {
 var path, m;
 for (path in modules) {
 new Module(id + "/" + path, modules[path]);
 if (m = path.match(/^(.+)\/index$/)) new Module(id + "/" + m[1], modules[path]);
 }
 if (main) require._modules["$" + id] = require._modules["$" + id + "/" + main];
 };
 if (vnpJs && vnpJs.expose) {
 vnpJs.expose("global", global);
 vnpJs.expose("require", require);
 vnpJs.expose("provide", provide);
 vnpJs.expose("Module", Module);
 }
 /***
 * vnpFix - Fix Old Browser
 ***/
 Module.createPackage("vnpFix", {
 vnpFix: function(module, exports, require, global) {
 var _slice = Array.prototype.slice;
 try {
 _slice.call(document.documentElement);
 } catch (e) {
 Array.prototype.slice = function(begin, end) {
 end = typeof end !== "undefined" ? end : this.length;
 if (Object.prototype.toString.call(this) === "[object Array]") {
 return _slice.call(this, begin, end);
 }
 var i, cloned = [], size, len = this.length;
 var start = begin || 0;
 start = start >= 0 ? start : len + start;
 var upTo = end ? end : len;
 if (end < 0) {
 upTo = len + end;
 }
 size = upTo - start;
 if (size > 0) {
 cloned = new Array(size);
 if (this.charAt) {
 for (i = 0; i < size; i++) {
 cloned[i] = this.charAt(start + i);
 }
 } else {
 for (i = 0; i < size; i++) {
 cloned[i] = this[start + i];
 }
 }
 }
 return cloned;
 };
 }
 !('getComputedStyle' in this) && (this.getComputedStyle = (function () {
 function getPixelSize(element, style, property, fontSize) {
 var
 sizeWithSuffix = style[property],
 size = parseFloat(sizeWithSuffix),
 suffix = sizeWithSuffix.split(/\d/)[0],
 rootSize;

 fontSize = fontSize != null ? fontSize : /%|em/.test(suffix) && element.parentElement ? getPixelSize(element.parentElement, element.parentElement.currentStyle, 'fontSize', null) : 16;
 rootSize = property == 'fontSize' ? fontSize : /width/i.test(property) ? element.clientWidth : element.clientHeight;

 return (suffix == 'em') ? size * fontSize : (suffix == 'in') ? size * 96 : (suffix == 'pt') ? size * 96 / 72 : (suffix == '%') ? size / 100 * rootSize : size;
 }

 function setShortStyleProperty(style, property) {
 var
 borderSuffix = property == 'border' ? 'Width' : '',
 t = property + 'Top' + borderSuffix,
 r = property + 'Right' + borderSuffix,
 b = property + 'Bottom' + borderSuffix,
 l = property + 'Left' + borderSuffix;

 style[property] = (style[t] == style[r] == style[b] == style[l] ? [style[t]]
 : style[t] == style[b] && style[l] == style[r] ? [style[t], style[r]]
 : style[l] == style[r] ? [style[t], style[r], style[b]]
 : [style[t], style[r], style[b], style[l]]).join(' ');
 }

 function CSSStyleDeclaration(element) {
 var
 currentStyle = element.currentStyle,
 style = this,
 fontSize = getPixelSize(element, currentStyle, 'fontSize', null);

 for (property in currentStyle) {
 if (/width|height|margin.|padding.|border.+W/.test(property) && style[property] !== 'auto') {
 style[property] = getPixelSize(element, currentStyle, property, fontSize) + 'px';
 } else if (property === 'styleFloat') {
 style['float'] = currentStyle[property];
 } else {
 style[property] = currentStyle[property];
 }
 }

 setShortStyleProperty(style, 'margin');
 setShortStyleProperty(style, 'padding');
 setShortStyleProperty(style, 'border');

 style.fontSize = fontSize + 'px';

 return style;
 }

 CSSStyleDeclaration.prototype = {
 constructor: CSSStyleDeclaration,
 getPropertyPriority: function () {},
 getPropertyValue: function ( prop ) {
 return this[prop] || '';
 },
 item: function () {},
 removeProperty: function () {},
 setProperty: function () {},
 getPropertyCSSValue: function () {}
 };

 function getComputedStyle(element) {
 return new CSSStyleDeclaration(element);
 }

 return getComputedStyle;
 })(this));
 if (!document.querySelectorAll || !document.querySelector) {
 var style = document.createStyleSheet(), select = function(selector, maxCount) {
 var all = document.all, l = all.length, i, resultSet = [];
 style.addRule(selector, "foo:bar");
 for (i = 0; i < l; i += 1) {
 if (all[i].currentStyle.foo === "bar") {
 resultSet.push(all[i]);
 if (resultSet.length > maxCount) {
 break;
 }
 }
 }
 style.removeRule(0);
 return resultSet;
 };
 document.querySelectorAll = function(selector) {
 return select(selector, Infinity);
 };
 document.querySelector = function(selector) {
 return select(selector, 1)[0] || null;
 };
 }
 if (!String.prototype.trim) {
 String.prototype.trim = function() {
 return String(this).replace(/^\s\s*/, "").replace(/\s\s*$/, "");
 };
 }
 if (!Array.isArray) {
 Array.isArray = function(obj) {
 return Object.prototype.toString.call(obj) === "[object Array]";
 };
 }
 }
 }, "vnpFix");
 require("vnpFix");
 /***
 * vnpJs - Selector
 ***/
 Module.createPackage("vnpSel", {
 vnpSel: function(module, exports, require, global) {
 (function(name, context, definition) {
 if (typeof module != "undefined" && module.exports) module.exports = definition(); else if (typeof define == "function" && define.amd) define(definition); else context[name] = definition();
 })("vnpSel", this, function() {
 var classOnly = /^\.([\w\-]+)$/, doc = document, win = window, html = doc.documentElement, nodeType = "nodeType";
 var isAncestor = "compareDocumentPosition" in html ? function(element, container) {
 return (container.compareDocumentPosition(element) & 16) == 16;
 } : function(element, container) {
 container = container == doc || container == window ? html : container;
 return container !== element && container.contains(element);
 };
 function toArray(ar) {
 return [].slice.call(ar, 0);
 }
 function isNode(el) {
 var t;
 return el && typeof el === "object" && (t = el.nodeType) && (t == 1 || t == 9);
 }
 function arrayLike(o) {
 return typeof o === "object" && isFinite(o.length);
 }
 function flatten(ar) {
 for (var r = [], i = 0, l = ar.length; i < l; ++i) arrayLike(ar[i]) ? r = r.concat(ar[i]) : r[r.length] = ar[i];
 return r;
 }
 function uniq(ar) {
 var a = [], i, j;
 label: for (i = 0; i < ar.length; i++) {
 for (j = 0; j < a.length; j++) {
 if (a[j] == ar[i]) {
 continue label;
 }
 }
 a[a.length] = ar[i];
 }
 return a;
 }
 function normalizeRoot(root) {
 if (!root) return doc;
 if (typeof root == "string") return vnpSel(root)[0];
 if (!root[nodeType] && arrayLike(root)) return root[0];
 return root;
 }
 function vnpSel(selector, opt_root) {
 var m, root = normalizeRoot(opt_root);
 if (!root || !selector) return [];
 if (selector === win || isNode(selector)) {
 return !opt_root || selector !== win && isNode(root) && isAncestor(selector, root) ? [ selector ] : [];
 }
 if (selector && arrayLike(selector)) return flatten(selector);
 if (doc.getElementsByClassName && selector == "string" && (m = selector.match(classOnly))) {
 return toArray(root.getElementsByClassName(m[1]));
 }
 if (selector && (selector.document || selector.nodeType && selector.nodeType == 9)) {
 return !opt_root ? [ selector ] : [];
 }
 return toArray(root.querySelectorAll(selector));
 }
 vnpSel.uniq = uniq;
 return vnpSel;
 }, this);
 },
 vnpJsSrc: function(module, exports, require, global) {
 (function($) {
 var q = require("vnpSel");
 $._select = function(s, r) {
 return ($._select = function() {
 var b;
 if (typeof $.create == "function") return function(s, r) {
 return /^\s*</.test(s) ? $.create(s, r) : q(s, r);
 };
 try {
 b = require("vnpDOM");
 return function(s, r) {
 return /^\s*</.test(s) ? b.create(s, r) : q(s, r);
 };
 } catch (e) {}
 return q;
 }())(s, r);
 };
 $.vnpJs({
 find: function(s) {
 var r = [], i, l, j, k, els;
 for (i = 0, l = this.length; i < l; i++) {
 els = q(s, this[i]);
 for (j = 0, k = els.length; j < k; j++) r.push(els[j]);
 }
 return $(q.uniq(r));
 }
 }, true);
 })(vnpJs);
 }
 }, "vnpSel");
 require("vnpSel");
 require("vnpSel/vnpJsSrc");
 /***
 * vnpJs - vnpDOM
 ***/
 Module.createPackage("vnpDOM", {
 vnpDOM: function(module, exports, require, global) {
 (function(name, context, definition) {
 if (typeof module != "undefined" && module.exports) module.exports = definition(); else if (typeof define == "function" && define.amd) define(definition); else context[name] = definition();
 })("vnpDOM", this, function() {
 var win = window, doc = win.document, html = doc.documentElement, parentNode = "parentNode", specialAttributes = /^(checked|value|selected|disabled)$/i, specialTags = /^(select|fieldset|table|tbody|tfoot|td|tr|colgroup)$/i, simpleScriptTagRe = /\s*<script +src=['"]([^'"]+)['"]>/, table = [ "<table>", "</table>", 1 ], td = [ "<table><tbody><tr>", "</tr></tbody></table>", 3 ], option = [ "<select>", "</select>", 1 ], noscope = [ "_", "", 0, 1 ], tagMap = {
 thead: table,
 tbody: table,
 tfoot: table,
 colgroup: table,
 caption: table,
 tr: [ "<table><tbody>", "</tbody></table>", 2 ],
 th: td,
 td: td,
 col: [ "<table><colgroup>", "</colgroup></table>", 2 ],
 fieldset: [ "<form>", "</form>", 1 ],
 legend: [ "<form><fieldset>", "</fieldset></form>", 2 ],
 option: option,
 optgroup: option,
 script: noscope,
 style: noscope,
 link: noscope,
 param: noscope,
 base: noscope
 }, stateAttributes = /^(checked|selected|disabled)$/, hasClass, addClass, removeClass, uidMap = {}, uuids = 0, digit = /^-?[\d\.]+$/, dattr = /^data-(.+)$/, px = "px", setAttribute = "setAttribute", getAttribute = "getAttribute", features = function() {
 var e = doc.createElement("p");
 return {
 transform: function() {
 var props = [ "transform", "webkitTransform", "MozTransform", "OTransform", "msTransform" ], i;
 for (i = 0; i < props.length; i++) {
 if (props[i] in e.style) return props[i];
 }
 }(),
 classList: "classList" in e
 };
 }(), whitespaceRegex = /\s+/, toString = String.prototype.toString, unitless = {
 lineHeight: 1,
 zoom: 1,
 zIndex: 1,
 opacity: 1,
 boxFlex: 1,
 WebkitBoxFlex: 1,
 MozBoxFlex: 1
 }, query = doc.querySelectorAll && function(selector) {
 return doc.querySelectorAll(selector);
 };
 function getStyle(el, property) {
 var value = null, computed = getComputedStyle(el, "");
 computed && (value = computed[property]);
 return el.style[property] || value;
 }
 function isNode(node) {
 return node && node.nodeName && (node.nodeType == 1 || node.nodeType == 11);
 }
 function normalize(node, host, clone) {
 var i, l, ret;
 if (typeof node == "string") return vnpDOM.create(node);
 if (isNode(node)) node = [ node ];
 if (clone) {
 ret = [];
 for (i = 0, l = node.length; i < l; i++) ret[i] = cloneNode(host, node[i]);
 return ret;
 }
 return node;
 }
 function classReg(c) {
 return new RegExp("(^|\\s+)" + c + "(\\s+|$)");
 }
 function each(ar, fn, opt_scope, opt_rev) {
 var ind, i = 0, l = ar.length;
 for (;i < l; i++) {
 ind = opt_rev ? ar.length - i - 1 : i;
 fn.call(opt_scope || ar[ind], ar[ind], ind, ar);
 }
 return ar;
 }
 function deepEach(ar, fn, opt_scope) {
 for (var i = 0, l = ar.length; i < l; i++) {
 if (isNode(ar[i])) {
 deepEach(ar[i].childNodes, fn, opt_scope);
 fn.call(opt_scope || ar[i], ar[i], i, ar);
 }
 }
 return ar;
 }
 function camelize(s) {
 return s.replace(/-(.)/g, function(m, m1) {
 return m1.toUpperCase();
 });
 }
 function decamelize(s) {
 return s ? s.replace(/([a-z])([A-Z])/g, "$1-$2").toLowerCase() : s;
 }
 function data(el) {
 el[getAttribute]("data-node-uid") || el[setAttribute]("data-node-uid", ++uuids);
 var uid = el[getAttribute]("data-node-uid");
 return uidMap[uid] || (uidMap[uid] = {});
 }
 function clearData(el) {
 var uid = el[getAttribute]("data-node-uid");
 if (uid) delete uidMap[uid];
 }
 function dataValue(d) {
 var f;
 try {
 return d === null || d === undefined ? undefined : d === "true" ? true : d === "false" ? false : d === "null" ? null : (f = parseFloat(d)) == d ? f : d;
 } catch (e) {}
 return undefined;
 }
 function some(ar, fn, opt_scope) {
 for (var i = 0, j = ar.length; i < j; ++i) if (fn.call(opt_scope || null, ar[i], i, ar)) return true;
 return false;
 }
 function styleProperty(p) {
 p == "transform" && (p = features.transform) || /^transform-?[Oo]rigin$/.test(p) && (p = features.transform + "Origin");
 return p ? camelize(p) : null;
 }
 function insert(target, host, fn, rev) {
 var i = 0, self = host || this, r = [], nodes = query && typeof target == "string" && target.charAt(0) != "<" ? query(target) : target;
 each(normalize(nodes), function(t, j) {
 each(self, function(el) {
 fn(t, r[i++] = j > 0 ? cloneNode(self, el) : el);
 }, null, rev);
 }, this, rev);
 self.length = i;
 each(r, function(e) {
 self[--i] = e;
 }, null, !rev);
 return self;
 }
 function xy(el, x, y) {
 var $el = vnpDOM(el), style = $el.css("position"), offset = $el.offset(), rel = "relative", isRel = style == rel, delta = [ parseInt($el.css("left"), 10), parseInt($el.css("top"), 10) ];
 if (style == "static") {
 $el.css("position", rel);
 style = rel;
 }
 isNaN(delta[0]) && (delta[0] = isRel ? 0 : el.offsetLeft);
 isNaN(delta[1]) && (delta[1] = isRel ? 0 : el.offsetTop);
 x != null && (el.style.left = x - offset.left + delta[0] + px);
 y != null && (el.style.top = y - offset.top + delta[1] + px);
 }
 if (features.classList) {
 hasClass = function(el, c) {
 return el.classList.contains(c);
 };
 addClass = function(el, c) {
 el.classList.add(c);
 };
 removeClass = function(el, c) {
 el.classList.remove(c);
 };
 } else {
 hasClass = function(el, c) {
 return classReg(c).test(el.className);
 };
 addClass = function(el, c) {
 el.className = (el.className + " " + c).trim();
 };
 removeClass = function(el, c) {
 el.className = el.className.replace(classReg(c), " ").trim();
 };
 }
 function setter(el, v) {
 return typeof v == "function" ? v.call(el, el) : v;
 }
 function scroll(x, y, type) {
 var el = this[0];
 if (!el) return this;
 if (x == null && y == null) {
 return (isBody(el) ? getWindowScroll() : {
 x: el.scrollLeft,
 y: el.scrollTop
 })[type];
 }
 if (isBody(el)) {
 win.scrollTo(x, y);
 } else {
 x != null && (el.scrollLeft = x);
 y != null && (el.scrollTop = y);
 }
 return this;
 }
 function vnpDOMCore(elements) {
 this.length = 0;
 if (elements) {
 elements = typeof elements !== "string" && !elements.nodeType && typeof elements.length !== "undefined" ? elements : [ elements ];
 this.length = elements.length;
 for (var i = 0; i < elements.length; i++) this[i] = elements[i];
 }
 }
 vnpDOMCore.prototype = {
 get: function(index) {
 return this[index] || null;
 },
 each: function(fn, opt_scope) {
 return each(this, fn, opt_scope);
 },
 deepEach: function(fn, opt_scope) {
 return deepEach(this, fn, opt_scope);
 },
 map: function(fn, opt_reject) {
 var m = [], n, i;
 for (i = 0; i < this.length; i++) {
 n = fn.call(this, this[i], i);
 opt_reject ? opt_reject(n) && m.push(n) : m.push(n);
 }
 return m;
 },
 html: function(h, opt_text) {
 var method = opt_text ? "textContent" : "innerHTML", that = this, append = function(el, i) {
 each(normalize(h, that, i), function(node) {
 el.appendChild(node);
 });
 }, updateElement = function(el, i) {
 try {
 if (opt_text || typeof h == "string" && !specialTags.test(el.tagName)) {
 return el[method] = h;
 }
 } catch (e) {}
 append(el, i);
 };
 return typeof h != "undefined" ? this.empty().each(updateElement) : this[0] ? this[0][method] : "";
 },
 text: function(opt_text) {
 return this.html(opt_text, true);
 },
 append: function(node) {
 var that = this;
 return this.each(function(el, i) {
 each(normalize(node, that, i), function(i) {
 el.appendChild(i);
 });
 });
 },
 prepend: function(node) {
 var that = this;
 return this.each(function(el, i) {
 var first = el.firstChild;
 each(normalize(node, that, i), function(i) {
 el.insertBefore(i, first);
 });
 });
 },
 appendTo: function(target, opt_host) {
 return insert.call(this, target, opt_host, function(t, el) {
 t.appendChild(el);
 });
 },
 prependTo: function(target, opt_host) {
 return insert.call(this, target, opt_host, function(t, el) {
 t.insertBefore(el, t.firstChild);
 }, 1);
 },
 before: function(node) {
 var that = this;
 return this.each(function(el, i) {
 each(normalize(node, that, i), function(i) {
 el[parentNode].insertBefore(i, el);
 });
 });
 },
 after: function(node) {
 var that = this;
 return this.each(function(el, i) {
 each(normalize(node, that, i), function(i) {
 el[parentNode].insertBefore(i, el.nextSibling);
 }, null, 1);
 });
 },
 insertBefore: function(target, opt_host) {
 return insert.call(this, target, opt_host, function(t, el) {
 t[parentNode].insertBefore(el, t);
 });
 },
 insertAfter: function(target, opt_host) {
 return insert.call(this, target, opt_host, function(t, el) {
 var sibling = t.nextSibling;
 sibling ? t[parentNode].insertBefore(el, sibling) : t[parentNode].appendChild(el);
 }, 1);
 },
 replaceWith: function(node) {
 var that = this;
 return this.each(function(el, i) {
 each(normalize(node, that, i), function(i) {
 el[parentNode] && el[parentNode].replaceChild(i, el);
 });
 });
 },
 clone: function(opt_host) {
 var ret = [], l, i;
 for (i = 0, l = this.length; i < l; i++) ret[i] = cloneNode(opt_host || this, this[i]);
 return vnpDOM(ret);
 },
 addClass: function(c) {
 c = toString.call(c).split(whitespaceRegex);
 return this.each(function(el) {
 each(c, function(c) {
 if (c && !hasClass(el, setter(el, c))) addClass(el, setter(el, c));
 });
 });
 },
 removeClass: function(c) {
 c = toString.call(c).split(whitespaceRegex);
 return this.each(function(el) {
 each(c, function(c) {
 if (c && hasClass(el, setter(el, c))) removeClass(el, setter(el, c));
 });
 });
 },
 hasClass: function(c) {
 c = toString.call(c).split(whitespaceRegex);
 return some(this, function(el) {
 return some(c, function(c) {
 return c && hasClass(el, c);
 });
 });
 },
 toggleClass: function(c, opt_condition) {
 c = toString.call(c).split(whitespaceRegex);
 return this.each(function(el) {
 each(c, function(c) {
 if (c) {
 typeof opt_condition !== "undefined" ? opt_condition ? !hasClass(el, c) && addClass(el, c) : removeClass(el, c) : hasClass(el, c) ? removeClass(el, c) : addClass(el, c);
 }
 });
 });
 },
 show: function(opt_type) {
 opt_type = typeof opt_type == "string" ? opt_type : "block";
 return this.each(function(el) {
 el.style.display = opt_type;
 });
 },
 hide: function() {
 return this.each(function(el) {
 el.style.display = "none";
 });
 },
 toggle: function(opt_callback, opt_type) {
 opt_type = typeof opt_type == "string" ? opt_type : "block";
 typeof opt_callback != "function" && (opt_callback = null);
 return this.each(function(el) {
 el.style.display = el.offsetWidth || el.offsetHeight ? "none" : opt_type;
 opt_callback && opt_callback.call(el);
 });
 },
 first: function() {
 return vnpDOM(this.length ? this[0] : []);
 },
 last: function() {
 return vnpDOM(this.length ? this[this.length - 1] : []);
 },
 next: function() {
 return this.related("nextSibling");
 },
 previous: function() {
 return this.related("previousSibling");
 },
 parent: function() {
 return this.related(parentNode);
 },
 related: function(method) {
 return vnpDOM(this.map(function(el) {
 el = el[method];
 while (el && el.nodeType !== 1) {
 el = el[method];
 }
 return el || 0;
 }, function(el) {
 return el;
 }));
 },
 focus: function() {
 this.length && this[0].focus();
 return this;
 },
 blur: function() {
 this.length && this[0].blur();
 return this;
 },
 css: function(o, opt_v) {
 var p, iter = o;
 if (opt_v === undefined && typeof o == "string") {
 opt_v = this[0];
 if (!opt_v) return null;
 if (opt_v === doc || opt_v === win) {
 p = opt_v === doc ? vnpDOM.doc() : vnpDOM.viewport();
 return o == "width" ? p.width : o == "height" ? p.height : "";
 }
 return (o = styleProperty(o)) ? getStyle(opt_v, o) : null;
 }
 if (typeof o == "string") {
 iter = {};
 iter[o] = opt_v;
 }
 function fn(el, p, v) {
 for (var k in iter) {
 if (iter.hasOwnProperty(k)) {
 v = iter[k];
 (p = styleProperty(k)) && digit.test(v) && !(p in unitless) && (v += px);
 try {
 el.style[p] = setter(el, v);
 } catch (e) {}
 }
 }
 }
 return this.each(fn);
 },
 offset: function(opt_x, opt_y) {
 if (opt_x && typeof opt_x == "object" && (typeof opt_x.top == "number" || typeof opt_x.left == "number")) {
 return this.each(function(el) {
 xy(el, opt_x.left, opt_x.top);
 });
 } else if (typeof opt_x == "number" || typeof opt_y == "number") {
 return this.each(function(el) {
 xy(el, opt_x, opt_y);
 });
 }
 if (!this[0]) return {
 top: 0,
 left: 0,
 height: 0,
 width: 0
 };
 var el = this[0], de = el.ownerDocument.documentElement, bcr = el.getBoundingClientRect(), scroll = getWindowScroll(), width = el.offsetWidth, height = el.offsetHeight, top = bcr.top + scroll.y - Math.max(0, de && de.clientTop, doc.body.clientTop), left = bcr.left + scroll.x - Math.max(0, de && de.clientLeft, doc.body.clientLeft);
 return {
 top: top,
 left: left,
 height: height,
 width: width
 };
 },
 dim: function() {
 if (!this.length) return {
 height: 0,
 width: 0
 };
 var el = this[0], de = el.nodeType == 9 && el.documentElement, orig = !de && !!el.style && !el.offsetWidth && !el.offsetHeight ? function(t) {
 var s = {
 position: el.style.position || "",
 visibility: el.style.visibility || "",
 display: el.style.display || ""
 };
 t.first().css({
 position: "absolute",
 visibility: "hidden",
 display: "block"
 });
 return s;
 }(this) : null, width = de ? Math.max(el.body.scrollWidth, el.body.offsetWidth, de.scrollWidth, de.offsetWidth, de.clientWidth) : el.offsetWidth, height = de ? Math.max(el.body.scrollHeight, el.body.offsetHeight, de.scrollHeight, de.offsetHeight, de.clientHeight) : el.offsetHeight;
 orig && this.first().css(orig);
 return {
 height: height,
 width: width
 };
 },
 attr: function(k, opt_v) {
 var el = this[0], n;
 if (typeof k != "string" && !(k instanceof String)) {
 for (n in k) {
 k.hasOwnProperty(n) && this.attr(n, k[n]);
 }
 return this;
 }
 return typeof opt_v == "undefined" ? !el ? null : specialAttributes.test(k) ? stateAttributes.test(k) && typeof el[k] == "string" ? true : el[k] : el[getAttribute](k) : this.each(function(el) {
 specialAttributes.test(k) ? el[k] = setter(el, opt_v) : el[setAttribute](k, setter(el, opt_v));
 });
 },
 removeAttr: function(k) {
 return this.each(function(el) {
 stateAttributes.test(k) ? el[k] = false : el.removeAttribute(k);
 });
 },
 val: function(s) {
 return typeof s == "string" || typeof s == "number" ? this.attr("value", s) : this.length ? this[0].value : null;
 },
 data: function(opt_k, opt_v) {
 var el = this[0], o, m;
 if (typeof opt_v === "undefined") {
 if (!el) return null;
 o = data(el);
 if (typeof opt_k === "undefined") {
 each(el.attributes, function(a) {
 (m = ("" + a.name).match(dattr)) && (o[camelize(m[1])] = dataValue(a.value));
 });
 return o;
 } else {
 if (typeof o[opt_k] === "undefined") o[opt_k] = dataValue(this.attr("data-" + decamelize(opt_k)));
 return o[opt_k];
 }
 } else {
 return this.each(function(el) {
 data(el)[opt_k] = opt_v;
 });
 }
 },
 remove: function() {
 this.deepEach(clearData);
 return this.detach();
 },
 empty: function() {
 return this.each(function(el) {
 deepEach(el.childNodes, clearData);
 while (el.firstChild) {
 el.removeChild(el.firstChild);
 }
 });
 },
 detach: function() {
 return this.each(function(el) {
 el[parentNode] && el[parentNode].removeChild(el);
 });
 },
 scrollTop: function(y) {
 return scroll.call(this, null, y, "y");
 },
 scrollLeft: function(x) {
 return scroll.call(this, x, null, "x");
 }
 };
 function cloneNode(host, el) {
 var c = el.cloneNode(true), cloneElems, elElems, i;
 if (host.$ && typeof host.cloneEvents == "function") {
 host.$(c).cloneEvents(el);
 cloneElems = host.$(c).find("*");
 elElems = host.$(el).find("*");
 for (i = 0; i < elElems.length; i++) host.$(cloneElems[i]).cloneEvents(elElems[i]);
 }
 return c;
 }
 function isBody(element) {
 return element === win || /^(?:body|html)$/i.test(element.tagName);
 }
 function getWindowScroll() {
 return {
 x: win.pageXOffset || html.scrollLeft,
 y: win.pageYOffset || html.scrollTop
 };
 }
 function createScriptFromHtml(html) {
 var scriptEl = document.createElement("script"), matches = html.match(simpleScriptTagRe);
 scriptEl.src = matches[1];
 return scriptEl;
 }
 function vnpDOM(els) {
 return new vnpDOMCore(els);
 }
 vnpDOM.setQueryEngine = function(q) {
 query = q;
 delete vnpDOM.setQueryEngine;
 };
 vnpDOM.aug = function(o, target) {
 for (var k in o) {
 o.hasOwnProperty(k) && ((target || vnpDOMCore.prototype)[k] = o[k]);
 }
 };
 vnpDOM.create = function(node) {
 return typeof node == "string" && node !== "" ? function() {
 if (simpleScriptTagRe.test(node)) return [ createScriptFromHtml(node) ];
 var tag = node.match(/^\s*<([^\s>]+)/), el = doc.createElement("div"), els = [], p = tag ? tagMap[tag[1].toLowerCase()] : null, dep = p ? p[2] + 1 : 1, ns = p && p[3], pn = parentNode;
 el.innerHTML = p ? p[0] + node + p[1] : node;
 while (dep--) el = el.firstChild;
 if (ns && el && el.nodeType !== 1) el = el.nextSibling;
 do {
 if (!tag || el.nodeType == 1) {
 els.push(el);
 }
 } while (el = el.nextSibling);
 each(els, function(el) {
 el[pn] && el[pn].removeChild(el);
 });
 return els;
 }() : isNode(node) ? [ node.cloneNode(true) ] : [];
 };
 vnpDOM.doc = function() {
 var vp = vnpDOM.viewport();
 return {
 width: Math.max(doc.body.scrollWidth, html.scrollWidth, vp.width),
 height: Math.max(doc.body.scrollHeight, html.scrollHeight, vp.height)
 };
 };
 vnpDOM.firstChild = function(el) {
 for (var c = el.childNodes, i = 0, j = c && c.length || 0, e; i < j; i++) {
 if (c[i].nodeType === 1) e = c[j = i];
 }
 return e;
 };
 vnpDOM.viewport = function() {
 return {
 width: win.innerWidth,
 height: win.innerHeight
 };
 };
 vnpDOM.isAncestor = "compareDocumentPosition" in html ? function(container, element) {
 return (container.compareDocumentPosition(element) & 16) == 16;
 } : function(container, element) {
 return container !== element && container.contains(element);
 };
 return vnpDOM;
 });
 },
 vnpJsSrc: function(module, exports, require, global) {
 (function($) {
 var b = require("vnpDOM");
 b.setQueryEngine($);
 $.vnpJs(b);
 $.vnpJs(b(), true);
 $.vnpJs({
 create: function(node) {
 return $(b.create(node));
 }
 });
 function indexOf(ar, val) {
 for (var i = 0; i < ar.length; i++) if (ar[i] === val) return i;
 return -1;
 }
 function uniq(ar) {
 var r = [], i = 0, j = 0, k, item, inIt;
 for (;item = ar[i]; ++i) {
 inIt = false;
 for (k = 0; k < r.length; ++k) {
 if (r[k] === item) {
 inIt = true;
 break;
 }
 }
 if (!inIt) r[j++] = item;
 }
 return r;
 }
 $.vnpJs({
 parents: function(selector, closest) {
 if (!this.length) return this;
 if (!selector) selector = "*";
 var collection = $(selector), j, k, p, r = [];
 for (j = 0, k = this.length; j < k; j++) {
 p = this[j];
 while (p = p.parentNode) {
 if (~indexOf(collection, p)) {
 r.push(p);
 if (closest) break;
 }
 }
 }
 return $(uniq(r));
 },
 parent: function() {
 return $(uniq(b(this).parent()));
 },
 closest: function(selector) {
 return this.parents(selector, true);
 },
 first: function() {
 return $(this.length ? this[0] : this);
 },
 last: function() {
 return $(this.length ? this[this.length - 1] : []);
 },
 next: function() {
 return $(b(this).next());
 },
 previous: function() {
 return $(b(this).previous());
 },
 related: function(t) {
 return $(b(this).related(t));
 },
 appendTo: function(t) {
 return b(this.selector).appendTo(t, this);
 },
 prependTo: function(t) {
 return b(this.selector).prependTo(t, this);
 },
 insertAfter: function(t) {
 return b(this.selector).insertAfter(t, this);
 },
 insertBefore: function(t) {
 return b(this.selector).insertBefore(t, this);
 },
 clone: function() {
 return $(b(this).clone(this));
 },
 siblings: function() {
 var i, l, p, r = [];
 for (i = 0, l = this.length; i < l; i++) {
 p = this[i];
 while (p = p.previousSibling) p.nodeType == 1 && r.push(p);
 p = this[i];
 while (p = p.nextSibling) p.nodeType == 1 && r.push(p);
 }
 return $(r);
 },
 children: function() {
 var i, l, el, r = [];
 for (i = 0, l = this.length; i < l; i++) {
 if (!(el = b.firstChild(this[i]))) continue;
 r.push(el);
 while (el = el.nextSibling) el.nodeType == 1 && r.push(el);
 }
 return $(uniq(r));
 },
 height: function(v) {
 return dimension.call(this, "height", v);
 },
 width: function(v) {
 return dimension.call(this, "width", v);
 },
 outerHeight: function(margin) {
 var total_height = dimension.call(this, "height");
 if (margin) {
 var margin_top = parseInt($(b(this)).css("margin-top"));
 var margin_bot = parseInt($(b(this)).css("margin-bottom"));
 total_height = total_height + margin_top + margin_bot;
 }
 return total_height;
 },
 fadeIn: function(time) {
 time = time / 100 || 3;
 el = $(this.selector);
 if (el.css("opacity") == 0 || el.css("display") == "none") {
 for (var i = 1; i <= 100; i++) {
 setTimeout(function(x) {
 return function() {
 fadeOpacity(el, x);
 if (x == 1) el.css("display", "block");
 };
 }(i), i * time);
 }
 }
 },
 fadeOut: function(time) {
 time = time / 100 || 3;
 el = $(this.selector);
 if (el.css("opacity") == 1 || el.css("display") == "block" || el.css("display") == "inline-block") {
 for (var i = 1; i <= 100; i++) {
 setTimeout(function(x) {
 return function() {
 fadeOpacity(el, x);
 if (x == 1) {
 el.css("display", "none");
 }
 };
 }(100 - i), i * time);
 }
 }
 }
 }, true);
 function fadeOpacity(el, op) {
 el.css("opacity", op / 100);
 el.css("filter", "alpha(opacity=" + op + ")");
 }
 function dimension(type, opt_v) {
 return typeof opt_v == "undefined" ? b(this).dim()[type] : this.css(type, opt_v);
 }
 })(vnpJs);
 }
 }, "vnpDOM");
 require("vnpDOM");
 require("vnpDOM/vnpJsSrc");
 /***
 * vnpJs - vnpJar - vnpCookie
 ***/
 Module.createPackage("vnpJar", {
 vnpJar: function(module, exports, require, global) {
 var vnpJar;
 vnpJar = typeof exports !== "undefined" && exports !== null ? exports : this["vnpJar"] = {};
 vnpJar.Cookie = function() {
 function Cookie(name, value, options) {
 var date, _base;
 this.name = name;
 this.value = value;
 this.options = options;
 if (this.value === null) {
 this.value = "";
 this.options.expires = -(60 * 60 * 24);
 }
 if (this.options.expires) {
 if (typeof this.options.expires === "number") {
 date = new Date();
 date.setTime(date.getTime() + this.options.expires * 1e3);
 this.options.expires = date;
 }
 if (this.options.expires instanceof Date) {
 this.options.expires = this.options.expires.toUTCString();
 }
 }
 (_base = this.options).path || (_base.path = "/");
 }
 Cookie.prototype.toString = function() {
 var domain, expires, path, secure;
 path = "; path=" + this.options.path;
 expires = this.options.expires ? "; expires=" + this.options.expires : "";
 domain = this.options.domain ? "; domain=" + this.options.domain : "";
 secure = this.options.secure ? "; secure" : "";
 return [ this.name, "=", this.value, expires, path, domain, secure ].join("");
 };
 return Cookie;
 }();
 vnpJar.Jar = function() {
 function Jar() {}
 Jar.prototype.parse = function() {
 var cookie, m, _i, _len, _ref;
 this.cookies = {};
 _ref = this._getCookies().split(/;\s/g);
 for (_i = 0, _len = _ref.length; _i < _len; _i++) {
 cookie = _ref[_i];
 m = cookie.match(/([^=]+)=(.*)/);
 if (Array.isArray(m)) {
 this.cookies[m[1]] = m[2];
 }
 }
 };
 Jar.prototype.encode = function(value) {
 return encodeURIComponent(JSON.stringify(value));
 };
 Jar.prototype.decode = function(value) {
 return JSON.parse(decodeURIComponent(value));
 };
 Jar.prototype.get = function(name, options) {
 var value;
 if (options == null) {
 options = {};
 }
 value = this.cookies[name];
 if (!("raw" in options) || !options.raw) {
 try {
 value = this.decode(value);
 } catch (e) {
 return;
 }
 }
 return value;
 };
 Jar.prototype.set = function(name, value, options) {
 var cookie;
 if (options == null) {
 options = {};
 }
 if (!("raw" in options) || !options.raw) {
 value = this.encode(value);
 }
 cookie = new vnpJar.Cookie(name, value, options);
 this._setCookie(cookie);
 this.cookies[name] = value;
 };
 return Jar;
 }();
 if (typeof process !== "undefined" && process !== null ? process.pid : void 0) {
 require("./node");
 }
 },
 vnpJsSrc: function(module, exports, require, global) {
 var __hasProp = {}.hasOwnProperty, __extends = function(child, parent) {
 for (var key in parent) {
 if (__hasProp.call(parent, key)) child[key] = parent[key];
 }
 function ctor() {
 this.constructor = child;
 }
 ctor.prototype = parent.prototype;
 child.prototype = new ctor();
 child.__super__ = parent.prototype;
 return child;
 };
 (function($) {
 var vnpJar;
 vnpJar = require("vnpJar");
 vnpJar.Jar = function(_super) {
 __extends(Jar, _super);
 function Jar() {
 return Jar.__super__.constructor.apply(this, arguments);
 }
 Jar.prototype._getCookies = function() {
 return document.cookie;
 };
 Jar.prototype._setCookie = function(cookie) {
 document.cookie = cookie.toString();
 };
 Jar.prototype.get = function() {
 this.parse();
 return Jar.__super__.get.apply(this, arguments);
 };
 Jar.prototype.set = function() {
 this.parse();
 return Jar.__super__.set.apply(this, arguments);
 };
 return Jar;
 }(vnpJar.Jar);
 return $.vnpJs({
 vnpJar: new vnpJar.Jar(),
 cookie: function(name, value, options) {
 if (value != null) {
 return $.vnpJar.set(name, value, options);
 } else {
 return $.vnpJar.get(name);
 }
 },
 removeCookie: function(name) {
 return $.vnpJar.set(name, "", {
 expires: -1
 });
 }
 });
 })(vnpJs);
 }
 }, "vnpJar");
 require("vnpJar");
 require("vnpJar/vnpJsSrc");
 /***
 * vnpJs - vnpBrowser
 ***/
 Module.createPackage("vnpBrowser", {
 vnpBrowser: function(module, exports, require, global) {
 !function(name, definition) {
 if (typeof module != "undefined" && module.exports) module.exports["browser"] = definition(); else if (typeof define == "function") define(definition); else this[name] = definition();
 }("vnpBrowser", function() {
 var ua = navigator.userAgent, t = true, ie = /(msie|trident)/i.test(ua), opera = /opera/i.test(ua) || /opr/i.test(ua), firefox = /firefox/i.test(ua), chrome = /chrome|crios/i.test(ua), webkitVersion = /version\/(\d+(\.\d+)?)/i, firefoxVersion = /firefox\/(\d+(\.\d+)?)/i;
 function detect() {
 if (ie) return {
 name: "Internet Explorer",
 msie: t,
 version: ua.match(/(msie |rv:)(\d+(\.\d+)?)/i)[2]
 };
 if (opera) return {
 name: "Opera",
 opera: t,
 version: ua.match(webkitVersion) ? ua.match(webkitVersion)[1] : ua.match(/opr\/(\d+(\.\d+)?)/i)[1]
 };
 if (firefox) return {
 name: "Firefox",
 firefox: t,
 version: ua.match(firefoxVersion)[1]
 };
 if (chrome) return {
 name: "Chrome",
 chrome: t,
 version: ua.match(/(?:chrome|crios)\/(\d+(\.\d+)?)/i)[1]
 };
 return {};
 }
 var vnpBrowser = detect();
 return vnpBrowser;
 });
 },
 vnpJsSrc: function(module, exports, require, global) {
 !function($) {
 var browser = require("vnpBrowser");
 $.vnpJs({
 browser: browser.browser
 });
 }(vnpJs);
 }
 }, "vnpBrowser");
 require("vnpBrowser");
 require("vnpBrowser/vnpJsSrc");
 /***
 * vnpJs - vnpRequest
 ***/
 Module.createPackage("vnpReq", {
 vnpReq: function(module, exports, require, global) {
 !function(name, context, definition) {
 if (typeof module != "undefined" && module.exports) module.exports = definition(); else if (typeof define == "function" && define.amd) define(definition); else context[name] = definition();
 }("vnpReq", this, function() {
 var win = window, doc = document, httpsRe = /^http/, twoHundo = /^(20\d|1223)$/, byTag = "getElementsByTagName", readyState = "readyState", contentType = "Content-Type", requestedWith = "X-Requested-With", head = doc[byTag]("head")[0], uniqid = 0, callbackPrefix = "vnpReq_" + +new Date(), lastValue, xmlHttpRequest = "XMLHttpRequest", xDomainRequest = "XDomainRequest", noop = function() {}, isArray = typeof Array.isArray == "function" ? Array.isArray : function(a) {
 return a instanceof Array;
 }, defaultHeaders = {
 contentType: "application/x-www-form-urlencoded",
 requestedWith: xmlHttpRequest,
 accept: {
 "*": "text/javascript, text/html, application/xml, text/xml, */*",
 xml: "application/xml, text/xml",
 html: "text/html",
 text: "text/plain",
 json: "application/json, text/javascript",
 js: "application/javascript, text/javascript"
 }
 }, xhr = function(o) {
 if (o["crossOrigin"] === true) {
 var xhr = win[xmlHttpRequest] ? new XMLHttpRequest() : null;
 if (xhr && "withCredentials" in xhr) {
 return xhr;
 } else if (win[xDomainRequest]) {
 return new XDomainRequest();
 } else {
 throw new Error("Browser does not support cross-origin requests");
 }
 } else if (win[xmlHttpRequest]) {
 return new XMLHttpRequest();
 } else {
 return new ActiveXObject("Microsoft.XMLHTTP");
 }
 }, globalSetupOptions = {
 dataFilter: function(data) {
 return data;
 }
 };
 function succeed(request) {
 return httpsRe.test(window.location.protocol) ? twoHundo.test(request.status) : !!request.response;
 }
 function handleReadyState(r, success, error) {
 return function() {
 if (r._aborted) return error(r.request);
 if (r.request && r.request[readyState] == 4) {
 r.request.onreadystatechange = noop;
 if (succeed(r.request)) success(r.request); else error(r.request);
 }
 };
 }
 function setHeaders(http, o) {
 var headers = o["headers"] || {}, h;
 headers["Accept"] = headers["Accept"] || defaultHeaders["accept"][o["type"]] || defaultHeaders["accept"]["*"];
 var isAFormData = typeof FormData === "function" && o["data"] instanceof FormData;
 if (!o["crossOrigin"] && !headers[requestedWith]) headers[requestedWith] = defaultHeaders["requestedWith"];
 if (!headers[contentType] && !isAFormData) headers[contentType] = o["contentType"] || defaultHeaders["contentType"];
 for (h in headers) headers.hasOwnProperty(h) && "setRequestHeader" in http && http.setRequestHeader(h, headers[h]);
 }
 function setCredentials(http, o) {
 if (typeof o["withCredentials"] !== "undefined" && typeof http.withCredentials !== "undefined") {
 http.withCredentials = !!o["withCredentials"];
 }
 }
 function generalCallback(data) {
 lastValue = data;
 }
 function urlappend(url, s) {
 return url + (/\?/.test(url) ? "&" : "?") + s;
 }
 function handleJsonp(o, fn, err, url) {
 var reqId = uniqid++, cbkey = o["jsonpCallback"] || "callback", cbval = o["jsonpCallbackName"] || vnpReq.getcallbackPrefix(reqId), cbreg = new RegExp("((^|\\?|&)" + cbkey + ")=([^&]+)"), match = url.match(cbreg), script = doc.createElement("script"), loaded = 0, isIE10 = navigator.userAgent.indexOf("MSIE 10.0") !== -1;
 if (match) {
 if (match[3] === "?") {
 url = url.replace(cbreg, "$1=" + cbval);
 } else {
 cbval = match[3];
 }
 } else {
 url = urlappend(url, cbkey + "=" + cbval);
 }
 win[cbval] = generalCallback;
 script.type = "text/javascript";
 script.src = url;
 script.async = true;
 if (typeof script.onreadystatechange !== "undefined" && !isIE10) {
 script.htmlFor = script.id = "_vnpReq_" + reqId;
 }
 script.onload = script.onreadystatechange = function() {
 if (script[readyState] && script[readyState] !== "complete" && script[readyState] !== "loaded" || loaded) {
 return false;
 }
 script.onload = script.onreadystatechange = null;
 script.onclick && script.onclick();
 fn(lastValue);
 lastValue = undefined;
 head.removeChild(script);
 loaded = 1;
 };
 head.appendChild(script);
 return {
 abort: function() {
 script.onload = script.onreadystatechange = null;
 err({}, "Request is aborted: timeout", {});
 lastValue = undefined;
 head.removeChild(script);
 loaded = 1;
 }
 };
 }
 function getRequest(fn, err) {
 var o = this.o, method = (o["method"] || "GET").toUpperCase(), url = typeof o === "string" ? o : o["url"], data = o["processData"] !== false && o["data"] && typeof o["data"] !== "string" ? vnpReq.toQueryString(o["data"]) : o["data"] || null, http, sendWait = false;
 if ((o["type"] == "jsonp" || method == "GET") && data) {
 url = urlappend(url, data);
 data = null;
 }
 if (o["type"] == "jsonp") return handleJsonp(o, fn, err, url);
 http = o.xhr && o.xhr(o) || xhr(o);
 http.open(method, url, o["async"] === false ? false : true);
 setHeaders(http, o);
 setCredentials(http, o);
 if (win[xDomainRequest] && http instanceof win[xDomainRequest]) {
 http.onload = fn;
 http.onerror = err;
 http.onprogress = function() {};
 sendWait = true;
 } else {
 http.onreadystatechange = handleReadyState(this, fn, err);
 }
 o["before"] && o["before"](http);
 if (sendWait) {
 setTimeout(function() {
 http.send(data);
 }, 200);
 } else {
 http.send(data);
 }
 return http;
 }
 function vnpReqCore(o, fn) {
 this.o = o;
 this.fn = fn;
 init.apply(this, arguments);
 }
 function setType(header) {
 if (header.match("json")) return "json";
 if (header.match("javascript")) return "js";
 if (header.match("text")) return "html";
 if (header.match("xml")) return "xml";
 }
 function init(o, fn) {
 this.url = typeof o == "string" ? o : o["url"];
 this.timeout = null;
 this._fulfilled = false;
 this._successHandler = function() {};
 this._fulfillmentHandlers = [];
 this._errorHandlers = [];
 this._completeHandlers = [];
 this._erred = false;
 this._responseArgs = {};
 var self = this;
 fn = fn || function() {};
 if (o["timeout"]) {
 this.timeout = setTimeout(function() {
 self.abort();
 }, o["timeout"]);
 }
 if (o["success"]) {
 this._successHandler = function() {
 o["success"].apply(o, arguments);
 };
 }
 if (o["error"]) {
 this._errorHandlers.push(function() {
 o["error"].apply(o, arguments);
 });
 }
 if (o["complete"]) {
 this._completeHandlers.push(function() {
 o["complete"].apply(o, arguments);
 });
 }
 function complete(resp) {
 o["timeout"] && clearTimeout(self.timeout);
 self.timeout = null;
 while (self._completeHandlers.length > 0) {
 self._completeHandlers.shift()(resp);
 }
 }
 function success(resp) {
 var type = o["type"] || setType(resp.getResponseHeader("Content-Type"));
 resp = type !== "jsonp" ? self.request : resp;
 var filteredResponse = globalSetupOptions.dataFilter(resp.responseText, type), r = filteredResponse;
 try {
 resp.responseText = r;
 } catch (e) {}
 if (r) {
 switch (type) {
 case "json":
 try {
 resp = win.JSON ? win.JSON.parse(r) : eval("(" + r + ")");
 } catch (err) {
 return error(resp, "Could not parse JSON in response", err);
 }
 break;

 case "js":
 resp = eval(r);
 break;

 case "html":
 resp = r;
 break;

 case "xml":
 resp = resp.responseXML && resp.responseXML.parseError && resp.responseXML.parseError.errorCode && resp.responseXML.parseError.reason ? null : resp.responseXML;
 break;
 }
 }
 self._responseArgs.resp = resp;
 self._fulfilled = true;
 fn(resp);
 self._successHandler(resp);
 while (self._fulfillmentHandlers.length > 0) {
 resp = self._fulfillmentHandlers.shift()(resp);
 }
 complete(resp);
 }
 function error(resp, msg, t) {
 resp = self.request;
 self._responseArgs.resp = resp;
 self._responseArgs.msg = msg;
 self._responseArgs.t = t;
 self._erred = true;
 while (self._errorHandlers.length > 0) {
 self._errorHandlers.shift()(resp, msg, t);
 }
 complete(resp);
 }
 this.request = getRequest.call(this, success, error);
 }
 vnpReqCore.prototype = {
 abort: function() {
 this._aborted = true;
 this.request.abort();
 },
 retry: function() {
 init.call(this, this.o, this.fn);
 },
 then: function(success, fail) {
 success = success || function() {};
 fail = fail || function() {};
 if (this._fulfilled) {
 this._responseArgs.resp = success(this._responseArgs.resp);
 } else if (this._erred) {
 fail(this._responseArgs.resp, this._responseArgs.msg, this._responseArgs.t);
 } else {
 this._fulfillmentHandlers.push(success);
 this._errorHandlers.push(fail);
 }
 return this;
 },
 always: function(fn) {
 if (this._fulfilled || this._erred) {
 fn(this._responseArgs.resp);
 } else {
 this._completeHandlers.push(fn);
 }
 return this;
 },
 fail: function(fn) {
 if (this._erred) {
 fn(this._responseArgs.resp, this._responseArgs.msg, this._responseArgs.t);
 } else {
 this._errorHandlers.push(fn);
 }
 return this;
 },
 "catch": function(fn) {
 return this.fail(fn);
 }
 };
 function vnpReq(o, fn) {
 return new vnpReqCore(o, fn);
 }
 function normalize(s) {
 return s ? s.replace(/\r?\n/g, "\r\n") : "";
 }
 function serial(el, cb) {
 var n = el.name, t = el.tagName.toLowerCase(), optCb = function(o) {
 if (o && !o["disabled"]) cb(n, normalize(o["attributes"]["value"] && o["attributes"]["value"]["specified"] ? o["value"] : o["text"]));
 }, ch, ra, val, i;
 if (el.disabled || !n) return;
 switch (t) {
 case "input":
 if (!/reset|button|image|file/i.test(el.type)) {
 ch = /checkbox/i.test(el.type);
 ra = /radio/i.test(el.type);
 val = el.value;
 (!(ch || ra) || el.checked) && cb(n, normalize(ch && val === "" ? "on" : val));
 }
 break;

 case "textarea":
 cb(n, normalize(el.value));
 break;

 case "select":
 if (el.type.toLowerCase() === "select-one") {
 optCb(el.selectedIndex >= 0 ? el.options[el.selectedIndex] : null);
 } else {
 for (i = 0; el.length && i < el.length; i++) {
 el.options[i].selected && optCb(el.options[i]);
 }
 }
 break;
 }
 }
 function eachFormElement() {
 var cb = this, e, i, serializeSubtags = function(e, tags) {
 var i, j, fa;
 for (i = 0; i < tags.length; i++) {
 fa = e[byTag](tags[i]);
 for (j = 0; j < fa.length; j++) serial(fa[j], cb);
 }
 };
 for (i = 0; i < arguments.length; i++) {
 e = arguments[i];
 if (/input|select|textarea/i.test(e.tagName)) serial(e, cb);
 serializeSubtags(e, [ "input", "select", "textarea" ]);
 }
 }
 function serializeQueryString() {
 return vnpReq.toQueryString(vnpReq.serializeArray.apply(null, arguments));
 }
 function serializeHash() {
 var hash = {};
 eachFormElement.apply(function(name, value) {
 if (name in hash) {
 hash[name] && !isArray(hash[name]) && (hash[name] = [ hash[name] ]);
 hash[name].push(value);
 } else hash[name] = value;
 }, arguments);
 return hash;
 }
 vnpReq.serializeArray = function() {
 var arr = [];
 eachFormElement.apply(function(name, value) {
 arr.push({
 name: name,
 value: value
 });
 }, arguments);
 return arr;
 };
 vnpReq.serialize = function() {
 if (arguments.length === 0) return "";
 var opt, fn, args = Array.prototype.slice.call(arguments, 0);
 opt = args.pop();
 opt && opt.nodeType && args.push(opt) && (opt = null);
 opt && (opt = opt.type);
 if (opt == "map") fn = serializeHash; else if (opt == "array") fn = vnpReq.serializeArray; else fn = serializeQueryString;
 return fn.apply(null, args);
 };
 vnpReq.toQueryString = function(o, trad) {
 var prefix, i, traditional = trad || false, s = [], enc = encodeURIComponent, add = function(key, value) {
 value = "function" === typeof value ? value() : value == null ? "" : value;
 s[s.length] = enc(key) + "=" + enc(value);
 };
 if (isArray(o)) {
 for (i = 0; o && i < o.length; i++) add(o[i]["name"], o[i]["value"]);
 } else {
 for (prefix in o) {
 if (o.hasOwnProperty(prefix)) buildParams(prefix, o[prefix], traditional, add);
 }
 }
 return s.join("&").replace(/%20/g, "+");
 };
 vnpReq.post = function(url, data, fn, type) {
 var o = {
 method: "post",
 url: url,
 data: data,
 success: fn,
 type: type
 };
 return vnpReq(o);
 };
 function buildParams(prefix, obj, traditional, add) {
 var name, i, v, rbracket = /\[\]$/;
 if (isArray(obj)) {
 for (i = 0; obj && i < obj.length; i++) {
 v = obj[i];
 if (traditional || rbracket.test(prefix)) {
 add(prefix, v);
 } else {
 buildParams(prefix + "[" + (typeof v === "object" ? i : "") + "]", v, traditional, add);
 }
 }
 } else if (obj && obj.toString() === "[object Object]") {
 for (name in obj) {
 buildParams(prefix + "[" + name + "]", obj[name], traditional, add);
 }
 } else {
 add(prefix, obj);
 }
 }
 vnpReq.getcallbackPrefix = function() {
 return callbackPrefix;
 };
 vnpReq.compat = function(o, fn) {
 if (o) {
 o["type"] && (o["method"] = o["type"]) && delete o["type"];
 o["dataType"] && (o["type"] = o["dataType"]);
 o["jsonpCallback"] && (o["jsonpCallbackName"] = o["jsonpCallback"]) && delete o["jsonpCallback"];
 o["jsonp"] && (o["jsonpCallback"] = o["jsonp"]);
 }
 return new vnpReqCore(o, fn);
 };
 vnpReq.ajaxSetup = function(options) {
 options = options || {};
 for (var k in options) {
 globalSetupOptions[k] = options[k];
 }
 };
 return vnpReq;
 });
 },
 vnpJsSrc: function(module, exports, require, global) {
 !function($) {
 var r = require("vnpReq"), integrate = function(method) {
 return function() {
 var args = Array.prototype.slice.call(arguments, 0), i = this && this.length || 0;
 while (i--) args.unshift(this[i]);
 return r[method].apply(null, args);
 };
 }, s = integrate("serialize"), sa = integrate("serializeArray");
 $.vnpJs({
 ajax: r,
 post: r.post,
 serialize: r.serialize,
 serializeArray: r.serializeArray,
 toQueryString: r.toQueryString,
 ajaxSetup: r.ajaxSetup
 });
 $.vnpJs({
 serialize: s,
 serializeArray: sa
 }, true);
 }(vnpJs);
 }
 }, "vnpReq");
 require("vnpReq");
 require("vnpReq/vnpJsSrc");
 /***
 * vnpJs - vnpPlug2
 ***/
 Module.createPackage("vnpPlug2", {
 vnpPlug2: function(module, exports, require, global) {
 (function(name, context, definition) {
 if (typeof module != "undefined" && module.exports) module.exports = definition(); else if (typeof define == "function" && define.amd) define(definition); else context[name] = definition();
 })("vnpPlug2", this, function() {
 var context = this, old = context.vnpPlug2, doc = window.document, html = doc.documentElement, toString = Object.prototype.toString, Ap = Array.prototype, slice = Ap.slice, matchesSelector = function(el, pfx, name, i, ms) {
 while (i < pfx.length) if (el[ms = pfx[i++] + name]) return ms;
 }(html, [ "msM", "webkitM", "mozM", "oM", "m" ], "atchesSelector", 0), Kfalse = function() {
 return false;
 }, isNumber = function(o) {
 return toString.call(o) === "[object Number]";
 }, isString = function(o) {
 return toString.call(o) === "[object String]";
 }, isFunction = function(o) {
 return toString.call(o) === "[object Function]";
 }, isUndefined = function(o) {
 return o === void 0;
 }, isElement = function(o) {
 return o && o.nodeType === 1;
 }, getIndex = function(selector, index) {
 return isUndefined(selector) && !isNumber(index) ? 0 : isNumber(selector) ? selector : isNumber(index) ? index : null;
 }, getSelector = function(selector) {
 return isString(selector) ? selector : "*";
 }, nativeSelectorFind = function(selector, el) {
 return slice.call(el.querySelectorAll(selector), 0);
 }, nativeSelectorMatches = function(selector, el) {
 return selector === "*" || el[matchesSelector](selector);
 }, selectorFind = nativeSelectorFind, selectorMatches = nativeSelectorMatches, createUnorderedEngineSelectorFind = function(engineSelect, selectorMatches) {
 return function(selector, el) {
 if (/,/.test(selector)) {
 var ret = [], i = -1, els = el.getElementsByTagName("*");
 while (++i < els.length) if (isElement(els[i]) && selectorMatches(selector, els[i])) ret.push(els[i]);
 return ret;
 }
 return engineSelect(selector, el);
 };
 }, isAncestor = "compareDocumentPosition" in html ? function(element, container) {
 return (container.compareDocumentPosition(element) & 16) == 16;
 } : "contains" in html ? function(element, container) {
 container = container.nodeType === 9 || container == window ? html : container;
 return container !== element && container.contains(element);
 } : function(element, container) {
 while (element = element.parentNode) if (element === container) return 1;
 return 0;
 }, unique = function(ar) {
 var a = [], i = -1, j, has;
 while (++i < ar.length) {
 j = -1;
 has = false;
 while (++j < a.length) {
 if (a[j] === ar[i]) {
 has = true;
 break;
 }
 }
 if (!has) a.push(ar[i]);
 }
 return a;
 }, collect = function(els, fn) {
 var ret = [], res, i = 0, j, l = els.length, l2;
 while (i < l) {
 j = 0;
 l2 = (res = fn(els[i], i++)).length;
 while (j < l2) ret.push(res[j++]);
 }
 return ret;
 }, move = function(els, method, selector, index, filterFn) {
 index = getIndex(selector, index);
 selector = getSelector(selector);
 return collect(els, function(el, elind) {
 var i = index || 0, ret = [];
 if (!filterFn) el = el[method];
 while (el && (index === null || i >= 0)) {
 if (isElement(el) && (!filterFn || filterFn === true || filterFn(el, elind)) && selectorMatches(selector, el) && (index === null || i-- === 0)) {
 index === null && method != "nextSibling" && method != "parentNode" ? ret.unshift(el) : ret.push(el);
 }
 el = el[method];
 }
 return ret;
 });
 }, eqIndex = function(length, index, def) {
 if (index < 0) index = length + index;
 if (index < 0 || index >= length) return null;
 return !index && index !== 0 ? def : index;
 }, filter = function(els, fn) {
 var arr = [], i = 0, l = els.length;
 for (;i < l; i++) if (fn(els[i], i)) arr.push(els[i]);
 return arr;
 }, filterFn = function(slfn) {
 var to;
 return isElement(slfn) ? function(el) {
 return el === slfn;
 } : (to = typeof slfn) == "function" ? function(el, i) {
 return slfn.call(el, i);
 } : to == "string" && slfn.length ? function(el) {
 return selectorMatches(slfn, el);
 } : Kfalse;
 }, inv = function(fn) {
 return function() {
 return !fn.apply(this, arguments);
 };
 }, vnpPlug2 = function() {
 function T(els) {
 this.length = 0;
 if (els) {
 els = unique(!els.nodeType && !isUndefined(els.length) ? els : [ els ]);
 var i = this.length = els.length;
 while (i--) this[i] = els[i];
 }
 }
 T.prototype = {
 down: function(selector, index) {
 index = getIndex(selector, index);
 selector = getSelector(selector);
 return vnpPlug2(collect(this, function(el) {
 var f = selectorFind(selector, el);
 return index === null ? f : [ f[index] ] || [];
 }));
 },
 up: function(selector, index) {
 return vnpPlug2(move(this, "parentNode", selector, index));
 },
 parents: function() {
 return T.prototype.up.apply(this, arguments.length ? arguments : [ "*" ]);
 },
 closest: function(selector, index) {
 if (isNumber(selector)) {
 index = selector;
 selector = "*";
 } else if (!isString(selector)) {
 return vnpPlug2([]);
 } else if (!isNumber(index)) {
 index = 0;
 }
 return vnpPlug2(move(this, "parentNode", selector, index, true));
 },
 previous: function(selector, index) {
 return vnpPlug2(move(this, "previousSibling", selector, index));
 },
 next: function(selector, index) {
 return vnpPlug2(move(this, "nextSibling", selector, index));
 },
 siblings: function(selector, index) {
 var self = this, arr = slice.call(this, 0), i = 0, l = arr.length;
 for (;i < l; i++) {
 arr[i] = arr[i].parentNode.firstChild;
 while (!isElement(arr[i])) arr[i] = arr[i].nextSibling;
 }
 if (isUndefined(selector)) selector = "*";
 return vnpPlug2(move(arr, "nextSibling", selector || "*", index, function(el, i) {
 return el !== self[i];
 }));
 },
 children: function(selector, index) {
 return vnpPlug2(move(T.prototype.down.call(this), "nextSibling", selector || "*", index, true));
 },
 first: function() {
 return T.prototype.eq.call(this, 0);
 },
 last: function() {
 return T.prototype.eq.call(this, -1);
 },
 eq: function(index) {
 return vnpPlug2(this.get(index));
 },
 get: function(index) {
 return this[eqIndex(this.length, index, 0)];
 },
 slice: function(start, end) {
 var e = end, l = this.length, arr = [];
 start = eqIndex(l, Math.max(-this.length, start), 0);
 e = eqIndex(end < 0 ? l : l + 1, end, l);
 end = e === null || e > l ? end < 0 ? 0 : l : e;
 while (start !== null && start < end) arr.push(this[start++]);
 return vnpPlug2(arr);
 },
 filter: function(slfn) {
 return vnpPlug2(filter(this, filterFn(slfn)));
 },
 not: function(slfn) {
 return vnpPlug2(filter(this, inv(filterFn(slfn))));
 },
 has: function(slel) {
 return vnpPlug2(filter(this, isElement(slel) ? function(el) {
 return isAncestor(slel, el);
 } : typeof slel == "string" && slel.length ? function(el) {
 return selectorFind(slel, el).length;
 } : Kfalse));
 },
 is: function(slfn) {
 var i = 0, l = this.length, fn = filterFn(slfn);
 for (;i < l; i++) if (fn(this[i], i)) return true;
 return false;
 },
 toArray: function() {
 return Ap.slice.call(this);
 },
 size: function() {
 return this.length;
 },
 each: function(fn, ctx) {
 var i = 0, l = this.length;
 for (;i < l; i++) fn.call(ctx || this[i], this[i], i, this);
 return this;
 },
 push: Ap.push,
 sort: Ap.sort,
 splice: Ap.splice
 };
 T.prototype.prev = T.prototype.previous;
 function t(els) {
 return new T(isString(els) ? selectorFind(els, doc) : els);
 }
 t.aug = function(methods) {
 var key, method;
 for (key in methods) {
 method = methods[key];
 if (typeof method == "function") T.prototype[key] = method;
 }
 };
 t.setSelectorEngine = function(s) {
 var ss, r, a, _selectorMatches, _selectorFind, e = doc.createElement("p"), select = s.select || s.sel || s;
 e.innerHTML = "<a/><i/><b/>";
 a = e.firstChild;
 try {
 _selectorMatches = isFunction(s.matching) ? function(selector, el) {
 return s.matching([ el ], selector).length > 0;
 } : isFunction(s.is) ? function(selector, el) {
 return s.is(el, selector);
 } : isFunction(s.matchesSelector) ? function(selector, el) {
 return s.matchesSelector(el, selector);
 } : isFunction(s.match) ? function(selector, el) {
 return s.match(el, selector);
 } : isFunction(s.matches) ? function(selector, el) {
 return s.matches(el, selector);
 } : null;
 if (!_selectorMatches) {
 ss = s("a", e);
 _selectorMatches = isFunction(ss._is) ? function(selector, el) {
 return s(el)._is(selector);
 } : isFunction(ss.matching) ? function(selector, el) {
 return s(el).matching(selector).length > 0;
 } : isFunction(ss.is) && !ss.is.__ignore ? function(selector, el) {
 return s(el).is(selector);
 } : isFunction(ss.matchesSelector) ? function(selector, el) {
 return s(el).matchesSelector(selector);
 } : isFunction(ss.match) ? function(selector, el) {
 return s(el).match(selector);
 } : isFunction(ss.matches) ? function(selector, el) {
 return s(el).matches(selector);
 } : null;
 }
 if (!_selectorMatches) throw new Error("vnpPlug2: couldn't find selector engine's `matchesSelector`");
 if (_selectorMatches("x,y", e) || !_selectorMatches("a,p", e)) throw new Error("vnpPlug2: couldn't make selector engine's `matchesSelector` work");
 if ((r = select("b,a", e)).length !== 2) throw new Error("vnpPlug2: don't know how to use this selector engine");
 _selectorFind = r[0] === a ? select : createUnorderedEngineSelectorFind(select, _selectorMatches);
 if ((r = _selectorFind("b,a", e)).length !== 2 || r[0] !== a) throw new Error("vnpPlug2: couldn't make selector engine work");
 selectorMatches = _selectorMatches;
 selectorFind = _selectorFind;
 } catch (ex) {
 throw isString(ex) ? ex : new Error("vnpPlug2: error while figuring out how the selector engine works: " + (ex.message || ex));
 } finally {
 e = null;
 }
 return t;
 };
 t.noConflict = function() {
 context.vnpPlug2 = old;
 return this;
 };
 return t;
 }();
 return vnpPlug2;
 });
 },
 vnpJsSrc: function(module, exports, require, global) {
 (function($) {
 var t = require("vnpPlug2"), integrated = false, integrate = function(meth) {
 var fn = function(self, selector, index) {
 if (!integrated) {
 try {
 t.setSelectorEngine($);
 } catch (ex) {}
 integrated = true;
 }
 fn = meth == "is" ? function(self, slfn) {
 return t(self)[meth](slfn);
 } : function(self, selector, index) {
 return $(t(self)[meth](selector, index));
 };
 return fn(self, selector, index);
 };
 return function(selector, index) {
 return fn(this, selector, index);
 };
 }, methods = "up down next previous prev parents closest siblings children first last eq slice filter not is has".split(" "), b = {}, i = methods.length;
 if ($.fn.is) $.fn._is = $.fn.is;
 while (--i >= 0) b[methods[i]] = integrate(methods[i]);
 $.vnpJs(b, true);
 $.fn.is.__ignore = true;
 })(vnpJs);
 }
 }, "vnpPlug2");
 require("vnpPlug2");
 require("vnpPlug2/vnpJsSrc");
}).call(window);

/* Selector with noConflict */
$vnpJs = vnpJs.noConflict();var vgc_isTabActive = 0;
var isShowNotifi = 0;
/* cookie lưu expires time theo phút */

var remove_element_vchat = ''; // setTimeout remove element vchat

// is typing
var is_typing; // biến timeout để gửi đi
var is_send_typing = true;
var sto_typing; // biến timeout để set lại trạng thái có cho gửi đi hay không

/* banned => set auto_reply = 0
chủ website chat đến => set auto_reply = 0*/
var vc_auto_reply = 1;

/* câu trả lời hiện tại đang là câu thứ mấy first hay second, dữ liệu này sẽ lấy từ cookie để check cookie: autoreply_step */
var vc_auto_reply_step = 1;

/* Nhắc nhở nhân viên chat ngay */
var vc_time_wait_chat = 5; /* thời gian chờ sau 15s mà không trả lời thì người dùng có quyền yêu cầu nhân viên trả lời */
var vc_is_me_send = 0; /* khi mình chat đi thì set biến vc_is_me_send = 1, nếu sau 15s mà chưa trả lời thì bật nút yêu cầu lên, nếu nhân viên trả lời thì lại cho vc_is_me_send = 0*/

/* reply when busy */
var auto_rep_buzy = 0;
var auto_rep_buzy_time = 60;
var set_auto_rep_buzy = "";

/* reply when click cancel */
var rep_when_cancel = 0;
var rep_when_cancel_text = "";

/* biễn xác định có đang focus vào ô chat hay không */
var _is_text_focus = false;

/* function hiện nut yêu cầu chat */
var run_require_chat = 0;
var setTime_require_chat = 0;
function show_button_require_chat(){
 setTime_require_chat = setTimeout(function(){
 $vnpJs('#vgc_require_chat').removeClass('vgc_hide');
 }, (vc_time_wait_chat * 1000));
}
/* hàm ẩn nút hiện chat */
function hide_button_require_chat(){
 $vnpJs('#vgc_require_chat').addClass('vgc_hide');
 vc_is_me_send = 0;
}
/* function send request require chat */
function send_request_require_chat(data){
 
 var _text_msg = '';
 var _bot_id = 0;
 var _bot_id_opt = 0;
 
 if(typeof data == "object"){
 _text_msg = data.quest || '';
 _bot_id = data.bot_id || 0;
 _bot_id_opt = (data.bot_id_opt + 1) || 0;
 $vnpJs('#vgc_chatbot_cbi').val(_bot_id);
 $vnpJs('#vgc_chatbot_cbi_opt').val(_bot_id_opt);
 }else{
 vc_is_me_send = 1;
 }
 
 if(_text_msg == ''){
 if(isset(typeof _vcclient_config)){
 if(isset(typeof _vcclient_config.orther)){
 if(isset(typeof _vcclient_config.orther.require_chat)){
 if(_vcclient_config.orther.require_chat.text !== ''){
 _text_msg = _vcclient_config.orther.require_chat.text;
 }
 } 
 }
 }
 } 
 
 if(_text_msg == '') _text_msg = "Xin chào! Tôi đã chờ một thời gian, vui lòng hỗ trợ tôi";
 
 $vnpJs('#vgc_message').val(_text_msg);
 vgchatClientSend('', 'submit');
 /*khi đã gửi yêu cầu thì cho thông tin về rỗng*/ 
 hide_button_require_chat();
 
}

console.log('%c Vchat.vn phần mềm livechat chat hỗ trợ khách hàng trực tuyến phổ biến nhất hiện nay', 'font-size:25px; background-color: #0165bb; color: #fff;font-family: tahoma;padding:5px 10px;');


/* các biến liên quan đến việc di chuyển box chat */
var is_moveboxchat = 0;
var position_downx = 0; /* vị trí x click chuột xuống */
var posotion_downy = 0; /* vị trí y click chuột xuống */
var box_old_x = 0; /* vị trí x box chat cũ */
var box_old_y = 0; /* vị trí y boxchat cũ */
var box_new_x = 0; /* vị trí x di chuột */
var box_new_y = 0; /* vị trí y di chuột */
var space_x = 0; /* khoảng cách x chênh giữa vị trí cũ và mới */
var space_y = 0; /* khoảng cách y chênh giữa vị trí cũ và mới */
var vgc_box_w = 0; /* width boxchat */
var vgc_box_h = 0; /* height boxchat */
var newx = -1;
var newy = -1;
var elm_template_chat = "";

/*document.onmousemove = vgc_vc_mousemove;*/
/*document.onmouseup = vgc_vc_mouseup;*/
/**
13/3/2016 Tạm bỏ tính năng click vào tiêu đề và kéo boxchat di chuyển sang vị trí khac
var findelm = setInterval(function(){
 if(document.getElementById('vgc_bcl_move')){
 var elmmove = document.getElementById('vgc_bcl_move') || '';
 elmmove.onmousedown = vgc_vc_mousedown;
 elmmove.onmouseup = vgc_vc_mouseup;
 //elmmove.onmouseout = vgc_vc_mouseup;
 clearInterval(findelm);
 }
}, 1000);
*/


/* Neu nguoi dung click vao tab trinh duyet */
window.onfocus = function () {
 vgc_isTabActive = 1;
}

/* Neu nguoi dung roi khoi tab trinh duyet */
window.onblur = function () {
 vgc_isTabActive = 0;
}

/* Title của website */
var vgc_title = document.title;

/* Tổng số tin nhắn mới đến nếu có */
var vgc_new_msg = 0;

/* Tiêu đề mới cho lần thứ 2 khi có ng chat đến */
var vgc_new_title = '';

/**
 * function fn_raw_chat - Xu ly khi co su kien chat
 */
var vgc_count_msg = 0;
var vgc_check_show_box_chat_on_raw = false;

function fn_raw_chat(data){
 
 if(isset(typeof vgc_read_log)){
 if(vgc_read_log == 1) console.log(data);
 }
 /* login social */
 if(data.action == 'social_login'){
 var name = data.send_name || '';
 $vnpJs('#vgc_name').val(name);
 vgc_close_guest_info();
 return false;
 }
 
 /* nếu là chủ web click vào nút cancel khi khách hàng yêu cầu chat thì xem hiển thị trả lời của chử website */
 if(isset(typeof data.cancel_reply)){
 if(data.cancel_reply == 1){
 var data_msg = {
 owner : 'vgc_rowfriend',
 msg : rep_when_cancel_text,
 id : $vnpJs('#vgc_to_id').val(),
 };
 if(data_msg.msg != ''){
 vgchatClientAppendMsgToBoxchat(data_msg);
 clearTimeout(set_auto_rep_buzy);
 return false;
 }
 }
 }
 
 clearTimeout(set_auto_rep_buzy);
 clearTimeout(setTime_require_chat);
 
 /* ẩn nút yêu cầu chat */
 hide_button_require_chat()
 
 /* check nếu bị ban thì không cho chat luôn */
 var banned = parseInt(data.banned) || 0;
 if(banned > 0){
 $vnpJs('#vgc_message').attr({'readonly':'true', 'disabled':'disabled'});
 /* set auto_reply = 0 */
 vc_auto_reply = 0;
 return false;
 }

 /* chủ website chủ động tắt boxchat với khách hàng */
 if(isset(typeof data.close_chat) && data.close_chat == 1){
 $vnpJs('#vgc_message').attr('disabled','disabled');
 var html_close = '<div style="font-size:12px;color:#999;padding:5px;text-align:right;line-height:25px;">Chủ website đã dừng cuộc chat<p><span style="text-decoration: underline;cursor:pointer" onclick="vgc_rechat();">Tạo cuộc chat mới</span></p></div>';
 $vnpJs('#panel_history_vgchat').append(html_close);
 vgchatClientscrollBot();
 return false;
 }

 /* kiểm tra nếu là tài khoản miễn phí và đc nhận chat đến thì xóa thông tin chờ đợi đi */
 if($vnpJs('#vgc_quere_chat').length) $vnpJs('#vgc_quere_chat').remove();

 /* kiểm tra nếu có dòng chat rồi thì thôi không append vào nưa */
 var vgc_time = parseInt(data.vgc_time) || 0;

 /* kiểm tra nếu vgc_time > 0 và là tin nhắn của mình thì bỏ trạng thái mờ đi */
 if(vgc_time > 0 && $vnpJs('#vgc_me_send_'+vgc_time).length){
 /* bỏ opacity của tin nhắn đi */
 $vnpJs('#vgc_me_send_'+vgc_time).removeClass('vgc_temmsg');
 return false;
 }

 var vgc_to_id = $vnpJs('#vgc_to_id');
 var vgc_to_id_val = parseInt(vgc_to_id.val()) || 0;
 var vgc_send_id = parseInt($vnpJs('#vgc_send_id').val()) || 0;
 /* kiểm tra nếu đúng tab chat, đúng website và đúng 1 tài khoản nhúng thì hiển thị không thì thôi */
 if(data.send_id != vgc_send_id && data.send_id != vgc_to_id_val) return false;

 /* nếu tin nhắn từ mình nhưng khác to_id thì cũng thoát luôn */
 if(data.send_id == vgc_send_id && data.id != vgc_to_id_val) return false;

 if(isset(typeof execute_notification)){
 if(vgc_isTabActive == 0) {
 vgc_new_msg += 1;
 vgc_new_title = '('+vgc_new_msg+') ' + vgc_title;
 document.title = vgc_new_title;
 /*execute_notification(data);*/
 }
 }

 var support_id = data.support_id || 0;
 var tranfer = data.tranfer || 0;
 var vgc_support_id = parseInt($vnpJs('#vgc_support_id').val()) || 0;

 /* Check nếu là tranfer thì thay luôn support_id và select_office */
 if(tranfer == 1){
 $vnpJs('#vgc_support_id').val(support_id);
 $vnpJs('#vgc_select_office').val(support_id);
 return false;
 }

 /* có 2 TH

 1: có list office (vgc_select_office != 0 và != '' và != -1)
 - nếu vgc_support_id > 0 và vgc_support_id in array vgc_select_office thì thôi
 - nếu vgc_support_id > 0 và vgc_support_id not in array vgc_select_office
 if(support_id > 0){
 + đè lại support_id thay cho vgc_support_id
 + đè lại vgc_select_office = 0
 }

 2: không có list office (vgc_select_office = - hoặc < 0)
 - check nếu support > 0 và giá trị hiện tại <= 0 thì đè lại biến support

 */
 var vgc_select_office = $vnpJs('#vgc_select_office').val() || 0;
 if(support_id > 0 && vgc_support_id <= 0){
 $vnpJs('#vgc_support_id').val(support_id);
 }
 
 /* TH1: */
 if(vgc_select_office != 0){
 var arrayOffice = vgc_select_office.split(',');
 if(vgc_support_id > 0 && arrayOffice.indexOf(vgc_support_id) == -1){
 if(support_id > 0){
 $vnpJs('#vgc_support_id').val(support_id);
 $vnpJs('#vgc_select_office').val(support_id);

 /* đánh dấu biến set lại vgc_set_again_support = 1 để set lại support id trong bảng history */
 $vnpJs('#vgc_set_again_support').val(1);
 }
 }
 }

 var vgc_count_chat = $vnpJs('#vatgiaClient_count_chat');
 var vatgiaClient_count = 0;
 if(vgc_count_chat.length > 0){
 vatgiaClient_count = parseInt(vgc_count_chat.val()) + 1;
 vgc_count_chat.val(vatgiaClient_count);
 }
 /* update tên của nhân viên hỗ trợ và email */
 var support_name = data.support_name || '';
 var support_image = data.support_image || '';
 
 var sup_help = ' - Hỗ trợ tư vấn';
 if(isset(_vcclient_config)){
 if(isset(_vcclient_config.chs_lang_id)){
 if(_vcclient_config.chs_lang_id.lang == 'en'){
 sup_help = ' - Support';
 }
 }
 } 
 
 
 if(support_name != ''){
 var vgc_append_name = $vnpJs('#vgc_append_name').val() || 0;
 if(vgc_append_name == 0){
 setTimeout(function(){
 $vnpJs('#vgc_sp_name').text(support_name + sup_help);
 }, 300);
 $vnpJs('#vgc_append_name').val(1);
 if(support_image != ''){
 $vnpJs('.vgc_info_support_avatar img').attr('src' , support_image);
 }
 }
 }

 //if(parseInt(data.send_id) == vgc_to_id_val){

 var panel_body_vgchat = $vnpJs('#panel_body_vgchat');

 /* Neu box chat dang an va box chua load lan nao thi thuc hien */
 if(panel_body_vgchat.hasClass('vgc_hide') && vgc_check_show_box_chat_on_raw == false){
 /* show box chat và không bắt nhập thông tin trước khi chat */
 var data_show_box = {div_id : "panel_body_vgchat", div_toggle : "show", require_info : 0};
 vgchatClientToggleDiv(data_show_box);
 }

 /* chi cho phep bat box chat 1 lan */
 vgc_check_show_box_chat_on_raw = true;

 /* Thong bao tin nhan chua doc */
 if(panel_body_vgchat.hasClass('vgc_hide') && vgc_check_show_box_chat_on_raw == true){
 vgc_count_msg++;

 /* Remove khung count */
 var vgc_count_message = $vnpJs('.vgc_count_message');
 if(vgc_count_message.length > 0) vgc_count_message.remove();

 /* Tao khung count moi */
 $vnpJs('.panel_head_vgchat').append('<span class="vgc_count_message">' + vgc_count_msg + '</span>');

 }

 /* append content chat */
 data.owner = 'vgc_rowme';
 if(parseInt(data.send_id) == vgc_to_id_val){
 data.owner = 'vgc_rowfriend';
 /* set auto_reply = 0 */
 vc_auto_reply = 0;
 }

 if(vgc_check_get_history == true) vgchatClientAppendMsgToBoxchat(data);

 //} /* End if(data.send_id == vgc_to_id_val) */

} /* End function fn_raw_chat */

/* event logout */
function fn_raw_logout(data){
 var _logout = data.log || 0;
 if(_logout == 1){
 if(confirm("Tài khoản của bạn đã thoát ở một trang web khác. Bạn có muốn Load lại trang không?")){
 window.location.reload();
 }else{
 return false;
 }
 }
}

/**
 * function vgchatClientGetHistoryChat - Get History Data Chat
 obj = {
 require_info : 0 or 1 (0 không bắt nhập thông tin cá nhân, 1 có bắt buộc nhập thông tin cá nhân)
 }
 */
var vgc_check_get_history = false;
function vgchatClientGetHistoryChat(obj){
 var require_info = obj.require_info;
 if(require_info != 0) require_info = 1;
 data_send = vgc_client_box_data +'&require_info='+require_info;

 /* Nếu không load được box và history thì thoát khỏi hàm */
 if(vgc_check_get_history == true || $vnpJs('#panel_content_vgchat').length < 1) return;

 /* Tao icon loading */
 $vnpJs('#panel_content_vgchat').html('<div id="vgc_loading" align="center"><span class="vgc_ic_loading"></span></div>');

 var ga = document.createElement("script");
 ga.type = "text/javascript";
 ga.id = "vchat_load_box_client";
 ga.src = url_server_vgchat_client+"h_client_box.php?"+data_send;
 var s = document.getElementsByTagName("script");
 s[0].parentNode.insertBefore(ga, s[0]);

} /* End function vgchatClientGetHistoryChat */

/**
 * function vgchatClientscrollBot - Keo thanh chuot xuong cuoi khung chat
 */
function vgchatClientscrollBot(selector){

 var selector = selector || '#panel_history_vgchat'
 if($vnpJs(selector).length > 0){
 $vnpJs(selector).scrollTop($vnpJs(selector)[0].scrollHeight);
 }

} /* End function vgchatClientscrollBot */

/**
 * function vgchatClientAppendMsgToBoxchat - Append message to content chat
 */
function vgchatClientAppendMsgToBoxchat(data){
 var owner = data.owner || 'vgc_rowme';
 var msg = data.msg || '';
 msg = msg.replace(/&lt;br&gt;/g, ' <br /> ');
 msg = msg.replace(/\%2B/g, '+');
 var guest_name = $vnpJs('#vgc_import_name').val() || 'Bạn';
 msg = msg.replace('{{tenkhach}}', guest_name);
 var showboxchat = data.showboxchat || 0; /* biến để kiểm tra là khách dùng chức năng showboxchat */

 var ask_location = data.ask_location || 0;
 if(ask_location){
 msg = 'Chủ website gửi một yêu cầu xin vị trí hiện tại của bạn <br><br><button onclick="_vcclient.send_location()">Chia sẻ vị trí hiện tại</button>';
 }
 var boxchat_id = data.id || 0;
 /*
 var vgc_rand = data.vgc_rand || 0;
 var vgc_rand_current = parseInt($vnpJs('#vgc_random').val()) || 0;
 */
 var first_msg = data.first || 0;
 var temmsg = data.temmsg || 0; /* phân biệt tin nhắn vừa gửi lên chờ socket trả về */
 var vgc_time = parseInt(data.vgc_time) || 0;
 var classTemmsg = '';
 if(temmsg == 1) classTemmsg = ' vgc_temmsg';

 //console.log(vgc_time);
 /* Name */
 var name = '';
 if(owner == 'vgc_rowfriend' && !$vnpJs('.template_vgchat .vgc_row').last().hasClass('vgc_rowfriend')){
 name = $vnpJs('.vgc_info_support_avatar').html();
 name = (name != null) ? name : 'Hỗ trợ';
 name = '<span class="vgc_name">' + name + '</span>';
 }

 /* nếu là chức năng showboxchat thì sẽ không lấy được thông tin ảnh hỗ trợ nên phải tạo biến khác để sau replace lại */
 if(showboxchat){
 name = '{logoimage}';
 }

 /* Kiểm tra dòng chat vs time đã gửi lên có chưa, có rồi thì return luôn vì chính là tap mình đang chat */
 if(vgc_time > 0 && $vnpJs('#vgc_me_send_'+vgc_time).length) return false;

 /* Message */
 var html = '';
 html += '<div class="' + owner + ' vgc_row">';
 html += name;
 html += '<span id="vgc_me_send_'+vgc_time+'" class="vgc_msgchat '+ classTemmsg +'">';
 html += '<i class="vgc_ic_chat"></i>'
 /* nếu trong đoạn chat có picture/service thì hiển thị ảnh xoay xoay ra trước */
 if(msg.indexOf('vchat.vn/pictures/service/') > 0 && first_msg == 1){
 msg = '<span class="send_file_loadding"></span>';
 }
 html += '<span class="vgc_msgsend">' + msg + '</span>'

 html += '</span>'
 html += '</div>';

 /* Append noi dung chat vao khung chat */
 if(showboxchat){
 var st = setInterval(function(){
 if($vnpJs('#panel_history_vgchat').length){
 /* xóa dòng chữ của invite_time vì đã có dòng chữ mới của showboxchat */
 $vnpJs('.first_message').remove();

 /* lấy lại ảnh của hỗ trợ admin vì phải load xong mới lấy đc */
 name = $vnpJs('.vgc_info_support_avatar').html();
 name = (name != null) ? name : 'Hỗ trợ';
 name = '<span class="vgc_name">' + name + '</span>';

 /* replace ngược lại thông tin ảnh vào nội dung tin nhắn */
 html = html.replace('{logoimage}', name);

 /* chèn câu chào vào phần tin nhắn */
 $vnpJs('#panel_history_vgchat').append(html);
 clearInterval(st);
 }
 }, 100);
 }else{
 $vnpJs('#panel_history_vgchat').append(html);
 }

 /*
 if(vgc_rand != vgc_rand_current || vgc_rand == 0 ){
 $vnpJs('#vgc_random').val(vgc_rand);
 $vnpJs('#panel_history_vgchat').append(html);
 }
 */
 
 // check âm thanh rồi play
 var playsound = document.getElementById('vgc_audio_sound');
 if(playsound != null){
 playsound.play();
 }

 /* keo thanh cuon xuong cuoi */
 vgchatClientscrollBot();

 if(owner == 'vgc_rowfriend'){
 /* Tao am thanh */
 if($vnpJs.browser.msie && $vnpJs.browser.version < 8) return false;
 if(parseInt(vgc_check_config_sound) == 1){
 if($vnpJs('#vgc_audio_message').length){
 $vnpJs('#vgc_audio_message')[0].play();
 }
 }
 return false;
 var noti_msg = vgc_getCookie('noti_msg');
 var window_blur_val = vgc_getCookie('window_blur');
 if(window_blur_val == 1 && noti_msg != msg){
 notifyMe(msg);
 vgc_setCookie({name : 'noti_msg', value : msg, expires : 30, type : 'm'});
 }
 }
} /* End function vgchatClientAppendMsgToBoxchat */

/**
 * function vgchatClientSend - Dieu khien noi dung chat gui di
 */
function vgchatClientSend(event, send){

 if(!event.shiftKey && (event.keyCode==13 || send == 'submit')){
 if(send == '') vgchatClientStopEvent(event);
 
 is_send_typing = true;
 
 var panel_content_vgchat = $vnpJs('#panel_content_vgchat');
 var vgc_message = $vnpJs('#vgc_message');

 vgc_message_val = vgc_message.val();
 vgc_message_val = vgc_message_val.replace(/\n/g, "<br>"); 
 vgc_message_val = vgc_message_val.replace(/\+/g, '%2B'); 
 vgc_message_val = vgc_message_val.trim();
 /* Neu co noi dung chat */
 if(vgc_message_val != ''){
 
 /* Object */
 var _date = new Date();
 var vgc_to_id = $vnpJs('#vgc_to_id');
 var vgc_name = $vnpJs('#vgc_name');
 var vgc_send_id = $vnpJs('#vgc_send_id');
 var vgc_hash = $vnpJs('#vgc_hash');
 var vgc_count_chat = $vnpJs('#vatgiaClient_count_chat');
 var vgc_support_id = $vnpJs('#vgc_support_id').val() || 0;
 
 var vgc_chatbot_cbi = $vnpJs('#vgc_chatbot_cbi').val();
 var vgc_chatbot_cbi_opt = $vnpJs('#vgc_chatbot_cbi_opt').val();
 
 var vgc_link = document.location.href;
 var vgc_rand = Math.floor((Math.random() * 1000000) + 1);
 var vgc_time = _date.getTime();
 var vgc_is_change_office = $vnpJs('#vgc_is_change_office').val() || 0; /*xác định có set support_id = 0 hay không*/
 var vgc_set_again_support = $vnpJs('#vgc_set_again_support').val() || 0; /*xác định có set lại support hay không*/


 /* lấy xong biến vgc_is_change_office thì set = 0 luôn */
 if(vgc_is_change_office > 0) $vnpJs('#vgc_is_change_office').val(0);

 /* khi đã biết phải set lại support thì bật lại về 0 để không gửi lên nữa */
 if(vgc_set_again_support > 0) $vnpJs('#vgc_set_again_support').val(0);

 /* lấy thêm biến vgc_select_office truyền đi đẻ biết đang chat vs nhóm hỗ trợ nào
 nếu biến này không có thì lấy từ cookie $vnpJs.cookie('vgc_select_office', ròi gắn đè vào biến element vgc_select_office
 */
 var vgc_select_office = 0;

 /* kiểm tra nếu show office = 1 thì mới lấy trong cookie hoặc lấy từ biến */
 var vgc_show_office = parseInt($vnpJs('#vgc_show_office').val()) || 0;

 if(vgc_show_office == 1){
 vgc_select_office = $vnpJs('#vgc_select_office').val() || 0;
 if(vgc_select_office == 0) vgc_select_office = vgc_getCookie('vgc_select_office');
 if(typeof vgc_select_office != 'undefined' && typeof vgc_select_office != undefined){
 if(vgc_select_office == '' ){
 $vnpJs('#vgc_select_office').val(0);
 }else{
 $vnpJs('#vgc_select_office').val(vgc_select_office);
 }
 }else{
 vgc_select_office = 0;
 }
 }

 /* Object data */
 var vgc_to_id_val,vgc_name_val,vgc_send_id_val,vgc_hash_val,vgc_count_chat_val;

 if(vgc_to_id.length > 0) vgc_to_id_val = vgc_to_id.val();
 if(vgc_name.length > 0) vgc_name_val = vgc_name.val();
 if(vgc_send_id.length > 0) vgc_send_id_val = vgc_send_id.val();
 if(vgc_hash.length > 0) vgc_hash_val = vgc_hash.val();
 if(vgc_count_chat.length > 0) vgc_count_chat_val = vgc_count_chat.val();
 if(vgc_message_val.indexOf('http') >= 0){
 vgc_message_val = encodeURIComponent(vgc_message_val);
 }
 data_send = 'send_id='+vgc_send_id_val+'&name='+vgc_name_val+'&to_id='+vgc_to_id_val+'&message='+vgc_message_val+'&hash='+vgc_hash_val+'&count_vgchat='+vgc_count_chat_val+'&link='+vgc_link+'&support_id='+vgc_support_id+'&vgc_rand='+vgc_rand+'&vgc_time='+vgc_time+'&vgc_select_office='+vgc_select_office+'&vgc_is_change_office='+vgc_is_change_office+'&vgc_set_again_support='+vgc_set_again_support+'&require_chat='+vc_is_me_send+'&vgc_chatbot_cbi='+vgc_chatbot_cbi+'&vgc_chatbot_cbi_opt='+vgc_chatbot_cbi_opt;

 /* Object data json */
 var data = {};
 data.msg = vgc_message_val;
 data.id = vgc_to_id_val;
 data.name = vgc_name_val;
 data.send_id = vgc_send_id_val;
 data.hash = vgc_hash_val;
 data.owner = 'vgc_rowme';
 data.first = 1;
 data.vgc_rand = vgc_rand;
 data.temmsg = 1;
 data.vgc_time = vgc_time;

 /* Append noi dung chat */
 vgchatClientAppendMsgToBoxchat(data);

 /* Lam rong message */
 vgc_message.val('');

 /* Tap trung vao khung input message */
 vgc_message.focus();
 
 /* vchat event send */
 vChatEvent.sendChat();

 $vnpJs.ajax({
 url: url_server_vgchat_client + 'send.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: data_send,
 error: function(err) {},
 success: function(res) {

 /* message trả về là tin nhắn gửi ảnh */
 if(res.msg){
 /* Xoa dong chat cuoi cung */
 $vnpJs('#panel_history_vgchat .vgc_row').last().remove();
 /* append data da bbcode */
 setTimeout(function(){ vgchatClientAppendMsgToBoxchat(res); }, 30);

 /* Xử lý 2 lần để có thể kéo thanh cuộn xuống cuối nếu load ảnh chậm */
 setTimeout(function(){ vgchatClientscrollBot(); }, 100);
 setTimeout(function(){ vgchatClientscrollBot(); }, 300);
 }
 vgc_message.focus();

 $vnpJs('#vgc_send_btn').addClass('vgc_hide')
 
 /* check botchat */
 if(typeof res['bot'] != 'undefined'){
 
 console.log(res['bot']);
 
 data.msg = res['bot'].html;
 data.first = 0;
 data.temmsg = 0;
 data.owner = 'vgc_rowfriend';
 data.vgc_time = data.vgc_time + 1;
 console.log(data);
 vgchatClientAppendMsgToBoxchat(data);
 
 }else{
 
 /* thực hiện auto reply */
 _vcclient.set_answer_auto_reply();
 
 /* thực hiện require chat */
 if(isset(typeof event)){
 if(event.keyCode == 13){
 if(run_require_chat == 1){
 clearTimeout(setTime_require_chat);
 vc_is_me_send = 0;
 show_button_require_chat();
 }
 
 /* trả lời tự động sau 1 khoảng thời gian dài */
 if(auto_rep_buzy == 1 && auto_rep_buzy_time > 0){
 clearTimeout(set_auto_rep_buzy);
 _vcclient.auto_rep_buzy();
 }
 }
 }
 }
 
 
 
 
 }
 });

 } /* End if(vgc_message_val == '') */
 
 setTimeout(function(){
 vgc_message.val(''); 
 },200);
 

 } /* End if(event.keyCode==13 || send == 'submit') */

} /* End function vgchatClientSend */

/**
 * function vgchatClientSaveInfoUser
 */
var cookie_vgc_name_contact = vgc_getCookie('vgc_name_contact');
var cookie_vgc_email_contact = vgc_getCookie('vgc_email_contact');
function vgchatClientSaveInfoUser(){

 var vgc_name_contact = $vnpJs('#vgc_name_contact');
 var vgc_email_contact = $vnpJs('#vgc_email_contact');

 vgc_name_contact_val = vgc_name_contact.val();
 vgc_email_contact_val = vgc_email_contact.val();



 vgc_setCookie({name : 'vgc_name_contact', value : vgc_name_contact_val, expires : 365});
 vgc_setCookie({name : 'vgc_email_contact', value : vgc_email_contact_val, expires : 365});

 vgchatClientToggle('vgc_setting_option_contact_details', 'hide');
 vgchatClientToggle('vgc_setting_option_default', 'show');

} /* End function vgchatClientSaveInfoUser */

/**
 * function vgchatClientToggleDiv - An hien khung chat
 object data
 data = {
 div_id:ten_div (id of element want to show),
 div_toggle:toggle (show, hide, toggle),
 require_info: 0 or 1 (1 bắt nhập, 0 không bắt nhập)
 event : event (event window. trường hợp greeting chat)
 }
 */
function vgchatClientToggleDiv(data_obj) {

 /* get data from object data */
 var div_id = data_obj.div_id || '';
 var div_toggle = data_obj.div_toggle || 'show';
 var require_info = data_obj.require_info;
 var msg = data_obj.msg || '';
 var _event = data_obj.event || window.event;
 var data_msg = data_obj.data_msg || '';

 if(require_info != 0) require_info = 1;

 if(div_id == '') return;

 /* div element */
 var e_div_id = $vnpJs('#'+ div_id);

 /* remove div wellcome chat (eye chat) */
 /*
 if($vnpJs('#vgbc_ichelp').length > 0){
 $vnpJs('#vgbc_ichelp').remove();
 }
 */

 /* remove div ad */
 if($vnpJs('#vgc_html_avg').length > 0) $vnpJs('#vgc_html_avg').remove();

 /* check if isset element div then action */
 if(e_div_id.length > 0){

 var e_div_check_status = $vnpJs('#'+div_id).hasClass('vgc_show');

 /* trường hợp ẩn box chat xuống */
 if((div_toggle == 'hide' || div_toggle == 'toggle') && e_div_check_status){
 
 if(!$vnpJs('#vgc_btn_chat_mobile').hasClass('vgc_hide')){
 $vnpJs('.panel_head_vgchat').addClass('vgc_hide');
 }
 /* lấy giá trị vị trí mặc định để cho về đúng vị trí */
 var vgc_define_position = 'right'; /* giá trị mặc định mình gắn cho để check so vs giá trị default */
 var vgc_default_position = $vnpJs('#vgc_default_position').val() || 'right';
 var vgc_value_default_position = $vnpJs('#vgc_value_default_position').val() +'px' || '50px';
 if(vgc_default_position == 'right') vgc_define_position = 'left';

 e_div_id.removeClass('vgc_show').addClass('vgc_hide');
 $vnpJs('.vgc_icon_plus').removeClass('vgc_hide').addClass('vgc_show_inline');
 $vnpJs('.vgc_icon_shrink').removeClass('vgc_show_inline').addClass('vgc_hide');
 $vnpJs('.vgc_client_close_polls').addClass('vgc_hide');
 $vnpJs('.vgbc_ichelp').removeClass('vgc_hide');
 $vnpJs('#vgc_bcl_move').removeClass('vgc_show').addClass('vgc_hide');
 $vnpJs('#vgc_bcl_bottom').removeClass('vgc_hide').addClass('vgc_show');
 $vnpJs('.template_vgchat').removeAttr('right left');
 $vnpJs('.template_vgchat').css(vgc_default_position, vgc_value_default_position)
 .css(vgc_define_position, 'auto')
 .css('bottom', '-1px')
 .css('top', 'auto');
 if(isset(typeof vchat_is_mobile)){
 $vnpJs('meta[name=viewport]').attr('content',vgc_viewport_default);
 if($vnpJs('#panel_chat_vatgia').length && !vgc_check_mobile_viewport){
 $vnpJs("#panel_chat_vatgia").css({"transform":"scaleX(3) scaleY(3)", "position":"fixed", "bottom":"0px", "right":"30%", "width":"100%", "max-width":"300px"});
 }
 document.getElementsByTagName('body')[0].style.cssText = '';
 }

 }

 /* trường hợp hiện box chat lên */
 if((div_toggle == 'show' || div_toggle == 'toggle') && !e_div_check_status){
 if(!$vnpJs('#vgc_btn_chat_mobile').hasClass('vgc_hide')){
 $vnpJs('.panel_head_vgchat').removeClass('vgc_hide');
 
 
 }
 $vnpJs('.vgc_greeting_chat').remove(); /* xóa greeting chat */
 var vgcPosition_l = $vnpJs('#vgcPosition_l').val();
 var vgcPosition_t = $vnpJs('#vgcPosition_t').val();
 if(vgcPosition_l >= 0 || vgcPosition_t >= 0){
 $vnpJs('.template_vgchat').css({'left':vgcPosition_l, 'top': vgcPosition_t, 'bottom':'auto', 'right':'auto'});
 }

 if(isset(typeof vchat_is_mobile)){
 if($vnpJs("meta[name=viewport]").length){
 $vnpJs("meta[name=viewport]").attr("content", vgc_viewport_default);/*"width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0");*/
 }else{
 $vnpJs("head").append('<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />');
 }


 if($vnpJs('#panel_chat_vatgia').length && !vgc_check_mobile_viewport){
 $vnpJs("#panel_chat_vatgia").css({"transform":"none", "position":"fixed", "bottom":"0px", "right":"0", "width":"100%", "max-width":"300px"});
 }
 
 document.getElementsByTagName('body')[0].style.cssText = 'position: fixed !important;overflow-y: hidden !important; width: 100% !important; height: 100% !important; left: 0px !important; right: 0px !important; top: 0px !important; bottom: 0px !important;';
 }
 
 vChatEvent.userOpen();
 e_div_id.removeClass('vgc_hide').addClass('vgc_show');
 $vnpJs('.vgc_icon_plus').removeClass('vgc_show_inline').addClass('vgc_hide');
 $vnpJs('.vgc_icon_shrink').removeClass('vgc_hide').addClass('vgc_show_inline');
 $vnpJs('.vgc_client_close_polls').removeClass('vgc_hide');
 $vnpJs('.vgbc_ichelp').addClass('vgc_hide');
 $vnpJs('#vgc_bcl_bottom').removeClass('vgc_show').addClass('vgc_hide');
 $vnpJs('#vgc_bcl_move').removeClass('vgc_hide').addClass('vgc_show');
 /* Khi nguoi dung click khung chat hien len thi thuc hien */

 /* Dua count message ve 0 */
 vgc_count_msg = 0;

 /* Remove khung count */
 if($vnpJs('.vgc_count_message').length > 0){
 $vnpJs('.vgc_count_message').remove();
 }

 /* Neu chua co history duoc load */
 if(vgc_check_get_history == false){
 /* Load history */
 var obj_data = {require_info : require_info};
 vgchatClientGetHistoryChat(obj_data);
 }


 /* ScrollBot chat */
 var vgc_time_out = 300;
 if(vgc_check_get_history == true) vgc_time_out = 0;
 setTimeout(
 function(){
 vgchatClientscrollBot();
 /* nội dung tin nhắn khi sử dụng show boxchat */
 if(data_msg != ''){
 var sitv = setInterval(function(){
 if(vgc_check_get_history==true){
 vgchatClientAppendMsgToBoxchat(data_msg);
 clearInterval(sitv);
 setTimeout(function(){vgchatClientscrollBot();},300); 
 }
 }, 1000);
 
 }
 }, vgc_time_out
 );

 /* gửi tin nhắn greeting chat của khách hàng nếu có */
 if(msg != ''){
 var _s = setInterval(function(){
 if($vnpJs('#vgc_message').length){

 clearInterval(_s);
 $vnpJs('#vgc_message').val(msg);
 vgchatClientSend(_event, 'submit');

 }
 }, 200);

 }
 }

 } /* End if(e_div_id) */

 /* tổng số lần box chat được show lên tự động trước là > 10 lần trong ngày thì thôi */
 vgc_setCookie({name : 'vgc_showboxafter', value : 1, expires : 1});

} /* End function vgchatClientToggleDiv */


/**
 * function vgchatClientChangeClass
 */
function vgchatClientChangeClass(_this, _this_class_before, _this_class_after){
 _this = document.getElementById(_this);
 if(_this && _this_class_before && _this_class_after){
 _this.className = _this.className.replace(_this_class_before, _this_class_after);
 }
} /* End function vgchatClientChangeClass */

/**
 * function vgchatClientToggleIcon
 */
var vgc_check_config_sound = vgc_getCookie('vgc_check_config_sound') || '1';
function vgchatClientToggleIcon(_this, _this_class_before, _this_class_after) {

 $vnpJs('#'+_this).toggleClass(_this_class_before+' '+_this_class_after);

 if(_this == 'vgc_sound'){
 if($vnpJs('#'+_this).hasClass(_this_class_before)) vgc_check_config_sound = '1';
 if($vnpJs('#'+_this).hasClass(_this_class_after)) vgc_check_config_sound = '0';
 vgc_setCookie({name : 'vgc_check_config_sound', value : vgc_check_config_sound, expires : 365});
 }

} /* End function vgchatClientToggleIcon */

/**
 * function vgchatClientToggle
 */
function vgchatClientToggle(div_id, div_toggle) {
 var e_div_id = $vnpJs('#'+div_id);

 if(e_div_id.length > 0){

 if(div_toggle == 'hide'){
 e_div_id.removeClass('vgc_show').addClass('vgc_hide');
 }else if(div_toggle == 'show'){
 e_div_id.removeClass('vgc_hide').addClass('vgc_show');
 }else{
 e_div_id.toggleClass('vgc_show vgc_hide');
 }

 } /* End if(e_div_id) */
 vgc_new_msg = 0;
 if(isset(typeof vgc_isTabActive)) vgc_isTabActive = 1;
 document.title = vgc_title;

} /* End function vgchatClientToggle */

function vchat_check_send_btn(obj){
 var objtext = $vnpJs(obj);
 var msg = '';
 var typing_msg = '';
 
 if(objtext.val().trim() != ''){
 msg = objtext.val(); 
 typing_msg = 'typing';
 }else{
 msg = '';
 typing_msg = 'deleting';
 }
 
 /* tìm cách bắn sang cho người kia biết là mình đang gõ vào ô chat */ 
 if(is_send_typing){ 
 clearTimeout(is_typing);
 is_send_typing = false;
 is_typing = setTimeout(function(){ 
 var data = {};
 data.msg = objtext.val();
 data.to_id = $vnpJs('#vgc_to_id').val();
 data.send_id = $vnpJs('#vgc_send_id').val();
 data.hash = $vnpJs('#vgc_hash').val();
 data.typing_text = typing_msg;
 /* send */
 typing(data); 
 clearTimeout(is_typing);
 }, 500);
 };
 
 if(typeof vchat_is_mobile != 'undefined'){
 if($vnpJs('#vgc_send_btn').length){
 $vnpJs('#vgc_send_btn').removeClass('vgc_hide')
 }
 }
}


function typing(opt){
 var data = {};
 data.message = opt.msg || '';
 data.to_id = opt.to_id;
 data.send_id = opt.send_id;
 data.hash = opt.hash;
 data.typing_text = opt.typing_text;
 
 is_send_typing = false;
 $vnpJs.ajax({
 url: '//live.vnpgroup.net/js/typing.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: data,
 success: function(res){
 if(opt.typing_text == 'deleting') is_send_typing = true;
 // sau 3s lại gửi đi lần nữa
 clearTimeout(sto_typing);
 sto_typing = setTimeout(function(){
 is_send_typing = true;
 }, 3000); 
 }
 });
}

/**
 * function vgchatClientCloseDiv - Xoa khung chat
 */
function vgchatClientCloseDiv(div_id){
 $vnpJs('#'+div_id).remove();
} /* End function vgchatClientCloseDiv */

/*
 * Stop Event
 */
function vgchatClientStopEvent(e) {
 if (!e) var e = window.event;

 /* e.cancelBubble is supported by IE - this will kill the bubbling process. */
 e.cancelBubble = true;
 e.returnValue = false;

 /* e.stopPropagation works only in Firefox. */
 if (e.stopPropagation) e.stopPropagation();
 if (e.preventDefault) e.preventDefault();

 return false;
} /* End function vgchatClientStopEvent */


/* Function slideup,down advertis */
var vgc_current_sl = 0;
var vgc_current_sl_next = 0;
var vgc_top = 50;

timeInterValSlide = setInterval(function sliderAdvertis(){
 if(document.getElementById('vgc_ad') == null){
 clearInterval(timeInterValSlide);
 return false;
 }
 var vgc_advertis_item = document.getElementById('vgc_ad').getElementsByTagName('li');
 var vgc_ad_len = vgc_advertis_item.length;
 if(vgc_ad_len > 0){

 vgc_current_sl_next = vgc_current_sl + 1;
 if(vgc_current_sl_next >= vgc_ad_len ){
 vgc_current_sl_next = 0;
 }


 var vgc_li_next = vgc_advertis_item[vgc_current_sl_next];
 for(i = 0; i < vgc_ad_len; i++){
 var vgc_li = vgc_advertis_item[i];
 vgc_li.style.top = '50px';
 }
 var s = setInterval(function(){
 vgc_top -= 5;
 vgc_li_next.style.top = vgc_top +'px';
 if(vgc_top <= 0){
 clearInterval(s);
 vgc_top = 50;
 }

 }, 50);
 vgc_current_sl += 1;
 if(vgc_current_sl == vgc_ad_len) vgc_current_sl = 0;

 }else{
 clearInterval(timeInterValSlide);
 }

}, 5000);

/**
 Function send polls client box chat
*/
function polls_vgc_send(){
 /* lấy data của from bắn đi */
 var pollsData = $vnpJs('#pollsSend').serialize();

 var poll_btn = $vnpJs('polls_btn')
 var poll_load = $vnpJs('#poll_load');

 /* lấy id của sso */
 var sso_id_support = $vnpJs('polls_sso_id').val() || 0;

 /* lấy id của send id */
 var send_id = $vnpJs('polls_send_id').val() || 0;

 /* kiểm tra xem đã chọn chưa */
 var radioChecked = $vnpJs('input[name=polls]:checked').val() || 0;

 if(radioChecked > 0){

 poll_btn.attr('disabled', true);
 poll_load.removeClass('vgc_hide');

 $vnpJs.ajax({
 url: url_server_vgchat_client + 'ajax_polls_add.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: pollsData,
 error: function(err) {},
 success: function(res) {
 poll_load.addClass('vgc_hide');
 if(res.status == 1){
 $vnpJs('#polls_vgc').remove();
 $vnpJs('.panel_control_vgchat').removeClass('vgc_hide');
 }else{
 $vnpJs('#polls_error').html(res.error);
 }
 }
 });
 }else{
 $vnpJs('#polls_error').html('Vui lòng chọn câu trả lời');
 }

}

function vgc_close_polls(){
 $vnpJs('#polls_vgc').remove();
 $vnpJs('.panel_control_vgchat').removeClass('vgc_hide');
}

function vgc_send_guest_info(){
 var elm_name = $vnpJs('#vgc_client_info_name').val() || '';
 var elm_email = $vnpJs('#vgc_client_info_email').val() || '';
 var elm_phone = $vnpJs('#vgc_client_info_phone').val() || '';
 var elm_address = $vnpJs('#vgc_client_info_address').val() || '';
 var elm_error = $vnpJs('#vgc_client_info_error');
 var elm_load = $vnpJs('#vgc_client_info .poll_load');
 var elm_guest_id = $vnpJs('#vgc_client_info_guest_id').val() || '';
 var elm_hash = $vnpJs('#vgc_client_info_hash').val() || '';
 var elm_to_id = $vnpJs('#vgc_client_info_to_id').val() || '';
 var elm_office = $vnpJs('#vgc_optselect_office');
 var elm_quest = $vnpJs('#vgc_client_info_question').val() || '';
 var elm_quest_name = $vnpJs('#vgc_client_info_question').data('quest') || '';

 var error = '';
 var error_office = 'Bạn chưa chọn bộ phận hỗ trợ <br>';
 var error_name = 'Bạn chưa nhập tên <br>';
 var error_email = 'Bạn chưa nhập email <br>';
 var error_phone = 'Bạn chưa nhập số điện thoại <br>';
 var error_mes = 'Bạn chưa nhập nội dung tin nhắn <br>';
 var error_address = 'Bạn chưa nhập địa chỉ <br>';
 var email_fail = 'Email không đúng định dạng<br>';
 var error_quest = 'Bạn chưa trả lời câu hỏi của chủ website <br>';
 var phone_fail = 'Số điện thoại không đúng định dạng<br>';
 
 if(isset(vc_lang)){
 error_office = vc_lang.error_office + '<br>';
 error_name = vc_lang.error_name + '<br>';
 error_email = vc_lang.error_email + '<br>';
 error_phone = vc_lang.error_phone + '<br>';
 error_mes = vc_lang.error_mes + '<br>';
 error_address = vc_lang.error_address + '<br>';
 email_fail = 'Malformed email<br>';
 error_quest = 'You have not answered questions of website owners <br>';
 phone_fail = 'Malformed phone<br>';
 }

 /* kiểm tra thêm nếu có phần chọn bộ phận thì xem đã chọn chưa */
 if(elm_office.length){
 if(elm_office.val() <= 0){
 error += error_office;
 }
 }

 $vnpJs('#vgc_client_info .form_info_text').each(function(){
 var dtype = $vnpJs(this).data('type') || 'str';
 var txt = $vnpJs(this).val() || '';
 var rq = $vnpJs(this).data('require') || 0;
 var name = $vnpJs(this).data('name') || 0;
 if(rq == 1 && txt == ''){
 switch(dtype){
 case 'email':
 error += error_email;
 break;
 case 'name':
 error += error_name;
 break;
 case 'phone':
 error += error_phone;
 break;
 case 'address':
 error += error_address;
 break;
 case 'mes':
 error += error_mes;
 break;
 case 'quest':
 error += error_quest;
 break;
 } 
 }
 if(txt != ''){
 switch(dtype){
 case 'email':
 var regular_email = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
 if(!regular_email.test(txt)){
 error += email_fail;
 }
 break;
 case 'phone':
 var regular_phone = /[0-9 -()+]+$/;
 if(txt.match(/\d/g).length < 9 || txt.match(/\d/g).length > 16){
 error += phone_fail;
 }
 break;
 }
 }
 });

 if(error != ''){
 elm_error.html(error);
 return false;
 }else{
 elm_error.html('');
 elm_load.removeClass('vgc_hide');

 client_info_Data = {};
 client_info_Data.name = elm_name;
 client_info_Data.phone = elm_phone;
 client_info_Data.address = elm_address;
 client_info_Data.guest_id = elm_guest_id;
 client_info_Data.hash = elm_hash;
 client_info_Data.to_id = elm_to_id;
 client_info_Data.email = elm_email;
 client_info_Data.quest = elm_quest;
 client_info_Data.quest_name = elm_quest_name;

 var name_guest = elm_name +' - '+elm_phone;
 $vnpJs('#vgc_name').val(name_guest);
 
 /* send event guest */
 vChatEvent.sendContact();
 
 $vnpJs.ajax({
 url: url_server_vgchat_client + 'ajax_client_info_add.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: client_info_Data,
 error: function(err) {},
 success: function(res) {
 elm_load.addClass('vgc_hide');
 if(res.status == 1 || res.error == ''){
 vgc_close_guest_info(); 
 /* tạo tin nhắn gửi cho chủ website */
 
 var _newmessage = 'Khách hàng vừa nhập Form thông tin trước khi chat <br>';
 if(client_info_Data.name != ''){
 _newmessage += 'Tên khách: '+ client_info_Data.name +'<br>'; 
 }
 if(client_info_Data.phone != ''){
 _newmessage += 'Số điện thoại: '+ client_info_Data.phone +'<br>';
 }
 if(client_info_Data.email != ''){
 _newmessage += 'Email: '+ client_info_Data.email +'<br>';
 }
 if(client_info_Data.address != ''){
 _newmessage += 'Địa chỉ: '+ client_info_Data.address +'<br>';
 }
 if(client_info_Data.quest != '' && client_info_Data.quest_name != ''){
 _newmessage += 'Trả lời câu hỏi ['+ client_info_Data.quest_name +']: '+ client_info_Data.quest;
 }
 
 // send
 $vnpJs('#vgc_message').val(_newmessage);
 vgchatClientSend('', 'submit');
 
 }else{
 elm_error.html(res.error);
 }
 }
 });
 }



}
function vgc_close_guest_info(){
 $vnpJs('#vgc_client_info').remove();
 var elm_polls = $vnpJs('#polls_vgc');
 if(elm_polls.length){
 elm_polls.removeClass('vgc_hide');
 }else{
 $vnpJs('.panel_control_vgchat').removeClass('vgc_hide');
 }

}

function closeAdVgc(){
 clearInterval(timeInterValSlide);
 var element = document.getElementById("vgc_ad_bottom");
 if(element) element.parentNode.removeChild(element);
}

/* save chat contact */
function vgc_boxchat_send_msg_offline(estore_id){
 $vnpJs('#vgc_er').html('');
 var error = '';
 var vgc_link = window.location.href;

 var vgc_name = $vnpJs('#vgc_use_name').val() || '';
 var vgc_email = $vnpJs('#vgc_use_email').val() || '';
 var vgc_phone = $vnpJs('#vgc_use_phone').val() || '';
 var strmsg = $vnpJs('#vgc_msg_off').val() || '';
 var vgc_address = $vnpJs('#vgc_address').val() || '';
 
 
 var error_name = 'Bạn chưa nhập tên <br>';
 var error_email = 'Bạn chưa nhập email <br>';
 var error_phone = 'Bạn chưa nhập số điện thoại <br>';
 var error_mes = 'Bạn chưa nhập tin nhắn <br>';
 var error_phone_format = 'Số điện thoại chưa đúng <br>';
 var error_email_format = 'Email sai định dạng <br>';
 var error_msg_short = 'Nội dung quá ngắn <br>';
 
 if(isset(vc_lang)){
 var error_name = vc_lang.error_name + '<br>';
 var error_email = vc_lang.error_email+ '<br>';
 var error_phone = vc_lang.error_phone + '<br>';
 var error_mes = vc_lang.error_mes + '<br>';
 error_phone_format = 'Phone number failed <br>';
 error_email_format = 'Email failed <br>';
 error_msg_short = 'Content is too short <br>';
 }
 
 /* check thông tin trước khi gửi đi */
 if(vgc_name == ''){
 error += error_name;
 }
 
 var phone_require = parseInt($vnpJs('#vgc_use_phone').data('require')) || 0;
 var email_require = parseInt($vnpJs('#vgc_use_email').data('require')) || 0;
 
 console.log(phone_require, email_require);
 if(phone_require == 1){
 if(vgc_phone == ''){
 error += error_phone;
 }else{
 if(vgc_phone.match(/\d/g).length < 9 || vgc_phone.match(/\d/g).length > 16){
 error += error_phone_format;
 }
 }
 }
 
 
 if(email_require == 1){
 if(vgc_email == ''){
 error += error_email;
 }else{
 var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
 if(!re.test(vgc_email)){
 error += error_email_format;
 }
 }
 }

 if(strmsg == ''){
 error += error_mes;
 }else{
 if(strmsg.length < 5){
 error += error_msg_short;
 }
 }
 
 

 if(error == ''){

 var data_post = {
 vgc_use_name : vgc_name,
 vgc_use_email : vgc_email,
 vgc_use_phone : vgc_phone,
 vgc_msg_off : strmsg,
 vgc_link : vgc_link,
 vgc_estore : estore_id,
 vgc_myid : $vnpJs('#vgc_myid').val(),
 vgc_address : vgc_address
 }

 /* save cookie */
 vgc_setCookie({name : 'vchat_web_name', value : vgc_name, expires : 600});
 vgc_setCookie({name : 'vchat_web_email', value : vgc_email, expires : 600});
 vgc_setCookie({name : 'vchat_web_phone', value : vgc_phone, expires : 600});
 //$vnpJs('#vgc_frm_off').serialize()+'&vgc_link='+vgc_link+'&vgc_estore='+estore_id;

 $vnpJs('#vgc_bc_off').append('<div id="vgc_loading" align="center"><span style="margin-top: 50px;" class="vgc_ic_loading"></span></div>');
 
 /* send message offline */
 vChatEvent.sendMessage();
 
 $vnpJs.ajax({
 url: url_server_vgchat_client + 'aj_chat_contact.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: data_post,
 error: function(err) {},
 success: function(res) {

 if(res.status == 1){
 $vnpJs('#vgc_er').html(res.note);
 $vnpJs('.vgc_remove').remove();
 setTimeout(function(){
 $vnpJs('.vgc_off_row').css('display', 'none');
 $vnpJs('#vgc_logo_msgoffline').css('display', 'none');
 $vnpJs('#vgc_off_close').css('display', 'none');
 }, 2000);
 
 /* setcookie để không tự động bật lên nữa */
 vgc_setCookie({name : 'auto_open_offline', value : 1, expires : 1});
 
 }else{
 $vnpJs('#vgc_er').html(res.error);
 }

 /* Xoa icon loading */
 $vnpJs('#vgc_loading').fadeOut(300);
 setTimeout(function(){
 $vnpJs('#vgc_loading').remove();
 }, 1000);

 }
 });

 }else{
 $vnpJs('#vgc_er').html(error);
 return false;
 }
}

/*
 Gọi hàm này khi boxchat offline
 - check xem khách hàng đã nhập thông tin chưa
 - nhập rồi thì không bật nữa
 - chưa nhập thì bật box lên
*/
function call_open_offline(){
 /* Đầu tiên là xóa hết các element thừa */
 var _box_offline = document.getElementsByClassName('vgc_off_row');
 var _box_online = document.getElementsByClassName('template_vgchat');
 if(_box_offline.length > 0){
 var _length = _box_offline.length;
 for(i=1;i<_length;i++){ _box_offline[i].parentNode.parentNode.remove() }
 
 var _is_open = parseInt(vgc_getCookie('auto_open_offline')) || 0;
 if(_is_open == 0){
 /* tính thời gian timeout để bật lên */
 var _time_open = 2000;
 if(typeof vchat_is_mobile !== 'undefined'){
 _time_open = 20000;
 }
 
 console.log('auto open offline');
 
 clearTimeout(remove_element_vchat);
 remove_element_vchat = setTimeout(function(){
 vgc_sh_chat_contact();
 }, _time_open);
 }
 
 }else{
 var _length = _box_online.length;
 for(i=1;i<_length;i++){ _box_online[i].parentNode.remove() }
 }
 
 
}

/* Function show, hide box chat contact */
function vgc_sh_chat_contact(){
 var vgc_frm = $vnpJs('.vgc_off_row');
 var vgc_close = $vnpJs('#vgc_off_close');
 var vgc_logo = $vnpJs('#vgc_logo_msgoffline');
 if(vgc_frm !== null){
 var vgc_frm_sh = vgc_frm.css('display');
 if(vgc_frm_sh == 'block'){
 vgc_frm.css('display','none');
 vgc_close.css('display','none');
 vgc_logo.css('display','none'); 

 //check mobile thì thêm thẻ meta viewport
 if(isset(typeof vchat_is_mobile)){
 $vnpJs('#vgc_bc_off').addClass('vgc_hide');
 $vnpJs('meta[name=viewport]').attr('content',vgc_viewport_default);
 if($vnpJs('#panel_chat_vatgia').length && !vgc_check_mobile_viewport){
 $vnpJs("#panel_chat_vatgia").css({"transform":"scaleX(3) scaleY(3)", "position":"fixed", "bottom":"0px", "right":"30%", "width":"100%", "max-width":"300px"});
 }
 }
 }else{

 /* get cookie */
 var vgc_name = vgc_getCookie('vchat_web_name') || '';
 var vgc_email = vgc_getCookie('vchat_web_email') || '';
 var vgc_phone = vgc_getCookie('vchat_web_phone') || '';

 $vnpJs('#vgc_use_name').val(vgc_name);
 $vnpJs('#vgc_use_email').val(vgc_email);
 $vnpJs('#vgc_use_phone').val(vgc_phone);

 vgc_frm.css('display','block');
 vgc_close.css('display','block');
 vgc_logo.css('display','block');

 //check mobile thì thêm thẻ meta viewport
 if(isset(typeof vchat_is_mobile)){
 
 $vnpJs('#vgc_bc_off').removeClass('vgc_hide');
 if($vnpJs("meta[name=viewport]").length){
 $vnpJs("meta[name=viewport]").attr("content", vgc_viewport_default); /*"width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0");*/
 }else{
 $vnpJs("head").append('<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />');
 }


 if($vnpJs('#panel_chat_vatgia').length && !vgc_check_mobile_viewport){
 $vnpJs("#panel_chat_vatgia").css({"transform":"none", "position":"fixed", "bottom":"0px", "right":"0", "width":"100%", "max-width":"300px"});
 }
 }
 }
 }
 if($vnpJs('#vgc_html_avg').length > 0) $vnpJs('#vgc_html_avg').remove();
}

/*
 kiểm tra nếu có eye chat thì sau khi eyechat ẩn sẽ hiển thị quảng cáo
 - nếu không có eyechat thì sau 4s sẽ bật lên quảng cáo
*/
function vatgiaClient_show_advertive(){
 /* kiểm tra cookie xem còn thời gian để show quảng cáo không trong vòng 30 phút đã tắt đi thì không hiển thị lại */
 var vgc_close_advg = parseInt(vgc_getCookie('vgc_close_advg')) || 0;
 if(vgc_close_advg == 0){
 var elm = $vnpJs('#vgc_html_avg');
 if(elm.length > 0){
 elm.removeClass('vgc_hide');
 setTimeout(function(){
 elm.remove();
 }, 10000);
 }
 }
}

/* time out 4s then show icon help */
function vatgiaClient_help(){
 var elm = $vnpJs('#vgbc_ichelp');
 if(elm.length > 0){
 elm.removeClass('vgc_hide');

 setTimeout(function(){
 elm.remove();
 vatgiaClient_show_advertive();
 }, 10000);
 }else{
 vatgiaClient_show_advertive();
 }
}

/* tạm bỏ để chuyển sang dạn object */
/*
setTimeout("vatgiaClient_help()", 3000);
*/

function vatgiaClient_Closehelp(){
 $vnpJs('#vgbc_ichelp').remove();
 vatgiaClient_show_advertive();
}

/**
 Function slide message note offline
*/
function message_note_offline_slide(){
 var vgc_top = 30;
 setTimeout(function(){
 var elmMsg = document.getElementById('vatgia_note_message');
 if(isset(typeof elmMsg)){
 if(elmMsg != null){
 var class_name = elmMsg.className;
 elmMsg.className = class_name.replace('vgc_hide', '');
 var s = setInterval(function(){
 vgc_top += 10;
 elmMsg.style.bottom = vgc_top +'px';
 if(vgc_top >= 80){
 clearInterval(s);
 /*
 setTimeout(function(){
 elmMsg.remove();
 }, 10000);*/
 }
 }, 30);
 }
 }
 }, 500);
}
/* slide tin nhắn offline */
setTimeout("message_note_offline_slide()", 2000);

function isset(myVariable) {
 if (myVariable != "undefined" && myVariable != undefined && myVariable != null){
 return true;
 }else{
 return false;
 }
}

/* check chỉ lấy thông tin notice 1 lần */
var vgc_check_get_notice = false;

/* function show thông tin chi tiết những gười đã nhắn tin cho user khi họ online từ vatgia */
function vatgiaClient_show_notice_vg(guest_id){
 var gid = parseInt(guest_id) || 0;
 if(gid <= 0) return false;

 if(!vgc_check_get_notice){
 var ga = document.createElement("script");
 ga.type = "text/javascript";
 ga.id = "vgc_html_notice_vg";
 ga.src = url_server_vgchat_client+"ajax_detail_notice_vg.php?gid="+guest_id;
 var s = document.getElementsByTagName("script");
 s[0].parentNode.insertBefore(ga, s[0]);
 }else{
 vgc_notice_close(0);
 }
}

/* Function remove box notify message */
function vgc_close_notifymsg(){
 var vg_notice_msg = $vnpJs('#vatgia_note_message');
 if(vg_notice_msg.length > 0){
 vg_notice_msg.remove();
 }
}

/*
 Hàm tăt notice của vatgia
 type_action: 0 => mặc định
 type_action: 1 => close
*/
function vgc_notice_close(type_action){
 if( !isset(typeof type_action) ) type_action = 0;
 var elm = document.getElementById('vgc_notice_vatgia');
 if(isset(typeof elm)){
 var class_name = elm.className;
 if(parseInt(type_action) > 0 ){
 elm.className = class_name.replace('vgc_hide', '');
 elm.className = class_name +' vgc_hide';
 }else{
 if(class_name.indexOf('vgc_hide') == -1){
 elm.className = class_name +' vgc_hide';
 }else{
 elm.className = class_name.replace('vgc_hide', '');
 }
 }

 }
}

/* Sau 3s hiện lên thông báo hỏi quyền notification người dùng nếu không blog */
/*
setTimeout(function(){
 var isFirefox = typeof InstallTrigger !== 'undefined';
 if (("Notification" in window)) {
 if(Notification.permission === "granted"){

 }
 else if(Notification.permission === "default"){
 Notification.requestPermission(function(status){ });
 }
 else if(Notification.permission === "denied"){

 }
 }
}, 3000);
*/

function execute_notification(obj){
 if(isShowNotifi == 1) return false;
 isShowNotifi = 1;
 var _title = (obj.name !== '')? obj.name +' nhắn tin' : 'Vchat thông báo';
 var _msg = (obj.msg !== '')? obj.msg : 'message';
 var _msg_text = (obj.text !== '')? obj.text : _msg;
 var _icon = 'http://vchat.vn/themes/v1/images/icon.png';
 var _notifi = new Notification(
 _title,
 {
 body: _msg_text
 ,icon: _icon
 ,tag: 'remove'
 }
 );
 //_notifi.close();
 _notifi.onshow = function(){
 setTimeout(function(){
 _notifi.close();
 }, 3000);
 };
 setTimeout(function(){isShowNotifi = 0;}, 5000);
}

/**
 Function create notifi chrome 5+, firefox 22+, safari 15+
*/
function create_notification_browser(obj){
 /* kiểm tra xem trình duyệt có hỗ trợ notification không và xin quyền của user */
 Notification.requestPermission(function(status){ });
 $vnpJs(obj).parent().hide();
}

function vgc_close_get_notification(obj){
 $vnpJs(obj).parent().hide();
 return false;
}
function remove_box_chat(id, obj){
 var elm_error = $vnpJs('#vatgia_client_polls .vgc_p_a_error');
 vatgiaClient_remove_polls();
 elm_error.html('');
}
function vatgiaClient_remove_polls(){
 $vnpJs('#vatgia_client_polls').addClass('vgc_hide');
 var data_show_box = {div_id : "panel_body_vgchat", div_toggle : "toggle"};
 vgchatClientToggleDiv(data_show_box);
 /* bỏ quere để cho người khác chat tiếp */
 /*
 if(isset(typeof vgc_js_permission) && isset(typeof vgc_js_permission.max_chat)){
 var to_id = $vnpJs('#vgc_to_id').val() || 0;
 var send_id = $vnpJs('#vgc_send_id').val() || 0;
 var hash = $vnpJs('#vgc_hash').val() || '';
 var quere = to_id +':'+ send_id;
 var data_post = {send_id : send_id, to_id : to_id, hash : hash, quere : quere};
 $vnpJs.ajax({
 url: url_server_vgchat_client + 'ajax_close_quere.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: data_post,
 error: function(err) {},
 success: function(res) {
 if(res.status == 0){
 alert(res.error);
 return false;
 }
 }
 });
 }
 */
}
function vgc_get_polls_after(id){
 if(parseInt(id) <= 0) return;
 var elm_error = $vnpJs('#vatgia_client_polls .vgc_quest_error');

 var data_post = {};
 data_post.est = parseInt(id);
 data_post.hash = $vnpJs('#vgc_hash').val();
 data_post.to_id = parseInt(id);
 data_post.send_id = $vnpJs('#vgc_send_id').val();

 $vnpJs('#vatgia_client_polls .vgc_p_a_loadding').removeClass('vgc_hide');
 $vnpJs.ajax({
 url: url_server_vgchat_client + 'ajax_polls_after.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: data_post,
 error: function(err) {},
 success: function(res) {
 if(res.status == 0){
 elm_error.html(res.error);
 setTimeout("vatgiaClient_remove_polls()", 2000);
 $vnpJs('#vatgia_client_polls .vgc_p_a_loadding').addClass('vgc_hide');
 }else{
 $vnpJs('#vatgia_client_polls').html(res.html);
 return false;
 }

 }
 });
}
function vatgiaClient_polls_call_after(e){
 var elm = $vnpJs('#vatgia_client_polls');
 var poll_vl = $vnpJs('#vgc_poll_after').val() || 1;
 if(poll_vl > 0){
 /*
 if(elm.hasClass('vgc_hide')){
 elm.removeClass('vgc_hide');
 }else{
 remove_box_chat(1,{a:1});
 }
 */
 
 
 var chat_len = $vnpJs('#panel_history_vgchat .vgc_rowme').length;
 if(chat_len > 1){
 if(elm.hasClass('vgc_hide')){
 elm.removeClass('vgc_hide');
 }else{
 remove_box_chat(1,{a:1});
 }
 }else{
 var data_show_box = {div_id : "panel_body_vgchat", div_toggle : "toggle"};
 vgchatClientToggleDiv(data_show_box);
 }
 
 }else{
 var data_show_box = {div_id : "panel_body_vgchat", div_toggle : "toggle"};
 vgchatClientToggleDiv(data_show_box);
 }

 /* tính end chat cho khách hàng */
 /*vgc_end_chat();*/
 vgchatClientStopEvent(e);
 vgc_check_get_history = false;
}
function set_polls_after(obj){

 var id = obj.id || 0;
 var quest = obj.quest || 0;
 var new_id = id;
 var poa_ans = 0;
 if(parseInt(id) <= 0) return false;
 var total_check = 0;
 var elm_error = $vnpJs('#vatgia_client_polls .vgc_p_a_error');

 $vnpJs('.poa_ans').each(function(){
 if($vnpJs(this).is(':checked')){
 total_check++;
 poa_ans = $vnpJs(this).val();
 }
 })

 var support_id = $vnpJs('#vgc_support_id').val() || 0;
 if(support_id > 0){
 new_id = support_id;
 }

 if(total_check <= 0){
 elm_error.html('Bạn vui lòng chọn 01 lựa chọn');
 return false;
 }else{

 /* set poll after */
 var data_post = {};
 data_post.est = new_id;
 data_post.hash = $vnpJs('#vgc_hash').val();
 data_post.to_id = id;
 data_post.send_id = $vnpJs('#vgc_send_id').val();
 data_post.poa_id = quest;
 data_post.poa_ans = poa_ans;

 $vnpJs('#vatgia_client_polls .vgc_p_a_loadding').removeClass('vgc_hide');
 $vnpJs.ajax({
 url: url_server_vgchat_client + 'ajax_polls_after_save.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: data_post,
 error: function(err) {},
 success: function(res) {
 if(res.status == 0){
 elm_error.html(res.error);
 $vnpJs('#vatgia_client_polls .vgc_p_a_loadding').addClass('vgc_hide');
 }else{
 $vnpJs('#vatgia_client_polls').remove();
 vatgiaClient_remove_polls();
 return false;
 }

 }
 });
 }
}

/*
 config after 10s show boxchat
 $vnpJs.cookie('noti_msg', msg, {expires: 30*86400});
*/
function vgc_show_box_after20s(_time){
 var vgc_body = $vnpJs('#panel_body_vgchat');
 var vgc_cookie_show = parseInt(vgc_getCookie('vgc_showboxafter'));
 if(vgc_cookie_show < 10){
 if(vgc_body.css('display') !== 'block'){
 clearInterval(vgc_showbox_interval);
 setTimeout(function(){
 var data_show_box = {div_id : "panel_body_vgchat", div_toggle : "show", require_info : 0};
 vgchatClientToggleDiv(data_show_box);
 vgc_setCookie({name : 'vgc_showboxafter', value : (vgc_cookie_show + 1), expires : 1});
 }, (_time * 1000));
 }
 }else{
 clearInterval(vgc_showbox_interval);
 }
}

/**
 Call function config
 : gọi function để show eyechat, greeting :
 vl: 0=> desktop, 1=> mobile
*/
function call_start_function_client(vl){
 
 if(parseInt(vl) == 0){
 if(isset(typeof _vcclient_config)){
 if(isset(typeof _vcclient_config.orther)){
 if(isset(typeof _vcclient_config.orther.require_chat)){
 run_require_chat = _vcclient_config.orther.require_chat.vl;
 vc_time_wait_chat = _vcclient_config.orther.require_chat.time;
 }
 
 if(isset(typeof _vcclient_config.orther.auto_rep_buzy)){
 auto_rep_buzy = _vcclient_config.orther.auto_rep_buzy.vl;
 auto_rep_buzy_time = _vcclient_config.orther.auto_rep_buzy.time;
 }
 
 if(isset(typeof _vcclient_config.orther.rep_when_cancel)){
 rep_when_cancel = _vcclient_config.orther.rep_when_cancel.vl;
 rep_when_cancel_text = _vcclient_config.orther.rep_when_cancel.text;
 }
 
 }
 }
 
 
 /* fix số trang người này đang xem trong vòng 30 phút */
 var pageNumber = parseInt(vgc_getCookie('vgc_page_number')) || 0;
 vgc_setCookie({name : 'vgc_page_number', value : (pageNumber + 1), expires : 30, type : 'm'});
 
 if(isset(typeof _vcclient_config)){
 
 /* B1: Ưu tiên hiển thị eyechat đầu tiên, nếu không có thì mới đến show boxchat tiếp đó mới đến new_visit */
 var show_eyechat = 0;
 if(isset(typeof _vcclient_config.eyechat)){
 if(isset(typeof _vcclient_config.eyechat.show_eye_chat)){
 show_eyechat = _vcclient_config.eyechat.show_eye_chat;
 }
 }
 
 if(show_eyechat == 1){
 var _time_show = _vcclient_config.eyechat.time_show_eye_chat || 2;
 var _time_close = _vcclient_config.eyechat.time_close_eye_chat || 10;
 _vcclient.show_eyechat({time_show : _time_show,time_close : _time_close});
 }else{
 /* không tồn tại eye chat thì mới đến show boxchat */
 var _greeting_time = _vcclient_config.greeting.invite_showbox.time || 1;
 _vcclient.greeting_showboxchat(_greeting_time);
 } 
 }
 }else{
 
 if(isset(typeof _vcclient_config)){
 var _greeting_time = _vcclient_config.greeting.invite_showbox.time || 1;
 var _greeting_mobile = _vcclient_config.greeting.invite_showbox.vl_mobile || 0;
 if(_greeting_mobile == 1){
 _vcclient.greeting_showboxchat_mobile(_greeting_time);
 }
 }
 
 }

}// end function


/**
 Chat bot vChat
*/
var vgc_chatbot = {
 send_quest : function(obj){
 var _cbi_id = $vnpJs(obj).data('id') || 0;
 var _quest = $vnpJs(obj).data('quest') || '';
 
 var data_bot = {
 quest : _quest,
 bot_id : _cbi_id
 };
 console.log(data_bot);
 send_request_require_chat(data_bot);
 
 $vnpJs('#vgc_chatbot_cbi').val(0);
 $vnpJs('#vgc_chatbot_cbi_opt').val(0);
 },
 
 chose_opt : function(obj){
 var _myans = $vnpJs(obj).data('ans') || '';
 var _quest = $vnpJs(obj).data('quest') || '';
 var _cbi_id = $vnpJs(obj).data('id') || 0;
 var _index = $vnpJs(obj).data('index') || 0;
 
 var data_bot = {
 quest : _quest,
 bot_id : _cbi_id,
 bot_id_opt : _index
 };
 
 send_request_require_chat(data_bot);
 
 /*
 if(_myans != ''){
 
 var _date = new Date();
 var vgc_time = _date.getTime();
 var vgc_to_id = $vnpJs('#vgc_to_id');
 var vgc_name = $vnpJs('#vgc_name');
 var vgc_send_id = $vnpJs('#vgc_send_id');
 var vgc_hash = $vnpJs('#vgc_hash');
 var vgc_count_chat = $vnpJs('#vatgiaClient_count_chat');
 var vgc_support_id = $vnpJs('#vgc_support_id').val() || 0;
 var vgc_link = document.location.href;
 var vgc_rand = Math.floor((Math.random() * 1000000) + 1);
 
 if(vgc_to_id.length > 0) vgc_to_id_val = vgc_to_id.val();
 if(vgc_name.length > 0) vgc_name_val = vgc_name.val();
 if(vgc_send_id.length > 0) vgc_send_id_val = vgc_send_id.val();
 if(vgc_hash.length > 0) vgc_hash_val = vgc_hash.val();
 if(vgc_count_chat.length > 0) vgc_count_chat_val = vgc_count_chat.val();
 
 var data = {};
 data.msg = _myans;
 data.id = vgc_to_id_val;
 data.name = vgc_name_val;
 data.send_id = vgc_send_id_val;
 data.hash = vgc_hash_val;
 data.owner = 'vgc_rowfriend';
 data.first = 0;
 data.vgc_rand = vgc_rand;
 data.temmsg = 0;
 data.vgc_time = vgc_time;

 vgchatClientAppendMsgToBoxchat(data);
 
 
 }
 */
 $vnpJs('#vgc_chatbot_cbi').val(0);
 $vnpJs('#vgc_chatbot_cbi_opt').val(0);
 }
}


/** 
 Vatgia Chat API
*/
var $vchat = {
 api : {
 /* set name customer*/
 set_name : function(myname){
 if(myname != ''){
 
 client_info_Data = {};
 client_info_Data.name = myname;
 var elm_guest_id = $vnpJs('#vgc_send_id').val();
 var vgc_hash = $vnpJs('#vgc_hash').val();
 var to_id = $vnpJs('#vgc_to_id').val();
 client_info_Data.guest_id = elm_guest_id; 
 client_info_Data.hash = vgc_hash;
 client_info_Data.to_id = to_id;
 $vnpJs.ajax({
 url: url_server_vgchat_client + 'ajax_client_info_add.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: client_info_Data,
 error: function(err) {},
 success: function(res) {
 //elm_load.addClass('vgc_hide');
 if(res.status == 1){
 vgc_close_guest_info();
 }else{
 if(res.error == ''){
 vgc_close_guest_info();
 }else{
 //elm_error.html(res.error);
 }
 }
 }
 });
 }
 
 },
 
 /* set email customer */
 set_email : function(myemail){
 if(myemail != ''){
 client_info_Data = {};
 client_info_Data.email = myemail;
 var elm_guest_id = $vnpJs('#vgc_send_id').val();
 var vgc_hash = $vnpJs('#vgc_hash').val();
 var to_id = $vnpJs('#vgc_to_id').val();
 client_info_Data.guest_id = elm_guest_id; 
 client_info_Data.hash = vgc_hash;
 client_info_Data.to_id = to_id;
 $vnpJs.ajax({
 url: url_server_vgchat_client + 'ajax_client_info_add.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: client_info_Data,
 error: function(err) {},
 success: function(res) {
 //elm_load.addClass('vgc_hide');
 if(res.status == 1){
 vgc_close_guest_info();
 }else{
 if(res.error == ''){
 vgc_close_guest_info();
 }else{
 //elm_error.html(res.error);
 }
 }
 }
 });
 }
 },
 
 /* set phone customer */
 set_phone : function(myphone){
 if(myphone != ''){
 client_info_Data = {};
 client_info_Data.phone = myphone;
 var elm_guest_id = $vnpJs('#vgc_send_id').val();
 var vgc_hash = $vnpJs('#vgc_hash').val();
 var to_id = $vnpJs('#vgc_to_id').val();
 client_info_Data.guest_id = elm_guest_id; 
 client_info_Data.hash = vgc_hash;
 client_info_Data.to_id = to_id;
 $vnpJs.ajax({
 url: url_server_vgchat_client + 'ajax_client_info_add.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: client_info_Data,
 error: function(err) {},
 success: function(res) {
 //elm_load.addClass('vgc_hide');
 if(res.status == 1){
 vgc_close_guest_info();
 }else{
 if(res.error == ''){
 vgc_close_guest_info();
 }else{
 //elm_error.html(res.error);
 }
 }
 }
 });
 }
 },
 
 /* set address customer */
 set_address : function(myadd){
 if(myadd != ''){
 client_info_Data = {};
 client_info_Data.address = myadd;
 var elm_guest_id = $vnpJs('#vgc_send_id').val();
 var vgc_hash = $vnpJs('#vgc_hash').val();
 var to_id = $vnpJs('#vgc_to_id').val();
 client_info_Data.guest_id = elm_guest_id; 
 client_info_Data.hash = vgc_hash;
 client_info_Data.to_id = to_id;
 $vnpJs.ajax({
 url: url_server_vgchat_client + 'ajax_client_info_add.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: client_info_Data,
 error: function(err) {},
 success: function(res) {
 //elm_load.addClass('vgc_hide');
 if(res.status == 1){
 vgc_close_guest_info();
 }else{
 if(res.error == ''){
 vgc_close_guest_info();
 }else{
 //elm_error.html(res.error);
 }
 }
 }
 });
 }
 },
 
 /* set message customer */
 say : function(mes){
 if(mes.trim != ''){
 
 if($vnpJs('#panel_body_vgchat').length){
 
 // show boxchat
 $vchat.api.show_box();
 
 var _timecheck = 0;
 vc_is_me_send = 1;
 var vchat_say = setInterval(function(){
 _timecheck += 100;
 if($vnpJs('#vgc_message').length){
 $vnpJs('#vgc_message').val(mes);
 vgchatClientSend('', 'submit');
 
 clearInterval(vchat_say);
 }
 if(_timecheck > 10000){
 clearInterval(vchat_say);
 }
 
 }, 100);
 
 
 }
 }
 },
 
 /* auto show box */
 show_box : function(){
 
 vgc_close_guest_info();
 
 var data_show_box = {div_id : "panel_body_vgchat", div_toggle : "show", require_info : 0};
 vgchatClientToggleDiv(data_show_box);
 }
 },
 
 /* setting */
 setting : {
 
 
 
 } 
};


/**
 Hàm show boxchat mời chat
*/
var _vcclient = {

 /* hàm close greeting chat khi click vào nút x ở phần mời chat */
 close_greeting : function(){
 $vnpJs('.vgc_greeting_chat').remove();
 _vcclient.eyechat_show();
 },
 /* trigger show boxchat client khi click vào text mời chat*/
 trigger_show_boxchat : function(){
 vgchatClientToggleDiv({div_id : 'panel_body_vgchat', div_toggle : 'show'});
 },
 /* hàm hiển thị boxchat ngay sau khi load xong nếu có tin nhắn offline qua lại giữa 2 người */
 find_show_boxchat_client : function(){
 var _st = setInterval(function(){
 if($vnpJs('#vgc_bcl_bottom').length){
 _vcclient.trigger_show_boxchat();
 clearInterval(_st);
 }
 }, 1000);
 },
 /* hàm set auto_reply */
 set_answer_auto_reply : function(){
 if(vc_auto_reply == 0) return;
 if(_vcclient_config.autoreply.auto_reply == 1){
 var autoreply_step = parseInt(vgc_getCookie('autoreply_step')) || 1;
 if(autoreply_step > 2) return;
 var data_msg = {
 owner : 'vgc_rowfriend',
 msg : ((autoreply_step == 2)? _vcclient_config.autoreply.auto_reply_second : _vcclient_config.autoreply.auto_reply_first),
 id : $vnpJs('#vgc_to_id').val(),
 };
 if(data_msg.msg != ''){
 vgchatClientAppendMsgToBoxchat(data_msg);
 }
 vgc_setCookie({name : 'autoreply_step', value : (autoreply_step + 1), expires : 1});
 return;
 }
 },
 /* Tự động trả lời khi chờ quá lâu */
 auto_rep_buzy : function(){
 if(auto_rep_buzy == 0) return false;
 var auto_rep_text = _vcclient_config.orther.auto_rep_buzy.text;
 if(auto_rep_text != ''){
 var data_msg = {
 owner : 'vgc_rowfriend',
 msg : auto_rep_text,
 id : $vnpJs('#vgc_to_id').val()
 };
 if(data_msg.msg != ''){
 set_auto_rep_buzy = setTimeout(function(){
 if(run_require_chat == 1){
 clearTimeout(setTime_require_chat);
 vc_is_me_send = 0;
 hide_button_require_chat();
 }
 vgchatClientAppendMsgToBoxchat(data_msg); 
 }, (auto_rep_buzy_time * 1000));
 
 }
 }
 }, 
 /* hàm hide boxchat */
 hide_box_chat : function(){
 vgchatClientToggleDiv({div_id : 'panel_body_vgchat', div_toggle : 'hide'});
 },
 /* function send location */
 send_location : function(){
 navigator.geolocation.getCurrentPosition(_vcclient.callback_get_location);
 },
 callback_get_location : function(position){
 lat = position.coords.latitude;
 lon = position.coords.longitude;
 var msg = 'http://maps.google.com/maps?q='+ lat +',' + lon +'&&key=AIzaSyAVVSRboF_rJnKbMLoDlm6XUOmtuV6QpE4';
 $vnpJs('#vgc_message').val(msg);
 var event = {shiftKey : false, keyCode : 0};
 vgchatClientSend(event, 'submit');
 },

 /* hàm add event */
 addEvent : function(obj){
 var event_guest_id = $vnpJs('#vgc_send_id').val() || 0;
 var event_estore_id = $vnpJs('#vgc_to_id').val() || 0;
 var event_hash = $vnpJs('#vgc_hash').val() || '';
 obj.event_guest_id = event_guest_id;
 obj.event_estore_id = event_estore_id;
 obj.event_hash = event_hash;

 $vnpJs.ajax({
 url: url_server_vgchat_client + 'vchat_sdk_api.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: obj,
 error: function(err) {},
 success: function(res) {

 }
 });
 },

 /* Hàm bật eyechat sau bao nhiêu giây và tắt sau bao nhiêu giây : chs_setting_eyechat */
 show_eyechat : function(obj){
 //console.log('eye chat');
 var _time_show = obj.time_show || 4;
 var _time_close = obj.time_close || 10;

 if(_time_show > 2) _time_show = _time_show - 2;
 
 var timeout = _time_show * 1000;
 timeout = 10; /* 14/06/2016 fix theo logic mới */
 
 var sinterval = setInterval(function(){
 var elm = $vnpJs('#vgbc_ichelp');
 if(elm.length > 0){
 
 clearInterval(sinterval);
 
 var _greeting_time = _vcclient_config.greeting.invite_showbox.time || 5;
 
 /* sau _time_show giây sẽ hiển thị */
 setTimeout(function(){
 elm.removeClass('vgc_hide');
 
 /* sau _time_close giây sẽ đóng lại */
 setTimeout(function(){
 /*elm.remove();*/
 /*_vcclient.greeting_new_visitor(_greeting_time);*/
 /* tạm bỏ đi vì chưa có quảng cáo */
 /*vatgiaClient_show_advertive();*/
 _vcclient.greeting_showboxchat(_greeting_time);
 }, (_greeting_time * 1000));
 
 }, timeout);
 }
 
 /* sau 10s vẫn không tìm thấy thì bỏ đi */
 setTimeout(function(){
 if(elm.length <= 0){
 var _greeting_time = _vcclient_config.greeting.invite_showbox.time || 1;
 clearInterval(sinterval);
 /*vatgiaClient_show_advertive();*/
 _vcclient.greeting_showboxchat(_greeting_time);
 }
 }, 10000);
 }, 200);
 
 },
 /* hàm mời khách chat sau 1 khoảng thời gian đã đặt trước : invites_time */
 greeting_invites : function(_time){
 //console.log('invite');
 if(_time <= 0) _time = _vcclient_config.greeting.invite_times.time || 2;
 var vgc_body = $vnpJs('#panel_body_vgchat');
 var greeting_check = _vcclient_config.greeting.invite_times.vl || 0;
 if(greeting_check == 1){
 setTimeout(function(){
 var greeting_text = _vcclient_config.greeting.invite_times.first_msg;
 $vnpJs('#vgc_greeting_text').html(greeting_text);
 $vnpJs('.vgc_greeting_chat').removeClass('vgc_hide');
 }, (_time * 1000));
 }else{
 var _greeting_time = _vcclient_config.greeting.after3page.time || 2;
 _vcclient.greeting_after3page(_greeting_time);
 }

 /*
 var vgc_cookie_show = parseInt($vnpJs.cookie('vgc_showboxafter'));
 if(vgc_cookie_show < 10 && greeting_check == 1){
 if(vgc_body.css('display') !== 'block'){
 setTimeout(function(){
 var data_show_box = {div_id : "panel_body_vgchat", div_toggle : "show", require_info : 0};
 vgchatClientToggleDiv(data_show_box);
 $vnpJs.cookie('vgc_showboxafter', (vgc_cookie_show + 1), {expires: 3600});
 }, (_time * 1000));
 }
 }
 */
 },
 hide_eyechat : function(){
 var elm = $vnpJs('#vgbc_ichelp');
 if(elm.length > 0){
 elm.addClass('vgc_hide');
 }
 },
 eyechat_show: function(){
 var elm = $vnpJs('#vgbc_ichelp');
 if(elm.length > 0){
 elm.removeClass('vgc_hide');
 }
 },

 /* Hàm bật boxchat mới chat đối với khách hàng */
 greeting_showboxchat : function(_time){
 
 if(_time <= 0) _time = _vcclient_config.greeting.invite_showbox.time || 2;
 var greeting_check = _vcclient_config.greeting.invite_showbox.vl || 0;
 var greeting_check_mobile = _vcclient_config.greeting.invite_showbox.vl_mobile || 0;
 /* kiểm tra cookie xem đã bật lên bao nhiêu lần hoặc đang chat hay không, bật < 4 lần thì đc bật hoặc đang chat cũng đc bật */
 var _auto_showbox = parseInt(vgc_getCookie('auto_showbox')) || 0;
 /* nếu vừa chat xong thì cho bật lên luôn */
 if(isset(typeof _vcclient_showboxchat) && _vcclient_showboxchat == 1){
 _vcclient.trigger_show_boxchat();
 return false;
 }

 /* check xem có quyền show boxchat không (đây là check quyền theo gói) */
 if(isset(typeof vgc_js_permission) && isset(typeof vgc_js_permission.show_boxchat)){
 if(vgc_js_permission.show_boxchat == 0){
 /* nếu không phải khách mới thì check xem có mời khách quay lại không */
 var _greeting_time = _vcclient_config.greeting.new_visit.time || 2;
 _vcclient.greeting_new_visitor(_greeting_time);
 return false;
 }
 }

 /*check: _vcclient_showboxchat (tin nhắn cuối cùng cách mấy phút để bật lên luôn ) => web_client_box*/
 if((greeting_check == 1 && _auto_showbox < 3)){
 setTimeout(function(){
 var first_msg = _vcclient_config.greeting.invite_showbox.first_msg || '';
 var msg_option = _vcclient_config.greeting.invite_showbox.option_msg || {};
 
 var option_msg = '<div>';  
  option_msg += '<a href="'+ msg_option.url +'" target="_blank"><img src="'+ msg_option.img +'" /></a>';
  option_msg += '<p><a target="_blank" class="vgc_ris_link" href="'+ msg_option.url +'">'+ msg_option.title +'</a></p>';
  option_msg += '<p class="vgc_ris_sapo">'+ msg_option.sapo +'</p>';
 option_msg += '</div>';
 var full_msg = '';
 if(_vcclient_config.greeting.invite_showbox.type_msg == 1){
  full_msg = first_msg;
 }else{
  full_msg = option_msg;
 }
 
 var data_msg = {
 owner : 'vgc_rowfriend',
 msg : full_msg,
 id : $vnpJs('#vgc_to_id').val(),
 showboxchat : 1
 };
 var data_show_box = {div_id : "panel_body_vgchat", div_toggle : "show", require_info : 0, 'data_msg' : data_msg};
 vgchatClientToggleDiv(data_show_box);
 vgc_setCookie({name : 'auto_showbox', value : (_auto_showbox + 1), expires : 60, type : 'm'});
 
 /* call auto open boxchat */
 vChatEvent.autoOpen();
 
 }, (_time * 1000));
 }else{
 /* nếu không phải khách mới thì check xem có mời khách quay lại không */
 var _greeting_time = _vcclient_config.greeting.new_visit.time || 1;
 _vcclient.greeting_new_visitor(_greeting_time);
 }

 },
 
 greeting_showboxchat_mobile : function(_time){
 
 if(_time <= 0) _time = _vcclient_config.greeting.invite_showbox.time || 2;
 var greeting_check_mobile = _vcclient_config.greeting.invite_showbox.vl_mobile || 0;
 /* kiểm tra cookie xem đã bật lên bao nhiêu lần hoặc đang chat hay không, bật < 4 lần thì đc bật hoặc đang chat cũng đc bật */
 var _auto_showbox = parseInt(vgc_getCookie('auto_showbox')) || 0;
 /* nếu vừa chat xong thì cho bật lên luôn */
 if(isset(typeof _vcclient_showboxchat) && _vcclient_showboxchat == 1){
 _vcclient.trigger_show_boxchat();
 return false;
 }

 /* check xem có quyền show boxchat không (đây là check quyền theo gói) */
 if(isset(typeof vgc_js_permission) && isset(typeof vgc_js_permission.show_boxchat)){
 if(vgc_js_permission.show_boxchat == 0){
 /* nếu không phải khách mới thì check xem có mời khách quay lại không */
 var _greeting_time = _vcclient_config.greeting.new_visit.time || 2;
 _vcclient.greeting_new_visitor(_greeting_time);
 return false;
 }
 }

 /*check: _vcclient_showboxchat (tin nhắn cuối cùng cách mấy phút để bật lên luôn ) => web_client_box*/
 if((greeting_check_mobile == 1 && _auto_showbox < 3)){
 setTimeout(function(){
 var first_msg = _vcclient_config.greeting.invite_showbox.first_msg || ''; 
 var msg_option = _vcclient_config.greeting.invite_showbox.option_msg || {};
 
 var option_msg = '<div>';  
  option_msg += '<a href="'+ msg_option.url +'" target="_blank"><img src="'+ msg_option.img +'" /></a>';
  option_msg += '<p><a target="_blank" class="vgc_ris_link" href="'+ msg_option.url +'">'+ msg_option.title +'</a></p>';
  option_msg += '<p class="vgc_ris_sapo">'+ msg_option.sapo +'</p>';
 option_msg += '</div>';
 var full_msg = '';
 if(_vcclient_config.greeting.invite_showbox.type_msg == 1){
  full_msg = first_msg;
 }else{
  full_msg = option_msg;
 }
 var data_msg = {
 owner : 'vgc_rowfriend',
 msg : full_msg,
 id : $vnpJs('#vgc_to_id').val(),
 showboxchat : 1
 };
 var data_show_box = {div_id : "panel_body_vgchat", div_toggle : "show", require_info : 0, 'data_msg' : data_msg};
 vgchatClientToggleDiv(data_show_box);
 vgc_setCookie({name : 'auto_showbox', value : (_auto_showbox + 1), expires : 60, type : 'm'});

 }, (_time * 1000));
 }else{
 /* nếu không phải khách mới thì check xem có mời khách quay lại không */
 var _greeting_time = _vcclient_config.greeting.new_visit.time || 1;
 _vcclient.greeting_new_visitor(_greeting_time);
 }

 },
 
 /* Hàm bật mời chat đối với khách mới : new visitor*/
 greeting_new_visitor : function(_time){
 //console.log('new visitor');
 /* kiểm tra xem có phải là khách mới không, nếu đúng là khách mới thì làm */
 if(_time <= 0) _time = _vcclient_config.greeting.new_visit.time || 2;
 var check_new_visitor = parseInt(vgc_getCookie('vgc_new_visitor')) || 0;
 var greeting_check = _vcclient_config.greeting.new_visit.vl || 0;
 if(check_new_visitor == 0 && greeting_check == 1){
 setTimeout(function(){
 
 /* ẩn eyechat */
 _vcclient.hide_eyechat();
  
 var greeting_text = _vcclient_config.greeting.new_visit.first_msg;
 $vnpJs('#vgc_greeting_text').html(greeting_text);
 $vnpJs('.vgc_greeting_chat').removeClass('vgc_hide');
 vgc_setCookie({name : 'vgc_new_visitor', value : 1, expires : 10});
 }, (_time * 1000));
 }else{
 /* nếu không phải khách mới thì check xem có mời khách quay lại không */
 var _greeting_time = _vcclient_config.greeting.return_visit.time || 2;
 _vcclient.greeting_return_visitor(_greeting_time);
 }
 },
 /* Hàm bật mời chat với khách quay lại : return_visitor*/
 greeting_return_visitor : function(_time){
 //console.log('return visit');
 if(_time <= 0) _time = _vcclient_config.greeting.return_visit.time || 2;
 var check_new_visitor = parseInt(vgc_getCookie('vgc_new_visitor')) || 0;
 var greeting_check = _vcclient_config.greeting.return_visit.vl || 0;
 if(check_new_visitor > 0 && greeting_check == 1){
 setTimeout(function(){
 
  /* ẩn eyechat */
  _vcclient.hide_eyechat();
  
 var greeting_text = _vcclient_config.greeting.return_visit.first_msg;
 $vnpJs('#vgc_greeting_text').html(greeting_text);
 $vnpJs('.vgc_greeting_chat').removeClass('vgc_hide');
 vgc_setCookie({name : 'vgc_new_visitor', value : (check_new_visitor + 1), expires : 30});

 /* sau 5s sẽ hiển thị nếu có cài đặt mời chat sau 3 page */
 setTimeout(function(){
 _vcclient.greeting_after3page(0);
 }, 10000);
 }, (_time * 1000));
 }else{
 var _greeting_time = _vcclient_config.greeting.invite_times.time || 2;
 _vcclient.greeting_invites(_greeting_time);
 }
 },
 /* Hàm bật mời chat với khách truy cập quá 3 page : after3page*/
 greeting_after3page : function(_time){
 //console.log('3page');
 if(_time <= 0) _time = _vcclient_config.greeting.after3page.time || 2;
 var greeting_check = _vcclient_config.greeting.after3page.vl || 0;
 if(greeting_check == 1){
 var check_page = parseInt(vgc_getCookie('vgc_page_number')) || 0;
 if(check_page > 3){
 setTimeout(function(){
 /* ẩn eyechat */
  _vcclient.hide_eyechat();
 var greeting_text = _vcclient_config.greeting.after3page.first_msg;
 $vnpJs('#vgc_greeting_text').html(greeting_text);
 $vnpJs('.vgc_greeting_chat').removeClass('vgc_hide');
  
  var _greeting_time = _vcclient_config.greeting.onpage_special.time || 2;
  setTimeout(function(){
 _vcclient.greeting_onpage_special(_greeting_time);
  }, (_greeting_time * 1000));
 
 }, (_time * 1000));
 }else{
 var _greeting_time = _vcclient_config.greeting.onpage_special.time || 2;
 _vcclient.greeting_onpage_special(_greeting_time);
 }
 }else{
 var _greeting_time = _vcclient_config.greeting.onpage_special.time || 2;
 _vcclient.greeting_onpage_special(_greeting_time);
 }
 },
 /* Hàm bật mời chat với khách trên trang chỉ định : onpage_special */
 greeting_onpage_special : function(_time){
 //console.log('special');
 if(_time <= 0) _time = _vcclient_config.greeting.onpage_special.time || 2;
 var greeting_check = _vcclient_config.greeting.onpage_special.vl || 0;
 if(greeting_check == 1){
 var url_page = window.location.pathname || '';
 var container_text = _vcclient_config.greeting.onpage_special.contain_text || "";
 if(container_text != ''){
 
 if(url_page.indexOf(container_text) > 0){
 setTimeout(function(){
 /* ẩn eyechat */
 _vcclient.hide_eyechat();
 
 var greeting_text = _vcclient_config.greeting.onpage_special.first_msg;
 $vnpJs('#vgc_greeting_text').html(greeting_text);
 $vnpJs('.vgc_greeting_chat').removeClass('vgc_hide');
 }, (_time * 1000));
 }
 }

 } // end if(greeting_check == 1)
 }, // end function
 /* hàm gọi nội dung chat, bật boxchat, send khi người dùng nhập vào ô greetting chat */
 greeting_chat_send : function(obj, event, send){
 var _msg = $vnpJs(obj).val().trim() || '';
 if((!event.shiftKey && event.keyCode==13) || send == 'submit'){
 vgchatClientStopEvent(event);
 vgchatClientToggleDiv({div_id : 'panel_body_vgchat', div_toggle : 'toggle', 'msg' : _msg, 'event' : event});
 }
 }
}

/* function upload image on boxchat */
function vgc_send_file_img(obj, data_post){
 var box_id = data_post.id || 0;
 var form = document.getElementById('vgc_box_frm_img');
 var fileSelect = document.getElementById('vgc_upload_img');
 var files = fileSelect.files;
 var formData = new FormData($vnpJs(obj).parent()[0]);
 var file = files[0];
 var filename = '';
 if(typeof file.name != 'undefined'){
 filename = file.name;
 }
 formData.append('picture', file, filename);
 var xhr = new XMLHttpRequest();
 xhr.open('POST', '//vchat.vn/service/upload_image.php', true);
 xhr.onload = function (return_data) {
 if (xhr.status === 200) {
 var return_data = JSON.parse(xhr.responseText);
 if(return_data.error != ''){
 alert(return_data.error);
 return false;
 }
 if(return_data.data != ''){
 $vnpJs('#vgc_message').val(return_data.data);
 document.getElementById('vgc_box_btn').click();
 }
 } else {
 alert('Gặp lỗi trong quá trình truyền file!');
 return false;
 }
 };

 xhr.send(formData);
 return false;
}

/* Hàm xóa ad vatgia và lưu cookie */
function vgc_close_advg(){
 vgc_setCookie({name : 'vgc_close_advg', value : 1, expires : 10});
 $vnpJs('#vgc_html_avg').remove();
}

function vgc_vc_mousedown(e){
 if(e){
 position_downx = e.screenX;
 position_downy = e.screenY;
 }else{
 position_downx = window.event.screenX;
 position_downy = window.event.screenY;
 }
 /*
 position_downx = e.pageX;
 position_downy = e.pageY;
 */
 elm_template_chat = $vnpJs('.template_vgchat');
 box_old_x = elm_template_chat.offset().left;
 box_old_y = elm_template_chat.offset().top - $vnpJs(window).scrollTop();
 vgc_box_w = elm_template_chat.width();
 vgc_box_h = elm_template_chat.height();
 is_moveboxchat = 1;

 $vnpJs('.template_vgchat .panel_head_vgchat .vgc_title .vgc_title_top').css({"cursor": "-webkit-grabbing","cursor": "-moz-grabbing","cursor": "-o-grabbing",'cursor':'grabbing'});
}
function vgc_vc_mousemove(e){
 if(is_moveboxchat == 1){
 /* vị trí mới */
 if(e){
 box_new_x = e.screenX;
 box_new_y = e.screenY;
 }else{
 box_new_x = window.event.screenX;
 box_new_y = window.event.screenY;
 }

 /* khoảng cách giữa vị trí cũ và vị trí mới */
 space_x = position_downx - box_new_x;
 space_y = position_downy - box_new_y;

 /* vị trí di chuyển đến */
 newx = (box_old_x - space_x);
 newy = (box_old_y - space_y);
 /* nếu left <=0, top <=0, right > screen - w, bottom > screen - h */
 if(newx <= 0) newx = 0;
 if(newy <= 0) newy = 0;
 if((vgc_box_w + newx) >= window.innerWidth) newx = window.innerWidth - vgc_box_w;
 if((vgc_box_h + newy) >= window.innerHeight) newy = window.innerHeight - vgc_box_h;

 elm_template_chat.css({
 'right':'auto',
 'bottom':'auto',
 'left': newx +'px',
 'top': newy +'px'
 })
 $vnpJs('#panel_loading_vgchat *').addClass('vgc_no_hilight');
 }
}

function vgc_vc_mouseup(){
 is_moveboxchat = 0;
 $vnpJs('#panel_loading_vgchat *').removeClass('vgc_no_hilight');
 $vnpJs('#vgcPosition_l').val(newx);
 $vnpJs('#vgcPosition_t').val(newy);
 $vnpJs('.template_vgchat .panel_head_vgchat .vgc_title .vgc_title_top').css({"cursor": "-webkit-grab","cursor": "-moz-grab","cursor": "-o-grab",'cursor':'grab'});
}

/* hàm start chat để biết rằng bắt đầu cuộc chat trở lại */
function vgc_start_chat(){
 if($vnpJs('#vgc_select_office').val() <= 0){
 $vnpJs('#vgc_client_info_error').text('Vui lòng chọn bộ phận hỗ trợ trước khi chat');
 return false;
 }

 var elm_load = $vnpJs('#vgc_client_info .poll_load');

 var data_post = {};
 data_post.send_id = $vnpJs('#vgc_send_id').val() || 0;
 data_post.to_id = $vnpJs('#vgc_to_id').val() || 0;
 data_post.hash = $vnpJs('#vgc_hash').val() || '';

 elm_load.removeClass('vgc_hide');
 $vnpJs.ajax({
 url: url_server_vgchat_client + 'ajax_start_chat.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: data_post,
 error: function(err) {},
 success: function(res) {
 if(res.status == 0){
 alert(res.error);
 }else{
 elm_load.addClass('vgc_hide');
 vgc_close_guest_info();
 }
 }
 });
}

/** hàm cập nhật kết thúc cuộc chat */
function vgc_end_chat(){
 /* set poll after */
 var data_post = {};
 data_post.send_id = $vnpJs('#vgc_send_id').val() || 0;
 data_post.to_id = $vnpJs('#vgc_to_id').val() || 0;
 data_post.hash = $vnpJs('#vgc_hash').val() || '';

 $vnpJs.ajax({
 url: url_server_vgchat_client + 'ajax_end_chat.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: data_post,
 error: function(err) {},
 success: function(res) {
 if(res.status == 0){
 alert(res.error);
 }else{
 /* thành công thì xóa ô nhập nội dung chat, set vgc_check_get_history = false, xóa history */
 $vnpJs('#panel_history_vgchat').html('');
 vgc_check_get_history = false;
 }
 }
 });
}

/* hàm thay đổi bộ phận hỗ trợ khi chọn bộ phận hỗ trợ */
function vgc_change_select_office(obj){
 var listSp = $vnpJs(obj).val();
 $vnpJs('#vgc_select_office').val(listSp);
 vgc_setCookie({name : 'vgc_select_office', value : listSp, expires : 10, type : 'm'});

 /* kiểm tra xem vgc_support_id có nằm trong vgc_select_office không
 nếu có thì thôi không set biến vgc_is_change_office = 1 nữa
 nếu không có thì set biến vgc_is_change_office = 1 để set lại biến support = 0 đẻ cho ai cũng trả lời được
 */
 var vgc_support_id = parseInt($vnpJs('#vgc_support_id').val()) || 0;
 if(vgc_support_id > 0 && listSp != ''){
 var arrayListOffice = listSp.split(',').map(Number) || new Array;
 if(arrayListOffice.indexOf(vgc_support_id) == -1){
 $vnpJs('#vgc_is_change_office').val(1);
 }else{
 $vnpJs('#vgc_is_change_office').val(0);
 }
 }

}

/*
 hàm check thông tin thẻ meta viewport trên page có không
*/
function vgc_fun_check_mobile_viewport(){
 if(vgc_check_mobile_viewport){
 clearInterval(check_viewport_interval);
 $vnpJs('meta[name=viewport]').attr('content', vgc_viewport_default);
 return;
 }

 if($vnpJs('#panel_chat_vatgia').length){
 $vnpJs("#panel_chat_vatgia").css({"transform":"scaleX(3) scaleY(3)", "position":"fixed", "bottom":"0px", "right":"30%", "width":"100%", "max-width":"300px"});
 clearInterval(check_viewport_interval);
 }
}

/*
 hàm get cookie
 name : tên cookie
*/
function vgc_getCookie(name){
 var value = "; " + document.cookie;
 var parts = value.split("; " + name + "=");
 if (parts.length == 2) return parts.pop().split(";").shift();
}


/*
 hàm set cookie
 obj
 name : tên cookie
 value : giá trị cookie
 expires : thời gian hết hạn
 type : kiểu thời gian (m: phút, d: ngày)
*/
function vgc_setCookie(obj){
 var d = new Date();
 var _name = obj.name || '';
 var _value = obj.value || '';
 var _expire = obj.expires || '';
 var _type = obj.type || 'd';
 var _time = 0;
 if(_type == 'm'){
 _time = (_expire*60*1000);
 }else{
 _time = (_expire*24*60*60*1000);
 }
 d.setTime(d.getTime() +_time );
 var expires = "expires="+d.toUTCString();
 document.cookie = _name + "=" + _value + "; " + expires +';path=/';
}


/*
 login with facebook
*/
function social_login(p){
 var url_provider = '';
 if(p != 'fb' && p != 'gg') return false;
 var s_id = $vnpJs('#vgc_send_id').val() || 0;

 url_provider = 'http://vchat.vn/service/login_social_idvatgia.php?p='+p+'&s_id='+s_id;
 window.open(url_provider,'',"toolbar=no, scrollbars=no, resizable=no, top=100, left=350, width=650, height=400");
}

/* function rechat */
function vgc_rechat(){
 vatgiaClient_remove_polls();
 vgc_check_get_history = false;
 vgchatClientToggleDiv({div_id : 'panel_body_vgchat', div_toggle : 'toggle'});
}

function vgc_update_info(obj){
 alert('Chức năng này không được cho phép tại đây.');
}

function togle_emoji(){
 $vnpJs('#emoji_list').toggle();
}

function set_emoji(obj){
 var _key = $vnpJs(obj).data('key') || '';
 if(_key != ''){
 $vnpJs('#vgc_message').val(_key);
 vgchatClientSend('', 'submit');
 togle_emoji();
 }
}

function vgc_change_country(obj){
 var _id = parseInt($vnpJs(obj).val()) || 0;
 var _to_id = parseInt($vnpJs('#vgc_to_id').val()) || 0;
 console.log(_id);
 
 if(_id > 0 && _to_id > 0){
 $vnpJs.ajax({
 url: '//live.vnpgroup.net/js/ajax_get_department.php',
 method: 'POST',
 type: 'json',
 crossOrigin: true,
 data: {
 id : _id,
 to_id : _to_id
 },
 error: function(err) {},
 success : function(data){
 if(data.status == 1){
 $vnpJs('#vgc_department').html(data.html);
 }
 }
 });
 }
}

/* ctrl + v vào ô input */
function retrieveImageFromClipboardAsBase64(pasteEvent, callback, imageFormat){
 if(pasteEvent.clipboardData == false){
 if(typeof(callback) == "function"){
 callback(undefined);
 }
 };

 var items = pasteEvent.clipboardData.items;

 if(items == undefined){
 if(typeof(callback) == "function"){
 callback(undefined);
 }
 };

 for (var i = 0; i < items.length; i++) {
 // Skip content if not image
 if (items[i].type.indexOf("image") == -1) continue;
 // Retrieve image on clipboard as blob
 var blob = items[i].getAsFile();

 // Create an abstract canvas and get context
 var mycanvas = document.createElement("canvas");
 var ctx = mycanvas.getContext('2d');
 
 // Create an image
 var img = new Image();

 // Once the image loads, render the img on the canvas
 img.onload = function(){
 // Update dimensions of the canvas with the dimensions of the image
 mycanvas.width = this.width;
 mycanvas.height = this.height;

 // Draw the image
 ctx.drawImage(img, 0, 0);

 // Execute callback with the base64 URI of the image
 if(typeof(callback) == "function"){
 callback(mycanvas.toDataURL(
 (imageFormat || "image/png")
 ));
 }
 };

 // Crossbrowser support for URL
 var URLObj = window.URL || window.webkitURL;

 // Creates a DOMString containing a URL representing the object given in the parameter
 // namely the original Blob
 img.src = URLObj.createObjectURL(blob);
 }
}

window.addEventListener("paste", function(thePasteEvent){ 
 retrieveImageFromClipboardAsBase64(thePasteEvent, function(imageDataBase64){
 if(imageDataBase64){
 var elm = document.getElementById('vgc_message'); 
 if(elm == document.activeElement){
 var to_id = $vnpJs('#vgc_to_id').val();
 
 var dataPost = {
 send_id: $vnpJs('#vgc_send_id').val(),
 to_id: to_id,
 hash: $vnpJs('#vgc_hash').val(),
 baseimage: imageDataBase64
 };
 
 $vnpJs.ajax({
 url: '//vchat.vn/service/upload_pasteimage.php',
 method: 'post',
 type: 'json',
 crossOrigin: true,
 data: dataPost,
 error: function(err) {},
 success: function(res) {
 
 if(res.url != ''){
 $vnpJs('#vgc_message').val(res.url);
 document.getElementById('vgc_box_btn').click();
 }
 }
 }); 
 }
 }
 });
}, false);

function getIp(){
 var _ip = $vnpJs('#vgc_ip').val() || '';
 if(_ip != ''){
 $vnpJs.ajax({
 url: '//freegeoip.net/json/'+_ip,
 method: 'GET',
 type: 'json',
 crossOrigin: true,
 data: {},
 error: function(err) {},
 success : function(data){
 if(typeof data.region_name != 'undefined'){
 $vnpJs('#vgc_address').val(data.region_name);
 }
 }
 });
 } 
}

/*
 settimeout 2s gọi check code
*/

var checkcodetime = setInterval(function(){
 if(typeof url_server_vgchat_client != 'undefined' && typeof url_server_vgchat_client != undefined){
 var xmlHttp = new XMLHttpRequest();
 var dm=document.location.hostname;
 var ref = document.referrer;
 if(document.getElementById('vgc_to_id') != null){
 
 var _ref = '';
 var _cuid = 0;
 if(typeof vgc_webowner_status != 'undefined'){
 if(vgc_webowner_status == 1){
 _ref = '&ref='+ref;
 }
 }
 
 if(typeof vgc_cuid != 'undefined'){
 _cuid = vgc_cuid;
 }
 
 var wid=parseInt(document.getElementById('vgc_to_id').value) || web_vchat_id; 
 xmlHttp.open( "GET", url_server_vgchat_client +'web_code.php?cuid='+_cuid+'&w='+dm+'&s='+wid+_ref, false ); // false for synchronous request
 xmlHttp.send( null );
 
 clearInterval(checkcodetime);
 } 
 }
}, 1000);

setTimeout(function(){
 clearInterval(checkcodetime);
}, 10000);

/* 
 Event Google Analytic 
 ga('send', 'event', 'vChat_category', 'action', 'label');
*/
var vChatEvent = {
 autoOpen : function(){
 
 /* bắn lên google */
 if(typeof ga === 'function'){
 console.log('Event ga: AutoOpen');
 ga('send', 'event', 'Open Chat', 'AutoOpen', 'Cài đặt tự bật boxchat');
 }
 
 /* bắn lên gtag */
 if(typeof gtag === 'function'){
 console.log('Event Gtag: AutoOpen');
 gtag('event', 'AutoOpen', {
 'event_category': 'Open Chat',
 'event_label': 'Cài đặt tự bật boxchat',
 'value': ''
 });
 }
 
 /* bắn lên facebook */
 if(typeof fbq === 'function'){
 console.log('Event fb: AutoOpen');
 fbq('trackCustom', 'Open Chat', {action: 'AutoOpen', label : 'Cài đặt tự bật boxchat',});
 }
 },
 
 userOpen : function(){
 
 /* bắn lên google */
 if(typeof ga === 'function'){
 console.log('Event ga: UserOpen');
 ga('send', 'event', 'Open Chat', 'UserOpen', 'User click bật boxchat');
 }
 
 /* bắn lên gtag */
 if(typeof gtag === 'function'){
 console.log('Event Gtag: UserOpen');
 gtag('event', 'UserOpen', {
 'event_category': 'Open Chat',
 'event_label': 'User click bật boxchat',
 'value': ''
 });
 }
 
 
 /* check bắn lên server của google ads qua gtag gọi hàm của bên website */
 if(typeof gtag_report_convertion_openchat === 'function'){
 gtag_report_convertion_openchat();
 }
 
 /* bắn lên facebook */
 if(typeof fbq === 'function'){
 console.log('Event fb: UserOpen');
 fbq('trackCustom', 'Open Chat', {action: 'UserOpen', label : 'User click bật boxchat',});
 }
 },
 
 sendChat : function(){
 
 /* bắn lên google */
 if(typeof ga === 'function'){
 console.log('Event ga: SendChat');
 ga('send', 'event', 'vChat Send', 'SendChat', 'Khách gửi tin chat');
 }
 
 /* bắn lên gtag */
 if(typeof gtag === 'function'){
 console.log('Event Gtag: SendChat');
 gtag('event', 'SendChat', {
 'event_category': 'vChat Send',
 'event_label': 'Khách gửi tin chat',
 'value': ''
 });
 }
 
 /* check bắn lên server của google ads qua gtag gọi hàm của bên website */
 if(typeof gtag_report_convertion_sendchat === 'function'){
 gtag_report_convertion_sendchat();
 }
 
 /* bắn lên facebook */
 if(typeof fbq === 'function'){
 console.log('Event fb: vChat Send');
 fbq('trackCustom', 'vChat Send', {action: 'SendChat', label : 'Khách gửi tin chat',});
 }
 },
 
 sendContact : function(){
 
 /* bắn lên google */
 if(typeof ga === 'function'){
 console.log('Event ga: SendContact');
 ga('send', 'event', 'vChat Send', 'SendContact', 'Khách nhập thông tin cá nhân');
 }
 
 /* bắn lên gtag */
 if(typeof gtag === 'function'){
 console.log('Event Gtag: SendContact');
 gtag('event', 'SendContact', {
 'event_category': 'vChat Send',
 'event_label': 'Khách nhập thông tin cá nhân',
 'value': ''
 });
 }
 
 /* check bắn lên server của google ads qua gtag gọi hàm của bên website */
 if(typeof gtag_report_convertion_sendcontact === 'function'){
 gtag_report_convertion_sendcontact();
 }
 
 /* bắn lên facebook */
 if(typeof fbq === 'function'){
 console.log('Event fb: vChat Send');
 fbq('trackCustom', 'vChat Send', {action: 'SendContact', label : 'Khách nhập thông tin cá nhân',});
 }
 },
 
 sendMessage : function(){
 
 /* bắn lên google */
 if(typeof ga === 'function'){
 console.log('Event ga: SendMessage');
 ga('send', 'event', 'vChat Send', 'SendMessage', 'Khách gửi tin nhắn Offline');
 }
 
 /* bắn lên gtag */
 if(typeof gtag === 'function'){
 console.log('Event Gtag: SendMessage');
 gtag('event', 'SendMessage', {
 'event_category': 'vChat Send',
 'event_label': 'Khách gửi tin nhắn Offline',
 'value': ''
 });
 }
 
 /* bắn lên facebook */
 if(typeof fbq === 'function'){
 console.log('Event fb: vChat Send');
 fbq('trackCustom', 'vChat Send', {action: 'SendMessage', label : 'Khách gửi tin nhắn Offline',});
 }
 }
}

/* DINH BONG */
/*
! function(f, b, e, v, n, t, s) {
 if (f.vgq) return;
 n = f.vgq = function() {
 n.callMethod ?
 n.callMethod.apply(n, arguments) : n.queue.push(arguments)
 };
 f.vgDomain = '//track.vatgia.com';
 if (!f._vgq) f._vgq = n;
 n.push = n;
 n.loaded = !0;
 n.version = '1.0';
 n.queue = [];
 t = b.createElement(e);
 t.async = !0;
 t.src = v;
 s = b.getElementsByTagName(e)[0];
 s.parentNode.insertBefore(t, s)
}(window, document, 'script', '//track.vatgia.com/static/v1/event.min.js');

vgq('init', '1');
vgq('track', "PageView");
*/var url_server_vgchat_client = "//live.vnpgroup.net/js/";var vgc_js_permission = {customer_online:1,history_chat:1,history_search:1,message_offline:1,tags:1,label:1,office:1,permission:1,email_offline:1,report:1,report_office:1,app_desktop:1,app_mobile:1,tranfer:1,baned:1,show_boxchat:1,max_chat:0,answer_auto:1,payment:1,orther_setting:1,greet_setting:1,customer_send_location:1,customer_send_image:1,customer_poll:1,show_office:1,seting_target:1,};var web_vchat_id = 2628978;_vcclient_config = {size: {w:250,h:250},themes: {box_color:'#982584',box_position:'right',box_call:0,template:2,link_like:'https://www.facebook.com/banlinhkienMH/'},button: {pc : {type:'larger',icon:1},mobile : {type:'larger',icon:1}},greeting: {invite_showbox : {vl:0,vl_mobile:0,time:10,first_msg:'Chào mừng các bạn đến với Banlinhkien.vn, Hãy cùng Banlinhkien.vn gửi tới những người thân yêu bộ Light Love Noel vô cùng ý nghĩa.',name:'Bật boxchat online kèm câu chat tự động chào khách hàng',type_msg:1,option_msg : {img:'',url:'',title:'',sapo:''}},invite_showbox_offline : {vl_pc:0,vl_mobile:0,name:'Bật boxchat offline tăng tương tác khi bạn không online',time:1},invite_times : {vl:0,time:10,first_msg:'Chào bạn! Tôi có thể giúp gì cho bạn',name:'Mời chat sau một khoảng thời gian nhất định'},return_visit : {vl:0,time:5,first_msg:'Chào mừng bạn đã quay trở lại Banlinhkien.vn, bạn có cần tôi giúp gì không?',name:'Mời chat đối với khách quay lại'},new_visit : {vl:0,time:5,first_msg:'Chào mừng bạn đã đến với Banlinhkien.vn, bạn có cần tôi giúp gì không?',name:'Mời chat đối với khách mới'},after3page : {vl:0,time:10,first_msg:'Chào bạn, bạn có cần tôi giúp gì không?',name:'Mời chat sau khi đã xem quá 03 trang'},onpage_special : {vl:0,time:10,first_msg:'<a href="https://goo.gl/BQNyEJ" target="_blank">https://goo.gl/BQNyEJ</a>',contain_text:'https://goo.gl/BQNyE',name:'Mời chat trên đường dẫn chỉ định'}},eyechat: {icon_eye_chat:62,show_eye_chat:1,time_show_eye_chat:4,time_close_eye_chat:20},text: {title_online : {name:'Hỗ Trợ Khách Hàng Và Kỹ Thuật',holder:'Tiêu đề khi bạn online',maxlen:50},company_name : {name:'Hỗ Trợ Tư Vấn',holder:'Tên hỗ trợ',maxlen:40},conpany_phone : {name:'Tổng Đài Hỗ Trợ: 1900.03.44',holder:'Số điện thoại hỗ trợ',maxlen:40},company_address : {name:'Email: support@minhhagroup.com',holder:'Địa chỉ công ty',maxlen:60},sugget_chat : {name:'Gõ vào đây và nhấn <enter> để chat',holder:'Nội dung hướng dẫn khách hàng nhập vào ô chat',maxlen:80},title_msg_offline : {name:'',holder:'Tiêu đề gửi tin nhắn khi bạn không online',maxlen:50},msg_default : {name:'Câu chào mặc định khi chat với khách',holder:'Câu chào mặc định khi chat với khách',maxlen:100},title_mobile_online : {name:'Chat',holder:'Tiêu đề khi bạn online trên bản mobile',maxlen:100},title_mobile_offline : {name:'Gửi tin nhắn',holder:'Tiêu đề khi bạn offline trên bản mobile',maxlen:100}},autoreply: {auto_reply:1,auto_reply_first:'Chào QK Banlinhkien.vn giúp gì được QK ạ!',auto_reply_second:''},payment: {logo : {img:'',logo_link:''},advertive : {title:'',link:''}},orther: {from_info : {vl:0,name:'Nhập thông tin cá nhân trước khi chat',note:'Khi sử dụng tính năng này, khách hàng phải điền các thông tin cá nhân trước khi chat ( Họ tên, Email, Số điện thoại, Địa chỉ).',question : {ques:'',note:'Một câu hỏi mở để biết thêm thông tin về khách hàng ví dụ: Bạn đang có nhu cầu gì?',ans_option:0,ans_list:''},field : {title:'Vui lòng cho chúng tôi biết thông tin cá nhân để chúng tôi hỗ trợ bạn tốt hơn',s_name:'Array',s_email:'Array',s_phone:'Array',s_address:'Array'}},contact_in_chat : {vl:1,name:'Yêu cầu Khách hàng nhập thông tin trong cuộc chat',note:'Khi sử dụng tính năng này sau câu chat đầu tiên của khách hàng nếu khách hàng chưa có thông tin liên hệ thì vchat sẽ tự động hiển thị form nhập thông tin để mời khách hàng nhập thông tin vào.',field : {c_name:'Array',c_email:'Array',c_phone:'Array',c_address:'Array',title:'Vui lòng cho chúng tôi biết thông tin dưới đây để hỗ trợ được bạn tốt hơn'},thank:'Cảm ơn bạn đã cung cấp thông tin cho chúng tôi.',question : {note:'Một câu hỏi mở để biết thêm thông tin về khách hàng ví dụ: Bạn đang có nhu cầu gì?',ques:'',ans_option:0,ans_list:''}},show_office : {vl:0,name:'Chọn bộ phận hỗ trợ trước khi chat',note:'Khi sử dụng tính năng này, khách hàng phải lựa chọn Bộ phận hỗ trợ phù hợp trước khi chat.  Để tính năng này hoạt động, bạn cần tạo trước ít nhất 2 bộ phận trong mục Bộ phận hỗ trợ.',country:0},send_file : {vl:1,name:'Cho phép khách hàng gửi ảnh và file khi chat',note:'Khi sử dụng tính năng này, khách hàng của bạn có thể gửi ảnh và file qua cửa chat khi chat. Các định dạng file được vChat.vn hỗ trợ bao gồm: .jpg,.jpeg,.gif,.png,.doc,.docx,.xls,.xlsx,.pdf,.txt'},poll : {vl:0,name:'Yêu cầu đánh giá sau chat',note:'Khi sử dụng tính năng này, khách hàng của bạn có thể đánh giá chất lượng cuộc chat ngay khi kết thúc chat.'},require_chat : {vl:0,time:55,name:'Cho phép khách hàng yêu cầu chat khi chờ quá lâu',note:'Với chức năng này, khi khách hàng không được hỗ trợ viên trả lời lại sau một khoảng thời gian cài đặt nhất định, họ sẽ nhận được một nút bấm Yêu cầu chat để thông báo cho hỗ trợ viên.',text:'Chào bạn! Hiện tại tôi đang cần được hỗ trợ'},auto_rep_buzy : {vl:0,time:60,name:'Tự động trả lời khách hàng khi khách đợi quá lâu',note:'Với chức năng này, khi hách hàng không được hỗ trợ viên trả lời sau một khoảng thời gian cài đặt nhất định, hệ thống tự động trả lời khách hàng với cú pháp kèm theo.',text:'Chào bạn! Hiện tại tôi đang không có ở đây hoặc tôi đang rất bận, bạn vui lòng gọi vào số 04.66.85.11.66 để được tư vấn trực tiếp. Cảm ơn bạn!'},rep_when_cancel : {vl:1,name:'Trả lời lại khách khi bạn click vào nút Cancel khi khách yêu cầu trả lời',note:'Với chức năng này, hệ thống sẽ tự động trả lời khách hàng khi hỗ trợ viên nhấn vào nút Cancel khi khách hàng gửi Yêu cầu chat.',text:'Chào bạn! Hiện tại tôi đang không có ở đây hoặc tôi đang rất bận, bạn vui lòng gọi vào số 04.66.85.11.66 để được tư vấn trực tiếp. Cảm ơn bạn!'}},omnichanel: {vl:0,name:'Bật tính năng cài đặt đa kênh cho tài khoản',type:1,field : {chat : {show:1,value:'',text:'Chat trực tiếp cho tôi',holder:'vChat'},email : {show:0,value:'',text:'Gửi email cho tôi',holder:'Email nhận của bạn'},hotline : {show:0,value:'',text:'Gọi trực tiếp cho tôi',holder:'Số hotline của bạn'},facebook : {show:0,value:'',text:'Nhắn tin với tôi qua facebook',holder:'ID page facebook'}}},chs_lang_id: {lang:'vi'},text_en: {title_online : {name:'Chat with us!',holder:'Chat with us!',maxlen:50},company_name : {name:'Support',holder:'Company name',maxlen:40},conpany_phone : {name:'',holder:'company phone',maxlen:40},company_address : {name:'',holder:'Company address',maxlen:60},sugget_chat : {name:'Type your message here',holder:'Type your message here',maxlen:80},title_msg_offline : {name:'Send us a message!',holder:'Title when you are offline.',maxlen:50},msg_default : {name:'',holder:'Câu chào mặc định khi chat với khách',maxlen:100},title_mobile_online : {name:'Chat',holder:'Title online on Mobile',maxlen:100},title_mobile_offline : {name:'Send message',holder:'Title offline on Mobile',maxlen:100}}};setTimeout("call_start_function_client(0)", 1000);var vc_lang = {"change_avatar":"Thay \u1ea3nh \u0111\u1ea1i di\u1ec7n","title_show":"Tr\u1ea1ng th\u00e1i","title_online":"Ti\u00eau \u0111\u1ec1 khi b\u1ea1n online","title_msg_offline":"Vui l\u00f2ng \u0111\u1ec3 l\u1ea1i l\u1eddi nh\u1eafn","company_name":"T\u00ean h\u1ed7 tr\u1ee3","conpany_phone":"S\u1ed1 \u0111i\u1ec7n tho\u1ea1i h\u1ed7 tr\u1ee3","company_address":"\u0110\u1ecba ch\u1ec9 c\u00f4ng ty","sugget_chat":"H\u01b0\u1edbng d\u1eabn kh\u00e1ch h\u00e0ng g\u1eedi tin nh\u1eafn","msg_default":"C\u00e2u ch\u00e0o m\u1eb7c \u0111\u1ecbnh khi chat v\u1edbi kh\u00e1ch","setting":"C\u00e0i \u0111\u1eb7t","info_title":"Ch\u1ecdn b\u1ed9 ph\u1eadn online \u0111\u1ec3 c\u00f3 th\u1ec3 h\u1ed7 tr\u1ee3 b\u1ea1n","choose_office":"Ch\u1ecdn b\u1ed9 ph\u1eadn","s_name":"H\u1ecd t\u00ean","s_email":"Email","s_phone":"S\u1ed1 \u0111i\u1ec7n tho\u1ea1i","s_address":"\u0110\u1ecba ch\u1ec9","info_loginwith":"\u0110\u0103ng nh\u1eadp v\u1edbi","btn_savechat":"L\u01b0u & B\u1eaft \u0111\u1ea7u chat","btn_ignore":"B\u1ecf qua","poll_title":"B\u1ea1n c\u00f3 mu\u1ed1n k\u1ebft th\u00fac cu\u1ed9c tr\u00f2 chuy\u00ean n\u00e0y kh\u00f4ng?","poll_btn_yes":"\u0110\u00e1nh gi\u00e1","poll_btn_exit":"Tho\u00e1t","offline_title_phone":"\u0110T","offline_name":"T\u00ean c\u1ee7a b\u1ea1n","offline_email":"Email","offline_phone":"S\u1ed1 \u0111i\u1ec7n tho\u1ea1i","offline_mes":"Tin nh\u1eafn (kh\u00f4ng qu\u00e1 255 k\u00fd t\u1ef1)","offline_title_mobile":"G\u1eedi tin","offline_btn":"G\u1eedi tin nh\u1eafn","btn_save":"L\u01b0u c\u00e0i \u0111\u1eb7t","language":"Ng\u00f4n ng\u1eef","mini_box":"Thu nh\u1ecf khung chat","exit_chat":"D\u1eebng chat","send_location":"G\u1eedi v\u1ecb tr\u00ed c\u1ee7a b\u1ea1n cho ch\u1ee7 web","send_photo":"G\u1eedi \u1ea3nh minh h\u1ecda cho ch\u1ee7 web","error_office":"B\u1ea1n ch\u01b0a ch\u1ecdn b\u1ed9 ph\u1eadn h\u1ed7 tr\u1ee3","error_name":"B\u1ea1n ch\u01b0a nh\u1eadp t\u00ean","error_email":"B\u1ea1n ch\u01b0a nh\u1eadp email","error_phone":"B\u1ea1n ch\u01b0a nh\u1eadp s\u1ed1 \u0111i\u1ec7n tho\u1ea1i","error_mes":"B\u1ea1n ch\u01b0a nh\u1eadp n\u1ed9i dung tin nh\u1eafn","error_address":"B\u1ea1n ch\u01b0a nh\u1eadp \u0111\u1ecba ch\u1ec9","greeting_close":"T\u1eaft m\u1eddi chat","greeting_send":"G\u1eedi tin nh\u1eafn","greeting_holder":"Nh\u1eadp n\u1ed9i dung v\u00e0 nh\u1ea5n <Enter>","require_chat":"B\u1ea1n \u0111\u1ee3i \u0111\u00e3 kh\u00e1 l\u00e2u, b\u1ea1n c\u00f3 mu\u1ed1n y\u00eau c\u1ea7u tr\u1ea3 l\u1eddi?","require_send":"Y\u00eau c\u1ea7u h\u1ed7 tr\u1ee3 ngay","require_ignore":"B\u1ecf qua","send_offline":"C\u1ea3m \u01a1n b\u1ea1n \u0111\u00e3 g\u1eedi tin nh\u1eafn cho ch\u00fang t\u00f4i. Ch\u00fang t\u00f4i s\u1ebd li\u00ean h\u1ec7 v\u1edbi b\u1ea1n trong th\u1eddi gian s\u1edbm nh\u1ea5t.","send_offline_over":"Xin l\u1ed7i v\u1ec1 s\u1ef1 b\u1ea5t ti\u1ec7n n\u00e0y! B\u1ea1n ch\u1ec9 \u0111\u01b0\u1ee3c g\u1eedi tin nh\u1eafn \u0111\u1ebfn cho kh\u00e1ch h\u00e0ng 03 l\u1ea7n trong ng\u00e0y.","offline_text":"Th\u00f4ng tin khi b\u1ea1n offline","offline_note":"V\u00ed d\u1ee5 c\u1ee7a t\u00f4i","c_name":"H\u1ecd t\u00ean","c_email":"Email","c_phone":"S\u1ed1 \u0111i\u1ec7n tho\u1ea1i","c_address":"\u0110\u1ecba ch\u1ec9","offline_greet":"C\u00e2u m\u1eddi kh\u00e1ch \u0111i\u1ec1n th\u00f4ng tin","offline_list_field":"C\u00e1c tr\u01b0\u1eddng y\u00eau c\u1ea7u nh\u1eadp"};var vgc_webowner_status = 0;var vgc_cuid = 1305049145;var vgc_client_box_data = "send_id=1305049145&to_id=2628978&hash=ddbbedfcf21b51cccc08eb29960e6573&nocache=0";
$vnpJs('head').append('<style>/* * Template */.vgc_clear {clear: both;}.vgc_fl, .vgc_left {float: left;}.vgc_fr, .vgc_right {float: right;}.vgc_hide {display: none !important;}.vgc_show {/*display: block !important;*/}.vgc_show_inline {display: inline-block !important;}.vgc_block{display:block !important}.vgc_no_hilight{-webkit-touch-callout: none;-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}.vgc_marquee { margin: 0 auto; overflow: hidden; white-space: nowrap; box-sizing: border-box; animation: marquee 25s linear infinite;}.vgc_marquee:hover { animation-play-state: paused}/* Make it move */@keyframes marquee { 0% { text-indent: 100px } 100% { text-indent: -300px }}/* * Sprites */#vgc_require_chat{ width: 100%; position: absolute; bottom: 70px; left: 0; text-align: center; padding: 20px 0; box-sizing: border-box; background-color: #fff; line-height: 25px;}#vgc_require_chat span{ background-color: #ddd; color: #222; padding: 5px 10px; border-radius: 3px; cursor: pointer; text-transform: uppercase; line-height: 20px; display: inline-block;}#vgc_require_chat span:hover{ background-color: #0072BD; color: #fff;}#vgc_client_banned{border: medium none;box-sizing: border-box;color: #f00;font-family: arial;font-size: 12px;font-style: normal;line-height: 20px;padding: 20px 5%;text-align: center;width: 90%;}#panel_chat_vatgia{ z-index: 999999999999 !important;}#panel_chat_vatgia .vgc_icon_sprites, .vgc_ic { background-image: url(data:image/png;base64,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);}#panel_chat_vatgia .vgc_icon_plus {background-position: -54px -17px;display: inline-block;float: left;height: 17px;margin: 6px 0 0 3px;vertical-align: text-bottom;width: 24px;}#panel_chat_vatgia .vgc_icon_shrink {border-radius: 1px;height: 20px;position: absolute;right: 23px;top: 5px;width: 18px;background-position: -18px 6px;}#panel_chat_vatgia .vgc_client_close_polls{background-position: -111px 1px;border-radius: 1px;height: 16px;position: absolute;right: 0px;top: 6px;width: 16px;}#panel_chat_vatgia .vgc_icon_sound { position: absolute; width: 12px; height: 13px; background-position: -106px -17px; top: 5px; right: 22px;}#panel_chat_vatgia .vgc_icon_sound:hover{ opacity:0.5; transition-duration:0.5s;}#panel_chat_vatgia .vgc_icon_off_sound{ position:absolute; width:17px; height:17px; background-position:-118px -17px; top:3px; right:22px;}#panel_chat_vatgia .vgc_icon_actions{ position:absolute; top:5px; opacity:0.55;}#panel_chat_vatgia .vgc_icon_actions:hover{ opacity:1; transition-duration:1s;}#panel_chat_vatgia .vgc_icon_close{ width:10px; height:10px; background-position:-44px -18px; right:5px;}#panel_chat_vatgia .vgc_icon_edit{ width:12px; height:12px; background-position:-32px -18px; right:25px;}.botania_private{ width: auto !important; padding: 0 10px 0 40px !important;}.botania_private img{ left: 10px !important;}/* * Template */#panel_chat_vatgia .template_vgchat { font-family: Arial; font-size: 12px;background: #078AE8;box-shadow: 0 0 1px #fff inset;border-radius: 6px 6px 0 0;width: auto;position: fixed;bottom: -1px; z-index: 2147483646; /*_position: absolute;_top:expression(eval(document.documentElement.scrollTop+document.documentElement.clientHeight-this.offsetHeight));_left:expression(eval(document.documentElement.scrollLeft+document.documentElement.clientWidth-this.offsetWidth))-100; */}#panel_chat_vatgia .tem_v1 {border-radius: 6px 6px 0 0 !important;}#panel_chat_vatgia .tem_v2 { box-shadow: 0 5px 15px rgba(0,0,0,0.3); border-radius: 0;}#panel_chat_vatgia .tem_v4{ border-radius: 10px 10px 0 0;}#panel_chat_vatgia .vgbc_ichelp{height: auto;right: 0px;position: absolute;bottom: 30px;display: block;}#panel_chat_vatgia .vgbc_closehelp{position: absolute;right: 0;cursor: pointer;padding: 2px 5px;top: -18px;font-size: 14px;z-index: 99999;}#panel_chat_vatgia .vgc_text_white { color: #fff; float: left; margin-top: 0; font-size: 20px !important; height: 20px; line-height: 22px !important;}#panel_chat_vatgia .vgc_btn{ padding: 3px 10px; border-radius: 3px; cursor: pointer;}#panel_chat_vatgia .vgc_btn_primary{ background: #078AE8; color: #FFF;}#panel_chat_vatgia .vgc_form_control{ width: 95%;padding: 5px 3px;font-size: 12.5px;color: #555555;vertical-align: middle;background-color: #ffffff;border: 1px solid #cccccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);-webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;}#panel_chat_vatgia .vgc_form_control:focus {border-color: #999;outline: 0;}/* Container */#panel_chat_vatgia .panel_container_vgchat {padding:0px 5px;position: relative;}#panel_chat_vatgia .tem_vg_v2{padding:0px !important;position: relative;}#panel_chat_vatgia .tem_vg_v4{}/* Head */#panel_chat_vatgia .panel_head_vgchat {color: #FFF;font-size: 13px;font-weight: bold;cursor: pointer;position: relative;height: 30px;}#panel_chat_vatgia .tem_head_vc_v2{padding: 0 5px;}#panel_chat_vatgia .tem_head_vc_v4{padding: 0 8px;}#panel_chat_vatgia .panel_head_vgchat .vgc_title {color: #FFFFFF;min-width: 80px;overflow: hidden;text-overflow: ellipsis;text-align: left;height: 30px;white-space: nowrap;line-height: normal !important;position: relative;}#panel_chat_vatgia .panel_head_vgchat .vgc_title .vgc_bg_logo{border-radius: 3px 0 0;float: left;height: 18px;left: -5px;overflow: hidden;padding: 6px 5px;position: absolute;top: 0;width: 24px;}#panel_chat_vatgia .panel_head_vgchat .vgc_title .vgc_title_top{cursor: -webkit-grab;cursor: -moz-grab;cursor: -o-grab; overflow: hidden; text-overflow: ellipsis; font-family: tahoma; white-space: nowrap; font-size: 14px !important; display: block; max-width: 230px; margin-right: 50px; line-height: 30px; font-weight: normal}#panel_chat_vatgia .panel_head_vgchat .vgc_title .vgc_title_bottom{ cursor: pointer; display: block; font-family: tahoma; font-size: 14px !important; line-height: 30px; margin-left: 35px; max-width: 300px; overflow: hidden; padding-right: 5px; text-align: left; text-overflow: ellipsis; white-space: nowrap; font-weight: normal;}#panel_chat_vatgia .panel_head_vgchat .vgc_count_message{background-color: #ff7800; border: 1px #ff6600 solid; padding: 1px 4px; border-radius: 3px; font-size: 12px; font-style: normal; color: #FFF; font-weight: bold; position: absolute; top: -8px;right: 10px; animation:tungtung 0.5s; -moz-animation:tungtung 0.5s infinite; -webkit-animation:tungtung 0.5s infinite;}@-moz-keyframes tungtung /* Firefox */{ 0% {margin-top:0px;} 50% {margin-top:2px;} 100% {margin-top:0px;}}@-webkit-keyframes tungtung /* Firefox */{ 0% {margin-top:0px;} 50% {margin-top:2px;} 100% {margin-top:0px;}}/* Body */#panel_chat_vatgia .panel_body_vgchat {margin: 0;width: 300px;}/* Content */#panel_chat_vatgia .panel_body_vgchat .panel_content_vgchat {border: 1px solid #777;border-radius: 5px;box-shadow: 0 0 1px #fff;margin: 0px;max-height: 415px;padding: 0;}#panel_chat_vatgia .panel_body_vgchat .tem_ctvc_v2{border: none;border-radius: 0;}#panel_chat_vatgia .panel_body_vgchat .tem_ctvc_v4{border: none;border-radius: 0;}/* Content info */#panel_chat_vatgia .panel_content_vgchat .panel_info_vgchat {background-color: #fff;position: absolute;padding: 6px 5px 5px 5px;line-height: 16px;width: 100%;box-sizing: border-box;}#panel_chat_vatgia .panel_info_vgchat .vgc_info_support_avatar { border: solid 2px transparent; border-radius: 100%; margin-right: 6px; overflow: hidden; height: 50px; width: 50px;}.tem_vg_v4 .panel_info_vgchat{ height: 65px !important;}.vgc_button_call{ position: absolute; right: 5px; top: 25px; display: block; width: 30px; height: 30px; overflow: hidden; float: right; background-color: #0AAD26 !important; text-align: center; line-height: 30px; border-radius: 100%;}.vgc_button_call img{ width: 16px; position: absolute; left: 7px; top: 7px; }.tem_vg_v4 .vgc_info_support_avatar{ border: solid 2px transparent !important; margin-right: 10px; padding: 0px !important; overflow: hidden; height: 50px; border-radius: 100% !important; width: 50px;}#panel_chat_vatgia .panel_info_vgchat .vgc_info_support { text-align: left; overflow: hidden; padding-top: 2px;}#panel_chat_vatgia .panel_info_vgchat .vgc_info_support_avatar img { width: 100%; height: 100%;}#panel_chat_vatgia .panel_info_vgchat .vgc_info_support_name {color: #333;font-size: 13px;font-weight: bold;height: 16px;line-height: 16px;margin-bottom: 3px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}#panel_chat_vatgia .panel_info_vgchat .vgc_info_support_address { font-size: 11px; color: #777; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 85%;}/* Content history */#panel_chat_vatgia .panel_content_vgchat .panel_history_vgchat {background-color: #fff; border-top: 1px solid #eee; border-bottom: 1px solid #eee; height: auto;max-height: 300px; height: 300px;overflow: auto; margin: 0 0 0px 0; padding: 0px 0 30px 0; box-sizing: border-box;}.tem_vg_v4 .panel_history_vgchat{ border: none !important;}#panel_chat_vatgia .panel_content_vgchat .panel_history_vgchat .vgc_line_time_today{background-color: #f2f2f2;border-radius: 10px;box-sizing: content-box;color: #ad9f9c;font-family: arial;font-size: 10px;font-style: normal;font-weight: normal;margin: 5px auto;overflow: hidden;padding: 2px 5px;text-align: center;width: 130px;}/* Row */#panel_chat_vatgia .panel_history_vgchat .vgc_row{ position: relative;overflow: hidden;padding: 5px 0;margin: 0 5px;}#panel_chat_vatgia .panel_history_vgchat .vgc_row .vgc_temmsg{opacity: 0.2;}.vgc_ris_link{ display: block; text-align: left; color: #4C8BF5 !important; font-size: 14px; line-height: 19px; font-weight: bold;}.vgc_ris_sapo{ color: #666; text-align: left !important;}/* Row message */#panel_chat_vatgia .panel_history_vgchat .vgc_row .vgc_msgchat{color: #222; position: relative; max-width: 80%; min-height: 16px; border-radius: 5px; white-space: pre-wrap; word-wrap: break-word;display: block;padding: 8px; line-height: 15px; font-size: 12px;}#panel_chat_vatgia .panel_history_vgchat .vgc_row .vgc_msgchat a{color: #0059AB;text-decoration: none;}#panel_chat_vatgia .panel_history_vgchat .vgc_row .vgc_msgchat img{ display: inline-block; max-width: 100%; vertical-align: middle;}#panel_chat_vatgia .panel_history_vgchat .vgc_row .vgc_msgchat p { text-align: center;}#panel_chat_vatgia .panel_history_vgchat .vgc_row .vgc_msgchat p a{ text-decoration: none;}#panel_chat_vatgia .panel_history_vgchat .vgc_row .vgc_msgchat p img{ max-width: 230px; margin: auto;}#panel_chat_vatgia .panel_history_vgchat .vgc_row .vgc_msgchat p span { border-top: 1px dashed #999; color: #666666; display: block; font-weight: bold; padding-top: 5px;}.vgc_update_info{ background-color: #4C8CF5; color: #fff; border-radius: 4px; display: inline-block; line-height: 18px; padding: 0 10px; cursor: pointer; text-shadow: none; margin-left: 10px;}/* Rowme message */#panel_chat_vatgia .panel_history_vgchat .vgc_rowme .vgc_msgchat{background-color: #CCE1FD;border-radius: 5px;float: right;}#panel_chat_vatgia .panel_history_vgchat .vgc_rowme .vgc_msg_time{ color: #999; float: right; font-size: 10px; font-style: normal; font-weight: normal; vertical-align: text-bottom; clear: both;}/* Rowfriend name */#panel_chat_vatgia .panel_history_vgchat .vgc_rowfriend .vgc_name{ position: absolute; top: 5px; left: 0; font-weight: bold;}#panel_chat_vatgia .panel_history_vgchat .vgc_rowfriend .vgc_msg_time{ color: #999; font-size: 10px; font-style: normal; font-weight: normal; margin-top: 9px; padding-left: 5px; vertical-align: text-bottom; display: block; clear: both; margin-left: 35px;}#panel_chat_vatgia .panel_history_vgchat .vgc_rowfriend .vgc_name img{width: 30px; height: 30px; padding: 1px; border-radius: 100%;}/* Rowfriend message */#panel_chat_vatgia .panel_history_vgchat .vgc_rowfriend .vgc_msgchat{ background-color: #F4F4F4; border-radius: 5px; color: #222; display: block; padding: 8px 10px; margin-left: 40px; float: left; text-align: left; word-wrap: break-word;}.botchat_suggest_text{ margin: 0 0 5px 0; text-align: left !important;}.botchat_opt{ background-color: #649BF7; margin-right: 5px; display: inline-block; margin-bottom: 5px; color: #fff; text-shadow: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; line-height: 15px !important;}.botchat_question { margin: 0 !important; border: none; text-align: left !important;}.botchat_question span{ border: none !important; text-align: left !important; display: inline !important; padding: 5px 0 !important; color: #4C8BF5 !important; cursor: pointer; font-weight: normal !important;}.botchat_question span:hover{ text-decoration: underline !important;}#panel_chat_vatgia .panel_history_vgchat .vgc_linesp{background: #eee;color: #999;font-size: 10px;margin: 4px 0;padding: 3px 0;text-align: center;}.vgc_msgsend{ color: #000 !important;}/* Control */#panel_chat_vatgia .panel_body_vgchat .panel_control_vgchat {background-color: #fff; margin: 0 !important;padding: 3px !important; height: 35px !important; -webkit-box-sizing: content-box; -moz-box-sizing: content-box; box-sizing: content-box; position: relative;}.tem_vg_v4 .panel_control_vgchat{ box-shadow: 0 0px 30px rgba(0,0,0,0.3);}#panel_chat_vatgia .panel_body_vgchat .panel_control_vgchat .vgc_send_img_icon{background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAQAAABKfvVzAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAAAmJLR0QAAKqNIzIAAAAJcEhZcwAADdcAAA3XAUIom3gAAAAHdElNRQfhAxAFCx4ZRWmLAAAByUlEQVQ4y43SP2gTYRjH8e/75hWHDoJTJqlVa6ouZlHU1mvuubN2P3R00sFNF7EFA2Jx6NCpaqEISq11EAUFNbEpqOBUC0Kpg5DWodXBgn+GUpPXIUnvQi/XPtO9L5/nx4+XUySO261ukOWXmlm/NrMCoJO4DKqinVMBV2zazLudyeFIXn673Run196HLbmcCs9ev1gn3bKS5LnK2eL78OZPiXXd0WJhM4e2AFOd31YZAMfIrEzFvlJcumN2TLL73+Xtpz+Rcl97Ij+zv7HmGJmqcT8jy56ro7xRRroq7zgNEKTMBMeN86rsZ6olnhWmY9LdQ7IioygIUvJYFv29tXS5g4rn3+O4dzfKBxq893DIvUlZko56+r0Iz52QNb8HwD8iP7bkeS0L7i0A2RNyeRThYxEOvQel4rcByG15XucT3rfcvliO1kf58uYvACcpYIPU6gPVU3Gmv/qZasm+KF7CRheMrthU/fshg97On/26M+RvLzZz0PoTB2QXwOp9dd32qZLJ1rh6uZkDKHfOHWm+8jOyLOPN3SOTy8qadyHyizhJXAG459Wo+shTu6Darcc5NVS4GVdmYwGctBngGF2UmWW4+JmW8x+Cu8Gs5QSzrQAAACV0RVh0ZGF0ZTpjcmVhdGUAMjAxNy0wMy0xNlQwNToxMTozMCswMTowMG2RIuoAAAAldEVYdGRhdGU6bW9kaWZ5ADIwMTctMDMtMTZUMDU6MTE6MzArMDE6MDAczJpWAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAABJRU5ErkJggg=="); }#panel_chat_vatgia .panel_body_vgchat .panel_control_vgchat .vgc_icon_file{background-color: #fff;background-position: 3px 2px;background-repeat: no-repeat;background-size: 16px auto;bottom: 1px;cursor: pointer;display: block;height: 18px;overflow: hidden;position: absolute;right: 0;width: 22px;}.panel_control_vgchat .vgc_send_location{background-color: #fff;background-position: -21px -40px;background-repeat: no-repeat;bottom: 22px;cursor: pointer;display: block;height: 18px;opacity: 0.56;overflow: hidden;position: absolute;right: 0;width: 22px;}.panel_control_vgchat .vgc_send_location:hover{opacity: 1;}#vgc_send_btn{ position: absolute; background-color: #4C8CF5; color: #fff; padding: 3px; border-radius: 5px 0 0 5px; right: 0; z-index: 999999999; height: 100%; line-height: 40px; top: 0px; width: 50px !important; text-align: center;} #panel_chat_vatgia .panel_body_vgchat .first_message{border: medium none;color: #444;font-family: arial;font-size: 12px;height: auto !important;line-height: 16px !important;padding: 10px !important;text-align: center;box-sizing: border-box !important;}#panel_chat_vatgia .panel_control_vgchat .vgc_message {background-color: #fff;box-sizing: border-box !important; width: 100%; height: 35px !important; border: none; margin: 0 !important; padding: 0 20px 0 0 !important; font: 13px Tahoma, Geneva, sans-serif;outline: none; resize: none; outline: none; color: #222; box-shadow: none;}/* Footer */#panel_chat_vatgia .panel_footer_vgchat{ padding: 2px 0px 4px; overflow: hidden;}#panel_chat_vatgia .panel_footer_vgchat .vgc_setting{float: left;}#panel_chat_vatgia .panel_footer_vgchat .vgc_setting a{font-weight: normal;text-decoration: none;}#panel_chat_vatgia .panel_footer_vgchat .vgc_bt_logovchat, #vgc_logo_msgoffline .vgclogovchat, .vgc_bt_logovchat{ background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADUAAAAUCAYAAAAtFnXjAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAPJSURBVHjaYvz//z/DcAMAAcSCRUwaiKOA2ACIWYEY5OsvQHwEiA8DsSAQ/wDix0D8YTB6CiCAGNFiSheIm4D4BhCvBOKHQMwExDpAHAzEQUAsDvUUSM0CIJ4PxN8Gk6cAAogB5CkoFgXiLUDsiySGjpOA+PN/VLAQiLnx6KE7BgggJiT/xQPxaSDejCcM5kFjBxnEAXHoYIoogABC9pQ3NMkRAruhyQ8ZOOHInwMCAAII2VPMQPyTCD2/gPgfmthXaIEyKABAACF7ahcQuxOhxwyIuZD474B4LRD/HSyeAggg5AwmD8TLgVgBTyZ0BeJ3aAXFZiBmwaJWCIhTgHgBEG8C4vlAHAXEbFB5HiDOA2JNahcUAAGEnA/eAvFzIBYFYjEgvgfEH6FywtDCAJTvbkLrrVPQ2DGG1m0PkcyyAuLZQKwOVQuq02yg9V8iFIOS60QgzgLi62TGCT8QK0KrF3g+BwggZE+B6h9zqGUgzAnE36F5jQ2ILwNxGBB/QjNYBC0vqgDxcmgSBXliC9QcbiBOA+JyILYE4gOwxEJBQrMF4o1ArA/EV2CCAAGE7ClQ62EpNH+8hHpGEhobz5HUcUE9CWtNvIHSYlB2MTS27YD4DJI+UIz1AfEGqPmiUPHP0IBRhYrfQ3M4KHC1oZ6/AHUnLzRwhaHlgipUH7gRABBA5KTZGCA+Ds0zMDFvIN4LxDpAfAeafwiZowjE34F4OxBfgubP+0CcA8SMUDX6QLwPiL9C8UogXgbEXUBcAMRvoPpeA/FEmNkAAUSOp1yhBsUiiW2FOgzkiH9AnEWEOXJA/AeInwBxMhDbAfF6IP4NxCZAzAHEV6EtmBqoZy9C7d4AxCpA3A7lg+Q1YGYDBBC5JcwBID4GZesC8XsgTgBidagluUSYoQBVW4UkpgKNvRRo7IM87Y8krwzEL6DNORDfBWqGCbLZAAHERGYGnQNtxYNKPTdo3twArbPuALErMY1ptDzJAM0Tb6B5Rhtaop5Ekn8IzaccUD6MZkc2GCCAyPXUWqgF7UAcCS2+P0AdtA6IfaHi2EAZEOchtUrQWzWM0MIBZL4UtMiGAR4gVoO2alCqW2QOQACR2177Dm3YdkDrsmQkw3ugRe18aAm4CupZNWijGeThKUD8B4sbGKF8ASDeCy3hZkMDDxRoKdCS7gZSIGBEDkAAUdIIBTk2B2o5cuX5GogToBVrELQi/gbtXIKK8SXQPhvIIfehlT4MgOq721AzQAFRCMS9QNwAxL+h+Bi0MmeAevoxtO0JBwABxDgcu/MAAQYAFnPrxCtWghwAAAAASUVORK5CYII=);background-size: 50px auto;display: block;float: right;height: 19px;width: 50px; background-repeat: no-repeat !important;}#panel_chat_vatgia .panel_footer_vgchat .vgc_bt_logoprivate{background-size: 50px auto;display: block;float: right;height: 19px;width: 50px;overflow: hidden;}#panel_chat_vatgia .panel_footer_vgchat .vgc_bt_logoprivate img{max-width: 100%;}#panel_chat_vatgia .panel_footer_vgchat .vgc_bt_logovchat:hover{opacity: 1;}/* Option */#panel_chat_vatgia .panel_footer_vgchat .vgc_setting .vgc_setting_option{ position: absolute; bottom: 28px; left: 1px; background-color: #FFF; width: 180px; border: 1px #cacaca solid; border-radius: 5px; padding: 5px 0 10px 0; box-shadow: 0px 0px 4px #AAA; z-index: 101;}#panel_chat_vatgia .panel_footer_vgchat .vgc_setting .vgc_setting_option:before { content: \'\'; position: absolute; width: 0px; height: 0px; bottom: -8px; z-index: 9; left: 24px; border-style: solid; border-width: 8px 7px 0 7px; border-color: #cacaca transparent transparent transparent;}#panel_chat_vatgia .panel_footer_vgchat .vgc_setting .vgc_setting_option:after { content: \'\'; position: absolute; width: 0px; height: 0px; bottom: -6px; z-index: 9; left: 25px; border-style: solid; border-width: 7px 6px 0 6px; border-color: #FFF transparent transparent transparent;}/* Option - Sound */#panel_chat_vatgia .panel_footer_vgchat .vgc_setting .vgc_setting_config_sound { position: relative; display: inline-block; width: 100%; border-bottom: 1px #cacaca solid; padding: 5px 0 8px 0; cursor: pointer; margin-bottom: 2px;}#panel_chat_vatgia .panel_footer_vgchat .vgc_setting .vgc_setting_config_sound .vgc_name_sound{ display:inline-block; color:#444; padding-left: 10px; cursor:pointer;}/* Option - Line */#panel_chat_vatgia .panel_footer_vgchat .vgc_setting .vgc_setting_config_line { position: relative; display: inline-block; margin-top: 8px; padding: 0 9px; color: #444;}#panel_chat_vatgia .panel_footer_vgchat .vgc_setting .vgc_setting_config_line a{ color:#444;}#panel_chat_vatgia .panel_footer_vgchat .vgc_setting .vgc_setting_config_line a:hover{ color:#0071af;}#panel_chat_vatgia .panel_footer_vgchat .vgc_setting .vgc_setting_config_line .vgc_btn{ margin: 2px; display: block; color:#FFF;}#panel_chat_vatgia .panel_footer_vgchat .vgc_setting .vgc_setting_config_line label{ color:#333; font-weight: bold; text-transform: uppercase;}#panel_chat_vatgia .panel_footer_vgchat #vgc_adv_client{margin: 2px 75px 0 50px;white-space: nowrap;font-size: 12px;line-height: normal !important;}#panel_chat_vatgia .panel_footer_vgchat #vgc_adv_client a{box-sizing: content-box;color: #fff;font-style: normal;font-weight: normal;padding: 0;display: inline-block !important;text-decoration: none; white-space: pre !important;}/* Option - Complete */#panel_chat_vatgia .panel_footer_vgchat .vgc_setting .vgc_setting_config_complete { position: relative; display: inline-block; width: 75%; white-space: pre-wrap; word-break: break-all; word-wrap: break-word; padding: 5px 18px;}/* Loading */#panel_chat_vatgia #vgc_loading{background-color: #fff; position:relative; top: 40%;}#panel_chat_vatgia #vgc_loading .vgc_ic_loading, #vgc_bc_off #vgc_loading .vgc_ic_loading, .send_file_loadding{ width: 66px; height: 66px; display: block; background-repeat: no-repeat; background-position: center CENTER; background-image: url(data:image/gif;base64,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);}#vgc_bc_off #vgc_loading{background-color: rgba(255, 255, 255, 0.7);height: 100%;position: absolute;top: 0;left:0;width: 100%;}#panel_loading_vgchat{border-radius: 3px;overflow: hidden;}.tem_ctvc_v2 #panel_loading_vgchat{border-radius: 0px;}.tem_ctvc_v4 #panel_loading_vgchat{border-radius: 0px; box-shadow: 0 0 20px rgba(0,0,0,0.2);}/* * Advertise */#vgc_ad_bottom{background-color: #078AE8;border: 1px solid #336FAA;border-bottom: none;bottom: 0;border-radius: 5px 5px 0 0;padding: 0 2px;overflow: hidden;position: fixed;font-family: arial;right: 30px;font-size: 12px;height: 80px;z-index: 9999;}#vgc_ad_bottom .vgc_ad_title{color: #FFFFFF;font-size: 13px;height: 18px;line-height: 14px;margin: 0;padding: 5px;position: relative;}#vgc_ad_bottom .vgc_ad_title .vgc_ad_close{background-position: 0 -104px;cursor: pointer;display: block;height: 16px;position: absolute;right: 0;top: 5px;width: 16px;}#vgc_ad_bottom #vgc_ad{background-color: #FFFFA4;bottom: 0;height: 50px;margin: 0;overflow: hidden;padding: 0;position: relative;width: 300px;}#vgc_ad_bottom #vgc_ad li{height: 45px;overflow: hidden;position: absolute;padding: 5px 0;width: 100%;background-color: #FFFFA4;}#vgc_ad_bottom #vgc_ad li .vgc_img{float: left;margin: 0;overflow: hidden;height: 40px;width: 40px;text-align: center;padding-left: 5px;}#vgc_ad_bottom #vgc_ad li .vgc_name{height: 40px;margin-left: 50px;overflow: hidden;}#vgc_ad_bottom #vgc_ad li .vgc_name .vgc_price{color: #E1292A;font-weight: bold;}#vgc_ad_bottom #vgc_ad li img{border: 1px solid #CCCCCC;height: 36px;padding: 1px;}#vgc_ad_bottom #vgc_ad li a{color: #045B9B;display: block;font-size: 13px;margin-bottom: 5px;text-decoration: none;text-overflow: ellipsis;white-space: nowrap;width: 100%;overflow: hidden;font-weight: bold;}#vgc_ad_bottom #vgc_ad .vgc_sl_off{top: 50px;}#vgc_ad_bottom #vgc_ad .vgc_sl_on{top: 0px;z-index: 100;}#vgc_bc_off{background-color: #078AE8;border-radius: 5px 5px 0 0 !important;box-shadow: 0 0 1px #fff inset;bottom: -1px;font-family: arial;font-size: 12px;max-width: 300px;min-width: 180px;padding: 4px 5px;position: fixed;z-index: 2147483647;}#vgc_bc_off .vgc_tt{ background-color: transparent;color: #FFFFFF;cursor: pointer;font-family: verdana !important;font-size: 12px !important;font-weight: bold !important;line-height: 14px;margin: 0;max-width: 300px;overflow: hidden;padding: 3px 5px 0;position: relative;text-overflow: ellipsis;white-space: nowrap;display: block; text-align: left; text-transform: none !important;height: auto;letter-spacing: normal;}#vgc_bc_off .vgc_ic_off{ background-color: transparent;background-position: -137px -17px;display: block;float: left;height: 17px;margin-right: 8px !important;margin-top: 1px;width: 19px;}#vgc_bc_off #vgc_off_close{background-color: #FFFFFF;border-radius: 3px;display: none;height: 4px;position: absolute;right: 0;top: 11px;width: 20px;}#vgc_bc_off #vgc_frm_off{position: relative !important;margin: 0;display: none;}#vgc_bc_off .vgc_off_row{background-color: #FFFFFF;border-radius: 5px 5px 0 0;display: none;list-style: none outside none;margin: 10px 0 0;overflow: hidden;padding: 10px;}#vgc_bc_off .vgc_off_row li{background: none;float: left;padding: 5px 0;width: 100%;list-style: none;margin: 0;}#vgc_bc_off .vgc_off_row li .vgc_name_title{color: #666;display: block;font-size: 12px;font-family: verdana;height: 17px;line-height: 16px;margin: 0;overflow: hidden;padding: 0;text-align: left;white-space: nowrap;}#vgc_bc_off .vgc_off_row li .vgc_polls_contact{border-radius: 3px;font-weight: bold;overflow: hidden;}#vgc_bc_off .vgc_off_row li .vgc_polls_contact p{color: #FFFFFF;font-family: verdana !important;font-size: 12px !important;line-height: 20px !important;margin: 0 !important;overflow: hidden;padding: 0 !important;text-overflow: ellipsis;white-space: nowrap;width: 100%;}#vgc_bc_off .vgc_control{border: 1px solid #dddddd;border-radius: 3px;box-sizing: content-box;font-family: arial;font-size: 13px;height: 15px;padding: 5px 2%;width: 96%;}#vgc_bc_off .vgc_control_msg{border: 1px solid #DDDDDD;border-radius: 3px;font-family: arial !important;font-size: 13px !important;min-height: 50px;padding: 5px 2%;width: 96%;box-sizing: content-box;}#vgc_bc_off .vgc_control_send{background-color: #078AE8;border: none;border-radius: 3px;color: #FFFFFF;cursor: pointer;display: block;font-family: verdana;font-size: 12px;font-weight: bold;margin: 0 auto;padding: 5px 40px;}#vgc_bc_off #vgc_er{color: #FF0000;font-size: 11px;}/* Polls */#polls_vgc{border-radius: 5px 5px 0 0;bottom: 0;font-family: arial;font-size: 12px;overflow: hidden;width: 100%;}#polls_vgc .polls_header{background-color: #0E76BC;color: #FFFFFF;padding: 5px 10px;overflow: hidden;position: relative;}#polls_vgc .polls_header h3{color: #FFFFFF;font-size: 15px;line-height: 23px;margin: 0;text-shadow: 0 1px 0 #0A4E7A;cursor: pointer;}#polls_vgc .polls_header #poll_mini{cursor: pointer;height: 13px;position: absolute;right: 30px;top: 11px;width: 20px;}#polls_vgc .polls_header .poll_minimize{background-position: -19px -55px;}#polls_vgc .polls_header .poll_maxnimize{background-position: -19px -44px;}#polls_vgc .polls_header .poll_close{background-position: -18px -67px;cursor: pointer;height: 17px;position: absolute;right: 5px;width: 16px;top: 7px;}#polls_vgc .polls_info{background-color: #FFFFFF;padding: 5px 0 0;}#polls_vgc .polls_info h3{color: #0E76BC;font-size: 12px;margin: 5px 10px;font-weight: bold;line-height: normal !important;}#polls_vgc table{list-style: none outside none;overflow: hidden;padding: 0px;}#polls_vgc table tr{border:none !important;}#polls_vgc table td{padding: 5px 0;vertical-align: top;position: relative;border:none !important;}#polls_vgc .ip{float: left;margin: 0 0 0 10px;float: left;margin-top: 3px;}#polls_vgc table label{color: #888888;display: block;font-family: arial;font-size: 13px;margin: 0 0 0 5px !important;overflow: hidden;line-height: 16px !important;text-align: left;}#polls_vgc .polls_btn{background-color: #0E76BC;border: none;border-radius: 3px;color: #FFFFFF;display: inline-block;font-size: 13px;margin: 0px 5px;padding: 5px 20px;cursor: pointer;}.poll_load{background-image: url(data:image/gif;base64,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);height: 20px;position: absolute;top: 5px;width: 20px;}#vgc_client_info{box-sizing: border-box;overflow: auto;padding: 5% 2%;width: 100%;height: 100%;position: absolute;left: 0;top: 0;background-color: #fff;z-index: 8;}#vgc_client_info .vgc_client_info_note{color: #333;font-family: arial;font-size: 12px;font-style: normal;font-weight: normal;line-height: 18px;margin: 0 0 5px;overflow: hidden;text-align: left;text-shadow: none;width: 100%;}#vgc_client_info table{border-collapse: collapse;width: 100%;}#vgc_client_info table td{padding: 2px 0;position: relative;border: none;}#vgc_client_info_error{color: #f00;font-size: 12px;text-align: left;font-family: arial;font-style: normal;font-weight: normal;text-shadow: none;}#vgc_client_info input[type=text]{ border: 1px solid #ccc; box-sizing: border-box; color: #222; font-family: arial; font-size: 13px; font-weight: normal; line-height: 20px; margin-bottom: 3px; max-width: 300px; padding: 2px 5px; width: 100%;}#vgc_client_info .vgc_not_office{margin: 2px 0;line-height: normal;width: 100%;display: block;overflow: hidden;height: 15px;color: #333;font-size: 12px;border: none;}#vgc_client_info select{border: 1px solid #ccc;box-sizing: border-box;color: #222;font-family: arial;font-size: 13px;font-weight: normal;line-height: 20px;margin-bottom: 3px;max-width: 300px;padding: 2px 5px;width: 100%;}#vgc_client_info select option{color: #555;}#vgc_client_info select option.sp_online{color: #007500;}#vgc_client_info select option.sp_offline{color: #C9CDCC;}#vgc_client_info input[type=button]{background-color: #0e76bc;background-image: none;border: medium none;border-radius: 3px;color: #ffffff;cursor: pointer;display: inline-block;font-size: 13px !important; text-transform: capitalize; font-weight: normal !important;margin: 0 10px 0 0;padding: 5px 10px;max-width: 200px;}.vgc_icon-help {display: block;font-family: arial;font-size: 18px;font-weight: bold;height: auto;position: relative;text-align: center;width: auto;font-size: 18px; cursor: pointer;}.vgc_icon-help span{display: block;font-family: Tahoma;font-size: 26px;font-weight: bold;left: 12px;line-height: 35px;position: relative;text-align: center;top: -8px;width: auto;}#vgc_logo_msgoffline{float: left;margin: 0;padding-top: 5px;position: relative;text-align: right;width: 100%;}#vgc_logo_msgoffline .vgc_ad_client{display: inline-block;margin-right: 100px;overflow: hidden;margin: -3px 0 0;float: left;}#vgc_logo_msgoffline .vgc_ad_client a{text-decoration: none;color: #fff;font-size: 12px;font-family: verdana;text-shadow: none;line-height:normal; white-space: pre !important;}#panel_chat_vatgia{z-index: 999999999; position: fixed;}#panel_chat_vatgia #vatgia_note_message{background-color: #2784C3;border-radius: 3px;bottom: 30px;position: fixed;right: 5%;z-index: 9999999;}#panel_chat_vatgia #vatgia_note_message .vgc_msg_close{cursor: pointer;font-family: arial;font-size: 10px;padding: 0 5px;position: absolute;right: 0px;top: -13px;}#panel_chat_vatgia #vatgia_note_message #vatgia_note_content{color: #fff;cursor: pointer;display: block;font-size: 12px;padding: 5px 10px;font-family: arial;text-decoration: none;text-shadow: 0 1px 0 #2170A5;}#panel_chat_vatgia #vatgia_note_message #vatgia_note_content b{animation: 0.5s ease 0s normal none infinite contact;-webkit-animation: 0.5s ease 0s normal none infinite contact;-moz-animation: 0.5s ease 0s normal none infinite contact;-o-animation: 0.5s ease 0s normal none infinite contact;border-radius: 2px;background-color: #f00;position: relative;padding: 0 2px;margin: 0 2px;color: #fff;}#panel_chat_vatgia #vgc_client_notice_vg{position: fixed;bottom: 0;right: 2%;border-top: 1px solid #ccc;border-left: 1px solid #ccc;border-right: 1px solid #ccc;height: 30px;width: 30px;border-radius: 5px 5px 0 0;cursor: pointer;background-color: #fff;z-index: 9999999999;}#panel_chat_vatgia #vgc_client_notice_vg .vgc_quacau{background-position: 0 -38px;height: 24px;left: 4px;position: absolute;top: 5px;width: 24px;}#panel_chat_vatgia #vgc_client_notice_vg .vgc_notice_count{background-color: #f00;border-radius: 30px;color: #fff;font-family: arial;font-size: 11px;font-weight: bold;padding: 1px 2px;position: absolute;right: -7px;top: -9px;line-height: normal !important;}#panel_chat_vatgia #vgc_client_notice_vg #vgc_notice_vatgia{position: fixed;bottom: 35px;width: 250px;background-color: #fff;border-radius: 5px;padding: 0;overflow: hidden;box-sizing: content-box;}#panel_chat_vatgia #vgc_client_notice_vg #vgc_notice_vatgia #vgc_notice_header{background-color: #2784C3;margin: 0;font-family: arial;font-size: 13px;font-weight: bold;color: #fff;padding: 0 0px 0 10px;line-height: 30px;position: relative;}#panel_chat_vatgia #vgc_client_notice_vg #vgc_notice_vatgia #vgc_notice_header .vgc_notice_close{cursor: pointer;overflow: hidden;position: absolute;right: 5px;text-align: center;top: 0;width: 20px;font-weight: normal;font-family: arial;color: #fff;}#panel_chat_vatgia #vgc_client_notice_vg #vgc_notice_vatgia .vgc_notice_all_item{border: 1px solid #ccc;border-radius: 0px 0 5px 5px;max-height: 500px; overflow-y: auto; padding: 10px 0;}#panel_chat_vatgia #vgc_client_notice_vg #vgc_notice_vatgia .vgc_notice_item{padding: 5px 10px;border-bottom: 1px solid #eee;color: #999;font-size: 11px;font-family: sans-serif;line-height: 19px; text-align: left;}#panel_chat_vatgia #vgc_client_notice_vg #vgc_notice_vatgia .vgc_notice_item:last-child{border-bottom: none;}#panel_chat_vatgia #vgc_client_notice_vg #vgc_notice_vatgia .vgc_notice_item .vgc_namecpn{ color: #2784c3; display: block; font-weight: bold; height: 20px; font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 70%; float: left; padding-right: 5px;}#panel_chat_vatgia #vgc_client_notice_vg #vgc_notice_vatgia .vgc_notice_item i{margin-top: 1px;display: inline-block;}#panel_chat_vatgia #vgc_client_notice_vg #vgc_notice_vatgia .vgc_notice_item span{color: #222;display: block;font-family: arial;font-size: 12px;font-style: normal;font-weight: normal;line-height: 18px;width: 100%; clear: both;}#panel_chat_vatgia #vgc_client_notice_vg #vgc_notice_vatgia .vgc_notice_item span a{color: #222;cursor: pointer;display: block;}#panel_chat_vatgia #vgc_client_notice_vg #vgc_notice_vatgia .vgc_notice_item .vgc_count{color: #E53D37;font-weight: bold;}#panel_chat_vatgia #vgc_client_notice_vg #vgc_notice_vatgia .vgc_notice_item a{color: #0072BD;overflow: hidden;text-decoration: none;text-overflow: ellipsis;white-space: nowrap;width: 100%;}#panel_chat_vatgia #vgc_client_notice_vg #vgc_notice_vatgia .vgc_notice_item a:hover{text-decoration: underline;color: #0072bd;}.vgc_get_notification_browser{ display: none; position: fixed; top: 40px; left: 35%; background-color: #FFFF80; border: 1px solid #FFFF00; border-radius: 3px; box-shadow: 0px 2px 4px #DDDD00; padding: 5px 30px 5px 10px; color: #222; font-weight: bold; cursor: pointer; font-family: arial; font-size: 12px; z-index: 99999999;}.vgc_get_notification_browser i{ position: absolute; top: 7px; right: 5px; font-style: normal; font-weight: normal; font-size: 11px; background-color: #D9D900; display: block; width: 12px; height: 12px; line-height: 10px; text-align: center; color: #fff; cursor: pointer; z-index: 2;}.vgc_get_notification_browser i:hover{ background-color: #222;}#vatgia_client_polls{background-color: #fff;box-sizing:content-box;height: 90%;left: 0;padding: 5% 2%;position: absolute;top: 0;width: 96%;z-index: 999;}#vatgia_client_polls .vgc_quest_item{box-sizing: content-box;color: #666;font-family: arial;font-size: 12px;font-style: normal;font-weight: bold;margin: 0;padding: 15px 10px;}#vatgia_client_polls .vgc_polls_btn, #vatgia_client_polls .vgc_p_a_btn{background-color: #f2f2f2;border: 1px solid #ccc;border-radius: 3px;box-shadow: 0 1px 0 #fff inset;box-sizing: content-box;color: #666;cursor: pointer;display: inline-block;font-family: arial;font-size: 12px;font-weight: normal;margin: 0 5px;padding: 5px 10px;}#vatgia_client_polls .vgc_polls_btn:hover, #vatgia_client_polls .vgc_p_a_btn:hover{color: #222;}#vatgia_client_polls .vgc_quest_action{margin: 0;padding: 10px 0;text-align: center;}#vatgia_client_polls .vgc_p_a_title {box-sizing: content-box;color: #444;font-family: arial;font-size: 13px;font-weight: bold;margin: 0;padding-bottom: 10px;text-align: left;}#vatgia_client_polls .vgc_p_a_error, #vatgia_client_polls .vgc_quest_error {color: #f00;font-family: arial;font-size: 12px;font-style: normal;margin: 0 0 5px;padding: 0;text-align: center;}#vatgia_client_polls .vgc_p_a_list {list-style: none outside none;margin: 0;padding: 0;overflow: hidden;}#vatgia_client_polls .vgc_p_a_list li {box-sizing: content-box;display: block;float: left;margin: 0;padding: 3px 0;width: 100%;}#vatgia_client_polls .vgc_p_a_list li input[type="radio"] {float: left;line-height: normal;margin: 2px 0 0;padding: 0;}#vatgia_client_polls .vgc_p_a_list li label {color: #444;display: block;float: none !important;line-height: normal !important;padding-left: 20px;text-align: left;}#vatgia_client_polls .vgc_p_a_ac {box-sizing: content-box;display: block;margin: 10px 0;overflow: hidden;padding: 0;text-align: center;}#vatgia_client_polls .vgc_p_a_ac .vgc_p_a_loadding {background-image: url(\'data:image/gif;base64,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\');display: inline-block;height: 20px;vertical-align: text-bottom;width: 20px;}/* vatgia ad */#vgc_html_avg{height: 35px;overflow: hidden;position: absolute;right: 0;top: -40px;width: 240px;}#vgc_html_avg a{display: block;height: 100%;position: relative;text-decoration: none;width: 100%;}#vgc_html_avg img{border: medium none;margin: 0;max-width: 100%;outline: medium none;padding: 0;}#vgc_html_avg:hover #vgc_close_advg{display: block;}#vgc_close_advg{background-color: #fff;border-radius: 0 0 0 5px;color: #000;cursor: pointer;display: none;font-size: 12px;font-style: normal;font-weight: bold;height: 15px;line-height: 12px;position: absolute;right: 0;text-align: center;top: 0;width: 15px;}#vgc_close_advg:hover{background-color: #f00;color: yellow;}.vgc_greet_bottom{background-color: #fff;border: 1px solid #eee;bottom: 0;box-sizing: border-box;margin: 0;overflow: hidden;right: 0;width: 100%;}#vgc_greet_msg{border: medium none;box-sizing: border-box;display: block;font-family: arial;font-size: 13px;line-height: 20px;padding: 5px 35px 5px 5px;width: 100%;}.vgc_greet_bottom > input::-moz-placeholder{color: #666;font-style: italic;}.vgc_greet_bottom > input::-webkit-input-placeholder{color: #666;font-style: italic;}.vgc_greet_bottom > input:-ms-input-placeholder{color: #666;font-style: italic;}.vgc_greet_enter{bottom: 1px;display: block;height: 30px;overflow: hidden;position: absolute;right: 1px;width: 30px;}.vgc_greet_enter::before {border-bottom: 9px solid transparent;border-top: 9px solid transparent;content: "";position: absolute;right: 3px;top: 6px;}.vgc_greet_enter::after {border-bottom: 8px solid transparent;border-left: 4px solid #fff;border-top: 8px solid transparent;content: "";position: absolute;right: 18px;top: 7px;}.vgc_greet_enter .vgc_arrow_r{border-bottom: 1px solid transparent;border-left: 5px solid #fff;border-top: 1px solid transparent;content: "";position: absolute;right: 13px;top: 14px;}.vgc_greeting_chat{border-radius: 10px 10px 0 0;box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);bottom: 0px;box-sizing: border-box;cursor: pointer;padding: 0px;position: absolute;width: 101%;min-width: 230px;z-index: 9;}.vgc_greeting_chat .vgc_greeting_text{color: #fff;font-size: 15px;font-weight: normal;line-height: 20px;margin: 0;padding: 35px 15px;}.vgc_greeting_chat .vgc_greeting_text img{float: left;max-width: 80px;margin: 6px 0 20px 0;width: 30%;}.vgc_greeting_chat #vgc_greeting_text{color: #fff;display: block;font-family: arial;font-size: 18px;line-height: 24px;margin-left: 30%;padding-left: 15px;}.vgc_greeting_chat .vgc_greeting_logo{left: 0;margin: 0;opacity: 0.5;padding: 0 10px;position: absolute;top: 5px;}.vgc_greeting_chat .vgc_greeting_close{background-color: #fff;border-radius: 0 9px;color: #999;cursor: pointer;display: none;font-size: 14px;padding: 0px 5px;position: absolute;right: 0;top: 0;}.vgc_greeting_chat:hover .vgc_greeting_close{display: block;}.vgc_greeting_chat:hover .vgc_greeting_logo{opacity: 1;}.vgc_greeting_chat .vgc_greeting_close:hover{color: #111;}.vgc_social{color: #fff;padding: 2px 5px;text-decoration: none;display: inline-block;}.vgcfb{background-color: #145fad;}.vgcgg{background-color: #e53d37;}#vgc_quere_chat{background-color: #fff;box-sizing: border-box;display: none;height: 100%;left: 0;padding: 50px 10px;position: absolute;text-align: center;top: 0;width: 100%;z-index: 999;}#vgc_start_chat{}#vgc_start_chat select{}#vgc_start_chat p{}#vgc_start_chat p span{}#vgc_btn_chat_mobile{ bottom: 10px; position: fixed; text-align: center; font-family: arial; color: #fff; height: 40px; line-height: 40px; font-size: 18px; border-radius: 40px; cursor: pointer; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 40px;}#vgc_btn_chat_mobile img{ height: 20px !important; width: 20px !important; position: absolute; left: calc((100% - 20px) / 2); margin: 0 !important; top: calc((100% - 20px) / 2); display: block;}.vgc_logosvg{ animation: phonering-alo-circle-img-anim 1s infinite ease-in-out; transform-origin: 50% 50%; -webkit-animation: phonering-alo-circle-img-anim 1s infinite ease-in-out;}@keyframes phonering-alo-circle-img-anim { 0% {-webkit-transform:rotate(0) scale(1) skew(1deg);transform:rotate(0) scale(1) skew(1deg)} 10% {-webkit-transform:rotate(-25deg) scale(1) skew(1deg);transform:rotate(-25deg) scale(1) skew(1deg)} 20% {-webkit-transform:rotate(25deg) scale(1) skew(1deg);transform:rotate(25deg) scale(1) skew(1deg)} 30% {-webkit-transform:rotate(-25deg) scale(1) skew(1deg);transform:rotate(-25deg) scale(1) skew(1deg)} 40% {-webkit-transform:rotate(25deg) scale(1) skew(1deg);transform:rotate(25deg) scale(1) skew(1deg)} 50% {-webkit-transform:rotate(0) scale(1) skew(1deg);transform:rotate(0) scale(1) skew(1deg)} 100% {-webkit-transform:rotate(0) scale(1) skew(1deg);transform:rotate(0) scale(1) skew(1deg)}}.vgc_icon_mobile{background-position: -54px -17px;display: inline-block;height: 20px;margin-right: 5px;vertical-align: text-bottom;width: 24px;}#vgc_off_mobile{ right: 10px; bottom: 10px; position: fixed; text-align: left; font-family: arial; color: #fff; height: 40px; line-height: 40px; font-size: 18px; border-radius: 40px; cursor: pointer; max-width: 250px; width: 40px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;}#vgc_off_mobile .vgc_ic_off{ background-color: transparent; background-position: -137px -17px; display: block; float: left; height: 17px; margin-right: 0px !important; margin-top: 12px; width: 19px; margin-left: 10px;}.tem_ctvc_v4 .panel_info_vgchat{ background-color: #F8F8F8 !important;}.vgc_emoji{ position: absolute; right: 20px; display: inline-block; width: 20px !important; height: 20px; font-size: 15px; bottom: 0; text-align: center; cursor: pointer;}#emoji_list{ position: absolute; max-width: 200px; max-height: 200px; padding: 5px; border: 1px solid #ccc; bottom: 20px; right: 25px; overflow: auto; background-color: #fff; display: none;}#emoji_list span{ display: inline-block; width: 35px !important; height: 35px; padding: 5px; box-sizing: border-box; overflow: hidden; cursor: pointer; border: 1px solid transparent;}#emoji_list span:hover{ border: 1px solid #000;}#emoji_list span img{ width: 100%;}/*skill*/#uhchat, #subiz, #sbzoff_frame, .sbzoff, #tawkchat-maximized-wrapper, .zopim, #livechat-full, #livechat-compact-container{ display: none !important; position: fixed !important; top: -1000px !important; left: -1000px !important; background-color: #000 !important; font-size: 0px !important; line-height: 0px !important; color:#000 !important; }.promptButtonContainer { display: none !important;}</style>');
$vnpJs('head').append('');
$vnpJs('body').append('<div id="panel_chat_vatgia"><div class="" id="vgc_bc_off" style="background-color:#982584; right:10px; border: 1px solid rgb(132,17,112)"><h3 class="vgc_tt" onclick="vgc_sh_chat_contact();"><i class="vgc_ic vgc_ic_off"></i>Để lại tin nhắn cho chúng tôi <i id="vgc_off_close"></i></h3><ul class="vgc_off_row"><li class="vgc_remove"><div class="vgc_polls_contact" style="color:#982584;"><p style="color:#982584;">Hỗ Trợ Tư Vấn</p><p style="color:#982584;">Tổng Đài Hỗ Trợ: 1900.03.44</p><p style="color:#982584;">Email: support@minhhagroup.com</p></div></li><li><span id="vgc_er"></span></li><li class="vgc_remove"><p class="vgc_name_title">Tên của bạn</p><input class="vgc_control" id="vgc_use_name" type="text" value="" name="vgc_use_name" /></li><li class="vgc_remove"><p class="vgc_name_title">Email</p><input class="vgc_control" id="vgc_use_email" type="text" value="" name="vgc_use_email" data-require="0" /></li><li class="vgc_remove"><p class="vgc_name_title">Số điện thoại</p><input class="vgc_control" id="vgc_use_phone" type="text" value="" name="vgc_use_phone" data-require="1" /></li><li class="vgc_remove"><p class="vgc_name_title">Tin nhắn (không quá 255 ký tự)</p><textarea maxlength="255" class="vgc_control_msg" name="vgc_msg_off" id="vgc_msg_off"></textarea></li><li class="vgc_remove"><input class="vgc_control_send" style="background-color:#982584;" type="button" value="Gửi tin nhắn" onclick="vgc_boxchat_send_msg_offline(2628978);" /><span class="vgc_off_loadding"></span></li></ul><input type="hidden" name="vgc_myid" id="vgc_myid" value="1305049145" /><input type="hidden" name="vgc_send_id" id="vgc_send_id" value="1305049145" /><input type="hidden" name="vgc_to_id" id="vgc_to_id" value="2628978" /><input type="hidden" name="vgc_hash" id="vgc_hash" value="ddbbedfcf21b51cccc08eb29960e6573" /><input type="hidden" name="vgc_address" id="vgc_address" value="" /><input type="hidden" name="vgc_ip" id="vgc_ip" value="180.93.143.230" /><div id="vgc_logo_msgoffline" style="display: none;"><p class="vgc_ad_client"><marquee style="float:left;margin-top:4px;width:200px;" onmouseover="this.setAttribute(\'scrollamount\', 0, 0);" onmouseout="this.setAttribute(\'scrollamount\', 3, 0);" behavior="scroll" scrollamount="3" direction="left"><a target="_blank" href="//vchat.vn/home/?utm_source=refer_client&utm_medium=click&utm_campaign=refer_client" target="_blank" style="color:#fff;font-size:12px;">Phần mềm chat MIỄN PHÍ trên website hàng đầu Việt Nam</a></marquee></p><a class="vgclogovchat" target="_blank" href="//vchat.vn/home/?utm_campaign=Box_chat_client&utm_medium=referral&utm_source=Box_chat_client" ></a></div></div></div>');
(function() {
   var ga = document.createElement('script'); 
   ga.type = 'text/javascript'; 
   ga.async=1; 
   ga.src = 'https://live.vnpgroup.net/geolocation/geo.js?v=123';
   var s = document.getElementsByTagName('script');
   s[0].parentNode.insertBefore(ga, s[0]);})();
setTimeout("getIp()",3000);
console.log("Packet: 0");
