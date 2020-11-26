function setListId(a, b) {
    PDF_close(a);
}
var pre_satisfaction = "很不满意\n不满意\n一般\n满意\n很满意";
var pre_agree = "很不同意\n不同意\n一般\n同意\n很同意";
var pre_import = "很不重要\n不重要\n一般\n重要\n很重要";
var pre_accord = "很不符合\n不符合\n一般\n符合\n很符合";
var pre_wanting = "很不愿意\n不愿意\n一般\n愿意\n很愿意";
var pre_bool_1 = "是\n否";
var pre_bool_2 = "对\n错";
var pre_bool_3 = "满意\n不满意";
var pre_bool_4 = "同意\n不同意";
var pre_bool_5 = "正确\n错误";
var pre_bool_6 = "支持\n反对";
var pre_bool_7 = "Ture\nFalse";
var pre_bool_8 = "Yes\nNo";
var currentRelation = "";
var itemImage = "";
function setFloat(a) {
    a.className = "spanLeft";
}
function getEditorIndex() {
    return EditorIndex++;
}
function getVerifyHtml(f) {
    var b = "验证：<select onchange='cur.setVerify(this);'>";
    var e = ["0,无", "数字,整数", "小数", "日期", "手机", "固话", "电话,手机或固话", "Email,邮件", "密码", "城市单选,省份城市", "省市区", "高校", "地图", "指定选项", "网址", "身份证号", "学号", "QQ", "汉字", "姓名,中文姓名", "英文"];
    for (var d = 0; d < e.length; d++) {
        var g = e[d];
        var h = g.split(",");
        var a = h[0];
        var c = h.length == 2 ? h[1] : h[0];
        if (g == "指定选项" && f == 0) {
            continue;
        }
        b += "<option value='" + a + "'>" + c + "</option>";
    }
    b += "</select>";
    return b;
}
function checkVerifyMinMax(d, a, e) {
    var b = d.value;
    var c = a.value;
    if (b && !isInt(b)) {
        return "您输入的数据不正确";
    }
    if (c && !isInt(b)) {
        return "您输入的数据不正确";
    }
    if (b && c && b - c > 0) {
        return "您输入的数据不正确";
    }
    if (e == "数字" || e == "小数") {
        return "";
    }
    if (b && b - 3000 > 0) {
        return "字数不能超过3000";
    }
    if (c && c - 3000 > 0) {
        return "字数不能超过3000";
    }
    return "";
}
function createAttr() {
    var ak = new Date().getTime();
    var d4;
    var B = document.createElement("div");
    this.appendChild(B);
    var aH = document.createDocumentFragment();
    this.tabAttr = B;
    var cA = this;
    var n = this.dataNode;
    var cx = n._type;
    var cV = this.dataNode._tag || 0;
    var cG = cx == "question";
    var cf = cx == "slider";
    var u = cx == "sum";
    var bq = cx == "page";
    var F = cx == "cut";
    var bj = cx == "check";
    var aK = cx == "radio";
    var aX = aK && cV;
    var cT = cx == "radio_down";
    var au = cx == "matrix";
    var bW = cx == "matrix" && cV > 300;
    var d5 = cx == "fileupload";
    var dY = cx == "gapfill";
    var dS = bj && cV;
    var dW = aK || cT || bj || au;
    var eh = !F && !bq;
    var dB = n._verify || "0";
    var a2 = dB != "0";
    var bB = n._isTouPiao;
    var bM = n._isCeShi;
    var N = n._isQingJing;
    var r = bM && n._ispanduan;
    var cR = n._isShop;
    var eg = n._isCePing;
    var aB = au && cV < 102;
    var ab = this.get_div_title();
    var dN = "";
    var cY = new Array();
    this.option_radio = cY;
    var aD = document.createElement("div");
    this.attrMain = aD;
    aD.className = "div_title_attr_question";
    if (bM && !dY) {
        this.addCeShiSetting = function(ev) {
            var ep = $ce("div", "", ev);
            ep.style.marginTop = "15px";
            if (cA.dataNode._type == "question") {
                ep.style.marginBottom = "10px";
            }
            var es = $ce("span", "<b>题目分数：</b>", ep);
            var er = "<select onchange='cur.setTValue(this.value);'>";
            var eu = cA.dataNode._answer == "简答题无答案" || cA.dataNode._type == "fileupload";
            if (!eu) {
                er += "<option value='0.5'>0.5</option>";
            }
            for (var eo = 1; eo <= 50; eo++) {
                er += "<option value='" + eo + "'>" + eo + "</option>";
                if (eo == 1 && !eu) {
                    er += "<option value='1.5'>1.5</option>";
                } else {
                    if (eo == 2 && !eu) {
                        er += "<option value='2.5'>2.5</option>";
                    }
                }
            }
            er += "</select>&nbsp;";
            es.innerHTML += er;
            this.setTValue = function(i) {
                cA.dataNode._ceshiValue = i;
                dw.style.display = "";
                if (cG) {
                    cA.setCeshiQTip();
                } else {
                    cA.spanCeShi.innerHTML = "（分值：" + cA.dataNode._ceshiValue + "分）";
                }
                Calculatedscore();
            };
            this.initValue = function() {
                if (this.dataNode._ceshiValue) {
                    var i = $$("select", es)[0];
                    i.value = this.dataNode._ceshiValue;
                }
            };
            ep.appendChild(es);
            var dw = $ce("span", "", ep);

            if (cG) {
                var et = $ce("span", "&nbsp;答案：", ep);
                var eq = control_text("14");
                et.appendChild(eq);
                $ce("span", "<a title='如果有多个答案，请以“|”分隔' href='javascript:void(0);'  onclick='alert(this.title);return false;' style='color:green;'><b>(?)</b></a>&nbsp;&nbsp;", et);
                eq.value = n._answer;
                eq.onfocus = function() {
                    if (this.value == "请设置答案") {
                        this.value = "";
                    }
                };
                eq.onchange = eq.onblur = function() {
                    if (this.value == "") {
                        this.value = "请设置答案";
                    }
                    var i = trim(this.value);
                    if (/^[A-Za-z\s]+$/.test(i)) {
                        i = i.replace(/\s+/g, " ");
                    }
                    this.value = i;
                    cA.dataNode._answer = i;
                    cA.setCeshiQTip();
                };
                eq.onchange();
                var j = control_check();
                et.appendChild(j);
                $ce("span", "<b>包含答案即可得分</b><a title='用户提交的答案不需要跟设置的答案完全一样，只要包含设置的答案就可得分。' href='javascript:void(0);' onclick='alert(this.title);return false;' style='color:green;'><b>(?)</b></a>", et);
                j.onclick = function() {
                    cA.dataNode._include = this.checked;
                };
                j.checked = n._include;
                if (eq.value == "简答题无答案") {
                    et.style.display = "none";
                }
            }
            var en = $ce("span", "&nbsp;&nbsp;答案解析<a title='您可以填写针对此题答案的一些解析说明，在用户参与完测试后会看到此解析' href='javascript:void;' onclick='alert(this.title);return false;' style='color:green;'><b>(?)</b></a>：", ep);
            var em = control_text("14");
            en.appendChild(em);
            em.value = n._ceshiDesc;
            em.onchange = em.onblur = function() {
                cA.dataNode._ceshiDesc = this.value;
            };
            em.onchange();
            em.style.display = "none";
            $ce("span", "&nbsp;", ep);
            var ew = $ce("a", "设置答案解析", ep);
            ew.href = "javascript:void(0)";
            ew.className = "link-U666";

            this.initValue();
        };
    }
    var c1 = document.createElement("div");
    c1.style.marginLeft = "8px";
    var af = n._title == "请在此输入问题标题" || n._title == "请根据您的实际情况选择最符合的项：1-->5表示非常不满意-->非常满意";
    var cj = $ce("div", "", c1);
    if (N) {
        cj.style.display = "none";
    }
    var c = n._title.indexOf("<") > -1;
    if (!c) {
        var aV = "";
        if ((eh && n._topic - 1 > 0) || F) {
            aV = "";
        }
        var ac = $ce("div", "<span style='float:left;'>&nbsp;<b style='font-size:14px;'>标题</b>" + aV + "</span>", cj);
        ac.style.background = "#e2e0e1";
        ac.style.height = "27px";
        ac.style.lineHeight = "27px";
        ac.style.width = "650px";
        var bx = $ce("span", "", ac);
        bx.className = "spanRight";
        bx.style.paddingRight = "5px";

        $ce("div", "", ac).className = "divclear";
    }
    var bm = $ce("div", "", cj);
    var dV = $ce("div", "", bm);
    bm.style.width = "650px";
    var G = control_textarea("4", "29");
    this.txttitle = G;
    G.tabIndex = 1;
    if (af) {
        G.defValue = n._title;
    }
    if (eh) {
        G.title = "例如：您最喜欢的车型？";
        G.value = n.title;
    }
    if (bq) {
        G.title = "您可以在此输入本页的页面标题信息（选填）";
    }
    if (F) {
        G.title = "请在此输入内容";
        G.value = n.title;
    }
    var w = "tc" + n._type + getEditorIndex();
    G.value = n._title;
    G.id = w;
    G.style.overflow = "auto";
    G.style.padding = "5px 0 0 5px";
    G.style.border = "1px solid #dddddd";
    dV.appendChild(G);

	G.style.width = "648px";
	G.style.height = "100px";

    G.onkeyup = G.onchange = function() {
        cA.checkTitle();
    };
    G.onfocus = function() {
        if (this.value == "请在此输入问题标题" || this.value == "请根据您的实际情况选择最符合的项：1-->5表示非常不满意-->非常满意") {
            this.value = "";
        }
    };
    G.onblur = function() {
        if (!this.value && cA.dataNode._type != "page") {
            this.value = this.defValue || "";
            cA.checkTitle();
        }
    };
    cA.gettextarea = function() {
        return G;
    };
    var bF = "";

    if (eh) {
        var d6 = $ce("div", "", c1);
        if (N) {
            d6.style.display = "none";
        }
        d6.innerHTML = "&nbsp;&nbsp;";
        var m = control_check();
        m.title = "用户在填写问卷时必须回答这道题";
        var du = document.createTextNode("必答题");
        d6.appendChild(m);
        d6.appendChild(du);
        m.onclick = function() {
            cA.dataNode._requir = this.checked;
            cA.setreqstatus();
        };
        this.get_requir = function() {
            return m;
        };
        var e = document.createElement("div");
        if (N || cR) {
            e.style.display = "none";
        }
        e.innerHTML = "&nbsp;&nbsp;";
    }

    if (dY) {
        var cO = $ce("div", "", dP);
        cO.style.marginLeft = "8px";
        var dn = control_check();
        cO.appendChild(dn);
        dn.onclick = function() {
            cA.dataNode._useTextBox = this.checked;
            cA.checkTitle();
        };
        $ce("span", "文本框样式&nbsp;&nbsp;", cO);
    }
    aD.appendChild(c1);
    aH.appendChild(aD);
    if (bq) {
        var dt = document.createElement("span");
        dt.innerHTML = "此页是否是甄别页：";
        dt.title = "可以在此页设置筛选规则，如果用户提交的答卷不符合要求，则会终止后面的答题";
        var t = control_check();
        t.title = dt.title;
        t.onclick = function() {
            cA.dataNode._iszhenbie = this.checked;
            cA.divZhenBie.style.display = this.checked ? "": "none";
        };
        c1.appendChild(dt);
        c1.appendChild(t);
        var d2 = $ce("i", "", c1);
        d2.className = "design-icon design-qmark";
        d2.onmouseover = function() {
            toolTipLayer.style.width = "300px";
            toolTipLayer.innerHTML = "可以对此页的题目设置无效答卷筛选规则，用户点击下一页时，如果此页的答题不符合要求，系统会终止该用户继续答题。";
            sb_setmenunav(toolTipLayer, true, this);
        };
        d2.onmouseout = function(i) {
            sb_setmenunav(toolTipLayer, false, this);
        };
        var by = !window.isPromote;
        if (by) {
            dt.style.display = t.style.display = d2.style.display = "none";
        }
        var ag = document.createElement("div");
        ag.style.margin = "10px 0 0 15px";
        aD.appendChild(ag);
        var cW = document.createElement("div");
        cW.style.margin = "10px 0 0 15px";
        $ce("span", "<b>填写时间控制</b>&nbsp;", cW);
        aD.appendChild(cW);
        var bi = control_text(3);
        $ce("span", "此页允许停留时间为：<b>最短</b>", cW).appendChild(bi);
        var dC = control_text(3);
        var g = $ce("span", "秒&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>最长</b>", cW);
        g.appendChild(dC);
        $ce("span", "秒（空表示不限制）", cW);
        bi.parDiv = cA;
        bi.onblur = bi.onchange = function() {
            var dw = this;
            var em = dw.value;
            var j = this.parDiv;
            var en = j.dataNode._maxtime;
            j.dataNode._istimer = false;
            if (em) {
                if (isPositive(em) && (!en || (em - en <= 0))) {
                    if (en && em - en == 0) {
                        j.dataNode._istimer = true;
                    }
                    j.dataNode._mintime = parseInt(em);
                } else {
                    show_status_tip("最短时间必须为正整数并且少于最长时间", 4000);
                    dw.value = "";
                    j.dataNode._mintime = "";
                }
            } else {
                j.dataNode._mintime = "";
            }
            if (cur.dataNode._mintime && cur.dataNode._mintime == cur.dataNode._maxtime) {
                if (total_page == 1 && !window.alertTime) {
                    window.alertTime = 1;
                    alert("提示：您设置的最短填写时间等于最长填写时间，这个一般用于控制用户在确定的时间内观看图片或者视频！");
                }
            }
            var i = j.dataNode._mintime || j.dataNode._maxtime;
            b7.style.display = (window.isPromote) && i ? "": "none";
            j.showTimeLimit();
        };
        bi.onclick = function() {

        };
        dC.onclick = function() {

        };
        dC.onblur = dC.onchange = function() {
            var dw = this;
            var em = dw.value;
            var j = cur.dataNode._mintime;
            cur.dataNode._istimer = false;
            if (em) {
                if (isPositive(em) && (!j || (em - j >= 0))) {
                    if (j && em - j == 0) {
                        cur.dataNode._istimer = true;
                    }
                    cur.dataNode._maxtime = parseInt(em);
                } else {
                    show_status_tip("最长时间必须为正整数并且大于最短时间", 4000);
                    dw.value = "";
                }
            } else {
                cur.dataNode._maxtime = "";
            }
            var i = cur.dataNode._mintime || cur.dataNode._maxtime;
            if (cur.dataNode._mintime && cur.dataNode._mintime == cur.dataNode._maxtime) {
                if (total_page == 1 && !window.alertTime) {
                    window.alertTime = 1;
                    alert("提示：您设置的最短填写时间等于最长填写时间，这个一般用于控制用户在确定的时间内观看图片或者视频！");
                }
            }
            b7.style.display = (window.isPromote) && i ? "": "none";
            cA.showTimeLimit();
        };
        cA.setMinMaxTime = function() {
            if (bi) {
                bi.value = this.dataNode._mintime;
            }
            if (dC) {
                dC.value = this.dataNode._maxtime;
            }
        };
        var ci = $ce("i", "", cW);
        ci.className = "design-icon design-qmark";
        ci.onmouseover = function() {
            toolTipLayer.style.width = "250px";
            toolTipLayer.innerHTML = "说明：从进入此页开始计时，在还未达到最短填写时间时不能进入下一页或提交答卷，当到达最长填写时间后还未做答完成将自动跳转到下一页或提交答卷。如果要控制用户观看图片或者视频的时间，可以设置最短填写时间等于最长填写时间";
            sb_setmenunav(toolTipLayer, true, this);
        };
        ci.onmouseout = function(i) {
            sb_setmenunav(toolTipLayer, false, this);
        };
        var b7 = $ce("div", "将上面的填写时间复制到&nbsp;", cW);
        b7.style.margin = "6px 0 0 86px";
        var ar = 1;
        var x = 1;
        var aa = total_page;
        $ce("span", "<input type='radio'  name='rbltimesp' onclick='cur.setTimePageTime(1);'  checked='checked'  />所有页<input type='radio' name='rbltimesp'  onclick='cur.setTimePageTime(2);'/>指定页", b7);
        var M = $ce("span", "：第<input type='text' value='1' onblur='cur.setTimePageStart(this);' style='width:20px;'/>页到<input type='text' value='" + aa + "' onblur='cur.setTimePageEnd(this);' style='width:20px;'/>页", b7);
        M.style.display = "none";
        cA.setTimePageTime = function(i) {
            ar = i;
            M.style.display = i == 1 ? "none": "";
        };
        cA.setTimePageStart = function(i) {
            var j = i.value;
            if (!isPositive(j) || j - aa >= 0) {
                i.value = 1;
                show_status_tip("必须为正数，并且小于最大页数", 4000);
            }
            x = i.value;
        };
        cA.setTimePageEnd = function(i) {
            var j = i.value;
            if (!isPositive(j) || j - total_page > 0) {
                i.value = total_page;
                show_status_tip("必须为正数，并且小于总页数", 4000);
            } else {
                if (j - x <= 0) {
                    i.value = total_page;
                    show_status_tip("必须大于最小页数", 4000);
                }
            }
            aa = i.value;
        };
        var ea = $ce("span", "&nbsp;", b7);
        var aJ = control_btn("复制");
        aJ.className = "finish cancle";
        ea.appendChild(aJ);
        aJ.onclick = function() {
            if (ar == 1) {
                aa = total_page;
            }
            dv(firstPage);
            for (var j = 0; j < questionHolder.length; j++) {
                dv(questionHolder[j]);
            }
            show_status_tip("成功复制", 4000);
        };
        var c8 = $ce("div", "", aD);
        c8.style.margin = "10px 0px 0px 15px";
        if (!window.isPromote) {
            c8.style.display = "none";
        }
        $ce("span", "<b>批量插入分页符</b>&nbsp;", c8);
        var ek = $ce("span", "每隔", c8);
        var a8 = control_text(3);
        a8.value = "1";
        ek.appendChild(a8);
        $ce("span", "题插入一个分页符", ek);
        a8.maxlength = 2;
        a8.onblur = a8.onchange = function() {
            if (!isPositive(this.value)) {
                this.value = 1;
            }
        };
        var db = 1;
        var a0 = 1;
        var dr = total_question;
        $ce("span", "&nbsp;&nbsp;&nbsp;范围：<input type='radio' name='rblPageScope' onclick='cur.setInsertScopeType(1);' checked='checked'/>全部题<input type='radio' name='rblPageScope' onclick='cur.setInsertScopeType(2);'/>指定题", c8);
        var o = $ce("span", "&nbsp;第<input type='text' style='width:20px;' value='1' onblur='cur.setInsertMinPage(this);'>至第<input type='text' style='width:20px;'  onblur='cur.setInsertMaxPage(this);' value='" + dr + "'>题", c8);
        o.style.display = "none";
        cA.setInsertScopeType = function(i) {
            db = i;
            o.style.display = i == 1 ? "none": "";
        };
        cA.setInsertMinPage = function(i) {
            var j = i.value;
            if (!isPositive(j) || j - dr >= 0) {
                i.value = 1;
                show_status_tip("必须为正数，并且小于最大题数", 4000);
            }
            a0 = i.value;
        };
        cA.setInsertMaxPage = function(i) {
            var j = i.value;
            if (!isPositive(j) || j - total_question > 0) {
                i.value = total_question;
                show_status_tip("必须为正数，并且小于总题数", 4000);
            } else {
                if (j - a0 <= 0) {
                    i.value = total_question;
                    show_status_tip("必须大于最小题数", 4000);
                }
            }
            dr = i.value;
        };
        var el = $ce("span", "", c8);
        var T = control_btn("批量插入");
        T.className = "finish cancle";
        el.appendChild(T);
        $ce("span", "&nbsp;", el);
        var b8 = control_btn("删除");
        b8.className = "finish cancle";
        b8.title = "删除除第一页之外的所有分页";
        el.appendChild(b8);
        b8.onclick = function() {
            if (confirm("确定删除除第一页之外的所有分页吗？")) {
                var em = new Array();
                for (var j = 0; j < questionHolder.length; j++) {
                    var dw = questionHolder[j].dataNode;
                    if (dw._type == "page") {
                        em.push(questionHolder[j]);
                    }
                }
                for (var j = 0; j < em.length; j++) {
                    em[j].deleteQ("noNeedConfirm");
                }
            }
        };
        T.onclick = function() {
            var eo = new Array();
            for (var en = 0; en < questionHolder.length; en++) {
                var eq = questionHolder[en].dataNode;
                questionHolder[en].pageNext = false;
                if (eq._type != "page" && eq._type != "cut") {
                    eo.push(questionHolder[en]);
                } else {
                    if (eq._type == "page") {
                        if (en > 0) {
                            questionHolder[en - 1].pageNext = true;
                        }
                    }
                }
            }
            var j = eo.length;
            if (j > 1) {
                var dw = parseInt(a8.value);
                var er = (a0 - 1) || dw - 1;
                if (db == 1) {
                    dr = total_question;
                }
                for (var en = er; en < dr - 1; en += dw) {
                    var ep = questionHolder[en + 1];
                    if (eo[en].pageNext) {
                        continue;
                    }
                    var em = createFreQdataNode("page");
                    addNewQ(em, false, false, eo[en]);
                }
            } else {
                show_status_tip("最后一题不需要添加分页", 4000);
            }
        };
        function dv(j) {
            var dw = j.dataNode;
            if (dw._type == "page") {
                var i = parseInt(dw._topic);
                if (i >= x && i <= aa) {
                    dw._mintime = cur.dataNode._mintime;
                    dw._maxtime = cur.dataNode._maxtime;
                    if (j.setMinMaxTime) {
                        j.setMinMaxTime();
                    }
                }
            }
        }
    }
    if (dW || u) {
        var bO = "";
        bO = " style='display:none;'";
        var a6 = "<a href='javascript:'" + bO + " onclick='cur.show_divAddFromCheck();return false;' class='link-U666' title='引用其它题的选中项'>引用其它题选中项</a>&nbsp;";
        var U = document.createElement("div");
        U.style.margin = "10px 0px";
        var bl = $ce("select", "", U);
        this.selAddFromCheck = bl;
        bl.style.width = "220px";
        U.style.display = "none";
        var d8 = document.createElement("span");
        d8.style.display = "none";
        this.show_divAddFromCheck = function() {
            if (!U.inited) {
                bl.onchange = function() {

                    cur.addFromCheck(this);
                };
                U.inited = true;
                var i = $ce("label", "<i class='design-icon design-qmark'></i>", U);
                i.onmouseover = function() {
                    toolTipLayer.style.width = "300px";
                    toolTipLayer.innerHTML = "引用前面多选题或者排序题的选中项";
                    sb_setmenunav(toolTipLayer, true, this);
                };
                i.onmouseout = function() {
                    sb_setmenunav(toolTipLayer, false, this);
                };
            }
            U.style.display = U.style.display == "" ? "none": "";
            if (isMergeAnswer && !this.isMergeNewAdded) {
                U.style.display = "none";
            }

            if (au) {
                if (bI) {
                    bI.style.display = U.style.display == "" ? "none": "";
                }
                if (dj && !dj.checked) {
                    bI.style.display = "none";
                }
                if (bl.value > 0 && bI) {
                    bI.style.display = "none";
                }
            }
            this.updateSelCheck();
            this.hasDisplaySelCheck = U.style.display == "";
        };
        this.updateSelCheck = function() {
            for (var en = 0; en < bl.options.length; en++) {
                bl.options[en] = null;
            }
            bl.options[0] = new Option("请选择来源题目(多选或排序题)", 0);
            var er = 1;
            for (var en = 0; en < questionHolder.length; en++) {
                var eq = questionHolder[en].dataNode;
                var et = this.dataNode._topic;
                if (eq._type == "check" && eq._topic - et < 0 && !questionHolder[en]._referDivQ) {
                    var dw = "[多选题]";
                    if (eq._tag) {
                        dw = "[排序题]";
                    }
                    var em = eq._title;
                    em = em.replace(/<(?!img|embed).*?>/ig, "");
                    em = em.replace(/&nbsp;/ig, " ").substring(0, 16);
                    var ep = em + "  " + dw;
                    if (!WjxActivity._use_self_topic) {
                        ep = eq._topic + "." + ep;
                    }
                    var eo = new Option(ep, eq._topic);
                    eo.referDivQ = questionHolder[en];
                    bl.options[er++] = eo;
                }
            }
            if (this._referDivQ) {
                var es = bl.options;
                for (var en = 0; en < es.length; en++) {
                    var j = es[en];
                    if (j.referDivQ == this._referDivQ) {
                        bl.value = j.value;
                        break;
                    }
                }
            }
        };
        this.addFromCheck = function(eo) {
            var em = bl.selectedIndex;
            if (aK || bj) {
                if (em > 0 && b2.checked) {
                    b2.checked = false;
                    b2.onclick();
                }
                b2.disabled = em > 0 ? true: false;
                cp.style.display = em > 0 ? "none": "";
            } else {
                ei.style.display = em > 0 ? "none": "";
                if (aG) {
                    aG.style.display = ei.style.display;
                }
            }
            d8.style.display = em > 0 ? "": "none";
            this.clearReferQ();
            if (bl.value > 0) {
                if (this._referedArray) {
                    var dw = "";
                    for (var en = 0; en < this._referedArray.length; en++) {
                        if (en > 0) {
                            dw += ",";
                        }
                        dw += this._referedArray[en].dataNode._topic;
                    }
                    show_status_tip("第" + dw + "题的选项或行标题来源于此题的选中项，此题不能再引用其他题");
                    bl.value = "0";
                    this.show_divAddFromCheck();
                    return;
                }
                var j = bl.options[bl.selectedIndex].referDivQ;
                this._referDivQ = j;
                if (!j._referedArray) {
                    j._referedArray = new Array();
                }
                if (j._referedArray.indexOf(this) == -1) {
                    j._referedArray.push(this);
                }
                j._referedArray.sort(function(ep, i) {
                    return ep.dataNode._topic - i.dataNode._topic;
                });
                this.updateReferQ();
            } else {
                this.show_divAddFromCheck();
            }
            if (this.dataNode._daoZhi) {
                if (cE) {
                    cE.checked = false;
                    this.dataNode._daoZhi = false;
                    cA.updateSpanMatrix();
                    show_status_tip("使用引用逻辑后，不能再使用竖向选择", 5000);
                }
            }
            if (this.updateItem) {
                this.updateItem();
            } else {
                if (this.createSum) {
                    this.createSum();
                }
            }
        };
        this.removeRefer = function() {
            bl.value = "0";
            this.addFromCheck();
        };
        this.clearReferQ = function() {
            if (this._referDivQ) {
                this._referDivQ._referedArray.remove(this);
                if (!this._referDivQ._referedArray.length) {
                    this._referDivQ._referedArray = null;
                }
                this._referDivQ = null;
            }
        };
        this.clearReferedQ = function() {
            if (this._referedArray) {
                for (var j = 0; j < this._referedArray.Length; j++) {
                    var dw = this._referedArray[j];
                    dw._referDivQ = null;
                    if (dw.updateItem) {
                        dw.updateItem();
                    } else {
                        if (dw.createSum) {
                            dw.createSum();
                        }
                    }
                }
            }
        };
        this.updateReferQ = function() {
            if (this._referDivQ) {
                var i = this._referDivQ;
                var dw = "选项";
                if (this.dataNode._type == "matrix" || this.dataNode._type == "sum") {
                    dw = "行标题";
                }
                var j = "&nbsp;<a href='javascript:' onclick='cur.removeRefer();return false;' class='link-U666'>取消引用</a>&nbsp;";
                if (isMergeAnswer && !this.isMergeNewAdded) {
                    j = "";
                }
                d8.innerHTML = j;
            }
        };
    }
    if (eh) {
        $ce("div", "", aD).style.clear = "both";
        var dc = document.createElement("div");
        dc.style.marginLeft = "8px";
        if (!dW) {
            var dT = document.createElement("div");
            dT.style.margin = "10px 0";
            dc.appendChild(dT);
        }
        aD.appendChild(dc);
    }
    if (cf) {
        $ce("span", "最小值：", dT);
        var aI = control_text("3");
        aI.title = "用户可以选择的最小值";
        aI.maxLength = 4;
        aI.style.height = "20px";
        dT.appendChild(aI);
        $ce("span", "&nbsp;&nbsp;&nbsp;&nbsp;最小值显示文本：", dT);
        var b9 = control_text("10");
        b9.style.height = "20px";
        dT.appendChild(b9);
        var bG = document.createElement("br");
        dT.appendChild(bG);
        $ce("div", "", dT).style.height = "6px";
        $ce("span", "最大值：", dT);
        var d = control_text("3");
        d.style.height = "20px";
        d.title = "用户可以选择的最大值";
        d.maxLength = 4;
        dT.appendChild(d);
        $ce("span", "&nbsp;&nbsp;&nbsp;&nbsp;最大值显示文本：", dT);
        var at = control_text("10");
        at.style.height = "20px";
        dT.appendChild(at);
        aI.onchange = aI.onblur = function() {
            var i = 100;
            if (!isInt(this.value) || this.value - d.value > 0) {
                show_status_tip("最小值不合法", 4000);
                i = (0 - d.value < 0) ? 0 : toInt(d.value) - 1;
            } else {
                i = toInt(this.value);
            }
            cA.dataNode._minvalue = i;
            this.value = i;
            cA.get_span_min_value().innerHTML = "(" + i + ")";
        };
        d.onchange = d.onblur = function() {
            if (!isInt(this.value) || this.value - aI.value < 0) {
                show_status_tip("最大值不合法", 4000);
                val = (100 - aI.value > 0) ? 100 : toInt(aI.value) + 1;
            } else {
                val = toInt(this.value);
            }
            cA.dataNode._maxvalue = val;
            this.value = val;
            cA.get_span_max_value().innerHTML = "(" + val + ")";
        };
        b9.onchange = b9.onblur = function() {
            this.value = replace_specialChar(this.value);
            cA.get_span_min_value_text().innerHTML = cA.dataNode._minvaluetext = this.value;
        };
        at.onchange = at.onblur = function() {
            this.value = replace_specialChar(this.value);
            cA.get_span_max_value_text().innerHTML = cA.dataNode._maxvaluetext = this.value;
        };
    } else {
        if (dY) {
            var V = document.createElement("a");
            V.innerHTML = "<b>插入填空</b>";
            V.className = "link-U00a6e6";
            V.href = "javascript:void(0);";
            V.onclick = function() {

				var i = G.value.length;
				G.focus();
				if (typeof document.selection != "undefined") {
					document.selection.createRange().text = GapFillStr;
				} else {
					G.value = G.value.substr(0, G.selectionStart) + GapFillStr + G.value.substring(G.selectionEnd, i);
				}
				cA.checkTitle();

                return false;
            };
            dT.appendChild(V);
            $ce("span", "&nbsp;&nbsp;<b>提示：</b>填空用连续三个下划线'_ _ _'表示，填空长度跟下划线的个数相关。", dT);
        }
    }
    if (d5) {
        dT.innerHTML = "<span style='font-size:10px'>●&nbsp;</span>";
        $ce("span", "上传文件允许的最大文件大小(KB)：", dT);
        var K = control_text("8");
        var dF = fileMaxSize / 1024;
        K.maxLength = 5;
        dT.appendChild(K);
        $ce("span", "&nbsp;不能超过" + fileMaxSize + "KB(" + dF + "M)。提示：文件上传后最多保存5年。", dT);
        K.onblur = K.onchange = function() {
            var i = K.value;
            if (i) {
                if (isPositive(i) && i - fileMaxSize <= 0) {
                    cA.dataNode._maxsize = i;
                } else {
                    cA.dataNode._maxsize = fileMaxSize;
                    show_status_tip("最大文件大小必须为正数，并且不能超过" + fileMaxSize + "KB（即" + dF + "M）", 3000);
                    this.value = "";
                }
            } else {
                cA.dataNode._maxsize = fileMaxSize;
            }
            cA.updateFileUpload();
        };
        $ce("br", "", dT);
        $ce("span", "<span style='font-size:10px'>●&nbsp;</span>上传文件允许的扩展名：", dT);
        var am = "<div><b>&nbsp;&nbsp;图片文件：</b><input type='checkbox' value=''/>全选&nbsp;&nbsp;<input type='checkbox' value='.gif'/>.gif&nbsp;<input type='checkbox' value='.png'/>.png&nbsp;<input type='checkbox' value='.jpg'/>.jpg</div>";
        //am += "<div><b>&nbsp;&nbsp;文档文件：</b><input type='checkbox' value=''/>全选&nbsp;&nbsp;<input type='checkbox' value='.doc'/>.doc&nbsp;<input type='checkbox' value='.docx'/>.docx&nbsp;<input type='checkbox' value='.pdf'/>.pdf&nbsp;<input type='checkbox' value='.xls'/>.xls&nbsp;<input type='checkbox' value='.xlsx'/>.xlsx&nbsp;<input type='checkbox' value='.ppt'/>.ppt&nbsp;<input type='checkbox' value='.pptx'/>.pptx&nbsp;<input type='checkbox' value='.txt'/>.txt&nbsp;</div>";
        //am += "<div><b>&nbsp;&nbsp;压缩文件：</b><input type='checkbox' value=''/>全选&nbsp;&nbsp;<input type='checkbox' value='.rar'/>.rar&nbsp;<input type='checkbox' value='.zip'/>.zip&nbsp;<input type='checkbox' value='.gzip'/>.gzip</div>";
        var dU = $ce("div", am, dT);
        cA.updateExt = function(em) {
            var i = $$("input", dU);
            var dw = "";
            for (var j = 0; j < i.length; j++) {
                if (i[j].checked && i[j].value) {
                    if (dw) {
                        dw += "|";
                    }
                    dw += i[j].value;
                }
            }
            this.dataNode._ext = dw;
            this.updateFileUpload();
        };
        if (bM) {
            var bR = document.createElement("div");
            dc.appendChild(bR);
            this.addCeShiSetting(bR);
        }
    }
    if (cG && cA.dataNode._verify != "多级下拉") {
        if (bM) {
            var bR = document.createElement("div");
            dc.appendChild(bR);
            this.addCeShiSetting(bR);
        }
        var cQ = this.get_span_maxword();
        var bV = this.get_textarea();
        var bC = $ce("span", "高度：", dT);
        var dM = "<select onchange='cur.setTHeight(this);'><option value='1'>1行</option><option value='2'>2行</option><option value='3'>3行</option><option value='4'>4行</option><option value='5'>5行</option><option value='10'>10行</option><option value='自定义'>自定义</option></select>&nbsp;";
        bC.innerHTML += dM;
        var df = $ce("span", "", dT);
        var aO = control_text("3");
        aO.maxLength = 3;
        aO.onchange = aO.onblur = function() {
            var i = this.value;
            if (!isEmpty(i) && !isInt(i)) {
                show_status_tip("您输入的高度不合法！");
                this.value = "";
                cA.dataNode._height = "1";
            } else {
                cA.dataNode._height = i ? parseInt(i) : "";
            }
            c4.style.display = "";
            cA.showTextAreaHeight();
        };
        this.setTHeight = function(i) {
            if (i.value != "自定义") {
                aO.value = i.value;
                df.style.display = "none";
                aO.onchange();
            } else {
                df.style.display = "";
            }
        };
        this.initHeight = function() {
            if (this.dataNode._height) {
                var i = $$("select", bC)[0];
                i.value = this.dataNode._height;
                if (i.selectedIndex == -1) {
                    i.value = "自定义";
                    this.setTHeight(selWidth);
                }
            }
        };
        df.style.display = "none";
        df.appendChild(aO);
        var c4 = $ce("span", "&nbsp;&nbsp;", dT);
        c4.style.display = "none";

        var cz = $ce("span", "&nbsp;&nbsp;宽度：", dT);
        var cL = "<select onchange='cur.setTWidth(this);'><option value=''>默认</option><option value='50'>50</option><option value='100'>100</option><option value='200'>200</option><option value='300'>300</option><option value='400'>400</option><option value='500'>500</option><option value='600'>600</option><option value='自定义'>自定义</option></select>&nbsp;";
        cz.innerHTML += cL;
        var aT = control_text("5");
        aT.maxLength = 3;
        aT.onchange = aT.onblur = function() {
            var i = this.value;
            if (!isEmpty(i) && !isInt(i)) {
                show_status_tip("您输入的宽度不合法！");
                this.value = "";
                cA.dataNode._width = "";
            } else {
                cA.dataNode._width = i ? parseInt(i) : "";
                if (i == "1" && cA.dataNode._requir) {
                    m.click();
                }
            }
            cA.showTextAreaWidth();
        };
        this.setTWidth = function(i) {
            if (i.value != "自定义") {
                aT.value = i.value;
                aT.style.display = "none";
                aT.onchange();
            } else {
                aT.style.display = "";
            }
        };
        this.initWidth = function() {
            if (this.dataNode._width) {
                var i = $$("select", cz)[0];
                i.value = this.dataNode._width;
                if (i.selectedIndex == -1 || this.dataNode._width == "1") {
                    i.value = "自定义";
                    this.setTWidth(i);
                }
            }
        };
        dT.appendChild(cz);
        aT.style.display = "none";
        dT.appendChild(aT);
        var O = $ce("span", "&nbsp;&nbsp;", dT);
        O.style.display = "none";
        var cn = control_check();
        O.appendChild(cn);
        O.appendChild(document.createTextNode("下划线样式"));
        cn.onclick = function() {
            cA.dataNode._underline = this.checked;
            cA.showTextAreaUnder();
        };
        var aS = $ce("span", "&nbsp;&nbsp;", dT);
        aS.style.display = "none";
        var A = control_check();
        aS.appendChild(A);
        var cK = "默认值";
        var cq = $ce("span", cK, aS);
        var bL = control_textarea("1", "18");
        bL.style.overflow = "auto";
        bL.style.height = "";
        bL.style.display = "none";
        bL.style.verticalAlign = "middle";
        bL.maxLength = "20";
        bL.title = "最多输入20个字符";
        A.onclick = function() {
            bL.style.display = bL.style.display == "none" ? "": "none";
            if (!this.checked) {
                cA.dataNode._olddefault = bL.value;
                bL.value = "";
            } else {
                bL.value = cA.dataNode._olddefault || "";
            }
            bL.onchange();
        };
        aS.appendChild(bL);
        bL.onchange = bL.onblur = function() {
            if (cA.checkDefault) {
                cA.checkDefault();
            }
        };
        this.changeDefaultAttr = function(i) {
            if (this.dataNode._verify == "省市区" || this.dataNode._verify == "高校") {
                cq.innerHTML = "指定省份";
                bL.onmouseover = function() {
                    openProvinceWindow(cA, this);
                };
                bL.onmouseout = function() {
                    sb_setmenunav(toolTipLayer, false);
                };
            } else {
                cq.innerHTML = cK;
                bL.onmouseover = bL.onmouseout = null;
            }
            if (i) {
                bL.value = "";
                bL.onchange();
            }
        };
        var c3 = document.createElement("div");
        var b6 = control_check();
        var cm = document.createElement("span");
        c3.appendChild(cm);

        var cl = "&nbsp;";
        if (bM) {
            cl = "";
        }
        var aY = $ce("span", cl, c3);
                aY.style.display = "none";

        var ai = "字数";
        if (n._verify == "数字" || n._verify == "小数") {
            ai = "值";
        }
        var aE = $ce("span", "最小" + ai + "：", aY);
        var aw = control_text("4");
        aw.title = "不填表示无限制";
        aY.appendChild(aw);
        aw.onchange = aw.onblur = function() {
            var j = this.value;
            var i = eb.value;
            if (!isEmpty(j) && (!isInt(j) || (!isEmpty(i) && this.value - i > 0))) {
                show_status_tip("最小" + ai + "不合法", 4000);
                this.value = "";
            } else {
                if (!isEmpty(j) && cA.dataNode._verify != "数字" && cA.dataNode._verify != "小数") {
                    if (j - 3000 > 0) {
                        show_status_tip("最小字数不能超过3000", 4000);
                        this.value = "";
                    }
                }
            }
            cA.dataNode._minword = this.value;
            cA.showMinMaxWord(i, this.value);
            cA.checkDefault();
        };
        var al = $ce("span", "最大" + ai + "：", aY);
        al.style.marginLeft = "10px";
        var eb = control_text("4");
        eb.title = "不填表示无限制";
        eb.onchange = eb.onblur = function() {
            var j = this.value;
            var i = aw.value;
            if (!isEmpty(j) && (!isInt(j) || (!isEmpty(i) && this.value - i < 0))) {
                show_status_tip("最大字数不合法", 4000);
                this.value = "";
            } else {
                if (!isEmpty(j) && cA.dataNode._verify != "数字" && cA.dataNode._verify != "小数") {
                    if (j - 3000 > 0) {
                        show_status_tip("最大字数不能超过3000", 4000);
                        this.value = "";
                    }
                }
            }
            cA.dataNode._maxword = this.value;
            cA.showMinMaxWord(this.value, i);
            cA.checkDefault();
        };
        aY.appendChild(eb);
        var ct = control_check();
        var c5 = $ce("span", "&nbsp;&nbsp;", c3);
        c5.style.display = "none";
        c5.appendChild(ct);
        var f = "不允许重复";
        if (n._verify == "地图") {
            f = "不允许填写者修改";
        }
        $ce("span", f + "&nbsp;", c5);
        ct.onclick = function() {
            cA.dataNode._needOnly = this.checked;
            if (this.checked && !cA.dataNode._requir && cA.dataNode._verify != "地图") {
                show_status_tip("由于设置了唯一性，推荐将该题设为必答题", 4000);
                m.click();
            }
        };
        ct.title = "要求每个人填写的答案是唯一的";
        var bU = control_check();

        dc.appendChild(c3);
        this.changeDefaultAttr();

    } else {

    }
    if (dY) {
        var I = "填空属性";
        if (bM) {
            I = "答案设置";
        }
        var dG = $ce("div", "", aD);
        var cH = $ce("span", "&nbsp;&nbsp;", dG);
        cH.style.display = "none";

        dG.style.margin = "15px 0 15px 10px";

    }
    if (dW) {
        var cp = document.createElement("div");
        cp.style.clear = "both";
    }
    var dX = false;
    for (var dJ = 0; dJ < questionHolder.length; dJ++) {
        var J = questionHolder[dJ].dataNode;
        if (J._type == "cut" || J._type == "page") {
            continue;
        }
        var d4 = J._topic;
        if (d4 - this.dataNode._topic < 0 && J._type == "check") {
            dX = true;
            break;
        } else {
            if (d4 - this.dataNode._topic >= 0) {
                break;
            }
        }
    }
    if (au || u) {
        var bs = document.createElement("div");
        bs.style.padding = "5px 0 0";
        if (au) {
            cp.appendChild(bs);
            bs.style.width = "315px";
            if (bW) {
                bs.style.width = "385px";
            }
        } else {
            dc.appendChild(bs);
        }
        var di = document.createElement("div");
        bs.appendChild(di);
        di.className = "matrixtitle";
        di.style.width = "315px";
        if (bW) {
            di.style.width = "186px";
        }
        if (u) {
            bs.style.width = "400px";
            var co = $ce("div", "可分配的总比重值：", di);
            co.style.marginBottom = "10px";
            var cP = control_text("3");
            cP.style.height = "20px";
            cP.maxLength = 3;
            cP.style.overflow = "auto";
            cP.value = this.dataNode._total || "100";
            co.appendChild(cP);
            cP.onchange = cP.onblur = function() {
                if (isInt(this.value) && parseInt(this.value) > 0) {
                    cA.dataNode._total = parseInt(this.value);
                } else {
                    cA.dataNode._total = 100;
                    show_status_tip("可分配总比重值要大于0", 4000);
                }
                this.value = cA.dataNode._total;
            };
        }
        var bf = au ? "左行标题": "比重评估项目";
        if (bW) {
            bf = "行标题";
        }
        var b5 = bW ? "16": "30";
        var ej = "7";
        if (isMergeAnswer && !this.isMergeNewAdded) {
            a6 = "";
        }
        if (!dX) {
            a6 = "";
        }
        var ca = $ce("div", "<span style='float:left;'><b>" + bf + "</b></span>", di);
        ca.className = "matrixhead";
        ca.style.paddingLeft = "4px";
		/*
        if (au && !bW) {
            var bn = $ce("span", "", ca);
            bn.className = "spanRight";
            bn.style.paddingRight = "20px";
            var dj = control_check();
            bn.appendChild(dj);
            var bx = $ce("span", "右行标题", bn);
            dj.checked = n._rowtitle2 ? true: false;
        }
		*/
        $ce("div", "", ca).className = "divclear";
        var ei = control_textarea(ej, b5);
        ei.style.overflow = "auto";
        ei.tabIndex = 1;
        ei.value = this.dataNode._rowtitle;
        ei.style.padding = "2px";
        ei.style.height = "142px";
        if ( cV == "201" || cV == "202") {
            ei.style.height = "154px";
            if (cV == "201" || cV == "202") {
                ei.style.width = "308px";
            }
        }
        if (!u) {
            ei.style.marginTop = "7px";
            if (cV == "102") {
                ei.style.height = "112px";
            }
        } else {
            ei.style.width = "308px";
            ei.style.height = "154px";
        }
        if (!isMergeAnswer || this.isMergeNewAdded) {
            ei.title = "相当于每个小题的标题";
        } else {
            ei.oldLen = ei.value.split("\n").length;
            ei.oldValue = ei.value;
            ei.title = "特别提示：有答卷的情况下只能修改文字，不能增加或删除行标题，也不能移动行标题顺序";
            ei.onclick = function() {
                if (!ei.alert) {
                    alert(ei.title);
                    ei.alert = true;
                }
            };
            ei.onkeypress = function(i) {
                var i = i || window.event;
                if (i.keyCode == 13) {
                    alert("有答卷的情况下不能添加新行，只能修改文字内容！");
                    if (i.preventDefault) {
                        i.preventDefault();
                    }
                    if (i.returnValue !== undefined) {
                        i.returnValue = false;
                    }
                    event.keyCode = 0;
                    return false;
                }
            };
        }
        di.appendChild(ei);
        this.checkRowTitle = function() {
            this.popHint("");
            var dw = "";
            ei.value = replace_specialChar(ei.value);
            if (trim(ei.value) == "") {
                ei.value = "外观\n功能";
            }
            var ep = ei.value.split("\n");
            var eo = 0;
            for (var en = 0; en < ep.length; en++) {
                if (trim(ep[en]) != "") {
                    if (eo > 0) {
                        dw += "\n";
                    }
                    dw += ep[en];
                    eo++;
                }
                for (var em = en + 1; em < ep.length; em++) {
                    if (trim(ep[en]) != "" && trim(ep[en]) == trim(ep[em])) {
                        this.popHint(bf + "的第" + (en + 1) + "行与第" + (em + 1) + "行重复，请修改！");
                        this.isRowTitleValid = false;
                        return false;
                    }
                }
            }
            var eq = dw.split("\n").length;
            if (ei.oldLen && eq != ei.oldLen) {
                if (!confirm("有答卷的情况下不能增加或删除行标题，只能修改文字内容！\r\n是否还原为初始状态的值？")) {
                    this.isRowTitleValid = false;
                    return false;
                }
                dw = ei.oldValue;
            }
            this.isRowTitleValid = true;
            ei.value = dw;
            this.dataNode._rowtitle = dw;
            return true;
        };
        ei.onfocus = function() {
            if (this.value == "外观\n功能") {
                this.value = "";
            }
        };
        ei.onblur = function() {
            if (!this.value) {
                this.value = "外观\n功能";
            }
            var i = cA.checkRowTitle();
            if (u) {
                cA.createSum();
            } else {
                if (au && i) {
                    cA.updateItem();
                    if (cA.refreshSelRow) {
                        cA.refreshSelRow();
                    }
                }
            }
        };
		/*
        if (au && !bW) {
            var bI = document.createElement("div");
            bI.style.display = dj.checked ? "": "none";
            bI.style.width = "159px";
            bI.className = "spanLeft matrixhead";
            bs.appendChild(bI);
            var cB = control_check();
            var S = $ce("div", "", bI);
            S.appendChild(cB);
            $ce("span", "右行标题(可选)", S);
            cB.checked = dj.checked;
            dj.onclick = cB.onclick = function() {
                bI.style.display = this.checked ? "": "none";
                ei.style.width = this.checked ? "150px": "290px";
                if (!this.checked && (cV == "201" || cV == "202")) {
                    ei.style.width = "308px";
                }
                di.style.width = this.checked ? "156px": "315px";
                aL.style.display = this.checked ? "": "none";
                dj.style.display = this.checked ? "none": "";
                bx.style.display = this.checked ? "none": "";
                dj.checked = cB.checked = this.checked;
                if (!this.checked) {
                    dA.prevValue = dA.value;
                    dA.value = "";
                } else {
                    if (!dA.value) {
                        dA.value = dA.prevValue || "";
                    }
                }
                dA.onblur();
            };
            var dA = control_textarea("7", "14");
            bI.appendChild(dA);
            dA.style.overflow = "auto";
            dA.value = this.dataNode._rowtitle2 || "";
            dA.title = "适用于“语义差异法”等场景";
            dA.style.padding = "2px";
            dA.style.margin = "7px 0 0 4px";
            if (cV != "201" && cV != "202") {
                dA.style.height = "142px";
                if (cV == "102") {
                    dA.style.height = "112px";
                }
            } else {
                dA.style.marginLeft = "10px";
            }
            this.checkRowTitle2 = function() {
                if (au) {
                    dA.value = replace_specialChar(dA.value);
                    this.dataNode._rowtitle2 = dA.value;
                }
                return true;
            };
            dA.onblur = function() {
                cA.checkRowTitle2();
                cA.updateItem();
            };
        }
		*/
        if (bW) {
            var b = document.createElement("div");
            setFloat(b);

            b.style.width = "186px";

            bs.appendChild(b);
            $ce("div", "<b>列标题</b>&nbsp;", b).className = "matrixhead";
            var aR = control_textarea("7", "17");
            aR.style.overflow = "auto";
            aR.value = this.dataNode._columntitle;
            aR.style.margin = "0px";
            aR.style.height = "142px";
            if (bW) {
                aR.style.height = "154px";
            }
            aR.style.padding = "2px";
            aR.style.margin = "7px 0 0 4px";
            if (!isMergeAnswer || this.isMergeNewAdded) {
                aR.title = "列标题";
            } else {
                aR.disabled = true;
                aR.title = "提示：部分修改问卷模式下不能更改列标题！";
            }
            b.appendChild(aR);
            aR.onblur = function() {
                var i = cA.checkColumnTitle();
                if (i) {
                    cA.updateItem();
                    if (cA.refreshSelRow) {
                        cA.refreshSelRow();
                    }
                }
            };
            this.checkColumnTitle = function() {
                this.popHint("");
                if (trim(aR.value) == "") {
                    this.popHint("列标题不能为空！");
                    aR.focus();
                    this.isColumnTitleValid = false;
                    return false;
                } else {
                    var en = aR.value.split("\n");
                    for (var em = 0; em < en.length; em++) {
                        for (var dw = em + 1; dw < en.length; dw++) {
                            if (trim(en[em]) != "" && trim(en[em]) == trim(en[dw])) {
                                this.popHint("列标题的第" + (em + 1) + "行与第" + (dw + 1) + "行重复，请修改！");
                                this.isColumnTitleValid = false;
                                return false;
                            }
                        }
                    }
                }
                this.isColumnTitleValid = true;
                aR.value = replace_specialChar(aR.value);
                this.dataNode._columntitle = aR.value;
                return true;
            };
        }
        $ce("div", "", bs).className = "divclear";
        this.addLabel = function() {
            var i = "\n【标签】标签名";
            var j = ei.value.length;
            ei.focus();
            if (typeof document.selection != "undefined") {
                document.selection.createRange().text = i;
            } else {
                ei.value = ei.value.substr(0, ei.selectionStart) + i + ei.value.substring(ei.selectionEnd, j);
            }
            ei.onblur();
        };
        var H = $ce("div", "&nbsp;&nbsp;" + a6, bs);
        H.style.margin = "12px 0 8px";

		/*
        if (au) {
            var dm = control_check();
            dm.onclick = function() {
                cA.dataNode._randomRow = this.checked;
            };
            dm.checked = cA.dataNode._randomRow;
            var Y = document.createElement("span");
            Y.innerHTML = "行标题随机&nbsp;&nbsp;";
            Y.title = "标题随机显示";
            H.appendChild(dm);
            H.appendChild(Y);
        }
        if (bW) {
            var dh = "&nbsp;&nbsp;<a href='javascript:void(0);' class='link-U666' onclick='cur.changeRowColumnTitle();return false;'>交换行列标题</a>";
            $ce("span", dh, H);
            this.changeRowColumnTitle = function() {
                var i = this.dataNode._rowtitle;
                this.dataNode._rowtitle = this.dataNode._columntitle;
                this.dataNode._columntitle = i;
                ei.value = this.dataNode._rowtitle;
                aR.value = cur.dataNode._columntitle;
                this.updateItem();
            };
        }
		*/
        bs.appendChild(U);
        U.appendChild(d8);
        if (au) {
            var dZ = document.createElement("div");
            dZ.style.margin = "8px 0 5px";
            $ce("span", "最小值：", dZ);
            var cs = control_text("3");
            cs.title = "用户可以选择的最小值";
            cs.maxLength = 3;
            cs.value = this.dataNode._minvalue;
            dZ.appendChild(cs);
            cs.onchange = cs.onblur = function() {
                var i = this.value;
                if (!isEmpty(this.value) || cA.dataNode._tag == "202") {
                    if (!isInt(this.value) || this.value - aq.value > 0) {
                        show_status_tip("最小值不合法", 4000);
                        i = (0 - aq.value < 0) ? 0 : toInt(aq.value) - 1;
                        if (parseInt(i) != i) {
                            i = 0;
                        }
                    } else {
                        i = toInt(this.value);
                    }
                }
                this.value = cA.dataNode._minvalue = i;
                cA.updateItem();
                if (cA.updateSpanCheck) {
                    cA.updateSpanCheck();
                }
            };
            var dq = $ce("span", "最大值：", dZ);
            dq.style.marginLeft = "60px";
            var aq = control_text("3");
            aq.title = "用户可以选择的最大值";
            aq.maxLength = 3;

            aq.value = this.dataNode._maxvalue;
            dZ.appendChild(aq);

            dZ.style.display = (this.dataNode._tag == "202") ? "": "none";
            aq.onchange = aq.onblur = function() {
                var i = this.value;
                if (!isEmpty(this.value) || cA.dataNode._tag == "202") {
                    if (!isInt(this.value) || this.value - cs.value < 0) {
                        show_status_tip("最大值不合法", 4000);
                        i = (10 - cs.value > 0) ? 10 : toInt(cs.value) + 1;
                        if (parseInt(i) != i) {
                            i = 10;
                        }
                    } else {
                        i = toInt(this.value);
                    }
                }
                this.value = cA.dataNode._maxvalue = i;
                cA.updateItem();
                if (cA.updateSpanCheck) {
                    cA.updateSpanCheck();
                }
            };
            bs.appendChild(dZ);
        }
    }
    if (dW) {
        $ce("div", "", dc).style.paddingTop = "10px";
        var d0 = document.createElement("div");
        d0.style.margin = "12px 0 5px";
        if (N || cR) {
            d0.style.display = "none";
        }
        var de = $ce("div", "", d0);
        if (aB || aX) {
            var bP = document.createElement("span");
            bP.className = "spanRight";
            de.appendChild(bP);
            var bp = "<ul class='likertImageTypeList' style='display:inline;margin:0;' ><li>&nbsp;&nbsp;<b>样式：</b></li>";
            if (au) {
                bp += "<li class='design-icon design-offr' onclick='cur.set_likertMode(101,this);' style='background:url(/images/radio.gif) no-repeat;height:16px;width:18px;'></li>";
            } else {
                bp += "<li style='font-size:16px;'><a style='height:24px;line-height:24px;' href='javascript:' onclick='cur.set_likertMode(1,this);return false;'><b>123</b></a></li> <li class='design-icon design-offr' style='background:url(/images/radio.gif) no-repeat;height:16px;width:18px;' onclick='cur.set_likertMode(101,this);'></li>";
            }
            bp += "<li class='off2' onclick='cur.set_likertMode(2,this);' style='margin-top:-3px;'></li>";
            bp += "<li  class='off3' onclick='cur.set_likertMode(3,this);' style='margin-top:-3px;'></li>";
            bp += "<li  class='off4' onclick='cur.set_likertMode(4,this);' style='margin-top:-3px;'></li>";
            bp += "<li class='design-icon design-off6' onclick='cur.set_likertMode(6,this);' style='margin-top:-3px;'></li>";
            bp += "<li style='clear:both;'></li>";
            bp += "</ul>";
            bP.innerHTML = bp;
            this.set_likertMode = function(i, dw) {
                var j = this.dataNode._tag < 102;
                this.dataNode._tag = i;
                this.createTableRadio(true);
                if (this.prevModeObj) {
                    if (this.prevMode == 6) {
                        this.prevModeObj.className = "design-icon design-off6";
                    } else {
                        if (this.prevMode == 101) {
                            this.prevModeObj.style.backgroundPosition = "0 0px";
                        } else {
                            this.prevModeObj.className = "off" + this.prevMode;
                        }
                    }
                    this.prevModeObj = null;
                }
                if (i == 2 || i == 3 || i == 4 || i == 6) {
                    if (i == 6) {
                        dw.className = "design-icon design-off6 design-on6";
                    } else {
                        dw.className = "on" + i;
                    }
                    this.prevModeObj = dw;
                    this.prevMode = i;
                } else {
                    if (i == 101) {
                        dw.style.backgroundPosition = "0 -16px";
                        this.prevModeObj = dw;
                        this.prevMode = i;
                    }
                }
                if (this.dataNode._type == "matrix") {
                    d0.style.display = i > 200 ? "none": "";
                    b0.style.display = d0.style.display;
                    if (j && i > 101) {
                        Q.disabled = false;
                        Q.checked = false;
                        Q.onclick();
                        this.dataNode._hasvalue = false;
                    } else {
                        if (!j && i < 102) {
                            Q.checked = true;
                            Q.onclick();
                            Q.disabled = true;
                            this.dataNode._hasvalue = true;
                        }
                    }
                    Q.disabled = i > 101 ? false: true;
                    if (!isMergeAnswer || this.isMergeNewAdded) {
                        cF.style.display = i > 101 ? "none": "";
                    }
                    if (i == 202) {
                        dZ.style.display = "";
                        this.dataNode._minvalue = this.dataNode._minvalue || 0;
                        this.dataNode._maxvalue = this.dataNode._maxvalue || 10;
                        this.dataNode._rowwidth = this.dataNode._rowwidth || 100;
                        rowwidth_text.value = rowwidth_text.value || 100;
                    } else {
                        dZ.style.display = "none";
                    }
                }
            };
            if (isMergeAnswer && !this.isMergeNewAdded && this.dataNode._tag > 101) {
                bP.style.display = "none";
            }
        }
        if (((aX) || au) && (!isMergeAnswer || this.isMergeNewAdded)) {
            var cF = $ce("span", "", de);
            cF.className = "spanRight";
            var cD = document.createElement("span");
            var b4 = "<select onchange='cur.set_likert_num(this);'>";
            var q = n._select.length - 1;
            for (var dJ = 2; dJ <= 11; dJ++) {
                var b3 = "";
                if (dJ == q) {
                    b3 = " selected='selected' ";
                }
                var cI = dJ + "级量表";
                if (dJ == 11) {
                    cI = "NPS量表";
                }
                b4 += "<option" + b3 + " value='" + dJ + "'>" + cI + "</option>";
            }
            b4 += "</select>";
            cD.innerHTML += b4;
            this.set_likert_num = function(eo) {
                var ep = eo.value;
                var em = cY.length - 1;
                var dw = ep == "11";
                var j = 1;
                if (dw) {
                    j = 0;
                }
                for (var i = 0; i < ep; i++) {
                    cY[em + i].get_item_add().onclick();
                    cY[em + i + 1].get_item_title().value = i + j;
                    cY[em + i + 1].get_item_value().value = i + j;
                }
                for (var i = 1; i < em + 1; i++) {
                    cY[1].get_item_del().onclick();
                }
                if (!aB) {
                    var en = "非常不满意";
                    var eq = "非常满意";
                    if (dw) {
                        en = "不可能";
                        eq = "极有可能";
                    }
                    cY[1].get_item_title().value = en;
                    cY[cY.length - 1].get_item_title().value = eq;
                }
                cY[1].get_item_title().onchange();
                if (dw) {
                    this.dataNode._tag = "6";
                    this.createTableRadio(true);
                }
                this.popHint("");
                window.focus();
            };
            cF.appendChild(cD);
            if (au && this.dataNode._tag > 101) {
                cF.style.display = "none";
            }
        }
        if (cx == "radio" || cx == "check") {
            if (dS) {
                cp.style.width = "98%";
            } else {
                if (cV) {
                    cp.style.width = "75%";
                } else {
                    if (bM) {
                        cp.style.width = "70%";
                    } else {
                        if (N) {
                            cp.style.width = "62%";
                        } else {
                            if (cR) {
                                cp.style.width = "70%";
                            } else {
                                if (eg) {
                                    if (cx == "radio") {
                                        cp.style.width = "100%";
                                    } else {
                                        cp.style.width = "86%";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            if (cx == "matrix") {
                if (cV == "201" || cV == "202") {
                    cp.style.width = "400px";
                }
            } else {
                cp.style.width = "75%";
            }
        }
        if (!au) {
            d0.appendChild(U);
            U.appendChild(d8);
        }
        this.setNotNewAdd = function() {
            if (!cA.newAddQ) {
                return;
            }
            c0.style.display = "";
            cA.newAddQ = false;
            b0.style.display = "";
        };
        this.addNewItem = function() {
            var i = cY.length;
            cY[i - 1].get_item_add().onclick();
        };
        if (bB) {
            var az = $ce("div", "<span style='color:#333; font-weight:bold;'>投票设置：</span>", d0);
            az.style.marginTop = "10px";
            var dE = $ce("span", "&nbsp;&nbsp;", az);
            var bu = $ce("span", "", dE);
            var ce = control_check();
            bu.appendChild(ce);
            bu.appendChild(document.createTextNode("显示缩略图"));
            var h = control_check();
            dE.appendChild(h);
            dE.appendChild(document.createTextNode("显示投票数"));
            var ds = control_check();
            dE.appendChild(ds);
            dE.appendChild(document.createTextNode("显示百分比"));
            h.checked = h.defaultChecked = n._displayNum;
            ds.checked = ds.defaultChecked = n._displayPercent;
            ce.checked = ce.defaultChecked = n._displayThumb;
            ce.onclick = function() {
                cA.dataNode._displayThumb = this.checked;
                cA.createTableRadio(true);
            };
            ds.onclick = function() {
                cA.dataNode._displayPercent = this.checked;
                cA.createTableRadio(true);
            };
            h.onclick = function() {
                cA.dataNode._displayNum = this.checked;
                cA.createTableRadio(true);
            };
            var P = $ce("span", "选项宽度：", az);
            cA.setchoiceWidth = function() {
                var eo = false;
                var em = true;
                for (var en = 1; en < cA.dataNode._select.length; en++) {
                    var dw = cA.dataNode._select[en]._item_img;
                    if (dw) {
                        if (dw.indexOf(".sojump.com") == -1 && dw.indexOf(".paperol.cn") == -1) {
                            em = false;
                        }
                        eo = true;
                        break;
                    }
                }
                P.style.display = eo ? "none": "";
                var j = eo && em;
                if (!j) {
                    cA.dataNode._displayThumb = false;
                    ce.checked = false;
                    ce.onclick();
                }
                bu.style.display = j ? "": "none";
            };
            cA.setchoiceWidth();
            P.style.marginLeft = "8px";
            var dL = "<select onchange='cur.setTWidth(this);'><option value='20'>20%</option><option value='30'>30%</option><option value='40'>40%</option><option value='50'>50%</option><option value='60'>60%</option><option value='70'>70%</option><option value='自定义'>自定义</option></select>&nbsp;";
            P.innerHTML += dL;
            var s = $ce("span", "", P);
            var b1 = control_text("3");
            b1.maxLength = 2;
            b1.onchange = b1.onblur = function() {
                var i = this.value;
                if (!isEmpty(i) && !isInt(i)) {
                    show_status_tip("您输入的宽度不合法！");
                    this.value = "";
                    cA.dataNode._touPiaoWidth = 50;
                } else {
                    cA.dataNode._touPiaoWidth = i ? parseInt(i) : "";
                }
                cA.createTableRadio(true);
            };
            this.setTWidth = function(i) {
                if (i.value != "自定义") {
                    b1.value = i.value;
                    s.style.display = "none";
                    b1.onchange();
                } else {
                    s.style.display = "";
                }
            };
            this.initWidth = function() {
                if (this.dataNode._touPiaoWidth) {
                    b1.value = this.dataNode._touPiaoWidth;
                    var em = $$("select", P)[0];
                    em.value = this.dataNode._touPiaoWidth;
                    var j = true;
                    for (var dw = 0; dw < em.options.length; dw++) {
                        if (em.options[dw].value == this.dataNode._touPiaoWidth) {
                            j = false;
                            break;
                        }
                    }
                    if (j) {
                        em.value = "自定义";
                        this.setTWidth(em);
                    }
                }
            };
            s.appendChild(b1);
            s.appendChild(document.createTextNode("%"));
            s.style.display = "none";
            az.appendChild(P);
            $ce("span", "&nbsp;&nbsp;&nbsp;&nbsp;", az);
            this.initWidth();
        } else {
            if (bM) {
                this.addCeShiSetting(d0);
            }
        }
        var b0 = $ce("div", "", cp);
        var aW = document.createElement("table");
        aW.className = "tableoption";
        aW.cellSpacing = "0";
        aW.cellPadding = "0";
        aW.width = "98%";
        var cr = aW.insertRow( - 1);
        cr.style.background = "#e1e0e0";
        cY[0] = cr;
        var bb = !n._tag && !eg && (n._type == "check" || n._type == "radio" || n._type == "radio_down");
        var D = cr.insertCell( - 1);
        D.style.width = "150px";
        D.style.paddingRight = "20px";
        if (au && cV && cV <= 101) {
            D.style.width = "80px";
        }
        if (dS) {
            D.style.width = "400px";
        }
        var dH = $ce("span", "", D);
        if (cR) {
            dH.innerHTML = "商品名称";
        } else {
            var dR = $ce("a", "选项文字<i class='design-icon design-ctext'>", dH);
            dR.title = "交换选项文字";
            dR.style.color = "#222";
            dR.style.textDecoration = "none";
            dR.href = "javascript:void(0);";
            dR.onclick = function() {
                if (isMergeAnswer && !cur.isMergeNewAdded) {
                    show_status_tip("提示：在部分修改问卷模式下，不允许交换选项文字", 4000);
                    return false;
                }
                var en = false;
                var j = false;
                if (aX || aB || eg ) {
                    j = true;
                }
                if (j && confirm("是否同时交换选项分数？")) {
                    en = true;
                }
                var em = 1;
                var i = cY.length - 1;
                while (em < i) {
                    var dw = cY[i].get_item_title().value;
                    cY[i].get_item_title().value = cY[em].get_item_title().value;
                    cY[em].get_item_title().value = dw;
                    if (en) {
                        dw = cY[i].get_item_value().value;
                        cY[i].get_item_value().value = cY[em].get_item_value().value;
                        cY[em].get_item_value().value = dw;
                        if (cY[i].get_item_novalue()) {
                            dw = cY[i].get_item_novalue().checked;
                            cY[i].get_item_novalue().checked = cY[em].get_item_novalue().checked;
                            cY[em].get_item_novalue().checked = dw;
                        }
                    }
                    em++;
                    i--;
                }
                cur.updateItem();
                return false;
            };
        }
        var ef = bB || (bb && !eg && !N && !cR && !bM);
        if (eg || (bb && n._type != "radio_down" && !N) || dS) {
            var ap = cr.insertCell( - 1);
            var d3 = "图片";
            imgwidth = "50px";
            if (cR) {
                d3 = "商品图片";
                imgwidth = "60px";
            }
            $ce("span", d3, ap);
            ap.style.width = imgwidth;
			ap.align = "center";
        }

        if ((bb && n._type != "radio_down") || eg || dS) {
            var dl = cr.insertCell( - 1);
            dl.style.letterSpacing = "1px";
            var an = $ce("span", "允许填空", dl);
            dl.style.width = "80px";
            dl.align = "center";
            if (bM || N || cR) {
                dl.style.display = "none";
            }
        }
        var ay = cr.insertCell( - 1);
        if (!bM && !bW && !N) {
            ay.style.width = "92px";
        }
        if (cR) {
            ay.style.width = "60px";
        }
        var br = $ce("span", "", ay);
        var Q = control_check();
        Q.title = "给选项设置分数，可用于Likert量表或者测试类型的问卷";
        br.appendChild(Q);
        var dd = $ce("span", "", br);
        dd.innerHTML = "&nbsp;分数<span class='bordCss bordBottomCss' style='border-color:#333 transparent transparent;'></span>";
        dd.style.cursor = "pointer";
        if (N) {
            dd.innerHTML = "数量限制";
        } else {
            if (cR) {
                dd.innerHTML = "商品价格";
            }
        }
        if (aX || aB || eg ) {
            Q.style.display = "none";
            br.onmouseover = function() {
                openValWindow(cA, this);
            };
            br.onmouseout = function() {
                sb_setmenunav(toolTipLayer, false);
            };
        } else {
            if (!N && !cR) {
                ay.style.display = "none";
            } else {
                Q.style.display = "none";
            }
        }
        Q.onclick = function() {
            if (cA.dataNode._isCeShi) {
                return;
            }
            if (this.checked) {
                for (var i = 1; i < cY.length; i++) {
                    cY[i].get_item_value().parentNode.style.display = "";
                }
            } else {
                for (var i = 1; i < cY.length; i++) {
                    cY[i].get_item_value().parentNode.style.display = "none";
                }
            }
            cA.dataNode._hasvalue = this.checked;
        };
        var bE = cr.insertCell( - 1);
		/*
        if (cx == "check" && !bM && !dS && !eg && !cR) {
            var bD = cr.insertCell( - 1);
            bD.style.width = "66px";
            var L = $ce("span", "&nbsp;", bD);
            var bK = $ce("span", "选项互斥", L);
            bK.title = "与其它选项互斥";
            L.appendChild(bK);
        }
		*/
        if (cx == "matrix") {
            bE.style.display = "none";
        }
        var ch = cr.insertCell( - 1);
        if (bb && !N && !cR) {
            var bh = $ce("span", "&nbsp;", ch);
            var bX = $ce("span", "", bh);
            bX.innerHTML = "默认";
            if (bM) {
                bX.innerHTML = "正确答案";
                ch.align = "center";
            }
            this.defaultCheckSet = function() {
                if (this.dataNode._isCeShi) {
                    return;
                }
                var j = bQ.checked || (cZ ? cZ.checked: false);
                for (var i = 1; i < cY.length; i++) {
                    var dw = cY[i].get_item_check();
                    dw.parentNode.style.display = j ? "none": "";
                    if (j) {
                        dw.checked = false;
                    }
                }
            };
        } else {
            ch.style.display = "none";
        }

        var a1 = cr.insertCell( - 1);
        a1.align = "center";
        a1.style.width = "124px";
        $ce("span", "操作", a1);
        if (bj) {
            for (var dI = 1; dI < n._select.length; dI++) {
                cY[dI] = new creat_item(cA, dI, aW, "check", false, n._select[dI]);
            }
        } else {
            for (var dI = 1; dI < n._select.length; dI++) {
                cY[dI] = new creat_item(cA, dI, aW, "radio", false, n._select[dI]);
            }
        }
        this.checkItemTitle = function() {
            this.popHint("");
            var i = true;
            if (!this.checkEmptyItem()) {
                return false;
            }
            if (!this.checkRepeatItem()) {
                return false;
            }
            this.isItemTitleValid = true;
            return true;
        };
        this.checkEmptyItem = function() {
            var em = true;
            for (var j = 1; j < cY.length; j++) {
                var dw = cY[j].get_item_title();
                if (trim(dw.value) == "") {
                    if (dw.initText) {
                        dw.value = dw.initText;
                    } else {
                        this.popHint("选项不能为空！");
                        em = false;
                        this.isItemTitleValid = false;
                    }
                }
            }
            return em;
        };
        this.checkRepeatItem = function() {
            var ep = true;
            for (var en = 1; en < cY.length; en++) {
                var em = cY[en].get_item_title();
                if (em._oldBorder || em._oldBorder == "") {
                    em.style.border = em._oldBorder;
                    em.title = em._oldTitle;
                }
                for (var dw = en + 1; dw < cY.length; dw++) {
                    if (trim(cY[en].get_item_title().value) == trim(cY[dw].get_item_title().value)) {
                        var eo = cY[dw].get_item_title();
                        em.rel = eo;
                        eo.rel = em;
                        this.popHint("第" + en + "个选项与第" + dw + "个选项重复，请修改！");
                        ep = false;
                        this.isItemTitleValid = false;
                        return false;
                    }
                }
            }
            return ep;
        };
        this.checkItemValue = function() {
            var em = true;
            if (Q.checked) {
                for (var dw = 1; dw < cY.length; dw++) {
                    var j = trim(cY[dw].get_item_value().value);
                    if (j == "") {
                        if (!cY[dw].get_item_novalue() || !cY[dw].get_item_novalue().checked) {
                            cY[dw].get_item_value().value = 0;
                        }
                    } else {
                        if (!isInt(j)) {
                            this.popHint("选项的分数不合法，请修改！");
                            em = false;
                        }
                    }
                }
            }
            this.isItemValueValid = em;
            return em;
        };
        this.checkItemJump = function(dw) {
            var eo = true;

            if (!dw) {
                this.isItemJumpValid = eo;
            } else {
                this.isItemJumpValid = false;
            }
            return eo;
        };
        this.checkCeShiSet = function() {
            if (!this.dataNode._isCeShi) {
                return true;
            }
            var j = false;
            for (var i = 1; i < n._select.length; i++) {
                if (n._select[i]._item_radio) {
                    j = true;
                }
            }
            if (!j) {
                this.popHint("请设置此题的正确答案");
            }
            this.isCeShiValid = j;
            return j;
        };
        this.setItemJump = function() {
            for (var j = 1; j < cY.length; j++) {
                cY[j].get_item_jump().value = this.dataNode._select[j]._item_jump;
            }
        };
        b0.appendChild(aW);
        dc.appendChild(cp);
        $ce("div", "", dc).className = "divclear";
        var cy = $ce("span", "", de);
        de.style.width = "98%";
        if (bM) {
            de.style.width = "68%";
            if (r) {
                de.style.display = "none";
            }
        } else {
            if (aX) {
                de.style.width = "74%";
            } else {
                if (dS) {
                    de.style.width = "85%";
                } else {
                    if (cT) {
                        de.style.width = "72%";
                    } else {
                        if (bj && eg) {
                            de.style.width = "84%";
                        }
                    }
                }
            }
        }
        cy.className = "spanLeft";
        if (cT || bM || eg || bB || (isMergeAnswer && !this.isMergeNewAdded) || au || !dX) {
            a6 = "";
        }
        var aj = "<a href='javascript:' onclick='cur.addNewItem();return false;' class='link-U00a6e6' style='text-decoration:none;'><span class='choiceimg design-icon design-singleadd' ></span><b>添加选项</b></a>&nbsp;&nbsp;";
        var dQ = "<a href='javascript:' onclick='PDF_launch(\""+oftenoptions+"\",540,400);return false;' class='batchadd' title='批量添加选项'><span class='choiceimg design-icon design-badd'></span><b>批量增加</b></a>&nbsp;";

        var bv = "";
        var dx = aj + dQ + "&nbsp;" + a6;
        if (bB) {
            dx = dx + bv;
        }
        cy.innerHTML = dx;
        if (aX) {
            cy.innerHTML = aj + dQ;
        } else {
            if (au && (this.dataNode._tag && this.dataNode._tag <= 101 || this.dataNode._tag == 102)) {
                cy.innerHTML = dQ;
            }
        }
        if (isMergeAnswer && !this.isMergeNewAdded) {
            cy.innerHTML = aj;
        }
        var cM = $ce("span", "", de);
        if ((aK && cV == 0) || (bj && !dS)) {
            var ec = null;
            var z = document.createElement("span");
            var ax = "";
            var c6 = 1;

            z.innerHTML = "<select onchange='cur.checkNumPer(this);' style='width:90px;'><option value='1'>竖向排列</option>" + ax + "<optgroup label='横向排列'><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option><option value='10'>10</option><option value='11'>11</option><option value='12'>12</option><option value='15'>15</option><option value='20'>20</option><option value='30'>30</option></optgroup></select>&nbsp;";
            ec = $$("select", z)[0];
            for (var dJ = c6; dJ < ec.options.length; dJ++) {
                ec.options[dJ].text = "每行" + ec.options[dJ].value + "列";
            }
            cM.appendChild(z);
            if (dS && ec) {
                z.style.display = "none";
            }
            this.checkNumPer = function(dw) {
                var i = trim(dw.value);
                if (i == 0) {
                    this.selChangeType("radio_down");
                    return;
                }
                this.dataNode._numperrow = parseInt(i);
                this.createTableRadio(true);
                this.focus();

            };
            z.style.display = "inline-block";
        }
        cM.className = "spanRight";
        var R = $ce("span", "", de);
        var b2 = control_check();
        R.appendChild(b2);
        $ce("span", "选项随机&nbsp;", R);
        R.className = "spanRight";
        if (bM) {
            var a = $ce("span", "<a class='titlelnk' onclick='setAllRandom();return false;' href='javascript:' title='复制随机属性到其它的考试题'>复制到其它考试题</a>&nbsp;", R);
            a.style.display = "none";
            this.get_random = function() {
                return b2;
            };
        }
        b2.onclick = function() {
            cA.dataNode._randomChoice = this.checked;
            if (bM) {
                a.style.display = "";
            }
        };
        if (aX || au) {
            R.style.display = "none";
        }
        if ((bj && !bM) || (au && cV == "102")) {
            var bd = null;
            bd = $ce("span", "", de);
            var av = "至少选 ";
            if (au) {
                av = "&nbsp;&nbsp;" + av;
            }
            $ce("span", av, bd);
            bd.className = "spanRight";
            var da = document.createElement("span");
            var aM = "<select onchange='cur.limitChange(this);' onclick='cur.lowLimitClick(this);' style='width:45px;'>";
            aM += "<option value=''></option>";
            for (var dJ = 1; dJ < n._select.length; dJ++) {
                aM += "<option value='" + dJ + "'>" + dJ + "</option>";
            }
            aM += "</select>";
            da.innerHTML = aM;
            var a4 = document.createElement("span");
            a4.innerHTML = " 项&nbsp;最多选 ";
            var aP = document.createElement("span");
            var Z = "<select onchange='cur.limitChange(this);' onclick='cur.upLimitClick(this);' style='width:45px;'>";
            Z += "<option value=''></option>";
            for (var dJ = 2; dJ < n._select.length; dJ++) {
                Z += "<option value='" + dJ + "'>" + dJ + "</option>";
            }
            Z += "</select>";
            aP.innerHTML = Z;
            var dD = document.createTextNode(" 项");
            bd.appendChild(da);
            bd.appendChild(a4);
            bd.appendChild(aP);
            bd.appendChild(dD);
            this.limitChange = function() {
                cA.checkCheckLimit();
                window.focus();
            };
            this.lowLimitClick = function(em) {
                if (n._select.length - 1 == em.options.length - 1) {
                    return;
                }
                em.options.length = 0;
                em.options.add(new Option(" ", ""));
                for (var dw = 1; dw < n._select.length; dw++) {
                    var j = new Option(dw, dw);
                    em.options.add(j);
                }
            };
            this.upLimitClick = function(em) {
                if (n._select.length - 1 == em.options.length) {
                    return;
                }
                em.options.length = 0;
                em.options.add(new Option(" ", ""));
                for (var dw = 2; dw < n._select.length; dw++) {
                    var j = new Option(dw, dw);
                    em.options.add(j);
                }
            };
            var dO = n._lowLimit;
            var d7 = n._upLimit;
            if (dS) {
                if (!n._lowLimit) {
                    dO = n._lowLimit = n._select.length - 1;
                }
                if (!n._upLimit) {
                    d7 = n._upLimit = n._select.length - 1;
                }
                if (n._lowLimit == -1) {
                    dO = "";
                }
                if (n._upLimit == -1) {
                    d7 = "";
                }
            }
            $$("select", da)[0].value = dO || "";
            $$("select", aP)[0].value = d7 || "";
            $ce("span", "&nbsp;&nbsp;", bd);
            this.checkCheckLimit = function() {
                if (bj || (au && cV == "102")) {
                    var j = $$("select", da)[0].value;
                    var dw = $$("select", aP)[0].value;
                    var i = cY.length - 1;
                    if (j != "") {
                        if (dw != "" && dw - j < 0) {
                            j = dw;
                            $$("select", da)[0].value = dw;
                            show_status_tip("要求用户最多选中选项数量不合法！", 4000);
                        }
                        if (!m.checked) {
                            show_status_tip("由于设置了选项数量限制，建议将该题设为必答", 4000);
                        }
                    } else {
                        if (dS) {
                            j = -1;
                        }
                    }
                    if (dw != "") {
                        if (j != "" && dw - j < 0) {
                            dw = j;
                            $$("select", aP)[0].value = j;
                            show_status_tip("要求用户最多选中选项数量不合法！", 4000);
                        }
                    } else {
                        if (dS) {
                            dw = -1;
                        }
                    }
                    this.dataNode._lowLimit = j;
                    this.dataNode._upLimit = dw;
                    this.updateSpanCheck();
                }
                return true;
            };
        }
        $ce("div", "", de).className = "divclear";
        if (au) {
            b0.appendChild(d0);
        } else {
            dc.appendChild(d0);
        }
        this.initFreOptions = function(en) {
            var em = "";
            var dw = /^选项\d+$/;
            var j = 0;
            for (var i = 1; i < cY.length; i++) {
                var eo = trim(cY[i].get_item_title().value);
                if (!eo) {
                    continue;
                }
                if (dw.test(eo)) {
                    continue;
                }
                if (j > 0) {
                    em += "\n";
                }
                em += eo;
                j++;
            }
            if (em) {
                en.value = em + "\n";
            }
        };
        this.setFreOptions = function(en) {
            var eo = en.split("\n");
            if (en == "每行一个选项，可以添加多个选项") {
                return;
            }
            var ep = new Array();
            for (var em = 0; em < eo.length; em++) {
                if (eo[em] && trim(eo[em])) {
                    ep.push(eo[em]);
                }
            }
            for (var em = ep.length; em < 2; em++) {
                ep[em] = "选项" + (em + 1);
            }
            for (var j = 0; j < ep.length; j++) {
                if (cY[j + 1]) {
                    cY[j + 1].get_item_title().value = trim(ep[j]);
                    continue;
                }
                if (bj) {
                    cY[j + 1] = new creat_item(this, j + 1, aW, "check", false, null);
                } else {
                    cY[j + 1] = new creat_item(this, j + 1, aW, "radio", false, null);
                }
                cY[j + 1].get_item_title().value = trim(ep[j]);
                cY[j + 1].get_item_value().value = j + 1;
            }
            var dw = cY.length - 1;
            for (var j = dw; j > ep.length; j--) {
                aW.deleteRow(j);
                cY.length = cY.length - 1;
            }
            this.updateItem();
            this.setNotNewAdd();
            setQTopPos(this);
        };

        this.updateItem = function(eq) {
            var en = true;
            if (this.dataNode._type != "matrix") {
                this.popHint("");
            }
            if (!this.checkItemTitle()) {
                en = false;
            }
            if (!this.checkItemValue()) {
                en = false;
            }
            if (!this.checkItemJump(true)) {
                en = false;
            }
            if (!en) {
                return;
            }
            var ep = this.dataNode;
            ep._select = new Array();
            var em = !ep._tag && (ep._type == "check" || ep._type == "radio" || ep._type == "radio_down");
            for (var dw = 1; dw < cY.length; dw++) {
                ep._select[dw] = new Object();
                var eo = cY[dw].get_item_title();
                var i = replace_specialChar(trim(eo.value));
                if (i != eo.value) {
                    eo.value = i;
                }
                ep._select[dw]._item_title = eo.value.replace(/\</g, "&lt;");
                ep._select[dw]._item_radio = false;
                if (em || ep._isCeShi) {
                    ep._select[dw]._item_radio = cY[dw].get_item_check().checked;
                }
                ep._select[dw]._item_value = trim(cY[dw].get_item_value().value);
                ep._select[dw]._item_jump = 0;
                if (aK || cT || cR) {
                    ep._select[dw]._item_jump = trim(cY[dw].get_item_jump().value);
                }
                if (cY[dw].get_item_huchi()) {
                    ep._select[dw]._item_huchi = cY[dw].get_item_huchi().checked;
                }
                ep._select[dw]._item_tb = false;
                ep._select[dw]._item_tbr = false;
                ep._select[dw]._item_img = "";
                ep._select[dw]._item_imgtext = false;
                ep._select[dw]._item_desc = "";
                ep._select[dw]._item_label = "";
                if (cY[dw].get_item_tb()) {
                    ep._select[dw]._item_tb = cY[dw].get_item_tb().checked;
                }
                if (cY[dw].get_item_tbr()) {
                    ep._select[dw]._item_tbr = cY[dw].get_item_tbr().checked;
                }
                if (cY[dw].get_item_img()) {
                    ep._select[dw]._item_img = cY[dw].get_item_img().value = replace_specialChar(trim(cY[dw].get_item_img().value));
                }
                if (cY[dw].get_item_imgtext()) {
                    ep._select[dw]._item_imgtext = cY[dw].get_item_imgtext().checked;
                }
            }
            if (!this.checkCeShiSet()) {
                return;
            }
            if (!eq) {
                this.createTableRadio(true);
            }
        };
    }
    if (au || u) {
        var aG = $ce("div", "", dc);
        aG.style.marginTop = "8px";
        if (au) {
            var C = $ce("span", "<b>题目总宽度：</b>", aG);
            var dL = "<select onchange='cur.setMainWidth(this);'><option value=''>默认</option><option value='50'>50%</option><option value='60'>60%</option><option value='70'>70%</option><option value='80'>80%</option><option value='90'>90%</option><option value='100'>100%</option><option value='自定义'>自定义</option></select>&nbsp;";
            C.innerHTML += dL;
            var aQ = $ce("span", "", C);
            var a3 = control_text("3");
            a3.maxLength = 2;
            a3.onchange = a3.onblur = function() {
                var i = this.value;
                if (!isEmpty(i) && !isInt(i)) {
                    show_status_tip("您输入的宽度不合法！");
                    this.value = "";
                    cA.dataNode._mainWidth = 50;
                } else {
                    cA.dataNode._mainWidth = i ? parseInt(i) : "";
                }
                cA.createTableRadio(true);
            };
            this.setMainWidth = function(i) {
                if (i.value != "自定义") {
                    a3.value = i.value;
                    aQ.style.display = "none";
                    a3.onchange();
                } else {
                    aQ.style.display = "";
                }
            };
            this.initMainWidth = function() {
                if (this.dataNode._mainWidth) {
                    a3.value = this.dataNode._mainWidth;
                    var em = $$("select", C)[0];
                    em.value = this.dataNode._mainWidth;
                    var j = true;
                    for (var dw = 0; dw < em.options.length; dw++) {
                        if (em.options[dw].value == this.dataNode._mainWidth) {
                            j = false;
                            break;
                        }
                    }
                    if (j) {
                        em.value = "自定义";
                        this.setMainWidth(em);
                    }
                }
            };
            aQ.appendChild(a3);
            aQ.appendChild(document.createTextNode("%"));
            aQ.style.display = "none";
            if (cV == "102") {
                C.style.display = "none";
            }
        }
        var dy = "左行标题宽度：";
        if (u) {
            dy = "行标题宽度：";
        }
        var cv = $ce("span", "&nbsp;&nbsp;<b>" + dy + "</b>", aG);
        var cL = "<select onchange='cur.setTWidth(this);'><option value=''>默认</option><option value='10%'>10%</option><option value='15%'>15%</option><option value='20%'>20%</option><option value='30%'>30%</option><option value='40%'>40%</option><option value='50%'>50%</option><option value='自定义'>自定义</option></select>&nbsp;";
        cv.innerHTML += cL;
        var aT = control_text("3");
        aT.maxLength = 3;
        aT.onchange = aT.onblur = function() {
            var i = this.value;
            if (!isEmpty(i) && (!isInt(i) || i - 100 > 0)) {
                show_status_tip("您输入的宽度不合法！");
                this.value = "";
                cA.dataNode._rowwidth = "";
            } else {
                cA.dataNode._rowwidth = i + "%";
                if (cA.dataNode._rowwidth == "%") {
                    cA.dataNode._rowwidth = "";
                }
            }
            if (u) {
                cA.createSum();
            } else {
                if (au) {
                    cA.updateItem();
                }
            }
            window.focus();
        };
        this.setTWidth = function(i) {
            if (i.value != "自定义") {
                aT.value = i.value.replace("%", "");
                aT.style.display = "none";
                aT.onchange();
            } else {
                aT.style.display = "";
            }
        };
        this.initWidth = function() {
            if (this.dataNode._rowwidth && this.dataNode._rowwidth.indexOf("%") > -1) {
                var i = $$("select", cv)[0];
                i.value = this.dataNode._rowwidth;
                aT.value = this.dataNode._rowwidth.replace("%", "");
                if (i.selectedIndex == -1) {
                    i.value = "自定义";
                    this.setTWidth(i);
                }
            }
        };
        aT.style.display = "none";
        cv.appendChild(aT);
        if (au) {
            var aL = $ce("span", "<b>右行标题宽度：</b>", aG);
            aL.style.display = "none";
            var bT = "<select onchange='cur.setTWidth2(this);'><option value=''>默认</option><option value='10%'>10%</option><option value='15%'>15%</option><option value='20%'>20%</option><option value='30%'>30%</option><option value='40%'>40%</option><option value='50%'>50%</option><option value='自定义'>自定义</option></select>&nbsp;";
            aL.innerHTML += bT;
            var dK = control_text("3");
            dK.maxLength = 3;
            dK.onchange = dK.onblur = function() {
                var i = this.value;
                if (!isEmpty(i) && (!isInt(i) || i - 100 > 0)) {
                    show_status_tip("您输入的宽度不合法！");
                    this.value = "";
                    cA.dataNode._rowwidth2 = "";
                } else {
                    cA.dataNode._rowwidth2 = i + "%";
                    if (cA.dataNode._rowwidth2 == "%") {
                        cA.dataNode._rowwidth2 = "";
                    }
                }
                cA.updateItem();
                window.focus();
            };
            this.setTWidth2 = function(i) {
                if (i.value != "自定义") {
                    dK.value = i.value.replace("%", "");
                    dK.style.display = "none";
                    dK.onchange();
                } else {
                    dK.style.display = "";
                }
            };
            this.initWidth2 = function() {
                if (this.dataNode._rowwidth2 && this.dataNode._rowwidth2.indexOf("%") > -1) {
                    var i = $$("select", aL)[0];
                    i.value = this.dataNode._rowwidth2;
                    dK.value = this.dataNode._rowwidth2.replace("%", "");
                    if (i.selectedIndex == -1) {
                        i.value = "自定义";
                        this.setTWidth2(i);
                    }
                }
            };
            dK.style.display = "none";
            aL.appendChild(dK);
        }
        /*
        if (au && (this.dataNode._tag == 102 || this.dataNode._tag == 103)) {
            var cE = control_check();
            aG.appendChild(cE);
            var k = $ce("span", "竖向选择", aG);
            var cS = $ce("i", "", k);
            cS.className = "design-icon design-qmark";
            cS.onmouseover = function() {
                toolTipLayer.style.width = "250px";
                toolTipLayer.innerHTML = "当选项太多时，通过竖向选择可以得到更好的显示效果，只支持电脑端。";
                sb_setmenunav(toolTipLayer, true, this);
            };
            cS.onmouseout = function() {
                sb_setmenunav(toolTipLayer, false, this);
            };
            cE.onclick = function() {
                if (cA._referDivQ) {
                    show_status_tip("使用引用逻辑后，不能再使用竖向选择", 5000);
                    return;
                }
                cA.dataNode._daoZhi = this.checked;
                cA.updateSpanMatrix();
                cA.updateItem();
            };
            var v = $ce("a", "交换选项与行", aG);
            v.className = "link-666";
            v.style.display = "inline-block";
            v.style.marginLeft = "10px";
            var bN = $ce("i", "", aG);
            bN.className = "design-icon design-qmark";
            bN.onmouseover = function() {
                toolTipLayer.style.width = "250px";
                toolTipLayer.innerHTML = "如果您将行与选项正好相反，可以“交换选项与行”。";
                sb_setmenunav(toolTipLayer, true, this);
            };
            bN.onmouseout = function() {
                sb_setmenunav(toolTipLayer, false, this);
            };
            v.href = "javascript:void(0);";
            v.onclick = function() {
                if (isMergeAnswer && !cA.isMergeNewAdded) {
                    show_status_tip("在部分修改问卷模式下，不能“交换选项与行”", 5000);
                    return false;
                }
                if (window.confirm("确定交换行与选项吗？")) {
                    var j = "";
                    var em = cA.dataNode._select;
                    for (var i = 1; i < em.length; i++) {
                        if (i > 1) {
                            j += "\n";
                        }
                        j += em[i]._item_title;
                    }
                    var dw = ei.value;
                    if (dw) {
                        ei.value = j;
                        cA.checkRowTitle();
                        cA.setFreOptions(dw);
                    }
                }
                return false;
            };
        }
        */

    }
    if (au) {
        b0.style.width = "310px";
        if (cV == "102" || cV == "103") {
            b0.style.width = "410px";
        }
        if (cV && cV <= 101) {
            b0.style.width = "410px";
        }
        b0.style.paddingTop = "5px";
        aW.cellSpacing = "0";
        aW.cellPadding = "2";
        aW.width = "98%";
        setFloat(b0);
        setFloat(bs);
        $ce("div", "", cp).style.clear = "both";
    }
    var cC = $ce("div", "&nbsp;", aD);
    cC.style.clear = "both";
    cC.style.lineHeight = "1px";

    var c0 = document.createElement("div");
    c0.style.margin = "15px 10px";
    var a9 = control_btn("完成编辑");
    a9.className = "submitbutton";
    a9.style.width = "100%";
    a9.onclick = function() {
        qonclick.call(cA);
    };
    if (this.newAddQ) {}
    c0.appendChild(a9);
    var p = $ce("div", "", c0);
    p.style.color = "red";
    p.style.fontSize = "14px";
    p.style.display = "inline-block";
    p.style.margin = "6px 0 0 10px";
    aD.appendChild(c0);
    B.appendChild(aH);
    this.hasCreatedAttr = true;
    this.createEditBox = function() {
        if (this.hasEditBox) {
            return;
        }
        this.hasEditBox = true;
        if (ac) {
            ac.style.display = "none";
        }
        G.style.height = "102px";
        if (dY) {
            G.style.height = "116px";
        }
        G.style.width = "650px";
        var i = EditToolBarItemsPageCut;
        if (eh) {
            i = EditToolBarItems;
        } else {
            G.style.height = "152px";
        }

		this.titleId = w;

		KindEditor.create('#'+w, {
			DEBUG : true,
			items: i,
			filterMode: true,
            afterChange: function() {
                this.sync();
				G.onchange();
            },
			DesignPage: 1
		});


    };
    this.popHint = function(i) {
        if (p) {
            if (i) {
                p.innerHTML = "<b>提示：</b>" + i;
            } else {
                p.innerHTML = "";
            }
        }
    };
    if (c) {
        this.createEditBox();
    }
    this.checkTitle = function() {
        if (dY) {
            var i = getGapFillCount(G.value);
            if (i == 0) {
                show_status_tip("填空题的标题必须包含空“" + GapFillStr + "”!", 4000);
                this.isTitleValid = false;
                return false;
            } else {
                if (isMergeAnswer && !cA.isMergeNewAdded) {
                    if (i < this.dataNode._gapcount) {
                        show_status_tip("合并答卷时，不能删除填空题标题中的空!", 4000);
                        this.isTitleValid = false;
                        return false;
                    }
                }
            }
        }
        var dw = G.value;
        if (!this.hasEditBox && /\r\n|\n|\r/.test(dw)) {
            dw = G.value = dw.replace(/\r\n|\n|\r/g, "<br />");
            d9.onclick();
        }
        if (dY) {
            dw = replaceGapFill(dw, this.dataNode).replace(/\<br\s*\/\>/g, "<div style='margin-top:8px;'></div>");
        }
        ab.innerHTML = dw;
        this.dataNode._title = G.value;
        this.isTitleValid = true;
        if (this.dataNode._type == "cut") {
            ab.innerHTML = dw || "请在此输入说明文字";
            if (this.div_video_title) {
                this.div_video_title.innerHTML = "";
            }
            if (this.dataNode._video) {
                var j = "<iframe height=498 width=510 src='" + this.dataNode._video + "' frameborder=0 allowfullscreen></iframe>";
                if (this.div_video_title) {
                    this.div_video_title.innerHTML = j;
                } else {
                    this.div_video_title = $ce("div", j, ab);
                }
            }
        }
        if (!this.hasEditBox && this.dataNode._title.indexOf("<") > -1) {
            this.createEditBox();
        }
        if (bq) {
            if (ab.innerHTML == "") {
                ab.parentNode.style.borderBottom = "none";
            } else {
                ab.parentNode.style.borderBottom = "dashed 1px #efefef";
            }
        }
        return true;
    };
    this.checkDefault = function() {
        this.popHint("");
        var i = replace_specialChar(trim(bL.value));
        bL.value = i;
        if (aw.value != "" && i != "" && i.length < aw.value) {
            this.popHint("您输入的默认值少于您设置的最小字数，请修改！");
            this.isDefaultValid = false;
            return false;
        } else {
            if (eb.value != "" && i.length > eb.value) {
                this.popHint("您输入的默认值超过了您设置的最大字数，请修改！");
                this.isDefaultValid = false;
                return false;
            } else {
                if (this.dataNode._verify != "省市区" && this.dataNode._verify != "高校") {
                    bV.value = i;
                } else {
                    if (i) {
                        bV.value = "指定省份为：" + i;
                    } else {
                        bV.value = "";
                    }
                }
                this.dataNode._default = i;
                this.isDefaultValid = true;
            }
        }
        return true;
    };
    this.checkAnyJump = function(j) {
        return true;
    };
    this.setAnyTimeJumpTo = function() {
        bz.value = this.dataNode._anytimejumpto;
    };
    this.checkValid = function() {
        var en = this.isAnyJumpValid == undefined || this.isAnyJumpValid;
        var eo = this.isTitleValid == undefined || this.isTitleValid;
        if (cG) {
            var j = this.isDefaultValid == undefined || this.isDefaultValid;
            return eo && j && en;
        } else {
            var er = this.isItemTitleValid == undefined || this.isItemTitleValid;
            var i = this.isItemJumpValid == undefined || this.isItemJumpValid;
            var dw = this.isItemValueValid == undefined || this.isItemValueValid;
            var em = this.isRowTitleValid == undefined || this.isRowTitleValid;
            var ep = this.isColumnTitleValid == undefined || this.isColumnTitleValid;
            var eq = this.isCeShiValid == undefined || this.isCeShiValid;
            return er && i && dw && en && eo && em && ep && eq;
        }
    };
    this.validate = function() {
        p.innerHTML = "";
        this.checkTitle();
        if (cG) {
            this.checkDefault();
        } else {
            if (u) {
                this.checkRowTitle();
            } else {
                if (dW) {
                    var i = true;
                    var j = true;
                    if (au) {
                        i = this.checkRowTitle();
                    }
                    if (bW) {
                        j = this.checkColumnTitle();
                    }
                    if (i && j) {
                        this.checkItemTitle();
                    }
                    this.checkItemValue();
                    this.checkItemJump();
                    if (this.checkCheckLimit) {
                        this.checkCheckLimit();
                    }
                    if (this.checkCeShiSet) {
                        this.checkCeShiSet();
                    }
                }
            }
        }
        if (eh) {
            this.checkAnyJump();
        }
        this.setErrorStyle();
    };
    this.setErrorStyle = function() {
        if (!this.checkValid()) {
            this.className += " div_question_error";
        } else {
            this.className = this.className.replace(/div_question_error/, "");
        }
    };
    this.setDataNodeToDesign = function() {
        var eo = this.dataNode;
        switch (eo._type) {
        case "question":

            G.value = eo._title;
            G.onblur();
            eb.value = eo._maxword;
            m.checked = eo._requir;
            bL.value = eo._default;
            if (eo._default) {
                eo._olddefault = eo._default;
                A.click();
            }
            if (eo._hasjump) {
                bQ.click();
            }
            ct.checked = eo._needOnly;
            bU.checked = eo._needsms;
            aT.value = eo._width;
            if (eo._underline) {
                cn.checked = true;
            }
            aw.value = eo._minword;
            this.initWidth();
            this.initHeight();
            break;
        case "sum":
            G.value = eo._title;
            G.onblur();
            m.checked = eo._requir;
            ei.value = eo._rowtitle;
            cP.value = eo._total;
            d1.value = eo._ins;
            if (eo._hasjump) {
                bQ.click();
            }
            bz.value = eo._anytimejumpto;
            if (this._referDivQ) {
                this.show_divAddFromCheck();
                bl.value = this._referDivQ.dataNode._topic;
                this.addFromCheck(bl);
            }
            break;
        case "cut":
        case "page":
            G.value = eo._title;
            G.onblur();
            if (eo._type == "page") {
                t.checked = eo._iszhenbie;
                bi.value = eo._mintime;
                dC.value = eo._maxtime;
                dC.onblur();
            }
            break;
        case "fileupload":
            G.value = eo._title;
            G.onblur();
            var es = $$("input", dU);
            var eu = eo._ext || defaultFileExt;
            K.value = eo._maxsize;
            var en = eu.split("|");
            for (var ev = 0; ev < es.length; ev++) {
                es[ev].onclick = function() {
                    if (!this.value) {
                        var eB = this.parentNode;
                        var eA = $$("input", eB);
                        for (var ex = 0; ex < eA.length; ex++) {
                            if (eA[ex] != this) {
                                eA[ex].checked = this.checked;
                            }
                        }
                    }
                    if (cur.updateExt) {
                        cur.updateExt(this);
                    }
                };
                if (en.indexOf(es[ev].value) > -1) {
                    es[ev].checked = true;
                }
            }
            for (var ev = 0; ev < es.length; ev++) {
                if (es[ev].value) {
                    continue;
                }
                var ez = true;
                var er = es[ev].parentNode;
                var ep = $$("input", er);
                for (var j = 0; j < ep.length; j++) {
                    if (ep[j] != es[ev] && !ep[j].checked) {
                        ez = false;
                        break;
                    }
                }
                if (ez) {
                    es[ev].checked = true;
                }
            }
            m.checked = eo._requir;
            //d1.value = eo._ins;
            if (eo._hasjump) {
                bQ.click();
            }
            //bz.value = eo._anytimejumpto;
            break;
        case "gapfill":
            G.value = eo._title;
            G.onblur();
            m.checked = eo._requir;
            d1.value = eo._ins;
            if (eo._hasjump) {
                bQ.click();
            }
            bz.value = eo._anytimejumpto;
            dn.checked = eo._useTextBox;
            break;
        case "slider":
            G.value = eo._title;
            G.onblur();

            aI.value = eo._minvalue || 0;
            d.value = eo._maxvalue || 100;
            b9.value = eo._minvaluetext || "";
            at.value = eo._maxvaluetext || "";
            if (eo._hasjump) {
                bQ.click();
            }
            break;
        case "radio":
        case "radio_down":
        case "check":
        case "matrix":
            G.value = eo._title;
            G.onblur();

            if (ec) {
                ec.value = eo._numperrow;
            }
            b2.checked = eo._randomChoice;
            if (eo._hasvalue) {
                Q.checked = true;
                Q.onclick();
            }
            if (eo._tag) {
                Q.disabled = true;
            }
            if (eo._type == "matrix") {
                this.initMainWidth();
                ei.value = eo._rowtitle;
                this.initWidth();
                if (eo._rowtitle2 && cB) {
                    cB.checked = true;
                    cB.onclick();
                    cB.checked = true;
                }
                if (eo._rowwidth2) {
                    this.initWidth2();
                }
                if (eo._daoZhi) {
                    if (cE) {
                        cE.checked = true;
                    }
                }
                d0.style.display = (eo._tag > 200) ? "none": "";
                b0.style.display = d0.style.display;
                Q.disabled = (eo._tag <= 101) ? true: false;
            }
            var eq = false;
            var em = !eo._tag && (eo._type == "check" || eo._type == "radio" || eo._type == "radio_down");
            for (var dw = 1; dw < eo._select.length; dw++) {
                cY[dw].get_item_title().value = eo._select[dw]._item_title.replace(/&lt;/, "<");
                if (em || eo._isCeShi) {
                    var i = eo._select[dw]._item_radio;
                    cY[dw].get_item_check().checked = i;
                    if (i) {
                        eq = true;
                    }
                    if (eo._select[dw]._item_huchi) {
                        cY[dw].get_item_huchi().checked = true;
                    }
                }
                if (eq && this.defaultCheckSet) {
                    this.defaultCheckSet();
                }
                var ew = eo._select[dw]._item_value;
                if (eo._isShop && (ew == "0" || !ew)) {
                    ew = "10";
                }
                cY[dw].get_item_value().value = ew;
                if (ew == NoValueData) {
                    cY[dw].get_item_value().value = "";
                    if (cY[dw].get_item_novalue()) {
                        cY[dw].get_item_novalue().checked = true;
                    }
                }
                var ey = eo._select[dw]._item_jump;
                cY[dw].get_item_jump().value = ey;
                if (cY[dw].get_item_tb()) {
                    cY[dw].get_item_tb().checked = eo._select[dw]._item_tb;
                }
                if (cY[dw].get_item_tbr()) {
                    cY[dw].get_item_tbr().checked = eo._select[dw]._item_tbr;
                }
                if (cY[dw].get_item_img()) {
                    cY[dw].get_item_img().value = eo._select[dw]._item_img;
                }
                if (cY[dw].get_item_imgtext()) {
                    cY[dw].get_item_imgtext().checked = eo._select[dw]._item_imgtext;
                }
            }
            if (m) {
                m.checked = eo._requir;
            }
            if (eo._hasjump) {
                if (eo._type == "radio" || eo._type == "radio_down") {
                    if (eo._anytimejumpto < 1) {
                        cZ.click();
                    } else {
                        bQ.click();
                    }
                } else {
                    bQ.click();
                }
            }

            if (this._referDivQ) {
                this.show_divAddFromCheck();
                bl.value = this._referDivQ.dataNode._topic;
                this.addFromCheck(bl);
            }
            break;
        }
        if (eo._ins) {
            eo._oldins = eo._ins;
            aC.click();
            aC.checked = true;
        }
        try {
            G.focus();
        } catch(et) {}
    };
}
function setImage(b, a) {
    PDF_close(b);
}
function creat_item(J, N, C, ak, H, K) {
    var U = N;
    var aa = C;
    var l = C.insertRow(U);
    var u = l.insertCell( - 1);
    var a = control_text("15");
    a.tabIndex = 1;
    a.title = "回车添加新选项，上下键编辑前后选项";
    if (H) {
        select_item_num++;
    }
    a.value = K ? K._item_title: "选项" + select_item_num;
    if (C.rows.length - 1 == U && cur && (cur.dataNode._isQingJing || cur.dataNode._isShop)) {
        var D = "情景" + U;
        if (cur.dataNode._isShop) {
            D = "商品" + U;
        }
        var R = false;
        for (var ad = 1; ad < cur.dataNode._select.length; ad++) {
            if (cur.dataNode._select[ad]._item_title == D) {
                R = true;
                break;
            }
        }
        if (!R) {
            a.value = D;
        }
    }
    a.className += " choicetxt";
    a.onchange = a.onblur = function() {
        if (!this.value) {
            this.value = this.initText || "";
        }
        if (cur && cur.updateItem) {
            cur.updateItem();

        }
    };
    a.onfocus = function() {
        if (!this.initText) {
            this.initText = this.value;
        }
        var j = /^选项\d+$/;
        if (j.test(this.value)) {
            this.value = "";
        }
    };
    a.onkeydown = function(al) {
        var j = al || window.event;
        if (j.keyCode == 13) {
            ab.onclick();
        } else {
            if (j.keyCode == 40) {
                if (U < cur.option_radio.length - 1) {
                    cur.option_radio[U + 1].get_item_title().focus();
                    cur.option_radio[U + 1].get_item_title().select();
                }
            } else {
                if (j.keyCode == 38) {
                    if (U > 1) {
                        cur.option_radio[U - 1].get_item_title().focus();
                        cur.option_radio[U - 1].get_item_title().select();
                    }
                }
            }
        }
        return true;
    };
    u.appendChild(a);
    u.style.width = "150px";
    var w = cur || J;
    var k = w.dataNode._isCePing;
    if (w.dataNode._type == "matrix" && w.dataNode._tag && w.dataNode._tag <= 101) {
        u.style.width = "80px";
        a.style.width = "80px";
    } else {
        if (w.dataNode._type == "check" && w.dataNode._tag) {
            u.style.width = "400px";
            a.style.width = "400px";
        }
    }
    var O = w.dataNode._type == "check" && (w.dataNode._tag || 0);
    var T = !w.dataNode._tag && (w.dataNode._type == "check" || w.dataNode._type == "radio") && !k;
    var c = w.dataNode._isCeShi;
    var q = w.dataNode._isQingJing;
    var x = w.dataNode._isShop;
    if ((T || k || O) && !q) {
        var A = l.insertCell( - 1);
        A.align = "center";
        var ai = document.createElement("input");
        ai.type = "hidden";
        ai.value = K ? K._item_img: "";
        A.appendChild(ai);
        var S = document.createElement("span");
        S.title = "添加图片";
        S.className = "choiceimg design-icon design-img";
        A.appendChild(S);
        if (ai.value) {
            S.title = "编辑图片";
            S.className = "choiceimg design-icon design-imgedit";
        }
        if (ai.value) {
            C.rows[0].cells[1].style.width = "52px";
        }
        function r(ao) {
            if (ao == undefined) {
                return;
            }
            var am = cur.option_radio[U].get_item_img();
            if (x && ao.indexOf(".sojump.com") > -1) {
                if (ao.indexOf("pubali") > -1) {
                    var an = "?x-oss-process";
                    var j = ao.indexOf(an);
                    if (j > -1) {
                        ao = ao.substring(0, j);
                    }
                    ao = ao + an + "=image/quality,q_90/resize,m_fill,h_126,w_190";
                } else {
                    var an = "?imageMogr2";
                    var j = ao.indexOf(an);
                    if (j > -1) {
                        ao = ao.substring(0, j);
                    }
                    ao = ao + an + "/thumbnail/190x126!";
                }
            }
            itemImage = am.value = ao;
            if (!x) {
                var al = cur.option_radio[U].get_item_imgtext();
                al.parentNode.style.display = (ao) ? "": "none";
                al.checked = true;
            }
            S.className = ao ? "choiceimg design-icon design-imgedit": "choiceimg design-icon design-img";
            C.rows[0].cells[1].style.width = "52px";
            cur.updateItem();
            if (cur.setchoiceWidth) {
                cur.setchoiceWidth();
            }
        }
        S.onclick = function() {
            itemImage = cur.option_radio[U].get_item_img().value;
            PDF_launch(uploadimg, 650, 470, r, cur.option_radio[U].get_item_title().value);
        };
        var V = $ce("span", "", A);
        var ah = control_check();
        V.style.verticalAlign = "bottom";
        ah.title = "是否显示选项文字";
        ah.className = "checkbox";
        V.appendChild(ah);
        V.style.display = ai.value ? "": "none";
        if (x) {
            V.style.display = "none";
        }
        ah.onclick = function() {
            cur.updateItem();
        };
    }
    if (O || T || k) {
        var z = l.insertCell( - 1);
        z.align = "center";
        var Y = $ce("span", "", z);
        Y.style.verticalAlign = "bottom";
        Y.style.fontSize = "12px";
        var v = control_check();
        v.style.verticalAlign = "bottom";
        v.title = "允许填空";
        v.className = "checkbox";
        Y.appendChild(v);
        v.checked = K ? K._item_tb: false;
        var ag = $ce("span", "<span style='font-size:16px;'>|</span>", Y);
        var b = $ce("span", "", ag);
        var p = "cbr" + J.dataNode._topic + "_" + U;
        var n = control_check();
        n.title = "文本框必填";
        n.id = p;
        n.className = "checkbox";
        b.appendChild(n);
        $ce("label", "必填", b).setAttribute("for", p);
        n.checked = K ? K._item_tbr: false;
        ag.style.display = v.checked ? "": "none";
        v.onclick = function() {
            if (!this.checked) {
                cur.option_radio[U].get_item_tbr().checked = false;
            }
            cur.option_radio[U].get_item_tbr().parentNode.parentNode.style.display = this.checked ? "": "none";
            cur.updateItem();
        };
        n.onclick = function() {
            cur.updateItem();
        };
        if (c || q || x) {
            z.style.display = "none";
        }
    }
    var B = l.insertCell( - 1);
    B.align = "left";
    var e = $ce("span", "&nbsp;", B);
    var o = control_text("4");
    o.maxLength = 5;
    o.title = "在此可以设置每个选项的分数（请输入整数）";
    if (q) {
        o.title = "请设置每个情景需要的数量,0表示不限制";
    } else {
        if (x) {
            o.title = "设置商品价格";
        }
    }
    o.value = K ? K._item_value: U;
    e.appendChild(o);
    if (w.dataNode._hasvalue && (k || w.dataNode._tag)) {
        B.style.display = "";
        if (w.dataNode._type != "check") {
            /*
            var aj = control_check();
            aj.title = "不记分";
            aj.className = "checkbox";
            aj.onclick = function() {
                o.value = "";
                cur.updateItem();
            };
            o.style.width = "16px";
            o.style.height = "16px";
            B.appendChild(aj);
            $ce("span", "不记分", B).style.fontSize = "11px";
            */
        }
    } else {
        if (!q && !x) {
            if(w.dataNode._tag == '1'){

            }else{
                B.style.display = "none";
            }
        }
    }
    o.onchange = o.onblur = function() {
        if (x) {
            if (!this.value || parseFloat(this.value) != this.value) {
                this.value = 10;
            }
        } else {
            if (q) {
                if (!isInt(this.value) || parseInt(this.value) < 1) {
                    this.value = 10;
                    if (q) {
                        this.value = 50;
                    }
                }
            }
        }
        if (k || x || (cur.dataNode._type == "radio" && cur.dataNode._tag)) {
            cur.updateItem();
        } else {
            cur.updateItem(true);
        }
    };
    o.onkeydown = function(al) {
        var j = al || window.event;
        if (j.keyCode == 13) {
            ab.onclick();
        } else {
            if (j.keyCode == 40) {
                if (U < cur.option_radio.length - 1) {
                    cur.option_radio[U + 1].get_item_value().select();
                }
            } else {
                if (j.keyCode == 38) {
                    if (U > 1) {
                        cur.option_radio[U - 1].get_item_value().select();
                    }
                }
            }
        }
    };
    var m = l.insertCell( - 1);
    if (J.dataNode._type == "matrix") {
        m.style.display = "none";
    }
    var W = "&nbsp;&nbsp;";
    if (x) {
        W = "";
    }
    var M = $ce("span", W, m);
    m.align = "left";
    var I = control_text(4);
    I.maxLength = 4;
    I.style.height = "20px";
    if (x) {
        I.title = "为空表示不限制库存，0表示已售完";
    }
    I.value = K ? K._item_jump: "";
    if (w.dataNode._hasjump && w.dataNode._anytimejumpto == "0") {
        M.style.display = "";
    } else {
        if (x) {
            M.style.display = "";
        } else {
            M.style.display = "none";
        }
    }
    M.appendChild(I);
    I.onclick = function() {
        if (!x) {
            openJumpWindow(w, this);
        }
    };
    I.onmouseout = function() {
        if (!x) {
            sb_setmenunav(toolTipLayer, false);
        }
    };
    I.onmouseover = function() {
        if (!x) {
            if (!this.error) {
                if (!this.title || this.hasChanged) {
                    this.title = getJumpTitle(this.value);
                }
            }
        }
    };
    I.onblur = function() {
        if (x) {
            if (!isInt(this.value) || parseInt(this.value) < 1) {
                this.value = "";
            }
        }
        if (this.value == this.prevValue) {
            this.hasChanged = false;
            return;
        }
        this.hasChanged = true;
        this.prevValue = this.value;
        cur.updateItem(true);
    };
    I.onkeydown = function(al) {
        var j = al || window.event;
        if (j.keyCode == 13) {
            ab.onclick();
        } else {
            if (j.keyCode == 40) {
                if (U < cur.option_radio.length - 1) {
                    cur.option_radio[U + 1].get_item_jump().select();
                }
            } else {
                if (j.keyCode == 38) {
                    if (U > 1) {
                        cur.option_radio[U - 1].get_item_jump().select();
                    }
                }
            }
        }
    };
    var E = null;
	/*
    if (T && ak == "check" && !c && !x) {
        var Q = l.insertCell( - 1);
        var y = $ce("span", "&nbsp;", Q);
        Q.align = "center";
        E = control_check();
        E.title = "与其它选项互斥";
        E.className = "checkbox";
        y.appendChild(E);
        E.onclick = function() {
            cur.updateItem();
        };
    }
	*/
    var g = l.insertCell( - 1);
    var P = $ce("span", "&nbsp;", g);
    var ae = null;
    ae = control_check();
    ae.className = "checkbox";
    if (!c) {
        ae.title = "若选中，用户在填问卷时此选项会默认被选中";
    } else {
        g.align = "center";
    }
    if ((T || w.dataNode._type == "radio_down") && !q && !x) {
        g.style.display = "";
    } else {
        g.style.display = "none";
    }
    P.appendChild(ae);
    if ((T || w.dataNode._type == "radio_down") && ak == "radio") {
        ae.onclick = function() {
            if (this.checked) {
                for (var j = 1; j < cur.option_radio.length; j++) {
                    if (cur.option_radio[j].get_item_check() == this) {
                        this.checked = true;
                    } else {
                        cur.option_radio[j].get_item_check().checked = false;
                    }
                }
            }
            cur.updateItem();
        };
    }
    if (T && ak == "check") {
        ae.onclick = function() {
            cur.updateItem();
        };
    }

    var L = l.insertCell( - 1);
    L.align = "center";
    var ab = document.createElement("span");
    ab.title = "在此选项下面插入一个新的选项";
    ab.className = "choiceimg design-icon design-add";
    var f = document.createElement("span");
    f.title = "删除当前选项（最少保留2个选项）";
    f.className = "choiceimg design-icon design-minus";
    var Z = document.createElement("span");
    Z.title = "将当前选项上移一个位置";
    Z.className = "choiceimg design-icon design-cup";
    var F = document.createElement("span");
    F.title = "将当前选项下移一个位置";
    F.className = "choiceimg design-icon design-cdown";
    L.appendChild(ab);
    L.appendChild(f);
    L.appendChild(Z);
    L.appendChild(F);
    ab.style.cursor = "pointer";
    f.style.cursor = "pointer";
    Z.style.cursor = "pointer";
    F.style.cursor = "pointer";
    ab.onclick = function() {
        if (isMergeAnswer && !cur.isMergeNewAdded && U < cur.option_radio.length - 1) {
            alert("很抱歉，部分修改问卷模式下，不能在中间插入选项，只能在最后面添加选项！");
            return;
        }
        if (isMergeAnswer && !cur.isMergeNewAdded && !confirm("此题不能删除选项，是否确认增加选项？")) {
            return;
        }
        for (var j = cur.option_radio.length - 1; j > U; j--) {
            cur.option_radio[j].set_item_num(j + 1);
            cur.option_radio[j + 1] = cur.option_radio[j];
        }
        if (ak == "radio") {
            cur.option_radio[U + 1] = new creat_item(cur, U + 1, C, "radio", true);
        }
        if (ak == "check") {
            cur.option_radio[U + 1] = new creat_item(cur, U + 1, C, "check", true);
        }
        cur.updateItem();
        cur.option_radio[U + 1].get_item_title().select();
    };
    f.onclick = function() {
        if (isMergeAnswer && !cur.isMergeNewAdded) {
            alert("很抱歉，在部分修改问卷模式下，为了保持数据一致性不允许删除题目选项！");
            return;
        }
        if (cur.option_radio.length > 2) {
            C.deleteRow(U);
            for (var al = U + 1; al < cur.option_radio.length; al++) {
                cur.option_radio[al].set_item_num(al - 1);
                cur.option_radio[al - 1] = cur.option_radio[al];
            }
            cur.option_radio.length--;
            cur.updateItem();
        } else {
            show_status_tip("请至少保留1个选项", 4000);
        }
    };
    Z.onclick = function() {
        if (isMergeAnswer && !cur.isMergeNewAdded) {
            alert("很抱歉，在部分修改问卷模式下，为了保持数据一致性不允许移动题目选项！");
            return;
        }
        if ((U - 1) > 0) {
            d(cur.option_radio[U], cur.option_radio[U - 1]);
            if (ak == "check" || ak == "radio") {
                af(cur.option_radio[U], cur.option_radio[U - 1]);
            }
            s(cur.option_radio[U], cur.option_radio[U - 1]);
            cur.updateItem();
        }
    };
    F.onclick = function() {
        if (isMergeAnswer && !cur.isMergeNewAdded) {
            alert("很抱歉，在部分修改问卷模式下，为了保持数据一致性不允许移动题目选项！");
            return;
        }
        if ((U + 1) < cur.option_radio.length) {
            d(cur.option_radio[U], cur.option_radio[U + 1]);
            if (ak == "check" || ak == "radio") {
                af(cur.option_radio[U], cur.option_radio[U + 1]);
            }
            s(cur.option_radio[U], cur.option_radio[U + 1]);
            cur.updateItem();
        }
    };
    function d(am, j) {
        var al = am.get_item_title().value;
        am.get_item_title().value = j.get_item_title().value;
        j.get_item_title().value = al;
    }
    function af(am, j) {
        var al = am.get_item_check().checked;
        am.get_item_check().checked = j.get_item_check().checked;
        j.get_item_check().checked = al;
    }
    function s(am, j) {
        var al = am.get_item_value().value;
        am.get_item_value().value = j.get_item_value().value;
        j.get_item_value().value = al;
        al = am.get_item_jump().value;
        am.get_item_jump().value = j.get_item_jump().value;
        j.get_item_jump().value = al;
        if (am.get_item_novalue()) {
            al = am.get_item_novalue().checked;
            am.get_item_novalue().checked = j.get_item_novalue().checked;
            j.get_item_novalue().checked = al;
        }
        if (am.get_item_tb()) {
            al = am.get_item_tb().checked;
            am.get_item_tb().checked = j.get_item_tb().checked;
            j.get_item_tb().checked = al;
        }
        if (am.get_item_tbr()) {
            al = am.get_item_tbr().checked;
            am.get_item_tbr().checked = j.get_item_tbr().checked;
            j.get_item_tbr().checked = al;
        }
        if (am.get_item_img()) {
            al = am.get_item_img().value;
            am.get_item_img().value = j.get_item_img().value;
            j.get_item_img().value = al;
        }
        if (am.get_item_imgtext()) {
            al = am.get_item_imgtext().checked;
            am.get_item_imgtext().checked = j.get_item_imgtext().checked;
            j.get_item_imgtext().checked = al;
        }

    }
    this.get_item_add = function() {
        return ab;
    };
    this.get_item_del = function() {
        return f;
    };
    this.get_item_table = function() {
        return aa;
    };
    this.get_item_tr = function() {
        return l;
    };
    this.set_item_num = function(j) {
        U = j;
    };
    this.get_item_addimg = function() {
        return S;
    };
    this.get_item_num = function() {
        return U;
    };
    this.get_item_title = function() {
        return a;
    };
    this.get_item_check = function() {
        return ae;
    };
    this.get_item_huchi = function() {
        return E;
    };
    this.get_item_tb = function() {
        return v;
    };
    this.get_item_tbr = function() {
        return n;
    };
    this.get_item_img = function() {
        return ai;
    };

    this.get_item_imgtext = function() {
        return ah;
    };
    this.get_item_value = function() {
        return o;
    };
    this.get_item_novalue = function() {
        return aj;
    };
    this.get_item_jump = function() {
        return I;
    };
    return true;
}
function setAllRequired(c) {
    for (var a = 0; a < questionHolder.length; a++) {
        var b = questionHolder[a].dataNode._type;
        if (b != "cut" && b != "page" && questionHolder[a].dataNode._requir != c) {
            if (questionHolder[a].get_requir) {
                questionHolder[a].get_requir().checked = c;
            }
            questionHolder[a].dataNode._requir = c;
            questionHolder[a].setreqstatus();
        }
    }
    show_status_tip("设置成功！", 4000);
}
function setAllRandom() {
    var a = cur.dataNode._randomChoice;
    for (var b = 0; b < questionHolder.length; b++) {
        if (!questionHolder[b].dataNode._isCeShi) {
            continue;
        }
        if (questionHolder[b].dataNode._type == "question") {
            continue;
        }
        if (questionHolder[b].get_random) {
            questionHolder[b].get_random().checked = a;
        }
        questionHolder[b].dataNode._randomChoice = a;
    }
    show_status_tip("设置成功！", 4000);
}
initAttrHandler();
function initAttrHandler() {
    firstPage.createAttr = createAttr;
    for (var b = 0; b < questionHolder.length; b++) {
        var a = questionHolder[b];
        setAttrHander(a);
    }
}
function setAttrHander(a) {
    a.createAttr = createAttr;
}