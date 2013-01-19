/**
 * PROGOROD Javascript API library
 * http://www.pro-gorod.ru
 *
 * Copyright 2011, LLC ProGorod
 * All rights reserved
 *
 * Date: November 20 12:24:35 2012
 *
 * @verison 0.1.8
 * @author Murzina A.A.
 */
function PGmap(a, b) {
    var c;
    b === void 0 && (b = {});
    if (a && a.tagName) {
        c = PGmap.Base.create("PGmap");
        c.style.position = "relative";
        c.style.overflow = "hidden";
        c.style.height = "100%";
        a.appendChild(c);
        for (var d = JSON.parse('{"projection":"EPSG:3395","tilesize":256,"bbox":{"minx":-20037508.343,"miny":-19930481.933,"maxx":20037508.343,"maxy":20144534.753},"resolutions":[78271.516964844,39135.758482422,19567.879241211,9783.9396206055,4891.9698103027,2445.9849051514,1222.9924525757,611.49622628784,305.74811314392,152.87405657196,76.43702828598,38.21851414299,19.109257071495,9.5546285357475,4.7773142678738,2.3886571339369,1.19432856696841]}'),
        e = {
            bbox: d.bbox,
            resolutions: [],
            count: []
        }, f = 0, l = 2, m = d.resolutions.length; f < m;) e.resolutions.push(d.resolutions[f]), e.count.push(l), f++, l *= 2;
        f = PGmap.Base.createAbs("PGmap-layer-container");
        f.style.position = "relative";
        f.style.left = f.style.top = 0;
        var d = {}, h = new PGmap.Globals(this, a, c, f, d, e, b.roundRobin || null, b.minZoom, b.maxZoom, b.noKinematicDrop, b.noSmoothZoom, null, b.lang);
        b.coord && b.coord instanceof PGmap.Coord && h.setCoords(b.coord.lon, b.coord.lat);
        b.zoom && h.setZoom(b.zoom);
        PGmap.Utils.addLib("css", h.mainCss);
        PGmap.Request.flashRequest = new PGmap.CrossDomainAjax.flashRequest;
        c = new PGmap.Services(h);
        this.layers = new PGmap.Layers(h);
        this.element = a;
        this.geometry = this.layers.geometry;
        this.controls = new PGmap.Controls(h);
        b.offsets && this.layers.setOffSets(b.offsets);
        d.layers = this.layers;
        d.controls = this.controls;
        d.geometry = this.geometry;
        delete this.layers.geometry;
        this.route = new PGmap.Router(h, this.geometry);
        PGmap.Ruler && (this.ruler = new PGmap.Ruler(h, this.geometry));
        d.ruler = this.ruler;
        this.setCenter = this.layers.setCenter;
        this.setCenterFast = this.layers.setCenterFast;
        this.setCenterByBbox = this.layers.setCenterByBbox;
        this.setCenterInView = this.layers.setCenterInView;
        this.testCoordsOnMovie = this.layers.willSetMovie;
        this.setOffsets = this.layers.setOffSets;
        this.getBboxCoords = function (a) {
            return {
                bbox: h.getBbox(a),
                coord: h.getCoords()
            }
        };
        this.screenShot = {};
        this.screenShot.print = function () {};
        this.screenShot.getScreen = function () {};
        this.ajax = (new PGmap.CrossDomainAjax(new HTMLCrossDomainAjax)).request;
        this.search = c.search;
        this.prepareQ = c.prepareQ;
        this.geoParser = PGmap.GeoParser;
        this.globals = h;
        this.balloonGeom = new PGmap.BalloonGeom({
            isHidden: !1,
            isClosing: !0,
            content: ""
        });
        this.balloon = new PGmap.Balloon({
            isHidden: !1,
            isClosing: !0,
            content: ""
        });
        this.geometry.add(this.balloon)
    } else throw "Map element is not defined";
}
PGmap.config = {};
PGmap.DEBUG = 0;
(typeof Crypto == "undefined" || !Crypto.util) && function () {
    var a = window.Crypto = {}, b = a.util = {
        rotl: function (a, b) {
            return a << b | a >>> 32 - b
        },
        rotr: function (a, b) {
            return a << 32 - b | a >>> b
        },
        endian: function (a) {
            if (a.constructor == Number) return b.rotl(a, 8) & 16711935 | b.rotl(a, 24) & 4278255360;
            for (var c = 0; c < a.length; c++) a[c] = b.endian(a[c]);
            return a
        },
        randomBytes: function (a) {
            for (var b = []; a > 0; a--) b.push(Math.floor(Math.random() * 256));
            return b
        },
        bytesToWords: function (a) {
            for (var b = [], c = 0, l = 0; c < a.length; c++, l += 8) b[l >>> 5] |= (a[c] & 255) << 24 - l % 32;
            return b
        },
        wordsToBytes: function (a) {
            for (var b = [], c = 0; c < a.length * 32; c += 8) b.push(a[c >>> 5] >>> 24 - c % 32 & 255);
            return b
        },
        bytesToHex: function (a) {
            for (var b = [], c = 0; c < a.length; c++) b.push((a[c] >>> 4).toString(16)), b.push((a[c] & 15).toString(16));
            return b.join("")
        },
        hexToBytes: function (a) {
            for (var b = [], c = 0; c < a.length; c += 2) b.push(parseInt(a.substr(c, 2), 16));
            return b
        },
        bytesToBase64: function (a) {
            if (typeof btoa == "function") return btoa(c.bytesToString(a));
            for (var b = [], f = 0; f < a.length; f += 3) for (var l = a[f] << 16 | a[f + 1] << 8 | a[f + 2], m = 0; m < 4; m++) f * 8 + m * 6 <= a.length * 8 ? b.push("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".charAt(l >>> 6 * (3 - m) & 63)) : b.push("=");
            return b.join("")
        },
        base64ToBytes: function (a) {
            if (typeof atob == "function") return c.stringToBytes(atob(a));
            for (var a = a.replace(/[^A-Z0-9+\/]/ig, ""), b = [], f = 0, l = 0; f < a.length; l = ++f % 4) l != 0 && b.push(("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".indexOf(a.charAt(f - 1)) & Math.pow(2, -2 * l + 8) - 1) << l * 2 | "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".indexOf(a.charAt(f)) >>> 6 - l * 2);
            return b
        }
    }, a = a.charenc = {};
    a.UTF8 = {
        stringToBytes: function (a) {
            return c.stringToBytes(unescape(encodeURIComponent(a)))
        },
        bytesToString: function (a) {
            return decodeURIComponent(escape(c.bytesToString(a)))
        }
    };
    var c = a.Binary = {
        stringToBytes: function (a) {
            for (var b = [], c = 0; c < a.length; c++) b.push(a.charCodeAt(c) & 255);
            return b
        },
        bytesToString: function (a) {
            for (var b = [], c = 0; c < a.length; c++) b.push(String.fromCharCode(a[c]));
            return b.join("")
        }
    }
}();
(function () {
    var a = Crypto,
        b = a.util,
        c = a.charenc,
        d = c.UTF8,
        e = c.Binary,
        f = a.MD5 = function (a, c) {
            var d = b.wordsToBytes(f._md5(a));
            return c && c.asBytes ? d : c && c.asString ? e.bytesToString(d) : b.bytesToHex(d)
        };
    f._md5 = function (a) {
        a.constructor == String && (a = d.stringToBytes(a));
        for (var c = b.bytesToWords(a), h = a.length * 8, a = 1732584193, j = -271733879, g = -1732584194, e = 271733878, k = 0; k < c.length; k++) c[k] = (c[k] << 8 | c[k] >>> 24) & 16711935 | (c[k] << 24 | c[k] >>> 8) & 4278255360;
        c[h >>> 5] |= 128 << h % 32;
        c[(h + 64 >>> 9 << 4) + 14] = h;
        for (var h = f._ff, o = f._gg, p = f._hh, q = f._ii, k = 0; k < c.length; k += 16) var r = a,
            u = j,
            y = g,
            x = e,
            a = h(a, j, g, e, c[k + 0], 7, -680876936),
            e = h(e, a, j, g, c[k + 1], 12, -389564586),
            g = h(g, e, a, j, c[k + 2], 17, 606105819),
            j = h(j, g, e, a, c[k + 3], 22, -1044525330),
            a = h(a, j, g, e, c[k + 4], 7, -176418897),
            e = h(e, a, j, g, c[k + 5], 12, 1200080426),
            g = h(g, e, a, j, c[k + 6], 17, -1473231341),
            j = h(j, g, e, a, c[k + 7], 22, -45705983),
            a = h(a, j, g, e, c[k + 8], 7, 1770035416),
            e = h(e, a, j, g, c[k + 9], 12, -1958414417),
            g = h(g, e, a, j, c[k + 10], 17, -42063),
            j = h(j, g, e, a, c[k + 11], 22, -1990404162),
            a = h(a, j, g, e, c[k + 12], 7, 1804603682),
            e = h(e, a,
            j, g, c[k + 13], 12, -40341101),
            g = h(g, e, a, j, c[k + 14], 17, -1502002290),
            j = h(j, g, e, a, c[k + 15], 22, 1236535329),
            a = o(a, j, g, e, c[k + 1], 5, -165796510),
            e = o(e, a, j, g, c[k + 6], 9, -1069501632),
            g = o(g, e, a, j, c[k + 11], 14, 643717713),
            j = o(j, g, e, a, c[k + 0], 20, -373897302),
            a = o(a, j, g, e, c[k + 5], 5, -701558691),
            e = o(e, a, j, g, c[k + 10], 9, 38016083),
            g = o(g, e, a, j, c[k + 15], 14, -660478335),
            j = o(j, g, e, a, c[k + 4], 20, -405537848),
            a = o(a, j, g, e, c[k + 9], 5, 568446438),
            e = o(e, a, j, g, c[k + 14], 9, -1019803690),
            g = o(g, e, a, j, c[k + 3], 14, -187363961),
            j = o(j, g, e, a, c[k + 8], 20, 1163531501),
            a = o(a, j, g, e, c[k + 13], 5, -1444681467),
            e = o(e, a, j, g, c[k + 2], 9, -51403784),
            g = o(g, e, a, j, c[k + 7], 14, 1735328473),
            j = o(j, g, e, a, c[k + 12], 20, -1926607734),
            a = p(a, j, g, e, c[k + 5], 4, -378558),
            e = p(e, a, j, g, c[k + 8], 11, -2022574463),
            g = p(g, e, a, j, c[k + 11], 16, 1839030562),
            j = p(j, g, e, a, c[k + 14], 23, -35309556),
            a = p(a, j, g, e, c[k + 1], 4, -1530992060),
            e = p(e, a, j, g, c[k + 4], 11, 1272893353),
            g = p(g, e, a, j, c[k + 7], 16, -155497632),
            j = p(j, g, e, a, c[k + 10], 23, -1094730640),
            a = p(a, j, g, e, c[k + 13], 4, 681279174),
            e = p(e, a, j, g, c[k + 0], 11, -358537222),
            g = p(g, e, a, j, c[k + 3], 16, -722521979),
            j = p(j, g, e, a, c[k + 6], 23, 76029189),
            a = p(a, j, g, e, c[k + 9], 4, -640364487),
            e = p(e, a, j, g, c[k + 12], 11, -421815835),
            g = p(g, e, a, j, c[k + 15], 16, 530742520),
            j = p(j, g, e, a, c[k + 2], 23, -995338651),
            a = q(a, j, g, e, c[k + 0], 6, -198630844),
            e = q(e, a, j, g, c[k + 7], 10, 1126891415),
            g = q(g, e, a, j, c[k + 14], 15, -1416354905),
            j = q(j, g, e, a, c[k + 5], 21, -57434055),
            a = q(a, j, g, e, c[k + 12], 6, 1700485571),
            e = q(e, a, j, g, c[k + 3], 10, -1894986606),
            g = q(g, e, a, j, c[k + 10], 15, -1051523),
            j = q(j, g, e, a, c[k + 1], 21, -2054922799),
            a = q(a, j, g, e, c[k + 8], 6, 1873313359),
            e = q(e, a, j, g, c[k + 15], 10, -30611744),
            g = q(g, e, a, j, c[k + 6], 15, -1560198380),
            j = q(j, g, e, a, c[k + 13], 21, 1309151649),
            a = q(a, j, g, e, c[k + 4], 6, -145523070),
            e = q(e, a, j, g, c[k + 11], 10, -1120210379),
            g = q(g, e, a, j, c[k + 2], 15, 718787259),
            j = q(j, g, e, a, c[k + 9], 21, -343485551),
            a = a + r >>> 0,
            j = j + u >>> 0,
            g = g + y >>> 0,
            e = e + x >>> 0;
        return b.endian([a, j, g, e])
    };
    f._ff = function (a, b, c, d, g, e, f) {
        a = a + (b & c | ~b & d) + (g >>> 0) + f;
        return (a << e | a >>> 32 - e) + b
    };
    f._gg = function (a, b, c, d, e, f, k) {
        a = a + (b & d | c & ~d) + (e >>> 0) + k;
        return (a << f | a >>> 32 - f) + b
    };
    f._hh = function (a, b, c, d, e, f, k) {
        a = a + (b ^ c ^ d) + (e >>> 0) + k;
        return (a << f | a >>> 32 - f) + b
    };
    f._ii = function (a, b, c, d, e, f, k) {
        a = a + (c ^ (b | ~d)) + (e >>> 0) + k;
        return (a << f | a >>> 32 - f) + b
    };
    f._blocksize = 16;
    f._digestsize = 16
})();
PGmap.Utils = {
    degRad: function (a) {
        return a * 0.017453292519943295
    },
    degDecimal: function (a) {
        return a * 57.29577951308232
    },
    mercX: function (a) {
        return 6378137 * this.degRad(a)
    },
    fromMercX: function (a) {
        return this.degDecimal(a / 6378137)
    },
    mercY: function (a) {
        a > 89.5 && (a = 89.5);
        a < -89.5 && (a = -89.5);
        var a = this.degRad(a),
            b = 0.08181919092890692 * Math.sin(a),
            a = Math.tan(0.5 * (1.5707963267948966 - a)) / Math.pow((1 - b) / (1 + b), 0.04090959546445346);
        return 0 - 6378137 * Math.log(a)
    },
    fromMercY: function (a) {
        var a = Math.exp(-a / 6378137),
            b = 1.5707963267948966 - 2 * Math.atan(a),
            c = 15,
            d;
        for (d = 0.1; Math.abs(d) > 1.0E-7 && --c > 0;) d = 0.08181919092890692 * Math.sin(b), d = 1.5707963267948966 - 2 * Math.atan(a * Math.pow((1 - d) / (1 + d), 0.04090959546445346)) - b, b += d;
        return this.degDecimal(b)
    },
    timeToDegreesLon: function (a) {
        var b = new Number(a.substr(0, 3)),
            a = new Number(a.substr(3));
        return b + a / 60
    },
    timeToDegreesLat: function (a) {
        var b = new Number(a.substr(0, 2)),
            a = new Number(a.substr(2));
        return b + a / 60
    },
    distVincenty: function (a, b, c, d) {
        var e, a = this.degRad(a),
            b = this.degRad(b);
        e = this.degRad(c);
        var c = this.degRad(d),
            f, l, m, h, j, g, d = 1 / 298.257223563,
            a = e - a;
        e = Math.atan((1 - d) * Math.tan(b));
        var n = Math.atan((1 - d) * Math.tan(c)),
            b = Math.sin(e),
            c = Math.cos(e);
        e = Math.sin(n);
        for (var n = Math.cos(n), k = a, o = 2 * Math.PI, p = 20; Math.abs(k - o) > 1.0E-12 && --p > 0;) {
            h = Math.sin(k);
            f = Math.cos(k);
            g = Math.sqrt(n * h * n * h + (c * e - b * n * f) * (c * e - b * n * f));
            if (!g) return 0;
            var q = b * e + c * n * f;
            f = Math.atan2(g, q);
            l = c * n * h / g;
            m = 1 - l * l;
            h = q - 2 * b * e / m;
            isNaN(h) && (h = 0);
            j = d / 16 * m * (4 + d * (4 - 3 * m));
            o = k;
            k = a + (1 - j) * d * l * (f + j * g * (h + j * q * (-1 + 2 * h * h)))
        }
        if (p == 0) return NaN;
        m = m * 2.723316066819453E11 / 4.0408299984087055E13;
        d = m / 1024 * (256 + m * (-128 + m * (74 - 47 * m)));
        return g = (6356752.3142 * (1 + m / 16384 * (4096 + m * (-768 + m * (320 - 175 * m)))) * (f - d * g * (h + d / 4 * (q * (-1 + 2 * h * h) - d / 6 * h * (-3 + 4 * g * g) * (-3 + 4 * h * h))))).toFixed(3)
    },
    distVincentyMerc: function (a, b, c, d) {
        return this.distVincenty(this.fromMercX(a), this.fromMercY(b), this.fromMercX(c), this.fromMercY(d))
    },
    isFunc: function (a) {
        return a && (typeof a == "function" || typeof window[a] == "function")
    },
    stringToXML: function (a) {
        var b;
        b = window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest;
        b.open("GET", a, !1);
        b.send();
        return b.responseXML
    },
    browser: function () {
        var a = {
            init: function () {
                this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
                this.version = this.searchVersion(navigator.userAgent) || this.searchVersion(navigator.appVersion) || "an unknown version";
                this.OS = this.searchString(this.dataOS) || "an unknown OS"
            },
            searchString: function (a) {
                for (var c = 0; c < a.length; c++) {
                    var d = a[c].string,
                        e = a[c].prop;
                    this.versionSearchString = a[c].versionSearch || a[c].identity;
                    if (d) {
                        if (d.indexOf(a[c].subString) != -1) return a[c].identity
                    } else if (e) return a[c].identity
                }
            },
            searchVersion: function (a) {
                var c = a.indexOf(this.versionSearchString);
                return c == -1 ? void 0 : parseFloat(a.substring(c + this.versionSearchString.length + 1))
            },
            dataBrowser: [{
                string: navigator.userAgent,
                subString: "Chrome",
                identity: "Chrome"
            }, {
                string: navigator.userAgent,
                subString: "OmniWeb",
                versionSearch: "OmniWeb/",
                identity: "OmniWeb"
            }, {
                string: navigator.vendor,
                subString: "Apple",
                identity: "Safari",
                versionSearch: "Version"
            }, {
                prop: window.opera,
                identity: "Opera"
            }, {
                string: navigator.vendor,
                subString: "iCab",
                identity: "iCab"
            }, {
                string: navigator.vendor,
                subString: "KDE",
                identity: "Konqueror"
            }, {
                string: navigator.userAgent,
                subString: "Firefox",
                identity: "Firefox"
            }, {
                string: navigator.vendor,
                subString: "Camino",
                identity: "Camino"
            }, {
                string: navigator.userAgent,
                subString: "Netscape",
                identity: "Netscape"
            }, {
                string: navigator.userAgent,
                subString: "MSIE",
                identity: "Explorer",
                versionSearch: "MSIE"
            }, {
                string: navigator.userAgent,
                subString: "Gecko",
                identity: "Mozilla",
                versionSearch: "rv"
            }, {
                string: navigator.userAgent,
                subString: "Mozilla",
                identity: "Netscape",
                versionSearch: "Mozilla"
            }],
            dataOS: [{
                string: navigator.platform,
                subString: "Win",
                identity: "Windows"
            }, {
                string: navigator.platform,
                subString: "Mac",
                identity: "Mac"
            }, {
                string: navigator.userAgent,
                subString: "iPhone",
                identity: "iPhone/iPod"
            }, {
                string: navigator.platform,
                subString: "Linux",
                identity: "Linux"
            }]
        };
        a.init();
        return a
    }(),
    getIndexOf: function (a, b) {
        if (!a) return -1;
        if (a.indexOf) return a.indexOf(b);
        else {
            for (var c = a.length; c--;) if (b === a[c]) return c;
            return -1
        }
    },
    myRound: function (a,
    b) {
        return Math.ceil(a / b) * b
    },
    getElementsByClassName: function (a, b) {
        if (!a || !b) return [];
        if (!document.getElementsByClassName) {
            for (var c = b.getElementsByTagName("*"), d = [], e = RegExp("(^|\\b)" + a + "(\\b|&)"), f = 0, l = c.length; f < l; f++) e.test(c[f].className) && d.push(c[f]);
            return d
        }
        return b.getElementsByClassName(a)
    },
    extendMethods: function (a, b) {
        if (a && b) for (var c in b) a[c] = b[c]
    },
    addMethod: function (a, b, c) {
        var d = a[b];
        a[b] = function () {
            if (c.length == arguments.length) return c.apply(this, arguments);
            else if (typeof d == "function") return d.apply(this,
            arguments)
        }
    },
    hide: function (a) {
        a.style.display = "none";
        return a
    },
    show: function (a) {
        a.style.display = "block";
        return a
    },
    getStyle: function (a, b) {
        if (a && b) return (a.currentStyle || window.getComputedStyle(a, null))[b]
    },
    setStyle: function (a, b) {
        var c, d = "";
        if (a instanceof a.tagName && b) {
            for (c in b) d += "; " + c + ": " + b[c] + ";";
            a.style.cssText += d
        }
    },
    setClass: function (a, b, c) {
        if (a && a.tagName && (b || c)) b = b || [], c = c || [], a.className = ((c.length ? a.className.replace(RegExp("(^|\\s)(" + c.join("|") + ")($|\\s)", "g"), " ") : a.className) + (b.length ?
            " " + b.join(" ") : "")).replace(/\s{2,}/g, " ")
    },
    hasClass: function (a, b) {
        return RegExp("(\\s|^)" + b + "(\\s|$)").test(a.className)
    },
    addClass: function (a, b) {
        this.hasClass(a, b) || (a.className += (a.className ? " " : "") + b)
    },
    removeClass: function (a, b) {
        if (this.hasClass(a, b)) a.className = a.className.replace(RegExp("(\\s|^)" + b + "(\\s|$)"), " ").replace(/^\s+|\s+$/g, "")
    },
    getSize: function (a) {
        return a ? {
            width: a.offsetWidth,
            height: a.offsetHeight
        } : null
    },
    getRealSize: function (a) {
        var b = this.getSize(a),
            c, d, e = ["paddingLeft", "paddingRight",
                "borderLeftWidth", "borderRightWidth"],
            f = ["paddingTop", "paddingBottom", "borderTopWidth", "borderBottomWidth"];
        for (c = 0; c < e.length; c++) d = parseInt(this.getStyle(a, e[c])), b.width -= isNaN(d) ? 0 : d;
        for (c = 0; c < f.length; c++) d = parseInt(this.getStyle(a, f[c])), b.height -= isNaN(d) ? 0 : d;
        return b
    },
    getOffset: function (a) {
        return a.getBoundingClientRect ? PGmap.Utils.getOffsetRect(a) : PGmap.Utils.getOffsetSum(a)
    },
    getOffsetRect: function (a) {
        var a = a.getBoundingClientRect(),
            b = document.body,
            c = document.documentElement,
            d = a.top + (window.pageYOffset || c.scrollTop || b.scrollTop) - (c.clientTop || b.clientTop || 0);
        return {
            left: Math.round(a.left + (window.pageXOffset || c.scrollLeft || b.scrollLeft) - (c.clientLeft || b.clientLeft || 0)),
            top: Math.round(d)
        }
    },
    getOffsetSum: function (a) {
        for (var b = 0, c = 0; a;) c += parseInt(a.offsetLeft), b += parseInt(a.offsetTop), a = a.offsetParent;
        return {
            left: c,
            top: b
        }
    },
    pxToNum: function (a) {
        return Number(a.replace(/px/, ""))
    },
    getPosition: function (a) {
        return a && this.getStyle(a, "position").match(/[absolute|relative|fixed]/) ? {
            left: PGmap.Utils.pxToNum(a.style.left),
            top: PGmap.Utils.pxToNum(a.style.top)
        } : !1
    },
    getParPosition: function (a) {
        if (a && a.parentNode) {
            var b = PGmap.Utils.getPosition(a),
                a = PGmap.Utils.getPosition(a.parentNode);
            return {
                left: b.left - a.left,
                top: b.top - a.top
            }
        }
        return !1
    },
    destroyChildren: function (a) {
        for (; a.firstChild;) a.removeChild(a.firstChild)
    },
    hasDescendant: function (a, b) {
        for (; b && document.body !== b;) {
            if (b === a) return !0;
            b = b.parentNode
        }
        return !1
    },
    circ: function (a) {
        return 1 - Math.sin(Math.acos(a))
    },
    square: function (a) {
        return a * a
    },
    animate: function (a) {
        if (!a.duration) a.duration = 200;
        if (!a.func) a.func = function (a) {
            return a * a
        };
        if (a.elem && a.from && a.to && a.func) {
            var b = a.duration,
                c = +new Date,
                d = "";
            setTimeout(function () {
                var e = (new Date - c) / b,
                    f;
                for (f in a.from) if (a.from[f] != void 0 && a.to[f] != void 0) {
                    var l = (a.to[f] - a.from[f]) * Math.min(a.func(e), 1) + a.from[f];
                    d += f + ": " + l + "px;"
                }
                a.elem.style.cssText += "; " + d;
                e < 1 ? setTimeout(arguments.callee, 10) : a.callBack && a.callBack()
            }, 10)
        }
    },
    elementContains: function (a, b) {
        var c = a.childNodes,
            d;
        for (d in c) if (b === c[d]) return !0;
        return !1
    },
    inArray: function (a, b) {
        for (var c = 0, d = a.length; c < d; c++) if (a[c] == b) return !0;
        return !1
    },
    arrayIndexOf: function (a, b) {
        for (var c = 0, d = a.length; c < d; c++) if (a[c] == b) return c;
        return -1
    },
    addLib: function (a, b, c) {
        if (!a || !b) return !1;
        var d;
        switch (a) {
            case "css":
                d = document.createElement("link");
                d.rel = "stylesheet";
                d.href = b;
                d.media = "all";
                break;
            case "js":
                d = document.createElement("script");
                d.src = b;
                break;
            default:
                return !1
        }
        if (c && PGmap.Utils.isFunc(c)) d.onreadystatechange = function () {
            if (d.readyState == "loaded" || d.readyState == "complete") c(), d.onreadystatechange = null
        }, d.onload = function () {
            c();
            d.onload = null
        };
        document.getElementsByTagName("head")[0].appendChild(d)
    },
    roundPres: function (a, b) {
        return a && parseFloat(a.toPrecision(b || 2))
    },
    formatString: function (a, b, c) {
        switch (c) {
            default: a = PGmap.Utils.fromMercX(a),
            b = PGmap.Utils.fromMercY(b),
            b = PGmap.Utils.formatDegrees(Math.abs(b)) + (b > 0 ? " N, " : " S, ") + PGmap.Utils.formatDegrees(Math.abs(a)) + (a > 0 ? " E" : " W")
        }
        return b
    },
    formatDegrees: function (a) {
        var a = Math.round(1E7 * a) / 1E7 + 1.0E-8,
            b = Math.floor(a),
            c = Math.floor(60 * (a - b)),
            a = 3600 * (a - b - c / 60);
        return PGmap.Utils.zeroDigit2(b) + "\u00b0" + PGmap.Utils.zeroDigit2(c) + "'" + PGmap.Utils.zeroDigit2(a).substr(0, 2) + '"'
    },
    zeroDigit2: function (a) {
        return (a < 10 ? "0" : "") + a
    },
    clone: function (a) {
        PGmap.Utils.Clone.prototype = a;
        return new PGmap.Utils.Clone
    },
    deepClone: function (a) {
        if (!a || "object" !== typeof a) return a;
        var b = "function" === typeof a.pop ? [] : {}, c, d;
        for (c in a) a.hasOwnProperty(c) && (d = a[c], b[c] = d && "object" === typeof d ? PGmap.Utils.deepClone(d) : d);
        return b
    }
};
PGmap.Utils.isChild = PGmap.Utils.elementContains;
PGmap.Utils.create = PGmap.Utils.addLib;
PGmap.Utils.extend = PGmap.Utils.extendMethods;
PGmap.Utils.getPosIntoView = PGmap.Utils.getOffsetRect;
PGmap.Utils.delegateEv = function (a, b, c) {
    var d = a == "load" && c == null;
    c == null && (c = document);
    document.addEventListener ? c.addEventListener(d ? "DOMContentLoaded" : a, b, !1) : document.attachEvent && (d ? window : c).attachEvent("on" + a, b)
};
PGmap.browser = PGmap.Utils.browser;
Alert = function (a) {
    if (PGmap.DEBUG) if (window.console) console.log.apply ? console.log.apply(console, arguments) : arguments.length == 1 ? console.log(arguments[0]) : arguments.length == 2 ? console.log(arguments[0], arguments[1]) : arguments.length == 3 ? console.log(arguments[0], arguments[1], arguments[2]) : console.log(arguments[0], arguments[1], arguments[2], arguments[3], arguments[4], arguments[5], arguments[6]);
    else {
        for (var b = 0, c = []; b < arguments.length; b++) c[b] = arguments[b];
        alert(c)
    }
};
ErPool = function (a) {
    var b = this !== window,
        c = a,
        d = PGmap.DEBUG;
    PGmap.DEBUG = 1;
    if (typeof a == "string" && !b || b && this instanceof String) {
        for (var c = this instanceof String ? this : a, e = a != "" && typeof a == "string" && !b ? 1 : 0, f = []; e < arguments.length; e++) f[e + (b ? 1 : 0)] = arguments[e];
        if (a != "" && typeof a == "string" || b) f[0] = "==" + c + "==; ";
        Alert.apply(window, f)
    } else if (a.from && a.from != "" && a.msg) {
        if (!(a.msg instanceof Array)) b = a.msg, a.msg = [], a.msg[0] = b;
        e = 0;
        for (f = []; e < a.msg.length; e++) f[e + 1] = a.msg[e];
        f[0] = "==" + a.from + "==; ";
        Alert.apply(window,
        f)
    } else Alert.apply(window, arguments);
    PGmap.DEBUG = d
};
String.prototype.ErPool = ErPool;
PGmap.EventFactory = new function () {
    this.eventsType = navigator.userAgent.toLowerCase().indexOf("iphone") >= 0 || navigator.userAgent.toLowerCase().indexOf("ipad") >= 0 || navigator.userAgent.toLowerCase().indexOf("ipod") >= 0 || navigator.userAgent.toLowerCase().indexOf("android") >= 0 ? {
        click: "touchend",
        mousedown: "touchstart",
        mouseup: "touchend",
        mousemove: "touchmove"
    } : {
        click: "click",
        mousedown: "mousedown",
        mouseup: "mouseup",
        mousemove: "mousemove"
    }
};
PGmap.Language = function (a) {
    var b = {};
    switch (a) {
        case "EN":
            b = {
                pointName: "Point",
                polygonName: "Polygon",
                polylineName: "Polyline",
                controlsTitle: {
                    plus: "Zoom in",
                    minus: "Zoom out",
                    route: "Build the route",
                    ruler: "Measure the distance",
                    point: "Put the point",
                    polygon: "Build the polygon",
                    polyline: "Build the polyline",
                    navigate: {
                        left: "Move to the left",
                        right: "Move to the right",
                        top: "Move to the top",
                        bottom: "Move to the bottom"
                    },
                    traffic: "Show/hide traffic jam",
                    copyright: "All rights reserved<br/>&copy;&nbsp; NFB Investment Corp. " + (new Date).getFullYear() + "<br/>&#x2117; &copy;&nbsp; CDCOM NAVIGATION LLC " + (new Date).getFullYear()
                }
            };
            break;
        case "RU":
            b = {
                pointName: "\u0422\u043e\u0447\u043a\u0430",
                polygonName: "\u041f\u043e\u043b\u0438\u0433\u043e\u043d",
                polylineName: "\u041f\u043e\u043b\u0438\u043b\u0438\u043d\u0438\u044f",
                controlsTitle: {
                    plus: "\u0423\u0432\u0435\u043b\u0438\u0447\u0438\u0442\u044c \u043c\u0430\u0441\u0448\u0442\u0430\u0431",
                    minus: "\u0423\u043c\u0435\u043d\u044c\u0448\u0438\u0442\u044c \u043c\u0430\u0441\u0448\u0442\u0430\u0431",
                    route: "\u041f\u043e\u0441\u0442\u0440\u043e\u0438\u0442\u044c \u043c\u0430\u0440\u0448\u0440\u0443\u0442",
                    ruler: "\u0418\u0437\u043c\u0435\u0440\u0438\u0442\u044c \u0440\u0430\u0441\u0441\u0442\u043e\u044f\u043d\u0438\u0435",
                    point: "\u041f\u043e\u0441\u0442\u0430\u0432\u0438\u0442\u044c \u0442\u043e\u0447\u043a\u0443",
                    polygon: "\u041f\u043e\u0441\u0442\u0440\u043e\u0438\u0442\u044c \u043c\u043d\u043e\u0433\u043e\u0443\u0433\u043e\u043b\u044c\u043d\u0438\u043a",
                    polyline: "\u041f\u043e\u0441\u0442\u0440\u043e\u0438\u0442\u044c \u043b\u0438\u043d\u0438\u044e",
                    navigate: {
                        left: "\u0421\u0434\u0432\u0438\u043d\u0443\u0442\u044c \u0432\u043b\u0435\u0432\u043e",
                        right: "\u0421\u0434\u0432\u0438\u043d\u0443\u0442\u044c \u0432\u043f\u0440\u0430\u0432\u043e",
                        top: "\u0421\u0434\u0432\u0438\u043d\u0443\u0442\u044c \u0432\u0432\u0435\u0440\u0445",
                        bottom: "\u0421\u0434\u0432\u0438\u043d\u0443\u0442\u044c \u0432\u043d\u0438\u0437"
                    },
                    traffic: "\u041f\u043e\u043a\u0430\u0437\u0430\u0442\u044c/\u0441\u043a\u0440\u044b\u0442\u044c \u043f\u0440\u043e\u0431\u043a\u0438",
                    copyright: "\u0412\u0441\u0435 \u043f\u0440\u0430\u0432\u0430 \u0437\u0430\u0449\u0438\u0449\u0435\u043d\u044b<br/>&copy;&nbsp; NFB Investment Corp. " + (new Date).getFullYear() + "<br/>&#x2117; &copy;&nbsp;\u041e\u041e\u041e &laquo;\u0421\u0418\u0414\u0418\u041a\u041e\u041c \u041d\u0410\u0412\u0418\u0413\u0410\u0426\u0418\u042f&raquo; " + (new Date).getFullYear()
                }
            }
    }
    return b
};
PGmap.Events = new function () {
    var a = [],
        b = [],
        c = [],
        d = [],
        e = this;
    this.MAP_MOVED = "map moved";
    this.MAP_ZOOMED = "map zoomed";
    this.MAP_CYCLE = "map cycle";
    this.LAYER_FIRST_DRAW = "layer first draw";
    this.LAYER_DRAW = "layer draw";
    this.MAP_MOVE = "map move";
    this.MAP_ZOOM = "map zoom";
    this.ZOOMING_END = "map zooming end";
    var f = PGmap.EventFactory.eventsType;
    this.addEventListener = function (e, f, h, j) {
        e && f && PGmap.Utils.isFunc(j) && (a.push(e), c.push(j), b.push(f), d.push(h))
    };
    this.removeEventListener = function (e, f, h) {
        for (var j = 0, g = a.length; j < g; j++) if (a[j] === e && b[j] == f && c[j] === h) return a.splice(j, 1), b.splice(j, 1), c.splice(j, 1), d.splice(j, 1), !0;
        return !1
    };
    this.removeAllEventListeners = function (e, f) {
        for (var h = 0, j = a.length; h < j; h++) a[h] === e && b[h] == f && (a.splice(h, 1), b.splice(h, 1), c.splice(h, 1), d.splice(h, 1));
        return !1
    };
    this.hasEventListener = function (c, d) {
        if (c && d) for (var e = 0, f = a.length; e < f; e++) if (a[e] === c && b[e] == d) return !0;
        return !1
    };
    this.dispatchEvent = function (e, f) {
        if (e && f instanceof PGmap.Events.Event) for (var h = 0, j = a.length; h < j; h++) a[h] === e && b[h] == f.type && c[h].call(d[h], f)
    };
    this.addHandler = function (a, b, c) {
        if (typeof a.addEventListener != "undefined") a.addEventListener(b, c, !1);
        else if (typeof a.attachEvent != "undefined") a.attachEvent("on" + b, c);
        else throw "addHandler error: Incompatible browser";
    };
    this.removeHandler = function (a, b, c) {
        a.removeEventListener ? a.removeEventListener(b, c, !1) : a.detachEvent && c && a.detachEvent("on" + b, c)
    };
    this.addHandlerByName = function (a, b, c, d) {
        (e.addHandlerByName.registry === void 0 ? {} : e.addHandlerByName.registry)[d] = c;
        e.addHandler(a,
        b, c)
    };
    this.removeHandlerByName = function (a, b, c) {
        var d = e.addHandlerByName.registry === void 0 ? {} : e.addHandlerByName.registry;
        e.removeHandler(a, b, d[c]);
        delete d[c]
    };
    this.stopEvent = function (a) {
        a.stopPropagation && a.stopPropagation();
        a.cancelBubble = !0
    };
    this.killEvent = function (a) {
        a.stopPropagation && a.stopPropagation();
        a.cancelBubble = !0;
        a.preventDefault && a.preventDefault();
        return a.returnValue = !1
    };
    this.resizeEvent = new function () {
        var a = [],
            b = [],
            c, d;
        setInterval(function () {
            for (var e = 0, f = a.length; e < f;) {
                d = PGmap.Utils.getSize(a[e]);
                c = b[e].size;
                if (d.width != c.width || d.height != c.height) b[e].size = d, b[e].handler(d);
                e++
            }
        }, 100);
        this.add = function (c, d) {
            c && PGmap.Utils.isFunc(d) && (b[a.push(c) - 1] = {
                handler: d,
                size: PGmap.Utils.getSize(c)
            })
        };
        this.remove = function (c) {
            c && (c = a.indexOf(c), c != -1 && (a.splice(c), delete b.splice(c)[0].handler))
        }
    };
    this.Draggable = function (a, b, c, d) {
        var e, n;
        a.length && a[0] && a[1] ? (e = a[0].element || a[0], n = a[1].element || a[1]) : n = e = a.element || a;
        var b = b || {}, c = c || function () {
                return {
                    x: n.offsetLeft,
                    y: n.offsetTop
                }
            }, d = d || function (a) {
                n.style.left = a.x + "px";
                n.style.top = a.y + "px"
            }, k, o, p = PGmap.Events.killEvent,
            q = PGmap.Events.addHandler,
            r = PGmap.Events.removeHandler,
            u = function (a, b) {
                b || p(a || window.event);
                q(document, f.mousemove, y);
                q(document, f.mouseup, x);
                return !1
            }, y = function (e) {
                e = e || window.event;
                if (!k && (k = {
                    mouse: {
                        x: e.touches && e.touches[0].clientX || e.clientX,
                        y: e.touches && e.touches[0].clientY || e.clientY
                    },
                    obj: c()
                }, b.dragStart && b.dragStart.call(a, k.obj) === !1)) return x();
                o = {
                    x: k.obj.x + (e.touches && e.touches[0].clientX || e.clientX) - k.mouse.x,
                    y: k.obj.y + (e.touches && e.touches[0].clientY || e.clientY) - k.mouse.y
                };
                d(o);
                k.obj = c();
                k.mouse = {
                    x: e.touches && e.touches[0].clientX || e.clientX,
                    y: e.touches && e.touches[0].clientY || e.clientY
                };
                b.drag && b.drag.call(a, o)
            }, x = function () {
                r(document, f.mousemove, y);
                r(document, f.mouseup, x);
                k = null;
                o ? b.dragEnd && b.dragEnd.call(a, o) : b.click && b.click.call(a);
                o = null
            };
        q(e, f.mousedown, u);
        return {
            callback: function (a, c) {
                b[a] = c
            },
            start: function (a) {
                a ? u(a) : u(0, 1)
            },
            kill: function () {
                r(e, f.mousedown, u)
            },
            rebind: function (a) {
                r(e, f.mousedown, u);
                a.length && a[0] && a[1] ? (e = a[0].element || a[0], n = a[1].element || a[1]) : n = e = a.element || a;
                q(e, f.mousedown, u)
            }
        }
    };
    this.fixEvent = function (a) {
        if (PGmap.Utils.browser.browser == "Explorer") {
            a = window.event;
            a.target = event.srcElement;
            if (a.pageX == null && a.clientX != null) {
                var b = document.documentElement,
                    c = document.body;
                a.pageX = a.clientX + (b && b.scrollLeft || c && c.scrollLeft || 0) - (b.clientLeft || 0);
                a.pageY = a.clientY + (b && b.scrollTop || c && c.scrollTop || 0) - (b.clientTop || 0)
            }
            a.which = a.button & 1 ? 1 : a.button & 2 ? 3 : a.button & 4 ? 2 : 0;
            a.relatedTarget = a.fromElement == a.target ? a.toElement : a.fromElement;
            a.stopPropagation = function () {
                a.cancelBubble = !0
            };
            a.preventDefault = function () {
                a.returnValue = !1
            }
        }
        PGmap.Events.gTrackEvent(a);
        return a
    };
    this.gTrackEvent = function (a) {
        var b;
        b = PGmap.Events;
        if (window._gaq && (a.type == b.MOUSE_DOWN || a.type == b.MOUSE_UP)) b = window._gaq, b.push(["_trackEvent", "PGmap", "MouseEvents", a.type, 1])
    }
};
PGmap.Events.Event = function (a, b) {
    if (typeof a == "string") this.type = a;
    else throw "event type is undefined";
    if (b) this.data = b
};
PGmap.Utils.extend(PGmap.prototype, {
    event: function (a, b) {
        a && (b instanceof Array || b === void 0 ? this.event[a].apply(this, b) : b instanceof Function ? this.event[a].addHandler() : b === null && this.event.remove(a))
    }
});
PGmap.Utils.extend(PGmap.prototype.event, {
    user: function (a, b) {
        if (a) if (b instanceof Array || b === void 0) {
            var c = this.user[a];
            if (c) for (var d = 0, e = c.length; d < e; d++) c[d].apply(this, b || [])
        } else typeof b == "function" ? (this.user[a] || (this.user[a] = []), this.user[a].push(b)) : b === null && this.remove(a)
    },
    remove: function (a, b) {
        var c = /^(user|remove|has|addHandler|removeHandler)$/;
        if (!a || !(b instanceof Function)) {
            for (var d in this.user) Alert(d, this.user.hasOwnProperty(d)), Alert(this.user[d]);
            for (d in this) c.test(d) || (Alert(d,
            this.hasOwnProperty(d), this[d].__proto__), Alert(this[d]))
        }
        if (a) {
            if (this.user[a]) {
                c = 0;
                d = this.user[a];
                for (var e = d.length; c < e; c++) if (d[c] === b) {
                    d.splice(c, 1);
                    break
                }
            }
            this[a] && (this[a][0].removeHandler(), delete this[a][0])
        }
    },
    has: function (a, b) {
        if (!a) {
            for (var c in this.event.user) if (this.event[c] === b) return !0;
            for (c in this.event) if (this.event[c] === b) return !0
        }
        return a && !b && this.event[a] != null || a && b && this.event[a] != null && this.event[a] === b || !1
    },
    removeHandler: function () {
        PGmap.Events.removeHandler(this, eventName,
        func)
    },
    addHandler: function (a, b) {
        PGmap.Events.addHandler(this, a, b)
    }
});
PGmap.Coord = function (a, b, c) {
    this.lon = c ? PGmap.Utils.mercX(a) : a;
    this.lat = c ? PGmap.Utils.mercY(b) : b
};
PGmap.Coord.prototype.getGeo = function () {
    return {
        lon: PGmap.Utils.fromMercX(this.lon),
        lat: PGmap.Utils.fromMercY(this.lat)
    }
};
PGmap.Coord.prototype.distance = function (a) {
    return a instanceof PGmap.Coord ? PGmap.Coord.distance(this, a) : null
};
PGmap.Coord.distance = function (a, b) {
    return a instanceof PGmap.Coord && b instanceof PGmap.Coord ? PGmap.Utils.distVincentyMerc(a.lon, a.lat, b.lon, b.lat) : null
};
PGmap.Coord.distance.meters = function (a, b) {
    return PGmap.Coord.distance(a, b).toFixed(0) + " \u043c"
};
PGmap.Coord.distance.kilometers = function (a, b) {
    return (PGmap.Coord.distance(a, b) / 1E3).toFixed(2) + " \u043a\u043c"
};
PGmap.Coord.distanceTrip = function (a) {
    var b = 0,
        c, d, e, f = 0;
    if (a instanceof Array && a.length > 1) for (c = a.length - 1; b < c;) d = a[b], e = a[b + 1], d instanceof PGmap.Coord && e instanceof PGmap.Coord && (f += d.distance(e)), b++;
    return f
};
PGmap.Services = function (a) {
    if (a instanceof PGmap.Globals) this.globals = a;
    else throw "param is not instanceof PGmap.Globals";
};
PGmap.Services.prototype = {
    search: function (a, b) {
        var c = "q,str,type,lon1,lat1,lon2,lat2,lon,lat,z,n,isAddr,r,t,af,lf,stat,id,callback,esc".split(",");
        if (b == null && a.callback) b = a.callback;
        var d = this.globals,
            e = PGmap.Utils,
            f, l, m, h;
        if (a.type && d && d instanceof PGmap.Globals) {
            h = d.getBbox();
            var j = d.getCoords(),
                g = d.searchUrl + "?a=" + a.type,
                n = "";
            if (a.q || a.str) n = this.prepareQ(a.q || a.str);
            f = a.lon1 != void 0 ? a.lon1 : h.lon1;
            l = a.lat1 != void 0 ? a.lat1 : h.lat1;
            m = a.lon2 != void 0 ? a.lon2 : h.lon2;
            h = a.lat2 != void 0 ? a.lat2 : h.lat2;
            f = "&lon1=" + e.fromMercX(f) + "&lat1=" + e.fromMercY(l) + "&lon2=" + e.fromMercX(m) + "&lat2=" + e.fromMercY(h);
            l = a.z || d.getZoom();
            m = a.n ? "&n=" + a.n : "";
            switch (a.type) {
                case "search":
                    g += n + f + "&z=" + l + m;
                    break;
                case "addrcode":
                    g += n + f + "&z=" + l;
                    break;
                case "suggest":
                    g += n + f + "&z=" + l + (a.t ? "&t=" + a.t : "");
                    break;
                case "geocode":
                    g += "&lon=" + e.fromMercX(a.lon != void 0 ? a.lon : j.lon) + "&lat=" + e.fromMercY(a.lat != void 0 ? a.lat : j.lat) + ("&is_addr=" + a.isAddr || 0) + (a.r ? "&r=" + a.r : "") + m;
                    break;
                case "poiclass":
                    g += "&t=" + a.t;
                    break;
                case "city":
                    g += f
            }
            a.type != "click" && (g += "&esc=" + (a.esc ? a.esc : "1"));
            g += (a.af ? "&af=" + a.af : "") + (a.lf ? "&lf=" + a.lf : "") + (a.stat ? "&stat=" + a.stat : "");
            if (a.type != "click") for (e = 0; e < c.length; e++) delete a[c[e]];
            c = null;
            for (c in a) g += "&" + c + "=" + a[c];
            g += "&" + d.key();
            d.mapObject().ajax({
                url: encodeURI(g),
                callback: b,
                type: "GET"
            })
        }
    },
    prepareQ: function (a) {
        return a instanceof Array ? "&q[]=" + a.join("&q[]=") : "&q=" + a
    },
    addPrefix: function (a, b, c) {
        c[b] = "&q[]=" + a
    }
};
PGmap.Globals = function (a, b, c, d, e, f, l, m, h, j, g, n, k) {
    typeof a == "object" && a.constructor != PGmap && (params = a, c = a.mainEl || c, d = a.mapEl || d, e = a.mainObj || e, f = a.config || f, l = a.roundRobin || l, m = a.minZoom || m || 0, h = a.maxZoom || h || 0, j = a.noKinematicDrop || j || 0, g = a.noSmoothZoom || g || 0, a = a.mapObj || a, k = a.lang || "RU");
    var o = new PGmap.Coord(4187521, 7473956),
        p = 10,
        q = m || 1,
        r = h || 17,
        u = f || {}, y = a,
        x = c,
        z = d,
        t = 0,
        w = 0,
        a = a.path || "http://js.tmcrussia.com/",
        B = PGmap.Utils.getRealSize(x),
        D = k || "RU";
    this.sourceSrv = a;
    this.mapDOM = c;
    this.mainObject = e;
    this.mainCss = a + "css/PGmap.min.css";
    this.tileUrl = "http://tiles.tmcrussia.com/";
    this.searchUrl = "http://search2.tmcrussia.com/";
    this.routeUrl = "http://route.tmcrussia.com/cgi/getroute?";
    this.trafficUrl = "http://traffic.tmcrussia.com/";
    this.trafficInfoUrl = "http://traffic.tmcrussia.com";
    this.noKinematicDrop = j;
    this.noSmoothZoom = g;
    this.langObj = new PGmap.Language(D);
    var A = {
        tiles: ["http://tiles.tmcrussia.com/"],
        search: ["http://search2.tmcrussia.com/"],
        route: ["http://route.tmcrussia.com/cgi/getroute?"],
        traffic: ["http://traffic.tmcrussia.com/"]
    };
    if (l) for (var s in l) A[s] && l[s] instanceof Array && l[s].length && (A[s] = l[s]);
    this.roundRobin = function (a, b, c) {
        return A[a] && A[a].length ? (b = Crypto.MD5(a + b + c), b = parseInt(b, 16) % A[a].length, A[a][b]) : null
    };
    this.dImgPath = a;
    this.minZoom = function () {
        return q
    };
    this.maxZoom = function () {
        return r
    };
    this.tileSize = function () {
        return 256
    };
    this.mapBbox = function () {
        if (this.config() && this.config().bbox) return this.config().bbox
    };
    this.key = function () {
        return "e316fab34585f12b5506493c1c17d41c"
    };
    this.config = function (a) {
        a && !u && (u = a);
        return u
    };
    this.mapObject = function () {
        return y
    };
    this.mainElement = function () {
        return x
    };
    this.mapElement = function () {
        return z
    };
    this.controlsElement = function () {};
    this.getPosition = function () {
        return {
            left: t,
            top: w
        }
    };
    this.setPosition = function (a, b) {
        typeof a == "number" && typeof b == "number" && (t = a, w = b);
        return this.getPosition()
    };
    this.setCoords = function (a, b) {
        var c = !1,
            d = this.mapBbox();
        a > d.maxx ? (o.lon = d.minx + a - d.maxx, c = !0) : a < d.minx ? (o.lon = d.maxx + a - d.minx, c = !0) : o.lon = Number(a);
        o.lat = Number(b);
        return c
    };
    this.getCoords = function () {
        var a = new PGmap.Coord(o.lon,
        o.lat);
        a.zoom = p;
        return a
    };
    this.mapLanguage = function () {
        return D
    };
    this.setZoom = function (a) {
        a > 0 && a >= this.minZoom() && a <= this.maxZoom() && (new PGmap.Coord(o.lon, o.lat), p = parseInt(a))
    };
    this.getZoom = function () {
        return p
    };
    this.resolutionByZoom = function (a) {
        return u && a > 0 && u.resolutions[a - 1] ? u.resolutions[a - 1] : null
    };
    this.countByZoom = function (a) {
        return u && a > 0 && u.count[a - 1] ? u.count[a - 1] : null
    };
    this.resolution = function () {
        return this.resolutionByZoom(p)
    };
    this.countX = function () {
        return this.countXByZoom(p)
    };
    this.scale = function () {
        return this.resolution() * this.tileSize()
    };
    this.getWindow = function () {
        return {
            width: B.width,
            height: B.height
        }
    };
    this.setWindow = function (a, b) {
        if (a >= 0 && b >= 0) B.width = a, B.height = b
    };
    this.getBbox = function (a) {
        return this.getBboxByZoom(p, a)
    };
    this.getBboxByZoom = function (a, b) {
        var c = this.getCoords(),
            d = this.getWindow(),
            e = this.resolutionByZoom(a),
            f = d.width / 2 * e,
            d = d.height / 2 * e;
        return b ? {
            lb: new PGmap.Coord(c.lon - f, c.lat - d),
            rt: new PGmap.Coord(c.lon + f, c.lat + d)
        } : {
            lon1: c.lon - f,
            lat1: c.lat - d,
            lon2: c.lon + f,
            lat2: c.lat + d
        }
    };
    this.xyToLonLat = function (a, b, c, d) {
        d = this.getBboxByZoom(d || p);
        c = c || this.resolution();
        return new PGmap.Coord(d.lon1 + (a + t) * c, d.lat2 - (b + w) * c)
    };
    this.lonlatToXY = function (a, b, c) {
        var d = this.getCoords(),
            c = c || this.resolution(),
            e = this.getWindow(),
            f = {};
        d.lon -= e.width / 2 * c;
        d.lat += e.height / 2 * c;
        f.x = PGmap.Utils.myRound(1 / c * (a - d.lon), 0.2) - t;
        f.y = PGmap.Utils.myRound(1 / c * (d.lat - b), 0.2) - w;
        return f
    }
};
PGmap.Globals.prototype = {
    tileSize: 256
};
if (!this.JSON) this.JSON = {};
(function () {
    function a(a) {
        return a < 10 ? "0" + a : a
    }
    function b(a) {
        e.lastIndex = 0;
        return e.test(a) ? '"' + a.replace(e, function (a) {
            var b = m[a];
            return typeof b === "string" ? b : "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4)
        }) + '"' : '"' + a + '"'
    }
    function c(a, d) {
        var e, k, m, p, q = f,
            r, u = d[a];
        u && typeof u === "object" && typeof u.toJSON === "function" && (u = u.toJSON(a));
        typeof h === "function" && (u = h.call(d, a, u));
        switch (typeof u) {
            case "string":
                return b(u);
            case "number":
                return isFinite(u) ? String(u) : "null";
            case "boolean":
            case "null":
                return String(u);
            case "object":
                if (!u) return "null";
                f += l;
                r = [];
                if (Object.prototype.toString.apply(u) === "[object Array]") {
                    p = u.length;
                    for (e = 0; e < p; e += 1) r[e] = c(e, u) || "null";
                    m = r.length === 0 ? "[]" : f ? "[\n" + f + r.join(",\n" + f) + "\n" + q + "]" : "[" + r.join(",") + "]";
                    f = q;
                    return m
                }
                if (h && typeof h === "object") {
                    p = h.length;
                    for (e = 0; e < p; e += 1) k = h[e], typeof k === "string" && (m = c(k, u)) && r.push(b(k) + (f ? ": " : ":") + m)
                } else for (k in u) Object.hasOwnProperty.call(u, k) && (m = c(k, u)) && r.push(b(k) + (f ? ": " : ":") + m);
                m = r.length === 0 ? "{}" : f ? "{\n" + f + r.join(",\n" + f) + "\n" + q + "}" : "{" + r.join(",") + "}";
                f = q;
                return m
        }
    }
    if (typeof Date.prototype.toJSON !== "function") Date.prototype.toJSON = function () {
        return isFinite(this.valueOf()) ? this.getUTCFullYear() + "-" + a(this.getUTCMonth() + 1) + "-" + a(this.getUTCDate()) + "T" + a(this.getUTCHours()) + ":" + a(this.getUTCMinutes()) + ":" + a(this.getUTCSeconds()) + "Z" : null
    }, String.prototype.toJSON = Number.prototype.toJSON = Boolean.prototype.toJSON = function () {
        return this.valueOf()
    };
    var d = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        e = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        f, l, m = {
            "\u0008": "\\b",
            "\t": "\\t",
            "\n": "\\n",
            "\u000c": "\\f",
            "\r": "\\r",
            '"': '\\"',
            "\\": "\\\\"
        }, h;
    if (typeof JSON.stringify !== "function") JSON.stringify = function (a, b, d) {
        var e;
        l = f = "";
        if (typeof d === "number") for (e = 0; e < d; e += 1) l += " ";
        else typeof d === "string" && (l = d);
        if ((h = b) && typeof b !== "function" && (typeof b !== "object" || typeof b.length !== "number")) throw Error("JSON.stringify");
        return c("", {
            "": a
        })
    };
    if (typeof JSON.parse !== "function") JSON.parse = function (a, b) {
        function c(a, d) {
            var e, f, h = a[d];
            if (h && typeof h === "object") for (e in h) Object.hasOwnProperty.call(h, e) && (f = c(h, e), f !== void 0 ? h[e] = f : delete h[e]);
            return b.call(a, d, h)
        }
        var e, a = String(a);
        d.lastIndex = 0;
        d.test(a) && (a = a.replace(d, function (a) {
            return "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4)
        }));
        if (/^[\],:{}\s]*$/.test(a.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,
            "]").replace(/(?:^|:|,)(?:\s*\[)+/g, ""))) return e = eval("(" + a + ")"), typeof b === "function" ? c({
            "": e
        }, "") : e;
        throw new SyntaxError("JSON.parse");
    }
})();
PGmap.CrossDomainAjax = function (a) {
    if (this.instance != void 0) return this.instance;
    this.instance = this;
    var b = this.adapter = a;
    this.request = function (a) {
        b.request(a)
    }
};
PGmap.Request = function () {};
PGmap.CrossDomainAjax.flashRequest = function () {
    this.poolCalls = [];
    var a = this;
    this.receive = function (b, c) {
        b != void 0 && (a.poolCalls[b].c(c, a.poolCalls[b].u), a.poolCalls[b] = null)
    }
};
PGmap.CrossDomainAjax.prototype.getAdapter = function () {
    return this.adapter
};
PGmap.CrossDomainAjax.requestObject = function () {
    var a = null;
    try {
        a = new ActiveXObject("Msxml2.XMLHTTP")
    } catch (b) {
        try {
            a = new ActiveXObject("Microsoft.XMLHTTP")
        } catch (c) {
            typeof XMLHttpRequest != "undefined" && (a = new XMLHttpRequest)
        }
    }
    return a
};
FlashCrossDomainAjax = function () {
    function a(a) {
        function e() {
            document.body.appendChild(b);
            window.swfobject.embedSWF(f + "/swf/request.swf", b.id, "1", "1", "10.0", f + "/swf/expressInstall.swf", {}, {
                wmode: "window",
                allowScriptAccess: "always"
            }, {}, function () {
                c = !0;
                b = document.getElementById(b.id);
                b.style.cssText += "; visibility: hidden; position: relative;";
                a()
            })
        }
        var f = "http://jsapi.murzina.jsdev.tmcrussia.com";
        window.swfobject ? e() : PGmap.Utils.addLib("js", f + "/js/utils/swfobject.js", e)
    }
    var b = document.createElement("div"),
        c = !1;
    b.id = "PGmapFlObj";
    this.request = function (d) {
        function e() {
            b && b.load ? f() : setTimeout(arguments.callee, 200)
        }
        function f() {
            var a = {};
            if (d.url) a.url = d.url, a.id = PGmap.Request.flashRequest.poolCalls.length, d.callback && PGmap.Utils.isFunc(d.callback) && (PGmap.Request.flashRequest.poolCalls[a.id] = {
                c: d.callback,
                u: d.url
            }), b.load && b.load(a)
        }!c && !b.load ? a(e) : f()
    }
};
ProxyCrossDomainAjax = function (a) {
    this.request = function (b) {
        var c = b.callback || null,
            d = b.type,
            b = JSON.stringify(b.data),
            e;
        e = PGmap.CrossDomainAjax.requestObject();
        e.open(d, encodeURI(a), !0);
        e.setRequestHeader("Content-Type", "application/x-javascript");
        e.setRequestHeader("Content-length", b.length);
        e.setRequestHeader("Connection", "close");
        e.onreadystatechange = function () {
            if (e.readyState == 4) window.status = "", (e.status == 200 || e.status == 304) && c && c(e.responseText)
        };
        switch (d) {
            case "POST":
                e.send(b);
                break;
            case "GET":
                e.send()
        }
    }
};
HTMLCrossDomainAjax = function () {
    var a = PGmap.CrossDomainAjax.requestObject();
    if (typeof a.withCredentials == "undefined") return new FlashCrossDomainAjax;
    this.request = function (b) {
        var c = b.data,
            d = b.url,
            e = b.type,
            f = b.callback || null;
        if (window !== window.top) window.top.location = location;
        a.onload = function () {
            this.status == 200 && f(this.responseText, b.url)
        };
        a.onabort = function (a) {
            f(a)
        };
        switch (e) {
            case "POST":
                c = JSON.stringify(c);
                a.open("POST", d, !0);
                a.send(c);
                break;
            case "GET":
                a.open("GET", d, !0);
                a.send();
                break;
            default:
                console.log("error")
        }
    }
};
PGmap.Base = new function () {
    var a = function (a, b) {
        var c = document.createElement("DIV");
        if (a != null) c.className = a;
        if (b != null) c.style.cssText = b;
        return c
    };
    this.create = a;
    var b = function (a, b) {
        var c = document.createElement("DIV");
        c.style.position = "absolute";
        if (a != null) c.className = a;
        if (b != null) c.style.cssText = b;
        return c
    };
    this.createAbs = b;
    var c = b(),
        d = document.createElement("img"),
        e = a("PGmap-geometry-point"),
        f = a("PGmap-geometry-widget"),
        l = a("b-balloon m-balloon-default js-balloon-visible", "");
    d.style.position = "absolute";
    d.style.width = d.style.height = PGmap.Globals.prototype.tileSize + "px";
    l.innerHTML = '<div style="cursor: auto;" class="b-balloon__content"><b style="display:none" class="g-closer m-closer-balloon"><i class="g-closer__icon"></i></b><span></span></div>';
    this.layerDiv = function () {
        return c.cloneNode(!1)
    };
    this.fragment = function () {
        return document.createDocumentFragment()
    };
    this.tileImg = function () {
        return d.cloneNode(!1)
    };
    this.placemarkDiv = function () {
        return e.cloneNode(!1)
    };
    this.widgetDiv = function () {
        return f.cloneNode(!1)
    };
    this.balloonDiv = function () {
        return l.cloneNode(!0)
    }
};
PGmap.GeoParser = new function () {
    function a(a) {
        for (var a = a.getElementsByTagName("Placemark"), b = [], c = 0, m = a.length; c < m; c++) {
            for (var h = a[c].getAttribute("id"), j = a[c].getElementsByTagName("coordinates"), j = j && j[0].childNodes[0].nodeValue.toString().split(","), j = new PGmap.Coord(j[0], j[1], !0), g = a[c], n = d, k = void 0, k = void 0, o = {}, p = 0, q = n.length; p < q; p++) {
                var r = "",
                    k = g.getElementsByTagName(n[p]);
                if (k[0]) {
                    for (var k = k[0].childNodes, u = 0, y = k.length; u < y; u++) r += k[u].nodeValue.toString();
                    o[n[p]] = r
                }
            }
            b.push({
                type: "point",
                coords: j,
                id: h,
                data: o
            })
        }
        return b
    }
    function b(a, b) {
        for (var d = [], m = a.getElementsByTagName(b == "polyline" ? "LineString" : "Polygon"), h, j, g, n, k, o, p, q = a.getElementsByTagName("Document")[0], r = 0, u = m.length; r < u; r++) {
            o = m[r].getElementsByTagName("coordinates")[0].childNodes[0].nodeValue.toString().match(/(\d{1,3}\.\d+,\d{1,3}\.\d+)/gm);
            k = {};
            k.type = b;
            k.coords = [];
            if (h = m[r].parentNode.getElementsByTagName("styleUrl")[0].childNodes[0].nodeValue.toString()) {
                j = a.getElementsByTagName("Style");
                for (var y = 0, x = j.length; y < x; y++) if (g = j[y], g.parentNode == q && g.getAttribute("id").indexOf(h.replace("#", "")) != -1 && (n = g.getElementsByTagName("LineStyle")[0], p = n.getElementsByTagName("color")[0].childNodes[0].nodeValue, k.style = {}, k.style.line = {}, k.style.line.color = "#" + p.substr(6, 2) + p.substr(4, 2) + p.substr(2, 2), k.style.line.opacity = parseInt(p.substr(0, 2).toUpperCase(), 16) / 255, k.style.line.lineHeight = new Number(n.lastChild.nodeValue), g = g.getElementsByTagName("PolyStyle"), g.length)) g = g[0], p = g.getElementsByTagName("color")[0].childNodes[0].nodeValue,
                k.style.color = "#" + p.substr(6, 2) + p.substr(4, 2) + p.substr(2, 2), k.style.opacity = parseInt(p.substr(0, 2).toUpperCase(), 16) / 255
            }
            for (var z in o) h = o[z].split(","), k.coords.push({
                lon: c.mercX(h[0]),
                lat: c.mercY(h[1])
            });
            d.push(k)
        }
        return d
    }
    var c = PGmap.Utils,
        d = "name,visibility,open,atom:author,atom:link,address,xal:AddressDetails,phoneNumber,description,AbstractView,TimePrimitive,styleUrl,StyleSelector,Region,ExtendedData,Geometry".split(",");
    this.parseKML = function (d) {
        var f = [],
            l;
        return d && (d instanceof Document ? l = d : typeof d == "string" && (l = c.stringToXML(d)), l) ? f = f.concat(a(l), b(l, "polyline"), b(l, "polygon")) : null
    };
    this.parseNMEA = function (a) {
        var b;
        a: {
            if (a) if (b = a.length / 1E6, b < 3) {
                b = !0;
                break a
            } else throw "too much data size";
            b = !1
        }
        if (b) {
            var a = a.match(/^\$GPRMC,[0-9]{6}\.[0-9]{1,3},A,\d{4}\.\d+,\w,\d{5}\.\d+,\w/gm),
                d;
            b = [];
            for (var m = {
                type: "polyline",
                coords: []
            }; a.length;) {
                d = a.shift().split(",");
                var h = c.timeToDegreesLon(d[5]);
                d = c.timeToDegreesLat(d[3]);
                m.coords.push({
                    lon: c.mercX(h),
                    lat: c.mercY(d)
                })
            }
            b.push(m);
            return b
        } else return null
    }
};
PGmap.BasicGeom = {
    init: function (a) {
        if (a instanceof PGmap.Globals) this.globals = a, this.update(), this.init = null
    },
    update: function () {
        var a = this.globals.lonlatToXY(this.coord.lon, this.coord.lat);
        this.left = a.x;
        this.top = a.y;
        this.element.style.cssText += "; left: " + a.x + "px; top: " + a.y + "px;"
    },
    hide: function () {
        this.element.style.display = "none"
    },
    show: function () {
        this.element.style.display = "block"
    },
    clean: function () {}
};
PGmap.Graphics = new function () {
    function a(a) {
        a.path_commands = [
            [],
            []
        ];
        a.draw_style = {};
        a.draw_xy = [0, 0];
        a.s_x = a.s_y = 1
    }
    var b = this,
        c = {}, d = {}, e = document,
        f = {
            NaN: 1,
            Infinity: 1,
            "-Infinity": 1
        }, l = String.prototype.toLowerCase,
        m = Object.prototype.toString,
        h = Math.round,
        j = {
            M: 1,
            L: 2,
            Q: 3,
            m: 1,
            l: 2,
            c: 3
        }, g = [0, function (a, b, c, d) {
            b < a.length && (b += n[1]([a[b].x, a[b].y], 0, c, d) / 2);
            return b
        }, function (a, b, c, d) {
            b < a.length && (b += n[2]([a[b].x, a[b].y], 0, c, d) / 2);
            return b
        }, function (a, b, c, d) {
            b + 1 < a.length && (b += n[3]([a[b].x, a[b].y, a[b + 1].x,
            a[b + 1].y], 0, c, d) / 2);
            return b
        }],
        n = [function (a, b, c) {
            b < a[0] ? a[0] = b : b > a[2] && (a[2] = b);
            c < a[1] ? a[1] = c : c > a[3] && (a[3] = c)
        }, function (a, b, c, d) {
            b + 1 < a.length && (this[0](d, a[b], a[b + 1]), c.push("M" + a[b++] + "," + a[b++] + " "));
            return b
        }, function (a, b, c, d) {
            b + 1 < a.length && (this[0](d, a[b], a[b + 1]), c.push(a[b++] + "," + a[b++] + " "));
            return b
        }, function (a, b, c, d) {
            b + 3 < a.length && (this[0](d, a[b], a[b + 1]), this[0](d, a[b + 2], a[b + 3]), c.push("Q" + a[b++] + "," + a[b++] + "," + a[b++] + "," + a[b++] + " "));
            return b
        }];
    b.type = window.SVGAngle || e.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure",
        "1.1") ? "SVG" : "VML";
    b.vml = b.type == "VML";
    b.svg = !b.vml;
    if (b.vml) {
        var k = e.createElement("div");
        k.innerHTML = '<v:shape adj="1"/>';
        k = k.firstChild;
        k.style.behavior = "url(#default#VML)";
        if (!(k && typeof k.adj == "object")) return b.type = null;
        k = null
    }
    b.is = function (a, b) {
        b = l.call(b);
        return b == "finite" ? !f.hasOwnProperty(+a) : b == "null" && a === null || b == typeof a || b == "object" && a === Object(a) || b == "array" && Array.isArray && Array.isArray(a) || m.call(a).slice(8, -1).toLowerCase() == b
    };
    var o = function (a) {
        a.toString = function () {
            return this[0].toString()
        };
        return a
    }, p = function (a, b) {
        return {
            x: a[0] - b / 2,
            y: a[1] - b / 2,
            width: Math.abs(a[2] - a[0]) + b,
            height: Math.abs(a[3] - a[1]) + b
        }
    }, q = function (a, b) {
        return [a.x + b / 2, a.y + b / 2, a.x + a.width - b / 2, a.y + a.height - b / 2]
    }, r = function (a, b, c) {
        return [Math.min(a[0], b[0] - c / 2), Math.min(a[1], b[1] - c / 2), Math.max(a[2], b[2] + c / 2), Math.max(a[3], b[3] + c / 2)]
    }, u = function (a) {
        return b.is(a, "array") && a.length == 2 && b.is(a[0], "string") && b.is(a[1], "array") && a[1].length == 4
    }, y = function (a, b, c) {
        var d = c["in"],
            e = c.out;
        if (a > 0) if (a = b / a, a > e) return !1;
        else a > d && (c["in"] = a);
        else if (a < 0) if (a = b / a, a < d) return !1;
        else a < e && (c.out = a);
        else if (b > 0) return !1;
        return !0
    }, x = function (a, b, c, d) {
        var e, f, g, h, j, k, l, m, n, s;
        d[0] && (e = f = c[0], h = Math.abs(a.x - e), j = Math.abs(b.x - e), g = 1);
        d[2] ? (g ? (g = Math.abs(a.x - c[2]), e = h > g ? c[2] : c[0], g = Math.abs(b.x - c[2]), f = j > g ? c[2] : c[0]) : e = f = c[2], h = Math.abs(a.x - e), j = Math.abs(b.x - e)) : g || (g = Math.abs(a.x - c[2]), h = Math.abs(a.x - c[0]), e = h > g ? ((h = g) || 1) && c[2] : c[0], g = Math.abs(b.x - c[2]), j = Math.abs(b.x - c[0]), f = j > g ? ((j = g) || 1) && c[2] : c[0]);
        d[1] && (k = l = c[1], n = Math.abs(a.y - k), s = Math.abs(b.y - k), m = 1);
        d[3] ? (m ? (g = Math.abs(a.y - c[3]), k = n > g ? c[3] : c[1], g = Math.abs(b.y - c[3]), l = s > g ? c[3] : c[1]) : k = l = c[3], n = Math.abs(a.y - k), s = Math.abs(b.y - k)) : m || (g = Math.abs(a.y - c[3]), n = Math.abs(a.y - c[1]), k = n > g ? ((n = g) || 1) && c[3] : c[1], g = Math.abs(b.y - c[3]), s = Math.abs(b.y - c[1]), l = s > g ? ((s = g) || 1) && c[3] : c[1]);
        return d.join("") == "truetruetruetrue" ? [e, k, h > j ? e : f, n > s ? k : l, f, l] : e == f && k == l ? [e, k] : [e, k, f, l]
    };
    if (b.svg) {
        b.svgns = "http://www.w3.org/2000/svg";
        b.xlink = "http://www.w3.org/1999/xlink";
        var z = " Z",
            t = function (a) {
                return a || function (a) {
                    return a
                }
            },
            w = function (a, c, d) {
                if (c) for (var f in c) c.hasOwnProperty(f) && (d ? d - 1 ? (a.setAttribute(f, String(c[f])), a.setAttributeNS(b.svgns, f, String(c[f]))) : a.setAttributeNS(b.svgns, f, String(c[f])) : a.setAttribute(f, String(c[f])));
                else return a = e.createElementNS(b.svgns, a), a.style && (a.style.webkitTapHighlightColor = "rgba(0,0,0,0)"), a
            }, B = function (a, b) {
                var c = b.x,
                    d = b.y,
                    e = b.width,
                    f = b.height,
                    g, h = (g = a.filter && a.filter.on && a.filter.params) || {
                        d: 0,
                        devi: 0
                    }, j = h.devi > 0 ? -2 * h.devi : 0;
                c += (h.d < 0 ? h.d : 0) + j;
                d += (h.d < 0 ? h.d : 0) + j;
                e += Math.abs(h.d) - 2 * j;
                f += Math.abs(h.d) - 2 * j;
                w(a.element, {
                    width: e,
                    height: f,
                    viewBox: c + " " + d + " " + e + " " + f
                });
                a.offsetWidth = a.box.width;
                a.offsetHeight = a.box.height;
                a.element.style.cssText = "position:absolute;left:" + c + "px;top:" + d + "px";
                g && w(a.filter.node, {
                    x: c - 100,
                    y: d - 100,
                    width: e + 200,
                    height: f + 200
                }, 2)
            }, D = function () {
                this.id = "shadow-" + h(1E4 * Math.random()) + "-" + h(1E4 * Math.random());
                this.params = {
                    d: 6,
                    devi: 6,
                    opacity: 0.5
                };
                this.node = w("filter");
                this.blur = w("feGaussianBlur");
                this.offset = w("feOffset");
                this.blend = w("feBlend");
                this.flood = w("feFlood");
                this.compos = w("feComposite");
                w(this.flood, {
                    "in": "SourceGraphic",
                    result: "flooding",
                    "flood-opacity": 0.5
                });
                w(this.compos, {
                    "in": "flooding",
                    in2: "SourceGraphic",
                    result: "compos",
                    operator: "in"
                });
                w(this.offset, {
                    "in": "compos",
                    result: "theShadow",
                    dx: 6,
                    dy: 6
                });
                w(this.blur, {
                    "in": "theShadow",
                    result: "blurOut",
                    stdDeviation: 6
                });
                w(this.blend, {
                    "in": "SourceGraphic",
                    in2: "blurOut",
                    mode: "normal"
                });
                w(this.node, {
                    id: this.id,
                    filterUnits: "userSpaceOnUse"
                }, 2);
                this.node.appendChild(this.flood);
                this.node.appendChild(this.compos);
                this.node.appendChild(this.offset);
                this.node.appendChild(this.blur);
                this.node.appendChild(this.blend)
            };
        D.prototype.set = function (a) {
            if (a) {
                var b = this.params;
                a.hasOwnProperty("d") && w(this.offset, {
                    dx: a.d || 1,
                    dy: b.d = a.d || 1
                });
                a.hasOwnProperty("devi") && w(this.blur, {
                    stdDeviation: b.devi = a.devi || 1
                });
                a.hasOwnProperty("opacity") && w(this.flood, {
                    "flood-opacity": b.opacity = a.opacity || 0.5
                })
            }
            return this
        };
        var A = function (c) {
            a(this);
            var d = w("svg"),
                e = w("g"),
                f, g;
            this.defs = w("defs");
            this.group = e;
            d.appendChild(this.defs);
            d.appendChild(this.group);
            c && c.constructor && c.constructor == s ? (g = p(c.box, c.attrs["stroke-width"] || 1), f = c.node, this.elements = [c], this.x = g.x + g.width / 2, this.y = g.y + g.height / 2) : (g = {
                x: 0,
                y: 0,
                width: c && c.offsetWidth || 0,
                height: c && c.offsetHeight || 0
            }, e = c, f = d, this.elements = [], this.x = this.y = 0, c && (this.handleset = !0));
            this.element = d;
            this.box = g;
            w(d, {
                xmlns: b.svgns,
                version: 1.1,
                width: g.width,
                height: g.height,
                preserveAspectRatio: "none",
                viewBox: g.x + " " + g.y + " " + g.width + " " + g.height
            });
            d.style.cssText = "position:absolute;left:" + g.x + "px;top:" + g.y + "px";
            e && e.appendChild(f)
        }, c = A.prototype;
        c.shadow = function (a) {
            if (a == "off") w(this.group, {
                filter: "none"
            }), this.filter.on = !1;
            else {
                a = b.is(a, "object") && a;
                if (!this.filter) this.filter = new D, this.defs.appendChild(this.filter.node);
                this.filter.set(a);
                this.filter.on = !0;
                w(this.group, {
                    filter: "url(#" + this.filter.id + ")"
                });
                B(this, this.box)
            }
            return this
        };
        c.append = function (a) {
            a.paper = this;
            this.mergeBox(a.box, a.attrs["stroke-width"] || 1);
            this.group.appendChild(a.node);
            this.elements.push(a);
            return this
        };
        c.mergeBox = function (a, b) {
            var c = q(this.box, 0);
            this.box = c = p(r(c, a, b), 0);
            B(this, c);
            if (!this.handleset) this.x = c.x + c.width / 2, this.y = c.y + c.height / 2
        };
        c.updateBox = function (a) {
            var b = this.elements,
                c = b.length,
                d = [this.x, this.y, this.x, this.y];
            this.handleset || b[0] && (d = [b[0].box[0], b[0].box[1], b[0].box[2], b[0].box[3]]);
            if (a) {
                for (var a = [], e = 0; e < c; e++) b[e].paper === this && (d = r(d, b[e].box, b[e].attrs["stroke-width"] || 1), a.push(b[e]));
                this.elements = a
            } else for (; c--;) d = r(d, b[c].box, b[c].attrs["stroke-width"] || 1);
            this.box = d = p(d, 0);
            B(this, d);
            if (!this.handleset) this.x = d.x + d.width / 2, this.y = d.y + d.height / 2
        };
        c.scale = function (a, b) {
            arguments.length - 1 || (b = a);
            var c = this.s_x,
                d = this.s_y,
                a = Math.abs(a || c),
                b = Math.abs(b || d);
            w(this.element, {
                width: a * this.box.width,
                height: b * this.box.height
            });
            this.element.style.cssText = "position:absolute;left:" + (this.x - a * (this.x - this.box.x)) + "px;top:" + (this.y - b * (this.y - this.box.y)) + "px";
            this.s_x = a;
            this.s_y = b;
            return this
        };
        var s = function (a, b, c) {
            this.box = [0, 0, 0, 0];
            this.node = a;
            a.grapher = this;
            this.attrs = this.attrs || {};
            b(this);
            c && w("svg").append(this)
        }, d = s.prototype;
        d.show = function () {
            this.node.style.display = "";
            return this
        };
        d.hide = function () {
            this.node.style.display = "none";
            return this
        };
        var v = function (a, b) {
            return a.group.removeChild(b.node)
        }, E = function (a, b) {
            var c = w("path");
            return new s(c, function (b) {
                b.type = "path";
                var c = {
                    fill: "none",
                    stroke: "#000"
                };
                a && (c.path = a);
                H(b, c)
            }, b)
        }, M = function (a, b) {
            var c = w("marker"),
                d = new s(c, function () {
                    var b = E().attr({
                        d: "M20,25 L0,17 L6,25 L0,33 L20,25",
                        fill: a.fill || "#017E8D",
                        stroke: a.stroke || "#017E8D"
                    });
                    c.appendChild(b.node)
                }, b);
            d.attr({
                id: a.id,
                viewBox: "0 0 50 50",
                refX: "16",
                refY: "25",
                markerUnits: "strokeWidth",
                orient: "auto",
                markerWidth: "10",
                markerHeight: "10"
            });
            return d
        }, J = function (a, b, c, d, e) {
            var f = w("ellipse");
            return new s(f, function (e) {
                e.attrs = {
                    cx: a,
                    cy: b,
                    rx: c,
                    ry: d,
                    fill: "none",
                    stroke: "#000"
                };
                e.box = [a - c, b - d, a + c, b + d];
                e.rect = p(e.box, 1);
                e.type = c == d ? "circle" : "ellipse";
                w(f, e.attrs)
            }, e)
        }, F = function (a, b, c, d, e, f, g) {
            var h = w("rect");
            return new s(h, function (g) {
                g.attrs = {
                    x: a,
                    y: b,
                    width: c,
                    height: d,
                    r: 0,
                    rx: e || 0,
                    ry: f || 0,
                    fill: "none",
                    stroke: "#000"
                };
                g.box = [a, b, a + c, b + d];
                g.rect = p(g.box, 1);
                g.type = e || f ? "roundrect" : "rect";
                w(h, g.attrs)
            }, g)
        }, H = function (a, c) {
            var d, e, f, g = a.node,
                h = a.paper,
                j = a.attrs,
                k = {
                    data: 1,
                    points: 1,
                    commands: 1
                }, l;
            for (l in c) if (c.hasOwnProperty(l)) {
                var m = c[l];
                k[l] || (j[l] = m);
                switch (l) {
                    case "cursor":
                        g.style.cursor = m;
                        break;
                    case "points":
                        d || (m = b.formatPath(m), d = !0);
                    case "commands":
                        d || (m = b.formatPath(m, c.data), d = !0);
                    case "data":
                        d || (m = b.formatPath(c.commands, m), d = !0);
                    case "path":
                        if (a.type == "path" && u(m)) m = m || [], j.path = o([m[0] || "M0,0", a.box = m[1]]), e = j.path + "", d = !0;
                        break;
                    case "stroke-width":
                        g.style.strokeWidth = m;
                        g.setAttribute(l, m);
                        break;
                    case "stroke-dasharray":
                        g.style.strokeDasharray = m;
                        g.setAttribute(l, m);
                        break;
                    case "fill-opacity":
                        if (g.setAttribute(l, m), m > 0 && !c.hasOwnProperty("fill") && j.fill == "none") l = "fill", m = "#000", j[l] = m;
                        else break;
                    case "fill-rule":
                        g.style.fillRule = m;
                        g.setAttribute(l, m);
                        break;
                    case "fill":
                        g.setAttribute(l, m);
                        j.path && (m != "none" ? RegExp("s*" + z + "s*$", "i").test(j.path[0]) || (j.path[0] += z, e = j.path + "") : (j.path[0] = j.path[0].replace(z, ""), e = j.path + ""));
                        break;
                    case "x":
                    case "y":
                    case "cx":
                    case "cy":
                    case "width":
                    case "height":
                    case "r":
                    case "rx":
                    case "ry":
                        f = 1;
                        break;
                    default:
                        /^style\.\w+$/.test(l) ? g.style[l.replace("style.", "")] = m : g.setAttribute(l, m)
                }
            }
            e && g.setAttribute("d", e);
            f && (a.box = a.type.indexOf("rect") > -1 ? [j.x, j.y, j.x + j.width, j.y + j.height] : [j.cx - j.rx, j.cy - j.ry, j.cx + j.rx, j.cy + j.ry]);
            if (d || c.hasOwnProperty("stroke-width") || f) a.rect = p(a.box, j["stroke-width"] || 1), h && h.updateBox()
        }
    }
    if (b.vml) {
        var z =
            " Z",
            I;
        e.createStyleSheet().addRule(".rvml", "behavior:url(#default#VML)");
        try {
            !e.namespaces.rvml && e.namespaces.add("rvml", "urn:schemas-microsoft-com:vml"), I = function (a) {
                return e.createElement("<rvml:" + a + ' class="rvml">')
            }
        } catch (T) {
            I = function (a) {
                return e.createElement("<" + a + ' xmlns="urn:schemas-microsoft.com:vml" class="rvml">')
            }
        }
        n[1] = function (a, b, c, d) {
            b + 1 < a.length && (this[0](d, a[b], a[b + 1]), c.push("m" + h(10 * a[b++]) + "," + h(10 * a[b++]) + " "));
            return b
        };
        n[2] = function (a, b, c, d) {
            b + 1 < a.length && (this[0](d, a[b],
            a[b + 1]), c.push("l" + h(10 * a[b++]) + "," + h(10 * a[b++]) + " "));
            return b
        };
        n[3] = function (a, b, c, d) {
            if (b + 3 < a.length) {
                this[0](d, a[b], a[b + 1]);
                this[0](d, a[b + 2], a[b + 3]);
                var d = a[b++],
                    e = a[b++],
                    g = a[b++],
                    f = a[b++],
                    j = 1 / 3,
                    k = 2 / 3,
                    a = [j * (a[b - 2] || 0) + k * d, j * (a[b - 1] || 0) + k * e, j * g + k * d, j * f + k * e, g, f];
                c.push("c" + h(10 * a[0]) + "," + h(10 * a[1]) + "," + h(10 * a[2]) + "," + h(10 * a[3]) + "," + h(10 * a[4]) + "," + h(10 * a[5]) + " ")
            }
            return b
        };
        var t = function (a) {
            a = a || function (a) {
                return a
            };
            return function (b) {
                return a(h(b / 10))
            }
        }, O = String.prototype.toUpperCase,
            C = /([achlmqstvz])[\s,]*((-?\d*\.?\d*(?:e[-+]?\d+)?\s*,?\s*)+)/ig,
            L = /(-?\d*\.?\d*(?:e[-+]?\d+)?)\s*,?\s*/ig,
            U = function (a) {
                if (!a) return null;
                var b = {
                    a: 7,
                    c: 6,
                    h: 1,
                    l: 2,
                    m: 2,
                    q: 4,
                    s: 4,
                    t: 2,
                    v: 1,
                    z: 0
                }, c = [];
                c.length || String(a).replace(C, function (a, d, e) {
                    var g = [],
                        a = l.call(d);
                    e.replace(L, function (a, b) {
                        b && g.push(+b)
                    });
                    a == "m" && g.length > 2 && (c.push([d].concat(g.splice(0, 2))), a = "l", d = d == "m" ? "l" : "L");
                    for (; g.length >= b[a];) if (c.push([d].concat(g.splice(0, b[a]))), !b[a]) break
                });
                return c
            }, K = function (a) {
                if (!b.is(a, "array") || !b.is(a && a[0], "array")) a = U(a);
                var c = [],
                    d = 0,
                    e = 0,
                    g = 0,
                    f = 0,
                    h = 0;
                a[0][0] ==
                    "M" && (d = +a[0][1], e = +a[0][2], g = d, f = e, h++, c[0] = ["M", d, e]);
                for (var j = a.length; h < j; h++) {
                    var k = c[h] = [],
                        l = a[h];
                    if (l[0] != O.call(l[0])) switch (k[0] = O.call(l[0]), k[0]) {
                        case "A":
                            k[1] = l[1];
                            k[2] = l[2];
                            k[3] = l[3];
                            k[4] = l[4];
                            k[5] = l[5];
                            k[6] = +(l[6] + d);
                            k[7] = +(l[7] + e);
                            break;
                        case "V":
                            k[1] = +l[1] + e;
                            break;
                        case "H":
                            k[1] = +l[1] + d;
                            break;
                        case "M":
                            g = +l[1] + d, f = +l[2] + e;
                        default:
                            for (var m = 1, n = l.length; m < n; m++) k[m] = +l[m] + (m % 2 ? d : e)
                    } else {
                        m = 0;
                        for (n = l.length; m < n; m++) c[h][m] = l[m]
                    }
                    switch (k[0]) {
                        case "Z":
                            d = g;
                            e = f;
                            break;
                        case "H":
                            d = k[1];
                            break;
                        case "V":
                            e = k[1];
                            break;
                        case "M":
                            g = c[h][c[h].length - 2], f = c[h][c[h].length - 1];
                        default:
                            d = c[h][c[h].length - 2], e = c[h][c[h].length - 1]
                    }
                }
                return c
            }, G = function (a, b, c, d, e, g) {
                a = 1 / 3;
                b = 2 / 3;
                return [(c * 1 - a * e) / b, (d * 1 - a * g) / b, e, g]
            }, V = function (a, b, c, d, e, g, f, h, j, k) {
                var l = Math,
                    m = l.PI,
                    n = m * 120 / 180,
                    s = m / 180 * (+e || 0),
                    p = [],
                    o, E = function (a, b, c) {
                        var d = a * l.cos(c) - b * l.sin(c),
                            a = a * l.sin(c) + b * l.cos(c);
                        return {
                            x: d,
                            y: a
                        }
                    };
                if (k) r = k[0], o = k[1], g = k[2], t = k[3];
                else {
                    o = E(a, b, -s);
                    a = o.x;
                    b = o.y;
                    o = E(h, j, -s);
                    h = o.x;
                    j = o.y;
                    l.cos(m / 180 * e);
                    l.sin(m / 180 * e);
                    o = (a - h) / 2;
                    r = (b - j) / 2;
                    t = o * o / (c * c) + r * r / (d * d);
                    t > 1 && (t = l.sqrt(t), c *= t, d *= t);
                    var t = c * c,
                        q = d * d,
                        t = (g == f ? -1 : 1) * l.sqrt(l.abs((t * q - t * r * r - q * o * o) / (t * r * r + q * o * o))),
                        g = t * c * r / d + (a + h) / 2,
                        t = t * -d * o / c + (b + j) / 2,
                        r = l.asin(((b - t) / d).toFixed(9));
                    o = l.asin(((j - t) / d).toFixed(9));
                    r = a < g ? m - r : r;
                    o = h < g ? m - o : o;
                    r < 0 && (r = m * 2 + r);
                    o < 0 && (o = m * 2 + o);
                    f && r > o && (r -= m * 2);
                    !f && o > r && (o -= m * 2)
                }
                l.abs(o - r) > n && (p = o, m = h, q = j, o = r + n * (f && o > r ? 1 : -1), h = g + c * l.cos(o), j = t + d * l.sin(o), p = V(h, j, c, d, e, 0, f, m, q, [o, p, g, t]));
                g = o - r;
                e = l.cos(r);
                n = l.sin(r);
                f = l.cos(o);
                m = l.sin(o);
                o = l.tan(g / 4);
                c = 4 / 3 * c * o;
                o *= 4 / 3 * d;
                d = [a, b];
                a = [a + c * n, b - o * e];
                b = [h + c * m, j - o * f];
                h = [h, j];
                a[0] = 2 * d[0] - a[0];
                a[1] = 2 * d[1] - a[1];
                if (k) return [a, b, h].concat(p);
                else {
                    p = [a, b, h].concat(p).join().split(",");
                    k = [];
                    h = 0;
                    for (j = p.length; h < j; h++) k[h] = h % 2 ? E(p[h - 1], p[h], s).y : E(p[h], p[h + 1], s).x;
                    return k
                }
            }, N = function (a) {
                var a = W(a),
                    b, c;
                res = [];
                for (var d = 0, e = a.length; d < e; d++) {
                    b = a[d];
                    c = l.call(a[d][0]);
                    c == "z" && (c = "x");
                    for (var g = 1, f = b.length; g < f; g++) c += h(b[g] * 10) + (g != f - 1 ? "," : "");
                    res.push(c)
                }
                return res.join(" ")
            }, W = function (a) {
                var b = K(a),
                    a = {
                        x: 0,
                        y: 0,
                        bx: 0,
                        by: 0,
                        X: 0,
                        Y: 0,
                        qx: null,
                        qy: null
                    }, c = {
                        x: 0,
                        y: 0,
                        bx: 0,
                        by: 0,
                        X: 0,
                        Y: 0,
                        qx: null,
                        qy: null
                    }, d = function (a, b) {
                        if (!a) return ["C", b.x, b.y, b.x, b.y, b.x, b.y];
                        !(a[0] in {
                            T: 1,
                            Q: 1
                        }) && (b.qx = b.qy = null);
                        switch (a[0]) {
                            case "M":
                                b.X = a[1];
                                b.Y = a[2];
                                break;
                            case "A":
                                a = ["C"].concat(V.apply(0, [b.x, b.y].concat(a.slice(1))));
                                break;
                            case "Z":
                                a = ["C"].concat([b.x, b.y, b.X, b.Y, b.X, b.Y])
                        }
                        return a
                    }, e = function (a, c) {
                        if (a[c].length > 7) {
                            a[c].shift();
                            for (var d = a[c]; d.length;) a.splice(c++, 0, ["C"].concat(d.splice(0, 6)));
                            a.splice(c, 1);
                            h = Math.max(b.length,
                            0)
                        }
                    }, g = function (a, c, d, e, g) {
                        if (a && c && a[g][0] == "M" && c[g][0] != "M") c.splice(g, 0, ["M", e.x, e.y]), d.bx = 0, d.by = 0, d.x = a[g][1], d.y = a[g][2], h = Math.max(b.length, 0)
                    }, f = 0,
                    h = Math.max(b.length, 0);
                for (; f < h; f++) {
                    b[f] = d(b[f], a);
                    e(b, f);
                    g(b, void 0, a, c, f);
                    g(void 0, b, c, a, f);
                    var j = b[f],
                        k = j.length;
                    a.x = j[k - 2];
                    a.y = j[k - 1];
                    a.bx = parseFloat(j[k - 4]) || a.x;
                    a.by = parseFloat(j[k - 3]) || a.y;
                    c.bx = void 0;
                    c.by = void 0;
                    c.x = void 0;
                    c.y = void 0
                }
                return b
            }, Q = function (a) {
                a.shadow = a.node.cloneNode(!0);
                var b = a.shadow,
                    c = a.node,
                    d = ["strokeweight", "coordsize",
                        "coordorigin", "filled", "stroked"];
                b.filled = "f";
                b.stroked = "f";
                for (var e = 0, g = d.length; e < g; e++) c[d[e]] && (b[d[e]] = c[d[e]]);
                b.style.cssText = c.style.cssText;
                (b.tagName + "").indexOf("shape") > -1 && (b.path = c.path);
                b.style.left = parseInt(b.style.left || 0) + 4 + "px";
                b.style.top = parseInt(b.style.top || 0) + 4 + "px";
                a.shadow.style.filter = "progid:DXImageTransform.Microsoft.Blur(pixelRadius=6,makeShadow=true,shadowOpacity=0.4)";
                a.group.insertBefore(a.shadow, a.node)
            }, A = function (b) {
                a(this);
                var c = e.createElement("div"),
                    d, g, f;
                b && b.constructor && b.constructor == s ? (f = p(b.box, b.attrs["stroke-width"] || 1), d = c, g = b.group, this.elements = [b], this.x = f.x + f.width / 2, this.y = f.y + f.height / 2, b.setSizeOrigin(1E3, 1E3, 10, 10, "0 0")) : (f = {
                    x: 0,
                    y: 0,
                    width: b && b.offsetWidth || 0,
                    height: b && b.offsetHeight || 0
                }, d = b, g = c, this.elements = [], this.x = this.y = 0, b && (this.handleset = !0));
                c.style.cssText = "position:absolute;left:0;top:0;overflow:visible;width:0;height:0";
                this.box = f;
                this.group = this.element = c;
                d && d.appendChild(g)
            }, c = A.prototype;
        c.shadow = function (a) {
            if (a ==
                "off") {
                if (this.filter.on) for (var a = 0, c = this.elements.length; a < c; a++) this.elements[a].shadow && (this.elements[a].shadow.style.display = "none");
                this.filter.on = !1
            } else {
                b.is(a, "object");
                if (!this.filter) {
                    this.filter = {
                        d: 6,
                        devi: 6,
                        opacity: 0.5
                    };
                    a = 0;
                    for (c = this.elements.length; a < c; a++) Q(this.elements[a]);
                    this.filter.on = !0
                }
                if (!this.filter.on) {
                    a = 0;
                    for (c = this.elements.length; a < c; a++) this.elements[a].shadow ? this.elements[a].shadow.style.display = "" : Q(this.elements[a])
                }
                this.filter.on = !0
            }
            return this
        };
        c.append = function (a) {
            a.paper = this;
            a.setSizeOrigin(1E3, 1E3, 10, 10, "0 0");
            this.mergeBox(a.box, a.attrs["stroke-width"] || 1);
            var b = this.s_x,
                c = this.s_y,
                d = this.x,
                e = this.y,
                g = a.attrs["stroke-width"],
                f = a.group.coordsize.split(" ");
            a.group.coordsize = 1 / b * (a.group.coordsize.x || f[0]) + " " + 1 / c * (a.group.coordsize.y || f[1]);
            a.group.coordorigin = d * a.fx * (1 - 1 / b) + " " + e * a.fy * (1 - 1 / c);
            g && (a.attr("stroke-width", g * Math.max(b, c)).attrs["stroke-width"] = g);
            this.element.appendChild(a.group);
            this.filter && this.filter.on && (a.shadow ? a.shadow.style.display = "" : Q(a));
            this.elements.push(a);
            return this
        };
        c.mergeBox = function (a, b) {
            var c = q(this.box, 0);
            this.box = c = p(r(c, a, b), 0);
            this.offsetWidth = this.box.width;
            this.offsetHeight = this.box.height;
            if (!this.handleset) this.x = c.x + c.width / 2, this.y = c.y + c.height / 2
        };
        c.updateBox = function (a) {
            var b = this.elements,
                c = b.length,
                d = [this.x, this.y, this.x, this.y];
            this.handleset || b[0] && (d = [b[0].box[0], b[0].box[1], b[0].box[2], b[0].box[3]]);
            if (a) {
                for (var a = [], e = 0; e < c; e++) b[e].paper === this && (d = r(d, b[e].box, b[e].attrs["stroke-width"] || 1), a.push(b[e]));
                this.elements = a
            } else for (; c--;) d = r(d, b[c].box, b[c].attrs["stroke-width"] || 1);
            this.box = d = p(d, 0);
            this.offsetWidth = this.box.width;
            this.offsetHeight = this.box.height;
            if (!this.handleset) this.x = d.x + d.width / 2, this.y = d.y + d.height / 2
        };
        c.scale = function (a, b) {
            arguments.length - 1 || (b = a);
            for (var c, d = this.elements, e = d.length, g = this.s_x, f = this.s_y, h = this.x, j = this.y, a = Math.abs(a || g), b = Math.abs(b || f); e--;) if (d[e].group.coordsize = g / a * d[e].group.coordsize.x + " " + f / b * d[e].group.coordsize.y, d[e].group.coordorigin = h * d[e].fx * (1 - 1 / a) + " " + j * d[e].fy * (1 - 1 / b), c = d[e].attrs["stroke-width"]) d[e].attr("stroke-width", c * Math.max(a, b)).attrs["stroke-width"] = c;
            this.s_x = a;
            this.s_y = b;
            return this
        };
        s = function (a, b, c, d) {
            this.box = [0, 0, 0, 0];
            this.rect = {
                x: 0,
                y: 0,
                width: 0,
                height: 0
            };
            this.node = a;
            this.group = b;
            a.grapher = this;
            this.attrs = this.attrs || {};
            c(this);
            d && d.append(this)
        };
        d = s.prototype;
        d.show = function () {
            this.group.style.display = "block";
            return this
        };
        d.hide = function () {
            this.group.style.display = "none";
            return this
        };
        var v = function (a, b) {
            return a.element.removeChild(b.group)
        },
        E = function (a, b) {
            var c = I("group");
            c.style.cssText = "position:absolute;left:0;top:0;width:1000px;height:1000px";
            var d = I("shape"),
                e = d.style;
            e.width = "1000px";
            e.height = "1000px";
            c.appendChild(d);
            return new s(d, c, function (b) {
                var c = {
                    fill: "none",
                    stroke: "#000"
                };
                b.setSizeOrigin = function (a, b, c, d, e) {
                    this.node.coordsize = a + " " + b;
                    this.group.coordsize = c * a + " " + d * b;
                    this.group.coordorigin = this.node.coordorigin = e;
                    this.fx = c;
                    this.fy = d
                };
                if (a) c.path = a, a[1] && (b.box = a[1]);
                b.rect = p(b.box, 1);
                b.type = "path";
                H(b, c)
            }, b)
        }, J = function (a,
        b, c, d, e) {
            var g = I("group"),
                f = I("oval"),
                h = f.style;
            g.style.cssText = "position:absolute;left:0;top:0;width:1000px;height:1000px";
            g.appendChild(f);
            return new s(f, g, function (e) {
                e.setSizeOrigin = function (a, b, c, d, e) {
                    this.group.coordsize = a + " " + b;
                    this.group.coordorigin = e;
                    this.fx = this.fy = 1
                };
                e.type = c == d ? "circle" : "ellipse";
                H(e, {
                    stroke: "#000",
                    fill: "none"
                });
                e.attrs.cx = a;
                e.attrs.cy = b;
                e.attrs.rx = c;
                e.attrs.ry = d;
                e.box = [a - c, b - d, a + c, b + d];
                e.rect = p(e.box, 1);
                h.left = e.box[0] + "px";
                h.top = e.box[1] + "px";
                h.width = 2 * c + "px";
                h.height = 2 * d + "px"
            }, e)
        }, X = function (a, b, c, d, e, g) {
            e = e || g;
            return (g = g || (e = e || 0)) ? "M" + (a + e) + "," + b + ",l" + (c - e * 2) + ",0a" + e + "," + g + ",0,0,1," + e + "," + g + "l0," + (d - g * 2) + "a" + e + "," + g + ",0,0,1," + -e + "," + g + "l" + (e * 2 - c) + ",0a" + e + "," + g + ",0,0,1," + -e + "," + -g + "l0," + (g * 2 - d) + "a" + e + "," + g + ",0,0,1," + e + "," + -g + "z" : "M" + a + "," + b + "l" + c + ",0,0," + d + "," + -c + ",0z"
        }, F = function (a, b, c, d, e, g, f) {
            var h = N(X(a, b, c, d, e, g)),
                f = E([h, [a, b, a + c, b + d]], f),
                h = f.attrs;
            h.x = a;
            h.y = b;
            h.width = c;
            h.height = d;
            h.rx = e;
            h.ry = g;
            f.type = e || g ? "roundrect" : "rect";
            return f
        }, H = function (a, c) {
            var d,
            e, g, f, h, j, k, l = a.paper,
                m = a.node,
                n = l && l.filter && l.filter.on && a.shadow,
                s = a.attrs,
                E = [],
                r = [],
                t = {
                    data: 1,
                    points: 1,
                    commands: 1
                }, q;
            for (q in c) if (c.hasOwnProperty(q)) {
                var v = c[q];
                t[q] || (s[q] = v);
                switch (q) {
                    case "cursor":
                        m.style.cursor = v;
                        break;
                    case "points":
                        d || (v = b.formatPath(c.commands, v), d = !0);
                    case "commands":
                        d || (v = b.formatPath(v, c.data), d = !0);
                    case "data":
                        d || (v = b.formatPath(c.commands, v), d = !0);
                    case "path":
                        if (a.type == "path" && u(v)) v = v || [], s.path = o([v[0] || "M0,0", a.box = v[1]]), e = s.path + "", d = !0;
                        break;
                    case "stroke":
                        r.color = v;
                        g = 1;
                        break;
                    case "stroke-width":
                        r.weight = (parseFloat(v) || 1) * 0.75;
                        g = 1;
                        break;
                    case "stroke-opacity":
                        r.opacity = v;
                        g = 1;
                        break;
                    case "fill-opacity":
                        if (E.opacity = v, f = 1, v > 0 && !c.hasOwnProperty("fill") && s.fill == "none") q = "fill", v = "#000", s[q] = v;
                        else break;
                    case "fill":
                        E.color = v;
                        f = 1;
                        s.path && (v != "none" ? RegExp("s*" + z + "s*$", "i").test(s.path[0]) || (s.path[0] += z, e = s.path + "") : (s.path[0] = s.path[0].replace(z, ""), e = s.path + ""));
                        break;
                    case "x":
                    case "y":
                    case "cx":
                    case "cy":
                    case "width":
                    case "height":
                    case "r":
                    case "rx":
                    case "ry":
                        k = 1;
                        break;
                    default:
                        /^style\.\w+$/.test(q) ? m.style[q.replace("style.", "")] = v : m.setAttribute(q, v)
                }
            }
            if (g) {
                (g = (g = m.getElementsByTagName("stroke")) && g[0]) || (h = g = I("stroke"));
                g.on = !! (s.stroke && s.stroke != "none");
                if (g.on) for (var F in r) g[F] = r[F];
                h && m.appendChild(g);
                if (n) n.strokeweight = m.strokeweight, n.stroked = m.stroked || "f"
            }
            if (f) {
                (f = (f = m.getElementsByTagName("fill")) && f[0]) || (j = f = I("fill"));
                f.on = !! (s.fill && s.fill != "none");
                if (f.on) for (F in E) f[F] = E[F];
                j && m.appendChild(f);
                n && (n.filled = m.filled || "f")
            }
            if (k) if (g = f = c.hasOwnProperty("r") && ((s.rx = s.ry = s.r) || 1), c.hasOwnProperty("rx") && (g = (s.rx = c.rx) || 1), c.hasOwnProperty("ry") && (f = (s.ry = c.ry) || 1), a.type.indexOf("rect") > -1) m.path = (s.path = o([N(X(s.x, s.y, s.width, s.height, s.rx, s.ry)), [s.x, s.y, s.x + s.width, s.y + s.height]])) + "", n && (n.path = m.path);
            else {
                a.box = [s.cx - s.rx, s.cy - s.ry, s.cx + s.rx, s.cy + s.ry];
                if (g || c.hasOwnProperty("cx")) m.style.left = a.box[0] + "px", g && (m.style.width = 2 * s.rx + "px");
                if (f || c.hasOwnProperty("cy")) m.style.top = a.box[1] + "px", f && (m.style.height = 2 * s.ry + "px");
                if (n) n.style.sccText = m.style.cssText, n.style.left = parseInt(n.style.left || 0) + a.filter.dx + "px", n.style.top = parseInt(n.style.top || 0) + a.filter.dy + "px"
            }
            if (e) m.path = e.toLowerCase().replace("z", "x").replace("e", "") + " e", n && (n.path = m.path);
            if (d || c.hasOwnProperty("stroke-width") || k) a.rect = p(a.box, s["stroke-width"] || 1), l && l.updateBox()
        }
    }
    d.appendTo = function (a, b) {
        if (a === this.paper || a.graphic && this.paper === a.graphic) return this;
        this.paper && this.paper.remove(this);
        a.graphic && !b ? a.graphic.append(this) : a.constructor == A ? a.append(this) : this.makeCanvas(a);
        return this
    };
    d.attr = function (a, c) {
        if (arguments.length == 1) if (b.is(a, "string")) return this.attrs[a];
        else H(this, a);
        else {
            var d = {};
            d[a] = c;
            H(this, d)
        }
        return this
    };
    d.remove = function () {
        var a;
        if (a = this.paper) a.remove(this), a.elements.length || a.remove()
    };
    d.makeCanvas = function (a) {
        this.paper = new A(this);
        this.paper.appendTo(a);
        return this.paper
    };
    d.addPath = function (a) {
        if (this.type != "path" || !a) return this;
        var c = this.attrs.path,
            d = t();
        u(a) || ((c = /\d+\,\d+\s*$/.exec(c + "")) && (c = c[0].replace(/\s/g,
            "").split(",")), c && c.length == 2 ? (c[0] = d(c[0] * 1), c[1] = d(c[1] * 1)) : c = [], a = b.formatPath.apply(b, a.concat([c])));
        u(a) && (c = this.attrs.path || ["", a[1]], a = [c[0] + a[0], r(c[1], a[1], 0)], H(this, {
            path: a
        }));
        return this
    };
    d.forPath = function (a, c) {
        !(arguments.length - 1) && !b.is(a, "function") && (c = a, a = 0);
        if (this.type != "path") return c ? !1 : this;
        for (var d, e, g, f = [], h = [], k = (this.attrs.path + "").split(" "), a = t(a), l = 0, m = k.length; l < m; l++) if (k[l]) {
            d = k[l].replace(/([^\d\,])(\d)/g, "$1,$2").split(",");
            g = d[0] == "c";
            f.push(e = /^\d+$/.test(d[0]) ? f[f.length - 1] || 1 : j[d.shift()]);
            a(e);
            g && (d = G.apply(this, d));
            e = 0;
            for (g = d.length; e < g; e++) h.push(a(d[e] * 1))
        }
        return c ? [f, h] : this
    };
    c.scaleX = function (a) {
        this.scale(a)
    };
    c.scaleY = function (a) {
        this.scale(0, a)
    };
    c.remove = function (a) {
        a ? (a.paper === this && (a.paper = v(this, a) && null), this.updateBox(1)) : this.element.parentNode && this.element.parentNode.removeChild(this.element);
        return this
    };
    c.clear = function () {
        for (var a = this.elements, b = 0, c = a.length; b < c; b++) if (a[b].paper === this) a[b].paper = v(this, a[b]) && null;
        this.elements = [];
        this.updateBox();
        this.temp_path = 0;
        this.draw_xy = [0, 0];
        this.path_commands = [
            [],
            []
        ];
        return this
    };
    c.appendTo = function (a) {
        this.parent && this.parent.graphics && (this.remove().parent.graphics = null);
        a && (((a.graphics = this).parent = a).appendChild ? a.appendChild(this.element) : a.append && a.append(this.element));
        return this
    };
    c.show = function () {
        this.element.style.display = "block";
        return this
    };
    c.hide = function () {
        this.element.style.display = "none";
        return this
    };
    c.beginFill = function (a, b) {
        this.temp_path = 0;
        this.draw_style.fill = a || "#000";
        this.draw_style["fill-opacity"] = b || 1;
        return this
    };
    c.endFill = function () {
        this.temp_path = 0;
        delete this.draw_style.fill;
        delete this.draw_style["fill-opacity"];
        return this
    };
    c.lineStyle = function (a, b, c) {
        this.temp_path = 0;
        this.draw_style["stroke-width"] = a || 1;
        this.draw_style.stroke = b || "#000";
        this.draw_style["stroke-opacity"] = c || 1;
        return this
    };
    c.drawPath = function () {
        E(b.formatPath.apply(b, arguments), this).attr(this.draw_style);
        return this
    };
    c.drawCircle = function (a, b, c) {
        J(a, b, c, c, this).attr(this.draw_style);
        return this
    };
    c.drawEllipse = function (a, b, c, d) {
        J(a, b, c, d, this).attr(this.draw_style);
        return this
    };
    c.drawRect = function (a, b, c, d) {
        F(a, b, c, d, 0, 0, this).attr(this.draw_style);
        return this
    };
    c.drawRoundRect = function (a, b, c, d, e, g) {
        F(a, b, c, d, e, g || e, this).attr(this.draw_style);
        return this
    };
    c.lineTo = function (a, b, c) {
        !this.path_commands[0].length && (!c || !this.temp_path) && this.moveTo.apply(this, this.draw_xy);
        this.path_commands[0].push(2);
        this.path_commands[1].push(a, b);
        this.draw_xy = [a, b];
        c && this.drawAll();
        return this
    };
    c.curveTo = function (a, b, c, d, e) {
        !this.path_commands[0].length && (!e || !this.temp_path) && this.moveTo.apply(this, this.draw_xy);
        this.path_commands[0].push(3);
        this.path_commands[1].push(a, b, c, d);
        this.draw_xy = [a, b];
        e && this.drawAll();
        return this
    };
    c.moveTo = function (a, b) {
        this.path_commands[0].push(1);
        this.path_commands[1].push(a, b);
        this.draw_xy = [a, b];
        return this
    };
    c.drawAll = function () {
        if (this.path_commands[1].length) {
            if (!this.temp_path) this.temp_path = E(0, this).attr(this.draw_style);
            this.temp_path.addPath(this.path_commands)
        }
        this.path_commands = [
            [],
            []
        ];
        return this
    };
    b.Circle = function (a, b, c) {
        return J(a, b, c, c)
    };
    b.Ellipse = function (a, b, c, d) {
        return J(a, b, c, d)
    };
    b.Path = function () {
        return E(b.formatPath.apply(b, arguments))
    };
    b.Marker = function (a) {
        return M(a)
    };
    b.Rect = function (a, b, c, d) {
        return F(a, b, c, d)
    };
    b.RoundRect = function (a, b, c, d, e, g) {
        return F(a, b, c, d, e, g || e)
    };
    b.setDrawleble = function (a, b) {
        if (!a) return new A;
        arguments.length - 1 ? (a.graphics = new A, a.childNodes.length && b ? a.insertBefore(a.graphics.element, a.childNodes.item(b)) : a.appendChild(a.graphics.element)) : a.graphics = new A(a);
        a.graphics.parent = a;
        a.graphics.renew = function () {
            var a = this.renew,
                b = this.parent;
            this.remove();
            b.graphics = new A(b);
            b.graphics.parent = b;
            b.graphics.renew = a;
            return b.graphics
        };
        return a.graphics
    };
    b.formatPath = function (a, c, d) {
        var e, f, h = 0,
            j = [];
        if (!(arguments.length - 1) || !a && (a = c)) c = a, a = [1], e = 1, a.length = Math.floor(c.length / 2);
        b.is(c, "array") || (c = [0, 0]);
        b.is(a, "array") || (a = [1]);
        c[0] && c[0].hasOwnProperty("x") ? (e && (a.length = Math.floor(c.length)), e = [c[0].x, c[0].y, c[1].x, c[1].y], f = g, a[0] == 3 && (c.unshift(d && (d.hasOwnProperty("x") ? d : {
            x: d[0],
            y: d[1]
        }) || {
            x: 0,
            y: 0
        }), h = 1)) : (e = [c[0], c[1], c[0], c[1]], f = n, a[0] == 3 && (c.unshift.apply(c, d && (d.hasOwnProperty("x") ? [d.x, d.y] : d) || [0, 0]), h = 2));
        for (var k = 0, l = a.length; k < l; k++) h = f[a[k] || 2](c, h, j, e);
        return [j.join(""), e]
    };
    b.clipLine = function (a, b) {
        var c = {
            x: a[0],
            y: a[1]
        }, d = {
            x: a[2],
            y: a[3]
        }, e = d.x - c.x,
            g = d.y - c.y;
        if (e == 0 && g == 0) return c;
        else {
            var f;
            f = {
                "in": 0,
                out: 1
            };
            var h, j = [],
                k = [];
            if (y(e, b[0] - c.x, f) && y(-e, c.x - b[2], f) && y(g, b[1] - c.y, f) && y(-g, c.y - b[3], f)) {
                h = f["in"];
                var l = f.out,
                    m = c.x,
                    s = c.y;
                f = [c.x, c.y, d.x, d.y];
                h > 0 && (f[0] = m + e * h, f[1] = s + g * h, h = [(c.x - b[0]) * (f[0] - b[0]) <= 0 && f[0] - b[0], (c.y - b[1]) * (f[1] - b[1]) <= 0 && f[1] - b[1], (c.x - b[2]) * (f[0] - b[2]) <= 0 && f[0] - b[2], (c.y - b[3]) * (f[1] - b[3]) <= 0 && f[1] - b[3]], h.join("") != "falsefalsefalsefalse" && (j = x(c, {
                    x: f[0],
                    y: f[1]
                }, b, h)));
                l < 1 && (f[2] = m + e * l, f[3] = s + g * l, h = [(f[2] - b[0]) * (d.x - b[0]) <= 0 && f[2] - b[0], (f[3] - b[1]) * (d.y - b[1]) <= 0 && f[3] - b[1], (f[2] - b[2]) * (d.x - b[2]) <= 0 && f[2] - b[2], (f[3] - b[3]) * (d.y - b[3]) <= 0 && f[3] - b[3]], h.join("") != "falsefalsefalsefalse" && (k = x({
                    x: f[2],
                    y: f[3]
                }, d, b, h)));
                return j.concat(f, k)
            }
            h = [(c.x - b[0]) * (d.x - b[0]) <= 0, (c.y - b[1]) * (d.y - b[1]) <= 0, (c.x - b[2]) * (d.x - b[2]) <= 0, (c.y - b[3]) * (d.y - b[3]) <= 0];
            return h.join("") == "falsefalsefalsefalse" ? !1 : f = x(c, d, b, h)
        }
    };
    b.Balloon = function (a) {
        function c() {
            h.style.display = "none";
            return !1
        }
        function d(a) {
            PGmap.Events.stopEvent(a)
        }
        var a = a || {}, e, g, f = 10 - Math.sqrt(50),
            h = PGmap.Base.createAbs(),
            j = PGmap.Base.createAbs();
        g = PGmap.Base.createAbs();
        h.style.cssText += "z-index:4;";
        j.style.cssText += "left:" + f + "px; bottom:" + (f + 10) + "px;";
        g.style.cssText += "left:0px;bottom:0px;";
        b.Path([0, -20, 0, 0, 20, -20]).attr({
            fill: "#fff",
            stroke: 0
        }).appendTo(g);
        var k = b.RoundRect(0, 0, 10, 10, 10).attr({
            fill: "#fff",
            stroke: 0
        }).appendTo(h);
        this.graph = k;
        k.paper.shadow();
        h.appendChild(g);
        h.appendChild(j);
        this.isClosing = a.hasOwnProperty("isClosing") ? a.isClosing : !1;
        this.isHidden = a.hasOwnProperty("isHidden") ? a.isHidden : !1;
        j.innerHTML = '<b style="display:none" class="g-closer m-closer-balloon"><i class="g-closer__icon"></i></b><span></span>';
        if (this.isClosing) e = j.firstChild, e.style.cssText = "", PGmap.Events.addHandler(e, "mousedown", c);
        PGmap.Events.addHandler(h, "mousedown", d);
        g = j.lastChild;
        this.element = h;
        this.content = g;
        this.clean = function () {
            PGmap.Events.removeHandler(e, "mousedown", c);
            PGmap.Events.removeHandler(h, "mousedown", d)
        };
        this.setSize = function (a, b) {
            k.attr({
                width: a + 2 * f,
                height: b + 2 * f,
                y: -b - 10 - 2 * f
            });
            j.style.width = a + "px";
            j.style.height = b + "px"
        };
        this.getSize = function () {
            return PGmap.Utils.getSize(this.rr.paper)
        };
        b.is(a.content, "string") ? g.innerHTML = '<div style="padding:15px 17px;">' + a.content + "</div>" : g.appendChild(a.content);
        PGmap.Events.resizeEvent.add(g, function () {
            if (h.style.display != "none") {
                var a = j.offsetHeight;
                k.attr({
                    height: a + 2 * f,
                    y: -a - 10 - 2 * f,
                    width: j.offsetWidth + 2 * f
                })
            }
        })
    };
    b.Balloon.prototype = PGmap.BasicGeom
};
PGmap.Balloon = function (a) {
    function b(a) {
        PGmap.Events.stopEvent(a)
    }
    function c() {
        PGmap.Utils.addClass(l, "PGmap-balloon-hidden")
    }
    if (a && a.graphic) return new PGmap.Graphics.Balloon(a);
    var d, e = PGmap.Base.balloonDiv(a && a.type || null),
        f = e.getElementsByTagName("span")[0],
        l = f.parentNode,
        m = PGmap.Events.addHandler,
        h = PGmap.Events.removeHandler,
        j = PGmap.EventFactory.eventsType;
    this.coord = new PGmap.Coord(0, 0);
    this.isClosing = a && a.isClosing != void 0 ? a.isClosing : !1;
    this.shift = a.shift || !0;
    this.isHidden = a && a.isHidden != void 0 ? a.isHidden : !1;
    if (a && (a.content && typeof a.content == "string" ? f.innerHTML = '<div style="padding:14px 12px;">' + a.content + "</div>" : a.content && a.content.tagName && f.appendChild(a.content), this.isClosing)) d = e.getElementsByTagName("b")[0], d.style.display = "", m(d, j.mousedown, c);
    m(e, j.mousedown, b);
    l.style.cssText += "; width: 249px";
    this.element = e;
    this.container = l;
    this.content = f;
    this.clean = function () {
        h(d, j.mousedown, c);
        h(e, j.mousedown, b)
    };
    c()
};
PGmap.Balloon.prototype = PGmap.BasicGeom;
PGmap.Balloon.prototype.open = function (a) {
    var b = this.globals;
    this.currEl = a;
    this.coord = a.coord;
    this.setContent(a.name);
    this.update();
    PGmap.Utils.removeClass(this.container, "PGmap-balloon-hidden");
    if (this.shift) {
        var a = this.getSize(),
            c = b.getWindow(),
            d = PGmap.Utils.getOffset(b.mainElement()),
            e = PGmap.Utils.getOffset(this.element.childNodes[0]),
            f = b.getCoords(),
            f = b.lonlatToXY(f.lon, f.lat);
        f.x = e.left + a.width > c.width + d.left ? f.x - (c.width + d.left - e.left - a.width) : f.x;
        f.y = e.top < d.top ? f.y - a.height - 20 : f.y;
        e.left < d.left && (f.x -= d.left - e.left);
        b.mainObject.layers.setCenterFast(b.xyToLonLat(f.x, f.y))
    }
};
PGmap.Balloon.prototype.close = function () {
    PGmap.Utils.addClass(this.container, "PGmap-balloon-hidden")
};
PGmap.Balloon.prototype.getSize = function () {
    return PGmap.Utils.getSize(this.element.childNodes[0])
};
PGmap.Balloon.prototype.setSize = function (a, b) {
    a && b && typeof a == "number" && typeof b == "number" && (this.element.style.cssText += "; width: " + a + "px; height: " + b + "px;")
};
PGmap.Balloon.prototype.setContent = function (a) {
    if (a && typeof a == "string") this.content.innerHTML = '<div class="PGmap-balloon-inner">' + a + "</div>";
    this.update()
};
PGmap.BalloonGeom = function () {
    var a = this.element,
        b = PGmap.Events.addHandler,
        c = PGmap.Utils.getElementsByClassName,
        d = this;
    this.currEl = null;
    this.content.innerHTML = '<div style="display: block; margin: 4px 6px"><span style="font: 1.8em  Arial;" class="PGmap-geom-name"></span><br /><span class="PGmap_geom_edit" style="">\u0420\u0435\u0434\u0430\u043a\u0442\u0438\u0440\u043e\u0432\u0430\u0442\u044c</span><span class="PGmap_geom_edit" style="">\u041f\u0435\u0440\u0435\u043c\u0435\u0441\u0442\u0438\u0442\u044c</span><span class="PGmap_geom_edit" style="">\u0423\u0434\u0430\u043b\u0438\u0442\u044c</span></div>';
    c("PGmap-layer-container-geometry",
    document)[0].appendChild(a);
    b(c("PGmap_geom_edit", this.element)[0], "click", function () {
        d.currEl.editableOn();
        PGmap.EditBox.open(d.currEl.tempPolyline || d.currEl);
        d.hide()
    });
    b(c("PGmap_geom_edit", this.element)[1], "click", function () {
        d.currEl.dragOnce();
        d.hide()
    });
    b(c("PGmap_geom_edit", this.element)[2], "click", function () {
        d.currEl.parent.remove(d.currEl);
        d.hide()
    });
    this.hide()
};
PGmap.BalloonGeom.prototype = new PGmap.Balloon({
    isClosing: !0
});
PGmap.BalloonGeom.prototype.setContent = function (a) {
    if (a && typeof a == "string") PGmap.Utils.getElementsByClassName("PGmap-geom-name", this.content)[0].innerHTML = a
};
PGmap.BalloonGeom.prototype.open = function (a, b) {
    var c = a.globals.getPosition(),
        b = b || window.event,
        d = PGmap.Utils.getOffset(a.globals.mapDOM),
        e = (b.pageY || b.clientY) - d.top - c.top;
    this.element.style.left = (b.pageX || b.clientX) - d.left - c.left + "px";
    this.element.style.bottom = -e + "px";
    this.setContent(a.name);
    PGmap.Utils.removeClass(this.container, "PGmap-balloon-hidden");
    this.show();
    this.currEl = a
};
PGmap.Point = function (a, b) {
    if (!a.coord || !(a.coord instanceof PGmap.Coord)) throw "Coord is not defined";
    var c = PGmap.Base.createAbs(),
        d = this,
        e = a.width || "22",
        f = a.height || "24",
        l = PGmap.Base.placemarkDiv();
    c.appendChild(l);
    l.style.zIndex = 73;
    a.innerImage ? (e = PGmap.Base.tileImg(), f = a.innerImage, e.src = f.src, e.style.width = f.width ? f.width + "px" : "", e.style.height = f.height ? f.height + "px" : "", e.style.marginLeft = f.width ? -f.width / 2 + "px" : "", e.style.marginTop = f.height ? -f.height / 2 + "px" : "", l.appendChild(e)) : (l.style.marginLeft = -e / 2 + "px", l.style.marginTop = -f / 2 + "px", l.style.width = e + "px", l.style.height = f + "px", l.style.backgroundImage = "url(" + (a.url || "http://js.tmcrussia.com/img/cluster_sprite.png") + ")", l.style.backgroundPosition = a.backpos || "-191px 0", l.style.backgroundRepeat = "no-repeat");
    b && b();
    e = typeof a.draggable == "function" ? a.draggable : function () {
        for (var a = l.getElementsByTagName("i"), b = a.length; b--;) if (a[b].className.indexOf("b-balloon__ar") > -1) return a[b];
        return l
    };
    this.globals = null;
    this.element = c;
    this.container = l;
    this.balloon = null;
    this.name = "\u0422\u043e\u0447\u043a\u0430";
    this.top = this.left = null;
    this.coord = a.coord;
    if (a.draggable) this.draggable = new PGmap.Events.Draggable([e() || this, this], {
        drag: function (a) {
            this[1].coord = this[1].globals.xyToLonLat(a.x, a.y)
        },
        click: function (a) {
            a && PGmap.Events.fixEvent(a);
            d.balloon = d.globals && d.globals.mapObject().balloon;
            if (!d.balloon.element.offsetHeight || d.balloon.currEl != d) {
                if (d.balloon.currEl) d.balloon.currEl.container.style.zIndex = "72";
                d.balloon.open(d);
                l.style.zIndex = "75"
            } else d.balloon.close(),
            l.style.zIndex = "72";
            a && a.stopPropagation()
        }
    });
    this.toggleBalloon = function (a) {
        a && (a = PGmap.Events.fixEvent(a), a.preventDefault(), a.stopPropagation());
        if (!d.balloon.element.offsetHeight || d.balloon.currEl != d) {
            if (d.balloon.currEl) d.balloon.currEl.container.style.zIndex = "72";
            d.balloon.open(d);
            l.style.zIndex = "75"
        } else d.balloon.close(), l.style.zIndex = "72"
    };
    this.addHandlers = function () {
        var a = this;
        PGmap.Events.addHandlerByName(this.container, "mouseover", function () {
            a.container.style.backgroundPosition = "-185px -35px"
        },
            "mouseover_" + this.name);
        PGmap.Events.addHandlerByName(this.container, "mouseout", function () {
            a.container.style.backgroundPosition = "-185px 0px"
        }, "mouseout_" + this.name)
    };
    this.removeHandlers = function () {
        PGmap.Events.removeHandlerByName(this.container, "mouseover", "mouseover" + this.name);
        PGmap.Events.removeHandlerByName(this.container, "mouseout", "mouseout" + this.name)
    }
};
PGmap.Utils.extendMethods(PGmap.Point.prototype, PGmap.BasicGeom);
PGmap.Point.prototype.addContent = function (a) {
    var b = PGmap.EventFactory.eventsType;
    this.name = a;
    this.balloon = this.globals && this.globals.mapObject().balloon;
    this.container && !this.draggable && PGmap.Events.addHandler(this.container, b.mousedown, this.toggleBalloon)
};
PGmap.Point.prototype.clean = function () {
    this.removeHandlers();
    this.balloon && this.balloon.currEl == this && this.balloon.close();
    this.balloon = null
};
PGmap.Rectangle = function (a, b) {
    b && b()
};
PGmap.Utils.extendMethods(PGmap.Rectangle.prototype, PGmap.BasicGeom);
PGmap.Polyline = function (a, b) {
    a = a || {};
    this.points = a.points || [];
    this.style = {
        color: "#017E8D",
        lineHeight: 5,
        lineOpacity: 1
    };
    PGmap.Utils.extendMethods(this.style, a.style || {});
    this.graphic = PGmap.Graphics.Path().attr({
        "stroke-width": this.style.lineHeight,
        stroke: this.style.color,
        "stroke-opacity": this.style.lineOpacity,
        "stroke-dasharray": this.style.dashArray
    });
    this.globals = null;
    this.real_coords = [];
    this.real_box = [];
    this.offset = 2;
    this.name = "\u041f\u043e\u043b\u0438\u043b\u0438\u043d\u0438\u044f";
    this.element = this.graphic.node;
    this.top = this.editPoint = this.left = null;
    this._movupdate = function () {
        var a = [],
            b, e = this.offset,
            f = this.globals,
            l = this.graphic,
            m = this.real_coords,
            h = this.real_box,
            f = [f.getPosition(), f.getWindow()],
            f = [-f[0].left - e * f[1].width, -f[0].top - e * f[1].height, f[1].width - f[0].left + e * f[1].width, f[1].height - f[0].top + e * f[1].height];
        if (!(f[0] > h[2] || f[2] < h[0] || f[3] < h[1] || f[1] > h[3])) {
            e = 0;
            for (h = m.length - 1; e < h; e++) {
                var j = PGmap.Graphics.clipLine([m[e].x, m[e].y, m[e + 1].x, m[e + 1].y], f);
                if (j) {
                    b = b && b[b.length - 2] == j[0] && b[b.length - 1] == j[1] ? 2 : 0;
                    for (var g = j.length - 1; b < g; b += 2) a.push({
                        x: j[b],
                        y: j[b + 1]
                    })
                }
                b = j
            }
        }
        a.length > 1 ? l.attr("points", a).show() : l.hide()
    };
    a.draggable && this.draggable(1);
    a.editable && this.editable();
    b && b()
};
PGmap.Utils.extendMethods(PGmap.Polyline.prototype, PGmap.BasicGeom);
PGmap.Polyline.prototype.update = function () {
    var a = [],
        b, c, d;
    b = this.offset;
    var e = this.globals,
        f = Math.max,
        l = Math.min,
        m = this.graphic,
        h = this.points,
        j = [e.getPosition(), e.getWindow()],
        j = [-j[0].left - b * j[1].width, -j[0].top - b * j[1].height, j[1].width - j[0].left + b * j[1].width, j[1].height - j[0].top + b * j[1].height];
    b = this.real_coords = [];
    for (var g = 0, n = h.length - 1; g < n; g++) {
        h[g].init ? h[g].init(e) : h[g].update && h[g].update();
        h[g + 1].init ? h[g + 1].init(e) : h[g + 1].update && h[g + 1].update();
        b[g] = e.lonlatToXY(h[g].lon || h[g].coord.lon,
        h[g].lat || h[g].coord.lat);
        b[g + 1] = e.lonlatToXY(h[g + 1].lon || h[g + 1].coord.lon, h[g + 1].lat || h[g + 1].coord.lat);
        d = d ? [l(d[0], b[g].x, b[g + 1].x), l(d[1], b[g].y, b[g + 1].y), f(d[2], b[g].x, b[g + 1].x), f(d[3], b[g].y, b[g + 1].y)] : [b[g].x, b[g].y, b[g].x, b[g].y];
        var k = PGmap.Graphics.clipLine([b[g].x, b[g].y, b[g + 1].x, b[g + 1].y], j);
        if (k) {
            c = c && c[c.length - 2] == k[0] && c[c.length - 1] == k[1] ? 2 : 0;
            for (var o = k.length - 1; c < o; c += 2) a.push({
                x: k[c],
                y: k[c + 1]
            })
        }
        c = k
    }(this.real_box = d) && (d[2] - d[0] > j[2] - j[0] || d[3] - d[1] > j[3] - j[1]) ? this.movupdate = this._movupdate : (this.movupdate = null, a = b);
    a.length > 1 ? m.attr("points", a).show() : m.hide();
    this.left = this.graphic.rect.x;
    this.top = this.graphic.rect.y;
    return this
};
PGmap.Polyline.prototype.clean = function () {
    for (var a = this.points.length; a--;) this.points[a] && this.points[a].remove && (this.points[a].remove() || 1) && this.points[a].clean()
};
PGmap.Polyline.prototype.showPoint = function (a, b) {
    if (this.points[a]) {
        if (b && this.points[a] instanceof PGmap.Point) this.points[a].remove && this.points[a].remove(), this.points[a].clean(), this.points[a] = this.points[a].coord;
        this.points[a] instanceof PGmap.Point || (this.points[a] = new PGmap.Point({
            coord: this.points[a],
            url: "http://js.tmcrussia.com/img/poligon_point.png",
            width: 17,
            height: 18,
            draggable: 1,
            backpos: "0 0"
        }));
        var c = this;
        this.points[a].init && (this.points[a].init = function (a) {
            if (a instanceof PGmap.Globals) this.globals = a, this.update(), this.init = null, this.remove && c.parent && c.parent.element.appendChild(this.element)
        });
        this.points[a].remove = function () {
            c.parent && c.parent.element === this.element.parentNode && c.parent.element.removeChild(this.element);
            this.remove = null
        };
        this.globals && (this.points[a].init ? this.points[a].init(this.globals) : this.parent.element.appendChild(this.points[a].element));
        this.points[a].draggable && this.points[a].draggable.callback("drag", function (a) {
            this[1].coord = this[1].globals.xyToLonLat(a.x, a.y);
            c.update()
        })
    }
    return this
};
PGmap.Polyline.prototype.hidePoint = function (a) {
    this.points[a] instanceof PGmap.Point && this.points[a].remove && this.points[a].remove();
    return this.points[a]
};
PGmap.Polyline.prototype.hidePoints = function () {
    for (var a = 0, b = this.points.length; a < b; a++) this.points[a].coord && this.points[a].remove && this.points[a].remove()
};
PGmap.Polyline.prototype.showPoints = function () {
    for (var a = PGmap.Events.addHandler, b = PGmap.Events.killEvent, c = this, d = this.points.length; d--;) this.showPoint(d);
    for (d = this.points.length; d--;)(function (d) {
        a(d.element, "dblclick", function (a) {
            for (var l = c.points.length; l--;) c.points[l] == d && c.points.splice(l, 1);
            c.parent && c.parent.element === d.element.parentNode && c.parent.element.removeChild(d.element);
            c.update();
            return b(a || window.event)
        })
    })(this.points[d])
};
PGmap.Polyline.prototype.draggable = function () {
    function a() {
        return {
            x: b.left,
            y: b.top
        }
    }
    var b = this,
        c, d;
    this.draggable = new PGmap.Events.Draggable(b.element, {
        dragStart: function (a) {
            c = b.globals.xyToLonLat(a.x, a.y)
        },
        drag: function (e) {
            for (var f = 0, l = b.points.length; f < l; f++) d = b.globals.xyToLonLat(e.x, e.y), b.points[f].coord.lon += d.lon - c.lon, b.points[f].coord.lat += d.lat - c.lat;
            d = a();
            c = b.globals.xyToLonLat(d.x, d.y);
            b.update()
        }
    }, a)
};
PGmap.Polyline.prototype.dragOnce = function () {
    function a() {
        return {
            x: b.left,
            y: b.top
        }
    }
    var b = this,
        c, d;
    this.draggable = new PGmap.Events.Draggable(b.element, {
        dragStart: function (a) {
            c = b.globals.xyToLonLat(a.x, a.y)
        },
        drag: function (e) {
            for (var f = b.points.length; f--;) d = b.globals.xyToLonLat(e.x, e.y), b.points[f].coord.lon += d.lon - c.lon, b.points[f].coord.lat += d.lat - c.lat;
            d = a();
            c = b.globals.xyToLonLat(d.x, d.y);
            b.update()
        },
        dragEnd: function () {
            b.draggable.callback("dragStart", function () {});
            b.draggable.callback("drag", function () {});
            b.draggable.callback("dragEnd", function () {})
        }
    }, a)
};

function VirtualPointContainer(a) {
    function b(b, c, d, e) {
        var b = a.globals.lonlatToXY(b.coord.lon, b.coord.lat),
            c = a.globals.lonlatToXY(c.coord.lon, c.coord.lat),
            d = a.globals.lonlatToXY(d.coord.lon, d.coord.lat),
            j = Math.sqrt(Math.pow(b.x - c.x, 2) + Math.pow(b.y - c.y, 2)),
            g = Math.sqrt(Math.pow(b.x - d.x, 2) + Math.pow(b.y - d.y, 2)),
            n = Math.sqrt(Math.pow(d.x - c.x, 2) + Math.pow(d.y - c.y, 2)),
            k = (j + g + n) / 2,
            j = Math.round(Math.sqrt(k * (k - j) * (k - g) * (k - n)) * 2 / j);
        return Math.min(b.y, c.y) - Math.max(b.y, c.y) < 1 && j <= e + 1 && d.x >= Math.min(b.x, c.x) && d.x <= Math.max(b.x, c.x) ? !0 : Math.min(b.x, c.x) - Math.max(b.x, c.x) < 1 && j <= e + 1 && d.y >= Math.min(b.y, c.y) && d.y <= Math.max(b.y, c.y) ? !0 : j <= e + 1 && d.x >= Math.min(b.x, c.x) && d.x <= Math.max(b.x, c.x) && d.y >= Math.min(b.y, c.y) && d.y <= Math.max(b.y, c.y)
    }
    var c = this;
    this.polyline = a;
    var d = PGmap.Events.killEvent,
        e = PGmap.Events.addHandler;
    (this.reset = function (a) {
        function l() {
            c.point.temporary = !1;
            for (var a = 0, f = c.polyline.points.length; a < f - 1; a++) if (b(c.polyline.points[a], c.polyline.points[a + 1], c.point, c.polyline.style.lineHeight / 2)) {
                var j = c.point;
                j.container.style.backgroundImage = "http://js.tmcrussia.com/img/poligon_point.png";
                j.container.style.width = "17px";
                j.container.style.height = "18px";
                j.container.style.marginLeft = "-8.5px";
                j.container.style.marginTop = "-9px";
                c.polyline.points.splice(a + 1, 0, c.point);
                j.draggable && c.point.draggable.callback("dragStart", function () {});
                e(c.point.element, "dblclick", function (a) {
                    for (var b = 0, e = c.polyline.points.length; b < e; b++) c.polyline.points[b] == j && c.polyline.points.splice(b, 1);
                    c.polyline.parent && c.polyline.parent.element === j.element.parentNode && c.polyline.parent.element.removeChild(j.element);
                    c.polyline.update();
                    return d(a || window.event)
                });
                c.reset(c.point.coords);
                c.polyline.update();
                c.polyline.showPoint(a + 1);
                return !0
            }
            c.point.hide();
            c.reset(c.point.coords);
            return !0
        }
        a = a || new PGmap.Coord(1, 1);
        c.point = new PGmap.Point({
            coord: a,
            url: "http://js.tmcrussia.com/img/poligon_midpoint.png",
            width: 14,
            height: 14,
            draggable: 1,
            backpos: "0 0"
        });
        c.point.draggable && c.point.draggable.callback("dragStart", l);
        c.point.temporary = !0
    })()
}
PGmap.Polyline.prototype.editableOff = function () {
    var a = PGmap.Events.removeHandler;
    this.editPoint && this.editPoint.point.hide();
    this.hidePoints();
    a(this.element, "mouseover", this.onPolylineMouseOver);
    this.update()
};
PGmap.Polyline.prototype.editableOn = function () {
    var a = PGmap.Events.addHandler;
    a(this.element, "mouseover", this.onPolylineMouseOver);
    this.showPoints()
};
PGmap.Polyline.prototype.editable = function () {
    function a(a) {
        var b = d.globals.mapObject().balloonGeom;
        !b.element.offsetHeight && !PGmap.EditBox.opened ? b.open(d, a) : b.hideMe()
    }
    var b = PGmap.Events.killEvent,
        c = PGmap.Events.addHandler,
        d = this;
    this.editPoint = new VirtualPointContainer(d);
    PGmap.EditBox || PGmap.Utils.addLib("js", "http://js.tmcrussia.com/modules/EditBox.js", function () {
        d.globals.mapObject().appendChild(PGmap.EditBox.element)
    });
    this.onPolylineMouseOver = function (a) {
        var f = d.globals.getPosition(),
            a = a || window.event,
            l = d.editPoint.point,
            m = PGmap.Utils.getOffset(d.globals.mapDOM),
            f = d.globals.xyToLonLat((a.pageX || a.clientX) - m.left - f.left, (a.pageY || a.clientY) - m.top - f.top);
        d.parent.add(l);
        c(l.element, "mouseout", function (a) {
            if (l.temporary) return l.hide(), b(a || window.event)
        });
        l.coord.lon = f.lon;
        l.coord.lat = f.lat;
        l.update();
        l.element.offsetHeight || l.show();
        return b(a || window.event)
    };
    this.element && c(this.element, "click", a)
};
PGmap.Polyline.prototype.setGeoArray = function (a) {
    this.points = [];
    for (var b = 0, c = a.length; b < c; b++) this.points[b] = new PGmap.Coord(a[b][0], a[b][1], !0)
};
PGmap.Polygon = function (a, b) {
    a = a || {};
    this.points = a.points || [];
    this.points[0] && (this.points[0].lon != this.points[this.points.length - 1].lon || this.points[0].lat != this.points[this.points.length - 1].lat) && this.points.push(this.points[0]);
    this.style = a.style || {
        color: "#013c6b",
        lineHeight: 5,
        backgroundColor: "none",
        lineOpacity: 1,
        backgroundOpacity: "none",
        fillRule: "nonzero"
    };
    PGmap.Utils.extendMethods(this.style, a.style || {});
    this.graphic = PGmap.Graphics.Path().attr({
        "stroke-width": this.style.lineHeight,
        stroke: this.style.color,
        fill: this.style.backgroundColor,
        "stroke-opacity": this.style.lineOpacity,
        "fill-opacity": this.style.backgroundOpacity,
        "fill-rule": this.style.fillRule
    });
    this.globals = null;
    this.real_coords = [];
    this.name = "\u041f\u043e\u043b\u0438\u0433\u043e\u043d";
    this.real_box = [];
    this.offset = 2;
    this.tempPolyline = null;
    this.element = this.graphic.node;
    this.balloon = this.editPoint = this.top = this.left = null;
    this._movupdate = function () {
        var a = [],
            b, e = this.offset,
            f = this.globals,
            l = this.graphic,
            m = this.real_coords,
            h = this.real_box,
            f = [f.getPosition(),
            f.getWindow()],
            f = [-f[0].left - e * f[1].width, -f[0].top - e * f[1].height, f[1].width - f[0].left + e * f[1].width, f[1].height - f[0].top + e * f[1].height];
        if (!(f[0] > h[2] || f[2] < h[0] || f[3] < h[1] || f[1] > h[3])) {
            e = 0;
            for (h = m.length - 1; e < h; e++) {
                var j = PGmap.Graphics.clipLine([m[e].x, m[e].y, m[e + 1].x, m[e + 1].y], f);
                if (j) {
                    b = b && b[b.length - 2] == j[0] && b[b.length - 1] == j[1] ? 2 : 0;
                    for (var g = j.length - 1; b < g; b += 2) a.push({
                        x: j[b],
                        y: j[b + 1]
                    })
                }
                b = j
            }
        }
        a.length > 1 ? l.attr("points", a).show() : l.hide()
    };
    a.draggable && this.draggable();
    a.editable && this.editable();
    b && b()
};
PGmap.Utils.extendMethods(PGmap.Polygon.prototype, PGmap.BasicGeom);
PGmap.Polygon.prototype.update = function () {
    var a = [],
        b, c, d;
    b = this.offset;
    var e = this.globals,
        f = Math.max,
        l = Math.min,
        m = this.graphic,
        h = this.points,
        j = [e.getPosition(), e.getWindow()],
        j = [-j[0].left - b * j[1].width, -j[0].top - b * j[1].height, j[1].width - j[0].left + b * j[1].width, j[1].height - j[0].top + b * j[1].height];
    b = this.real_coords = [];
    for (var g = 0, n = h.length - 1; g < n; g++) {
        h[g].init ? h[g].init(e) : h[g].update && h[g].update();
        h[g + 1].init ? h[g + 1].init(e) : h[g + 1].update && h[g + 1].update();
        b[g] = e.lonlatToXY(h[g].lon || h[g].coord.lon,
        h[g].lat || h[g].coord.lat);
        b[g + 1] = e.lonlatToXY(h[g + 1].lon || h[g + 1].coord.lon, h[g + 1].lat || h[g + 1].coord.lat);
        d = d ? [l(d[0], b[g].x, b[g + 1].x), l(d[1], b[g].y, b[g + 1].y), f(d[2], b[g].x, b[g + 1].x), f(d[3], b[g].y, b[g + 1].y)] : [b[g].x, b[g].y, b[g].x, b[g].y];
        var k = PGmap.Graphics.clipLine([b[g].x, b[g].y, b[g + 1].x, b[g + 1].y], j);
        if (k) {
            c = c && c[c.length - 2] == k[0] && c[c.length - 1] == k[1] ? 2 : 0;
            for (var o = k.length - 1; c < o; c += 2) a.push({
                x: k[c],
                y: k[c + 1]
            })
        }
        c = k
    }(this.real_box = d) && (d[2] - d[0] > j[2] - j[0] || d[3] - d[1] > j[3] - j[1]) ? this.movupdate = this._movupdate : (this.movupdate = null, a = b);
    a.length > 1 ? m.attr("points", a).show() : m.hide();
    this.left = this.graphic.rect.x;
    this.top = this.graphic.rect.y;
    return this
};
PGmap.Polygon.prototype.clean = function () {
    for (var a = this.points.length; a--;) this.points[a] && this.points[a].remove && (this.points[a].remove() || 1) && this.points[a].clean()
};
PGmap.Polygon.prototype.showPoint = function (a, b) {
    if (this.points[a]) {
        a || (a = this.points.length - 1);
        if (b && this.points[a] instanceof PGmap.Point) this.points[a].remove && this.points[a].remove(), this.points[a].clean(), this.points[a] = this.points[a].coord;
        this.points[a] instanceof PGmap.Point || (PGmap.Utils.extendMethods(tmp = {
            draggable: 1,
            url: "http://js.tmcrussia.com/img/poligon_point.png",
            width: 17,
            height: 18,
            backpos: "0 0"
        }, b || {}), PGmap.Utils.extendMethods(tmp, {
            coord: this.points[a]
        }), this.points[a] = new PGmap.Point(tmp));
        var c = this;
        this.points[a].init && (this.points[a].init = function (a) {
            if (a instanceof PGmap.Globals) this.globals = a, this.update(), this.init = null, this.remove && c.parent && c.parent.element.appendChild(this.element)
        });
        this.points[a].remove = function () {
            c.parent && c.parent.element === this.element.parentNode && c.parent.element.removeChild(this.element);
            this.remove = null
        };
        this.globals && (this.points[a].init ? this.points[a].init(this.globals) : this.parent.element.appendChild(this.points[a].element));
        this.points[a].draggable && this.points[a].draggable.callback("drag", function (a) {
            this[1].coord = this[1].globals.xyToLonLat(a.x, a.y);
            c.update()
        });
        a == this.points.length - 1 && (this.points[0] = this.points[a])
    }
    return this.points[a]
};
PGmap.Polygon.prototype.hidePoint = function (a) {
    this.points[a] instanceof PGmap.Point && this.points[a].remove && this.points[a].remove();
    return this.points[a]
};
PGmap.Polygon.prototype.hidePoints = function () {
    for (var a = 0, b = this.points.length; a < b - 1; a++) this.points[a].coord && this.parent.remove(this.points[a])
};
PGmap.Polygon.prototype.showPoints = function () {
    for (var a = 0, b = this.points.length; a < b - 1; a++) this.showPoint(a)
};
PGmap.Polygon.prototype.editable = function () {
    function a(a) {
        var c = b.globals.mapObject().balloonGeom;
        !c.element.offsetHeight && !PGmap.EditBox.opened ? c.open(b, a) : c.hideMe()
    }
    var b = this,
        c = PGmap.Events.addHandler;
    PGmap.EditBox || PGmap.Utils.addLib("js", "http://js.tmcrussia.com/modules/EditBox.js", function () {
        b.globals.mapObject().appendChild(PGmap.EditBox.element)
    });
    this.element && c(this.element, "click", a)
};
PGmap.Polygon.prototype.editableOff = function () {
    this.tempPolyline.editableOff();
    this.graphic.attr({
        fill: this.tempPolyline.graphic.attrs.fill,
        "fill-opacity": "0.6",
        stroke: this.tempPolyline.graphic.attrs.stroke
    });
    this.update().show();
    this.tempPolyline.points = this.points;
    this.tempPolyline.update();
    this.tempPolyline.hide()
};
PGmap.Polygon.prototype.editableOn = function () {
    var a = this;
    if (!this.tempPolyline) this.tempPolyline = new PGmap.Polyline({
        points: this.points,
        editable: 1
    }), this.parent.add(this.tempPolyline), this.tempPolyline.hide(), this.tempPolyline.name = this.name, this.tempPolyline.polygon = this, this.tempPolyline.graphic.attr({
        fill: this.graphic.attrs.fill,
        "fill-opacity": "0.6",
        stroke: this.graphic.attrs.stroke
    });
    this.tempPolyline.show();
    this.tempPolyline.editableOn();
    this.tempPolyline.points[0].draggable && this.points[0].draggable.callback("drag",

    function (b) {
        this[1].coord = this[1].globals.xyToLonLat(b.x, b.y);
        a.tempPolyline.points[a.tempPolyline.points.length - 1].coord = this[1].globals.xyToLonLat(b.x, b.y);
        a.tempPolyline.update()
    });
    a.hide()
};
PGmap.Polygon.prototype.draggable = function () {
    function a() {
        return {
            x: b.left,
            y: b.top
        }
    }
    var b = this,
        c = PGmap.Events.addHandler,
        d = PGmap.Events.killEvent,
        e, f;
    this.showPoints();
    this.draggable = new PGmap.Events.Draggable(b.element, {
        dragStart: function (a) {
            e = b.globals.xyToLonLat(a.x, a.y)
        },
        drag: function (c) {
            for (var d = 0, g = b.points.length; d < g - 1; d++) f = b.globals.xyToLonLat(c.x, c.y), b.points[d].coord.lon += f.lon - e.lon, b.points[d].coord.lat += f.lat - e.lat;
            f = a();
            e = b.globals.xyToLonLat(f.x, f.y);
            b.update()
        }
    }, a);
    for (var l = 0, m = b.points.length; l < m; l++)(function (a) {
        c(a.element, "dblclick", function (c) {
            for (var e = 0, f = b.points.length; e < f; e++) b.points[e] == a && (e ? b.points.splice(e, 1) : b.points[e] = b.points[f - 2]);
            b.parent && b.parent.element === a.element.parentNode && b.parent.element.removeChild(a.element);
            b.update();
            return d(c || window.event)
        })
    })(this.points[l])
};
PGmap.Polygon.prototype.dragOnce = function () {
    function a() {
        return {
            x: b.left,
            y: b.top
        }
    }
    var b = this,
        c, d;
    this.draggable = new PGmap.Events.Draggable(b.element, {
        dragStart: function (a) {
            c = b.globals.xyToLonLat(a.x, a.y)
        },
        drag: function (e) {
            for (var f = 0, l = b.points.length; f < l - 1; f++) d = b.globals.xyToLonLat(e.x, e.y), b.points[f].coord ? (b.points[f].coord.lon += d.lon - c.lon, b.points[f].coord.lat += d.lat - c.lat) : (b.points[f].lon += d.lon - c.lon, b.points[f].lat += d.lat - c.lat);
            d = a();
            c = b.globals.xyToLonLat(d.x, d.y);
            b.update();
            b.tempPolyline && b.tempPolyline.update().hide()
        },
        dragEnd: function () {
            b.draggable.callback("dragStart", function () {});
            b.draggable.callback("drag", function () {});
            b.draggable.callback("dragEnd", function () {})
        }
    }, a)
};
PGmap.Polygon.prototype.setGeoArray = function (a) {
    this.points = [];
    for (var b = 0, c = a.length; b < c; b++) this.points[b] = new PGmap.Coord(a[b][0], a[b][1], !0);
    this.points[0] && (this.points[0].lon != this.points[this.points.length - 1].lon || this.points[0].lat != this.points[this.points.length - 1].lat) && this.points.push(this.points[0])
};
PGmap.Widget = function (a) {
    if (!a || !a.coord || !(a.coord instanceof PGmap.Coord)) throw "Coord is not defined";
    if (!a.widgetpackage) throw "Widgets constructor is not defined";
    var b = PGmap.Base.createAbs();
    cont = PGmap.Base.widgetDiv();
    b.appendChild(cont);
    var c = document.createElement("img");
    c.style.visibility = "hidden";
    a.offsets = a.offsets || {};
    c.onload = function () {
        cont.style.marginLeft = (typeof a.offsets.left == "number" ? a.offsets.left : -c.width / 2) + "px";
        cont.style.marginTop = (typeof a.offsets.top == "number" ? a.offsets.top : -c.height / 2) + "px";
        c.style.visibility = "";
        c.onload = null
    };
    c.src = a.url || "http://www.veryicon.com/icon/32/Flag/Flags/Canada.png";
    cont.appendChild(c);
    this.element = b;
    this.top = this.left = this.globals = null;
    this.coord = a.coord;
    this.callbacks = a.callbacks || {};
    this.onready = [];
    var d = this.init,
        e = this.update,
        f = {};
    if (a.imports) for (var b = 0, l = a.imports.length; b < l; b++) PGmap[a.imports[b]] && (f[a.imports[b]] = PGmap[a.imports[b]]);
    this.init = function () {
        var a = d.apply(this, arguments);
        return a = this.main && this.main.init && this.init !== this.main.init ? this.main.init.apply(this.main, arguments) : a
    };
    this.update = function () {
        return this.main && this.main.update && this.update !== this.main.update ? this.main.update.apply(this.main, arguments) : e.apply(this, arguments)
    };
    var m = function (a) {
        if (PGmap.Utils.isFunc(a)) {
            this.loadWidget = null;
            a.prototype = this;
            var b = !this.init;
            this.main = new a(f);
            b && this.main.init();
            a = 0;
            for (b = this.onready.length; a < b; a++) this.onready[a]();
            this.onready = []
        }
    };
    typeof a.widgetpackage == "string" ? a.name && PGmap.Request(a.widgetpackage,

    function (b) {
        return function (c) {
            m.call(b, b.loadWidget.call(0, c, a.name))
        }
    }(this), "plainJs") : m.call(this, a.widgetpackage)
};
PGmap.Utils.extendMethods(PGmap.Widget.prototype, PGmap.BasicGeom);
PGmap.Widget.prototype.getWindowBox = function () {
    if (this.globals) return [this.globals.getPosition(), this.globals.getWindow()];
    throw "Globals object is undefined";
};
PGmap.Widget.prototype.getCoords = function () {
    if (this.globals) return {
        bbox: this.globals.getBbox(),
        coords: this.globals.getCoords()
    };
    throw "Globals object is undefined";
};
PGmap.Widget.prototype.convertLonLattoXY = function (a, b) {
    if (this.globals) return this.globals.lonlatToXY(a, b);
    throw "Globals object is undefined";
};
PGmap.Widget.prototype.convertXYtoLonLat = function (a, b) {
    if (this.globals) return this.globals.xyToLonLat(a, b);
    throw "Globals object is undefined";
};
PGmap.Widget.prototype.jsonParse = function (a) {
    return JSON.parse(a)
};
PGmap.Widget.prototype.fromMercX = function (a) {
    return PGmap.Utils.fromMercX(a)
};
PGmap.Widget.prototype.fromMercY = function (a) {
    return PGmap.Utils.fromMercY(a)
};
PGmap.Widget.prototype.mercX = function (a) {
    return PGmap.Utils.mercX(a)
};
PGmap.Widget.prototype.mercY = function (a) {
    return PGmap.Utils.mercY(a)
};
PGmap.Widget.prototype.ajax = function (a) {
    if (this.globals) return PGmap.Request.FlashRequest.load(a);
    throw "Globals object is undefined";
};
PGmap.Widget.prototype.loadContent = function () {
    if (this.globals) return PGmap.Request.apply(PGmap, arguments);
    throw "Globals object is undefined";
};
PGmap.Widget.prototype.callback = function (a, b) {
    return this.callbacks[a] && this.callbacks[a].apply(this, b || [])
};
PGmap.Widget.prototype.loadWidget = function (a, b) {
    return (new Function(a + "\nreturn " + b))()
};
PGmap.Widget.prototype.onReady = function (a, b) {
    var c = this,
        d = typeof a == "string" ? function () {
            c.main[a].apply(c.main, b || [])
        } : function () {
            a.apply(c.main, b || [])
        };
    this.main ? d() : this.onready.push(d)
};
PGmap.Widget.prototype.setDrawleble = PGmap.Graphics.setDrawleble;
PGmap.Widget.prototype.clipLine = PGmap.Graphics.clipLine;
PGmap.Route = function (a) {
    var a = a || {}, b = a.PPlanConst;
    this.points = a.points || [];
    this.plancoords = a.plan || [];
    this.plan = [];
    this.style = a.style && {
        color: a.style.color || "#958",
        lineHeight: a.style.lineHeight || 4
    } || {
        color: "#958",
        lineHeight: 4
    };
    this.graphic = PGmap.Graphics.Path().attr({
        "stroke-width": this.style.lineHeight,
        stroke: this.style.color
    });
    this.globals = null;
    this.real_coords = [];
    this.real_box = [];
    this.offset = 2;
    this.element = this.graphic.node;
    this.top = this.left = null;
    a.outF && a.moveF && (PGmap.Events.addHandler(this.element,
        "mouseout", a.outF), PGmap.Events.addHandler(this.element, "mousemove", a.moveF));
    this._movupdate = function () {
        var a = [],
            b, e = this.offset,
            f = this.globals,
            l = this.graphic,
            m = this.real_coords,
            h = this.real_box,
            f = [f.getPosition(), f.getWindow()],
            f = [-f[0].left - e * f[1].width, -f[0].top - e * f[1].height, f[1].width - f[0].left + e * f[1].width, f[1].height - f[0].top + e * f[1].height];
        if (!(f[0] > h[2] || f[2] < h[0] || f[3] < h[1] || f[1] > h[3])) {
            e = 0;
            for (h = m.length - 1; e < h; e++) {
                var j = PGmap.Graphics.clipLine([m[e].x, m[e].y, m[e + 1].x, m[e + 1].y], f);
                if (j) {
                    b = b && b[b.length - 2] == j[0] && b[b.length - 1] == j[1] ? 2 : 0;
                    for (var g = j.length - 1; b < g; b += 2) a.push({
                        x: j[b],
                        y: j[b + 1]
                    })
                }
                b = j
            }
        }
        a.length > 1 ? l.attr("points", a).show() : l.hide()
    };
    this.setPlan = function () {
        if (b) {
            var a;
            a = this.plancoords;
            for (var d = a.length, e = 0; e < d; e++) this.plan[e] || (this.plan[e] = new b(e)), this.plan[e].setCoords(a[e], e), this.plan[e].hide();
            a = this.plan.length;
            if (a > d) {
                a = this.plan.splice(d, a - d);
                for (d = a.length; d--;) a[d].remove()
            }
        }
    };
    this.setPlan()
};
PGmap.Utils.extendMethods(PGmap.Route.prototype, PGmap.BasicGeom);
PGmap.Route.prototype.update = function () {
    for (var a = [], b, c = this.offset, d = this.globals, e = Math.max, f = Math.min, l = this.graphic, m = this.points, h = [d.getPosition(), d.getWindow()], h = [-h[0].left - c * h[1].width, -h[0].top - c * h[1].height, h[1].width - h[0].left + c * h[1].width, h[1].height - h[0].top + c * h[1].height], c = this.real_coords = [], j = 0, g = m.length - 1; j < g; j++) {
        c[j] = d.lonlatToXY(m[j].lon, m[j].lat);
        c[j + 1] = d.lonlatToXY(m[j + 1].lon, m[j + 1].lat);
        var n = n ? [f(n[0], c[j].x, c[j + 1].x), f(n[1], c[j].y, c[j + 1].y), e(n[2], c[j].x, c[j + 1].x), e(n[3],
        c[j].y, c[j + 1].y)] : [f(c[j].x, c[j + 1].x), f(c[j].y, c[j + 1].y), e(c[j].x, c[j + 1].x), e(c[j].y, c[j + 1].y)],
            k = PGmap.Graphics.clipLine([c[j].x, c[j].y, c[j + 1].x, c[j + 1].y], h);
        if (k) {
            b = b && b[b.length - 2] == k[0] && b[b.length - 1] == k[1] ? 2 : 0;
            for (var o = k.length - 1; b < o; b += 2) a.push({
                x: k[b],
                y: k[b + 1]
            })
        }
        b = k
    }
    this.real_box = n;
    n[2] - n[0] > h[2] - h[0] || n[3] - n[1] > h[3] - h[1] ? this.movupdate = this._movupdate : (this.movupdate = null, a = c);
    a.length > 1 ? l.attr("points", a).show() : l.hide();
    this.left = this.graphic.rect.x;
    this.top = this.graphic.rect.y;
    this.updatePlan(d)
};
PGmap.Route.prototype.clean = function () {
    for (var a = this.plan.length; a--;) this.plan[a].remove();
    this.plan = [];
    this.base = null
};
PGmap.Route.prototype.updatePlan = function (a) {
    for (var b = this.plan.length; b--;) this.plan[b].update(a);
    if (this.base) for (b = this.base.length; b--;) this.base[b].update(a)
};
PGmap.Route.prototype.hide = function () {
    this.element.style.display = "none";
    for (var a = this.plan.length; a--;) this.plan[a].hide();
    if (this.base) for (a = this.base.length; a--;) this.base[a].hide()
};
PGmap.Route.prototype.show = function () {
    this.element.style.display = "block";
    if (this.base) for (var a = this.base.length; a--;) this.base[a].show()
};
PGmap.Route.prototype.setGeoArray = function (a) {
    this.points = [];
    for (var b = 0, c = a.length; b < c; b++) this.points[b] = new PGmap.Coord(a[b][0], a[b][1], !0)
};
PGmap.Route.prototype.setGeoPlan = function (a) {
    this.plancoords = [];
    for (var b = 0, c = a.length; b < c; b++) this.plancoords[b] = new PGmap.Coord(a[b].to[0], a[b].to[1], !0);
    this.setPlan()
};
PGmap.RulerLine = function (a) {
    a = a || {};
    this.points = a.points || [];
    this.plan = [];
    this.style = a.style && {
        color: a.style.color || "#f00",
        lineHeight: a.style.lineHeight || 3
    } || {
        color: "#f00",
        lineHeight: 3
    };
    this.graphic = PGmap.Graphics.Path().attr({
        "stroke-width": this.style.lineHeight,
        stroke: this.style.color
    });
    this.globals = null;
    this.real_coords = [];
    this.real_box = [];
    this.offset = 2;
    this.element = this.graphic.node;
    this.top = this.left = null;
    this._movupdate = function () {
        var a = [],
            c, d;
        c = this.offset;
        var e = this.globals,
            f = this.graphic,
            l = this.real_coords,
            m = this.real_box,
            e = [e.getPosition(), e.getWindow()],
            e = [-e[0].left - c * e[1].width, -e[0].top - c * e[1].height, e[1].width - e[0].left + c * e[1].width, e[1].height - e[0].top + c * e[1].height];
        if (!(e[0] > m[2] || e[2] < m[0] || e[3] < m[1] || e[1] > m[3])) for (var m = 0, h = l.length - 1; m < h; m++) {
            if (c = PGmap.Graphics.clipLine([l[m].x, l[m].y, l[m + 1].x, l[m + 1].y], e)) {
                d = d && d[d.length - 2] == c[0] && d[d.length - 1] == c[1] ? 2 : 0;
                for (var j = c.length - 1; d < j; d += 2) a.push({
                    x: c[d],
                    y: c[d + 1]
                })
            }
            d = c
        }
        a.length > 1 ? f.attr("points", a).show() : f.hide()
    }
};
PGmap.Utils.extendMethods(PGmap.RulerLine.prototype, PGmap.BasicGeom);
PGmap.RulerLine.prototype.update = function () {
    var a = [],
        b, c, d, e;
    b = this.offset;
    var f = this.globals,
        l = Math.max,
        m = Math.min,
        h = this.graphic,
        j = this.points,
        g = [f.getPosition(), f.getWindow()],
        g = [-g[0].left - b * g[1].width, -g[0].top - b * g[1].height, g[1].width - g[0].left + b * g[1].width, g[1].height - g[0].top + b * g[1].height];
    b = this.real_coords = [];
    j[0] && j[0].update && j[0].update();
    for (var n = 0, k = j.length - 1; n < k; n++) {
        j[n].update && j[n].update();
        j[n + 1].update && j[n + 1].update();
        b[n] = f.lonlatToXY(j[n].lon, j[n].lat);
        b[n + 1] = f.lonlatToXY(j[n + 1].lon, j[n + 1].lat);
        e = e ? [m(e[0], b[n].x, b[n + 1].x), m(e[1], b[n].y, b[n + 1].y), l(e[2], b[n].x, b[n + 1].x), l(e[3], b[n].y, b[n + 1].y)] : [m(b[n].x, b[n + 1].x), m(b[n].y, b[n + 1].y), l(b[n].x, b[n + 1].x), l(b[n].y, b[n + 1].y)];
        if (c = PGmap.Graphics.clipLine([b[n].x, b[n].y, b[n + 1].x, b[n + 1].y], g)) {
            d = d && d[d.length - 2] == c[0] && d[d.length - 1] == c[1] ? 2 : 0;
            for (var o = c.length - 1; d < o; d += 2) a.push({
                x: c[d],
                y: c[d + 1]
            })
        }
        d = c
    }(this.real_box = e) && (e[2] - e[0] > g[2] - g[0] || e[3] - e[1] > g[3] - g[1]) ? this.movupdate = this._movupdate : (this.movupdate = null, a = b);
    a.length > 1 ? h.attr("points", a).show() : h.hide();
    this.left = this.graphic.rect.x;
    this.top = this.graphic.rect.y
};
PGmap.RulerLine.prototype.clean = function () {
    for (var a = this.points.length; a--;) this.points[a].remove()
};
PGmap.RulerLine.prototype.hide = function () {
    this.element.style.display = "none"
};
PGmap.RulerLine.prototype.show = function () {
    this.element.style.display = "block"
};
PGmap.Geometry = function (a) {
    function b(a) {
        for (var b in l) {
            var c = l[b];
            if (c instanceof Array) for (var d = c.length, e = 0; e < d;) {
                if (c[e] && c[e][a] && PGmap.Utils.isFunc(c[e][a])) c[e][a]();
                e++
            } else if (c && c[a] && PGmap.Utils.isFunc(c[a])) c[a]()
        }
    }
    function c(a, c) {
        if (c == "show" || c == "hide") if (a == "all") b("hide");
        else if (a && a.type && l[type] && a.id != void 0 && l[type][id] && l[type][id][c]) l[type][id][c]();
        else if (a && a.type && l[type]) for (var d = 0, e = l[type].length; d < e;) {
            if (l[type][d] && l[type][d][c]) l[type][d][c]();
            d++
        }
    }
    function d(a) {
        if (a instanceof
        PGmap.Point) return l.points;
        else if (a instanceof PGmap.Rectangle) return l.rectangles;
        else if (a instanceof PGmap.Polyline) return l.polylines;
        else if (a instanceof PGmap.Polygon) return l.polygons;
        else if (a instanceof PGmap.Widget) return l.widgets;
        else if (a instanceof PGmap.Route) return l.route;
        else if (a instanceof PGmap.RulerLine) return l.ruler;
        else if (a instanceof PGmap.Balloon) return l.balloon;
        else throw "getTypedArr: Geometry object is not supported";
    }
    function e() {
        j.remove(this)
    }
    var f = PGmap.Base.create("PGmap-layer-container-geometry"),
        l = {
            points: [],
            rectangles: [],
            polylines: [],
            polygons: [],
            multipolygons: [],
            freehands: [],
            widgets: [],
            route: [],
            ruler: [],
            balloon: []
        }, m = {
            width: 0,
            height: 0
        }, h = {
            left: 0,
            top: 0
        }, j = this;
    this.element = f;
    PGmap.Graphics.setDrawleble(f, 0);
    a.mapObject().event.user(PGmap.Events.MAP_ZOOMED, function () {
        h = a.getPosition();
        m = a.getWindow();
        b("update")
    });
    a.mapObject().event.user(PGmap.Events.MAP_MOVE, function () {
        var c = a.getPosition();
        if (Math.abs(h.left - c.left) > 2 * m.width || Math.abs(h.top - c.top) > 2 * m.height) h = c, b("movupdate")
    });
    this.create = function (a) {
        if (!a.type) return !1;
        switch (a.type) {
            case "point":
                return new PGmap.Point(a);
            case "rectangle":
                return new PGmap.Rectangle(a);
            case "polyline":
                return new PGmap.Polyline(a);
            case "polygon":
                return new PGmap.Polygon(a);
            case "widget":
                return new PGmap.Widget(a);
            case "route":
                return new PGmap.Route(a);
            case "ruler":
                return new PGmap.RulerLine(a);
            case "balloon":
                return new PGmap.Balloon(a)
        }
    };
    this.add = function (b) {
        m = a.getWindow();
        if (PGmap.Utils.isFunc(b.init)) {
            var c = d(b);
            if (c) c.push(b), b.parent = this, b.init(a),
            b.remove = e, c !== l.points && c !== l.widgets && c != l.balloon ? f.graphics.append(b.graphic) : f.appendChild(b.element)
        }
    };
    this.remove = function (a) {
        var b = d(a);
        if (b) {
            var c = PGmap.Utils.getIndexOf(b, a);
            if (c != -1 && PGmap.Utils.isFunc(a.clean)) a.clean(), delete b[c], b.splice(c, 1), b !== l.points && b !== l.widgets ? f.graphics.remove(a.graphic) : f.removeChild(a.element), a.parent === this && (a.parent = null)
        }
    };
    this.get = function (a) {
        var b;
        a == "all" ? b = l : a && a.type && l[a.type] && a.id != void 0 && l[a.type][a.id] ? b = l[a.type][a.id] : a && a.type && l[a.type] && (b = l[a.type]);
        return b
    };
    this.setStyle = function () {};
    this.update = function (a) {
        if (a == "all") for (var b in l) for (var c in l[b]) l[b][c].update();
        else if (a && a.type && l[a.type] && a.id != void 0 && l[a.type][a.id]) l[a.type][a.id].update();
        else if (a && a.type && l[a.type]) for (var d in l[a.type]) l[a.type][d].update()
    };
    this.hide = function (a) {
        a ? c(a, "hide") : PGmap.Utils.hide(f)
    };
    this.show = function (a) {
        PGmap.Utils.show(f);
        a && c(a, "show")
    }
};
PGmap.Cluster = function (a) {
    if (!a.coord || !(a.coord instanceof PGmap.Coord)) throw "Coord is not defined";
    this.coord = a.coord;
    this.count = a.count;
    this.type = a.type || "svg";
    this.points = a.points;
    var b = document.createElement("span"),
        c, d = this,
        e = a.layer.globals,
        f = e.mapObject().geometry,
        l = a.layer.element,
        m = PGmap.Base.create(null, "position:absolute;left:0px;top:0px"),
        h = function () {
            c = e.lonlatToXY(d.coord.lon, d.coord.lat);
            m.style.left = c.x + "px";
            m.style.top = c.y + "px"
        };
    this.content = b;
    var j = e.lonlatToXY(d.coord.lon, d.coord.lat);
    m.style.cssText = "position:absolute;left:" + j.x + "px;top:" + j.y + "px";
    switch (this.type) {
        case "svg":
            j = PGmap.Graphics.Circle(0, 0, 15).attr(a.style).appendTo(m).paper;
            this.element = this.container = j.element;
            b.style.cssText = "position:absolute; left:" + (this.count < 10 ? -4 : this.count < 100 ? -8 : -12) + "px;top:-8px";
            break;
        case "bitmap":
            PGmap.Utils.addClass(m, "PGmap-cluster-container");
            PGmap.Utils.addClass(b, "PGmap-cluster-bitmap");
            m.style.backgroundPosition = a.bgPos || "center center";
            m.style.backgroundImage = a.bgImage || "";
            this.element = this.container = m;
            break;
        default:
            throw "Unknown type of cluster";
    }
    l.appendChild(m);
    b.innerHTML = a.count;
    m.appendChild(b);
    this.update = function () {
        h.call(this);
        f.element.appendChild(m);
        this.update = h
    };
    this.clear = function () {
        f.element.removeChild(m)
    }
};
PGmap.GeometryLayer = function (a) {
    function b() {
        d.setClusters()
    }
    function c(a, b) {
        a.sort(function (a, b) {
            return a - b
        });
        b.sort(function (a, b) {
            return a - b
        });
        return {
            lon1: a[0],
            lat1: b[0],
            lon2: a[a.length - 1],
            lat2: b[b.length - 1]
        }
    }
    var d = this,
        e = !1;
    this.points = a.points || [];
    this.clusterSize = a.clusterSize || 120;
    this.clusters = [];
    this.clusterType = a.clusterType || "bitmap";
    this.globals = a.points[0].globals;
    this.bgImage = "url(" + (a.clusterImage || "http://js.tmcrussia.com/img/cluster_sprite.png") + ")";
    this.clusterStyle = a.clusterStyle || {
        "stroke-width": 2,
        fill: "#fff",
        "fill-opacity": "0.6"
    };
    this.element = PGmap.Base.layerDiv();
    this.setClusters = function () {
        var a, l, m = {}, h = 0,
            j = this.clusterSize * this.globals.resolutionByZoom(this.globals.getZoom());
        this.removeClusters();
        if (this.globals.getZoom() == this.globals.maxZoom()) {
            for (var g = this.points.length; g--;) this.points[g].show();
            this.globals.mapObject().event.user(PGmap.Events.ZOOMING_END, b)
        } else {
            for (g = this.points.length; g--;) this.points[g].show(), a = parseInt(this.points[g].coord.lon / j) * j, l = parseInt(this.points[g].coord.lat / j) * j, a = a + "," + l, m[a] || (m[a] = []), m[a].push(this.points[g]);
            for (var n in m) {
                a = m[n].length;
                var k = l = 0,
                    j = [],
                    g = [];
                if (a != 1) {
                    for (var o = a; o--;) {
                        var p = m[n][o];
                        l += p.coord.lon;
                        k += p.coord.lat;
                        p.hide();
                        j.push(p.coord.lon);
                        g.push(p.coord.lat)
                    }
                    this.setClusterColorByCount(a);
                    a = this.clusterType == "bitmap" ? new PGmap.Cluster({
                        coord: new PGmap.Coord(l / a, k / a),
                        url: "",
                        layer: d,
                        count: a,
                        points: m[n],
                        bgImage: d.bgImage,
                        type: "bitmap",
                        bgPos: d.setClusterImageByCount(a)
                    }) : new PGmap.Cluster({
                        coord: new PGmap.Coord(l / a, k / a),
                        url: "",
                        layer: d,
                        count: a,
                        style: d.clusterStyle
                    });
                    a.index = h;
                    a.bbox = c(j, g);
                    this.clusters.push(a);
                    a.update();
                    h++
                }
            }
            e || this.mapCallbacks();
            this.setClusters.callback && this.setClusters.callback()
        }
    };
    this.mapCallbacks = function () {
        e = !0;
        this.globals.mapObject().event.user(PGmap.Events.MAP_MOVED, b);
        this.globals.mapObject().event.user(PGmap.Events.ZOOMING_END, b)
    };
    this.setClusterColorByCount = function (a) {
        this.clusterStyle.stroke = a <= 10 ? "#00cc33" : a < 100 ? "#ff0000" : "#660066"
    };
    this.setClusterImageByCount = function (a) {
        return a <= 10 ? "-120px 0" : a < 100 ? "-56px 0" : "5px 0"
    };
    this.removeClusters = function () {
        for (var a = this.clusters.length; a--;) this.clusters[a].clear(), delete this.clusters[a];
        this.clusters = [];
        this.globals.mapObject().event.remove(PGmap.Events.MAP_MOVED, b);
        this.globals.mapObject().event.remove(PGmap.Events.ZOOMING_END, b);
        e = !1
    }
};
PGmap.MainLayer = function (a, b) {
    b = b || {};
    this.globals = a;
    this.element = PGmap.Base.layerDiv();
    this.fragment = PGmap.Base.fragment();
    this.url = b.url || this.globals.tileUrl;
    this.tilePool = [];
    this.tileArr = {};
    this.keyTile = null;
    this.scTileSize = this.tileSize = this.globals.tileSize();
    this.mapBbox = this.globals.mapBbox();
    this.firstStart = !0;
    this.pxBbox = this.buffer = this.scale = this.zoom = this.countTiles = this.height = this.width = this.resolution = null;
    this.posElement = this.globals.mapElement();
    this.position = null;
    this.key = this.globals.key();
    this.i = null;
    this.params = b
};
PGmap.MainLayer.methods = {
    initVars: function () {
        if (this.firstStart) {
            this.firstStart = !1;
            var a = this.globals.getWindow();
            this.buffer = this.tileSize * 1;
            this.width = a.width;
            this.height = a.height;
            this.zoom = this.globals.getZoom();
            if (this.params.zoom) this.zoom = this.params.zoom;
            this.resolution = this.globals.resolutionByZoom(this.zoom);
            this.scale = this.resolution * this.tileSize;
            this.pxBbox = {
                left: -this.buffer,
                top: -this.buffer,
                right: this.width + this.buffer,
                bottom: this.height + this.buffer
            };
            this.considerTilesCoord()
        }
        this.position = this.globals.getPosition()
    },
    updateWindow: function () {
        var a = this.globals.getWindow();
        this.width = a.width;
        this.height = a.height;
        this.pxBbox.right = this.width + this.buffer;
        this.pxBbox.bottom = this.height + this.buffer
    },
    firstDraw: function () {
        this.cleanMap();
        this.initVars();
        this.tilePassage();
        if (!this.params.depend) this.onLoadHandler(PGmap.Events.LAYER_FIRST_DRAW);
        return this
    },
    draw: function () {
        this.initVars();
        this.tilePassage();
        if (!this.params.depends) this.onLoadHandler(PGmap.Events.LAYER_DRAW);
        return this
    },
    considerTilesCoord: function () {
        this.countTiles = this.globals.countByZoom(this.zoom)
    },
    normalizeTile: function (a) {
        if (a.x == this.countTiles) a.x = 0;
        else if (a.x > this.countTiles) a.x -= this.countTiles;
        else if (a.x < 0) a.x = Math.abs(a.x + this.countTiles)
    },
    addTile: function (a) {
        a.layer = this;
        if (a instanceof PGmap.MainLayer.Tile && a.y >= 0 && a.y < this.countTiles) a.url = this.globals.roundRobin("tiles", a.x, a.y), this.tileArr[a.toHash()] = a, a.init(), this.fragment.appendChild(a.element)
    },
    removeTile: function (a) {
        var b;
        if (a instanceof PGmap.MainLayer.Tile && (b = a.toHash(), this.tileArr[b] == a)) delete this.tileArr[b], a = a.element, a.parentNode && a.parentNode.removeChild(a)
    },
    contains: function (a) {
        return a instanceof PGmap.MainLayer.Tile && this.tileArr && this.tileArr[a.toHash()]
    },
    getTileByHash: function (a) {
        return this.tileArr[a]
    },
    getSameTile: function (a) {
        return a instanceof PGmap.MainLayer.Tile ? this.getTileByHash(a.toHash()) : null
    },
    calculateCornerTile: function () {
        this.keyTile = this.getTileByPoint({
            pageX: this.pxBbox.left,
            pageY: this.pxBbox.top
        });
        this.normalizeTile(this.keyTile)
    },
    tilePassage: function () {
        this.calculateCornerTile();
        var a = this.keyTile.left,
            b = this.keyTile.x,
            c;
        for (this.cleanNotUsedTiles(); this.pxBbox.bottom > this.keyTile.top;) {
            for (; this.pxBbox.right > this.keyTile.left;) c = this.keyTile.clone(), c.left -= this.position.left, c.top -= this.position.top, (!this.contains(c) || this.zoom < 3) && this.addTile(c), this.keyTile.x += 1, this.keyTile.left += this.scTileSize, this.normalizeTile(this.keyTile);
            this.keyTile.x = b;
            this.keyTile.left = a;
            this.keyTile.y -= 1;
            this.keyTile.top += this.scTileSize
        }
        this.element.appendChild(this.fragment)
    },
    calculateCenterTile: function () {
        this.keyTile = this.getTileByPoint({
            pageX: this.width / 2,
            pageY: this.height / 2
        });
        this.normalizeTile(this.keyTile);
        this.addTile(this.keyTile);
        this.tilePool.push(this.keyTile)
    },
    getTileByPoint: function (a, b) {
        var b = b || this.globals.getCoords(),
            c = this.width / 2 * this.resolution,
            d = this.height / 2 * this.resolution,
            e = Math.floor((b.lon - c + a.pageX * this.resolution - this.mapBbox.minx) / this.scale),
            f = Math.floor((b.lat + d - a.pageY * this.resolution - this.mapBbox.miny) / this.scale),
            l = this.mapBbox.miny + f * this.scale + this.scale,
            c = PGmap.Utils.myRound((this.mapBbox.minx + e * this.scale + c - b.lon) / this.resolution, 0.2),
            d = PGmap.Utils.myRound((b.lat + d - l) / this.resolution, 0.2);
        return new PGmap.MainLayer.Tile(e, f, this.zoom, c, d, this.url, this.key, this.scTileSize, this.globals)
    },
    carouselRender: function () {
        this.afterPool = [];
        this.calculateCenterTile();
        this.addTiles(this.setDirectionTile);
        this.element.appendChild(this.fragment)
    },
    addTiles: function () {
        var a = [{
            y: 1,
            top: -this.scTileSize
        }, {
            x: 1,
            left: this.scTileSize
        }, {
            y: -1,
            top: this.scTileSize
        }, {
            x: -1,
            left: -this.scTileSize
        }],
            b = this.tilePool.shift();
        if (b && b instanceof PGmap.MainLayer.Tile) {
            for (var c = 0, d = a.length; c < d; c++) this.setDirectionTile(a[c], b);
            this.tilePool.length && this.addTiles()
        }
    },
    setDirectionTile: function (a, b) {
        var c = b.clone(),
            d;
        for (d in a) c[d] += a[d];
        c.left + this.tileSize >= this.pxBbox.left && c.top + this.tileSize >= this.pxBbox.top && c.left <= this.pxBbox.right && c.top <= this.pxBbox.bottom && (this.normalizeTile(c), c.left -= this.position.left, c.top -= this.position.top, this.contains(c) || (this.tilePool.push(c), this.addTile(c)))
    },
    onLoadHandler: function (a) {
        function b() {
            e.i && clearInterval(e.i)
        }
        var c = this.tileArr,
            d = this.globals,
            e = this;
        b();
        e.i = setInterval(function () {
            var e = 0,
                l = 0,
                m = 10,
                h;
            for (h in c) c[h].isLoad && l++, e++;
            if (e && l && l / e >= 1) {
                for (h in PGmap.Events) if (PGmap.Events[h] == a) break;
                d.mapObject().event.user(PGmap.Events[h]);
                b()
            }--m <= 0 && b()
        }, 1800)
    },
    zoomEffect: function (a, b, c, d) {
        var e = this.tileSize / 2,
            f = this.tileSize * 2,
            l = this.globals,
            m = b.zoom;
        if (m >= l.minZoom() && m <= l.maxZoom() && b instanceof PGmap.Coord) {
            var b = a.zoom < m,
                h = this.scTileSize * (b ? 2 : 0.5);
            if (h >= e && h <= f) a = this.getTileByPoint(c,
            a), this.scTileSize = h, this.element.style.zIndex--, this.arrangeTiles(c, d, a, b * 2 - 1)
        }
        if (l.mainObject.controls.slider) c = l.mainObject.controls.slider.plus, d = l.mainObject.controls.slider.minus, m <= l.minZoom() ? d.setActive(3) : m > l.minZoom() && d.setActive(0), m >= l.maxZoom() ? c.setActive(3) : m < l.maxZoom() && c.setActive(0)
    },
    arrangeTiles: function (a, b, c, d) {
        var e, f = [],
            l = this.globals,
            m, h = PGmap.Utils.getPosIntoView(l.mainElement());
        m = c.left + d * h.left / 2 + (d - 1) * h.left / 4;
        d = c.top + d * h.top / 2 + (d - 1) * h.top / 4;
        if (c) {
            m = a.pageX - m;
            d = a.pageY - d;
            c.left = a.pageX - this.scTileSize * m / this.tileSize;
            c.top = a.pageY - this.scTileSize * d / this.tileSize;
            var j = c.tileSize,
                g = this.scTileSize,
                n = (this.scTileSize - c.tileSize) / 4,
                k;
            for (k in this.tileArr) e = this.tileArr[k], e.left += b.left, e.top += b.top, e.update(), m = e.y - c.y, a = c.left + (e.x - c.x) * this.scTileSize, m = c.top - m * this.scTileSize, f.push({
                tile: e,
                incTile: n,
                incLeft: (a - e.left) / 4,
                incTop: (m - e.top) / 4,
                tileSize: e.tileSize,
                left: a,
                top: m
            });
            setTimeout(function () {
                for (var a = 0, b = f.length; a < b; a++) e = f[a].tile, e.tileSize == g ? (e.left = f[a].left, e.top = f[a].top) : (e.tileSize += f[a].incTile, e.left += f[a].incLeft, e.top += f[a].incTop), e.update();
                j == g ? l.mapObject().event.user(PGmap.Events.ZOOMING_END, []) : setTimeout(arguments.callee, 50);
                j += n
            }, 50)
        }
    },
    cleanNotUsedTiles: function () {
        var a, b, c, d;
        for (c in this.tileArr) d = this.tileArr[c], a = d.left + this.position.left, b = d.top + this.position.top, (a + this.scTileSize < this.pxBbox.left || b + this.scTileSize < this.pxBbox.top || a > this.pxBbox.right || b > this.pxBbox.bottom) && this.removeTile(d)
    },
    hide: function () {
        this.element.style.display =
            "none";
        return this
    },
    show: function () {
        this.element.style.display = "block";
        return this
    },
    cleanMap: function () {
        for (; this.element.firstChild;) this.element.removeChild(this.element.firstChild);
        this.tileArr = {};
        this.tilePool = [];
        this.firstStart = !0;
        return this
    },
    clean: function () {
        this.cleanMap()
    }
};
PGmap.MainLayer.prototype = PGmap.MainLayer.methods;
PGmap.MainLayer.Tile = function (a, b, c, d, e, f, l, m, h, j) {
    if (typeof a == "object") j = typeof b == "function" ? b : a.formatF, b = a.y, c = a.zoom, d = a.left, e = a.top, f = a.url, l = a.key, m = a.tileSize, h = a.gl, a = a.x;
    this.x = a;
    this.y = b;
    this.zoom = c;
    if (!h.baseLeft) h.baseLeft = d;
    if (!h.baseTop) h.baseTop = e;
    this.left = d;
    this.top = e;
    this.url = f;
    this.key = l;
    this.tileSize = m;
    this.gl = h;
    this.element = null;
    this.isLoad = !1;
    this.formatF = j;
    typeof j == "function" && (this.formatUrl = j)
};
PGmap.MainLayer.Tile.prototype = {
    getHashString: function () {
        return this.zoom + "00" + (1E9 + this.x).toString().substr(1) + (1E9 + this.y).toString().substr(1)
    },
    formatUrl: function (a) {
        return "lv" + a.getHashString().replace(/^(\d{1,2})00(\d{3})(\d{3})(\d{3})(\d{3})(\d{3})(\d{3})/, "$1/00/$2/$3/$4/$5/$6/$7") + ".png"
    },
    init: function (a) {
        var b = PGmap.Base.tileImg(),
            c = this;
        b.src = c.url + c.formatUrl(c) + "?" + c.key;
        b.style.left = Math.round(c.left) + "px";
        b.style.top = Math.round(c.top) + "px";
        if (c.tileSize) b.style.width = c.tileSize + "px",
        b.style.height = c.tileSize + "px";
        c.element = b;
        a ? c.isLoad = !0 : (b.style.visibility = "hidden", setTimeout(function () {
            b.complete ? (b.style.visibility = "visible", c.isLoad = !0) : setTimeout(arguments.callee, 200)
        }, 200));
        return b
    },
    update: function () {
        this.element.style.cssText += "; left: " + this.left + "px; top: " + this.top + "px; width: " + this.tileSize + "px; height: " + this.tileSize + "px;";
        return this
    },
    clone: function (a) {
        return new PGmap.MainLayer.Tile(this, a)
    }
};
PGmap.MainLayer.Tile.prototype.toString = PGmap.MainLayer.Tile.prototype.getHashString;
PGmap.MainLayer.Tile.prototype.toHash = PGmap.MainLayer.Tile.prototype.getHashString;
PGmap.Layer = function (a, b) {
    if (!(a instanceof PGmap.Globals)) throw "invalid first argument";
    this.globals = a;
    this.tileArr = {};
    this.element = PGmap.Base.layerDiv();
    b = b || {};
    if (!b.url) throw "url layer is undefined";
    var c = this.url = b.url;
    this.position = null;
    this.df = document.createDocumentFragment();
    this.formatTileUrl = b.formatTileUrl;
    this.getUrl = typeof this.url == "string" ? function () {
        return c
    } : function (a, e) {
        if (b.cache) {
            var f = Crypto.MD5("layer" + a + e),
                f = parseInt(f, 16) % c.length;
            return c[f]
        } else return c[Math.floor(a % 10 * c.length / 10)]
    }
};
PGmap.Layer.prototype.draw = function (a) {
    this.Mlayer = a;
    if (this.element.style.display != "none") {
        var a = a.tileArr,
            b = {}, c;
        for (c in this.tileArr) b[c] = this.tileArr[c];
        this.tileArr = {};
        for (var d in a) b[d] ? (this.tileArr[d] = b[d], delete b[d]) : (c = a[d].clone(this.formatTileUrl), c.url = this.getUrl(c.x, c.y), this.addTile(c));
        for (var e in b) b[e].element && this.element.removeChild(b[e].element);
        this.element.appendChild(this.df)
    }
    return this
};
PGmap.Layer.prototype.update = function () {
    var a;
    if (this.element.parentNode) {
        a = this.tileArr;
        this.element.innerHTML = "";
        this.tileArr = {};
        this.fastTileLoad = !0;
        for (var b in a) this.addTile(a[b]);
        this.element.appendChild(this.df);
        this.fastTileLoad = !1
    }
    return this
};
PGmap.Layer.prototype.addTile = function (a) {
    if (a instanceof PGmap.MainLayer.Tile && !this.tileArr[a.toHash()]) this.tileArr[a.toHash()] = a, a.layer = this, a.init(this.fastTileLoad), this.df.appendChild(a.element)
};
PGmap.Layer.prototype.removeTile = PGmap.MainLayer.methods.removeTile;
PGmap.Layer.prototype.show = function () {
    this.element.style.display = "block";
    this.draw && this.draw(this.Mlayer || this);
    return this
};
PGmap.Layer.prototype.hide = PGmap.MainLayer.methods.hide;
PGmap.Layer.prototype.cleanMap = function () {
    this.tileArr = {};
    this.element.innerHTML = "";
    return this
};
PGmap.Layer.prototype.clean = function () {
    this.cleanMap();
    delete this.tilePool;
    delete this.firstStart
};
PGmap.Minimap = function (a) {
    this.globals = a;
    var b = a.minimap;
    this.element = PGmap.Base.layerDiv("PGmap-minimap", "; left:0; top:0;border: 1px solid;");
    this.fragment = PGmap.Base.fragment();
    this.url = a.tileUrl;
    this.tileArr = {};
    this.tilePool = [];
    this.cornerTile = null;
    this.scTileSize = this.tileSize = a.tileSize();
    this.mapBbox = a.mapBbox();
    this.resolution = null;
    this.width = b.width;
    this.height = b.height;
    this.scale = this.zoom = this.countXTiles = null;
    this.buffer = this.tileSize * 1;
    this.pxBbox = null;
    this.key = a.key();
    this.params = {};
    this.initVars = function () {
        if (!this.nextStart) {
            this.nextStart = 1;
            var c = Math.sqrt(a.getWindow().width / b.width * a.getWindow().height / b.height),
                d = Math.min(a.maxZoom(), Math.max(a.minZoom(), Math.floor((a.getZoom() - Math.log(c / b.frameRelMax) * Math.LOG2E + 1.0E-5) * b.orderZoom) / b.orderZoom)),
                e = c / Math.pow(2, a.getZoom() - d),
                f = Math.pow(2, a.getZoom() - d);
            this.zoom = d;
            this.resolution = a.resolutionByZoom(this.zoom);
            this.scale = this.resolution * this.tileSize;
            this.pxBbox = {
                left: -this.buffer / 4,
                top: -this.buffer / 4,
                right: b.width + this.buffer / 4,
                bottom: b.height + this.buffer / 4
            };
            b.elementFrameMap.style.width = b.width * e + "px";
            b.elementFrameMap.style.height = b.height * e + "px";
            b.elementFrameMap.style.left = b.width * (1 - e) / 2 + "px";
            b.elementFrameMap.style.top = b.height * (1 - e) / 2 + "px";
            this.considerTilesCoord()
        }
        var d = this.element.parentNode.getElementsByTagName("div"),
            l;
        for (l in d) if (d[l].className == "PGmap-minimap-shown") {
            this.shown = d[l];
            break
        }
        this.koefCoords = e / f / f / c;
        this.zoomDegree = f;
        this.setCoords()
    };
    this.setCoords = function () {
        var b = this.element.parentNode;
        this.shown.style.width = parseInt(a.getWindow().width / this.zoomDegree) + "px";
        this.shown.style.height = parseInt(a.getWindow().height / this.zoomDegree) + "px";
        this.shown.style.left = parseInt((parseInt(b.style.width) - parseInt(this.shown.style.width)) / 2) + "px";
        this.shown.style.top = parseInt((parseInt(b.style.height) - parseInt(this.shown.style.height)) / 2) + "px";
        b = a.getPosition();
        this.position = {
            left: b.left / this.zoomDegree,
            top: b.top / this.zoomDegree
        };
        this.element.style.cssText += "; left: " + Math.round(this.position.left) +
            "px; top: " + Math.round(this.position.top) + "px;"
    };
    this.update = function () {
        this.nextStart = 0;
        this.firstDraw();
        "update".ErPool(this.zoom, a.getZoom())
    };
    this.processMove = function (a, b) {
        PGmap.Layers.moveScrollPosition({
            left: a * this.zoomDegree,
            top: b * this.zoomDegree
        })
    }
};
PGmap.Minimap.prototype = PGmap.MainLayer.methods;
PGmap.Cursors = {
    grabbing: function () {
        return "url('http://js.tmcrussia.com/cursors/grabbing3.cur'), move"
    }
};
PGmap.ControlsContainer = function () {
    this.element = PGmap.Base.create("PGmap-controls-container")
};
PGmap.ControlsContainer.prototype = {
    show: function () {
        this.element.style.display = "block"
    },
    hide: function () {
        this.element.style.display = "none"
    }
};
PGmap.ScaleControl = function (a) {
    function b() {
        var a = 90,
            b = c.resolution(),
            m = c.getCoords(),
            h = c.getCoords();
        h.lon++;
        b *= m.distance(h);
        a = Math.round(a * b);
        m = "5e" + (a.toString().length - 2);
        a = Math.round(a / m) * m;
        d.innerHTML = a >= 1E3 ? (a / 1E3).toString() + "\u043a\u043c" : a.toString() + "\u043c";
        e.style.width = a / b + "px"
    }
    this.element.className = "PGmap-scale";
    var c = a,
        d = document.createElement("span"),
        e = PGmap.Base.create("PGmap-scale-line");
    d.className = "PGmap-scale-text";
    this.element.appendChild(d);
    this.element.appendChild(e);
    b();
    this.update = b
};
PGmap.ScaleControl.prototype = new PGmap.ControlsContainer;
PGmap.MinimapCont = function (a) {
    function b() {
        h.removeHandler(j.element, "mouseout", b);
        h.removeHandler(j.element, "mousedown", d);
        j.setActive(l)
    }
    function c(b) {
        r ? q.style.right = y + (b ? n : a.minimap.width) + 2 * z + "px" : q.style.left = y + "px";
        u ? q.style.bottom = x + (b ? k : a.minimap.height) + 2 * z + "px" : q.style.top = x + "px"
    }
    function d() {
        var b = m.getSize(q),
            d = n,
            e = k;
        b.width == n + 2 * z && b.height == k + 2 * z ? (d = a.minimap.width, e = a.minimap.height, c(), j.element.className = "PGmap-button minimap minus") : (c("minimized"), j.element.className = "PGmap-button minimap plus");
        a.minimap.disabled = a.minimap.width <= 20 && a.minimap.height <= 20;
        q.style.width = d + "px";
        q.style.height = e + "px";
        p.width = d;
        p.height = e;
        p.setCoords()
    }
    function e(a) {
        move_state == "move" && (dx = oldMoveEvent.clientX - a.clientX, dy = oldMoveEvent.clientY - a.clientY, oldMoveEvent = a, p.processMove(dx, dy));
        a = h.fixEvent(a);
        a.preventDefault();
        return !1
    }
    function f(a) {
        move_state = "none";
        oldMoveEvent = null;
        h.fixEvent(a);
        h.removeHandler(document, "mousemove", e);
        h.removeHandler(document, "mouseup", f);
        q.style.cursor = ""
    }
    var l = 0;
    if (!a.minimap) a.minimap = {};
    var m = PGmap.Utils,
        h = PGmap.Events,
        j = new PGmap.SimpleButton("minimap minus"),
        g = new PGmap.ControlsContainer,
        n = a.minimap.minWidth || a.minimap.minSize || 75,
        k = a.minimap.minHeight || a.minimap.minSize || 57,
        o = a.minimap.frameRelMin || o;
    a.minimap.width = a.minimap.width || 250;
    a.minimap.height = a.minimap.height || 180;
    a.minimap.frameRelMax = a.minimap.frameRelMax || 0.6;
    a.minimap.orderZoom = a.minimap.orderZoom || 1;
    if (o) a.minimap.orderZoom = Math.floor(Math.LN2 / Math.log(Math.max(1, a.minimap.frameRelMax / Math.abs(o + 1.0E-5))));
    a.minimap.elementFrameMap = PGmap.Base.createAbs("frameMapInMini");
    var p = new PGmap.Minimap(a);
    this.layerMini = p;
    a.minimap.control = this;
    var q = this.element;
    q.className = "PGmap-minimap-container";
    q.style.position = "relative";
    q.style.overflow = "hidden";
    q.style.zIndex = 80;
    var r = /right/.test(a.minimap.corners || "bottom left"),
        u = !/top/.test(a.minimap.corners || "bottom left"),
        y = a.minimap.shiftHorizontal || a.minimap.shiftHoriz || a.minimap.shift || 14,
        x = a.minimap.shiftVertical || a.minimap.shiftVert || a.minimap.shift || 14,
        o = (a.minimap.border || "1px").match(/(\d+)px/),
        z = Number(o && o instanceof Array ? o[1] : 1);
    if (a.minimap.border) q.style.border = a.minimap.border;
    q.style.borderWidth = z + "px";
    c();
    q.style.width = a.minimap.width + "px";
    q.style.height = a.minimap.height + "px";
    g.element.appendChild(j.element);
    g.element.style.right = 0;
    q.appendChild(p.element);
    q.appendChild(g.element);
    o = document.createElement("div");
    o.style.position = "absolute";
    o.style.border = "solid 1px blue";
    o.style.width = "50px";
    o.style.height = "50px";
    o.className = "PGmap-minimap-shown";
    q.appendChild(o);
    g.element.appendChild(a.minimap.elementFrameMap);
    p.draw();
    h.addHandler(j.element, "mouseover", function () {
        h.addHandler(j.element, "mousedown", d);
        h.addHandler(j.element, "mouseout", b);
        j.setActive(1)
    });
    h.addHandler(this.element, "mousedown", function (a) {
        move_state = "move";
        oldMoveEvent = a;
        a = h.fixEvent(a);
        h.addHandler(q, "mousemove", e);
        h.addHandler(q, "mouseup", f);
        q.style.cursor = PGmap.Cursors.grabbing;
        a.preventDefault();
        return !1
    });
    oldMoveEvent = null;
    move_state = "none"
};
PGmap.MinimapCont.prototype = new PGmap.ControlsContainer;
PGmap.Layers = function (a) {
    function b() {
        for (var a, b = w.length; b--;) a = w.shift(), x.removeChild(a.element), a.clean()
    }
    function c(a, b) {
        var c = new PGmap.MainLayer(k, a);
        x.appendChild(c.element);
        c.element.style.left = "0px";
        c.element.style.top = "0px";
        c.element.style.zIndex = b != null ? b : x.childNodes.length;
        return c
    }
    function d() {
        q.draw();
        for (var a = 0, b = t.length; a < b; a++) t[a].draw(q)
    }
    function e(a, b, c) {
        var d = k.getZoom(),
            e = k.getBbox(),
            f = e.lon2 - e.lon1,
            g = e.lat2 - e.lat1;
        e.lon1 -= f / 2;
        e.lon2 += f / 2;
        e.lat1 -= g / 2;
        e.lat2 += g / 2;
        return (e.lon1 <= a.lon && e.lon2 >= a.lon && e.lat1 <= a.lat && e.lat2 >= a.lat || c) && (!b || b == d)
    }
    function f(d, f, g, h) {
        A();
        if (d instanceof PGmap.Coord) if (e(d, f, h)) m(d, h);
        else if (f >= a.minZoom() && f <= a.maxZoom()) {
            var h = k.getWindow(),
                j = p.getOffset(r),
                h = {
                    pageX: h.width / 2 + j.left,
                    pageY: h.height / 2 + j.top
                };
            if (g) h.pageX = g.pageX - j.left, h.pageY = g.pageY - j.top;
            g = k.getCoords();
            k.setCoords(d.lon, d.lat);
            k.setZoom(f);
            d = k.getCoords();
            j = k.getPosition();
            k.setPosition(0, 0);
            y.style.cssText += ";left:0;top:0;";
            var l = q;
            l.params.depends = !0;
            !k.noSmoothZoom && g.zoom != f && (z.hide(), l.zoomEffect(g, d, h, j));
            w.push(l);
            setTimeout(b, 12600);
            u.event.user(PGmap.Events.LAYER_FIRST_DRAW, b);
            q = c({}, 1);
            q.firstDraw();
            h = 0;
            for (d = t.length; h < d; h++) t[h].cleanMap().draw(q);
            u.event.user(PGmap.Events.MAP_ZOOMED, [k.getCoords()]);
            u.event.user(f && f != g.zoom ? PGmap.Events.MAP_ZOOMED : PGmap.Events.MAP_MOVED, [k.getCoords()])
        }
    }
    function l(a, b) {
        f(a, b, null, !0)
    }
    function m(a, b) {
        var c = k.getCoords(),
            e = k.getPosition(),
            f = k.resolution(),
            g = -(a.lon - c.lon) / f,
            h = (a.lat - c.lat) / f;
        k.setCoords(a.lon, a.lat);
        var j, l = function () {
            k.setPosition(e.left + g, e.top + h);
            d();
            k.mapObject().event.user(PGmap.Events.MAP_MOVED, [k.getCoords()])
        };
        b ? (y.style.left = Math.round(e.left + g) + "px", y.style.top = Math.round(e.top + h) + "px", l()) : j = p.animate({
            elem: y,
            from: {
                left: e.left,
                top: e.top
            },
            to: {
                left: e.left + g,
                top: e.top + h
            },
            func: p.square,
            callBack: l,
            duration: 500
        });
        j && (B = function () {
            j();
            y.style.left = Math.round(e.left + g) + "px";
            y.style.top = Math.round(e.top + h) + "px";
            l();
            B = null
        })
    }
    var h = PGmap.Events.addHandler,
        j = PGmap.Events.removeHandler,
        g = PGmap.Events.fixEvent,
        n = PGmap.Events.stopEvent,
        k = a,
        o = {}, p = PGmap.Utils,
        q, r = k.mainElement(),
        u = k.mapObject(),
        y = k.mapElement(),
        x = PGmap.Base.create("PGmap-layer-container-layers"),
        z = new PGmap.Geometry(k),
        t = [],
        w = [],
        B, D = this,
        A = function () {};
    r.appendChild(y);
    y.appendChild(x);
    y.appendChild(z.element);
    k.offsetCaps = o;
    q = c();
    q.firstDraw();
    u.event.user(PGmap.Events.LAYER_FIRST_DRAW, function () {});
    u.event.user(PGmap.Events.LAYER_DRAW, function () {
        for (var a = 0, b = t.length; a < b; a++) t[a].draw(q)
    });
    u.event.user(PGmap.Events.ZOOMING_END, function () {
        z.show()
    });
    (function () {
        function b(a) {
            a = g(a);
            P = {
                x: a.touches && a.touches[0].pageX || a.pageX,
                y: a.touches && a.touches[0].pageY || a.pageY,
                t: +new Date
            };
            var c = K[K.length - 1];
            f({
                left: P.x - c.x,
                top: P.y - c.y
            });
            K.push(P);
            K.length > U && K.shift();
            clearTimeout(V);
            V = setTimeout(d, 100);
            a.preventDefault();
            a.stopPropagation();
            return !1
        }
        function c(a) {
            a = g(a);
            y.style.cursor = "";
            var d = (P.t - K[K.length - 1].t) / 1E3,
                f = K[0],
                h = (P.t - K[0].t) / 1E3 * 24;
            C = {
                left: (P.x - f.x) / h,
                top: (P.y - f.y) / h
            };
            Y = Math.min(1, Math.sqrt(C.left * C.left + C.top * C.top)) / $;
            C.left = Math.min(W,
            Math.max(-W, C.left));
            C.top = Math.min(W, Math.max(-W, C.top));
            if (K.length < 4) C.left = Math.min(Q, Math.max(-Q, C.left)), C.top = Math.min(Q, Math.max(-Q, C.top));
            if (k.noKinematicDrop || d > ca) C.left = C.top = 0;
            clearTimeout(V);
            clearTimeout(G);
            G = setTimeout(e, 30);
            r.ondragstart = r.onselectstart = null;
            j(document, R.mouseup, c);
            j(document, R.mousemove, b);
            a.preventDefault();
            return !1
        }
        function e() {
            C = {
                left: C.left * X,
                top: C.top * X
            };
            if (Math.abs(C.left) < 0.1) C.left = 0;
            if (Math.abs(C.top) < 0.1) C.top = 0;
            if (C.left || C.top) {
                var a = Math.sqrt(C.left * C.left + C.top * C.top) / $;
                Y != a && (Y = a, d());
                f(C);
                G = setTimeout(e, 30)
            } else A(), u.event.user(PGmap.Events.MAP_MOVED, [k.getCoords()])
        }
        function f(a) {
            var b = k.getPosition(),
                c = k.resolution(),
                b = k.setPosition(b.left + a.left, b.top + a.top);
            y.style.left = Math.round(b.left) + "px";
            y.style.top = Math.round(b.top) + "px";
            L.lon -= a.left * c;
            L.lat += a.top * c;
            k.setCoords(L.lon, L.lat);
            u.event.user(PGmap.Events.MAP_MOVE, [a])
        }
        function m(a) {
            a = g(a);
            a.type == "mouseover" ? (r.ondragstart = r.onselectstart = function () {} /*h(window, "DOMMouseScroll", o),*/
            /*h(window, "mousewheel", o),*/ /*h(document, "mousewheel", o)*/) : (/*j(window, "DOMMouseScroll", o),*/ j(window, "mousewheel", o), j(document, "mousewheel", o))
			
        }
        function o(a) {
            g(a);
            Z = (new Date).getMilliseconds();
            Math.abs(aa - Z) > 100 && t(a);
            aa = Z;
            a.preventDefault();
            a.stopPropagation();
            return !1
        }
        function t(b) {
            var c = 0,
                b = g(b);
            b.wheelDelta ? c = b.wheelDelta / 120 : b.detail ? c = -b.detail / 3 : b.scale && (j(r, "gesturestart", t), h(r, "touchmove", w), h(r, "touchend", T), b.preventDefault(), b.stopPropagation());
            if (c && !a.isiOS) b.delta = c, O(b)
        }
        function w(a) {
            var b = k.getWindow().height;
            ba.style.cssText += "-webkit-transform: scale(" + a.scale + ") translate3d(0px, " + b * (1 - Math.pow(a.scale, 0.5)) / 2 + "px, 0px); -o-transform: scale(" + a.scale + ") translate3d(0px, " + b * (1 - Math.pow(a.scale, 0.5)) / 2 + "px, 0px);";
            a.preventDefault();
            a.stopPropagation()
        }
        function T(a) {
            a.delta = a.scale < 1 ? -1 : 1;
            ba.style.cssText += "-webkit-transform: scale(1) translate3d(0px, 0px, 0px); -o-transform: scale(1) translate3d(0px, 0px, 0px);";
            O(a);
            a.preventDefault();
            a.stopPropagation();
            j(r, "touchend", T);
            j(r,
                "touchmove", w);
            h(r, "gesturestart", t)
        }
        function O(a) {
            if (a.target === r || p.hasDescendant(x, a.target) || p.hasDescendant(z.element, a.target)) {
                if (a.shiftKey || a.ctrlKey) a.delta = -a.delta;
                var b = a.delta > 0 ? 1 : -1,
                    c = k.getCoords(),
                    d = c.zoom + b,
                    e = k.resolution();
                if (d >= k.minZoom() && d <= k.maxZoom()) {
                    var f = k.getWindow(),
                        g = k.resolutionByZoom(d),
                        h = p.getOffset(r),
                        e = b > 0 ? g : e;
                    l(new PGmap.Coord(c.lon + ((a.touches && a.touches[0].pageX || a.pageX) - h.left - f.width / 2) * e * b, c.lat - ((a.touches && a.touches[0].pageY || a.pageY) - h.top - f.height / 2) * e * b), d, a);
                    n(a)
                }
            }
        }
        var C = {
            left: 0,
            top: 0
        }, L, U = 4,
            K = [],
            G = 0,
            V = 0,
            N = k.getWindow(),
            N = Math.sqrt(N.width * N.width + N.height * N.height) / 900 / 1.15,
            W = 50 * N,
            Q = 30 * N,
            X = 0.75,
            Y, $ = 40,
            ca = 0.1,
            R = PGmap.EventFactory.eventsType;
        h(r, R.mousedown, function (a) {
            if (a.touches && a.touches.length > 1) h(r, "touchmove", w), h(r, "touchend", T);
            else {
                a = g(a);
                if (a.target === r || p.hasDescendant(x, a.target) || p.hasDescendant(z.element, a.target)) {
                    A();
                    y.style.cursor = PGmap.Cursors.grabbing();
                    L = k.getCoords();
                    K = [{
                        x: a.touches && a.touches[0].pageX || a.pageX,
                        y: a.touches && a.touches[0].pageY || a.pageY,
                        t: +new Date
                    }];
                    var d = k.getPosition(),
                        e = PGmap.Utils.getOffset(r);
                    S = k.xyToLonLat(K[0].x - e.left - d.left, K[0].y - e.top - d.top).lon;
                    h(document, R.mouseup, c);
                    D.RIGHTLIMITLON && D.LEFTLIMITLON ? (S < 0 && S < D.RIGHTLIMITLON || S > 0 && S > D.RIGHTLIMITLON) && Math.abs(S) > D.LEFTLIMITLON && h(document, R.mousemove, b) : h(document, R.mousemove, b);
                    r.ondragstart = r.onselectstart = function () {
                        return !1
                    };
                    a.preventDefault()
                }
                return !1
            }
        });
        h(r, "mouseover", m);
        h(r, "mouseout", m);
        h(r, "dblclick", function (a) {
            a = g(a);
            a.delta = 1;
            O(a)
        });
        h(r, "gesturestart", t);
        (function () {
            PGmap.Events.resizeEvent.add(r, function (a) {
                var b = k.getWindow(),
                    c = k.getPosition(),
                    b = k.setPosition(c.left + (a.width - b.width) / 2, c.top + (a.height - b.height) / 2);
                k.getWindow();
                y.style.cssText += "; left: " + Math.round(b.left) + "px; top: " + Math.round(b.top) + "px;";
                k.setWindow(a.width, a.height);
                q.updateWindow();
                d()
            })
        })();
        L = k.getCoords();
        PGmap.Utils.getPosIntoView(a.mainElement());
        a.getPosition();
        q.tileSize = 256;
        var S;
        PGmap.Layers.moveScrollPosition = function (a) {
            f(a);
            d()
        };
        var P = {};
        A = function () {
            G && (G = clearTimeout(G) || 0, C = {
                left: 0,
                top: 0
            }, k.getCoords(), d());
            B && B()
        };
        var aa = (new Date).getMilliseconds(),
            Z, ba = PGmap.Utils.getElementsByClassName("PGmap-layer-container", document)[0]
    })();
    this.geometry = z;
    this.elements = t;
    this.setCenter = f;
    this.setCenterByBbox = function (a, b) {
        var c, d, e, g, h, j, l, m = k.getWindow(),
            n = k.maxZoom(),
            o = n,
            p = k.minZoom();
        if (a && a.lon1 != void 0 && a.lat1 != void 0 && a.lon2 != void 0 && a.lat2 != void 0) {
            l = new PGmap.Coord((a.lon1 + a.lon2) / 2, (a.lat1 + a.lat2) / 2);
            for (d = b ? function () {
                o = n;
                return e > 0 && g > 0
            } : function () {
                return h && Math.abs(e) > h && Math.abs(g) > j
            }; n >= p;) {
                c = k.resolutionByZoom(n);
                e = m.width * c - (a.lon2 - a.lon1);
                g = m.height * c - (a.lat2 - a.lat1);
                if (d()) {
                    f(l, o);
                    break
                }
                h = Math.abs(e);
                j = Math.abs(g);
                n--;
                o = n
            }
        }
    };
    this.setCenterInView = function (a, b, c, d, e) {
        if (!B) {
            A();
            var b = b || o,
                c = (c || k.getZoom()) * 1,
                g = k.resolutionByZoom(c);
            if (a.hasOwnProperty("lon") && a.hasOwnProperty("width")) var h = k.lonlatToXY(a.lon, a.lat, g),
                a = {
                    x1: Math.min(h.x, h.x + a.width),
                    y1: Math.min(h.y, h.y + a.height),
                    x2: Math.max(h.x, h.x + a.width),
                    y2: Math.max(h.y,
                    h.y + a.height)
                };
            a.hasOwnProperty("x") && a.hasOwnProperty("y") && (a = {
                x1: a.x,
                y1: a.y,
                x2: a.x,
                y2: a.y
            });
            var j = k.getBboxByZoom(c),
                j = {
                    lt: k.lonlatToXY(j.lon1, j.lat1, g),
                    rb: k.lonlatToXY(j.lon2, j.lat2, g)
                };
            if (d) return g = k.xyToLonLat((a.x1 + a.x2) / 2 - (b.left || 0) / 2 + (b.right || 0) / 2, (a.y1 + a.y2) / 2 - (b.top || 0) / 2 + (b.bottom || 0) / 2, g, c), e || f(g, c), [g, c];
            var d = Math.round(Math.max(j.lt.x + (b.left || 0) - a.x1, 0)),
                h = Math.round(Math.max(a.x2 - j.rb.x + (b.right || 0), d, 0)),
                l = Math.round(Math.max(j.rb.y + (b.top || 0) - a.y1, 0)),
                a = Math.round(Math.max(a.y2 - j.lt.y + (b.bottom || 0), l, 0));
            h == d ? h = 0 : d = 0;
            a == l ? a = 0 : l = 0;
            if (d || h || l || a || c != k.getZoom()) return b = k.getCoords(), b = k.lonlatToXY(b.lon, b.lat, g), g = k.xyToLonLat(b.x - d + h, b.y - l + a, g, c), e || f(g, c), [g, c]
        }
    };
    this.willSetMovie = e;
    this.setCenterFast = l;
    this.create = function (a, b, c, d) {
        typeof b == "number" && (b = c = b);
        a = new PGmap.Layer(k, {
            url: a,
            formatTileUrl: b,
            cache: d
        });
        if (t[c]) {
            x.insertBefore(a.element, t[c].element);
            a.element.style.zIndex = t[c].element.style.zIndex;
            t.splice(c, 0, a);
            c += 1;
            for (b = t.length; c < b; c++) t[c].element.style.zIndex = 5 + c + 1
        } else x.appendChild(a.element), a.element.style.zIndex = 5 + t.length + 1, t.push(a);
        a.draw(q);
        return a
    };
    this.remove = function (a) {
        var b = [],
            c = typeof a == "number" ? t[a] : a;
        if (c instanceof PGmap.Layer) {
            c.clean();
            x.removeChild(c.element);
            for (var d, c = t.length; d < c; d++) d != a && (b[d] = t[d]);
            t = b
        }
    };
    this.hide = function (a) {
        t[a] && t[a].hide()
    };
    this.show = function (a) {
        t[a] && t[a].show()
    };
    this.update = function (a) {
        t[a] && t[a].update()
    };
    this.setOffSets = function (a) {
        o.left = (a.hasOwnProperty("left") ? a.left : o.left) || 0;
        o.top = (a.hasOwnProperty("top") ? a.top : o.top) || 0;
        o.right = (a.hasOwnProperty("right") ? a.right : o.right) || 0;
        o.bottom = (a.hasOwnProperty("bottom") ? a.bottom : o.bottom) || 0;
        k.offsetCaps = o
    }
};
PGmap.SimpleButton = function (a, b) {
    function c() {
        h.state != k && (o(m, "mousedown", e), o(m, "mouseout", d), h.setActive(g))
    }
    function d() {
        h.state != k && (p(m, "mouseout", d), p(m, "mousedown", e), h.setActive(j))
    }
    function e() {
        h.state != k && (p(m, "mouseover", c), p(m, "mouseout", d), o(m, "mouseup", f), h.setActive(n))
    }
    function f() {
        p(m, "mousedown", e);
        o(m, "mouseover", c);
        h.state != k && h.setActive(j)
    }
    var l = document.createElement("li"),
        m = document.createElement("ul"),
        h = this,
        j = 0,
        g = 1,
        n = 2,
        k = 3,
        o = PGmap.Events.addHandler,
        p = PGmap.Events.removeHandler,
        q = j;
    m.className = "PGmap-button " + a;
    for (var r = 0; r < 3; r++) {
        l = l.cloneNode(!1);
        l.style.listStyle = "none";
        switch (r) {
            case 0:
                l.className = "up active";
                break;
            case 1:
                l.className = "over";
                break;
            default:
                l.className = "down"
        }
        m.appendChild(l)
    }
    b && m.setAttribute("title", b);
    this.element = m;
    this.state = q;
    this.name = a;
    o(m, "mouseover", c);
    this.clean = function () {
        p(m, "mouseover", c);
        p(m, "mouseout", d);
        p(m, "mousedown", e);
        p(m, "mouseup", f)
    }
};
PGmap.SimpleButton.prototype = new PGmap.ControlsContainer;
PGmap.SimpleButton.prototype.setActive = function (a) {
    for (var b = this.element.getElementsByTagName("li"), c = 0; c < 3; c++) b[c].className = b[c].className.replace(" active", "");
    b[a == 3 ? 2 : a].className += " active";
    this.element.style.cursor = "pointer";
    this.state = a
};
PGmap.Control = function () {
    this.isactive = !1;
    this.commute = function () {};
    this.execute = function () {};
    this.disconnect = function () {};
    this.button = null
};
PGmap.Control.prototype = {
    show: function () {
        this.button.element.style.display = "block"
    },
    hide: function () {
        this.button.element.style.display = "none"
    },
    disable: function () {
        this.button.setActive(0)
    },
    enable: function () {
        this.button.setActive(3)
    }
};
PGmap.Router = function (a, b) {
    function c(c) {
        var d, e, f, j, l, m, n, o = a,
            p = c,
            t = PGmap.Base.create(null, "position:absolute;left:0px;top:0px"),
            w = function () {
                e = o.lonlatToXY(d.lon, d.lat);
                t.style.cssText = "position:absolute;left:" + e.x + "px;top:" + e.y + "px"
            }, u = function () {
                (c + "").indexOf("b") && (q && (q = clearTimeout(q)), r = 1, B != c && g && g.plan[B] && g.plan[B].hide(), B = c);
                G.scale(1.5)
            }, x = PGmap.Base.create(),
            G = PGmap.Graphics.Circle(0, 0, H).attr({
                "stroke-width": 1,
                stroke: O,
                fill: O
            }).appendTo(t).paper;
        PGmap.Graphics.Circle(0, 0, I).attr({
            "stroke-width": T,
            stroke: C
        }).appendTo(G);
        x.style.cssText = "left:0; bottom:0";
        x.innerHTML = J;
        (l = x.firstChild) && (l.style.cursor = "move");
        this.element = t;
        var L = new y(this, {
            dragStart: function () {
                if ((c + "").indexOf("b") && (m = !0, U && (x.className = F + U, !x.parentNode || !x.parentNode.tagName))) if (t.appendChild(x), !M) G.parent = G.remove().parent.graphics = null, L.rebind([l, this]);
                A.onDrag && A.onDrag(c)
            },
            dragEnd: function (b) {
                e = b;
                d = a.xyToLonLat(b.x, b.y);
                f = d.getGeo();
                if ((c + "").indexOf("b")) {
                    for (var h = k.plan[p].to, j = -1, l = 1E10, m = k.points.length; m--;) b = k.points[m], b = Math.abs(h[0] - b[0]) + Math.abs(h[1] - b[1]), b < l && (l = b, j = m);
                    for (m = k.indexes.length; m--;) if (j > k.indexes[m]) {
                        g.plan.splice(p, 1);
                        s.splice(m + 1, 0, {
                            lon: f.lon,
                            lat: f.lat
                        });
                        v.splice(m + 1, 0, this);
                        break
                    }
                } else s[p] = {
                    lon: f.lon,
                    lat: f.lat
                };
                D.get({
                    points: s
                });
                A.onDrop && A.onDrop(c, n, d)
            },
            click: function () {
                A.onClick && A.onClick(c)
            }
        });
        z(t, "mouseover", function () {
            u();
            A.onMouseOver && A.onMouseOver(c)
        });
        z(t, "mouseout", function () {
            G.scale(1);
            m || r && h();
            A.onMouseOut && A.onMouseOut(c)
        });
        z(t, "dblclick", function (a) {
            if (n) return D.removePoint(p),
            PGmap.Events.killEvent(a || window.event)
        });
        this.doOver = function () {
            u()
        };
        this.doOut = function () {
            G.scale(1);
            m || r && h()
        };
        this.doClick = function () {};
        this.update = function () {
            w.call(this);
            b.element.appendChild(t);
            this.update = w
        };
        this.show = function () {
            G.show()
        };
        this.hide = function () {
            j || G.hide()
        };
        this.visibility = function (a) {
            (j = a) ? G.show() : G.hide()
        };
        this.remove = function () {
            t.parentNode && t.parentNode.removeChild(t)
        };
        this.setCoords = function (a, b) {
            p = c = b;
            d = a;
            j = 0
        };
        this.getXY = function () {
            return e
        };
        this.setBase = function (a, b,
        e) {
            m = !0;
            n = a && a < b - 1;
            if (!n && (x.className = F + K[a && 1], !x.parentNode || !x.parentNode.tagName)) if (t.appendChild(x), !E) G.parent = G.remove().parent.graphics = null, L.rebind([l, this]);
            c = "b" + (p = a);
            f = {
                lon: e.lon,
                lat: e.lat
            };
            d = new PGmap.Coord(e.lon, e.lat, !0);
            this.update();
            return this
        }
    }
    function d(a) {
        (k = JSON.parse(a)) && k.points && k.points.length > 1 ? (g = g || b.create({
            type: "route",
            PPlanConst: c,
            moveF: m,
            outF: h,
            style: L
        }), g.setGeoArray(k.points), g.setGeoPlan(k.plan), g.base = v, g.init ? b.add(g) : g.update(), g.show()) : (g && (g = b.remove(g) || null), j());
        u && e();
        u = null;
        A.get && A.get(k);
        if (D.onRouteReceived && PGmap.Utils.isFunc(D.onRouteReceived)) D.onRouteReceived(a)
    }
    function e() {
        if (g) {
            var b = a.xyToLonLat(g.real_box[0], g.real_box[1]),
                c = a.xyToLonLat(g.real_box[2], g.real_box[3]);
            a.mapObject().setCenterByBbox({
                lon1: b.lon,
                lat2: b.lat,
                lon2: c.lon,
                lat1: c.lat
            }, !0)
        }
    }
    function f(b) {
        if (!a.mainObject.ruler || !a.mainObject.ruler.isactive) {
            var b = x(b),
                c = w.getOffset(a.mainElement());
            if (n && (n = clearTimeout(n) || 0, p && p.x == (b.touches && b.touches[0].pageX || b.pageX) - c.left && p.y == (b.touches && b.touches[0].pageY || b.pageY) - c.top)) return p = null;
            p = {
                x: (b.touches && b.touches[0].pageX || b.pageX) - c.left,
                y: (b.touches && b.touches[0].pageY || b.pageY) - c.top
            }
        }
    }
    function l(b) {
        n && clearTimeout(n);
        var b = x(b),
            c = w.getOffset(a.mainElement());
        if (b.touches || p && !(b.pageX - c.left != p.x || b.pageY - c.top != p.y)) n = setTimeout(function () {
            n = 0;
            var b = a.getPosition();
            D.setRoutePoint(a.xyToLonLat(p.x - b.left, p.y - b.top));
            p = null
        }, 200)
    }
    function m(b) {
        q && (q = clearTimeout(q));
        b = x(b);
        r = 1;
        for (var c = -1, d = g.plan, e = 1E5, f = d.length, h = a.getPosition(), j = w.getOffset(a.mainElement()), l = (b.touches && b.touches[0].pageX || b.pageX) - h.left - j.left, h = (b.touches && b.touches[0].pageY || b.pageY) - h.top - j.top; f--;) b = d[f].getXY(), b = Math.pow(l - b.x, 2) + Math.pow(h - b.y, 2), b < e && (e = b, c = f);
        c != B && (d[B] && d[B].hide(), d[B = c] && d[c].show())
    }
    function h() {
        q && clearTimeout(q);
        q = setTimeout(function () {
            g && (r = q = null, g.plan[B] && g.plan[B].hide(), B = -1)
        }, 1E3)
    }
    function j() {
        s = [];
        for (var a = v.length; a--;) v[a] && v[a].remove();
        v = [];
        B = -1
    }
    var g, n, k, o, p, q, r, u, y = PGmap.Events.Draggable,
        x = PGmap.Events.fixEvent,
        z = PGmap.Events.addHandler,
        t = PGmap.Events.removeHandler,
        w = PGmap.Utils,
        B = -1,
        D = this,
        A = {}, s = [],
        v = [],
        E = 1,
        M = 1,
        J = "<i></i>",
        F = "PGmap-route-point-",
        H = 5,
        I = 5,
        T = 2,
        O = "#fff",
        C = "rgba(3,10,103,0.7)",
        L = {
            color: "rgba(3,10,103,0.7)",
            lineHeight: 4
        }, U = "m",
        K = ["a", "b"],
        G = PGmap.EventFactory.eventsType;
    this.button = new PGmap.SimpleButton("route", a.langObj.controlsTitle.route);
    this.execute = function () {
        this.button && this.enable();
        if (!o) o = a.mapElement(), z(o, G.mousedown, f), z(o, G.mouseup, l), this.isactive = !0
    };
    this.disconnect = function () {
        this.disable();
        if (o) t(o, G.mousedown, f), t(o, G.mouseup, l), this.isactive = !1, o = null, this.remove(), g = null
    };
    this.commute = function () {
        this.disable();
        if (o) t(o, G.mousedown, f), t(o, G.mouseup, l), this.isactive = !1, o = null
    };
    this.setBboxByRoute = e;
    this.callBack = A;
    this.show = function () {
        g && g.show()
    };
    this.hide = function () {
        g && g.hide()
    };
    this.removePoint = function (a) {
        v.splice(a, 1)[0].remove();
        s.splice(a, 1);
        this.get({
            points: s
        })
    };
    this.visibilityPoint = function (a, b) {
        (a + "").indexOf("b") && g && g.plan[a] && g.plan[a].visibility(b)
    };
    this.remove = function () {
        g && (g = b.remove(g) || null);
        j()
    };
    this.doOver = function (a) {
        (a + "").indexOf("b") ? g && g.plan[a] && g.plan[a].doOver() : v[a] && v[a].doOver()
    };
    this.doOut = function (a) {
        (a + "").indexOf("b") ? g && g.plan[a] && g.plan[a].doOut() : v[a] && v[a].doOut()
    };
    this.doClick = function (a) {
        (a + "").indexOf("b") ? g && g.plan[a] && g.plan[a].doClick() : v[a] && v[a].doClick()
    };
    this.setRoutePoint = function (a, d) {
        if (!(arguments.length - 1) && A.onSetPoint && A.onSetPoint(a, !! g) === !1) return !1;
        g ? (g = b.remove(g) || null,
        j()) : v.length > 1 && v[0] && j();
        if (a instanceof PGmap.Coord) {
            var e = arguments.length - 1 ? d : v.length - 1 ? v[0] ? -1 : 0 : 1;
            s[e] = a.getGeo();
            v[e] = (new c(e)).setBase(e, 2, s[e]);
            arguments.length - 1 || A.onDrop && A.onDrop("b" + e, 0, a);
            if (v[0] && v[1]) s.length = 2, this.get({
                points: s
            })
        }
    };
    this.get = function (b, e, f) {
        if (b.points.length < 2) return !1;
        var g, h = b.points,
            j = b.points.length,
            l = b.url || "http://route.tmcrussia.com/cgi/getroute?";
        g = b.method || "optimal";
        var k = b.type || "route,plan,indexes",
            m = ["method", "url", "points", "type"],
            e = d;
        s = h;
        l.indexOf("?") < 0 && (l += "?");
        l += "n=" + j + "&type=" + k + "&method=" + g;
        if (v.length > j) {
            g = v.splice(j, v.length - j);
            for (k = g.length; k--;) g[k].remove()
        }
        for (k = 0; k < j; k++) g = h[k] instanceof PGmap.Coord ? h[k].getGeo() : h[k], v[k] = (v[k] || new c(k)).setBase(k, j, g), l += "&p" + k + "x=" + g.lon + "&p" + k + "y=" + g.lat;
        for (g = 0; g < m.length; g++) delete b[m[g]] && j--;
        for (var n in b) l += "&" + n + "=" + b[h];
        this.traffic && (l += "&traffic=1");
        u = f;
        a.mapObject().ajax({
            url: l + "&" + a.key(),
            callback: e,
            type: "GET"
        })
    };
    this.onRouteReceived = function () {};
    a.mapObject().event.user(PGmap.Events.MAP_ZOOMED,

    function () {
        if (!g) for (var a = v.length; a--;) v[a] && v[a].update()
    })
};
PGmap.Router.prototype = new PGmap.Control;
PGmap.Ruler = function (a, b) {
    function c(c) {
        var e, f, g, j = a,
            k = !0,
            r = 0,
            E = h ? h.points.length : 0,
            u = function () {
                e = j.lonlatToXY(c.lon, c.lat);
                F.style.left = e.x + "px";
                F.style.top = e.y + "px"
            }, y = function () {
                I.scale(1.5);
                y = function () {
                    I.scale(1.5);
                    I.group.style.cursor = "move";
                    F.style.zIndex = 11;
                    if (!k && E) H.style.display = "block"
                }
            }, F = document.createElement("div"),
            H = document.createElement("div"),
            I = PGmap.Graphics.Circle(0, 0, 7).attr({
                "stroke-width": 1,
                stroke: "#f00",
                fill: "#fff"
            }).appendTo(F).paper;
        F.className = "PGmap-ruler";
        H.className =
            "PGmap-ruler-label";
        PGmap.Graphics.Circle(0, 0, 4).attr({
            "stroke-width": 1,
            stroke: "#f00",
            fill: "#f00"
        }).appendTo(I);
        H.innerHTML = '<div style="padding-right:25px; margin: 1px 0 1px 7px;cursor:auto;"></div><div class="r-closer"><i class="r-closer__icon"></i></div>';
        g = H.firstChild;
        q(H, z.mousedown, function (a) {
            o(a || window.event)
        });
        f = H.lastChild;
        F.appendChild(H);
        this.element = F;
        new n([I, this], {
            dragStart: function () {},
            drag: function (b) {
                e = b;
                c = a.xyToLonLat(b.x, b.y);
                this[1].lon = c.lon;
                this[1].lat = c.lat;
                F.style.zIndex = 11;
                c.getGeo();
                h && h.update();
                m(E)
            },
            dragEnd: function () {},
            click: function () {}
        });
        q(I.element, "mouseover", function () {
            y()
        });
        q(I.element, "mouseout", function () {
            I.scale(1);
            F.style.zIndex = 10;
            if (!k) H.style.display = "none"
        });
        q(I.element, "dblclick", function (a) {
            if (h) {
                h.points.splice(E, 1)[0].remove();
                h.points.length ? h.update() : h = b.remove(h) || null;
                var c = E;
                if (h) {
                    var d = h.points,
                        e = d.length,
                        f = l(d[c - 1], d[c]);
                    d[c] && d[c].value(f);
                    for (c -= 1; c < e; c++) d[c] && d[c].setInd(c);
                    d[e - 1] && d[e - 1].lockLabel()
                }
            }
            return p(a || window.event)
        });
        q(f, z.click, function () {
            (!x.remove || x.remove() !== !1) && d()
        });
        this.lon = c.lon;
        this.lat = c.lat;
        this.update = function () {
            u.call(this);
            b.element.appendChild(F);
            this.update = u
        };
        this.point = function () {
            return c
        };
        this.unlockLabel = function () {
            k = !1;
            H.style.display = "none";
            f.style.display = "none";
            g.style.cssText = ""
        };
        this.lockLabel = function () {
            k = !0;
            H.style.display = "block";
            f.style.display = "";
            g.style.cssText = "padding-right:25px;"
        };
        this.remove = function () {
            F.parentNode && F.parentNode.removeChild(F)
        };
        this.setInd = function (a) {
            E = a
        };
        this.updateVal = function () {
            var a = E,
                b = 0;
            if (h) for (var c = 0; c < a; c++) b += h.points[c].value();
            a = (Math.round((b + r) / 10) / 100 + "").replace(".", ",") + " \u043a\u043c";
            g.innerHTML = a
        };
        this.value = function (a) {
            var b = r;
            arguments.length && (r = a, this.updateVal());
            return b
        };
        this.getXY = function () {
            return e
        }
    }
    function d() {
        h && (h = b.remove(h) || null)
    }
    function e(b) {
        var b = k(b),
            c = u.getOffset(a.mainElement());
        if (y && (y = clearTimeout(y) || 0, g && g.x == (b.touches && b.touches[0].pageX || b.pageX) - c.left && g.y == (b.touches && b.touches[0].pageY || b.pageY) - c.top)) return g = null;
        g = {
            x: (b.touches && b.touches[0].pageX || b.pageX) - c.left,
            y: (b.touches && b.touches[0].pageY || b.pageY) - c.top
        }
    }
    function f(d) {
        y && clearTimeout(y);
        var d = k(d),
            e = u.getOffset(a.mainElement());
        if (d.touches || g && !(d.pageX - e.left != g.x || d.pageY - e.top != g.y)) y = setTimeout(function () {
            y = 0;
            var d = a.getPosition(),
                d = new c(a.xyToLonLat(g.x - d.left, g.y - d.top));
            h ? h.points.push(d) : h = b.create({
                type: "ruler",
                points: [d]
            });
            h.init ? b.add(h) : h.update();
            h.points[h.points.length - 2] && h.points[h.points.length - 2].unlockLabel();
            m(h.points.length - 1);
            g = null
        }, 200)
    }
    function l(a, b) {
        return a && b ? a.point().distance(b.point()) * 1 : 0
    }
    function m(a) {
        if (h) {
            var b = h.points,
                c = l(b[a - 1], b[a]),
                d = l(b[a], b[a + 1]);
            b[a] && b[a].value(c);
            b[a + 1] && b[a + 1].value(d);
            a += 1;
            for (c = b.length; a < c; a++) b[a].updateVal()
        }
    }
    var h, j, g, n = PGmap.Events.Draggable,
        k = PGmap.Events.fixEvent,
        o = PGmap.Events.stopEvent,
        p = PGmap.Events.killEvent,
        q = PGmap.Events.addHandler,
        r = PGmap.Events.removeHandler,
        u = PGmap.Utils,
        y, x = {}, z = PGmap.EventFactory.eventsType;
    this.button = new PGmap.SimpleButton("ruler",
    a.langObj.controlsTitle.ruler);
    this.execute = function () {
        this.button && this.enable();
        if (!j) j = a.mapElement(), q(j, z.mousedown, e), q(j, z.mouseup, f), this.isactive = !0, u.setClass(a.mapElement(), ["ruler-active"], ["ruler-active"])
    };
    this.disconnect = function () {
        this.disable();
        if (j) r(j, z.mousedown, e), r(j, z.mouseup, f), j = null, this.isactive = !1, u.setClass(a.mapElement(), 0, ["ruler-active"]), b.remove(h), h = null
    };
    this.commute = function () {
        this.disable();
        if (j) r(j, z.mousedown, e), r(j, z.mouseup, f), j = null, this.isactive = !1, u.setClass(a.mapElement(),
        0, ["ruler-active"])
    };
    this.callBack = x;
    this.setResult = function (a) {
        d();
        var e;
        a[0] && (h = b.create({
            type: "ruler",
            points: [e = new c(new PGmap.Coord(a[0].lon, a[0].lat))]
        }));
        e.unlockLabel();
        for (var f = 1, g = a.length; f < g; f++) h.points.push(e = new c(new PGmap.Coord(a[f].lon, a[f].lat))), e.unlockLabel(), m(f);
        h.init ? b.add(h) : h.update();
        h.points[h.points.length - 1].lockLabel()
    };
    this.getResult = function () {
        var a = [];
        if (h) for (var b = 0, c = h.points, d = c.length; b < d; b++) a[b] = {
            lon: c[b].lon,
            lat: c[b].lat
        };
        return a.length ? a : null
    };
    this.remove = d
};
PGmap.Ruler.prototype = new PGmap.Control;
PGmap.CoordsControl = function (a) {
    function b() {
        var b = a.getCoords();
        c.set(b.lon, b.lat);
        a.minimap && !a.minimap.disabled && a.minimap.control.layerMini.setCoords()
    }
    var c = this;
    this.element = document.createElement("div");
    this.element.className = "PGmap-coords";
    this.element.style.cssText = "font-size: .7em;";
    this.disconnect = function () {
        a.mapObject().event.remove(PGmap.Events.MAP_MOVE, b)
    };
    a.mapObject().event.user(PGmap.Events.MAP_MOVE, b)
};
PGmap.CoordsControl.prototype = new PGmap.ControlsContainer;
PGmap.CoordsControl.prototype.set = function (a, b) {
    this.element.innerHTML = PGmap.Utils.formatString(a, b)
};
PGmap.Control.Slider = function (a) {
    function b() {
        a.mapObject().controls.scale && a.mapObject().controls.scale.update();
        a.minimap && !a.minimap.disabled && a.minimap.control.layerMini.update()
    }
    var c = this;
    this.minus = new PGmap.SimpleButton("minus", a.langObj.controlsTitle.minus);
    this.plus = new PGmap.SimpleButton("plus", a.langObj.controlsTitle.plus);
    a.getZoom() <= a.minZoom() && this.minus.setActive(3);
    a.getZoom() >= a.maxZoom() && this.plus.setActive(3);
    PGmap.Events.addHandler(this.minus.element, "mousedown", function () {
        if (c.minus.state != 3) {
            var b = a.getCoords();
            b.zoom--;
            a.mainObject.layers.setCenter(new PGmap.Coord(b.lon, b.lat), b.zoom)
        }
    });
    PGmap.Events.addHandler(this.plus.element, "mousedown", function () {
        if (c.plus.state != 3) {
            var b = a.getCoords();
            b.zoom++;
            a.mainObject.layers.setCenter(new PGmap.Coord(b.lon, b.lat), b.zoom)
        }
    });
    this.disconnect = function () {
        a.mapObject().event.remove(PGmap.Events.MAP_ZOOMED, b)
    };
    a.mapObject().event.user(PGmap.Events.MAP_ZOOMED, b)
};
PGmap.Control.Slider.prototype = new PGmap.Control;
PGmap.Control.Traffic = function (a) {
    function b() {
        J.style.display = "none"
    }
    function c(a) {
        for (var b = B.button.element.getElementsByTagName("li"), c = b.length; c--;) b[c].innerHTML = '<span style="display:block;margin-left:29px;margin-top:6px;position:absolute;">' + a + "</span>"
    }
    function d(b) {
        J.innerHTML = b.clientX - v.clientX;
        var b = b || window.event,
            c = a.getPosition(),
            d = PGmap.Utils.getOffset(this),
            e = PGmap.Utils.getOffset(a.mapDOM),
            c = E.getImageData((b.pageX || b.clientX) - d.left - c.left, (b.pageY || b.clientY) - d.top - c.top, 1, 1);
        if (c.data[1]) {
            J.innerHTML = c.data[1] + " \u043a\u043c/\u0447";
            if (!J.offsetHeight) J.style.display = "block";
            J.style.top = b.clientY - e.top + 5 + "px";
            J.style.left = b.clientX - e.left + 5 + "px"
        } else J.style.display = "none"
    }
    function e(a) {
        window.clearInterval(r);
        window.clearInterval(u);
        return x(a || window.event)
    }
    function f(a) {
        k();
        m();
        r = window.setInterval(k, 6E4);
        u = window.setInterval(m, 12E4);
        return x(a || window.event)
    }
    function l() {
        k();
        m()
    }
    function m() {
        B.layer ? g(h) : h()
    }
    function h() {
        B.layer = D.layers.create(B.trafficURL);
        a.mapObject().route.traffic = !0;
        B.isactive = !0;
        n();
        B.layer.element.appendChild(v)
    }
    function j(a) {
        if (a.target.src) {
            var b = a.target.src.replace("png", "trfinfo").replace("traffic-sem", "traffic").replace(/h[0][0-9]\.tiles./, "jsapi.murzina.jsdev."),
                c = PGmap.Base.tileImg();
            c.onload = function () {
                c.style.left = v.style.left = a.target.style.left;
                c.style.top = v.style.top = a.target.style.top;
                v.width = v.height = c.width;
                E.drawImage(c, 0, 0)
            };
            c.src = b
        }
    }
    function g(b) {
        a.mapObject().layers.remove(B.layer);
        a.mapObject().route.traffic = !1;
        B.layer = null;
        B.isactive = 0;
        n();
        b && b()
    }
    function n() {
        var b = a.mapObject().geometry.get({
            type: "route"
        });
        b.length && (b = b.split(";"), b = [new PGmap.Coord(b[0].split(",")[0], b[0].split(",")[1]), new PGmap.Coord(b[1].split(",")[0], b[1].split(",")[1])], a.mapObject().route.get({
            points: b
        }))
    }
    function k() {
        var b = a.getBbox();
        A.url = a.trafficInfoUrl + "?lon1=" + PGmap.Utils.fromMercX(b.lon1) + "&lat1=" + PGmap.Utils.fromMercY(b.lat1) + "&lon2=" + PGmap.Utils.fromMercX(b.lon2) + "&lat2=" + PGmap.Utils.fromMercY(b.lat2) + "&z=" + a.getZoom();
        A.callback = function (a) {
            a = JSON.parse(a);
            if (a.b) if (a.city != void 0) {
                var b = a.city.Mark,
                    d = Math.floor(a.updated_utc / 60);
                switch (b) {
                    case "0":
                    case "1":
                    case "2":
                    case "3":
                        q = "green";
                        break;
                    case "4":
                    case "5":
                        q = "yellow";
                        break;
                    default:
                        q = "red"
                }
                p = b == "1" ? b + " \u0431\u0430\u043b\u043b" : b > 4 ? b + " \u0431\u0430\u043b\u043b\u043e\u0432" : b + " \u0431\u0430\u043b\u043b\u0430";
                o = '<div style="display: block; text-align: left; margin: 6px 0 0 9px;"><strong>\u0433. ' + a.city.CITY_NAM + "</strong><br />";
                o += d ? d == 1 ? d + " \u043c\u0438\u043d\u0443\u0442\u0443 \u043d\u0430\u0437\u0430\u0434" : d < 5 ? d + " \u043c\u0438\u043d\u0443\u0442\u044b \u043d\u0430\u0437\u0430\u0434</span>" : d + " \u043c\u0438\u043d\u0443\u0442 \u043d\u0430\u0437\u0430\u0434</div>" : "\u0441\u0435\u0439\u0447\u0430\u0441";
                PGmap.Utils.removeClass(B.button.element, B.className);
                PGmap.Utils.addClass(B.button.element, q);
                B.className = q;
                c(p);
                M.innerHTML = o;
                M.style.display = "block"
            } else p = w, PGmap.Utils.addClass(B.button.element, "notrafinfo"), M.style.display = "none", c(w)
        };
        s.request(A)
    }
    var o, p, q, r, u, y = a.langObj.controlsTitle.traffic,
        x = PGmap.Events.killEvent,
        z = PGmap.Events.addHandler,
        t = PGmap.Events.removeHandler,
        w = "\u041f\u0440\u043e\u0431\u043a\u0438",
        B = this,
        D = a.mapObject(),
        A = {
            type: "GET",
            url: "",
            data: {}
        }, s = new PGmap.CrossDomainAjax(new HTMLCrossDomainAjax);
    this.trafficURL = ["http://h05.tiles.tmcrussia.com/traffic-sem/", "http://h06.tiles.tmcrussia.com/traffic-sem/", "http://h07.tiles.tmcrussia.com/traffic-sem/"];
    this.isactive = !1;
    this.layer = null;
    this.button = new PGmap.SimpleButton("traffic", y);
    PGmap.Utils.addClass(this.button.element,
        "invisible");
    c(w);
    var v = document.createElement("canvas"),
        E = v.getContext("2d");
    v.style.cursor = "pointer";
    v.style.position = "absolute";
    var M = document.createElement("li");
    PGmap.Utils.addClass(M, "PGmap-traffic-info");
    this.button.element.appendChild(M);
    var J = PGmap.Base.createAbs("PGmap-tooltip", "");
    a.mapDOM.appendChild(J);
    this.disconnect = function () {
        g();
        a.mapObject().event.remove(PGmap.Events.MAP_MOVED, l);
        a.mapObject().event.remove(PGmap.Events.ZOOMING_END, l);
        PGmap.Utils.removeClass(B.button.element, B.className);
        B.className = "invisible";
        PGmap.Utils.addClass(B.button.element, B.className);
        c(w);
        M.style.display = "none";
        this.button.setActive(0);
        t(window, "blur", e);
        t(window, "focus", f);
        t(a.mapDOM, "mouseover", j);
        t(v, "mousemove", d);
        t(v, "mouseout", b);
        window.clearInterval(r);
        window.clearInterval(u)
    };
    this.execute = function () {
        B.isactive ? B.disconnect() : (m(), k(), r = window.setInterval(k, 6E4), u = window.setInterval(m, 12E4), a.mapObject().event.user(PGmap.Events.MAP_MOVED, l), a.mapObject().event.user(PGmap.Events.ZOOMING_END, l), z(window,
            "blur", e), z(window, "focus", f), z(a.mapDOM, "mouseover", j), z(v, "mousemove", d), z(v, "mouseout", b))
    };
    this.className = "invisible"
};
PGmap.Control.Traffic.prototype = new PGmap.Control;
PGmap.Control.Navigate = function (a) {
    var b = PGmap.Events.addHandler,
        c = a.mapObject().controls.navigateContainer;
    this.buttonLeft = new PGmap.SimpleButton("navigate-left", a.langObj.controlsTitle.navigate.left);
    this.buttonRight = new PGmap.SimpleButton("navigate-right", a.langObj.controlsTitle.navigate.right);
    this.buttonTop = new PGmap.SimpleButton("navigate-top", a.langObj.controlsTitle.navigate.top);
    this.buttonBottom = new PGmap.SimpleButton("navigate-bottom", a.langObj.controlsTitle.navigate.bottom);
    this.buttonTop.element.style.left = this.buttonBottom.element.style.left = "32px";
    this.buttonBottom.element.style.top = "62px";
    this.buttonLeft.element.style.top = this.buttonRight.element.style.top = "32px";
    this.buttonRight.element.style.left = "62px";
    c.appendChild(this.buttonLeft.element);
    c.appendChild(this.buttonRight.element);
    c.appendChild(this.buttonTop.element);
    c.appendChild(this.buttonBottom.element);
    b(this.buttonLeft.element, "mousedown", function () {
        var b = a.getCoords();
        b.lon -= 1E4;
        a.mapObject().setCenterFast(b, b.zoom)
    });
    b(this.buttonRight.element,
        "mousedown", function () {
        var b = a.getCoords();
        b.lon += 1E4;
        a.mapObject().setCenterFast(b, b.zoom)
    });
    b(this.buttonTop.element, "mousedown", function () {
        var b = a.getCoords();
        b.lat += 1E4;
        a.mapObject().setCenterFast(b, b.zoom)
    });
    b(this.buttonBottom.element, "mousedown", function () {
        var b = a.getCoords();
        b.lat -= 1E4;
        a.mapObject().setCenterFast(b, b.zoom)
    })
};
PGmap.Control.Slider.prototype = new PGmap.Control;
PGmap.Controls = function (a) {
    var b = a.mainElement();
    if (this.instance != void 0) return this.instance;
    this.instance = this;
    this.globals = a;
    this.geometry = a.mapObject().geometry;
    this.scaleContainer = PGmap.Base.create("PGmap-bottomcenter-container", "position:relative;z-index:80;top:100%;height:40px;width:150px;margin: 0 auto;text-align:center;line-height:1em;line-height: 14px;");
    this.infoContainer = PGmap.Base.create("", "position:absolute;bottom:40px;height:40px;width:150px; left: 0;");
    this.scaleContainer.appendChild(this.infoContainer);
    this.sliderContainer = PGmap.Base.create("PGmap-controls-container");
    this.geometryContainer = this.sliderContainer.cloneNode(!1);
    this.trafficContainer = this.geometryContainer.cloneNode(!1);
    this.serviceContainer = this.geometryContainer.cloneNode(!1);
    this.navigateContainer = this.sliderContainer.cloneNode(!1);
    PGmap.Utils.addClass(this.sliderContainer, "PGmap-slider-container");
    PGmap.Utils.addClass(this.geometryContainer, "PGmap-geometry-container");
    PGmap.Utils.addClass(this.trafficContainer, "PGmap-traffic-container");
    PGmap.Utils.addClass(this.serviceContainer, "PGmap-service-container");
    PGmap.Utils.addClass(this.navigateContainer, "PGmap-navigate-container");
    var c = PGmap.Base.create("PGmap-copyright", "position:absolute;z-index:80;bottom:7px;right:7px;line-height: 14px;"),
        d = document.createElement("span");
    d.style.cssText = "display: block; font-size: 7pt; margin: 0; text-align: right;";
    d.innerHTML = a.langObj.controlsTitle.copyright;
    c.appendChild(d);
    b.appendChild(c);
    b.appendChild(this.scaleContainer);
    b.appendChild(this.geometryContainer);
    b.appendChild(this.sliderContainer);
    b.appendChild(this.trafficContainer);
    b.appendChild(this.serviceContainer);
    b.appendChild(this.navigateContainer)
};
PGmap.Controls.prototype.addControl = function (a) {
    function b(a) {
        PGmap.Events.addHandler(a.button.element, "mousedown", function () {
            c.activeControl ? c.activeControl == a ? (a.disconnect(), c.activeControl = null) : (c.activeControl.commute(), a.execute(), c.activeControl = a) : (a.execute(), c.activeControl = a)
        })
    }
    var c = this;
    if (typeof a == "string") switch (a) {
        case "slider":
            this.slider = new PGmap.Control.Slider(this.globals, this.geometry);
            this.sliderContainer.appendChild(this.slider.minus.element);
            this.sliderContainer.appendChild(this.slider.plus.element);
            break;
        case "ruler":
            this.ruler = new PGmap.Ruler(this.globals, this.geometry);
            this.serviceContainer.appendChild(this.ruler.button.element);
            b(this.ruler);
            break;
        case "route":
            this.route = this.globals.mapObject().route;
            this.serviceContainer.appendChild(this.route.button.element);
            b(this.route);
            break;
        case "point":
            this.point = new PGmap.Control.Placemark(this.globals, this.geometry);
            this.geometryContainer.appendChild(this.point.button.element);
            b(this.point);
            break;
        case "polyline":
            this.polyline = new PGmap.Control.Polyline(this.globals,
            this.geometry);
            this.geometryContainer.appendChild(this.polyline.button.element);
            b(this.polyline);
            break;
        case "polygon":
            this.polygon = new PGmap.Control.Polygon(this.globals, this.geometry);
            this.geometryContainer.appendChild(this.polygon.button.element);
            b(this.polygon);
            break;
        case "traffic":
            this.traffic = new PGmap.Control.Traffic(this.globals, this.geometry);
            this.trafficContainer.appendChild(this.traffic.button.element);
            PGmap.Events.addHandler(this.traffic.button.element, "mouseup", this.traffic.execute);
            break;
        case "scale":
            this.scale = new PGmap.ScaleControl(this.globals);
            this.infoContainer.appendChild(this.scale.element);
            break;
        case "coords":
            a = this.globals.getCoords();
            this.coords = new PGmap.CoordsControl(this.globals);
            this.coords.set(a.lon, a.lat);
            this.infoContainer.appendChild(this.coords.element);
            break;
        case "navigate":
            this.navigateContainer.style.display = "block", this.navigate = new PGmap.Control.Navigate(this.globals, this.geometry)
    } else this[a.name] = a
};
PGmap.Controls.prototype.removeControl = function (a) {
    if (!this[a]) return !1;
    this[a].disconnect();
    PGmap.Events.removeHandler(a.button.element, "mousedown", this[a].execute)
};
PGmap.Controls.prototype.showControl = function (a) {
    if (!this[a]) return !1;
    this[a].button.element.style.display = "block"
};
PGmap.Controls.prototype.hideControl = function (a) {
    if (!this[a]) return !1;
    this[a].disconnect();
    this[a].button.element.style.display = "none"
};
PGmap.Control.Polyline = function (a, b) {
    function c(b) {
        var c = z.getOffset(a.mainElement()),
            b = u(b);
        if (o && (o = clearTimeout(o) || 0, k && k.x == (b.touches && b.touches[0].pageX || b.pageX) - c.left && k.y == (b.touches && b.touches[0].pageY || b.pageY) - c.top)) return k = null;
        k = {
            x: (b.touches && b.touches[0].pageX || b.pageX) - c.left,
            y: (b.touches && b.touches[0].pageY || b.pageY) - c.top
        }
    }
    function d(c) {
        var d = z.getOffset(a.mainElement());
        o && clearTimeout(o);
        c = u(c);
        if (c.touches || k && !(c.pageX - d.left != k.x || c.pageY - d.top != k.y)) o = setTimeout(function () {
            o = 0;
            a: {
                var c = a.getPosition(),
                    c = a.xyToLonLat(k.x - c.left, k.y - c.top);
                if (PGmap.EditBox && PGmap.EditBox.opened) t.disconnect();
                else {
                    if (!p && !n) p = new PGmap.Polyline({
                        points: [new PGmap.Coord(c.lon, c.lat), new PGmap.Coord(c.lon, c.lat)],
                        style: B
                    }), b.add(p), p.showPoint(0), m();
                    else {
                        if (!n) {
                            var d = [new PGmap.Coord(p.points[0].coord.lon, p.points[0].coord.lat), new PGmap.Coord(c.lon, c.lat)];
                            n = new PGmap.Polyline({
                                points: d,
                                editable: 1
                            });
                            b.add(n);
                            n.editableOn();
                            q = d[1];
                            e(d[0]);
                            e(d[1]);
                            g([new PGmap.Coord(c.lon, c.lat), new PGmap.Coord(c.lon,
                            c.lat)]);
                            x(d[1].element, "click", j);
                            break a
                        }
                        D(n.points[n.points.length - 1].element, "click", j);
                        q = new PGmap.Point({
                            coord: new PGmap.Coord(c.lon, c.lat),
                            draggable: 1,
                            width: 17,
                            height: 18,
                            url: "http://js.tmcrussia.com/img/poligon_point.png",
                            backpos: "0 0"
                        });
                        n.points.push(q);
                        n.showPoint(n.points.length - 1).update();
                        e(q);
                        x(q.element, "click", j);
                        g([new PGmap.Coord(c.lon, c.lat), new PGmap.Coord(c.lon, c.lat)])
                    }
                    k = null
                }
            }
        }, 200)
    }
    function e(a) {
        D(a.element, "dblclick", h);
        x(a.element, "dblclick", function (b) {
            if (n) for (var c = 0, d = n.points.length; c < d; c++) n.points[c] === a && n.points.splice(c, 1);
            n.parent && n.parent.element === this.parentNode && n.parent.element.removeChild(this);
            n.update();
            return y(b || window.event)
        })
    }
    function f() {
        x(q.element, "dblclick", h);
        q.draggable.callback("dragEnd", function (b) {
            b = a.xyToLonLat(b.x, b.y);
            p.points[0].lon = b.lon;
            p.points[0].lat = b.lat;
            p.update()
        })
    }
    function l(b) {
        if (p) {
            var b = u(b),
                c = a.getPosition(),
                d = PGmap.Utils.getOffset(a.mapDOM),
                b = a.xyToLonLat((b.touches && b.touches[0].pageX || b.pageX) - d.left - c.left, (b.touches && b.touches[0].pageY || b.pageY) - d.top - c.top);
            p.points[1].lon = b.lon;
            p.points[1].lat = b.lat;
            p.update()
        }
    }
    function m() {
        x(p.points[0].element, "dblclick", function (a) {
            b.remove(p);
            p = null;
            return y(a || window.event)
        })
    }
    function h(a) {
        var c = n.points.length - 1;
        if (c == 0) return p.points[0] = n.points[0], b.remove(n), p.showPoint(0), m(), n = null, y(a || window.event);
        (q = n.points[c]) && x(q.element, "dblclick", h);
        g([new PGmap.Coord(q.coord.lon, q.coord.lat), new PGmap.Coord(q.coord.lon, q.coord.lat)]);
        q && f();
        return y(a || window.event)
    }
    function j(a) {
        t.disconnect();
        return y(a || window.event)
    }
    function g(a) {
        b.remove(p);
        p = new PGmap.Polyline({
            points: a,
            style: B
        });
        b.add(p)
    }
    var n, k, o, p, q, r = a.langObj.controlsTitle.polyline,
        u = PGmap.Events.fixEvent,
        y = PGmap.Events.killEvent,
        x = PGmap.Events.addHandler,
        z = PGmap.Utils,
        t = this,
        w = a.mapElement(),
        B = {
            dashArray: "10, 5",
            color: "#017E8D",
            lineHeight: 3
        }, D = PGmap.Events.removeHandler,
        A = PGmap.EventFactory.eventsType;
    this.button = new PGmap.SimpleButton("polyline", r);
    PGmap.EditBox || PGmap.Utils.addLib("js", "http://js.tmcrussia.com/modules/EditBox.js",

    function () {
        w.appendChild(PGmap.EditBox.element)
    });
    this.execute = function () {
        if (!this.isactive) this.enable && this.enable(), x(w, A.mousedown, c), x(w, A.mouseup, d), x(w, A.mousemove, l), this.isactive = !0
    };
    this.commute = this.disconnect = function () {
        if (this.isactive) this.disable(), D(w, A.mousedown, c), D(w, A.mouseup, d), D(w, A.mousemove, l), p && b.remove(p), n && n.editableOff(), p = n = null, this.isactive = !1
    }
};
PGmap.Control.Polyline.prototype = new PGmap.Control;
PGmap.Control.Polygon = function (a, b) {
    function c(b) {
        var b = y(b),
            c = B.getOffset(a.mainElement());
        if (q && (q = clearTimeout(q) || 0, r && r.x == (b.touches && b.touches[0].pageX || b.pageX) - c.left && r.y == (b.touches && b.touches[0].pageY || b.pageY) - c.top)) return r = null;
        r = {
            x: (b.touches && b.touches[0].pageX || b.pageX) - c.left,
            y: (b.touches && b.touches[0].pageY || b.pageY) - c.top
        }
    }
    function d(c) {
        q && clearTimeout(q);
        var c = y(c),
            d = B.getOffset(a.mainElement());
        if (c.touches || r && !(c.pageX - d.left != r.x || c.pageY - d.top != r.y)) q = setTimeout(function () {
            q = 0;
            a: {
                var c = a.getPosition(),
                    c = a.xyToLonLat(r.x - c.left, r.y - c.top);
                if (PGmap.EditBox && PGmap.EditBox.opened) D.disconnect();
                else {
                    if (!o && !n) o = new PGmap.Polyline({
                        points: [new PGmap.Coord(c.lon, c.lat), new PGmap.Coord(c.lon, c.lat)],
                        style: A
                    }), b.add(o), o.showPoint(0), h();
                    else {
                        if (!n) {
                            var d = [new PGmap.Coord(o.points[0].coord.lon, o.points[0].coord.lat), new PGmap.Coord(c.lon, c.lat)];
                            n = new PGmap.Polyline({
                                points: d,
                                editable: 1
                            });
                            n.graphic.attr({
                                fill: "#017E8D",
                                "fill-opacity": "0.6"
                            });
                            b.add(n);
                            n.editableOn();
                            p = d[1];
                            e(d[0]);
                            e(d[1]);
                            f();
                            g(d[0]);
                            b.remove(o);
                            o = new PGmap.Polyline({
                                points: [new PGmap.Coord(o.points[0].coord.lon, o.points[0].coord.lat), new PGmap.Coord(c.lon, c.lat), new PGmap.Coord(c.lon, c.lat)],
                                style: A
                            });
                            b.add(o);
                            break a
                        }
                        l(n.points[n.points.length - 1]);
                        p = new PGmap.Point({
                            coord: new PGmap.Coord(c.lon, c.lat),
                            draggable: 1,
                            width: 17,
                            height: 18,
                            url: "http://js.tmcrussia.com/img/poligon_point.png",
                            backpos: "0 0"
                        });
                        n.points.push(p);
                        n.showPoint(n.points.length - 1);
                        n.update();
                        e(p);
                        f();
                        b.remove(o);
                        o = new PGmap.Polyline({
                            points: [new PGmap.Coord(n.points[0].coord.lon,
                            n.points[0].coord.lat), new PGmap.Coord(c.lon, c.lat), new PGmap.Coord(c.lon, c.lat)],
                            style: A
                        });
                        b.add(o)
                    }
                    r = null
                }
            }
        }, 200)
    }
    function e(a) {
        t(a.element, "dblclick", j);
        z(a.element, "dblclick", function (b) {
            if (n) for (var c = 0, d = n.points.length; c < d; c++) n.points[c] === a && n.points.splice(c, 1);
            n.parent && n.parent.element === this.parentNode && n.parent.element.removeChild(this);
            n.update();
            return x(b || window.event)
        })
    }
    function f() {
        z(p.element, "dblclick", j);
        p.draggable.callback("dragEnd", function (b) {
            b = a.xyToLonLat(b.x, b.y);
            o.points[2].lon = b.lon;
            o.points[2].lat = b.lat;
            o.update()
        })
    }
    function l(a) {
        t(a.element, "dblclick", j);
        a.draggable.callback("dragEnd", function () {})
    }
    function m(b) {
        if (o) {
            var c = a.getPosition(),
                b = y(b),
                d = PGmap.Utils.getOffset(a.mapDOM),
                c = a.xyToLonLat((b.touches && b.touches[0].pageX || b.pageX) - d.left - c.left, (b.touches && b.touches[0].pageY || b.pageY) - d.top - c.top);
            o.points[1].lon = c.lon;
            o.points[1].lat = c.lat;
            o.update()
        }
    }
    function h() {
        z(o.points[0].element, "dblclick", function (a) {
            b.remove(o);
            o = null;
            return x(a || window.event)
        })
    }
    function j(a) {
        var c = n.points.length - 1;
        if (!c) return o.points[0] = n.points[0], b.remove(n), o.showPoint(0), h(), n = null, x(a || window.event);
        p = n.points[c];
        z(p.element, "dblclick", j);
        b.remove(o);
        o = new PGmap.Polyline({
            points: [new PGmap.Coord(n.points[0].coord.lon, n.points[0].coord.lat), new PGmap.Coord(p.coord.lon, p.coord.lat), new PGmap.Coord(p.coord.lon, p.coord.lat)],
            style: A
        });
        b.add(o);
        p && f();
        g(n.points[0]);
        return x(a || window.event)
    }
    function g(a) {
        z(a.element, "click", function (a) {
            if (n && n.pointslength < 3) return x(a || window.event);
            for (var c = [], d = 0, e = n.points.length; d < e; d++) c.push(n.points[d].coord);
            b.remove(n);
            b.remove(o);
            o = n = null;
            k = new PGmap.Polygon({
                points: c,
                editable: 1,
                style: s
            });
            b.add(k);
            D.disconnect();
            return x(a || window.event)
        })
    }
    var n, k, o, p, q, r, u = a.langObj.controlsTitle.polygon,
        y = PGmap.Events.fixEvent,
        x = PGmap.Events.killEvent,
        z = PGmap.Events.addHandler,
        t = PGmap.Events.removeHandler,
        w = a.mapElement(),
        B = PGmap.Utils,
        D = this,
        A = {
            dashArray: "10, 5",
            color: "#017E8D",
            lineHeight: 3
        }, s = {
            color: "#017E8D",
            lineHeight: 5,
            backgroundColor: "#017E8D",
            lineOpacity: 1,
            backgroundOpacity: "0.6"
        }, v = PGmap.EventFactory.eventsType;
    this.button = new PGmap.SimpleButton("polygon", u);
    PGmap.EditBox || PGmap.Utils.addLib("js", "http://js.tmcrussia.com/modules/EditBox.js", function () {
        w.appendChild(PGmap.EditBox.element)
    });
    this.execute = function () {
        if (!this.isactive) this.enable && this.enable(), z(w, v.mousedown, c), z(w, v.mouseup, d), z(w, v.mousemove, m), this.isactive = !0
    };
    this.commute = this.disconnect = function () {
        if (this.isactive) {
            this.disable();
            t(w, v.mousedown, c);
            t(w, v.mouseup, d);
            t(w, v.mousemove,
            m);
            if (!k && n) {
                if (n.points.length < 3) return;
                for (var a = [], e = 0, f = n.points.length; e < f; e++) a.push(n.points[e].coord);
                b.remove(n);
                k = new PGmap.Polygon({
                    points: a,
                    editable: 1,
                    style: s
                });
                b.add(k)
            }
            o && b.remove(o);
            n && n.editableOff();
            o = n = k = null;
            this.isactive = !1
        }
    }
};
PGmap.Control.Polygon.prototype = new PGmap.Control;
PGmap.Control.Placemark = function (a, b) {
    function c() {
        for (var a = 0, c = p.length; a < c; a++) p[a] && (p[a] = b.remove(p[a]) || null);
        p = []
    }
    function d(b) {
        var b = j(b),
            c = k.getOffset(a.mainElement());
        if (h && (h = clearTimeout(h) || 0, m && m.x == (b.touches && b.touches[0].pageX || b.pageX) - c.left && m.y == (b.touches && b.touches[0].pageY || b.pageY) - c.top)) return m = null;
        m = {
            x: (b.touches && b.touches[0].pageX || b.pageX) - c.left,
            y: (b.touches && b.touches[0].pageY || b.pageY) - c.top
        }
    }
    function e(b) {
        h && clearTimeout(h);
        var b = j(b),
            c = k.getOffset(a.mainElement());
        if (b.touches || m && !(b.pageX - c.left != m.x || b.pageY - c.top != m.y)) h = setTimeout(function () {
            h = 0;
            f()
        }, 200)
    }
    function f() {
        var c = a.getPosition(),
            c = a.xyToLonLat(m.x - c.left, m.y - c.top),
            d;
        p.push(new PGmap.Point({
            coord: new PGmap.Coord(c.lon, c.lat),
            draggable: 1
        }));
        d = p.length - 1;
        b.add(p[d]);
        p[d].addContent('<div style="margin: 7px 0 0 7px;">\u0422\u043e\u0447\u043a\u0430</div>');
        n(p[d].element, "dblclick", function (a) {
            b.remove(p[d]);
            p[d] = null;
            return g(a || window.event)
        });
        m = null;
        return !1
    }
    var l, m, h, j = PGmap.Events.fixEvent,
        g = PGmap.Events.killEvent,
        n = PGmap.Events.addHandler,
        k = PGmap.Utils,
        o = PGmap.Events.removeHandler,
        p = [],
        q = PGmap.EventFactory.eventsType;
    this.button = new PGmap.SimpleButton("point", a.langObj.controlsTitle.point);
    this.execute = function () {
        if (!l) this.enable && this.enable(), l = a.mapElement(), n(l, "dblclick", c), n(l, q.mousedown, d), n(l, q.mouseup, e), this.isactive = !0
    };
    this.commute = this.disconnect = function () {
        if (l) this.disable(), o(l, q.mousedown, d), o(l, q.mouseup, e), l = null, this.isactive = !1
    };
    this.removeAll = c
};
PGmap.Control.Placemark.prototype = new PGmap.Control;
var FlashDetect = new function () {
        var a = this;
        a.installed = !1;
        a.raw = "";
        a.major = -1;
        a.minor = -1;
        a.revision = -1;
        a.revisionStr = "";
        var b = [{
            name: "ShockwaveFlash.ShockwaveFlash.7",
            version: function (a) {
                return c(a)
            }
        }, {
            name: "ShockwaveFlash.ShockwaveFlash.6",
            version: function (a) {
                var b = "6,0,21";
                try {
                    a.AllowScriptAccess = "always", b = c(a)
                } catch (f) {}
                return b
            }
        }, {
            name: "ShockwaveFlash.ShockwaveFlash",
            version: function (a) {
                return c(a)
            }
        }],
            c = function (a) {
                var b = -1;
                try {
                    b = a.GetVariable("$version")
                } catch (c) {}
                return b
            };
        a.majorAtLeast = function (b) {
            return a.major >= b
        };
        a.minorAtLeast = function (b) {
            return a.minor >= b
        };
        a.revisionAtLeast = function (b) {
            return a.revision >= b
        };
        a.versionAtLeast = function (b) {
            var c = [a.major, a.minor, a.revision],
                f = Math.min(c.length, arguments.length);
            for (i = 0; i < f; i++) if (c[i] >= arguments[i]) {
                if (!(i + 1 < f && c[i] == arguments[i])) return !0
            } else return !1
        };
        a.FlashDetect = function () {
            var c, e, f, l, m;
            if (navigator.plugins && navigator.plugins.length > 0) {
                var h = navigator.mimeTypes;
                if (h && h["application/x-shockwave-flash"] && h["application/x-shockwave-flash"].enabledPlugin && h["application/x-shockwave-flash"].enabledPlugin.description) {
                    c = h = h["application/x-shockwave-flash"].enabledPlugin.description;
                    var h = c.split(/ +/),
                        j = h[2].split(/\./),
                        h = h[3];
                    e = parseInt(j[0], 10);
                    f = parseInt(j[1], 10);
                    l = h;
                    m = parseInt(h.replace(/[a-zA-Z]/g, ""), 10) || a.revision;
                    a.raw = c;
                    a.major = e;
                    a.minor = f;
                    a.revisionStr = l;
                    a.revision = m;
                    a.installed = !0
                }
            } else if (navigator.appVersion.indexOf("Mac") == -1 && window.execScript) {
                h = -1;
                for (j = 0; j < b.length && h == -1; j++) {
                    c = -1;
                    try {
                        c = new ActiveXObject(b[j].name)
                    } catch (g) {
                        c = {
                            activeXError: !0
                        }
                    }
                    if (!c.activeXError && (a.installed = !0, h = b[j].version(c), h != -1)) c = h, l = c.split(","), e = parseInt(l[0].split(" ")[1], 10), f = parseInt(l[1], 10), m = parseInt(l[2], 10), l = l[2], a.raw = c, a.major = e, a.minor = f, a.revision = m, a.revisionStr = l
                }
            }
        }()
    };
FlashDetect.JS_RELEASE = "1.0.4";