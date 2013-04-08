(function (af, aX) {
    var aY = aX.documentElement;
    var aH = {};

    function aj(a) {
        if (!(a in aH)) {
            aH[a] = new RegExp("(^|\\s+)" + a + "(\\s+|$)", "")
        }
        return aH[a]
    }
    function ai(a, b) {
        return aj(b).test(a.className || "")
    }
    function aA(a, b) {
        if (!ai(a, b)) {
            a.className += " " + b
        }
    }
    function aK(a, b) {
        if (a) {
            a.className = a.className.replace(new RegExp("((?:^|\\s+)" + b + "|" + b + "(?:\\s+|$))", "g"), "")
        }
    }
    function aZ(a, b) {
        if (ai(a, b)) {
            aK(a, b)
        } else {
            aA(a, b)
        }
    }
    var ae, al, ad = ae = function (b, a) {
        b = b || aX;
        var e = b[az] && b[az]("*"),
            f = [],
            c = 0,
            d = e.length;
        for (; c < d; c++) {
            if (ai(e[c], a)) {
                f.push(e[c])
            }
        }
        return f
    };
    if (aX.querySelectorAll) {
        ae = function (b, a) {
            return b.querySelectorAll("." + a)
        }
    } else {
        if (aX.getElementsByClassName) {
            ae = function (b, a) {
                if (b.getElementsByClassName) {
                    return b.getElementsByClassName(a)
                }
                return ad(b, a)
            }
        }
    }
    function aF(c, a) {
        var b = c;
        do {
            if (ai(b, a)) {
                return b
            }
        } while (b = b.parentNode);
        return null
    }
    if (af.innerHeight) {
        al = function () {
            return {
                width: af.innerWidth,
                height: af.innerHeight
            }
        }
    } else {
        if (aY && aY.clientHeight) {
            al = function () {
                return {
                    width: aY.clientWidth,
                    height: aY.clientHeight
                }
            }
        } else {
            al = function () {
                var a = aX.body;
                return {
                    width: a.clientWidth,
                    height: a.clientHeight
                }
            }
        }
    }
    var aU = aX.addEventListener ? function (c, b, a) {
            c.addEventListener(b, a, false)
        } : function (c, b, a) {
            c.attachEvent("on" + b, a)
        }, a4 = aX.removeEventListener ? function (c, b, a) {
            c.removeEventListener(b, a, false)
        } : function (c, b, a) {
            c.detachEvent("on" + b, a)
        };
    var aM, ak;
    if ("onmouseenter" in aY) {
        aM = function (b, a) {
            aU(b, "mouseenter", a)
        };
        ak = function (b, a) {
            aU(b, "mouseleave", a)
        }
    } else {
        aM = function (b, a) {
            aU(b, "mouseover", function (c) {
                if (ag(c, this)) {
                    a(c)
                }
            })
        };
        ak = function (b, a) {
            aU(b, "mouseout", function (c) {
                if (ag(c, this)) {
                    a(c)
                }
            })
        }
    }
    function ag(b, d) {
        var c = b.relatedTarget;
        try {
            while (c && c !== d) {
                c = c.parentNode
            }
            if (c !== d) {
                return true
            }
        } catch (a) {}
    }
    function ar(a) {
        try {
            a.preventDefault()
        } catch (b) {
            a.returnValue = false
        }
    }
    function ao(a) {
        try {
            a.stopPropagation()
        } catch (b) {
            a.cancelBubble = true
        }
    }
    var aL = (function (f, d) {
        var b = {
            boxModel: null
        }, l = d.defaultView && d.defaultView.getComputedStyle,
            c = /([A-Z])/g,
            k = /-([a-z])/ig,
            j = function (o, n) {
                return n.toUpperCase()
            }, h = /^-?\d+(?:px)?$/i,
            e = /^-?\d/,
            a = function () {};
        if ("getBoundingClientRect" in aY) {
            return function (n) {
                if (!n || !n.ownerDocument) {
                    return null
                }
                m();
                var o = n.getBoundingClientRect(),
                    s = n.ownerDocument,
                    p = s.body,
                    q = (aY.clientTop || p.clientTop || 0) + (parseInt(p.style.marginTop, 10) || 0),
                    r = (aY.clientLeft || p.clientLeft || 0) + (parseInt(p.style.marginLeft, 10) || 0),
                    t = o.top + (f.pageYOffset || b.boxModel && aY.scrollTop || p.scrollTop) - q,
                    u = o.left + (f.pageXOffset || b.boxModel && aY.scrollLeft || p.scrollLeft) - r;
                return {
                    top: t,
                    left: u
                }
            }
        } else {
            return function (p) {
                if (!p || !p.ownerDocument) {
                    return null
                }
                i();
                var r = p.offsetParent,
                    s = p,
                    u = p.ownerDocument,
                    w, o = u.body,
                    n = u.defaultView,
                    t = n ? n.getComputedStyle(p, null) : p.currentStyle,
                    v = p.offsetTop,
                    q = p.offsetLeft;
                while ((p = p.parentNode) && p !== o && p !== aY) {
                    if (b.supportsFixedPosition && t.position === "fixed") {
                        break
                    }
                    w = n ? n.getComputedStyle(p, null) : p.currentStyle;
                    v -= p.scrollTop;
                    q -= p.scrollLeft;
                    if (p === r) {
                        v += p.offsetTop;
                        q += p.offsetLeft;
                        if (b.doesNotAddBorder && !(b.doesAddBorderForTableAndCells && /^t(able|d|h)$/i.test(p.nodeName))) {
                            v += parseFloat(w.borderTopWidth, 10) || 0;
                            q += parseFloat(w.borderLeftWidth, 10) || 0
                        }
                        s = r, r = p.offsetParent
                    }
                    if (b.subtractsBorderForOverflowNotVisible && w.overflow !== "visible") {
                        v += parseFloat(w.borderTopWidth, 10) || 0;
                        q += parseFloat(w.borderLeftWidth, 10) || 0
                    }
                    t = w
                }
                if (t.position === "relative" || t.position === "static") {
                    v += o.offsetTop;
                    q += o.offsetLeft
                }
                if (b.supportsFixedPosition && t.position === "fixed") {
                    v += Math.max(aY.scrollTop, o.scrollTop);
                    q += Math.max(aY.scrollLeft, o.scrollLeft)
                }
                return {
                    top: v,
                    left: q
                }
            }
        }
        function g(p, s, r) {
            var v, t = p.style;
            if (!r && t && t[s]) {
                v = t[s]
            } else {
                if (l) {
                    s = s.replace(c, "-$1").toLowerCase();
                    var w = p.ownerDocument.defaultView;
                    if (!w) {
                        return null
                    }
                    var u = w.getComputedStyle(p, null);
                    if (u) {
                        v = u.getPropertyValue(s)
                    }
                } else {
                    if (p.currentStyle) {
                        var o = s.replace(k, j);
                        v = p.currentStyle[s] || p.currentStyle[o];
                        if (!h.test(v) && e.test(v)) {
                            var q = t.left,
                                n = p.runtimeStyle.left;
                            p.runtimeStyle.left = p.currentStyle.left;
                            t.left = o === "fontSize" ? "1em" : (v || 0);
                            v = t.pixelLeft + "px";
                            t.left = q;
                            p.runtimeStyle.left = n
                        }
                    }
                }
            }
            return v
        }
        function m() {
            var n = d.createElement("div");
            n.style.width = n.style.paddingLeft = "1px";
            d.body.appendChild(n);
            b.boxModel = n.offsetWidth === 2;
            d.body.removeChild(n).style.display = "none";
            n = null;
            m = a
        }
        function i() {
            var p = d.body,
                o = d.createElement("div"),
                t, r, s, q, n = parseFloat(g(p, "marginTop", true), 10) || 0,
                u = "<div style='position:absolute;top:0;left:0;margin:0;border:5px solid #000;padding:0;width:1px;height:1px;'><div></div></div><table style='position:absolute;top:0;left:0;margin:0;border:5px solid #000;padding:0;width:1px;height:1px;' cellpadding='0' cellspacing='0'><tr><td></td></tr></table>";
            o.style.cssText = "position:absolute;top:0;lefto:0;margin:0;border:0;width:1px;height:1px;visibility:hidden;";
            o.innerHTML = u;
            p.insertBefore(o, p.firstChild);
            t = o.firstChild;
            r = t.firstChild;
            q = t.nextSibling.firstChild.firstChild;
            b.doesNotAddBorder = (r.offsetTop !== 5);
            b.doesAddBorderForTableAndCells = (q.offsetTop === 5);
            r.style.position = "fixed", r.style.top = "20px";
            b.supportsFixedPosition = (r.offsetTop === 20 || r.offsetTop === 15);
            r.style.position = r.style.top = "";
            t.style.overflow = "hidden", t.style.position = "relative";
            b.subtractsBorderForOverflowNotVisible = (r.offsetTop === -5);
            b.doesNotIncludeMarginInBodyOffset = (p.offsetTop !== n);
            p.removeChild(o);
            p = o = t = r = s = q = null;
            m();
            i = a
        }
    })(af, aX);
    var a0 = (function (g, b) {
        var e = false,
            f = false,
            a = [],
            d;

        function c() {
            if (!e) {
                if (!b.body) {
                    return setTimeout(c, 13)
                }
                e = true;
                if (a) {
                    var j, k = 0;
                    while ((j = a[k++])) {
                        j.call(null)
                    }
                    a = null
                }
            }
        }
        function h() {
            if (f) {
                return
            }
            f = true;
            if (b.readyState === "complete") {
                return c()
            }
            if (b.addEventListener) {
                b.addEventListener("DOMContentLoaded", d, false);
                g.addEventListener("load", c, false)
            } else {
                if (b.attachEvent) {
                    b.attachEvent("onreadystatechange", d);
                    g.attachEvent("onload", c);
                    var k = false;
                    try {
                        k = g.frameElement == null
                    } catch (j) {}
                    if (aY.doScroll && k) {
                        i()
                    }
                }
            }
        }
        if (b.addEventListener) {
            d = function () {
                b.removeEventListener("DOMContentLoaded", d, false);
                c()
            }
        } else {
            if (b.attachEvent) {
                d = function () {
                    if (b.readyState === "complete") {
                        b.detachEvent("onreadystatechange", d);
                        c()
                    }
                }
            }
        }
        function i() {
            if (e) {
                return
            }
            try {
                aY.doScroll("left")
            } catch (j) {
                setTimeout(i, 1);
                return
            }
            c()
        }
        return function (j) {
            h();
            if (e) {
                j.call(null)
            } else {
                a.push(j)
            }
        }
    })(af, aX);

    function aJ() {
        var b = (function (e) {
            e = e.toLowerCase();
            var f = /(webkit)[ \/]([\w.]+)/.exec(e) || /(opera)(?:.*version)?[ \/]([\w.]+)/.exec(e) || /(msie) ([\w.]+)/.exec(e) || !/compatible/.test(e) && /(mozilla)(?:.*? rv:([\w.]+))?/.exec(e) || [];
            return {
                browser: f[1] || "",
                version: f[2] || "0"
            }
        })(navigator.userAgent);
        var d = '',
            a = "";
        var c = document.createElement("style");
        c.type = "text/css";
        c.id = "ya_share_style";
        if (c.styleSheet) {
            c.styleSheet.cssText = b.browser === "msie" && (b.version < 8 || aX.documentMode < 8) ? d + a : d
        } else {
            c.appendChild(aX.createTextNode(d))
        }
        d = a = "";
        aN.appendChild(c);
        aJ = function () {}
    }
    var aT = function () {}, av = null,
        az = "getElementsByTagName",
        aO = encodeURIComponent,
        aN = aX[az]("head")[0] || aX.body,
        aW = "//yandex.st/share",
        aS = "http://share.yandex.ru",
        aP = {
            az: {
                blogger: "Blogger",
                delicious: "delicious",
                diary: "Diary",
                digg: "Digg",
                evernote: "Evernote",
                facebook: "Facebook",
                friendfeed: "FriendFeed",
                gbuzz: "Google Buzz",
                gplus: "Google Plus",
                greader: "Google Reader",
                juick: "Juick",
                linkedin: "LinkedIn",
                liveinternet: "LiveInternet",
                lj: "LiveJournal",
                moikrug: "Moy Kruq",
                moimir: "Moy Mir",
                myspace: "MySpace",
                odnoklassniki: "Odnoklassniki",
                pinterest: "Pinterest",
                pocket: "Pocket",
                surfingbird: "Surfingbird",
                tutby: "YA TUT!",
                twitter: "Twitter",
                vkontakte: "ВКонтакте",
                yaru: "Ya.ru",
                yazakladki: "Yandex.Əlfəcinlər"
            },
            be: {
                blogger: "Blogger",
                delicious: "delicious",
                diary: "Diary",
                digg: "Digg",
                evernote: "Evernote",
                facebook: "Facebook",
                friendfeed: "FriendFeed",
                gbuzz: "Google Buzz",
                gplus: "Google Plus",
                greader: "Google Reader",
                juick: "Juick",
                linkedin: "LinkedIn",
                liveinternet: "LiveInternet",
                lj: "LiveJournal",
                moikrug: "Мой Круг",
                moimir: "Мой Мир",
                myspace: "MySpace",
                odnoklassniki: "Аднакласнікі",
                pinterest: "Pinterest",
                pocket: "Pocket",
                surfingbird: "Surfingbird",
                tutby: "Я ТУТ!",
                twitter: "Twitter",
                vkontakte: "ВКонтакте",
                yaru: "Я.ру",
                yazakladki: "Яндекс.Закладкі"
            },
            en: {
                blogger: "Blogger",
                delicious: "delicious",
                diary: "Diary",
                digg: "Digg",
                evernote: "Evernote",
                facebook: "Facebook",
                friendfeed: "FriendFeed",
                gbuzz: "Google Buzz",
                gplus: "Google Plus",
                greader: "Google Reader",
                juick: "Juick",
                linkedin: "LinkedIn",
                liveinternet: "LiveInternet",
                lj: "LiveJournal",
                moikrug: "Moi Krug",
                moimir: "Moi Mir",
                myspace: "MySpace",
                odnoklassniki: "Odnoklassniki",
                pinterest: "Pinterest",
                pocket: "Pocket",
                surfingbird: "Surfingbird",
                tutby: "Tut.by",
                twitter: "Twitter",
                vkontakte: "VKontakte",
                yaru: "Ya.ru",
                yazakladki: "Yandex.Bookmarks"
            },
            hy: {
                blogger: "Blogger",
                delicious: "delicious",
                diary: "Diary",
                digg: "Digg",
                evernote: "Evernote",
                facebook: "Facebook",
                friendfeed: "FriendFeed",
                gbuzz: "Google Buzz",
                gplus: "Google Plus",
                greader: "Google Reader",
                juick: "Juick",
                linkedin: "LinkedIn",
                liveinternet: "LiveInternet",
                lj: "LiveJournal",
                moikrug: "Moi Krug",
                moimir: "Moi Mir",
                myspace: "MySpace",
                odnoklassniki: "Odnoklassniki",
                pinterest: "Pinterest",
                pocket: "Pocket",
                surfingbird: "Surfingbird",
                tutby: "YA TUT",
                twitter: "Twitter",
                vkontakte: "VKontakte",
                yaru: "Ya.Ru",
                yazakladki: "Yandex.Էջանիշներ"
            },
            ka: {
                blogger: "Blogger",
                delicious: "delicious",
                diary: "Diary",
                digg: "Digg",
                evernote: "Evernote",
                facebook: "Facebook",
                friendfeed: "FriendFeed",
                gbuzz: "Google Buzz",
                gplus: "Google Plus",
                greader: "Google Reader",
                juick: "Juick",
                linkedin: "LinkedIn",
                liveinternet: "LiveInternet",
                lj: "LiveJournal",
                moikrug: "Moi Krug",
                moimir: "Moi Mir",
                myspace: "MySpace",
                odnoklassniki: "Odnoklasniki",
                pinterest: "Pinterest",
                pocket: "Pocket",
                surfingbird: "Surfingbird",
                tutby: "Ya Tut!",
                twitter: "Twitter",
                vkontakte: "VKontakte",
                yaru: "Ya.ru",
                yazakladki: "Yandex ჩანართები"
            },
            kk: {
                blogger: "Blogger",
                delicious: "delicious",
                diary: "Diary",
                digg: "Digg",
                evernote: "Evernote",
                facebook: "Facebook",
                friendfeed: "Friendfeed",
                gbuzz: "Google Buzz",
                gplus: "Google Plus",
                greader: "Google Reader",
                juick: "Juick",
                linkedin: "LinkedIn",
                liveinternet: "LiveInternet",
                lj: "LiveJournal",
                moikrug: "Мой Круг",
                moimir: "Мой Мир",
                myspace: "MySpace",
                odnoklassniki: "Одноклассники",
                pinterest: "Pinterest",
                pocket: "Pocket",
                surfingbird: "Surfingbird",
                tutby: "Я ТУТ!",
                twitter: "Twitter",
                vkontakte: "ВКонтакте",
                yaru: "Я.ру",
                yazakladki: "Яндекс.Бетбелгілер"
            },
            ro: {
                blogger: "Blogger",
                delicious: "delicious",
                diary: "Diary",
                digg: "Digg",
                evernote: "Evernote",
                facebook: "Facebook",
                friendfeed: "FriendFeed",
                gbuzz: "Google Buzz",
                gplus: "Google Plus",
                greader: "Google Reader",
                juick: "Juick",
                linkedin: "LinkedIn",
                liveinternet: "LiveInternet",
                lj: "LiveJournal",
                moikrug: "Moi Krug",
                moimir: "Moi Mir",
                myspace: "MySpace",
                odnoklassniki: "Odnoklassniki",
                pinterest: "Pinterest",
                pocket: "Pocket",
                surfingbird: "Surfingbird",
                tutby: "YA TUT!",
                twitter: "Twitter",
                vkontakte: "VKontakte",
                yaru: "Ya.ru",
                yazakladki: "Yandex.Notiţe"
            },
            ru: {
                blogger: "Blogger",
                delicious: "delicious",
                diary: "Diary",
                digg: "Digg",
                evernote: "Evernote",
                facebook: "Facebook",
                friendfeed: "FriendFeed",
                gbuzz: "Google Buzz",
                gplus: "Google Plus",
                greader: "Google Reader",
                juick: "Juick",
                linkedin: "LinkedIn",
                liveinternet: "LiveInternet",
                lj: "LiveJournal",
                moikrug: "Мой Круг",
                moimir: "Мой Мир",
                myspace: "MySpace",
                odnoklassniki: "Одноклассники",
                pinterest: "Pinterest",
                pocket: "Pocket",
                surfingbird: "Surfingbird",
                tutby: "Я ТУТ!",
                twitter: "Twitter",
                vkontakte: "ВКонтакте",
                yaru: "Я.ру",
                yazakladki: "Яндекс.Закладки"
            },
            tr: {
                blogger: "Blogger",
                delicious: "delicious",
                diary: "Diary",
                digg: "Digg",
                evernote: "Evernote",
                facebook: "Facebook ",
                friendfeed: "FriendFeed",
                gbuzz: "Google Buzz",
                gplus: "Google Plus",
                greader: "Google Reader",
                juick: "Juick",
                linkedin: "LinkedIn",
                liveinternet: "LiveInternet",
                lj: "LiveJournal ",
                moikrug: "Moy Krug",
                moimir: "Moi Mir",
                myspace: "MySpace",
                odnoklassniki: "Odnoklasniki",
                pinterest: "Pinterest",
                pocket: "Pocket",
                surfingbird: "Surfingbird",
                tutby: "Tut.by!",
                twitter: "Twitter ",
                vkontakte: "VKontakte ",
                yaru: "Ya.ru",
                yazakladki: "Yandex.Favoriler"
            },
            tt: {
                blogger: "Blogger",
                delicious: "delicious",
                diary: "Diary",
                digg: "Digg",
                evernote: "Evernote",
                facebook: "Facebook",
                friendfeed: "FriendFeed",
                gbuzz: "Google Buzz",
                gplus: "Google Plus",
                greader: "Google Reader",
                juick: "Juick",
                linkedin: "LinkedIn",
                liveinternet: "LiveInternet",
                lj: "LiveJournal",
                moikrug: "Мой Круг",
                moimir: "Мой Мир",
                myspace: "MySpace",
                odnoklassniki: "Одноклассники",
                pinterest: "Pinterest",
                pocket: "Pocket",
                surfingbird: "Surfingbird",
                tutby: "Я ТУТ!",
                twitter: "Twitter",
                vkontakte: "ВКонтакте",
                yaru: "Я.ру",
                yazakladki: "Яндекс.Кыстыргычлар"
            },
            uk: {
                blogger: "Blogger",
                delicious: "delicious",
                diary: "Diary",
                digg: "Digg",
                evernote: "Evernote",
                facebook: "Facebook",
                friendfeed: "FriendFeed",
                gbuzz: "Google Buzz",
                gplus: "Google Plus",
                greader: "Google Reader",
                juick: "Juick",
                linkedin: "LinkedIn",
                liveinternet: "LiveInternet",
                lj: "LiveJournal",
                moikrug: "Мой Круг",
                moimir: "Мой Мир",
                myspace: "MySpace",
                odnoklassniki: "Однокласники",
                pinterest: "Pinterest",
                pocket: "Pocket",
                surfingbird: "Surfingbird",
                tutby: "Я ТУТ!",
                twitter: "Twitter",
                vkontakte: "ВКонтакті",
                yaru: "Я.ру",
                yazakladki: "Яндекс.Закладки"
            }
        }, a3 = {
            az: {
                close: "bağla",
                "code blog": "Bloq üçün kod",
                link: "Əlaqə",
                share: "Paylaş",
                "share with friends": "Dostlarla paylaş"
            },
            be: {
                close: "закрыць",
                "code blog": "Код для блога",
                link: "Cпасылка",
                share: "Падзяліцца",
                "share with friends": "Падзяліцца з сябрамi"
            },
            en: {
                close: "close",
                "code blog": "Blog code",
                link: "Link",
                share: "Share",
                "share with friends": "Share with friends"
            },
            hy: {
                close: "Փակել",
                "code blog": "Օրագրի կոդ",
                link: "Հղում",
                share: "Կիսվել",
                "share with friends": "Կիսվեք ընկերների հետ"
            },
            ka: {
                close: "დახურვა",
                "code blog": "ბლოგის კოდი",
                link: "ბმული",
                share: "გაზიარება",
                "share with friends": "მეგობრებთან გაზიარება"
            },
            kk: {
                close: "жабу",
                "code blog": "Блог үшін код",
                link: "Сілтеме",
                share: "Бөлісу",
                "share with friends": "Достарыңызбен бөлісіңіз"
            },
            ro: {
                close: "închide",
                "code blog": "Cod pentru blog",
                link: "Link",
                share: "Distribuie",
                "share with friends": "Distribuie prietenilor"
            },
            ru: {
                close: "закрыть",
                "code blog": "Код для блога",
                link: "Ссылка",
                share: "Поделиться",
                "share with friends": "Поделитесь с друзьями"
            },
            tr: {
                close: "kapat",
                "code blog": "Blog için kod",
                link: "Bağlantı",
                share: "Paylaş",
                "share with friends": "Arkadaşlarla paylaş"
            },
            tt: {
                close: "ябу",
                "code blog": "Блог өчен код",
                link: "Сылтама",
                share: "Бүлешү",
                "share with friends": "Дусларгыз белән бүлешегез"
            },
            uk: {
                close: "закрити",
                "code blog": "Код для блогу",
                link: "Посилання",
                share: "Поділитися",
                "share with friends": "Поділіться з друзями"
            }
        };
    aP.custom = {};

    function ay(h, f) {
        aJ();
        if (!h || (!h.element && !h.elementID)) {
            throw new Error("Invalid parameters")
        }
        var E = h.element || h.elementID;
        if (typeof E === "string") {
            E = aX.getElementById(E)
        } else {
            if (!E.nodeType === 1) {
                E = null
            }
        }
        if (!E || E.yashareInited) {
            return
        }
        E.yashareInited = true;
        var w = {};
        if (h.style) {
            w.type = h.style.button === false ? "link" : "button";
            w.linkUnderline = h.style.link;
            w.border = h.style.border;
            w.linkIcon = h.style.icon
        }
        var H, d, a = h.l10n,
            z = (h.link || af.location) + "",
            p = h.elementStyle || w || {
                type: "button"
            }, F = p.quickServices || h.services || ["|", "yaru", "vkontakte", "odnoklassniki", "twitter", "facebook", "moimir", "lj"],
            b = h.title || aX.title,
            u = h.serviceSpecific || h.override || {}, i = h.popupStyle || h.popup || {}, G = i.codeForBlog,
            l = i.blocks || ["yaru", "vkontakte", "odnoklassniki", "twitter", "facebook", "moimir", "lj", "gplus"],
            j = i.copyPasteField || i.input,
            B = "ya-share-" + Math.random() + "-" + +(new Date()),
            v = !/(?:moikrug\.ru|ya\.ru|yandex\.by|yandex\.com|yandex\.com\.tr|yandex\.kz|yandex\.net|yandex\.ru|yandex\.ua|yandex-team\.ru)$/.test(location.hostname),
            n, D = h.servicesDeclaration;
        if (!a || !(a in a3)) {
            a = location.hostname.split(".").splice(-1, 1)[0];
            switch (a) {
                case "by":
                    a = "be";
                    break;
                case "kz":
                    a = "kk";
                    break;
                case "ua":
                    a = "uk";
                    break;
                default:
                    a = "ru"
            }
        }
        d = a3[a];
        if (!v && D) {
            for (n in D) {
                if (D.hasOwnProperty(n) && !(n in aP.custom)) {
                    var c = D[n];
                    if (c && c.url && c.title && (c.icon_16 || c.icon_from)) {
                        aP.custom[n] = D[n]
                    } else {
                        throw new Error("Invalid new service declaration")
                    }
                }
            }
        }
        if (!p.type || ("block button link icon none".indexOf(p.type) === -1)) {
            p.type = "button"
        }
        var y = p.afterIconsText;
        if (y) {
            p.type = "text"
        }
        H = p.text || h.text || (d.share + (p.type == "button" ? "\u2026" : ""));
        H = am(H);
        if (Object.prototype.toString.call(l) === "[object Array]") {
            var g = l;
            l = {};
            l[d["share with friends"]] = g;
            g = null
        }
        switch (typeof G) {
            case "string":
                var q = G;
                G = {};
                G[d["code blog"]] = q;
                break;
            case "object":
                for (var e in G) {
                    break
                }
                if (!e) {
                    G = null
                }
                break;
            default:
                G = null
        }
        var t = ' id="' + B + '" data-hdirection="' + (i.hDirection || "") + '" data-vdirection="' + (i.vDirection || "") + '"';
        var s, o, c, r = ['<span class="b-share' + (p.type == "block" ? " b-share-form-button b-share-form-button_icons" : "") + (p.border ? " b-share_bordered" : "") + (p.linkUnderline ? " b-share_link" : "") + '"' + (p.background ? ' style="background:' + p.background + '"' : "") + ">" + ((p.type !== "none" && p.type !== "text") ? '<a class="b-share__handle"' + t + ">" : "")];
        if (p.type == "block") {
            r = ['<div class="b-share"><span class="b-share-form-button b-share-form-button_icons"><i class="b-share-form-button__before"></i>']
        } else {
            if (p.type == "button") {
                r.push('<span class="b-share-form-button b-share-form-button_share"><i class="b-share-form-button__before"></i><i class="b-share-form-button__icon"></i>' + H + '<i class="b-share-form-button__after"></i></span>')
            } else {
                if (p.type === "link" || p.type === "text") {
                    r.push('<span class="b-share__text' + (p.type === "text" ? " b-share__handle b-share__handle_cursor_default" : "") + (p.linkUnderline === "dotted" ? " b-share-pseudo-link" : "") + '">')
                }
                if (((p.type === "link" || p.type === "text") && p.linkIcon) || p.type === "icon") {
                    r.push('<img alt="" class="b-share-icon" src="' + aW + '/static/b-share.png"/>')
                }
                if (p.type === "link" || p.type === "text") {
                    r.push(H + "</span>")
                }
            }
        }
        if (p.type !== "none" && p.type !== "text") {
            r.push("</a>")
        }
        if (p.group) {
            r.push('<span class="b-share__group">')
        }
        for (s = 0, o = F.length; s < o; s++) {
            c = F[s];
            r.push(a2({
                serviceName: c,
                serviceTitle: ax(c, "serviceTitle", "", u),
                link: ax(c, "link", z, u),
                title: ax(c, "title", b, u),
                description: ax(c, "description", h.description || "", u),
                image: ax(c, "image", h.image || "", u),
                lang: a
            }))
        }
        if (p.group) {
            r.push("</span>")
        }
        if (p.type == "block") {
            y = "▼";
            r.push('<a class="b-share__handle b-share__handle_more" title="Ещё" ' + t + '><span class="__b-share__handle_more">' + y + "</span></a>");
            r.push('<i class="b-share-form-button__after"></i>')
        } else {
            if (y) {
                r.push('<a class="b-share__handle b-share_link"' + t + '><span class="b-share__text b-share-pseudo-link">' + y + "</span></a>")
            }
        }
        r.push("</span>");
        if (p.type == "block") {
            r.push("</div>")
        }
        E.innerHTML = r.join("");
        if (h.theme) {
            aA(E, "b-share_theme_" + h.theme.replace(/\"/g, ""))
        }
        aE(E, f, y, p.type === "none");
        if (p.type !== "none") {
            var k = ['<div class="b-share-popup-wrap b-share-popup-wrap_state_hidden' + (h.theme ? " b-share_theme_" + h.theme.replace(/\"/g, "") : "") + '" id="' + B + '-popup"><div class="b-share-popup b-share-popup_down b-share-popup_to-right"><div class="b-share-popup__i' + (j ? " b-share-popup_with-link" : "") + '">'];
            if (G) {
                k.push('<div class="b-share-popup__form b-share-popup__form_html">');
                for (var x in G) {
                    if (G.hasOwnProperty(x)) {
                        k.push('<label class="b-share-popup__input">' + x + '<textarea class="b-share-popup__input__input" cols="10" rows="5">' + G[x] + "</textarea></label>")
                    }
                }
                k.push('<a class="b-share-popup__form__close">' + d.close + "</a></div>")
            }
            k.push('<div class="b-share-popup__main ' + (v ? " b-share-popup_yandexed" : "") + '">');
            if (j) {
                k.push('<label class="b-share-popup__input b-share-popup__input_link">' + d.link + '<input class="b-share-popup__input__input" readonly="readonly" type="text" value="' + am(z) + '" /></label>')
            }
            for (var m in l) {
                if (l.hasOwnProperty(m)) {
                    var C = l[m];
                    o = C.length;
                    if (o) {
                        if (m) {
                            k.push('<div class="b-share-popup__header b-share-popup__header_first">' + m + "</div>")
                        }
                        for (s = 0; s < o; s++) {
                            c = C[s];
                            k.push(aw({
                                serviceName: c,
                                serviceTitle: ax(c, "serviceTitle", "", u),
                                link: ax(c, "link", z, u),
                                title: ax(c, "title", b, u),
                                description: ax(c, "description", h.description || "", u),
                                image: ax(c, "image", h.image || "", u),
                                lang: a
                            }))
                        }
                    }
                }
            }
            if (G) {
                k.push('<div class="b-share-popup__spacer"></div><a class="b-share-popup__item b-share-popup__item_type_code"><span class="b-share-popup__icon"><span class="b-share-icon b-share-icon_html"></span></span><span class="b-share-popup__item__text">' + d["code blog"] + "</span></a>")
            }
            if (v) {
                k.push('<a href="http://api.yandex.ru/share/" class="b-share-popup__yandex">\u042F\u043D\u0434\u0435\u043A\u0441</a>')
            }
            k.push("</div>");
            k.push('</div><div class="b-share-popup__tail"></div></div></div>');
            var A = aX.createElement("div"),
                I;
            A.innerHTML = k.join("");
            I = A.firstChild;
            aX.body.appendChild(I);
            A = null;
            au(I, f)
        }
        return [E, I]
    }
    function aD(a) {
        var b = aX.createElement("script");
        b.setAttribute("src", location.protocol + "//clck.yandex.ru/jclck/dtype=stred/pid=52/cid=70685/path=" + a + "/rnd=" + Math.round(Math.random() * 100000) + "/*" + encodeURIComponent(location.href));
        aN.appendChild(b)
    }
    function ax(f, a, e, d) {
        var b = e,
            c = d[f];
        if (c && a in c) {
            b = c[a]
        }
        return (a == "description" || a == "image" || a == "serviceTitle") ? b : aO(b)
    }
    function aQ(a) {
        return Boolean(aP.custom[a] && aP.custom[a]["title"])
    }
    function a1(c, a) {
        var d = aP.custom[c] || an(c, a);
        var e = "";
        var b = "";
        if (aQ(c)) {
            if (d.icon_from) {
                e += d.icon_from
            } else {
                e += "custom";
                b = ' style="background-image:url(' + d.icon_16 + ");" + (d.icon_16_css ? d.icon_16_css : "") + '"'
            }
        } else {
            e += c
        }
        return '<span class="b-share-icon b-share-icon_' + e + '"' + b + "></span>"
    }
    function an(b, a) {
        a = a || "ru";
        return aP.custom[b] ? aP.custom[b].title : aP[a] && aP[a][b]
    }
    function aV(f, b, g, c, h) {
        h = h ? aO(h) : "";
        c = c ? aO(c) : "";
        var e = aS + "/go.xml?service=" + f;
        if (aQ(f)) {
            var d = aP.custom[f];
            var a = d.url.replace("{link}", b).replace("{title}", g).replace("{description}", c).replace("{image}", h);
            if (d.directLink) {
                return a
            } else {
                return e + "&amp;goto=" + aO(a)
            }
        } else {
            return e + "&amp;url=" + b + "&amp;title=" + g + (c ? "&amp;description=" + c : "") + (h ? "&amp;image=" + h : "")
        }
    }
    function a2(d, b, g, c, a) {
        var f, h;
        if (arguments.length == 1 && typeof arguments[0] == "object") {
            var e = arguments[0];
            h = e.lang;
            f = e.serviceTitle;
            d = e.serviceName;
            b = e.link;
            g = e.title;
            c = e.description;
            a = e.image
        }
        if (d == "pinterest" && !a) {
            return ""
        }
        if (d in aP[h || "ru"] || d in aP.custom) {
            return '<a rel="nofollow" target="_blank" title="' + (f || an(d, h)) + '" class="b-share__handle b-share__link b-share-btn__' + d + '" href="' + aV(d, b, g, c, a) + '" data-service="' + d + '">' + a1(d) + '<span class="b-share-counter"></span></a>'
        } else {
            if (d == "|") {
                return '<span class="b-share__hr"></span>'
            }
        }
    }
    function aw(d, b, g, c, a) {
        var f, h;
        if (arguments.length == 1 && typeof arguments[0] == "object") {
            var e = arguments[0];
            h = e.lang;
            f = e.serviceTitle;
            d = e.serviceName;
            b = e.link;
            g = e.title;
            c = e.description;
            a = e.image
        }
        if (d == "pinterest" && !a) {
            return ""
        }
        if (d in aP[h || "ru"] || d in aP.custom) {
            return '<a rel="nofollow" target="_blank" href="' + aV(d, b, g, c, a) + '" class="b-share-popup__item" data-service="' + d + '"><span class="b-share-popup__icon">' + a1(d) + '</span><span class="b-share-popup__item__text">' + (f || an(d, h)) + "</span></a>"
        } else {
            if (d == "|") {
                return '<div class="b-share-popup__spacer"></div>'
            }
        }
    }
    var aR;

    function at() {
        af.clearTimeout(aR)
    }
    function aI(a) {
        aR = af.setTimeout(a.onDocumentClick, 2000)
    }
    function au(g, d) {
        var a, c, b = ae(g, "b-share-popup__expander")[0],
            h = ae(g, "b-share-popup__item");
        if (b) {
            aU(b.firstChild, "click", aq)
        }
        for (a = 0, c = h.length; a < c; a++) {
            aU(h[a], "click", d.onshare)
        }
        ap(g[az]("input"));
        ap(g[az]("textarea"));
        var f = ae(g, "b-share-popup__item_type_code")[0];
        if (f) {
            var e = ae(g, "b-share-popup__i")[0];
            aU(f, "click", function (i) {
                aA(e, "b-share-popup_show_form_html");
                ar(i)
            });
            aU(ae(g, "b-share-popup__form__close")[0], "click", function (i) {
                aK(e, "b-share-popup_show_form_html");
                ar(i)
            })
        }
        aM(g, at);
        ak(g, d.setPopupCloseTimeout)
    }
    function ap(d) {
        var a = 0,
            b = d.length,
            c;
        for (; a < b; a++) {
            c = d[a];
            aU(c, "click", (function (e) {
                return function () {
                    e.select()
                }
            })(c))
        }
    }
    function aE(h, f, i, c) {
        var d = 1,
            e, g = ae(h, "b-share__handle");
        var j = g.length;
        var a = j;
        if (c) {
            d = 0
        } else {
            var b = g[0];
            if (i) {
                b = g[j - 1];
                a--
            }
            aU(b, "click", f.toggleOpenMode);
            aM(b, at);
            ak(b, f.setPopupCloseTimeout)
        }
        for (d, e = a; d < e; d++) {
            aU(g[d], "click", f.onshare)
        }
    }
    function ah(a) {
        var c, d, b;
        if (!(c = a.currentTarget)) {
            b = a.target || a.srcElement;
            if (!(c = aF(b, "b-share__handle"))) {
                c = aF(b, "b-share-popup__item")
            }
        }
        if (c && (d = c.getAttribute("data-service"))) {
            aD(d);
            switch (d) {
                case "facebook":
                    aC(a, c, 800, 500);
                    break;
                case "moimir":
                    aC(a, c, 560, 400);
                    break;
                case "twitter":
                    aC(a, c, 650, 520);
                    break;
                case "vkontakte":
                    aC(a, c, 550, 420);
                    break;
                case "odnoklassniki":
                    aC(a, c, 560, 370);
                    break;
                case "gplus":
                    aC(a, c, 560, 370);
                    break;
                case "surfingbird":
                    aC(a, c, 500, 170);
                    break
            }
        }
        return d
    }
    function aC(a, c, b, d) {
        ar(a);
        window.open(c.href, "yashare_popup", "scrollbars=1,resizable=1,menubar=0,toolbar=0,status=0,left=" + ((screen.width - b) / 2) + ",top=" + ((screen.height - d) / 2) + ",width=" + b + ",height=" + d).focus()
    }
    function aq() {
        var a = aF(this, "b-share-popup__i");
        aZ(a, "b-share-popup_full")
    }
    function a5(a, c) {
        if (a && typeof a !== "number") {
            var b = a.which ? a.which : 1;
            if (b !== 1 || aF(a.target || a.srcElement, "b-share-popup-wrap")) {
                return
            }
        }
        if (av) {
            c.closePopup(av.id)
        }
    }
    function a6(g, c) {
        g = g.replace("-popup", "");
        var d = aX.getElementById(g),
            e = aX.getElementById(g + "-popup"),
            f = ae(e, "b-share-popup__input__input");
        aK(d, "b-share-popup_opened");
        aK(d, "b-share-form-button_state_pressed");
        aA(e, "b-share-popup-wrap_state_hidden");
        aK(e, "b-share-popup-wrap_state_visibale");
        e.style.cssText = "";
        if (f) {
            for (var a = 0, b = f.length; a < b; a++) {
                f[a].style.cssText = ""
            }
        }
        e.firstChild.className = "b-share-popup";
        a4(aX, "click", c.onDocumentClick);
        av = null;
        c.onclose(c._this)
    }
    function aB(r, m, a) {
        a = a || {};
        var n = a.hDirection || r.getAttribute("data-hdirection"),
            f = a.vDirection || r.getAttribute("data-vdirection"),
            c = aX.getElementById(r.id + "-popup"),
            o = c.firstChild,
            p = ae(c, "b-share-popup__input__input"),
            q = al(),
            e, d, j = aL(r);
        if (n === "left" || n === "right") {
            e = n
        } else {
            e = (j.left - Math.max(aY.scrollLeft, aX.body.scrollLeft)) >= q.width / 2 ? "left" : "right"
        }
        if (f === "up" || f === "down") {
            d = f
        } else {
            d = (j.top - Math.max(aY.scrollTop, aX.body.scrollTop)) >= q.height / 2 ? "up" : "down"
        }
        m.onDocumentClick();
        var l = ae(c, "b-share-popup__tail")[0],
            h = Math.round(r.offsetWidth / 2),
            k = a.width || o.offsetWidth,
            s = Math.round(k / 2);
        if (j.left - (s - h) > 5) {
            j.left -= Math.round(s - h);
            var b = Math.max(j.left + k - aX.body.offsetWidth, 0);
            j.left -= b;
            h = s - 10 + b
        }
        j.top += (d === "up" ? -9 : 9 + r.offsetHeight);
        c.style.cssText = "top:" + (a.top || j.top) + "px;left:" + (a.left || j.left) + "px;width:" + k + "px";
        l.style.cssText = "left: " + h + "px";
        if (p) {
            for (var g = 0, i = p.length; g < i; g++) {
                p[g].style.width = (k - 30) + "px"
            }
        }
        o.className = "b-share-popup b-share-popup_" + d + " b-share-popup_to-" + e;
        aA(c, "b-share-popup-wrap_state_visibale");
        aK(c, "b-share-popup-wrap_state_hidden");
        aA(r, "b-share-popup_opened");
        aA(r, "b-share-form-button_state_pressed");
        o.firstChild.style.zoom = 1;
        af.setTimeout(function () {
            aU(aX, "click", m.onDocumentClick)
        }, 50);
        aD("share");
        av = c;
        m.onopen(m._this)
    }
    function aG(a, b) {
        var c = a.currentTarget || aF(a.target || a.srcElement, "b-share__handle");
        if (ai(c, "b-share-popup_opened")) {
            b.closePopup(c.id, b)
        } else {
            if (b.onbeforeopen(b._this) !== false) {
                b.openPopup(c, b)
            }
        }
        ar(a);
        ao(a)
    }
    if (!("Ya" in af)) {
        af.Ya = {}
    }
    Ya.share = function (b) {
        if (!(this instanceof Ya.share)) {
            return new Ya.share(b)
        }
        if (b) {
            aW = b.STATIC_HOST || aW;
            aS = b.SHARE_HOST || aS
        }
        this._loaded = false;
        var a = this,
            d = b.onshare || aT,
            c = b.onBeforeShare || null,
            e = {
                onready: b.onready || b.onload || aT,
                onbeforeclose: b.onbeforeclose || aT,
                onclose: b.onclose || aT,
                onbeforeopen: b.onbeforeopen || aT,
                onopen: b.onopen || aT,
                onshare: function (g) {
                    if (c) {
                        try {
                            if (c(a) === false) {
                                g.preventDefault();
                                return false
                            }
                        } catch (h) {
                            g.preventDefault();
                            return false
                        }
                    }
                    var f = ah(g);
                    if (f) {
                        d(f, a)
                    }
                },
                _this: a
            };
        e.toggleOpenMode = function (f) {
            aG(f, e)
        };
        e.closePopup = function (f) {
            at();
            if (e.onbeforeclose(a) !== false) {
                a6(f, e)
            }
        };
        e.openPopup = function (f, g) {
            aB(f, g)
        };
        e.onDocumentClick = function (f) {
            a5(f, e)
        };
        e.setPopupCloseTimeout = function () {
            aI(e)
        };
        this.closePopup = function () {
            a6(this._popup.id, e)
        };
        this.openPopup = function (f) {
            aB(ae(this._block, "b-share__handle")[0], e, f)
        };
        a0(function () {
            var f = ay(b, e);
            b = null;
            if (!f) {
                return
            }
            a._block = f[0];
            a._popup = f[1];
            a._loaded = true;
            e.onready(a)
        })
    };
    Ya.share.prototype = {
        updateShareLink: function (e, d, b) {
            if (!this._loaded) {
                return this
            }
            var h, i, a, g, c = "",
                j = "";
            if (arguments.length == 1 && typeof arguments[0] == "object") {
                var f = arguments[0];
                e = f.link || af.location.toString();
                d = f.title || aX.title;
                c = f.description || "";
                j = f.image || "";
                b = f.serviceSpecific || {}
            } else {
                e = e || af.location.toString();
                d = d || aX.title;
                b = b || {}
            }
            a = ae(this._block, "b-share__link");
            for (h = 0, i = a.length; h < i; h++) {
                g = a[h].getAttribute("data-service");
                a[h].href = aV(g, ax(g, "link", e, b), ax(g, "title", d, b), ax(g, "description", c, b), ax(g, "image", j, b))
            }
            if (this._popup) {
                a = ae(this._popup, "b-share-popup__item");
                for (h = 0, i = a.length; h < i; h++) {
                    g = a[h].getAttribute("data-service");
                    if (g) {
                        a[h].href = aV(g, ax(g, "link", e, b), ax(g, "title", d, b), ax(g, "description", c, b), ax(g, "image", j, b))
                    }
                }
                a = ae(this._popup, "b-share-popup__input__input");
                for (var h = a.length - 1; h >= 0; h--) {
                    if (a[h] && a[h].tagName.toLowerCase() !== "textarea") {
                        a[h].value = e
                    }
                }
            }
            return this
        },
        updateCustomService: function (b, a) {
            if (aP.custom[b] && aP.custom[b].url) {
                aP.custom[b].url = a;
                this.updateShareLink();
                return true
            }
            return false
        }
    };
    a0(function () {
        var g = ae(aX.body, "yashare-auto-init"),
            c = 0,
            e = g.length,
            b, d, f, a;
        for (; c < e; c++) {
            b = g[c];
            d = b.getAttribute("data-yashareQuickServices");
            f = b.getAttribute("data-yasharePopupServices");
            if (typeof d === "string") {
                d = d.split(",")
            } else {
                d = null
            }
            a = {
                element: b,
                theme: b.getAttribute("data-yashareTheme"),
                l10n: b.getAttribute("data-yashareL10n"),
                image: b.getAttribute("data-yashareImage"),
                link: b.getAttribute("data-yashareLink"),
                title: b.getAttribute("data-yashareTitle"),
                description: b.getAttribute("data-yashareDescription"),
                elementStyle: {
                    type: b.getAttribute("data-yashareType"),
                    quickServices: d
                }
            };
            if (f && typeof f === "string") {
                f = f.split(",");
                a.popupStyle = {
                    blocks: f
                }
            }
            new Ya.share(a)
        }
    });

    function am(a) {
        return (a || "").replace(/</g, "&lt;").replace(/"/g, "&quot;")
    }
})(window, document);