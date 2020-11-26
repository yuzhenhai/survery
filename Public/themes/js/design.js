var type_radio_down="请选择";
var type_order="排序题";//排序题，请依次点击选项进行排序，請依次點擊選項進行排序
var type_order_limit_begin="请选择";
var type_order_limit_end="并排序";
var type_check="多选题";//多选题
var type_check_limit1="请选择";//多选题限制选项1请选择
var type_check_limit2="可以选择";//多选题限制选项2可以选择
var type_check_limit3="<b>最少</b>选择";//多选题限制选项3最少选择
var type_check_limit4="<b>最多</b>选择";//多选题限制选项4最多选择
var type_check_limit5="项";//多选题限制选项5项
var type_order_all="全部选";
var defaultOtherText="";
var type_wd_limit="字以内";
var type_wd_minlimit="最少";
var type_wd_minlimitDigit="最小值";
var type_wd_maxlimitDigit="最大值";
var type_wd_digitfrom="从";
var type_wd_words="字";
var type_wd_to="到";
var type_radio_clear="清除选择";
var validate_info_submit1="请输入验证码";
var slider_hint="拖动或点击滑动条";
var slider_value="当前滑动值为：";
var sum_hint="可以拖动滑动条也可以通过文本框直接输入";

var totalHideQcount = 0;
var referRelHT = new Object();
var url = "http://localhost/survey/Public/themes";


$ = function (element) {
	return (typeof (element) == 'object' ? element : document.getElementById(element));
};
$ce = function(c, d, a) {
    var b = document.createElement(c);
    if (d) {
        b.innerHTML = d;
    }
    a.appendChild(b);
    return b;
};
$$ = function(a, c) {
    if (c) {
        var b = c.getElementsByTagName(a);
        if (!b || b.length == 0) {
            b = new Array();
            getbyTagName(c, a, b);
            return b;
        }
        return b;
    } else {
        return document.getElementsByTagName(a);
    }
};
function StringBuilder() {
    this._stringArray = new Array();
}
StringBuilder.prototype.append = function(a) {
    this._stringArray.push(a);
};
StringBuilder.prototype.toString = function(a) {
    a = a || "";
    return this._stringArray.join(a);
};
StringBuilder.prototype.clear = function() {
    this._stringArray.length = 0;
};
function cancelInputClick(c) {
    var d = c.div_question_preview;
    var a = $$("input", d);
    for (var b = 0; b < a.length; b++) {
        a[b].onclick = function() {
            if (this.checked) {
                this.checked = false;
                return false;
            }
        };
        a[b].onkeydown = function() {
            this.value = "";
            return false;
        };
    }
}
function trim(a) {
    if (a && a.replace) {
        return a.replace(/(^\s*)|(\s*$)/g, "");
    } else {
        return a;
    }
}
function isEmpty(a) {
    return trim(a) == "";
}
function isInt(a) {
    var b = /^-?[0-9]+$/;
    return b.test(a);
}
function isPositive(a) {
    var b = /^\+?[1-9][0-9]*$/;
    return b.test(a);
}
function toInt(a) {
    return parseInt(trim(a));
}
function control_text(b) {
    var a = document.createElement("input");
    a.type = "text";
    a.style.width = b * 10 + "px";
    a.className = "choicetxt";
    return a;
}
function control_image(b) {
    var a = document.createElement("img");
    a.src = b;
    return a;
}
function control_check() {
    var a = document.createElement("input");
    a.type = "checkbox";
    a.tabIndex = "-1";
    return a;
}
function control_textarea(c, b) {
    var a = document.createElement("textarea");
    a.wrap = "soft";
    a.rows = c;
    a.style.width = b * 10 + "px";
    a.style.height = c * 22 + "px";
    a.className = "inputtext";
    return a;
}
function control_btn(b) {
    var a = document.createElement("input");
    a.type = "button";
    a.value = b;
    return a;
}
function control_radio(a) {
    if (navigator.appName.indexOf("Microsoft") != -1) {
        try {
            var c = document.createElement('<input type="radio" name="' + a + '" />');
            return c;
        } catch(b) {
            var c = document.createElement("input");
            c.type = "radio";
            c.name = a;
            return c;
        }
    } else {
        var c = document.createElement("input");
        c.type = "radio";
        c.name = a;
        return c;
    }
}

function getbyTagName(b, c, e) {
    var d;
    for (var a = 0; a < b.childNodes.length; a++) {
        d = b.childNodes[a];
        if (d.tagName === c) {
            e.push(d);
        }
        if (d.childNodes.length > 0 && d.nodeType == 1) {
            getbyTagName(d, c, e);
        }
    }
}


function set_data_fromServer(c) {
	var DataArray = new Array();

    var g = new Array();
    var f = c;
    g = f.split("¤");

    for (var d = 2; d < g.length; d++) {
        DataArray[d - 1] = set_string_to_dataNode(g[d]);
    }
	return DataArray;
}
function set_string_to_dataNode(r) {
	
    var f = new Object();
    var d = new Array();
    d = r.split("§");
    f._type = d[0];
    switch (d[0]) {
    case "fileupload":
        f._topic = d[1];
        var v = d[2].split("〒");
        f._title = v[0];
        f._keyword = v.length == 2 ? v[1] : "";
        f._relation = v[2] || "";
        if (d[4] == "true") {
            f._requir = true;
        } else {
            f._requir = false;
        }
        f._width = d[5] ? parseInt(d[5]) : 200;
        f._ext = d[6] || "";
        f._maxsize = d[7] ? parseInt(d[7]) : 4096;
        f._ins = d[8];
        if (d[9] == "true") {
            f._hasjump = true;
        } else {
            f._hasjump = false;
        }
        f._anytimejumpto = d[10];
        if (d[11]) {
            var t = d[11].split("〒");
            f._isCeShi = true;
            f._ceshiValue = t[0] || 5;
            f._ceshiDesc = t[1] || "";
        }
        break;
    case "slider":
        f._topic = d[1];
        var v = d[2].split("〒");
        f._title = v[0];
        f._keyword = v.length == 2 ? v[1] : "";
        f._relation = v[2] || "";
        if (d[4] == "true") {
            f._requir = true;
        } else {
            f._requir = false;
        }
        f._minvalue = d[5];
        f._maxvalue = d[6];
        f._minvaluetext = d[7];
        f._maxvaluetext = d[8];
        f._ins = d[9];
        if (d[10] == "true") {
            f._hasjump = true;
        } else {
            f._hasjump = false;
        }
        f._anytimejumpto = d[11];
        break;
    case "question":
        f._topic = d[1];
        var v = d[2].split("〒");
        f._title = v[0];
        f._keyword = v.length == 2 ? v[1] : "";
        f._relation = v[2] || "";
        f._height = d[4] ? parseInt(d[4]) : 1;
        f._maxword = d[5];
        if (d[6] == "true") {
            f._requir = true;
        } else {
            f._requir = false;
        }
        if (d[7] == "true") {
            f._norepeat = true;
        } else {
            f._norepeat = false;
        }
        f._default = d[8];
        f._ins = d[9];
        if (d[10] == "true") {
            f._hasjump = true;
        } else {
            f._hasjump = false;
        }
        f._anytimejumpto = d[11];
        f._verify = d[12];
        if (d[13]) {
            var l = d[13].split("〒");
            f._needOnly = l[0] == "true" ? true: false;
            f._needsms = l[1] == "true" ? true: false;
        }
        f._hasList = d[14] == "true" ? true: false;
        f._listId = d[15] ? parseInt(d[15]) : -1;
        f._width = d[16] ? parseInt(d[16]) : "";
        f._underline = d[17] == "true" ? true: false;
        f._minword = d[18] ? parseInt(d[18]) : "";

        break;
    case "gapfill":
        f._topic = d[1];
        var v = d[2].split("〒");
        f._title = v[0];
        f._keyword = v.length == 2 ? v[1] : "";
        f._relation = v[2] || "";
        if (d[4] == "true") {
            f._requir = true;
        } else {
            f._requir = false;
        }
        f._gapcount = d[5] ? parseInt(d[5]) : 1;
        f._ins = d[6];
        if (d[7] == "true") {
            f._hasjump = true;
        } else {
            f._hasjump = false;
        }
        f._anytimejumpto = d[8];
        var k = d[9] || "";
        f._rowVerify = new Array();

        break;
    case "sum":
        f._topic = d[1];
        var v = d[2].split("〒");
        f._title = v[0];
        f._keyword = v.length == 2 ? v[1] : "";
        f._relation = v[2] || "";
        if (d[4] == "true") {
            f._requir = true;
        } else {
            f._requir = false;
        }
        f._total = parseInt(d[5]);
        f._rowtitle = d[6];
        f._rowwidth = d[7].indexOf("%") > -1 ? d[7] : "";
        f._ins = d[9];
        if (d[10] == "true") {
            f._hasjump = true;
        } else {
            f._hasjump = false;
        }
        f._anytimejumpto = d[11];
        break;
    case "radio":
    case "check":
    case "radio_down":
    case "matrix":
    case "boolean":
        if (d[0] == "boolean") {
            f._isbool = true;
            f._type = "radio";
        } else {
            f._type = d[0];
        }
        f._topic = d[1];
        var v = d[2].split("〒");
        f._title = v[0];
        f._keyword = v.length == 2 ? v[1] : "";
        f._relation = v[2] || "";
        f._mainWidth = v[3] || "";
        f._tag = isInt(d[3]) ? toInt(d[3]) : 0;
        if (f._type == "matrix") {
            var n = d[4].split("〒");
            f._rowtitle = n[0];
            if (n.length >= 2) {
                f._rowtitle2 = n[1];
            } else {
                f._rowtitle2 = "";
            }
            if (n.length == 3) {
                f._columntitle = n[2];
            } else {
                f._columntitle = "";
            }
        } else {
            var x = d[4].split("〒");
            f._numperrow = isInt(x[0]) ? toInt(x[0]) : 1;
            f._randomChoice = false;
            if (x.length == 2) {
                f._randomChoice = x[1] == "true" ? true: false;
            }
        }
        if (d[5] == "true") {
            f._hasvalue = true;
        } else {
            f._hasvalue = false;
        }
        if (d[6] == "true") {
            f._hasjump = true;
        } else {
            f._hasjump = false;
        }
        f._anytimejumpto = d[7];
        if (d[0] == "check" || (d[0] == "matrix" && f._tag == "102")) {
            var i = d[8].split(",");
            if (i[0] == "true") {
                f._requir = true;
            } else {
                f._requir = false;
            }
            if (i[1] == "shop") {
                f._isShop = true;
            } else {
                f._lowLimit = i[1];
                f._upLimit = i[2];
            }
        } else {
            if (d[8] == "true") {
                f._requir = true;
            } else {
                if (d[0] == "radio") {
                    var i = d[8].split(",");
                    f._requir = i[0] == "true";
                    if (i[1] == "1") {
                        f._isQingJing = true;
                    } else {
                        if (i[1] == "2") {
                            f._ispanduan = true;
                        }
                    }
                } else {
                    f._requir = false;
                }
            }
        }
        if (f._type == "matrix") {
            var A = d[9].split("〒");
            var B = A[0].split(",");
            f._rowwidth = B[0].indexOf("%") > -1 ? B[0] : "";
            f._randomRow = B[1] == "true";
            f._rowwidth2 = "";
            if (A.length >= 2) {
                f._rowwidth2 = A[1].indexOf("%") > -1 ? A[1] : "";
            }
            f._minvalue = 0;
            f._maxvalue = 10;
            if (f._tag == "202" || f._tag == "301") {
                f._minvalue = A[2] || "";
                var p = A[3] || "";
                f._maxvalue = p;
                if (f._tag == "301") {
                    var s = p.split(",");
                    f._maxvalue = s[0] || "";
                    f._digitType = s[1] || 0;
                }
            } else {
                if (f._tag == "102" || f._tag == "103") {
                    f._daoZhi = A[2] == "true";
                } else {
                    if (f._tag == "201" || f._tag == "302") {
                        f._hasvalue = false;
                        var k = A[2] || "";
                        f._rowVerify = new Array();
                        if (k) {
                            var o = k.split(";");
                            for (var y = 0; y < o.length; y++) {
                                if (!o[y]) {
                                    continue;
                                }
                                var z = new Object();
                                var e = o[y].split("¦");
                                if (e[1] == "指定选项") {
                                    z._verify = e[1];
                                    z._choice = e[2] || "";
                                    z._isRequir = e[3] == "false" ? false: true;
                                    var c = parseInt(e[0]);
                                    f._rowVerify[c] = z;
                                } else {
                                    var q = o[y].split(",");
                                    z._verify = q[1];
                                    z._minword = q[2];
                                    z._maxword = q[3];
                                    z._width = q[4] || "";
                                    z._isRequir = q[5] == "false" ? false: true;
                                    z._needOnly = q[6] == "true";
                                    var c = parseInt(q[0]);
                                    f._rowVerify[c] = z;
                                }
                            }
                        }
                    }
                }
            }
            f._isTouPiao = false;
            f._isCeShi = false;
        } else {
            var g = d[9].split("〒");
            if (g[0] == "true") {
                f._isTouPiao = true;
                f._touPiaoWidth = isInt(g[1]) ? parseInt(g[1]) : 50;
                f._displayDesc = g[2] == "true";
                f._displayNum = g[3] == "true";
                f._displayPercent = g[4] == "true";
                f._displayThumb = g[5] == "true";
                f._displayDescTxt = g[6] || "";
            } else {
                if (g[0] == "ceshi") {
                    f._isCeShi = true;
                    hasCeShiQ = true;
                    f._ceshiValue = g[1] || 5;
                    f._ceshiDesc = g[2];
                } else {
                    if (g[0] == "ceping") {
                        f._isCePing = true;
                    } else {
                        if (g[0] == "desc") {
                            f._displayDesc = true;
                            f._displayDescTxt = g[1] || "";
                        }
                    }
                }
            }
        }
        f._ins = d[10];
        var a = d[11].split(",");
        f._verify = a[0];
        if (a[1] == "true") {
            f._nocolumn = true;
        }
        f._referTopic = d[12];
        f._referedTopics = d[13];
        f._select = new Array();
        var b = 14;
        for (var u = b; u < d.length; u++) {
            var w = new Array();
            w = d[u].split("〒");
            var m = u - b + 1;
            f._select[m] = new Object();
            f._select[m]._item_title = w[0];
            if (w[1] == "true") {
                f._select[m]._item_radio = true;
            } else {
                f._select[m]._item_radio = false;
            }
            f._select[m]._item_value = w[2];
            f._select[m]._item_jump = w[3];
            f._select[m]._item_tb = false;
            f._select[m]._item_tbr = false;
            f._select[m]._item_img = "";
            f._select[m]._item_imgtext = false;
            f._select[m]._item_desc = "";
            f._select[m]._item_label = "";
            if (w.length >= 9) {
                f._select[m]._item_tb = w[4] == "true";
                f._select[m]._item_tbr = w[5] == "true";
                f._select[m]._item_img = w[6];
                f._select[m]._item_imgtext = w[7] == "true";
                f._select[m]._item_desc = w[8];
                f._select[m]._item_label = w[9];
                f._select[m]._item_huchi = w[10] == "true";
            }
        }
        break;
    default:
        break;
    }
	//console.log(f);
    return f;
}

function create_question(g) {
    var ao = g._type;
    var x = g._verify;
    var B = g._height > 1;
    _likertMode = g._tag || 0;
    var L = false;
    var h = false;

    var H = document.createElement("div");
    H.className = "div_question";
    H.dataNode = g;
    H.tabIndex = -1;
    var R = $ce("div", "", H);
    R.className = "div_preview";
    H.div_question_preview = R;
    var Q = ao == "question";
    var N = ao == "slider";
    var aa = ao == "sum";
    var X = ao == "page";
    var K = ao == "cut";
    var b = ao == "check";
    var n = ao == "radio";
    var u = n && _likertMode;
    var r = n && _likertMode > 1;
    var i = ao == "radio_down";
    var e = ao == "matrix";
    var a = ao == "matrix" && _likertMode > 300;
    var M = b && _likertMode;
    var ah = ao == "fileupload";
    var m = n || i || b || e;
    var am = !K && !X;
    var T = ao == "gapfill";
    H.isMergeNewAdded = h;

    var l = document.createElement("div");
    if (am) {
        var U = g._topic;
        U = U - totalHideQcount;
        var ag = U + "";
        if (U - 100 < 0) {
            ag += ".";
        }

        var J = $ce("div", ag, l);
        H.divTopic = J;
        J.className = "div_topic_question";
        if (g._topic - 100 >= 0) {
            J.style.fontSize = "14px";
        }
    }

    if (K) {
        l.className = "div_title_cut_question";
    }
    if (am) {
        l.className = "div_title_question_all";
    }
    if (g._isQingJing) {
        l.style.display = "none";
    }
    var p = $ce("div", "", l);
    var aj = g._title;
    if (T) {
        var t = "<div style='margin-top:8px;'></div>";
        aj = replaceGapFill(aj, g).replace(/\<br\s*\/\>/g, t);
    } else {
        if (K) {
            aj = aj || "请在此输入说明文字";
            if (aj.length <= 10) {
                aj = "<b>" + aj + "</b>";
            }
        }
    }
    var ad = $ce("span", aj, p);
    if (X) {
        p.className = "div_title_page_question";
        if (aj == "") {
            p.style.margin = "0 auto";
        } else {
            p.style.margin = "0 auto";
            p.style.marginBottom = "10px";
            p.style.borderBottom = "1px dashed #efefef";
        }
    } else {
        if (K && g._video) {
            var G = "<iframe height='498' width='510' src='" + g._video + "' frameborder=0 allowfullscreen></iframe>";
            H.div_video_title = $ce("div", G, p);
        } else {
            p.className = "div_title_question";
        }
    }
    H.get_div_title = function() {
        return ad;
    };

    if (am) {
        var an = $ce("span", "&nbsp;*", p);
        H.setreqstatus = function() {
            an.style.color = "red";
            an.style.display = this.dataNode._requir ? "": "none";
            if (T) {
                if (this.checkTitle) {
                    this.checkTitle();
                }
                if (!EndGapReq || this.dataNode._partRequir) {
                    an.style.display = "none";
                }
            } else {
                if (this.dataNode._requir && this.dataNode._type == "matrix" && this.dataNode._tag == "201" && this.dataNode._partRequir) {
                    an.style.display = "none";
                }
            }
            return an;
        };
        H.setreqstatus();
        H.updateTitle = function() {
            if (this.txttitle) {
                this.txttitle.value = this.dataNode._title;
            }
            this.get_div_title().innerHTML = this.dataNode._title;
        };
        if (Q) {
            var A = $ce("span", "", p);
            A.className = "font-a0";
            H.showMinMaxWord = function(ar, ap) {
                var av = this.dataNode;
                var aq = "";
                var at = type_wd_words;
                var au = type_wd_minlimit;
                var v = av._verify == "数字" || av._verify == "小数";
                if (v) {
                    at = "";
                    au = type_wd_minlimitDigit;
                    aq = type_wd_digitfrom;
                }
                if (!isEmpty(ar) && !isEmpty(ap)) {
                    A.innerHTML = "&nbsp;（" + aq + ap + type_wd_to + ar + at + "）";
                    A.style.display = "";
                    if (ap == ar && !v) {
                        A.innerHTML = "&nbsp;（" + ar + type_wd_words + "）";
                        if (av._verify == "学号") {
                            A.innerHTML = "&nbsp;（" + ar + "位数字）";
                        }
                    }
                } else {
                    if (!isEmpty(ar)) {
                        A.innerHTML = "&nbsp;（" + ar + type_wd_limit + "）";
                        if (v) {
                            A.innerHTML = "&nbsp;（" + type_wd_maxlimitDigit + ar + "）";
                        }
                        A.style.display = "";
                    } else {
                        if (!isEmpty(ap)) {
                            A.innerHTML = "&nbsp;（" + au + ap + at + "）";
                            A.style.display = "";
                        } else {
                            A.style.display = "none";
                        }
                    }
                }
            };
            H.showMinMaxWord(g._maxword, g._minword);
            H.get_span_maxword = function() {
                return A;
            };
        }
        if (g._isCeShi && (Q || n || b || ah)) {
            var D = $ce("span", "", p);
            D.style.color = "#efa030";
            if (Q) {
                H.setCeshiQTip = function() {
                    var v = "答案：" + g._answer;
                    if (g._answer == "简答题无答案") {
                        v = "无标准答案需人工评分";
                    }
                    D.innerHTML = "（" + v + "，分值：" + g._ceshiValue + "分）";
                };
                H.setCeshiQTip();
            } else {
                D.innerHTML = "（分值：" + g._ceshiValue + "分）";
            }
            H.spanCeShi = D;
        }
        if (g._isTouPiao) {
            var P = $ce("span", "", p);
            P.style.color = "#efa030";
            var V = "&nbsp;投票题";
            P.innerHTML = V;
        }
        if (b || (e && _likertMode == "102")) {
            var al = document.createElement("span");
            H.updateSpanCheck = function() {
                var au = this.dataNode;
                if (au._isShop) {
                    return;
                }
                au._lowLimit = au._lowLimit || "";
                au._upLimit = au._upLimit || "";
                var v = type_check;
                if (e) {
                    v = "多选题";
                }
                var at = "";
                var ar = false;
                if (M) {
                    at = "-1";
                }
                var ap = "";
                if (!au._isTouPiao) {
                    ap = "[";
                }
                if (au._lowLimit != at && au._upLimit != at) {
                    if (au._lowLimit == au._upLimit) {
                        al.innerHTML = "&nbsp;" + ap + type_check_limit1 + "<b>" + au._lowLimit + "</b>" + type_check_limit5;
                    } else {
                        al.innerHTML = "&nbsp;" + ap + type_check_limit1 + "<b>" + au._lowLimit + "</b>-<b>" + au._upLimit + "</b>" + type_check_limit5;
                    }
                    ar = true;
                } else {
                    if (au._lowLimit != at) {
                        var aq = type_check_limit5;
                        if (au._lowLimit == "1" && langVer == 1) {
                            aq = " item";
                        }
                        al.innerHTML = "&nbsp;" + ap + type_check_limit3 + "<b>" + au._lowLimit + "</b>" + aq;
                        ar = true;
                    } else {
                        if (au._upLimit != at) {
                            al.innerHTML = "&nbsp;" + ap + type_check_limit4 + "<b>" + au._upLimit + "</b>" + type_check_limit5;
                            ar = true;
                        } else {
                            al.innerHTML = "&nbsp;" + ap + v;
                        }
                    }
                }
                if (M) {
                    if (ar) {
                        if (au._lowLimit == "" || au._lowLimit == au._select.length - 1) {
                            al.innerHTML = "&nbsp;[" + type_check_limit1 + "<b>" + type_order_all + "</b>" + type_check_limit5;
                        }
                        al.innerHTML += type_order_limit_end;
                    } else {
                        al.innerHTML = "&nbsp;[" + type_order;
                    }
                }
                if (!au._isTouPiao) {
                    al.innerHTML += "]";
                }
                if (au._isCeShi) {
                    al.innerHTML = "[" + type_check + "]";
                }
                al.className = "qtypetip";
            };
            H.updateSpanCheck();
            p.appendChild(al);
        } else {
            if (a) {
                var al = $ce("span", "", p);
                H.updateSpanCheck = function() {
                    if (this.dataNode._tag == "301" && this.dataNode._minvalue !== "" && this.dataNode._maxvalue !== "") {
                        al.innerHTML = "&nbsp;[输入" + this.dataNode._minvalue + "到" + this.dataNode._maxvalue + "的数字]";
                        al.className = "qtypetip";
                    } else {
                        al.innerHTML = "";
                    }
                    al.style.display = this.dataNode._tag == "301" ? "": "none";
                };
                H.updateSpanCheck();
            }
        }
        if (e) {
            if (g._tag == "102" || g._tag == "103") {
                var o = $ce("span", "", p);
                H.updateSpanMatrix = function() {
                    if (g._daoZhi) {
                        var v = "竖向单选";
                        if (g._tag == "102") {
                            v = "竖向多选";
                        }
                        o.innerHTML = "&nbsp;[" + v + "]";
                        o.className = "qtypetip";
                    } else {
                        o.innerHTML = "";
                    }
                };
                H.updateSpanMatrix();
            }
        }
    }
    var ac = $ce("div", "", l);
    ac.style.clear = "both";
    R.appendChild(l);
    if (am) {
        var z = document.createElement("div");
        z.className = "div_table_radio_question";
        var q = $ce("div", "", z);
        q.className = "div_table_clear_top";
        R.appendChild(z);
        if (g._isQingJing) {
            z.style.paddingLeft = "0";
        }
    }
    if (Q) {
        var C = $ce("div", "", z);
        C.style.position = "relative";
        var E = control_textarea("1", "50");
        C.appendChild(E);
        var d = $ce("span", "", C);
        d.style.position = "absolute";
        d.style.left = "3px";
        d.style.top = "3px";
        E.style.overflow = "auto";
        $ce("div", "", z).className = "div_table_clear_bottom";
        E.value = g._default;

        H.showTextAreaUnder = function() {
            E.className = this.dataNode._underline ? "underline": "inputtext";
        };
        H.showTextAreaWidth = function() {
            if (isEmpty(this.dataNode._width)) {
                E.style.width = "62%";
            } else {
                E.style.width = this.dataNode._width + "px";
                E.style.display = this.dataNode._width == 1 ? "none": "";
            }
        };
        H.showTextAreaHeight = function() {
            E.style.height = this.dataNode._height * 22 + "px";
        };
        H.showTextAreaDate = function() {
            var v = this.dataNode._verify;
            var ap = this.dataNode._topic;
            E.onclick = null;
            var aq = "";
            d.innerHTML = "";

			E.style.width = "150px";
			E.style.height = "22px";

            
            if (aq) {
                d.innerHTML = "<img src='" + aq + "' alt=''/>";
            }
        };
        H.showTextAreaUnder();
        H.showTextAreaWidth();
        H.showTextAreaHeight();
        H.showTextAreaDate();

        H.get_textarea = function() {
            return E;
        };
    }
    if (ah) {
        var y = $ce("div", "", z);
        var k = $ce("div", "请选择上传文件", z);
        H.updateFileUpload = function() {
            var ap = g._maxsize;
            var au = "";
            if (ap % 1024 == 0) {
                au = "（不超过" + (ap / 1024) + "M）";
            } else {
                au = "（不超过" + ap + "KB）";
            }
            var av = getIEVersion();
            var ar = av && (!document.documentMode || document.documentMode < 8);
            var aq = av <= 7 || ar;
            var at = "position:relative;";
            if (aq) {
                at = "";
            }
            var v = '<div style="width: 200px;height: 30px; background:#fff;' + at + ' text-align: center; line-height: 30px; overflow: hidden;border:1px solid #d5d5d5;color:#333;margin-bottom:5px;">选择文件' + au;
            if (!aq) {
                v += '<input type="file" style="position: absolute;left: 0;top: 0;height: 30px; filter:alpha(opacity=0);opacity:0; background-color: transparent;width:200px; font-size:180px;"/>';
            }
            v += "</div>";
            y.innerHTML = v;
            if (g._ext) {
                k.innerHTML = "请选择上传文件，扩展名为" + g._ext;
            } else {
                k.innerHTML = "请选择上传文件，扩展名为" + defaultFileExt;
            }
        };
        H.updateFileUpload();
    }
    if (T) {}
    if (N) {
        var W = $ce("span", g._minvaluetext || "", z);
        W.className = "spanLeft";
        W.style.color = "red";
        H.get_span_min_value_text = function() {
            return W;
        };
        var f = $ce("span", "(" + (g._minvalue || 0) + ")", z);
        f.className = "spanLeft";
        f.style.color = "red";
        H.get_span_min_value = function() {
            return f;
        };
        var F = $ce("span", "(" + (g._maxvalue || 100) + ")", z);
        F.className = "spanRight";
        F.style.color = "red";
        H.get_span_max_value = function() {
            return F;
        };
        var I = $ce("span", g._maxvaluetext || "", z);
        I.className = "spanRight";
        I.style.color = "red";
        H.get_span_max_value_text = function() {
            return I;
        };
        $ce("div", "", z).className = "divclear";
        var O = control_image(url+"/img/slider1.jpg");
        O.style.width = "10px";
        var ai = control_image(url+"/img/sliderEnd.jpg");
        ai.style.width = "97%";
        ai.style.height = "23px";
        z.appendChild(O);
        z.appendChild(ai);
        $ce("div", "", z).className = "divclear";
        z.style.width = "60%";
        var E = control_textarea("1", "10");
        E.style.display = "none";
    }
    if (aa) {
        H.createSum = function() {
            var av = new StringBuilder();
            av.append("<div  class='div_table_clear_top'></div>");
            if (this._referDivQ) {
                av.append("此题行标题来源于第" + this._referDivQ.dataNode._topic + "题的选中项");
            } else {
                av.append("<table style='width:100%;' border='0px'  cellpadding='5' cellspacing='0'>");
                var aq = "";
                var v = "";
                av.append("<tbody>");
                var au = new Array();
                au = trim(g._rowtitle).split("\n");
                var at = "";
                for (var ap = 0; ap < au.length; ap++) {
                    if (ap == au.length - 1) {
                        aq = "";
                        v = "";
                    }
                    if (au[ap].length > 4 && au[ap].substring(0, 4) == "【标签】") {
                        var ar = au[ap].substring(4);
                        av.append("<tr><th align='left'><b style='color:#0066ff;'>" + ar + "</b></th><td></td></tr>");
                        at = "padding-left:10px;";
                        continue;
                    }
                    if (g._rowwidth == "") {
                        av.append("<tr><th align='left' style='" + v + at + "'>" + au[ap] + "</th>");
                    } else {
                        av.append("<tr><th align='left' style='width:" + g._rowwidth + ";" + v + at + "'>" + au[ap] + "</th>");
                    }
                    av.append("<td  " + aq + "align='left' width='36'><input  type='text' style='width:36px;'/></td>");
                    av.append("<td  " + aq + "align='left'><img src='"+url+"/img/slider1.jpg' style='width: 10px;'/><img src='"+url+"/img/sliderEnd.jpg' style='width:250px;height: 23px;'/></td>");
                    av.append("</tr>");
                }
                av.append("</tbody></table>");
            }
            av.append("<div style='margin-top:10px;'><span style='color:#666666;'>" + sum_hint + "</span></div>");
            z.innerHTML = av.toString("");
        };
        H.createSum();
    }
    if (m) {
        H.createTableRadio = function() {
            var aP = this.dataNode;
            var bg = aP._isTouPiao;
            var aF = aP._isCeShi;
            var aQ = aP._isCePing;
            var bQ = aP._numperrow ? aP._numperrow: 1;
            var bn = aP._select;
            var aU = aP._tag;
            var aM = aP._displayThumb;
            var bF = bg && aM && bn[1]._item_img;
            if (bF) {
                bQ = 4;
            }
            var by = new StringBuilder();
            by.append("<div  class='div_table_clear_top'></div>");
            var a0 = false;

            if (!a0) {
                if (i) {
                    by.append("<select><option>" + type_radio_down + "</option>");
                    for (var aY = 1; aY < bn.length; aY++) {
                        if (bn[aY]._item_radio) {
                            by.append("<option selected='selected'>" + trim(bn[aY]._item_title) + "</option>");
                        } else {
                            by.append("<option>" + trim(bn[aY]._item_title) + "</option>");
                        }
                    }
                    by.append("</select>");
                }
                if (n || (b && !M)) {
                    by.append("<ul>");
                    var aE;
                    var bm = "%";
                    if (u) {
                        aE = 40;
                        bm = "px";
                        bQ = 1;
                    } else {
                        aE = (100 / bQ) - 1;
                    }
                    var aK = false;
                    var bB = 1;
                    for (var aY = 1; aY < bn.length; aY++) {
                        if (ao == "radio" && aU >= 1 && aU != 101 && aY == 1) {
                            var bp = "5px";
                            by.append("<li class='notchoice' style='padding-right:15px;padding-top:" + bp + "'><b>" + bn[1]._item_title + "</b></li>");
                            if (aU == "6") {
                                by.append("<li><ul class='onscore'>");
                            }
                        }
                        if (n && aU > 1 && aU != 101) {
                            var bt = "style='padding-left:3px;'";
                            var aC = bn.length - 1;
                            var bG = "off";
                            var bC = "on";
                            if (aU == "6") {
                                var bh = parseInt(355 / aC) - 2;
                                if (aY == aC) {
                                    bh += 355 % aC;
                                }
                                if (aC >= 18) {
                                    var aI = 12;
                                    var bf = 9;
                                    if (aC == 21) {
                                        aI = 11;
                                        bf = 10;
                                    }
                                    var a6 = (aI + 2) * bf;
                                    var aL = aC - bf;
                                    var ap = 355 - a6;
                                    if (aC >= 18) {
                                        if (aY >= bf + 1) {
                                            bh = parseInt(ap / aL) - 2;
                                        } else {
                                            bh = aI;
                                        }
                                    }
                                    if (aY == aC) {
                                        bh += ap % aL;
                                    }
                                }
                                bt = "style='width:" + bh + "px' ";
                                bG = "off";
                                bC = "on";
                            }
                            if (aY == aC) {
                                by.append("<li " + bt + " class='" + bG + aP._tag + "'  >");
                            } else {
                                by.append("<li " + bt + " class='" + bC + aP._tag + "'  >");
                            }
                            if (aU == "6") {
                                var bw = bn[aY]._item_value;
                                if (bw == NoValueData) {
                                    bw = "&nbsp;";
                                }
                                by.append(bw);
                            }
                            by.append("</li>");
                        } else {
                            if (bn[aY]._item_label) {
                                if (aK) {
                                    by.append("</ul></li>");
                                }
                                by.append("<li style='width:100%;'><div><b>" + bn[aY]._item_label + "</b></div><ul>");
                                aK = true;
                                bB = 1;
                            }
                            if (aU == 101) {
                                aE = trim(bn[aY]._item_title).length * 16 + 28;
                            }
                            by.append("<li  style='width:" + aE + bm + ";");
                            if (bn[aY]._item_img) {
                                by.append("margin-bottom:15px;");
                            }
                            by.append("'>");
                            var v = false;
                            if ((ao == "radio" || ao == "check") && bn[aY]._item_radio) {
                                v = true;
                            }
                            if (!bn[aY]._item_img) {
                                if (bg) {
                                    by.append("<div style='float:left;width:" + aP._touPiaoWidth + "%;'>");
                                } else {
                                    if (aF && bn[aY]._item_radio) {
                                        by.append("<span style='color:#efa030;'>");
                                    }
                                }
                                var bI = "jqRadio";
                                if (!n) {
                                    bI = "jqCheckbox";
                                }
                                if (v) {
                                    bI += " jqChecked";
                                }
                                by.append("<a href='###' class='" + bI + "' style='position:static;'></a><input style='display:none;'");
                                if (n) {
                                    by.append(" type='radio'");
                                } else {
                                    by.append(" type='checkbox'");
                                }
                                if (v) {
                                    by.append(" checked='checked'");
                                }
                                if (ao == "radio" && aU == 1) {
                                    var aq = trim(bn[aY]._item_value);
                                    if (aq == "-77777") {
                                        aq = "";
                                    }
                                    by.append("/><label style='vertical-align:middle;padding-left:2px;'>" + aq + "</label>");
                                } else {
                                    by.append("/><label style='vertical-align:middle;padding-left:2px;'>" + trim(bn[aY]._item_title) + "</label>");
                                }
                                if (bn[aY]._item_tb) {
                                    by.append(" <input type='text' class='underline' style='color:#999999;max-width:500px;' value='" + defaultOtherText + "'/>");
                                }
                                if (bn[aY]._item_tbr) {
                                    by.append(" <span style='color: red;'> *</span>");
                                }
                                if (bg) {
                                    by.append("</div>");
                                    by.append("<div style='float:left;'>");
                                    addTouPiao(by, aP, aY);
                                    by.append("</div><div class='divclear'></div>");
                                } else {
                                    if (aF && bn[aY]._item_radio) {
                                        by.append("&nbsp;<label style='vertical-align:middle;'>(正确答案)</label></span>");
                                    } else {
                                        if (aQ) {
                                            var bO = bn[aY]._item_value;
                                            if (bO == NoValueData || bO == "") {
                                                bO = "N/A";
                                            }
                                            by.append("<span style='color:#efa030;font-size:14px;'>&nbsp;(分值：" + bO + ")</span>");
                                        }
                                    }
                                }
                                if (aP._hasjump && aP._anytimejumpto < 1) {
                                    var aw = "跳转到下一题";
                                    if (bn[aY]._item_jump == "1") {
                                        aw = "结束作答";
                                    } else {
                                        if (bn[aY]._item_jump == "-1") {
                                            aw = "点下一页时提交为无效答卷";
                                        } else {
                                            if (bn[aY]._item_jump - 1 > 0) {
                                                aw = "跳转到第" + bn[aY]._item_jump + "题";
                                            }
                                        }
                                    }
                                    by.append("<span style='color:#efa030;font-size:14px;'>&nbsp;(" + aw + ")</span>");
                                }
                                if (bn[aY]._item_desc) {

                                    by.append("<div class='div_item_desc'>" + bn[aY]._item_desc + "</div>");
                                    
                                }
                            } else {
                                var a4 = bn[aY]._item_img;
                                var bM = "";
                                var aD = "";
                                if (bF && a4.indexOf(".sojump.com") > -1) {
                                    if (a4.indexOf("pubali") > -1) {
                                        var aV = "?x-oss-process";
                                        var bi = a4.indexOf(aV);
                                        if (bi > -1) {
                                            a4 = a4.substring(0, bi);
                                        }
                                        a4 = a4 + aV + "=image/quality,q_90/resize,m_lfit,h_150,w_150";
                                    } else {
                                        var aV = "?imageMogr2";
                                        var bi = a4.indexOf(aV);
                                        if (bi > -1) {
                                            a4 = a4.substring(0, bi);
                                        }
                                        a4 = a4 + aV + "/thumbnail/150x150!";
                                    }
                                    bM = "width:152px;height:200px;";
                                    if (bn[aY]._item_tb) {
                                        bM = "width:152px;height:210px;";
                                    }
                                    aD = " style='height:150px;' ";
                                } else {
                                    bM = "margin-right:15px;";
                                }
                                by.append("<div style='text-align:center;padding:5px;border:1px solid #ddd;" + bM + "'>");
                                by.append("<table align='center' cellspacing='0' cellpadding='0'><tr><td>");
                                by.append("<div" + aD + "><img style='border:none;margin:0 auto;' src='" + a4 + "' alt='' /></div>");
                                by.append("</td></tr>");
                                if (bn[aY]._item_desc) {
                                    by.append("<tr><td>");
                          
									by.append("<div class='div_item_desc'");
									by.append(" style='text-align:left;");
									if (bF) {
										by.append("height:20px;width:150px;margin-left:0px;overflow:hidden;");
									}
									by.append("'");
									by.append(">");
									by.append(bn[aY]._item_desc);
									by.append("</div>");
                                    
                                    by.append("</td></tr>");
                                }
                                by.append("</table>");
                                by.append("<div style='margin-top:6px;'>");
                                if (bF) {
                                    var bI = "jqRadio";
                                    if (!n) {
                                        bI = "jqCheckbox";
                                    }
                                    if (v) {
                                        bI += " jqChecked";
                                    }
                                    by.append("<a href='###' class='" + bI + "' style='position:static;'></a><input style='display:none;'");
                                    if (n) {
                                        by.append(" type='radio'");
                                    } else {
                                        by.append(" type='checkbox'");
                                    }
                                    if (v) {
                                        by.append(" checked='checked'");
                                    }
                                    if (ao == "radio" && aU == 1) {
                                        by.append("'/><label style='vertical-align:middle;padding-left:2px;'>" + trim(bn[aY]._item_value) + "</label>");
                                    } else {
                                        by.append("'/>");
                                    }
                                }
                                if (!bF) {
                                    var bI = "jqRadio";
                                    if (!n) {
                                        bI = "jqCheckbox";
                                    }
                                    if (v) {
                                        bI += " jqChecked";
                                    }
                                    by.append("<a href='###' class='" + bI + "' style='position:static;'></a><input style='display:none;'");
                                    if (n) {
                                        by.append(" type='radio'");
                                    } else {
                                        by.append(" type='checkbox'");
                                    }
                                    if (v) {
                                        by.append(" checked='checked'");
                                    }
                                    if (ao == "radio" && aU == 1) {
                                        by.append("'/><label style='vertical-align:middle;padding-left:2px;'>" + trim(bn[aY]._item_value) + "</label>");
                                    } else {
                                        by.append("'/>");
                                    }
                                }
                                if (bF) {
                                    by.append("<label style='display:inline-block;padding:0;overflow: hidden;text-overflow: ellipsis;max-width: 125px;white-space: nowrap;vertical-align: middle;'>" + trim(bn[aY]._item_title) + "</label>");
                                } else {
                                    if (bn[aY]._item_imgtext) {
                                        by.append(trim(bn[aY]._item_title));
                                    } else {
                                        by.append("&nbsp;");
                                    }
                                }
                                if (bn[aY]._item_tb) {
                                    if (bF) {
                                        by.append("<br/>");
                                    }
                                    by.append(" <input type='text' class='inputtext' style='color:#999999;max-width:500px;' value='" + defaultOtherText + "'/>");
                                }
                                if (bn[aY]._item_tbr) {
                                    by.append("<span style='color: red;'> *</span>");
                                }
                                by.append("</div>");
                                by.append("</div>");
                                if (bg) {
                                    by.append("<div style='text-align:center;'>");
                                    addTouPiao(by, aP, aY);
                                    by.append("</div>");
                                }
                            }
                            by.append("</li>");
                        }
                        if (n && aU >= 1 && aU != 101 && aY == bn.length - 1) {
                            var bp = "5px";
                            if (aU == 6) {
                                by.append("</ul></li>");
                            }
                            by.append("<li  class='notchoice'  style='padding-left:15px;padding-top:" + bp + "'><b>" + bn[bn.length - 1]._item_title + "</b></li>");
                        }
                        if (bQ > 1 && bB % bQ == 0) {
                            by.append("<div style='clear:both;'></div></ul><ul>");
                        }
                        bB++;
                    }
                    by.append("<div style='clear:both;'></div></ul>");
                    if (aK) {
                        by.append("</li></ul>");
                    }
                    if (aF && aP._ceshiDesc) {
                        by.append("<div style='color:#666;'>答案解析：" + aP._ceshiDesc + "</div>");
                    }
                }
                if (M) {
                    by.append("<div><ul>");
                    var aE;
                    aE = 100 / bQ;
                    for (var aY = 1; aY < bn.length; aY++) {
                        var bv = "sortnum";
                        by.append("<li style='float:none;margin-bottom:6px;padding:3px 0;'>");
                        by.append("<div style='float:left;'><input type='checkbox' style='display:none;' /><span class='" + bv + "'></span></div>");
                        by.append("<div style='float:left;text-align:center;'>");
                        var ax = true;
                        if (bn[aY]._item_img) {
                            var a4 = bn[aY]._item_img;
                            by.append("<div style='text-align:left;'>");
                            by.append("<img style='border:none;margin:0;' src='" + a4 + "' alt='' />");
                            by.append("</div>");
                            if (!bn[aY]._item_imgtext) {
                                ax = false;
                            }
                        }
                        if (ax) {
                            by.append(trim(bn[aY]._item_title));
                        }
                        if (bn[aY]._item_tb) {
                            by.append(" <input type='text' class='underline' style='color:#999999;' value=''/>");
                        }
                        if (bn[aY]._item_tbr) {
                            by.append(" <span style='color: red;'> *</span>");
                        }
                        by.append("</div>");
                        by.append("<div style='clear:both;'></div>");
                        by.append("</li>");
                    }
                    by.append("</ul>");
                }
                if (e) {
                    var bK = aP._daoZhi;
                    var aJ = "100%";
                    if (aP._mainWidth) {
                        aJ = aP._mainWidth + "%";
                    }
                    by.append("<table style='width:" + aJ + ";' border='0px'  cellpadding='5' cellspacing='0'>");
                    var aT = "";
                    var au = "";
                    var a9 = "radio";
                    var bP = new Array();
                    bP = trim(aP._rowtitle).split("\n");
                    var aS = trim(aP._rowtitle2).split("\n");
                    var aZ = trim(aP._rowtitle2) ? true: false;
                    if (this._referDivQ) {
                        bP = new Array();
                        for (var bE = 1; bE < this._referDivQ.dataNode._select.length; bE++) {
                            bP.push(this._referDivQ.dataNode._select[bE]._item_title);
                            if (bE == 3) {
                                break;
                            }
                        }
                        bP.push("......");
                        aZ = false;
                    }
                    var bo = false;
                    var bs = "";
                    if ((aU == 0) || (aU > 100 && aU < 200) || aU > 300) {
                        by.append("<thead><tr><th></th>");
                        if (aU > 300) {
                            var bj = trim(aP._columntitle).split("\n");
                            for (var aY = 0; aY < bj.length; aY++) {
                                by.append("<td align='center'>" + trim(bj[aY]) + "</td>");
                            }
                        } else {
                            if (bK) {
                                for (var aY = 0; aY < bP.length; aY++) {
                                    if (bP[aY].length > 4 && bP[aY].substring(0, 4) == "【标签】") {
                                        continue;
                                    }
                                    by.append("<td align='center'>" + trim(bP[aY]) + "</td>");
                                }
                            } else {
                                var a2 = 100;
                                var bz = 12;
                                var bx = 1;
                                for (var aY = 1; aY < bn.length; aY++) {
                                    var bl = trim(bn[aY]._item_title).length;
                                    if (bl > bx) {
                                        bx = bl;
                                    }
                                }
                                if (bx == 2) {
                                    bz = 6;
                                } else {
                                    if (bx == 3) {
                                        bz = 8;
                                    } else {
                                        var a1 = 75 / (bn.length - 1);
                                        bz = 2.4 * bx;
                                        if (bz > a1) {
                                            bz = a1;
                                        }
                                    }
                                }
                                if (aP._rowwidth) {
                                    a2 -= parseInt(aP._rowwidth);
                                    bz = a2 / (bn.length - 1);
                                }
                                if (aS.length > 0 && aP._rowwidth2) {
                                    a2 -= parseInt(aP._rowwidth2);
                                    bz = a2 / (bn.length - 1);
                                }
                                for (var aY = 1; aY < bn.length; aY++) {
                                    by.append("<td");
                                    if (bx > 1) {
                                        by.append(" width='" + bz + "%'");
                                    }
                                    by.append(" align='center'>" + trim(bn[aY]._item_title) + "</td>");
                                }
                            }
                        }
                        au = "border-bottom:1px solid #efefef;";
                        aT = "style='" + au + "'";
                        by.append("</tr>");
                        if (aU == 101) {
                            by.append("<tr><th style='color:#efa030;font-size:14px;' align='left'>分值</th>");
                            for (var aY = 1; aY < bn.length; aY++) {
                                var bO = trim(bn[aY]._item_value);
                                if (bO == -77777) {
                                    bO = "";
                                }
                                by.append("<td align='center' style='color:#efa030;font-size:14px;'>" + bO + "</td>");
                            }
                            by.append("</tr>");
                        }
                        by.append("</thead>");
                        if (aU == 102) {
                            a9 = "checkbox";
                        }
                    }
                    if (aU == 301) {
                        a9 = "text";
                    }
                    by.append("<tbody>");
                    if (aU == "202") {
                        var bJ = aP._minvalue;
                        var aB = aP._maxvalue;
                        var ay = " width='60%'";
                        var bb = "90";
                        by.append("<tr><th></th><td align='left' width='410'><table width='100%'><tr><td " + ay + "><div style='width:" + bb + "%'><div style='float:left;color:red;'>" + bJ + "</div><div style='float:right;color:red;'>" + aB + "</div><div style='clear:both;'></div></div></td></tr></table></td><th></th>");
                    }
                    var bk = false;
                    if (aU == 201 && aP._requir && aP._rowVerify) {
                        for (var aY = 0; aY < aP._rowVerify.length; aY++) {
                            if (aP._rowVerify[aY]._isRequir == false) {
                                bk = true;
                                break;
                            }
                        }
                        aP._partRequir = bk;
                        if (bk) {
                            H.setreqstatus();
                        }
                    }
                    if (!bK) {
                        var ba = 0;
                        var bN = false;
                        var bu = "";
                        for (var aY = 0; aY < bP.length; aY++) {
                            if (aY == bP.length - 1) {
                                aT = "";
                                au = "";
                            }
                            if (bP[aY].length > 4 && bP[aY].substring(0, 4) == "【标签】") {
                                var bc = bP[aY].substring(4);
                                by.append("<tr class='labelname'><th align='left'><b>" + bc + "</b></th><td colspan='" + bn.length + "'></td>");
                                by + "</tr>";
                                bo = true;
                                bs = "padding-left:20px;";
                                bN = !bN;
                                continue;
                            }
                            if (aP._rowwidth == "") {
                                by.append("<tr><th align='left' style='" + au + bs + "'>" + bP[aY] + "</th>");
                            } else {
                                by.append("<tr><th align='left' style='width:" + aP._rowwidth + ";" + au + bs + "'>" + bP[aY] + "</th>");
                            }
                            if (aU < 100 && aU) {
                                by.append("<td>");
                                by.append("<ul");
                                if (aU == 6) {
                                    by.append(" class='onscore'");
                                }
                                by.append(">");
                            }
                            if (aU > 200 && aU < 300) {
                                if (aU == 201) {
                                    var a8 = aP._rowVerify && aP._rowVerify[ba] ? aP._rowVerify[ba]._verify: "";
                                    var bD = "";
                                    var aX = "";
                                    var aH = aP._rowVerify && aP._rowVerify[ba] ? aP._rowVerify[ba]._width: "";
                                    if (aH) {
                                        aH = "width:" + aH + "%";
                                    }
                                    if (a8 == "日期") {
                                        bD = "/images/form/date.png";
                                    } else {
                                        if (a8 == "地图") {
                                            bD = "/images/form/map.png";
                                        } else {
                                            if (a8 == "手机") {
                                                bD = "/images/form/mobile.png";
                                            } else {
                                                if (a8 == "Email") {
                                                    bD = "/images/form/email.png";
                                                } else {
                                                    if (a8 == "电话" || a8 == "固话") {
                                                        bD = "/images/form/tel.png";
                                                    } else {
                                                        if (a8 == "QQ") {
                                                            bD = "/images/form/qq.png";
                                                        } else {
                                                            if (a8 == "姓名") {
                                                                bD = "/images/form/name.png";
                                                            } else {
                                                                if (a8 == "网址") {
                                                                    bD = "/images/form/website.png";
                                                                } else {
                                                                    if (a8 == "指定选项") {
                                                                        aX = aP._rowVerify[ba]._choice;
                                                                    } else {
                                                                        if (a8 == "高校") {
                                                                            bD = "/images/form/school.png";
                                                                        } else {
                                                                            if (a8 == "城市单选" || a8 == "城市多选" || a8 == "省市区") {
                                                                                bD = "/images/form/city.png";
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    by.append("<td  " + aT + "align='left'>");
                                    by.append("<div style='position:relative;'>");
                                    if (aX) {
                                        var aG = aX.split(/[,，]/);
                                        var br = "<select style='border:1px solid #d5d5d5;vertical-align:middle;'><option value=''>请选择</option>";
                                        for (var aO = 0; aO < aG.length; aO++) {
                                            var a3 = aG[aO];
                                            br += "<option value='" + a3 + "'>" + a3 + "</option>";
                                        }
                                        br += "</select>";
                                        by.append(br);
                                    } else {
                                        by.append("<textarea class='inputtext' style='overflow:auto;height:22px;" + aH + "'></textarea>");
                                        if (bD) {
                                            by.append("<img src='" + bD + "' alt='' style='position:absolute;top:3px;left:3px;'/>");
                                        }
                                    }
                                    var bH = true;
                                    if (aP._rowVerify && aP._rowVerify[ba] && aP._rowVerify[ba]._isRequir == false) {
                                        bH = false;
                                    }
                                    if (bk && bH) {
                                        by.append("<span class='req'>&nbsp;*</span>");
                                    }
                                    by.append("</div>");
                                    by.append("</td>");
                                } else {
                                    if (aU == 202) {
                                        var ay = " width='410'";
                                        var bb = "90";
                                        by.append("<td  " + aT + "align='left' " + ay + "><img src='"+url+"/img/slider1.jpg' style='width: 10px;'/><img src='"+url+"/img/sliderEnd.jpg' style='width:" + bb + "%;height: 23px;'/></td>");
                                    }
                                }
                            } else {
                                if (aU > 300) {
                                    var bA = "";
                                    if (aU == "303") {
                                        bA += "<select><option>" + type_radio_down + "</option>";
                                        for (var be = 1; be < bn.length; be++) {
                                            bA += "<option>" + trim(bn[be]._item_title) + "</option>";
                                        }
                                        bA += "</select>";
                                    }
                                    var bj = trim(aP._columntitle).split("\n");
                                    var bd = Number(300 / bj.length);
                                    for (var aW = 0; aW < bj.length; aW++) {
                                        var at = "";
                                        if (aU == "303") {
                                            by.append("<td  " + aT + "align='center'>" + bA + "</td>");
                                        } else {
                                            if (aU == "301") {
                                                bd = "30";
                                            }
                                            by.append("<td  " + aT + "align='center'>");
                                            var aX = "";
                                            if (aU == "302") {
                                                var a8 = aP._rowVerify && aP._rowVerify[aW] ? aP._rowVerify[aW]._verify: "";
                                                if (a8 == "指定选项") {
                                                    aX = aP._rowVerify[aW]._choice;
                                                }
                                                if (aX) {
                                                    var aG = aX.split(/[,，]/);
                                                    var br = "<select style='border:1px solid #d5d5d5;vertical-align:middle;'><option value=''>请选择</option>";
                                                    for (var aO = 0; aO < aG.length; aO++) {
                                                        var a3 = aG[aO];
                                                        br += "<option value='" + a3 + "'>" + a3 + "</option>";
                                                    }
                                                    br += "</select>";
                                                    by.append(br);
                                                }
                                            }
                                            if (!aX) {
                                                if (at) {
                                                    by.append("<div style='position:relative;'>");
                                                }
                                                by.append("<textarea class='inputtext' type='text' style='overflow:auto;height:22px;width:" + bd + "px;'></textarea>");
                                                if (at) {
                                                    by.append(at);
                                                    by.append("</div>");
                                                }
                                            }
                                            by.append("</td>");
                                        }
                                    }
                                } else {
                                    for (var aW = 1; aW < bn.length; aW++) {
                                        if (aU > 100 || aU == 0) {
                                            var bI = "jqRadio";
                                            if (a9 == "checkbox") {
                                                bI = "jqCheckbox";
                                            }
                                            by.append("<td " + aT + "align='center'");
                                            if ((aU == 102 || aU == 103 || aU == 101) && bn[aW]._item_tb) {
                                                by.append(" onmouseover=showFillData(this); onmouseout=' sb_setmenunav(toolTipLayer, false);' ");
                                            }
                                            by.append(">");
                                            by.append("<a href='###' class='" + bI + "' style='position:static;'></a><input style='display:none;' type='" + a9 + "'/>");
                                            by.append("</td>");
                                        } else {
                                            var bt = "style='padding-left:3px;'";
                                            var aC = bn.length - 1;
                                            var bG = "off";
                                            var bC = "on";
                                            if (aU == "6") {
                                                var bh = parseInt(355 / aC) - 2;
                                                if (aW == bn.length - 1) {
                                                    bh += 355 % aC;
                                                }
                                                bt = "style='width:" + bh + "px' ";
                                                bG = "off";
                                                bC = "on";
                                            }
                                            if (aW == bn.length - 1) {
                                                by.append("<li " + bt + " class='" + bG + aU + "'>");
                                            } else {
                                                by.append("<li " + bt + " class='" + bC + aP._tag + "'>");
                                            }
                                            if (aU == "6") {
                                                var bw = bn[aW]._item_value;
                                                if (bw == NoValueData) {
                                                    bw = "&nbsp;";
                                                }
                                                by.append(bw);
                                            }
                                            by.append("</li>");
                                        }
                                    }
                                }
                            }
                            if (aU < 100 && aU) {
                                by.append("</ul></td>");
                            }
                            var aN = "";
                            if (ba < aS.length) {
                                aN = aS[ba];
                            }
                            if (aP._rowwidth2 == "") {
                                by.append("<th " + aT + ">" + aN + "</th>");
                            } else {
                                by.append("<th style='width:" + aP._rowwidth2 + ";" + au + "'>" + aN + "</th>");
                            }
                            by.append("</tr>");
                            bN = !bN;
                            ba++;
                        }
                    } else {
                        for (var aY = 1; aY < bn.length; aY++) {
                            if (aY == bn.length - 1) {
                                aT = "";
                                au = "";
                            }
                            if (aP._rowwidth == "") {
                                by.append("<tr><th align='left' style='" + au + bs + "'>" + trim(bn[aY]._item_title) + "</th>");
                            } else {
                                by.append("<tr><th align='left' style='width:" + aP._rowwidth + ";" + au + bs + "'>" + trim(bn[aY]._item_title) + "</th>");
                            }
                            for (var aW = 0; aW < bP.length; aW++) {
                                if (bP[aW].length > 4 && bP[aW].substring(0, 4) == "【标签】") {
                                    continue;
                                }
                                by.append("<td  " + aT + "align='center'><input  type='" + a9 + "'/></td>");
                            }
                            by.append("</tr>");
                        }
                    }
                    by.append("</tbody></table>");
                }
            }
            by.append("<div class='div_table_clear_bottom'></div>");
            z.innerHTML = by.toString("");
        };
        H.createTableRadio(true);
    }

    return H;
}

function set_dataNode_to_Design(DataArray,question) {
    var f;
    var h = 0;
    var d = 0;
    var b = document.createDocumentFragment();
	
    for (var c = 1; c < DataArray.length; c++) {
		f = create_question(DataArray[c]);
		
		if(DataArray[c]._type != "page"){
			b.appendChild(f);
		}		

        if (DataArray[c]._referedTopics) {
            var g = DataArray[c]._referedTopics.split(",");
            for (var e = 0; e < g.length; e++) {
                referRelHT[g[e]] = f;
            }
        }
        if (DataArray[c]._type != "page") {
            if (referRelHT[DataArray[c]._topic]) {
                var a = referRelHT[DataArray[c]._topic];
                f._referDivQ = a;
                if (!a._referedArray) {
                    a._referedArray = new Array();
                }
                a._referedArray.push(f);
                if (DataArray[c]._type == "sum") {
                    f.createSum();
                } else {
                    if (f.createTableRadio) {
                        f.createTableRadio();
                    }
                }
            }
        }
    }
    $(question).appendChild(b);

}