var type_radio_down = "请选择";
var type_order = "排序题";
var type_order_limit_begin = "请选择";
var type_order_limit_end = "并排序";
var type_check = "多选题";
var type_check_limit1 = "请选择";
var type_check_limit2 = "可以选择";
var type_check_limit3 = "<b>最少</b>选择";
var type_check_limit4 = "<b>最多</b>选择";
var type_check_limit5 = "项";
var type_order_all = "全部选";
var subjectInfo = "提示：";
var jump_info = "此题设置了跳转逻辑";
var page_info = "页";
var defaultOtherText = "";
var validate_email = "请输入正确的Email地址如cs@sojump.com";
var validate_phone = "请输入正确的电话号码（如：027-87789123或086-027-87789123），请注意使用英文字符格式。";
var validate_mobile = "请输入正确的手机号码（如：13812341234），请注意使用英文字符格式。";
var validate_mo_phone = "请输入正确的号码（如：13812341234或027-87789123），请注意使用英文字符格式。";
var validate_reticulation = "请输入正确的网址如：http://www.sojump.com";
var validate_chinese = "此题只允许输入汉字，请不要输入英文和标点符号。";
var validate_english = "此题只允许输入英文字母";
var validate_idcardNum = "请输入正确的身份证号（如：432123198912103118），请注意使用英文字符格式。";
var validate_num = "请输入正确的整数（如：12345678），请注意使用英文字符格式。";
var validate_decnum = "请输入正确的数字（如：12.34），请注意使用英文字符格式。";
var validate_num1 = "最大值不能超过";
var validate_num2 = "最小值不能小于";
var validate_date = "请输入正确的日期";
var validate_qq = "请输入正确的QQ号码，可以为邮箱";
var validate_only = "此题要求每个用户输入的答案都唯一，您输入的答案之前已有用户输入，请重新输入！";
var validate_list = "您输入的答案不在问卷发布者预先设定的答案列表中，请重新输入！";
var validate_error = "正在验证此题答案的正确性，请再次点击按钮！";
var validate_textbox = "文本框内容必须填写！";
var validate_submit = "请确保所有内容填写正确，页面将自动定位到第一个不符合要求的题目，请检查！";
var type_wd_limit = "字以内";
var type_wd_minlimit = "最少";
var type_wd_minlimitDigit = "最小值";
var type_wd_maxlimitDigit = "最大值";
var type_wd_digitfrom = "从";
var type_wd_words = "字";
var type_wd_to = "到";
var validate_info = "此";
var validate_info_wd1 = "请回答此题";
var validate_info_q1 = "请输入内容";
var validate_info_c1 = "请选择选项";
var validate_info_f1 = "请上传文件";
var validate_info_o1 = "请选择选项并排序";
var validate_info_wd2 = "题的填写超过了最大字数，请修改！";
var validate_info_wd3 = "您的输入已超过最大字数{0},当前字数为{1}";
var validate_info_wd4 = "您的输入小于最小输入字数{0},当前字数为{1}";
var validate_info_check1 = "题请选择";
var validate_info_check2 = "项并排序";
var validate_info_check3 = "题请选择所有选项并排序";
var validate_info_check4 = "题最多只能选择";
var validate_info_check5 = "题最少要选择";
var validate_info_matrix1 = "小题";
var validate_info_matrix2 = "题为必答题，请回答";
var validate_info_matrix3 = "！";
var validate_info_matrix4 = "题必须填写数字";
var validate_info_submit1 = "请输入验证码";
var validate_info_submit2 = "正在提交......";
var validate_info_submit8 = "很抱歉，由于网络异常您的提交没有成功，请再试一次！";
var validate_info_submit_title1 = "看不清吗？请点击刷新！";
var validate_info_submit_title3 = "点击获取验证码";
var validate_info_submit_title2 = "将本题所有选项设为未选中状态";
var type_radio_clear = "清除选择";
var validate_info_submit1 = "请输入验证码";
var slider_hint = "拖动或点击滑动条";
var slider_value = "当前滑动值为：";
var sum_hint = "可以拖动滑动条也可以通过文本框直接输入";
var sum_warn = "请修改！";
var sum_total = "提示：总比重值必须为：";
var sum_left = "，已分配比重：";
var minTimeTip = "秒后继续";

if (typeof(Array.prototype.push) != "function") {
    Array.prototype.push = function() {
        for (var a = 0; a < arguments.length; a++) {
            this[this.length] = arguments[a];
        }
        return this.length;
    };
}
if (typeof(Array.prototype.indexOf) != "function") {
    Array.prototype.indexOf = function(b) {
        for (var a = 0; a < this.length; a++) {
            if (b === this[a]) {
                return a;
            }
        }
        return - 1;
    };
}
String.prototype.format = function() {
    var a = arguments;
    return this.replace(/\{(\d+)\}/g,
    function(b, c) {
        return a[c];
    });
};
String.prototype.dbc2sbc = function() {
    return this.replace(/[\uff01-\uff5e]/g,
    function(b) {
        return String.fromCharCode(b.charCodeAt(0) - 65248);
    }).replace(/\u3000/g, " ");
};
if (!window.maxCheatTimes) {
    maxCheatTimes = 0;
}
var hasAnswer = false;
var cur_page = 0;
var jumpPages;
var pageHolder = new Array();
var trapHolder = new Array();
var totalQ = 0;
var completeLoaded = false;
var MaxTopic = 0;

var curdiv = null;
var curfilediv = null;
var isUploadingFile = false;
var hasZhenBiePage = false;
var progressArray = new Object();
var questionsObject = new Object();
var joinedTopic = 0;
var randomparm = "";
var hasTouPiao = false;
var useSelfTopic = false;
document.oncontextmenu = document.ondragstart = document.onselectstart = avoidCopy;
var ZheZhaoControl = null;
var divTimeUp = document.getElementById("divTimeUp");
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
function avoidCopy(b) {
    b = window.event || b;

    var a;
    if (b) {
        if (b.target) {
            a = b.target;
        } else {
            if (b.srcElement) {
                a = b.srcElement;
            }
        }
        if (a.nodeType == 3) {
            a = a.parentNode;
        }
        if (a.tagName == "INPUT" || a.tagName == "TEXTAREA" || a.tagName == "SELECT") {
            return true;
        }
    }
    if (document.selection && document.selection.empty) {
        document.selection.empty();
    }
    return false;
}

$$tag = function(a, b) {
    if (b) {
        return b.getElementsByTagName(a);
    } else {
        return document.getElementsByTagName(a);
    }
};
function getTop(b) {
    var a = b.offsetLeft;
    var c = b.offsetTop;
    while (b = b.offsetParent) {
        a += b.offsetLeft;
        c += b.offsetTop;
    }
    return {
        x: a,
        y: c
    };
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

var submit_tip = document.getElementById("submit_tip");
var submit_div = document.getElementById("submit_div");
function trim(a) {
    //return a.replace(/(^\s*)|(\s*$)/g, "");
	return a;
}
function isInt(a) {
    var b = /^-?[0-9]+$/;
    return b.test(a);
}
var spChars = ["$", "}", "^", "|", "<"];
var spToChars = ["ξ", "｝", "ˆ", "¦", "&lt;"];
function replace_specialChar(c) {
    for (var a = 0; a < spChars.length; a++) {
        var b = new RegExp("(\\" + spChars[a] + ")", "g");
        c = c.replace(b, spToChars[a]);
    }
    if (/^[A-Za-z\s\.,]+$/.test(c)) {
        c = c.replace(/\s+/g, " ");
    }
    c = c.replace(/[^\x09\x0A\x0D\x20-\uD7FF\uE000-\uFFFD\u10000-\u10FFFF]/ig, "");
    return trim(c);
}
function isRadioImage(a) {
    if (!a || a == "0" || a == "1" || a == "101") {
        return false;
    } else {
        return true;
    }
}
function isRadioRate(a) {
    return a != "" && a != "0" && a != "1" && a != "-1";
}
var submit_table = document.getElementById("submit_table");
var submit_button = document.getElementById("submit_button");
//var tCode = document.getElementById(tdCode);
var divMaxTime = document.getElementById("divMaxTime");
var spanMaxTime = document.getElementById("spanMaxTime");
var maxCounter = 0;
var maxTimer = null;
var minTimer = null;
var initMaxSurveyTime = 0;
function changeHeight(d) {
    var e = parseInt(d.style.height);
    if (!e) {
        return;
    }
    if (!d.initHeight) {
        d.initHeight = e;
    }
    var c = 18;
    var b = 100;
    var a = d.scrollHeight;
    a = a > c ? a: c;
    a = a > b ? b: a;
    if (a - e >= 10) {
        d.style.height = a + "px";
    }
    if (!d.value || d.value.length < 5) {
        d.style.height = d.initHeight + "px";
    }
}

function fcInputboxBlur() {
    if (!this.value) {
        this.value = defaultOtherText;
        this.style.color = "#999999";
    } else {
        this.style.color = "#000000";
        if (langVer != 0) {
            return;
        }
        if (this.tagName == "select") {
            return;
        }
        var e = this.parent;
        var c = e.itemInputs;
        var f = this.value.split(/(,|，)/ig);
        for (var d = 0; d < c.length; d++) {
            var b = getNextNode(c[d]);
            if (e.dataNode && e.dataNode.isSort) {
                b = c[d].parentNode.getElementsByTagName("label")[0];
            }
            if (!b) {
                return;
            }
            for (var a = 0; a < f.length; a++) {
                if (trim(f[a].toLowerCase()) == trim(b.innerHTML.toLowerCase())) {
                    popUpAlert("提示：您输入的“" + f[a] + "”已经包含在题目选项当中");
                    if (!c[d].checked) {
                        c[d].parentNode.click();
                    }
                    b.style.color = "red";
                    if (f.length == 1 && this.choiceRel && this.choiceRel.checked) {
                        this.value = "";
                        this.parentNode.click();
                    }
                }
            }
        }
    }
}
function isTextBoxEmpty(a) {
    a = trim(a);
    if (a == "" || a == defaultOtherText) {
        return true;
    }
    return false;
}
var curMatrixFill = null;
var curMatrixError = null;
var divMatrixRel = document.getElementById("divMatrixRel");
var matrixinput = document.getElementById("matrixinput");

function showMatrixFill(e, g) {
    if (g) {
        if (curMatrixError) {
            return;
        }
        curMatrixError = e;
    }
    curMatrixFill = e;
    var f = "请注明...";
    var h = e.getAttribute("req");
    if (h) {
        f = "请注明...[必填]";
    }
    if (langVer == 1) {
        f = "Please specify";
    }
    var d = e.fillvalue || e.getAttribute("fillvalue") || "";
    matrixinput.value = d;
    if (!d) {
        matrixinput.value = f;
    }
    var b = getPreviousNode(e);
    var c = getTop(b);
    var k = c.y - 35;
    var a = c.x - 190;
    divMatrixRel.style.top = k + "px";
    divMatrixRel.style.left = a + "px";
    divMatrixRel.style.display = "";
}
divMatrixRel.onclick = function(a) {
    if (curMatrixFill) {
        var b = curMatrixFill.parent.parent;
        if (b && b.removeError) {
            b.removeError();
        }
    }
    stopPropa(a);
};
matrixinput.onkeyup = matrixinput.onblur = matrixinput.onfocus = function(a) {
    if (curMatrixFill) {
        var b = this.value;
        if (b.indexOf("请注明...") == 0 || b.indexOf("Please specify") == 0) {
            this.value = b = "";
        }
        curMatrixFill.fillvalue = trim(b);
    }
};
function refresh_validate() {

	if (window.useAliVerify) {
		ncCaptchaObj.reset();
	}
    
}
function enter_clicksub(a) {
    a = a || window.event;
    if (a && a.keyCode == 13) {
        submit(1);
    }
}
var relationHT = new Array();
var relationQs = new Object();
var relationGroup = new Array();
var relationGroupHT = new Object();
var relationNotDisplayQ = new Object();
var nextPageAlertText = "";
var hasMaxtime = false;
var imgVerify = null;
var isEdtData = false;
var shopHT = new Array();
Init();
function showSubmitTable(a) {
    submit_table.style.display = a ? "": "none";
}
function Init() {

    pageHolder = $$tag("fieldset", survey);

    submit_button.onmouseover = function() {
        this.className = "submitbutton submitbutton_hover";
    };
    submit_button.onclick = function() {
        if (checkDisalbed()) {
            return;
        }
        submit(1);
    };
    showSubmitTable(true);
   
    for (var L = 0; L < pageHolder.length; L++) {
        var X = $$tag("div", pageHolder[L]);

        var ao = new Array();
        var v = new Array();
        var G = 0;
        for (var N = 0; N < X.length; N++) {
            var f = X[N].className.toLowerCase();
            if (f == "div_question") {
                var aJ = X[N].getAttribute("istrap") == "1";
                X[N].onclick = divQuestionClick;
                if (aJ) {
                    X[N].isTrap = true;
                    trapHolder.push(X[N]);
                    initItem(X[N]);
                    X[N].pageIndex = L + 1;
                } else {
                    X[N].indexInPage = G;
                    ao[G] = X[N];
                    ao[G].pageIndex = L + 1;
                    G++;
                    totalQ++;
                }
            } else {
                if (X[N].id && X[N].id.indexOf("divCut") == 0) {
                    v.push(X[N]);
                }
            }
        }
        pageHolder[L].questions = ao;
        pageHolder[L].cuts = v;
    }
    set_data_fromServer(qstr);
    var P = new Array();
    for (var am = 0; am < pageHolder.length; am++) {
        var b = pageHolder[am].questions;
        for (var N = 0; N < b.length; N++) {
            var m = b[N].dataNode;
            var T = m._type;
            var aC = b[N].getAttribute("relation");
            var a = b[N].getAttribute("isshop");
            if (a == "1") {
                b[N].isShop = true;
                shopHT.push(b[N]);
            }
            if (aC && aC != "0") {
                if (aC.indexOf("|") != -1) {
                    var K = aC.split("|");
                    for (var L = 0; L < K.length; L++) {
                        var aj = K[L];
                        if (!aj || aj.indexOf(",") == -1) {
                            continue;
                        }
                        var l = aj.split(",");
                        var ac = l[0];
                        var az = l[1].split(";");
                        for (var aB = 0; aB < az.length; aB++) {
                            var y = ac + "," + az[aB];
                            if (!relationGroupHT[y]) {
                                relationGroupHT[y] = new Array();
                            }
                            relationGroupHT[y].push(b[N]);
                        }
                        if (!relationQs[ac]) {
                            relationQs[ac] = new Array();
                        }
                        relationQs[ac].push(b[N]);
                        if (relationGroup.indexOf(ac) == -1) {
                            relationGroup.push(ac);
                        }
                    }
                } else {
                    var l = aC.split(",");
                    var ac = l[0];
                    var az = l[1].split(";");
                    for (var aB = 0; aB < az.length; aB++) {
                        var y = ac + "," + az[aB];
                        if (!relationHT[y]) {
                            relationHT[y] = new Array();
                        }
                        relationHT[y].push(b[N]);
                    }
                    if (!relationQs[ac]) {
                        relationQs[ac] = new Array();
                    }
                    relationQs[ac].push(b[N]);
                }
                relationNotDisplayQ[m._topic] = "1";
            } else {
                if (aC == "0") {
                    relationNotDisplayQ[m._topic] = "1";
                }
            }
            if (T != "page" && T != "cut") {
                questionsObject[m._topic] = b[N];
            }
            var d = b[N].getAttribute("titletopic");
            if (d) {
                var an = questionsObject[d];
                if (an) {
                    if (!an.dataNode._titleTopic) {
                        an.dataNode._titleTopic = new Array();
                    }
                    an.dataNode._titleTopic.push(m._topic);
                    var R = document.getElementById("divTitle" + m._topic);
                    if (R) {
                        R.innerHTML = R.innerHTML.replace("[q" + d + "]", "<span id='spanTitleTopic" + m._topic + "' style='text-decoration:underline;'></span>");
                    }
                }
            }
            if (b[N].getAttribute("hrq") == "1") {
                continue;
            }
            if (T == "radio" || T == "check") {
                if (T == "radio" && isRadioImage(m._mode)) {
                    initLikertItem(b[N]);
                } else {
                    initItem(b[N]);
                    checkPeiE(b[N]);
                }
            }
            if (T == "fileupload") {
                var ay = $$tag("iframe", b[N]);
                var X = $$tag("div", b[N]);
                for (var M = 0; M < X.length; M++) {
                    if (X[M].className.toLowerCase() == "uploadmsg") {
                        b[N].uploadmsg = X[M];
                        X[M].style.color = "red";
                        break;
                    }
                }
                var ah = null;
                for (var E = 0; E < ay.length; E++) {
                    if (ay[E].id && ay[E].id.indexOf("uploadFrame") == 0) {
                        ah = ay[E];
                        break;
                    }
                }
                b[N].uploadFinish = function(q, aL, k) {
                    this.uploadmsg.innerHTML = q;
                    if (k) {
                        this.uploadmsg.innerHTML += "<div><img src='" + k + "' alt='' /></div>";
                    }
                    this.fileName = aL;
                    isUploadingFile = false;
                    this.uploadFrame.style.display = "";
                    var aa = document.frames ? document.frames[this.uploadFrame.id] : document.getElementById(this.uploadFrame.id).contentWindow;
                    aa.curdiv = this;
                    aa._ext = this.dataNode._ext;
                    updateProgressBar(this.dataNode);
                    jump(this, this.uploadFrame);
                };
                if (ah) {
                    b[N].uploadFrame = ah;
                    b[N].uploadFrame.allowTransparency = true;
                    var W = document.frames ? document.frames[ah.id] : document.getElementById(ah.id).contentWindow;
                    W.curdiv = b[N];
                    W._ext = m._ext;
                    var ax = ah.getAttribute("fn");
                    if (ax && ax != "(空)") {
                        b[N].uploadFinish("文件已经成功上传！", ax);
                    }
                }
            }
            if (T == "matrix") {
                var ad = m._mode;
                if (m._hasjump) {
                    if (ad && ad - 100 < 0) {
                        initLikertItem(b[N]);
                    } else {
                        initItem(b[N]);
                    }
                }
                var aG = b[N].getAttribute("DaoZhi");
                var au = null;
                if (!aG) {
                    au = $$tag("tr", b[N]);
                } else {
                    var t = $$tag("tr", b[N]);
                    au = new Array();
                    var H = t[0].cells.length - 1;
                    for (var p = 0; p < H; p++) {
                        au[p] = t[0].cells[p + 1];
                        au[p].itemInputs = new Array();
                    }
                    for (var p = 0; p < H; p++) {
                        for (var at = 0; at < t.length; at++) {
                            au[p].parent = b[N];
                            var u = t[at].cells[p + 1];
                            u.parent = au[p];
                            u.onclick = function() {
                                if (curMatrixItem != this.parent) {
                                    var q = this.parent.itemInputs;
                                    if (q) {
                                        for (var aa = 0; aa < q.length; aa++) {
                                            q[aa].parentNode.style.background = "#edfafe";
                                        }
                                    }
                                    if (curMatrixItem && curMatrixItem.daoZhi) {
                                        q = curMatrixItem.itemInputs;
                                        for (var aa = 0; aa < q.length; aa++) {
                                            q[aa].parentNode.style.background = "";
                                        }
                                    }
                                    divMatrixItemClick.call(this.parent);
                                }
                                if (this.parent.parent) {
                                    var k = this.parent.parent.dataNode;
                                    if (k._maxvalue) {
                                        k._maxValue = k._maxvalue;
                                    }
                                    if (k._minvalue) {
                                        k._minValue = k._minvalue;
                                    }
                                    checkMinMax(this.getElementsByTagName("input")[0], this.parent.parent.dataNode, this.parent);
                                }
                            };
                            au[p].daoZhi = true;
                            var n = u.getElementsByTagName("input")[0];
                            if (n) {
                                au[p].itemInputs.push(n);
                            }
                        }
                    }
                }
                if (!aG) {
                    for (var M = 0; M < au.length; M++) {
                        var U = au[M].style.display == "none";
                        if (!U) {
                            if (ad && ad - 100 < 0) {
                                initLikertItem(au[M]);
                            } else {
                                if (!aG) {
                                    initItem(au[M]);
                                }
                            }
                        }
                        au[M].parent = b[N];
                        au[M].onclick = divMatrixItemClick;
                    }
                }
                if (ad == "102") {
                    var ai = b[N].getAttribute("minvalue");
                    var ae = b[N].getAttribute("maxvalue");
                    b[N].dataNode._minvalue = ai;
                    b[N].dataNode._maxvalue = ae;
                }
                if (au.length > 0) {
                    b[N].itemTrs = au;
                }
            }
            if (T == "sum") {
                initItem(b[N]);
                var au = $$tag("tr", b[N]);
                var C = new Array();
                for (var M = 0; M < au.length; M++) {
                    var e = au[M].getAttribute("rowid");
                    if (e) {
                        au[M].parent = b[N];
                        C.push(au[M]);
                    }
                }
                var S = b[N].itemInputs.length;
                var c = b[N].itemInputs;
                for (var M = 0; M < S; M++) {
                    c[M].onblur = function() {
                        txtChange(this);
                    };
                }
                if (C.length > 0) {
                    b[N].itemTrs = C;
                }
                var I = b[N].getAttribute("rel");
                b[N].relSum = document.getElementById(I);
            }
            if (T == "check" && m.isSort) {
                var O = $$tag("li", b[N]);
                for (M = 0; M < O.length; M++) {
                    O[M].onclick = itemSortClick;
                    O[M].style.cursor = "pointer";
                    O[M].onmouseover = function() {
                        this.style.background = "#efefef";
                    };
                    O[M].onmouseout = function() {
                        this.style.background = "";
                    };
                }
                var ab = O[0].parentNode.getAttribute("dval");
                var Y = new Array();
                for (var M = 0; M < b[N].itemInputs.length; M++) {
                    if (b[N].itemInputs[M].type == "checkbox") {
                        Y.push(b[N].itemInputs[M]);
                    }
                }
                if (ab) {
                    var w = ab.split(",");
                    for (var M = 0; M < w.length; M++) {
                        for (var L = 0; L < O.length; L++) {
                            if (Y[L].value == w[M]) {
                                O[L].onclick();
                                break;
                            }
                        }
                    }
                }
            }
            if (T == "question") {
                var B = $$tag("textarea", b[N]);
                if (B.length > 0) {
                    B[0].onkeyup = function() {
                        txtChange(this);
                        referTitle(this.parent, this.value);
                    };
                    if (!B[0].onclick) {
                        B[0].onclick = B[0].onkeyup;
                    }
                    B[0].onblur = B[0].onchange = function() {
                        txtChange(this, 1);
                        referTitle(this.parent, this.value);
                    };
                    B[0].parent = b[N];
                    b[N].itemTextarea = B[0];
                } 
            } else {
                if (T == "gapfill") {
                    var B = $$tag("input", b[N]);
                    for (var x = 0; x < B.length; x++) {
                        B[x].parent = b[N];
                        B[x].onkeyup = function() {
                            txtChange(this);
                        };
                        if (!B[x].onclick) {
                            B[x].onclick = B[x].onkeyup;
                        }
                        B[x].onblur = B[x].onchange = function() {
                            txtChange(this, 1);
                        };
                    }
                    b[N].gapFills = B;
                }
            }
            if (T == "radio_down") {
                var J = $$tag("select", b[N]);
                if (J.length > 0) {
                    J[0].onchange = itemClick;
                    J[0].parent = b[N];
                    b[N].itemSel = J[0];
                }
            }
            var s = $$tag("div", b[N]);
            var aI = 0;
            var aK = null;
            for (M = 0; M < s.length; M++) {
                if (s[M].className.toLowerCase() == "div_title_question") {
                    b[N].divTitle = s[M];
                } else {
                    if (s[M].className.toLowerCase() == "slider") {
                        if (T == "matrix" || T == "sum") {
                            aK = s[M].parentNode.parentNode;
                            aI++;
                        } else {
                            if (T == "slider") {
                                aK = b[N];
                            }
                        }
                        aK.divSlider = s[M];
                        s[M].parent = aK;
                        var ai = s[M].getAttribute("minvalue");
                        var ae = s[M].getAttribute("maxvalue");
                        b[N].dataNode._minvalue = ai;
                        b[N].dataNode._maxvalue = ae;
                        var af;
                        if (T == "sum") {
                            af = aK.getElementsByTagName("input")[0];
                        } else {
                            var A = s[M].getAttribute("rel");
                            af = document.getElementById(A);
                        }
                        var aA = new neverModules.modules.slider({
                            targetId: s[M].id,
                            sliderCss: "imageSlider1",
                            barCss: "imageBar1",
                            min: parseInt(ai),
                            max: parseInt(ae),
                            sliderValue: af,
                            hints: slider_hint,
                            change: itemClick
                        });
                        aA.create();
                        aK.sliderImage = aA;
                        var aD = s[M].getAttribute("defvalue");
                        if (aD && isInt(aD)) {
                            aA.setValue(parseInt(aD));
                            aK.divSlider.value = parseInt(aD);
                            if (T == "sum") {
                                b[N].sumLeft = 0;
                            }
                        }
                    }
                }
            }
            if (T == "matrix") {
                var aE = new Array();
                var au = b[N].itemTrs;
                if (au) {
                    for (var M = 0; M < au.length; M++) {
                        var at = au[M].getAttribute("rindex");
                        if (parseInt(at) == at) {
                            aE.push(au[M]);
                        }
                    }
                    if (aE.length > 0) {
                        b[N].itemTrs = aE;
                    }
                }
            }
            if (m && m._hasjump) {
                cur_page = am;
                clearAllOption(b[N]);
                cur_page = 0;
            }
            if (m._referedTopics) {
                P.push(b[N]);
            }
        }
    }
    completeLoaded = true;


    for (var N = 0; N < P.length; N++) {
        var G = P[N];
        createItem(G);
    }
    
    for (var am = 0; am < pageHolder.length; am++) {
        var b = pageHolder[am].questions;
        for (var N = 0; N < b.length; N++) {
            var m = b[N].dataNode;
            var aH = m._topic;
            if (relationQs[aH]) {
                relationJoin(b[N]);
            }
            var ar = b[N].getAttribute("qingjing");
            if (b[N].style.display == "" && ar) {
                var ag = b[N].getElementsByTagName("input")[0];
                ag.checked = true;
                displayRelationRaidoCheck(b[N], m);
            }
        }
    }

    if (totalQ == 0) {
        showSubmitTable(false);
    }
    processMinMax();
    showProgressBar();
    if (window.hasQJump && window.jqLoaded) {
        jqLoaded();
    }
}
var prevPostion;
var resizedMax;
function getMaxTimeStr(c) {
    var d = "";
    var b = c;
    var a = parseInt(b / 3600);
    if (a) {
        if (a < 10) {
            d += "0";
        }
        d += a + ":";
        b = b % 3600;
    } else {
        d = "00:";
    }
    var e = parseInt(b / 60);
    if (e) {
        if (e < 10) {
            d += "0";
        }
        d += e + ":";
        b = b % 60;
    } else {
        d += "00:";
    }
    if (b < 0) {
        b = 0;
    }
    if (b) {
        if (b < 10) {
            d += "0";
        }
        d += b;
    } else {
        d += "00";
    }
    return d;
}
function processMinMax() {
    if (maxTimer) {
        clearInterval(maxTimer);
    }
    if (minTimer) {
        clearInterval(minTimer);
    }
}
function resizeMaxTime() {
    resizedMax = true;
    mmMaxTime();
}
function mmMaxTime() {
    var a = document.getElementById("mainCss");
    if (!a) {
        divMaxTime.style.right = "50px";
        return;
    }
    var b = getTop(a);
    divMaxTime.style.top = b.y + "px";
    divMaxTime.style.left = (b.x - 120) + "px";
}
function getPreviousNode(b) {
    var a = b.previousSibling;
    if (a && a.nodeType != 1) {
        a = a.previousSibling;
    }
    return a;
}
function getNextNode(b) {
    var a = b.nextSibling;
    if (a && a.nodeType != 1) {
        a = a.nextSibling;
    }
    return a;
}
function updateCart() {
    var k = "";
    var o = 0;
    var c = 0;
    for (var h = 0; h < shopHT.length; h++) {
        var m = shopHT[h];
        if (m.style.display == "none") {
            continue;
        }
        var q = m.itemInputs;
        for (var g = 0; g < q.length; g++) {
            var f = q[g];
            var e = parseInt(f.value);
            if (e == 0) {
                continue;
            }
            var d = f.parentNode.parentNode;
            var p = $$tag("div", d)[0].innerHTML;
            var b = $$tag("p", d)[0].getAttribute("price");
            var n = e * parseFloat(b);
            var a = '<li class="productitem"><span class="fpname">' + p + '</span><span class="fpnum">' + e + '</span><span class="fpprice">￥' + toFixed0d(n) + "</span></li>";
            k += a;
            o += n;
            c += e;
        }
    }
    k = "<ul class='productslist'>" + k + '</ul><div class="ftotalprice" style="position:relative;"><span style="position:absolute;left:60%;">' + c + '</span><span class="priceshow">￥' + toFixed0d(o) + "</span></div>";
    var l = document.getElementById("shopcart");
    l.innerHTML = k;
    l.style.display = o > 0 ? "": "none";
}
function toFixed0d(a) {
    return a.toFixed(2).replace(".00", "");
}
var hasPeiEFull = false;
function checkPeiE(f, g) {
    if (hasPeiEFull) {
        return;
    }
    if (!f.dataNode._requir) {
        return;
    }
    if (f.getAttribute("peie") == "1" && f.style.display == "") {
        var b = true;
        var a = f.itemInputs;
        for (var c = 0; c < a.length; c++) {
            var e = a[c].disabled;
            if (!e) {
                b = false;
                break;
            }
        }
        if (b) {
            hasPeiEFull = true;
        }
    }
    if (hasPeiEFull) {
        var d = document.getElementById("spanNotSubmit");
        if (!d) {
            var h = document.getElementById("divPeiE");
            h.style.display = "";
            h.innerHTML = "<div style='background:#FFE4C8;color:#3E3E3E;border-radius:8px; padding:8px 15px; margin: 15px auto;width: 650px; text-align: left; clear: both; font-size:14px;'><span id='spanNotSubmit'>此问卷配额已满，暂时不能填写！</span></div>";
        } else {
            d.innerHTML = "此问卷配额已满，暂时不能填写！";
        }
    }
}
function initItem(h) {
    var b = $$tag("input", h);
    if (b.length == 0) {
        b = $$tag("textarea", h);
    }
    for (var g = 0; g < b.length; g++) {
        b[g].parent = h;
        if (h.isShop) {
            var q = getPreviousNode(b[g]);
            q.rel = b[g];
            var a = getNextNode(b[g]);
            a.rel = b[g];
            a.onclick = function() {
                var w = parseInt(this.rel.value);
                var v = false;
                var t = 0;
                var c = this.rel.getAttribute("num");
                if (c) {
                    v = true;
                    t = parseInt(c);
                }
                if (v && w >= t) {
                    var u = "库存只剩" + t + "件，不能再增加！";
                    if (t <= 0) {
                        u = "已售完，无法添加";
                    }
                    popUpAlert(u);
                } else {
                    this.rel.value = w + 1;
                    updateCart();
                }
            };
            q.onclick = function() {
                var c = parseInt(this.rel.value);
                if (c < 1) {
                    return;
                }
                this.rel.value = c - 1;
                updateCart();
            };
            b[g].onchange = b[g].onblur = function() {
                if (!isInt(this.value) || this.value - 1 < 0) {
                    this.value = 0;
                }
                updateCart();
            };
            continue;
        }
        if (!b[g].onclick) {
            b[g].onclick = itemClick;
        }
        if (b[g].tagName == "TEXTAREA") {
            b[g].onchange = b[g].onblur = itemClick;
        }
        var s = b[g].getAttribute("rel");
        if (s) {
            var p = null;
            if (s == "psibling") {
                p = getPreviousNode(b[g]);
                b[g].onclick = itemClick;
            } else {
                p = document.getElementById(s);
            }
            p.itemText = b[g];
            b[g].choiceRel = p;
            b[g].onblur = fcInputboxBlur;
            if (h.dataNode && h.dataNode._referedTopics) {
                b[g].onchange = itemClick;
            }
            if (!b[g].value) {
                b[g].value = defaultOtherText;
            }
            b[g].style.color = "#999999";
            var o = b[g].getAttribute("req");
            if (o == "true") {
                p.req = true;
            } else {
                p.req = false;
            }
        }
        var m = "";
        if (h.dataNode && (h.dataNode._type == "radio" || h.dataNode._type == "check")) {
            m = b[g].getAttribute("rimg");
            if (m) {
                var r = document.getElementById(m);
                if (r) {
                    r.onclick = function(w) {
                        var v = this.getAttribute("pimg");
                        if (v) {
                            var t = document.createElement("img");
                            t.onload = function() {
                                var y = document.getElementById("divImgPop");
                                if (!y) {
                                    y = document.createElement("div");
                                    y.id = "divImgPop";
                                    document.body.appendChild(y);
                                }
                                var D = this.width;
                                var B = this.height;
                                var A = (document.documentElement.clientWidth || document.body.clientWidth) - 60;
                                var C = (document.documentElement.clientHeight || document.body.clientHeight) - 40;
                                var z, x;
                                var E = 0.9;
                                if (B > C * E) {
                                    x = C * E;
                                    z = x / B * D;
                                    if (z > A * E) {
                                        z = A * E;
                                    }
                                } else {
                                    if (D > A * E) {
                                        z = A * E;
                                        x = z / D * B;
                                    } else {
                                        z = D;
                                        x = B;
                                    }
                                }
                                y.innerHTML = "<img src='" + v + "' style='width:" + z + "px;' alt=''/>";
                                PDF_launch("divImgPop", z + 20, x + 20);
                            };
                            t.src = v;
                            stopPropa(w);
                        } else {
                            var c = this.getAttribute("irel");
                            if (c) {
                                var u = document.getElementById(c);
                                u.parentNode.onclick();
                            }
                        }
                    };
                } else {
                    var l = m;
                }
            }
        }
        var f = h.dataNode && (h.dataNode._type == "radio" || h.dataNode._type == "check") && !h.dataNode.isSort && (!h.dataNode.isRate || h.dataNode._mode == "101");
        if (h.isTrap || f) {
            var k = b[g].nextSibling;
            if (b[g].choiceRel) {
                var p = getPreviousNode(b[g]);
                if (p) {
                    if (p.tagName && p.tagName.toLowerCase() == "label") {
                        p.style.display = "inline-block";
                    }
                    b[g].style.position = "static";
                }
            } else {
                if (k != null) {
                    var n = b[g].parentNode;
                    if (!m) {
                        n.onmouseover = function() {
                            this.style.background = "#efefef";
                        };
                        n.onmouseout = function() {
                            this.style.background = "";
                        };
                    }
                    if (b[g].checked && b[g].type == "radio") {
                        var e = getPreviousNode(b[g]);
                        if (e && e.tagName.toLowerCase() == "a") {
                            h.prevARadio = e;
                        }
                    }
                    n.onclick = function(x) {
                        var w = this.getElementsByTagName("a")[0];
                        if (!w) {
                            return;
                        }
                        if (h.hasConfirm) {
                            return;
                        }
                        var c = w.getAttribute("rel");
                        if (!c) {
                            return;
                        }
                        var v = document.getElementById(c);
                        if (v.disabled) {
                            return;
                        }
                        var u = v.type == "radio";
                        if (u) {
                            w.className = "jqRadio jqChecked";
                            v.checked = true;
                        } else {
                            v.checked = !v.checked;
                            w.className = v.checked ? "jqCheckbox jqChecked": "jqCheckbox";
                        }
                        var t = null;
                        if (v.parent) {
                            t = v.parent.parent || v.parent;
                        }
                        itemClick.call(v);
                        if (u) {
                            if (t && t.prevARadio && t.prevARadio != w) {
                                t.prevARadio.className = "jqRadio";
                            }
                            t.prevARadio = w;
                        }
                    };
                }
            }
        } else {
            if (h.tagName == "TR" && (b[g].type == "radio" || b[g].type == "checkbox")) {
                var d = b[g].parentNode;
                d.style.cursor = "pointer";
                d.onclick = function(w) {
                    var c = this.getElementsByTagName("input");
                    var t = c[0];
                    var v = getPreviousNode(t);
                    if (t.type == "checkbox") {
                        t.checked = !t.checked;
                        t.onclick();
                        if (v) {
                            v.className = t.checked ? "jqCheckbox jqChecked": "jqCheckbox";
                        }
                    } else {
                        if (v) {
                            v.className = "jqRadio jqChecked";
                            var u = t.parent;
                            if (u.prevARadio && u.prevARadio != v) {
                                u.prevARadio.className = "jqRadio";
                            }
                            u.prevARadio = v;
                        }
                        t.checked = true;
                        t.onclick();
                        stopPropa(w);
                    }
                };
                d.onmouseover = function() {
                    this.style.background = "#efefef";
                };
                d.onmouseout = function() {
                    this.style.background = "";
                };
            }
        }
    }
    if (b.length > 0) {
        h.itemInputs = b;
    }
}
function initLikertItem(b) {
    var e = $$tag("li", b);
    var a = new Array();
    var f = false;
    var d;
    for (j = 0; j < e.length; j++) {
        var c = e[j].className.toLowerCase();
        if (e[j].className && (c.indexOf("off") > -1 || c.indexOf("on") > -1)) {
            e[j].onclick = itemLiClick;
            e[j].onmouseover = itemMouseOver;
            e[j].onmouseout = itemMouseOut;
            e[j].parent = b;
            a.push(e[j]);
            if (c.indexOf("on") > -1) {
                d = e[j];
            } else {
                if (c.indexOf("off") > -1 && d) {
                    d.parent.holder = d.value;
                }
            }
        }
    }
    if (e.length > 0) {
        if (d) {
            d.parent.holder = d.value;
        }
        b.itemLis = a;
    }
}
function referTitle(e, h) {
    var k = e.dataNode;
    if (!k._titleTopic) {
        return;
    }
    var b = "";
    if (h == undefined && e.itemInputs) {
        for (var d = 0; d < e.itemInputs.length; d++) {
            if (e.itemInputs[d].checked) {
                var g = getNextNode(e.itemInputs[d]);
                if (k.isSort) {
                    g = e.itemInputs[d].parentNode.getElementsByTagName("label")[0];
                }
                if (b) {
                    b += "&nbsp;";
                }
                b += g.innerHTML;
            }
        }
    } else {
        b = h || "";
    }
    for (var a = 0; a < k._titleTopic.length; a++) {
        var f = k._titleTopic[a];
        var c = document.getElementById("spanTitleTopic" + f);
        if (c) {
            c.innerHTML = b;
        }
    }
}
function getparentNode(b, a) {
    while (b.parentNode.tagName.toLowerCase() != a) {
        b = b.parentNode;
    }
    return b.parentNode;
}
function createItem(u) {
    var C = u.dataNode;
    var A = C._referedTopics.split(",");
    var G = new Array();
    for (var r = 0; r < u.itemInputs.length; r++) {
        if (u.itemInputs[r].checked) {
            G.push(u.itemInputs[r]);
        }
    }
    for (var r = 0; r < A.length; r++) {
        var v = A[r];
        var w = questionsObject[v];
        if (!w) {
            continue;
        }
        var a = false;
        var c = document.getElementById("divRef" + v);
        if (!c) {
            continue;
        }
        var g = 0;
        var D = [];
        var e = new Object();
        var p = null;
        var E = document.getElementById("lbl" + C._topic + "_1") ? true: false;
        switch (w.dataNode._type) {
        case "matrix":
        case "sum":
            var t = w.dataNode._mode;
            for (var z = 0; z < u.itemInputs.length; z++) {
                var d = u.itemInputs[z];
                if (!d.value || d.type != "checkbox") {
                    continue;
                }
                var x = parseInt(d.value) - 1 + g;
                if (!w.itemTrs[x]) {
                    break;
                }
                w.itemTrs[x].style.display = d.checked ? "": "none";
                if (d.checked && !w.itemTrs[x].hasInit && w.dataNode._type == "matrix") {
                    w.itemTrs[x].hasInit = true;
                    if (t && t - 100 < 0) {
                        initLikertItem(w.itemTrs[x]);
                    } else {
                        initItem(w.itemTrs[x]);
                    }
                }
                if (d.checked) {
                    a = true;
                    if (d.itemText) {
                        var H = d.itemText.value;
                        var l = w.itemTrs[x].getElementsByTagName("th")[0];
                        if (l) {
                            if (!l.span) {
                                l.span = document.createElement("span");
                                l.appendChild(l.span);
                            }
                            if (H && H != defaultOtherText) {
                                l.span.innerHTML = "[<span style='color:red;'>" + H + "</span>]";
                            } else {
                                l.span.innerHTML = "";
                            }
                        }
                    }
                }
                if (E) {
                    var n = w.itemTrs[x].getAttribute("group");
                    if (n) {
                        p = n;
                        D.push(n);
                    }
                    if (p) {
                        if (d.checked && !e[p]) {
                            e[p] = "1";
                        }
                    }
                }
            }
            if (g == 1) {
                w.itemTrs[0].style.display = a ? "": "none";
            }
            c.style.display = a ? "none": "";
            w.displayContent = a;
            w.referDiv = u;
            break;
        case "radio":
        case "check":
            var k = w.itemInputs;
            var B = new Object();
            for (var z = 0; z < u.itemInputs.length; z++) {
                var d = u.itemInputs[z];
                if (d.checked) {
                    B[d.value] = d;
                }
            }
            for (var z = 0; z < k.length; z++) {
                var d = k[z];
                if (d.type != "checkbox" && d.type != "radio") {
                    continue;
                }
                var o = B[d.value];
                var h = d.parentNode;
                if (h.tagName.toLowerCase() != "li") {
                    h = getparentNode(h, "li");
                }
                if (o) {
                    h.style.display = "";
                    a = true;
                    if (o.itemText && d.itemText) {
                        d.itemText.value = o.itemText.value;
                    }
                } else {
                    h.style.display = "none";
                }
                if (E) {
                    var n = h.getAttribute("group");
                    if (n) {
                        p = n;
                        D.push(n);
                    }
                    if (p) {
                        if (o && !e[p]) {
                            e[p] = "1";
                        }
                    }
                }
            }
            c.style.display = a ? "none": "";
            w.displayContent = a;
            break;
        }
        if (E) {
            for (var y = 0; y < D.length; y++) {
                var n = D[y];
                var f = e[n];
                var F = "lbl" + v + "_" + n;
                var s = document.getElementById(F);
                if (s) {
                    s.style.display = f ? "": "none";
                }
            }
        }
    }
}
var curMatrixItem = null;
function divMatrixItemClick() {
    if (curMatrixItem == this) {
        return;
    }
    if (curMatrixItem != null) {
        curMatrixItem.style.background = curMatrixItem.prevBackColor || "";
        if (curMatrixItem.daoZhi) {
            itemInputs = curMatrixItem.itemInputs;
            for (var a = 0; a < itemInputs.length; a++) {
                itemInputs[a].parentNode.style.background = "";
            }
        }
    }
    curMatrixItem = this;
    if (!this.parent) {
        return;
    }
    var b = this.parent.dataNode;
    updateProgressBar(b);
}
function divQuestionClick() {
    if (curdiv == this) {
        return;
    }
    showLeftBar();
    curdiv = this;
    if (curMatrixItem != null && curMatrixItem.parent != curdiv) {
        curMatrixItem.style.background = curMatrixItem.prevBackColor || "";
    }
    if (curMatrixItem != null && curMatrixItem.parent == curdiv) {
        this.style.background = "";
    }
    if (this.removeError) {
        this.removeError();
    }
    if (!completeLoaded) {
        curdiv = null;
    }
    if (this.itemTextarea && curdiv.parentNode && curdiv.parentNode.style.display != "none") {
        this.itemTextarea.focus();
    }
}
function showLeftBar() {
    if (window.divLeftBar && !hasDisplayed) {
        if (divProgressImg) {
            divProgressImg.style.visibility = "visible";
            document.getElementById("loadprogress").style.visibility = "visible";
        }
        if (divSave) {
            divSave.parentNode.style.visibility = "visible";
            divSave.parentNode.style.marginTop = "5px";
        }
        hasDisplayed = true;
        divLeftBar.style.background = "#ffffff";
    }
}
var loadcss = null;
var loadprogress = null;
function updateProgressBar(b) {
    var a = b._topic;
    if (a > MaxTopic) {
        MaxTopic = a;
    }
    if (!progressArray[a]) {
        joinedTopic++;
        progressArray[a] = true;
        showProgressBar(b);
    }
    setTimeout(function() {
        postHeight();
    },
    500);
}
function showProgressBar(d) {
    if (window.divProgressImg) {
        if (!loadcss) {
            loadcss = document.getElementById("loadcss");
        }
        if (!loadprogress) {
            loadprogress = document.getElementById("loadprogress");
        }
        var c = totalQ;
        var e = joinedTopic;

        var b = parseFloat(e) / c * 100;
        b = b || 0;
        if (b >= 70 && d && d._topic == totalQ) {
            b = 100;
        }
        var a = b + "%";
        loadcss.style.height = a;
        loadprogress.innerHTML = "&nbsp;&nbsp;" + b.toFixed(0) + "%";
    }
}
function checkMinMax(g, e, d) {
    if ((e._maxValue > 0 || e._minValue > 0)) {
        var c = d.itemInputs;
        var h = 0;
        for (var b = 0; b < c.length; b++) {
            if (c[b].checked) {
                h++;
            }
        }
        if (d.parent) {
            d = d.parent;
        }
        if (!d.divChecktip) {
            d.divChecktip = document.createElement("div");
            d.appendChild(d.divChecktip);
            d.divChecktip.style.color = "#666";
        }
        var a = "&nbsp;&nbsp;&nbsp;您已经选择了" + h + "项";
        if (e._maxValue > 0 && h > e._maxValue) {
            if (langVer == 0) {
                //popUpAlert("此题最多只能选择" + e._maxValue + "项");
            }
            g.checked = false;
            if (g.onclick) {
                g.onclick();
            }
            var f = getPreviousNode(g);
            if (f && f.tagName.toLowerCase() == "a") {
                f.className = "jqCheckbox";
            }
            g.checked = false;
            h--;
            a = "&nbsp;&nbsp;&nbsp;您已经选择了" + h + "项";
        } else {
            if (e._minValue > 0 && h < e._minValue) {
                a += ",<span style='color:red;'>少选择了" + (e._minValue - h) + "项</span>";
                if (g.checked && e._select[g.value - 1] && e._select[g.value - 1]._item_huchi) {
                    a = "";
                }
            }
        }
        if (langVer == 0) {
            d.divChecktip.innerHTML = a;
        }
    }
    return false;
}
function itemSortClick() {
    var e = this.getElementsByTagName("input")[0];
    var f = e.parent.parent || e.parent;
    hasAnswer = true;
    var k = f.dataNode;
    updateProgressBar(k);
    var b = e.checked;
    var l = this.parentNode.getElementsByTagName("li");
    var a = this.getElementsByTagName("span")[0];
    if (!b) {
        var d = 1;
        for (var c = 0; c < l.length; c++) {
            if (l[c].getElementsByTagName("input")[0].checked) {
                d++;
            }
        }
        a.innerHTML = d;
        a.className = "sortnum sortnum-sel";
        e.checked = true;
    } else {
        var d = a.innerHTML;
        for (var c = 0; c < l.length; c++) {
            if (!l[c].getElementsByTagName("input")[0].checked) {
                continue;
            }
            var h = l[c].getElementsByTagName("span")[0];
            var g = h.innerHTML;
            if (g - d > 0) {
                h.innerHTML = (g - 1);
            }
        }
        a.innerHTML = "";
        a.className = "sortnum";
        e.checked = false;
    }
    if (k._referedTopics) {
        createItem(f);
    }
    referTitle(f);
    displayRelationRaidoCheck(f, k);
    this.inputLi = e;
    checkMinMax(this, k, f);
    jump(f, this);
}
function checkMatrixMaxValue(e, c) {
    if (c && c.dataNode._maxvalue) {
        var a = e.parentNode.parentNode.getElementsByTagName("input");
        var d = 0;
        for (var b = 0; b < a.length; b++) {
            if (a[b].checked) {
                d++;
            }
        }
        if (d - c.dataNode._maxvalue > 0) {
            e.checked = false;
            return true;
        }
    }
    return false;
}
function stopPropa(a) {
    a = a || window.event;
    if (a) {
        if (a.stopPropagation) {
            a.stopPropagation();
        } else {
            a.cancelBubble = true;
        }
    }
}
function itemClick(h) {
    if (!this.parent) {
        return;
    }
    var c = this.parent.parent || this.parent;
    if (c.isTrap) {
        return;
    }
    if (c.hasConfirm) {
        return;
    }
    hasAnswer = true;
    var l = c.dataNode;
    updateProgressBar(l);
    if (this.itemText && this.itemText.onclick) {
        if (this.checked) {
            this.itemText.onclick();
        } else {
            if (this.itemText.onblur) {
                this.itemText.onblur();
            }
        }
    }
    if (this.type == "checkbox") {
        checkHuChi(c, this);
        if (this.itemText) {
            if (!this.checked) {
                this.itemText.pvalue = this.itemText.value;
                this.itemText.value = "";
            } else {
                this.itemText.value = this.itemText.pvalue || "";
            }
        }
        if (l._referedTopics) {
            createItem(c);
        }
        referTitle(c);
        showAnswer(c);
        displayRelationRaidoCheck(c, l);
        checkMinMax(this, l, c);
        jump(c, this);
        if (l._type == "matrix") {
            checkMatrixMaxValue(this, c);
            divMatrixRel.style.display = "none";
            var b = this.getAttribute("needfill");
            if (b && this.checked) {
                showMatrixFill(this);
            }
            if (c.removeError) {
                c.removeError();
            }
            stopPropa(h);
        }
    } else {
        if (this.type == "radio" || l._type == "slider" || (l._type == "matrix" && l._mode != "201")) {
            if (!l._requir && !c.hasClearHref) {
                addClearHref(c);
            }
            if (this.type == "radio") {
                if (l._type == "matrix") {
                    processRadioInput(this.parentNode.parentNode, this);
                    divMatrixRel.style.display = "none";
                    var b = this.getAttribute("needfill");
                    if (b) {
                        showMatrixFill(this);
                    }
                    if (c.removeError) {
                        c.removeError();
                    }
                } else {
                    processRadioInput(c, this);
                }
                referTitle(c);
                showAnswer(c);
            }
            displayRelationRaidoCheck(c, l);
            jump(c, this);
            if ((popUpindex == 0 && l._type == "matrix") || (l._type != "matrix" && itempopUpindex == 0 && l._mode && l._mode != "0")) {
                processSamecount(c, this);
            }
        } else {
            if (l._type == "matrix" && l._mode == "201") {
                var g = c.itemTrs;
                var d = 0;
                var m;
                for (var a = 0; a < g.length; a++) {
                    if (g[a].style.display == "none") {
                        continue;
                    }
                    d = validateMatrix(l, g[a], g[a].itemInputs[0]);
                    if (d && !m) {
                        m = g[a].itemInputs[0];
                        break;
                    }
                    txtChange(g[a], g[a].itemInputs[0]);
                }
                if (c.removeError) {
                    c.removeError();
                }
                if (m) {
                    c.errorControl = m;
                    validate_ok = writeError(c, verifyMsg, 3000);
                }
                if (c.dataNode._hasjump) {
                    var k = false;
                    for (var a = 0; a < g.length; a++) {
                        var f = g[a].itemInputs[0];
                        if (trim(f.value) != "") {
                            k = true;
                            break;
                        }
                    }
                    jumpAny(k, c);
                }
                stopPropa(h);
            } else {
                if (l._type == "sum") {
                    if (this.parent.sliderImage) {
                        sumClick(c, this.parent.sliderImage.sliderValue);
                    } else {
                        sumClick(c, this);
                    }
                } else {
                    if (this.type == "text") {
                        processTextR(this, c, l);
                        stopPropa(h);
                    } else {
                        if (this.nodeName == "SELECT") {
                            if (l._type == "check") {
                                return;
                            }
                            c.focus();
                            jump(c, this);
                            displayRelationDropDown(c, l);
                            var e = this.options[this.selectedIndex].text;
                            if (this.value == -2) {
                                e = "";
                            }
                            referTitle(c, e);
                        }
                    }
                }
            }
        }
    }
    postHeight();
}
var hasConfirmBtn = false;
function showAnswer(a) {
    if (!window.isChuangGuan) {
        return;
    }
    if (a.getAttribute("ceshi") != "1") {
        return;
    }
    if (a.confirmButton) {
        return;
    }
    var b = document.createElement("a");
    b.style.margin = "10px 0 0 20px";
    b.className = "sumitbutton cancle";
    a.insertBefore(b, a.lastChild);
    a.confirmButton = b;
    b.innerHTML = "确认";
    b.onclick = function() {
        if (!hasConfirmBtn) {
            if (!confirm("确认后答案将无法修改，确认吗？")) {
                return;
            }
        }
        a.hasConfirm = true;
        hasConfirmBtn = true;
        var d = true;
        var c = "";
        var g = a.itemInputs;
        for (var f = 0; f < g.length; f++) {
            var m = g[f].getAttribute("ans") == "1";
            if (m) {
                if (!g[f].checked) {
                    d = false;
                }
                var l = getNextNode(g[f]);
                var k = "";
                if (l) {
                    k = l.innerHTML;
                }
                if (/^[A-Z][\.、．\s]/.test(k)) {
                    k = k.substring(0, 1);
                }
                if (c) {
                    c += ",";
                }
                c += k;
            } else {
                if (g[f].checked) {
                    d = false;
                }
            }
            var l = getNextNode(g[f]);
            if (l.tagName.toLowerCase() == "label") {
                l.removeAttribute("for");
            }
        }
        if (!a.correctAnswer) {
            var n = document.createElement("div");
            n.style.margin = "10px 0 0 20px";
            n.style.fontSize = "16px";
            a.insertBefore(n, a.lastChild);
            a.correctAnswer = n;
        }
        var e = d ? "<span style='color:green;'>回答正确</span>": "<span style='color:red;'>回答错误，正确答案为：" + c + "</span>";
        a.correctAnswer.innerHTML = e;
        var h = document.getElementById("divjx" + a.id.replace("div", ""));
        if (h) {
            h.style.display = "";
        }
    };
}
var itempopUpindex = 0;
var popUpindex = 0;
function processSamecount(k, e) {

}
function processRadioInput(a, b) {
    if (a.prevRadio && a.prevRadio.itemText && a.prevRadio != b) {
        a.prevRadio.itemText.pvalue = a.prevRadio.itemText.value;
        a.prevRadio.itemText.value = "";
    }
    if (b.itemText && b != a.prevRadio) {
        b.itemText.value = b.itemText.pvalue || "";
    }
    a.prevRadio = b;
}
function processTextR(d, a, b) {
    if (d.choiceRel) {
        if (d.value == defaultOtherText) {
            d.value = "";
        }
        if (b._mode == 1 && d.choiceRel.type == "checkbox") {
            if (!d.choiceRel.checked) {
                d.parentNode.click();
            }
        } else {
            d.choiceRel.checked = true;
            if (b._type == "matrix" && b._mode == "102") {
                var e = checkMatrixMaxValue(d.choiceRel, a);
                if (e) {
                    if (d.blur) {
                        d.blur();
                    }
                    return;
                }
            }
            d.style.color = "#000000";
            d.style.background = "";
            if (b._referedTopics) {
                createItem(a);
            }
            if (d.choiceRel.type == "checkbox") {
                if (d.pvalue && !d.value) {
                    d.value = d.pvalue;
                }
                var c = getPreviousNode(d.choiceRel);
                if (c && c.tagName.toLowerCase() == "a") {
                    c.className = "jqCheckbox jqChecked";
                }
                checkHuChi(a, d.choiceRel);
                checkMinMax(d.choiceRel, b, a);
            } else {
                if (d.choiceRel.type == "radio") {
                    if (b._type == "matrix") {
                        processRadioInput(d.parentNode.parentNode, d.choiceRel);
                    } else {
                        var c = getPreviousNode(d.choiceRel);
                        if (c && c.tagName.toLowerCase() == "a") {
                            c.className = "jqRadio jqChecked";
                            if (a && a.prevARadio && a.prevARadio != c) {
                                a.prevARadio.className = "jqRadio";
                            }
                            a.prevARadio = c;
                        }
                        processRadioInput(a, d.choiceRel);
                    }
                }
            }
            displayRelationRaidoCheck(a, b);
            jump(a, d.choiceRel);
        }
    }
}
function checkHuChi(c, f) {
    if (!f.checked) {
        return;
    }
    var e = c.dataNode;
    if (!e.hasHuChi) {
        return;
    }
    var a = c.itemInputs;
    var d = e._select[f.value - 1]._item_huchi;
    for (var b = 0; b < a.length; b++) {
        if (a[b].type != "checkbox") {
            continue;
        }
        if (a[b] == f) {
            continue;
        }
        if (!a[b].checked) {
            continue;
        }
        if (d) {
            if (a[b].parentNode.onclick) {
                a[b].parentNode.onclick();
            }
            a[b].checked = false;
        } else {
            var g = e._select[a[b].value - 1]._item_huchi;
            if (g) {
                if (a[b].parentNode.onclick) {
                    a[b].parentNode.onclick();
                }
                a[b].checked = false;
            }
        }
    }
}
function relationJoin(b) {
    if (b.style.display != "none") {
        var c = b.dataNode;
        var a = c._type;
        if (a == "radio" || a == "check") {
            displayRelationRaidoCheck(b, c);
        } else {
            if (a == "radio_down") {
                displayRelationDropDown(b, c);
            }
        }
    }
}
function displayRelationRaidoCheck(k, m) {
    var e = m._topic;
    if (!relationQs[e]) {
        return;
    }
    k.hasDisplayByRelation = new Object();
    var l = -1;
    if (k.itemLis) {
        var f = k.itemLis;
        for (var g = 0; g < f.length; g++) {
            if (f[g].className.indexOf("on") > -1) {
                l = g + 1;
            }
        }
        for (var g = 0; g < f.length; g++) {
            var d = false;
            var b = f[g].value;
            var h = e + "," + b;
            if (l > -1 && b == l) {
                d = true;
            }
            displayByRelation(k, h, d);
        }
    } else {
        var f = k.itemInputs;
        for (var g = 0; g < f.length; g++) {
            var d = false;
            var b = f[g].value;
            var h = e + "," + b;
            if (f[g].checked) {
                d = true;
            }
            displayByRelation(k, h, d);
            var a = e + ",-" + b;
            if (relationGroup.indexOf(k.dataNode._topic) != -1) {
                var c = relationGroupHT[a] || relationGroupHT[a.replace(",", ",-")];
                displayByRelation(k, a, d, true);
            }
            displayByRelationNotSelect(k, a, d);
        }
    }
    loopJoinProgressQ(e);
}
function loopJoinProgressQ(b, e) {
    if (!relationQs[b]) {
        return;
    }
    for (var d = 0; d < relationQs[b].length; d++) {
        var c = relationQs[b][d];
        if (!c.dataNode) {
            continue;
        }
        var a = c.dataNode._topic;
        if (c.style.display == "none" && !progressArray[a]) {
            progressArray[a] = "jump";
            joinedTopic++;
        }
        loopJoinProgressQ(a, e);
    }
}
function displayRelationDropDown(f, h) {
    var c = h._topic;
    if (!relationQs[c]) {
        return;
    }
    var k = f.itemSel;
    var g = f.itemSel.value;
    f.hasDisplayByRelation = new Object();
    for (var d = 0; d < k.length; d++) {
        var b = false;
        var a = k[d].value;
        var e = c + "," + a;
        if (a == g) {
            b = true;
        }
        displayByRelation(f, e, b);
    }
    loopJoinProgressQ(c);
}
function displayByRelation(s, J, w, F) {
    var x = s.dataNode._topic;
    if (relationGroup.indexOf(x) != -1) {
        var t = relationGroupHT[J] || relationGroupHT[J.replace(",", ",-")];
        if (t) {
            for (var y = 0; y < t.length; y++) {
                var n = new Object();
                var a = "";
                if (t[y].dataNode) {
                    a = t[y].dataNode._topic;
                } else {
                    a = t[y].getAttribute("topic");
                }
                for (var g in relationGroupHT) {
                    for (var o = 0; o < relationGroupHT[g].length; o++) {
                        var H = "";
                        if (relationGroupHT[g][o].dataNode) {
                            H = relationGroupHT[g][o].dataNode._topic;
                        } else {
                            H = relationGroupHT[g][o].getAttribute("topic");
                        }
                        if (a == H) {
                            var D = g.split(",")[0];
                            if (!n[D]) {
                                n[D] = new Array();
                            }
                            n[D].push("q" + g.replace(",", "_"));
                        }
                    }
                }
                var E = true;
                for (var c in n) {
                    var q = false;
                    for (var o = 0; o < n[c].length; o++) {
                        var G = n[c][o].replace("-", "");
                        var l = n[c][o].replace("q", "").split("_");
                        var m = document.getElementById(G);
                        var b = document.getElementById("q" + l[0]);
                        var e = document.getElementById("div" + l[0]);
                        var v = false;
                        if (l[1] < 0 && m && m.parentNode.parentNode.tagName.toLowerCase() == "ul") {
                            var C = m.parentNode.parentNode.getElementsByTagName("input");
                            for (var B = 0; B < C.length; B++) {
                                if (C[B].checked) {
                                    v = true;
                                }
                            }
                        }
                        if (m && (l[1] > 0 == m.checked)) {
                            q = true;
                            if (l[1] < 0 && !v) {
                                q = false;
                            } else {
                                break;
                            }
                        } else {
                            if (!m && b && l[1] == b.value) {
                                q = true;
                                break;
                            } else {
                                if (!m && e) {
                                    var f = e.itemInputs || e.itemLis;
                                    if (f) {
                                        var p = -1;
                                        var u = 0;
                                        for (var z = 0; z < f.length; z++) {
                                            if (f[z].className && f[z].className.toLowerCase().indexOf("on") > -1) {
                                                p = z + 1;
                                            }
                                        }
                                        if (l[1] == p) {
                                            q = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (!q) {
                        E = false;
                        break;
                    }
                }
                var h = questionsObject[a];
                if (h) {
                    h.style.display = E ? "": "none";
                    if (!E) {
                        loopHideRelation(h);
                    } else {
                        if (progressArray[a] == "jump") {
                            progressArray[a] = false;
                            joinedTopic--;
                        }
                    }
                } else {
                    var I = document.getElementById("divCut" + (a.replace("c", "")));
                    if (I) {
                        I.style.display = E ? "": "none";
                    }
                }
            }
        }
    }
    var r = relationHT[J];
    if (!r) {
        return;
    }
    for (var A = 0; A < r.length; A++) {
        var d = "";
        if (r[A].dataNode) {
            d = r[A].dataNode._topic;
        } else {
            d = r[A].getAttribute("topic");
        }
        if (s.hasDisplayByRelation[d]) {
            continue;
        }
        if (!w && r[A].style.display != "none") {
            loopHideRelation(r[A]);
        } else {
            if (w) {
                r[A].style.display = "";
                if (r[A].getAttribute("isshop") == "1") {
                    updateCart(r[A]);
                }
                if (!F) {
                    s.hasDisplayByRelation[d] = "1";
                }
                if (progressArray[d] == "jump") {
                    progressArray[d] = false;
                    joinedTopic--;
                }
                if (relationNotDisplayQ[d]) {
                    relationNotDisplayQ[d] = "";
                }
            }
        }
    }
}
function displayByRelationNotSelect(c, f, b) {
    var d = relationHT[f];
    if (!d) {
        return;
    }
    for (var a = 0; a < d.length; a++) {
        var e = "";
        if (d[a].dataNode) {
            e = d[a].dataNode._topic;
        } else {
            e = d[a].getAttribute("topic");
        }
        if (c.hasDisplayByRelation[e]) {
            continue;
        }
        if (b && d[a].style.display != "none") {
            loopHideRelation(d[a]);
        } else {
            if (!b) {
                d[a].style.display = "";
                c.hasDisplayByRelation[e] = "1";
                if (progressArray[e] == "jump") {
                    progressArray[e] = false;
                    joinedTopic--;
                }
                if (relationNotDisplayQ[e]) {
                    relationNotDisplayQ[e] = "";
                }
            }
        }
    }
}
function loopHideRelation(a) {
    var c = "";
    if (a.dataNode) {
        c = a.dataNode._topic;
    } else {
        c = a.getAttribute("topic");
    }
    var b = relationQs[c];
    if (b) {
        for (var d = 0; d < b.length; d++) {
            loopHideRelation(b[d], false);
        }
    }
    clearAllOption(a);
    a.style.display = "none";
    if (a.getAttribute("isshop") == "1") {
        updateCart(a);
    }
    if (relationNotDisplayQ[c] == "") {
        relationNotDisplayQ[c] = "1";
    }
}
function sumClick(m, l, n) {
    var e = m.getElementsByTagName("input");
    var s = m.dataNode;
    updateProgressBar(s);
    if (e) {
        var r = e.length;
        var w = s._total;
        var c = w;
        var g = 0;
        var u;
        var t;
        var h = l.value;
        if (parseInt(h) < 0) {
            l.value = "";
        }
        var p = new Array();
        for (var q = 0; q < r; q++) {
            var k = e[q].value;
            var a = m.itemTrs[q];
            var x = a.sliderImage;
            if (a.style.display == "none") {
                k = "";
                e[q].value = "";
                continue;
            } else {
                u = e[q];
                t = x;
                e[q].sIndex = q;
                p.push(e[q]);
            }
            if (k && trim(k)) {
                if (isInt(k)) {
                    c = c - parseInt(k);
                    if (x._value == undefined) {
                        x.setValue(parseInt(h), true);
                    } else {
                        if (n && e[q] == l) {
                            x.setValue(parseInt(h), true);
                        }
                    }
                } else {
                    e[q].value = "";
                    g++;
                }
            } else {
                if (a.style.display == "none") {} else {
                    g++;
                }
            }
        }
        if (g == 1 && c >= 0) {
            t.setValue(c, true);
            u.value = c;
            c = 0;
        }
        var d = "";
        if (g == 0 && c != 0) {
            var f = parseInt(u.value) + c;
            if (f >= 0) {
                if (u != l) {
                    t.setValue(f, true);
                    u.value = f;
                    c = 0;
                } else {
                    if (p.length == 2) {
                        var b = w - parseInt(u.value);
                        var o = p[0].sIndex;
                        m.itemTrs[o].sliderImage.setValue(b);
                        p[0].value = b;
                        c = 0;
                    }
                }
            } else {
                d = "，<span style='color:red;'>" + sum_warn + "</span>";
            }
        }
        if (c == 0) {
            for (var q = 0; q < r; q++) {
                if (!e[q].value) {
                    e[q].value = "0";
                }
            }
        }
        m.sumLeft = c;
        if (m.relSum) {
            m.relSum.innerHTML = sum_total + "<b>" + w + "</b>" + sum_left + "<span style='color:red;font-bold:true;'>" + (w - c) + "</span>" + d;
        }
        jump(m, this);
    }
}
function jump(c, f) {
    var d = c.dataNode;
    var a = d._anytimejumpto;
    var e = d._hasjump;
    var b = d._referTopic;
    if (e && !b) {
        if (a > 0) {
            jumpAnyChoice(c);
        } else {
            if (a == 0 && d._type != "radio" && d._type != "radio_down") {
                jumpAnyChoice(c);
            } else {
                jumpByChoice(c, f);
            }
        }
    }
}
function jumpAnyChoice(d, e) {
    var a = d.itemInputs || d.itemLis || d.itemTrs || d.gapFills;
    var c = false;
    if (a) {
        for (var b = 0; b < a.length; b++) {
            if (a[b].checked) {
                c = true;
            } else {
                if (a[b].className.indexOf("on") > -1) {
                    c = true;
                } else {
                    if (a[b].divSlider && a[b].divSlider.value) {
                        c = true;
                    } else {
                        if (a[b].tagName == "TEXTAREA" && trim(a[b].value) != "") {
                            c = true;
                        } else {
                            if (a[b].type == "text" && trim(a[b].value) != "") {
                                c = true;
                            } else {
                                if (a[b].itemSels) {
                                    for (var f = 0; f < a[b].itemSels.length; f++) {
                                        if (a[b].itemSels[f]) {
                                            c = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (c) {
                break;
            }
        }
    } else {
        if (d.itemSel) {
            if (d.itemSel.selectedIndex > 0) {
                c = true;
            } else {
                c = false;
            }
        } else {
            if (d.divSlider) {
                c = (d.divSlider.value != undefined && d.divSlider.value != null) ? true: false;
            } else {
                if (d.itemTextarea) {
                    c = trim(d.itemTextarea.value) != "";
                } else {
                    if (d.uploadFrame) {
                        c = d.fileName ? true: false;
                    }
                }
            }
        }
    }
    jumpAny(c, d, e);
}
function jumpByChoice(b, d) {
    var c = b.dataNode;
    if (d.value == "-2") {
        processJ(b.indexInPage - 0, 0);
    } else {
        if (d.value == "-1" || d.value == "") {
            processJ(b.indexInPage - 0, 0);
        } else {
            if ((c._type == "radio" || c._type == "radio_down") && parseInt(d.value) == d.value) {
                var a = c._select[d.value - 1]._item_jump;
                processJ(b.indexInPage - 0, a - 0);
            }
        }
    }
}
function txtChange(f, o) {
    var g = f.parent.parent || f.parent;
    updateProgressBar(g.dataNode);
    hasAnswer = true;
    if (g.removeError) {
        g.removeError();
    }
    var l = g.dataNode._verify;
    var m = g.dataNode._needOnly || f.getAttribute("needonly");
    var e = f.value;
    if (!e && o && o.value) {
        e = o.value;
    }
    if (!e) {
        e = "";
    }
    e = trim(e);

    if (g.dataNode._type == "matrix" && g.dataNode._mode == "201") {
        return;
    }

    if (g.dataNode._type == "gapfill") {
        var h = 0;
        h = validateMatrix(g.dataNode, f, f);
        if (h) {
            g.errorControl = f;
            writeError(g, verifyMsg, 3000);
        }
    }
    if (g.dataNode._type == "sum") {
        sumClick(g, f, 1);
    } else {
        jumpAny(e != "", g);
    }
}
function jumpAny(a, b, d) {
    var c = b.dataNode;
    if (c._hasjump) {
        if (a) {
            processJ(b.indexInPage - 0, c._anytimejumpto - 0, d);
        } else {
            processJ(b.indexInPage - 0, 0, d);
        }
    }
}
function processJ(o, c, d) {
    var a = o + 1;
    var b = cur_page;
    var k = c == 1 || c == -1;
    var p = 0;
    for (var g = cur_page; g < pageHolder.length; g++) {
        var l = pageHolder[g].questions;
        if (k) {
            b = g;
        }
        if (!p && l[o] && l[o].dataNode) {
            p = parseInt(l[o].dataNode._topic);
        }
        for (var f = a; f < l.length; f++) {
            var h = l[f].dataNode._topic;
            if (h == c || k) {
                b = g;
            }
            if (l[f].getAttribute("nhide") == "1") {
                continue;
            }
            if (h < c || k) {
                l[f].style.display = "none";
                if (!progressArray[h]) {
                    joinedTopic++;
                    progressArray[h] = "jump";
                }
            } else {
                if (relationNotDisplayQ[h]) {
                    var e = 1;
                } else {
                    l[f].style.display = "";
                }
                if (progressArray[h] == "jump") {
                    joinedTopic--;
                    progressArray[h] = false;
                }
                if (l[f].dataNode._hasjump && !d) {
                    clearAllOption(l[f]);
                }
            }
        }
        for (var f = 0; f < pageHolder[g].cuts.length; f++) {
            var n = pageHolder[g].cuts[f];
            var h = n.getAttribute("qtopic");
            if (!h) {
                continue;
            }
            if (p && h <= p) {
                continue;
            } else {
                if (h <= a) {
                    continue;
                }
            }
            if (h < c || k) {
                n.style.display = "none";
            } else {
                var m = n.getAttribute("topic");
                if (relationNotDisplayQ[m]) {
                    var e = 1;
                } else {
                    n.style.display = "";
                }
            }
        }
        a = 0;
    }
    if (c == 1) {
        joinedTopic = totalQ;
    }
    showProgressBar();
}
function addClearHref(b) {

    var c = b.dataNode;
    var a = document.createElement("a");
    a.title = validate_info_submit_title2;
    a.className = "link-999";
    a.style.marginLeft = "25px";
    a.innerHTML = "[" + type_radio_clear + "]";
    a.href = "javascript:void(0);";
    b.hasClearHref = true;
    b.divTitle.appendChild(a);
    b.clearHref = a;
    a.onclick = function() {
        clearAllOption(b);
        referTitle(b);
        jumpAny(false, b);
    };
}
function clearAllOption(d) {
    var e = d.itemSel;
    if (e) {
        e.selectedIndex = 0;
    }
    if (d.divSlider && d.divSlider.value != undefined) {
        d.sliderImage.setValue(d.dataNode._minvalue, true);
        d.divSlider.value = undefined;
    }
    var a = d.itemInputs || d.itemLis || d.itemTrs;
    if (!a) {
        return;
    }
    if (d.getAttribute("qingjing") == "1") {
        return;
    }
    d.hasClearHref = false;
    if (d.clearHref) {
        d.clearHref.parentNode.removeChild(d.clearHref);
        d.clearHref = null;
    }
    for (var c = 0; c < a.length; c++) {
        if (a[c].checked) {
            a[c].checked = false;
            var f = getPreviousNode(a[c]);
            if (f && f.tagName.toLowerCase() == "a") {
                f.className = f.className.replace("jqChecked", "");
            }
        } else {
            if (a[c].className.toLowerCase().indexOf("on") == 0) {
                a[c].className = "off" + d.dataNode._mode;
            } else {
                if (a[c].parent && a[c].parent.holder) {
                    a[c].parent.holder = 0;
                } else {
                    if (a[c].divSlider && a[c].divSlider.value) {
                        a[c].sliderImage.setValue(d.dataNode._minvalue, true);
                        a[c].divSlider.value = undefined;
                    } else {
                        var g = a[c].itemInputs || a[c].itemLis;
                        if (g) {
                            for (var b = 0; b < g.length; b++) {
                                if (g[b].checked) {
                                    g[b].checked = false;
                                    var f = getPreviousNode(g[b]);
                                    if (f && f.tagName.toLowerCase() == "a") {
                                        f.className = f.className.replace("jqChecked", "");
                                    }
                                } else {
                                    if (g[b].className.toLowerCase().indexOf("on") == 0) {
                                        g[b].className = "off" + d.dataNode._mode;
                                    } else {
                                        if (g[b].parent && g[b].parent.holder) {
                                            g[b].parent.holder = 0;
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
    if (d.holder) {
        d.holder = 0;
    }
}
function itemMouseOver() {
    var c = this.parent.parent || this.parent;
    if (c.dataNode.isRate) {
        var a = this.parent.itemLis.length;
        var d = "on";
        for (var b = 0; b < a; b++) {
            d = b < this.value ? "on": "off";
            this.parent.itemLis[b].className = d + c.dataNode._mode;
        }
    }
}
function itemMouseOut() {
    var d = this.parent.parent || this.parent;
    if (d.dataNode.isRate) {
        var a = this.parent.itemLis.length;
        var e = "on";
        var c = this.parent.holder || 0;
        for (var b = 0; b < a; b++) {
            e = b < c ? "on": "off";
            this.parent.itemLis[b].className = e + d.dataNode._mode;
        }
    }
}
function itemLiClick() {
    var b = this.parent.parent || this.parent;
    var c = b.dataNode;
    updateProgressBar(c);
    if (c.isRate) {
        this.parent.holder = this.value;
        for (var a = 0; a < this.value; a++) {
            this.parent.itemLis[a].className = "on" + c._mode;
        }
        if (!c._requir && !b.hasClearHref) {
            addClearHref(b);
        }
        displayRelationRaidoCheck(b, c);
        jump(b, this);
        if (itempopUpindex == 0) {
            processSamecount(b, this);
        }
    }
}
function set_data_fromServer(d) {
    var m = new Array();
    m = d.split("¤");
    var h = m[0];
    var a = h.split("§");
    hasTouPiao = a[0] == "true";
    useSelfTopic = a[1] == "true";
    var q = 0;
    var n = 0;
    var l = true;
    var p = 0;
    for (var k = 1; k < m.length; k++) {
        var f = new Object();
        var e = m[k].split("§");
        switch (e[0]) {
        case "page":
            if (l) {
                l = false;
            } else {
                n++;
            }
            p = 0;
            if (e[2] == "true") {
                pageHolder[n]._iszhenbie = true;
            } else {
                if (e[2] == "time") {
                    pageHolder[n]._istimer = true;
                }
            }
            pageHolder[n]._mintime = e[3] ? parseInt(e[3]) : "";
            pageHolder[n]._maxtime = e[4] ? parseInt(e[4]) : "";
            break;
        case "question":
            f._type = trim(e[0]);
            f._topic = trim(e[1]);
            f._height = trim(e[2]);
            f._maxword = trim(e[3]);
            if (e[4] == "true") {
                f._requir = true;
            } else {
                f._requir = false;
            }
            if (e[5] == "true") {
                f._norepeat = true;
            } else {
                f._norepeat = false;
            }
            if (trim(e[6]) == "true") {
                f._hasjump = true;
            } else {
                f._hasjump = false;
            }
            f._anytimejumpto = trim(e[7]);
            f._verify = trim(e[8]);
            f._needOnly = e[9] == "true" ? true: false;
            f._hasList = e[10] == "true" ? true: false;
            f._listId = e[11] ? parseInt(e[11]) : -1;
            f._minword = e[12];
            pageHolder[n].questions[p].dataNode = f;
            p++;
            break;
        case "slider":
            f._type = trim(e[0]);
            f._topic = trim(e[1]);
            if (e[2] == "true") {
                f._requir = true;
            } else {
                f._requir = false;
            }
            f._minvalue = trim(e[3]);
            f._maxvalue = trim(e[4]);
            if (trim(e[5]) == "true") {
                f._hasjump = true;
            } else {
                f._hasjump = false;
            }
            f._anytimejumpto = trim(e[6]);
            pageHolder[n].questions[p].dataNode = f;
            p++;
            break;
        case "fileupload":
            f._type = trim(e[0]);
            f._topic = trim(e[1]);
            if (e[2] == "true") {
                f._requir = true;
            } else {
                f._requir = false;
            }
            f._maxsize = trim(e[3]);
            f._ext = trim(e[4]);
            if (trim(e[5]) == "true") {
                f._hasjump = true;
            } else {
                f._hasjump = false;
            }
            f._anytimejumpto = trim(e[6]);
            pageHolder[n].questions[p].dataNode = f;
            p++;
            break;
        case "gapfill":
            f._type = trim(e[0]);
            f._topic = trim(e[1]);
            if (e[2] == "true") {
                f._requir = true;
            } else {
                f._requir = false;
            }
            f._gapcount = trim(e[3]);
            if (trim(e[4]) == "true") {
                f._hasjump = true;
            } else {
                f._hasjump = false;
            }
            f._anytimejumpto = trim(e[5]);
            pageHolder[n].questions[p].dataNode = f;
            p++;
            break;
        case "sum":
            f._type = trim(e[0]);
            f._topic = trim(e[1]);
            if (e[2] == "true") {
                f._requir = true;
            } else {
                f._requir = false;
            }
            f._total = parseInt(e[3]);
            if (trim(e[4]) == "true") {
                f._hasjump = true;
            } else {
                f._hasjump = false;
            }
            f._anytimejumpto = trim(e[5]);
            f._referTopic = e[6];
            pageHolder[n].questions[p].dataNode = f;
            p++;
            break;
        case "radio":
        case "check":
        case "radio_down":
        case "matrix":
            f._type = trim(e[0]);
            f._topic = trim(e[1]);
            f._numperrow = trim(e[2]);
            if (e[3] == "true") {
                f._hasvalue = true;
            } else {
                f._hasvalue = false;
            }
            if (e[4] == "true") {
                f._hasjump = true;
            } else {
                f._hasjump = false;
            }
            f._anytimejumpto = e[5];
            f._mode = trim(e[9]);
            if (e[0] != "check") {
                if (e[6] == "true") {
                    f._requir = true;
                } else {
                    f._requir = false;
                }
                f.isSort = false;
                f.isRate = isRadioRate(f._mode);
            } else {
                var o = e[6].split(",");
                f._minValue = 0;
                f._maxValue = 0;
                if (o[0] == "true") {
                    f._requir = true;
                } else {
                    f._requir = false;
                }
                if (o[1] != "") {
                    f._minValue = Number(o[1]);
                }
                if (o[2] != "") {
                    f._maxValue = Number(o[2]);
                }
                f.isSort = f._mode != "" && f._mode != "0";
                f.isRate = false;
            }
            if (e[7] == "true") {
                f._isTouPiao = true;
            } else {
                f._isTouPiao = false;
            }
            f._verify = trim(e[8]);
            f._referTopic = e[10];
            f._referedTopics = e[11];
            var c = 12;
            f._select = new Array();
            for (var g = c; g < e.length; g++) {
                f._select[g - c] = new Object();
                var b = e[g].split("〒");
                if (b[0] == "true") {
                    f._select[g - c]._item_radio = true;
                } else {
                    f._select[g - c]._item_radio = false;
                }
                f._select[g - c]._item_value = trim(b[1]);
                f._select[g - c]._item_jump = trim(b[2]);
                f._select[g - c]._item_huchi = b[3] == "true";
                if (f._select[g - c]._item_huchi) {
                    f.hasHuChi = true;
                }
            }
            pageHolder[n].questions[p].dataNode = f;
            p++;
            break;
        default:
            break;
        }
    }
}

function checkDisalbed() {
    curdiv = null;
    if (!submit_button.disabled) {
        return false;
    }
    if (divMinTime.innerHTML) {
        var a = divMinTime.innerHTML.replace(/<.+?>/gim, "");
        popUpAlert(a);
    }
    return true;
}

var havereturn = false;
var timeoutTimer = null;
var errorTimes = 0;
var hasSendErrorMail = false;
function processError(e, c, b, a) {
    if (!havereturn) {
        havereturn = true;
        var d = "";
        var f = encodeURIComponent(e);
        if (f.length > 1800) {
            d = a + "&submitdata=exceed&partdata=" + f.substring(0, 1700);
        } else {
            d = a;
            if (a.indexOf("submitdata=") == -1) {
                d += "&submitdata=" + f;
            }
        }
        errorTimes++;
        if (errorTimes == 1 && !hasSendErrorMail) {
            d += "&nsd=1";
            hasSendErrorMail = true;
        }
        refresh_validate();
        submit_tip.style.display = "none";
        submit_div.style.display = "block";
    }
    prevsaveanswer = "";
    if (!window.submitWithGet) {
        window.submitWithGet = 1;
    }
    if (timeoutTimer) {
        clearTimeout(timeoutTimer);
    }
}
var prevsaveanswer = "";

function submit(o) {
    if (o != 2 && !validate()) {
        return;
    }
    submit_tip.innerHTML = validate_info_submit2;
    var A = 1;
    if (o == 0) {
        PromoteUser("正在处理，请稍候...", 3000, true);
    } else {
        if (o == 2) {
            A = cur_page;
        } else {
            if (o == 3) {
                PromoteUser("正在验证，请稍候...", 3000, true);
            } else {
                submit_tip.style.display = "block";
                submit_div.style.display = "none";
            }
        }
    }
    needCheckLeave = false;
    var h = sent_to_answer();
    if (o == 2 && prevsaveanswer == h) {
        return;
    }
    //console.log(h);  
    var B = getXmlHttp();
    var q = encodeURIComponent(h);
    var b = "POST";      
    var a = saveurl;
    var n = "submittype=" + o + "&t=" + (new Date()).valueOf()+"&subject_id="+subject_id+"&submitdata=" + q;
    console.log(n);  
    B.open(b, a, true);
    B.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");


    B.onreadystatechange = function() {
        if (B.readyState == 4) {
            clearTimeout(timeoutTimer);
            var e = B.status;
            if (e == 200) {
				data=eval("("+B.responseText+")");//将ThinkPHP的传回来的数据处理成JavaScript可处理的Json数组  
                afterSubmit(data, o);
                prevsaveanswer = h;
            } else {
                processError(h, e, o, n);
            }
        }
    };
    B.send(n);


    havereturn = false;
}

function getExpDate(d, a, c) {
    var b = new Date();
    if (typeof(d) == "number" && typeof(a) == "number" && typeof(a) == "number") {
        b.setDate(b.getDate() + parseInt(d));
        b.setHours(b.getHours() + parseInt(a));
        b.setMinutes(b.getMinutes() + parseInt(c));
        return b.toGMTString();
    }
}
function processRedirect(l) {
    var c = l[1];
    var b = l[3] || "";
    var g = l[2];
    var a = l[4] || "";
    if (!c || c[0] == "?") {
        c = window.location.href;
    } else {
        if (c.toLowerCase().indexOf("http://") == -1 && c.toLowerCase().indexOf("https://") == -1) {
            c = "http://" + c;
        }
    }
    var d = false;
    if (c.indexOf("{output}") > -1) {
        if (a) {
            c = c.replace("{output}", a);
        }
        d = true;
    }
    var k = 1000;
    if (g && g != "不提示" ) {
        PromoteUser(g, 5000, true);
        k = 2000;
    }
    setTimeout(function() {
        location.replace(c);
    },
    k);
}
var changeSave = false;
var nvvv = 0;
function afterSubmit(v, l) {
    havereturn = true;
    errorTimes = 0;
    clearTimeout(timeoutTimer);
    var m = v.split("〒");
    var k = m[0];	
    if (l == 0) {//正在处理
    } else {
        if (l == 2) {//换页
        } else {
            if (l == 3) {//正在验证

            } else {
                if (k == 10) {
                    var h = m[1];
                    location.replace(h);
                    return;
                } else {
					popUpAlert(m[1]);
				}
            }
        }
    }
    refresh_validate();
    submit_tip.style.display = "none";
    submit_div.style.display = "block";
    return;
	
}
var firstError = null;
var firstMatrixError = null;
var startAge = 0;
var endAge = 0;
var gender = 0;
var education = 0;
var marriage = 0;
var labelName = "";
var labelIndex = 0;
var rName = "";
function getAgeGenderLabel(d, a) {
    if (d._type == "radio" && a.itemInputs) {
        for (var c = 0; c < a.itemInputs.length; c++) {
            if (a.itemInputs[c].checked) {
                var b = getNextNode(a.itemInputs[c]);
                labelName = b.innerHTML;
                labelIndex = c;
                break;
            }
        }
    } else {
        if (d._type == "radio_down") {
            labelName = a.itemSel.options[a.itemSel.selectedIndex].text;
            labelIndex = a.itemSel.selectedIndex - 1;
        }
    }
}
function getRname(f, a) {
    if (rName) {
        return;
    }
    if (f._type != "question") {
        if (f._type == "matrix" && f._mode == "201") {
            var c = a.getElementsByTagName("th");
            for (var e = 0; e < c.length; e++) {
                if (c[e].innerHTML.indexOf("姓名") > -1 || (c[e].innerHTML.indexOf("姓") > -1 && c[e].innerHTML.indexOf("名") > -1)) {
                    var b = c[e].parentNode.getElementsByTagName("textarea");
                    if (b[0]) {
                        rName = b[0].value;
                    }
                    break;
                }
            }
        }
        return;
    }
    var g = a.divTitle.innerHTML;
    if (g.indexOf("姓名") == -1) {
        return;
    }
    var d = a.itemTextarea || a.itemInputs[0];
    if (d) {
        rName = d.value;
    }
}
function getAgeGender(c, a) {
    if (c._type != "radio" && c._type != "radio_down") {
        return;
    }
    var d = a.divTitle.innerHTML;
    if (d.indexOf("年龄") > -1) {
        getAgeGenderLabel(c, a);
        if (!labelName) {
            return;
        }
        var b = /[1-9][0-9]*/g;
        var e = labelName.match(b);
        if (!e || e.length == 0) {
            return;
        }
        if (e.length > 2) {
            return;
        }
        if (e.length == 2) {
            startAge = e[0];
            endAge = e[1];
        } else {
            if (e.length == 1) {
                if (labelIndex == 0) {
                    endAge = e[0];
                } else {
                    startAge = e[0];
                }
            }
        }
    } else {
        if (d.indexOf("性别") > -1) {
            getAgeGenderLabel(c, a);
            if (!labelName) {
                return;
            }
            if (labelName.indexOf("男") > -1) {
                gender = 1;
            } else {
                if (labelName.indexOf("女") > -1) {
                    gender = 2;
                }
            }
        } else {
            if (d.indexOf("学历") > -1 || d.indexOf("教育程度") > -1) {
                getAgeGenderLabel(c, a);
                if (!labelName) {
                    return;
                }
                if (labelName.indexOf("初中") > -1) {
                    education = 1;
                } else {
                    if (labelName.indexOf("高中") > -1 || labelName.indexOf("中专") > -1) {
                        education = 2;
                    } else {
                        if (labelName.indexOf("大专") > -1) {
                            education = 3;
                        } else {
                            if (labelName.indexOf("本科") > -1) {
                                education = 4;
                            } else {
                                if (labelName.indexOf("硕士") > -1) {
                                    education = 5;
                                } else {
                                    if (labelName.indexOf("博士") > -1) {
                                        education = 6;
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                if (d.indexOf("婚姻") > -1) {
                    getAgeGenderLabel(c, a);
                    if (!labelName) {
                        return;
                    }
                    if (labelName.indexOf("已婚") > -1) {
                        marriage = 1;
                    } else {
                        if (labelName.indexOf("未婚") > -1) {
                            marriage = 2;
                        } else {
                            if (labelName.indexOf("离婚") > -1) {
                                marriage = 3;
                            } else {
                                if (labelName.indexOf("再婚") > -1) {
                                    marriage = 4;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
function sent_to_answer() {
    var B = new Array();
    var H = 0;
    var al = new Object();
    var af = 1;
    for (var C = 0; C < pageHolder.length; C++) {
        var am = pageHolder[C].questions;
        var z = pageHolder[C]._maxtime > 0;
        for (var ak = 0; ak < am.length; ak++) {
            var P = am[ak].dataNode;
            var x = am[ak].style.display.toLowerCase() == "none" || (am[ak].dataNode._referTopic && !am[ak].displayContent ) || pageHolder[C].skipPage;
            var y = new Object();
            y._topic = P._topic;
            y._value = "";
            B[H++] = y;
            try {
                getAgeGender(P, am[ak]);
                getRname(P, am[ak]);
            } catch(aa) {}

            switch (P._type) {
            case "question":
                if (x) {
                    y._value = "(跳过)";
                    if (am[ak].getAttribute("hrq") == "1") {
                        y._value = "Ⅳ";
                    }
                    continue;
                }
                var ab = am[ak].itemTextarea || am[ak].itemInputs[0];
                var I = ab.value || "";
                if (I && ab.lnglat) {
                    I = I + "[" + ab.lnglat + "]";
                }
                y._value = replace_specialChar(I);
                break;
            case "gapfill":
                if (x) {
                    if (am[ak].getAttribute("hrq") == "1") {
                        y._value = "Ⅳ";
                        continue;
                    }
                }
                var m = am[ak].gapFills;
                for (var ah = 0; ah < m.length; ah++) {
                    if (ah > 0) {
                        y._value += spChars[2];
                    }
                    if (x) {
                        y._value += "(跳过)";
                    } else {
                        var I = trim(m[ah].value.substring(0, 3000));
                        if (I && m[ah].lnglat) {
                            I = I + "[" + m[ah].lnglat + "]";
                        }
                        y._value += replace_specialChar(I);
                    }
                }
                break;
            case "slider":
                var ac = am[ak].divSlider.value;
                if (x) {
                    y._value = "(跳过)";
                    continue;
                }
                y._value = ac == undefined ? "": ac;
                break;
            case "fileupload":
                var M = am[ak].fileName;
                if (x) {
                    y._value = "(跳过)";
                    continue;
                }
                y._value = M || "";
                break;
            case "sum":
                var D = am[ak].itemInputs;
                G = D.length;
                for (var ah = 0; ah < G; ah++) {
                    var U = D[ah];
                    var X = am[ak].relSum == 0 ? trim(U.value) || "0": trim(U.value);
                    if (am[ak].itemTrs[ah].style.display == "none") {
                        X = "Ⅳ";
                    }
                    if (ah > 0) {
                        y._value += spChars[2];
                    }
                    var A = am[ak].itemTrs[ah].getAttribute("rowid");
                    if (A) {
                        y._value += A + spChars[4];
                    }
                    y._value += X;
                }
                if (x) {
                    var ae = 0;
                    while (ae < G) {
                        if (ae == 0) {
                            y._value = "(跳过)";
                        } else {
                            y._value += spChars[2] + "(跳过)";
                        }
                        ae++;
                    }
                }
                break;
            case "radio":
            case "check":
                if (P.isSort) {
                    var r = new Array();
                    for (var ah = 0; ah < am[ak].itemInputs.length; ah++) {
                        if (am[ak].itemInputs[ah].type == "checkbox") {
                            var V = am[ak].itemInputs[ah].parentNode;
                            var W = V.getElementsByTagName("span")[0].innerHTML;
                            var e = new Object();
                            e.sIndex = W;
                            var O = am[ak].itemInputs[ah].value;
                            if (x) {
                                O = "-3";
                            } else {
                                if (!W) {
                                    O = "-2";
                                }
                            }
                            e.val = O;
                            if (am[ak].itemInputs[ah].checked && am[ak].itemInputs[ah].itemText) {
                                var ai = am[ak].itemInputs[ah].itemText.value;
                                if (ai == defaultOtherText) {
                                    ai = "";
                                }
                                e.val += spChars[2] + replace_specialChar(trim(ai.substring(0, 3000)));
                            }
                            if (!e.sIndex) {
                                e.sIndex = 10000;
                            }
                            r.push(e);
                        }
                    }
                    r.sort(function(t, k) {
                        return t.sIndex - k.sIndex;
                    });
                    for (var d = 0; d < r.length; d++) {
                        if (d > 0) {
                            y._value += ",";
                        }
                        y._value += r[d].val;
                    }
                    continue;
                }
                if (x) {
                    y._value = "-3";
                    if (am[ak].getAttribute("hrq") == "1") {
                        y._value = "-4";
                    }
                    continue;
                }
                var L = am[ak].itemInputs || am[ak].itemLis;
                var E = -1;
                var Y = 0;
                for (var ah = 0; ah < L.length; ah++) {
                    if (L[ah].className.toLowerCase().indexOf("on") > -1) {
                        E = ah;
                    }
                    var J = L[ah].parentNode && L[ah].parentNode.style.display == "none";
                    if (!J && L[ah].checked) {
                        Y++;
                        if (y._value) {
                            y._value += spChars[3] + L[ah].value;
                        } else {
                            y._value = L[ah].value + "";
                        }
                        if (L[ah].itemText) {
                            var ai = L[ah].itemText.value;
                            if (ai == defaultOtherText) {
                                ai = "";
                            }
                            y._value += spChars[2] + replace_specialChar(trim(ai.substring(0, 3000)));
                        }
                    }
                }
                if (E > -1) {
                    y._value = L[E].value + "";
                } else {
                    if (Y > 0) {} else {
                        y._value = "-2";
                    }
                }
                break;
            case "radio_down":
                if (x) {
                    y._value = "-3";
                    continue;
                }
                y._value = am[ak].itemSel.value;
                break;
            case "matrix":
                var p = am[ak].itemTrs;
                var s = P._mode;
                G = p.length;
                var an = 0;
                var w = 0;
                var T = 0;
                var K = 0;
                var ad = new Array();
                var o = false;
                for (var ah = 0; ah < p.length; ah++) {
                    var c = p[ah].getAttribute("rindex");
                    if (c == 0 && p[ah].getAttribute("randomrow") == "true") {
                        o = true;
                    }
                    var ap = new Object();
                    ap.rIndex = parseInt(c);
                    var b = s != "201" && s != "202";
                    if (p[ah].style.display == "none") {
                        if (b) {
                            var f = "-4";
                            if (y._value) {
                                y._value += "," + f;
                            } else {
                                y._value = f;
                            }
                            ap.val = f;
                            continue;
                        }
                    }
                    var L = p[ah].itemInputs || p[ah].itemLis || p[ah].divSlider || p[ah].itemSels;
                    if (!L) {
                        G = G - 1;
                        K = 1;
                        continue;
                    } else {
                        an = L.length;
                    }
                    var E = -1;
                    var S = "";
                    if (s != "201" && s != "202") {
                        for (var ae = 0; ae < L.length; ae++) {
                            if (L[ae].className.toLowerCase().indexOf("on") > -1) {
                                E = ae;
                                S = L[ae].value;
                            }
                            if (L[ae].checked) {
                                E = ae;
                                if (S) {
                                    S += ";" + L[ae].value;
                                } else {
                                    S = L[ae].value;
                                }
                                if ((s == "103" || s == "102" || s == "101")) {
                                    var ao = L[ae].getAttribute("needfill");
                                    if (ao) {
                                        var aj = L[ae].fillvalue || L[ae].getAttribute("fillvalue") || "";
                                        if (aj == defaultOtherText) {
                                            aj = "";
                                        }
                                        aj = replace_specialChar(aj).replace(/;/g, "；").replace(/,/g, "，");
                                        S += spChars[2] + aj;
                                    }
                                }
                            } else {
                                if (L[ae].tagName == "TEXTAREA" || L[ae].tagName == "SELECT") {
                                    E = ae;
                                    var aj = trim(L[ae].value);
                                    if (p[ah].style.display == "none") {
                                        aj = "Ⅳ";
                                    }
                                    if (ae > 0) {
                                        S += spChars[3];
                                    }
                                    if (aj) {
                                        S += aj;
                                    } else {
                                        S += "(空)";
                                    }
                                }
                            }
                        }
                        if (E > -1) {
                            if (y._value) {
                                y._value += "," + S;
                            } else {
                                y._value = S;
                            }
                            ap.val = S;
                        } else {
                            if (y._value) {
                                y._value += ",-2";
                            } else {
                                y._value = "-2";
                            }
                            ap.val = "-2";
                        }
                    } else {
                        if (s == "201") {
                            var O = trim(L[0].value.substring(0, 3000));
                            if (p[ah].style.display == "none") {
                                O = "Ⅳ";
                            }
                            if (O && L[0].lnglat) {
                                O = O + "[" + L[0].lnglat + "]";
                            }
                            if (T > 0) {
                                y._value += spChars[2] + replace_specialChar(O);
                            } else {
                                y._value = replace_specialChar(O);
                            }
                            ap.val = replace_specialChar(O);
                        } else {
                            if (s == "202") {
                                var ag = p[ah].divSlider.value == undefined ? "": p[ah].divSlider.value;
                                if (p[ah].style.display == "none") {
                                    ag = "Ⅳ";
                                }
                                if (T > 0) {
                                    y._value += spChars[2] + ag;
                                } else {
                                    y._value = ag;
                                }
                                ap.val = ag;
                            }
                        }
                    }
                    ad.push(ap);
                    T++;
                }
                if (x) {
                    var ae = 0;
                    y._value = "";
                    while (ae < G) {
                        if (s == "201" || s == "202") {
                            if (ae == 0) {
                                y._value = "(跳过)";
                            } else {
                                y._value += spChars[2] + "(跳过)";
                            }
                        } else {
                            if (ae == 0) {
                                y._value = "-3";
                            } else {
                                y._value += ",-3";
                            }
                        }
                        ae++;
                    }
                    continue;
                } else {
                    ad.sort(function(t, k) {
                        return t.rIndex - k.rIndex;
                    });
                    var h = spChars[2];
                    if (s != "201" && s != "202") {
                        h = ",";
                    }
                    var Z = "";
                    for (var a = 0; a < ad.length; a++) {
                        if (a > 0) {
                            Z += h;
                        }
                        var u = ad[a].rIndex;
                        if (parseInt(u) == u) {
                            var c = parseInt(u) + 1;
                            Z += c + spChars[4];
                        }
                        Z += ad[a].val;
                    }
                    y._value = Z;
                }
                break;
            }
        }
    }
    B.sort(function(t, k) {
        return t._topic - k._topic;
    });
    var n = "";
    for (ak = 0; ak < B.length; ak++) {
        if (ak > 0) {
            n += spChars[1];
        }
        n += B[ak]._topic;
        n += spChars[0];
        n += B[ak]._value;
    }

    return n;
}
var verifyMsg = "";
var needSubmitNotValid = false;
function validate() {
    var A = true;
    needSubmitNotValid = false;
    var S = pageHolder[cur_page].questions;
    var C = pageHolder[cur_page].hasExceedTime;
    firstError = null;
    firstMatrixError = null;
    curMatrixError = null;

    for (var R = 0; R < S.length; R++) {
        var y = S[R].dataNode;
        var V = y._hasjump;
        verifyMsg = "";
        var u = S[R].style.display.toLowerCase() == "none" || pageHolder[cur_page].skipPage;
        if (S[R].removeError) {
            S[R].removeError();
        }
        if (u || (S[R].dataNode._referTopic && !S[R].displayContent) || C) {
            continue;
        }
        switch (y._type) {
        case "question":
            var I = S[R].itemTextarea || S[R].itemInputs[0];
            var q = I.value || "";
            if (y._requir) {
                if (trim(q) == "") {
                    A = writeError(S[R], validate_info_q1, 3000);
                }
            }
            if (q.length - 3000 > 0) {
                A = writeError(S[R], "您输入的字数超过了3000，请修改！", 3000);
            }
            var o = y._verify;

            var K = S[R].getAttribute("issample");
            var F = true;
            if (K) {
                F = false;
            }
            if (F) {
                if (q) {
                    var H = verifyMinMax(I, o, y._minword, y._maxword);
                    if (H != "") {
                        A = writeError(S[R], H, 3000);
                    }
                }
                if (q != "" && o && o != "0") {
                    var H = verifydata(I, o, y);
                    if (H != "") {
                        A = writeError(S[R], H, 3000);
                    }
                }
            }

            if (V) {
                jumpAnyChoice(S[R], true);
            }
            break;
        case "gapfill":
            var c = S[R].gapFills;
            for (var P = 0; P < c.length; P++) {
                var q = c[P].value || "";
                if (trim(q) == "") {
                    if (y._requir && c[P].getAttribute("isrequir") != "0") {
                        S[R].errorControl = c[P];
                        A = writeError(S[R], validate_info_q1, 3000);
                        break;
                    }
                } else {
                    var O = 0;
                    O = validateMatrix(y, c[P], c[P]);
                    if (O) {
                        S[R].errorControl = c[P];
                        A = writeError(S[R], verifyMsg, 3000);
                        break;
                    }
                    if (c[P].getAttribute("needonly")) {
                        if (c[P].isOnly == false) {
                            S[R].errorControl = c[P];
                            A = writeError(S[R], validate_only, 3000);
                        } else {
                            if (c[P].isOnly != true && c[P].getAttribute("itemverify") != "地图") {
                                S[R].errorControl = c[P];
                                A = writeError(S[R], validate_error, 3000);
                                break;
                            }
                        }
                    }
                }
            }
            if (V) {
                jumpAnyChoice(S[R], true);
            }
            break;
        case "slider":
            var L = S[R].divSlider.value;
            if (y._requir && L == undefined) {
                A = writeError(S[R], validate_info_wd1, 3000);
            }
            if (V) {
                jumpAnyChoice(S[R], true);
            }
            break;
        case "fileupload":
            if (y._requir && !S[R].fileName) {
                A = writeError(S[R], validate_info_f1, 3000);
            }
            if (V) {
                jumpAnyChoice(S[R], true);
            }
            break;
        case "sum":
            var M = S[R].sumLeft;
            if (M == 0) {
                var n = S[R].getElementsByTagName("input");
                M = S[R].dataNode._total;
                for (var z = 0; z < n.length; z++) {
                    var x = n[z];
                    var W = S[R].itemTrs[z];
                    if (W.style.display != "none") {
                        M = M - parseInt(x.value);
                    }
                }
            }
            if (y._requir) {
                if (M != 0) {
                    A = false;
                    if (!M) {
                        M = 100;
                    }
                    if (!firstError) {
                        firstError = S[R];
                    }
                    var H = "<span style='color:red;'>" + sum_warn + "</span>";
                    if (S[R].relSum) {
                        S[R].relSum.innerHTML = sum_total + "<b>" + y._total + "</b>" + sum_left + "<span style='color:red;font-bold:true;'>" + (y._total - M) + "</span>，" + H;
                    }
                }
            } else {
                if (M != undefined && M != 0) {
                    A = false;
                    if (!firstError) {
                        firstError = S[R];
                    }
                    var H = "<span style='color:red;'>" + sum_warn + "</span>";
                    if (S[R].relSum) {
                        S[R].relSum.innerHTML = sum_total + "<b>" + y._total + "</b>" + sum_left + "<span style='color:red;font-bold:true;'>" + (y._total - M) + "</span>，" + H;
                    }
                }
            }
            if (V) {
                jumpAnyChoice(S[R], true);
            }
            break;
        case "radio":
        case "check":
            if (S[R].itemSel) {
                var T = S[R].itemSel;
                var v = T.options;
                if (v.length == 0 && y._requir) {
                    A = writeError(S[R], validate_info_o1, 3000);
                } else {
                    if (v.length > 0) {
                        if ((y._minValue == 0 || y._minValue == y._select.length) && v.length != y._select.length) {
                            A = writeError(S[R], validate_info + validate_info_check3, 3000);
                        } else {
                            if (y._maxValue > 0 && v.length > y._maxValue) {
                                var E = validate_info + validate_info_check1 + y._maxValue + validate_info_check2;
                                if (langVer == 0) {
                                    E += ",您多选择了" + (v.length - y._maxValue) + "项";
                                }
                                A = writeError(S[R], E, 3000);
                            } else {
                                if (y._minValue > 0 && v.length < y._minValue) {
                                    var E = validate_info + validate_info_check1 + y._minValue + validate_info_check2;
                                    if (langVer == 0) {
                                        E += ",您少选择了" + (y._minValue - v.length) + "项";
                                    }
                                    A = writeError(S[R], E, 3000);
                                }
                            }
                        }
                    }
                }
                if (V) {
                    jumpAnyChoice(S[R], true);
                }
                continue;
            }
            var v = S[R].itemInputs || S[R].itemLis;
            var p = -1;
            var G = 0;
            var g = -1;
            for (var P = 0; P < v.length; P++) {
                if (S[R].isShop) {
                    if (v[P].value && v[P].value - 0 > 0) {
                        G++;
                        g = P;
                    }
                } else {
                    if (v[P].className.toLowerCase().indexOf("on") > -1) {
                        p = P;
                        g = P;
                    }
                }
                if (v[P].checked) {
                    G++;
                    g = P;
                    if (y._type == "radio") {
                        if (V && y._select[v[g].value - 1] && y._select[v[g].value - 1]._item_jump == -1) {
                            needSubmitNotValid = true;
                        }
                    }
                    if (v[P].req && isTextBoxEmpty(v[P].itemText.value)) {
                        A = writeError(S[R], validate_textbox, 3000);
                    }
                }
            }
            if (p > -1) {
                hasChoice = true;
            } else {
                if (G > 0) {
                    hasChoice = true;
                    if (y._maxValue > 0 && G > y._maxValue) {
                        var E = validate_info + validate_info_check4 + y._maxValue + type_check_limit5;
                        if (langVer == 0) {
                            E += ",您多选择了" + (G - y._maxValue) + "项";
                        }
                        A = writeError(S[R], E, 3000);
                    } else {
                        if (y._minValue > 0 && G < y._minValue) {
                            var E = validate_info + validate_info_check5 + y._minValue + type_check_limit5;
                            if (langVer == 0) {
                                E += ",您少选择了" + (y._minValue - G) + "项";
                            }
                            if (G == 1 && y._select[g - 1] && y._select[g - 1]._item_huchi) {
                                E = "";
                            } else {
                                A = writeError(S[R], E, 3000);
                            }
                        }
                    }
                } else {
                    if (y._requir) {
                        A = writeError(S[R], validate_info_c1, 3000);
                    }
                }
            }
            if (V) {
                if (y._type == "radio" && y._anytimejumpto < 1) {
                    if (g > -1) {
                        processJ(S[R].indexInPage - 0, y._select[v[g].value - 1]._item_jump - 0, true);
                    } else {
                        processJ(S[R].indexInPage - 0, 0, true);
                    }
                } else {
                    jumpAnyChoice(S[R], true);
                }
            }
            break;
        case "radio_down":
            if (y._requir && S[R].itemSel.selectedIndex == 0) {
                A = writeError(S[R], validate_info_c1, 3000);
            }
            if (V) {
                if (S[R].itemSel.selectedIndex > 0 && y._select[S[R].itemSel.value - 1] && y._select[S[R].itemSel.value - 1]._item_jump == -1) {
                    needSubmitNotValid = true;
                }
                if (y._anytimejumpto < 1) {
                    if (S[R].itemSel.selectedIndex == 0) {
                        processJ(S[R].indexInPage - 0, 0, true);
                    } else {
                        processJ(S[R].indexInPage - 0, y._select[S[R].itemSel.value - 1]._item_jump - 0, true);
                    }
                } else {
                    jumpAnyChoice(S[R], true);
                }
            }
            break;
        case "matrix":
            var e = S[R].itemTrs;
            var h = y._mode;
            len = e.length;
            var l = 0;
            var d = 0;
            var O = 0;
            var w;
            for (var P = 0; P < e.length; P++) {
                if (e[P].style.display == "none") {
                    len = len - 1;
                    continue;
                }
                var v = e[P].itemInputs || e[P].itemLis || e[P].divSlider || e[P].itemSels;
                if (!v) {
                    len = len - 1;
                    continue;
                }
                var p = -1;
                var G = 0;
                if (h != "201" && h != "202") {
                    for (var N = 0; N < v.length; N++) {
                        if (v[N].className.toLowerCase().indexOf("on") > -1) {
                            p = N;
                        } else {
                            if (v[N].checked) {
                                p = N;
                                G++;
                                if ((h == "103" || h == "102" || h == "101")) {
                                    var X = v[N].getAttribute("needfill");
                                    var D = v[N].getAttribute("req");
                                    if (X && D) {
                                        var b = v[N].fillvalue || v[N].getAttribute("fillvalue") || "";
                                        if (isTextBoxEmpty(b)) {
                                            verifyMsg = validate_textbox;
                                            O = 1;
                                            if (!firstMatrixError) {
                                                firstMatrixError = S[R].itemTrs[P];
                                            }
                                            showMatrixFill(v[N], 1);
                                            break;
                                        }
                                    }
                                }
                            } else {
                                if (v[N].tagName == "TEXTAREA" || v[N].tagName == "SELECT") {
                                    var Q = trim(v[N].value);
                                    p = N;
                                    if (!Q) {
                                        var r = v[N].parentNode;                                      
                                        if (r.style.display != "none") {
                                            p = -1;
                                        }                                        
                                    }
                                }
                            }
                        }
                    }
                    if (h == "102") {
                        if (p > -1) {
                            var a = false;
                            if (y._maxvalue > 0 && G > y._maxvalue) {
                                var E = validate_info + validate_info_check4 + y._maxvalue + type_check_limit5;
                                if (langVer == 0) {
                                    E += ",您多选择了" + (G - y._maxvalue) + "项";
                                }
                                verifyMsg = E;
                                O = 1;
                                if (!firstMatrixError) {
                                    firstMatrixError = S[R].itemTrs[P];
                                }
                            } else {
                                if (y._minvalue > 0 && G < y._minvalue) {
                                    var E = validate_info + validate_info_check5 + y._minvalue + type_check_limit5;
                                    if (langVer == 0) {
                                        E += ",您少选择了" + (y._minvalue - G) + "项";
                                    }
                                    verifyMsg = E;
                                    O = 1;
                                    if (!firstMatrixError) {
                                        firstMatrixError = S[R].itemTrs[P];
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if (h == "201") {
                        if (!O) {
                            O = validateMatrix(y, e[P], v[0]);
                        }
                        if (O) {
                            if (!w) {
                                w = v[0];
                            }
                            if (!firstMatrixError) {
                                firstMatrixError = S[R].itemTrs[P];
                            }
                        }
                        if (e[P].getAttribute("needonly")) {
                            if (e[P].isOnly == false) {
                                if (!w) {
                                    w = v[0];
                                }
                                if (!firstMatrixError) {
                                    firstMatrixError = S[R].itemTrs[P];
                                }
                                verifyMsg = validate_only;
                                O = 1;
                            } else {
                                if (e[P].isOnly != true && e[P].getAttribute("itemverify") != "地图") {
                                    if (!w) {
                                        w = v[0];
                                    }
                                    if (!firstMatrixError) {
                                        firstMatrixError = S[R].itemTrs[P];
                                    }
                                    verifyMsg = validate_error;
                                    O = 1;
                                }
                            }
                        }
                        if (trim(v[0].value) != "") {
                            p = 0;
                        } else {
                            if (e[P].getAttribute("isrequir") == "0") {
                                p = 0;
                            }
                        }
                    } else {
                        if (h == "202") {
                            if (e[P].divSlider.value != undefined) {
                                p = 0;
                            }
                        }
                    }
                }
                if (p > -1) {
                    l++;
                } else {
                    if (y._requir) {
                        break;
                    }
                }
            }
            if ((h == "201") && O) {
                if (w) {
                    S[R].errorControl = w;
                }
                A = writeError(S[R], verifyMsg, 3000);
                if (firstMatrixError) {
                    firstMatrixError.onclick();
                }
            }
            if (y._requir && l < len) {
                A = writeError(S[R], validate_info + validate_info_matrix2 + validate_info_matrix1 + (l + 1) + validate_info_matrix3, 3000);
                if (S[R].itemTrs[P] && !firstMatrixError) {
                    firstMatrixError = S[R].itemTrs[P];
                    var f = S[R].getAttribute("DaoZhi");
                    if (!f) {
                        S[R].itemTrs[P].onclick();
                    }
                }
            }
            if ((h == "102" || h == "103" || h == "101") && O) {
                if (w) {
                    S[R].errorControl = w;
                }
                A = writeError(S[R], verifyMsg, 3000);
                if (firstMatrixError) {
                    firstMatrixError.onclick();
                }
            }
            if (V) {
                jumpAnyChoice(S[R], true);
            }
            break;
        }
    }
    for (var B = 0; B < trapHolder.length; B++) {
        if (trapHolder[B].pageIndex != cur_page + 1) {
            continue;
        }
        var v = trapHolder[B].itemInputs;
        var m = "";
        for (var P = 0; P < v.length; P++) {
            if (v[P].checked) {
                m += v[P].value + ",";
            }
        }
        if (!m) {
            A = writeError(trapHolder[B], validate_info_wd1, 3000);
            break;
        }
    }
    if (firstError) {
        PromoteUser(validate_submit, 3000, false);
        if (firstMatrixError && firstMatrixError.parent == firstError) {
            firstMatrixError.scrollIntoView();
        } else {
            firstError.scrollIntoView();
        }
    }
    return A;
}
function validateMatrix(k, b, f) {
    var d = 0;
    if (!f.value) {
        return d;
    }
    var h = f.value;
    var a = b.getAttribute("itemverify") || "";
    var g = b.getAttribute("minword") || "";
    var c = b.getAttribute("maxword") || "";
    var l = b.getAttribute("issample");
    var e = true;
    verifyMsg = "";
    if (l) {
        e = false;
    }
    if (e) {
        verifyMsg = verifyMinMax(f, a, g, c);
    }
    if (verifyMsg != "") {
        d = 1;
    }
    if (e && d == 0 && a && a != "0") {
        verifyMsg = verifydata(f, a, k);
        if (verifyMsg != "") {
            d = 2;
        }
    }
    return d;
}
function removeError() {
    if (this.errorMessage) {
        this.errorMessage.innerHTML = "";
        this.removeError = null;
        this.style.border = "solid 2px white";
        if (this.errorControl) {
            this.errorControl.style.background = "white";
            this.errorControl = null;
        }
    }
}
function PromoteUser(c, b, a) {
	show_status_tip(c, b);
}
function writeError(e, g, f, b) {
    if (e.errorMessage && e.errorMessage.innerHTML != "") {
        return;
    }
    if (e.dataNode && ((e.dataNode._type == "matrix" && e.dataNode._mode == "202") || e.dataNode._type == "slider")) {} else {
        if (!b) {
            e.style.border = "solid 2px #ff9900";
        }
    }
    if (!e.errorMessage) {
        var d = $$tag("div", e);
        for (var c = 0; c < d.length; c++) {
            var a = d[c].className.toLowerCase();
            if (a == "errormessage") {
                e.errorMessage = d[c];
                break;
            }
        }
    }
    if (!e.errorMessage) {
        return;
    }
    e.errorMessage.innerHTML = g;
    e.removeError = removeError;
    if (e.errorControl) {
        e.errorControl.style.background = "#FBD5B5";
    }
    if (!firstError) {
        firstError = e;
    }
    return false;
}
function show_status_tip(a, b) {
    submit_tip.style.display = "block";
    submit_tip.innerHTML = a;
    if (b > 0) {
        setTimeout("submit_tip.style.display='none'", b);
    }
}
function isDate(c) {
    var a = new Array();
    if (c.indexOf("-") != -1) {
        a = c.toString().split("-");
    } else {
        if (c.indexOf("/") != -1) {
            a = c.toString().split("/");
        } else {
            return false;
        }
    }
    if (a[0].length == 4) {
        var b = new Date(a[0], a[1] - 1, a[2]);
        if (b.getFullYear() == a[0] && b.getMonth() == a[1] - 1 && b.getDate() == a[2]) {
            return true;
        }
    }
    return false;
}
function DBC2SBC(b) {
    var c = b.value;
    var a = c.dbc2sbc();
    if (c != a) {
        b.value = a;
    }
    return b.value;
}
function verifydata(f, d, e) {
    var c = trim(f.value);
    var a = null;

    return "";
}

function verifyMinMax(f, e, c, a) {
    var d = f.value;
    if (e == "数字" || e == "小数") {
        d = DBC2SBC(f);
        var b = /^(\-)?\d+$/;
        if (e == "小数") {
            b = /^(\-)?\d+(\.\d+)?$/;
        }
        if (!b.exec(d)) {
            if (e == "小数") {
                return validate_decnum;
            } else {
                return validate_num;
            }
        }
        if (d != 0) {
            d = d.replace(/^0+/, "");
        }
        if (c != "") {
            if (e == "数字" && parseInt(d) - parseInt(c) < 0) {
                return validate_num2 + c;
            } else {
                if (e == "小数" && parseFloat(d) - parseFloat(c) < 0) {
                    return validate_num2 + c;
                }
            }
        }
        if (a != "") {
            if (e == "数字" && parseInt(d) - parseInt(a) > 0) {
                return validate_num1 + a;
            } else {
                if (e == "小数" && parseFloat(d) - parseFloat(a) > 0) {
                    return validate_num1 + a;
                }
            }
        }
    } else {
        if (a != "" && d.length - a > 0) {
            return validate_info_wd3.format(a, d.length);
        }
        if (c != "" && d.length - c < 0) {
            return validate_info_wd4.format(c, d.length);
        }
    }
    return "";
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
function postHeight() {
    if (window == window.top) {
        return;
    }
    try {
        var b = parent.postMessage ? parent: parent.document.postMessage ? parent.document: null;
        if (b != null) {
            b.postMessage("heightChanged," + ((document.documentElement.scrollHeight || document.body.scrollHeight) + 50), "*");
        }
    } catch(a) {}
}
postHeight();
function popUpAlert(a) {
    if (maxCheatTimes > 0 && window.screenfull) {
        window.screenfull.alert(a);
    } else {
        alert(a);
    }
}
''