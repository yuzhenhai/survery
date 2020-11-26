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
var subjectInfo="提示：";//填写提示
var jump_info="此题设置了跳转逻辑";//有跳题逻辑提示
var page_info = "页";
var defaultOtherText="";
var validate_email="请输入正确的Email地址如cs@sojump.com";
var validate_phone = "请输入正确的电话号码（如：027-87789123或086-027-87789123），请注意使用英文字符格式。";
var validate_mobile = "请输入正确的手机号码（如：13812341234），请注意使用英文字符格式。";
var validate_mo_phone = "请输入正确的号码（如：13812341234或027-87789123），请注意使用英文字符格式。";
var validate_reticulation="请输入正确的网址如：http://www.sojump.com";
var validate_chinese="此题只允许输入汉字，请不要输入英文和标点符号。";
var validate_english="此题只允许输入英文字母";
var validate_idcardNum="请输入正确的身份证号（如：432123198912103118），请注意使用英文字符格式。";
var validate_num = "请输入正确的整数（如：12345678），请注意使用英文字符格式。";
var validate_decnum = "请输入正确的数字（如：12.34），请注意使用英文字符格式。";
var validate_num1="最大值不能超过";
var validate_num2="最小值不能小于";
var validate_date = "请输入正确的日期";
var validate_qq="请输入正确的QQ号码，可以为邮箱";
var validate_only="此题要求每个用户输入的答案都唯一，您输入的答案之前已有用户输入，请重新输入！";
var validate_list="您输入的答案不在问卷发布者预先设定的答案列表中，请重新输入！";
var validate_error="正在验证此题答案的正确性，请再次点击按钮！";//此题未通过检查，请确认您输入的内容！
var validate_textbox="文本框内容必须填写！";
var validate_submit="请确保所有内容填写正确，页面将自动定位到第一个不符合要求的题目，请检查！";
var type_wd_limit="字以内";
var type_wd_minlimit="最少";
var type_wd_minlimitDigit="最小值";
var type_wd_maxlimitDigit="最大值";
var type_wd_digitfrom="从";
var type_wd_words="字";
var type_wd_to="到";
var validate_info="此";
var validate_info_wd1 = "请回答此题";
var validate_info_q1 = "请输入内容";
var validate_info_c1 = "请选择选项";
var validate_info_f1 = "请上传文件";
var validate_info_o1 = "请选择选项并排序";

var validate_info_wd2="题的填写超过了最大字数，请修改！";
var validate_info_wd3="您的输入已超过最大字数{0},当前字数为{1}";
var validate_info_wd4="您的输入小于最小输入字数{0},当前字数为{1}";
var validate_info_check1="题请选择";
var validate_info_check2="项并排序";
var validate_info_check3="题请选择所有选项并排序";
var validate_info_check4="题最多只能选择";
var validate_info_check5="题最少要选择";

var validate_info_matrix1="小题";
var validate_info_matrix2="题为必答题，请回答";
var validate_info_matrix3="！";
var validate_info_matrix4="题必须填写数字";

var validate_info_submit1="请输入验证码";
var validate_info_submit2 = "正在提交......";
var validate_info_submit8="很抱歉，由于网络异常您的提交没有成功，请再试一次！";


var validate_info_submit_title1="看不清吗？请点击刷新！";
var validate_info_submit_title3="点击获取验证码";
var validate_info_submit_title2="将本题所有选项设为未选中状态";
var type_radio_clear="清除选择";
var validate_info_submit1="请输入验证码";
var slider_hint="拖动或点击滑动条";
var slider_value="当前滑动值为：";
var sum_hint="可以拖动滑动条也可以通过文本框直接输入";
var sum_warn="请修改！";
var sum_total="提示：总比重值必须为：";
var sum_left="，已分配比重：";
var minTimeTip="秒后继续";



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
var prevSaveData = "";
var hasCeShiQ = false;
var totalHideQcount = 0;
var initQCount = 0;
var hasErrorImg = false;
$ce = function(c, d, a) {
    var b = document.createElement(c);
    if (d) {
        b.innerHTML = d;
    }
    a.appendChild(b);
    return b;
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
function forbidBackSpace(f) {
    var c = f || window.event;
    var d = c.target || c.srcElement;
    var b = d.type || d.getAttribute("type");
    var a = c.keyCode == 8 && b != "password" && b != "text" && b != "textarea";
    if (a) {
        return false;
    }
}
document.onkeydown = forbidBackSpace;
function getGapFillCount(b) {
    var d = 0;
    var e = 0;
    var a = b.length;
    do {
        e = b.indexOf(GapFillStr, e);
        if (e != -1) {
            d++;
            e += GapFillStr.length;
            for (var c = e; c < a; c++) {
                if (b.charAt(c) != "_") {
                    break;
                }
                e++;
            }
        }
    } while ( e != - 1 );
    return d;
}
function replaceImg(c) {
    var b = "http://pubimage.sojump.cn";
    var a = "//pubalifr.sojump.cn";
    if (c.src.indexOf("http://pubssl.sojump.com") == 0 || c.src.indexOf("https://pubssl.sojump.com") == 0 || c.src.indexOf("http://pubimage.sojump.com") == 0) {
        c.src = c.src.replace("http://pubssl.sojump.com", b).replace("https://pubssl.sojump.com", b).replace("http://pubimage.sojump.com", b);
        hasErrorImg = true;
    } else {
        if (c.src.indexOf("http://pubalifr.sojump.com") == 0 || c.src.indexOf("https://pubalifr.sojump.com") == 0 || c.src.indexOf("https://pubali.sojump.com") == 0 || c.src.indexOf("http://pubali.sojump.com") == 0) {
            c.src = c.src.replace("http://pubalifr.sojump.com", a).replace("https://pubalifr.sojump.com", a).replace("http://pubali.sojump.com", a).replace("https://pubali.sojump.com", a);
            hasErrorImg = true;
        }
    }
}
var EndGapReq = true;
var batAddQTimes = 0;
function replaceGapFill(n, e) {
    var g = 0;
    var f = 0;
    EndGapReq = true;
    if (e._requir) {
        var C = n.indexOf("<br");
        if (C > -1) {
            var h = n.indexOf(GapFillStr);
            if (h > C) {
                EndGapReq = false;
                n = n.substring(0, C) + "<span class='req'>&nbsp;*</span>" + n.substring(C);
            }
        }
    }
    var u = new StringBuilder();
    var D = 0;
    do {
        f = g;
        g = n.indexOf(GapFillStr, g);
        var m = GapFillStr;
        var k = "";
        var s = false;
        if (e._requir && e._rowVerify) {
            for (var z = 0; z < e._rowVerify.length; z++) {
                if (e._rowVerify[z]._isRequir == false) {
                    s = true;
                    EndGapReq = false;
                    break;
                }
            }
        }
        if (g != -1) {
            var o = 0;
            u.append(n.substr(f, g - f));
            g += GapFillStr.length;
            for (var y = g; y < n.length; y++) {
                if (n[y] != "_") {
                    break;
                }
                o++;
                m += "_";
                g++;
            }
            var b = GapWidth + o * (GapWidth / 3);
            if (b > 600) {
                b = 600;
            }
            var B = false;
            if (e._rowVerify[D]) {
                if (e._rowVerify[D]._verify == "日期") {
                    b = 70;
                    B = true;
                } else {
                    if (e._rowVerify[D]._verify == "指定选项") {
                        k = e._rowVerify[D]._choice;
                    }
                }
            }
            var l = "";
            if (e._isCeShi) {
                var r = e._rowVerify;
                if (r[D]) {
                    var t = (r[D]._answer || "请设置答案");
                    l = t + ":" + (r[D]._ceshiValue || 1) + "分";
                    var x = t.length * 12 + 24;
                    if (b < x) {
                        b = x;
                    }
                }
            }
            var p = "";
            if (k) {
                p = GapFillReplace.replace("width:" + GapWidth + "px", "display:none;width:" + b + "px");
            } else {
                p = GapFillReplace.replace("width:" + GapWidth + "px", "width:" + b + "px");
            }
            if (l) {
                p = p.replace("/>", " value='" + l + "'/>");
            }
            if (e._useTextBox) {
                p = p.replace("/>", " class='inputtext'/>");
            } else {
                p = p.replace("/>", " class='underline'/>");
            }
            if (k) {
                var q = k.split("|");
                var w = q[0].split(/[,，]/);
                var a = q[1] || "请选择";
                var d = "<select style='vertical-align:middle;'><option value=''>" + a + "</option>";
                for (var z = 0; z < w.length; z++) {
                    var A = w[z];
                    d += "<option value='" + A + "'>" + A + "</option>";
                }
                d += "</select>";
                p = p.replace("/>", "/>" + d);
            }
            var v = true;
            if (e._rowVerify[D] && e._rowVerify[D]._isRequir == false) {
                v = false;
            }
            u.append(p);
            if (s && v) {
                u.append("<span class='req'>&nbsp;*</span>");
            }
            D++;
        } else {
            if (f < n.length) {
                u.append(n.substr(f));
            }
        }
    } while ( g != - 1 );
    return u.toString();
}

function replace_specialChar(a) {
    return a.replace(/(§)/g, "ξ").replace(/(¤)/g, "○").replace(/(〒)/g, "╤");
}
function getCoords(a) {
    var d = a.getBoundingClientRect(),
    i = a.ownerDocument,
    f = i.body,
    e = i.documentElement,
    c = e.clientTop || f.clientTop || 0,
    g = e.clientLeft || f.clientLeft || 0,
    h = d.top + (self.pageYOffset || e.scrollTop || f.scrollTop) - c,
    b = d.left + (self.pageXOffset || e.scrollLeft || f.scrollLeft) - g;
    return {
        top: h,
        left: b
    };
}
function mouseCoords(a) {
    if (!a) {
        return;
    }
    if (a.pageX || a.pageY) {
        return {
            x: a.pageX,
            y: a.pageY
        };
    }
    return {
        x: a.clientX + document.body.scrollLeft - document.body.clientLeft,
        y: a.clientY + document.body.scrollTop - document.body.clientTop
    };
}
function showFillData(a) {
    toolTipLayer.innerHTML = "选中此项后，需要进行填空";
    sb_setmenunav(toolTipLayer, true, a);
}
var prevQType = null;
function sb_setmenunav(k, h, f, n, c) {
    var q = k;
    if (typeof(q) != "object") {
        q = document.getElementById(k);
    }
    if (!q) {
        return;
    }
    if (h) {
        if (q.timeArray) {
            window.clearTimeout(q.timeArray);
            q.timeArray = 0;
        }
        q.style.display = "block";
        if (!q.onmouseover) {
            q.onmouseover = function() {
                sb_setmenunav(k, true);
            };
            q.onmouseout = function() {
                sb_setmenunav(k, false);
            };
        }
        if (n) {
            var p = window.event || sb_setmenunav.caller.arguments[0];
            var m = mouseCoords(p);
            q.style.left = m.x + 1 + "px";
            q.style.top = m.y + 1 + "px";
        } else {
            if (f) {
                var e = f;
                if (e.parentNode.tagName.toLowerCase() == "li") {
                    e = f.parentNode;
                }
                var b = getCoords(e);
                var g = b.left;
                var j = b.top + e.offsetHeight;
                var i = c || document.documentElement.clientHeight || document.body.clientHeight;
                var s = document.documentElement.clientWidth || document.body.clientWidth;
                var o = s;
                if (q.id == "divDesc") {
                    o = 700;
                }
                if (f.nextObj) {
                    j = b.top - 33;
                }
                var r = f.getAttribute("qtype");
                if (q.innerHTML.indexOf("如果有多个答案") > -1) {
                    g += 88;
                    j -= 30;
                }
                if (r) {
                    g -= (q.offsetWidth - f.offsetWidth) / 2;
                    if (prevQType && prevQType != q) {
                        prevQType.style.display = "none";
                    }
                    prevQType = q;
                }
                if (g + q.offsetWidth > o) {
                    g = o - q.offsetWidth - 30;
                }
                if (j + q.offsetHeight > i) {
                    var l = 30;
                    if (r) {
                        l = 0;
                    }
                    var a = i - l - j;
                    if (a < 30) {
                        a = 30;
                    }
                    q.style.height = a + "px";
                }
                q.style.left = g + "px";
                q.style.top = j + "px";
            }
        }
    }
    if (f && f.tagName.toLowerCase() == "a") {
        q.needSaveClass = f;
        q.prevClass = f.className;
    } else {
        if (q.needSaveClass) {
            if (h) {
                q.needSaveClass.className = q.prevClass ? q.prevClass + " hover": "hover";
            } else {
                q.needSaveClass.className = q.prevClass || "";
            }
        }
    }
    if (!h) {
        if (jumpTipLayer && q != jumpTipLayer && jumpTipLayer.style.display != "none") {
            return;
        }
        q.timeArray = window.setTimeout(function() {
            q.style.display = "none";
            q.style.height = "";
        },
        300);
    }
}
var GapFillStr = "___";
var GapWidth = 21;
var GapFillReplace = "<input style='width:" + GapWidth + "px;' />";
function getFillStr(b) {
    var c = "";
    for (var a = 0; a < b; a++) {
        c += GapFillStr;
    }
    if (!c) {
        c = GapFillStr;
    }
    return c;
}
var EditorIndex = 1;
var EditToolBarItems = ["fontname", "fontsize", "textcolor", "bgcolor", "bold", "italic", "underline", "emoticons", "link", "image", "flash", "subscript", "superscript"];
var EditToolBarItemsPageCut = ["fontname", "fontsize", "textcolor", "bgcolor", "bold", "italic", "underline", "strikethrough", "subscript", "superscript", "plainpaste", "-", "justifyleft", "justifycenter", "justifyright", "indent", "outdent", "link", "emoticons", "image", "flash", "table", "hr"];
function getByClass(b, f, d) {
    var a = $$(b, f);
    var e = new Array();
    for (var c = 0; c < a.length; c++) {
        if (a[c].className.toLowerCase() == d.toLowerCase()) {
            e.push(a[c]);
        }
    }
    return e;
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
var defaultFileExt = ".gif|.png|.jpg";
function Request(d) {
    var b = window.document.location.href;
    var f = b.indexOf("?");
    var e = b.substr(f + 1);
    var c = e.split("&");
    for (var a = 0; a < c.length; a++) {
        var g = c[a].split("=");
        if (g[0].toUpperCase() == d.toUpperCase()) {
            return g[1];
        }
    }
    return "";
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
var status_tip = $("status_tip");
var topnav = $("topnav");
var divSurvey = $("sur");
var divMenu = $("divMenu");
var questions = $("question");
var firstPage = null;
var questionHolder = new Array();
var cur = null;
var curover = null;
var curinsert = null;
var langVer = 0;
var WjxActivity = new Object();
var DataArray = new Array();
var total_page = 0;
var total_question = 0;
var select_item_num = 1;
var isMergeAnswer = false;
var isCompleteLoad = false;
var referRelHT = new Object();
var designversion = "7";
var hasInsPromoteJump = false;
var lastAddNewQTime = null;
var prevcurmove = null;
var useShortCutAddNewQ = false;
var QIndentity = 1;
function trim(a) {
    if (a && a.replace) {
        return a.replace(/(^\s*)|(\s*$)/g, "");
    } else {
        return a;
    }
}
var interval_time;
init_page();
function init_page() {
    addEventSimple(window, "resize", setSidePos);
    setSidePos();
    show_status_tip("正在读取数据，请稍后...");
    processData();
    interval_time = setInterval(autoSave, 90 * 1000);
}
function processData() {
    var c = hfData.value;

	show_status_tip("数据读取成功，初始化...");
	set_data_fromServer(c);
	set_data_toDesign();
	isCompleteLoad = true;
	loadComplete();
	document.title = "设计问卷";
	Calculatedscore();
	if (total_question < 100) {
		var a = document.getElementsByTagName("img");
		for (var b = 0; b < a.length; b++) {
			a[b].onerror = function() {
				this.onerror = null;
				replaceImg(this);
			};
			replaceImg(a[b]);
		}
	}
}
function autoSave() {

	//save_paper("edit", false);

}

function set_data_fromServer(c) {
    var g = new Array();
    var f = c;
    g = f.split("¤");

    for (var d = 1; d < g.length; d++) {
        DataArray[d - 1] = set_string_to_dataNode(g[d]);
        if (DataArray[d - 1]._type != "page" && DataArray[d - 1]._type != "cut") {
            initQCount++;
        }
    }
}
function set_string_to_dataNode(r) {
    var f = new Object();
    var d = new Array();
    d = r.split("§");
    f._type = d[0];
    switch (d[0]) {
    case "page":
        f._topic = d[1];
        f._title = d[2];
        f._iszhenbie = d[4] == "true";
        f._istimer = d[4] == "time";
        f._mintime = d[5] ? parseInt(d[5]) : "";
        f._maxtime = d[6] ? parseInt(d[6]) : "";
        total_page++;
        break;
    case "cut":
        f._title = d[1];
        f._video = d[2] || "";
        f._relation = d[3] || "";
        break;
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
        if (d[19]) {
            if (f._verify == "多级下拉") {
                f._levelData = d[19] || "";
            } else {
                var h = d[19].split("〒");
                f._isCeShi = true;
                f._ceshiValue = h[0] || 5;
                f._answer = h[1] || "请设置答案";
                f._ceshiDesc = h[2] || "";
                f._include = h[3] == "true";
                hasCeShiQ = true;
            }
        }
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
        if (d[11]) {
            f._isCeShi = true;
            hasCeShiQ = true;
        }
        if (k) {
            var o = k.split("〒");
            for (var y = 0; y < o.length; y++) {
                var z = new Object();
                var e = o[y].split("¦");
                if (e[0] == "指定选项") {
                    z._verify = e[0];
                    z._choice = e[1] || "";
                    z._isRequir = e[2] == "false" ? false: true;
                } else {
                    var q = o[y].split(",");
                    z._verify = q[0];
                    z._minword = q[1];
                    z._maxword = q[2];
                    if (f._isCeShi) {
                        z._ceshiValue = q[3] || 1;
                        z._answer = q[4] || "请设置答案";
                        z._ceshiDesc = q[5] || "";
                        z._include = q[6] == "true";
                        hasCeShiQ = true;
                    } else {
                        z._isRequir = q[3] == "false" ? false: true;
                        z._needOnly = q[4] == "true";
                    }
                }
                f._rowVerify[y] = z;
            }
        }
        f._useTextBox = d[10] == "true";
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
            if (f._tag == "202" ) {
                f._minvalue = A[2] || "";
                var p = A[3] || "";
                f._maxvalue = p;
            } else {
                if (f._tag == "102" || f._tag == "103") {
                    f._daoZhi = A[2] == "true";
                } else {
                    if (f._tag == "201") {
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
            f._select[m]._item_tb = w[4] == "true";
            f._select[m]._item_tbr = w[5] == "true";
            f._select[m]._item_img = w[6];
            f._select[m]._item_imgtext = w[7] == "true";
            f._select[m]._item_desc = w[8];
            f._select[m]._item_label = w[9];
            if (w.length >= 9) {
                f._select[m]._item_huchi = w[10] == "true";
            }
            select_item_num++;
        }
        break;
    default:
        break;
    }
    return f;
}
function set_data_toDesign() {
    document.title = "正在加载问卷，请耐心等待....";
    set_dataNode_to_Design();
}
function getIEVersion() {
    var a = navigator.userAgent.match(/(?:MSIE |Trident\/.*; rv:)(\d+)/);
    return a ? parseInt(a[1]) : undefined;
}
function setQTopPos(d) {
    var g = 0;
    var f = document.documentElement.clientHeight || document.body.clientHeight;
    g = d.offsetTop + d.offsetHeight - f + 10;
    if (d.offsetHeight < f - 100) {
        g += 100;
    }
    var e = getIEVersion();
    var c = e && (!document.documentMode || document.documentMode < 8);
    var a = e <= 7 || c;
    if (a) {
        g += 85;
    }
    if (e) {
        setTimeout(function() {
            divSurvey.scrollTop = g;
        },
        400);
    } else {
        divSurvey.scrollTop = g;
    }
    if (d.dataNode._select && d.dataNode._select.length > 6 && d.attrMain) {
        d.attrMain.scrollIntoView();
    } else {
        if (d.dataNode._rowtitle && d.attrMain) {
            var b = trim(d.dataNode._rowtitle).split("\n");
            if (b.length > 5) {
                d.attrMain.scrollIntoView();
            }
        }
    }
}
function set_dataNode_to_Design() {
    var f;
    var h = 0;
    var d = 0;
    var b = document.createDocumentFragment();
    for (var c = 0; c < DataArray.length; c++) {
		f = create_question(DataArray[c]);
		if(DataArray[c]._type != "page"){
			b.appendChild(f);
		}
        if (DataArray[c]._type == "page" && firstPage == null) {
            firstPage = f;
            if (window.isTiKu) {
                firstPage.style.display = "none";
            }
        } else {
            questionHolder[h++] = f;
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
    questions.appendChild(b);
    if (total_question == 0 && firstPage && !firstPage.dataNode._title) {
        firstPage.style.display = "none";
    }
}
function getDataNodeByTopic(b) {
    for (var c = 0,
    a = questionHolder.length; c < a; c++) {
        var d = questionHolder[c].dataNode;
        if (d._type == "page" || d._type == "cut") {
            continue;
        }
        if (b == d._topic) {
            return d;
        }
    }
    return null;
}
function getDivIndex(b) {
    for (var c = 0,
    a = questionHolder.length; c < a; c++) {
        var d = questionHolder[c].dataNode;
        if (d._type == "page" || d._type == "cut") {
            continue;
        }
        if (b == d._topic) {
            return c;
        }
    }
    return - 1;
}
function getJumpTitle(c) {
    if (c == "0" || c == "") {
        return "直接跳到下一题";
    } else {
        if (c == "1") {
            return "直接跳到问卷末尾";
        } else {
            if (isInt(c)) {
                var b = getDataNodeByTopic(c);
                if (b) {
                    var a = b._title;
                    if (!WjxActivity._use_self_topic) {
                        a = b._topic + "." + a;
                    }
                    return a;
                }
            }
        }
    }
    return "";
}
var status_tip_timeout = null;
function show_status_tip(b, d) {
    clearTimeout(status_tip_timeout);
    status_tip.style.display = "block";
    status_tip.innerHTML = b;
    var a = document.documentElement.scrollTop || document.body.scrollTop;
    var c = document.documentElement.clientHeight || document.body.clientHeight;
    if (status_tip.hasSetWidth) {
        status_tip.style.width = "";
    }
    status_tip.style.top = a + c - status_tip.offsetHeight - 596 + "px";
    status_tip.style.width = (divSurvey.offsetWidth - 10) + "px";
    if (d > 0) {
        status_tip_timeout = setTimeout("status_tip.style.display='none';status_tip.style.width = '';status_tip.hasSetWidth=false;", d);
    }
}
function setSidePos() {
    status_tip.style.left = "0px";
    var b = document.documentElement.clientHeight || document.body.clientHeight;
    divSurvey.style.height = b - 144 + "px";
    var a = document.documentElement.clientWidth || document.body.clientWidth;
    //document.getElementById("m-rightbar").style.right = a < 1280 ? "20px": "-45px";
    //document.getElementById("divNewTip").style.display = a < 1280 ? "none": "";
}
function show(a) {
    return;
}


function getQList(a, h) {
    var m = a.dataNode._topic;
    var c = "";
    c += "<div style='border-top:1px solid #ccddff;margin:10px 0;'>";
    for (var d = 0,
    f = questionHolder.length; d < f; d++) {
        var k = questionHolder[d].dataNode;
        if (k._type == "page" || k._type == "cut") {
            continue;
        }
        var l = k._topic;
        if (h == 2 && questionHolder[d] == a) {
            break;
        }
        var g = (l - m > 0 && h == 1) || (h == 2);
        if (h == 2 && k._type != "question" && k._type != "radio" && k._type != "radio_down" && k._type != "check") {
            continue;
        }
        if (g) {
            var b = l + ".";
            if (WjxActivity._use_self_topic) {
                b = "";
            }
            var e = k._title.replace(/<.+?>/gim, "");
            var j = "";
            if (h == 1) {
                j = "jumpSelected(" + l + ",this);return false;";
            } else {
                j = "referSelected(" + l + ",this);return false;";
            }
            c += "<div style='margin-top:6px;'><a class='link-U666' onclick='" + j + "' href='javascript:void(0);'  title='" + e + "'>" + b + e.substring(0, 23) + "</a></div>";
        }
    }
    c += "</div></div>";
    return c;
}

function referSelected(b, e) {
    var d = "[q" + b + "]";
    var a = cur.gettextarea();
    var c = a.value.match(/\[q(\d+)\]/);
    if (c && isInt(c[1])) {
        alert("此题已经设置了引用到第" + c[1] + "题！");
        return;
    }
    var f = a.value.length;
    a.focus();
    if (typeof document.selection != "undefined") {
        document.selection.createRange().text = d;
    } else {
        a.value = a.value.substr(0, a.selectionStart) + d + a.value.substring(a.selectionEnd, f);
    }
    cur.checkTitle();
    toolTipLayer.style.width = "250px";
    show_status_tip("操作成功，被引用题目[" + b + "]的答案将会显示在此题标题中！", 6000);
    sb_setmenunav(toolTipLayer, false);
}
var jumpTipLayer = null;
function showJumpOver(a) {
    if (!jumpTipLayer) {
        jumpTipLayer = toolTipLayer.cloneNode(true);
        document.body.appendChild(jumpTipLayer);
    }
    jumpTipLayer.style.width = "250px";
    jumpTipLayer.innerHTML = "提示：题号“-1”表示当用户<b style='color:red;'>点击下一页</b>时，系统会自动提交并将答卷标为无效。请在题目后面添加<b style='color:red;'>分页栏</b>。";
    sb_setmenunav(jumpTipLayer, true, a);
}
function openJumpWindow(c, d, a) {
    var b = "&nbsp;<span style='color:#333;font-weight:bold;'>请选择要跳转到的题目：</span>";
    b += "<div style='padding:5px;'>";
    if (!a) {
        b += "<a onclick='jumpSelected(0,this);return false;' href='javascript:void(0);' title='提示：题号“0”表示顺序填写下一题' class='link-UF90'>跳到下一题</a>\r\n&nbsp;&nbsp;";
    }
    b += "<a onclick='jumpSelected(1,this);return false;'  href='javascript:void(0);' title='提示：题号“1”表示直接跳到问卷末尾' class='link-UF90'>跳到问卷末尾</a>\r\n&nbsp;&nbsp;";
    if (!a) {
        b += "<a onclick='jumpSelected(-1,this);return false;' href='javascript:void(0);'  onmouseover='showJumpOver(this);' onmouseout='sb_setmenunav(jumpTipLayer,false,this);'  class='link-UF90'>提交为无效答卷</a>\r\n";
    }
    b += getQList(c, 1);
    b += "</div>";
    toolTipLayer.innerHTML = b;
    toolTipLayer.jumpObj = d;
    toolTipLayer.style.width = "300px";
    sb_setmenunav(toolTipLayer, true, d);
}

function openValWindow(b, c) {
    var a = "<div style='padding:5px 10px;'>";
    a += "<div style='cursor:pointer;margin-top:3px;'><a onclick='valChanged(2);return false;' class='link-444' href='javascript:void(0);'>交换选项分数</a></div>";
    a += "<div style='cursor:pointer;margin-top:3px;'><a onclick='valChanged(0);return false;' class='link-444' href='javascript:void(0);'>分数<b>从1开始</b>顺序递增</a></div>";
    a += "<div style='cursor:pointer;margin-top:3px;'><a onclick='valChanged(1);return false;' class='link-444' href='javascript:void(0);'>选项分数全部<b>加1</b></a></div>";
    a += "<div style='cursor:pointer;margin-top:3px;'><a onclick='valChanged(-1);return false;' class='link-444' href='javascript:void(0);'>选项分数全部<b>减1</b></a></div>";
    a += "</div>";
    toolTipLayer.innerHTML = a;
    toolTipLayer.valObj = b;
    toolTipLayer.style.width = "150px";
    sb_setmenunav(toolTipLayer, true, c);
}
function valChanged(f) {
    if (!toolTipLayer.valObj) {
        return;
    }
    var c = toolTipLayer.valObj;
    var h = toolTipLayer.valObj.dataNode;
    var g = c.option_radio;
    if (f == 0) {
        for (var d = 1; d < g.length; d++) {
            if (g[d].get_item_value().value != "") {
                g[d].get_item_value().value = d;
            }
        }
    } else {
        if (f == 2) {
            var e = 1;
            var a = g.length - 1;
            while (e < a) {
                var b = g[a].get_item_value().value;
                g[a].get_item_value().value = g[e].get_item_value().value;
                g[e].get_item_value().value = b;
                if (g[a].get_item_novalue()) {
                    b = g[a].get_item_novalue().checked;
                    g[a].get_item_novalue().checked = g[e].get_item_novalue().checked;
                    g[e].get_item_novalue().checked = b;
                }
                e++;
                a--;
            }
        } else {
            for (var d = 1; d < g.length; d++) {
                if (g[d].get_item_value().value != "") {
                    g[d].get_item_value().value = parseInt(h._select[d]._item_value) + f;
                }
            }
        }
    }
    c.updateItem();
    toolTipLayer.valObj = null;
    sb_setmenunav(toolTipLayer, false);
}
function openProvinceWindow(a, c) {
    var b = "北京,天津,河北,山西,内蒙古,辽宁,吉林,黑龙江,上海,江苏,浙江,安徽,福建,江西,山东,河南,湖北,湖南,广东,广西,海南,重庆,四川,贵州,云南,西藏,陕西,宁夏,甘肃,青海,新疆,香港,澳门,台湾,其它国家,不指定";
    var g = "<div style='padding:5px 10px;'>";
    var f = b.split(",");
    for (var d = 1; d <= f.length; ++d) {
        var j = f[d - 1];
        var e = "link-06f";
        if (j == "不指定") {
            e = "link-f60";
        }
        var h = "<span style='cursor:pointer;margin-top:3px;'><a onclick='provinceChanged(this);return false;' class='" + e + "' href='javascript:void(0);'>" + j + "</a></span>";
        g += h;
        if (d % 8 == 0) {
            g += "<div></div>";
        } else {
            g += "&nbsp;&nbsp;";
        }
    }
    g += "</div>";
    toolTipLayer.innerHTML = g;
    toolTipLayer.provinceObj = c;
    toolTipLayer.style.width = "360px";
    sb_setmenunav(toolTipLayer, true, c);
}
function provinceChanged(a) {
    if (!toolTipLayer.provinceObj) {
        return;
    }
    toolTipLayer.provinceObj.value = a.innerHTML;
    if (toolTipLayer.provinceObj.value == "不指定") {
        toolTipLayer.provinceObj.value = "";
    }
    if (toolTipLayer.provinceObj.onblur) {
        toolTipLayer.provinceObj.onblur();
    }
    toolTipLayer.provinceObj = null;
    sb_setmenunav(toolTipLayer, false);
}
function jumpSelected(a, c) {
    var b = "";
    if (toolTipLayer.jumpObj) {
        b = toolTipLayer.jumpObj.value;
        toolTipLayer.jumpObj.value = a || "0";
        toolTipLayer.jumpObj.title = c.innerHTML;
        if (toolTipLayer.jumpObj.onblur) {
            toolTipLayer.jumpObj.onblur();
        }
        toolTipLayer.jumpObj = null;
        if (cur && cur.updateItem) {
            cur.updateItem();
        }
    }
    if (a == -1 && b != "-1") {
        if (confirm("此功能需要在该题后面添加分页栏才能生效，是否添加分页栏？")) {
            curinsert = cur;
            createFreQ("page");
        }
    }
    toolTipLayer.style.width = "250px";
    toolTipLayer.style.display = "none";
}
function getPageQCount() {
    var c = 0;
    var d = new Array();
    var a = 0;
    for (var b = 0; b < questionHolder.length; b++) {
        if (questionHolder[b].dataNode._type == "page") {
            c++;
            d.push(a);
            a = 0;
        } else {
            if (questionHolder[b].dataNode._type != "cut") {
                a++;
            }
        }
    }
    d.push(a);
    return d;
}

function $import(b) {
    var a = document.createElement("script");
    a.setAttribute("src", b);
    a.setAttribute("type", "text/javascript");
    document.getElementsByTagName("head")[0].appendChild(a);
}
function $importNoCache(a) {
    $import(a);
}
function loadComplete() {
    show_status_tip("成功获得数据", 2000);
    save_paper("init", false);
    setSidePos();
    divMenu.style.visibility = "visible";
    topnav.style.visibility = "visible";
    $importNoCache(url+"js/operation_new.js?v=26");
    //$importNoCache("/kindeditor/kindeditor.js?v=3");
    $importNoCache(url+"js/createqattr_new.js?v=45");
    //$importNoCache("/Js/utility_new.js?v=1");
}
function getXmlHttp() {
    var a;
    try {
        a = new XMLHttpRequest();
    } catch(b) {
        try {
            a = new ActiveXObject("Msxml2.XMLHTTP");
        } catch(b) {
            try {
                a = new ActiveXObject("Microsoft.XMLHTTP");
            } catch(b) {}
        }
    }
    return a;
}
function removeEventSimple(c, a, b) {
    if (c.removeEventListener) {
        c.removeEventListener(a, b, false);
    } else {
        if (c.detachEvent) {
            c.detachEvent("on" + a, b);
        }
    }
}
function addEventSimple(c, a, b) {
    if (c.addEventListener) {
        c.addEventListener(a, b, false);
    } else {
        if (c.attachEvent) {
            c.attachEvent("on" + a, b);
        }
    }
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
function addTouPiao(c, b, a) {
    if (b._displayPercent || b._displayNum) {
        if (b._displayNum) {
            c.append("<span style='color:#ff6600;'>0票</span>");
        }
        if (b._displayPercent) {
            if (b._displayNum) {
                c.append("(");
            }
            c.append("0%");
            if (b._displayNum) {
                c.append(")");
            }
        }
    }
}

function create_question(g) {
    var ao = g._type;
    var x = g._verify;
    var B = g._height > 1;
    _likertMode = g._tag || 0;
    var L = false;
    var h = false;
    if (isMergeAnswer && isCompleteLoad) {
        h = true;
    }
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
    if (am) {
        total_question++;
    }
    QIndentity++;
    var l = document.createElement("div");
    if (am) {
        var U = g._topic;
        U = U - totalHideQcount;
        var ag = U + "";
        if (U - 100 < 0) {
            ag += ".";
        }

        if (hasCeShiQ) {
            if (!g._isCeShi) {
                ag = "";
                totalHideQcount++;
            }
        }
        var J = $ce("div", ag, l);
        H.divTopic = J;
        J.className = "div_topic_question";
        if (g._topic - 100 >= 0) {
            J.style.fontSize = "14px";
        }
        if (WjxActivity._use_self_topic) {
            J.style.display = "none";
        }
    }
    if (X) {
        var j = g._iszhenbie;
        l.style.position = "relative";
        var s = "<span style='font-size:14px; font-weight:bold;'>第" + g._topic + "页/共" + total_page + "页</span>";
        var J = $ce("span", s, l);
        var w = $ce("span", false, l);
        J.className = "div_topic_page_question paging-bg";
        if (total_page == 1) {
            J.style.visibility = "hidden";
            w.style.visibility = "hidden";
        } else {
            J.style.visibility = "visible";
            w.style.visibility = "visible";
        }
        var ae = $ce("div", false, l);
        ae.className = "related_settings";
        w.className = "line_as_hr";
        H.title = "分页栏";
        H.line = w;
        H.divTopic = J;
        H.divZhenBie = $ce("span", "<b style='color:red;'>甄别页</b>", ae);
        if (g._istimer) {
            $ce("span", "<b style='color:red;'>--时间页</b>", ae);
        }
        H.divZhenBie.style.display = j ? "": "none";
        H.divTimeLimit = $ce("span", "", ae);
        H.showTimeLimit = function() {
            var v = "";
            if (this.dataNode._istimer) {
                if (this.dataNode._mintime) {
                    v = "<b style='color:green;'>页面停留时间：" + this.dataNode._mintime + "秒</b>";
                }
            } else {
                if (this.dataNode._mintime) {
                    v = "<b style='color:green;'>最短填写时间：" + this.dataNode._mintime + "秒</b>";
                }
                if (this.dataNode._maxtime) {
                    if (v) {
                        v += "&nbsp;";
                    }
                    v += "<b style='color:red;'>最长填写时间：" + this.dataNode._maxtime + "秒</b>";
                }
            }
            H.divTimeLimit.innerHTML = v;
        };
        H.showTimeLimit();
        if (g._topic == "1") {
            isPrevFirstPage = true;
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
                    al.innerHTML = "";
                    al.style.display = "none";
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
        H.showTextAreaDate();
        H.showTextAreaUnder();
        H.showTextAreaWidth();
        H.showTextAreaHeight();

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
            if (this._referDivQ) {
                var aA = "选项";
                if (aP._type == "matrix" || aP.type == "sum") {
                    aA = "行标题";
                }
                a0 = true;
                var ar = "第" + this._referDivQ.dataNode._topic + "题";
                by.append("此题" + aA + "来源于" + ar + "的选中项");
            } else {
                if (aP._isQingJing) {
                    var az = this.qingjing || 1;
                    if (az >= bn.length) {
                        az = 1;
                    }
                    a0 = true;
                    by.append("<div style='font-size:16px;color:#333;font-weight:bold;margin-top:10px;'>" + bn[az]._item_title + "&nbsp;<a style='font-size:16px;cursor:pointer;' onclick='if(curover)curover.createTableRadio();'>切换场景</a></div>");
                    by.append(bn[az]._item_desc || "请设置情景说明");
                    az++;
                    this.qingjing = az;
                } else {
                    if (aP._isShop) {
                        a0 = true;
                        by.append("<ul>");
                        for (var aY = 1; aY < bn.length; aY++) {
                            var a7 = bn[aY]._item_title;
                            var aR = bn[aY]._item_value;
                            var bq = bn[aY]._item_img;
                            var a5 = "";
                            if (aY > 1 && aY % 3 == 1) {
                                a5 = " style='clear:both;'";
                            }
                            by.append("<li class='shop-item' " + a5 + ">");
                            if (bq) {
                                by.append("<div class='img_place'><img src='" + bq + "' alt='' /></div>");
                            }
                            by.append("<div class='text_place'>");
                            by.append("<div class='item_name'>" + a7 + "</div>");
                            by.append('<p class="item_price">￥' + aR + "</p>");
                            by.append('<p class="item_select"><span class="operation remove">-</span><input class="operation itemnum" value="0" disabled="disabled"><span class="operation add">+</span></p>');
                            by.append("</div><div class='divclear'></div></li>");
                        }
                        by.append("</ul>");
                    }
                }
            }
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
    if (am) {
        var Y = document.createElement("div");
        Y.className = "div_ins_question";
        Y.innerHTML = g._ins ? subjectInfo + g._ins: "";
        R.appendChild(Y);
        H.get_div_ins = function() {
            return Y;
        };
    }
    var ab = $ce("div", "", R);
    ab.style.height = "18px";
    ab.className = "spanLeft";
    if (am) {
        var Z = document.createElement("div");
        Z.className = "div_ins_question spanLeft";
        Z.style.clear = "none";
        ab.appendChild(Z);
        H.set_jump_ins = function() {
            var v = "*" + jump_info;
            Z.style.display = this.dataNode._hasjump ? "": "none";
            if (this.dataNode._hasjump) {
                if (this.dataNode._anytimejumpto < 1) {} else {
                    if (this.dataNode._anytimejumpto == "1") {
                        v += "<span style='color:#ff6600;'>(结束作答)</span>";
                    } else {
                        v += "<span style='color:#ff6600;'>(跳转到第" + this.dataNode._anytimejumpto + "题)</span>";
                    }
                }
            }
            Z.innerHTML = v;
        };
        H.set_jump_ins();
    }
    if (am || K) {
        var c = document.createElement("div");
        c.className = "div_ins_question spanLeft";
        c.style.clear = "none";
        c.innerHTML = "";
        H.getRelation = function() {
            var aq = this.dataNode._relation;
            if (!aq) {
                return;
            }
            var aB = aq.split("|");
            var aA = "依赖于";
            var at = "";
            var aF = "";
            for (var ax = 0; ax < aB.length; ax++) {
                var ar = aB[ax];
                if (!ar) {
                    continue;
                }
                var az = ar.split(",");
                if (az.length < 2) {
                    continue;
                }
                if (aA != "依赖于") {
                    aA += "，";
                }
                var aE = getDataNodeByTopic(az[0]);
                if (!aE) {
                    continue;
                }
                if (WjxActivity._use_self_topic) {
                    var aD = aE._title.match(/^\s*\d+[、\.\-\_\(\/]?\d*\)?/);
                    if (aD) {
                        aA += "第" + aD + "题的第" + az[1].replace(/-/g, "").replace(/;/g, "、") + "个选项";
                    }
                } else {
                    aA += "第" + az[0] + "题的第" + az[1].replace(/-/g, "").replace(/;/g, "、") + "个选项";
                }
                var v = az[1].split(";");
                var au = "选择";
                var ap = "";
                if (v.length > 1) {
                    ap = "中的任何一个选项";
                }
                at = "";
                for (var av = 0; av < v.length; av++) {
                    var aC = v[av];
                    if (aC - 0 < 0) {
                        aC = aC * -1;
                        au = "没有选择";
                    }
                    if (aE._select && aE._select[aC]) {
                        if (at) {
                            at += "、";
                        }
                        at += "[" + aE._select[aC]._item_title + "]";
                    } else {
                        return;
                    }
                }
                var aw = aE._topic + ".";
                if (WjxActivity._use_self_topic) {
                    aw = "";
                }
                if (aF != "") {
                    aF += "<b>并且</b>";
                }
                aF += "当题目<span style='color:#0066ff;'>" + aw + aE._title + "</span>" + au + "<span style='color:#0066ff;font-size:12px;'>" + at + "</span>" + ap + "时，";
            }
            aF += "<b>此题才显示</b>";
            var ay = new Array();
            ay.push(aA);
            ay.push(aF);
            return ay;
        };
        c.style.display = g._relation ? "": "none";
        var af = H.getRelation();
        if (af) {
            c.innerHTML = af[0];
            c.dtitle = af[1];
            c.onmouseover = function() {
                toolTipLayer.style.width = "350px";
                toolTipLayer.innerHTML = this.dtitle;
                sb_setmenunav(toolTipLayer, true, this);
            };
            c.onmouseout = function(v) {
                sb_setmenunav(toolTipLayer, false, this);
            };
        }
        ab.appendChild(c);
        H.RelationIns = c;
    }
    if (g._relation == "0") {
        H.style.display = "none";
    }
    var S = document.createElement("div");
    S.className = "div_ins_question spanLeft";
    S.innerHTML = "<a href='javascript:void(0);' onclick='insertQ(curover);return false;' class='link-UF90' style='text-decoration:underline;'>在此题后插入新题</a>";
    S.style.clear = "none";
    S.style.visibility = "hidden";
    R.appendChild(S);
    H.divInsertOp = S;
    var ak = document.createElement("div");
    ak.className = "spanRight";
    ak.style.clear = "none";
    R.appendChild(ak);
    H.divTableOperation = ak;
    if (g._hasjump || g._relation) {
        H.divTableOperation.style.visibility = "";
    }
    $ce("div", "", R).style.clear = "both";
    if (X || K) {
        $ce("div", "", R).style.clear = "both";
    }
    cancelInputClick(H);
    return H;
}
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
function Calculatedscore() {
    return;
}
var needCheckLeave = true;
window.onbeforeunload = function() {
    if (needCheckLeave) {
        return "系统可能不会保存您所做的更改。";
    }
};
window.onunload = function() {
    finishEditing();
};
function windowGotoUrl(a) {
    needCheckLeave = false;
    window.location.href = a;
}
function chkAutoSave_Click(a) {}
function returnOld() {
    if (window.confirm("确认使用旧版编辑界面吗？")) {
        save_paper("old", true);
    }
}
var havereturn = false;
var timeoutTimer = null;
var errorTimes = 0;
function processError() {
    if (!havereturn) {
        havereturn = true;
        errorTimes++;
        var a = "网络异常，可能是您电脑上的防火墙拦截了保存的问卷数据，请关闭防火墙试试！";
        show_status_tip(a, 0);
    }
    if (timeoutTimer) {
        clearTimeout(timeoutTimer);
    }
}

var sendStr = "";
var hasLogicNotify = false;
var saveNotifyText = "";

function replaceTitleImg(c) {
    if (!hasErrorImg) {
        return c;
    }
    var b = "http://pubimageqiniu.paperol.cn";
    var a = "//pubnewfr.paperol.cn";
    c = c.replace(/\/\/pubalifr.sojump.com/g, a).replace(/\/\/pubali.sojump.com/g, a).replace(/\/\/pubssl.sojump.com/g, b).replace(/\/\/http:\/\/pubimage.sojump.com/g, b);
    return c;
}
function biaozhuDivQ(a) {
    a.style.border = "1px solid red";
    a.scrollIntoView(false);
}
function addNewPage(a) {
    if (curinsert) {
        lastAddNewQTime = new Date().getTime();
        createFreQ("page");
        if (a.parentNode) {
            a.parentNode.innerHTML = "添加成功，请重新勾选闯关模式！";
        }
        return false;
    }
}
function checkCanChuangGuan() {
    var a = false;
    for (var b = 0; b < questionHolder.length; b++) {
        var d = questionHolder[b].dataNode;
        var c = d._type;
        if (c == "cut") {
            continue;
        }
        if (c == "page") {
            if (a) {
                biaozhuDivQ(questionHolder[b]);
                return "提示：考试题后面不能再有分页栏，不能设置闯关模式";
            }
            continue;
        }
        if (d._isCeShi) {
            if (d._type != "radio") {
                biaozhuDivQ(questionHolder[b]);
                return "提示：题型只能为考试单选题或者考试判断题，不能设置闯关模式";
            }
            if (!a) {
                if (d._topic != "1") {
                    if (questionHolder[b - 1] && questionHolder[b - 1].dataNode._type != "page") {
                        curinsert = questionHolder[b - 1];
                        return "提示：第一个考试题前面必须有分页栏，不能设置闯关模式，<a href='javascript:' onclick='window.parent.addNewPage(this);'>添加分页栏</a>";
                    }
                }
            }
            a = true;
        } else {
            if (a) {
                biaozhuDivQ(questionHolder[b]);
                return "提示：非考试题型必须在考试题型的前面，不能设置闯关模式";
            }
        }
    }
}
function checkRandomType() {
    var d = "";
    var b = {};
    for (var c = 0; c < questionHolder.length; c++) {
        var e = questionHolder[c].dataNode;
        var a = getTypeName(e);
        if (d != a) {
            d = a;
            if (a != "") {
                if (!b[a]) {
                    b[a] = true;
                } else {
                    return false;
                }
            }
        }
    }
    return true;
}
function sortQuestionByType(d) {
    if (!confirm("提示：确认调整题型顺序吗？")) {
        return;
    }
    var m = new Array();
    var q = new Array();
    var o = {};
    for (var e = 0; e < questionHolder.length; e++) {
        if (getTypeName(questionHolder[e].dataNode)) {
            m.push(questionHolder[e]);
        }
    }
    var g = 1;
    var b = 0;
    var l = "";
    for (var e = 0; e < questionHolder.length; e++) {
        var n = getTypeName(questionHolder[e].dataNode);
        if (n != "") {
            if (!o[n]) {
                o[n] = 1;
                for (var c = 0; c < m.length; c++) {
                    var f = m[c].dataNode;
                    l = getTypeName(f);
                    if (l == n) {
                        q.splice(++b, 0, m[c]);
                    }
                }
            }
        } else {
            q.splice(++b, 0, questionHolder[e]);
        }
    }
    DataArray[0] = firstPage.dataNode;
    firstPage = null;
    totalHideQcount = 0;
    var p = 1;
    var a = 2;
    for (var e = 0; e < q.length; e++) {
        var h = q[e].dataNode;
        if (h._type != "page" && h._type != "cut") {
            h._topic = p;
            p++;
        } else {
            if (h._type == "page") {
                h._topic = a;
                a++;
            }
        }
        DataArray[e + 1] = h;
    }
    questions.innerHTML = "";
    set_dataNode_to_Design();
    initAttrHandler();
    initEventHandler();
    if (d) {
        d.parentNode.innerHTML = "提示：成功调整，请重新选择按题型随机！";
    }
}
function getTypeName(d) {
    if (!d._isCeShi) {
        return "";
    }
    var c = d._type;
    var e = d._answer;
    var b = d._ispanduan;
    var a = "";
    switch (c) {
    case "radio":
        if (b) {
            a = "判断题";
        } else {
            a = "单选题";
        }
        break;
    case "check":
        a = "多选题";
        break;
    case "question":
        if (e == "简答题无答案") {
            a = "简答题";
        } else {
            a = "单项填空题";
        }
        break;
    case "fileupload":
        a = "简答题";
        break;
    case "gapfill":
        a = "多项填空题";
        break;
    default:
        break;
    }
    return a;
}
function save_paper(ab, y, q) {
    if (ab != "init" && questionHolder.length == 0) {
        show_status_tip("您还未添加题目！", 3000);
        return false;
    }
    show_status_tip("正在保存，请耐心等候...", 0);
    if (ab != "init" && !save_paper_validate(y)) {
        return false;
    }
    var v = new Array();
    v[0] = new Object();
    v[0]._display_part = false;
    v[0]._display_part_num = 0;
    v[0]._partset = "";
    v[0]._random_mode = WjxActivity._random_mode;
    if (v[0]._random_mode == "3") {
        v[0]._partset = WjxActivity._partset;
        var o = WjxActivity._partset.split(",");
        var ac = "";
        var u = true;
        for (var G = 0; G < o.length; G++) {
            var r = o[G].split(";");
            var D = parseInt(r[0]);
            var d = parseInt(r[1]);
            var ad = getPageQCount()[D];
            var aa = ad + ":" + d;
            if (!ac) {
                ac = aa;
            } else {
                if (ac != aa) {
                    u = false;
                }
            }
        }
        if (o.length < 2) {
            u = false;
        }
        if (u) {
            v[0]._partset += "|true";
        }
    } else {
        if (v[0]._random_mode == "4") {
            v[0]._partset = WjxActivity._partset;
        } else {
            if (v[0]._random_mode == "5") {
                v[0]._partset = WjxActivity._partsetnew;
            }
        }
    }
    v[0]._display_part = WjxActivity._display_part;
    v[0]._display_part_num = WjxActivity._display_part_num;
    v[0]._random_begin = WjxActivity._random_begin;
    v[0]._random_end = WjxActivity._random_end;
    v[1] = firstPage.dataNode;
    var e = false;
    var f = false;
    var k = false;
    var A = 1;
    var X = 2;
    for (var G = 0; G < questionHolder.length; G++) {
        if (questionHolder[G].checkValid && questionHolder[G].checkValid() == false) {
            v[G + 2] = questionHolder[G].validate();
        }
        v[G + 2] = questionHolder[G].dataNode;
        var L = v[G + 2]._type;
        if (L == "page") {
            if (v[G + 2]._topic != X) {
                v[G + 2]._topic = X;
            }
            X++;
        } else {
            if (L != "cut") {
                if (v[G + 2]._topic != A) {
                    v[G + 2]._topic = A;
                }
                A++;
            }
        }
        if (v[G + 2]._hasjump) {
            f = true;
        }
        var an = v[G + 2]._relation;
        if (an && an != "0") {
            var H = an.split(",");
            var x = true;
            k = true;
            var au = H[0];
            var aj = H[1].split(";");
            var aq = getDataNodeByTopic(au);
            var B = false;
            if (L == "cut" && aq) {
                var N = getDivIndex(au);
                if (N < G) {
                    B = true;
                }
            } else {
                B = aq && v[G + 2]._topic - au > 0;
            }
            if (B) {
                var a = aq._select;
                var L = aq._type;
                if (L == "radio" || L == "radio_down" || L == "check") {
                    for (var al = 0; al < aj.length; al++) {
                        var n = aj[al];
                        if (n == 0 || n >= a.length) {
                            x = false;
                        }
                    }
                } else {
                    x = false;
                }
            } else {
                x = false;
            }
            if (!x) {
                v[G + 2]._relation = "";
            }
        }
        v[G + 2]._referTopic = "";
        v[G + 2]._referedTopics = "";
        if (questionHolder[G]._referDivQ) {
            v[G + 2]._referTopic = questionHolder[G]._referDivQ.dataNode._topic;
            e = true;
        }
        if (questionHolder[G]._referedArray) {
            v[G + 2]._referedTopics = "";
            for (var am = 0; am < questionHolder[G]._referedArray.length; am++) {
                if (am > 0) {
                    v[G + 2]._referedTopics += ",";
                }
                v[G + 2]._referedTopics += questionHolder[G]._referedArray[am].dataNode._topic;
            }
        }
    }
    saveNotifyText = "";
    if (v[0]._random_mode != "0") {
        var J = "";
        var R = false;
        if (f) {
            J = "跳题逻辑";
            R = true;
        } else {
            if (e) {
                J = "引用逻辑";
                R = true;
            } else {
                if (k) {
                    J = "关联逻辑";
                    R = true;
                }
            }
        }
        if (R) {
            var S = "此问卷包含" + J + "，设置随机逻辑可能会导致" + J + "失效，请注意检查！";
            if (!hasLogicNotify && ab != "init") {
                alert(S);
                hasLogicNotify = true;
            }
            saveNotifyText = S;
        }
    }
    var ae = 0;
    for (var G = 1; G < v.length; G++) {
        if (v[G]._type == "page") {
            ae++;
        }
    }
    v[0]._total_page = ae;
    var h = new StringBuilder();
    var Z = false;
    var Q = false;
    var ai = false;
    var c = false;
    var l = false;
    var ap =  '';
    if ((v[0]._random_mode == "1" || v[0]._random_mode == "2") && v[0]._display_part) {
        ap += "§" + v[0]._display_part + "§" + v[0]._display_part_num;
    } else {
        if (v[0]._random_mode == "3" || v[0]._random_mode == "4" || v[0]._random_mode == "5") {
            ap += "§" + v[0]._partset + "§";
        } else {
            ap += "§§";
        }
    }
    ap += "§" + designversion;
    var P = 0;
    for (var G = 1; G < v.length; G++) {
        var g = "";
        var Y = v[G]._title.match(/\[q(\d+)\]/);
        if (Y && isInt(Y[1])) {
            g = "〒" + Y[1];
        }
        if (ab != "init") {
            v[G]._title = replaceTitleImg(v[G]._title);
        }
        switch (v[G]._type) {
        case "question":
            var an = v[G]._relation || "";
            var t = v[G]._needOnly;
            if (v[G]._needsms) {
                t += "〒" + v[G]._needsms;
            }
            h.append("¤" + v[G]._type + "§" + v[G]._topic + "§" + v[G]._title + "〒" + v[G]._keyword + "〒" + an + g + "§" + v[G]._tag + "§" + v[G]._height + "§" + v[G]._maxword + "§" + v[G]._requir + "§" + v[G]._norepeat + "§" + v[G]._default + "§" + v[G]._ins + "§" + v[G]._hasjump + "§" + v[G]._anytimejumpto + "§" + v[G]._verify + "§" + t + "§" + v[G]._hasList + "§" + v[G]._listId + "§" + v[G]._width + "§" + v[G]._underline + "§" + v[G]._minword);
            if (v[G]._isCeShi) {
                h.append("§" + v[G]._ceshiValue + "〒" + v[G]._answer + "〒" + v[G]._ceshiDesc + "〒" + v[G]._include);
                c = true;
            } else {
                if (v[G]._verify == "多级下拉") {
                    h.append("§" + (v[G]._levelData || ""));
                }
            }
            P++;
            break;
        case "gapfill":
            var an = v[G]._relation || "";
            var s = getGapFillCount(v[G]._title);
            var C = v[G]._useTextBox ? "true": "";
            h.append("¤" + v[G]._type + "§" + v[G]._topic + "§" + v[G]._title + "〒" + v[G]._keyword + "〒" + an + g + "§" + v[G]._tag + "§" + v[G]._requir + "§" + s + "§" + v[G]._ins + "§" + v[G]._hasjump + "§" + v[G]._anytimejumpto);
            h.append("§");
            if (v[G]._rowVerify) {
                for (var T = 0; T < s; T++) {
                    if (T > 0) {
                        h.append("〒");
                    }
                    if (!v[G]._rowVerify[T]) {
                        continue;
                    }
                    var F = v[G]._rowVerify[T];
                    h.append(F._verify || "");
                    if (F._verify == "指定选项") {
                        h.append("¦");
                        h.append(F._choice || "");
                        if (v[G]._requir && F._isRequir == false) {
                            h.append("¦");
                            h.append("false");
                        }
                    } else {
                        h.append(",");
                        h.append(F._minword || "");
                        h.append(",");
                        h.append(F._maxword || "");
                    }
                    if (v[G]._isCeShi) {
                        h.append(",");
                        h.append(F._ceshiValue || "1");
                        h.append(",");
                        var m = F._answer || "";
                        m = m.replace(/,/g, "，");
                        h.append(m);
                        h.append(",");
                        var V = F._ceshiDesc || "";
                        V = V.replace(/,/g, "，");
                        h.append(V);
                        h.append(",");
                        h.append(F._include);
                    } else {
                        if (F._verify != "指定选项") {
                            h.append(",");
                            if (v[G]._requir && F._isRequir == false) {
                                h.append("false");
                            }
                            h.append(",");
                            if (F._needOnly) {
                                h.append("true");
                            }
                        }
                    }
                }
            }
            h.append("§");
            h.append(C);
            if (v[G]._isCeShi) {
                h.append("§1");
                c = true;
            }
            P++;
            break;
        case "slider":
            var an = v[G]._relation || "";
            h.append("¤" + v[G]._type + "§" + v[G]._topic + "§" + v[G]._title + "〒" + v[G]._keyword + "〒" + an + g + "§" + v[G]._tag + "§" + v[G]._requir + "§" + v[G]._minvalue + "§" + v[G]._maxvalue + "§" + v[G]._minvaluetext + "§" + v[G]._maxvaluetext + "§" + v[G]._ins + "§" + v[G]._hasjump + "§" + v[G]._anytimejumpto);
            P++;
            break;
        case "fileupload":
            var an = v[G]._relation || "";
            var ao = "";
            if (v[G]._isCeShi) {
                ao = "§" + (v[G]._ceshiValue || 5) + "〒" + (v[G]._ceshiDesc || "");
                c = true;
            }
            h.append("¤" + v[G]._type + "§" + v[G]._topic + "§" + v[G]._title + "〒" + v[G]._keyword + "〒" + an + g + "§" + v[G]._tag + "§" + v[G]._requir + "§" + v[G]._width + "§" + v[G]._ext + "§" + v[G]._maxsize + "§" + v[G]._ins + "§" + v[G]._hasjump + "§" + v[G]._anytimejumpto + ao);
            P++;
            break;
        case "sum":
            var an = v[G]._relation || "";
            h.append("¤" + v[G]._type + "§" + v[G]._topic + "§" + v[G]._title + "〒" + v[G]._keyword + "〒" + an + g + "§" + v[G]._tag + "§" + v[G]._requir + "§" + v[G]._total + "§" + v[G]._rowtitle + "§" + v[G]._rowwidth + "§0§" + v[G]._ins + "§" + v[G]._hasjump + "§" + v[G]._anytimejumpto);
            h.append("§" + v[G]._referTopic + "§" + v[G]._referedTopics);
            P++;
            break;
        case "cut":
            var an = v[G]._relation || "";
            h.append("¤" + v[G]._type + "§" + v[G]._title + "§" + (v[G]._video || "") + "§" + an + g);
            break;
        case "page":
            h.append("¤" + v[G]._type + "§" + v[G]._topic + "§" + v[G]._title + "§" + v[G]._tag);
            var O = v[G]._iszhenbie ? "true": "";
            O = v[G]._istimer ? "time": O;
            h.append("§" + O);
            h.append("§" + v[G]._mintime);
            if (v[G]._mintime) {
                Z = true;
            }
            h.append("§" + v[G]._maxtime);
            if (v[G]._maxtime) {
                Q = true;
            }
            break;
        case "check":
        case "radio_down":
        case "radio":
        case "matrix":
            var an = v[G]._relation || "";
            v[G]._tag = isNaN(v[G]._tag) ? 0 : v[G]._tag;
            var U = v[G]._mainWidth || "";
            h.append("¤" + v[G]._type + "§" + v[G]._topic + "§" + v[G]._title + "〒" + v[G]._keyword + "〒" + an + "〒" + U + g + "§" + v[G]._tag + "§");
            if (v[G]._type == "matrix") {
                if (v[G]._referTopic) {
                    v[G]._rowtitle2 = "";
                }
                h.append(v[G]._rowtitle + "〒" + v[G]._rowtitle2 + "〒" + v[G]._columntitle);
            } else {
                h.append(v[G]._numperrow + "〒" + v[G]._randomChoice);
            }
            h.append("§" + v[G]._hasvalue + "§" + v[G]._hasjump + "§" + v[G]._anytimejumpto + "§" + v[G]._requir);
            if (v[G]._type == "check" || (v[G]._type == "matrix" && v[G]._tag == "102")) {
                if (v[G]._isShop) {
                    h.append(",shop");
                    l = true;
                } else {
                    h.append("," + v[G]._lowLimit + "," + v[G]._upLimit);
                }
            } else {
                if (v[G]._type == "radio" && v[G]._isQingJing) {
                    h.append(",1");
                } else {
                    if (v[G]._type == "radio" && v[G]._ispanduan) {
                        h.append(",2");
                    }
                }
            }
            if (v[G]._type == "matrix") {
                var af = v[G]._rowwidth;
                if (v[G]._randomRow) {
                    af += ",true";
                }
                h.append("§" + af + "〒" + v[G]._rowwidth2);
                if (v[G]._tag == "202" ) {
                    h.append("〒" + v[G]._minvalue + "〒" + v[G]._maxvalue);
                } else {
                    if (v[G]._tag == "102" || v[G]._tag == "103") {
                        var at = v[G]._daoZhi || "";
                        h.append("〒" + at);
                    } else {
                        if (v[G]._tag == "201" ) {
                            if (v[G]._rowVerify) {
                                h.append("〒");
                                var aw = trim(v[G]._rowtitle).split("\n");
                                var w = 0;
                                for (var T = 0; T < aw.length; T++) {
                                    if (v[G]._tag == "201" && aw[T].substring(0, 4) == "【标签】") {
                                        continue;
                                    }
                                    if (v[G]._rowVerify[w]) {
                                        var F = v[G]._rowVerify[w];
                                        if (F._verify == "指定选项") {
                                            h.append(w + "¦");
                                            h.append(F._verify + "¦");
                                            h.append(F._choice || "");
                                            if (v[G]._requir && F._isRequir == false) {
                                                h.append("¦");
                                                h.append("false");
                                            }
                                        } else {
                                            h.append(w + ",");
                                            h.append(F._verify || "");
                                            h.append(",");
                                            h.append(F._minword || "");
                                            h.append(",");
                                            h.append(F._maxword || "");
                                            h.append(",");
                                            h.append(F._width || "");
                                            h.append(",");
                                            if (v[G]._requir && F._isRequir == false) {
                                                h.append("false");
                                            }
                                            h.append(",");
                                            if (F._needOnly) {
                                                h.append("true");
                                            }
                                        }
                                        h.append(";");
                                    }
                                    w++;
                                }
                            }
                        }
                    }
                }
            } else {
                if (v[G]._displayDesc) {
                    var p = v[G]._displayDescTxt || "";
                    h.append("§desc〒" + p);
                } else {
                    h.append("§");
                }
            }
            var W = v[G]._verify;
            if (v[G]._type == "matrix" && v[G]._nocolumn) {
                W += ",true";
            }
            h.append("§" + v[G]._ins + "§" + W);
            h.append("§" + v[G]._referTopic + "§" + v[G]._referedTopics);
            for (var E = 1; E < v[G]._select.length; E++) {
                var M = "";
                if (v[G]._select[E]._item_huchi) {
                    M = "〒true";
                }
                var av = v[G]._select[E]._item_value;
                if (v[G]._select[E]._item_value == "") {
                    av = NoValueData;
                }
                if (ab != "init") {
                    v[G]._select[E]._item_img = replaceTitleImg(v[G]._select[E]._item_img);
                }
                if (ab != "init") {
                    v[G]._select[E]._item_desc = replaceTitleImg(v[G]._select[E]._item_desc);
                }
                h.append("§" + v[G]._select[E]._item_title + "〒" + v[G]._select[E]._item_radio + "〒" + av + "〒" + v[G]._select[E]._item_jump + "〒" + v[G]._select[E]._item_tb + "〒" + v[G]._select[E]._item_tbr + "〒" + v[G]._select[E]._item_img + "〒" + v[G]._select[E]._item_imgtext + "〒" + v[G]._select[E]._item_desc + "〒" + v[G]._select[E]._item_label + M);
            }
            P++;
            break;
        }
    }
    if (ab != "init") {
        hasErrorImg = false;
    }
    q = q || false;

    sendStr = ap + h.toString("");

	var ah = getXmlHttp();
    var ar = saveurl+"";

    if (q) {
        ah.open("post", ar, false);
    } else {
        ah.open("post", ar);
    }
    ah.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    if (!q) {
        ah.onreadystatechange = function() {
            if (ah.readyState == 4) {
                if (timeoutTimer) {
                    clearTimeout(timeoutTimer);
                }
                if (ah.status == 200) {
                    //alert(ah.responseText);
                    afterSave(unescape(ah.responseText), ab);
                } else {
                    show_status_tip("很抱歉，由于网络异常您的保存没有成功，请再试一次！", 6000);
                }
            }
        };
    }

    h.clear();
    if (ab == "init") {
        prevSaveData = sendStr;
        show_status_tip("成功加载", 1000);
        divSurvey.scrollTop = 0;
        return true;
    }
    havereturn = false;
    if (sendStr == prevSaveData) {
        saveSuc(ab);
    } else {
        if (!q) {
            timeoutTimer = setTimeout(function() {
                processError();
            },
            20000);
            if (errorTimes == 0) {
                ah.send("submitType=redesign&subject_id="+subject_id+"&surveydata=" + encodeURIComponent(sendStr));
            } else {
                postWithIframe(ar);
            }
        }
        if (q) {
            ah.send("submitType=redesign&subject_id="+subject_id+"&surveydata=" + encodeURIComponent(sendStr));
            var ak = afterSave(unescape(ah.responseText), ab);
            if (ak == false) {
                return false;
            }
        }
    }
    return true;
}
function postWithIframe(b) {
    var a = document.createElement("div");
    a.style.display = "none";
    a.innerHTML = "<iframe id='mainframe' name='mainframe' style='display:none;' > </iframe><form target='mainframe' id='frameform' action='' method='post' enctype='application/x-www-form-urlencoded'><input  value='' id='surveydata' name='surveydata' type='hidden'><input type='submit' value='提交' ></form>";
    document.body.appendChild(a);
    $("surveydata").value = sendStr;
    var c = $("frameform");
    c.action = b + "&iframe=1";
    c.submit();
}

function finishEditing(c) {
    var a = getXmlHttp();
    var b = designqfinish;
    a.open("post", b, false);
    a.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    a.send("edit=true&subject_id"+subject_id);
    if (c) {
        c();
    }
}
function afterSave(f, e) {
    havereturn = true;
    var b = false;

	clearInterval(interval_time);
	var a = f.split("&");
	var c = a[0];//true

	if (sendStr) {
		prevSaveData = sendStr;
	}
	saveSuc(e);
	b = true;

	interval_time = setInterval(autoSave, 60 * 1000);

    return b;
}

function saveSuc(a) {
    show_status_tip("保存问卷成功！" + saveNotifyText, 3000);
}
function doSaveValidate(a) {
    if (!a.createAttr) {
        return;
    }
    if (!a.hasCreatedAttr) {
        a.createOp();
        a.createAttr();
        a.setDataNodeToDesign();
        a.tabAttr.style.display = "none";
    }
    a.validate();
}
function isJumpToValid(b, a) {
    if (b != "" && b != 0 && b != 1 && b != -1) {
        if (toInt(b) <= a.dataNode._topic || toInt(b) > total_question) {
            return false;
        }
    }
    return true;
}
function save_paper_validate(k) {
    var d = true;
    var e;
    for (var h = 0; h < questionHolder.length; h++) {
        var f = questionHolder[h];
        if (f.checkValid && f.checkValid() == false) {
            doSaveValidate(f);
            if (questionHolder[h].checkValid() == false) {
                d = false;
                if (!e) {
                    e = questionHolder[h];
                }
            }
        } else {
            if (f.dataNode._hasjump) {
                if (!f.dataNode._anytimejumpto || f.dataNode._anytimejumpto == "0") {
                    var b = f.dataNode._select;
                    if (!b) {
                        continue;
                    }
                    var m = b.length;
                    for (var g = 1; g < m; g++) {
                        var c = trim(b[g]._item_jump);
                        if (!isJumpToValid(c, f)) {
                            doSaveValidate(f);
                            d = false;
                            if (!e) {
                                e = questionHolder[h];
                            }
                            break;
                        }
                    }
                } else {
                    var c = f.dataNode._anytimejumpto;
                    if (!isJumpToValid(c, f)) {
                        doSaveValidate(f);
                        d = false;
                        if (!e) {
                            e = questionHolder[h];
                        }
                    }
                }
            } else {
                if (f.dataNode._isCeShi) {
                    var b = f.dataNode._select;
                    if (!b) {
                        continue;
                    }
                    var m = b.length;
                    var a = false;
                    for (var g = 1; g < m; g++) {
                        if (b[g]._item_radio) {
                            a = true;
                        }
                    }
                    if (!a) {
                        doSaveValidate(f);
                        d = false;
                        if (!e) {
                            e = questionHolder[h];
                        }
                    }
                }
            }
        }
    }
    if (!d) {
        if (k) {
            if (e.ondblclick) {
                e.ondblclick();
            }
            e.scrollIntoView(false);
            show_status_tip("此题没有通过验证，保存失败！请按错误提示信息修改。", 6000);
        } else {
            show_status_tip("第" + e.dataNode._topic + "题没有通过验证，自动保存失败！请按错误提示信息修改。", 6000);
        }
        return false;
    }
    d = true;
    e = null;
    return true;
}




