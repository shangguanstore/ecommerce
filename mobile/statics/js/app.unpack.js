/**
 * Created by DELL on 2018/1/13.
 */

function showtime() {
    now = new Date;
    var e = parseInt((startTime - now.getTime()) / 1e3) + auctionDate,
        t = 0,
        i = 0,
        n = 0,
        s = 0,
        a = "",
        o = "",
        r = "";
    e < 0 ? (e = 0, CurHour = 0, CurMinute = 0, CurSecond = 0) : (t = parseInt(e / 86400), e -= 86400 * t, i = parseInt(e / 3600), e -= 3600 * i, n = parseInt(e / 60), s = e - 60 * n),
    i < 10 && (a = "0"),
    n < 10 && (o = "0"),
    s < 10 && (r = "0"),
        Temp = t > 0 ? "" + _day + a + i + _hour + o + n + _minute + r + s + _second : i > 0 ? i + _hour + o + n + _minute + r + s + _second : n > 0 ? n + _minute + r + s + _second : s > 0 ? s + _second : "",
    (auctionDate <= 0 || "" == Temp) && (Temp = "<strong>" + _end + "</strong>", stopclock()),
    document.getElementById(showTime) && (Temp = "<strong></strong>" != Temp && t <= 0 ? '<em class="color-whie">' + Temp : Temp, document.getElementById(showTime).innerHTML = Temp),
        timerID = setTimeout("showtime()", 1e3),
        timerRunning = !0
}
function stopclock() {
    timerRunning && clearTimeout(timerID),
        timerRunning = !1
}
function macauclock() {
    stopclock(),
        showtime()
}
function onload_leftTime(e) {
    try {
        _GMTEndTime = gmt_end_time,
            _day = day,
            _hour = hour,
            _minute = minute,
            _second = second,
            _end = end
    } catch (t) {
    }
    if (_GMTEndTime > 0) {
        if (void 0 == e) var i = parseInt(_GMTEndTime) - parseInt(cur_date.getTime() / 1e3 + 60 * cur_date.getTimezoneOffset());
        else var i = parseInt(_GMTEndTime) - e;
        i > 0 && (auctionDate = i)
    }
    macauclock();
    try {
        initprovcity()
    } catch (t) {
    }
}
function get_asynclist(e, t) {
    $("#J_ItemList").more({
        address: e,
        spinner_code: '<div style="text-align:center; margin:10px;"><img src="' + t + '" /></div>'
    }),
        $(window).scroll(function () {
            $(window).scrollTop() == $(document).height() - $(window).height() && $(".get_more").click()
        })
}
function addToCart(e, t) {
    var i = new Object,
        n = new Array,
        s = (new Array, 1),
        a = document.forms.ECS_FORMBUY,
        o = 0,
        r = 0;
    if (a && (str = getSelectedAttributes(a), n = str.split(","), a.elements.number && (s = a.elements.number.value), o = 1), document.getElementById("region_id")) {
        var l = document.getElementById("region_id").value;
        i.warehouse_id = l
    }
    if (document.getElementById("area_id")) {
        var c = document.getElementById("area_id").value;
        i.area_id = c
    }
    document.getElementsByName("store_id").length > 0 && (r = document.getElementsByName("store_id")[0].value),
        i.quick = o,
        i.spec = n,
        i.goods_id = e,
        i.store_id = parseInt(r),
        i.number = s,
        i.parent = "undefined" == typeof t ? 0 : parseInt(t),
        $.post("index.php?r=cart/index/add_to_cart", {
            goods: $.toJSON(i)
        }, function (e) {
            addToCartResponse(e)
        }, "json")
}
function getSelectedAttributes(e) {
    var t = new Array,
        n = 0;
    for (i = 0; i < e.elements.length; i++) {
        var s = e.elements[i].name.substr(0, 5);
        "spec_" != s || ("radio" != e.elements[i].type && "checkbox" != e.elements[i].type || !e.elements[i].checked) && "SELECT" != e.elements[i].tagName || (t[n] = e.elements[i].value, n++)
    }
    return t = t.join(",")
}
function addToCartResponse(e) {
    if (e.error > 0) 2 == e.error ? layer.open({
        content: e.message,
        btn: ["确定", "取消"],
        shadeClose: !1,
        yes: function () {
            location.href = "index.php?r=user/index/add_booking&id=" + e.goods_id + "&spec=" + e.product_spec
        },
        no: function () {
        }
    }) : 6 == e.error ? location.href = "index.php?r=goods/index/index&id=" + e.goods_id : d_messages(e.message);
    else {
        if (e.store_id > 0) return void(window.location.href = "index.php?r=flow&store_id=" + e.store_id + "&cart_value=" + e.cart_value);
        $(".cart-num").html(e.goods_number);
        var t = "index.php?r=cart/index/index";
        location.href = t
    }
}
function collect(e) {
    $.get("index.php?r=goods/index/add_collection", {
        id: e
    }, function (e) {
        collectResponse(e)
    }, "json")
}
function collectResponse(e) {
    0 == e.error ? $("#ECS_COLLECT").hasClass("active") > 0 ? $("#ECS_COLLECT").removeClass("active") : $("#ECS_COLLECT").addClass("active") : 2 == e.error && layer.open({
        content: "请登录后收藏该商品",
        btn: ["立即登录", "取消"],
        shadeClose: !1,
        yes: function () {
            window.location.href = "index.php?r=user/login"
        },
        no: function () {
        }
    })
}
function signInResponse(e) {
    toggleLoader(!1);
    var t = e.substr(0, 1),
        i = e.substr(2);
    1 == t ? document.getElementById("member-zone").innerHTML = i : alert(i)
}
function gotoPage(e, t, i, n) {
    $.get("index.php?c=comment&a=index&act=gotopage", {
        page: e,
        id: t,
        type: i,
        rank: n
    }, function (e) {
        gotoPageResponse(e)
    }, "json")
}
function gotoPageResponse(e) {
    document.getElementById("ECS_COMMENT").innerHTML = e.content
}
function gotoBuyPage(e, t) {
    $.get("index.php?c=goods&a=gotopage", {
        page: e,
        id: t
    }, function (e) {
        gotoBuyPageResponse(e)
    }, "json")
}
function gotoBuyPageResponse(e) {
    document.getElementById("ECS_BOUGHT").innerHTML = e.result
}
function getFormatedPrice(e) {
    return currencyFormat.indexOf("%s") > -1 ? currencyFormat.replace("%s", advFormatNumber(e, 2)) : currencyFormat.indexOf("%d") > -1 ? currencyFormat.replace("%d", advFormatNumber(e, 0)) : e
}
function bid(e) {
    var t = "",
        i = "";
    if (e != -1) {
        var n = document.forms.formBid;
        if (t = n.elements.price.value, id = n.elements.snatch_id.value, 0 == t.length) i += price_not_null + "\n";
        else {
            var s = /^[\.0-9]+/;
            s.test(t) || (i += price_not_number + "\n")
        }
    } else t = e;
    return i.length > 0 ? void alert(i) : void $.post("index.php?c=snatch&a=bid", {
        price: t,
        id: id
    }, function (e) {
        bidResponse(e)
    }, "json")
}
function bidResponse(e) {
    0 == e.error ? (document.getElementById("ECS_SNATCH").innerHTML = e.content, document.forms.formBid && document.forms.formBid.elements.price.focus(), newPrice()) : alert(e.content)
}
function newPrice(e) {
    $.get("index.php?c=snatch&a=new_price_list&id=" + e, "", function (e) {
        newPriceResponse(e)
    }, "text")
}
function newPriceResponse(e) {
    document.getElementById("ECS_PRICE_LIST").innerHTML = e
}
function getAttr(e) {
    var t = document.getElementsByTagName("tbody");
    for (i = 0; i < t.length; i++)"goods_type" == t[i].id.substr(0, 10) && (t[i].style.display = "none");
    var n = "goods_type_" + e;
    try {
        document.getElementById(n).style.display = ""
    } catch (s) {
    }
}
function advFormatNumber(e, t) {
    var i = formatNumber(e, t),
        n = parseFloat(i);
    if (e.toString().length > i.length) {
        var s = e.toString().substring(i.length, i.length + 1),
            a = parseFloat(s);
        if (a < 5) return i;
        var o, r;
        if (0 == t) r = 1;
        else {
            o = "0.";
            for (var l = 1; l < t; l++) o += "0";
            o += "1",
                r = parseFloat(o)
        }
        i = formatNumber(n + r, t)
    }
    return i
}
function formatNumber(e, t) {
    var i, n, s, a;
    if (i = e.toString(), n = i.indexOf("."), s = i.length, 0 == t) n != -1 && (i = i.substring(0, n));
    else if (n == -1) for (i += ".", a = 1; a <= t; a++) i += "0";
    else for (i = i.substring(0, n + t + 1), a = s; a <= n + t; a++) i += "0";
    return i
}
function set_insure_status() {
    var e = getRadioValue("shipping"),
        t = 0;
    e > 0 && (document.forms.theForm.elements["insure_" + e] && (t = document.forms.theForm.elements["insure_" + e].value), document.forms.theForm.elements.need_insure && (document.forms.theForm.elements.need_insure.checked = !1), document.getElementById("ecs_insure_cell") && (t > 0 ? (document.getElementById("ecs_insure_cell").style.display = "", setValue(document.getElementById("ecs_insure_fee_cell"), getFormatedPrice(t))) : (document.getElementById("ecs_insure_cell").style.display = "none", setValue(document.getElementById("ecs_insure_fee_cell"), ""))))
}
function getCoordinate(e) {
    var t = {
        x: 0,
        y: 0
    };
    t.x = document.body.offsetLeft,
        t.y = document.body.offsetTop;
    do t.x += e.offsetLeft,
        t.y += e.offsetTop,
        e = e.offsetParent;
    while ("BODY" != e.tagName.toUpperCase());
    return t
}
function showCatalog(e) {
    var t = getCoordinate(e),
        i = document.getElementById("ECS_CATALOG");
    i && "block" != i.style.display && (i.style.display = "block", i.style.left = t.x + "px", i.style.top = t.y + e.offsetHeight - 1 + "px")
}
function hideCatalog(e) {
    var t = document.getElementById("ECS_CATALOG");
    t && "none" != t.style.display && (t.style.display = "none")
}
function sendHashMail() {
    $.get("index.php?c=user&a=send_hash_mail", "", function (e) {
        sendHashMailResponse(e)
    }, "json")
}
function sendHashMailResponse(e) {
    alert(e.message)
}
function orderQuery() {
    var e = document.forms.ecsOrderQuery.order_sn.value,
        t = /^[\.0-9]+/;
    return e.length < 10 || !t.test(e) ? void alert(invalid_order_sn) : void $.get("index.php?c=user&a=order_query&order_sn=s" + e, "", function (e) {
        orderQueryResponse(e)
    }, "json")
}
function orderQueryResponse(e) {
    if (e.message.length > 0 && alert(e.message), 0 == e.error) {
        var t = document.getElementById("ECS_ORDER_QUERY");
        t.innerHTML = e.content
    }
}
function display_mode(e) {
    function t() {
        document.forms.listform.submit()
    }

    document.getElementById("display").value = e,
        setTimeout(t, 0)
}
function display_mode_wholesale(e) {
    function t() {
        document.forms.wholesale_goods.action = "index.php?c=wholesale&a=index",
            document.forms.wholesale_goods.submit()
    }

    document.getElementById("display").value = e,
        setTimeout(t, 0)
}
function fixpng() {
    var e = navigator.appVersion.split("MSIE"),
        t = parseFloat(e[1]);
    if (t >= 5.5 && document.body.filters) for (var i = 0; i < document.images.length; i++) {
        var n = document.images[i],
            s = n.src.toUpperCase();
        if ("PNG" == s.substring(s.length - 3, s.length)) {
            var a = n.id ? "id='" + n.id + "' " : "",
                o = n.className ? "class='" + n.className + "' " : "",
                r = n.title ? "title='" + n.title + "' " : "title='" + n.alt + "' ",
                l = "display:inline-block;" + n.style.cssText;
            "left" == n.align && (l = "float:left;" + l),
            "right" == n.align && (l = "float:right;" + l),
            n.parentElement.href && (l = "cursor:hand;" + l);
            var c = "<span " + a + o + r + ' style="width:' + n.width + "px; height:" + n.height + "px;" + l + ";filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + n.src + "', sizingMethod='scale');\"></span>";
            n.outerHTML = c,
                i -= 1
        }
    }
}
function hash(e, t) {
    var t = t ? t : 32,
        i = 0,
        n = 0,
        s = "";
    for (filllen = t - e.length % t, n = 0; n < filllen; n++) e += "0";
    for (; i < e.length;) s = stringxor(s, e.substr(i, t)),
        i += t;
    return s
}
function stringxor(e, t) {
    for (var i = "", n = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", s = Math.max(e.length, t.length), a = 0; a < s; a++) {
        var o = e.charCodeAt(a) ^ t.charCodeAt(a);
        i += n.charAt(o % 52)
    }
    return i
}
function evalscript(e) {
    if (e.indexOf("<script") == -1) return e;
    for (var t = /<script[^\>]*?src=\"([^\>]*?)\"[^\>]*?(reload=\"1\")?(?:charset=\"([\w\-]+?)\")?><\/script>/gi, i = new Array; i = t.exec(e);) appendscript(i[1], "", i[2], i[3]);
    return e
}
function $$(e) {
    return document.getElementById(e)
}
function appendscript(e, t, i, n) {
    var s = hash(e + t);
    if (i || !in_array(s, evalscripts)) {
        i && $$(s) && $$(s).parentNode.removeChild($$(s)),
            evalscripts.push(s);
        var a = document.createElement("script");
        a.type = "text/javascript",
            a.id = s;
        try {
            e ? a.src = e : t && (a.text = t),
                $$("append_parent").appendChild(a)
        } catch (o) {
        }
    }
}
function in_array(e, t) {
    if ("string" == typeof e || "number" == typeof e) for (var i in t) if (t[i] == e) return !0;
    return !1
}
function pmwin(e, t) {
    var n = document.getElementsByTagName("OBJECT");
    if ("open" == e) {
        for (i = 0; i < n.length; i++)"hidden" != n[i].style.visibility && (n[i].setAttribute("oldvisibility", n[i].style.visibility), n[i].style.visibility = "hidden");
        var s = document.body.clientWidth,
            a = document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight,
            o = document.body.scrollTop ? document.body.scrollTop : document.documentElement.scrollTop,
            r = 800,
            l = .9 * a;
        $$("pmlayer") || (div = document.createElement("div"), div.id = "pmlayer", div.style.width = r + "px", div.style.height = l + "px", div.style.left = (s - r) / 2 + "px", div.style.position = "absolute", div.style.zIndex = "999", $$("append_parent").appendChild(div), $$("pmlayer").innerHTML = '<div style="width: 800px; background: #666666; margin: 5px auto; text-align: left"><div style="width: 800px; height: ' + l + 'px; padding: 1px; background: #FFFFFF; border: 1px solid #7597B8; position: relative; left: -6px; top: -3px"><div onmousedown="pmwindrag(event, 1)" onmousemove="pmwindrag(event, 2)" onmouseup="pmwindrag(event, 3)" style="cursor: move; position: relative; left: 0px; top: 0px; width: 800px; height: 30px; margin-bottom: -30px;"></div><a href="###" onclick="pmwin(\'close\')"><img style="position: absolute; right: 20px; top: 15px" src="images/close.gif" title="关闭" /></a><iframe id="pmframe" name="pmframe" style="width:' + r + 'px;height:100%" allowTransparency="true" frameborder="0"></iframe></div></div>'),
            $$("pmlayer").style.display = "",
            $$("pmlayer").style.top = (a - l) / 2 + o + "px",
            t ? pmframe.location = "index.php?c=pm&a=index&" + t : pmframe.location = "index.php?c=pm&a=index"
    } else if ("close" == e) {
        for (i = 0; i < n.length; i++) n[i].attributes.oldvisibility && (n[i].style.visibility = n[i].attributes.oldvisibility.nodeValue, n[i].removeAttribute("oldvisibility"));
        hiddenobj = new Array,
            $$("pmlayer").style.display = "none"
    }
}
function pmwindrag(e, t) {
    if (1 == t) pmwindragstart = is_ie ? [event.clientX, event.clientY] : [e.clientX, e.clientY],
        pmwindragstart[2] = parseInt($$("pmlayer").style.left),
        pmwindragstart[3] = parseInt($$("pmlayer").style.top),
        doane(e);
    else if (2 == t && pmwindragstart[0]) {
        var i = is_ie ? [event.clientX, event.clientY] : [e.clientX, e.clientY];
        $$("pmlayer").style.left = pmwindragstart[2] + i[0] - pmwindragstart[0] + "px",
            $$("pmlayer").style.top = pmwindragstart[3] + i[1] - pmwindragstart[1] + "px",
            doane(e)
    } else 3 == t && (pmwindragstart = [], doane(e))
}
function doane(t) {
    e = t ? t : window.event,
        is_ie ? (e.returnValue = !1, e.cancelBubble = !0) : e && (e.stopPropagation(), e.preventDefault())
}
function addPackageToCart(e, t, i) {
    var n = new Object,
        s = 1;
    if (n.package_id = e, n.number = s, n.area_id = t, n.warehouse_id = i, document.getElementById("confirm_type")) {
        var a = document.getElementById("confirm_type").value;
        n.confirm_type = a
    }
    $.post("index.php?r=flow/index/add_package_to_cart", {
        package_info: $.toJSON(n)
    }, function (e) {
        addPackageToCartResponse(e)
    }, "json")
}
function addPackageToCartResponse(e) {
    if (e.error > 0) 2 == e.error ? layer.open({
        content: e.message,
        btn: ["好", "取消"],
        shadeClose: !1,
        yes: function () {
            window.location.href = "index.php?r=user/index/add_booking&id=" + e.package_id
        },
        no: function () {
        }
    }) : d_messages(e.message);
    else {
        var t = document.getElementById("ECS_CARTINFO"),
            i = "index.php?r=cart/index/index";
        if (t && (t.innerHTML = e.content), "1" == e.one_step_buy) location.href = i;
        else switch (e.confirm_type) {
            case "1":
                confirm(e.message) && (location.href = i);
                break;
            case "2":
                confirm(e.message) || (location.href = i);
                break;
            case "3":
                location.href = i
        }
    }
}
function setSuitShow(e) {
    var t = document.getElementById("suit_" + e);
    null != t && ("none" == t.style.display ? t.style.display = "" : t.style.display = "none")
}
function docEle() {
    return document.getElementById(arguments[0]) || !1
}
function openSpeDiv(e, t, i) {
    var n = "speDiv",
        s = "mask";
    docEle(n) && document.removeChild(docEle(n)),
    docEle(s) && document.removeChild(docEle(s));
    var a;
    "undefined" != typeof window.pageYOffset ? a = window.pageYOffset : "undefined" != typeof document.compatMode && "BackCompat" != document.compatMode ? a = document.documentElement.scrollTop : "undefined" != typeof document.body && (a = document.body.scrollTop);
    for (var o = 0, r = document.getElementsByTagName("select"); r[o];) o++;
    var l = document.createElement("div");
    l.id = n,
        l.style.position = "absolute",
        l.style.zIndex = "10000",
        l.style.width = "300px",
        l.style.height = "260px",
        l.style.top = parseInt(a + 200) + "px",
        l.style.left = (parseInt(document.body.offsetWidth) - 200) / 2 + "px",
        l.style.overflow = "auto",
        l.style.background = "#FFF",
        l.style.border = "3px solid #59B0FF",
        l.style.padding = "5px",
        l.innerHTML = '<h4 style="font-size:14; margin:15 0 0 15;">' + select_spe + "</h4>";
    for (var c = 0; c < e.length; c++) if (l.innerHTML += '<hr style="color: #EBEBED; height:1px;"><h6 style="text-align:left; background:#ffffff; margin-left:15px;">' + e[c].name + "</h6>", 1 == e[c].attr_type) {
        for (var d = 0; d < e[c].values.length; d++) 0 == d ? l.innerHTML += "<input style='margin-left:15px;' type='radio' name='spec_" + e[c].attr_id + "' value='" + e[c].values[d].id + "' id='spec_value_" + e[c].values[d].id + "' checked /><font color=#555555>" + e[c].values[d].label + "</font> [" + e[c].values[d].format_price + "]</font><br />" : l.innerHTML += "<input style='margin-left:15px;' type='radio' name='spec_" + e[c].attr_id + "' value='" + e[c].values[d].id + "' id='spec_value_" + e[c].values[d].id + "' /><font color=#555555>" + e[c].values[d].label + "</font> [" + e[c].values[d].format_price + "]</font><br />";
        l.innerHTML += "<input type='hidden' name='spec_list' value='" + d + "' />"
    } else {
        for (var d = 0; d < e[c].values.length; d++) l.innerHTML += "<input style='margin-left:15px;' type='checkbox' name='spec_" + e[c].attr_id + "' value='" + e[c].values[d].id + "' id='spec_value_" + e[c].values[d].id + "' /><font color=#555555>" + e[c].values[d].label + " [" + e[c].values[d].format_price + "]</font><br />";
        l.innerHTML += "<input type='hidden' name='spec_list' value='" + d + "' />"
    }
    l.innerHTML += "<br /><center>[<a href='javascript:submit_div(" + t + "," + i + ")' class='f6' >" + btn_buy + "</a>]&nbsp;&nbsp;[<a href='javascript:cancel_div()' class='f6' >" + is_cancel + "</a>]</center>",
        document.body.appendChild(l);
    var h = document.createElement("div");
    h.id = s,
        h.style.position = "absolute",
        h.style.zIndex = "9999",
        h.style.width = document.body.scrollWidth + "px",
        h.style.height = document.body.scrollHeight + "px",
        h.style.top = "0px",
        h.style.left = "0px",
        h.style.background = "#FFF",
        h.style.filter = "alpha(opacity=30)",
        h.style.opacity = "0.40",
        document.body.appendChild(h)
}
function submit_div(e, t) {
    var i = new Object,
        n = new Array,
        s = (new Array, 1),
        a = document.getElementsByTagName("input"),
        o = 1,
        n = new Array,
        r = 0;
    for (c = 0; c < a.length; c++) {
        var l = a[c].name.substr(0, 5);
        "spec_" != l || "radio" != a[c].type && "checkbox" != a[c].type || !a[c].checked || (n[r] = a[c].value, r++)
    }
    i.quick = o,
        i.spec = n,
        i.goods_id = e,
        i.number = s,
        i.parent = "undefined" == typeof t ? 0 : parseInt(t),
        $.post("index.php?c=flow&a=add_to_cart", {
            goods: $.toJSON(i)
        }, function (e) {
            addToCartResponse(e)
        }, "json"),
        document.body.removeChild(docEle("speDiv")),
        document.body.removeChild(docEle("mask"));
    for (var c = 0, d = document.getElementsByTagName("select"); d[c];) d[c].style.visibility = "",
        c++
}
function cancel_div() {
    document.body.removeChild(docEle("speDiv")),
        document.body.removeChild(docEle("mask"));
    for (var e = 0, t = document.getElementsByTagName("select"); t[e];) t[e].style.visibility = "",
        e++
}
function addToCart_quick(e, t) {
    var i = new Object,
        n = new Array,
        s = (new Array, 1),
        a = document.forms.ECS_FORMBUY,
        o = 0,
        r = 0;
    if (a && (str = getSelectedAttributes(a), n = str.split(","), a.elements.number && (s = a.elements.number.value), o = 1), document.getElementById("region_id")) {
        var l = document.getElementById("region_id").value;
        i.warehouse_id = l
    }
    if (document.getElementById("area_id")) {
        var c = document.getElementById("area_id").value;
        i.area_id = c
    }
    document.getElementsByName("store_id").length > 0 && (r = document.getElementsByName("store_id")[0].value),
        i.quick = o,
        i.spec = n,
        i.goods_id = e,
        i.store_id = parseInt(r),
        i.number = s,
        i.parent = "undefined" == typeof t ? 0 : parseInt(t),
        $.post("index.php?r=cart/index/add_to_cart", {
            goods: $.toJSON(i)
        }, function (e) {
            addToCartResponse_quick(e)
        }, "json")
}
function addToCartResponse_quick(e) {
    if (document.removeEventListener("touchmove", handler, !1), e.error > 0) 2 == e.error ? layer.open({
        content: e.message,
        btn: ["确定", "取消"],
        shadeClose: !1,
        yes: function () {
            location.href = "index.php?r=user/index/add_booking&id=" + e.goods_id + "&spec=" + e.product_spec
        },
        no: function () {
        }
    }) : 6 == e.error ? location.href = "index.php?r=goods/index/index&id=" + e.goods_id : d_messages(e.message, 1);
    else if (e.store_id > 0 && (window.location.href = "index.php?r=flow&store_id=" + e.store_id + "&cart_value=" + e.cart_value), d_messages("商品已加入购物", 2), $(".cart-num").html(e.goods_number), $(".j-filter-show-div").hasClass("show")) return $(".j-filter-show-div").removeClass("show"),
        $(".mask-filter-div").removeClass("show"),
        !1
}
function in_addToCart(e, t) {
    var i = new Object,
        n = new Array,
        s = (new Array, $("input[name=number]").val()),
        a = 0;
    e || (e = $(".goods-id").val()),
    ($(".goods-size").hasClass() || $(".goods-color").hasClass()) && (a = 1),
        i.quick = a,
        i.spec = n,
        i.goods_id = e,
        i.number = s,
        i.parent = "undefined" == typeof t ? 0 : parseInt(t),
        $.ajax({
            url: "index.php?r=cart/index/add_to_cart",
            type: "POST",
            data: {
                goods: $.toJSON(i)
            },
            dataType: "json",
            success: function (e) {
                0 == e.error ? d_messages("已加入购物车", 2) : d_messages(e.message, 2),
                    window.location.reload()
            }
        })
}
function addtocart_attr(e) {
    var t = new Object,
        i = 1;
    $("label.ts-1" + e).each(function () {
        $(this).hasClass("active") && (str += $(this).attr("attr-id") + ",")
    }),
        t.goods_id = e,
        t.spec = str.substr(0, str.length - 1).split(","),
        t.number = $(".attr-number" + e).val(),
        t.warehouse_id = $("input[name=warehouse_id]").val(),
        t.area_id = $("input[name=area_id]").val(),
        t.quick = i,
        $.ajax({
            url: "index.php?r=cart/index/add_to_cart",
            type: "POST",
            data: {
                goods: $.toJSON(t)
            },
            dataType: "json",
            success: function (e) {
                0 == e.error ? d_messages("已加入购物车", 2) : d_messages(e.message, 2),
                    window.location = "index.php?r=cart/index"
            }
        })
}
function addtocart_list(e) {
    var t = new Object,
        i = 1;
    $("label.ts-1" + e).each(function () {
        $(this).hasClass("active") && (strr += $(this).attr("attr-id") + ",")
    }),
        t.goods_id = e,
        t.spec = strr.substr(0, strr.length - 1).split(","),
        t.number = $("input[name=number" + e + "]").val(),
        t.warehouse_id = $("input[name=warehouse_id]").val(),
        t.area_id = $("input[name=area_id]").val(),
        t.quick = i,
        $.ajax({
            url: "index.php?r=cart/index/add_to_cart",
            type: "POST",
            data: {
                goods: $.toJSON(t)
            },
            dataType: "json",
            success: function (e) {
                0 == e.error ? d_messages("已加入购物车", 2) : d_messages(e.message, 2),
                    window.location = "index.php?r=cart/index"
            }
        })
}
function swiper_scroll() {
    new Swiper(".swiper-scroll", {
        scrollbar: !1,
        direction: "vertical",
        slidesPerView: "auto",
        mousewheelControl: !0,
        freeMode: !0
    })
}
function receivebonus(id) {
    $.ajax({
        type: "GET",
        url: "index.php?r=cart/index/receive_bonus",
        data: {
            bonus_id: id
        },
        success: function (data) {
            data = eval("(" + data + ")"),
                data.code ? layer.open({
                    content: "请登录后领取",
                    btn: ["立即登录", "取消"],
                    shadeClose: !1,
                    yes: function () {
                        window.location.href = "index.php?r=user/login"
                    },
                    no: function () {
                    }
                }) : d_messages(data.msg)
        }
    })
}
function d_messages(e, t) {
    var i = "";
    t = arguments[1] ? arguments[1] : 2,
    1 == t && (i = "border:none; background: rgba(0,0,0,.7); color:#fff; max-width:100%; top:0; position:fixed; left:0; right:0; border-radius:0;"),
    2 == t && (i = "border:none; background: rgba(0,0,0,.7); color:#fff; max-width:90%; min-width:1rem; margin:0 auto; border-radius:.8rem;"),
        layer.open({
            style: i,
            type: 0,
            anim: 3,
            content: e,
            shade: !1,
            time: 2
        })
}
function d_messages_btn(e, t, i) {
    layer.open({
        content: e,
        btn: [t, i],
        shadeClose: !1,
        yes: function () {
        },
        no: function () {
        }
    })
}
function warehouse(e, t) {
    if (e && t) {
        var i = "index.php?r=goods/index/in_warehouse";
        $.get(i, {
            pid: e,
            id: t
        }, function (e) {
            e.goods_id && (location.href = "index.php?r=goods&id=" + e.goods_id)
        }, "json")
    }
}
function showregionname() {
    var e = $("input[name=province_region_id]").val(),
        t = $("input[name=city_region_id]").val(),
        i = $("input[name=district_region_id]").val();
    $.post("index.php?r=user/index/show_region_name", {
        province: e,
        city: t,
        district: i
    }, function (e) {
        alert(e.city)
    })
}
!
    function (e, t) {
        "object" == typeof module && "object" == typeof module.exports ? module.exports = e.document ? t(e, !0) : function (e) {
            if (!e.document) throw new Error("jQuery requires a window with a document");
            return t(e)
        } : t(e)
    }("undefined" != typeof window ? window : this, function (e, t) {
        function i(e) {
            var t = e.length,
                i = se.type(e);
            return "function" !== i && !se.isWindow(e) && (!(1 !== e.nodeType || !t) || ("array" === i || 0 === t || "number" == typeof t && t > 0 && t - 1 in e))
        }

        function n(e, t, i) {
            if (se.isFunction(t)) return se.grep(e, function (e, n) {
                return !!t.call(e, n, e) !== i
            });
            if (t.nodeType) return se.grep(e, function (e) {
                return e === t !== i
            });
            if ("string" == typeof t) {
                if (ue.test(t)) return se.filter(t, e, i);
                t = se.filter(t, e)
            }
            return se.grep(e, function (e) {
                return se.inArray(e, t) >= 0 !== i
            })
        }

        function s(e, t) {
            do e = e[t];
            while (e && 1 !== e.nodeType);
            return e
        }

        function a(e) {
            var t = we[e] = {};
            return se.each(e.match(be) || [], function (e, i) {
                t[i] = !0
            }),
                t
        }

        function o() {
            fe.addEventListener ? (fe.removeEventListener("DOMContentLoaded", r, !1), e.removeEventListener("load", r, !1)) : (fe.detachEvent("onreadystatechange", r), e.detachEvent("onload", r))
        }

        function r() {
            (fe.addEventListener || "load" === event.type || "complete" === fe.readyState) && (o(), se.ready())
        }

        function l(e, t, i) {
            if (void 0 === i && 1 === e.nodeType) {
                var n = "data-" + t.replace(Te, "-$1").toLowerCase();
                if (i = e.getAttribute(n), "string" == typeof i) {
                    try {
                        i = "true" === i || "false" !== i && ("null" === i ? null : +i + "" === i ? +i : ke.test(i) ? se.parseJSON(i) : i)
                    } catch (s) {
                    }
                    se.data(e, t, i)
                } else i = void 0
            }
            return i
        }

        function c(e) {
            var t;
            for (t in e) if (("data" !== t || !se.isEmptyObject(e[t])) && "toJSON" !== t) return !1;
            return !0
        }

        function d(e, t, i, n) {
            if (se.acceptData(e)) {
                var s, a, o = se.expando,
                    r = e.nodeType,
                    l = r ? se.cache : e,
                    c = r ? e[o] : e[o] && o;
                if (c && l[c] && (n || l[c].data) || void 0 !== i || "string" != typeof t) return c || (c = r ? e[o] = G.pop() || se.guid++ : o),
                l[c] || (l[c] = r ? {} : {
                    toJSON: se.noop
                }),
                ("object" == typeof t || "function" == typeof t) && (n ? l[c] = se.extend(l[c], t) : l[c].data = se.extend(l[c].data, t)),
                    a = l[c],
                n || (a.data || (a.data = {}), a = a.data),
                void 0 !== i && (a[se.camelCase(t)] = i),
                    "string" == typeof t ? (s = a[t], null == s && (s = a[se.camelCase(t)])) : s = a,
                    s
            }
        }

        function h(e, t, i) {
            if (se.acceptData(e)) {
                var n, s, a = e.nodeType,
                    o = a ? se.cache : e,
                    r = a ? e[se.expando] : se.expando;
                if (o[r]) {
                    if (t && (n = i ? o[r] : o[r].data)) {
                        se.isArray(t) ? t = t.concat(se.map(t, se.camelCase)) : t in n ? t = [t] : (t = se.camelCase(t), t = t in n ? [t] : t.split(" ")),
                            s = t.length;
                        for (; s--;) delete n[t[s]];
                        if (i ? !c(n) : !se.isEmptyObject(n)) return
                    }
                    (i || (delete o[r].data, c(o[r]))) && (a ? se.cleanData([e], !0) : ie.deleteExpando || o != o.window ? delete o[r] : o[r] = null)
                }
            }
        }

        function u() {
            return !0
        }

        function p() {
            return !1
        }

        function f() {
            try {
                return fe.activeElement
            } catch (e) {
            }
        }

        function m(e) {
            var t = ze.split("|"),
                i = e.createDocumentFragment();
            if (i.createElement) for (; t.length;) i.createElement(t.pop());
            return i
        }

        function g(e, t) {
            var i, n, s = 0,
                a = typeof e.getElementsByTagName !== Ce ? e.getElementsByTagName(t || "*") : typeof e.querySelectorAll !== Ce ? e.querySelectorAll(t || "*") : void 0;
            if (!a) for (a = [], i = e.childNodes || e; null != (n = i[s]); s++)!t || se.nodeName(n, t) ? a.push(n) : se.merge(a, g(n, t));
            return void 0 === t || t && se.nodeName(e, t) ? se.merge([e], a) : a
        }

        function v(e) {
            Pe.test(e.type) && (e.defaultChecked = e.checked)
        }

        function y(e, t) {
            return se.nodeName(e, "table") && se.nodeName(11 !== t.nodeType ? t : t.firstChild, "tr") ? e.getElementsByTagName("tbody")[0] || e.appendChild(e.ownerDocument.createElement("tbody")) : e
        }

        function b(e) {
            return e.type = (null !== se.find.attr(e, "type")) + "/" + e.type,
                e
        }

        function w(e) {
            var t = Ue.exec(e.type);
            return t ? e.type = t[1] : e.removeAttribute("type"),
                e
        }

        function _(e, t) {
            for (var i, n = 0; null != (i = e[n]); n++) se._data(i, "globalEval", !t || se._data(t[n], "globalEval"))
        }

        function x(e, t) {
            if (1 === t.nodeType && se.hasData(e)) {
                var i, n, s, a = se._data(e),
                    o = se._data(t, a),
                    r = a.events;
                if (r) {
                    delete o.handle,
                        o.events = {};
                    for (i in r) for (n = 0, s = r[i].length; s > n; n++) se.event.add(t, i, r[i][n])
                }
                o.data && (o.data = se.extend({}, o.data))
            }
        }

        function C(e, t) {
            var i, n, s;
            if (1 === t.nodeType) {
                if (i = t.nodeName.toLowerCase(), !ie.noCloneEvent && t[se.expando]) {
                    s = se._data(t);
                    for (n in s.events) se.removeEvent(t, n, s.handle);
                    t.removeAttribute(se.expando)
                }
                "script" === i && t.text !== e.text ? (b(t).text = e.text, w(t)) : "object" === i ? (t.parentNode && (t.outerHTML = e.outerHTML), ie.html5Clone && e.innerHTML && !se.trim(t.innerHTML) && (t.innerHTML = e.innerHTML)) : "input" === i && Pe.test(e.type) ? (t.defaultChecked = t.checked = e.checked, t.value !== e.value && (t.value = e.value)) : "option" === i ? t.defaultSelected = t.selected = e.defaultSelected : ("input" === i || "textarea" === i) && (t.defaultValue = e.defaultValue)
            }
        }

        function k(t, i) {
            var n, s = se(i.createElement(t)).appendTo(i.body),
                a = e.getDefaultComputedStyle && (n = e.getDefaultComputedStyle(s[0])) ? n.display : se.css(s[0], "display");
            return s.detach(),
                a
        }

        function T(e) {
            var t = fe,
                i = Ze[e];
            return i || (i = k(e, t), "none" !== i && i || (Je = (Je || se("<iframe frameborder='0' width='0' height='0'/>")).appendTo(t.documentElement), t = (Je[0].contentWindow || Je[0].contentDocument).document, t.write(), t.close(), i = k(e, t), Je.detach()), Ze[e] = i),
                i
        }

        function S(e, t) {
            return {
                get: function () {
                    var i = e();
                    if (null != i) return i ? void delete this.get : (this.get = t).apply(this, arguments)
                }
            }
        }

        function D(e, t) {
            if (t in e) return t;
            for (var i = t.charAt(0).toUpperCase() + t.slice(1), n = t, s = ut.length; s--;) if (t = ut[s] + i, t in e) return t;
            return n
        }

        function E(e, t) {
            for (var i, n, s, a = [], o = 0, r = e.length; r > o; o++) n = e[o],
            n.style && (a[o] = se._data(n, "olddisplay"), i = n.style.display, t ? (a[o] || "none" !== i || (n.style.display = ""), "" === n.style.display && Ee(n) && (a[o] = se._data(n, "olddisplay", T(n.nodeName)))) : (s = Ee(n), (i && "none" !== i || !s) && se._data(n, "olddisplay", s ? i : se.css(n, "display"))));
            for (o = 0; r > o; o++) n = e[o],
            n.style && (t && "none" !== n.style.display && "" !== n.style.display || (n.style.display = t ? a[o] || "" : "none"));
            return e
        }

        function I(e, t, i) {
            var n = lt.exec(t);
            return n ? Math.max(0, n[1] - (i || 0)) + (n[2] || "px") : t
        }

        function P(e, t, i, n, s) {
            for (var a = i === (n ? "border" : "content") ? 4 : "width" === t ? 1 : 0, o = 0; 4 > a; a += 2)"margin" === i && (o += se.css(e, i + De[a], !0, s)),
                n ? ("content" === i && (o -= se.css(e, "padding" + De[a], !0, s)), "margin" !== i && (o -= se.css(e, "border" + De[a] + "Width", !0, s))) : (o += se.css(e, "padding" + De[a], !0, s), "padding" !== i && (o += se.css(e, "border" + De[a] + "Width", !0, s)));
            return o
        }

        function M(e, t, i) {
            var n = !0,
                s = "width" === t ? e.offsetWidth : e.offsetHeight,
                a = et(e),
                o = ie.boxSizing && "border-box" === se.css(e, "boxSizing", !1, a);
            if (0 >= s || null == s) {
                if (s = tt(e, t, a), (0 > s || null == s) && (s = e.style[t]), nt.test(s)) return s;
                n = o && (ie.boxSizingReliable() || s === e.style[t]),
                    s = parseFloat(s) || 0
            }
            return s + P(e, t, i || (o ? "border" : "content"), n, a) + "px"
        }

        function j(e, t, i, n, s) {
            return new j.prototype.init(e, t, i, n, s)
        }

        function N() {
            return setTimeout(function () {
                pt = void 0
            }),
                pt = se.now()
        }

        function A(e, t) {
            var i, n = {
                    height: e
                },
                s = 0;
            for (t = t ? 1 : 0; 4 > s; s += 2 - t) i = De[s],
                n["margin" + i] = n["padding" + i] = e;
            return t && (n.opacity = n.width = e),
                n
        }

        function O(e, t, i) {
            for (var n, s = (bt[t] || []).concat(bt["*"]), a = 0, o = s.length; o > a; a++) if (n = s[a].call(i, t, e)) return n
        }

        function z(e, t, i) {
            var n, s, a, o, r, l, c, d, h = this,
                u = {},
                p = e.style,
                f = e.nodeType && Ee(e),
                m = se._data(e, "fxshow");
            i.queue || (r = se._queueHooks(e, "fx"), null == r.unqueued && (r.unqueued = 0, l = r.empty.fire, r.empty.fire = function () {
                r.unqueued || l()
            }), r.unqueued++, h.always(function () {
                h.always(function () {
                    r.unqueued--,
                    se.queue(e, "fx").length || r.empty.fire()
                })
            })),
            1 === e.nodeType && ("height" in t || "width" in t) && (i.overflow = [p.overflow, p.overflowX, p.overflowY], c = se.css(e, "display"), d = "none" === c ? se._data(e, "olddisplay") || T(e.nodeName) : c, "inline" === d && "none" === se.css(e, "float") && (ie.inlineBlockNeedsLayout && "inline" !== T(e.nodeName) ? p.zoom = 1 : p.display = "inline-block")),
            i.overflow && (p.overflow = "hidden", ie.shrinkWrapBlocks() || h.always(function () {
                p.overflow = i.overflow[0],
                    p.overflowX = i.overflow[1],
                    p.overflowY = i.overflow[2]
            }));
            for (n in t) if (s = t[n], mt.exec(s)) {
                if (delete t[n], a = a || "toggle" === s, s === (f ? "hide" : "show")) {
                    if ("show" !== s || !m || void 0 === m[n]) continue;
                    f = !0
                }
                u[n] = m && m[n] || se.style(e, n)
            } else c = void 0;
            if (se.isEmptyObject(u))"inline" === ("none" === c ? T(e.nodeName) : c) && (p.display = c);
            else {
                m ? "hidden" in m && (f = m.hidden) : m = se._data(e, "fxshow", {}),
                a && (m.hidden = !f),
                    f ? se(e).show() : h.done(function () {
                        se(e).hide()
                    }),
                    h.done(function () {
                        var t;
                        se._removeData(e, "fxshow");
                        for (t in u) se.style(e, t, u[t])
                    });
                for (n in u) o = O(f ? m[n] : 0, n, h),
                n in m || (m[n] = o.start, f && (o.end = o.start, o.start = "width" === n || "height" === n ? 1 : 0))
            }
        }

        function $(e, t) {
            var i, n, s, a, o;
            for (i in e) if (n = se.camelCase(i), s = t[n], a = e[i], se.isArray(a) && (s = a[1], a = e[i] = a[0]), i !== n && (e[n] = a, delete e[i]), o = se.cssHooks[n], o && "expand" in o) {
                a = o.expand(a),
                    delete e[n];
                for (i in a) i in e || (e[i] = a[i], t[i] = s)
            } else t[n] = s
        }

        function H(e, t, i) {
            var n, s, a = 0,
                o = yt.length,
                r = se.Deferred().always(function () {
                    delete l.elem
                }),
                l = function () {
                    if (s) return !1;
                    for (var t = pt || N(), i = Math.max(0, c.startTime + c.duration - t), n = i / c.duration || 0, a = 1 - n, o = 0, l = c.tweens.length; l > o; o++) c.tweens[o].run(a);
                    return r.notifyWith(e, [c, a, i]),
                        1 > a && l ? i : (r.resolveWith(e, [c]), !1)
                },
                c = r.promise({
                    elem: e,
                    props: se.extend({}, t),
                    opts: se.extend(!0, {
                        specialEasing: {}
                    }, i),
                    originalProperties: t,
                    originalOptions: i,
                    startTime: pt || N(),
                    duration: i.duration,
                    tweens: [],
                    createTween: function (t, i) {
                        var n = se.Tween(e, c.opts, t, i, c.opts.specialEasing[t] || c.opts.easing);
                        return c.tweens.push(n),
                            n
                    },
                    stop: function (t) {
                        var i = 0,
                            n = t ? c.tweens.length : 0;
                        if (s) return this;
                        for (s = !0; n > i; i++) c.tweens[i].run(1);
                        return t ? r.resolveWith(e, [c, t]) : r.rejectWith(e, [c, t]),
                            this
                    }
                }),
                d = c.props;
            for ($(d, c.opts.specialEasing); o > a; a++) if (n = yt[a].call(c, e, d, c.opts)) return n;
            return se.map(d, O, c),
            se.isFunction(c.opts.start) && c.opts.start.call(e, c),
                se.fx.timer(se.extend(l, {
                    elem: e,
                    anim: c,
                    queue: c.opts.queue
                })),
                c.progress(c.opts.progress).done(c.opts.done, c.opts.complete).fail(c.opts.fail).always(c.opts.always)
        }

        function L(e) {
            return function (t, i) {
                "string" != typeof t && (i = t, t = "*");
                var n, s = 0,
                    a = t.toLowerCase().match(be) || [];
                if (se.isFunction(i)) for (; n = a[s++];)"+" === n.charAt(0) ? (n = n.slice(1) || "*", (e[n] = e[n] || []).unshift(i)) : (e[n] = e[n] || []).push(i)
            }
        }

        function W(e, t, i, n) {
            function s(r) {
                var l;
                return a[r] = !0,
                    se.each(e[r] || [], function (e, r) {
                        var c = r(t, i, n);
                        return "string" != typeof c || o || a[c] ? o ? !(l = c) : void 0 : (t.dataTypes.unshift(c), s(c), !1)
                    }),
                    l
            }

            var a = {},
                o = e === Bt;
            return s(t.dataTypes[0]) || !a["*"] && s("*")
        }

        function R(e, t) {
            var i, n, s = se.ajaxSettings.flatOptions || {};
            for (n in t) void 0 !== t[n] && ((s[n] ? e : i || (i = {}))[n] = t[n]);
            return i && se.extend(!0, e, i),
                e
        }

        function F(e, t, i) {
            for (var n, s, a, o, r = e.contents, l = e.dataTypes;
                 "*" === l[0];) l.shift(),
            void 0 === s && (s = e.mimeType || t.getResponseHeader("Content-Type"));
            if (s) for (o in r) if (r[o] && r[o].test(s)) {
                l.unshift(o);
                break
            }
            if (l[0] in i) a = l[0];
            else {
                for (o in i) {
                    if (!l[0] || e.converters[o + " " + l[0]]) {
                        a = o;
                        break
                    }
                    n || (n = o)
                }
                a = a || n
            }
            return a ? (a !== l[0] && l.unshift(a), i[a]) : void 0
        }

        function B(e, t, i, n) {
            var s, a, o, r, l, c = {},
                d = e.dataTypes.slice();
            if (d[1]) for (o in e.converters) c[o.toLowerCase()] = e.converters[o];
            for (a = d.shift(); a;) if (e.responseFields[a] && (i[e.responseFields[a]] = t), !l && n && e.dataFilter && (t = e.dataFilter(t, e.dataType)), l = a, a = d.shift()) if ("*" === a) a = l;
            else if ("*" !== l && l !== a) {
                if (o = c[l + " " + a] || c["* " + a], !o) for (s in c) if (r = s.split(" "), r[1] === a && (o = c[l + " " + r[0]] || c["* " + r[0]])) {
                    o === !0 ? o = c[s] : c[s] !== !0 && (a = r[0], d.unshift(r[1]));
                    break
                }
                if (o !== !0) if (o && e["throws"]) t = o(t);
                else try {
                        t = o(t)
                    } catch (h) {
                        return {
                            state: "parsererror",
                            error: o ? h : "No conversion from " + l + " to " + a
                        }
                    }
            }
            return {
                state: "success",
                data: t
            }
        }

        function q(e, t, i, n) {
            var s;
            if (se.isArray(t)) se.each(t, function (t, s) {
                i || Ut.test(e) ? n(e, s) : q(e + "[" + ("object" == typeof s ? t : "") + "]", s, i, n)
            });
            else if (i || "object" !== se.type(t)) n(e, t);
            else for (s in t) q(e + "[" + s + "]", t[s], i, n)
        }

        function V() {
            try {
                return new e.XMLHttpRequest
            } catch (t) {
            }
        }

        function Y() {
            try {
                return new e.ActiveXObject("Microsoft.XMLHTTP")
            } catch (t) {
            }
        }

        function U(e) {
            return se.isWindow(e) ? e : 9 === e.nodeType && (e.defaultView || e.parentWindow)
        }

        var G = [],
            X = G.slice,
            K = G.concat,
            Q = G.push,
            J = G.indexOf,
            Z = {},
            ee = Z.toString,
            te = Z.hasOwnProperty,
            ie = {},
            ne = "1.11.1",
            se = function (e, t) {
                return new se.fn.init(e, t)
            },
            ae = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,
            oe = /^-ms-/,
            re = /-([\da-z])/gi,
            le = function (e, t) {
                return t.toUpperCase()
            };
        se.fn = se.prototype = {
            jquery: ne,
            constructor: se,
            selector: "",
            length: 0,
            toArray: function () {
                return X.call(this)
            },
            get: function (e) {
                return null != e ? 0 > e ? this[e + this.length] : this[e] : X.call(this)
            },
            pushStack: function (e) {
                var t = se.merge(this.constructor(), e);
                return t.prevObject = this,
                    t.context = this.context,
                    t
            },
            each: function (e, t) {
                return se.each(this, e, t)
            },
            map: function (e) {
                return this.pushStack(se.map(this, function (t, i) {
                    return e.call(t, i, t)
                }))
            },
            slice: function () {
                return this.pushStack(X.apply(this, arguments))
            },
            first: function () {
                return this.eq(0)
            },
            last: function () {
                return this.eq(-1)
            },
            eq: function (e) {
                var t = this.length,
                    i = +e + (0 > e ? t : 0);
                return this.pushStack(i >= 0 && t > i ? [this[i]] : [])
            },
            end: function () {
                return this.prevObject || this.constructor(null)
            },
            push: Q,
            sort: G.sort,
            splice: G.splice
        },
            se.extend = se.fn.extend = function () {
                var e, t, i, n, s, a, o = arguments[0] || {},
                    r = 1,
                    l = arguments.length,
                    c = !1;
                for ("boolean" == typeof o && (c = o, o = arguments[r] || {}, r++), "object" == typeof o || se.isFunction(o) || (o = {}), r === l && (o = this, r--); l > r; r++) if (null != (s = arguments[r])) for (n in s) e = o[n],
                    i = s[n],
                o !== i && (c && i && (se.isPlainObject(i) || (t = se.isArray(i))) ? (t ? (t = !1, a = e && se.isArray(e) ? e : []) : a = e && se.isPlainObject(e) ? e : {}, o[n] = se.extend(c, a, i)) : void 0 !== i && (o[n] = i));
                return o
            },
            se.extend({
                expando: "jQuery" + (ne + Math.random()).replace(/\D/g, ""),
                isReady: !0,
                error: function (e) {
                    throw new Error(e)
                },
                noop: function () {
                },
                isFunction: function (e) {
                    return "function" === se.type(e)
                },
                isArray: Array.isArray ||
                function (e) {
                    return "array" === se.type(e)
                },
                isWindow: function (e) {
                    return null != e && e == e.window
                },
                isNumeric: function (e) {
                    return !se.isArray(e) && e - parseFloat(e) >= 0
                },
                isEmptyObject: function (e) {
                    var t;
                    for (t in e) return !1;
                    return !0
                },
                isPlainObject: function (e) {
                    var t;
                    if (!e || "object" !== se.type(e) || e.nodeType || se.isWindow(e)) return !1;
                    try {
                        if (e.constructor && !te.call(e, "constructor") && !te.call(e.constructor.prototype, "isPrototypeOf")) return !1
                    } catch (i) {
                        return !1
                    }
                    if (ie.ownLast) for (t in e) return te.call(e, t);
                    for (t in e);
                    return void 0 === t || te.call(e, t)
                },
                type: function (e) {
                    return null == e ? e + "" : "object" == typeof e || "function" == typeof e ? Z[ee.call(e)] || "object" : typeof e
                },
                globalEval: function (t) {
                    t && se.trim(t) && (e.execScript ||
                    function (t) {
                        e.eval.call(e, t)
                    })(t)
                },
                camelCase: function (e) {
                    return e.replace(oe, "ms-").replace(re, le)
                },
                nodeName: function (e, t) {
                    return e.nodeName && e.nodeName.toLowerCase() === t.toLowerCase()
                },
                each: function (e, t, n) {
                    var s, a = 0,
                        o = e.length,
                        r = i(e);
                    if (n) {
                        if (r) for (; o > a && (s = t.apply(e[a], n), s !== !1); a++);
                        else for (a in e) if (s = t.apply(e[a], n), s === !1) break
                    } else if (r) for (; o > a && (s = t.call(e[a], a, e[a]), s !== !1); a++);
                    else for (a in e) if (s = t.call(e[a], a, e[a]), s === !1) break;
                    return e
                },
                trim: function (e) {
                    return null == e ? "" : (e + "").replace(ae, "")
                },
                makeArray: function (e, t) {
                    var n = t || [];
                    return null != e && (i(Object(e)) ? se.merge(n, "string" == typeof e ? [e] : e) : Q.call(n, e)),
                        n
                },
                inArray: function (e, t, i) {
                    var n;
                    if (t) {
                        if (J) return J.call(t, e, i);
                        for (n = t.length, i = i ? 0 > i ? Math.max(0, n + i) : i : 0; n > i; i++) if (i in t && t[i] === e) return i
                    }
                    return -1
                },
                merge: function (e, t) {
                    for (var i = +t.length, n = 0, s = e.length; i > n;) e[s++] = t[n++];
                    if (i !== i) for (; void 0 !== t[n];) e[s++] = t[n++];
                    return e.length = s,
                        e
                },
                grep: function (e, t, i) {
                    for (var n, s = [], a = 0, o = e.length, r = !i; o > a; a++) n = !t(e[a], a),
                    n !== r && s.push(e[a]);
                    return s
                },
                map: function (e, t, n) {
                    var s, a = 0,
                        o = e.length,
                        r = i(e),
                        l = [];
                    if (r) for (; o > a; a++) s = t(e[a], a, n),
                    null != s && l.push(s);
                    else for (a in e) s = t(e[a], a, n),
                    null != s && l.push(s);
                    return K.apply([], l)
                },
                guid: 1,
                proxy: function (e, t) {
                    var i, n, s;
                    return "string" == typeof t && (s = e[t], t = e, e = s),
                        se.isFunction(e) ? (i = X.call(arguments, 2), n = function () {
                            return e.apply(t || this, i.concat(X.call(arguments)))
                        }, n.guid = e.guid = e.guid || se.guid++, n) : void 0
                },
                now: function () {
                    return +new Date
                },
                support: ie
            }),
            se.each("Boolean Number String Function Array Date RegExp Object Error".split(" "), function (e, t) {
                Z["[object " + t + "]"] = t.toLowerCase()
            });
        var ce = function (e) {
            function t(e, t, i, n) {
                var s, a, o, r, l, c, h, p, f, m;
                if ((t ? t.ownerDocument || t : W) !== j && M(t), t = t || j, i = i || [], !e || "string" != typeof e) return i;
                if (1 !== (r = t.nodeType) && 9 !== r) return [];
                if (A && !n) {
                    if (s = ye.exec(e)) if (o = s[1]) {
                        if (9 === r) {
                            if (a = t.getElementById(o), !a || !a.parentNode) return i;
                            if (a.id === o) return i.push(a),
                                i
                        } else if (t.ownerDocument && (a = t.ownerDocument.getElementById(o)) && H(t, a) && a.id === o) return i.push(a),
                            i
                    } else {
                        if (s[2]) return Z.apply(i, t.getElementsByTagName(e)),
                            i;
                        if ((o = s[3]) && _.getElementsByClassName && t.getElementsByClassName) return Z.apply(i, t.getElementsByClassName(o)),
                            i
                    }
                    if (_.qsa && (!O || !O.test(e))) {
                        if (p = h = L, f = t, m = 9 === r && e, 1 === r && "object" !== t.nodeName.toLowerCase()) {
                            for (c = T(e), (h = t.getAttribute("id")) ? p = h.replace(we, "\\$&") : t.setAttribute("id", p), p = "[id='" + p + "'] ", l = c.length; l--;) c[l] = p + u(c[l]);
                            f = be.test(e) && d(t.parentNode) || t,
                                m = c.join(",")
                        }
                        if (m) try {
                            return Z.apply(i, f.querySelectorAll(m)),
                                i
                        } catch (g) {
                        } finally {
                            h || t.removeAttribute("id")
                        }
                    }
                }
                return D(e.replace(le, "$1"), t, i, n)
            }

            function i() {
                function e(i, n) {
                    return t.push(i + " ") > x.cacheLength && delete e[t.shift()],
                        e[i + " "] = n
                }

                var t = [];
                return e
            }

            function n(e) {
                return e[L] = !0,
                    e
            }

            function s(e) {
                var t = j.createElement("div");
                try {
                    return !!e(t)
                } catch (i) {
                    return !1
                } finally {
                    t.parentNode && t.parentNode.removeChild(t),
                        t = null
                }
            }

            function a(e, t) {
                for (var i = e.split("|"), n = e.length; n--;) x.attrHandle[i[n]] = t
            }

            function o(e, t) {
                var i = t && e,
                    n = i && 1 === e.nodeType && 1 === t.nodeType && (~t.sourceIndex || G) - (~e.sourceIndex || G);
                if (n) return n;
                if (i) for (; i = i.nextSibling;) if (i === t) return -1;
                return e ? 1 : -1
            }

            function r(e) {
                return function (t) {
                    var i = t.nodeName.toLowerCase();
                    return "input" === i && t.type === e
                }
            }

            function l(e) {
                return function (t) {
                    var i = t.nodeName.toLowerCase();
                    return ("input" === i || "button" === i) && t.type === e
                }
            }

            function c(e) {
                return n(function (t) {
                    return t = +t,
                        n(function (i, n) {
                            for (var s, a = e([], i.length, t), o = a.length; o--;) i[s = a[o]] && (i[s] = !(n[s] = i[s]))
                        })
                })
            }

            function d(e) {
                return e && typeof e.getElementsByTagName !== U && e
            }

            function h() {
            }

            function u(e) {
                for (var t = 0, i = e.length, n = ""; i > t; t++) n += e[t].value;
                return n
            }

            function p(e, t, i) {
                var n = t.dir,
                    s = i && "parentNode" === n,
                    a = F++;
                return t.first ?
                    function (t, i, a) {
                        for (; t = t[n];) if (1 === t.nodeType || s) return e(t, i, a)
                    } : function (t, i, o) {
                    var r, l, c = [R, a];
                    if (o) {
                        for (; t = t[n];) if ((1 === t.nodeType || s) && e(t, i, o)) return !0
                    } else for (; t = t[n];) if (1 === t.nodeType || s) {
                        if (l = t[L] || (t[L] = {}), (r = l[n]) && r[0] === R && r[1] === a) return c[2] = r[2];
                        if (l[n] = c, c[2] = e(t, i, o)) return !0
                    }
                }
            }

            function f(e) {
                return e.length > 1 ?
                    function (t, i, n) {
                        for (var s = e.length; s--;) if (!e[s](t, i, n)) return !1;
                        return !0
                    } : e[0]
            }

            function m(e, i, n) {
                for (var s = 0, a = i.length; a > s; s++) t(e, i[s], n);
                return n
            }

            function g(e, t, i, n, s) {
                for (var a, o = [], r = 0, l = e.length, c = null != t; l > r; r++)(a = e[r]) && (!i || i(a, n, s)) && (o.push(a), c && t.push(r));
                return o
            }

            function v(e, t, i, s, a, o) {
                return s && !s[L] && (s = v(s)),
                a && !a[L] && (a = v(a, o)),
                    n(function (n, o, r, l) {
                        var c, d, h, u = [],
                            p = [],
                            f = o.length,
                            v = n || m(t || "*", r.nodeType ? [r] : r, []),
                            y = !e || !n && t ? v : g(v, u, e, r, l),
                            b = i ? a || (n ? e : f || s) ? [] : o : y;
                        if (i && i(y, b, r, l), s) for (c = g(b, p), s(c, [], r, l), d = c.length; d--;)(h = c[d]) && (b[p[d]] = !(y[p[d]] = h));
                        if (n) {
                            if (a || e) {
                                if (a) {
                                    for (c = [], d = b.length; d--;)(h = b[d]) && c.push(y[d] = h);
                                    a(null, b = [], c, l)
                                }
                                for (d = b.length; d--;)(h = b[d]) && (c = a ? te.call(n, h) : u[d]) > -1 && (n[c] = !(o[c] = h))
                            }
                        } else b = g(b === o ? b.splice(f, b.length) : b),
                            a ? a(null, o, b, l) : Z.apply(o, b)
                    })
            }

            function y(e) {
                for (var t, i, n, s = e.length, a = x.relative[e[0].type], o = a || x.relative[" "], r = a ? 1 : 0, l = p(function (e) {
                    return e === t
                }, o, !0), c = p(function (e) {
                    return te.call(t, e) > -1
                }, o, !0), d = [function (e, i, n) {
                    return !a && (n || i !== E) || ((t = i).nodeType ? l(e, i, n) : c(e, i, n))
                }]; s > r; r++) if (i = x.relative[e[r].type]) d = [p(f(d), i)];
                else {
                    if (i = x.filter[e[r].type].apply(null, e[r].matches), i[L]) {
                        for (n = ++r; s > n && !x.relative[e[n].type]; n++);
                        return v(r > 1 && f(d), r > 1 && u(e.slice(0, r - 1).concat({
                                value: " " === e[r - 2].type ? "*" : ""
                            })).replace(le, "$1"), i, n > r && y(e.slice(r, n)), s > n && y(e = e.slice(n)), s > n && u(e))
                    }
                    d.push(i)
                }
                return f(d)
            }

            function b(e, i) {
                var s = i.length > 0,
                    a = e.length > 0,
                    o = function (n, o, r, l, c) {
                        var d, h, u, p = 0,
                            f = "0",
                            m = n && [],
                            v = [],
                            y = E,
                            b = n || a && x.find.TAG("*", c),
                            w = R += null == y ? 1 : Math.random() || .1,
                            _ = b.length;
                        for (c && (E = o !== j && o); f !== _ && null != (d = b[f]); f++) {
                            if (a && d) {
                                for (h = 0; u = e[h++];) if (u(d, o, r)) {
                                    l.push(d);
                                    break
                                }
                                c && (R = w)
                            }
                            s && ((d = !u && d) && p--, n && m.push(d))
                        }
                        if (p += f, s && f !== p) {
                            for (h = 0; u = i[h++];) u(m, v, o, r);
                            if (n) {
                                if (p > 0) for (; f--;) m[f] || v[f] || (v[f] = Q.call(l));
                                v = g(v)
                            }
                            Z.apply(l, v),
                            c && !n && v.length > 0 && p + i.length > 1 && t.uniqueSort(l)
                        }
                        return c && (R = w, E = y),
                            m
                    };
                return s ? n(o) : o
            }

            var w, _, x, C, k, T, S, D, E, I, P, M, j, N, A, O, z, $, H, L = "sizzle" + -new Date,
                W = e.document,
                R = 0,
                F = 0,
                B = i(),
                q = i(),
                V = i(),
                Y = function (e, t) {
                    return e === t && (P = !0),
                        0
                },
                U = "undefined",
                G = 1 << 31,
                X = {}.hasOwnProperty,
                K = [],
                Q = K.pop,
                J = K.push,
                Z = K.push,
                ee = K.slice,
                te = K.indexOf ||
                    function (e) {
                        for (var t = 0, i = this.length; i > t; t++) if (this[t] === e) return t;
                        return -1
                    },
                ie = "checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",
                ne = "[\\x20\\t\\r\\n\\f]",
                se = "(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+",
                ae = se.replace("w", "w#"),
                oe = "\\[" + ne + "*(" + se + ")(?:" + ne + "*([*^$|!~]?=)" + ne + "*(?:'((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\"|(" + ae + "))|)" + ne + "*\\]",
                re = ":(" + se + ")(?:\\((('((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\")|((?:\\\\.|[^\\\\()[\\]]|" + oe + ")*)|.*)\\)|)",
                le = new RegExp("^" + ne + "+|((?:^|[^\\\\])(?:\\\\.)*)" + ne + "+$", "g"),
                ce = new RegExp("^" + ne + "*," + ne + "*"),
                de = new RegExp("^" + ne + "*([>+~]|" + ne + ")" + ne + "*"),
                he = new RegExp("=" + ne + "*([^\\]'\"]*?)" + ne + "*\\]", "g"),
                ue = new RegExp(re),
                pe = new RegExp("^" + ae + "$"),
                fe = {
                    ID: new RegExp("^#(" + se + ")"),
                    CLASS: new RegExp("^\\.(" + se + ")"),
                    TAG: new RegExp("^(" + se.replace("w", "w*") + ")"),
                    ATTR: new RegExp("^" + oe),
                    PSEUDO: new RegExp("^" + re),
                    CHILD: new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(" + ne + "*(even|odd|(([+-]|)(\\d*)n|)" + ne + "*(?:([+-]|)" + ne + "*(\\d+)|))" + ne + "*\\)|)", "i"),
                    bool: new RegExp("^(?:" + ie + ")$", "i"),
                    needsContext: new RegExp("^" + ne + "*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(" + ne + "*((?:-\\d)?\\d*)" + ne + "*\\)|)(?=[^-]|$)", "i")
                },
                me = /^(?:input|select|textarea|button)$/i,
                ge = /^h\d$/i,
                ve = /^[^{]+\{\s*\[native \w/,
                ye = /^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,
                be = /[+~]/,
                we = /'|\\/g,
                _e = new RegExp("\\\\([\\da-f]{1,6}" + ne + "?|(" + ne + ")|.)", "ig"),
                xe = function (e, t, i) {
                    var n = "0x" + t - 65536;
                    return n !== n || i ? t : 0 > n ? String.fromCharCode(n + 65536) : String.fromCharCode(n >> 10 | 55296, 1023 & n | 56320)
                };
            try {
                Z.apply(K = ee.call(W.childNodes), W.childNodes),
                    K[W.childNodes.length].nodeType
            } catch (Ce) {
                Z = {
                    apply: K.length ?
                        function (e, t) {
                            J.apply(e, ee.call(t))
                        } : function (e, t) {
                        for (var i = e.length, n = 0; e[i++] = t[n++];);
                        e.length = i - 1
                    }
                }
            }
            _ = t.support = {},
                k = t.isXML = function (e) {
                    var t = e && (e.ownerDocument || e).documentElement;
                    return !!t && "HTML" !== t.nodeName
                },
                M = t.setDocument = function (e) {
                    var t, i = e ? e.ownerDocument || e : W,
                        n = i.defaultView;
                    return i !== j && 9 === i.nodeType && i.documentElement ? (j = i, N = i.documentElement, A = !k(i), n && n !== n.top && (n.addEventListener ? n.addEventListener("unload", function () {
                        M()
                    }, !1) : n.attachEvent && n.attachEvent("onunload", function () {
                        M()
                    })), _.attributes = s(function (e) {
                        return e.className = "i",
                            !e.getAttribute("className")
                    }), _.getElementsByTagName = s(function (e) {
                        return e.appendChild(i.createComment("")),
                            !e.getElementsByTagName("*").length
                    }), _.getElementsByClassName = ve.test(i.getElementsByClassName) && s(function (e) {
                            return e.innerHTML = "<div class='a'></div><div class='a i'></div>",
                                e.firstChild.className = "i",
                            2 === e.getElementsByClassName("i").length
                        }), _.getById = s(function (e) {
                        return N.appendChild(e).id = L,
                        !i.getElementsByName || !i.getElementsByName(L).length
                    }), _.getById ? (x.find.ID = function (e, t) {
                        if (typeof t.getElementById !== U && A) {
                            var i = t.getElementById(e);
                            return i && i.parentNode ? [i] : []
                        }
                    }, x.filter.ID = function (e) {
                        var t = e.replace(_e, xe);
                        return function (e) {
                            return e.getAttribute("id") === t
                        }
                    }) : (delete x.find.ID, x.filter.ID = function (e) {
                        var t = e.replace(_e, xe);
                        return function (e) {
                            var i = typeof e.getAttributeNode !== U && e.getAttributeNode("id");
                            return i && i.value === t
                        }
                    }), x.find.TAG = _.getElementsByTagName ?
                        function (e, t) {
                            return typeof t.getElementsByTagName !== U ? t.getElementsByTagName(e) : void 0
                        } : function (e, t) {
                        var i, n = [],
                            s = 0,
                            a = t.getElementsByTagName(e);
                        if ("*" === e) {
                            for (; i = a[s++];) 1 === i.nodeType && n.push(i);
                            return n
                        }
                        return a
                    }, x.find.CLASS = _.getElementsByClassName &&
                        function (e, t) {
                            return typeof t.getElementsByClassName !== U && A ? t.getElementsByClassName(e) : void 0
                        }, z = [], O = [], (_.qsa = ve.test(i.querySelectorAll)) && (s(function (e) {
                        e.innerHTML = "<select msallowclip=''><option selected=''></option></select>",
                        e.querySelectorAll("[msallowclip^='']").length && O.push("[*^$]=" + ne + "*(?:''|\"\")"),
                        e.querySelectorAll("[selected]").length || O.push("\\[" + ne + "*(?:value|" + ie + ")"),
                        e.querySelectorAll(":checked").length || O.push(":checked")
                    }), s(function (e) {
                        var t = i.createElement("input");
                        t.setAttribute("type", "hidden"),
                            e.appendChild(t).setAttribute("name", "D"),
                        e.querySelectorAll("[name=d]").length && O.push("name" + ne + "*[*^$|!~]?="),
                        e.querySelectorAll(":enabled").length || O.push(":enabled", ":disabled"),
                            e.querySelectorAll("*,:x"),
                            O.push(",.*:")
                    })), (_.matchesSelector = ve.test($ = N.matches || N.webkitMatchesSelector || N.mozMatchesSelector || N.oMatchesSelector || N.msMatchesSelector)) && s(function (e) {
                        _.disconnectedMatch = $.call(e, "div"),
                            $.call(e, "[s!='']:x"),
                            z.push("!=", re)
                    }), O = O.length && new RegExp(O.join("|")), z = z.length && new RegExp(z.join("|")), t = ve.test(N.compareDocumentPosition), H = t || ve.test(N.contains) ?
                        function (e, t) {
                            var i = 9 === e.nodeType ? e.documentElement : e,
                                n = t && t.parentNode;
                            return e === n || !(!n || 1 !== n.nodeType || !(i.contains ? i.contains(n) : e.compareDocumentPosition && 16 & e.compareDocumentPosition(n)))
                        } : function (e, t) {
                        if (t) for (; t = t.parentNode;) if (t === e) return !0;
                        return !1
                    }, Y = t ?
                        function (e, t) {
                            if (e === t) return P = !0,
                                0;
                            var n = !e.compareDocumentPosition - !t.compareDocumentPosition;
                            return n ? n : (n = (e.ownerDocument || e) === (t.ownerDocument || t) ? e.compareDocumentPosition(t) : 1, 1 & n || !_.sortDetached && t.compareDocumentPosition(e) === n ? e === i || e.ownerDocument === W && H(W, e) ? -1 : t === i || t.ownerDocument === W && H(W, t) ? 1 : I ? te.call(I, e) - te.call(I, t) : 0 : 4 & n ? -1 : 1)
                        } : function (e, t) {
                        if (e === t) return P = !0,
                            0;
                        var n, s = 0,
                            a = e.parentNode,
                            r = t.parentNode,
                            l = [e],
                            c = [t];
                        if (!a || !r) return e === i ? -1 : t === i ? 1 : a ? -1 : r ? 1 : I ? te.call(I, e) - te.call(I, t) : 0;
                        if (a === r) return o(e, t);
                        for (n = e; n = n.parentNode;) l.unshift(n);
                        for (n = t; n = n.parentNode;) c.unshift(n);
                        for (; l[s] === c[s];) s++;
                        return s ? o(l[s], c[s]) : l[s] === W ? -1 : c[s] === W ? 1 : 0
                    }, i) : j
                },
                t.matches = function (e, i) {
                    return t(e, null, null, i)
                },
                t.matchesSelector = function (e, i) {
                    if ((e.ownerDocument || e) !== j && M(e), i = i.replace(he, "='$1']"), !(!_.matchesSelector || !A || z && z.test(i) || O && O.test(i))) try {
                        var n = $.call(e, i);
                        if (n || _.disconnectedMatch || e.document && 11 !== e.document.nodeType) return n
                    } catch (s) {
                    }
                    return t(i, j, null, [e]).length > 0
                },
                t.contains = function (e, t) {
                    return (e.ownerDocument || e) !== j && M(e),
                        H(e, t)
                },
                t.attr = function (e, t) {
                    (e.ownerDocument || e) !== j && M(e);
                    var i = x.attrHandle[t.toLowerCase()],
                        n = i && X.call(x.attrHandle, t.toLowerCase()) ? i(e, t, !A) : void 0;
                    return void 0 !== n ? n : _.attributes || !A ? e.getAttribute(t) : (n = e.getAttributeNode(t)) && n.specified ? n.value : null
                },
                t.error = function (e) {
                    throw new Error("Syntax error, unrecognized expression: " + e)
                },
                t.uniqueSort = function (e) {
                    var t, i = [],
                        n = 0,
                        s = 0;
                    if (P = !_.detectDuplicates, I = !_.sortStable && e.slice(0), e.sort(Y), P) {
                        for (; t = e[s++];) t === e[s] && (n = i.push(s));
                        for (; n--;) e.splice(i[n], 1)
                    }
                    return I = null,
                        e
                },
                C = t.getText = function (e) {
                    var t, i = "",
                        n = 0,
                        s = e.nodeType;
                    if (s) {
                        if (1 === s || 9 === s || 11 === s) {
                            if ("string" == typeof e.textContent) return e.textContent;
                            for (e = e.firstChild; e; e = e.nextSibling) i += C(e)
                        } else if (3 === s || 4 === s) return e.nodeValue
                    } else for (; t = e[n++];) i += C(t);
                    return i
                },
                x = t.selectors = {
                    cacheLength: 50,
                    createPseudo: n,
                    match: fe,
                    attrHandle: {},
                    find: {},
                    relative: {
                        ">": {
                            dir: "parentNode",
                            first: !0
                        },
                        " ": {
                            dir: "parentNode"
                        },
                        "+": {
                            dir: "previousSibling",
                            first: !0
                        },
                        "~": {
                            dir: "previousSibling"
                        }
                    },
                    preFilter: {
                        ATTR: function (e) {
                            return e[1] = e[1].replace(_e, xe),
                                e[3] = (e[3] || e[4] || e[5] || "").replace(_e, xe),
                            "~=" === e[2] && (e[3] = " " + e[3] + " "),
                                e.slice(0, 4)
                        },
                        CHILD: function (e) {
                            return e[1] = e[1].toLowerCase(),
                                "nth" === e[1].slice(0, 3) ? (e[3] || t.error(e[0]), e[4] = +(e[4] ? e[5] + (e[6] || 1) : 2 * ("even" === e[3] || "odd" === e[3])), e[5] = +(e[7] + e[8] || "odd" === e[3])) : e[3] && t.error(e[0]),
                                e
                        },
                        PSEUDO: function (e) {
                            var t, i = !e[6] && e[2];
                            return fe.CHILD.test(e[0]) ? null : (e[3] ? e[2] = e[4] || e[5] || "" : i && ue.test(i) && (t = T(i, !0)) && (t = i.indexOf(")", i.length - t) - i.length) && (e[0] = e[0].slice(0, t), e[2] = i.slice(0, t)), e.slice(0, 3))
                        }
                    },
                    filter: {
                        TAG: function (e) {
                            var t = e.replace(_e, xe).toLowerCase();
                            return "*" === e ?
                                function () {
                                    return !0
                                } : function (e) {
                                return e.nodeName && e.nodeName.toLowerCase() === t
                            }
                        },
                        CLASS: function (e) {
                            var t = B[e + " "];
                            return t || (t = new RegExp("(^|" + ne + ")" + e + "(" + ne + "|$)")) && B(e, function (e) {
                                    return t.test("string" == typeof e.className && e.className || typeof e.getAttribute !== U && e.getAttribute("class") || "")
                                })
                        },
                        ATTR: function (e, i, n) {
                            return function (s) {
                                var a = t.attr(s, e);
                                return null == a ? "!=" === i : !i || (a += "", "=" === i ? a === n : "!=" === i ? a !== n : "^=" === i ? n && 0 === a.indexOf(n) : "*=" === i ? n && a.indexOf(n) > -1 : "$=" === i ? n && a.slice(-n.length) === n : "~=" === i ? (" " + a + " ").indexOf(n) > -1 : "|=" === i && (a === n || a.slice(0, n.length + 1) === n + "-"))
                            }
                        },
                        CHILD: function (e, t, i, n, s) {
                            var a = "nth" !== e.slice(0, 3),
                                o = "last" !== e.slice(-4),
                                r = "of-type" === t;
                            return 1 === n && 0 === s ?
                                function (e) {
                                    return !!e.parentNode
                                } : function (t, i, l) {
                                var c, d, h, u, p, f, m = a !== o ? "nextSibling" : "previousSibling",
                                    g = t.parentNode,
                                    v = r && t.nodeName.toLowerCase(),
                                    y = !l && !r;
                                if (g) {
                                    if (a) {
                                        for (; m;) {
                                            for (h = t; h = h[m];) if (r ? h.nodeName.toLowerCase() === v : 1 === h.nodeType) return !1;
                                            f = m = "only" === e && !f && "nextSibling"
                                        }
                                        return !0
                                    }
                                    if (f = [o ? g.firstChild : g.lastChild], o && y) {
                                        for (d = g[L] || (g[L] = {}), c = d[e] || [], p = c[0] === R && c[1], u = c[0] === R && c[2], h = p && g.childNodes[p]; h = ++p && h && h[m] || (u = p = 0) || f.pop();) if (1 === h.nodeType && ++u && h === t) {
                                            d[e] = [R, p, u];
                                            break
                                        }
                                    } else if (y && (c = (t[L] || (t[L] = {}))[e]) && c[0] === R) u = c[1];
                                    else for (;
                                            (h = ++p && h && h[m] || (u = p = 0) || f.pop()) && ((r ? h.nodeName.toLowerCase() !== v : 1 !== h.nodeType) || !++u || (y && ((h[L] || (h[L] = {}))[e] = [R, u]), h !== t)););
                                    return u -= s,
                                    u === n || u % n === 0 && u / n >= 0
                                }
                            }
                        },
                        PSEUDO: function (e, i) {
                            var s, a = x.pseudos[e] || x.setFilters[e.toLowerCase()] || t.error("unsupported pseudo: " + e);
                            return a[L] ? a(i) : a.length > 1 ? (s = [e, e, "", i], x.setFilters.hasOwnProperty(e.toLowerCase()) ? n(function (e, t) {
                                for (var n, s = a(e, i), o = s.length; o--;) n = te.call(e, s[o]),
                                    e[n] = !(t[n] = s[o])
                            }) : function (e) {
                                return a(e, 0, s)
                            }) : a
                        }
                    },
                    pseudos: {
                        not: n(function (e) {
                            var t = [],
                                i = [],
                                s = S(e.replace(le, "$1"));
                            return s[L] ? n(function (e, t, i, n) {
                                for (var a, o = s(e, null, n, []), r = e.length; r--;)(a = o[r]) && (e[r] = !(t[r] = a))
                            }) : function (e, n, a) {
                                return t[0] = e,
                                    s(t, null, a, i),
                                    !i.pop()
                            }
                        }),
                        has: n(function (e) {
                            return function (i) {
                                return t(e, i).length > 0
                            }
                        }),
                        contains: n(function (e) {
                            return function (t) {
                                return (t.textContent || t.innerText || C(t)).indexOf(e) > -1
                            }
                        }),
                        lang: n(function (e) {
                            return pe.test(e || "") || t.error("unsupported lang: " + e),
                                e = e.replace(_e, xe).toLowerCase(),


                                function (t) {
                                    var i;
                                    do
                                        if (i = A ? t.lang : t.getAttribute("xml:lang") || t.getAttribute("lang")) return i = i.toLowerCase(),
                                        i === e || 0 === i.indexOf(e + "-");
                                    while ((t = t.parentNode) && 1 === t.nodeType);
                                    return !1
                                }
                        }),
                        target: function (t) {
                            var i = e.location && e.location.hash;
                            return i && i.slice(1) === t.id
                        },
                        root: function (e) {
                            return e === N
                        },
                        focus: function (e) {
                            return e === j.activeElement && (!j.hasFocus || j.hasFocus()) && !!(e.type || e.href || ~e.tabIndex)
                        },
                        enabled: function (e) {
                            return e.disabled === !1
                        },
                        disabled: function (e) {
                            return e.disabled === !0
                        },
                        checked: function (e) {
                            var t = e.nodeName.toLowerCase();
                            return "input" === t && !!e.checked || "option" === t && !!e.selected
                        },
                        selected: function (e) {
                            return e.parentNode && e.parentNode.selectedIndex,
                            e.selected === !0
                        },
                        empty: function (e) {
                            for (e = e.firstChild; e; e = e.nextSibling) if (e.nodeType < 6) return !1;
                            return !0
                        },
                        parent: function (e) {
                            return !x.pseudos.empty(e)
                        },
                        header: function (e) {
                            return ge.test(e.nodeName)
                        },
                        input: function (e) {
                            return me.test(e.nodeName)
                        },
                        button: function (e) {
                            var t = e.nodeName.toLowerCase();
                            return "input" === t && "button" === e.type || "button" === t
                        },
                        text: function (e) {
                            var t;
                            return "input" === e.nodeName.toLowerCase() && "text" === e.type && (null == (t = e.getAttribute("type")) || "text" === t.toLowerCase())
                        },
                        first: c(function () {
                            return [0]
                        }),
                        last: c(function (e, t) {
                            return [t - 1]
                        }),
                        eq: c(function (e, t, i) {
                            return [0 > i ? i + t : i]
                        }),
                        even: c(function (e, t) {
                            for (var i = 0; t > i; i += 2) e.push(i);
                            return e
                        }),
                        odd: c(function (e, t) {
                            for (var i = 1; t > i; i += 2) e.push(i);
                            return e
                        }),
                        lt: c(function (e, t, i) {
                            for (var n = 0 > i ? i + t : i; --n >= 0;) e.push(n);
                            return e
                        }),
                        gt: c(function (e, t, i) {
                            for (var n = 0 > i ? i + t : i; ++n < t;) e.push(n);
                            return e
                        })
                    }
                },
                x.pseudos.nth = x.pseudos.eq;
            for (w in {
                radio: !0,
                checkbox: !0,
                file: !0,
                password: !0,
                image: !0
            }) x.pseudos[w] = r(w);
            for (w in {
                submit: !0,
                reset: !0
            }) x.pseudos[w] = l(w);
            return h.prototype = x.filters = x.pseudos,
                x.setFilters = new h,
                T = t.tokenize = function (e, i) {
                    var n, s, a, o, r, l, c, d = q[e + " "];
                    if (d) return i ? 0 : d.slice(0);
                    for (r = e, l = [], c = x.preFilter; r;) {
                        (!n || (s = ce.exec(r))) && (s && (r = r.slice(s[0].length) || r), l.push(a = [])),
                            n = !1,
                        (s = de.exec(r)) && (n = s.shift(), a.push({
                            value: n,
                            type: s[0].replace(le, " ")
                        }), r = r.slice(n.length));
                        for (o in x.filter)!(s = fe[o].exec(r)) || c[o] && !(s = c[o](s)) || (n = s.shift(), a.push({
                            value: n,
                            type: o,
                            matches: s
                        }), r = r.slice(n.length));
                        if (!n) break
                    }
                    return i ? r.length : r ? t.error(e) : q(e, l).slice(0)
                },
                S = t.compile = function (e, t) {
                    var i, n = [],
                        s = [],
                        a = V[e + " "];
                    if (!a) {
                        for (t || (t = T(e)), i = t.length; i--;) a = y(t[i]),
                            a[L] ? n.push(a) : s.push(a);
                        a = V(e, b(s, n)),
                            a.selector = e
                    }
                    return a
                },
                D = t.select = function (e, t, i, n) {
                    var s, a, o, r, l, c = "function" == typeof e && e,
                        h = !n && T(e = c.selector || e);
                    if (i = i || [], 1 === h.length) {
                        if (a = h[0] = h[0].slice(0), a.length > 2 && "ID" === (o = a[0]).type && _.getById && 9 === t.nodeType && A && x.relative[a[1].type]) {
                            if (t = (x.find.ID(o.matches[0].replace(_e, xe), t) || [])[0], !t) return i;
                            c && (t = t.parentNode),
                                e = e.slice(a.shift().value.length)
                        }
                        for (s = fe.needsContext.test(e) ? 0 : a.length; s-- && (o = a[s], !x.relative[r = o.type]);) if ((l = x.find[r]) && (n = l(o.matches[0].replace(_e, xe), be.test(a[0].type) && d(t.parentNode) || t))) {
                            if (a.splice(s, 1), e = n.length && u(a), !e) return Z.apply(i, n),
                                i;
                            break
                        }
                    }
                    return (c || S(e, h))(n, t, !A, i, be.test(e) && d(t.parentNode) || t),
                        i
                },
                _.sortStable = L.split("").sort(Y).join("") === L,
                _.detectDuplicates = !!P,
                M(),
                _.sortDetached = s(function (e) {
                    return 1 & e.compareDocumentPosition(j.createElement("div"))
                }),
            s(function (e) {
                return e.innerHTML = "<a href='#'></a>",
                "#" === e.firstChild.getAttribute("href")
            }) || a("type|href|height|width", function (e, t, i) {
                return i ? void 0 : e.getAttribute(t, "type" === t.toLowerCase() ? 1 : 2)
            }),
            _.attributes && s(function (e) {
                return e.innerHTML = "<input/>",
                    e.firstChild.setAttribute("value", ""),
                "" === e.firstChild.getAttribute("value")
            }) || a("value", function (e, t, i) {
                return i || "input" !== e.nodeName.toLowerCase() ? void 0 : e.defaultValue
            }),
            s(function (e) {
                return null == e.getAttribute("disabled")
            }) || a(ie, function (e, t, i) {
                var n;
                return i ? void 0 : e[t] === !0 ? t.toLowerCase() : (n = e.getAttributeNode(t)) && n.specified ? n.value : null
            }),
                t
        }(e);
        se.find = ce,
            se.expr = ce.selectors,
            se.expr[":"] = se.expr.pseudos,
            se.unique = ce.uniqueSort,
            se.text = ce.getText,
            se.isXMLDoc = ce.isXML,
            se.contains = ce.contains;
        var de = se.expr.match.needsContext,
            he = /^<(\w+)\s*\/?>(?:<\/\1>|)$/,
            ue = /^.[^:#\[\.,]*$/;
        se.filter = function (e, t, i) {
            var n = t[0];
            return i && (e = ":not(" + e + ")"),
                1 === t.length && 1 === n.nodeType ? se.find.matchesSelector(n, e) ? [n] : [] : se.find.matches(e, se.grep(t, function (e) {
                    return 1 === e.nodeType
                }))
        },
            se.fn.extend({
                find: function (e) {
                    var t, i = [],
                        n = this,
                        s = n.length;
                    if ("string" != typeof e) return this.pushStack(se(e).filter(function () {
                        for (t = 0; s > t; t++) if (se.contains(n[t], this)) return !0
                    }));
                    for (t = 0; s > t; t++) se.find(e, n[t], i);
                    return i = this.pushStack(s > 1 ? se.unique(i) : i),
                        i.selector = this.selector ? this.selector + " " + e : e,
                        i
                },
                filter: function (e) {
                    return this.pushStack(n(this, e || [], !1))
                },
                not: function (e) {
                    return this.pushStack(n(this, e || [], !0))
                },
                is: function (e) {
                    return !!n(this, "string" == typeof e && de.test(e) ? se(e) : e || [], !1).length
                }
            });
        var pe, fe = e.document,
            me = /^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]*))$/,
            ge = se.fn.init = function (e, t) {
                var i, n;
                if (!e) return this;
                if ("string" == typeof e) {
                    if (i = "<" === e.charAt(0) && ">" === e.charAt(e.length - 1) && e.length >= 3 ? [null, e, null] : me.exec(e), !i || !i[1] && t) return !t || t.jquery ? (t || pe).find(e) : this.constructor(t).find(e);
                    if (i[1]) {
                        if (t = t instanceof se ? t[0] : t, se.merge(this, se.parseHTML(i[1], t && t.nodeType ? t.ownerDocument || t : fe, !0)), he.test(i[1]) && se.isPlainObject(t)) for (i in t) se.isFunction(this[i]) ? this[i](t[i]) : this.attr(i, t[i]);
                        return this
                    }
                    if (n = fe.getElementById(i[2]), n && n.parentNode) {
                        if (n.id !== i[2]) return pe.find(e);
                        this.length = 1,
                            this[0] = n
                    }
                    return this.context = fe,
                        this.selector = e,
                        this
                }
                return e.nodeType ? (this.context = this[0] = e, this.length = 1, this) : se.isFunction(e) ? "undefined" != typeof pe.ready ? pe.ready(e) : e(se) : (void 0 !== e.selector && (this.selector = e.selector, this.context = e.context), se.makeArray(e, this))
            };
        ge.prototype = se.fn,
            pe = se(fe);
        var ve = /^(?:parents|prev(?:Until|All))/,
            ye = {
                children: !0,
                contents: !0,
                next: !0,
                prev: !0
            };
        se.extend({
            dir: function (e, t, i) {
                for (var n = [], s = e[t]; s && 9 !== s.nodeType && (void 0 === i || 1 !== s.nodeType || !se(s).is(i));) 1 === s.nodeType && n.push(s),
                    s = s[t];
                return n
            },
            sibling: function (e, t) {
                for (var i = []; e; e = e.nextSibling) 1 === e.nodeType && e !== t && i.push(e);
                return i
            }
        }),
            se.fn.extend({
                has: function (e) {
                    var t, i = se(e, this),
                        n = i.length;
                    return this.filter(function () {
                        for (t = 0; n > t; t++) if (se.contains(this, i[t])) return !0
                    })
                },
                closest: function (e, t) {
                    for (var i, n = 0, s = this.length, a = [], o = de.test(e) || "string" != typeof e ? se(e, t || this.context) : 0; s > n; n++) for (i = this[n]; i && i !== t; i = i.parentNode) if (i.nodeType < 11 && (o ? o.index(i) > -1 : 1 === i.nodeType && se.find.matchesSelector(i, e))) {
                        a.push(i);
                        break
                    }
                    return this.pushStack(a.length > 1 ? se.unique(a) : a)
                },
                index: function (e) {
                    return e ? "string" == typeof e ? se.inArray(this[0], se(e)) : se.inArray(e.jquery ? e[0] : e, this) : this[0] && this[0].parentNode ? this.first().prevAll().length : -1
                },
                add: function (e, t) {
                    return this.pushStack(se.unique(se.merge(this.get(), se(e, t))))
                },
                addBack: function (e) {
                    return this.add(null == e ? this.prevObject : this.prevObject.filter(e))
                }
            }),
            se.each({
                parent: function (e) {
                    var t = e.parentNode;
                    return t && 11 !== t.nodeType ? t : null
                },
                parents: function (e) {
                    return se.dir(e, "parentNode")
                },
                parentsUntil: function (e, t, i) {
                    return se.dir(e, "parentNode", i)
                },
                next: function (e) {
                    return s(e, "nextSibling")
                },
                prev: function (e) {
                    return s(e, "previousSibling")
                },
                nextAll: function (e) {
                    return se.dir(e, "nextSibling")
                },
                prevAll: function (e) {
                    return se.dir(e, "previousSibling")
                },
                nextUntil: function (e, t, i) {
                    return se.dir(e, "nextSibling", i)
                },
                prevUntil: function (e, t, i) {
                    return se.dir(e, "previousSibling", i)
                },
                siblings: function (e) {
                    return se.sibling((e.parentNode || {}).firstChild, e)
                },
                children: function (e) {
                    return se.sibling(e.firstChild)
                },
                contents: function (e) {
                    return se.nodeName(e, "iframe") ? e.contentDocument || e.contentWindow.document : se.merge([], e.childNodes)
                }
            }, function (e, t) {
                se.fn[e] = function (i, n) {
                    var s = se.map(this, t, i);
                    return "Until" !== e.slice(-5) && (n = i),
                    n && "string" == typeof n && (s = se.filter(n, s)),
                    this.length > 1 && (ye[e] || (s = se.unique(s)), ve.test(e) && (s = s.reverse())),
                        this.pushStack(s)
                }
            });
        var be = /\S+/g,
            we = {};
        se.Callbacks = function (e) {
            e = "string" == typeof e ? we[e] || a(e) : se.extend({}, e);
            var t, i, n, s, o, r, l = [],
                c = !e.once && [],
                d = function (a) {
                    for (i = e.memory && a, n = !0, o = r || 0, r = 0, s = l.length, t = !0; l && s > o; o++) if (l[o].apply(a[0], a[1]) === !1 && e.stopOnFalse) {
                        i = !1;
                        break
                    }
                    t = !1,
                    l && (c ? c.length && d(c.shift()) : i ? l = [] : h.disable())
                },
                h = {
                    add: function () {
                        if (l) {
                            var n = l.length;
                            !
                                function a(t) {
                                    se.each(t, function (t, i) {
                                        var n = se.type(i);
                                        "function" === n ? e.unique && h.has(i) || l.push(i) : i && i.length && "string" !== n && a(i)
                                    })
                                }(arguments),
                                t ? s = l.length : i && (r = n, d(i))
                        }
                        return this
                    },
                    remove: function () {
                        return l && se.each(arguments, function (e, i) {
                            for (var n;
                                 (n = se.inArray(i, l, n)) > -1;) l.splice(n, 1),
                            t && (s >= n && s--, o >= n && o--)
                        }),
                            this
                    },
                    has: function (e) {
                        return e ? se.inArray(e, l) > -1 : !(!l || !l.length)
                    },
                    empty: function () {
                        return l = [],
                            s = 0,
                            this
                    },
                    disable: function () {
                        return l = c = i = void 0,
                            this
                    },
                    disabled: function () {
                        return !l
                    },
                    lock: function () {
                        return c = void 0,
                        i || h.disable(),
                            this
                    },
                    locked: function () {
                        return !c
                    },
                    fireWith: function (e, i) {
                        return !l || n && !c || (i = i || [], i = [e, i.slice ? i.slice() : i], t ? c.push(i) : d(i)),
                            this
                    },
                    fire: function () {
                        return h.fireWith(this, arguments),
                            this
                    },
                    fired: function () {
                        return !!n
                    }
                };
            return h
        },
            se.extend({
                Deferred: function (e) {
                    var t = [
                            ["resolve", "done", se.Callbacks("once memory"), "resolved"],
                            ["reject", "fail", se.Callbacks("once memory"), "rejected"],
                            ["notify", "progress", se.Callbacks("memory")]
                        ],
                        i = "pending",
                        n = {
                            state: function () {
                                return i
                            },
                            always: function () {
                                return s.done(arguments).fail(arguments),
                                    this
                            },
                            then: function () {
                                var e = arguments;
                                return se.Deferred(function (i) {
                                    se.each(t, function (t, a) {
                                        var o = se.isFunction(e[t]) && e[t];
                                        s[a[1]](function () {
                                            var e = o && o.apply(this, arguments);
                                            e && se.isFunction(e.promise) ? e.promise().done(i.resolve).fail(i.reject).progress(i.notify) : i[a[0] + "With"](this === n ? i.promise() : this, o ? [e] : arguments)
                                        })
                                    }),
                                        e = null
                                }).promise()
                            },
                            promise: function (e) {
                                return null != e ? se.extend(e, n) : n
                            }
                        },
                        s = {};
                    return n.pipe = n.then,
                        se.each(t, function (e, a) {
                            var o = a[2],
                                r = a[3];
                            n[a[1]] = o.add,
                            r && o.add(function () {
                                i = r
                            }, t[1 ^ e][2].disable, t[2][2].lock),
                                s[a[0]] = function () {
                                    return s[a[0] + "With"](this === s ? n : this, arguments),
                                        this
                                },
                                s[a[0] + "With"] = o.fireWith
                        }),
                        n.promise(s),
                    e && e.call(s, s),
                        s
                },
                when: function (e) {
                    var t, i, n, s = 0,
                        a = X.call(arguments),
                        o = a.length,
                        r = 1 !== o || e && se.isFunction(e.promise) ? o : 0,
                        l = 1 === r ? e : se.Deferred(),
                        c = function (e, i, n) {
                            return function (s) {
                                i[e] = this,
                                    n[e] = arguments.length > 1 ? X.call(arguments) : s,
                                    n === t ? l.notifyWith(i, n) : --r || l.resolveWith(i, n)
                            }
                        };
                    if (o > 1) for (t = new Array(o), i = new Array(o), n = new Array(o); o > s; s++) a[s] && se.isFunction(a[s].promise) ? a[s].promise().done(c(s, n, a)).fail(l.reject).progress(c(s, i, t)) : --r;
                    return r || l.resolveWith(n, a),
                        l.promise()
                }
            });
        var _e;
        se.fn.ready = function (e) {
            return se.ready.promise().done(e),
                this
        },
            se.extend({
                isReady: !1,
                readyWait: 1,
                holdReady: function (e) {
                    e ? se.readyWait++ : se.ready(!0)
                },
                ready: function (e) {
                    if (e === !0 ? !--se.readyWait : !se.isReady) {
                        if (!fe.body) return setTimeout(se.ready);
                        se.isReady = !0,
                        e !== !0 && --se.readyWait > 0 || (_e.resolveWith(fe, [se]), se.fn.triggerHandler && (se(fe).triggerHandler("ready"), se(fe).off("ready")))
                    }
                }
            }),
            se.ready.promise = function (t) {
                if (!_e) if (_e = se.Deferred(), "complete" === fe.readyState) setTimeout(se.ready);
                else if (fe.addEventListener) fe.addEventListener("DOMContentLoaded", r, !1),
                    e.addEventListener("load", r, !1);
                else {
                    fe.attachEvent("onreadystatechange", r),
                        e.attachEvent("onload", r);
                    var i = !1;
                    try {
                        i = null == e.frameElement && fe.documentElement
                    } catch (n) {
                    }
                    i && i.doScroll && !
                        function s() {
                            if (!se.isReady) {
                                try {
                                    i.doScroll("left")
                                } catch (e) {
                                    return setTimeout(s, 50)
                                }
                                o(),
                                    se.ready()
                            }
                        }()
                }
                return _e.promise(t)
            };
        var xe, Ce = "undefined";
        for (xe in se(ie)) break;
        ie.ownLast = "0" !== xe,
            ie.inlineBlockNeedsLayout = !1,
            se(function () {
                var e, t, i, n;
                i = fe.getElementsByTagName("body")[0],
                i && i.style && (t = fe.createElement("div"), n = fe.createElement("div"), n.style.cssText = "position:absolute;border:0;width:0;height:0;top:0;left:-9999px", i.appendChild(n).appendChild(t), typeof t.style.zoom !== Ce && (t.style.cssText = "display:inline;margin:0;border:0;padding:1px;width:1px;zoom:1", ie.inlineBlockNeedsLayout = e = 3 === t.offsetWidth, e && (i.style.zoom = 1)), i.removeChild(n))
            }),


            function () {
                var e = fe.createElement("div");
                if (null == ie.deleteExpando) {
                    ie.deleteExpando = !0;
                    try {
                        delete e.test
                    } catch (t) {
                        ie.deleteExpando = !1
                    }
                }
                e = null
            }(),
            se.acceptData = function (e) {
                var t = se.noData[(e.nodeName + " ").toLowerCase()],
                    i = +e.nodeType || 1;
                return (1 === i || 9 === i) && (!t || t !== !0 && e.getAttribute("classid") === t)
            };
        var ke = /^(?:\{[\w\W]*\}|\[[\w\W]*\])$/,
            Te = /([A-Z])/g;
        se.extend({
            cache: {},
            noData: {
                "applet ": !0,
                "embed ": !0,
                "object ": "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
            },
            hasData: function (e) {
                return e = e.nodeType ? se.cache[e[se.expando]] : e[se.expando],
                !!e && !c(e)
            },
            data: function (e, t, i) {
                return d(e, t, i)
            },
            removeData: function (e, t) {
                return h(e, t)
            },
            _data: function (e, t, i) {
                return d(e, t, i, !0)
            },
            _removeData: function (e, t) {
                return h(e, t, !0)
            }
        }),
            se.fn.extend({
                data: function (e, t) {
                    var i, n, s, a = this[0],
                        o = a && a.attributes;
                    if (void 0 === e) {
                        if (this.length && (s = se.data(a), 1 === a.nodeType && !se._data(a, "parsedAttrs"))) {
                            for (i = o.length; i--;) o[i] && (n = o[i].name, 0 === n.indexOf("data-") && (n = se.camelCase(n.slice(5)), l(a, n, s[n])));
                            se._data(a, "parsedAttrs", !0)
                        }
                        return s
                    }
                    return "object" == typeof e ? this.each(function () {
                        se.data(this, e)
                    }) : arguments.length > 1 ? this.each(function () {
                        se.data(this, e, t)
                    }) : a ? l(a, e, se.data(a, e)) : void 0
                },
                removeData: function (e) {
                    return this.each(function () {
                        se.removeData(this, e)
                    })
                }
            }),
            se.extend({
                queue: function (e, t, i) {
                    var n;
                    return e ? (t = (t || "fx") + "queue", n = se._data(e, t), i && (!n || se.isArray(i) ? n = se._data(e, t, se.makeArray(i)) : n.push(i)), n || []) : void 0
                },
                dequeue: function (e, t) {
                    t = t || "fx";
                    var i = se.queue(e, t),
                        n = i.length,
                        s = i.shift(),
                        a = se._queueHooks(e, t),
                        o = function () {
                            se.dequeue(e, t)
                        };
                    "inprogress" === s && (s = i.shift(), n--),
                    s && ("fx" === t && i.unshift("inprogress"), delete a.stop, s.call(e, o, a)),
                    !n && a && a.empty.fire()
                },
                _queueHooks: function (e, t) {
                    var i = t + "queueHooks";
                    return se._data(e, i) || se._data(e, i, {
                            empty: se.Callbacks("once memory").add(function () {
                                se._removeData(e, t + "queue"),
                                    se._removeData(e, i)
                            })
                        })
                }
            }),
            se.fn.extend({
                queue: function (e, t) {
                    var i = 2;
                    return "string" != typeof e && (t = e, e = "fx", i--),
                        arguments.length < i ? se.queue(this[0], e) : void 0 === t ? this : this.each(function () {
                            var i = se.queue(this, e, t);
                            se._queueHooks(this, e),
                            "fx" === e && "inprogress" !== i[0] && se.dequeue(this, e)
                        })
                },
                dequeue: function (e) {
                    return this.each(function () {
                        se.dequeue(this, e)
                    })
                },
                clearQueue: function (e) {
                    return this.queue(e || "fx", [])
                },
                promise: function (e, t) {
                    var i, n = 1,
                        s = se.Deferred(),
                        a = this,
                        o = this.length,
                        r = function () {
                            --n || s.resolveWith(a, [a])
                        };
                    for ("string" != typeof e && (t = e, e = void 0), e = e || "fx"; o--;) i = se._data(a[o], e + "queueHooks"),
                    i && i.empty && (n++, i.empty.add(r));
                    return r(),
                        s.promise(t)
                }
            });
        var Se = /[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,
            De = ["Top", "Right", "Bottom", "Left"],
            Ee = function (e, t) {
                return e = t || e,
                "none" === se.css(e, "display") || !se.contains(e.ownerDocument, e)
            },
            Ie = se.access = function (e, t, i, n, s, a, o) {
                var r = 0,
                    l = e.length,
                    c = null == i;
                if ("object" === se.type(i)) {
                    s = !0;
                    for (r in i) se.access(e, t, r, i[r], !0, a, o)
                } else if (void 0 !== n && (s = !0, se.isFunction(n) || (o = !0), c && (o ? (t.call(e, n), t = null) : (c = t, t = function (e, t, i) {
                        return c.call(se(e), i)
                    })), t)) for (; l > r; r++) t(e[r], i, o ? n : n.call(e[r], r, t(e[r], i)));
                return s ? e : c ? t.call(e) : l ? t(e[0], i) : a
            },
            Pe = /^(?:checkbox|radio)$/i;
        !
            function () {
                var e = fe.createElement("input"),
                    t = fe.createElement("div"),
                    i = fe.createDocumentFragment();
                if (t.innerHTML = "  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>", ie.leadingWhitespace = 3 === t.firstChild.nodeType, ie.tbody = !t.getElementsByTagName("tbody").length, ie.htmlSerialize = !!t.getElementsByTagName("link").length, ie.html5Clone = "<:nav></:nav>" !== fe.createElement("nav").cloneNode(!0).outerHTML, e.type = "checkbox", e.checked = !0, i.appendChild(e), ie.appendChecked = e.checked, t.innerHTML = "<textarea>x</textarea>", ie.noCloneChecked = !!t.cloneNode(!0).lastChild.defaultValue, i.appendChild(t), t.innerHTML = "<input type='radio' checked='checked' name='t'/>", ie.checkClone = t.cloneNode(!0).cloneNode(!0).lastChild.checked, ie.noCloneEvent = !0, t.attachEvent && (t.attachEvent("onclick", function () {
                        ie.noCloneEvent = !1
                    }), t.cloneNode(!0).click()), null == ie.deleteExpando) {
                    ie.deleteExpando = !0;
                    try {
                        delete t.test
                    } catch (n) {
                        ie.deleteExpando = !1
                    }
                }
            }(),


            function () {
                var t, i, n = fe.createElement("div");
                for (t in {
                    submit: !0,
                    change: !0,
                    focusin: !0
                }) i = "on" + t,
                (ie[t + "Bubbles"] = i in e) || (n.setAttribute(i, "t"), ie[t + "Bubbles"] = n.attributes[i].expando === !1);
                n = null
            }();
        var Me = /^(?:input|select|textarea)$/i,
            je = /^key/,
            Ne = /^(?:mouse|pointer|contextmenu)|click/,
            Ae = /^(?:focusinfocus|focusoutblur)$/,
            Oe = /^([^.]*)(?:\.(.+)|)$/;
        se.event = {
            global: {},
            add: function (e, t, i, n, s) {
                var a, o, r, l, c, d, h, u, p, f, m, g = se._data(e);
                if (g) {
                    for (i.handler && (l = i, i = l.handler, s = l.selector), i.guid || (i.guid = se.guid++), (o = g.events) || (o = g.events = {}), (d = g.handle) || (d = g.handle = function (e) {
                        return typeof se === Ce || e && se.event.triggered === e.type ? void 0 : se.event.dispatch.apply(d.elem, arguments)
                    }, d.elem = e), t = (t || "").match(be) || [""], r = t.length; r--;) a = Oe.exec(t[r]) || [],
                        p = m = a[1],
                        f = (a[2] || "").split(".").sort(),
                    p && (c = se.event.special[p] || {}, p = (s ? c.delegateType : c.bindType) || p, c = se.event.special[p] || {}, h = se.extend({
                        type: p,
                        origType: m,
                        data: n,
                        handler: i,
                        guid: i.guid,
                        selector: s,
                        needsContext: s && se.expr.match.needsContext.test(s),
                        namespace: f.join(".")
                    }, l), (u = o[p]) || (u = o[p] = [], u.delegateCount = 0, c.setup && c.setup.call(e, n, f, d) !== !1 || (e.addEventListener ? e.addEventListener(p, d, !1) : e.attachEvent && e.attachEvent("on" + p, d))), c.add && (c.add.call(e, h), h.handler.guid || (h.handler.guid = i.guid)), s ? u.splice(u.delegateCount++, 0, h) : u.push(h), se.event.global[p] = !0);
                    e = null
                }
            },
            remove: function (e, t, i, n, s) {
                var a, o, r, l, c, d, h, u, p, f, m, g = se.hasData(e) && se._data(e);
                if (g && (d = g.events)) {
                    for (t = (t || "").match(be) || [""], c = t.length; c--;) if (r = Oe.exec(t[c]) || [], p = m = r[1], f = (r[2] || "").split(".").sort(), p) {
                        for (h = se.event.special[p] || {}, p = (n ? h.delegateType : h.bindType) || p, u = d[p] || [], r = r[2] && new RegExp("(^|\\.)" + f.join("\\.(?:.*\\.|)") + "(\\.|$)"), l = a = u.length; a--;) o = u[a],
                        !s && m !== o.origType || i && i.guid !== o.guid || r && !r.test(o.namespace) || n && n !== o.selector && ("**" !== n || !o.selector) || (u.splice(a, 1), o.selector && u.delegateCount--, h.remove && h.remove.call(e, o));
                        l && !u.length && (h.teardown && h.teardown.call(e, f, g.handle) !== !1 || se.removeEvent(e, p, g.handle), delete d[p])
                    } else for (p in d) se.event.remove(e, p + t[c], i, n, !0);
                    se.isEmptyObject(d) && (delete g.handle, se._removeData(e, "events"))
                }
            },
            trigger: function (t, i, n, s) {
                var a, o, r, l, c, d, h, u = [n || fe],
                    p = te.call(t, "type") ? t.type : t,
                    f = te.call(t, "namespace") ? t.namespace.split(".") : [];
                if (r = d = n = n || fe, 3 !== n.nodeType && 8 !== n.nodeType && !Ae.test(p + se.event.triggered) && (p.indexOf(".") >= 0 && (f = p.split("."), p = f.shift(), f.sort()), o = p.indexOf(":") < 0 && "on" + p, t = t[se.expando] ? t : new se.Event(p, "object" == typeof t && t), t.isTrigger = s ? 2 : 3, t.namespace = f.join("."), t.namespace_re = t.namespace ? new RegExp("(^|\\.)" + f.join("\\.(?:.*\\.|)") + "(\\.|$)") : null, t.result = void 0, t.target || (t.target = n), i = null == i ? [t] : se.makeArray(i, [t]), c = se.event.special[p] || {}, s || !c.trigger || c.trigger.apply(n, i) !== !1)) {
                    if (!s && !c.noBubble && !se.isWindow(n)) {
                        for (l = c.delegateType || p, Ae.test(l + p) || (r = r.parentNode); r; r = r.parentNode) u.push(r),
                            d = r;
                        d === (n.ownerDocument || fe) && u.push(d.defaultView || d.parentWindow || e)
                    }
                    for (h = 0;
                         (r = u[h++]) && !t.isPropagationStopped();) t.type = h > 1 ? l : c.bindType || p,
                        a = (se._data(r, "events") || {})[t.type] && se._data(r, "handle"),
                    a && a.apply(r, i),
                        a = o && r[o],
                    a && a.apply && se.acceptData(r) && (t.result = a.apply(r, i), t.result === !1 && t.preventDefault());
                    if (t.type = p, !s && !t.isDefaultPrevented() && (!c._default || c._default.apply(u.pop(), i) === !1) && se.acceptData(n) && o && n[p] && !se.isWindow(n)) {
                        d = n[o],
                        d && (n[o] = null),
                            se.event.triggered = p;
                        try {
                            n[p]()
                        } catch (m) {
                        }
                        se.event.triggered = void 0,
                        d && (n[o] = d)
                    }
                    return t.result
                }
            },
            dispatch: function (e) {
                e = se.event.fix(e);
                var t, i, n, s, a, o = [],
                    r = X.call(arguments),
                    l = (se._data(this, "events") || {})[e.type] || [],
                    c = se.event.special[e.type] || {};
                if (r[0] = e, e.delegateTarget = this, !c.preDispatch || c.preDispatch.call(this, e) !== !1) {
                    for (o = se.event.handlers.call(this, e, l), t = 0;
                         (s = o[t++]) && !e.isPropagationStopped();) for (e.currentTarget = s.elem, a = 0;
                                                                          (n = s.handlers[a++]) && !e.isImmediatePropagationStopped();)(!e.namespace_re || e.namespace_re.test(n.namespace)) && (e.handleObj = n, e.data = n.data, i = ((se.event.special[n.origType] || {}).handle || n.handler).apply(s.elem, r), void 0 !== i && (e.result = i) === !1 && (e.preventDefault(), e.stopPropagation()));
                    return c.postDispatch && c.postDispatch.call(this, e),
                        e.result
                }
            },
            handlers: function (e, t) {
                var i, n, s, a, o = [],
                    r = t.delegateCount,
                    l = e.target;
                if (r && l.nodeType && (!e.button || "click" !== e.type)) for (; l != this; l = l.parentNode || this) if (1 === l.nodeType && (l.disabled !== !0 || "click" !== e.type)) {
                    for (s = [], a = 0; r > a; a++) n = t[a],
                        i = n.selector + " ",
                    void 0 === s[i] && (s[i] = n.needsContext ? se(i, this).index(l) >= 0 : se.find(i, this, null, [l]).length),
                    s[i] && s.push(n);
                    s.length && o.push({
                        elem: l,
                        handlers: s
                    })
                }
                return r < t.length && o.push({
                    elem: this,
                    handlers: t.slice(r)
                }),
                    o
            },
            fix: function (e) {
                if (e[se.expando]) return e;
                var t, i, n, s = e.type,
                    a = e,
                    o = this.fixHooks[s];
                for (o || (this.fixHooks[s] = o = Ne.test(s) ? this.mouseHooks : je.test(s) ? this.keyHooks : {}), n = o.props ? this.props.concat(o.props) : this.props, e = new se.Event(a), t = n.length; t--;) i = n[t],
                    e[i] = a[i];
                return e.target || (e.target = a.srcElement || fe),
                3 === e.target.nodeType && (e.target = e.target.parentNode),
                    e.metaKey = !!e.metaKey,
                    o.filter ? o.filter(e, a) : e
            },
            props: "altKey bubbles cancelable ctrlKey currentTarget eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),
            fixHooks: {},
            keyHooks: {
                props: "char charCode key keyCode".split(" "),
                filter: function (e, t) {
                    return null == e.which && (e.which = null != t.charCode ? t.charCode : t.keyCode),
                        e
                }
            },
            mouseHooks: {
                props: "button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),
                filter: function (e, t) {
                    var i, n, s, a = t.button,
                        o = t.fromElement;
                    return null == e.pageX && null != t.clientX && (n = e.target.ownerDocument || fe, s = n.documentElement, i = n.body, e.pageX = t.clientX + (s && s.scrollLeft || i && i.scrollLeft || 0) - (s && s.clientLeft || i && i.clientLeft || 0), e.pageY = t.clientY + (s && s.scrollTop || i && i.scrollTop || 0) - (s && s.clientTop || i && i.clientTop || 0)),
                    !e.relatedTarget && o && (e.relatedTarget = o === e.target ? t.toElement : o),
                    e.which || void 0 === a || (e.which = 1 & a ? 1 : 2 & a ? 3 : 4 & a ? 2 : 0),
                        e
                }
            },
            special: {
                load: {
                    noBubble: !0
                },
                focus: {
                    trigger: function () {
                        if (this !== f() && this.focus) try {
                            return this.focus(),
                                !1
                        } catch (e) {
                        }
                    },
                    delegateType: "focusin"
                },
                blur: {
                    trigger: function () {
                        return this === f() && this.blur ? (this.blur(), !1) : void 0
                    },
                    delegateType: "focusout"
                },
                click: {
                    trigger: function () {
                        return se.nodeName(this, "input") && "checkbox" === this.type && this.click ? (this.click(), !1) : void 0
                    },
                    _default: function (e) {
                        return se.nodeName(e.target, "a")
                    }
                },
                beforeunload: {
                    postDispatch: function (e) {
                        void 0 !== e.result && e.originalEvent && (e.originalEvent.returnValue = e.result)
                    }
                }
            },
            simulate: function (e, t, i, n) {
                var s = se.extend(new se.Event, i, {
                    type: e,
                    isSimulated: !0,
                    originalEvent: {}
                });
                n ? se.event.trigger(s, null, t) : se.event.dispatch.call(t, s),
                s.isDefaultPrevented() && i.preventDefault()
            }
        },
            se.removeEvent = fe.removeEventListener ?
                function (e, t, i) {
                    e.removeEventListener && e.removeEventListener(t, i, !1)
                } : function (e, t, i) {
                var n = "on" + t;
                e.detachEvent && (typeof e[n] === Ce && (e[n] = null), e.detachEvent(n, i))
            },
            se.Event = function (e, t) {
                return this instanceof se.Event ? (e && e.type ? (this.originalEvent = e, this.type = e.type, this.isDefaultPrevented = e.defaultPrevented || void 0 === e.defaultPrevented && e.returnValue === !1 ? u : p) : this.type = e, t && se.extend(this, t), this.timeStamp = e && e.timeStamp || se.now(), void(this[se.expando] = !0)) : new se.Event(e, t)
            },
            se.Event.prototype = {
                isDefaultPrevented: p,
                isPropagationStopped: p,
                isImmediatePropagationStopped: p,
                preventDefault: function () {
                    var e = this.originalEvent;
                    this.isDefaultPrevented = u,
                    e && (e.preventDefault ? e.preventDefault() : e.returnValue = !1)
                },
                stopPropagation: function () {
                    var e = this.originalEvent;
                    this.isPropagationStopped = u,
                    e && (e.stopPropagation && e.stopPropagation(), e.cancelBubble = !0)
                },
                stopImmediatePropagation: function () {
                    var e = this.originalEvent;
                    this.isImmediatePropagationStopped = u,
                    e && e.stopImmediatePropagation && e.stopImmediatePropagation(),
                        this.stopPropagation()
                }
            },
            se.each({
                mouseenter: "mouseover",
                mouseleave: "mouseout",
                pointerenter: "pointerover",
                pointerleave: "pointerout"
            }, function (e, t) {
                se.event.special[e] = {
                    delegateType: t,
                    bindType: t,
                    handle: function (e) {
                        var i, n = this,
                            s = e.relatedTarget,
                            a = e.handleObj;
                        return (!s || s !== n && !se.contains(n, s)) && (e.type = a.origType, i = a.handler.apply(this, arguments), e.type = t),
                            i
                    }
                }
            }),
        ie.submitBubbles || (se.event.special.submit = {
            setup: function () {
                return !se.nodeName(this, "form") && void se.event.add(this, "click._submit keypress._submit", function (e) {
                        var t = e.target,
                            i = se.nodeName(t, "input") || se.nodeName(t, "button") ? t.form : void 0;
                        i && !se._data(i, "submitBubbles") && (se.event.add(i, "submit._submit", function (e) {
                            e._submit_bubble = !0
                        }), se._data(i, "submitBubbles", !0))
                    })
            },
            postDispatch: function (e) {
                e._submit_bubble && (delete e._submit_bubble, this.parentNode && !e.isTrigger && se.event.simulate("submit", this.parentNode, e, !0))
            },
            teardown: function () {
                return !se.nodeName(this, "form") && void se.event.remove(this, "._submit")
            }
        }),
        ie.changeBubbles || (se.event.special.change = {
            setup: function () {
                return Me.test(this.nodeName) ? (("checkbox" === this.type || "radio" === this.type) && (se.event.add(this, "propertychange._change", function (e) {
                    "checked" === e.originalEvent.propertyName && (this._just_changed = !0)
                }), se.event.add(this, "click._change", function (e) {
                    this._just_changed && !e.isTrigger && (this._just_changed = !1),
                        se.event.simulate("change", this, e, !0)
                })), !1) : void se.event.add(this, "beforeactivate._change", function (e) {
                    var t = e.target;
                    Me.test(t.nodeName) && !se._data(t, "changeBubbles") && (se.event.add(t, "change._change", function (e) {
                        !this.parentNode || e.isSimulated || e.isTrigger || se.event.simulate("change", this.parentNode, e, !0)
                    }), se._data(t, "changeBubbles", !0))
                })
            },
            handle: function (e) {
                var t = e.target;
                return this !== t || e.isSimulated || e.isTrigger || "radio" !== t.type && "checkbox" !== t.type ? e.handleObj.handler.apply(this, arguments) : void 0
            },
            teardown: function () {
                return se.event.remove(this, "._change"),
                    !Me.test(this.nodeName)
            }
        }),
        ie.focusinBubbles || se.each({
            focus: "focusin",
            blur: "focusout"
        }, function (e, t) {
            var i = function (e) {
                se.event.simulate(t, e.target, se.event.fix(e), !0)
            };
            se.event.special[t] = {
                setup: function () {
                    var n = this.ownerDocument || this,
                        s = se._data(n, t);
                    s || n.addEventListener(e, i, !0),
                        se._data(n, t, (s || 0) + 1)
                },
                teardown: function () {
                    var n = this.ownerDocument || this,
                        s = se._data(n, t) - 1;
                    s ? se._data(n, t, s) : (n.removeEventListener(e, i, !0), se._removeData(n, t))
                }
            }
        }),
            se.fn.extend({
                on: function (e, t, i, n, s) {
                    var a, o;
                    if ("object" == typeof e) {
                        "string" != typeof t && (i = i || t, t = void 0);
                        for (a in e) this.on(a, t, i, e[a], s);
                        return this
                    }
                    if (null == i && null == n ? (n = t, i = t = void 0) : null == n && ("string" == typeof t ? (n = i, i = void 0) : (n = i, i = t, t = void 0)), n === !1) n = p;
                    else if (!n) return this;
                    return 1 === s && (o = n, n = function (e) {
                        return se().off(e),
                            o.apply(this, arguments)
                    }, n.guid = o.guid || (o.guid = se.guid++)),
                        this.each(function () {
                            se.event.add(this, e, n, i, t)
                        })
                },
                one: function (e, t, i, n) {
                    return this.on(e, t, i, n, 1)
                },
                off: function (e, t, i) {
                    var n, s;
                    if (e && e.preventDefault && e.handleObj) return n = e.handleObj,
                        se(e.delegateTarget).off(n.namespace ? n.origType + "." + n.namespace : n.origType, n.selector, n.handler),
                        this;
                    if ("object" == typeof e) {
                        for (s in e) this.off(s, t, e[s]);
                        return this
                    }
                    return (t === !1 || "function" == typeof t) && (i = t, t = void 0),
                    i === !1 && (i = p),
                        this.each(function () {
                            se.event.remove(this, e, i, t)
                        })
                },
                trigger: function (e, t) {
                    return this.each(function () {
                        se.event.trigger(e, t, this)
                    })
                },
                triggerHandler: function (e, t) {
                    var i = this[0];
                    return i ? se.event.trigger(e, t, i, !0) : void 0
                }
            });
        var ze = "abbr|article|aside|audio|bdi|canvas|data|datalist|details|figcaption|figure|footer|header|hgroup|mark|meter|nav|output|progress|section|summary|time|video",
            $e = / jQuery\d+="(?:null|\d+)"/g,
            He = new RegExp("<(?:" + ze + ")[\\s/>]", "i"),
            Le = /^\s+/,
            We = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi,
            Re = /<([\w:]+)/,
            Fe = /<tbody/i,
            Be = /<|&#?\w+;/,
            qe = /<(?:script|style|link)/i,
            Ve = /checked\s*(?:[^=]|=\s*.checked.)/i,
            Ye = /^$|\/(?:java|ecma)script/i,
            Ue = /^true\/(.*)/,
            Ge = /^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g,
            Xe = {
                option: [1, "<select multiple='multiple'>", "</select>"],
                legend: [1, "<fieldset>", "</fieldset>"],
                area: [1, "<map>", "</map>"],
                param: [1, "<object>", "</object>"],
                thead: [1, "<table>", "</table>"],
                tr: [2, "<table><tbody>", "</tbody></table>"],
                col: [2, "<table><tbody></tbody><colgroup>", "</colgroup></table>"],
                td: [3, "<table><tbody><tr>", "</tr></tbody></table>"],
                _default: ie.htmlSerialize ? [0, "", ""] : [1, "X<div>", "</div>"]
            },
            Ke = m(fe),
            Qe = Ke.appendChild(fe.createElement("div"));
        Xe.optgroup = Xe.option,
            Xe.tbody = Xe.tfoot = Xe.colgroup = Xe.caption = Xe.thead,
            Xe.th = Xe.td,
            se.extend({
                clone: function (e, t, i) {
                    var n, s, a, o, r, l = se.contains(e.ownerDocument, e);
                    if (ie.html5Clone || se.isXMLDoc(e) || !He.test("<" + e.nodeName + ">") ? a = e.cloneNode(!0) : (Qe.innerHTML = e.outerHTML, Qe.removeChild(a = Qe.firstChild)), !(ie.noCloneEvent && ie.noCloneChecked || 1 !== e.nodeType && 11 !== e.nodeType || se.isXMLDoc(e))) for (n = g(a), r = g(e), o = 0; null != (s = r[o]); ++o) n[o] && C(s, n[o]);
                    if (t) if (i) for (r = r || g(e), n = n || g(a), o = 0; null != (s = r[o]); o++) x(s, n[o]);
                    else x(e, a);
                    return n = g(a, "script"),
                    n.length > 0 && _(n, !l && g(e, "script")),
                        n = r = s = null,
                        a
                },
                buildFragment: function (e, t, i, n) {
                    for (var s, a, o, r, l, c, d, h = e.length, u = m(t), p = [], f = 0; h > f; f++) if (a = e[f], a || 0 === a) if ("object" === se.type(a)) se.merge(p, a.nodeType ? [a] : a);
                    else if (Be.test(a)) {
                        for (r = r || u.appendChild(t.createElement("div")), l = (Re.exec(a) || ["", ""])[1].toLowerCase(), d = Xe[l] || Xe._default, r.innerHTML = d[1] + a.replace(We, "<$1></$2>") + d[2], s = d[0]; s--;) r = r.lastChild;
                        if (!ie.leadingWhitespace && Le.test(a) && p.push(t.createTextNode(Le.exec(a)[0])), !ie.tbody) for (a = "table" !== l || Fe.test(a) ? "<table>" !== d[1] || Fe.test(a) ? 0 : r : r.firstChild, s = a && a.childNodes.length; s--;) se.nodeName(c = a.childNodes[s], "tbody") && !c.childNodes.length && a.removeChild(c);
                        for (se.merge(p, r.childNodes), r.textContent = ""; r.firstChild;) r.removeChild(r.firstChild);
                        r = u.lastChild
                    } else p.push(t.createTextNode(a));
                    for (r && u.removeChild(r), ie.appendChecked || se.grep(g(p, "input"), v), f = 0; a = p[f++];) if ((!n || -1 === se.inArray(a, n)) && (o = se.contains(a.ownerDocument, a), r = g(u.appendChild(a), "script"), o && _(r), i)) for (s = 0; a = r[s++];) Ye.test(a.type || "") && i.push(a);
                    return r = null,
                        u
                },
                cleanData: function (e, t) {
                    for (var i, n, s, a, o = 0, r = se.expando, l = se.cache, c = ie.deleteExpando, d = se.event.special; null != (i = e[o]); o++) if ((t || se.acceptData(i)) && (s = i[r], a = s && l[s])) {
                        if (a.events) for (n in a.events) d[n] ? se.event.remove(i, n) : se.removeEvent(i, n, a.handle);
                        l[s] && (delete l[s], c ? delete i[r] : typeof i.removeAttribute !== Ce ? i.removeAttribute(r) : i[r] = null, G.push(s))
                    }
                }
            }),
            se.fn.extend({
                text: function (e) {
                    return Ie(this, function (e) {
                        return void 0 === e ? se.text(this) : this.empty().append((this[0] && this[0].ownerDocument || fe).createTextNode(e))
                    }, null, e, arguments.length)
                },
                append: function () {
                    return this.domManip(arguments, function (e) {
                        if (1 === this.nodeType || 11 === this.nodeType || 9 === this.nodeType) {
                            var t = y(this, e);
                            t.appendChild(e)
                        }
                    })
                },
                prepend: function () {
                    return this.domManip(arguments, function (e) {
                        if (1 === this.nodeType || 11 === this.nodeType || 9 === this.nodeType) {
                            var t = y(this, e);
                            t.insertBefore(e, t.firstChild)
                        }
                    })
                },
                before: function () {
                    return this.domManip(arguments, function (e) {
                        this.parentNode && this.parentNode.insertBefore(e, this)
                    })
                },
                after: function () {
                    return this.domManip(arguments, function (e) {
                        this.parentNode && this.parentNode.insertBefore(e, this.nextSibling)
                    })
                },
                remove: function (e, t) {
                    for (var i, n = e ? se.filter(e, this) : this, s = 0; null != (i = n[s]); s++) t || 1 !== i.nodeType || se.cleanData(g(i)),
                    i.parentNode && (t && se.contains(i.ownerDocument, i) && _(g(i, "script")), i.parentNode.removeChild(i));
                    return this
                },
                empty: function () {
                    for (var e, t = 0; null != (e = this[t]); t++) {
                        for (1 === e.nodeType && se.cleanData(g(e, !1)); e.firstChild;) e.removeChild(e.firstChild);
                        e.options && se.nodeName(e, "select") && (e.options.length = 0)
                    }
                    return this
                },
                clone: function (e, t) {
                    return e = null != e && e,
                        t = null == t ? e : t,
                        this.map(function () {
                            return se.clone(this, e, t)
                        })
                },
                html: function (e) {
                    return Ie(this, function (e) {
                        var t = this[0] || {},
                            i = 0,
                            n = this.length;
                        if (void 0 === e) return 1 === t.nodeType ? t.innerHTML.replace($e, "") : void 0;
                        if (!("string" != typeof e || qe.test(e) || !ie.htmlSerialize && He.test(e) || !ie.leadingWhitespace && Le.test(e) || Xe[(Re.exec(e) || ["", ""])[1].toLowerCase()])) {
                            e = e.replace(We, "<$1></$2>");
                            try {
                                for (; n > i; i++) t = this[i] || {},
                                1 === t.nodeType && (se.cleanData(g(t, !1)), t.innerHTML = e);
                                t = 0
                            } catch (s) {
                            }
                        }
                        t && this.empty().append(e)
                    }, null, e, arguments.length)
                },
                replaceWith: function () {
                    var e = arguments[0];
                    return this.domManip(arguments, function (t) {
                        e = this.parentNode,
                            se.cleanData(g(this)),
                        e && e.replaceChild(t, this)
                    }),
                        e && (e.length || e.nodeType) ? this : this.remove()
                },
                detach: function (e) {
                    return this.remove(e, !0)
                },
                domManip: function (e, t) {
                    e = K.apply([], e);
                    var i, n, s, a, o, r, l = 0,
                        c = this.length,
                        d = this,
                        h = c - 1,
                        u = e[0],
                        p = se.isFunction(u);
                    if (p || c > 1 && "string" == typeof u && !ie.checkClone && Ve.test(u)) return this.each(function (i) {
                        var n = d.eq(i);
                        p && (e[0] = u.call(this, i, n.html())),
                            n.domManip(e, t)
                    });
                    if (c && (r = se.buildFragment(e, this[0].ownerDocument, !1, this), i = r.firstChild, 1 === r.childNodes.length && (r = i), i)) {
                        for (a = se.map(g(r, "script"), b), s = a.length; c > l; l++) n = r,
                        l !== h && (n = se.clone(n, !0, !0), s && se.merge(a, g(n, "script"))),
                            t.call(this[l], n, l);
                        if (s) for (o = a[a.length - 1].ownerDocument, se.map(a, w), l = 0; s > l; l++) n = a[l],
                        Ye.test(n.type || "") && !se._data(n, "globalEval") && se.contains(o, n) && (n.src ? se._evalUrl && se._evalUrl(n.src) : se.globalEval((n.text || n.textContent || n.innerHTML || "").replace(Ge, "")));
                        r = i = null
                    }
                    return this
                }
            }),
            se.each({
                appendTo: "append",
                prependTo: "prepend",
                insertBefore: "before",
                insertAfter: "after",
                replaceAll: "replaceWith"
            }, function (e, t) {
                se.fn[e] = function (e) {
                    for (var i, n = 0, s = [], a = se(e), o = a.length - 1; o >= n; n++) i = n === o ? this : this.clone(!0),
                        se(a[n])[t](i),
                        Q.apply(s, i.get());
                    return this.pushStack(s)
                }
            });
        var Je, Ze = {};
        !
            function () {
                var e;
                ie.shrinkWrapBlocks = function () {
                    if (null != e) return e;
                    e = !1;
                    var t, i, n;
                    return i = fe.getElementsByTagName("body")[0],
                        i && i.style ? (t = fe.createElement("div"), n = fe.createElement("div"), n.style.cssText = "position:absolute;border:0;width:0;height:0;top:0;left:-9999px", i.appendChild(n).appendChild(t), typeof t.style.zoom !== Ce && (t.style.cssText = "-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;display:block;margin:0;border:0;padding:1px;width:1px;zoom:1", t.appendChild(fe.createElement("div")).style.width = "5px", e = 3 !== t.offsetWidth), i.removeChild(n), e) : void 0
                }
            }();
        var et, tt, it = /^margin/,
            nt = new RegExp("^(" + Se + ")(?!px)[a-z%]+$", "i"),
            st = /^(top|right|bottom|left)$/;
        e.getComputedStyle ? (et = function (e) {
            return e.ownerDocument.defaultView.getComputedStyle(e, null)
        }, tt = function (e, t, i) {
            var n, s, a, o, r = e.style;
            return i = i || et(e),
                o = i ? i.getPropertyValue(t) || i[t] : void 0,
            i && ("" !== o || se.contains(e.ownerDocument, e) || (o = se.style(e, t)), nt.test(o) && it.test(t) && (n = r.width, s = r.minWidth, a = r.maxWidth, r.minWidth = r.maxWidth = r.width = o, o = i.width, r.width = n, r.minWidth = s, r.maxWidth = a)),
                void 0 === o ? o : o + ""
        }) : fe.documentElement.currentStyle && (et = function (e) {
            return e.currentStyle
        }, tt = function (e, t, i) {
            var n, s, a, o, r = e.style;
            return i = i || et(e),
                o = i ? i[t] : void 0,
            null == o && r && r[t] && (o = r[t]),
            nt.test(o) && !st.test(t) && (n = r.left, s = e.runtimeStyle, a = s && s.left, a && (s.left = e.currentStyle.left), r.left = "fontSize" === t ? "1em" : o, o = r.pixelLeft + "px", r.left = n, a && (s.left = a)),
                void 0 === o ? o : o + "" || "auto"
        }),
            !
                function () {
                    function t() {
                        var t, i, n, s;
                        i = fe.getElementsByTagName("body")[0],
                        i && i.style && (t = fe.createElement("div"), n = fe.createElement("div"), n.style.cssText = "position:absolute;border:0;width:0;height:0;top:0;left:-9999px", i.appendChild(n).appendChild(t), t.style.cssText = "-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;display:block;margin-top:1%;top:1%;border:1px;padding:1px;width:4px;position:absolute", a = o = !1, l = !0, e.getComputedStyle && (a = "1%" !== (e.getComputedStyle(t, null) || {}).top, o = "4px" === (e.getComputedStyle(t, null) || {
                                width: "4px"
                            }).width, s = t.appendChild(fe.createElement("div")), s.style.cssText = t.style.cssText = "-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;display:block;margin:0;border:0;padding:0", s.style.marginRight = s.style.width = "0", t.style.width = "1px", l = !parseFloat((e.getComputedStyle(s, null) || {}).marginRight)), t.innerHTML = "<table><tr><td></td><td>t</td></tr></table>", s = t.getElementsByTagName("td"), s[0].style.cssText = "margin:0;border:0;padding:0;display:none", r = 0 === s[0].offsetHeight, r && (s[0].style.display = "", s[1].style.display = "none", r = 0 === s[0].offsetHeight), i.removeChild(n))
                    }

                    var i, n, s, a, o, r, l;
                    i = fe.createElement("div"),
                        i.innerHTML = "  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>",
                        s = i.getElementsByTagName("a")[0],
                    (n = s && s.style) && (n.cssText = "float:left;opacity:.5", ie.opacity = "0.5" === n.opacity, ie.cssFloat = !!n.cssFloat, i.style.backgroundClip = "content-box", i.cloneNode(!0).style.backgroundClip = "", ie.clearCloneStyle = "content-box" === i.style.backgroundClip, ie.boxSizing = "" === n.boxSizing || "" === n.MozBoxSizing || "" === n.WebkitBoxSizing, se.extend(ie, {
                        reliableHiddenOffsets: function () {
                            return null == r && t(),
                                r
                        },
                        boxSizingReliable: function () {
                            return null == o && t(),
                                o
                        },
                        pixelPosition: function () {
                            return null == a && t(),
                                a
                        },
                        reliableMarginRight: function () {
                            return null == l && t(),
                                l
                        }
                    }))
                }(),
            se.swap = function (e, t, i, n) {
                var s, a, o = {};
                for (a in t) o[a] = e.style[a],
                    e.style[a] = t[a];
                s = i.apply(e, n || []);
                for (a in t) e.style[a] = o[a];
                return s
            };
        var at = /alpha\([^)]*\)/i,
            ot = /opacity\s*=\s*([^)]*)/,
            rt = /^(none|table(?!-c[ea]).+)/,
            lt = new RegExp("^(" + Se + ")(.*)$", "i"),
            ct = new RegExp("^([+-])=(" + Se + ")", "i"),
            dt = {
                position: "absolute",
                visibility: "hidden",
                display: "block"
            },
            ht = {
                letterSpacing: "0",
                fontWeight: "400"
            },
            ut = ["Webkit", "O", "Moz", "ms"];
        se.extend({
            cssHooks: {
                opacity: {
                    get: function (e, t) {
                        if (t) {
                            var i = tt(e, "opacity");
                            return "" === i ? "1" : i
                        }
                    }
                }
            },
            cssNumber: {
                columnCount: !0,
                fillOpacity: !0,
                flexGrow: !0,
                flexShrink: !0,
                fontWeight: !0,
                lineHeight: !0,
                opacity: !0,
                order: !0,
                orphans: !0,
                widows: !0,
                zIndex: !0,
                zoom: !0
            },
            cssProps: {
                "float": ie.cssFloat ? "cssFloat" : "styleFloat"
            },
            style: function (e, t, i, n) {
                if (e && 3 !== e.nodeType && 8 !== e.nodeType && e.style) {
                    var s, a, o, r = se.camelCase(t),
                        l = e.style;
                    if (t = se.cssProps[r] || (se.cssProps[r] = D(l, r)), o = se.cssHooks[t] || se.cssHooks[r], void 0 === i) return o && "get" in o && void 0 !== (s = o.get(e, !1, n)) ? s : l[t];
                    if (a = typeof i, "string" === a && (s = ct.exec(i)) && (i = (s[1] + 1) * s[2] + parseFloat(se.css(e, t)), a = "number"), null != i && i === i && ("number" !== a || se.cssNumber[r] || (i += "px"), ie.clearCloneStyle || "" !== i || 0 !== t.indexOf("background") || (l[t] = "inherit"), !(o && "set" in o && void 0 === (i = o.set(e, i, n))))) try {
                        l[t] = i
                    } catch (c) {
                    }
                }
            },
            css: function (e, t, i, n) {
                var s, a, o, r = se.camelCase(t);
                return t = se.cssProps[r] || (se.cssProps[r] = D(e.style, r)),
                    o = se.cssHooks[t] || se.cssHooks[r],
                o && "get" in o && (a = o.get(e, !0, i)),
                void 0 === a && (a = tt(e, t, n)),
                "normal" === a && t in ht && (a = ht[t]),
                    "" === i || i ? (s = parseFloat(a), i === !0 || se.isNumeric(s) ? s || 0 : a) : a
            }
        }),
            se.each(["height", "width"], function (e, t) {
                se.cssHooks[t] = {
                    get: function (e, i, n) {
                        return i ? rt.test(se.css(e, "display")) && 0 === e.offsetWidth ? se.swap(e, dt, function () {
                            return M(e, t, n)
                        }) : M(e, t, n) : void 0
                    },
                    set: function (e, i, n) {
                        var s = n && et(e);
                        return I(e, i, n ? P(e, t, n, ie.boxSizing && "border-box" === se.css(e, "boxSizing", !1, s), s) : 0)
                    }
                }
            }),
        ie.opacity || (se.cssHooks.opacity = {
            get: function (e, t) {
                return ot.test((t && e.currentStyle ? e.currentStyle.filter : e.style.filter) || "") ? .01 * parseFloat(RegExp.$1) + "" : t ? "1" : ""
            },
            set: function (e, t) {
                var i = e.style,
                    n = e.currentStyle,
                    s = se.isNumeric(t) ? "alpha(opacity=" + 100 * t + ")" : "",
                    a = n && n.filter || i.filter || "";
                i.zoom = 1,
                (t >= 1 || "" === t) && "" === se.trim(a.replace(at, "")) && i.removeAttribute && (i.removeAttribute("filter"), "" === t || n && !n.filter) || (i.filter = at.test(a) ? a.replace(at, s) : a + " " + s)
            }
        }),
            se.cssHooks.marginRight = S(ie.reliableMarginRight, function (e, t) {
                return t ? se.swap(e, {
                    display: "inline-block"
                }, tt, [e, "marginRight"]) : void 0
            }),
            se.each({
                margin: "",
                padding: "",
                border: "Width"
            }, function (e, t) {
                se.cssHooks[e + t] = {
                    expand: function (i) {
                        for (var n = 0, s = {}, a = "string" == typeof i ? i.split(" ") : [i]; 4 > n; n++) s[e + De[n] + t] = a[n] || a[n - 2] || a[0];
                        return s
                    }
                },
                it.test(e) || (se.cssHooks[e + t].set = I)
            }),
            se.fn.extend({
                css: function (e, t) {
                    return Ie(this, function (e, t, i) {
                        var n, s, a = {},
                            o = 0;
                        if (se.isArray(t)) {
                            for (n = et(e), s = t.length; s > o; o++) a[t[o]] = se.css(e, t[o], !1, n);
                            return a
                        }
                        return void 0 !== i ? se.style(e, t, i) : se.css(e, t)
                    }, e, t, arguments.length > 1)
                },
                show: function () {
                    return E(this, !0)
                },
                hide: function () {
                    return E(this)
                },
                toggle: function (e) {
                    return "boolean" == typeof e ? e ? this.show() : this.hide() : this.each(function () {
                        Ee(this) ? se(this).show() : se(this).hide()
                    })
                }
            }),
            se.Tween = j,
            j.prototype = {
                constructor: j,
                init: function (e, t, i, n, s, a) {
                    this.elem = e,
                        this.prop = i,
                        this.easing = s || "swing",
                        this.options = t,
                        this.start = this.now = this.cur(),
                        this.end = n,
                        this.unit = a || (se.cssNumber[i] ? "" : "px")
                },
                cur: function () {
                    var e = j.propHooks[this.prop];
                    return e && e.get ? e.get(this) : j.propHooks._default.get(this)
                },
                run: function (e) {
                    var t, i = j.propHooks[this.prop];
                    return this.pos = t = this.options.duration ? se.easing[this.easing](e, this.options.duration * e, 0, 1, this.options.duration) : e,
                        this.now = (this.end - this.start) * t + this.start,
                    this.options.step && this.options.step.call(this.elem, this.now, this),
                        i && i.set ? i.set(this) : j.propHooks._default.set(this),
                        this
                }
            },
            j.prototype.init.prototype = j.prototype,
            j.propHooks = {
                _default: {
                    get: function (e) {
                        var t;
                        return null == e.elem[e.prop] || e.elem.style && null != e.elem.style[e.prop] ? (t = se.css(e.elem, e.prop, ""), t && "auto" !== t ? t : 0) : e.elem[e.prop]
                    },
                    set: function (e) {
                        se.fx.step[e.prop] ? se.fx.step[e.prop](e) : e.elem.style && (null != e.elem.style[se.cssProps[e.prop]] || se.cssHooks[e.prop]) ? se.style(e.elem, e.prop, e.now + e.unit) : e.elem[e.prop] = e.now
                    }
                }
            },
            j.propHooks.scrollTop = j.propHooks.scrollLeft = {
                set: function (e) {
                    e.elem.nodeType && e.elem.parentNode && (e.elem[e.prop] = e.now)
                }
            },
            se.easing = {
                linear: function (e) {
                    return e
                },
                swing: function (e) {
                    return .5 - Math.cos(e * Math.PI) / 2
                }
            },
            se.fx = j.prototype.init,
            se.fx.step = {};
        var pt, ft, mt = /^(?:toggle|show|hide)$/,
            gt = new RegExp("^(?:([+-])=|)(" + Se + ")([a-z%]*)$", "i"),
            vt = /queueHooks$/,
            yt = [z],
            bt = {
                "*": [function (e, t) {
                    var i = this.createTween(e, t),
                        n = i.cur(),
                        s = gt.exec(t),
                        a = s && s[3] || (se.cssNumber[e] ? "" : "px"),
                        o = (se.cssNumber[e] || "px" !== a && +n) && gt.exec(se.css(i.elem, e)),
                        r = 1,
                        l = 20;
                    if (o && o[3] !== a) {
                        a = a || o[3],
                            s = s || [],
                            o = +n || 1;
                        do r = r || ".5",
                            o /= r,
                            se.style(i.elem, e, o + a);
                        while (r !== (r = i.cur() / n) && 1 !== r && --l)
                    }
                    return s && (o = i.start = +o || +n || 0, i.unit = a, i.end = s[1] ? o + (s[1] + 1) * s[2] : +s[2]),
                        i
                }]
            };
        se.Animation = se.extend(H, {
            tweener: function (e, t) {
                se.isFunction(e) ? (t = e, e = ["*"]) : e = e.split(" ");
                for (var i, n = 0, s = e.length; s > n; n++) i = e[n],
                    bt[i] = bt[i] || [],
                    bt[i].unshift(t)
            },
            prefilter: function (e, t) {
                t ? yt.unshift(e) : yt.push(e)
            }
        }),
            se.speed = function (e, t, i) {
                var n = e && "object" == typeof e ? se.extend({}, e) : {
                    complete: i || !i && t || se.isFunction(e) && e,
                    duration: e,
                    easing: i && t || t && !se.isFunction(t) && t
                };
                return n.duration = se.fx.off ? 0 : "number" == typeof n.duration ? n.duration : n.duration in se.fx.speeds ? se.fx.speeds[n.duration] : se.fx.speeds._default,
                (null == n.queue || n.queue === !0) && (n.queue = "fx"),
                    n.old = n.complete,
                    n.complete = function () {
                        se.isFunction(n.old) && n.old.call(this),
                        n.queue && se.dequeue(this, n.queue)
                    },
                    n
            },
            se.fn.extend({
                fadeTo: function (e, t, i, n) {
                    return this.filter(Ee).css("opacity", 0).show().end().animate({
                        opacity: t
                    }, e, i, n)
                },
                animate: function (e, t, i, n) {
                    var s = se.isEmptyObject(e),
                        a = se.speed(t, i, n),
                        o = function () {
                            var t = H(this, se.extend({}, e), a);
                            (s || se._data(this, "finish")) && t.stop(!0)
                        };
                    return o.finish = o,
                        s || a.queue === !1 ? this.each(o) : this.queue(a.queue, o)
                },
                stop: function (e, t, i) {
                    var n = function (e) {
                        var t = e.stop;
                        delete e.stop,
                            t(i)
                    };
                    return "string" != typeof e && (i = t, t = e, e = void 0),
                    t && e !== !1 && this.queue(e || "fx", []),
                        this.each(function () {
                            var t = !0,
                                s = null != e && e + "queueHooks",
                                a = se.timers,
                                o = se._data(this);
                            if (s) o[s] && o[s].stop && n(o[s]);
                            else for (s in o) o[s] && o[s].stop && vt.test(s) && n(o[s]);
                            for (s = a.length; s--;) a[s].elem !== this || null != e && a[s].queue !== e || (a[s].anim.stop(i), t = !1, a.splice(s, 1));
                            (t || !i) && se.dequeue(this, e)
                        })
                },
                finish: function (e) {
                    return e !== !1 && (e = e || "fx"),
                        this.each(function () {
                            var t, i = se._data(this),
                                n = i[e + "queue"],
                                s = i[e + "queueHooks"],
                                a = se.timers,
                                o = n ? n.length : 0;
                            for (i.finish = !0, se.queue(this, e, []), s && s.stop && s.stop.call(this, !0), t = a.length; t--;) a[t].elem === this && a[t].queue === e && (a[t].anim.stop(!0), a.splice(t, 1));
                            for (t = 0; o > t; t++) n[t] && n[t].finish && n[t].finish.call(this);
                            delete i.finish
                        })
                }
            }),
            se.each(["toggle", "show", "hide"], function (e, t) {
                var i = se.fn[t];
                se.fn[t] = function (e, n, s) {
                    return null == e || "boolean" == typeof e ? i.apply(this, arguments) : this.animate(A(t, !0), e, n, s)
                }
            }),
            se.each({
                slideDown: A("show"),
                slideUp: A("hide"),
                slideToggle: A("toggle"),
                fadeIn: {
                    opacity: "show"
                },
                fadeOut: {
                    opacity: "hide"
                },
                fadeToggle: {
                    opacity: "toggle"
                }
            }, function (e, t) {
                se.fn[e] = function (e, i, n) {
                    return this.animate(t, e, i, n)
                }
            }),
            se.timers = [],
            se.fx.tick = function () {
                var e, t = se.timers,
                    i = 0;
                for (pt = se.now(); i < t.length; i++) e = t[i],
                e() || t[i] !== e || t.splice(i--, 1);
                t.length || se.fx.stop(),
                    pt = void 0
            },
            se.fx.timer = function (e) {
                se.timers.push(e),
                    e() ? se.fx.start() : se.timers.pop()
            },
            se.fx.interval = 13,
            se.fx.start = function () {
                ft || (ft = setInterval(se.fx.tick, se.fx.interval))
            },
            se.fx.stop = function () {
                clearInterval(ft),
                    ft = null
            },
            se.fx.speeds = {
                slow: 600,
                fast: 200,
                _default: 400
            },
            se.fn.delay = function (e, t) {
                return e = se.fx ? se.fx.speeds[e] || e : e,
                    t = t || "fx",
                    this.queue(t, function (t, i) {
                        var n = setTimeout(t, e);
                        i.stop = function () {
                            clearTimeout(n)
                        }
                    })
            },


            function () {
                var e, t, i, n, s;
                t = fe.createElement("div"),
                    t.setAttribute("className", "t"),
                    t.innerHTML = "  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>",
                    n = t.getElementsByTagName("a")[0],
                    i = fe.createElement("select"),
                    s = i.appendChild(fe.createElement("option")),
                    e = t.getElementsByTagName("input")[0],
                    n.style.cssText = "top:1px",
                    ie.getSetAttribute = "t" !== t.className,
                    ie.style = /top/.test(n.getAttribute("style")),
                    ie.hrefNormalized = "/a" === n.getAttribute("href"),
                    ie.checkOn = !!e.value,
                    ie.optSelected = s.selected,
                    ie.enctype = !!fe.createElement("form").enctype,
                    i.disabled = !0,
                    ie.optDisabled = !s.disabled,
                    e = fe.createElement("input"),
                    e.setAttribute("value", ""),
                    ie.input = "" === e.getAttribute("value"),
                    e.value = "t",
                    e.setAttribute("type", "radio"),
                    ie.radioValue = "t" === e.value
            }();
        var wt = /\r/g;
        se.fn.extend({
            val: function (e) {
                var t, i, n, s = this[0];
                return arguments.length ? (n = se.isFunction(e), this.each(function (i) {
                    var s;
                    1 === this.nodeType && (s = n ? e.call(this, i, se(this).val()) : e, null == s ? s = "" : "number" == typeof s ? s += "" : se.isArray(s) && (s = se.map(s, function (e) {
                        return null == e ? "" : e + ""
                    })), t = se.valHooks[this.type] || se.valHooks[this.nodeName.toLowerCase()], t && "set" in t && void 0 !== t.set(this, s, "value") || (this.value = s))
                })) : s ? (t = se.valHooks[s.type] || se.valHooks[s.nodeName.toLowerCase()], t && "get" in t && void 0 !== (i = t.get(s, "value")) ? i : (i = s.value, "string" == typeof i ? i.replace(wt, "") : null == i ? "" : i)) : void 0
            }
        }),
            se.extend({
                valHooks: {
                    option: {
                        get: function (e) {
                            var t = se.find.attr(e, "value");
                            return null != t ? t : se.trim(se.text(e))
                        }
                    },
                    select: {
                        get: function (e) {
                            for (var t, i, n = e.options, s = e.selectedIndex, a = "select-one" === e.type || 0 > s, o = a ? null : [], r = a ? s + 1 : n.length, l = 0 > s ? r : a ? s : 0; r > l; l++) if (i = n[l], !(!i.selected && l !== s || (ie.optDisabled ? i.disabled : null !== i.getAttribute("disabled")) || i.parentNode.disabled && se.nodeName(i.parentNode, "optgroup"))) {
                                if (t = se(i).val(), a) return t;
                                o.push(t)
                            }
                            return o
                        },
                        set: function (e, t) {
                            for (var i, n, s = e.options, a = se.makeArray(t), o = s.length; o--;) if (n = s[o], se.inArray(se.valHooks.option.get(n), a) >= 0) try {
                                n.selected = i = !0
                            } catch (r) {
                                n.scrollHeight
                            } else n.selected = !1;
                            return i || (e.selectedIndex = -1),
                                s
                        }
                    }
                }
            }),
            se.each(["radio", "checkbox"], function () {
                se.valHooks[this] = {
                    set: function (e, t) {
                        return se.isArray(t) ? e.checked = se.inArray(se(e).val(), t) >= 0 : void 0
                    }
                },
                ie.checkOn || (se.valHooks[this].get = function (e) {
                    return null === e.getAttribute("value") ? "on" : e.value
                })
            });
        var _t, xt, Ct = se.expr.attrHandle,
            kt = /^(?:checked|selected)$/i,
            Tt = ie.getSetAttribute,
            St = ie.input;
        se.fn.extend({
            attr: function (e, t) {
                return Ie(this, se.attr, e, t, arguments.length > 1)
            },
            removeAttr: function (e) {
                return this.each(function () {
                    se.removeAttr(this, e)
                })
            }
        }),
            se.extend({
                attr: function (e, t, i) {
                    var n, s, a = e.nodeType;
                    if (e && 3 !== a && 8 !== a && 2 !== a) return typeof e.getAttribute === Ce ? se.prop(e, t, i) : (1 === a && se.isXMLDoc(e) || (t = t.toLowerCase(), n = se.attrHooks[t] || (se.expr.match.bool.test(t) ? xt : _t)), void 0 === i ? n && "get" in n && null !== (s = n.get(e, t)) ? s : (s = se.find.attr(e, t), null == s ? void 0 : s) : null !== i ? n && "set" in n && void 0 !== (s = n.set(e, i, t)) ? s : (e.setAttribute(t, i + ""), i) : void se.removeAttr(e, t))
                },
                removeAttr: function (e, t) {
                    var i, n, s = 0,
                        a = t && t.match(be);
                    if (a && 1 === e.nodeType) for (; i = a[s++];) n = se.propFix[i] || i,
                        se.expr.match.bool.test(i) ? St && Tt || !kt.test(i) ? e[n] = !1 : e[se.camelCase("default-" + i)] = e[n] = !1 : se.attr(e, i, ""),
                        e.removeAttribute(Tt ? i : n)
                },
                attrHooks: {
                    type: {
                        set: function (e, t) {
                            if (!ie.radioValue && "radio" === t && se.nodeName(e, "input")) {
                                var i = e.value;
                                return e.setAttribute("type", t),
                                i && (e.value = i),
                                    t
                            }
                        }
                    }
                }
            }),
            xt = {
                set: function (e, t, i) {
                    return t === !1 ? se.removeAttr(e, i) : St && Tt || !kt.test(i) ? e.setAttribute(!Tt && se.propFix[i] || i, i) : e[se.camelCase("default-" + i)] = e[i] = !0,
                        i
                }
            },
            se.each(se.expr.match.bool.source.match(/\w+/g), function (e, t) {
                var i = Ct[t] || se.find.attr;
                Ct[t] = St && Tt || !kt.test(t) ?
                    function (e, t, n) {
                        var s, a;
                        return n || (a = Ct[t], Ct[t] = s, s = null != i(e, t, n) ? t.toLowerCase() : null, Ct[t] = a),
                            s
                    } : function (e, t, i) {
                    return i ? void 0 : e[se.camelCase("default-" + t)] ? t.toLowerCase() : null
                }
            }),
        St && Tt || (se.attrHooks.value = {
            set: function (e, t, i) {
                return se.nodeName(e, "input") ? void(e.defaultValue = t) : _t && _t.set(e, t, i)
            }
        }),
        Tt || (_t = {
            set: function (e, t, i) {
                var n = e.getAttributeNode(i);
                return n || e.setAttributeNode(n = e.ownerDocument.createAttribute(i)),
                    n.value = t += "",
                    "value" === i || t === e.getAttribute(i) ? t : void 0
            }
        }, Ct.id = Ct.name = Ct.coords = function (e, t, i) {
            var n;
            return i ? void 0 : (n = e.getAttributeNode(t)) && "" !== n.value ? n.value : null
        }, se.valHooks.button = {
            get: function (e, t) {
                var i = e.getAttributeNode(t);
                return i && i.specified ? i.value : void 0
            },
            set: _t.set
        }, se.attrHooks.contenteditable = {
            set: function (e, t, i) {
                _t.set(e, "" !== t && t, i)
            }
        }, se.each(["width", "height"], function (e, t) {
            se.attrHooks[t] = {
                set: function (e, i) {
                    return "" === i ? (e.setAttribute(t, "auto"), i) : void 0
                }
            }
        })),
        ie.style || (se.attrHooks.style = {
            get: function (e) {
                return e.style.cssText || void 0
            },
            set: function (e, t) {
                return e.style.cssText = t + ""
            }
        });
        var Dt = /^(?:input|select|textarea|button|object)$/i,
            Et = /^(?:a|area)$/i;
        se.fn.extend({
            prop: function (e, t) {
                return Ie(this, se.prop, e, t, arguments.length > 1)
            },
            removeProp: function (e) {
                return e = se.propFix[e] || e,
                    this.each(function () {
                        try {
                            this[e] = void 0,
                                delete this[e]
                        } catch (t) {
                        }
                    })
            }
        }),
            se.extend({
                propFix: {
                    "for": "htmlFor",
                    "class": "className"
                },
                prop: function (e, t, i) {
                    var n, s, a, o = e.nodeType;
                    if (e && 3 !== o && 8 !== o && 2 !== o) return a = 1 !== o || !se.isXMLDoc(e),
                    a && (t = se.propFix[t] || t, s = se.propHooks[t]),
                        void 0 !== i ? s && "set" in s && void 0 !== (n = s.set(e, i, t)) ? n : e[t] = i : s && "get" in s && null !== (n = s.get(e, t)) ? n : e[t]
                },
                propHooks: {
                    tabIndex: {
                        get: function (e) {
                            var t = se.find.attr(e, "tabindex");
                            return t ? parseInt(t, 10) : Dt.test(e.nodeName) || Et.test(e.nodeName) && e.href ? 0 : -1
                        }
                    }
                }
            }),
        ie.hrefNormalized || se.each(["href", "src"], function (e, t) {
            se.propHooks[t] = {
                get: function (e) {
                    return e.getAttribute(t, 4)
                }
            }
        }),
        ie.optSelected || (se.propHooks.selected = {
            get: function (e) {
                var t = e.parentNode;
                return t && (t.selectedIndex, t.parentNode && t.parentNode.selectedIndex),
                    null
            }
        }),
            se.each(["tabIndex", "readOnly", "maxLength", "cellSpacing", "cellPadding", "rowSpan", "colSpan", "useMap", "frameBorder", "contentEditable"], function () {
                se.propFix[this.toLowerCase()] = this
            }),
        ie.enctype || (se.propFix.enctype = "encoding");
        var It = /[\t\r\n\f]/g;
        se.fn.extend({
            addClass: function (e) {
                var t, i, n, s, a, o, r = 0,
                    l = this.length,
                    c = "string" == typeof e && e;
                if (se.isFunction(e)) return this.each(function (t) {
                    se(this).addClass(e.call(this, t, this.className))
                });
                if (c) for (t = (e || "").match(be) || []; l > r; r++) if (i = this[r], n = 1 === i.nodeType && (i.className ? (" " + i.className + " ").replace(It, " ") : " ")) {
                    for (a = 0; s = t[a++];) n.indexOf(" " + s + " ") < 0 && (n += s + " ");
                    o = se.trim(n),
                    i.className !== o && (i.className = o)
                }
                return this
            },
            removeClass: function (e) {
                var t, i, n, s, a, o, r = 0,
                    l = this.length,
                    c = 0 === arguments.length || "string" == typeof e && e;
                if (se.isFunction(e)) return this.each(function (t) {
                    se(this).removeClass(e.call(this, t, this.className))
                });
                if (c) for (t = (e || "").match(be) || []; l > r; r++) if (i = this[r], n = 1 === i.nodeType && (i.className ? (" " + i.className + " ").replace(It, " ") : "")) {
                    for (a = 0; s = t[a++];) for (; n.indexOf(" " + s + " ") >= 0;) n = n.replace(" " + s + " ", " ");
                    o = e ? se.trim(n) : "",
                    i.className !== o && (i.className = o)
                }
                return this
            },
            toggleClass: function (e, t) {
                var i = typeof e;
                return "boolean" == typeof t && "string" === i ? t ? this.addClass(e) : this.removeClass(e) : this.each(se.isFunction(e) ?
                    function (i) {
                        se(this).toggleClass(e.call(this, i, this.className, t), t)
                    } : function () {
                    if ("string" === i) for (var t, n = 0, s = se(this), a = e.match(be) || []; t = a[n++];) s.hasClass(t) ? s.removeClass(t) : s.addClass(t);
                    else(i === Ce || "boolean" === i) && (this.className && se._data(this, "__className__", this.className), this.className = this.className || e === !1 ? "" : se._data(this, "__className__") || "")
                })
            },
            hasClass: function (e) {
                for (var t = " " + e + " ", i = 0, n = this.length; n > i; i++) if (1 === this[i].nodeType && (" " + this[i].className + " ").replace(It, " ").indexOf(t) >= 0) return !0;
                return !1
            }
        }),
            se.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error contextmenu".split(" "), function (e, t) {
                se.fn[t] = function (e, i) {
                    return arguments.length > 0 ? this.on(t, null, e, i) : this.trigger(t)
                }
            }),
            se.fn.extend({
                hover: function (e, t) {
                    return this.mouseenter(e).mouseleave(t || e)
                },
                bind: function (e, t, i) {
                    return this.on(e, null, t, i)
                },
                unbind: function (e, t) {
                    return this.off(e, null, t)
                },
                delegate: function (e, t, i, n) {
                    return this.on(t, e, i, n)
                },
                undelegate: function (e, t, i) {
                    return 1 === arguments.length ? this.off(e, "**") : this.off(t, e || "**", i)
                }
            });
        var Pt = se.now(),
            Mt = /\?/,
            jt = /(,)|(\[|{)|(}|])|"(?:[^"\\\r\n]|\\["\\\/bfnrt]|\\u[\da-fA-F]{4})*"\s*:?|true|false|null|-?(?!0\d)\d+(?:\.\d+|)(?:[eE][+-]?\d+|)/g;
        se.parseJSON = function (t) {
            if (e.JSON && e.JSON.parse) return e.JSON.parse(t + "");
            var i, n = null,
                s = se.trim(t + "");
            return s && !se.trim(s.replace(jt, function (e, t, s, a) {
                return i && t && (n = 0),
                    0 === n ? e : (i = s || t, n += !a - !s, "")
            })) ? Function("return " + s)() : se.error("Invalid JSON: " + t)
        },
            se.parseXML = function (t) {
                var i, n;
                if (!t || "string" != typeof t) return null;
                try {
                    e.DOMParser ? (n = new DOMParser, i = n.parseFromString(t, "text/xml")) : (i = new ActiveXObject("Microsoft.XMLDOM"), i.async = "false", i.loadXML(t))
                } catch (s) {
                    i = void 0
                }
                return i && i.documentElement && !i.getElementsByTagName("parsererror").length || se.error("Invalid XML: " + t),
                    i
            };
        var Nt, At, Ot = /#.*$/,
            zt = /([?&])_=[^&]*/,
            $t = /^(.*?):[ \t]*([^\r\n]*)\r?$/gm,
            Ht = /^(?:about|app|app-storage|.+-extension|file|res|widget):$/,
            Lt = /^(?:GET|HEAD)$/,
            Wt = /^\/\//,
            Rt = /^([\w.+-]+:)(?:\/\/(?:[^\/?#]*@|)([^\/?#:]*)(?::(\d+)|)|)/,
            Ft = {},
            Bt = {},
            qt = "*/".concat("*");
        try {
            At = location.href
        } catch (Vt) {
            At = fe.createElement("a"),
                At.href = "",
                At = At.href
        }
        Nt = Rt.exec(At.toLowerCase()) || [],
            se.extend({
                active: 0,
                lastModified: {},
                etag: {},
                ajaxSettings: {
                    url: At,
                    type: "GET",
                    isLocal: Ht.test(Nt[1]),
                    global: !0,
                    processData: !0,
                    async: !0,
                    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                    accepts: {
                        "*": qt,
                        text: "text/plain",
                        html: "text/html",
                        xml: "application/xml, text/xml",
                        json: "application/json, text/javascript"
                    },
                    contents: {
                        xml: /xml/,
                        html: /html/,
                        json: /json/
                    },
                    responseFields: {
                        xml: "responseXML",
                        text: "responseText",
                        json: "responseJSON"
                    },
                    converters: {
                        "* text": String,
                        "text html": !0,
                        "text json": se.parseJSON,
                        "text xml": se.parseXML
                    },
                    flatOptions: {
                        url: !0,
                        context: !0
                    }
                },
                ajaxSetup: function (e, t) {
                    return t ? R(R(e, se.ajaxSettings), t) : R(se.ajaxSettings, e)
                },
                ajaxPrefilter: L(Ft),
                ajaxTransport: L(Bt),
                ajax: function (e, t) {
                    function i(e, t, i, n) {
                        var s, d, v, y, w, x = t;
                        2 !== b && (b = 2, r && clearTimeout(r), c = void 0, o = n || "", _.readyState = e > 0 ? 4 : 0, s = e >= 200 && 300 > e || 304 === e, i && (y = F(h, _, i)), y = B(h, y, _, s), s ? (h.ifModified && (w = _.getResponseHeader("Last-Modified"), w && (se.lastModified[a] = w), w = _.getResponseHeader("etag"), w && (se.etag[a] = w)), 204 === e || "HEAD" === h.type ? x = "nocontent" : 304 === e ? x = "notmodified" : (x = y.state, d = y.data, v = y.error, s = !v)) : (v = x, (e || !x) && (x = "error", 0 > e && (e = 0))), _.status = e, _.statusText = (t || x) + "", s ? f.resolveWith(u, [d, x, _]) : f.rejectWith(u, [_, x, v]), _.statusCode(g), g = void 0, l && p.trigger(s ? "ajaxSuccess" : "ajaxError", [_, h, s ? d : v]), m.fireWith(u, [_, x]), l && (p.trigger("ajaxComplete", [_, h]), --se.active || se.event.trigger("ajaxStop")))
                    }

                    "object" == typeof e && (t = e, e = void 0),
                        t = t || {};
                    var n, s, a, o, r, l, c, d, h = se.ajaxSetup({}, t),
                        u = h.context || h,
                        p = h.context && (u.nodeType || u.jquery) ? se(u) : se.event,
                        f = se.Deferred(),
                        m = se.Callbacks("once memory"),
                        g = h.statusCode || {},
                        v = {},
                        y = {},
                        b = 0,
                        w = "canceled",
                        _ = {
                            readyState: 0,
                            getResponseHeader: function (e) {
                                var t;
                                if (2 === b) {
                                    if (!d) for (d = {}; t = $t.exec(o);) d[t[1].toLowerCase()] = t[2];
                                    t = d[e.toLowerCase()]
                                }
                                return null == t ? null : t
                            },
                            getAllResponseHeaders: function () {
                                return 2 === b ? o : null
                            },
                            setRequestHeader: function (e, t) {
                                var i = e.toLowerCase();
                                return b || (e = y[i] = y[i] || e, v[e] = t),
                                    this
                            },
                            overrideMimeType: function (e) {
                                return b || (h.mimeType = e),
                                    this
                            },
                            statusCode: function (e) {
                                var t;
                                if (e) if (2 > b) for (t in e) g[t] = [g[t], e[t]];
                                else _.always(e[_.status]);
                                return this
                            },
                            abort: function (e) {
                                var t = e || w;
                                return c && c.abort(t),
                                    i(0, t),
                                    this
                            }
                        };
                    if (f.promise(_).complete = m.add, _.success = _.done, _.error = _.fail, h.url = ((e || h.url || At) + "").replace(Ot, "").replace(Wt, Nt[1] + "//"), h.type = t.method || t.type || h.method || h.type, h.dataTypes = se.trim(h.dataType || "*").toLowerCase().match(be) || [""], null == h.crossDomain && (n = Rt.exec(h.url.toLowerCase()), h.crossDomain = !(!n || n[1] === Nt[1] && n[2] === Nt[2] && (n[3] || ("http:" === n[1] ? "80" : "443")) === (Nt[3] || ("http:" === Nt[1] ? "80" : "443")))), h.data && h.processData && "string" != typeof h.data && (h.data = se.param(h.data, h.traditional)), W(Ft, h, t, _), 2 === b) return _;
                    l = h.global,
                    l && 0 === se.active++ && se.event.trigger("ajaxStart"),
                        h.type = h.type.toUpperCase(),
                        h.hasContent = !Lt.test(h.type),
                        a = h.url,
                    h.hasContent || (h.data && (a = h.url += (Mt.test(a) ? "&" : "?") + h.data, delete h.data), h.cache === !1 && (h.url = zt.test(a) ? a.replace(zt, "$1_=" + Pt++) : a + (Mt.test(a) ? "&" : "?") + "_=" + Pt++)),
                    h.ifModified && (se.lastModified[a] && _.setRequestHeader("If-Modified-Since", se.lastModified[a]), se.etag[a] && _.setRequestHeader("If-None-Match", se.etag[a])),
                    (h.data && h.hasContent && h.contentType !== !1 || t.contentType) && _.setRequestHeader("Content-Type", h.contentType),
                        _.setRequestHeader("Accept", h.dataTypes[0] && h.accepts[h.dataTypes[0]] ? h.accepts[h.dataTypes[0]] + ("*" !== h.dataTypes[0] ? ", " + qt + "; q=0.01" : "") : h.accepts["*"]);
                    for (s in h.headers) _.setRequestHeader(s, h.headers[s]);
                    if (h.beforeSend && (h.beforeSend.call(u, _, h) === !1 || 2 === b)) return _.abort();
                    w = "abort";
                    for (s in {
                        success: 1,
                        error: 1,
                        complete: 1
                    }) _[s](h[s]);
                    if (c = W(Bt, h, t, _)) {
                        _.readyState = 1,
                        l && p.trigger("ajaxSend", [_, h]),
                        h.async && h.timeout > 0 && (r = setTimeout(function () {
                            _.abort("timeout")
                        }, h.timeout));
                        try {
                            b = 1,
                                c.send(v, i)
                        } catch (x) {
                            if (!(2 > b)) throw x;
                            i(-1, x)
                        }
                    } else i(-1, "No Transport");
                    return _
                },
                getJSON: function (e, t, i) {
                    return se.get(e, t, i, "json")
                },
                getScript: function (e, t) {
                    return se.get(e, void 0, t, "script")
                }
            }),
            se.each(["get", "post"], function (e, t) {
                se[t] = function (e, i, n, s) {
                    return se.isFunction(i) && (s = s || n, n = i, i = void 0),
                        se.ajax({
                            url: e,
                            type: t,
                            dataType: s,
                            data: i,
                            success: n
                        })
                }
            }),
            se.each(["ajaxStart", "ajaxStop", "ajaxComplete", "ajaxError", "ajaxSuccess", "ajaxSend"], function (e, t) {
                se.fn[t] = function (e) {
                    return this.on(t, e)
                }
            }),
            se._evalUrl = function (e) {
                return se.ajax({
                    url: e,
                    type: "GET",
                    dataType: "script",
                    async: !1,
                    global: !1,
                    "throws": !0
                })
            },
            se.fn.extend({
                wrapAll: function (e) {
                    if (se.isFunction(e)) return this.each(function (t) {
                        se(this).wrapAll(e.call(this, t))
                    });
                    if (this[0]) {
                        var t = se(e, this[0].ownerDocument).eq(0).clone(!0);
                        this[0].parentNode && t.insertBefore(this[0]),
                            t.map(function () {
                                for (var e = this; e.firstChild && 1 === e.firstChild.nodeType;) e = e.firstChild;
                                return e
                            }).append(this)
                    }
                    return this
                },
                wrapInner: function (e) {
                    return this.each(se.isFunction(e) ?
                        function (t) {
                            se(this).wrapInner(e.call(this, t))
                        } : function () {
                        var t = se(this),
                            i = t.contents();
                        i.length ? i.wrapAll(e) : t.append(e)
                    })
                },
                wrap: function (e) {
                    var t = se.isFunction(e);
                    return this.each(function (i) {
                        se(this).wrapAll(t ? e.call(this, i) : e)
                    })
                },
                unwrap: function () {
                    return this.parent().each(function () {
                        se.nodeName(this, "body") || se(this).replaceWith(this.childNodes)
                    }).end()
                }
            }),
            se.expr.filters.hidden = function (e) {
                return e.offsetWidth <= 0 && e.offsetHeight <= 0 || !ie.reliableHiddenOffsets() && "none" === (e.style && e.style.display || se.css(e, "display"))
            },
            se.expr.filters.visible = function (e) {
                return !se.expr.filters.hidden(e)
            };
        var Yt = /%20/g,
            Ut = /\[\]$/,
            Gt = /\r?\n/g,
            Xt = /^(?:submit|button|image|reset|file)$/i,
            Kt = /^(?:input|select|textarea|keygen)/i;
        se.param = function (e, t) {
            var i, n = [],
                s = function (e, t) {
                    t = se.isFunction(t) ? t() : null == t ? "" : t,
                        n[n.length] = encodeURIComponent(e) + "=" + encodeURIComponent(t)
                };
            if (void 0 === t && (t = se.ajaxSettings && se.ajaxSettings.traditional), se.isArray(e) || e.jquery && !se.isPlainObject(e)) se.each(e, function () {
                s(this.name, this.value)
            });
            else for (i in e) q(i, e[i], t, s);
            return n.join("&").replace(Yt, "+")
        },
            se.fn.extend({
                serialize: function () {
                    return se.param(this.serializeArray())
                },
                serializeArray: function () {
                    return this.map(function () {
                        var e = se.prop(this, "elements");
                        return e ? se.makeArray(e) : this
                    }).filter(function () {
                        var e = this.type;
                        return this.name && !se(this).is(":disabled") && Kt.test(this.nodeName) && !Xt.test(e) && (this.checked || !Pe.test(e))
                    }).map(function (e, t) {
                        var i = se(this).val();
                        return null == i ? null : se.isArray(i) ? se.map(i, function (e) {
                            return {
                                name: t.name,
                                value: e.replace(Gt, "\r\n")
                            }
                        }) : {
                            name: t.name,
                            value: i.replace(Gt, "\r\n")
                        }
                    }).get()
                }
            }),
            se.ajaxSettings.xhr = void 0 !== e.ActiveXObject ?
                function () {
                    return !this.isLocal && /^(get|post|head|put|delete|options)$/i.test(this.type) && V() || Y()
                } : V;
        var Qt = 0,
            Jt = {},
            Zt = se.ajaxSettings.xhr();
        e.ActiveXObject && se(e).on("unload", function () {
            for (var e in Jt) Jt[e](void 0, !0)
        }),
            ie.cors = !!Zt && "withCredentials" in Zt,
            Zt = ie.ajax = !!Zt,
        Zt && se.ajaxTransport(function (e) {
            if (!e.crossDomain || ie.cors) {
                var t;
                return {
                    send: function (i, n) {
                        var s, a = e.xhr(),
                            o = ++Qt;
                        if (a.open(e.type, e.url, e.async, e.username, e.password), e.xhrFields) for (s in e.xhrFields) a[s] = e.xhrFields[s];
                        e.mimeType && a.overrideMimeType && a.overrideMimeType(e.mimeType),
                        e.crossDomain || i["X-Requested-With"] || (i["X-Requested-With"] = "XMLHttpRequest");
                        for (s in i) void 0 !== i[s] && a.setRequestHeader(s, i[s] + "");
                        a.send(e.hasContent && e.data || null),
                            t = function (i, s) {
                                var r, l, c;
                                if (t && (s || 4 === a.readyState)) if (delete Jt[o], t = void 0, a.onreadystatechange = se.noop, s) 4 !== a.readyState && a.abort();
                                else {
                                    c = {},
                                        r = a.status,
                                    "string" == typeof a.responseText && (c.text = a.responseText);
                                    try {
                                        l = a.statusText
                                    } catch (d) {
                                        l = ""
                                    }
                                    r || !e.isLocal || e.crossDomain ? 1223 === r && (r = 204) : r = c.text ? 200 : 404
                                }
                                c && n(r, l, c, a.getAllResponseHeaders())
                            },
                            e.async ? 4 === a.readyState ? setTimeout(t) : a.onreadystatechange = Jt[o] = t : t()
                    },
                    abort: function () {
                        t && t(void 0, !0)
                    }
                }
            }
        }),
            se.ajaxSetup({
                accepts: {
                    script: "text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"
                },
                contents: {
                    script: /(?:java|ecma)script/
                },
                converters: {
                    "text script": function (e) {
                        return se.globalEval(e),
                            e
                    }
                }
            }),
            se.ajaxPrefilter("script", function (e) {
                void 0 === e.cache && (e.cache = !1),
                e.crossDomain && (e.type = "GET", e.global = !1)
            }),
            se.ajaxTransport("script", function (e) {
                if (e.crossDomain) {
                    var t, i = fe.head || se("head")[0] || fe.documentElement;
                    return {
                        send: function (n, s) {
                            t = fe.createElement("script"),
                                t.async = !0,
                            e.scriptCharset && (t.charset = e.scriptCharset),
                                t.src = e.url,
                                t.onload = t.onreadystatechange = function (e, i) {
                                    (i || !t.readyState || /loaded|complete/.test(t.readyState)) && (t.onload = t.onreadystatechange = null, t.parentNode && t.parentNode.removeChild(t), t = null, i || s(200, "success"))
                                },
                                i.insertBefore(t, i.firstChild)
                        },
                        abort: function () {
                            t && t.onload(void 0, !0)
                        }
                    }
                }
            });
        var ei = [],
            ti = /(=)\?(?=&|$)|\?\?/;
        se.ajaxSetup({
            jsonp: "callback",
            jsonpCallback: function () {
                var e = ei.pop() || se.expando + "_" + Pt++;
                return this[e] = !0,
                    e
            }
        }),
            se.ajaxPrefilter("json jsonp", function (t, i, n) {
                var s, a, o, r = t.jsonp !== !1 && (ti.test(t.url) ? "url" : "string" == typeof t.data && !(t.contentType || "").indexOf("application/x-www-form-urlencoded") && ti.test(t.data) && "data");
                return r || "jsonp" === t.dataTypes[0] ? (s = t.jsonpCallback = se.isFunction(t.jsonpCallback) ? t.jsonpCallback() : t.jsonpCallback, r ? t[r] = t[r].replace(ti, "$1" + s) : t.jsonp !== !1 && (t.url += (Mt.test(t.url) ? "&" : "?") + t.jsonp + "=" + s), t.converters["script json"] = function () {
                    return o || se.error(s + " was not called"),
                        o[0]
                }, t.dataTypes[0] = "json", a = e[s], e[s] = function () {
                    o = arguments
                }, n.always(function () {
                    e[s] = a,
                    t[s] && (t.jsonpCallback = i.jsonpCallback, ei.push(s)),
                    o && se.isFunction(a) && a(o[0]),
                        o = a = void 0
                }), "script") : void 0
            }),
            se.parseHTML = function (e, t, i) {
                if (!e || "string" != typeof e) return null;
                "boolean" == typeof t && (i = t, t = !1),
                    t = t || fe;
                var n = he.exec(e),
                    s = !i && [];
                return n ? [t.createElement(n[1])] : (n = se.buildFragment([e], t, s), s && s.length && se(s).remove(), se.merge([], n.childNodes))
            };
        var ii = se.fn.load;
        se.fn.load = function (e, t, i) {
            if ("string" != typeof e && ii) return ii.apply(this, arguments);
            var n, s, a, o = this,
                r = e.indexOf(" ");
            return r >= 0 && (n = se.trim(e.slice(r, e.length)), e = e.slice(0, r)),
                se.isFunction(t) ? (i = t, t = void 0) : t && "object" == typeof t && (a = "POST"),
            o.length > 0 && se.ajax({
                url: e,
                type: a,
                dataType: "html",
                data: t
            }).done(function (e) {
                s = arguments,
                    o.html(n ? se("<div>").append(se.parseHTML(e)).find(n) : e)
            }).complete(i &&
                function (e, t) {
                    o.each(i, s || [e.responseText, t, e])
                }),
                this
        },
            se.expr.filters.animated = function (e) {
                return se.grep(se.timers, function (t) {
                    return e === t.elem
                }).length
            };
        var ni = e.document.documentElement;
        se.offset = {
            setOffset: function (e, t, i) {
                var n, s, a, o, r, l, c, d = se.css(e, "position"),
                    h = se(e),
                    u = {};
                "static" === d && (e.style.position = "relative"),
                    r = h.offset(),
                    a = se.css(e, "top"),
                    l = se.css(e, "left"),
                    c = ("absolute" === d || "fixed" === d) && se.inArray("auto", [a, l]) > -1,
                    c ? (n = h.position(), o = n.top, s = n.left) : (o = parseFloat(a) || 0, s = parseFloat(l) || 0),
                se.isFunction(t) && (t = t.call(e, i, r)),
                null != t.top && (u.top = t.top - r.top + o),
                null != t.left && (u.left = t.left - r.left + s),
                    "using" in t ? t.using.call(e, u) : h.css(u)
            }
        },
            se.fn.extend({
                offset: function (e) {
                    if (arguments.length) return void 0 === e ? this : this.each(function (t) {
                        se.offset.setOffset(this, e, t)
                    });
                    var t, i, n = {
                            top: 0,
                            left: 0
                        },
                        s = this[0],
                        a = s && s.ownerDocument;
                    return a ? (t = a.documentElement, se.contains(t, s) ? (typeof s.getBoundingClientRect !== Ce && (n = s.getBoundingClientRect()), i = U(a), {
                        top: n.top + (i.pageYOffset || t.scrollTop) - (t.clientTop || 0),
                        left: n.left + (i.pageXOffset || t.scrollLeft) - (t.clientLeft || 0)
                    }) : n) : void 0
                },
                position: function () {
                    if (this[0]) {
                        var e, t, i = {
                                top: 0,
                                left: 0
                            },
                            n = this[0];
                        return "fixed" === se.css(n, "position") ? t = n.getBoundingClientRect() : (e = this.offsetParent(), t = this.offset(), se.nodeName(e[0], "html") || (i = e.offset()), i.top += se.css(e[0], "borderTopWidth", !0), i.left += se.css(e[0], "borderLeftWidth", !0)),
                        {
                            top: t.top - i.top - se.css(n, "marginTop", !0),
                            left: t.left - i.left - se.css(n, "marginLeft", !0)
                        }
                    }
                },
                offsetParent: function () {
                    return this.map(function () {
                        for (var e = this.offsetParent || ni; e && !se.nodeName(e, "html") && "static" === se.css(e, "position");) e = e.offsetParent;
                        return e || ni
                    })
                }
            }),
            se.each({
                scrollLeft: "pageXOffset",
                scrollTop: "pageYOffset"
            }, function (e, t) {
                var i = /Y/.test(t);
                se.fn[e] = function (n) {
                    return Ie(this, function (e, n, s) {
                        var a = U(e);
                        return void 0 === s ? a ? t in a ? a[t] : a.document.documentElement[n] : e[n] : void(a ? a.scrollTo(i ? se(a).scrollLeft() : s, i ? s : se(a).scrollTop()) : e[n] = s)
                    }, e, n, arguments.length, null)
                }
            }),
            se.each(["top", "left"], function (e, t) {
                se.cssHooks[t] = S(ie.pixelPosition, function (e, i) {
                    return i ? (i = tt(e, t), nt.test(i) ? se(e).position()[t] + "px" : i) : void 0
                })
            }),
            se.each({
                Height: "height",
                Width: "width"
            }, function (e, t) {
                se.each({
                    padding: "inner" + e,
                    content: t,
                    "": "outer" + e
                }, function (i, n) {
                    se.fn[n] = function (n, s) {
                        var a = arguments.length && (i || "boolean" != typeof n),
                            o = i || (n === !0 || s === !0 ? "margin" : "border");
                        return Ie(this, function (t, i, n) {
                            var s;
                            return se.isWindow(t) ? t.document.documentElement["client" + e] : 9 === t.nodeType ? (s = t.documentElement, Math.max(t.body["scroll" + e], s["scroll" + e], t.body["offset" + e], s["offset" + e], s["client" + e])) : void 0 === n ? se.css(t, i, o) : se.style(t, i, n, o)
                        }, t, a ? n : void 0, a, null)
                    }
                })
            }),
            se.fn.size = function () {
                return this.length
            },
            se.fn.andSelf = se.fn.addBack,
        "function" == typeof define && define.amd && define("jquery", [], function () {
            return se
        });
        var si = e.jQuery,
            ai = e.$;
        return se.noConflict = function (t) {
            return e.$ === se && (e.$ = ai),
            t && e.jQuery === se && (e.jQuery = si),
                se
        },
        typeof t === Ce && (e.jQuery = e.$ = se),
            se
    }),


    function (e) {
        "use strict";
        var t = null,
            i = !1,
            n = {
                url: "",
                pager: "1",
                size: "10",
                params: "",
                template: "",
                type: "post",
                format: "json",
                offset: "100"
            },
            s = 0,
            a = {
                init: function (i) {
                    t = e(this),
                    i && e.extend(n, i),
                        a.getData(),
                        e(window).scroll(a.checkScroll);
                    var o = {};
                    return o.getPager = function () {
                        return n.pager
                    },
                        o.reload = function () {
                            a.getData()
                        },
                        o.onload = function (e) {
                            e && (n.params = e),
                                n.pager = 1,
                                a.getData()
                        },
                        o.getTotalPage = function () {
                            return s
                        },
                        o
                },
                getParam: function () {
                    var e = "page=" + n.pager + "&size=" + n.size;
                    return e = e + "&" + n.params
                },
                getData: function () {
                    i = !0;
                    var o = n.url.indexOf("?") > 0 ? "&" : "?",
                        r = n.url + o + "ts=" + Math.random();
                    if (e.ajax({
                            url: r,
                            type: n.type,
                            dataType: n.format,
                            data: a.getParam(),
                            async: !1,
                            success: function (e) {
                                s = "undefind" == e.totalPage ? 0 : e.totalPage,
                                    template.config("openTag", "<%"),
                                    template.config("closeTag", "%>");
                                var a = template(n.template, e);
                                n.pager > 1 ? t.append(a) : t.html(a),
                                    n.pager++,
                                    i = !1
                            }
                        }), "j-product" == n.template) {
                        new Swiper(".j-g-s-p-con", {
                            scrollbarHide: !0,
                            slidesPerView: "auto",
                            centeredSlides: !1,
                            grabCursor: !0
                        })
                    }
                },
                checkScroll: function () {
                    var t = e(window).scrollTop() + parseInt(n.offset),
                        o = e(document).height() - e(window).height();
                    t >= o && n.pager <= s && 0 == i && a.getData()
                }
            };
        e.fn.infinite = function (t) {
            return a[t] ? a[t].apply(this, Array.prototype.slice.call(arguments, 1)) : "object" != typeof t && t ? void e.error("Method " + t + " does not exist!") : a.init.apply(this, arguments)
        }
    }(jQuery),
    !
        function (e) {
            "use strict";
            var t = "";
            t = t ? t : document.scripts[document.scripts.length - 1].src.match(/[\s\S]*\//)[0];
            var i = document,
                n = "querySelectorAll",
                s = "getElementsByClassName",
                a = function (e) {
                    return i[n](e)
                };
            document.head.appendChild(function () {
                var e = i.createElement("link");
                return e.href = t + "need/layer.css",
                    e.type = "text/css",
                    e.rel = "styleSheet",
                    e.id = "layermcss",
                    e
            }());
            var o = {
                type: 0,
                shade: !0,
                shadeClose: !0,
                fixed: !0,
                anim: !0
            };
            e.ready = {
                extend: function (e) {
                    var t = JSON.parse(JSON.stringify(o));
                    for (var i in e) t[i] = e[i];
                    return t
                },
                timer: {},
                end: {}
            },
                ready.touch = function (e, t) {
                    var i;
                    e.addEventListener("touchmove", function () {
                        i = !0
                    }, !1),
                        e.addEventListener("touchend", function (e) {
                            e.preventDefault(),
                            i || t.call(this, e),
                                i = !1
                        }, !1)
                };
            var r = 0,
                l = ["layermbox"],
                c = function (e) {
                    var t = this;
                    t.config = ready.extend(e),
                        t.view()
                };
            c.prototype.view = function () {
                var e = this,
                    t = e.config,
                    n = i.createElement("div");
                e.id = n.id = l[0] + r,
                    n.setAttribute("class", l[0] + " " + l[0] + (t.type || 0)),
                    n.setAttribute("index", r);
                var o = function () {
                        var e = "object" == typeof t.title;
                        return t.title ? '<h3 style="' + (e ? t.title[1] : "") + '">' + (e ? t.title[0] : t.title) + '</h3><button class="layermend"></button>' : ""
                    }(),
                    c = function () {
                        var e, i = (t.btn || []).length;
                        return 0 !== i && t.btn ? (e = '<span type="1">' + t.btn[0] + "</span>", 2 === i && (e = '<span type="0">' + t.btn[1] + "</span>" + e), '<div class="layermbtn">' + e + "</div>") : ""
                    }();
                if (t.fixed || (t.top = t.hasOwnProperty("top") ? t.top : 100, t.style = t.style || "", t.style += " top:" + (i.body.scrollTop + t.top) + "px"), 2 === t.type && (t.content = '<i></i><i class="laymloadtwo"></i><i></i><div>' + (t.content || "") + "</div>"), n.innerHTML = (t.shade ? "<div " + ("string" == typeof t.shade ? 'style="' + t.shade + '"' : "") + ' class="laymshade"></div>' : "") + '<div class="layermmain" ' + (t.fixed ? "" : 'style="position:static;"') + '><div class="section"><div class="layermchild ' + (t.className ? t.className : "") + " " + (t.type || t.shade ? "" : "layermborder ") + (t.anim ? "layermanim" : "") + '" ' + (t.style ? 'style="' + t.style + '"' : "") + ">" + o + '<div class="layermcont">' + t.content + "</div>" + c + "</div></div></div>", !t.type || 2 === t.type) {
                    var h = i[s](l[0] + t.type),
                        u = h.length;
                    u >= 1 && d.close(h[0].getAttribute("index"))
                }
                document.body.appendChild(n);
                var p = e.elem = a("#" + e.id)[0];
                t.success && t.success(p),
                    e.index = r++,
                    e.action(t, p)
            },
                c.prototype.action = function (e, t) {
                    var i = this;
                    if (e.time && (ready.timer[i.index] = setTimeout(function () {
                            d.close(i.index)
                        }, 1e3 * e.time)), e.title) {
                        var n = t[s]("layermend")[0],
                            a = function () {
                                e.cancel && e.cancel(),
                                    d.close(i.index)
                            };
                        ready.touch(n, a),
                            n.onclick = a
                    }
                    var o = function () {
                        var t = this.getAttribute("type");
                        0 == t ? (e.no && e.no(), d.close(i.index)) : e.yes ? e.yes(i.index) : d.close(i.index)
                    };
                    if (e.btn) for (var r = t[s]("layermbtn")[0].children, l = r.length, c = 0; l > c; c++) ready.touch(r[c], o),
                        r[c].onclick = o;
                    if (e.shade && e.shadeClose) {
                        var h = t[s]("laymshade")[0];
                        ready.touch(h, function () {
                            d.close(i.index, e.end)
                        }),
                            h.onclick = function () {
                                d.close(i.index, e.end)
                            }
                    }
                    e.end && (ready.end[i.index] = e.end)
                };
            var d = {
                v: "1.6",
                index: r,
                open: function (e) {
                    var t = new c(e || {});
                    return t.index
                },
                close: function (e) {
                    var t = a("#" + l[0] + e)[0];
                    t && (t.innerHTML = "", i.body.removeChild(t), clearTimeout(ready.timer[e]), delete ready.timer[e], "function" == typeof ready.end[e] && ready.end[e](), delete ready.end[e])
                },
                closeAll: function () {
                    for (var e = i[s](l[0]), t = 0, n = e.length; n > t; t++) d.close(0 | e[0].getAttribute("index"))
                }
            };
            "function" == typeof define ? define(function () {
                return d
            }) : e.layer = d
        }(window),
    !
        function () {
            function e(e) {
                return e.replace(b, "").replace(w, ",").replace(_, "").replace(x, "").replace(C, "").split(k)
            }

            function t(e) {
                return "'" + e.replace(/('|\\)/g, "\\$1").replace(/\r/g, "\\r").replace(/\n/g, "\\n") + "'"
            }

            function i(i, n) {
                function s(e) {
                    return u += e.split(/\n/).length - 1,
                    d && (e = e.replace(/\s+/g, " ").replace(/<!--[\w\W]*?-->/g, "")),
                    e && (e = y[1] + t(e) + y[2] + "\n"),
                        e
                }

                function a(t) {
                    var i = u;
                    if (c ? t = c(t, n) : o && (t = t.replace(/\n/g, function () {
                            return u++,
                            "$line=" + u + ";"
                        })), 0 === t.indexOf("=")) {
                        var s = h && !/^=[=#]/.test(t);
                        if (t = t.replace(/^=[=#]?|[\s;]*$/g, ""), s) {
                            var a = t.replace(/\s*\([^\)]+\)/, "");
                            p[a] || /^(include|print)$/.test(a) || (t = "$escape(" + t + ")")
                        } else t = "$string(" + t + ")";
                        t = y[1] + t + y[2]
                    }
                    return o && (t = "$line=" + i + ";" + t),
                        v(e(t), function (e) {
                            if (e && !m[e]) {
                                var t;
                                t = "print" === e ? w : "include" === e ? _ : p[e] ? "$utils." + e : f[e] ? "$helpers." + e : "$data." + e,
                                    x += e + "=" + t + ",",
                                    m[e] = !0
                            }
                        }),
                    t + "\n"
                }

                var o = n.debug,
                    r = n.openTag,
                    l = n.closeTag,
                    c = n.parser,
                    d = n.compress,
                    h = n.escape,
                    u = 1,
                    m = {
                        $data: 1,
                        $filename: 1,
                        $utils: 1,
                        $helpers: 1,
                        $out: 1,
                        $line: 1
                    },
                    g = "".trim,
                    y = g ? ["$out='';", "$out+=", ";", "$out"] : ["$out=[];", "$out.push(", ");", "$out.join('')"],
                    b = g ? "$out+=text;return $out;" : "$out.push(text);",
                    w = "function(){var text=''.concat.apply('',arguments);" + b + "}",
                    _ = "function(filename,data){data=data||$data;var text=$utils.$include(filename,data,$filename);" + b + "}",
                    x = "'use strict';var $utils=this,$helpers=$utils.$helpers," + (o ? "$line=0," : ""),
                    C = y[0],
                    k = "return new String(" + y[3] + ");";
                v(i.split(r), function (e) {
                    e = e.split(l);
                    var t = e[0],
                        i = e[1];
                    1 === e.length ? C += s(t) : (C += a(t), i && (C += s(i)))
                });
                var T = x + C + k;
                o && (T = "try{" + T + "}catch(e){throw {filename:$filename,name:'Render Error',message:e.message,line:$line,source:" + t(i) + ".split(/\\n/)[$line-1].replace(/^\\s+/,'')};}");
                try {
                    var S = new Function("$data", "$filename", T);
                    return S.prototype = p,
                        S
                } catch (D) {
                    throw D.temp = "function anonymous($data,$filename) {" + T + "}",
                        D
                }
            }

            var n = function (e, t) {
                return "string" == typeof t ? g(t, {
                    filename: e
                }) : o(e, t)
            };
            n.version = "3.0.0",
                n.config = function (e, t) {
                    s[e] = t
                };
            var s = n.defaults = {
                    openTag: "<%",
                    closeTag: "%>",
                    escape: !0,
                    cache: !0,
                    compress: !1,
                    parser: null
                },
                a = n.cache = {};
            n.render = function (e, t) {
                return g(e, t)
            };
            var o = n.renderFile = function (e, t) {
                var i = n.get(e) || m({
                        filename: e,
                        name: "Render Error",
                        message: "Template not found"
                    });
                return t ? i(t) : i
            };
            n.get = function (e) {
                var t;
                if (a[e]) t = a[e];
                else if ("object" == typeof document) {
                    var i = document.getElementById(e);
                    if (i) {
                        var n = (i.value || i.innerHTML).replace(/^\s*|\s*$/g, "");
                        t = g(n, {
                            filename: e
                        })
                    }
                }
                return t
            };
            var r = function (e, t) {
                    return "string" != typeof e && (t = typeof e, "number" === t ? e += "" : e = "function" === t ? r(e.call(e)) : ""),
                        e
                },
                l = {
                    "<": "&#60;",
                    ">": "&#62;",
                    '"': "&#34;",
                    "'": "&#39;",
                    "&": "&#38;"
                },
                c = function (e) {
                    return l[e]
                },
                d = function (e) {
                    return r(e).replace(/&(?![\w#]+;)|[<>"']/g, c)
                },
                h = Array.isArray ||
                    function (e) {
                        return "[object Array]" === {}.toString.call(e)
                    },
                u = function (e, t) {
                    var i, n;
                    if (h(e)) for (i = 0, n = e.length; n > i; i++) t.call(e, e[i], i, e);
                    else for (i in e) t.call(e, e[i], i)
                },
                p = n.utils = {
                    $helpers: {},
                    $include: o,
                    $string: r,
                    $escape: d,
                    $each: u
                };
            n.helper = function (e, t) {
                f[e] = t
            };
            var f = n.helpers = p.$helpers;
            n.onerror = function (e) {
                var t = "Template Error\n\n";
                for (var i in e) t += "<" + i + ">\n" + e[i] + "\n\n";
                "object" == typeof console && void 0
            };
            var m = function (e) {
                    return n.onerror(e),


                        function () {
                            return "{Template Error}"
                        }
                },
                g = n.compile = function (e, t) {
                    function n(i) {
                        try {
                            return new l(i, r) + ""
                        } catch (n) {
                            return t.debug ? m(n)() : (t.debug = !0, g(e, t)(i))
                        }
                    }

                    t = t || {};
                    for (var o in s) void 0 === t[o] && (t[o] = s[o]);
                    var r = t.filename;
                    try {
                        var l = i(e, t)
                    } catch (c) {
                        return c.filename = r || "anonymous",
                            c.name = "Syntax Error",
                            m(c)
                    }
                    return n.prototype = l.prototype,
                        n.toString = function () {
                            return l.toString()
                        },
                    r && t.cache && (a[r] = n),
                        n
                },
                v = p.$each,
                y = "break,case,catch,continue,debugger,default,delete,do,else,false,finally,for,function,if,in,instanceof,new,null,return,switch,this,throw,true,try,typeof,var,void,while,with,abstract,boolean,byte,char,class,const,double,enum,export,extends,final,float,goto,implements,import,int,interface,long,native,package,private,protected,public,short,static,super,synchronized,throws,transient,volatile,arguments,let,yield,undefined",
                b = /\/\*[\w\W]*?\*\/|\/\/[^\n]*\n|\/\/[^\n]*$|"(?:[^"\\]|\\[\w\W])*"|'(?:[^'\\]|\\[\w\W])*'|\s*\.\s*[$\w\.]+/g,
                w = /[^\w$]+/g,
                _ = new RegExp(["\\b" + y.replace(/,/g, "\\b|\\b") + "\\b"].join("|"), "g"),
                x = /^\d[^,]*|,\d[^,]*/g,
                C = /^,+|,+$/g,
                k = /^$|,+/;
            s.openTag = "{{",
                s.closeTag = "}}";
            var T = function (e, t) {
                var i = t.split(":"),
                    n = i.shift(),
                    s = i.join(":") || "";
                return s && (s = ", " + s),
                "$helpers." + n + "(" + e + s + ")"
            };
            s.parser = function (e) {
                e = e.replace(/^\s/, "");
                var t = e.split(" "),
                    i = t.shift(),
                    s = t.join(" ");
                switch (i) {
                    case "if":
                        e = "if(" + s + "){";
                        break;
                    case "else":
                        t = "if" === t.shift() ? " if(" + t.join(" ") + ")" : "",
                            e = "}else" + t + "{";
                        break;
                    case "/if":
                        e = "}";
                        break;
                    case "each":
                        var a = t[0] || "$data",
                            o = t[1] || "as",
                            r = t[2] || "$value",
                            l = t[3] || "$index",
                            c = r + "," + l;
                        "as" !== o && (a = "[]"),
                            e = "$each(" + a + ",function(" + c + "){";
                        break;
                    case "/each":
                        e = "});";
                        break;
                    case "echo":
                        e = "print(" + s + ");";
                        break;
                    case "print":
                    case "include":
                        e = i + "(" + t.join(",") + ");";
                        break;
                    default:
                        if (/^\s*\|\s*[\w\$]/.test(s)) {
                            var d = !0;
                            0 === e.indexOf("#") && (e = e.substr(1), d = !1);
                            for (var h = 0, u = e.split("|"), p = u.length, f = u[h++]; p > h; h++) f = T(f, u[h]);
                            e = (d ? "=" : "=#") + f
                        } else e = n.helpers[i] ? "=#" + i + "(" + t.join(",") + ");" : "=" + e
                }
                return e
            },
                "function" == typeof define ? define(function () {
                    return n
                }) : "undefined" != typeof exports ? module.exports = n : this.template = n
        }();
var auctionDate = 0,
    _GMTEndTime = 0,
    showTime = "leftTime",
    _day = "day",
    _hour = "hour",
    _minute = "minute",
    _second = "second",
    _end = "end",
    cur_date = new Date,
    startTime = cur_date.getTime(),
    Temp, timerID = null,
    timerRunning = !1,
    timerID = null,
    timerRunning = !1;
!
    function ($) {
        "use strict";
        var escape = /["\\\x00-\x1f\x7f-\x9f]/g,
            meta = {
                "\b": "\\b",
                "\t": "\\t",
                "\n": "\\n",
                "\f": "\\f",
                "\r": "\\r",
                '"': '\\"',
                "\\": "\\\\"
            },
            hasOwn = Object.prototype.hasOwnProperty;
        $.toJSON = "object" == typeof JSON && JSON.stringify ? JSON.stringify : function (e) {
            if (null === e) return "null";
            var t, i, n, s, a = $.type(e);
            if ("undefined" !== a) {
                if ("number" === a || "boolean" === a) return String(e);
                if ("string" === a) return $.quoteString(e);
                if ("function" == typeof e.toJSON) return $.toJSON(e.toJSON());
                if ("date" === a) {
                    var o = e.getUTCMonth() + 1,
                        r = e.getUTCDate(),
                        l = e.getUTCFullYear(),
                        c = e.getUTCHours(),
                        d = e.getUTCMinutes(),
                        h = e.getUTCSeconds(),
                        u = e.getUTCMilliseconds();
                    return o < 10 && (o = "0" + o),
                    r < 10 && (r = "0" + r),
                    c < 10 && (c = "0" + c),
                    d < 10 && (d = "0" + d),
                    h < 10 && (h = "0" + h),
                    u < 100 && (u = "0" + u),
                    u < 10 && (u = "0" + u),
                    '"' + l + "-" + o + "-" + r + "T" + c + ":" + d + ":" + h + "." + u + 'Z"'
                }
                if (t = [], $.isArray(e)) {
                    for (i = 0; i < e.length; i++) t.push($.toJSON(e[i]) || "null");
                    return "[" + t.join(",") + "]"
                }
                if ("object" == typeof e) {
                    for (i in e) if (hasOwn.call(e, i)) {
                        if (a = typeof i, "number" === a) n = '"' + i + '"';
                        else {
                            if ("string" !== a) continue;
                            n = $.quoteString(i)
                        }
                        a = typeof e[i],
                        "function" !== a && "undefined" !== a && (s = $.toJSON(e[i]), t.push(n + ":" + s))
                    }
                    return "{" + t.join(",") + "}"
                }
            }
        },
            $.evalJSON = "object" == typeof JSON && JSON.parse ? JSON.parse : function (str) {
                return eval("(" + str + ")")
            },
            $.secureEvalJSON = "object" == typeof JSON && JSON.parse ? JSON.parse : function (str) {
                var filtered = str.replace(/\\["\\\/bfnrtu]/g, "@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:\s*\[)+/g, "");
                if (/^[\],:{}\s]*$/.test(filtered)) return eval("(" + str + ")");
                throw new SyntaxError("Error parsing JSON, source is not valid.")
            },
            $.quoteString = function (e) {
                return e.match(escape) ? '"' + e.replace(escape, function (e) {
                    var t = meta[e];
                    return "string" == typeof t ? t : (t = e.charCodeAt(), "\\u00" + Math.floor(t / 16).toString(16) + (t % 16).toString(16))
                }) + '"' : '"' + e + '"'
            }
    }(jQuery),


    function (e, t, i) {
        function n(t, i) {
            var n = (e(window).width() - t.outerWidth()) / 2,
                s = (e(window).height() - t.outerHeight()) / 2,
                s = (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + (s > 0 ? s : 0);
            t.css({
                left: n
            }).animate({
                top: s
            }, {
                duration: i,
                queue: !1
            })
        }

        function s() {
            return 0 === e("#Validform_msg").length && (o = e('<div id="Validform_msg"><div class="Validform_title">' + l.tit + '<a class="Validform_close" href="javascript:void(0);">&chi;</a></div><div class="Validform_info"></div><div class="iframe"><iframe frameborder="0" scrolling="no" height="100%" width="100%"></iframe></div></div>').appendTo("body"), o.find("a.Validform_close").click(function () {
                    return o.hide(),
                        r = !0,
                    a && a.focus().addClass("Validform_error"),
                        !1
                }).focus(function () {
                    this.blur()
                }), void e(window).bind("scroll resize", function () {
                    !r && n(o, 400)
                }))
        }

        var a = null,
            o = null,
            r = !0,
            l = {
                tit: "提示信息",
                w: {
                    "*": "不能为空！",
                    "*6-16": "请填写6到16位任意字符！",
                    n: "请填写数字！",
                    "n6-16": "请填写6到16位数字！",
                    s: "不能输入特殊字符！",
                    "s6-18": "请填写6到18位字符！",
                    p: "请填写邮政编码！",
                    m: "请填写手机号码！",
                    e: "邮箱地址格式不对！",
                    url: "请填写网址！"
                },
                def: "请填写正确信息！",
                undef: "datatype未定义！",
                reck: "两次输入的内容不一致！",
                r: "通过信息验证！",
                c: "正在检测信息…",
                s: "请{填写|选择}{0|信息}！",
                v: "所填信息没有经过验证，请稍后…",
                p: "正在提交数据…"
            };
        e.Tipmsg = l;
        var c = function (t, n, a) {
            var n = e.extend({}, c.defaults, n);
            n.datatype && e.extend(c.util.dataType, n.datatype);
            var o = this;
            return o.tipmsg = {
                w: {}
            },
                o.forms = t,
                o.objects = [],
            a !== !0 && (t.each(function () {
                if ("inited" == this.validform_inited) return !0;
                this.validform_inited = "inited";
                var t = this;
                t.settings = e.extend({}, n);
                var s = e(t);
                t.validform_status = "normal",
                    s.data("tipmsg", o.tipmsg),
                    s.delegate("[datatype]", "blur", function () {
                        var e = arguments[1];
                        c.util.check.call(this, s, e)
                    }),
                    s.delegate(":text", "keypress", function (e) {
                        13 == e.keyCode && 0 == s.find(":submit").length && s.submit()
                    }),
                    c.util.enhance.call(s, t.settings.tiptype, t.settings.usePlugin, t.settings.tipSweep),
                t.settings.btnSubmit && s.find(t.settings.btnSubmit).bind("click", function () {
                    return s.trigger("submit"),
                        !1
                }),
                    s.submit(function () {
                        var e = c.util.submitForm.call(s, t.settings);
                        return e === i && (e = !0),
                            e
                    }),
                    s.find("[type='reset']").add(s.find(t.settings.btnReset)).bind("click", function () {
                        c.util.resetForm.call(s)
                    })
            }), void((1 == n.tiptype || (2 == n.tiptype || 3 == n.tiptype) && n.ajaxPost) && s()))
        };
        c.defaults = {
            tiptype: 1,
            tipSweep: !1,
            showAllError: !1,
            postonce: !1,
            ajaxPost: !1
        },
            c.util = {
                dataType: {
                    "*": /[\w\W]+/,
                    "*6-16": /^[\w\W]{6,16}$/,
                    n: /^\d+$/,
                    "n6-16": /^\d{6,16}$/,
                    s: /^[\u4E00-\u9FA5\uf900-\ufa2d\w\.\s]+$/,
                    "s6-18": /^[\u4E00-\u9FA5\uf900-\ufa2d\w\.\s]{6,18}$/,
                    p: /^[0-9]{6}$/,
                    m: /^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}$|17[0-9]{9}$|18[0-9]{9}$/,
                    e: /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/,
                    url: /^(\w+:\/\/)?\w+(\.\w+)+.*$/
                },
                toString: Object.prototype.toString,
                isEmpty: function (t) {
                    return "" === t || t === e.trim(this.attr("tip"))
                },
                getValue: function (t) {
                    var n, s = this;
                    return t.is(":radio") ? (n = s.find(":radio[name='" + t.attr("name") + "']:checked").val(), n = n === i ? "" : n) : t.is(":checkbox") ? (n = "", s.find(":checkbox[name='" + t.attr("name") + "']:checked").each(function () {
                        n += e(this).val() + ","
                    }), n = n === i ? "" : n) : n = t.val(),
                        n = e.trim(n),
                        c.util.isEmpty.call(t, n) ? "" : n
                },
                enhance: function (t, i, n, s) {
                    var a = this;
                    a.find("[datatype]").each(function () {
                        2 == t ? 0 == e(this).parent().next().find(".Validform_checktip").length && (e(this).parent().next().append("<span class='Validform_checktip' />"), e(this).siblings(".Validform_checktip").remove()) : 3 != t && 4 != t || 0 == e(this).siblings(".Validform_checktip").length && (e(this).parent().append("<span class='Validform_checktip' />"), e(this).parent().next().find(".Validform_checktip").remove())
                    }),
                        a.find("input[recheck]").each(function () {
                            if ("inited" == this.validform_inited) return !0;
                            this.validform_inited = "inited";
                            var t = e(this),
                                i = a.find("input[name='" + e(this).attr("recheck") + "']");
                            i.bind("keyup", function () {
                                if (i.val() == t.val() && "" != i.val()) {
                                    if (i.attr("tip") && i.attr("tip") == i.val()) return !1;
                                    t.trigger("blur")
                                }
                            }).bind("blur", function () {
                                if (i.val() != t.val() && "" != t.val()) {
                                    if (t.attr("tip") && t.attr("tip") == t.val()) return !1;
                                    t.trigger("blur")
                                }
                            })
                        }),
                        a.find("[tip]").each(function () {
                            if ("inited" == this.validform_inited) return !0;
                            this.validform_inited = "inited";
                            var t = e(this).attr("tip"),
                                i = e(this).attr("altercss");
                            e(this).focus(function () {
                                e(this).val() == t && (e(this).val(""), i && e(this).removeClass(i))
                            }).blur(function () {
                                "" === e.trim(e(this).val()) && (e(this).val(t), i && e(this).addClass(i))
                            })
                        }),
                        a.find(":checkbox[datatype],:radio[datatype]").each(function () {
                            if ("inited" == this.validform_inited) return !0;
                            this.validform_inited = "inited";
                            var t = e(this),
                                i = t.attr("name");
                            a.find("[name='" + i + "']").filter(":checkbox,:radio").bind("click", function () {
                                setTimeout(function () {
                                    t.trigger("blur")
                                }, 0)
                            })
                        }),
                        a.find("select[datatype][multiple]").bind("click", function () {
                            var t = e(this);
                            setTimeout(function () {
                                t.trigger("blur")
                            }, 0)
                        }),
                        c.util.usePlugin.call(a, i, t, n, s)
                },
                usePlugin: function (t, i, n, s) {
                    var a = this,
                        t = t || {};
                    if (a.find("input[plugin='swfupload']").length && "undefined" != typeof swfuploadhandler) {
                        var o = {
                            custom_settings: {
                                form: a,
                                showmsg: function (e, t, s) {
                                    c.util.showmsg.call(a, e, i, {
                                        obj: a.find("input[plugin='swfupload']"),
                                        type: t,
                                        sweep: n
                                    })
                                }
                            }
                        };
                        o = e.extend(!0, {}, t.swfupload, o),
                            a.find("input[plugin='swfupload']").each(function (t) {
                                return "inited" == this.validform_inited || (this.validform_inited = "inited", e(this).val(""), void swfuploadhandler.init(o, t))
                            })
                    }
                    if (a.find("input[plugin='datepicker']").length && e.fn.datePicker && (t.datepicker = t.datepicker || {}, t.datepicker.format && (Date.format = t.datepicker.format, delete t.datepicker.format), t.datepicker.firstDayOfWeek && (Date.firstDayOfWeek = t.datepicker.firstDayOfWeek, delete t.datepicker.firstDayOfWeek), a.find("input[plugin='datepicker']").each(function (i) {
                            return "inited" == this.validform_inited || (this.validform_inited = "inited", t.datepicker.callback && e(this).bind("dateSelected", function () {
                                    var i = new Date(e.event._dpCache[this._dpId].getSelected()[0]).asString(Date.format);
                                    t.datepicker.callback(i, this)
                                }), void e(this).datePicker(t.datepicker))
                        })), a.find("input[plugin*='passwordStrength']").length && e.fn.passwordStrength && (t.passwordstrength = t.passwordstrength || {}, t.passwordstrength.showmsg = function (e, t, s) {
                            c.util.showmsg.call(a, t, i, {
                                obj: e,
                                type: s,
                                sweep: n
                            })
                        }, a.find("input[plugin='passwordStrength']").each(function (i) {
                            return "inited" == this.validform_inited || (this.validform_inited = "inited", void e(this).passwordStrength(t.passwordstrength))
                        })), "addRule" != s && t.jqtransform && e.fn.jqTransSelect) {
                        if ("true" == a[0].jqTransSelected) return;
                        a[0].jqTransSelected = "true";
                        var r = function (t) {
                                var i = e(".jqTransformSelectWrapper ul:visible");
                                i.each(function () {
                                    var i = e(this).parents(".jqTransformSelectWrapper:first").find("select").get(0);
                                    t && i.oLabel && i.oLabel.get(0) == t.get(0) || e(this).hide()
                                })
                            },
                            l = function (t) {
                                0 === e(t.target).parents(".jqTransformSelectWrapper").length && r(e(t.target))
                            },
                            d = function () {
                                e(document).mousedown(l)
                            };
                        t.jqtransform.selector ? (a.find(t.jqtransform.selector).filter('input:submit, input:reset, input[type="button"]').jqTransInputButton(), a.find(t.jqtransform.selector).filter("input:text, input:password").jqTransInputText(), a.find(t.jqtransform.selector).filter("input:checkbox").jqTransCheckBox(), a.find(t.jqtransform.selector).filter("input:radio").jqTransRadio(), a.find(t.jqtransform.selector).filter("textarea").jqTransTextarea(), a.find(t.jqtransform.selector).filter("select").length > 0 && (a.find(t.jqtransform.selector).filter("select").jqTransSelect(), d())) : a.jqTransform(),
                            a.find(".jqTransformSelectWrapper").find("li a").click(function () {
                                e(this).parents(".jqTransformSelectWrapper").find("select").trigger("blur")
                            })
                    }
                },
                getNullmsg: function (e) {
                    var t, i = this,
                        n = /[\u4E00-\u9FA5\uf900-\ufa2da-zA-Z\s]+/g,
                        s = e[0].settings.label || ".Validform_label";
                    if (s = i.siblings(s).eq(0).text() || i.siblings().find(s).eq(0).text() || i.parent().siblings(s).eq(0).text() || i.parent().siblings().find(s).eq(0).text(), s = s.replace(/\s(?![a-zA-Z])/g, "").match(n), s = s ? s.join("") : [""], n = /\{(.+)\|(.+)\}/, t = e.data("tipmsg").s || l.s, "" != s) {
                        if (t = t.replace(/\{0\|(.+)\}/, s), i.attr("recheck")) return t = t.replace(/\{(.+)\}/, ""),
                            i.attr("nullmsg", t),
                            t
                    } else t = i.is(":checkbox,:radio,select") ? t.replace(/\{0\|(.+)\}/, "") : t.replace(/\{0\|(.+)\}/, "$1");
                    return t = i.is(":checkbox,:radio,select") ? t.replace(n, "$2") : t.replace(n, "$1"),
                        i.attr("nullmsg", t),
                        t
                },
                getErrormsg: function (t, i, n) {
                    var s, a = /^(.+?)((\d+)-(\d+))?$/,
                        o = /^(.+?)(\d+)-(\d+)$/,
                        r = /(.*?)\d+(.+?)\d+(.*)/,
                        c = i.match(a);
                    if ("recheck" == n) return s = t.data("tipmsg").reck || l.reck;
                    var d = e.extend({}, l.w, t.data("tipmsg").w);
                    if (c[0] in d) return t.data("tipmsg").w[c[0]] || l.w[c[0]];
                    for (var h in d) if (h.indexOf(c[1]) != -1 && o.test(h)) return s = (t.data("tipmsg").w[h] || l.w[h]).replace(r, "$1" + c[3] + "$2" + c[4] + "$3"),
                        t.data("tipmsg").w[c[0]] = s,
                        s;
                    return t.data("tipmsg").def || l.def
                },
                _regcheck: function (e, t, n, s) {
                    var s = s,
                        a = null,
                        o = !1,
                        r = /\/.+\//g,
                        d = /^(.+?)(\d+)-(\d+)$/,
                        h = 3;
                    if (r.test(e)) {
                        var u = e.match(r)[0].slice(1, -1),
                            p = e.replace(r, ""),
                            f = RegExp(u, p);
                        o = f.test(t)
                    } else if ("[object Function]" == c.util.toString.call(c.util.dataType[e])) o = c.util.dataType[e](t, n, s, c.util.dataType),
                        o === !0 || o === i ? o = !0 : (a = o, o = !1);
                    else {
                        if (!(e in c.util.dataType)) {
                            var m, g = e.match(d);
                            if (g) {
                                for (var v in c.util.dataType) if (m = v.match(d), m && g[1] === m[1]) {
                                    var y = c.util.dataType[v].toString(),
                                        p = y.match(/\/[mgi]*/g)[1].replace("/", ""),
                                        b = new RegExp("\\{" + m[2] + "," + m[3] + "\\}", "g");
                                    y = y.replace(/\/[mgi]*/g, "/").replace(b, "{" + g[2] + "," + g[3] + "}").replace(/^\//, "").replace(/\/$/, ""),
                                        c.util.dataType[e] = new RegExp(y, p);
                                    break
                                }
                            } else o = !1,
                                a = s.data("tipmsg").undef || l.undef
                        }
                        "[object RegExp]" == c.util.toString.call(c.util.dataType[e]) && (o = c.util.dataType[e].test(t))
                    }
                    if (o) {
                        if (h = 2, a = n.attr("sucmsg") || s.data("tipmsg").r || l.r, n.attr("recheck")) {
                            var w = s.find("input[name='" + n.attr("recheck") + "']:first");
                            t != w.val() && (o = !1, h = 3, a = n.attr("errormsg") || c.util.getErrormsg.call(n, s, e, "recheck"))
                        }
                    } else a = a || n.attr("errormsg") || c.util.getErrormsg.call(n, s, e),
                    c.util.isEmpty.call(n, t) && (a = n.attr("nullmsg") || c.util.getNullmsg.call(n, s));
                    return {
                        passed: o,
                        type: h,
                        info: a
                    }
                },
                regcheck: function (e, t, i) {
                    var n = this,
                        s = null;
                    if ("ignore" === i.attr("ignore") && c.util.isEmpty.call(i, t)) return i.data("cked") && (s = ""),
                    {
                        passed: !0,
                        type: 4,
                        info: s
                    };
                    i.data("cked", "cked");
                    for (var a, o = c.util.parseDatatype(e), r = 0; r < o.length; r++) {
                        for (var l = 0; l < o[r].length && (a = c.util._regcheck(o[r][l], t, i, n), a.passed); l++);
                        if (a.passed) break
                    }
                    return a
                },
                parseDatatype: function (e) {
                    var t = /\/.+?\/[mgi]*(?=(,|$|\||\s))|[\w\*-]+/g,
                        i = e.match(t),
                        n = e.replace(t, "").replace(/\s*/g, "").split(""),
                        s = [],
                        a = 0;
                    s[0] = [],
                        s[0].push(i[0]);
                    for (var o = 0; o < n.length; o++)"|" == n[o] && (a++, s[a] = []),
                        s[a].push(i[o + 1]);
                    return s
                },
                showmsg: function (t, s, a, l) {
                    if (t != i && ("bycheck" != l || !a.sweep || (!a.obj || a.obj.is(".Validform_error")) && "function" != typeof s)) {
                        if (e.extend(a, {
                                curform: this
                            }), "function" == typeof s) return void s(t, a, c.util.cssctl);
                        (1 == s || "byajax" == l && 4 != s) && o.find(".Validform_info").html(t),
                        (1 == s && "bycheck" != l && 2 != a.type || "byajax" == l && 4 != s) && (r = !1, o.find(".iframe").css("height", o.outerHeight()), o.show(), n(o, 100)),
                        2 == s && a.obj && (a.obj.parent().next().find(".Validform_checktip").html(t), c.util.cssctl(a.obj.parent().next().find(".Validform_checktip"), a.type)),
                        3 != s && 4 != s || !a.obj || (a.obj.siblings(".Validform_checktip").html(t), c.util.cssctl(a.obj.siblings(".Validform_checktip"), a.type))
                    }
                },
                cssctl: function (e, t) {
                    switch (t) {
                        case 1:
                            e.removeClass("Validform_right Validform_wrong").addClass("Validform_checktip Validform_loading");
                            break;
                        case 2:
                            e.removeClass("Validform_wrong Validform_loading").addClass("Validform_checktip Validform_right");
                            break;
                        case 4:
                            e.removeClass("Validform_right Validform_wrong Validform_loading").addClass("Validform_checktip");
                            break;
                        default:
                            e.removeClass("Validform_right Validform_loading").addClass("Validform_checktip Validform_wrong")
                    }
                },
                check: function (t, i, n) {
                    var s = t[0].settings,
                        i = i || "",
                        o = c.util.getValue.call(t, e(this));
                    if (s.ignoreHidden && e(this).is(":hidden") || "dataIgnore" === e(this).data("dataIgnore")) return !0;
                    if (s.dragonfly && !e(this).data("cked") && c.util.isEmpty.call(e(this), o) && "ignore" != e(this).attr("ignore")) return !1;
                    var r = c.util.regcheck.call(t, e(this).attr("datatype"), o, e(this));
                    if (o == this.validform_lastval && !e(this).attr("recheck") && "" == i) return !!r.passed;
                    this.validform_lastval = o;
                    var d;
                    if (a = d = e(this), !r.passed) return c.util.abort.call(d[0]),
                    n || (c.util.showmsg.call(t, r.info, s.tiptype, {
                        obj: e(this),
                        type: r.type,
                        sweep: s.tipSweep
                    }, "bycheck"), !s.tipSweep && d.addClass("Validform_error")),
                        !1;
                    var h = e(this).attr("ajaxurl");
                    if (h && !c.util.isEmpty.call(e(this), o) && !n) {
                        var u = e(this);
                        if ("postform" == i ? u[0].validform_subpost = "postform" : u[0].validform_subpost = "", "posting" === u[0].validform_valid && o == u[0].validform_ckvalue) return "ajax";
                        u[0].validform_valid = "posting",
                            u[0].validform_ckvalue = o,
                            c.util.showmsg.call(t, t.data("tipmsg").c || l.c, s.tiptype, {
                                obj: u,
                                type: 1,
                                sweep: s.tipSweep
                            }, "bycheck"),
                            c.util.abort.call(d[0]);
                        var p = e.extend(!0, {}, s.ajaxurl || {}),
                            f = {
                                type: "POST",
                                cache: !1,
                                url: h,
                                data: "param=" + encodeURIComponent(o) + "&name=" + encodeURIComponent(e(this).attr("name")),
                                success: function (i) {
                                    "y" === e.trim(i.status) ? (u[0].validform_valid = "true", i.info && u.attr("sucmsg", i.info), c.util.showmsg.call(t, u.attr("sucmsg") || t.data("tipmsg").r || l.r, s.tiptype, {
                                        obj: u,
                                        type: 2,
                                        sweep: s.tipSweep
                                    }, "bycheck"), d.removeClass("Validform_error"), a = null, "postform" == u[0].validform_subpost && t.trigger("submit")) : (u[0].validform_valid = i.info, c.util.showmsg.call(t, i.info, s.tiptype, {
                                        obj: u,
                                        type: 3,
                                        sweep: s.tipSweep
                                    }), d.addClass("Validform_error")),
                                        d[0].validform_ajax = null
                                },
                                error: function (e) {
                                    if ("200" == e.status) return "y" == e.responseText ? p.success({
                                        status: "y"
                                    }) : p.success({
                                        status: "n",
                                        info: e.responseText
                                    }),
                                        !1;
                                    if ("abort" !== e.statusText) {
                                        var i = "status: " + e.status + "; statusText: " + e.statusText;
                                        c.util.showmsg.call(t, i, s.tiptype, {
                                            obj: u,
                                            type: 3,
                                            sweep: s.tipSweep
                                        }),
                                            d.addClass("Validform_error")
                                    }
                                    return u[0].validform_valid = e.statusText,
                                        d[0].validform_ajax = null,
                                        !0
                                }
                            };
                        if (p.success) {
                            var m = p.success;
                            p.success = function (e) {
                                f.success(e),
                                    m(e, u)
                            }
                        }
                        if (p.error) {
                            var g = p.error;
                            p.error = function (e) {
                                f.error(e) && g(e, u)
                            }
                        }
                        return p = e.extend({}, f, p, {
                            dataType: "json"
                        }),
                            d[0].validform_ajax = e.ajax(p),
                            "ajax"
                    }
                    return h && c.util.isEmpty.call(e(this), o) && (c.util.abort.call(d[0]), d[0].validform_valid = "true"),
                    n || (c.util.showmsg.call(t, r.info, s.tiptype, {
                        obj: e(this),
                        type: r.type,
                        sweep: s.tipSweep
                    }, "bycheck"), d.removeClass("Validform_error")),
                        a = null,
                        !0
                },
                submitForm: function (t, i, n, s, o) {
                    var r = this;
                    if ("posting" === r[0].validform_status) return !1;
                    if (t.postonce && "posted" === r[0].validform_status) return !1;
                    var d = t.beforeCheck && t.beforeCheck(r);
                    if (d === !1) return !1;
                    var h, u = !0;
                    if (r.find("[datatype]").each(function () {
                            if (i) return !1;
                            if (t.ignoreHidden && e(this).is(":hidden") || "dataIgnore" === e(this).data("dataIgnore")) return !0;
                            var n, s = c.util.getValue.call(r, e(this));
                            if (a = n = e(this), h = c.util.regcheck.call(r, e(this).attr("datatype"), s, e(this)), !h.passed) return c.util.showmsg.call(r, h.info, t.tiptype, {
                                obj: e(this),
                                type: h.type,
                                sweep: t.tipSweep
                            }),
                                n.addClass("Validform_error"),
                                t.showAllError ? (u && (u = !1), !0) : (n.focus(), u = !1, !1);
                            if (e(this).attr("ajaxurl") && !c.util.isEmpty.call(e(this), s)) {
                                if ("true" !== this.validform_valid) {
                                    var o = e(this);
                                    return c.util.showmsg.call(r, r.data("tipmsg").v || l.v, t.tiptype, {
                                        obj: o,
                                        type: 3,
                                        sweep: t.tipSweep
                                    }),
                                        n.addClass("Validform_error"),
                                        o.trigger("blur", ["postform"]),
                                        t.showAllError ? (u && (u = !1), !0) : (u = !1, !1)
                                }
                            } else e(this).attr("ajaxurl") && c.util.isEmpty.call(e(this), s) && (c.util.abort.call(this), this.validform_valid = "true");
                            c.util.showmsg.call(r, h.info, t.tiptype, {
                                obj: e(this),
                                type: h.type,
                                sweep: t.tipSweep
                            }),
                                n.removeClass("Validform_error"),
                                a = null
                        }), t.showAllError && r.find(".Validform_error:first").focus(), u) {
                        var p = t.beforeSubmit && t.beforeSubmit(r);
                        if (p === !1) return !1;
                        if (r[0].validform_status = "posting", !t.ajaxPost && "ajaxPost" !== s) {
                            t.postonce || (r[0].validform_status = "normal");
                            var n = n || t.url;
                            return n && r.attr("action", n),
                            t.callback && t.callback(r)
                        }
                        var f = e.extend(!0, {}, t.ajaxpost || {});
                        if (f.url = n || f.url || t.url || r.attr("action"), c.util.showmsg.call(r, r.data("tipmsg").p || l.p, t.tiptype, {
                                obj: r,
                                type: 1,
                                sweep: t.tipSweep
                            }, "byajax"), o ? f.async = !1 : o === !1 && (f.async = !0), f.success) {
                            var m = f.success;
                            f.success = function (i) {
                                t.callback && t.callback(i),
                                    r[0].validform_ajax = null,
                                    "y" === e.trim(i.status) ? r[0].validform_status = "posted" : r[0].validform_status = "normal",
                                    m(i, r)
                            }
                        }
                        if (f.error) {
                            var g = f.error;
                            f.error = function (e) {
                                t.callback && t.callback(e),
                                    r[0].validform_status = "normal",
                                    r[0].validform_ajax = null,
                                    g(e, r)
                            }
                        }
                        var v = {
                            type: "POST",
                            async: !0,
                            data: r.serializeArray(),
                            success: function (i) {
                                "y" === e.trim(i.status) ? (r[0].validform_status = "posted", c.util.showmsg.call(r, i.info, t.tiptype, {
                                    obj: r,
                                    type: 2,
                                    sweep: t.tipSweep
                                }, "byajax")) : (r[0].validform_status = "normal", c.util.showmsg.call(r, i.info, t.tiptype, {
                                    obj: r,
                                    type: 3,
                                    sweep: t.tipSweep
                                }, "byajax")),
                                t.callback && t.callback(i),
                                    r[0].validform_ajax = null
                            },
                            error: function (e) {
                                var i = "status: " + e.status + "; statusText: " + e.statusText;
                                c.util.showmsg.call(r, i, t.tiptype, {
                                    obj: r,
                                    type: 3,
                                    sweep: t.tipSweep
                                }, "byajax"),
                                t.callback && t.callback(e),
                                    r[0].validform_status = "normal",
                                    r[0].validform_ajax = null
                            }
                        };
                        f = e.extend({}, v, f, {
                            dataType: "json"
                        }),
                            r[0].validform_ajax = e.ajax(f)
                    }
                    return !1
                },
                resetForm: function () {
                    var e = this;
                    e.each(function () {
                        this.reset && this.reset(),
                            this.validform_status = "normal"
                    }),
                        e.find(".Validform_right").text(""),
                        e.find(".passwordStrength").children().removeClass("bgStrength"),
                        e.find(".Validform_checktip").removeClass("Validform_wrong Validform_right Validform_loading"),
                        e.find(".Validform_error").removeClass("Validform_error"),
                        e.find("[datatype]").removeData("cked").removeData("dataIgnore").each(function () {
                            this.validform_lastval = null
                        }),
                        e.eq(0).find("input:first").focus()
                },
                abort: function () {
                    this.validform_ajax && this.validform_ajax.abort()
                }
            },
            e.Datatype = c.util.dataType,
            c.prototype = {
                dataType: c.util.dataType,
                eq: function (t) {
                    var i = this;
                    return t >= i.forms.length ? null : (t in i.objects || (i.objects[t] = new c(e(i.forms[t]).get(), {}, (!0))), i.objects[t])
                },
                resetStatus: function () {
                    var t = this;
                    return e(t.forms).each(function () {
                        this.validform_status = "normal"
                    }),
                        this
                },
                setStatus: function (t) {
                    var i = this;
                    return e(i.forms).each(function () {
                        this.validform_status = t || "posting"
                    }),
                        this
                },
                getStatus: function () {
                    var t = this,
                        i = e(t.forms)[0].validform_status;
                    return i
                },
                ignore: function (t) {
                    var i = this,
                        t = t || "[datatype]";
                    return e(i.forms).find(t).each(function () {
                        e(this).data("dataIgnore", "dataIgnore").removeClass("Validform_error")
                    }),
                        this
                },
                unignore: function (t) {
                    var i = this,
                        t = t || "[datatype]";
                    return e(i.forms).find(t).each(function () {
                        e(this).removeData("dataIgnore")
                    }),
                        this
                },
                addRule: function (t) {
                    for (var i = this, t = t || [], n = 0; n < t.length; n++) {
                        var s = e(i.forms).find(t[n].ele);
                        for (var a in t[n])"ele" !== a && s.attr(a, t[n][a])
                    }
                    return e(i.forms).each(function () {
                        var t = e(this);
                        c.util.enhance.call(t, this.settings.tiptype, this.settings.usePlugin, this.settings.tipSweep, "addRule")
                    }),
                        this
                },
                ajaxPost: function (t, i, n) {
                    var a = this;
                    return e(a.forms).each(function () {
                        1 != this.settings.tiptype && 2 != this.settings.tiptype && 3 != this.settings.tiptype || s(),
                            c.util.submitForm.call(e(a.forms[0]), this.settings, t, n, "ajaxPost", i)
                    }),
                        this
                },
                submitForm: function (t, n) {
                    var s = this;
                    return e(s.forms).each(function () {
                        var s = c.util.submitForm.call(e(this), this.settings, t, n);
                        s === i && (s = !0),
                        s === !0 && this.submit()
                    }),
                        this
                },
                resetForm: function () {
                    var t = this;
                    return c.util.resetForm.call(e(t.forms)),
                        this
                },
                abort: function () {
                    var t = this;
                    return e(t.forms).each(function () {
                        c.util.abort.call(this)
                    }),
                        this
                },
                check: function (t, i) {
                    var i = i || "[datatype]",
                        n = this,
                        s = e(n.forms),
                        a = !0;
                    return s.find(i).each(function () {
                        c.util.check.call(this, s, "", t) || (a = !1)
                    }),
                        a
                },
                config: function (t) {
                    var i = this;
                    return t = t || {},
                        e(i.forms).each(function () {
                            var i = e(this);
                            this.settings = e.extend(!0, this.settings, t),
                                c.util.enhance.call(i, this.settings.tiptype, this.settings.usePlugin, this.settings.tipSweep)
                        }),
                        this
                }
            },
            e.fn.Validform = function (e) {
                return new c(this, e)
            },
            e.Showmsg = function (e) {
                s(),
                    c.util.showmsg.call(t, e, 1, {})
            },
            e.Hidemsg = function () {
                o.hide(),
                    r = !0
            }
    }(jQuery, window),
    $(function () {
        $.Tipmsg.r = null;
        var e = function (e) {
            alert(e)
        };
        $(".validform").Validform({
            tiptype: function (t) {
                e(t)
            },
            tipSweep: !0
        })
    });
var evalscripts = new Array,
    pmwinposition = new Array,
    userAgent = navigator.userAgent.toLowerCase(),
    is_opera = userAgent.indexOf("opera") != -1 && opera.version(),
    is_moz = "Gecko" == navigator.product && userAgent.substr(userAgent.indexOf("firefox") + 8, 3),
    is_ie = userAgent.indexOf("msie") != -1 && !is_opera && userAgent.substr(userAgent.indexOf("msie") + 5, 3),
    pmwindragstart = new Array,
    str = "",
    strr = "";
if ("undefined" == typeof jQuery) throw new Error("Bootstrap's JavaScript requires jQuery");
+
    function (e) {
        "use strict";
        var t = e.fn.jquery.split(" ")[0].split(".");
        if (t[0] < 2 && t[1] < 9 || 1 == t[0] && 9 == t[1] && t[2] < 1 || t[0] > 2) throw new Error("Bootstrap's JavaScript requires jQuery version 1.9.1 or higher, but lower than version 3")
    }(jQuery),
    +
        function (e) {
            "use strict";

            function t() {
                var e = document.createElement("bootstrap"),
                    t = {
                        WebkitTransition: "webkitTransitionEnd",
                        MozTransition: "transitionend",
                        OTransition: "oTransitionEnd otransitionend",
                        transition: "transitionend"
                    };
                for (var i in t) if (void 0 !== e.style[i]) return {
                    end: t[i]
                };
                return !1
            }

            e.fn.emulateTransitionEnd = function (t) {
                var i = !1,
                    n = this;
                e(this).one("bsTransitionEnd", function () {
                    i = !0
                });
                var s = function () {
                    i || e(n).trigger(e.support.transition.end)
                };
                return setTimeout(s, t),
                    this
            },
                e(function () {
                    e.support.transition = t(),
                    e.support.transition && (e.event.special.bsTransitionEnd = {
                        bindType: e.support.transition.end,
                        delegateType: e.support.transition.end,
                        handle: function (t) {
                            return e(t.target).is(this) ? t.handleObj.handler.apply(this, arguments) : void 0
                        }
                    })
                })
        }(jQuery),
    +
        function (e) {
            "use strict";

            function t(t) {
                return this.each(function () {
                    var i = e(this),
                        s = i.data("bs.alert");
                    s || i.data("bs.alert", s = new n(this)),
                    "string" == typeof t && s[t].call(i)
                })
            }

            var i = '[data-dismiss="alert"]',
                n = function (t) {
                    e(t).on("click", i, this.close)
                };
            n.VERSION = "3.3.6",
                n.TRANSITION_DURATION = 150,
                n.prototype.close = function (t) {
                    function i() {
                        o.detach().trigger("closed.bs.alert").remove()
                    }

                    var s = e(this),
                        a = s.attr("data-target");
                    a || (a = s.attr("href"), a = a && a.replace(/.*(?=#[^\s]*$)/, ""));
                    var o = e(a);
                    t && t.preventDefault(),
                    o.length || (o = s.closest(".alert")),
                        o.trigger(t = e.Event("close.bs.alert")),
                    t.isDefaultPrevented() || (o.removeClass("in"), e.support.transition && o.hasClass("fade") ? o.one("bsTransitionEnd", i).emulateTransitionEnd(n.TRANSITION_DURATION) : i())
                };
            var s = e.fn.alert;
            e.fn.alert = t,
                e.fn.alert.Constructor = n,
                e.fn.alert.noConflict = function () {
                    return e.fn.alert = s,
                        this
                },
                e(document).on("click.bs.alert.data-api", i, n.prototype.close)
        }(jQuery),
    +
        function (e) {
            "use strict";

            function t(t) {
                return this.each(function () {
                    var n = e(this),
                        s = n.data("bs.button"),
                        a = "object" == typeof t && t;
                    s || n.data("bs.button", s = new i(this, a)),
                        "toggle" == t ? s.toggle() : t && s.setState(t)
                })
            }

            var i = function (t, n) {
                this.$element = e(t),
                    this.options = e.extend({}, i.DEFAULTS, n),
                    this.isLoading = !1
            };
            i.VERSION = "3.3.6",
                i.DEFAULTS = {
                    loadingText: "loading..."
                },
                i.prototype.setState = function (t) {
                    var i = "disabled",
                        n = this.$element,
                        s = n.is("input") ? "val" : "html",
                        a = n.data();
                    t += "Text",
                    null == a.resetText && n.data("resetText", n[s]()),
                        setTimeout(e.proxy(function () {
                            n[s](null == a[t] ? this.options[t] : a[t]),
                                "loadingText" == t ? (this.isLoading = !0, n.addClass(i).attr(i, i)) : this.isLoading && (this.isLoading = !1, n.removeClass(i).removeAttr(i))
                        }, this), 0)
                },
                i.prototype.toggle = function () {
                    var e = !0,
                        t = this.$element.closest('[data-toggle="buttons"]');
                    if (t.length) {
                        var i = this.$element.find("input");
                        "radio" == i.prop("type") ? (i.prop("checked") && (e = !1), t.find(".active").removeClass("active"), this.$element.addClass("active")) : "checkbox" == i.prop("type") && (i.prop("checked") !== this.$element.hasClass("active") && (e = !1), this.$element.toggleClass("active")),
                            i.prop("checked", this.$element.hasClass("active")),
                        e && i.trigger("change")
                    } else this.$element.attr("aria-pressed", !this.$element.hasClass("active")),
                        this.$element.toggleClass("active")
                };
            var n = e.fn.button;
            e.fn.button = t,
                e.fn.button.Constructor = i,
                e.fn.button.noConflict = function () {
                    return e.fn.button = n,
                        this
                },
                e(document).on("click.bs.button.data-api", '[data-toggle^="button"]', function (i) {
                    var n = e(i.target);
                    n.hasClass("btn") || (n = n.closest(".btn")),
                        t.call(n, "toggle"),
                    e(i.target).is('input[type="radio"]') || e(i.target).is('input[type="checkbox"]') || i.preventDefault()
                }).on("focus.bs.button.data-api blur.bs.button.data-api", '[data-toggle^="button"]', function (t) {
                    e(t.target).closest(".btn").toggleClass("focus", /^focus(in)?$/.test(t.type))
                })
        }(jQuery),
    +
        function (e) {
            "use strict";

            function t(t) {
                return this.each(function () {
                    var n = e(this),
                        s = n.data("bs.carousel"),
                        a = e.extend({}, i.DEFAULTS, n.data(), "object" == typeof t && t),
                        o = "string" == typeof t ? t : a.slide;
                    s || n.data("bs.carousel", s = new i(this, a)),
                        "number" == typeof t ? s.to(t) : o ? s[o]() : a.interval && s.pause().cycle()
                })
            }

            var i = function (t, i) {
                this.$element = e(t),
                    this.$indicators = this.$element.find(".carousel-indicators"),
                    this.options = i,
                    this.paused = null,
                    this.sliding = null,
                    this.interval = null,
                    this.$active = null,
                    this.$items = null,
                this.options.keyboard && this.$element.on("keydown.bs.carousel", e.proxy(this.keydown, this)),
                "hover" == this.options.pause && !("ontouchstart" in document.documentElement) && this.$element.on("mouseenter.bs.carousel", e.proxy(this.pause, this)).on("mouseleave.bs.carousel", e.proxy(this.cycle, this))
            };
            i.VERSION = "3.3.6",
                i.TRANSITION_DURATION = 600,
                i.DEFAULTS = {
                    interval: 5e3,
                    pause: "hover",
                    wrap: !0,
                    keyboard: !0
                },
                i.prototype.keydown = function (e) {
                    if (!/input|textarea/i.test(e.target.tagName)) {
                        switch (e.which) {
                            case 37:
                                this.prev();
                                break;
                            case 39:
                                this.next();
                                break;
                            default:
                                return
                        }
                        e.preventDefault()
                    }
                },
                i.prototype.cycle = function (t) {
                    return t || (this.paused = !1),
                    this.interval && clearInterval(this.interval),
                    this.options.interval && !this.paused && (this.interval = setInterval(e.proxy(this.next, this), this.options.interval)),
                        this
                },
                i.prototype.getItemIndex = function (e) {
                    return this.$items = e.parent().children(".item"),
                        this.$items.index(e || this.$active)
                },
                i.prototype.getItemForDirection = function (e, t) {
                    var i = this.getItemIndex(t),
                        n = "prev" == e && 0 === i || "next" == e && i == this.$items.length - 1;
                    if (n && !this.options.wrap) return t;
                    var s = "prev" == e ? -1 : 1,
                        a = (i + s) % this.$items.length;
                    return this.$items.eq(a)
                },
                i.prototype.to = function (e) {
                    var t = this,
                        i = this.getItemIndex(this.$active = this.$element.find(".item.active"));
                    return e > this.$items.length - 1 || 0 > e ? void 0 : this.sliding ? this.$element.one("slid.bs.carousel", function () {
                        t.to(e)
                    }) : i == e ? this.pause().cycle() : this.slide(e > i ? "next" : "prev", this.$items.eq(e))
                },
                i.prototype.pause = function (t) {
                    return t || (this.paused = !0),
                    this.$element.find(".next, .prev").length && e.support.transition && (this.$element.trigger(e.support.transition.end), this.cycle(!0)),
                        this.interval = clearInterval(this.interval),
                        this
                },
                i.prototype.next = function () {
                    return this.sliding ? void 0 : this.slide("next")
                },
                i.prototype.prev = function () {
                    return this.sliding ? void 0 : this.slide("prev")
                },
                i.prototype.slide = function (t, n) {
                    var s = this.$element.find(".item.active"),
                        a = n || this.getItemForDirection(t, s),
                        o = this.interval,
                        r = "next" == t ? "left" : "right",
                        l = this;
                    if (a.hasClass("active")) return this.sliding = !1;
                    var c = a[0],
                        d = e.Event("slide.bs.carousel", {
                            relatedTarget: c,
                            direction: r
                        });
                    if (this.$element.trigger(d), !d.isDefaultPrevented()) {
                        if (this.sliding = !0, o && this.pause(), this.$indicators.length) {
                            this.$indicators.find(".active").removeClass("active");
                            var h = e(this.$indicators.children()[this.getItemIndex(a)]);
                            h && h.addClass("active")
                        }
                        var u = e.Event("slid.bs.carousel", {
                            relatedTarget: c,
                            direction: r
                        });
                        return e.support.transition && this.$element.hasClass("slide") ? (a.addClass(t), a[0].offsetWidth, s.addClass(r), a.addClass(r), s.one("bsTransitionEnd", function () {
                            a.removeClass([t, r].join(" ")).addClass("active"),
                                s.removeClass(["active", r].join(" ")),
                                l.sliding = !1,
                                setTimeout(function () {
                                    l.$element.trigger(u)
                                }, 0)
                        }).emulateTransitionEnd(i.TRANSITION_DURATION)) : (s.removeClass("active"), a.addClass("active"), this.sliding = !1, this.$element.trigger(u)),
                        o && this.cycle(),
                            this
                    }
                };
            var n = e.fn.carousel;
            e.fn.carousel = t,
                e.fn.carousel.Constructor = i,
                e.fn.carousel.noConflict = function () {
                    return e.fn.carousel = n,
                        this
                };
            var s = function (i) {
                var n, s = e(this),
                    a = e(s.attr("data-target") || (n = s.attr("href")) && n.replace(/.*(?=#[^\s]+$)/, ""));
                if (a.hasClass("carousel")) {
                    var o = e.extend({}, a.data(), s.data()),
                        r = s.attr("data-slide-to");
                    r && (o.interval = !1),
                        t.call(a, o),
                    r && a.data("bs.carousel").to(r),
                        i.preventDefault()
                }
            };
            e(document).on("click.bs.carousel.data-api", "[data-slide]", s).on("click.bs.carousel.data-api", "[data-slide-to]", s),
                e(window).on("load", function () {
                    e('[data-ride="carousel"]').each(function () {
                        var i = e(this);
                        t.call(i, i.data())
                    })
                })
        }(jQuery),
    +
        function (e) {
            "use strict";

            function t(t) {
                var i, n = t.attr("data-target") || (i = t.attr("href")) && i.replace(/.*(?=#[^\s]+$)/, "");
                return e(n)
            }

            function i(t) {
                return this.each(function () {
                    var i = e(this),
                        s = i.data("bs.collapse"),
                        a = e.extend({}, n.DEFAULTS, i.data(), "object" == typeof t && t);
                    !s && a.toggle && /show|hide/.test(t) && (a.toggle = !1),
                    s || i.data("bs.collapse", s = new n(this, a)),
                    "string" == typeof t && s[t]()
                })
            }

            var n = function (t, i) {
                this.$element = e(t),
                    this.options = e.extend({}, n.DEFAULTS, i),
                    this.$trigger = e('[data-toggle="collapse"][href="#' + t.id + '"],[data-toggle="collapse"][data-target="#' + t.id + '"]'),
                    this.transitioning = null,
                    this.options.parent ? this.$parent = this.getParent() : this.addAriaAndCollapsedClass(this.$element, this.$trigger),
                this.options.toggle && this.toggle()
            };
            n.VERSION = "3.3.6",
                n.TRANSITION_DURATION = 350,
                n.DEFAULTS = {
                    toggle: !0
                },
                n.prototype.dimension = function () {
                    var e = this.$element.hasClass("width");
                    return e ? "width" : "height"
                },
                n.prototype.show = function () {
                    if (!this.transitioning && !this.$element.hasClass("in")) {
                        var t, s = this.$parent && this.$parent.children(".panel").children(".in, .collapsing");
                        if (!(s && s.length && (t = s.data("bs.collapse"), t && t.transitioning))) {
                            var a = e.Event("show.bs.collapse");
                            if (this.$element.trigger(a), !a.isDefaultPrevented()) {
                                s && s.length && (i.call(s, "hide"), t || s.data("bs.collapse", null));
                                var o = this.dimension();
                                this.$element.removeClass("collapse").addClass("collapsing")[o](0).attr("aria-expanded", !0),
                                    this.$trigger.removeClass("collapsed").attr("aria-expanded", !0),
                                    this.transitioning = 1;
                                var r = function () {
                                    this.$element.removeClass("collapsing").addClass("collapse in")[o](""),
                                        this.transitioning = 0,
                                        this.$element.trigger("shown.bs.collapse")
                                };
                                if (!e.support.transition) return r.call(this);
                                var l = e.camelCase(["scroll", o].join("-"));
                                this.$element.one("bsTransitionEnd", e.proxy(r, this)).emulateTransitionEnd(n.TRANSITION_DURATION)[o](this.$element[0][l])
                            }
                        }
                    }
                },
                n.prototype.hide = function () {
                    if (!this.transitioning && this.$element.hasClass("in")) {
                        var t = e.Event("hide.bs.collapse");
                        if (this.$element.trigger(t), !t.isDefaultPrevented()) {
                            var i = this.dimension();
                            this.$element[i](this.$element[i]())[0].offsetHeight,
                                this.$element.addClass("collapsing").removeClass("collapse in").attr("aria-expanded", !1),
                                this.$trigger.addClass("collapsed").attr("aria-expanded", !1),
                                this.transitioning = 1;
                            var s = function () {
                                this.transitioning = 0,
                                    this.$element.removeClass("collapsing").addClass("collapse").trigger("hidden.bs.collapse")
                            };
                            return e.support.transition ? void this.$element[i](0).one("bsTransitionEnd", e.proxy(s, this)).emulateTransitionEnd(n.TRANSITION_DURATION) : s.call(this);
                        }
                    }
                },
                n.prototype.toggle = function () {
                    this[this.$element.hasClass("in") ? "hide" : "show"]()
                },
                n.prototype.getParent = function () {
                    return e(this.options.parent).find('[data-toggle="collapse"][data-parent="' + this.options.parent + '"]').each(e.proxy(function (i, n) {
                        var s = e(n);
                        this.addAriaAndCollapsedClass(t(s), s)
                    }, this)).end()
                },
                n.prototype.addAriaAndCollapsedClass = function (e, t) {
                    var i = e.hasClass("in");
                    e.attr("aria-expanded", i),
                        t.toggleClass("collapsed", !i).attr("aria-expanded", i)
                };
            var s = e.fn.collapse;
            e.fn.collapse = i,
                e.fn.collapse.Constructor = n,
                e.fn.collapse.noConflict = function () {
                    return e.fn.collapse = s,
                        this
                },
                e(document).on("click.bs.collapse.data-api", '[data-toggle="collapse"]', function (n) {
                    var s = e(this);
                    s.attr("data-target") || n.preventDefault();
                    var a = t(s),
                        o = a.data("bs.collapse"),
                        r = o ? "toggle" : s.data();
                    i.call(a, r)
                })
        }(jQuery),
    +
        function (e) {
            "use strict";

            function t(t) {
                var i = t.attr("data-target");
                i || (i = t.attr("href"), i = i && /#[A-Za-z]/.test(i) && i.replace(/.*(?=#[^\s]*$)/, ""));
                var n = i && e(i);
                return n && n.length ? n : t.parent()
            }

            function i(i) {
                i && 3 === i.which || (e(s).remove(), e(a).each(function () {
                    var n = e(this),
                        s = t(n),
                        a = {
                            relatedTarget: this
                        };
                    s.hasClass("open") && (i && "click" == i.type && /input|textarea/i.test(i.target.tagName) && e.contains(s[0], i.target) || (s.trigger(i = e.Event("hide.bs.dropdown", a)), i.isDefaultPrevented() || (n.attr("aria-expanded", "false"), s.removeClass("open").trigger(e.Event("hidden.bs.dropdown", a)))))
                }))
            }

            function n(t) {
                return this.each(function () {
                    var i = e(this),
                        n = i.data("bs.dropdown");
                    n || i.data("bs.dropdown", n = new o(this)),
                    "string" == typeof t && n[t].call(i)
                })
            }

            var s = ".dropdown-backdrop",
                a = '[data-toggle="dropdown"]',
                o = function (t) {
                    e(t).on("click.bs.dropdown", this.toggle)
                };
            o.VERSION = "3.3.6",
                o.prototype.toggle = function (n) {
                    var s = e(this);
                    if (!s.is(".disabled, :disabled")) {
                        var a = t(s),
                            o = a.hasClass("open");
                        if (i(), !o) {
                            "ontouchstart" in document.documentElement && !a.closest(".navbar-nav").length && e(document.createElement("div")).addClass("dropdown-backdrop").insertAfter(e(this)).on("click", i);
                            var r = {
                                relatedTarget: this
                            };
                            if (a.trigger(n = e.Event("show.bs.dropdown", r)), n.isDefaultPrevented()) return;
                            s.trigger("focus").attr("aria-expanded", "true"),
                                a.toggleClass("open").trigger(e.Event("shown.bs.dropdown", r))
                        }
                        return !1
                    }
                },
                o.prototype.keydown = function (i) {
                    if (/(38|40|27|32)/.test(i.which) && !/input|textarea/i.test(i.target.tagName)) {
                        var n = e(this);
                        if (i.preventDefault(), i.stopPropagation(), !n.is(".disabled, :disabled")) {
                            var s = t(n),
                                o = s.hasClass("open");
                            if (!o && 27 != i.which || o && 27 == i.which) return 27 == i.which && s.find(a).trigger("focus"),
                                n.trigger("click");
                            var r = " li:not(.disabled):visible a",
                                l = s.find(".dropdown-menu" + r);
                            if (l.length) {
                                var c = l.index(i.target);
                                38 == i.which && c > 0 && c--,
                                40 == i.which && c < l.length - 1 && c++,
                                ~c || (c = 0),
                                    l.eq(c).trigger("focus")
                            }
                        }
                    }
                };
            var r = e.fn.dropdown;
            e.fn.dropdown = n,
                e.fn.dropdown.Constructor = o,
                e.fn.dropdown.noConflict = function () {
                    return e.fn.dropdown = r,
                        this
                },
                e(document).on("click.bs.dropdown.data-api", i).on("click.bs.dropdown.data-api", ".dropdown form", function (e) {
                    e.stopPropagation()
                }).on("click.bs.dropdown.data-api", a, o.prototype.toggle).on("keydown.bs.dropdown.data-api", a, o.prototype.keydown).on("keydown.bs.dropdown.data-api", ".dropdown-menu", o.prototype.keydown)
        }(jQuery),
    +
        function (e) {
            "use strict";

            function t(t, n) {
                return this.each(function () {
                    var s = e(this),
                        a = s.data("bs.modal"),
                        o = e.extend({}, i.DEFAULTS, s.data(), "object" == typeof t && t);
                    a || s.data("bs.modal", a = new i(this, o)),
                        "string" == typeof t ? a[t](n) : o.show && a.show(n)
                })
            }

            var i = function (t, i) {
                this.options = i,
                    this.$body = e(document.body),
                    this.$element = e(t),
                    this.$dialog = this.$element.find(".modal-dialog"),
                    this.$backdrop = null,
                    this.isShown = null,
                    this.originalBodyPad = null,
                    this.scrollbarWidth = 0,
                    this.ignoreBackdropClick = !1,
                this.options.remote && this.$element.find(".modal-content").load(this.options.remote, e.proxy(function () {
                    this.$element.trigger("loaded.bs.modal")
                }, this))
            };
            i.VERSION = "3.3.6",
                i.TRANSITION_DURATION = 300,
                i.BACKDROP_TRANSITION_DURATION = 150,
                i.DEFAULTS = {
                    backdrop: !0,
                    keyboard: !0,
                    show: !0
                },
                i.prototype.toggle = function (e) {
                    return this.isShown ? this.hide() : this.show(e)
                },
                i.prototype.show = function (t) {
                    var n = this,
                        s = e.Event("show.bs.modal", {
                            relatedTarget: t
                        });
                    this.$element.trigger(s),
                    this.isShown || s.isDefaultPrevented() || (this.isShown = !0, this.checkScrollbar(), this.setScrollbar(), this.$body.addClass("modal-open"), this.escape(), this.resize(), this.$element.on("click.dismiss.bs.modal", '[data-dismiss="modal"]', e.proxy(this.hide, this)), this.$dialog.on("mousedown.dismiss.bs.modal", function () {
                        n.$element.one("mouseup.dismiss.bs.modal", function (t) {
                            e(t.target).is(n.$element) && (n.ignoreBackdropClick = !0)
                        })
                    }), this.backdrop(function () {
                        var s = e.support.transition && n.$element.hasClass("fade");
                        n.$element.parent().length || n.$element.appendTo(n.$body),
                            n.$element.show().scrollTop(0),
                            n.adjustDialog(),
                        s && n.$element[0].offsetWidth,
                            n.$element.addClass("in"),
                            n.enforceFocus();
                        var a = e.Event("shown.bs.modal", {
                            relatedTarget: t
                        });
                        s ? n.$dialog.one("bsTransitionEnd", function () {
                            n.$element.trigger("focus").trigger(a)
                        }).emulateTransitionEnd(i.TRANSITION_DURATION) : n.$element.trigger("focus").trigger(a)
                    }))
                },
                i.prototype.hide = function (t) {
                    t && t.preventDefault(),
                        t = e.Event("hide.bs.modal"),
                        this.$element.trigger(t),
                    this.isShown && !t.isDefaultPrevented() && (this.isShown = !1, this.escape(), this.resize(), e(document).off("focusin.bs.modal"), this.$element.removeClass("in").off("click.dismiss.bs.modal").off("mouseup.dismiss.bs.modal"), this.$dialog.off("mousedown.dismiss.bs.modal"), e.support.transition && this.$element.hasClass("fade") ? this.$element.one("bsTransitionEnd", e.proxy(this.hideModal, this)).emulateTransitionEnd(i.TRANSITION_DURATION) : this.hideModal())
                },
                i.prototype.enforceFocus = function () {
                    e(document).off("focusin.bs.modal").on("focusin.bs.modal", e.proxy(function (e) {
                        this.$element[0] === e.target || this.$element.has(e.target).length || this.$element.trigger("focus")
                    }, this))
                },
                i.prototype.escape = function () {
                    this.isShown && this.options.keyboard ? this.$element.on("keydown.dismiss.bs.modal", e.proxy(function (e) {
                        27 == e.which && this.hide()
                    }, this)) : this.isShown || this.$element.off("keydown.dismiss.bs.modal")
                },
                i.prototype.resize = function () {
                    this.isShown ? e(window).on("resize.bs.modal", e.proxy(this.handleUpdate, this)) : e(window).off("resize.bs.modal")
                },
                i.prototype.hideModal = function () {
                    var e = this;
                    this.$element.hide(),
                        this.backdrop(function () {
                            e.$body.removeClass("modal-open"),
                                e.resetAdjustments(),
                                e.resetScrollbar(),
                                e.$element.trigger("hidden.bs.modal")
                        })
                },
                i.prototype.removeBackdrop = function () {
                    this.$backdrop && this.$backdrop.remove(),
                        this.$backdrop = null
                },
                i.prototype.backdrop = function (t) {
                    var n = this,
                        s = this.$element.hasClass("fade") ? "fade" : "";
                    if (this.isShown && this.options.backdrop) {
                        var a = e.support.transition && s;
                        if (this.$backdrop = e(document.createElement("div")).addClass("modal-backdrop " + s).appendTo(this.$body), this.$element.on("click.dismiss.bs.modal", e.proxy(function (e) {
                                return this.ignoreBackdropClick ? void(this.ignoreBackdropClick = !1) : void(e.target === e.currentTarget && ("static" == this.options.backdrop ? this.$element[0].focus() : this.hide()))
                            }, this)), a && this.$backdrop[0].offsetWidth, this.$backdrop.addClass("in"), !t) return;
                        a ? this.$backdrop.one("bsTransitionEnd", t).emulateTransitionEnd(i.BACKDROP_TRANSITION_DURATION) : t()
                    } else if (!this.isShown && this.$backdrop) {
                        this.$backdrop.removeClass("in");
                        var o = function () {
                            n.removeBackdrop(),
                            t && t()
                        };
                        e.support.transition && this.$element.hasClass("fade") ? this.$backdrop.one("bsTransitionEnd", o).emulateTransitionEnd(i.BACKDROP_TRANSITION_DURATION) : o()
                    } else t && t()
                },
                i.prototype.handleUpdate = function () {
                    this.adjustDialog()
                },
                i.prototype.adjustDialog = function () {
                    var e = this.$element[0].scrollHeight > document.documentElement.clientHeight;
                    this.$element.css({
                        paddingLeft: !this.bodyIsOverflowing && e ? this.scrollbarWidth : "",
                        paddingRight: this.bodyIsOverflowing && !e ? this.scrollbarWidth : ""
                    })
                },
                i.prototype.resetAdjustments = function () {
                    this.$element.css({
                        paddingLeft: "",
                        paddingRight: ""
                    })
                },
                i.prototype.checkScrollbar = function () {
                    var e = window.innerWidth;
                    if (!e) {
                        var t = document.documentElement.getBoundingClientRect();
                        e = t.right - Math.abs(t.left)
                    }
                    this.bodyIsOverflowing = document.body.clientWidth < e,
                        this.scrollbarWidth = this.measureScrollbar()
                },
                i.prototype.setScrollbar = function () {
                    var e = parseInt(this.$body.css("padding-right") || 0, 10);
                    this.originalBodyPad = document.body.style.paddingRight || "",
                    this.bodyIsOverflowing && this.$body.css("padding-right", e + this.scrollbarWidth)
                },
                i.prototype.resetScrollbar = function () {
                    this.$body.css("padding-right", this.originalBodyPad)
                },
                i.prototype.measureScrollbar = function () {
                    var e = document.createElement("div");
                    e.className = "modal-scrollbar-measure",
                        this.$body.append(e);
                    var t = e.offsetWidth - e.clientWidth;
                    return this.$body[0].removeChild(e),
                        t
                };
            var n = e.fn.modal;
            e.fn.modal = t,
                e.fn.modal.Constructor = i,
                e.fn.modal.noConflict = function () {
                    return e.fn.modal = n,
                        this
                },
                e(document).on("click.bs.modal.data-api", '[data-toggle="modal"]', function (i) {
                    var n = e(this),
                        s = n.attr("href"),
                        a = e(n.attr("data-target") || s && s.replace(/.*(?=#[^\s]+$)/, "")),
                        o = a.data("bs.modal") ? "toggle" : e.extend({
                            remote: !/#/.test(s) && s
                        }, a.data(), n.data());
                    n.is("a") && i.preventDefault(),
                        a.one("show.bs.modal", function (e) {
                            e.isDefaultPrevented() || a.one("hidden.bs.modal", function () {
                                n.is(":visible") && n.trigger("focus")
                            })
                        }),
                        t.call(a, o, this)
                })
        }(jQuery),
    +
        function (e) {
            "use strict";

            function t(t) {
                return this.each(function () {
                    var n = e(this),
                        s = n.data("bs.tooltip"),
                        a = "object" == typeof t && t;
                    (s || !/destroy|hide/.test(t)) && (s || n.data("bs.tooltip", s = new i(this, a)), "string" == typeof t && s[t]())
                })
            }

            var i = function (e, t) {
                this.type = null,
                    this.options = null,
                    this.enabled = null,
                    this.timeout = null,
                    this.hoverState = null,
                    this.$element = null,
                    this.inState = null,
                    this.init("tooltip", e, t)
            };
            i.VERSION = "3.3.6",
                i.TRANSITION_DURATION = 150,
                i.DEFAULTS = {
                    animation: !0,
                    placement: "top",
                    selector: !1,
                    template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
                    trigger: "hover focus",
                    title: "",
                    delay: 0,
                    html: !1,
                    container: !1,
                    viewport: {
                        selector: "body",
                        padding: 0
                    }
                },
                i.prototype.init = function (t, i, n) {
                    if (this.enabled = !0, this.type = t, this.$element = e(i), this.options = this.getOptions(n), this.$viewport = this.options.viewport && e(e.isFunction(this.options.viewport) ? this.options.viewport.call(this, this.$element) : this.options.viewport.selector || this.options.viewport), this.inState = {
                            click: !1,
                            hover: !1,
                            focus: !1
                        }, this.$element[0] instanceof document.constructor && !this.options.selector) throw new Error("`selector` option must be specified when initializing " + this.type + " on the window.document object!");
                    for (var s = this.options.trigger.split(" "), a = s.length; a--;) {
                        var o = s[a];
                        if ("click" == o) this.$element.on("click." + this.type, this.options.selector, e.proxy(this.toggle, this));
                        else if ("manual" != o) {
                            var r = "hover" == o ? "mouseenter" : "focusin",
                                l = "hover" == o ? "mouseleave" : "focusout";
                            this.$element.on(r + "." + this.type, this.options.selector, e.proxy(this.enter, this)),
                                this.$element.on(l + "." + this.type, this.options.selector, e.proxy(this.leave, this))
                        }
                    }
                    this.options.selector ? this._options = e.extend({}, this.options, {
                        trigger: "manual",
                        selector: ""
                    }) : this.fixTitle()
                },
                i.prototype.getDefaults = function () {
                    return i.DEFAULTS
                },
                i.prototype.getOptions = function (t) {
                    return t = e.extend({}, this.getDefaults(), this.$element.data(), t),
                    t.delay && "number" == typeof t.delay && (t.delay = {
                        show: t.delay,
                        hide: t.delay
                    }),
                        t
                },
                i.prototype.getDelegateOptions = function () {
                    var t = {},
                        i = this.getDefaults();
                    return this._options && e.each(this._options, function (e, n) {
                        i[e] != n && (t[e] = n)
                    }),
                        t
                },
                i.prototype.enter = function (t) {
                    var i = t instanceof this.constructor ? t : e(t.currentTarget).data("bs." + this.type);
                    return i || (i = new this.constructor(t.currentTarget, this.getDelegateOptions()), e(t.currentTarget).data("bs." + this.type, i)),
                    t instanceof e.Event && (i.inState["focusin" == t.type ? "focus" : "hover"] = !0),
                        i.tip().hasClass("in") || "in" == i.hoverState ? void(i.hoverState = "in") : (clearTimeout(i.timeout), i.hoverState = "in", i.options.delay && i.options.delay.show ? void(i.timeout = setTimeout(function () {
                            "in" == i.hoverState && i.show()
                        }, i.options.delay.show)) : i.show())
                },
                i.prototype.isInStateTrue = function () {
                    for (var e in this.inState) if (this.inState[e]) return !0;
                    return !1
                },
                i.prototype.leave = function (t) {
                    var i = t instanceof this.constructor ? t : e(t.currentTarget).data("bs." + this.type);
                    return i || (i = new this.constructor(t.currentTarget, this.getDelegateOptions()), e(t.currentTarget).data("bs." + this.type, i)),
                    t instanceof e.Event && (i.inState["focusout" == t.type ? "focus" : "hover"] = !1),
                        i.isInStateTrue() ? void 0 : (clearTimeout(i.timeout), i.hoverState = "out", i.options.delay && i.options.delay.hide ? void(i.timeout = setTimeout(function () {
                            "out" == i.hoverState && i.hide()
                        }, i.options.delay.hide)) : i.hide())
                },
                i.prototype.show = function () {
                    var t = e.Event("show.bs." + this.type);
                    if (this.hasContent() && this.enabled) {
                        this.$element.trigger(t);
                        var n = e.contains(this.$element[0].ownerDocument.documentElement, this.$element[0]);
                        if (t.isDefaultPrevented() || !n) return;
                        var s = this,
                            a = this.tip(),
                            o = this.getUID(this.type);
                        this.setContent(),
                            a.attr("id", o),
                            this.$element.attr("aria-describedby", o),
                        this.options.animation && a.addClass("fade");
                        var r = "function" == typeof this.options.placement ? this.options.placement.call(this, a[0], this.$element[0]) : this.options.placement,
                            l = /\s?auto?\s?/i,
                            c = l.test(r);
                        c && (r = r.replace(l, "") || "top"),
                            a.detach().css({
                                top: 0,
                                left: 0,
                                display: "block"
                            }).addClass(r).data("bs." + this.type, this),
                            this.options.container ? a.appendTo(this.options.container) : a.insertAfter(this.$element),
                            this.$element.trigger("inserted.bs." + this.type);
                        var d = this.getPosition(),
                            h = a[0].offsetWidth,
                            u = a[0].offsetHeight;
                        if (c) {
                            var p = r,
                                f = this.getPosition(this.$viewport);
                            r = "bottom" == r && d.bottom + u > f.bottom ? "top" : "top" == r && d.top - u < f.top ? "bottom" : "right" == r && d.right + h > f.width ? "left" : "left" == r && d.left - h < f.left ? "right" : r,
                                a.removeClass(p).addClass(r)
                        }
                        var m = this.getCalculatedOffset(r, d, h, u);
                        this.applyPlacement(m, r);
                        var g = function () {
                            var e = s.hoverState;
                            s.$element.trigger("shown.bs." + s.type),
                                s.hoverState = null,
                            "out" == e && s.leave(s)
                        };
                        e.support.transition && this.$tip.hasClass("fade") ? a.one("bsTransitionEnd", g).emulateTransitionEnd(i.TRANSITION_DURATION) : g()
                    }
                },
                i.prototype.applyPlacement = function (t, i) {
                    var n = this.tip(),
                        s = n[0].offsetWidth,
                        a = n[0].offsetHeight,
                        o = parseInt(n.css("margin-top"), 10),
                        r = parseInt(n.css("margin-left"), 10);
                    isNaN(o) && (o = 0),
                    isNaN(r) && (r = 0),
                        t.top += o,
                        t.left += r,
                        e.offset.setOffset(n[0], e.extend({
                            using: function (e) {
                                n.css({
                                    top: Math.round(e.top),
                                    left: Math.round(e.left)
                                })
                            }
                        }, t), 0),
                        n.addClass("in");
                    var l = n[0].offsetWidth,
                        c = n[0].offsetHeight;
                    "top" == i && c != a && (t.top = t.top + a - c);
                    var d = this.getViewportAdjustedDelta(i, t, l, c);
                    d.left ? t.left += d.left : t.top += d.top;
                    var h = /top|bottom/.test(i),
                        u = h ? 2 * d.left - s + l : 2 * d.top - a + c,
                        p = h ? "offsetWidth" : "offsetHeight";
                    n.offset(t),
                        this.replaceArrow(u, n[0][p], h)
                },
                i.prototype.replaceArrow = function (e, t, i) {
                    this.arrow().css(i ? "left" : "top", 50 * (1 - e / t) + "%").css(i ? "top" : "left", "")
                },
                i.prototype.setContent = function () {
                    var e = this.tip(),
                        t = this.getTitle();
                    e.find(".tooltip-inner")[this.options.html ? "html" : "text"](t),
                        e.removeClass("fade in top bottom left right")
                },
                i.prototype.hide = function (t) {
                    function n() {
                        "in" != s.hoverState && a.detach(),
                            s.$element.removeAttr("aria-describedby").trigger("hidden.bs." + s.type),
                        t && t()
                    }

                    var s = this,
                        a = e(this.$tip),
                        o = e.Event("hide.bs." + this.type);
                    return this.$element.trigger(o),
                        o.isDefaultPrevented() ? void 0 : (a.removeClass("in"), e.support.transition && a.hasClass("fade") ? a.one("bsTransitionEnd", n).emulateTransitionEnd(i.TRANSITION_DURATION) : n(), this.hoverState = null, this)
                },
                i.prototype.fixTitle = function () {
                    var e = this.$element;
                    (e.attr("title") || "string" != typeof e.attr("data-original-title")) && e.attr("data-original-title", e.attr("title") || "").attr("title", "")
                },
                i.prototype.hasContent = function () {
                    return this.getTitle()
                },
                i.prototype.getPosition = function (t) {
                    t = t || this.$element;
                    var i = t[0],
                        n = "BODY" == i.tagName,
                        s = i.getBoundingClientRect();
                    null == s.width && (s = e.extend({}, s, {
                        width: s.right - s.left,
                        height: s.bottom - s.top
                    }));
                    var a = n ? {
                            top: 0,
                            left: 0
                        } : t.offset(),
                        o = {
                            scroll: n ? document.documentElement.scrollTop || document.body.scrollTop : t.scrollTop()
                        },
                        r = n ? {
                            width: e(window).width(),
                            height: e(window).height()
                        } : null;
                    return e.extend({}, s, o, r, a)
                },
                i.prototype.getCalculatedOffset = function (e, t, i, n) {
                    return "bottom" == e ? {
                        top: t.top + t.height,
                        left: t.left + t.width / 2 - i / 2
                    } : "top" == e ? {
                        top: t.top - n,
                        left: t.left + t.width / 2 - i / 2
                    } : "left" == e ? {
                        top: t.top + t.height / 2 - n / 2,
                        left: t.left - i
                    } : {
                        top: t.top + t.height / 2 - n / 2,
                        left: t.left + t.width
                    }
                },
                i.prototype.getViewportAdjustedDelta = function (e, t, i, n) {
                    var s = {
                        top: 0,
                        left: 0
                    };
                    if (!this.$viewport) return s;
                    var a = this.options.viewport && this.options.viewport.padding || 0,
                        o = this.getPosition(this.$viewport);
                    if (/right|left/.test(e)) {
                        var r = t.top - a - o.scroll,
                            l = t.top + a - o.scroll + n;
                        r < o.top ? s.top = o.top - r : l > o.top + o.height && (s.top = o.top + o.height - l)
                    } else {
                        var c = t.left - a,
                            d = t.left + a + i;
                        c < o.left ? s.left = o.left - c : d > o.right && (s.left = o.left + o.width - d)
                    }
                    return s
                },
                i.prototype.getTitle = function () {
                    var e, t = this.$element,
                        i = this.options;
                    return e = t.attr("data-original-title") || ("function" == typeof i.title ? i.title.call(t[0]) : i.title)
                },
                i.prototype.getUID = function (e) {
                    do e += ~~(1e6 * Math.random());
                    while (document.getElementById(e));
                    return e
                },
                i.prototype.tip = function () {
                    if (!this.$tip && (this.$tip = e(this.options.template), 1 != this.$tip.length)) throw new Error(this.type + " `template` option must consist of exactly 1 top-level element!");
                    return this.$tip
                },
                i.prototype.arrow = function () {
                    return this.$arrow = this.$arrow || this.tip().find(".tooltip-arrow")
                },
                i.prototype.enable = function () {
                    this.enabled = !0
                },
                i.prototype.disable = function () {
                    this.enabled = !1
                },
                i.prototype.toggleEnabled = function () {
                    this.enabled = !this.enabled
                },
                i.prototype.toggle = function (t) {
                    var i = this;
                    t && (i = e(t.currentTarget).data("bs." + this.type), i || (i = new this.constructor(t.currentTarget, this.getDelegateOptions()), e(t.currentTarget).data("bs." + this.type, i))),
                        t ? (i.inState.click = !i.inState.click, i.isInStateTrue() ? i.enter(i) : i.leave(i)) : i.tip().hasClass("in") ? i.leave(i) : i.enter(i)
                },
                i.prototype.destroy = function () {
                    var e = this;
                    clearTimeout(this.timeout),
                        this.hide(function () {
                            e.$element.off("." + e.type).removeData("bs." + e.type),
                            e.$tip && e.$tip.detach(),
                                e.$tip = null,
                                e.$arrow = null,
                                e.$viewport = null
                        })
                };
            var n = e.fn.tooltip;
            e.fn.tooltip = t,
                e.fn.tooltip.Constructor = i,
                e.fn.tooltip.noConflict = function () {
                    return e.fn.tooltip = n,
                        this
                }
        }(jQuery),
    +
        function (e) {
            "use strict";

            function t(t) {
                return this.each(function () {
                    var n = e(this),
                        s = n.data("bs.popover"),
                        a = "object" == typeof t && t;
                    (s || !/destroy|hide/.test(t)) && (s || n.data("bs.popover", s = new i(this, a)), "string" == typeof t && s[t]())
                })
            }

            var i = function (e, t) {
                this.init("popover", e, t)
            };
            if (!e.fn.tooltip) throw new Error("Popover requires tooltip.js");
            i.VERSION = "3.3.6",
                i.DEFAULTS = e.extend({}, e.fn.tooltip.Constructor.DEFAULTS, {
                    placement: "right",
                    trigger: "click",
                    content: "",
                    template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
                }),
                i.prototype = e.extend({}, e.fn.tooltip.Constructor.prototype),
                i.prototype.constructor = i,
                i.prototype.getDefaults = function () {
                    return i.DEFAULTS
                },
                i.prototype.setContent = function () {
                    var e = this.tip(),
                        t = this.getTitle(),
                        i = this.getContent();
                    e.find(".popover-title")[this.options.html ? "html" : "text"](t),
                        e.find(".popover-content").children().detach().end()[this.options.html ? "string" == typeof i ? "html" : "append" : "text"](i),
                        e.removeClass("fade top bottom left right in"),
                    e.find(".popover-title").html() || e.find(".popover-title").hide()
                },
                i.prototype.hasContent = function () {
                    return this.getTitle() || this.getContent()
                },
                i.prototype.getContent = function () {
                    var e = this.$element,
                        t = this.options;
                    return e.attr("data-content") || ("function" == typeof t.content ? t.content.call(e[0]) : t.content)
                },
                i.prototype.arrow = function () {
                    return this.$arrow = this.$arrow || this.tip().find(".arrow")
                };
            var n = e.fn.popover;
            e.fn.popover = t,
                e.fn.popover.Constructor = i,
                e.fn.popover.noConflict = function () {
                    return e.fn.popover = n,
                        this
                }
        }(jQuery),
    +
        function (e) {
            "use strict";

            function t(i, n) {
                this.$body = e(document.body),
                    this.$scrollElement = e(e(i).is(document.body) ? window : i),
                    this.options = e.extend({}, t.DEFAULTS, n),
                    this.selector = (this.options.target || "") + " .nav li > a",
                    this.offsets = [],
                    this.targets = [],
                    this.activeTarget = null,
                    this.scrollHeight = 0,
                    this.$scrollElement.on("scroll.bs.scrollspy", e.proxy(this.process, this)),
                    this.refresh(),
                    this.process()
            }

            function i(i) {
                return this.each(function () {
                    var n = e(this),
                        s = n.data("bs.scrollspy"),
                        a = "object" == typeof i && i;
                    s || n.data("bs.scrollspy", s = new t(this, a)),
                    "string" == typeof i && s[i]()
                })
            }

            t.VERSION = "3.3.6",
                t.DEFAULTS = {
                    offset: 10
                },
                t.prototype.getScrollHeight = function () {
                    return this.$scrollElement[0].scrollHeight || Math.max(this.$body[0].scrollHeight, document.documentElement.scrollHeight)
                },
                t.prototype.refresh = function () {
                    var t = this,
                        i = "offset",
                        n = 0;
                    this.offsets = [],
                        this.targets = [],
                        this.scrollHeight = this.getScrollHeight(),
                    e.isWindow(this.$scrollElement[0]) || (i = "position", n = this.$scrollElement.scrollTop()),
                        this.$body.find(this.selector).map(function () {
                            var t = e(this),
                                s = t.data("target") || t.attr("href"),
                                a = /^#./.test(s) && e(s);
                            return a && a.length && a.is(":visible") && [
                                    [a[i]().top + n, s]
                                ] || null
                        }).sort(function (e, t) {
                            return e[0] - t[0]
                        }).each(function () {
                            t.offsets.push(this[0]),
                                t.targets.push(this[1])
                        })
                },
                t.prototype.process = function () {
                    var e, t = this.$scrollElement.scrollTop() + this.options.offset,
                        i = this.getScrollHeight(),
                        n = this.options.offset + i - this.$scrollElement.height(),
                        s = this.offsets,
                        a = this.targets,
                        o = this.activeTarget;
                    if (this.scrollHeight != i && this.refresh(), t >= n) return o != (e = a[a.length - 1]) && this.activate(e);
                    if (o && t < s[0]) return this.activeTarget = null,
                        this.clear();
                    for (e = s.length; e--;) o != a[e] && t >= s[e] && (void 0 === s[e + 1] || t < s[e + 1]) && this.activate(a[e])
                },
                t.prototype.activate = function (t) {
                    this.activeTarget = t,
                        this.clear();
                    var i = this.selector + '[data-target="' + t + '"],' + this.selector + '[href="' + t + '"]',
                        n = e(i).parents("li").addClass("active");
                    n.parent(".dropdown-menu").length && (n = n.closest("li.dropdown").addClass("active")),
                        n.trigger("activate.bs.scrollspy")
                },
                t.prototype.clear = function () {
                    e(this.selector).parentsUntil(this.options.target, ".active").removeClass("active")
                };
            var n = e.fn.scrollspy;
            e.fn.scrollspy = i,
                e.fn.scrollspy.Constructor = t,
                e.fn.scrollspy.noConflict = function () {
                    return e.fn.scrollspy = n,
                        this
                },
                e(window).on("load.bs.scrollspy.data-api", function () {
                    e('[data-spy="scroll"]').each(function () {
                        var t = e(this);
                        i.call(t, t.data())
                    })
                })
        }(jQuery),
    +
        function (e) {
            "use strict";

            function t(t) {
                return this.each(function () {
                    var n = e(this),
                        s = n.data("bs.tab");
                    s || n.data("bs.tab", s = new i(this)),
                    "string" == typeof t && s[t]()
                })
            }

            var i = function (t) {
                this.element = e(t)
            };
            i.VERSION = "3.3.6",
                i.TRANSITION_DURATION = 150,
                i.prototype.show = function () {
                    var t = this.element,
                        i = t.closest("ul:not(.dropdown-menu)"),
                        n = t.data("target");
                    if (n || (n = t.attr("href"), n = n && n.replace(/.*(?=#[^\s]*$)/, "")), !t.parent("li").hasClass("active")) {
                        var s = i.find(".active:last a"),
                            a = e.Event("hide.bs.tab", {
                                relatedTarget: t[0]
                            }),
                            o = e.Event("show.bs.tab", {
                                relatedTarget: s[0]
                            });
                        if (s.trigger(a), t.trigger(o), !o.isDefaultPrevented() && !a.isDefaultPrevented()) {
                            var r = e(n);
                            this.activate(t.closest("li"), i),
                                this.activate(r, r.parent(), function () {
                                    s.trigger({
                                        type: "hidden.bs.tab",
                                        relatedTarget: t[0]
                                    }),
                                        t.trigger({
                                            type: "shown.bs.tab",
                                            relatedTarget: s[0]
                                        })
                                })
                        }
                    }
                },
                i.prototype.activate = function (t, n, s) {
                    function a() {
                        o.removeClass("active").find("> .dropdown-menu > .active").removeClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded", !1),
                            t.addClass("active").find('[data-toggle="tab"]').attr("aria-expanded", !0),
                            r ? (t[0].offsetWidth, t.addClass("in")) : t.removeClass("fade"),
                        t.parent(".dropdown-menu").length && t.closest("li.dropdown").addClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded", !0),
                        s && s()
                    }

                    var o = n.find("> .active"),
                        r = s && e.support.transition && (o.length && o.hasClass("fade") || !!n.find("> .fade").length);
                    o.length && r ? o.one("bsTransitionEnd", a).emulateTransitionEnd(i.TRANSITION_DURATION) : a(),
                        o.removeClass("in")
                };
            var n = e.fn.tab;
            e.fn.tab = t,
                e.fn.tab.Constructor = i,
                e.fn.tab.noConflict = function () {
                    return e.fn.tab = n,
                        this
                };
            var s = function (i) {
                i.preventDefault(),
                    t.call(e(this), "show")
            };
            e(document).on("click.bs.tab.data-api", '[data-toggle="tab"]', s).on("click.bs.tab.data-api", '[data-toggle="pill"]', s)
        }(jQuery),
    +
        function (e) {
            "use strict";

            function t(t) {
                return this.each(function () {
                    var n = e(this),
                        s = n.data("bs.affix"),
                        a = "object" == typeof t && t;
                    s || n.data("bs.affix", s = new i(this, a)),
                    "string" == typeof t && s[t]()
                })
            }

            var i = function (t, n) {
                this.options = e.extend({}, i.DEFAULTS, n),
                    this.$target = e(this.options.target).on("scroll.bs.affix.data-api", e.proxy(this.checkPosition, this)).on("click.bs.affix.data-api", e.proxy(this.checkPositionWithEventLoop, this)),
                    this.$element = e(t),
                    this.affixed = null,
                    this.unpin = null,
                    this.pinnedOffset = null,
                    this.checkPosition()
            };
            i.VERSION = "3.3.6",
                i.RESET = "affix affix-top affix-bottom",
                i.DEFAULTS = {
                    offset: 0,
                    target: window
                },
                i.prototype.getState = function (e, t, i, n) {
                    var s = this.$target.scrollTop(),
                        a = this.$element.offset(),
                        o = this.$target.height();
                    if (null != i && "top" == this.affixed) return i > s && "top";
                    if ("bottom" == this.affixed) return null != i ? !(s + this.unpin <= a.top) && "bottom" : !(e - n >= s + o) && "bottom";
                    var r = null == this.affixed,
                        l = r ? s : a.top,
                        c = r ? o : t;
                    return null != i && i >= s ? "top" : null != n && l + c >= e - n && "bottom"
                },
                i.prototype.getPinnedOffset = function () {
                    if (this.pinnedOffset) return this.pinnedOffset;
                    this.$element.removeClass(i.RESET).addClass("affix");
                    var e = this.$target.scrollTop(),
                        t = this.$element.offset();
                    return this.pinnedOffset = t.top - e
                },
                i.prototype.checkPositionWithEventLoop = function () {
                    setTimeout(e.proxy(this.checkPosition, this), 1)
                },
                i.prototype.checkPosition = function () {
                    if (this.$element.is(":visible")) {
                        var t = this.$element.height(),
                            n = this.options.offset,
                            s = n.top,
                            a = n.bottom,
                            o = Math.max(e(document).height(), e(document.body).height());
                        "object" != typeof n && (a = s = n),
                        "function" == typeof s && (s = n.top(this.$element)),
                        "function" == typeof a && (a = n.bottom(this.$element));
                        var r = this.getState(o, t, s, a);
                        if (this.affixed != r) {
                            null != this.unpin && this.$element.css("top", "");
                            var l = "affix" + (r ? "-" + r : ""),
                                c = e.Event(l + ".bs.affix");
                            if (this.$element.trigger(c), c.isDefaultPrevented()) return;
                            this.affixed = r,
                                this.unpin = "bottom" == r ? this.getPinnedOffset() : null,
                                this.$element.removeClass(i.RESET).addClass(l).trigger(l.replace("affix", "affixed") + ".bs.affix")
                        }
                        "bottom" == r && this.$element.offset({
                            top: o - t - a
                        })
                    }
                };
            var n = e.fn.affix;
            e.fn.affix = t,
                e.fn.affix.Constructor = i,
                e.fn.affix.noConflict = function () {
                    return e.fn.affix = n,
                        this
                },
                e(window).on("load", function () {
                    e('[data-spy="affix"]').each(function () {
                        var i = e(this),
                            n = i.data();
                        n.offset = n.offset || {},
                        null != n.offsetBottom && (n.offset.bottom = n.offsetBottom),
                        null != n.offsetTop && (n.offset.top = n.offsetTop),
                            t.call(i, n)
                    })
                })
        }(jQuery),
    !
        function () {
            "use strict";

            function e(e) {
                e.fn.swiper = function (t) {
                    var n;
                    return e(this).each(function () {
                        var e = new i(this, t);
                        n || (n = e)
                    }),
                        n
                }
            }

            var t, i = function (e, s) {
                function a() {
                    return "horizontal" === _.params.direction
                }

                function o(e) {
                    return Math.floor(e)
                }

                function r() {
                    _.autoplayTimeoutId = setTimeout(function () {
                        _.params.loop ? (_.fixLoop(), _._slideNext()) : _.isEnd ? s.autoplayStopOnLast ? _.stopAutoplay() : _._slideTo(0) : _._slideNext()
                    }, _.params.autoplay)
                }

                function l(e, i) {
                    var n = t(e.target);
                    if (!n.is(i)) if ("string" == typeof i) n = n.parents(i);
                    else if (i.nodeType) {
                        var s;
                        return n.parents().each(function (e, t) {
                            t === i && (s = i)
                        }),
                            s ? i : void 0
                    }
                    if (0 !== n.length) return n[0]
                }

                function c(e, t) {
                    t = t || {};
                    var i = window.MutationObserver || window.WebkitMutationObserver,
                        n = new i(function (e) {
                            e.forEach(function (e) {
                                _.onResize(!0),
                                    _.emit("onObserverUpdate", _, e)
                            })
                        });
                    n.observe(e, {
                        attributes: "undefined" == typeof t.attributes || t.attributes,
                        childList: "undefined" == typeof t.childList || t.childList,
                        characterData: "undefined" == typeof t.characterData || t.characterData
                    }),
                        _.observers.push(n)
                }

                function d(e) {
                    e.originalEvent && (e = e.originalEvent);
                    var t = e.keyCode || e.charCode;
                    if (!_.params.allowSwipeToNext && (a() && 39 === t || !a() && 40 === t)) return !1;
                    if (!_.params.allowSwipeToPrev && (a() && 37 === t || !a() && 38 === t)) return !1;
                    if (!(e.shiftKey || e.altKey || e.ctrlKey || e.metaKey || document.activeElement && document.activeElement.nodeName && ("input" === document.activeElement.nodeName.toLowerCase() || "textarea" === document.activeElement.nodeName.toLowerCase()))) {
                        if (37 === t || 39 === t || 38 === t || 40 === t) {
                            var i = !1;
                            if (_.container.parents(".swiper-slide").length > 0 && 0 === _.container.parents(".swiper-slide-active").length) return;
                            var n = {
                                    left: window.pageXOffset,
                                    top: window.pageYOffset
                                },
                                s = window.innerWidth,
                                o = window.innerHeight,
                                r = _.container.offset();
                            _.rtl && (r.left = r.left - _.container[0].scrollLeft);
                            for (var l = [
                                [r.left, r.top],
                                [r.left + _.width, r.top],
                                [r.left, r.top + _.height],
                                [r.left + _.width, r.top + _.height]
                            ], c = 0; c < l.length; c++) {
                                var d = l[c];
                                d[0] >= n.left && d[0] <= n.left + s && d[1] >= n.top && d[1] <= n.top + o && (i = !0)
                            }
                            if (!i) return
                        }
                        a() ? ((37 === t || 39 === t) && (e.preventDefault ? e.preventDefault() : e.returnValue = !1), (39 === t && !_.rtl || 37 === t && _.rtl) && _.slideNext(), (37 === t && !_.rtl || 39 === t && _.rtl) && _.slidePrev()) : ((38 === t || 40 === t) && (e.preventDefault ? e.preventDefault() : e.returnValue = !1), 40 === t && _.slideNext(), 38 === t && _.slidePrev())
                    }
                }

                function h(e) {
                    e.originalEvent && (e = e.originalEvent);
                    var t = _.mousewheel.event,
                        i = 0;
                    if (e.detail) i = -e.detail;
                    else if ("mousewheel" === t) if (_.params.mousewheelForceToAxis) if (a()) {
                        if (!(Math.abs(e.wheelDeltaX) > Math.abs(e.wheelDeltaY))) return;
                        i = e.wheelDeltaX
                    } else {
                        if (!(Math.abs(e.wheelDeltaY) > Math.abs(e.wheelDeltaX))) return;
                        i = e.wheelDeltaY
                    } else i = e.wheelDelta;
                    else if ("DOMMouseScroll" === t) i = -e.detail;
                    else if ("wheel" === t) if (_.params.mousewheelForceToAxis) if (a()) {
                        if (!(Math.abs(e.deltaX) > Math.abs(e.deltaY))) return;
                        i = -e.deltaX
                    } else {
                        if (!(Math.abs(e.deltaY) > Math.abs(e.deltaX))) return;
                        i = -e.deltaY
                    } else i = Math.abs(e.deltaX) > Math.abs(e.deltaY) ? -e.deltaX : -e.deltaY;
                    if (0 !== i) {
                        if (_.params.mousewheelInvert && (i = -i), _.params.freeMode) {
                            var n = _.getWrapperTranslate() + i * _.params.mousewheelSensitivity,
                                s = _.isBeginning,
                                o = _.isEnd;
                            if (n >= _.minTranslate() && (n = _.minTranslate()), n <= _.maxTranslate() && (n = _.maxTranslate()), _.setWrapperTransition(0), _.setWrapperTranslate(n), _.updateProgress(), _.updateActiveIndex(), (!s && _.isBeginning || !o && _.isEnd) && _.updateClasses(), _.params.freeModeSticky && (clearTimeout(_.mousewheel.timeout), _.mousewheel.timeout = setTimeout(function () {
                                    _.slideReset()
                                }, 300)), 0 === n || n === _.maxTranslate()) return
                        } else {
                            if ((new window.Date).getTime() - _.mousewheel.lastScrollTime > 60) if (0 > i) if (_.isEnd && !_.params.loop || _.animating) {
                                if (_.params.mousewheelReleaseOnEdges) return !0
                            } else _.slideNext();
                            else if (_.isBeginning && !_.params.loop || _.animating) {
                                if (_.params.mousewheelReleaseOnEdges) return !0
                            } else _.slidePrev();
                            _.mousewheel.lastScrollTime = (new window.Date).getTime()
                        }
                        return _.params.autoplay && _.stopAutoplay(),
                            e.preventDefault ? e.preventDefault() : e.returnValue = !1,
                            !1
                    }
                }

                function u(e, i) {
                    e = t(e);
                    var n, s, o;
                    n = e.attr("data-swiper-parallax") || "0",
                        s = e.attr("data-swiper-parallax-x"),
                        o = e.attr("data-swiper-parallax-y"),
                        s || o ? (s = s || "0", o = o || "0") : a() ? (s = n, o = "0") : (o = n, s = "0"),
                        s = s.indexOf("%") >= 0 ? parseInt(s, 10) * i + "%" : s * i + "px",
                        o = o.indexOf("%") >= 0 ? parseInt(o, 10) * i + "%" : o * i + "px",
                        e.transform("translate3d(" + s + ", " + o + ",0px)")
                }

                function p(e) {
                    return 0 !== e.indexOf("on") && (e = e[0] !== e[0].toUpperCase() ? "on" + e[0].toUpperCase() + e.substring(1) : "on" + e),
                        e
                }

                if (!(this instanceof i)) return new i(e, s);
                var f = {
                        direction: "horizontal",
                        touchEventsTarget: "container",
                        initialSlide: 0,
                        speed: 300,
                        autoplay: !1,
                        autoplayDisableOnInteraction: !0,
                        iOSEdgeSwipeDetection: !1,
                        iOSEdgeSwipeThreshold: 20,
                        freeMode: !1,
                        freeModeMomentum: !0,
                        freeModeMomentumRatio: 1,
                        freeModeMomentumBounce: !0,
                        freeModeMomentumBounceRatio: 1,
                        freeModeSticky: !1,
                        freeModeMinimumVelocity: .02,
                        autoHeight: !1,
                        setWrapperSize: !1,
                        virtualTranslate: !1,
                        effect: "slide",
                        coverflow: {
                            rotate: 50,
                            stretch: 0,
                            depth: 100,
                            modifier: 1,
                            slideShadows: !0
                        },
                        cube: {
                            slideShadows: !0,
                            shadow: !0,
                            shadowOffset: 20,
                            shadowScale: .94
                        },
                        fade: {
                            crossFade: !1
                        },
                        parallax: !1,
                        scrollbar: null,
                        scrollbarHide: !0,
                        scrollbarDraggable: !1,
                        scrollbarSnapOnRelease: !1,
                        keyboardControl: !1,
                        mousewheelControl: !1,
                        mousewheelReleaseOnEdges: !1,
                        mousewheelInvert: !1,
                        mousewheelForceToAxis: !1,
                        mousewheelSensitivity: 1,
                        hashnav: !1,
                        breakpoints: void 0,
                        spaceBetween: 0,
                        slidesPerView: 1,
                        slidesPerColumn: 1,
                        slidesPerColumnFill: "column",
                        slidesPerGroup: 1,
                        centeredSlides: !1,
                        slidesOffsetBefore: 0,
                        slidesOffsetAfter: 0,
                        roundLengths: !1,
                        touchRatio: 1,
                        touchAngle: 45,
                        simulateTouch: !0,
                        shortSwipes: !0,
                        longSwipes: !0,
                        longSwipesRatio: .5,
                        longSwipesMs: 300,
                        followFinger: !0,
                        onlyExternal: !1,
                        threshold: 0,
                        touchMoveStopPropagation: !0,
                        pagination: null,
                        paginationElement: "span",
                        paginationClickable: !1,
                        paginationHide: !1,
                        paginationBulletRender: null,
                        resistance: !0,
                        resistanceRatio: .85,
                        nextButton: null,
                        prevButton: null,
                        watchSlidesProgress: !1,
                        watchSlidesVisibility: !1,
                        grabCursor: !1,
                        preventClicks: !0,
                        preventClicksPropagation: !0,
                        slideToClickedSlide: !1,
                        lazyLoading: !1,
                        lazyLoadingInPrevNext: !1,
                        lazyLoadingOnTransitionStart: !1,
                        preloadImages: !0,
                        updateOnImagesReady: !0,
                        loop: !1,
                        loopAdditionalSlides: 0,
                        loopedSlides: null,
                        control: void 0,
                        controlInverse: !1,
                        controlBy: "slide",
                        allowSwipeToPrev: !0,
                        allowSwipeToNext: !0,
                        swipeHandler: null,
                        noSwiping: !0,
                        noSwipingClass: "swiper-no-swiping",
                        slideClass: "swiper-slide",
                        slideActiveClass: "swiper-slide-active",
                        slideVisibleClass: "swiper-slide-visible",
                        slideDuplicateClass: "swiper-slide-duplicate",
                        slideNextClass: "swiper-slide-next",
                        slidePrevClass: "swiper-slide-prev",
                        wrapperClass: "swiper-wrapper",
                        bulletClass: "swiper-pagination-bullet",
                        bulletActiveClass: "swiper-pagination-bullet-active",
                        buttonDisabledClass: "swiper-button-disabled",
                        paginationHiddenClass: "swiper-pagination-hidden",
                        observer: !1,
                        observeParents: !1,
                        a11y: !1,
                        prevSlideMessage: "Previous slide",
                        nextSlideMessage: "Next slide",
                        firstSlideMessage: "This is the first slide",
                        lastSlideMessage: "This is the last slide",
                        paginationBulletMessage: "Go to slide {{index}}",
                        runCallbacksOnInit: !0
                    },
                    m = s && s.virtualTranslate;
                s = s || {};
                var g = {};
                for (var v in s) if ("object" == typeof s[v]) {
                    g[v] = {};
                    for (var y in s[v]) g[v][y] = s[v][y]
                } else g[v] = s[v];
                for (var b in f) if ("undefined" == typeof s[b]) s[b] = f[b];
                else if ("object" == typeof s[b]) for (var w in f[b])"undefined" == typeof s[b][w] && (s[b][w] = f[b][w]);
                var _ = this;
                if (_.params = s, _.originalParams = g, _.classNames = [], "undefined" != typeof t && "undefined" != typeof n && (t = n), ("undefined" != typeof t || (t = "undefined" == typeof n ? window.Dom7 || window.Zepto || window.jQuery : n)) && (_.$ = t, _.currentBreakpoint = void 0, _.getActiveBreakpoint = function () {
                        if (!_.params.breakpoints) return !1;
                        var e, t = !1,
                            i = [];
                        for (e in _.params.breakpoints) _.params.breakpoints.hasOwnProperty(e) && i.push(e);
                        i.sort(function (e, t) {
                            return parseInt(e, 10) > parseInt(t, 10)
                        });
                        for (var n = 0; n < i.length; n++) e = i[n],
                        e >= window.innerWidth && !t && (t = e);
                        return t || "max"
                    }, _.setBreakpoint = function () {
                        var e = _.getActiveBreakpoint();
                        if (e && _.currentBreakpoint !== e) {
                            var t = e in _.params.breakpoints ? _.params.breakpoints[e] : _.originalParams;
                            for (var i in t) _.params[i] = t[i];
                            _.currentBreakpoint = e
                        }
                    }, _.params.breakpoints && _.setBreakpoint(), _.container = t(e), 0 !== _.container.length)) {
                    if (_.container.length > 1) return void _.container.each(function () {
                        new i(this, s)
                    });
                    _.container[0].swiper = _,
                        _.container.data("swiper", _),
                        _.classNames.push("swiper-container-" + _.params.direction),
                    _.params.freeMode && _.classNames.push("swiper-container-free-mode"),
                    _.support.flexbox || (_.classNames.push("swiper-container-no-flexbox"), _.params.slidesPerColumn = 1),
                    _.params.autoHeight && _.classNames.push("swiper-container-autoheight"),
                    (_.params.parallax || _.params.watchSlidesVisibility) && (_.params.watchSlidesProgress = !0),
                    ["cube", "coverflow"].indexOf(_.params.effect) >= 0 && (_.support.transforms3d ? (_.params.watchSlidesProgress = !0, _.classNames.push("swiper-container-3d")) : _.params.effect = "slide"),
                    "slide" !== _.params.effect && _.classNames.push("swiper-container-" + _.params.effect),
                    "cube" === _.params.effect && (_.params.resistanceRatio = 0, _.params.slidesPerView = 1, _.params.slidesPerColumn = 1, _.params.slidesPerGroup = 1, _.params.centeredSlides = !1, _.params.spaceBetween = 0, _.params.virtualTranslate = !0, _.params.setWrapperSize = !1),
                    "fade" === _.params.effect && (_.params.slidesPerView = 1, _.params.slidesPerColumn = 1, _.params.slidesPerGroup = 1, _.params.watchSlidesProgress = !0, _.params.spaceBetween = 0, "undefined" == typeof m && (_.params.virtualTranslate = !0)),
                    _.params.grabCursor && _.support.touch && (_.params.grabCursor = !1),
                        _.wrapper = _.container.children("." + _.params.wrapperClass),
                    _.params.pagination && (_.paginationContainer = t(_.params.pagination), _.params.paginationClickable && _.paginationContainer.addClass("swiper-pagination-clickable")),
                        _.rtl = a() && ("rtl" === _.container[0].dir.toLowerCase() || "rtl" === _.container.css("direction")),
                    _.rtl && _.classNames.push("swiper-container-rtl"),
                    _.rtl && (_.wrongRTL = "-webkit-box" === _.wrapper.css("display")),
                    _.params.slidesPerColumn > 1 && _.classNames.push("swiper-container-multirow"),
                    _.device.android && _.classNames.push("swiper-container-android"),
                        _.container.addClass(_.classNames.join(" ")),
                        _.translate = 0,
                        _.progress = 0,
                        _.velocity = 0,
                        _.lockSwipeToNext = function () {
                            _.params.allowSwipeToNext = !1
                        },
                        _.lockSwipeToPrev = function () {
                            _.params.allowSwipeToPrev = !1
                        },
                        _.lockSwipes = function () {
                            _.params.allowSwipeToNext = _.params.allowSwipeToPrev = !1
                        },
                        _.unlockSwipeToNext = function () {
                            _.params.allowSwipeToNext = !0
                        },
                        _.unlockSwipeToPrev = function () {
                            _.params.allowSwipeToPrev = !0
                        },
                        _.unlockSwipes = function () {
                            _.params.allowSwipeToNext = _.params.allowSwipeToPrev = !0
                        },
                    _.params.grabCursor && (_.container[0].style.cursor = "move", _.container[0].style.cursor = "-webkit-grab", _.container[0].style.cursor = "-moz-grab", _.container[0].style.cursor = "grab"),
                        _.imagesToLoad = [],
                        _.imagesLoaded = 0,
                        _.loadImage = function (e, t, i, n, s) {
                            function a() {
                                s && s()
                            }

                            var o;
                            e.complete && n ? a() : t ? (o = new window.Image, o.onload = a, o.onerror = a, i && (o.srcset = i), t && (o.src = t)) : a()
                        },
                        _.preloadImages = function () {
                            function e() {
                                "undefined" != typeof _ && null !== _ && (void 0 !== _.imagesLoaded && _.imagesLoaded++, _.imagesLoaded === _.imagesToLoad.length && (_.params.updateOnImagesReady && _.update(), _.emit("onImagesReady", _)))
                            }

                            _.imagesToLoad = _.container.find("img");
                            for (var t = 0; t < _.imagesToLoad.length; t++) _.loadImage(_.imagesToLoad[t], _.imagesToLoad[t].currentSrc || _.imagesToLoad[t].getAttribute("src"), _.imagesToLoad[t].srcset || _.imagesToLoad[t].getAttribute("srcset"), !0, e)
                        },
                        _.autoplayTimeoutId = void 0,
                        _.autoplaying = !1,
                        _.autoplayPaused = !1,
                        _.startAutoplay = function () {
                            return "undefined" == typeof _.autoplayTimeoutId && ( !!_.params.autoplay && (!_.autoplaying && (_.autoplaying = !0, _.emit("onAutoplayStart", _), void r())))
                        },
                        _.stopAutoplay = function (e) {
                            _.autoplayTimeoutId && (_.autoplayTimeoutId && clearTimeout(_.autoplayTimeoutId), _.autoplaying = !1, _.autoplayTimeoutId = void 0, _.emit("onAutoplayStop", _))
                        },
                        _.pauseAutoplay = function (e) {
                            _.autoplayPaused || (_.autoplayTimeoutId && clearTimeout(_.autoplayTimeoutId), _.autoplayPaused = !0, 0 === e ? (_.autoplayPaused = !1, r()) : _.wrapper.transitionEnd(function () {
                                _ && (_.autoplayPaused = !1, _.autoplaying ? r() : _.stopAutoplay())
                            }))
                        },
                        _.minTranslate = function () {
                            return -_.snapGrid[0]
                        },
                        _.maxTranslate = function () {
                            return -_.snapGrid[_.snapGrid.length - 1]
                        },
                        _.updateAutoHeight = function () {
                            var e = _.slides.eq(_.activeIndex)[0].offsetHeight;
                            e && _.wrapper.css("height", _.slides.eq(_.activeIndex)[0].offsetHeight + "px")
                        },
                        _.updateContainerSize = function () {
                            var e, t;
                            e = "undefined" != typeof _.params.width ? _.params.width : _.container[0].clientWidth,
                                t = "undefined" != typeof _.params.height ? _.params.height : _.container[0].clientHeight,
                            0 === e && a() || 0 === t && !a() || (e = e - parseInt(_.container.css("padding-left"), 10) - parseInt(_.container.css("padding-right"), 10), t = t - parseInt(_.container.css("padding-top"), 10) - parseInt(_.container.css("padding-bottom"), 10), _.width = e, _.height = t, _.size = a() ? _.width : _.height)
                        },
                        _.updateSlidesSize = function () {
                            _.slides = _.wrapper.children("." + _.params.slideClass),
                                _.snapGrid = [],
                                _.slidesGrid = [],
                                _.slidesSizesGrid = [];
                            var e, t = _.params.spaceBetween,
                                i = -_.params.slidesOffsetBefore,
                                n = 0,
                                s = 0;
                            "string" == typeof t && t.indexOf("%") >= 0 && (t = parseFloat(t.replace("%", "")) / 100 * _.size),
                                _.virtualSize = -t,
                                _.rtl ? _.slides.css({
                                    marginLeft: "",
                                    marginTop: ""
                                }) : _.slides.css({
                                    marginRight: "",
                                    marginBottom: ""
                                });
                            var r;
                            _.params.slidesPerColumn > 1 && (r = Math.floor(_.slides.length / _.params.slidesPerColumn) === _.slides.length / _.params.slidesPerColumn ? _.slides.length : Math.ceil(_.slides.length / _.params.slidesPerColumn) * _.params.slidesPerColumn, "auto" !== _.params.slidesPerView && "row" === _.params.slidesPerColumnFill && (r = Math.max(r, _.params.slidesPerView * _.params.slidesPerColumn)));
                            var l, c = _.params.slidesPerColumn,
                                d = r / c,
                                h = d - (_.params.slidesPerColumn * d - _.slides.length);
                            for (e = 0; e < _.slides.length; e++) {
                                l = 0;
                                var u = _.slides.eq(e);
                                if (_.params.slidesPerColumn > 1) {
                                    var p, f, m;
                                    "column" === _.params.slidesPerColumnFill ? (f = Math.floor(e / c), m = e - f * c, (f > h || f === h && m === c - 1) && ++m >= c && (m = 0, f++), p = f + m * r / c, u.css({
                                        "-webkit-box-ordinal-group": p,
                                        "-moz-box-ordinal-group": p,
                                        "-ms-flex-order": p,
                                        "-webkit-order": p,
                                        order: p
                                    })) : (m = Math.floor(e / d), f = e - m * d),
                                        u.css({
                                            "margin-top": 0 !== m && _.params.spaceBetween && _.params.spaceBetween + "px"
                                        }).attr("data-swiper-column", f).attr("data-swiper-row", m)
                                }
                                "none" !== u.css("display") && ("auto" === _.params.slidesPerView ? (l = a() ? u.outerWidth(!0) : u.outerHeight(!0), _.params.roundLengths && (l = o(l))) : (l = (_.size - (_.params.slidesPerView - 1) * t) / _.params.slidesPerView, _.params.roundLengths && (l = o(l)), a() ? _.slides[e].style.width = l + "px" : _.slides[e].style.height = l + "px"), _.slides[e].swiperSlideSize = l, _.slidesSizesGrid.push(l), _.params.centeredSlides ? (i = i + l / 2 + n / 2 + t, 0 === e && (i = i - _.size / 2 - t), Math.abs(i) < .001 && (i = 0), s % _.params.slidesPerGroup === 0 && _.snapGrid.push(i), _.slidesGrid.push(i)) : (s % _.params.slidesPerGroup === 0 && _.snapGrid.push(i), _.slidesGrid.push(i), i = i + l + t), _.virtualSize += l + t, n = l, s++)
                            }
                            _.virtualSize = Math.max(_.virtualSize, _.size) + _.params.slidesOffsetAfter;
                            var g;
                            if (_.rtl && _.wrongRTL && ("slide" === _.params.effect || "coverflow" === _.params.effect) && _.wrapper.css({
                                    width: _.virtualSize + _.params.spaceBetween + "px"
                                }), (!_.support.flexbox || _.params.setWrapperSize) && (a() ? _.wrapper.css({
                                    width: _.virtualSize + _.params.spaceBetween + "px"
                                }) : _.wrapper.css({
                                    height: _.virtualSize + _.params.spaceBetween + "px"
                                })), _.params.slidesPerColumn > 1 && (_.virtualSize = (l + _.params.spaceBetween) * r, _.virtualSize = Math.ceil(_.virtualSize / _.params.slidesPerColumn) - _.params.spaceBetween, _.wrapper.css({
                                    width: _.virtualSize + _.params.spaceBetween + "px"
                                }), _.params.centeredSlides)) {
                                for (g = [], e = 0; e < _.snapGrid.length; e++) _.snapGrid[e] < _.virtualSize + _.snapGrid[0] && g.push(_.snapGrid[e]);
                                _.snapGrid = g
                            }
                            if (!_.params.centeredSlides) {
                                for (g = [], e = 0; e < _.snapGrid.length; e++) _.snapGrid[e] <= _.virtualSize - _.size && g.push(_.snapGrid[e]);
                                _.snapGrid = g,
                                Math.floor(_.virtualSize - _.size) > Math.floor(_.snapGrid[_.snapGrid.length - 1]) && _.snapGrid.push(_.virtualSize - _.size)
                            }
                            0 === _.snapGrid.length && (_.snapGrid = [0]),
                            0 !== _.params.spaceBetween && (a() ? _.rtl ? _.slides.css({
                                marginLeft: t + "px"
                            }) : _.slides.css({
                                marginRight: t + "px"
                            }) : _.slides.css({
                                marginBottom: t + "px"
                            })),
                            _.params.watchSlidesProgress && _.updateSlidesOffset()
                        },
                        _.updateSlidesOffset = function () {
                            for (var e = 0; e < _.slides.length; e++) _.slides[e].swiperSlideOffset = a() ? _.slides[e].offsetLeft : _.slides[e].offsetTop
                        },
                        _.updateSlidesProgress = function (e) {
                            if ("undefined" == typeof e && (e = _.translate || 0), 0 !== _.slides.length) {
                                "undefined" == typeof _.slides[0].swiperSlideOffset && _.updateSlidesOffset();
                                var t = -e;
                                _.rtl && (t = e),
                                    _.slides.removeClass(_.params.slideVisibleClass);
                                for (var i = 0; i < _.slides.length; i++) {
                                    var n = _.slides[i],
                                        s = (t - n.swiperSlideOffset) / (n.swiperSlideSize + _.params.spaceBetween);
                                    if (_.params.watchSlidesVisibility) {
                                        var a = -(t - n.swiperSlideOffset),
                                            o = a + _.slidesSizesGrid[i],
                                            r = a >= 0 && a < _.size || o > 0 && o <= _.size || 0 >= a && o >= _.size;
                                        r && _.slides.eq(i).addClass(_.params.slideVisibleClass)
                                    }
                                    n.progress = _.rtl ? -s : s
                                }
                            }
                        },
                        _.updateProgress = function (e) {
                            "undefined" == typeof e && (e = _.translate || 0);
                            var t = _.maxTranslate() - _.minTranslate(),
                                i = _.isBeginning,
                                n = _.isEnd;
                            0 === t ? (_.progress = 0, _.isBeginning = _.isEnd = !0) : (_.progress = (e - _.minTranslate()) / t, _.isBeginning = _.progress <= 0, _.isEnd = _.progress >= 1),
                            _.isBeginning && !i && _.emit("onReachBeginning", _),
                            _.isEnd && !n && _.emit("onReachEnd", _),
                            _.params.watchSlidesProgress && _.updateSlidesProgress(e),
                                _.emit("onProgress", _, _.progress)
                        },
                        _.updateActiveIndex = function () {
                            var e, t, i, n = _.rtl ? _.translate : -_.translate;
                            for (t = 0; t < _.slidesGrid.length; t++)"undefined" != typeof _.slidesGrid[t + 1] ? n >= _.slidesGrid[t] && n < _.slidesGrid[t + 1] - (_.slidesGrid[t + 1] - _.slidesGrid[t]) / 2 ? e = t : n >= _.slidesGrid[t] && n < _.slidesGrid[t + 1] && (e = t + 1) : n >= _.slidesGrid[t] && (e = t);
                            (0 > e || "undefined" == typeof e) && (e = 0),
                                i = Math.floor(e / _.params.slidesPerGroup),
                            i >= _.snapGrid.length && (i = _.snapGrid.length - 1),
                            e !== _.activeIndex && (_.snapIndex = i, _.previousIndex = _.activeIndex, _.activeIndex = e, _.updateClasses())
                        },
                        _.updateClasses = function () {
                            _.slides.removeClass(_.params.slideActiveClass + " " + _.params.slideNextClass + " " + _.params.slidePrevClass);
                            var e = _.slides.eq(_.activeIndex);
                            if (e.addClass(_.params.slideActiveClass), e.next("." + _.params.slideClass).addClass(_.params.slideNextClass), e.prev("." + _.params.slideClass).addClass(_.params.slidePrevClass), _.bullets && _.bullets.length > 0) {
                                _.bullets.removeClass(_.params.bulletActiveClass);
                                var i;
                                _.params.loop ? (i = Math.ceil(_.activeIndex - _.loopedSlides) / _.params.slidesPerGroup, i > _.slides.length - 1 - 2 * _.loopedSlides && (i -= _.slides.length - 2 * _.loopedSlides), i > _.bullets.length - 1 && (i -= _.bullets.length)) : i = "undefined" != typeof _.snapIndex ? _.snapIndex : _.activeIndex || 0,
                                    _.paginationContainer.length > 1 ? _.bullets.each(function () {
                                        t(this).index() === i && t(this).addClass(_.params.bulletActiveClass)
                                    }) : _.bullets.eq(i).addClass(_.params.bulletActiveClass)
                            }
                            _.params.loop || (_.params.prevButton && (_.isBeginning ? (t(_.params.prevButton).addClass(_.params.buttonDisabledClass), _.params.a11y && _.a11y && _.a11y.disable(t(_.params.prevButton))) : (t(_.params.prevButton).removeClass(_.params.buttonDisabledClass), _.params.a11y && _.a11y && _.a11y.enable(t(_.params.prevButton)))), _.params.nextButton && (_.isEnd ? (t(_.params.nextButton).addClass(_.params.buttonDisabledClass), _.params.a11y && _.a11y && _.a11y.disable(t(_.params.nextButton))) : (t(_.params.nextButton).removeClass(_.params.buttonDisabledClass), _.params.a11y && _.a11y && _.a11y.enable(t(_.params.nextButton)))))
                        },
                        _.updatePagination = function () {
                            if (_.params.pagination && _.paginationContainer && _.paginationContainer.length > 0) {
                                for (var e = "", t = _.params.loop ? Math.ceil((_.slides.length - 2 * _.loopedSlides) / _.params.slidesPerGroup) : _.snapGrid.length, i = 0; t > i; i++) e += _.params.paginationBulletRender ? _.params.paginationBulletRender(i, _.params.bulletClass) : "<" + _.params.paginationElement + ' class="' + _.params.bulletClass + '"></' + _.params.paginationElement + ">";
                                _.paginationContainer.html(e),
                                    _.bullets = _.paginationContainer.find("." + _.params.bulletClass),
                                _.params.paginationClickable && _.params.a11y && _.a11y && _.a11y.initPagination()
                            }
                        },
                        _.update = function (e) {
                            function t() {
                                n = Math.min(Math.max(_.translate, _.maxTranslate()), _.minTranslate()),
                                    _.setWrapperTranslate(n),
                                    _.updateActiveIndex(),
                                    _.updateClasses()
                            }

                            if (_.updateContainerSize(), _.updateSlidesSize(), _.updateProgress(), _.updatePagination(), _.updateClasses(), _.params.scrollbar && _.scrollbar && _.scrollbar.set(), e) {
                                var i, n;
                                _.controller && _.controller.spline && (_.controller.spline = void 0),
                                    _.params.freeMode ? (t(), _.params.autoHeight && _.updateAutoHeight()) : (i = ("auto" === _.params.slidesPerView || _.params.slidesPerView > 1) && _.isEnd && !_.params.centeredSlides ? _.slideTo(_.slides.length - 1, 0, !1, !0) : _.slideTo(_.activeIndex, 0, !1, !0), i || t())
                            } else _.params.autoHeight && _.updateAutoHeight()
                        },
                        _.onResize = function (e) {
                            _.params.breakpoints && _.setBreakpoint();
                            var t = _.params.allowSwipeToPrev,
                                i = _.params.allowSwipeToNext;
                            if (_.params.allowSwipeToPrev = _.params.allowSwipeToNext = !0, _.updateContainerSize(), _.updateSlidesSize(), ("auto" === _.params.slidesPerView || _.params.freeMode || e) && _.updatePagination(), _.params.scrollbar && _.scrollbar && _.scrollbar.set(), _.controller && _.controller.spline && (_.controller.spline = void 0), _.params.freeMode) {
                                var n = Math.min(Math.max(_.translate, _.maxTranslate()), _.minTranslate());
                                _.setWrapperTranslate(n),
                                    _.updateActiveIndex(),
                                    _.updateClasses(),
                                _.params.autoHeight && _.updateAutoHeight()
                            } else _.updateClasses(),
                                ("auto" === _.params.slidesPerView || _.params.slidesPerView > 1) && _.isEnd && !_.params.centeredSlides ? _.slideTo(_.slides.length - 1, 0, !1, !0) : _.slideTo(_.activeIndex, 0, !1, !0);
                            _.params.allowSwipeToPrev = t,
                                _.params.allowSwipeToNext = i
                        };
                    var x = ["mousedown", "mousemove", "mouseup"];
                    window.navigator.pointerEnabled ? x = ["pointerdown", "pointermove", "pointerup"] : window.navigator.msPointerEnabled && (x = ["MSPointerDown", "MSPointerMove", "MSPointerUp"]),
                        _.touchEvents = {
                            start: _.support.touch || !_.params.simulateTouch ? "touchstart" : x[0],
                            move: _.support.touch || !_.params.simulateTouch ? "touchmove" : x[1],
                            end: _.support.touch || !_.params.simulateTouch ? "touchend" : x[2]
                        },
                    (window.navigator.pointerEnabled || window.navigator.msPointerEnabled) && ("container" === _.params.touchEventsTarget ? _.container : _.wrapper).addClass("swiper-wp8-" + _.params.direction),
                        _.initEvents = function (e) {
                            var i = e ? "off" : "on",
                                n = e ? "removeEventListener" : "addEventListener",
                                a = "container" === _.params.touchEventsTarget ? _.container[0] : _.wrapper[0],
                                o = _.support.touch ? a : document,
                                r = !!_.params.nested;
                            _.browser.ie ? (a[n](_.touchEvents.start, _.onTouchStart, !1), o[n](_.touchEvents.move, _.onTouchMove, r), o[n](_.touchEvents.end, _.onTouchEnd, !1)) : (_.support.touch && (a[n](_.touchEvents.start, _.onTouchStart, !1), a[n](_.touchEvents.move, _.onTouchMove, r), a[n](_.touchEvents.end, _.onTouchEnd, !1)), !s.simulateTouch || _.device.ios || _.device.android || (a[n]("mousedown", _.onTouchStart, !1), document[n]("mousemove", _.onTouchMove, r), document[n]("mouseup", _.onTouchEnd, !1))),
                                window[n]("resize", _.onResize),
                            _.params.nextButton && (t(_.params.nextButton)[i]("click", _.onClickNext), _.params.a11y && _.a11y && t(_.params.nextButton)[i]("keydown", _.a11y.onEnterKey)),
                            _.params.prevButton && (t(_.params.prevButton)[i]("click", _.onClickPrev), _.params.a11y && _.a11y && t(_.params.prevButton)[i]("keydown", _.a11y.onEnterKey)),
                            _.params.pagination && _.params.paginationClickable && (t(_.paginationContainer)[i]("click", "." + _.params.bulletClass, _.onClickIndex), _.params.a11y && _.a11y && t(_.paginationContainer)[i]("keydown", "." + _.params.bulletClass, _.a11y.onEnterKey)),
                            (_.params.preventClicks || _.params.preventClicksPropagation) && a[n]("click", _.preventClicks, !0)
                        },
                        _.attachEvents = function (e) {
                            _.initEvents()
                        },
                        _.detachEvents = function () {
                            _.initEvents(!0)
                        },
                        _.allowClick = !0,
                        _.preventClicks = function (e) {
                            _.allowClick || (_.params.preventClicks && e.preventDefault(), _.params.preventClicksPropagation && _.animating && (e.stopPropagation(), e.stopImmediatePropagation()))
                        },
                        _.onClickNext = function (e) {
                            e.preventDefault(),
                            (!_.isEnd || _.params.loop) && _.slideNext()
                        },
                        _.onClickPrev = function (e) {
                            e.preventDefault(),
                            (!_.isBeginning || _.params.loop) && _.slidePrev()
                        },
                        _.onClickIndex = function (e) {
                            e.preventDefault();
                            var i = t(this).index() * _.params.slidesPerGroup;
                            _.params.loop && (i += _.loopedSlides),
                                _.slideTo(i)
                        },
                        _.updateClickedSlide = function (e) {
                            var i = l(e, "." + _.params.slideClass),
                                n = !1;
                            if (i) for (var s = 0; s < _.slides.length; s++) _.slides[s] === i && (n = !0);
                            if (!i || !n) return _.clickedSlide = void 0,
                                void(_.clickedIndex = void 0);
                            if (_.clickedSlide = i, _.clickedIndex = t(i).index(), _.params.slideToClickedSlide && void 0 !== _.clickedIndex && _.clickedIndex !== _.activeIndex) {
                                var a, o = _.clickedIndex;
                                if (_.params.loop) {
                                    if (_.animating) return;
                                    a = t(_.clickedSlide).attr("data-swiper-slide-index"),
                                        _.params.centeredSlides ? o < _.loopedSlides - _.params.slidesPerView / 2 || o > _.slides.length - _.loopedSlides + _.params.slidesPerView / 2 ? (_.fixLoop(), o = _.wrapper.children("." + _.params.slideClass + '[data-swiper-slide-index="' + a + '"]:not(.swiper-slide-duplicate)').eq(0).index(), setTimeout(function () {
                                            _.slideTo(o)
                                        }, 0)) : _.slideTo(o) : o > _.slides.length - _.params.slidesPerView ? (_.fixLoop(), o = _.wrapper.children("." + _.params.slideClass + '[data-swiper-slide-index="' + a + '"]:not(.swiper-slide-duplicate)').eq(0).index(), setTimeout(function () {
                                            _.slideTo(o)
                                        }, 0)) : _.slideTo(o)
                                } else _.slideTo(o)
                            }
                        };
                    var C, k, T, S, D, E, I, P, M, j, N = "input, select, textarea, button",
                        A = Date.now(),
                        O = [];
                    _.animating = !1,
                        _.touches = {
                            startX: 0,
                            startY: 0,
                            currentX: 0,
                            currentY: 0,
                            diff: 0
                        };
                    var z, $;
                    if (_.onTouchStart = function (e) {
                            if (e.originalEvent && (e = e.originalEvent), z = "touchstart" === e.type, z || !("which" in e) || 3 !== e.which) {
                                if (_.params.noSwiping && l(e, "." + _.params.noSwipingClass)) return void(_.allowClick = !0);
                                if (!_.params.swipeHandler || l(e, _.params.swipeHandler)) {
                                    var i = _.touches.currentX = "touchstart" === e.type ? e.targetTouches[0].pageX : e.pageX,
                                        n = _.touches.currentY = "touchstart" === e.type ? e.targetTouches[0].pageY : e.pageY;
                                    if (!(_.device.ios && _.params.iOSEdgeSwipeDetection && i <= _.params.iOSEdgeSwipeThreshold)) {
                                        if (C = !0, k = !1, T = !0, D = void 0, $ = void 0, _.touches.startX = i, _.touches.startY = n, S = Date.now(), _.allowClick = !0, _.updateContainerSize(), _.swipeDirection = void 0, _.params.threshold > 0 && (P = !1), "touchstart" !== e.type) {
                                            var s = !0;
                                            t(e.target).is(N) && (s = !1),
                                            document.activeElement && t(document.activeElement).is(N) && document.activeElement.blur(),
                                            s && e.preventDefault()
                                        }
                                        _.emit("onTouchStart", _, e)
                                    }
                                }
                            }
                        }, _.onTouchMove = function (e) {
                            if (e.originalEvent && (e = e.originalEvent), !(z && "mousemove" === e.type || e.preventedByNestedSwiper)) {
                                if (_.params.onlyExternal) return _.allowClick = !1,
                                    void(C && (_.touches.startX = _.touches.currentX = "touchmove" === e.type ? e.targetTouches[0].pageX : e.pageX, _.touches.startY = _.touches.currentY = "touchmove" === e.type ? e.targetTouches[0].pageY : e.pageY, S = Date.now()));
                                if (z && document.activeElement && e.target === document.activeElement && t(e.target).is(N)) return k = !0,
                                    void(_.allowClick = !1);
                                if (T && _.emit("onTouchMove", _, e), !(e.targetTouches && e.targetTouches.length > 1)) {
                                    if (_.touches.currentX = "touchmove" === e.type ? e.targetTouches[0].pageX : e.pageX, _.touches.currentY = "touchmove" === e.type ? e.targetTouches[0].pageY : e.pageY, "undefined" == typeof D) {
                                        var i = 180 * Math.atan2(Math.abs(_.touches.currentY - _.touches.startY), Math.abs(_.touches.currentX - _.touches.startX)) / Math.PI;
                                        D = a() ? i > _.params.touchAngle : 90 - i > _.params.touchAngle
                                    }
                                    if (D && _.emit("onTouchMoveOpposite", _, e), "undefined" == typeof $ && _.browser.ieTouch && (_.touches.currentX !== _.touches.startX || _.touches.currentY !== _.touches.startY) && ($ = !0), C) {
                                        if (D) return void(C = !1);
                                        if ($ || !_.browser.ieTouch) {
                                            _.allowClick = !1,
                                                _.emit("onSliderMove", _, e),
                                                e.preventDefault(),
                                            _.params.touchMoveStopPropagation && !_.params.nested && e.stopPropagation(),
                                            k || (s.loop && _.fixLoop(), I = _.getWrapperTranslate(), _.setWrapperTransition(0), _.animating && _.wrapper.trigger("webkitTransitionEnd transitionend oTransitionEnd MSTransitionEnd msTransitionEnd"), _.params.autoplay && _.autoplaying && (_.params.autoplayDisableOnInteraction ? _.stopAutoplay() : _.pauseAutoplay()), j = !1, _.params.grabCursor && (_.container[0].style.cursor = "move", _.container[0].style.cursor = "-webkit-grabbing", _.container[0].style.cursor = "-moz-grabbin", _.container[0].style.cursor = "grabbing")),
                                                k = !0;
                                            var n = _.touches.diff = a() ? _.touches.currentX - _.touches.startX : _.touches.currentY - _.touches.startY;
                                            n *= _.params.touchRatio,
                                            _.rtl && (n = -n),
                                                _.swipeDirection = n > 0 ? "prev" : "next",
                                                E = n + I;
                                            var o = !0;
                                            if (n > 0 && E > _.minTranslate() ? (o = !1, _.params.resistance && (E = _.minTranslate() - 1 + Math.pow(-_.minTranslate() + I + n, _.params.resistanceRatio))) : 0 > n && E < _.maxTranslate() && (o = !1, _.params.resistance && (E = _.maxTranslate() + 1 - Math.pow(_.maxTranslate() - I - n, _.params.resistanceRatio))), o && (e.preventedByNestedSwiper = !0), !_.params.allowSwipeToNext && "next" === _.swipeDirection && I > E && (E = I), !_.params.allowSwipeToPrev && "prev" === _.swipeDirection && E > I && (E = I), _.params.followFinger) {
                                                if (_.params.threshold > 0) {
                                                    if (!(Math.abs(n) > _.params.threshold || P)) return void(E = I);
                                                    if (!P) return P = !0,
                                                        _.touches.startX = _.touches.currentX,
                                                        _.touches.startY = _.touches.currentY,
                                                        E = I,
                                                        void(_.touches.diff = a() ? _.touches.currentX - _.touches.startX : _.touches.currentY - _.touches.startY)
                                                }
                                                (_.params.freeMode || _.params.watchSlidesProgress) && _.updateActiveIndex(),
                                                _.params.freeMode && (0 === O.length && O.push({
                                                    position: _.touches[a() ? "startX" : "startY"],
                                                    time: S
                                                }), O.push({
                                                    position: _.touches[a() ? "currentX" : "currentY"],
                                                    time: (new window.Date).getTime()
                                                })),
                                                    _.updateProgress(E),
                                                    _.setWrapperTranslate(E)
                                            }
                                        }
                                    }
                                }
                            }
                        }, _.onTouchEnd = function (e) {
                            if (e.originalEvent && (e = e.originalEvent), T && _.emit("onTouchEnd", _, e), T = !1, C) {
                                _.params.grabCursor && k && C && (_.container[0].style.cursor = "move", _.container[0].style.cursor = "-webkit-grab", _.container[0].style.cursor = "-moz-grab", _.container[0].style.cursor = "grab");
                                var i = Date.now(),
                                    n = i - S;
                                if (_.allowClick && (_.updateClickedSlide(e), _.emit("onTap", _, e), 300 > n && i - A > 300 && (M && clearTimeout(M), M = setTimeout(function () {
                                        _ && (_.params.paginationHide && _.paginationContainer.length > 0 && !t(e.target).hasClass(_.params.bulletClass) && _.paginationContainer.toggleClass(_.params.paginationHiddenClass), _.emit("onClick", _, e))
                                    }, 300)), 300 > n && 300 > i - A && (M && clearTimeout(M), _.emit("onDoubleTap", _, e))), A = Date.now(), setTimeout(function () {
                                        _ && (_.allowClick = !0)
                                    }, 0), !C || !k || !_.swipeDirection || 0 === _.touches.diff || E === I) return void(C = k = !1);
                                C = k = !1;
                                var s;
                                if (s = _.params.followFinger ? _.rtl ? _.translate : -_.translate : -E, _.params.freeMode) {
                                    if (s < -_.minTranslate()) return void _.slideTo(_.activeIndex);
                                    if (s > -_.maxTranslate()) return void(_.slides.length < _.snapGrid.length ? _.slideTo(_.snapGrid.length - 1) : _.slideTo(_.slides.length - 1));
                                    if (_.params.freeModeMomentum) {
                                        if (O.length > 1) {
                                            var a = O.pop(),
                                                o = O.pop(),
                                                r = a.position - o.position,
                                                l = a.time - o.time;
                                            _.velocity = r / l,
                                                _.velocity = _.velocity / 2,
                                            Math.abs(_.velocity) < _.params.freeModeMinimumVelocity && (_.velocity = 0),
                                            (l > 150 || (new window.Date).getTime() - a.time > 300) && (_.velocity = 0)
                                        } else _.velocity = 0;
                                        O.length = 0;
                                        var c = 1e3 * _.params.freeModeMomentumRatio,
                                            d = _.velocity * c,
                                            h = _.translate + d;
                                        _.rtl && (h = -h);
                                        var u, p = !1,
                                            f = 20 * Math.abs(_.velocity) * _.params.freeModeMomentumBounceRatio;
                                        if (h < _.maxTranslate()) _.params.freeModeMomentumBounce ? (h + _.maxTranslate() < -f && (h = _.maxTranslate() - f), u = _.maxTranslate(), p = !0, j = !0) : h = _.maxTranslate();
                                        else if (h > _.minTranslate()) _.params.freeModeMomentumBounce ? (h - _.minTranslate() > f && (h = _.minTranslate() + f), u = _.minTranslate(), p = !0, j = !0) : h = _.minTranslate();
                                        else if (_.params.freeModeSticky) {
                                            var m, g = 0;
                                            for (g = 0; g < _.snapGrid.length; g += 1) if (_.snapGrid[g] > -h) {
                                                m = g;
                                                break
                                            }
                                            h = Math.abs(_.snapGrid[m] - h) < Math.abs(_.snapGrid[m - 1] - h) || "next" === _.swipeDirection ? _.snapGrid[m] : _.snapGrid[m - 1],
                                            _.rtl || (h = -h)
                                        }
                                        if (0 !== _.velocity) c = _.rtl ? Math.abs((-h - _.translate) / _.velocity) : Math.abs((h - _.translate) / _.velocity);
                                        else if (_.params.freeModeSticky) return void _.slideReset();
                                        _.params.freeModeMomentumBounce && p ? (_.updateProgress(u), _.setWrapperTransition(c), _.setWrapperTranslate(h), _.onTransitionStart(), _.animating = !0, _.wrapper.transitionEnd(function () {
                                            _ && j && (_.emit("onMomentumBounce", _), _.setWrapperTransition(_.params.speed), _.setWrapperTranslate(u), _.wrapper.transitionEnd(function () {
                                                _ && _.onTransitionEnd()
                                            }))
                                        })) : _.velocity ? (_.updateProgress(h), _.setWrapperTransition(c), _.setWrapperTranslate(h), _.onTransitionStart(), _.animating || (_.animating = !0, _.wrapper.transitionEnd(function () {
                                            _ && _.onTransitionEnd()
                                        }))) : _.updateProgress(h),
                                            _.updateActiveIndex()
                                    }
                                    return void((!_.params.freeModeMomentum || n >= _.params.longSwipesMs) && (_.updateProgress(), _.updateActiveIndex()))
                                }
                                var v, y = 0,
                                    b = _.slidesSizesGrid[0];
                                for (v = 0; v < _.slidesGrid.length; v += _.params.slidesPerGroup)"undefined" != typeof _.slidesGrid[v + _.params.slidesPerGroup] ? s >= _.slidesGrid[v] && s < _.slidesGrid[v + _.params.slidesPerGroup] && (y = v, b = _.slidesGrid[v + _.params.slidesPerGroup] - _.slidesGrid[v]) : s >= _.slidesGrid[v] && (y = v, b = _.slidesGrid[_.slidesGrid.length - 1] - _.slidesGrid[_.slidesGrid.length - 2]);
                                var w = (s - _.slidesGrid[y]) / b;
                                if (n > _.params.longSwipesMs) {
                                    if (!_.params.longSwipes) return void _.slideTo(_.activeIndex);
                                    "next" === _.swipeDirection && (w >= _.params.longSwipesRatio ? _.slideTo(y + _.params.slidesPerGroup) : _.slideTo(y)),
                                    "prev" === _.swipeDirection && (w > 1 - _.params.longSwipesRatio ? _.slideTo(y + _.params.slidesPerGroup) : _.slideTo(y))
                                } else {
                                    if (!_.params.shortSwipes) return void _.slideTo(_.activeIndex);
                                    "next" === _.swipeDirection && _.slideTo(y + _.params.slidesPerGroup),
                                    "prev" === _.swipeDirection && _.slideTo(y)
                                }
                            }
                        }, _._slideTo = function (e, t) {
                            return _.slideTo(e, t, !0, !0)
                        }, _.slideTo = function (e, t, i, n) {
                            "undefined" == typeof i && (i = !0),
                            "undefined" == typeof e && (e = 0),
                            0 > e && (e = 0),
                                _.snapIndex = Math.floor(e / _.params.slidesPerGroup),
                            _.snapIndex >= _.snapGrid.length && (_.snapIndex = _.snapGrid.length - 1);
                            var s = -_.snapGrid[_.snapIndex];
                            _.params.autoplay && _.autoplaying && (n || !_.params.autoplayDisableOnInteraction ? _.pauseAutoplay(t) : _.stopAutoplay()),
                                _.updateProgress(s);
                            for (var a = 0; a < _.slidesGrid.length; a++) -Math.floor(100 * s) >= Math.floor(100 * _.slidesGrid[a]) && (e = a);
                            return !(!_.params.allowSwipeToNext && s < _.translate && s < _.minTranslate()) && (!(!_.params.allowSwipeToPrev && s > _.translate && s > _.maxTranslate() && (_.activeIndex || 0) !== e) && ("undefined" == typeof t && (t = _.params.speed), _.previousIndex = _.activeIndex || 0, _.activeIndex = e, _.params.autoHeight && _.updateAutoHeight(), s === _.translate ? (_.updateClasses(), "slide" !== _.params.effect && _.setWrapperTranslate(s), !1) : (_.updateClasses(), _.onTransitionStart(i), 0 === t ? (_.setWrapperTransition(0), _.setWrapperTranslate(s), _.onTransitionEnd(i)) : (_.setWrapperTransition(t), _.setWrapperTranslate(s), _.animating || (_.animating = !0, _.wrapper.transitionEnd(function () {
                                    _ && _.onTransitionEnd(i)
                                }))), !0)))
                        }, _.onTransitionStart = function (e) {
                            "undefined" == typeof e && (e = !0),
                            _.lazy && _.lazy.onTransitionStart(),
                            e && (_.emit("onTransitionStart", _), _.activeIndex !== _.previousIndex && (_.emit("onSlideChangeStart", _), _.activeIndex > _.previousIndex ? _.emit("onSlideNextStart", _) : _.emit("onSlidePrevStart", _)))
                        }, _.onTransitionEnd = function (e) {
                            _.animating = !1,
                                _.setWrapperTransition(0),
                            "undefined" == typeof e && (e = !0),
                            _.lazy && _.lazy.onTransitionEnd(),
                            e && (_.emit("onTransitionEnd", _), _.activeIndex !== _.previousIndex && (_.emit("onSlideChangeEnd", _), _.activeIndex > _.previousIndex ? _.emit("onSlideNextEnd", _) : _.emit("onSlidePrevEnd", _))),
                            _.params.hashnav && _.hashnav && _.hashnav.setHash()
                        }, _.slideNext = function (e, t, i) {
                            return _.params.loop ? !_.animating && (_.fixLoop(), _.container[0].clientLeft, _.slideTo(_.activeIndex + _.params.slidesPerGroup, t, e, i)) : _.slideTo(_.activeIndex + _.params.slidesPerGroup, t, e, i)
                        }, _._slideNext = function (e) {
                            return _.slideNext(!0, e, !0)
                        }, _.slidePrev = function (e, t, i) {
                            return _.params.loop ? !_.animating && (_.fixLoop(), _.container[0].clientLeft, _.slideTo(_.activeIndex - 1, t, e, i)) : _.slideTo(_.activeIndex - 1, t, e, i)
                        }, _._slidePrev = function (e) {
                            return _.slidePrev(!0, e, !0)
                        }, _.slideReset = function (e, t, i) {
                            return _.slideTo(_.activeIndex, t, e)
                        }, _.setWrapperTransition = function (e, t) {
                            _.wrapper.transition(e),
                            "slide" !== _.params.effect && _.effects[_.params.effect] && _.effects[_.params.effect].setTransition(e),
                            _.params.parallax && _.parallax && _.parallax.setTransition(e),
                            _.params.scrollbar && _.scrollbar && _.scrollbar.setTransition(e),
                            _.params.control && _.controller && _.controller.setTransition(e, t),
                                _.emit("onSetTransition", _, e)
                        }, _.setWrapperTranslate = function (e, t, i) {
                            var n = 0,
                                s = 0,
                                r = 0;
                            a() ? n = _.rtl ? -e : e : s = e,
                            _.params.roundLengths && (n = o(n), s = o(s)),
                            _.params.virtualTranslate || (_.support.transforms3d ? _.wrapper.transform("translate3d(" + n + "px, " + s + "px, " + r + "px)") : _.wrapper.transform("translate(" + n + "px, " + s + "px)")),
                                _.translate = a() ? n : s;
                            var l, c = _.maxTranslate() - _.minTranslate();
                            l = 0 === c ? 0 : (e - _.minTranslate()) / c,
                            l !== _.progress && _.updateProgress(e),
                            t && _.updateActiveIndex(),
                            "slide" !== _.params.effect && _.effects[_.params.effect] && _.effects[_.params.effect].setTranslate(_.translate),
                            _.params.parallax && _.parallax && _.parallax.setTranslate(_.translate),
                            _.params.scrollbar && _.scrollbar && _.scrollbar.setTranslate(_.translate),
                            _.params.control && _.controller && _.controller.setTranslate(_.translate, i),
                                _.emit("onSetTranslate", _, _.translate)
                        }, _.getTranslate = function (e, t) {
                            var i, n, s, a;
                            return "undefined" == typeof t && (t = "x"),
                                _.params.virtualTranslate ? _.rtl ? -_.translate : _.translate : (s = window.getComputedStyle(e, null), window.WebKitCSSMatrix ? (n = s.transform || s.webkitTransform, n.split(",").length > 6 && (n = n.split(", ").map(function (e) {
                                    return e.replace(",", ".")
                                }).join(", ")), a = new window.WebKitCSSMatrix("none" === n ? "" : n)) : (a = s.MozTransform || s.OTransform || s.MsTransform || s.msTransform || s.transform || s.getPropertyValue("transform").replace("translate(", "matrix(1, 0, 0, 1,"), i = a.toString().split(",")), "x" === t && (n = window.WebKitCSSMatrix ? a.m41 : 16 === i.length ? parseFloat(i[12]) : parseFloat(i[4])), "y" === t && (n = window.WebKitCSSMatrix ? a.m42 : 16 === i.length ? parseFloat(i[13]) : parseFloat(i[5])), _.rtl && n && (n = -n), n || 0)
                        }, _.getWrapperTranslate = function (e) {
                            return "undefined" == typeof e && (e = a() ? "x" : "y"),
                                _.getTranslate(_.wrapper[0], e)
                        }, _.observers = [], _.initObservers = function () {
                            if (_.params.observeParents) for (var e = _.container.parents(), t = 0; t < e.length; t++) c(e[t]);
                            c(_.container[0], {
                                childList: !1
                            }),
                                c(_.wrapper[0], {
                                    attributes: !1
                                })
                        }, _.disconnectObservers = function () {
                            for (var e = 0; e < _.observers.length; e++) _.observers[e].disconnect();
                            _.observers = []
                        }, _.createLoop = function () {
                            _.wrapper.children("." + _.params.slideClass + "." + _.params.slideDuplicateClass).remove();
                            var e = _.wrapper.children("." + _.params.slideClass);
                            "auto" !== _.params.slidesPerView || _.params.loopedSlides || (_.params.loopedSlides = e.length),
                                _.loopedSlides = parseInt(_.params.loopedSlides || _.params.slidesPerView, 10),
                                _.loopedSlides = _.loopedSlides + _.params.loopAdditionalSlides,
                            _.loopedSlides > e.length && (_.loopedSlides = e.length);
                            var i, n = [],
                                s = [];
                            for (e.each(function (i, a) {
                                var o = t(this);
                                i < _.loopedSlides && s.push(a),
                                i < e.length && i >= e.length - _.loopedSlides && n.push(a),
                                    o.attr("data-swiper-slide-index", i)
                            }), i = 0; i < s.length; i++) _.wrapper.append(t(s[i].cloneNode(!0)).addClass(_.params.slideDuplicateClass));
                            for (i = n.length - 1; i >= 0; i--) _.wrapper.prepend(t(n[i].cloneNode(!0)).addClass(_.params.slideDuplicateClass))
                        }, _.destroyLoop = function () {
                            _.wrapper.children("." + _.params.slideClass + "." + _.params.slideDuplicateClass).remove(),
                                _.slides.removeAttr("data-swiper-slide-index")
                        }, _.fixLoop = function () {
                            var e;
                            _.activeIndex < _.loopedSlides ? (e = _.slides.length - 3 * _.loopedSlides + _.activeIndex, e += _.loopedSlides, _.slideTo(e, 0, !1, !0)) : ("auto" === _.params.slidesPerView && _.activeIndex >= 2 * _.loopedSlides || _.activeIndex > _.slides.length - 2 * _.params.slidesPerView) && (e = -_.slides.length + _.activeIndex + _.loopedSlides, e += _.loopedSlides, _.slideTo(e, 0, !1, !0))
                        }, _.appendSlide = function (e) {
                            if (_.params.loop && _.destroyLoop(), "object" == typeof e && e.length) for (var t = 0; t < e.length; t++) e[t] && _.wrapper.append(e[t]);
                            else _.wrapper.append(e);
                            _.params.loop && _.createLoop(),
                            _.params.observer && _.support.observer || _.update(!0)
                        }, _.prependSlide = function (e) {
                            _.params.loop && _.destroyLoop();
                            var t = _.activeIndex + 1;
                            if ("object" == typeof e && e.length) {
                                for (var i = 0; i < e.length; i++) e[i] && _.wrapper.prepend(e[i]);
                                t = _.activeIndex + e.length
                            } else _.wrapper.prepend(e);
                            _.params.loop && _.createLoop(),
                            _.params.observer && _.support.observer || _.update(!0),
                                _.slideTo(t, 0, !1)
                        }, _.removeSlide = function (e) {
                            _.params.loop && (_.destroyLoop(), _.slides = _.wrapper.children("." + _.params.slideClass));
                            var t, i = _.activeIndex;
                            if ("object" == typeof e && e.length) {
                                for (var n = 0; n < e.length; n++) t = e[n],
                                _.slides[t] && _.slides.eq(t).remove(),
                                i > t && i--;
                                i = Math.max(i, 0)
                            } else t = e,
                            _.slides[t] && _.slides.eq(t).remove(),
                            i > t && i--,
                                i = Math.max(i, 0);
                            _.params.loop && _.createLoop(),
                            _.params.observer && _.support.observer || _.update(!0),
                                _.params.loop ? _.slideTo(i + _.loopedSlides, 0, !1) : _.slideTo(i, 0, !1)
                        }, _.removeAllSlides = function () {
                            for (var e = [], t = 0; t < _.slides.length; t++) e.push(t);
                            _.removeSlide(e)
                        }, _.effects = {
                            fade: {
                                setTranslate: function () {
                                    for (var e = 0; e < _.slides.length; e++) {
                                        var t = _.slides.eq(e),
                                            i = t[0].swiperSlideOffset,
                                            n = -i;
                                        _.params.virtualTranslate || (n -= _.translate);
                                        var s = 0;
                                        a() || (s = n, n = 0);
                                        var o = _.params.fade.crossFade ? Math.max(1 - Math.abs(t[0].progress), 0) : 1 + Math.min(Math.max(t[0].progress, -1), 0);
                                        t.css({
                                            opacity: o
                                        }).transform("translate3d(" + n + "px, " + s + "px, 0px)")
                                    }
                                },
                                setTransition: function (e) {
                                    if (_.slides.transition(e), _.params.virtualTranslate && 0 !== e) {
                                        var t = !1;
                                        _.slides.transitionEnd(function () {
                                            if (!t && _) {
                                                t = !0,
                                                    _.animating = !1;
                                                for (var e = ["webkitTransitionEnd", "transitionend", "oTransitionEnd", "MSTransitionEnd", "msTransitionEnd"], i = 0; i < e.length; i++) _.wrapper.trigger(e[i])
                                            }
                                        })
                                    }
                                }
                            },
                            cube: {
                                setTranslate: function () {
                                    var e, i = 0;
                                    _.params.cube.shadow && (a() ? (e = _.wrapper.find(".swiper-cube-shadow"), 0 === e.length && (e = t('<div class="swiper-cube-shadow"></div>'), _.wrapper.append(e)), e.css({
                                        height: _.width + "px"
                                    })) : (e = _.container.find(".swiper-cube-shadow"), 0 === e.length && (e = t('<div class="swiper-cube-shadow"></div>'), _.container.append(e))));
                                    for (var n = 0; n < _.slides.length; n++) {
                                        var s = _.slides.eq(n),
                                            o = 90 * n,
                                            r = Math.floor(o / 360);
                                        _.rtl && (o = -o, r = Math.floor(-o / 360));
                                        var l = Math.max(Math.min(s[0].progress, 1), -1),
                                            c = 0,
                                            d = 0,
                                            h = 0;
                                        n % 4 === 0 ? (c = 4 * -r * _.size, h = 0) : (n - 1) % 4 === 0 ? (c = 0, h = 4 * -r * _.size) : (n - 2) % 4 === 0 ? (c = _.size + 4 * r * _.size, h = _.size) : (n - 3) % 4 === 0 && (c = -_.size, h = 3 * _.size + 4 * _.size * r),
                                        _.rtl && (c = -c),
                                        a() || (d = c, c = 0);
                                        var u = "rotateX(" + (a() ? 0 : -o) + "deg) rotateY(" + (a() ? o : 0) + "deg) translate3d(" + c + "px, " + d + "px, " + h + "px)";
                                        if (1 >= l && l > -1 && (i = 90 * n + 90 * l, _.rtl && (i = 90 * -n - 90 * l)), s.transform(u), _.params.cube.slideShadows) {
                                            var p = a() ? s.find(".swiper-slide-shadow-left") : s.find(".swiper-slide-shadow-top"),
                                                f = a() ? s.find(".swiper-slide-shadow-right") : s.find(".swiper-slide-shadow-bottom");
                                            0 === p.length && (p = t('<div class="swiper-slide-shadow-' + (a() ? "left" : "top") + '"></div>'), s.append(p)),
                                            0 === f.length && (f = t('<div class="swiper-slide-shadow-' + (a() ? "right" : "bottom") + '"></div>'), s.append(f)),
                                                s[0].progress,
                                            p.length && (p[0].style.opacity = -s[0].progress),
                                            f.length && (f[0].style.opacity = s[0].progress)
                                        }
                                    }
                                    if (_.wrapper.css({
                                            "-webkit-transform-origin": "50% 50% -" + _.size / 2 + "px",
                                            "-moz-transform-origin": "50% 50% -" + _.size / 2 + "px",
                                            "-ms-transform-origin": "50% 50% -" + _.size / 2 + "px",
                                            "transform-origin": "50% 50% -" + _.size / 2 + "px"
                                        }), _.params.cube.shadow) if (a()) e.transform("translate3d(0px, " + (_.width / 2 + _.params.cube.shadowOffset) + "px, " + -_.width / 2 + "px) rotateX(90deg) rotateZ(0deg) scale(" + _.params.cube.shadowScale + ")");
                                    else {
                                        var m = Math.abs(i) - 90 * Math.floor(Math.abs(i) / 90),
                                            g = 1.5 - (Math.sin(2 * m * Math.PI / 360) / 2 + Math.cos(2 * m * Math.PI / 360) / 2),
                                            v = _.params.cube.shadowScale,
                                            y = _.params.cube.shadowScale / g,
                                            b = _.params.cube.shadowOffset;
                                        e.transform("scale3d(" + v + ", 1, " + y + ") translate3d(0px, " + (_.height / 2 + b) + "px, " + -_.height / 2 / y + "px) rotateX(-90deg)")
                                    }
                                    var w = _.isSafari || _.isUiWebView ? -_.size / 2 : 0;
                                    _.wrapper.transform("translate3d(0px,0," + w + "px) rotateX(" + (a() ? 0 : i) + "deg) rotateY(" + (a() ? -i : 0) + "deg)")
                                },
                                setTransition: function (e) {
                                    _.slides.transition(e).find(".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left").transition(e),
                                    _.params.cube.shadow && !a() && _.container.find(".swiper-cube-shadow").transition(e)
                                }
                            },
                            coverflow: {
                                setTranslate: function () {
                                    for (var e = _.translate, i = a() ? -e + _.width / 2 : -e + _.height / 2, n = a() ? _.params.coverflow.rotate : -_.params.coverflow.rotate, s = _.params.coverflow.depth, o = 0, r = _.slides.length; r > o; o++) {
                                        var l = _.slides.eq(o),
                                            c = _.slidesSizesGrid[o],
                                            d = l[0].swiperSlideOffset,
                                            h = (i - d - c / 2) / c * _.params.coverflow.modifier,
                                            u = a() ? n * h : 0,
                                            p = a() ? 0 : n * h,
                                            f = -s * Math.abs(h),
                                            m = a() ? 0 : _.params.coverflow.stretch * h,
                                            g = a() ? _.params.coverflow.stretch * h : 0;
                                        Math.abs(g) < .001 && (g = 0),
                                        Math.abs(m) < .001 && (m = 0),
                                        Math.abs(f) < .001 && (f = 0),
                                        Math.abs(u) < .001 && (u = 0),
                                        Math.abs(p) < .001 && (p = 0);
                                        var v = "translate3d(" + g + "px," + m + "px," + f + "px)  rotateX(" + p + "deg) rotateY(" + u + "deg)";
                                        if (l.transform(v), l[0].style.zIndex = -Math.abs(Math.round(h)) + 1, _.params.coverflow.slideShadows) {
                                            var y = a() ? l.find(".swiper-slide-shadow-left") : l.find(".swiper-slide-shadow-top"),
                                                b = a() ? l.find(".swiper-slide-shadow-right") : l.find(".swiper-slide-shadow-bottom");
                                            0 === y.length && (y = t('<div class="swiper-slide-shadow-' + (a() ? "left" : "top") + '"></div>'), l.append(y)),
                                            0 === b.length && (b = t('<div class="swiper-slide-shadow-' + (a() ? "right" : "bottom") + '"></div>'), l.append(b)),
                                            y.length && (y[0].style.opacity = h > 0 ? h : 0),
                                            b.length && (b[0].style.opacity = -h > 0 ? -h : 0)
                                        }
                                    }
                                    if (_.browser.ie) {
                                        var w = _.wrapper[0].style;
                                        w.perspectiveOrigin = i + "px 50%"
                                    }
                                },
                                setTransition: function (e) {
                                    _.slides.transition(e).find(".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left").transition(e)
                                }
                            }
                        }, _.lazy = {
                            initialImageLoaded: !1,
                            loadImageInSlide: function (e, i) {
                                if ("undefined" != typeof e && ("undefined" == typeof i && (i = !0), 0 !== _.slides.length)) {
                                    var n = _.slides.eq(e),
                                        s = n.find(".swiper-lazy:not(.swiper-lazy-loaded):not(.swiper-lazy-loading)");
                                    !n.hasClass("swiper-lazy") || n.hasClass("swiper-lazy-loaded") || n.hasClass("swiper-lazy-loading") || (s = s.add(n[0])),
                                    0 !== s.length && s.each(function () {
                                        var e = t(this);
                                        e.addClass("swiper-lazy-loading");
                                        var s = e.attr("data-background"),
                                            a = e.attr("data-src"),
                                            o = e.attr("data-srcset");
                                        _.loadImage(e[0], a || s, o, !1, function () {
                                            if (s ? (e.css("background-image", "url(" + s + ")"), e.removeAttr("data-background")) : (o && (e.attr("srcset", o), e.removeAttr("data-srcset")), a && (e.attr("src", a), e.removeAttr("data-src"))), e.addClass("swiper-lazy-loaded").removeClass("swiper-lazy-loading"), n.find(".swiper-lazy-preloader, .preloader").remove(), _.params.loop && i) {
                                                var t = n.attr("data-swiper-slide-index");
                                                if (n.hasClass(_.params.slideDuplicateClass)) {
                                                    var r = _.wrapper.children('[data-swiper-slide-index="' + t + '"]:not(.' + _.params.slideDuplicateClass + ")");
                                                    _.lazy.loadImageInSlide(r.index(), !1)
                                                } else {
                                                    var l = _.wrapper.children("." + _.params.slideDuplicateClass + '[data-swiper-slide-index="' + t + '"]');
                                                    _.lazy.loadImageInSlide(l.index(), !1)
                                                }
                                            }
                                            _.emit("onLazyImageReady", _, n[0], e[0])
                                        }),
                                            _.emit("onLazyImageLoad", _, n[0], e[0])
                                    })
                                }
                            },
                            load: function () {
                                var e;
                                if (_.params.watchSlidesVisibility) _.wrapper.children("." + _.params.slideVisibleClass).each(function () {
                                    _.lazy.loadImageInSlide(t(this).index())
                                });
                                else if (_.params.slidesPerView > 1) for (e = _.activeIndex; e < _.activeIndex + _.params.slidesPerView; e++) _.slides[e] && _.lazy.loadImageInSlide(e);
                                else _.lazy.loadImageInSlide(_.activeIndex);
                                if (_.params.lazyLoadingInPrevNext) if (_.params.slidesPerView > 1) {
                                    for (e = _.activeIndex + _.params.slidesPerView; e < _.activeIndex + _.params.slidesPerView + _.params.slidesPerView; e++) _.slides[e] && _.lazy.loadImageInSlide(e);
                                    for (e = _.activeIndex - _.params.slidesPerView; e < _.activeIndex; e++) _.slides[e] && _.lazy.loadImageInSlide(e)
                                } else {
                                    var i = _.wrapper.children("." + _.params.slideNextClass);
                                    i.length > 0 && _.lazy.loadImageInSlide(i.index());
                                    var n = _.wrapper.children("." + _.params.slidePrevClass);
                                    n.length > 0 && _.lazy.loadImageInSlide(n.index())
                                }
                            },
                            onTransitionStart: function () {
                                _.params.lazyLoading && (_.params.lazyLoadingOnTransitionStart || !_.params.lazyLoadingOnTransitionStart && !_.lazy.initialImageLoaded) && _.lazy.load()
                            },
                            onTransitionEnd: function () {
                                _.params.lazyLoading && !_.params.lazyLoadingOnTransitionStart && _.lazy.load()
                            }
                        }, _.scrollbar = {
                            isTouched: !1,
                            setDragPosition: function (e) {
                                var t = _.scrollbar,
                                    i = a() ? "touchstart" === e.type || "touchmove" === e.type ? e.targetTouches[0].pageX : e.pageX || e.clientX : "touchstart" === e.type || "touchmove" === e.type ? e.targetTouches[0].pageY : e.pageY || e.clientY,
                                    n = i - t.track.offset()[a() ? "left" : "top"] - t.dragSize / 2,
                                    s = -_.minTranslate() * t.moveDivider,
                                    o = -_.maxTranslate() * t.moveDivider;
                                s > n ? n = s : n > o && (n = o),
                                    n = -n / t.moveDivider,
                                    _.updateProgress(n),
                                    _.setWrapperTranslate(n, !0)
                            },
                            dragStart: function (e) {
                                var t = _.scrollbar;
                                t.isTouched = !0,
                                    e.preventDefault(),
                                    e.stopPropagation(),
                                    t.setDragPosition(e),
                                    clearTimeout(t.dragTimeout),
                                    t.track.transition(0),
                                _.params.scrollbarHide && t.track.css("opacity", 1),
                                    _.wrapper.transition(100),
                                    t.drag.transition(100),
                                    _.emit("onScrollbarDragStart", _)
                            },
                            dragMove: function (e) {
                                var t = _.scrollbar;
                                t.isTouched && (e.preventDefault ? e.preventDefault() : e.returnValue = !1, t.setDragPosition(e), _.wrapper.transition(0), t.track.transition(0), t.drag.transition(0), _.emit("onScrollbarDragMove", _))
                            },
                            dragEnd: function (e) {
                                var t = _.scrollbar;
                                t.isTouched && (t.isTouched = !1, _.params.scrollbarHide && (clearTimeout(t.dragTimeout), t.dragTimeout = setTimeout(function () {
                                    t.track.css("opacity", 0),
                                        t.track.transition(400)
                                }, 1e3)), _.emit("onScrollbarDragEnd", _), _.params.scrollbarSnapOnRelease && _.slideReset())
                            },
                            enableDraggable: function () {
                                var e = _.scrollbar,
                                    i = _.support.touch ? e.track : document;
                                t(e.track).on(_.touchEvents.start, e.dragStart),
                                    t(i).on(_.touchEvents.move, e.dragMove),
                                    t(i).on(_.touchEvents.end, e.dragEnd)
                            },
                            disableDraggable: function () {
                                var e = _.scrollbar,
                                    i = _.support.touch ? e.track : document;
                                t(e.track).off(_.touchEvents.start, e.dragStart),
                                    t(i).off(_.touchEvents.move, e.dragMove),
                                    t(i).off(_.touchEvents.end, e.dragEnd)
                            },
                            set: function () {
                                if (_.params.scrollbar) {
                                    var e = _.scrollbar;
                                    e.track = t(_.params.scrollbar),
                                        e.drag = e.track.find(".swiper-scrollbar-drag"),
                                    0 === e.drag.length && (e.drag = t('<div class="swiper-scrollbar-drag"></div>'), e.track.append(e.drag)),
                                        e.drag[0].style.width = "",
                                        e.drag[0].style.height = "",
                                        e.trackSize = a() ? e.track[0].offsetWidth : e.track[0].offsetHeight,
                                        e.divider = _.size / _.virtualSize,
                                        e.moveDivider = e.divider * (e.trackSize / _.size),
                                        e.dragSize = e.trackSize * e.divider,
                                        a() ? e.drag[0].style.width = e.dragSize + "px" : e.drag[0].style.height = e.dragSize + "px",
                                        e.divider >= 1 ? e.track[0].style.display = "none" : e.track[0].style.display = "",
                                    _.params.scrollbarHide && (e.track[0].style.opacity = 0)
                                }
                            },
                            setTranslate: function () {
                                if (_.params.scrollbar) {
                                    var e, t = _.scrollbar,
                                        i = (_.translate || 0, t.dragSize);
                                    e = (t.trackSize - t.dragSize) * _.progress,
                                        _.rtl && a() ? (e = -e, e > 0 ? (i = t.dragSize - e, e = 0) : -e + t.dragSize > t.trackSize && (i = t.trackSize + e)) : 0 > e ? (i = t.dragSize + e, e = 0) : e + t.dragSize > t.trackSize && (i = t.trackSize - e),
                                        a() ? (_.support.transforms3d ? t.drag.transform("translate3d(" + e + "px, 0, 0)") : t.drag.transform("translateX(" + e + "px)"), t.drag[0].style.width = i + "px") : (_.support.transforms3d ? t.drag.transform("translate3d(0px, " + e + "px, 0)") : t.drag.transform("translateY(" + e + "px)"), t.drag[0].style.height = i + "px"),
                                    _.params.scrollbarHide && (clearTimeout(t.timeout), t.track[0].style.opacity = 1, t.timeout = setTimeout(function () {
                                        t.track[0].style.opacity = 0,
                                            t.track.transition(400)
                                    }, 1e3))
                                }
                            },
                            setTransition: function (e) {
                                _.params.scrollbar && _.scrollbar.drag.transition(e)
                            }
                        }, _.controller = {
                            LinearSpline: function (e, t) {
                                this.x = e,
                                    this.y = t,
                                    this.lastIndex = e.length - 1;
                                var i, n;
                                this.x.length,
                                    this.interpolate = function (e) {
                                        return e ? (n = s(this.x, e), i = n - 1, (e - this.x[i]) * (this.y[n] - this.y[i]) / (this.x[n] - this.x[i]) + this.y[i]) : 0
                                    };
                                var s = function () {
                                    var e, t, i;
                                    return function (n, s) {
                                        for (t = -1, e = n.length; e - t > 1;) n[i = e + t >> 1] <= s ? t = i : e = i;
                                        return e
                                    }
                                }()
                            },
                            getInterpolateFunction: function (e) {
                                _.controller.spline || (_.controller.spline = _.params.loop ? new _.controller.LinearSpline(_.slidesGrid, e.slidesGrid) : new _.controller.LinearSpline(_.snapGrid, e.snapGrid))
                            },
                            setTranslate: function (e, t) {
                                function n(t) {
                                    e = t.rtl && "horizontal" === t.params.direction ? -_.translate : _.translate,
                                    "slide" === _.params.controlBy && (_.controller.getInterpolateFunction(t), a = -_.controller.spline.interpolate(-e)),
                                    a && "container" !== _.params.controlBy || (s = (t.maxTranslate() - t.minTranslate()) / (_.maxTranslate() - _.minTranslate()), a = (e - _.minTranslate()) * s + t.minTranslate()),
                                    _.params.controlInverse && (a = t.maxTranslate() - a),
                                        t.updateProgress(a),
                                        t.setWrapperTranslate(a, !1, _),
                                        t.updateActiveIndex()
                                }

                                var s, a, o = _.params.control;
                                if (_.isArray(o)) for (var r = 0; r < o.length; r++) o[r] !== t && o[r] instanceof i && n(o[r]);
                                else o instanceof i && t !== o && n(o)
                            },
                            setTransition: function (e, t) {
                                function n(t) {
                                    t.setWrapperTransition(e, _),
                                    0 !== e && (t.onTransitionStart(), t.wrapper.transitionEnd(function () {
                                        a && (t.params.loop && "slide" === _.params.controlBy && t.fixLoop(), t.onTransitionEnd())
                                    }))
                                }

                                var s, a = _.params.control;
                                if (_.isArray(a)) for (s = 0; s < a.length; s++) a[s] !== t && a[s] instanceof i && n(a[s]);
                                else a instanceof i && t !== a && n(a)
                            }
                        }, _.hashnav = {
                            init: function () {
                                if (_.params.hashnav) {
                                    _.hashnav.initialized = !0;
                                    var e = document.location.hash.replace("#", "");
                                    if (e) for (var t = 0, i = 0, n = _.slides.length; n > i; i++) {
                                        var s = _.slides.eq(i),
                                            a = s.attr("data-hash");
                                        if (a === e && !s.hasClass(_.params.slideDuplicateClass)) {
                                            var o = s.index();
                                            _.slideTo(o, t, _.params.runCallbacksOnInit, !0)
                                        }
                                    }
                                }
                            },
                            setHash: function () {
                                _.hashnav.initialized && _.params.hashnav && (document.location.hash = _.slides.eq(_.activeIndex).attr("data-hash") || "")
                            }
                        }, _.disableKeyboardControl = function () {
                            t(document).off("keydown", d)
                        }, _.enableKeyboardControl = function () {
                            t(document).on("keydown", d)
                        }, _.mousewheel = {
                            event: !1,
                            lastScrollTime: (new window.Date).getTime()
                        }, _.params.mousewheelControl) {
                        try {
                            new window.WheelEvent("wheel"),
                                _.mousewheel.event = "wheel"
                        } catch (H) {
                        }
                        _.mousewheel.event || void 0 === document.onmousewheel || (_.mousewheel.event = "mousewheel"),
                        _.mousewheel.event || (_.mousewheel.event = "DOMMouseScroll")
                    }
                    _.disableMousewheelControl = function () {
                        return !!_.mousewheel.event && (_.container.off(_.mousewheel.event, h), !0)
                    },
                        _.enableMousewheelControl = function () {
                            return !!_.mousewheel.event && (_.container.on(_.mousewheel.event, h), !0)
                        },
                        _.parallax = {
                            setTranslate: function () {
                                _.container.children("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]").each(function () {
                                    u(this, _.progress)
                                }),
                                    _.slides.each(function () {
                                        var e = t(this);
                                        e.find("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]").each(function () {
                                            var t = Math.min(Math.max(e[0].progress, -1), 1);
                                            u(this, t)
                                        })
                                    })
                            },
                            setTransition: function (e) {
                                "undefined" == typeof e && (e = _.params.speed),
                                    _.container.find("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]").each(function () {
                                        var i = t(this),
                                            n = parseInt(i.attr("data-swiper-parallax-duration"), 10) || e;
                                        0 === e && (n = 0),
                                            i.transition(n)
                                    })
                            }
                        },
                        _._plugins = [];
                    for (var L in _.plugins) {
                        var W = _.plugins[L](_, _.params[L]);
                        W && _._plugins.push(W)
                    }
                    return _.callPlugins = function (e) {
                        for (var t = 0; t < _._plugins.length; t++) e in _._plugins[t] && _._plugins[t][e](arguments[1], arguments[2], arguments[3], arguments[4], arguments[5])
                    },
                        _.emitterEventListeners = {},
                        _.emit = function (e) {
                            _.params[e] && _.params[e](arguments[1], arguments[2], arguments[3], arguments[4], arguments[5]);
                            var t;
                            if (_.emitterEventListeners[e]) for (t = 0; t < _.emitterEventListeners[e].length; t++) _.emitterEventListeners[e][t](arguments[1], arguments[2], arguments[3], arguments[4], arguments[5]);
                            _.callPlugins && _.callPlugins(e, arguments[1], arguments[2], arguments[3], arguments[4], arguments[5])
                        },
                        _.on = function (e, t) {
                            return e = p(e),
                            _.emitterEventListeners[e] || (_.emitterEventListeners[e] = []),
                                _.emitterEventListeners[e].push(t),
                                _
                        },
                        _.off = function (e, t) {
                            var i;
                            if (e = p(e), "undefined" == typeof t) return _.emitterEventListeners[e] = [],
                                _;
                            if (_.emitterEventListeners[e] && 0 !== _.emitterEventListeners[e].length) {
                                for (i = 0; i < _.emitterEventListeners[e].length; i++) _.emitterEventListeners[e][i] === t && _.emitterEventListeners[e].splice(i, 1);
                                return _
                            }
                        },
                        _.once = function (e, t) {
                            e = p(e);
                            var i = function () {
                                t(arguments[0], arguments[1], arguments[2], arguments[3], arguments[4]),
                                    _.off(e, i)
                            };
                            return _.on(e, i),
                                _
                        },
                        _.a11y = {
                            makeFocusable: function (e) {
                                return e.attr("tabIndex", "0"),
                                    e
                            },
                            addRole: function (e, t) {
                                return e.attr("role", t),
                                    e
                            },
                            addLabel: function (e, t) {
                                return e.attr("aria-label", t),
                                    e
                            },
                            disable: function (e) {
                                return e.attr("aria-disabled", !0),
                                    e
                            },
                            enable: function (e) {
                                return e.attr("aria-disabled", !1),
                                    e
                            },
                            onEnterKey: function (e) {
                                13 === e.keyCode && (t(e.target).is(_.params.nextButton) ? (_.onClickNext(e), _.isEnd ? _.a11y.notify(_.params.lastSlideMessage) : _.a11y.notify(_.params.nextSlideMessage)) : t(e.target).is(_.params.prevButton) && (_.onClickPrev(e), _.isBeginning ? _.a11y.notify(_.params.firstSlideMessage) : _.a11y.notify(_.params.prevSlideMessage)), t(e.target).is("." + _.params.bulletClass) && t(e.target)[0].click())
                            },
                            liveRegion: t('<span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>'),
                            notify: function (e) {
                                var t = _.a11y.liveRegion;
                                0 !== t.length && (t.html(""), t.html(e))
                            },
                            init: function () {
                                if (_.params.nextButton) {
                                    var e = t(_.params.nextButton);
                                    _.a11y.makeFocusable(e),
                                        _.a11y.addRole(e, "button"),
                                        _.a11y.addLabel(e, _.params.nextSlideMessage)
                                }
                                if (_.params.prevButton) {
                                    var i = t(_.params.prevButton);
                                    _.a11y.makeFocusable(i),
                                        _.a11y.addRole(i, "button"),
                                        _.a11y.addLabel(i, _.params.prevSlideMessage)
                                }
                                t(_.container).append(_.a11y.liveRegion)
                            },
                            initPagination: function () {
                                _.params.pagination && _.params.paginationClickable && _.bullets && _.bullets.length && _.bullets.each(function () {
                                    var e = t(this);
                                    _.a11y.makeFocusable(e),
                                        _.a11y.addRole(e, "button"),
                                        _.a11y.addLabel(e, _.params.paginationBulletMessage.replace(/{{index}}/, e.index() + 1))
                                })
                            },
                            destroy: function () {
                                _.a11y.liveRegion && _.a11y.liveRegion.length > 0 && _.a11y.liveRegion.remove()
                            }
                        },
                        _.init = function () {
                            _.params.loop && _.createLoop(),
                                _.updateContainerSize(),
                                _.updateSlidesSize(),
                                _.updatePagination(),
                            _.params.scrollbar && _.scrollbar && (_.scrollbar.set(), _.params.scrollbarDraggable && _.scrollbar.enableDraggable()),
                            "slide" !== _.params.effect && _.effects[_.params.effect] && (_.params.loop || _.updateProgress(), _.effects[_.params.effect].setTranslate()),
                                _.params.loop ? _.slideTo(_.params.initialSlide + _.loopedSlides, 0, _.params.runCallbacksOnInit) : (_.slideTo(_.params.initialSlide, 0, _.params.runCallbacksOnInit), 0 === _.params.initialSlide && (_.parallax && _.params.parallax && _.parallax.setTranslate(), _.lazy && _.params.lazyLoading && (_.lazy.load(), _.lazy.initialImageLoaded = !0))),
                                _.attachEvents(),
                            _.params.observer && _.support.observer && _.initObservers(),
                            _.params.preloadImages && !_.params.lazyLoading && _.preloadImages(),
                            _.params.autoplay && _.startAutoplay(),
                            _.params.keyboardControl && _.enableKeyboardControl && _.enableKeyboardControl(),
                            _.params.mousewheelControl && _.enableMousewheelControl && _.enableMousewheelControl(),
                            _.params.hashnav && _.hashnav && _.hashnav.init(),
                            _.params.a11y && _.a11y && _.a11y.init(),
                                _.emit("onInit", _)
                        },
                        _.cleanupStyles = function () {
                            _.container.removeClass(_.classNames.join(" ")).removeAttr("style"),
                                _.wrapper.removeAttr("style"),
                            _.slides && _.slides.length && _.slides.removeClass([_.params.slideVisibleClass, _.params.slideActiveClass, _.params.slideNextClass, _.params.slidePrevClass].join(" ")).removeAttr("style").removeAttr("data-swiper-column").removeAttr("data-swiper-row"),
                            _.paginationContainer && _.paginationContainer.length && _.paginationContainer.removeClass(_.params.paginationHiddenClass),
                            _.bullets && _.bullets.length && _.bullets.removeClass(_.params.bulletActiveClass),
                            _.params.prevButton && t(_.params.prevButton).removeClass(_.params.buttonDisabledClass),
                            _.params.nextButton && t(_.params.nextButton).removeClass(_.params.buttonDisabledClass),
                            _.params.scrollbar && _.scrollbar && (_.scrollbar.track && _.scrollbar.track.length && _.scrollbar.track.removeAttr("style"), _.scrollbar.drag && _.scrollbar.drag.length && _.scrollbar.drag.removeAttr("style"))
                        },
                        _.destroy = function (e, t) {
                            _.detachEvents(),
                                _.stopAutoplay(),
                            _.params.scrollbar && _.scrollbar && _.params.scrollbarDraggable && _.scrollbar.disableDraggable(),
                            _.params.loop && _.destroyLoop(),
                            t && _.cleanupStyles(),
                                _.disconnectObservers(),
                            _.params.keyboardControl && _.disableKeyboardControl && _.disableKeyboardControl(),
                            _.params.mousewheelControl && _.disableMousewheelControl && _.disableMousewheelControl(),
                            _.params.a11y && _.a11y && _.a11y.destroy(),
                                _.emit("onDestroy"),
                            e !== !1 && (_ = null)
                        },
                        _.init(),
                        _
                }
            };
            i.prototype = {
                isSafari: function () {
                    var e = navigator.userAgent.toLowerCase();
                    return e.indexOf("safari") >= 0 && e.indexOf("chrome") < 0 && e.indexOf("android") < 0
                }(),
                isUiWebView: /(iPhone|iPod|iPad).*AppleWebKit(?!.*Safari)/i.test(navigator.userAgent),
                isArray: function (e) {
                    return "[object Array]" === Object.prototype.toString.apply(e)
                },
                browser: {
                    ie: window.navigator.pointerEnabled || window.navigator.msPointerEnabled,
                    ieTouch: window.navigator.msPointerEnabled && window.navigator.msMaxTouchPoints > 1 || window.navigator.pointerEnabled && window.navigator.maxTouchPoints > 1
                },
                device: function () {
                    var e = navigator.userAgent,
                        t = e.match(/(Android);?[\s\/]+([\d.]+)?/),
                        i = e.match(/(iPad).*OS\s([\d_]+)/),
                        n = e.match(/(iPod)(.*OS\s([\d_]+))?/),
                        s = !i && e.match(/(iPhone\sOS)\s([\d_]+)/);
                    return {
                        ios: i || s || n,
                        android: t
                    }
                }(),
                support: {
                    touch: window.Modernizr && Modernizr.touch === !0 ||
                    function () {
                        return !!("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch)
                    }(),
                    transforms3d: window.Modernizr && Modernizr.csstransforms3d === !0 ||
                    function () {
                        var e = document.createElement("div").style;
                        return "webkitPerspective" in e || "MozPerspective" in e || "OPerspective" in e || "MsPerspective" in e || "perspective" in e
                    }(),
                    flexbox: function () {
                        for (var e = document.createElement("div").style, t = "alignItems webkitAlignItems webkitBoxAlign msFlexAlign mozBoxAlign webkitFlexDirection msFlexDirection mozBoxDirection mozBoxOrient webkitBoxDirection webkitBoxOrient".split(" "), i = 0; i < t.length; i++) if (t[i] in e) return !0
                    }(),
                    observer: function () {
                        return "MutationObserver" in window || "WebkitMutationObserver" in window
                    }()
                },
                plugins: {}
            };
            for (var n = (function () {
                var e = function (e) {
                        var t = this,
                            i = 0;
                        for (i = 0; i < e.length; i++) t[i] = e[i];
                        return t.length = e.length,
                            this
                    },
                    t = function (t, i) {
                        var n = [],
                            s = 0;
                        if (t && !i && t instanceof e) return t;
                        if (t) if ("string" == typeof t) {
                            var a, o, r = t.trim();
                            if (r.indexOf("<") >= 0 && r.indexOf(">") >= 0) {
                                var l = "div";
                                for (0 === r.indexOf("<li") && (l = "ul"), 0 === r.indexOf("<tr") && (l = "tbody"), (0 === r.indexOf("<td") || 0 === r.indexOf("<th")) && (l = "tr"), 0 === r.indexOf("<tbody") && (l = "table"), 0 === r.indexOf("<option") && (l = "select"), o = document.createElement(l), o.innerHTML = t, s = 0; s < o.childNodes.length; s++) n.push(o.childNodes[s])
                            } else for (a = i || "#" !== t[0] || t.match(/[ .<>:~]/) ? (i || document).querySelectorAll(t) : [document.getElementById(t.split("#")[1])], s = 0; s < a.length; s++) a[s] && n.push(a[s])
                        } else if (t.nodeType || t === window || t === document) n.push(t);
                        else if (t.length > 0 && t[0].nodeType) for (s = 0; s < t.length; s++) n.push(t[s]);
                        return new e(n)
                    };
                return e.prototype = {
                    addClass: function (e) {
                        if ("undefined" == typeof e) return this;
                        for (var t = e.split(" "), i = 0; i < t.length; i++) for (var n = 0; n < this.length; n++) this[n].classList.add(t[i]);
                        return this
                    },
                    removeClass: function (e) {
                        for (var t = e.split(" "), i = 0; i < t.length; i++) for (var n = 0; n < this.length; n++) this[n].classList.remove(t[i]);
                        return this
                    },
                    hasClass: function (e) {
                        return !!this[0] && this[0].classList.contains(e)
                    },
                    toggleClass: function (e) {
                        for (var t = e.split(" "), i = 0; i < t.length; i++) for (var n = 0; n < this.length; n++) this[n].classList.toggle(t[i]);
                        return this
                    },
                    attr: function (e, t) {
                        if (1 === arguments.length && "string" == typeof e) return this[0] ? this[0].getAttribute(e) : void 0;
                        for (var i = 0; i < this.length; i++) if (2 === arguments.length) this[i].setAttribute(e, t);
                        else for (var n in e) this[i][n] = e[n],
                                this[i].setAttribute(n, e[n]);
                        return this
                    },
                    removeAttr: function (e) {
                        for (var t = 0; t < this.length; t++) this[t].removeAttribute(e);
                        return this
                    },
                    data: function (e, t) {
                        if ("undefined" != typeof t) {
                            for (var i = 0; i < this.length; i++) {
                                var n = this[i];
                                n.dom7ElementDataStorage || (n.dom7ElementDataStorage = {}),
                                    n.dom7ElementDataStorage[e] = t
                            }
                            return this
                        }
                        if (this[0]) {
                            var s = this[0].getAttribute("data-" + e);
                            return s ? s : this[0].dom7ElementDataStorage && e in this[0].dom7ElementDataStorage ? this[0].dom7ElementDataStorage[e] : void 0
                        }
                    },
                    transform: function (e) {
                        for (var t = 0; t < this.length; t++) {
                            var i = this[t].style;
                            i.webkitTransform = i.MsTransform = i.msTransform = i.MozTransform = i.OTransform = i.transform = e
                        }
                        return this
                    },
                    transition: function (e) {
                        "string" != typeof e && (e += "ms");
                        for (var t = 0; t < this.length; t++) {
                            var i = this[t].style;
                            i.webkitTransitionDuration = i.MsTransitionDuration = i.msTransitionDuration = i.MozTransitionDuration = i.OTransitionDuration = i.transitionDuration = e
                        }
                        return this
                    },
                    on: function (e, i, n, s) {
                        function a(e) {
                            var s = e.target;
                            if (t(s).is(i)) n.call(s, e);
                            else for (var a = t(s).parents(), o = 0; o < a.length; o++) t(a[o]).is(i) && n.call(a[o], e)
                        }

                        var o, r, l = e.split(" ");
                        for (o = 0; o < this.length; o++) if ("function" == typeof i || i === !1) for ("function" == typeof i && (n = arguments[1], s = arguments[2] || !1), r = 0; r < l.length; r++) this[o].addEventListener(l[r], n, s);
                        else for (r = 0; r < l.length; r++) this[o].dom7LiveListeners || (this[o].dom7LiveListeners = []),
                                this[o].dom7LiveListeners.push({
                                    listener: n,
                                    liveListener: a
                                }),
                                this[o].addEventListener(l[r], a, s);
                        return this
                    },
                    off: function (e, t, i, n) {
                        for (var s = e.split(" "), a = 0; a < s.length; a++) for (var o = 0; o < this.length; o++) if ("function" == typeof t || t === !1)"function" == typeof t && (i = arguments[1], n = arguments[2] || !1),
                            this[o].removeEventListener(s[a], i, n);
                        else if (this[o].dom7LiveListeners) for (var r = 0; r < this[o].dom7LiveListeners.length; r++) this[o].dom7LiveListeners[r].listener === i && this[o].removeEventListener(s[a], this[o].dom7LiveListeners[r].liveListener, n);
                        return this
                    },
                    once: function (e, t, i, n) {
                        function s(o) {
                            i(o),
                                a.off(e, t, s, n)
                        }

                        var a = this;
                        "function" == typeof t && (t = !1, i = arguments[1], n = arguments[2]),
                            a.on(e, t, s, n)
                    },
                    trigger: function (e, t) {
                        for (var i = 0; i < this.length; i++) {
                            var n;
                            try {
                                n = new window.CustomEvent(e, {
                                    detail: t,
                                    bubbles: !0,
                                    cancelable: !0
                                })
                            } catch (s) {
                                n = document.createEvent("Event"),
                                    n.initEvent(e, !0, !0),
                                    n.detail = t
                            }
                            this[i].dispatchEvent(n)
                        }
                        return this
                    },
                    transitionEnd: function (e) {
                        function t(a) {
                            if (a.target === this) for (e.call(this, a), i = 0; i < n.length; i++) s.off(n[i], t)
                        }

                        var i, n = ["webkitTransitionEnd", "transitionend", "oTransitionEnd", "MSTransitionEnd", "msTransitionEnd"],
                            s = this;
                        if (e) for (i = 0; i < n.length; i++) s.on(n[i], t);
                        return this
                    },
                    width: function () {
                        return this[0] === window ? window.innerWidth : this.length > 0 ? parseFloat(this.css("width")) : null
                    },
                    outerWidth: function (e) {
                        return this.length > 0 ? e ? this[0].offsetWidth + parseFloat(this.css("margin-right")) + parseFloat(this.css("margin-left")) : this[0].offsetWidth : null
                    },
                    height: function () {
                        return this[0] === window ? window.innerHeight : this.length > 0 ? parseFloat(this.css("height")) : null
                    },
                    outerHeight: function (e) {
                        return this.length > 0 ? e ? this[0].offsetHeight + parseFloat(this.css("margin-top")) + parseFloat(this.css("margin-bottom")) : this[0].offsetHeight : null
                    },
                    offset: function () {
                        if (this.length > 0) {
                            var e = this[0],
                                t = e.getBoundingClientRect(),
                                i = document.body,
                                n = e.clientTop || i.clientTop || 0,
                                s = e.clientLeft || i.clientLeft || 0,
                                a = window.pageYOffset || e.scrollTop,
                                o = window.pageXOffset || e.scrollLeft;
                            return {
                                top: t.top + a - n,
                                left: t.left + o - s
                            }
                        }
                        return null
                    },
                    css: function (e, t) {
                        var i;
                        if (1 === arguments.length) {
                            if ("string" != typeof e) {
                                for (i = 0; i < this.length; i++) for (var n in e) this[i].style[n] = e[n];
                                return this
                            }
                            if (this[0]) return window.getComputedStyle(this[0], null).getPropertyValue(e)
                        }
                        if (2 === arguments.length && "string" == typeof e) {
                            for (i = 0; i < this.length; i++) this[i].style[e] = t;
                            return this
                        }
                        return this
                    },
                    each: function (e) {
                        for (var t = 0; t < this.length; t++) e.call(this[t], t, this[t]);
                        return this
                    },
                    html: function (e) {
                        if ("undefined" == typeof e) return this[0] ? this[0].innerHTML : void 0;
                        for (var t = 0; t < this.length; t++) this[t].innerHTML = e;
                        return this
                    },
                    is: function (i) {
                        if (!this[0]) return !1;
                        var n, s;
                        if ("string" == typeof i) {
                            var a = this[0];
                            if (a === document) return i === document;
                            if (a === window) return i === window;
                            if (a.matches) return a.matches(i);
                            if (a.webkitMatchesSelector) return a.webkitMatchesSelector(i);
                            if (a.mozMatchesSelector) return a.mozMatchesSelector(i);
                            if (a.msMatchesSelector) return a.msMatchesSelector(i);
                            for (n = t(i), s = 0; s < n.length; s++) if (n[s] === this[0]) return !0;
                            return !1
                        }
                        if (i === document) return this[0] === document;
                        if (i === window) return this[0] === window;
                        if (i.nodeType || i instanceof e) {
                            for (n = i.nodeType ? [i] : i, s = 0; s < n.length; s++) if (n[s] === this[0]) return !0;
                            return !1
                        }
                        return !1
                    },
                    index: function () {
                        if (this[0]) {
                            for (var e = this[0], t = 0; null !== (e = e.previousSibling);) 1 === e.nodeType && t++;
                            return t
                        }
                    },
                    eq: function (t) {
                        if ("undefined" == typeof t) return this;
                        var i, n = this.length;
                        return t > n - 1 ? new e([]) : 0 > t ? (i = n + t, new e(0 > i ? [] : [this[i]])) : new e([this[t]])
                    },
                    append: function (t) {
                        var i, n;
                        for (i = 0; i < this.length; i++) if ("string" == typeof t) {
                            var s = document.createElement("div");
                            for (s.innerHTML = t; s.firstChild;) this[i].appendChild(s.firstChild)
                        } else if (t instanceof e) for (n = 0; n < t.length; n++) this[i].appendChild(t[n]);
                        else this[i].appendChild(t);
                        return this
                    },
                    prepend: function (t) {
                        var i, n;
                        for (i = 0; i < this.length; i++) if ("string" == typeof t) {
                            var s = document.createElement("div");
                            for (s.innerHTML = t, n = s.childNodes.length - 1; n >= 0; n--) this[i].insertBefore(s.childNodes[n], this[i].childNodes[0])
                        } else if (t instanceof e) for (n = 0; n < t.length; n++) this[i].insertBefore(t[n], this[i].childNodes[0]);
                        else this[i].insertBefore(t, this[i].childNodes[0]);
                        return this
                    },
                    insertBefore: function (e) {
                        for (var i = t(e), n = 0; n < this.length; n++) if (1 === i.length) i[0].parentNode.insertBefore(this[n], i[0]);
                        else if (i.length > 1) for (var s = 0; s < i.length; s++) i[s].parentNode.insertBefore(this[n].cloneNode(!0), i[s])
                    },
                    insertAfter: function (e) {
                        for (var i = t(e), n = 0; n < this.length; n++) if (1 === i.length) i[0].parentNode.insertBefore(this[n], i[0].nextSibling);
                        else if (i.length > 1) for (var s = 0; s < i.length; s++) i[s].parentNode.insertBefore(this[n].cloneNode(!0), i[s].nextSibling)
                    },
                    next: function (i) {
                        return new e(this.length > 0 ? i ? this[0].nextElementSibling && t(this[0].nextElementSibling).is(i) ? [this[0].nextElementSibling] : [] : this[0].nextElementSibling ? [this[0].nextElementSibling] : [] : [])
                    },
                    nextAll: function (i) {
                        var n = [],
                            s = this[0];
                        if (!s) return new e([]);
                        for (; s.nextElementSibling;) {
                            var a = s.nextElementSibling;
                            i ? t(a).is(i) && n.push(a) : n.push(a),
                                s = a
                        }
                        return new e(n)
                    },
                    prev: function (i) {
                        return new e(this.length > 0 ? i ? this[0].previousElementSibling && t(this[0].previousElementSibling).is(i) ? [this[0].previousElementSibling] : [] : this[0].previousElementSibling ? [this[0].previousElementSibling] : [] : [])
                    },
                    prevAll: function (i) {
                        var n = [],
                            s = this[0];
                        if (!s) return new e([]);
                        for (; s.previousElementSibling;) {
                            var a = s.previousElementSibling;
                            i ? t(a).is(i) && n.push(a) : n.push(a),
                                s = a
                        }
                        return new e(n)
                    },
                    parent: function (e) {
                        for (var i = [], n = 0; n < this.length; n++) e ? t(this[n].parentNode).is(e) && i.push(this[n].parentNode) : i.push(this[n].parentNode);
                        return t(t.unique(i))
                    },
                    parents: function (e) {
                        for (var i = [], n = 0; n < this.length; n++) for (var s = this[n].parentNode; s;) e ? t(s).is(e) && i.push(s) : i.push(s),
                            s = s.parentNode;
                        return t(t.unique(i))
                    },
                    find: function (t) {
                        for (var i = [], n = 0; n < this.length; n++) for (var s = this[n].querySelectorAll(t), a = 0; a < s.length; a++) i.push(s[a]);
                        return new e(i)
                    },
                    children: function (i) {
                        for (var n = [], s = 0; s < this.length; s++) for (var a = this[s].childNodes, o = 0; o < a.length; o++) i ? 1 === a[o].nodeType && t(a[o]).is(i) && n.push(a[o]) : 1 === a[o].nodeType && n.push(a[o]);
                        return new e(t.unique(n))
                    },
                    remove: function () {
                        for (var e = 0; e < this.length; e++) this[e].parentNode && this[e].parentNode.removeChild(this[e]);
                        return this
                    },
                    add: function () {
                        var e, i, n = this;
                        for (e = 0; e < arguments.length; e++) {
                            var s = t(arguments[e]);
                            for (i = 0; i < s.length; i++) n[n.length] = s[i],
                                n.length++
                        }
                        return n
                    }
                },
                    t.fn = e.prototype,
                    t.unique = function (e) {
                        for (var t = [], i = 0; i < e.length; i++) -1 === t.indexOf(e[i]) && t.push(e[i]);
                        return t
                    },
                    t
            }()), s = ["jQuery", "Zepto", "Dom7"], a = 0; a < s.length; a++) window[s[a]] && e(window[s[a]]);
            var o;
            o = "undefined" == typeof n ? window.Dom7 || window.Zepto || window.jQuery : n,
            o && ("transitionEnd" in o.fn || (o.fn.transitionEnd = function (e) {
                function t(a) {
                    if (a.target === this) for (e.call(this, a), i = 0; i < n.length; i++) s.off(n[i], t)
                }

                var i, n = ["webkitTransitionEnd", "transitionend", "oTransitionEnd", "MSTransitionEnd", "msTransitionEnd"],
                    s = this;
                if (e) for (i = 0; i < n.length; i++) s.on(n[i], t);
                return this
            }), "transform" in o.fn || (o.fn.transform = function (e) {
                for (var t = 0; t < this.length; t++) {
                    var i = this[t].style;
                    i.webkitTransform = i.MsTransform = i.msTransform = i.MozTransform = i.OTransform = i.transform = e
                }
                return this
            }), "transition" in o.fn || (o.fn.transition = function (e) {
                "string" != typeof e && (e += "ms");
                for (var t = 0; t < this.length; t++) {
                    var i = this[t].style;
                    i.webkitTransitionDuration = i.MsTransitionDuration = i.msTransitionDuration = i.MozTransitionDuration = i.OTransitionDuration = i.transitionDuration = e
                }
                return this
            })),
                window.Swiper = i
        }(),
    "undefined" != typeof module ? module.exports = window.Swiper : "function" == typeof define && define.amd && define([], function () {
        "use strict";
        return window.Swiper
    }),


    function (e, t) {
        function i(t, i) {
            var s, a, o, r = t.nodeName.toLowerCase();
            return "area" === r ? (s = t.parentNode, a = s.name, !(!t.href || !a || "map" !== s.nodeName.toLowerCase()) && (o = e("img[usemap=#" + a + "]")[0], !!o && n(o))) : (/input|select|textarea|button|object/.test(r) ? !t.disabled : "a" === r ? t.href || i : i) && n(t)
        }

        function n(t) {
            return e.expr.filters.visible(t) && !e(t).parents().addBack().filter(function () {
                    return "hidden" === e.css(this, "visibility")
                }).length
        }

        var s = 0,
            a = /^ui-id-\d+$/;
        e.ui = e.ui || {},
        e.ui.version || (e.extend(e.ui, {
            version: "1.10.1",
            keyCode: {
                BACKSPACE: 8,
                COMMA: 188,
                DELETE: 46,
                DOWN: 40,
                END: 35,
                ENTER: 13,
                ESCAPE: 27,
                HOME: 36,
                LEFT: 37,
                NUMPAD_ADD: 107,
                NUMPAD_DECIMAL: 110,
                NUMPAD_DIVIDE: 111,
                NUMPAD_ENTER: 108,
                NUMPAD_MULTIPLY: 106,
                NUMPAD_SUBTRACT: 109,
                PAGE_DOWN: 34,
                PAGE_UP: 33,
                PERIOD: 190,
                RIGHT: 39,
                SPACE: 32,
                TAB: 9,
                UP: 38
            }
        }), e.fn.extend({
            _focus: e.fn.focus,
            focus: function (t, i) {
                return "number" == typeof t ? this.each(function () {
                    var n = this;
                    setTimeout(function () {
                        e(n).focus(),
                        i && i.call(n)
                    }, t)
                }) : this._focus.apply(this, arguments)
            },
            scrollParent: function () {
                var t;
                return t = e.ui.ie && /(static|relative)/.test(this.css("position")) || /absolute/.test(this.css("position")) ? this.parents().filter(function () {
                    return /(relative|absolute|fixed)/.test(e.css(this, "position")) && /(auto|scroll)/.test(e.css(this, "overflow") + e.css(this, "overflow-y") + e.css(this, "overflow-x"))
                }).eq(0) : this.parents().filter(function () {
                    return /(auto|scroll)/.test(e.css(this, "overflow") + e.css(this, "overflow-y") + e.css(this, "overflow-x"))
                }).eq(0),
                    /fixed/.test(this.css("position")) || !t.length ? e(document) : t
            },
            zIndex: function (i) {
                if (i !== t) return this.css("zIndex", i);
                if (this.length) for (var n, s, a = e(this[0]); a.length && a[0] !== document;) {
                    if (n = a.css("position"), ("absolute" === n || "relative" === n || "fixed" === n) && (s = parseInt(a.css("zIndex"), 10), !isNaN(s) && 0 !== s)) return s;
                    a = a.parent()
                }
                return 0
            },
            uniqueId: function () {
                return this.each(function () {
                    this.id || (this.id = "ui-id-" + ++s)
                })
            },
            removeUniqueId: function () {
                return this.each(function () {
                    a.test(this.id) && e(this).removeAttr("id")
                })
            }
        }), e.extend(e.expr[":"], {
            data: e.expr.createPseudo ? e.expr.createPseudo(function (t) {
                return function (i) {
                    return !!e.data(i, t)
                }
            }) : function (t, i, n) {
                return !!e.data(t, n[3])
            },
            focusable: function (t) {
                return i(t, !isNaN(e.attr(t, "tabindex")))
            },
            tabbable: function (t) {
                var n = e.attr(t, "tabindex"),
                    s = isNaN(n);
                return (s || n >= 0) && i(t, !s)
            }
        }), e("<a>").outerWidth(1).jquery || e.each(["Width", "Height"], function (i, n) {
            function s(t, i, n, s) {
                return e.each(a, function () {
                    i -= parseFloat(e.css(t, "padding" + this)) || 0,
                    n && (i -= parseFloat(e.css(t, "border" + this + "Width")) || 0),
                    s && (i -= parseFloat(e.css(t, "margin" + this)) || 0)
                }),
                    i
            }

            var a = "Width" === n ? ["Left", "Right"] : ["Top", "Bottom"],
                o = n.toLowerCase(),
                r = {
                    innerWidth: e.fn.innerWidth,
                    innerHeight: e.fn.innerHeight,
                    outerWidth: e.fn.outerWidth,
                    outerHeight: e.fn.outerHeight
                };
            e.fn["inner" + n] = function (i) {
                return i === t ? r["inner" + n].call(this) : this.each(function () {
                    e(this).css(o, s(this, i) + "px")
                })
            },
                e.fn["outer" + n] = function (t, i) {
                    return "number" != typeof t ? r["outer" + n].call(this, t) : this.each(function () {
                        e(this).css(o, s(this, t, !0, i) + "px")
                    })
                }
        }), e.fn.addBack || (e.fn.addBack = function (e) {
            return this.add(null == e ? this.prevObject : this.prevObject.filter(e))
        }), e("<a>").data("a-b", "a").removeData("a-b").data("a-b") && (e.fn.removeData = function (t) {
            return function (i) {
                return arguments.length ? t.call(this, e.camelCase(i)) : t.call(this)
            }
        }(e.fn.removeData)), e.ui.ie = !!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase()), e.support.selectstart = "onselectstart" in document.createElement("div"), e.fn.extend({
            disableSelection: function () {
                return this.bind((e.support.selectstart ? "selectstart" : "mousedown") + ".ui-disableSelection", function (e) {
                    e.preventDefault()
                })
            },
            enableSelection: function () {
                return this.unbind(".ui-disableSelection")
            }
        }), e.extend(e.ui, {
            plugin: {
                add: function (t, i, n) {
                    var s, a = e.ui[t].prototype;
                    for (s in n) a.plugins[s] = a.plugins[s] || [],
                        a.plugins[s].push([i, n[s]])
                },
                call: function (e, t, i) {
                    var n, s = e.plugins[t];
                    if (s && e.element[0].parentNode && 11 !== e.element[0].parentNode.nodeType) for (n = 0; n < s.length; n++) e.options[s[n][0]] && s[n][1].apply(e.element, i)
                }
            },
            hasScroll: function (t, i) {
                if ("hidden" === e(t).css("overflow")) return !1;
                var n = i && "left" === i ? "scrollLeft" : "scrollTop",
                    s = !1;
                return t[n] > 0 || (t[n] = 1, s = t[n] > 0, t[n] = 0, s)
            }
        }))
    }(jQuery),


    function (e, t) {
        var i = 0,
            n = Array.prototype.slice,
            s = e.cleanData;
        e.cleanData = function (t) {
            for (var i, n = 0; null != (i = t[n]); n++) try {
                e(i).triggerHandler("remove")
            } catch (a) {
            }
            s(t)
        },
            e.widget = function (t, i, n) {
                var s, a, o, r, l = {},
                    c = t.split(".")[0];
                t = t.split(".")[1],
                    s = c + "-" + t,
                n || (n = i, i = e.Widget),
                    e.expr[":"][s.toLowerCase()] = function (t) {
                        return !!e.data(t, s)
                    },
                    e[c] = e[c] || {},
                    a = e[c][t],
                    o = e[c][t] = function (e, t) {
                        return this._createWidget ? void(arguments.length && this._createWidget(e, t)) : new o(e, t)
                    },
                    e.extend(o, a, {
                        version: n.version,
                        _proto: e.extend({}, n),
                        _childConstructors: []
                    }),
                    r = new i,
                    r.options = e.widget.extend({}, r.options),
                    e.each(n, function (t, n) {
                        return e.isFunction(n) ? void(l[t] = function () {
                            var e = function () {
                                    return i.prototype[t].apply(this, arguments)
                                },
                                s = function (e) {
                                    return i.prototype[t].apply(this, e)
                                };
                            return function () {
                                var t, i = this._super,
                                    a = this._superApply;
                                return this._super = e,
                                    this._superApply = s,
                                    t = n.apply(this, arguments),
                                    this._super = i,
                                    this._superApply = a,
                                    t
                            }
                        }()) : void(l[t] = n)
                    }),
                    o.prototype = e.widget.extend(r, {
                        widgetEventPrefix: a ? r.widgetEventPrefix : t
                    }, l, {
                        constructor: o,
                        namespace: c,
                        widgetName: t,
                        widgetFullName: s
                    }),
                    a ? (e.each(a._childConstructors, function (t, i) {
                        var n = i.prototype;
                        e.widget(n.namespace + "." + n.widgetName, o, i._proto)
                    }), delete a._childConstructors) : i._childConstructors.push(o),
                    e.widget.bridge(t, o)
            },
            e.widget.extend = function (i) {
                for (var s, a, o = n.call(arguments, 1), r = 0, l = o.length; r < l; r++) for (s in o[r]) a = o[r][s],
                o[r].hasOwnProperty(s) && a !== t && (e.isPlainObject(a) ? i[s] = e.isPlainObject(i[s]) ? e.widget.extend({}, i[s], a) : e.widget.extend({}, a) : i[s] = a);
                return i
            },
            e.widget.bridge = function (i, s) {
                var a = s.prototype.widgetFullName || i;
                e.fn[i] = function (o) {
                    var r = "string" == typeof o,
                        l = n.call(arguments, 1),
                        c = this;
                    return o = !r && l.length ? e.widget.extend.apply(null, [o].concat(l)) : o,
                        r ? this.each(function () {
                            var n, s = e.data(this, a);
                            return s ? e.isFunction(s[o]) && "_" !== o.charAt(0) ? (n = s[o].apply(s, l), n !== s && n !== t ? (c = n && n.jquery ? c.pushStack(n.get()) : n, !1) : void 0) : e.error("no such method '" + o + "' for " + i + " widget instance") : e.error("cannot call methods on " + i + " prior to initialization; attempted to call method '" + o + "'")
                        }) : this.each(function () {
                            var t = e.data(this, a);
                            t ? t.option(o || {})._init() : e.data(this, a, new s(o, this))
                        }),
                        c
                }
            },
            e.Widget = function () {
            },
            e.Widget._childConstructors = [],
            e.Widget.prototype = {
                widgetName: "widget",
                widgetEventPrefix: "",
                defaultElement: "<div>",
                options: {
                    disabled: !1,
                    create: null
                },
                _createWidget: function (t, n) {
                    n = e(n || this.defaultElement || this)[0],
                        this.element = e(n),
                        this.uuid = i++,
                        this.eventNamespace = "." + this.widgetName + this.uuid,
                        this.options = e.widget.extend({}, this.options, this._getCreateOptions(), t),
                        this.bindings = e(),
                        this.hoverable = e(),
                        this.focusable = e(),
                    n !== this && (e.data(n, this.widgetFullName, this), this._on(!0, this.element, {
                        remove: function (e) {
                            e.target === n && this.destroy()
                        }
                    }), this.document = e(n.style ? n.ownerDocument : n.document || n), this.window = e(this.document[0].defaultView || this.document[0].parentWindow)),
                        this._create(),
                        this._trigger("create", null, this._getCreateEventData()),
                        this._init()
                },
                _getCreateOptions: e.noop,
                _getCreateEventData: e.noop,
                _create: e.noop,
                _init: e.noop,
                destroy: function () {
                    this._destroy(),
                        this.element.unbind(this.eventNamespace).removeData(this.widgetName).removeData(this.widgetFullName).removeData(e.camelCase(this.widgetFullName)),
                        this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName + "-disabled ui-state-disabled"),
                        this.bindings.unbind(this.eventNamespace),
                        this.hoverable.removeClass("ui-state-hover"),
                        this.focusable.removeClass("ui-state-focus")
                },
                _destroy: e.noop,
                widget: function () {
                    return this.element
                },
                option: function (i, n) {
                    var s, a, o, r = i;
                    if (0 === arguments.length) return e.widget.extend({}, this.options);
                    if ("string" == typeof i) if (r = {}, s = i.split("."), i = s.shift(), s.length) {
                        for (a = r[i] = e.widget.extend({}, this.options[i]), o = 0; o < s.length - 1; o++) a[s[o]] = a[s[o]] || {},
                            a = a[s[o]];
                        if (i = s.pop(), n === t) return a[i] === t ? null : a[i];
                        a[i] = n
                    } else {
                        if (n === t) return this.options[i] === t ? null : this.options[i];
                        r[i] = n
                    }
                    return this._setOptions(r),
                        this
                },
                _setOptions: function (e) {
                    var t;
                    for (t in e) this._setOption(t, e[t]);
                    return this
                },
                _setOption: function (e, t) {
                    return this.options[e] = t,
                    "disabled" === e && (this.widget().toggleClass(this.widgetFullName + "-disabled ui-state-disabled", !!t).attr("aria-disabled", t), this.hoverable.removeClass("ui-state-hover"), this.focusable.removeClass("ui-state-focus")),
                        this
                },
                enable: function () {
                    return this._setOption("disabled", !1)
                },
                disable: function () {
                    return this._setOption("disabled", !0)
                },
                _on: function (t, i, n) {
                    var s, a = this;
                    "boolean" != typeof t && (n = i, i = t, t = !1),
                        n ? (i = s = e(i), this.bindings = this.bindings.add(i)) : (n = i, i = this.element, s = this.widget()),
                        e.each(n, function (n, o) {
                            function r() {
                                if (t || a.options.disabled !== !0 && !e(this).hasClass("ui-state-disabled")) return ("string" == typeof o ? a[o] : o).apply(a, arguments)
                            }

                            "string" != typeof o && (r.guid = o.guid = o.guid || r.guid || e.guid++);
                            var l = n.match(/^(\w+)\s*(.*)$/),
                                c = l[1] + a.eventNamespace,
                                d = l[2];
                            d ? s.delegate(d, c, r) : i.bind(c, r)
                        })
                },
                _off: function (e, t) {
                    t = (t || "").split(" ").join(this.eventNamespace + " ") + this.eventNamespace,
                        e.unbind(t).undelegate(t)
                },
                _delay: function (e, t) {
                    function i() {
                        return ("string" == typeof e ? n[e] : e).apply(n, arguments)
                    }

                    var n = this;
                    return setTimeout(i, t || 0)
                },
                _hoverable: function (t) {
                    this.hoverable = this.hoverable.add(t),
                        this._on(t, {
                            mouseenter: function (t) {
                                e(t.currentTarget).addClass("ui-state-hover")
                            },
                            mouseleave: function (t) {
                                e(t.currentTarget).removeClass("ui-state-hover")
                            }
                        })
                },
                _focusable: function (t) {
                    this.focusable = this.focusable.add(t),
                        this._on(t, {
                            focusin: function (t) {
                                e(t.currentTarget).addClass("ui-state-focus")
                            },
                            focusout: function (t) {
                                e(t.currentTarget).removeClass("ui-state-focus")
                            }
                        })
                },
                _trigger: function (t, i, n) {
                    var s, a, o = this.options[t];
                    if (n = n || {}, i = e.Event(i), i.type = (t === this.widgetEventPrefix ? t : this.widgetEventPrefix + t).toLowerCase(), i.target = this.element[0], a = i.originalEvent, a) for (s in a) s in i || (i[s] = a[s]);
                    return this.element.trigger(i, n),
                        !(e.isFunction(o) && o.apply(this.element[0], [i].concat(n)) === !1 || i.isDefaultPrevented())
                }
            },
            e.each({
                show: "fadeIn",
                hide: "fadeOut"
            }, function (t, i) {
                e.Widget.prototype["_" + t] = function (n, s, a) {
                    "string" == typeof s && (s = {
                        effect: s
                    });
                    var o, r = s ? s === !0 || "number" == typeof s ? i : s.effect || i : t;
                    s = s || {},
                    "number" == typeof s && (s = {
                        duration: s
                    }),
                        o = !e.isEmptyObject(s),
                        s.complete = a,
                    s.delay && n.delay(s.delay),
                        o && e.effects && e.effects.effect[r] ? n[t](s) : r !== t && n[r] ? n[r](s.duration, s.easing, a) : n.queue(function (i) {
                            e(this)[t](),
                            a && a.call(n[0]),
                                i()
                        })
                }
            })
    }(jQuery),


    function (e, t) {
        var i = !1;
        e(document).mouseup(function () {
            i = !1
        }),
            e.widget("ui.mouse", {
                version: "1.10.1",
                options: {
                    cancel: "input,textarea,button,select,option",
                    distance: 1,
                    delay: 0
                },
                _mouseInit: function () {
                    var t = this;
                    this.element.bind("mousedown." + this.widgetName, function (e) {
                        return t._mouseDown(e)
                    }).bind("click." + this.widgetName, function (i) {
                        if (!0 === e.data(i.target, t.widgetName + ".preventClickEvent")) return e.removeData(i.target, t.widgetName + ".preventClickEvent"),
                            i.stopImmediatePropagation(),
                            !1
                    }),
                        this.started = !1
                },
                _mouseDestroy: function () {
                    this.element.unbind("." + this.widgetName),
                    this._mouseMoveDelegate && e(document).unbind("mousemove." + this.widgetName, this._mouseMoveDelegate).unbind("mouseup." + this.widgetName, this._mouseUpDelegate)
                },
                _mouseDown: function (t) {
                    if (!i) {
                        this._mouseStarted && this._mouseUp(t),
                            this._mouseDownEvent = t;
                        var n = this,
                            s = 1 === t.which,
                            a = !("string" != typeof this.options.cancel || !t.target.nodeName) && e(t.target).closest(this.options.cancel).length;
                        return !(s && !a && this._mouseCapture(t)) || (this.mouseDelayMet = !this.options.delay, this.mouseDelayMet || (this._mouseDelayTimer = setTimeout(function () {
                                n.mouseDelayMet = !0
                            }, this.options.delay)), this._mouseDistanceMet(t) && this._mouseDelayMet(t) && (this._mouseStarted = this._mouseStart(t) !== !1, !this._mouseStarted) ? (t.preventDefault(), !0) : (!0 === e.data(t.target, this.widgetName + ".preventClickEvent") && e.removeData(t.target, this.widgetName + ".preventClickEvent"), this._mouseMoveDelegate = function (e) {
                                return n._mouseMove(e)
                            }, this._mouseUpDelegate = function (e) {
                                return n._mouseUp(e)
                            }, e(document).bind("mousemove." + this.widgetName, this._mouseMoveDelegate).bind("mouseup." + this.widgetName, this._mouseUpDelegate), t.preventDefault(), i = !0, !0))
                    }
                },
                _mouseMove: function (t) {
                    return e.ui.ie && (!document.documentMode || document.documentMode < 9) && !t.button ? this._mouseUp(t) : this._mouseStarted ? (this._mouseDrag(t), t.preventDefault()) : (this._mouseDistanceMet(t) && this._mouseDelayMet(t) && (this._mouseStarted = this._mouseStart(this._mouseDownEvent, t) !== !1, this._mouseStarted ? this._mouseDrag(t) : this._mouseUp(t)), !this._mouseStarted)
                },
                _mouseUp: function (t) {
                    return e(document).unbind("mousemove." + this.widgetName, this._mouseMoveDelegate).unbind("mouseup." + this.widgetName, this._mouseUpDelegate),
                    this._mouseStarted && (this._mouseStarted = !1, t.target === this._mouseDownEvent.target && e.data(t.target, this.widgetName + ".preventClickEvent", !0), this._mouseStop(t)),
                        !1
                },
                _mouseDistanceMet: function (e) {
                    return Math.max(Math.abs(this._mouseDownEvent.pageX - e.pageX), Math.abs(this._mouseDownEvent.pageY - e.pageY)) >= this.options.distance
                },
                _mouseDelayMet: function () {
                    return this.mouseDelayMet
                },
                _mouseStart: function () {
                },
                _mouseDrag: function () {
                },
                _mouseStop: function () {
                },
                _mouseCapture: function () {
                    return !0
                }
            })
    }(jQuery),


    function (e, t) {
        function i(e, t, i) {
            return [parseFloat(e[0]) * (p.test(e[0]) ? t / 100 : 1), parseFloat(e[1]) * (p.test(e[1]) ? i / 100 : 1)]
        }

        function n(t, i) {
            return parseInt(e.css(t, i), 10) || 0
        }

        function s(t) {
            var i = t[0];
            return 9 === i.nodeType ? {
                width: t.width(),
                height: t.height(),
                offset: {
                    top: 0,
                    left: 0
                }
            } : e.isWindow(i) ? {
                width: t.width(),
                height: t.height(),
                offset: {
                    top: t.scrollTop(),
                    left: t.scrollLeft()
                }
            } : i.preventDefault ? {
                width: 0,
                height: 0,
                offset: {
                    top: i.pageY,
                    left: i.pageX
                }
            } : {
                width: t.outerWidth(),
                height: t.outerHeight(),
                offset: t.offset()
            }
        }

        e.ui = e.ui || {};
        var a, o = Math.max,
            r = Math.abs,
            l = Math.round,
            c = /left|center|right/,
            d = /top|center|bottom/,
            h = /[\+\-]\d+(\.[\d]+)?%?/,
            u = /^\w+/,
            p = /%$/,
            f = e.fn.position;
        e.position = {
            scrollbarWidth: function () {
                if (a !== t) return a;
                var i, n, s = e("<div style='display:block;width:50px;height:50px;overflow:hidden;'><div style='height:100px;width:auto;'></div></div>"),
                    o = s.children()[0];
                return e("body").append(s),
                    i = o.offsetWidth,
                    s.css("overflow", "scroll"),
                    n = o.offsetWidth,
                i === n && (n = s[0].clientWidth),
                    s.remove(),
                    a = i - n
            },
            getScrollInfo: function (t) {
                var i = t.isWindow ? "" : t.element.css("overflow-x"),
                    n = t.isWindow ? "" : t.element.css("overflow-y"),
                    s = "scroll" === i || "auto" === i && t.width < t.element[0].scrollWidth,
                    a = "scroll" === n || "auto" === n && t.height < t.element[0].scrollHeight;
                return {
                    width: s ? e.position.scrollbarWidth() : 0,
                    height: a ? e.position.scrollbarWidth() : 0
                }
            },
            getWithinInfo: function (t) {
                var i = e(t || window),
                    n = e.isWindow(i[0]);
                return {
                    element: i,
                    isWindow: n,
                    offset: i.offset() || {
                        left: 0,
                        top: 0
                    },
                    scrollLeft: i.scrollLeft(),
                    scrollTop: i.scrollTop(),
                    width: n ? i.width() : i.outerWidth(),
                    height: n ? i.height() : i.outerHeight()
                }
            }
        },
            e.fn.position = function (t) {
                if (!t || !t.of) return f.apply(this, arguments);
                t = e.extend({}, t);
                var a, p, m, g, v, y, b = e(t.of),
                    w = e.position.getWithinInfo(t.within),
                    _ = e.position.getScrollInfo(w),
                    x = (t.collision || "flip").split(" "),
                    C = {};
                return y = s(b),
                b[0].preventDefault && (t.at = "left top"),
                    p = y.width,
                    m = y.height,
                    g = y.offset,
                    v = e.extend({}, g),
                    e.each(["my", "at"], function () {
                        var e, i, n = (t[this] || "").split(" ");
                        1 === n.length && (n = c.test(n[0]) ? n.concat(["center"]) : d.test(n[0]) ? ["center"].concat(n) : ["center", "center"]),
                            n[0] = c.test(n[0]) ? n[0] : "center",
                            n[1] = d.test(n[1]) ? n[1] : "center",
                            e = h.exec(n[0]),
                            i = h.exec(n[1]),
                            C[this] = [e ? e[0] : 0, i ? i[0] : 0],
                            t[this] = [u.exec(n[0])[0], u.exec(n[1])[0]]
                    }),
                1 === x.length && (x[1] = x[0]),
                    "right" === t.at[0] ? v.left += p : "center" === t.at[0] && (v.left += p / 2),
                    "bottom" === t.at[1] ? v.top += m : "center" === t.at[1] && (v.top += m / 2),
                    a = i(C.at, p, m),
                    v.left += a[0],
                    v.top += a[1],
                    this.each(function () {
                        var s, c, d = e(this),
                            h = d.outerWidth(),
                            u = d.outerHeight(),
                            f = n(this, "marginLeft"),
                            y = n(this, "marginTop"),
                            k = h + f + n(this, "marginRight") + _.width,
                            T = u + y + n(this, "marginBottom") + _.height,
                            S = e.extend({}, v),
                            D = i(C.my, d.outerWidth(), d.outerHeight());
                        "right" === t.my[0] ? S.left -= h : "center" === t.my[0] && (S.left -= h / 2),
                            "bottom" === t.my[1] ? S.top -= u : "center" === t.my[1] && (S.top -= u / 2),
                            S.left += D[0],
                            S.top += D[1],
                        e.support.offsetFractions || (S.left = l(S.left), S.top = l(S.top)),
                            s = {
                                marginLeft: f,
                                marginTop: y
                            },
                            e.each(["left", "top"], function (i, n) {
                                e.ui.position[x[i]] && e.ui.position[x[i]][n](S, {
                                    targetWidth: p,
                                    targetHeight: m,
                                    elemWidth: h,
                                    elemHeight: u,
                                    collisionPosition: s,
                                    collisionWidth: k,
                                    collisionHeight: T,
                                    offset: [a[0] + D[0], a[1] + D[1]],
                                    my: t.my,
                                    at: t.at,
                                    within: w,
                                    elem: d
                                })
                            }),
                        t.using && (c = function (e) {
                            var i = g.left - S.left,
                                n = i + p - h,
                                s = g.top - S.top,
                                a = s + m - u,
                                l = {
                                    target: {
                                        element: b,
                                        left: g.left,
                                        top: g.top,
                                        width: p,
                                        height: m
                                    },
                                    element: {
                                        element: d,
                                        left: S.left,
                                        top: S.top,
                                        width: h,
                                        height: u
                                    },
                                    horizontal: n < 0 ? "left" : i > 0 ? "right" : "center",
                                    vertical: a < 0 ? "top" : s > 0 ? "bottom" : "middle"
                                };
                            p < h && r(i + n) < p && (l.horizontal = "center"),
                            m < u && r(s + a) < m && (l.vertical = "middle"),
                                o(r(i), r(n)) > o(r(s), r(a)) ? l.important = "horizontal" : l.important = "vertical",
                                t.using.call(this, e, l)
                        }),
                            d.offset(e.extend(S, {
                                using: c
                            }))
                    })
            },
            e.ui.position = {
                fit: {
                    left: function (e, t) {
                        var i, n = t.within,
                            s = n.isWindow ? n.scrollLeft : n.offset.left,
                            a = n.width,
                            r = e.left - t.collisionPosition.marginLeft,
                            l = s - r,
                            c = r + t.collisionWidth - a - s;
                        t.collisionWidth > a ? l > 0 && c <= 0 ? (i = e.left + l + t.collisionWidth - a - s, e.left += l - i) : c > 0 && l <= 0 ? e.left = s : l > c ? e.left = s + a - t.collisionWidth : e.left = s : l > 0 ? e.left += l : c > 0 ? e.left -= c : e.left = o(e.left - r, e.left)
                    },
                    top: function (e, t) {
                        var i, n = t.within,
                            s = n.isWindow ? n.scrollTop : n.offset.top,
                            a = t.within.height,
                            r = e.top - t.collisionPosition.marginTop,
                            l = s - r,
                            c = r + t.collisionHeight - a - s;
                        t.collisionHeight > a ? l > 0 && c <= 0 ? (i = e.top + l + t.collisionHeight - a - s, e.top += l - i) : c > 0 && l <= 0 ? e.top = s : l > c ? e.top = s + a - t.collisionHeight : e.top = s : l > 0 ? e.top += l : c > 0 ? e.top -= c : e.top = o(e.top - r, e.top)
                    }
                },
                flip: {
                    left: function (e, t) {
                        var i, n, s = t.within,
                            a = s.offset.left + s.scrollLeft,
                            o = s.width,
                            l = s.isWindow ? s.scrollLeft : s.offset.left,
                            c = e.left - t.collisionPosition.marginLeft,
                            d = c - l,
                            h = c + t.collisionWidth - o - l,
                            u = "left" === t.my[0] ? -t.elemWidth : "right" === t.my[0] ? t.elemWidth : 0,
                            p = "left" === t.at[0] ? t.targetWidth : "right" === t.at[0] ? -t.targetWidth : 0,
                            f = -2 * t.offset[0];
                        d < 0 ? (i = e.left + u + p + f + t.collisionWidth - o - a, (i < 0 || i < r(d)) && (e.left += u + p + f)) : h > 0 && (n = e.left - t.collisionPosition.marginLeft + u + p + f - l, (n > 0 || r(n) < h) && (e.left += u + p + f))
                    },
                    top: function (e, t) {
                        var i, n, s = t.within,
                            a = s.offset.top + s.scrollTop,
                            o = s.height,
                            l = s.isWindow ? s.scrollTop : s.offset.top,
                            c = e.top - t.collisionPosition.marginTop,
                            d = c - l,
                            h = c + t.collisionHeight - o - l,
                            u = "top" === t.my[1],
                            p = u ? -t.elemHeight : "bottom" === t.my[1] ? t.elemHeight : 0,
                            f = "top" === t.at[1] ? t.targetHeight : "bottom" === t.at[1] ? -t.targetHeight : 0,
                            m = -2 * t.offset[1];
                        d < 0 ? (n = e.top + p + f + m + t.collisionHeight - o - a, e.top + p + f + m > d && (n < 0 || n < r(d)) && (e.top += p + f + m)) : h > 0 && (i = e.top - t.collisionPosition.marginTop + p + f + m - l, e.top + p + f + m > h && (i > 0 || r(i) < h) && (e.top += p + f + m))
                    }
                },
                flipfit: {
                    left: function () {
                        e.ui.position.flip.left.apply(this, arguments),
                            e.ui.position.fit.left.apply(this, arguments)
                    },
                    top: function () {
                        e.ui.position.flip.top.apply(this, arguments),
                            e.ui.position.fit.top.apply(this, arguments)
                    }
                }
            },


            function () {
                var t, i, n, s, a, o = document.getElementsByTagName("body")[0],
                    r = document.createElement("div");
                t = document.createElement(o ? "div" : "body"),
                    n = {
                        visibility: "hidden",
                        width: 0,
                        height: 0,
                        border: 0,
                        margin: 0,
                        background: "none"
                    },
                o && e.extend(n, {
                    position: "absolute",
                    left: "-1000px",
                    top: "-1000px"
                });
                for (a in n) t.style[a] = n[a];
                t.appendChild(r),
                    i = o || document.documentElement,
                    i.insertBefore(t, i.firstChild),
                    r.style.cssText = "position: absolute; left: 10.7432222px;",
                    s = e(r).offset().left,
                    e.support.offsetFractions = s > 10 && s < 11,
                    t.innerHTML = "",
                    i.removeChild(t)
            }()
    }(jQuery),


    function (e, t) {
        e.widget("ui.draggable", e.ui.mouse, {
            version: "1.10.1",
            widgetEventPrefix: "drag",
            options: {
                addClasses: !0,
                appendTo: "parent",
                axis: !1,
                connectToSortable: !1,
                containment: !1,
                cursor: "auto",
                cursorAt: !1,
                grid: !1,
                handle: !1,
                helper: "original",
                iframeFix: !1,
                opacity: !1,
                refreshPositions: !1,
                revert: !1,
                revertDuration: 500,
                scope: "default",
                scroll: !0,
                scrollSensitivity: 20,
                scrollSpeed: 20,
                snap: !1,
                snapMode: "both",
                snapTolerance: 20,
                stack: !1,
                zIndex: !1,
                drag: null,
                start: null,
                stop: null
            },
            _create: function () {
                "original" === this.options.helper && !/^(?:r|a|f)/.test(this.element.css("position")) && (this.element[0].style.position = "relative"),
                this.options.addClasses && this.element.addClass("ui-draggable"),
                this.options.disabled && this.element.addClass("ui-draggable-disabled"),
                    this._mouseInit()
            },
            _destroy: function () {
                this.element.removeClass("ui-draggable ui-draggable-dragging ui-draggable-disabled"),
                    this._mouseDestroy()
            },
            _mouseCapture: function (t) {
                var i = this.options;
                return !(this.helper || i.disabled || e(t.target).closest(".ui-resizable-handle").length > 0) && (this.handle = this._getHandle(t), !!this.handle && (e(i.iframeFix === !0 ? "iframe" : i.iframeFix).each(function () {
                        e("<div class='ui-draggable-iframeFix' style='background: #fff;'></div>").css({
                            width: this.offsetWidth + "px",
                            height: this.offsetHeight + "px",
                            position: "absolute",
                            opacity: "0.001",
                            zIndex: 1e3
                        }).css(e(this).offset()).appendTo("body")
                    }), !0))
            },
            _mouseStart: function (t) {
                var i = this.options;
                return this.helper = this._createHelper(t),
                    this.helper.addClass("ui-draggable-dragging"),
                    this._cacheHelperProportions(),
                e.ui.ddmanager && (e.ui.ddmanager.current = this),
                    this._cacheMargins(),
                    this.cssPosition = this.helper.css("position"),
                    this.scrollParent = this.helper.scrollParent(),
                    this.offset = this.positionAbs = this.element.offset(),
                    this.offset = {
                        top: this.offset.top - this.margins.top,
                        left: this.offset.left - this.margins.left
                    },
                    e.extend(this.offset, {
                        click: {
                            left: t.pageX - this.offset.left,
                            top: t.pageY - this.offset.top
                        },
                        parent: this._getParentOffset(),
                        relative: this._getRelativeOffset()
                    }),
                    this.originalPosition = this.position = this._generatePosition(t),
                    this.originalPageX = t.pageX,
                    this.originalPageY = t.pageY,
                i.cursorAt && this._adjustOffsetFromHelper(i.cursorAt),
                i.containment && this._setContainment(),
                    this._trigger("start", t) === !1 ? (this._clear(), !1) : (this._cacheHelperProportions(), e.ui.ddmanager && !i.dropBehaviour && e.ui.ddmanager.prepareOffsets(this, t), this._mouseDrag(t, !0), e.ui.ddmanager && e.ui.ddmanager.dragStart(this, t), !0)
            },
            _mouseDrag: function (t, i) {
                if (this.position = this._generatePosition(t), this.positionAbs = this._convertPositionTo("absolute"), !i) {
                    var n = this._uiHash();
                    if (this._trigger("drag", t, n) === !1) return this._mouseUp({}),
                        !1;
                    this.position = n.position
                }
                return this.options.axis && "y" === this.options.axis || (this.helper[0].style.left = this.position.left + "px"),
                this.options.axis && "x" === this.options.axis || (this.helper[0].style.top = this.position.top + "px"),
                e.ui.ddmanager && e.ui.ddmanager.drag(this, t),
                    !1
            },
            _mouseStop: function (t) {
                var i, n = this,
                    s = !1,
                    a = !1;
                for (e.ui.ddmanager && !this.options.dropBehaviour && (a = e.ui.ddmanager.drop(this, t)), this.dropped && (a = this.dropped, this.dropped = !1), i = this.element[0]; i && (i = i.parentNode);) i === document && (s = !0);
                return !(!s && "original" === this.options.helper) && ("invalid" === this.options.revert && !a || "valid" === this.options.revert && a || this.options.revert === !0 || e.isFunction(this.options.revert) && this.options.revert.call(this.element, a) ? e(this.helper).animate(this.originalPosition, parseInt(this.options.revertDuration, 10), function () {
                        n._trigger("stop", t) !== !1 && n._clear()
                    }) : this._trigger("stop", t) !== !1 && this._clear(), !1)
            },
            _mouseUp: function (t) {
                return e("div.ui-draggable-iframeFix").each(function () {
                    this.parentNode.removeChild(this)
                }),
                e.ui.ddmanager && e.ui.ddmanager.dragStop(this, t),
                    e.ui.mouse.prototype._mouseUp.call(this, t)
            },
            cancel: function () {
                return this.helper.is(".ui-draggable-dragging") ? this._mouseUp({}) : this._clear(),
                    this
            },
            _getHandle: function (t) {
                var i = !this.options.handle || !e(this.options.handle, this.element).length;
                return e(this.options.handle, this.element).find("*").addBack().each(function () {
                    this === t.target && (i = !0)
                }),
                    i
            },
            _createHelper: function (t) {
                var i = this.options,
                    n = e.isFunction(i.helper) ? e(i.helper.apply(this.element[0], [t])) : "clone" === i.helper ? this.element.clone().removeAttr("id") : this.element;
                return n.parents("body").length || n.appendTo("parent" === i.appendTo ? this.element[0].parentNode : i.appendTo),
                n[0] !== this.element[0] && !/(fixed|absolute)/.test(n.css("position")) && n.css("position", "absolute"),
                    n
            },
            _adjustOffsetFromHelper: function (t) {
                "string" == typeof t && (t = t.split(" ")),
                e.isArray(t) && (t = {
                    left: +t[0],
                    top: +t[1] || 0
                }),
                "left" in t && (this.offset.click.left = t.left + this.margins.left),
                "right" in t && (this.offset.click.left = this.helperProportions.width - t.right + this.margins.left),
                "top" in t && (this.offset.click.top = t.top + this.margins.top),
                "bottom" in t && (this.offset.click.top = this.helperProportions.height - t.bottom + this.margins.top)
            },
            _getParentOffset: function () {
                this.offsetParent = this.helper.offsetParent();
                var t = this.offsetParent.offset();
                return "absolute" === this.cssPosition && this.scrollParent[0] !== document && e.contains(this.scrollParent[0], this.offsetParent[0]) && (t.left += this.scrollParent.scrollLeft(), t.top += this.scrollParent.scrollTop()),
                (this.offsetParent[0] === document.body || this.offsetParent[0].tagName && "html" === this.offsetParent[0].tagName.toLowerCase() && e.ui.ie) && (t = {
                    top: 0,
                    left: 0
                }),
                {
                    top: t.top + (parseInt(this.offsetParent.css("borderTopWidth"), 10) || 0),
                    left: t.left + (parseInt(this.offsetParent.css("borderLeftWidth"), 10) || 0)
                }
            },
            _getRelativeOffset: function () {
                if ("relative" === this.cssPosition) {
                    var e = this.element.position();
                    return {
                        top: e.top - (parseInt(this.helper.css("top"), 10) || 0) + this.scrollParent.scrollTop(),
                        left: e.left - (parseInt(this.helper.css("left"), 10) || 0) + this.scrollParent.scrollLeft()
                    }
                }
                return {
                    top: 0,
                    left: 0
                }
            },
            _cacheMargins: function () {
                this.margins = {
                    left: parseInt(this.element.css("marginLeft"), 10) || 0,
                    top: parseInt(this.element.css("marginTop"), 10) || 0,
                    right: parseInt(this.element.css("marginRight"), 10) || 0,
                    bottom: parseInt(this.element.css("marginBottom"), 10) || 0
                }
            },
            _cacheHelperProportions: function () {
                this.helperProportions = {
                    width: this.helper.outerWidth(),
                    height: this.helper.outerHeight()
                }
            },
            _setContainment: function () {
                var t, i, n, s = this.options;
                if ("parent" === s.containment && (s.containment = this.helper[0].parentNode), "document" !== s.containment && "window" !== s.containment || (this.containment = ["document" === s.containment ? 0 : e(window).scrollLeft() - this.offset.relative.left - this.offset.parent.left, "document" === s.containment ? 0 : e(window).scrollTop() - this.offset.relative.top - this.offset.parent.top, ("document" === s.containment ? 0 : e(window).scrollLeft()) + e("document" === s.containment ? document : window).width() - this.helperProportions.width - this.margins.left, ("document" === s.containment ? 0 : e(window).scrollTop()) + (e("document" === s.containment ? document : window).height() || document.body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top]), /^(document|window|parent)$/.test(s.containment) || s.containment.constructor === Array) s.containment.constructor === Array && (this.containment = s.containment);
                else {
                    if (i = e(s.containment), n = i[0], !n) return;
                    t = "hidden" !== e(n).css("overflow"),
                        this.containment = [(parseInt(e(n).css("borderLeftWidth"), 10) || 0) + (parseInt(e(n).css("paddingLeft"), 10) || 0), (parseInt(e(n).css("borderTopWidth"), 10) || 0) + (parseInt(e(n).css("paddingTop"), 10) || 0), (t ? Math.max(n.scrollWidth, n.offsetWidth) : n.offsetWidth) - (parseInt(e(n).css("borderLeftWidth"), 10) || 0) - (parseInt(e(n).css("paddingRight"), 10) || 0) - this.helperProportions.width - this.margins.left - this.margins.right, (t ? Math.max(n.scrollHeight, n.offsetHeight) : n.offsetHeight) - (parseInt(e(n).css("borderTopWidth"), 10) || 0) - (parseInt(e(n).css("paddingBottom"), 10) || 0) - this.helperProportions.height - this.margins.top - this.margins.bottom],
                        this.relative_container = i
                }
            },
            _convertPositionTo: function (t, i) {
                i || (i = this.position);
                var n = "absolute" === t ? 1 : -1,
                    s = "absolute" !== this.cssPosition || this.scrollParent[0] !== document && e.contains(this.scrollParent[0], this.offsetParent[0]) ? this.scrollParent : this.offsetParent,
                    a = /(html|body)/i.test(s[0].tagName);
                return {
                    top: i.top + this.offset.relative.top * n + this.offset.parent.top * n - ("fixed" === this.cssPosition ? -this.scrollParent.scrollTop() : a ? 0 : s.scrollTop()) * n,
                    left: i.left + this.offset.relative.left * n + this.offset.parent.left * n - ("fixed" === this.cssPosition ? -this.scrollParent.scrollLeft() : a ? 0 : s.scrollLeft()) * n
                }
            },
            _generatePosition: function (t) {
                var i, n, s, a, o = this.options,
                    r = "absolute" !== this.cssPosition || this.scrollParent[0] !== document && e.contains(this.scrollParent[0], this.offsetParent[0]) ? this.scrollParent : this.offsetParent,
                    l = /(html|body)/i.test(r[0].tagName),
                    c = t.pageX,
                    d = t.pageY;
                return this.originalPosition && (this.containment && (this.relative_container ? (n = this.relative_container.offset(), i = [this.containment[0] + n.left, this.containment[1] + n.top, this.containment[2] + n.left, this.containment[3] + n.top]) : i = this.containment, t.pageX - this.offset.click.left < i[0] && (c = i[0] + this.offset.click.left), t.pageY - this.offset.click.top < i[1] && (d = i[1] + this.offset.click.top), t.pageX - this.offset.click.left > i[2] && (c = i[2] + this.offset.click.left), t.pageY - this.offset.click.top > i[3] && (d = i[3] + this.offset.click.top)), o.grid && (s = o.grid[1] ? this.originalPageY + Math.round((d - this.originalPageY) / o.grid[1]) * o.grid[1] : this.originalPageY, d = i ? s - this.offset.click.top >= i[1] || s - this.offset.click.top > i[3] ? s : s - this.offset.click.top >= i[1] ? s - o.grid[1] : s + o.grid[1] : s, a = o.grid[0] ? this.originalPageX + Math.round((c - this.originalPageX) / o.grid[0]) * o.grid[0] : this.originalPageX, c = i ? a - this.offset.click.left >= i[0] || a - this.offset.click.left > i[2] ? a : a - this.offset.click.left >= i[0] ? a - o.grid[0] : a + o.grid[0] : a)),
                {
                    top: d - this.offset.click.top - this.offset.relative.top - this.offset.parent.top + ("fixed" === this.cssPosition ? -this.scrollParent.scrollTop() : l ? 0 : r.scrollTop()),
                    left: c - this.offset.click.left - this.offset.relative.left - this.offset.parent.left + ("fixed" === this.cssPosition ? -this.scrollParent.scrollLeft() : l ? 0 : r.scrollLeft())
                }
            },
            _clear: function () {
                this.helper.removeClass("ui-draggable-dragging"),
                this.helper[0] !== this.element[0] && !this.cancelHelperRemoval && this.helper.remove(),
                    this.helper = null,
                    this.cancelHelperRemoval = !1
            },
            _trigger: function (t, i, n) {
                return n = n || this._uiHash(),
                    e.ui.plugin.call(this, t, [i, n]),
                "drag" === t && (this.positionAbs = this._convertPositionTo("absolute")),
                    e.Widget.prototype._trigger.call(this, t, i, n)
            },
            plugins: {},
            _uiHash: function () {
                return {
                    helper: this.helper,
                    position: this.position,
                    originalPosition: this.originalPosition,
                    offset: this.positionAbs
                }
            }
        }),
            e.ui.plugin.add("draggable", "connectToSortable", {
                start: function (t, i) {
                    var n = e(this).data("ui-draggable"),
                        s = n.options,
                        a = e.extend({}, i, {
                            item: n.element
                        });
                    n.sortables = [],
                        e(s.connectToSortable).each(function () {
                            var i = e.data(this, "ui-sortable");
                            i && !i.options.disabled && (n.sortables.push({
                                instance: i,
                                shouldRevert: i.options.revert
                            }), i.refreshPositions(), i._trigger("activate", t, a))
                        })
                },
                stop: function (t, i) {
                    var n = e(this).data("ui-draggable"),
                        s = e.extend({}, i, {
                            item: n.element
                        });
                    e.each(n.sortables, function () {
                        this.instance.isOver ? (this.instance.isOver = 0, n.cancelHelperRemoval = !0, this.instance.cancelHelperRemoval = !1, this.shouldRevert && (this.instance.options.revert = !0), this.instance._mouseStop(t), this.instance.options.helper = this.instance.options._helper, "original" === n.options.helper && this.instance.currentItem.css({
                            top: "auto",
                            left: "auto"
                        })) : (this.instance.cancelHelperRemoval = !1, this.instance._trigger("deactivate", t, s))
                    })
                },
                drag: function (t, i) {
                    var n = e(this).data("ui-draggable"),
                        s = this;
                    e.each(n.sortables, function () {
                        var a = !1,
                            o = this;
                        this.instance.positionAbs = n.positionAbs,
                            this.instance.helperProportions = n.helperProportions,
                            this.instance.offset.click = n.offset.click,
                        this.instance._intersectsWith(this.instance.containerCache) && (a = !0, e.each(n.sortables, function () {
                            return this.instance.positionAbs = n.positionAbs,
                                this.instance.helperProportions = n.helperProportions,
                                this.instance.offset.click = n.offset.click,
                            this !== o && this.instance._intersectsWith(this.instance.containerCache) && e.contains(o.instance.element[0], this.instance.element[0]) && (a = !1),
                                a
                        })),
                            a ? (this.instance.isOver || (this.instance.isOver = 1, this.instance.currentItem = e(s).clone().removeAttr("id").appendTo(this.instance.element).data("ui-sortable-item", !0), this.instance.options._helper = this.instance.options.helper, this.instance.options.helper = function () {
                                return i.helper[0]
                            }, t.target = this.instance.currentItem[0], this.instance._mouseCapture(t, !0), this.instance._mouseStart(t, !0, !0), this.instance.offset.click.top = n.offset.click.top, this.instance.offset.click.left = n.offset.click.left, this.instance.offset.parent.left -= n.offset.parent.left - this.instance.offset.parent.left, this.instance.offset.parent.top -= n.offset.parent.top - this.instance.offset.parent.top, n._trigger("toSortable", t), n.dropped = this.instance.element, n.currentItem = n.element, this.instance.fromOutside = n), this.instance.currentItem && this.instance._mouseDrag(t)) : this.instance.isOver && (this.instance.isOver = 0, this.instance.cancelHelperRemoval = !0, this.instance.options.revert = !1, this.instance._trigger("out", t, this.instance._uiHash(this.instance)), this.instance._mouseStop(t, !0), this.instance.options.helper = this.instance.options._helper, this.instance.currentItem.remove(), this.instance.placeholder && this.instance.placeholder.remove(), n._trigger("fromSortable", t), n.dropped = !1)
                    })
                }
            }),
            e.ui.plugin.add("draggable", "cursor", {
                start: function () {
                    var t = e("body"),
                        i = e(this).data("ui-draggable").options;
                    t.css("cursor") && (i._cursor = t.css("cursor")),
                        t.css("cursor", i.cursor)
                },
                stop: function () {
                    var t = e(this).data("ui-draggable").options;
                    t._cursor && e("body").css("cursor", t._cursor)
                }
            }),
            e.ui.plugin.add("draggable", "opacity", {
                start: function (t, i) {
                    var n = e(i.helper),
                        s = e(this).data("ui-draggable").options;
                    n.css("opacity") && (s._opacity = n.css("opacity")),
                        n.css("opacity", s.opacity)
                },
                stop: function (t, i) {
                    var n = e(this).data("ui-draggable").options;
                    n._opacity && e(i.helper).css("opacity", n._opacity)
                }
            }),
            e.ui.plugin.add("draggable", "scroll", {
                start: function () {
                    var t = e(this).data("ui-draggable");
                    t.scrollParent[0] !== document && "HTML" !== t.scrollParent[0].tagName && (t.overflowOffset = t.scrollParent.offset())
                },
                drag: function (t) {
                    var i = e(this).data("ui-draggable"),
                        n = i.options,
                        s = !1;
                    i.scrollParent[0] !== document && "HTML" !== i.scrollParent[0].tagName ? (n.axis && "x" === n.axis || (i.overflowOffset.top + i.scrollParent[0].offsetHeight - t.pageY < n.scrollSensitivity ? i.scrollParent[0].scrollTop = s = i.scrollParent[0].scrollTop + n.scrollSpeed : t.pageY - i.overflowOffset.top < n.scrollSensitivity && (i.scrollParent[0].scrollTop = s = i.scrollParent[0].scrollTop - n.scrollSpeed)), n.axis && "y" === n.axis || (i.overflowOffset.left + i.scrollParent[0].offsetWidth - t.pageX < n.scrollSensitivity ? i.scrollParent[0].scrollLeft = s = i.scrollParent[0].scrollLeft + n.scrollSpeed : t.pageX - i.overflowOffset.left < n.scrollSensitivity && (i.scrollParent[0].scrollLeft = s = i.scrollParent[0].scrollLeft - n.scrollSpeed))) : (n.axis && "x" === n.axis || (t.pageY - e(document).scrollTop() < n.scrollSensitivity ? s = e(document).scrollTop(e(document).scrollTop() - n.scrollSpeed) : e(window).height() - (t.pageY - e(document).scrollTop()) < n.scrollSensitivity && (s = e(document).scrollTop(e(document).scrollTop() + n.scrollSpeed))), n.axis && "y" === n.axis || (t.pageX - e(document).scrollLeft() < n.scrollSensitivity ? s = e(document).scrollLeft(e(document).scrollLeft() - n.scrollSpeed) : e(window).width() - (t.pageX - e(document).scrollLeft()) < n.scrollSensitivity && (s = e(document).scrollLeft(e(document).scrollLeft() + n.scrollSpeed)))),
                    s !== !1 && e.ui.ddmanager && !n.dropBehaviour && e.ui.ddmanager.prepareOffsets(i, t)
                }
            }),
            e.ui.plugin.add("draggable", "snap", {
                start: function () {
                    var t = e(this).data("ui-draggable"),
                        i = t.options;
                    t.snapElements = [],
                        e(i.snap.constructor !== String ? i.snap.items || ":data(ui-draggable)" : i.snap).each(function () {
                            var i = e(this),
                                n = i.offset();
                            this !== t.element[0] && t.snapElements.push({
                                item: this,
                                width: i.outerWidth(),
                                height: i.outerHeight(),
                                top: n.top,
                                left: n.left
                            })
                        })
                },
                drag: function (t, i) {
                    var n, s, a, o, r, l, c, d, h, u, p = e(this).data("ui-draggable"),
                        f = p.options,
                        m = f.snapTolerance,
                        g = i.offset.left,
                        v = g + p.helperProportions.width,
                        y = i.offset.top,
                        b = y + p.helperProportions.height;
                    for (h = p.snapElements.length - 1; h >= 0; h--) r = p.snapElements[h].left,
                        l = r + p.snapElements[h].width,
                        c = p.snapElements[h].top,
                        d = c + p.snapElements[h].height,
                        r - m < g && g < l + m && c - m < y && y < d + m || r - m < g && g < l + m && c - m < b && b < d + m || r - m < v && v < l + m && c - m < y && y < d + m || r - m < v && v < l + m && c - m < b && b < d + m ? ("inner" !== f.snapMode && (n = Math.abs(c - b) <= m, s = Math.abs(d - y) <= m, a = Math.abs(r - v) <= m, o = Math.abs(l - g) <= m, n && (i.position.top = p._convertPositionTo("relative", {
                                top: c - p.helperProportions.height,
                                left: 0
                            }).top - p.margins.top), s && (i.position.top = p._convertPositionTo("relative", {
                                top: d,
                                left: 0
                            }).top - p.margins.top), a && (i.position.left = p._convertPositionTo("relative", {
                                top: 0,
                                left: r - p.helperProportions.width
                            }).left - p.margins.left), o && (i.position.left = p._convertPositionTo("relative", {
                                top: 0,
                                left: l
                            }).left - p.margins.left)), u = n || s || a || o, "outer" !== f.snapMode && (n = Math.abs(c - y) <= m, s = Math.abs(d - b) <= m, a = Math.abs(r - g) <= m, o = Math.abs(l - v) <= m, n && (i.position.top = p._convertPositionTo("relative", {
                                top: c,
                                left: 0
                            }).top - p.margins.top), s && (i.position.top = p._convertPositionTo("relative", {
                                top: d - p.helperProportions.height,
                                left: 0
                            }).top - p.margins.top), a && (i.position.left = p._convertPositionTo("relative", {
                                top: 0,
                                left: r
                            }).left - p.margins.left), o && (i.position.left = p._convertPositionTo("relative", {
                                top: 0,
                                left: l - p.helperProportions.width
                            }).left - p.margins.left)), !p.snapElements[h].snapping && (n || s || a || o || u) && p.options.snap.snap && p.options.snap.snap.call(p.element, t, e.extend(p._uiHash(), {
                            snapItem: p.snapElements[h].item
                        })), p.snapElements[h].snapping = n || s || a || o || u) : (p.snapElements[h].snapping && p.options.snap.release && p.options.snap.release.call(p.element, t, e.extend(p._uiHash(), {
                            snapItem: p.snapElements[h].item
                        })), p.snapElements[h].snapping = !1)
                }
            }),
            e.ui.plugin.add("draggable", "stack", {
                start: function () {
                    var t, i = this.data("ui-draggable").options,
                        n = e.makeArray(e(i.stack)).sort(function (t, i) {
                            return (parseInt(e(t).css("zIndex"), 10) || 0) - (parseInt(e(i).css("zIndex"), 10) || 0)
                        });
                    n.length && (t = parseInt(e(n[0]).css("zIndex"), 10) || 0, e(n).each(function (i) {
                        e(this).css("zIndex", t + i)
                    }), this.css("zIndex", t + n.length))
                }
            }),
            e.ui.plugin.add("draggable", "zIndex", {
                start: function (t, i) {
                    var n = e(i.helper),
                        s = e(this).data("ui-draggable").options;
                    n.css("zIndex") && (s._zIndex = n.css("zIndex")),
                        n.css("zIndex", s.zIndex)
                },
                stop: function (t, i) {
                    var n = e(this).data("ui-draggable").options;
                    n._zIndex && e(i.helper).css("zIndex", n._zIndex)
                }
            })
    }(jQuery),


    function (e, t) {
        function i(e, t, i) {
            return e > t && e < t + i
        }

        e.widget("ui.droppable", {
            version: "1.10.1",
            widgetEventPrefix: "drop",
            options: {
                accept: "*",
                activeClass: !1,
                addClasses: !0,
                greedy: !1,
                hoverClass: !1,
                scope: "default",
                tolerance: "intersect",
                activate: null,
                deactivate: null,
                drop: null,
                out: null,
                over: null
            },
            _create: function () {
                var t = this.options,
                    i = t.accept;
                this.isover = !1,
                    this.isout = !0,
                    this.accept = e.isFunction(i) ? i : function (e) {
                        return e.is(i)
                    },
                    this.proportions = {
                        width: this.element[0].offsetWidth,
                        height: this.element[0].offsetHeight
                    },
                    e.ui.ddmanager.droppables[t.scope] = e.ui.ddmanager.droppables[t.scope] || [],
                    e.ui.ddmanager.droppables[t.scope].push(this),
                t.addClasses && this.element.addClass("ui-droppable")
            },
            _destroy: function () {
                for (var t = 0, i = e.ui.ddmanager.droppables[this.options.scope]; t < i.length; t++) i[t] === this && i.splice(t, 1);
                this.element.removeClass("ui-droppable ui-droppable-disabled")
            },
            _setOption: function (t, i) {
                "accept" === t && (this.accept = e.isFunction(i) ? i : function (e) {
                    return e.is(i)
                }),
                    e.Widget.prototype._setOption.apply(this, arguments)
            },
            _activate: function (t) {
                var i = e.ui.ddmanager.current;
                this.options.activeClass && this.element.addClass(this.options.activeClass),
                i && this._trigger("activate", t, this.ui(i))
            },
            _deactivate: function (t) {
                var i = e.ui.ddmanager.current;
                this.options.activeClass && this.element.removeClass(this.options.activeClass),
                i && this._trigger("deactivate", t, this.ui(i))
            },
            _over: function (t) {
                var i = e.ui.ddmanager.current;
                i && (i.currentItem || i.element)[0] !== this.element[0] && this.accept.call(this.element[0], i.currentItem || i.element) && (this.options.hoverClass && this.element.addClass(this.options.hoverClass), this._trigger("over", t, this.ui(i)))
            },
            _out: function (t) {
                var i = e.ui.ddmanager.current;
                i && (i.currentItem || i.element)[0] !== this.element[0] && this.accept.call(this.element[0], i.currentItem || i.element) && (this.options.hoverClass && this.element.removeClass(this.options.hoverClass), this._trigger("out", t, this.ui(i)))
            },
            _drop: function (t, i) {
                var n = i || e.ui.ddmanager.current,
                    s = !1;
                return !(!n || (n.currentItem || n.element)[0] === this.element[0]) && (this.element.find(":data(ui-droppable)").not(".ui-draggable-dragging").each(function () {
                        var t = e.data(this, "ui-droppable");
                        if (t.options.greedy && !t.options.disabled && t.options.scope === n.options.scope && t.accept.call(t.element[0], n.currentItem || n.element) && e.ui.intersect(n, e.extend(t, {
                                offset: t.element.offset()
                            }), t.options.tolerance)) return s = !0,
                            !1
                    }), !s && ( !!this.accept.call(this.element[0], n.currentItem || n.element) && (this.options.activeClass && this.element.removeClass(this.options.activeClass), this.options.hoverClass && this.element.removeClass(this.options.hoverClass), this._trigger("drop", t, this.ui(n)), this.element)))
            },
            ui: function (e) {
                return {
                    draggable: e.currentItem || e.element,
                    helper: e.helper,
                    position: e.position,
                    offset: e.positionAbs
                }
            }
        }),
            e.ui.intersect = function (e, t, n) {
                if (!t.offset) return !1;
                var s, a, o = (e.positionAbs || e.position.absolute).left,
                    r = o + e.helperProportions.width,
                    l = (e.positionAbs || e.position.absolute).top,
                    c = l + e.helperProportions.height,
                    d = t.offset.left,
                    h = d + t.proportions.width,
                    u = t.offset.top,
                    p = u + t.proportions.height;
                switch (n) {
                    case "fit":
                        return d <= o && r <= h && u <= l && c <= p;
                    case "intersect":
                        return d < o + e.helperProportions.width / 2 && r - e.helperProportions.width / 2 < h && u < l + e.helperProportions.height / 2 && c - e.helperProportions.height / 2 < p;
                    case "pointer":
                        return s = (e.positionAbs || e.position.absolute).left + (e.clickOffset || e.offset.click).left,
                            a = (e.positionAbs || e.position.absolute).top + (e.clickOffset || e.offset.click).top,
                        i(a, u, t.proportions.height) && i(s, d, t.proportions.width);
                    case "touch":
                        return (l >= u && l <= p || c >= u && c <= p || l < u && c > p) && (o >= d && o <= h || r >= d && r <= h || o < d && r > h);
                    default:
                        return !1
                }
            },
            e.ui.ddmanager = {
                current: null,
                droppables: {
                    "default": []
                },
                prepareOffsets: function (t, i) {
                    var n, s, a = e.ui.ddmanager.droppables[t.options.scope] || [],
                        o = i ? i.type : null,
                        r = (t.currentItem || t.element).find(":data(ui-droppable)").addBack();
                    e: for (n = 0; n < a.length; n++) if (!(a[n].options.disabled || t && !a[n].accept.call(a[n].element[0], t.currentItem || t.element))) {
                        for (s = 0; s < r.length; s++) if (r[s] === a[n].element[0]) {
                            a[n].proportions.height = 0;
                            continue e
                        }
                        a[n].visible = "none" !== a[n].element.css("display"),
                        a[n].visible && ("mousedown" === o && a[n]._activate.call(a[n], i), a[n].offset = a[n].element.offset(), a[n].proportions = {
                            width: a[n].element[0].offsetWidth,
                            height: a[n].element[0].offsetHeight
                        })
                    }
                },
                drop: function (t, i) {
                    var n = !1;
                    return e.each(e.ui.ddmanager.droppables[t.options.scope] || [], function () {
                        this.options && (!this.options.disabled && this.visible && e.ui.intersect(t, this, this.options.tolerance) && (n = this._drop.call(this, i) || n), !this.options.disabled && this.visible && this.accept.call(this.element[0], t.currentItem || t.element) && (this.isout = !0, this.isover = !1, this._deactivate.call(this, i)))
                    }),
                        n
                },
                dragStart: function (t, i) {
                    t.element.parentsUntil("body").bind("scroll.droppable", function () {
                        t.options.refreshPositions || e.ui.ddmanager.prepareOffsets(t, i)
                    })
                },
                drag: function (t, i) {
                    t.options.refreshPositions && e.ui.ddmanager.prepareOffsets(t, i),
                        e.each(e.ui.ddmanager.droppables[t.options.scope] || [], function () {
                            if (!this.options.disabled && !this.greedyChild && this.visible) {
                                var n, s, a, o = e.ui.intersect(t, this, this.options.tolerance),
                                    r = !o && this.isover ? "isout" : o && !this.isover ? "isover" : null;
                                r && (this.options.greedy && (s = this.options.scope, a = this.element.parents(":data(ui-droppable)").filter(function () {
                                    return e.data(this, "ui-droppable").options.scope === s
                                }), a.length && (n = e.data(a[0], "ui-droppable"), n.greedyChild = "isover" === r)), n && "isover" === r && (n.isover = !1, n.isout = !0, n._out.call(n, i)), this[r] = !0, this["isout" === r ? "isover" : "isout"] = !1, this["isover" === r ? "_over" : "_out"].call(this, i), n && "isout" === r && (n.isout = !1, n.isover = !0, n._over.call(n, i)))
                            }
                        })
                },
                dragStop: function (t, i) {
                    t.element.parentsUntil("body").unbind("scroll.droppable"),
                    t.options.refreshPositions || e.ui.ddmanager.prepareOffsets(t, i)
                }
            }
    }(jQuery),


    function (e, t) {
        function i(e) {
            return parseInt(e, 10) || 0
        }

        function n(e) {
            return !isNaN(parseInt(e, 10))
        }

        e.widget("ui.resizable", e.ui.mouse, {
            version: "1.10.1",
            widgetEventPrefix: "resize",
            options: {
                alsoResize: !1,
                animate: !1,
                animateDuration: "slow",
                animateEasing: "swing",
                aspectRatio: !1,
                autoHide: !1,
                containment: !1,
                ghost: !1,
                grid: !1,
                handles: "e,s,se",
                helper: !1,
                maxHeight: null,
                maxWidth: null,
                minHeight: 10,
                minWidth: 10,
                zIndex: 90,
                resize: null,
                start: null,
                stop: null
            },
            _create: function () {
                var t, i, n, s, a, o = this,
                    r = this.options;
                if (this.element.addClass("ui-resizable"), e.extend(this, {
                        _aspectRatio: !!r.aspectRatio,
                        aspectRatio: r.aspectRatio,
                        originalElement: this.element,
                        _proportionallyResizeElements: [],
                        _helper: r.helper || r.ghost || r.animate ? r.helper || "ui-resizable-helper" : null
                    }), this.element[0].nodeName.match(/canvas|textarea|input|select|button|img/i) && (this.element.wrap(e("<div class='ui-wrapper' style='overflow: hidden;'></div>").css({
                        position: this.element.css("position"),
                        width: this.element.outerWidth(),
                        height: this.element.outerHeight(),
                        top: this.element.css("top"),
                        left: this.element.css("left")
                    })), this.element = this.element.parent().data("ui-resizable", this.element.data("ui-resizable")), this.elementIsWrapper = !0, this.element.css({
                        marginLeft: this.originalElement.css("marginLeft"),
                        marginTop: this.originalElement.css("marginTop"),
                        marginRight: this.originalElement.css("marginRight"),
                        marginBottom: this.originalElement.css("marginBottom")
                    }), this.originalElement.css({
                        marginLeft: 0,
                        marginTop: 0,
                        marginRight: 0,
                        marginBottom: 0
                    }), this.originalResizeStyle = this.originalElement.css("resize"), this.originalElement.css("resize", "none"), this._proportionallyResizeElements.push(this.originalElement.css({
                        position: "static",
                        zoom: 1,
                        display: "block"
                    })), this.originalElement.css({
                        margin: this.originalElement.css("margin")
                    }), this._proportionallyResize()), this.handles = r.handles || (e(".ui-resizable-handle", this.element).length ? {
                            n: ".ui-resizable-n",
                            e: ".ui-resizable-e",
                            s: ".ui-resizable-s",
                            w: ".ui-resizable-w",
                            se: ".ui-resizable-se",
                            sw: ".ui-resizable-sw",
                            ne: ".ui-resizable-ne",
                            nw: ".ui-resizable-nw"
                        } : "e,s,se"), this.handles.constructor === String) for ("all" === this.handles && (this.handles = "n,e,s,w,se,sw,ne,nw"), t = this.handles.split(","), this.handles = {}, i = 0; i < t.length; i++) n = e.trim(t[i]),
                    a = "ui-resizable-" + n,
                    s = e("<div class='ui-resizable-handle " + a + "'></div>"),
                    s.css({
                        zIndex: r.zIndex
                    }),
                "se" === n && s.addClass("ui-icon ui-icon-gripsmall-diagonal-se"),
                    this.handles[n] = ".ui-resizable-" + n,
                    this.element.append(s);
                this._renderAxis = function (t) {
                    var i, n, s, a;
                    t = t || this.element;
                    for (i in this.handles) this.handles[i].constructor === String && (this.handles[i] = e(this.handles[i], this.element).show()),
                    this.elementIsWrapper && this.originalElement[0].nodeName.match(/textarea|input|select|button/i) && (n = e(this.handles[i], this.element), a = /sw|ne|nw|se|n|s/.test(i) ? n.outerHeight() : n.outerWidth(), s = ["padding", /ne|nw|n/.test(i) ? "Top" : /se|sw|s/.test(i) ? "Bottom" : /^e$/.test(i) ? "Right" : "Left"].join(""), t.css(s, a), this._proportionallyResize()),
                        e(this.handles[i]).length
                },
                    this._renderAxis(this.element),
                    this._handles = e(".ui-resizable-handle", this.element).disableSelection(),
                    this._handles.mouseover(function () {
                        o.resizing || (this.className && (s = this.className.match(/ui-resizable-(se|sw|ne|nw|n|e|s|w)/i)), o.axis = s && s[1] ? s[1] : "se")
                    }),
                r.autoHide && (this._handles.hide(), e(this.element).addClass("ui-resizable-autohide").mouseenter(function () {
                    r.disabled || (e(this).removeClass("ui-resizable-autohide"), o._handles.show())
                }).mouseleave(function () {
                    r.disabled || o.resizing || (e(this).addClass("ui-resizable-autohide"), o._handles.hide())
                })),
                    this._mouseInit()
            },
            _destroy: function () {
                this._mouseDestroy();
                var t, i = function (t) {
                    e(t).removeClass("ui-resizable ui-resizable-disabled ui-resizable-resizing").removeData("resizable").removeData("ui-resizable").unbind(".resizable").find(".ui-resizable-handle").remove()
                };
                return this.elementIsWrapper && (i(this.element), t = this.element, this.originalElement.css({
                    position: t.css("position"),
                    width: t.outerWidth(),
                    height: t.outerHeight(),
                    top: t.css("top"),
                    left: t.css("left")
                }).insertAfter(t), t.remove()),
                    this.originalElement.css("resize", this.originalResizeStyle),
                    i(this.originalElement),
                    this
            },
            _mouseCapture: function (t) {
                var i, n, s = !1;
                for (i in this.handles) n = e(this.handles[i])[0],
                (n === t.target || e.contains(n, t.target)) && (s = !0);
                return !this.options.disabled && s
            },
            _mouseStart: function (t) {
                var n, s, a, o = this.options,
                    r = this.element.position(),
                    l = this.element;
                return this.resizing = !0,
                    /absolute/.test(l.css("position")) ? l.css({
                        position: "absolute",
                        top: l.css("top"),
                        left: l.css("left")
                    }) : l.is(".ui-draggable") && l.css({
                        position: "absolute",
                        top: r.top,
                        left: r.left
                    }),
                    this._renderProxy(),
                    n = i(this.helper.css("left")),
                    s = i(this.helper.css("top")),
                o.containment && (n += e(o.containment).scrollLeft() || 0, s += e(o.containment).scrollTop() || 0),
                    this.offset = this.helper.offset(),
                    this.position = {
                        left: n,
                        top: s
                    },
                    this.size = this._helper ? {
                        width: l.outerWidth(),
                        height: l.outerHeight()
                    } : {
                        width: l.width(),
                        height: l.height()
                    },
                    this.originalSize = this._helper ? {
                        width: l.outerWidth(),
                        height: l.outerHeight()
                    } : {
                        width: l.width(),
                        height: l.height()
                    },
                    this.originalPosition = {
                        left: n,
                        top: s
                    },
                    this.sizeDiff = {
                        width: l.outerWidth() - l.width(),
                        height: l.outerHeight() - l.height()
                    },
                    this.originalMousePosition = {
                        left: t.pageX,
                        top: t.pageY
                    },
                    this.aspectRatio = "number" == typeof o.aspectRatio ? o.aspectRatio : this.originalSize.width / this.originalSize.height || 1,
                    a = e(".ui-resizable-" + this.axis).css("cursor"),
                    e("body").css("cursor", "auto" === a ? this.axis + "-resize" : a),
                    l.addClass("ui-resizable-resizing"),
                    this._propagate("start", t),
                    !0
            },
            _mouseDrag: function (t) {
                var i, n = this.helper,
                    s = {},
                    a = this.originalMousePosition,
                    o = this.axis,
                    r = this.position.top,
                    l = this.position.left,
                    c = this.size.width,
                    d = this.size.height,
                    h = t.pageX - a.left || 0,
                    u = t.pageY - a.top || 0,
                    p = this._change[o];
                return !!p && (i = p.apply(this, [t, h, u]), this._updateVirtualBoundaries(t.shiftKey), (this._aspectRatio || t.shiftKey) && (i = this._updateRatio(i, t)), i = this._respectSize(i, t), this._updateCache(i), this._propagate("resize", t), this.position.top !== r && (s.top = this.position.top + "px"), this.position.left !== l && (s.left = this.position.left + "px"), this.size.width !== c && (s.width = this.size.width + "px"), this.size.height !== d && (s.height = this.size.height + "px"), n.css(s), !this._helper && this._proportionallyResizeElements.length && this._proportionallyResize(), e.isEmptyObject(s) || this._trigger("resize", t, this.ui()), !1)
            },
            _mouseStop: function (t) {
                this.resizing = !1;
                var i, n, s, a, o, r, l, c = this.options,
                    d = this;
                return this._helper && (i = this._proportionallyResizeElements, n = i.length && /textarea/i.test(i[0].nodeName), s = n && e.ui.hasScroll(i[0], "left") ? 0 : d.sizeDiff.height, a = n ? 0 : d.sizeDiff.width, o = {
                    width: d.helper.width() - a,
                    height: d.helper.height() - s
                }, r = parseInt(d.element.css("left"), 10) + (d.position.left - d.originalPosition.left) || null, l = parseInt(d.element.css("top"), 10) + (d.position.top - d.originalPosition.top) || null, c.animate || this.element.css(e.extend(o, {
                    top: l,
                    left: r
                })), d.helper.height(d.size.height), d.helper.width(d.size.width), this._helper && !c.animate && this._proportionallyResize()),
                    e("body").css("cursor", "auto"),
                    this.element.removeClass("ui-resizable-resizing"),
                    this._propagate("stop", t),
                this._helper && this.helper.remove(),
                    !1
            },
            _updateVirtualBoundaries: function (e) {
                var t, i, s, a, o, r = this.options;
                o = {
                    minWidth: n(r.minWidth) ? r.minWidth : 0,
                    maxWidth: n(r.maxWidth) ? r.maxWidth : 1 / 0,
                    minHeight: n(r.minHeight) ? r.minHeight : 0,
                    maxHeight: n(r.maxHeight) ? r.maxHeight : 1 / 0
                },
                (this._aspectRatio || e) && (t = o.minHeight * this.aspectRatio, s = o.minWidth / this.aspectRatio, i = o.maxHeight * this.aspectRatio, a = o.maxWidth / this.aspectRatio, t > o.minWidth && (o.minWidth = t), s > o.minHeight && (o.minHeight = s), i < o.maxWidth && (o.maxWidth = i), a < o.maxHeight && (o.maxHeight = a)),
                    this._vBoundaries = o
            },
            _updateCache: function (e) {
                this.offset = this.helper.offset(),
                n(e.left) && (this.position.left = e.left),
                n(e.top) && (this.position.top = e.top),
                n(e.height) && (this.size.height = e.height),
                n(e.width) && (this.size.width = e.width)
            },
            _updateRatio: function (e) {
                var t = this.position,
                    i = this.size,
                    s = this.axis;
                return n(e.height) ? e.width = e.height * this.aspectRatio : n(e.width) && (e.height = e.width / this.aspectRatio),
                "sw" === s && (e.left = t.left + (i.width - e.width), e.top = null),
                "nw" === s && (e.top = t.top + (i.height - e.height), e.left = t.left + (i.width - e.width)),
                    e
            },
            _respectSize: function (e) {
                var t = this._vBoundaries,
                    i = this.axis,
                    s = n(e.width) && t.maxWidth && t.maxWidth < e.width,
                    a = n(e.height) && t.maxHeight && t.maxHeight < e.height,
                    o = n(e.width) && t.minWidth && t.minWidth > e.width,
                    r = n(e.height) && t.minHeight && t.minHeight > e.height,
                    l = this.originalPosition.left + this.originalSize.width,
                    c = this.position.top + this.size.height,
                    d = /sw|nw|w/.test(i),
                    h = /nw|ne|n/.test(i);
                return o && (e.width = t.minWidth),
                r && (e.height = t.minHeight),
                s && (e.width = t.maxWidth),
                a && (e.height = t.maxHeight),
                o && d && (e.left = l - t.minWidth),
                s && d && (e.left = l - t.maxWidth),
                r && h && (e.top = c - t.minHeight),
                a && h && (e.top = c - t.maxHeight),
                    e.width || e.height || e.left || !e.top ? !e.width && !e.height && !e.top && e.left && (e.left = null) : e.top = null,
                    e
            },
            _proportionallyResize: function () {
                if (this._proportionallyResizeElements.length) {
                    var e, t, i, n, s, a = this.helper || this.element;
                    for (e = 0; e < this._proportionallyResizeElements.length; e++) {
                        if (s = this._proportionallyResizeElements[e], !this.borderDif) for (this.borderDif = [], i = [s.css("borderTopWidth"), s.css("borderRightWidth"), s.css("borderBottomWidth"), s.css("borderLeftWidth")], n = [s.css("paddingTop"), s.css("paddingRight"), s.css("paddingBottom"), s.css("paddingLeft")], t = 0; t < i.length; t++) this.borderDif[t] = (parseInt(i[t], 10) || 0) + (parseInt(n[t], 10) || 0);
                        s.css({
                            height: a.height() - this.borderDif[0] - this.borderDif[2] || 0,
                            width: a.width() - this.borderDif[1] - this.borderDif[3] || 0
                        })
                    }
                }
            },
            _renderProxy: function () {
                var t = this.element,
                    i = this.options;
                this.elementOffset = t.offset(),
                    this._helper ? (this.helper = this.helper || e("<div style='overflow:hidden;'></div>"), this.helper.addClass(this._helper).css({
                        width: this.element.outerWidth() - 1,
                        height: this.element.outerHeight() - 1,
                        position: "absolute",
                        left: this.elementOffset.left + "px",
                        top: this.elementOffset.top + "px",
                        zIndex: ++i.zIndex
                    }), this.helper.appendTo("body").disableSelection()) : this.helper = this.element
            },
            _change: {
                e: function (e, t) {
                    return {
                        width: this.originalSize.width + t
                    }
                },
                w: function (e, t) {
                    var i = this.originalSize,
                        n = this.originalPosition;
                    return {
                        left: n.left + t,
                        width: i.width - t
                    }
                },
                n: function (e, t, i) {
                    var n = this.originalSize,
                        s = this.originalPosition;
                    return {
                        top: s.top + i,
                        height: n.height - i
                    }
                },
                s: function (e, t, i) {
                    return {
                        height: this.originalSize.height + i
                    }
                },
                se: function (t, i, n) {
                    return e.extend(this._change.s.apply(this, arguments), this._change.e.apply(this, [t, i, n]))
                },
                sw: function (t, i, n) {
                    return e.extend(this._change.s.apply(this, arguments), this._change.w.apply(this, [t, i, n]))
                },
                ne: function (t, i, n) {
                    return e.extend(this._change.n.apply(this, arguments), this._change.e.apply(this, [t, i, n]))
                },
                nw: function (t, i, n) {
                    return e.extend(this._change.n.apply(this, arguments), this._change.w.apply(this, [t, i, n]))
                }
            },
            _propagate: function (t, i) {
                e.ui.plugin.call(this, t, [i, this.ui()]),
                "resize" !== t && this._trigger(t, i, this.ui())
            },
            plugins: {},
            ui: function () {
                return {
                    originalElement: this.originalElement,
                    element: this.element,
                    helper: this.helper,
                    position: this.position,
                    size: this.size,
                    originalSize: this.originalSize,
                    originalPosition: this.originalPosition
                }
            }
        }),
            e.ui.plugin.add("resizable", "animate", {
                stop: function (t) {
                    var i = e(this).data("ui-resizable"),
                        n = i.options,
                        s = i._proportionallyResizeElements,
                        a = s.length && /textarea/i.test(s[0].nodeName),
                        o = a && e.ui.hasScroll(s[0], "left") ? 0 : i.sizeDiff.height,
                        r = a ? 0 : i.sizeDiff.width,
                        l = {
                            width: i.size.width - r,
                            height: i.size.height - o
                        },
                        c = parseInt(i.element.css("left"), 10) + (i.position.left - i.originalPosition.left) || null,
                        d = parseInt(i.element.css("top"), 10) + (i.position.top - i.originalPosition.top) || null;
                    i.element.animate(e.extend(l, d && c ? {
                        top: d,
                        left: c
                    } : {}), {
                        duration: n.animateDuration,
                        easing: n.animateEasing,
                        step: function () {
                            var n = {
                                width: parseInt(i.element.css("width"), 10),
                                height: parseInt(i.element.css("height"), 10),
                                top: parseInt(i.element.css("top"), 10),
                                left: parseInt(i.element.css("left"), 10)
                            };
                            s && s.length && e(s[0]).css({
                                width: n.width,
                                height: n.height
                            }),
                                i._updateCache(n),
                                i._propagate("resize", t)
                        }
                    })
                }
            }),
            e.ui.plugin.add("resizable", "containment", {
                start: function () {
                    var t, n, s, a, o, r, l, c = e(this).data("ui-resizable"),
                        d = c.options,
                        h = c.element,
                        u = d.containment,
                        p = u instanceof e ? u.get(0) : /parent/.test(u) ? h.parent().get(0) : u;
                    p && (c.containerElement = e(p), /document/.test(u) || u === document ? (c.containerOffset = {
                        left: 0,
                        top: 0
                    }, c.containerPosition = {
                        left: 0,
                        top: 0
                    }, c.parentData = {
                        element: e(document),
                        left: 0,
                        top: 0,
                        width: e(document).width(),
                        height: e(document).height() || document.body.parentNode.scrollHeight
                    }) : (t = e(p), n = [], e(["Top", "Right", "Left", "Bottom"]).each(function (e, s) {
                        n[e] = i(t.css("padding" + s))
                    }), c.containerOffset = t.offset(), c.containerPosition = t.position(), c.containerSize = {
                        height: t.innerHeight() - n[3],
                        width: t.innerWidth() - n[1]
                    }, s = c.containerOffset, a = c.containerSize.height, o = c.containerSize.width, r = e.ui.hasScroll(p, "left") ? p.scrollWidth : o, l = e.ui.hasScroll(p) ? p.scrollHeight : a, c.parentData = {
                        element: p,
                        left: s.left,
                        top: s.top,
                        width: r,
                        height: l
                    }))
                },
                resize: function (t) {
                    var i, n, s, a, o = e(this).data("ui-resizable"),
                        r = o.options,
                        l = o.containerOffset,
                        c = o.position,
                        d = o._aspectRatio || t.shiftKey,
                        h = {
                            top: 0,
                            left: 0
                        },
                        u = o.containerElement;
                    u[0] !== document && /static/.test(u.css("position")) && (h = l),
                    c.left < (o._helper ? l.left : 0) && (o.size.width = o.size.width + (o._helper ? o.position.left - l.left : o.position.left - h.left), d && (o.size.height = o.size.width / o.aspectRatio), o.position.left = r.helper ? l.left : 0),
                    c.top < (o._helper ? l.top : 0) && (o.size.height = o.size.height + (o._helper ? o.position.top - l.top : o.position.top), d && (o.size.width = o.size.height * o.aspectRatio), o.position.top = o._helper ? l.top : 0),
                        o.offset.left = o.parentData.left + o.position.left,
                        o.offset.top = o.parentData.top + o.position.top,
                        i = Math.abs((o._helper ? o.offset.left - h.left : o.offset.left - h.left) + o.sizeDiff.width),
                        n = Math.abs((o._helper ? o.offset.top - h.top : o.offset.top - l.top) + o.sizeDiff.height),
                        s = o.containerElement.get(0) === o.element.parent().get(0),
                        a = /relative|absolute/.test(o.containerElement.css("position")),
                    s && a && (i -= o.parentData.left),
                    i + o.size.width >= o.parentData.width && (o.size.width = o.parentData.width - i, d && (o.size.height = o.size.width / o.aspectRatio)),
                    n + o.size.height >= o.parentData.height && (o.size.height = o.parentData.height - n, d && (o.size.width = o.size.height * o.aspectRatio))
                },
                stop: function () {
                    var t = e(this).data("ui-resizable"),
                        i = t.options,
                        n = t.containerOffset,
                        s = t.containerPosition,
                        a = t.containerElement,
                        o = e(t.helper),
                        r = o.offset(),
                        l = o.outerWidth() - t.sizeDiff.width,
                        c = o.outerHeight() - t.sizeDiff.height;
                    t._helper && !i.animate && /relative/.test(a.css("position")) && e(this).css({
                        left: r.left - s.left - n.left,
                        width: l,
                        height: c
                    }),
                    t._helper && !i.animate && /static/.test(a.css("position")) && e(this).css({
                        left: r.left - s.left - n.left,
                        width: l,
                        height: c
                    })
                }
            }),
            e.ui.plugin.add("resizable", "alsoResize", {
                start: function () {
                    var t = e(this).data("ui-resizable"),
                        i = t.options,
                        n = function (t) {
                            e(t).each(function () {
                                var t = e(this);
                                t.data("ui-resizable-alsoresize", {
                                    width: parseInt(t.width(), 10),
                                    height: parseInt(t.height(), 10),
                                    left: parseInt(t.css("left"), 10),
                                    top: parseInt(t.css("top"), 10)
                                })
                            })
                        };
                    "object" != typeof i.alsoResize || i.alsoResize.parentNode ? n(i.alsoResize) : i.alsoResize.length ? (i.alsoResize = i.alsoResize[0], n(i.alsoResize)) : e.each(i.alsoResize, function (e) {
                        n(e)
                    })
                },
                resize: function (t, i) {
                    var n = e(this).data("ui-resizable"),
                        s = n.options,
                        a = n.originalSize,
                        o = n.originalPosition,
                        r = {
                            height: n.size.height - a.height || 0,
                            width: n.size.width - a.width || 0,
                            top: n.position.top - o.top || 0,
                            left: n.position.left - o.left || 0
                        },
                        l = function (t, n) {
                            e(t).each(function () {
                                var t = e(this),
                                    s = e(this).data("ui-resizable-alsoresize"),
                                    a = {},
                                    o = n && n.length ? n : t.parents(i.originalElement[0]).length ? ["width", "height"] : ["width", "height", "top", "left"];
                                e.each(o, function (e, t) {
                                    var i = (s[t] || 0) + (r[t] || 0);
                                    i && i >= 0 && (a[t] = i || null)
                                }),
                                    t.css(a)
                            })
                        };
                    "object" != typeof s.alsoResize || s.alsoResize.nodeType ? l(s.alsoResize) : e.each(s.alsoResize, function (e, t) {
                        l(e, t)
                    })
                },
                stop: function () {
                    e(this).removeData("resizable-alsoresize")
                }
            }),
            e.ui.plugin.add("resizable", "ghost", {
                start: function () {
                    var t = e(this).data("ui-resizable"),
                        i = t.options,
                        n = t.size;
                    t.ghost = t.originalElement.clone(),
                        t.ghost.css({
                            opacity: .25,
                            display: "block",
                            position: "relative",
                            height: n.height,
                            width: n.width,
                            margin: 0,
                            left: 0,
                            top: 0
                        }).addClass("ui-resizable-ghost").addClass("string" == typeof i.ghost ? i.ghost : ""),
                        t.ghost.appendTo(t.helper)
                },
                resize: function () {
                    var t = e(this).data("ui-resizable");
                    t.ghost && t.ghost.css({
                        position: "relative",
                        height: t.size.height,
                        width: t.size.width
                    })
                },
                stop: function () {
                    var t = e(this).data("ui-resizable");
                    t.ghost && t.helper && t.helper.get(0).removeChild(t.ghost.get(0))
                }
            }),
            e.ui.plugin.add("resizable", "grid", {
                resize: function () {
                    var t = e(this).data("ui-resizable"),
                        i = t.options,
                        n = t.size,
                        s = t.originalSize,
                        a = t.originalPosition,
                        o = t.axis,
                        r = "number" == typeof i.grid ? [i.grid, i.grid] : i.grid,
                        l = r[0] || 1,
                        c = r[1] || 1,
                        d = Math.round((n.width - s.width) / l) * l,
                        h = Math.round((n.height - s.height) / c) * c,
                        u = s.width + d,
                        p = s.height + h,
                        f = i.maxWidth && i.maxWidth < u,
                        m = i.maxHeight && i.maxHeight < p,
                        g = i.minWidth && i.minWidth > u,
                        v = i.minHeight && i.minHeight > p;
                    i.grid = r,
                    g && (u += l),
                    v && (p += c),
                    f && (u -= l),
                    m && (p -= c),
                        /^(se|s|e)$/.test(o) ? (t.size.width = u, t.size.height = p) : /^(ne)$/.test(o) ? (t.size.width = u, t.size.height = p, t.position.top = a.top - h) : /^(sw)$/.test(o) ? (t.size.width = u, t.size.height = p, t.position.left = a.left - d) : (t.size.width = u, t.size.height = p, t.position.top = a.top - h, t.position.left = a.left - d)
                }
            })
    }(jQuery),


    function (e, t) {
        e.widget("ui.selectable", e.ui.mouse, {
            version: "1.10.1",
            options: {
                appendTo: "body",
                autoRefresh: !0,
                distance: 0,
                filter: "*",
                tolerance: "touch",
                selected: null,
                selecting: null,
                start: null,
                stop: null,
                unselected: null,
                unselecting: null
            },
            _create: function () {
                var t, i = this;
                this.element.addClass("ui-selectable"),
                    this.dragged = !1,
                    this.refresh = function () {
                        t = e(i.options.filter, i.element[0]),
                            t.addClass("ui-selectee"),
                            t.each(function () {
                                var t = e(this),
                                    i = t.offset();
                                e.data(this, "selectable-item", {
                                    element: this,
                                    $element: t,
                                    left: i.left,
                                    top: i.top,
                                    right: i.left + t.outerWidth(),
                                    bottom: i.top + t.outerHeight(),
                                    startselected: !1,
                                    selected: t.hasClass("ui-selected"),
                                    selecting: t.hasClass("ui-selecting"),
                                    unselecting: t.hasClass("ui-unselecting")
                                })
                            })
                    },
                    this.refresh(),
                    this.selectees = t.addClass("ui-selectee"),
                    this._mouseInit(),
                    this.helper = e("<div class='ui-selectable-helper'></div>")
            },
            _destroy: function () {
                this.selectees.removeClass("ui-selectee").removeData("selectable-item"),
                    this.element.removeClass("ui-selectable ui-selectable-disabled"),
                    this._mouseDestroy()
            },
            _mouseStart: function (t) {
                var i = this,
                    n = this.options;
                this.opos = [t.pageX, t.pageY],
                this.options.disabled || (this.selectees = e(n.filter, this.element[0]), this._trigger("start", t), e(n.appendTo).append(this.helper), this.helper.css({
                    left: t.pageX,
                    top: t.pageY,
                    width: 0,
                    height: 0
                }), n.autoRefresh && this.refresh(), this.selectees.filter(".ui-selected").each(function () {
                    var n = e.data(this, "selectable-item");
                    n.startselected = !0,
                    !t.metaKey && !t.ctrlKey && (n.$element.removeClass("ui-selected"), n.selected = !1, n.$element.addClass("ui-unselecting"), n.unselecting = !0, i._trigger("unselecting", t, {
                        unselecting: n.element
                    }))
                }), e(t.target).parents().addBack().each(function () {
                    var n, s = e.data(this, "selectable-item");
                    if (s) return n = !t.metaKey && !t.ctrlKey || !s.$element.hasClass("ui-selected"),
                        s.$element.removeClass(n ? "ui-unselecting" : "ui-selected").addClass(n ? "ui-selecting" : "ui-unselecting"),
                        s.unselecting = !n,
                        s.selecting = n,
                        s.selected = n,
                        n ? i._trigger("selecting", t, {
                            selecting: s.element
                        }) : i._trigger("unselecting", t, {
                            unselecting: s.element
                        }),
                        !1
                }))
            },
            _mouseDrag: function (t) {
                if (this.dragged = !0, !this.options.disabled) {
                    var i, n = this,
                        s = this.options,
                        a = this.opos[0],
                        o = this.opos[1],
                        r = t.pageX,
                        l = t.pageY;
                    return a > r && (i = r, r = a, a = i),
                    o > l && (i = l, l = o, o = i),
                        this.helper.css({
                            left: a,
                            top: o,
                            width: r - a,
                            height: l - o
                        }),
                        this.selectees.each(function () {
                            var i = e.data(this, "selectable-item"),
                                c = !1;
                            i && i.element !== n.element[0] && ("touch" === s.tolerance ? c = !(i.left > r || i.right < a || i.top > l || i.bottom < o) : "fit" === s.tolerance && (c = i.left > a && i.right < r && i.top > o && i.bottom < l), c ? (i.selected && (i.$element.removeClass("ui-selected"), i.selected = !1), i.unselecting && (i.$element.removeClass("ui-unselecting"), i.unselecting = !1), i.selecting || (i.$element.addClass("ui-selecting"), i.selecting = !0, n._trigger("selecting", t, {
                                selecting: i.element
                            }))) : (i.selecting && ((t.metaKey || t.ctrlKey) && i.startselected ? (i.$element.removeClass("ui-selecting"), i.selecting = !1, i.$element.addClass("ui-selected"), i.selected = !0) : (i.$element.removeClass("ui-selecting"), i.selecting = !1, i.startselected && (i.$element.addClass("ui-unselecting"), i.unselecting = !0), n._trigger("unselecting", t, {
                                unselecting: i.element
                            }))), i.selected && !t.metaKey && !t.ctrlKey && !i.startselected && (i.$element.removeClass("ui-selected"), i.selected = !1, i.$element.addClass("ui-unselecting"), i.unselecting = !0, n._trigger("unselecting", t, {
                                unselecting: i.element
                            }))))
                        }),
                        !1
                }
            },
            _mouseStop: function (t) {
                var i = this;
                return this.dragged = !1,
                    e(".ui-unselecting", this.element[0]).each(function () {
                        var n = e.data(this, "selectable-item");
                        n.$element.removeClass("ui-unselecting"),
                            n.unselecting = !1,
                            n.startselected = !1,
                            i._trigger("unselected", t, {
                                unselected: n.element
                            })
                    }),
                    e(".ui-selecting", this.element[0]).each(function () {
                        var n = e.data(this, "selectable-item");
                        n.$element.removeClass("ui-selecting").addClass("ui-selected"),
                            n.selecting = !1,
                            n.selected = !0,
                            n.startselected = !0,
                            i._trigger("selected", t, {
                                selected: n.element
                            })
                    }),
                    this._trigger("stop", t),
                    this.helper.remove(),
                    !1
            }
        })
    }(jQuery),


    function (e, t) {
        function i(e, t, i) {
            return e > t && e < t + i
        }

        e.widget("ui.sortable", e.ui.mouse, {
            version: "1.10.1",
            widgetEventPrefix: "sort",
            ready: !1,
            options: {
                appendTo: "parent",
                axis: !1,
                connectWith: !1,
                containment: !1,
                cursor: "auto",
                cursorAt: !1,
                dropOnEmpty: !0,
                forcePlaceholderSize: !1,
                forceHelperSize: !1,
                grid: !1,
                handle: !1,
                helper: "original",
                items: "> *",
                opacity: !1,
                placeholder: !1,
                revert: !1,
                scroll: !0,
                scrollSensitivity: 20,
                scrollSpeed: 20,
                scope: "default",
                tolerance: "intersect",
                zIndex: 1e3,
                activate: null,
                beforeStop: null,
                change: null,
                deactivate: null,
                out: null,
                over: null,
                receive: null,
                remove: null,
                sort: null,
                start: null,
                stop: null,
                update: null
            },
            _create: function () {
                var e = this.options;
                this.containerCache = {},
                    this.element.addClass("ui-sortable"),
                    this.refresh(),
                    this.floating = !!this.items.length && ("x" === e.axis || /left|right/.test(this.items[0].item.css("float")) || /inline|table-cell/.test(this.items[0].item.css("display"))),
                    this.offset = this.element.offset(),
                    this._mouseInit(),
                    this.ready = !0
            },
            _destroy: function () {
                this.element.removeClass("ui-sortable ui-sortable-disabled"),
                    this._mouseDestroy();
                for (var e = this.items.length - 1; e >= 0; e--) this.items[e].item.removeData(this.widgetName + "-item");
                return this
            },
            _setOption: function (t, i) {
                "disabled" === t ? (this.options[t] = i, this.widget().toggleClass("ui-sortable-disabled", !!i)) : e.Widget.prototype._setOption.apply(this, arguments)
            },
            _mouseCapture: function (t, i) {
                var n = null,
                    s = !1,
                    a = this;
                return !this.reverting && (!this.options.disabled && "static" !== this.options.type && (this._refreshItems(t), e(t.target).parents().each(function () {
                        if (e.data(this, a.widgetName + "-item") === a) return n = e(this),
                            !1
                    }), e.data(t.target, a.widgetName + "-item") === a && (n = e(t.target)), !!n && (!(this.options.handle && !i && (e(this.options.handle, n).find("*").addBack().each(function () {
                        this === t.target && (s = !0)
                    }), !s)) && (this.currentItem = n, this._removeCurrentsFromItems(), !0))))
            },
            _mouseStart: function (t, i, n) {
                var s, a = this.options;
                if (this.currentContainer = this, this.refreshPositions(), this.helper = this._createHelper(t), this._cacheHelperProportions(), this._cacheMargins(), this.scrollParent = this.helper.scrollParent(), this.offset = this.currentItem.offset(), this.offset = {
                        top: this.offset.top - this.margins.top,
                        left: this.offset.left - this.margins.left
                    }, e.extend(this.offset, {
                        click: {
                            left: t.pageX - this.offset.left,
                            top: t.pageY - this.offset.top
                        },
                        parent: this._getParentOffset(),
                        relative: this._getRelativeOffset()
                    }), this.helper.css("position", "absolute"), this.cssPosition = this.helper.css("position"), this.originalPosition = this._generatePosition(t), this.originalPageX = t.pageX, this.originalPageY = t.pageY, a.cursorAt && this._adjustOffsetFromHelper(a.cursorAt), this.domPosition = {
                        prev: this.currentItem.prev()[0],
                        parent: this.currentItem.parent()[0]
                    }, this.helper[0] !== this.currentItem[0] && this.currentItem.hide(), this._createPlaceholder(), a.containment && this._setContainment(), a.cursor && (e("body").css("cursor") && (this._storedCursor = e("body").css("cursor")), e("body").css("cursor", a.cursor)), a.opacity && (this.helper.css("opacity") && (this._storedOpacity = this.helper.css("opacity")), this.helper.css("opacity", a.opacity)), a.zIndex && (this.helper.css("zIndex") && (this._storedZIndex = this.helper.css("zIndex")), this.helper.css("zIndex", a.zIndex)), this.scrollParent[0] !== document && "HTML" !== this.scrollParent[0].tagName && (this.overflowOffset = this.scrollParent.offset()), this._trigger("start", t, this._uiHash()), this._preserveHelperProportions || this._cacheHelperProportions(), !n) for (s = this.containers.length - 1; s >= 0; s--) this.containers[s]._trigger("activate", t, this._uiHash(this));
                return e.ui.ddmanager && (e.ui.ddmanager.current = this),
                e.ui.ddmanager && !a.dropBehaviour && e.ui.ddmanager.prepareOffsets(this, t),
                    this.dragging = !0,
                    this.helper.addClass("ui-sortable-helper"),
                    this._mouseDrag(t),
                    !0
            },
            _mouseDrag: function (t) {
                var i, n, s, a, o = this.options,
                    r = !1;
                for (this.position = this._generatePosition(t), this.positionAbs = this._convertPositionTo("absolute"), this.lastPositionAbs || (this.lastPositionAbs = this.positionAbs), this.options.scroll && (this.scrollParent[0] !== document && "HTML" !== this.scrollParent[0].tagName ? (this.overflowOffset.top + this.scrollParent[0].offsetHeight - t.pageY < o.scrollSensitivity ? this.scrollParent[0].scrollTop = r = this.scrollParent[0].scrollTop + o.scrollSpeed : t.pageY - this.overflowOffset.top < o.scrollSensitivity && (this.scrollParent[0].scrollTop = r = this.scrollParent[0].scrollTop - o.scrollSpeed), this.overflowOffset.left + this.scrollParent[0].offsetWidth - t.pageX < o.scrollSensitivity ? this.scrollParent[0].scrollLeft = r = this.scrollParent[0].scrollLeft + o.scrollSpeed : t.pageX - this.overflowOffset.left < o.scrollSensitivity && (this.scrollParent[0].scrollLeft = r = this.scrollParent[0].scrollLeft - o.scrollSpeed)) : (t.pageY - e(document).scrollTop() < o.scrollSensitivity ? r = e(document).scrollTop(e(document).scrollTop() - o.scrollSpeed) : e(window).height() - (t.pageY - e(document).scrollTop()) < o.scrollSensitivity && (r = e(document).scrollTop(e(document).scrollTop() + o.scrollSpeed)), t.pageX - e(document).scrollLeft() < o.scrollSensitivity ? r = e(document).scrollLeft(e(document).scrollLeft() - o.scrollSpeed) : e(window).width() - (t.pageX - e(document).scrollLeft()) < o.scrollSensitivity && (r = e(document).scrollLeft(e(document).scrollLeft() + o.scrollSpeed))), r !== !1 && e.ui.ddmanager && !o.dropBehaviour && e.ui.ddmanager.prepareOffsets(this, t)), this.positionAbs = this._convertPositionTo("absolute"), this.options.axis && "y" === this.options.axis || (this.helper[0].style.left = this.position.left + "px"), this.options.axis && "x" === this.options.axis || (this.helper[0].style.top = this.position.top + "px"), i = this.items.length - 1; i >= 0; i--) if (n = this.items[i], s = n.item[0], a = this._intersectsWithPointer(n), a && n.instance === this.currentContainer && !(s === this.currentItem[0] || this.placeholder[1 === a ? "next" : "prev"]()[0] === s || e.contains(this.placeholder[0], s) || "semi-dynamic" === this.options.type && e.contains(this.element[0], s))) {
                    if (this.direction = 1 === a ? "down" : "up", "pointer" !== this.options.tolerance && !this._intersectsWithSides(n)) break;
                    this._rearrange(t, n),
                        this._trigger("change", t, this._uiHash());
                    break
                }
                return this._contactContainers(t),
                e.ui.ddmanager && e.ui.ddmanager.drag(this, t),
                    this._trigger("sort", t, this._uiHash()),
                    this.lastPositionAbs = this.positionAbs,
                    !1
            },
            _mouseStop: function (t, i) {
                if (t) {
                    if (e.ui.ddmanager && !this.options.dropBehaviour && e.ui.ddmanager.drop(this, t), this.options.revert) {
                        var n = this,
                            s = this.placeholder.offset();
                        this.reverting = !0,
                            e(this.helper).animate({
                                left: s.left - this.offset.parent.left - this.margins.left + (this.offsetParent[0] === document.body ? 0 : this.offsetParent[0].scrollLeft),
                                top: s.top - this.offset.parent.top - this.margins.top + (this.offsetParent[0] === document.body ? 0 : this.offsetParent[0].scrollTop)
                            }, parseInt(this.options.revert, 10) || 500, function () {
                                n._clear(t)
                            })
                    } else this._clear(t, i);
                    return !1
                }
            },
            cancel: function () {
                if (this.dragging) {
                    this._mouseUp({
                        target: null
                    }),
                        "original" === this.options.helper ? this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper") : this.currentItem.show();
                    for (var t = this.containers.length - 1; t >= 0; t--) this.containers[t]._trigger("deactivate", null, this._uiHash(this)),
                    this.containers[t].containerCache.over && (this.containers[t]._trigger("out", null, this._uiHash(this)), this.containers[t].containerCache.over = 0)
                }
                return this.placeholder && (this.placeholder[0].parentNode && this.placeholder[0].parentNode.removeChild(this.placeholder[0]), "original" !== this.options.helper && this.helper && this.helper[0].parentNode && this.helper.remove(), e.extend(this, {
                    helper: null,
                    dragging: !1,
                    reverting: !1,
                    _noFinalSort: null
                }), this.domPosition.prev ? e(this.domPosition.prev).after(this.currentItem) : e(this.domPosition.parent).prepend(this.currentItem)),
                    this
            },
            serialize: function (t) {
                var i = this._getItemsAsjQuery(t && t.connected),
                    n = [];
                return t = t || {},
                    e(i).each(function () {
                        var i = (e(t.item || this).attr(t.attribute || "id") || "").match(t.expression || /(.+)[\-=_](.+)/);
                        i && n.push((t.key || i[1] + "[]") + "=" + (t.key && t.expression ? i[1] : i[2]))
                    }),
                !n.length && t.key && n.push(t.key + "="),
                    n.join("&")
            },
            toArray: function (t) {
                var i = this._getItemsAsjQuery(t && t.connected),
                    n = [];
                return t = t || {},
                    i.each(function () {
                        n.push(e(t.item || this).attr(t.attribute || "id") || "")
                    }),
                    n
            },
            _intersectsWith: function (e) {
                var t = this.positionAbs.left,
                    i = t + this.helperProportions.width,
                    n = this.positionAbs.top,
                    s = n + this.helperProportions.height,
                    a = e.left,
                    o = a + e.width,
                    r = e.top,
                    l = r + e.height,
                    c = this.offset.click.top,
                    d = this.offset.click.left,
                    h = n + c > r && n + c < l && t + d > a && t + d < o;
                return "pointer" === this.options.tolerance || this.options.forcePointerForContainers || "pointer" !== this.options.tolerance && this.helperProportions[this.floating ? "width" : "height"] > e[this.floating ? "width" : "height"] ? h : a < t + this.helperProportions.width / 2 && i - this.helperProportions.width / 2 < o && r < n + this.helperProportions.height / 2 && s - this.helperProportions.height / 2 < l
            },
            _intersectsWithPointer: function (e) {
                var t = "x" === this.options.axis || i(this.positionAbs.top + this.offset.click.top, e.top, e.height),
                    n = "y" === this.options.axis || i(this.positionAbs.left + this.offset.click.left, e.left, e.width),
                    s = t && n,
                    a = this._getDragVerticalDirection(),
                    o = this._getDragHorizontalDirection();
                return !!s && (this.floating ? o && "right" === o || "down" === a ? 2 : 1 : a && ("down" === a ? 2 : 1))
            },
            _intersectsWithSides: function (e) {
                var t = i(this.positionAbs.top + this.offset.click.top, e.top + e.height / 2, e.height),
                    n = i(this.positionAbs.left + this.offset.click.left, e.left + e.width / 2, e.width),
                    s = this._getDragVerticalDirection(),
                    a = this._getDragHorizontalDirection();
                return this.floating && a ? "right" === a && n || "left" === a && !n : s && ("down" === s && t || "up" === s && !t)
            },
            _getDragVerticalDirection: function () {
                var e = this.positionAbs.top - this.lastPositionAbs.top;
                return 0 !== e && (e > 0 ? "down" : "up")
            },
            _getDragHorizontalDirection: function () {
                var e = this.positionAbs.left - this.lastPositionAbs.left;
                return 0 !== e && (e > 0 ? "right" : "left")
            },
            refresh: function (e) {
                return this._refreshItems(e),
                    this.refreshPositions(),
                    this
            },
            _connectWith: function () {
                var e = this.options;
                return e.connectWith.constructor === String ? [e.connectWith] : e.connectWith
            },
            _getItemsAsjQuery: function (t) {
                var i, n, s, a, o = [],
                    r = [],
                    l = this._connectWith();
                if (l && t) for (i = l.length - 1; i >= 0; i--) for (s = e(l[i]), n = s.length - 1; n >= 0; n--) a = e.data(s[n], this.widgetFullName),
                a && a !== this && !a.options.disabled && r.push([e.isFunction(a.options.items) ? a.options.items.call(a.element) : e(a.options.items, a.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"), a]);
                for (r.push([e.isFunction(this.options.items) ? this.options.items.call(this.element, null, {
                    options: this.options,
                    item: this.currentItem
                }) : e(this.options.items, this.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"), this]), i = r.length - 1; i >= 0; i--) r[i][0].each(function () {
                    o.push(this)
                });
                return e(o)
            },
            _removeCurrentsFromItems: function () {
                var t = this.currentItem.find(":data(" + this.widgetName + "-item)");
                this.items = e.grep(this.items, function (e) {
                    for (var i = 0; i < t.length; i++) if (t[i] === e.item[0]) return !1;
                    return !0
                })
            },
            _refreshItems: function (t) {
                this.items = [],
                    this.containers = [this];
                var i, n, s, a, o, r, l, c, d = this.items,
                    h = [
                        [e.isFunction(this.options.items) ? this.options.items.call(this.element[0], t, {
                            item: this.currentItem
                        }) : e(this.options.items, this.element), this]
                    ],
                    u = this._connectWith();
                if (u && this.ready) for (i = u.length - 1; i >= 0; i--) for (s = e(u[i]), n = s.length - 1; n >= 0; n--) a = e.data(s[n], this.widgetFullName),
                a && a !== this && !a.options.disabled && (h.push([e.isFunction(a.options.items) ? a.options.items.call(a.element[0], t, {
                    item: this.currentItem
                }) : e(a.options.items, a.element), a]), this.containers.push(a));
                for (i = h.length - 1; i >= 0; i--) for (o = h[i][1], r = h[i][0], n = 0, c = r.length; n < c; n++) l = e(r[n]),
                    l.data(this.widgetName + "-item", o),
                    d.push({
                        item: l,
                        instance: o,
                        width: 0,
                        height: 0,
                        left: 0,
                        top: 0
                    })
            },
            refreshPositions: function (t) {
                this.offsetParent && this.helper && (this.offset.parent = this._getParentOffset());
                var i, n, s, a;
                for (i = this.items.length - 1; i >= 0; i--) n = this.items[i],
                n.instance !== this.currentContainer && this.currentContainer && n.item[0] !== this.currentItem[0] || (s = this.options.toleranceElement ? e(this.options.toleranceElement, n.item) : n.item, t || (n.width = s.outerWidth(), n.height = s.outerHeight()), a = s.offset(), n.left = a.left, n.top = a.top);
                if (this.options.custom && this.options.custom.refreshContainers) this.options.custom.refreshContainers.call(this);
                else for (i = this.containers.length - 1; i >= 0; i--) a = this.containers[i].element.offset(),
                    this.containers[i].containerCache.left = a.left,
                    this.containers[i].containerCache.top = a.top,
                    this.containers[i].containerCache.width = this.containers[i].element.outerWidth(),
                    this.containers[i].containerCache.height = this.containers[i].element.outerHeight();
                return this
            },
            _createPlaceholder: function (t) {
                t = t || this;
                var i, n = t.options;
                n.placeholder && n.placeholder.constructor !== String || (i = n.placeholder, n.placeholder = {
                    element: function () {
                        var n = e(document.createElement(t.currentItem[0].nodeName)).addClass(i || t.currentItem[0].className + " ui-sortable-placeholder").removeClass("ui-sortable-helper")[0];
                        return i || (n.style.visibility = "hidden"),
                            n
                    },
                    update: function (e, s) {
                        i && !n.forcePlaceholderSize || (s.height() || s.height(t.currentItem.innerHeight() - parseInt(t.currentItem.css("paddingTop") || 0, 10) - parseInt(t.currentItem.css("paddingBottom") || 0, 10)), s.width() || s.width(t.currentItem.innerWidth() - parseInt(t.currentItem.css("paddingLeft") || 0, 10) - parseInt(t.currentItem.css("paddingRight") || 0, 10)))
                    }
                }),
                    t.placeholder = e(n.placeholder.element.call(t.element, t.currentItem)),
                    t.currentItem.after(t.placeholder),
                    n.placeholder.update(t, t.placeholder)
            },
            _contactContainers: function (t) {
                var i, n, s, a, o, r, l, c, d, h = null,
                    u = null;
                for (i = this.containers.length - 1; i >= 0; i--) if (!e.contains(this.currentItem[0], this.containers[i].element[0])) if (this._intersectsWith(this.containers[i].containerCache)) {
                    if (h && e.contains(this.containers[i].element[0], h.element[0])) continue;
                    h = this.containers[i],
                        u = i
                } else this.containers[i].containerCache.over && (this.containers[i]._trigger("out", t, this._uiHash(this)), this.containers[i].containerCache.over = 0);
                if (h) if (1 === this.containers.length) this.containers[u]._trigger("over", t, this._uiHash(this)),
                    this.containers[u].containerCache.over = 1;
                else {
                    for (s = 1e4, a = null, o = this.containers[u].floating ? "left" : "top", r = this.containers[u].floating ? "width" : "height", l = this.positionAbs[o] + this.offset.click[o], n = this.items.length - 1; n >= 0; n--) e.contains(this.containers[u].element[0], this.items[n].item[0]) && this.items[n].item[0] !== this.currentItem[0] && (c = this.items[n].item.offset()[o], d = !1, Math.abs(c - l) > Math.abs(c + this.items[n][r] - l) && (d = !0, c += this.items[n][r]), Math.abs(c - l) < s && (s = Math.abs(c - l), a = this.items[n], this.direction = d ? "up" : "down"));
                    if (!a && !this.options.dropOnEmpty) return;
                    this.currentContainer = this.containers[u],
                        a ? this._rearrange(t, a, null, !0) : this._rearrange(t, null, this.containers[u].element, !0),
                        this._trigger("change", t, this._uiHash()),
                        this.containers[u]._trigger("change", t, this._uiHash(this)),
                        this.options.placeholder.update(this.currentContainer, this.placeholder),
                        this.containers[u]._trigger("over", t, this._uiHash(this)),
                        this.containers[u].containerCache.over = 1
                }
            },
            _createHelper: function (t) {
                var i = this.options,
                    n = e.isFunction(i.helper) ? e(i.helper.apply(this.element[0], [t, this.currentItem])) : "clone" === i.helper ? this.currentItem.clone() : this.currentItem;
                return n.parents("body").length || e("parent" !== i.appendTo ? i.appendTo : this.currentItem[0].parentNode)[0].appendChild(n[0]),
                n[0] === this.currentItem[0] && (this._storedCSS = {
                    width: this.currentItem[0].style.width,
                    height: this.currentItem[0].style.height,
                    position: this.currentItem.css("position"),
                    top: this.currentItem.css("top"),
                    left: this.currentItem.css("left")
                }),
                (!n[0].style.width || i.forceHelperSize) && n.width(this.currentItem.width()),
                (!n[0].style.height || i.forceHelperSize) && n.height(this.currentItem.height()),
                    n
            },
            _adjustOffsetFromHelper: function (t) {
                "string" == typeof t && (t = t.split(" ")),
                e.isArray(t) && (t = {
                    left: +t[0],
                    top: +t[1] || 0
                }),
                "left" in t && (this.offset.click.left = t.left + this.margins.left),
                "right" in t && (this.offset.click.left = this.helperProportions.width - t.right + this.margins.left),
                "top" in t && (this.offset.click.top = t.top + this.margins.top),
                "bottom" in t && (this.offset.click.top = this.helperProportions.height - t.bottom + this.margins.top)
            },
            _getParentOffset: function () {
                this.offsetParent = this.helper.offsetParent();
                var t = this.offsetParent.offset();
                return "absolute" === this.cssPosition && this.scrollParent[0] !== document && e.contains(this.scrollParent[0], this.offsetParent[0]) && (t.left += this.scrollParent.scrollLeft(), t.top += this.scrollParent.scrollTop()),
                (this.offsetParent[0] === document.body || this.offsetParent[0].tagName && "html" === this.offsetParent[0].tagName.toLowerCase() && e.ui.ie) && (t = {
                    top: 0,
                    left: 0
                }),
                {
                    top: t.top + (parseInt(this.offsetParent.css("borderTopWidth"), 10) || 0),
                    left: t.left + (parseInt(this.offsetParent.css("borderLeftWidth"), 10) || 0)
                }
            },
            _getRelativeOffset: function () {
                if ("relative" === this.cssPosition) {
                    var e = this.currentItem.position();
                    return {
                        top: e.top - (parseInt(this.helper.css("top"), 10) || 0) + this.scrollParent.scrollTop(),
                        left: e.left - (parseInt(this.helper.css("left"), 10) || 0) + this.scrollParent.scrollLeft()
                    }
                }
                return {
                    top: 0,
                    left: 0
                }
            },
            _cacheMargins: function () {
                this.margins = {
                    left: parseInt(this.currentItem.css("marginLeft"), 10) || 0,
                    top: parseInt(this.currentItem.css("marginTop"), 10) || 0
                }
            },
            _cacheHelperProportions: function () {
                this.helperProportions = {
                    width: this.helper.outerWidth(),
                    height: this.helper.outerHeight()
                }
            },
            _setContainment: function () {
                var t, i, n, s = this.options;
                "parent" === s.containment && (s.containment = this.helper[0].parentNode),
                "document" !== s.containment && "window" !== s.containment || (this.containment = [0 - this.offset.relative.left - this.offset.parent.left, 0 - this.offset.relative.top - this.offset.parent.top, e("document" === s.containment ? document : window).width() - this.helperProportions.width - this.margins.left, (e("document" === s.containment ? document : window).height() || document.body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top]),
                /^(document|window|parent)$/.test(s.containment) || (t = e(s.containment)[0], i = e(s.containment).offset(), n = "hidden" !== e(t).css("overflow"), this.containment = [i.left + (parseInt(e(t).css("borderLeftWidth"), 10) || 0) + (parseInt(e(t).css("paddingLeft"), 10) || 0) - this.margins.left, i.top + (parseInt(e(t).css("borderTopWidth"), 10) || 0) + (parseInt(e(t).css("paddingTop"), 10) || 0) - this.margins.top, i.left + (n ? Math.max(t.scrollWidth, t.offsetWidth) : t.offsetWidth) - (parseInt(e(t).css("borderLeftWidth"), 10) || 0) - (parseInt(e(t).css("paddingRight"), 10) || 0) - this.helperProportions.width - this.margins.left, i.top + (n ? Math.max(t.scrollHeight, t.offsetHeight) : t.offsetHeight) - (parseInt(e(t).css("borderTopWidth"), 10) || 0) - (parseInt(e(t).css("paddingBottom"), 10) || 0) - this.helperProportions.height - this.margins.top])
            },
            _convertPositionTo: function (t, i) {
                i || (i = this.position);
                var n = "absolute" === t ? 1 : -1,
                    s = "absolute" !== this.cssPosition || this.scrollParent[0] !== document && e.contains(this.scrollParent[0], this.offsetParent[0]) ? this.scrollParent : this.offsetParent,
                    a = /(html|body)/i.test(s[0].tagName);
                return {
                    top: i.top + this.offset.relative.top * n + this.offset.parent.top * n - ("fixed" === this.cssPosition ? -this.scrollParent.scrollTop() : a ? 0 : s.scrollTop()) * n,
                    left: i.left + this.offset.relative.left * n + this.offset.parent.left * n - ("fixed" === this.cssPosition ? -this.scrollParent.scrollLeft() : a ? 0 : s.scrollLeft()) * n
                }
            },
            _generatePosition: function (t) {
                var i, n, s = this.options,
                    a = t.pageX,
                    o = t.pageY,
                    r = "absolute" !== this.cssPosition || this.scrollParent[0] !== document && e.contains(this.scrollParent[0], this.offsetParent[0]) ? this.scrollParent : this.offsetParent,
                    l = /(html|body)/i.test(r[0].tagName);
                return "relative" === this.cssPosition && (this.scrollParent[0] === document || this.scrollParent[0] === this.offsetParent[0]) && (this.offset.relative = this._getRelativeOffset()),
                this.originalPosition && (this.containment && (t.pageX - this.offset.click.left < this.containment[0] && (a = this.containment[0] + this.offset.click.left), t.pageY - this.offset.click.top < this.containment[1] && (o = this.containment[1] + this.offset.click.top), t.pageX - this.offset.click.left > this.containment[2] && (a = this.containment[2] + this.offset.click.left), t.pageY - this.offset.click.top > this.containment[3] && (o = this.containment[3] + this.offset.click.top)), s.grid && (i = this.originalPageY + Math.round((o - this.originalPageY) / s.grid[1]) * s.grid[1], o = this.containment ? i - this.offset.click.top >= this.containment[1] && i - this.offset.click.top <= this.containment[3] ? i : i - this.offset.click.top >= this.containment[1] ? i - s.grid[1] : i + s.grid[1] : i, n = this.originalPageX + Math.round((a - this.originalPageX) / s.grid[0]) * s.grid[0], a = this.containment ? n - this.offset.click.left >= this.containment[0] && n - this.offset.click.left <= this.containment[2] ? n : n - this.offset.click.left >= this.containment[0] ? n - s.grid[0] : n + s.grid[0] : n)),
                {
                    top: o - this.offset.click.top - this.offset.relative.top - this.offset.parent.top + ("fixed" === this.cssPosition ? -this.scrollParent.scrollTop() : l ? 0 : r.scrollTop()),
                    left: a - this.offset.click.left - this.offset.relative.left - this.offset.parent.left + ("fixed" === this.cssPosition ? -this.scrollParent.scrollLeft() : l ? 0 : r.scrollLeft())
                }
            },
            _rearrange: function (e, t, i, n) {
                i ? i[0].appendChild(this.placeholder[0]) : t.item[0].parentNode.insertBefore(this.placeholder[0], "down" === this.direction ? t.item[0] : t.item[0].nextSibling),
                    this.counter = this.counter ? ++this.counter : 1;
                var s = this.counter;
                this._delay(function () {
                    s === this.counter && this.refreshPositions(!n)
                })
            },
            _clear: function (t, i) {
                this.reverting = !1;
                var n, s = [];
                if (!this._noFinalSort && this.currentItem.parent().length && this.placeholder.before(this.currentItem), this._noFinalSort = null, this.helper[0] === this.currentItem[0]) {
                    for (n in this._storedCSS)"auto" !== this._storedCSS[n] && "static" !== this._storedCSS[n] || (this._storedCSS[n] = "");
                    this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper")
                } else this.currentItem.show();
                for (this.fromOutside && !i && s.push(function (e) {
                    this._trigger("receive", e, this._uiHash(this.fromOutside))
                }), (this.fromOutside || this.domPosition.prev !== this.currentItem.prev().not(".ui-sortable-helper")[0] || this.domPosition.parent !== this.currentItem.parent()[0]) && !i && s.push(function (e) {
                    this._trigger("update", e, this._uiHash())
                }), this !== this.currentContainer && (i || (s.push(function (e) {
                    this._trigger("remove", e, this._uiHash())
                }), s.push(function (e) {
                    return function (t) {
                        e._trigger("receive", t, this._uiHash(this))
                    }
                }.call(this, this.currentContainer)), s.push(function (e) {
                    return function (t) {
                        e._trigger("update", t, this._uiHash(this))
                    }
                }.call(this, this.currentContainer)))), n = this.containers.length - 1; n >= 0; n--) i || s.push(function (e) {
                    return function (t) {
                        e._trigger("deactivate", t, this._uiHash(this))
                    }
                }.call(this, this.containers[n])),
                this.containers[n].containerCache.over && (s.push(function (e) {
                    return function (t) {
                        e._trigger("out", t, this._uiHash(this))
                    }
                }.call(this, this.containers[n])), this.containers[n].containerCache.over = 0);
                if (this._storedCursor && e("body").css("cursor", this._storedCursor), this._storedOpacity && this.helper.css("opacity", this._storedOpacity), this._storedZIndex && this.helper.css("zIndex", "auto" === this._storedZIndex ? "" : this._storedZIndex), this.dragging = !1, this.cancelHelperRemoval) {
                    if (!i) {
                        for (this._trigger("beforeStop", t, this._uiHash()), n = 0; n < s.length; n++) s[n].call(this, t);
                        this._trigger("stop", t, this._uiHash())
                    }
                    return this.fromOutside = !1,
                        !1
                }
                if (i || this._trigger("beforeStop", t, this._uiHash()), this.placeholder[0].parentNode.removeChild(this.placeholder[0]), this.helper[0] !== this.currentItem[0] && this.helper.remove(), this.helper = null, !i) {
                    for (n = 0; n < s.length; n++) s[n].call(this, t);
                    this._trigger("stop", t, this._uiHash())
                }
                return this.fromOutside = !1,
                    !0
            },
            _trigger: function () {
                e.Widget.prototype._trigger.apply(this, arguments) === !1 && this.cancel()
            },
            _uiHash: function (t) {
                var i = t || this;
                return {
                    helper: i.helper,
                    placeholder: i.placeholder || e([]),
                    position: i.position,
                    originalPosition: i.originalPosition,
                    offset: i.positionAbs,
                    item: i.currentItem,
                    sender: t ? t.element : null
                }
            }
        })
    }(jQuery),


    function (e, t) {
        var i = 0,
            n = {},
            s = {};
        n.height = n.paddingTop = n.paddingBottom = n.borderTopWidth = n.borderBottomWidth = "hide",
            s.height = s.paddingTop = s.paddingBottom = s.borderTopWidth = s.borderBottomWidth = "show",
            e.widget("ui.accordion", {
                version: "1.10.1",
                options: {
                    active: 0,
                    animate: {},
                    collapsible: !1,
                    event: "click",
                    header: "> li > :first-child,> :not(li):even",
                    heightStyle: "auto",
                    icons: {
                        activeHeader: "ui-icon-triangle-1-s",
                        header: "ui-icon-triangle-1-e"
                    },
                    activate: null,
                    beforeActivate: null
                },
                _create: function () {
                    var t = this.options;
                    this.prevShow = this.prevHide = e(),
                        this.element.addClass("ui-accordion ui-widget ui-helper-reset").attr("role", "tablist"),
                    !t.collapsible && (t.active === !1 || null == t.active) && (t.active = 0),
                        this._processPanels(),
                    t.active < 0 && (t.active += this.headers.length),
                        this._refresh()
                },
                _getCreateEventData: function () {
                    return {
                        header: this.active,
                        panel: this.active.length ? this.active.next() : e(),
                        content: this.active.length ? this.active.next() : e()
                    }
                },
                _createIcons: function () {
                    var t = this.options.icons;
                    t && (e("<span>").addClass("ui-accordion-header-icon ui-icon " + t.header).prependTo(this.headers), this.active.children(".ui-accordion-header-icon").removeClass(t.header).addClass(t.activeHeader), this.headers.addClass("ui-accordion-icons"))
                },
                _destroyIcons: function () {
                    this.headers.removeClass("ui-accordion-icons").children(".ui-accordion-header-icon").remove()
                },
                _destroy: function () {
                    var e;
                    this.element.removeClass("ui-accordion ui-widget ui-helper-reset").removeAttr("role"),
                        this.headers.removeClass("ui-accordion-header ui-accordion-header-active ui-helper-reset ui-state-default ui-corner-all ui-state-active ui-state-disabled ui-corner-top").removeAttr("role").removeAttr("aria-selected").removeAttr("aria-controls").removeAttr("tabIndex").each(function () {
                            /^ui-accordion/.test(this.id) && this.removeAttribute("id")
                        }),
                        this._destroyIcons(),
                        e = this.headers.next().css("display", "").removeAttr("role").removeAttr("aria-expanded").removeAttr("aria-hidden").removeAttr("aria-labelledby").removeClass("ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content ui-accordion-content-active ui-state-disabled").each(function () {
                            /^ui-accordion/.test(this.id) && this.removeAttribute("id")
                        }),
                    "content" !== this.options.heightStyle && e.css("height", "")
                },
                _setOption: function (e, t) {
                    return "active" === e ? void this._activate(t) : ("event" === e && (this.options.event && this._off(this.headers, this.options.event), this._setupEvents(t)), this._super(e, t), "collapsible" === e && !t && this.options.active === !1 && this._activate(0), "icons" === e && (this._destroyIcons(), t && this._createIcons()), "disabled" === e && this.headers.add(this.headers.next()).toggleClass("ui-state-disabled", !!t), void 0)
                },
                _keydown: function (t) {
                    if (!t.altKey && !t.ctrlKey) {
                        var i = e.ui.keyCode,
                            n = this.headers.length,
                            s = this.headers.index(t.target),
                            a = !1;
                        switch (t.keyCode) {
                            case i.RIGHT:
                            case i.DOWN:
                                a = this.headers[(s + 1) % n];
                                break;
                            case i.LEFT:
                            case i.UP:
                                a = this.headers[(s - 1 + n) % n];
                                break;
                            case i.SPACE:
                            case i.ENTER:
                                this._eventHandler(t);
                                break;
                            case i.HOME:
                                a = this.headers[0];
                                break;
                            case i.END:
                                a = this.headers[n - 1]
                        }
                        a && (e(t.target).attr("tabIndex", -1), e(a).attr("tabIndex", 0), a.focus(), t.preventDefault())
                    }
                },
                _panelKeyDown: function (t) {
                    t.keyCode === e.ui.keyCode.UP && t.ctrlKey && e(t.currentTarget).prev().focus()
                },
                refresh: function () {
                    var t = this.options;
                    this._processPanels(),
                    (t.active === !1 && t.collapsible === !0 || !this.headers.length) && (t.active = !1, this.active = e()),
                        t.active === !1 ? this._activate(0) : this.active.length && !e.contains(this.element[0], this.active[0]) ? this.headers.length === this.headers.find(".ui-state-disabled").length ? (t.active = !1, this.active = e()) : this._activate(Math.max(0, t.active - 1)) : t.active = this.headers.index(this.active),
                        this._destroyIcons(),
                        this._refresh()
                },
                _processPanels: function () {
                    this.headers = this.element.find(this.options.header).addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-all"),
                        this.headers.next().addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom").filter(":not(.ui-accordion-content-active)").hide()
                },
                _refresh: function () {
                    var t, n = this.options,
                        s = n.heightStyle,
                        a = this.element.parent(),
                        o = this.accordionId = "ui-accordion-" + (this.element.attr("id") || ++i);
                    this.active = this._findActive(n.active).addClass("ui-accordion-header-active ui-state-active ui-corner-top").removeClass("ui-corner-all"),
                        this.active.next().addClass("ui-accordion-content-active").show(),
                        this.headers.attr("role", "tab").each(function (t) {
                            var i = e(this),
                                n = i.attr("id"),
                                s = i.next(),
                                a = s.attr("id");
                            n || (n = o + "-header-" + t, i.attr("id", n)),
                            a || (a = o + "-panel-" + t, s.attr("id", a)),
                                i.attr("aria-controls", a),
                                s.attr("aria-labelledby", n)
                        }).next().attr("role", "tabpanel"),
                        this.headers.not(this.active).attr({
                            "aria-selected": "false",
                            tabIndex: -1
                        }).next().attr({
                            "aria-expanded": "false",
                            "aria-hidden": "true"
                        }).hide(),
                        this.active.length ? this.active.attr({
                            "aria-selected": "true",
                            tabIndex: 0
                        }).next().attr({
                            "aria-expanded": "true",
                            "aria-hidden": "false"
                        }) : this.headers.eq(0).attr("tabIndex", 0),
                        this._createIcons(),
                        this._setupEvents(n.event),
                        "fill" === s ? (t = a.height(), this.element.siblings(":visible").each(function () {
                            var i = e(this),
                                n = i.css("position");
                            "absolute" !== n && "fixed" !== n && (t -= i.outerHeight(!0))
                        }), this.headers.each(function () {
                            t -= e(this).outerHeight(!0)
                        }), this.headers.next().each(function () {
                            e(this).height(Math.max(0, t - e(this).innerHeight() + e(this).height()))
                        }).css("overflow", "auto")) : "auto" === s && (t = 0, this.headers.next().each(function () {
                            t = Math.max(t, e(this).css("height", "").height())
                        }).height(t))
                },
                _activate: function (t) {
                    var i = this._findActive(t)[0];
                    i !== this.active[0] && (i = i || this.active[0], this._eventHandler({
                        target: i,
                        currentTarget: i,
                        preventDefault: e.noop
                    }))
                },
                _findActive: function (t) {
                    return "number" == typeof t ? this.headers.eq(t) : e()
                },
                _setupEvents: function (t) {
                    var i = {
                        keydown: "_keydown"
                    };
                    t && e.each(t.split(" "), function (e, t) {
                        i[t] = "_eventHandler"
                    }),
                        this._off(this.headers.add(this.headers.next())),
                        this._on(this.headers, i),
                        this._on(this.headers.next(), {
                            keydown: "_panelKeyDown"
                        }),
                        this._hoverable(this.headers),
                        this._focusable(this.headers)
                },
                _eventHandler: function (t) {
                    var i = this.options,
                        n = this.active,
                        s = e(t.currentTarget),
                        a = s[0] === n[0],
                        o = a && i.collapsible,
                        r = o ? e() : s.next(),
                        l = n.next(),
                        c = {
                            oldHeader: n,
                            oldPanel: l,
                            newHeader: o ? e() : s,
                            newPanel: r
                        };
                    t.preventDefault(),
                    a && !i.collapsible || this._trigger("beforeActivate", t, c) === !1 || (i.active = !o && this.headers.index(s), this.active = a ? e() : s, this._toggle(c), n.removeClass("ui-accordion-header-active ui-state-active"), i.icons && n.children(".ui-accordion-header-icon").removeClass(i.icons.activeHeader).addClass(i.icons.header), a || (s.removeClass("ui-corner-all").addClass("ui-accordion-header-active ui-state-active ui-corner-top"), i.icons && s.children(".ui-accordion-header-icon").removeClass(i.icons.header).addClass(i.icons.activeHeader), s.next().addClass("ui-accordion-content-active")))
                },
                _toggle: function (t) {
                    var i = t.newPanel,
                        n = this.prevShow.length ? this.prevShow : t.oldPanel;
                    this.prevShow.add(this.prevHide).stop(!0, !0),
                        this.prevShow = i,
                        this.prevHide = n,
                        this.options.animate ? this._animate(i, n, t) : (n.hide(), i.show(), this._toggleComplete(t)),
                        n.attr({
                            "aria-expanded": "false",
                            "aria-hidden": "true"
                        }),
                        n.prev().attr("aria-selected", "false"),
                        i.length && n.length ? n.prev().attr("tabIndex", -1) : i.length && this.headers.filter(function () {
                            return 0 === e(this).attr("tabIndex")
                        }).attr("tabIndex", -1),
                        i.attr({
                            "aria-expanded": "true",
                            "aria-hidden": "false"
                        }).prev().attr({
                            "aria-selected": "true",
                            tabIndex: 0
                        })
                },
                _animate: function (e, t, i) {
                    var a, o, r, l = this,
                        c = 0,
                        d = e.length && (!t.length || e.index() < t.index()),
                        h = this.options.animate || {},
                        u = d && h.down || h,
                        p = function () {
                            l._toggleComplete(i)
                        };
                    return "number" == typeof u && (r = u),
                    "string" == typeof u && (o = u),
                        o = o || u.easing || h.easing,
                        r = r || u.duration || h.duration,
                        t.length ? e.length ? (a = e.show().outerHeight(), t.animate(n, {
                            duration: r,
                            easing: o,
                            step: function (e, t) {
                                t.now = Math.round(e)
                            }
                        }), e.hide().animate(s, {
                            duration: r,
                            easing: o,
                            complete: p,
                            step: function (e, i) {
                                i.now = Math.round(e),
                                    "height" !== i.prop ? c += i.now : "content" !== l.options.heightStyle && (i.now = Math.round(a - t.outerHeight() - c), c = 0)
                            }
                        }), void 0) : t.animate(n, r, o, p) : e.animate(s, r, o, p)
                },
                _toggleComplete: function (e) {
                    var t = e.oldPanel;
                    t.removeClass("ui-accordion-content-active").prev().removeClass("ui-corner-top").addClass("ui-corner-all"),
                    t.length && (t.parent()[0].className = t.parent()[0].className),
                        this._trigger("activate", null, e)
                }
            })
    }(jQuery),


    function (e, t) {
        var i = 0;
        e.widget("ui.autocomplete", {
            version: "1.10.1",
            defaultElement: "<input>",
            options: {
                appendTo: null,
                autoFocus: !1,
                delay: 300,
                minLength: 1,
                position: {
                    my: "left top",
                    at: "left bottom",
                    collision: "none"
                },
                source: null,
                change: null,
                close: null,
                focus: null,
                open: null,
                response: null,
                search: null,
                select: null
            },
            pending: 0,
            _create: function () {
                var t, i, n, s = this.element[0].nodeName.toLowerCase(),
                    a = "textarea" === s,
                    o = "input" === s;
                this.isMultiLine = !!a || !o && this.element.prop("isContentEditable"),
                    this.valueMethod = this.element[a || o ? "val" : "text"],
                    this.isNewMenu = !0,
                    this.element.addClass("ui-autocomplete-input").attr("autocomplete", "off"),
                    this._on(this.element, {
                        keydown: function (s) {
                            if (this.element.prop("readOnly")) return t = !0,
                                n = !0,
                                i = !0,
                                void 0;
                            t = !1,
                                n = !1,
                                i = !1;
                            var a = e.ui.keyCode;
                            switch (s.keyCode) {
                                case a.PAGE_UP:
                                    t = !0,
                                        this._move("previousPage", s);
                                    break;
                                case a.PAGE_DOWN:
                                    t = !0,
                                        this._move("nextPage", s);
                                    break;
                                case a.UP:
                                    t = !0,
                                        this._keyEvent("previous", s);
                                    break;
                                case a.DOWN:
                                    t = !0,
                                        this._keyEvent("next", s);
                                    break;
                                case a.ENTER:
                                case a.NUMPAD_ENTER:
                                    this.menu.active && (t = !0, s.preventDefault(), this.menu.select(s));
                                    break;
                                case a.TAB:
                                    this.menu.active && this.menu.select(s);
                                    break;
                                case a.ESCAPE:
                                    this.menu.element.is(":visible") && (this._value(this.term), this.close(s), s.preventDefault());
                                    break;
                                default:
                                    i = !0,
                                        this._searchTimeout(s)
                            }
                        },
                        keypress: function (n) {
                            if (t) return t = !1,
                                void n.preventDefault();
                            if (!i) {
                                var s = e.ui.keyCode;
                                switch (n.keyCode) {
                                    case s.PAGE_UP:
                                        this._move("previousPage", n);
                                        break;
                                    case s.PAGE_DOWN:
                                        this._move("nextPage", n);
                                        break;
                                    case s.UP:
                                        this._keyEvent("previous", n);
                                        break;
                                    case s.DOWN:
                                        this._keyEvent("next", n)
                                }
                            }
                        },
                        input: function (e) {
                            return n ? (n = !1, void e.preventDefault()) : void this._searchTimeout(e)
                        },
                        focus: function () {
                            this.selectedItem = null,
                                this.previous = this._value()
                        },
                        blur: function (e) {
                            return this.cancelBlur ? void delete this.cancelBlur : (clearTimeout(this.searching), this.close(e), this._change(e), void 0)
                        }
                    }),
                    this._initSource(),
                    this.menu = e("<ul>").addClass("ui-autocomplete ui-front").appendTo(this._appendTo()).menu({
                        input: e(),
                        role: null
                    }).hide().data("ui-menu"),
                    this._on(this.menu.element, {
                        mousedown: function (t) {
                            t.preventDefault(),
                                this.cancelBlur = !0,
                                this._delay(function () {
                                    delete this.cancelBlur
                                });
                            var i = this.menu.element[0];
                            e(t.target).closest(".ui-menu-item").length || this._delay(function () {
                                var t = this;
                                this.document.one("mousedown", function (n) {
                                    n.target !== t.element[0] && n.target !== i && !e.contains(i, n.target) && t.close()
                                })
                            })
                        },
                        menufocus: function (t, i) {
                            if (this.isNewMenu && (this.isNewMenu = !1, t.originalEvent && /^mouse/.test(t.originalEvent.type))) return this.menu.blur(),
                                void this.document.one("mousemove", function () {
                                    e(t.target).trigger(t.originalEvent)
                                });
                            var n = i.item.data("ui-autocomplete-item");
                            !1 !== this._trigger("focus", t, {
                                item: n
                            }) ? t.originalEvent && /^key/.test(t.originalEvent.type) && this._value(n.value) : this.liveRegion.text(n.value)
                        },
                        menuselect: function (e, t) {
                            var i = t.item.data("ui-autocomplete-item"),
                                n = this.previous;
                            this.element[0] !== this.document[0].activeElement && (this.element.focus(), this.previous = n, this._delay(function () {
                                this.previous = n,
                                    this.selectedItem = i
                            })),
                            !1 !== this._trigger("select", e, {
                                item: i
                            }) && this._value(i.value),
                                this.term = this._value(),
                                this.close(e),
                                this.selectedItem = i
                        }
                    }),
                    this.liveRegion = e("<span>", {
                        role: "status",
                        "aria-live": "polite"
                    }).addClass("ui-helper-hidden-accessible").insertAfter(this.element),
                    this._on(this.window, {
                        beforeunload: function () {
                            this.element.removeAttr("autocomplete")
                        }
                    })
            },
            _destroy: function () {
                clearTimeout(this.searching),
                    this.element.removeClass("ui-autocomplete-input").removeAttr("autocomplete"),
                    this.menu.element.remove(),
                    this.liveRegion.remove()
            },
            _setOption: function (e, t) {
                this._super(e, t),
                "source" === e && this._initSource(),
                "appendTo" === e && this.menu.element.appendTo(this._appendTo()),
                "disabled" === e && t && this.xhr && this.xhr.abort()
            },
            _appendTo: function () {
                var t = this.options.appendTo;
                return t && (t = t.jquery || t.nodeType ? e(t) : this.document.find(t).eq(0)),
                t || (t = this.element.closest(".ui-front")),
                t.length || (t = this.document[0].body),
                    t
            },
            _initSource: function () {
                var t, i, n = this;
                e.isArray(this.options.source) ? (t = this.options.source, this.source = function (i, n) {
                    n(e.ui.autocomplete.filter(t, i.term))
                }) : "string" == typeof this.options.source ? (i = this.options.source, this.source = function (t, s) {
                    n.xhr && n.xhr.abort(),
                        n.xhr = e.ajax({
                            url: i,
                            data: t,
                            dataType: "json",
                            success: function (e) {
                                s(e)
                            },
                            error: function () {
                                s([])
                            }
                        })
                }) : this.source = this.options.source
            },
            _searchTimeout: function (e) {
                clearTimeout(this.searching),
                    this.searching = this._delay(function () {
                        this.term !== this._value() && (this.selectedItem = null, this.search(null, e))
                    }, this.options.delay)
            },
            search: function (e, t) {
                return e = null != e ? e : this._value(),
                    this.term = this._value(),
                    e.length < this.options.minLength ? this.close(t) : this._trigger("search", t) !== !1 ? this._search(e) : void 0
            },
            _search: function (e) {
                this.pending++,
                    this.element.addClass("ui-autocomplete-loading"),
                    this.cancelSearch = !1,
                    this.source({
                        term: e
                    }, this._response())
            },
            _response: function () {
                var e = this,
                    t = ++i;
                return function (n) {
                    t === i && e.__response(n),
                        e.pending--,
                    e.pending || e.element.removeClass("ui-autocomplete-loading")
                }
            },
            __response: function (e) {
                e && (e = this._normalize(e)),
                    this._trigger("response", null, {
                        content: e
                    }),
                    !this.options.disabled && e && e.length && !this.cancelSearch ? (this._suggest(e), this._trigger("open")) : this._close()
            },
            close: function (e) {
                this.cancelSearch = !0,
                    this._close(e)
            },
            _close: function (e) {
                this.menu.element.is(":visible") && (this.menu.element.hide(), this.menu.blur(), this.isNewMenu = !0, this._trigger("close", e))
            },
            _change: function (e) {
                this.previous !== this._value() && this._trigger("change", e, {
                    item: this.selectedItem
                })
            },
            _normalize: function (t) {
                return t.length && t[0].label && t[0].value ? t : e.map(t, function (t) {
                    return "string" == typeof t ? {
                        label: t,
                        value: t
                    } : e.extend({
                        label: t.label || t.value,
                        value: t.value || t.label
                    }, t)
                })
            },
            _suggest: function (t) {
                var i = this.menu.element.empty();
                this._renderMenu(i, t),
                    this.menu.refresh(),
                    i.show(),
                    this._resizeMenu(),
                    i.position(e.extend({
                        of: this.element
                    }, this.options.position)),
                this.options.autoFocus && this.menu.next()
            },
            _resizeMenu: function () {
                var e = this.menu.element;
                e.outerWidth(Math.max(e.width("").outerWidth() + 1, this.element.outerWidth()))
            },
            _renderMenu: function (t, i) {
                var n = this;
                e.each(i, function (e, i) {
                    n._renderItemData(t, i)
                })
            },
            _renderItemData: function (e, t) {
                return this._renderItem(e, t).data("ui-autocomplete-item", t)
            },
            _renderItem: function (t, i) {
                return e("<li>").append(e("<a>").text(i.label)).appendTo(t)
            },
            _move: function (e, t) {
                return this.menu.element.is(":visible") ? this.menu.isFirstItem() && /^previous/.test(e) || this.menu.isLastItem() && /^next/.test(e) ? (this._value(this.term), void this.menu.blur()) : void this.menu[e](t) : void this.search(null, t)
            },
            widget: function () {
                return this.menu.element
            },
            _value: function () {
                return this.valueMethod.apply(this.element, arguments)
            },
            _keyEvent: function (e, t) {
                this.isMultiLine && !this.menu.element.is(":visible") || (this._move(e, t), t.preventDefault())
            }
        }),
            e.extend(e.ui.autocomplete, {
                escapeRegex: function (e) {
                    return e.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&")
                },
                filter: function (t, i) {
                    var n = new RegExp(e.ui.autocomplete.escapeRegex(i), "i");
                    return e.grep(t, function (e) {
                        return n.test(e.label || e.value || e)
                    })
                }
            }),
            e.widget("ui.autocomplete", e.ui.autocomplete, {
                options: {
                    messages: {
                        noResults: "No search results.",
                        results: function (e) {
                            return e + (e > 1 ? " results are" : " result is") + " available, use up and down arrow keys to navigate."
                        }
                    }
                },
                __response: function (e) {
                    var t;
                    this._superApply(arguments),
                    this.options.disabled || this.cancelSearch || (t = e && e.length ? this.options.messages.results(e.length) : this.options.messages.noResults, this.liveRegion.text(t))
                }
            })
    }(jQuery),


    function (e, t) {
        var i, n, s, a, o = "ui-button ui-widget ui-state-default ui-corner-all",
            r = "ui-state-hover ui-state-active ",
            l = "ui-button-icons-only ui-button-icon-only ui-button-text-icons ui-button-text-icon-primary ui-button-text-icon-secondary ui-button-text-only",
            c = function () {
                var t = e(this).find(":ui-button");
                setTimeout(function () {
                    t.button("refresh")
                }, 1)
            },
            d = function (t) {
                var i = t.name,
                    n = t.form,
                    s = e([]);
                return i && (i = i.replace(/'/g, "\\'"), s = n ? e(n).find("[name='" + i + "']") : e("[name='" + i + "']", t.ownerDocument).filter(function () {
                    return !this.form
                })),
                    s
            };
        e.widget("ui.button", {
            version: "1.10.1",
            defaultElement: "<button>",
            options: {
                disabled: null,
                text: !0,
                label: null,
                icons: {
                    primary: null,
                    secondary: null
                }
            },
            _create: function () {
                this.element.closest("form").unbind("reset" + this.eventNamespace).bind("reset" + this.eventNamespace, c),
                    "boolean" != typeof this.options.disabled ? this.options.disabled = !!this.element.prop("disabled") : this.element.prop("disabled", this.options.disabled),
                    this._determineButtonType(),
                    this.hasTitle = !!this.buttonElement.attr("title");
                var t = this,
                    r = this.options,
                    l = "checkbox" === this.type || "radio" === this.type,
                    h = l ? "" : "ui-state-active",
                    u = "ui-state-focus";
                null === r.label && (r.label = "input" === this.type ? this.buttonElement.val() : this.buttonElement.html()),
                    this._hoverable(this.buttonElement),
                    this.buttonElement.addClass(o).attr("role", "button").bind("mouseenter" + this.eventNamespace, function () {
                        r.disabled || this === i && e(this).addClass("ui-state-active")
                    }).bind("mouseleave" + this.eventNamespace, function () {
                        r.disabled || e(this).removeClass(h)
                    }).bind("click" + this.eventNamespace, function (e) {
                        r.disabled && (e.preventDefault(), e.stopImmediatePropagation())
                    }),
                    this.element.bind("focus" + this.eventNamespace, function () {
                        t.buttonElement.addClass(u)
                    }).bind("blur" + this.eventNamespace, function () {
                        t.buttonElement.removeClass(u)
                    }),
                l && (this.element.bind("change" + this.eventNamespace, function () {
                    a || t.refresh()
                }), this.buttonElement.bind("mousedown" + this.eventNamespace, function (e) {
                    r.disabled || (a = !1, n = e.pageX, s = e.pageY)
                }).bind("mouseup" + this.eventNamespace, function (e) {
                    r.disabled || n === e.pageX && s === e.pageY || (a = !0)
                })),
                    "checkbox" === this.type ? this.buttonElement.bind("click" + this.eventNamespace, function () {
                        if (r.disabled || a) return !1
                    }) : "radio" === this.type ? this.buttonElement.bind("click" + this.eventNamespace, function () {
                        if (r.disabled || a) return !1;
                        e(this).addClass("ui-state-active"),
                            t.buttonElement.attr("aria-pressed", "true");
                        var i = t.element[0];
                        d(i).not(i).map(function () {
                            return e(this).button("widget")[0]
                        }).removeClass("ui-state-active").attr("aria-pressed", "false")
                    }) : (this.buttonElement.bind("mousedown" + this.eventNamespace, function () {
                        return !r.disabled && (e(this).addClass("ui-state-active"), i = this, t.document.one("mouseup", function () {
                                i = null
                            }), void 0)
                    }).bind("mouseup" + this.eventNamespace, function () {
                        return !r.disabled && void e(this).removeClass("ui-state-active")
                    }).bind("keydown" + this.eventNamespace, function (t) {
                        return !r.disabled && void((t.keyCode === e.ui.keyCode.SPACE || t.keyCode === e.ui.keyCode.ENTER) && e(this).addClass("ui-state-active"))
                    }).bind("keyup" + this.eventNamespace + " blur" + this.eventNamespace, function () {
                        e(this).removeClass("ui-state-active")
                    }), this.buttonElement.is("a") && this.buttonElement.keyup(function (t) {
                        t.keyCode === e.ui.keyCode.SPACE && e(this).click()
                    })),
                    this._setOption("disabled", r.disabled),
                    this._resetButton()
            },
            _determineButtonType: function () {
                var e, t, i;
                this.element.is("[type=checkbox]") ? this.type = "checkbox" : this.element.is("[type=radio]") ? this.type = "radio" : this.element.is("input") ? this.type = "input" : this.type = "button",
                    "checkbox" === this.type || "radio" === this.type ? (e = this.element.parents().last(), t = "label[for='" + this.element.attr("id") + "']", this.buttonElement = e.find(t), this.buttonElement.length || (e = e.length ? e.siblings() : this.element.siblings(), this.buttonElement = e.filter(t), this.buttonElement.length || (this.buttonElement = e.find(t))), this.element.addClass("ui-helper-hidden-accessible"), i = this.element.is(":checked"), i && this.buttonElement.addClass("ui-state-active"), this.buttonElement.prop("aria-pressed", i)) : this.buttonElement = this.element
            },
            widget: function () {
                return this.buttonElement
            },
            _destroy: function () {
                this.element.removeClass("ui-helper-hidden-accessible"),
                    this.buttonElement.removeClass(o + " " + r + " " + l).removeAttr("role").removeAttr("aria-pressed").html(this.buttonElement.find(".ui-button-text").html()),
                this.hasTitle || this.buttonElement.removeAttr("title")
            },
            _setOption: function (e, t) {
                return this._super(e, t),
                    "disabled" === e ? void(t ? this.element.prop("disabled", !0) : this.element.prop("disabled", !1)) : void this._resetButton()
            },
            refresh: function () {
                var t = this.element.is("input, button") ? this.element.is(":disabled") : this.element.hasClass("ui-button-disabled");
                t !== this.options.disabled && this._setOption("disabled", t),
                    "radio" === this.type ? d(this.element[0]).each(function () {
                        e(this).is(":checked") ? e(this).button("widget").addClass("ui-state-active").attr("aria-pressed", "true") : e(this).button("widget").removeClass("ui-state-active").attr("aria-pressed", "false")
                    }) : "checkbox" === this.type && (this.element.is(":checked") ? this.buttonElement.addClass("ui-state-active").attr("aria-pressed", "true") : this.buttonElement.removeClass("ui-state-active").attr("aria-pressed", "false"))
            },
            _resetButton: function () {
                if ("input" === this.type) return void(this.options.label && this.element.val(this.options.label));
                var t = this.buttonElement.removeClass(l),
                    i = e("<span></span>", this.document[0]).addClass("ui-button-text").html(this.options.label).appendTo(t.empty()).text(),
                    n = this.options.icons,
                    s = n.primary && n.secondary,
                    a = [];
                n.primary || n.secondary ? (this.options.text && a.push("ui-button-text-icon" + (s ? "s" : n.primary ? "-primary" : "-secondary")), n.primary && t.prepend("<span class='ui-button-icon-primary ui-icon " + n.primary + "'></span>"), n.secondary && t.append("<span class='ui-button-icon-secondary ui-icon " + n.secondary + "'></span>"), this.options.text || (a.push(s ? "ui-button-icons-only" : "ui-button-icon-only"), this.hasTitle || t.attr("title", e.trim(i)))) : a.push("ui-button-text-only"),
                    t.addClass(a.join(" "))
            }
        }),
            e.widget("ui.buttonset", {
                version: "1.10.1",
                options: {
                    items: "button, input[type=button], input[type=submit], input[type=reset], input[type=checkbox], input[type=radio], a, :data(ui-button)"
                },
                _create: function () {
                    this.element.addClass("ui-buttonset")
                },
                _init: function () {
                    this.refresh()
                },
                _setOption: function (e, t) {
                    "disabled" === e && this.buttons.button("option", e, t),
                        this._super(e, t)
                },
                refresh: function () {
                    var t = "rtl" === this.element.css("direction");
                    this.buttons = this.element.find(this.options.items).filter(":ui-button").button("refresh").end().not(":ui-button").button().end().map(function () {
                        return e(this).button("widget")[0]
                    }).removeClass("ui-corner-all ui-corner-left ui-corner-right").filter(":first").addClass(t ? "ui-corner-right" : "ui-corner-left").end().filter(":last").addClass(t ? "ui-corner-left" : "ui-corner-right").end().end()
                },
                _destroy: function () {
                    this.element.removeClass("ui-buttonset"),
                        this.buttons.map(function () {
                            return e(this).button("widget")[0]
                        }).removeClass("ui-corner-left ui-corner-right").end().button("destroy")
                }
            })
    }(jQuery),


    function (e, t) {
        function i() {
            this._curInst = null,
                this._keyEvent = !1,
                this._disabledInputs = [],
                this._datepickerShowing = !1,
                this._inDialog = !1,
                this._mainDivId = "ui-datepicker-div",
                this._inlineClass = "ui-datepicker-inline",
                this._appendClass = "ui-datepicker-append",
                this._triggerClass = "ui-datepicker-trigger",
                this._dialogClass = "ui-datepicker-dialog",
                this._disableClass = "ui-datepicker-disabled",
                this._unselectableClass = "ui-datepicker-unselectable",
                this._currentClass = "ui-datepicker-current-day",
                this._dayOverClass = "ui-datepicker-days-cell-over",
                this.regional = [],
                this.regional[""] = {
                    closeText: "Done",
                    prevText: "Prev",
                    nextText: "Next",
                    currentText: "Today",
                    monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                    monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                    dayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
                    dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                    dayNamesMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
                    weekHeader: "Wk",
                    dateFormat: "mm/dd/yy",
                    firstDay: 0,
                    isRTL: !1,
                    showMonthAfterYear: !1,
                    yearSuffix: ""
                },
                this._defaults = {
                    showOn: "focus",
                    showAnim: "fadeIn",
                    showOptions: {},
                    defaultDate: null,
                    appendText: "",
                    buttonText: "...",
                    buttonImage: "",
                    buttonImageOnly: !1,
                    hideIfNoPrevNext: !1,
                    navigationAsDateFormat: !1,
                    gotoCurrent: !1,
                    changeMonth: !1,
                    changeYear: !1,
                    yearRange: "c-10:c+10",
                    showOtherMonths: !1,
                    selectOtherMonths: !1,
                    showWeek: !1,
                    calculateWeek: this.iso8601Week,
                    shortYearCutoff: "+10",
                    minDate: null,
                    maxDate: null,
                    duration: "fast",
                    beforeShowDay: null,
                    beforeShow: null,
                    onSelect: null,
                    onChangeMonthYear: null,
                    onClose: null,
                    numberOfMonths: 1,
                    showCurrentAtPos: 0,
                    stepMonths: 1,
                    stepBigMonths: 12,
                    altField: "",
                    altFormat: "",
                    constrainInput: !0,
                    showButtonPanel: !1,
                    autoSize: !1,
                    disabled: !1
                },
                e.extend(this._defaults, this.regional[""]),
                this.dpDiv = n(e("<div id='" + this._mainDivId + "' class='ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>"))
        }

        function n(t) {
            var i = "button, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar td a";
            return t.delegate(i, "mouseout", function () {
                e(this).removeClass("ui-state-hover"),
                this.className.indexOf("ui-datepicker-prev") !== -1 && e(this).removeClass("ui-datepicker-prev-hover"),
                this.className.indexOf("ui-datepicker-next") !== -1 && e(this).removeClass("ui-datepicker-next-hover")
            }).delegate(i, "mouseover", function () {
                e.datepicker._isDisabledDatepicker(a.inline ? t.parent()[0] : a.input[0]) || (e(this).parents(".ui-datepicker-calendar").find("a").removeClass("ui-state-hover"), e(this).addClass("ui-state-hover"), this.className.indexOf("ui-datepicker-prev") !== -1 && e(this).addClass("ui-datepicker-prev-hover"), this.className.indexOf("ui-datepicker-next") !== -1 && e(this).addClass("ui-datepicker-next-hover"))
            })
        }

        function s(t, i) {
            e.extend(t, i);
            for (var n in i) null == i[n] && (t[n] = i[n]);
            return t
        }

        e.extend(e.ui, {
            datepicker: {
                version: "1.10.1"
            }
        });
        var a, o = "datepicker",
            r = (new Date).getTime();
        e.extend(i.prototype, {
            markerClassName: "hasDatepicker",
            maxRows: 4,
            _widgetDatepicker: function () {
                return this.dpDiv
            },
            setDefaults: function (e) {
                return s(this._defaults, e || {}),
                    this
            },
            _attachDatepicker: function (t, i) {
                var n, s, a;
                n = t.nodeName.toLowerCase(),
                    s = "div" === n || "span" === n,
                t.id || (this.uuid += 1, t.id = "dp" + this.uuid),
                    a = this._newInst(e(t), s),
                    a.settings = e.extend({}, i || {}),
                    "input" === n ? this._connectDatepicker(t, a) : s && this._inlineDatepicker(t, a)
            },
            _newInst: function (t, i) {
                var s = t[0].id.replace(/([^A-Za-z0-9_\-])/g, "\\\\$1");
                return {
                    id: s,
                    input: t,
                    selectedDay: 0,
                    selectedMonth: 0,
                    selectedYear: 0,
                    drawMonth: 0,
                    drawYear: 0,
                    inline: i,
                    dpDiv: i ? n(e("<div class='" + this._inlineClass + " ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>")) : this.dpDiv
                }
            },
            _connectDatepicker: function (t, i) {
                var n = e(t);
                i.append = e([]),
                    i.trigger = e([]),
                n.hasClass(this.markerClassName) || (this._attachments(n, i), n.addClass(this.markerClassName).keydown(this._doKeyDown).keypress(this._doKeyPress).keyup(this._doKeyUp), this._autoSize(i), e.data(t, o, i), i.settings.disabled && this._disableDatepicker(t))
            },
            _attachments: function (t, i) {
                var n, s, a, o = this._get(i, "appendText"),
                    r = this._get(i, "isRTL");
                i.append && i.append.remove(),
                o && (i.append = e("<span class='" + this._appendClass + "'>" + o + "</span>"), t[r ? "before" : "after"](i.append)),
                    t.unbind("focus", this._showDatepicker),
                i.trigger && i.trigger.remove(),
                    n = this._get(i, "showOn"),
                ("focus" === n || "both" === n) && t.focus(this._showDatepicker),
                "button" !== n && "both" !== n || (s = this._get(i, "buttonText"), a = this._get(i, "buttonImage"), i.trigger = e(this._get(i, "buttonImageOnly") ? e("<img/>").addClass(this._triggerClass).attr({
                    src: a,
                    alt: s,
                    title: s
                }) : e("<button type='button'></button>").addClass(this._triggerClass).html(a ? e("<img/>").attr({
                    src: a,
                    alt: s,
                    title: s
                }) : s)), t[r ? "before" : "after"](i.trigger), i.trigger.click(function () {
                    return e.datepicker._datepickerShowing && e.datepicker._lastInput === t[0] ? e.datepicker._hideDatepicker() : e.datepicker._datepickerShowing && e.datepicker._lastInput !== t[0] ? (e.datepicker._hideDatepicker(), e.datepicker._showDatepicker(t[0])) : e.datepicker._showDatepicker(t[0]),
                        !1
                }))
            },
            _autoSize: function (e) {
                if (this._get(e, "autoSize") && !e.inline) {
                    var t, i, n, s, a = new Date(2009, 11, 20),
                        o = this._get(e, "dateFormat");
                    o.match(/[DM]/) && (t = function (e) {
                        for (i = 0, n = 0, s = 0; s < e.length; s++) e[s].length > i && (i = e[s].length, n = s);
                        return n
                    }, a.setMonth(t(this._get(e, o.match(/MM/) ? "monthNames" : "monthNamesShort"))), a.setDate(t(this._get(e, o.match(/DD/) ? "dayNames" : "dayNamesShort")) + 20 - a.getDay())),
                        e.input.attr("size", this._formatDate(e, a).length)
                }
            },
            _inlineDatepicker: function (t, i) {
                var n = e(t);
                n.hasClass(this.markerClassName) || (n.addClass(this.markerClassName).append(i.dpDiv), e.data(t, o, i), this._setDate(i, this._getDefaultDate(i), !0), this._updateDatepicker(i), this._updateAlternate(i), i.settings.disabled && this._disableDatepicker(t), i.dpDiv.css("display", "block"))
            },
            _dialogDatepicker: function (t, i, n, a, r) {
                var l, c, d, h, u, p = this._dialogInst;
                return p || (this.uuid += 1, l = "dp" + this.uuid, this._dialogInput = e("<input type='text' id='" + l + "' style='position: absolute; top: -100px; width: 0px;'/>"), this._dialogInput.keydown(this._doKeyDown), e("body").append(this._dialogInput), p = this._dialogInst = this._newInst(this._dialogInput, !1), p.settings = {}, e.data(this._dialogInput[0], o, p)),
                    s(p.settings, a || {}),
                    i = i && i.constructor === Date ? this._formatDate(p, i) : i,
                    this._dialogInput.val(i),
                    this._pos = r ? r.length ? r : [r.pageX, r.pageY] : null,
                this._pos || (c = document.documentElement.clientWidth, d = document.documentElement.clientHeight, h = document.documentElement.scrollLeft || document.body.scrollLeft, u = document.documentElement.scrollTop || document.body.scrollTop, this._pos = [c / 2 - 100 + h, d / 2 - 150 + u]),
                    this._dialogInput.css("left", this._pos[0] + 20 + "px").css("top", this._pos[1] + "px"),
                    p.settings.onSelect = n,
                    this._inDialog = !0,
                    this.dpDiv.addClass(this._dialogClass),
                    this._showDatepicker(this._dialogInput[0]),
                e.blockUI && e.blockUI(this.dpDiv),
                    e.data(this._dialogInput[0], o, p),
                    this
            },
            _destroyDatepicker: function (t) {
                var i, n = e(t),
                    s = e.data(t, o);
                n.hasClass(this.markerClassName) && (i = t.nodeName.toLowerCase(), e.removeData(t, o), "input" === i ? (s.append.remove(), s.trigger.remove(), n.removeClass(this.markerClassName).unbind("focus", this._showDatepicker).unbind("keydown", this._doKeyDown).unbind("keypress", this._doKeyPress).unbind("keyup", this._doKeyUp)) : ("div" === i || "span" === i) && n.removeClass(this.markerClassName).empty())
            },
            _enableDatepicker: function (t) {
                var i, n, s = e(t),
                    a = e.data(t, o);
                s.hasClass(this.markerClassName) && (i = t.nodeName.toLowerCase(), "input" === i ? (t.disabled = !1, a.trigger.filter("button").each(function () {
                    this.disabled = !1
                }).end().filter("img").css({
                    opacity: "1.0",
                    cursor: ""
                })) : "div" !== i && "span" !== i || (n = s.children("." + this._inlineClass), n.children().removeClass("ui-state-disabled"), n.find("select.ui-datepicker-month, select.ui-datepicker-year").prop("disabled", !1)), this._disabledInputs = e.map(this._disabledInputs, function (e) {
                    return e === t ? null : e
                }))
            },
            _disableDatepicker: function (t) {
                var i, n, s = e(t),
                    a = e.data(t, o);
                s.hasClass(this.markerClassName) && (i = t.nodeName.toLowerCase(), "input" === i ? (t.disabled = !0, a.trigger.filter("button").each(function () {
                    this.disabled = !0
                }).end().filter("img").css({
                    opacity: "0.5",
                    cursor: "default"
                })) : "div" !== i && "span" !== i || (n = s.children("." + this._inlineClass), n.children().addClass("ui-state-disabled"), n.find("select.ui-datepicker-month, select.ui-datepicker-year").prop("disabled", !0)), this._disabledInputs = e.map(this._disabledInputs, function (e) {
                    return e === t ? null : e
                }), this._disabledInputs[this._disabledInputs.length] = t)
            },
            _isDisabledDatepicker: function (e) {
                if (!e) return !1;
                for (var t = 0; t < this._disabledInputs.length; t++) if (this._disabledInputs[t] === e) return !0;
                return !1
            },
            _getInst: function (t) {
                try {
                    return e.data(t, o)
                } catch (i) {
                    throw "Missing instance data for this datepicker"
                }
            },
            _optionDatepicker: function (i, n, a) {
                var o, r, l, c, d = this._getInst(i);
                return 2 === arguments.length && "string" == typeof n ? "defaults" === n ? e.extend({}, e.datepicker._defaults) : d ? "all" === n ? e.extend({}, d.settings) : this._get(d, n) : null : (o = n || {}, "string" == typeof n && (o = {}, o[n] = a), d && (this._curInst === d && this._hideDatepicker(), r = this._getDateDatepicker(i, !0), l = this._getMinMaxDate(d, "min"), c = this._getMinMaxDate(d, "max"), s(d.settings, o), null !== l && o.dateFormat !== t && o.minDate === t && (d.settings.minDate = this._formatDate(d, l)), null !== c && o.dateFormat !== t && o.maxDate === t && (d.settings.maxDate = this._formatDate(d, c)), "disabled" in o && (o.disabled ? this._disableDatepicker(i) : this._enableDatepicker(i)), this._attachments(e(i), d), this._autoSize(d), this._setDate(d, r), this._updateAlternate(d), this._updateDatepicker(d)), void 0)
            },
            _changeDatepicker: function (e, t, i) {
                this._optionDatepicker(e, t, i)
            },
            _refreshDatepicker: function (e) {
                var t = this._getInst(e);
                t && this._updateDatepicker(t)
            },
            _setDateDatepicker: function (e, t) {
                var i = this._getInst(e);
                i && (this._setDate(i, t), this._updateDatepicker(i), this._updateAlternate(i))
            },
            _getDateDatepicker: function (e, t) {
                var i = this._getInst(e);
                return i && !i.inline && this._setDateFromField(i, t),
                    i ? this._getDate(i) : null
            },
            _doKeyDown: function (t) {
                var i, n, s, a = e.datepicker._getInst(t.target),
                    o = !0,
                    r = a.dpDiv.is(".ui-datepicker-rtl");
                if (a._keyEvent = !0, e.datepicker._datepickerShowing) switch (t.keyCode) {
                    case 9:
                        e.datepicker._hideDatepicker(),
                            o = !1;
                        break;
                    case 13:
                        return s = e("td." + e.datepicker._dayOverClass + ":not(." + e.datepicker._currentClass + ")", a.dpDiv),
                        s[0] && e.datepicker._selectDay(t.target, a.selectedMonth, a.selectedYear, s[0]),
                            i = e.datepicker._get(a, "onSelect"),
                            i ? (n = e.datepicker._formatDate(a), i.apply(a.input ? a.input[0] : null, [n, a])) : e.datepicker._hideDatepicker(),
                            !1;
                    case 27:
                        e.datepicker._hideDatepicker();
                        break;
                    case 33:
                        e.datepicker._adjustDate(t.target, t.ctrlKey ? -e.datepicker._get(a, "stepBigMonths") : -e.datepicker._get(a, "stepMonths"), "M");
                        break;
                    case 34:
                        e.datepicker._adjustDate(t.target, t.ctrlKey ? +e.datepicker._get(a, "stepBigMonths") : +e.datepicker._get(a, "stepMonths"), "M");
                        break;
                    case 35:
                        (t.ctrlKey || t.metaKey) && e.datepicker._clearDate(t.target),
                            o = t.ctrlKey || t.metaKey;
                        break;
                    case 36:
                        (t.ctrlKey || t.metaKey) && e.datepicker._gotoToday(t.target),
                            o = t.ctrlKey || t.metaKey;
                        break;
                    case 37:
                        (t.ctrlKey || t.metaKey) && e.datepicker._adjustDate(t.target, r ? 1 : -1, "D"),
                            o = t.ctrlKey || t.metaKey,
                        t.originalEvent.altKey && e.datepicker._adjustDate(t.target, t.ctrlKey ? -e.datepicker._get(a, "stepBigMonths") : -e.datepicker._get(a, "stepMonths"), "M");
                        break;
                    case 38:
                        (t.ctrlKey || t.metaKey) && e.datepicker._adjustDate(t.target, -7, "D"),
                            o = t.ctrlKey || t.metaKey;
                        break;
                    case 39:
                        (t.ctrlKey || t.metaKey) && e.datepicker._adjustDate(t.target, r ? -1 : 1, "D"),
                            o = t.ctrlKey || t.metaKey,
                        t.originalEvent.altKey && e.datepicker._adjustDate(t.target, t.ctrlKey ? +e.datepicker._get(a, "stepBigMonths") : +e.datepicker._get(a, "stepMonths"), "M");
                        break;
                    case 40:
                        (t.ctrlKey || t.metaKey) && e.datepicker._adjustDate(t.target, 7, "D"),
                            o = t.ctrlKey || t.metaKey;
                        break;
                    default:
                        o = !1
                } else 36 === t.keyCode && t.ctrlKey ? e.datepicker._showDatepicker(this) : o = !1;
                o && (t.preventDefault(), t.stopPropagation())
            },
            _doKeyPress: function (t) {
                var i, n, s = e.datepicker._getInst(t.target);
                if (e.datepicker._get(s, "constrainInput")) return i = e.datepicker._possibleChars(e.datepicker._get(s, "dateFormat")),
                    n = String.fromCharCode(null == t.charCode ? t.keyCode : t.charCode),
                t.ctrlKey || t.metaKey || n < " " || !i || i.indexOf(n) > -1
            },
            _doKeyUp: function (t) {
                var i, n = e.datepicker._getInst(t.target);
                if (n.input.val() !== n.lastVal) try {
                    i = e.datepicker.parseDate(e.datepicker._get(n, "dateFormat"), n.input ? n.input.val() : null, e.datepicker._getFormatConfig(n)),
                    i && (e.datepicker._setDateFromField(n), e.datepicker._updateAlternate(n), e.datepicker._updateDatepicker(n))
                } catch (s) {
                }
                return !0
            },
            _showDatepicker: function (t) {
                if (t = t.target || t, "input" !== t.nodeName.toLowerCase() && (t = e("input", t.parentNode)[0]), !e.datepicker._isDisabledDatepicker(t) && e.datepicker._lastInput !== t) {
                    var i, n, a, o, r, l, c;
                    i = e.datepicker._getInst(t),
                    e.datepicker._curInst && e.datepicker._curInst !== i && (e.datepicker._curInst.dpDiv.stop(!0, !0), i && e.datepicker._datepickerShowing && e.datepicker._hideDatepicker(e.datepicker._curInst.input[0])),
                        n = e.datepicker._get(i, "beforeShow"),
                        a = n ? n.apply(t, [t, i]) : {},
                    a !== !1 && (s(i.settings, a), i.lastVal = null, e.datepicker._lastInput = t, e.datepicker._setDateFromField(i), e.datepicker._inDialog && (t.value = ""), e.datepicker._pos || (e.datepicker._pos = e.datepicker._findPos(t), e.datepicker._pos[1] += t.offsetHeight), o = !1, e(t).parents().each(function () {
                        return o |= "fixed" === e(this).css("position"),
                            !o
                    }), r = {
                        left: e.datepicker._pos[0],
                        top: e.datepicker._pos[1]
                    }, e.datepicker._pos = null, i.dpDiv.empty(), i.dpDiv.css({
                        position: "absolute",
                        display: "block",
                        top: "-1000px"
                    }), e.datepicker._updateDatepicker(i), r = e.datepicker._checkOffset(i, r, o), i.dpDiv.css({
                        position: e.datepicker._inDialog && e.blockUI ? "static" : o ? "fixed" : "absolute",
                        display: "none",
                        left: r.left + "px",
                        top: r.top + "px"
                    }), i.inline || (l = e.datepicker._get(i, "showAnim"), c = e.datepicker._get(i, "duration"), i.dpDiv.zIndex(e(t).zIndex() + 1), e.datepicker._datepickerShowing = !0, e.effects && e.effects.effect[l] ? i.dpDiv.show(l, e.datepicker._get(i, "showOptions"), c) : i.dpDiv[l || "show"](l ? c : null), i.input.is(":visible") && !i.input.is(":disabled") && i.input.focus(), e.datepicker._curInst = i))
                }
            },
            _updateDatepicker: function (t) {
                this.maxRows = 4,
                    a = t,
                    t.dpDiv.empty().append(this._generateHTML(t)),
                    this._attachHandlers(t),
                    t.dpDiv.find("." + this._dayOverClass + " a").mouseover();
                var i, n = this._getNumberOfMonths(t),
                    s = n[1],
                    o = 17;
                t.dpDiv.removeClass("ui-datepicker-multi-2 ui-datepicker-multi-3 ui-datepicker-multi-4").width(""),
                s > 1 && t.dpDiv.addClass("ui-datepicker-multi-" + s).css("width", o * s + "em"),
                    t.dpDiv[(1 !== n[0] || 1 !== n[1] ? "add" : "remove") + "Class"]("ui-datepicker-multi"),
                    t.dpDiv[(this._get(t, "isRTL") ? "add" : "remove") + "Class"]("ui-datepicker-rtl"),
                t === e.datepicker._curInst && e.datepicker._datepickerShowing && t.input && t.input.is(":visible") && !t.input.is(":disabled") && t.input[0] !== document.activeElement && t.input.focus(),
                t.yearshtml && (i = t.yearshtml, setTimeout(function () {
                    i === t.yearshtml && t.yearshtml && t.dpDiv.find("select.ui-datepicker-year:first").replaceWith(t.yearshtml),
                        i = t.yearshtml = null
                }, 0))
            },
            _getBorders: function (e) {
                var t = function (e) {
                    return {
                            thin: 1,
                            medium: 2,
                            thick: 3
                        }[e] || e
                };
                return [parseFloat(t(e.css("border-left-width"))), parseFloat(t(e.css("border-top-width")))]
            },
            _checkOffset: function (t, i, n) {
                var s = t.dpDiv.outerWidth(),
                    a = t.dpDiv.outerHeight(),
                    o = t.input ? t.input.outerWidth() : 0,
                    r = t.input ? t.input.outerHeight() : 0,
                    l = document.documentElement.clientWidth + (n ? 0 : e(document).scrollLeft()),
                    c = document.documentElement.clientHeight + (n ? 0 : e(document).scrollTop());
                return i.left -= this._get(t, "isRTL") ? s - o : 0,
                    i.left -= n && i.left === t.input.offset().left ? e(document).scrollLeft() : 0,
                    i.top -= n && i.top === t.input.offset().top + r ? e(document).scrollTop() : 0,
                    i.left -= Math.min(i.left, i.left + s > l && l > s ? Math.abs(i.left + s - l) : 0),
                    i.top -= Math.min(i.top, i.top + a > c && c > a ? Math.abs(a + r) : 0),
                    i
            },
            _findPos: function (t) {
                for (var i, n = this._getInst(t), s = this._get(n, "isRTL"); t && ("hidden" === t.type || 1 !== t.nodeType || e.expr.filters.hidden(t));) t = t[s ? "previousSibling" : "nextSibling"];
                return i = e(t).offset(),
                    [i.left, i.top]
            },
            _hideDatepicker: function (t) {
                var i, n, s, a, r = this._curInst;
                !r || t && r !== e.data(t, o) || this._datepickerShowing && (i = this._get(r, "showAnim"), n = this._get(r, "duration"), s = function () {
                    e.datepicker._tidyDialog(r)
                }, e.effects && (e.effects.effect[i] || e.effects[i]) ? r.dpDiv.hide(i, e.datepicker._get(r, "showOptions"), n, s) : r.dpDiv["slideDown" === i ? "slideUp" : "fadeIn" === i ? "fadeOut" : "hide"](i ? n : null, s), i || s(), this._datepickerShowing = !1, a = this._get(r, "onClose"), a && a.apply(r.input ? r.input[0] : null, [r.input ? r.input.val() : "", r]), this._lastInput = null, this._inDialog && (this._dialogInput.css({
                    position: "absolute",
                    left: "0",
                    top: "-100px"
                }), e.blockUI && (e.unblockUI(), e("body").append(this.dpDiv))), this._inDialog = !1)
            },
            _tidyDialog: function (e) {
                e.dpDiv.removeClass(this._dialogClass).unbind(".ui-datepicker-calendar")
            },
            _checkExternalClick: function (t) {
                if (e.datepicker._curInst) {
                    var i = e(t.target),
                        n = e.datepicker._getInst(i[0]);
                    (i[0].id !== e.datepicker._mainDivId && 0 === i.parents("#" + e.datepicker._mainDivId).length && !i.hasClass(e.datepicker.markerClassName) && !i.closest("." + e.datepicker._triggerClass).length && e.datepicker._datepickerShowing && (!e.datepicker._inDialog || !e.blockUI) || i.hasClass(e.datepicker.markerClassName) && e.datepicker._curInst !== n) && e.datepicker._hideDatepicker()
                }
            },
            _adjustDate: function (t, i, n) {
                var s = e(t),
                    a = this._getInst(s[0]);
                this._isDisabledDatepicker(s[0]) || (this._adjustInstDate(a, i + ("M" === n ? this._get(a, "showCurrentAtPos") : 0), n), this._updateDatepicker(a))
            },
            _gotoToday: function (t) {
                var i, n = e(t),
                    s = this._getInst(n[0]);
                this._get(s, "gotoCurrent") && s.currentDay ? (s.selectedDay = s.currentDay, s.drawMonth = s.selectedMonth = s.currentMonth, s.drawYear = s.selectedYear = s.currentYear) : (i = new Date, s.selectedDay = i.getDate(), s.drawMonth = s.selectedMonth = i.getMonth(), s.drawYear = s.selectedYear = i.getFullYear()),
                    this._notifyChange(s),
                    this._adjustDate(n)
            },
            _selectMonthYear: function (t, i, n) {
                var s = e(t),
                    a = this._getInst(s[0]);
                a["selected" + ("M" === n ? "Month" : "Year")] = a["draw" + ("M" === n ? "Month" : "Year")] = parseInt(i.options[i.selectedIndex].value, 10),
                    this._notifyChange(a),
                    this._adjustDate(s)
            },
            _selectDay: function (t, i, n, s) {
                var a, o = e(t);
                e(s).hasClass(this._unselectableClass) || this._isDisabledDatepicker(o[0]) || (a = this._getInst(o[0]), a.selectedDay = a.currentDay = e("a", s).html(), a.selectedMonth = a.currentMonth = i, a.selectedYear = a.currentYear = n, this._selectDate(t, this._formatDate(a, a.currentDay, a.currentMonth, a.currentYear)))
            },
            _clearDate: function (t) {
                var i = e(t);
                this._selectDate(i, "")
            },
            _selectDate: function (t, i) {
                var n, s = e(t),
                    a = this._getInst(s[0]);
                i = null != i ? i : this._formatDate(a),
                a.input && a.input.val(i),
                    this._updateAlternate(a),
                    n = this._get(a, "onSelect"),
                    n ? n.apply(a.input ? a.input[0] : null, [i, a]) : a.input && a.input.trigger("change"),
                    a.inline ? this._updateDatepicker(a) : (this._hideDatepicker(), this._lastInput = a.input[0], "object" != typeof a.input[0] && a.input.focus(), this._lastInput = null)
            },
            _updateAlternate: function (t) {
                var i, n, s, a = this._get(t, "altField");
                a && (i = this._get(t, "altFormat") || this._get(t, "dateFormat"), n = this._getDate(t), s = this.formatDate(i, n, this._getFormatConfig(t)), e(a).each(function () {
                    e(this).val(s)
                }))
            },
            noWeekends: function (e) {
                var t = e.getDay();
                return [t > 0 && t < 6, ""]
            },
            iso8601Week: function (e) {
                var t, i = new Date(e.getTime());
                return i.setDate(i.getDate() + 4 - (i.getDay() || 7)),
                    t = i.getTime(),
                    i.setMonth(0),
                    i.setDate(1),
                Math.floor(Math.round((t - i) / 864e5) / 7) + 1
            },
            parseDate: function (t, i, n) {
                if (null == t || null == i) throw "Invalid arguments";
                if (i = "object" == typeof i ? i.toString() : i + "", "" === i) return null;
                var s, a, o, r, l = 0,
                    c = (n ? n.shortYearCutoff : null) || this._defaults.shortYearCutoff,
                    d = "string" != typeof c ? c : (new Date).getFullYear() % 100 + parseInt(c, 10),
                    h = (n ? n.dayNamesShort : null) || this._defaults.dayNamesShort,
                    u = (n ? n.dayNames : null) || this._defaults.dayNames,
                    p = (n ? n.monthNamesShort : null) || this._defaults.monthNamesShort,
                    f = (n ? n.monthNames : null) || this._defaults.monthNames,
                    m = -1,
                    g = -1,
                    v = -1,
                    y = -1,
                    b = !1,
                    w = function (e) {
                        var i = s + 1 < t.length && t.charAt(s + 1) === e;
                        return i && s++,
                            i
                    },
                    _ = function (e) {
                        var t = w(e),
                            n = "@" === e ? 14 : "!" === e ? 20 : "y" === e && t ? 4 : "o" === e ? 3 : 2,
                            s = new RegExp("^\\d{1," + n + "}"),
                            a = i.substring(l).match(s);
                        if (!a) throw "Missing number at position " + l;
                        return l += a[0].length,
                            parseInt(a[0], 10)
                    },
                    x = function (t, n, s) {
                        var a = -1,
                            o = e.map(w(t) ? s : n, function (e, t) {
                                return [[t, e]]
                            }).sort(function (e, t) {
                                return -(e[1].length - t[1].length)
                            });
                        if (e.each(o, function (e, t) {
                                var n = t[1];
                                if (i.substr(l, n.length).toLowerCase() === n.toLowerCase()) return a = t[0],
                                    l += n.length,
                                    !1
                            }), a !== -1) return a + 1;
                        throw "Unknown name at position " + l
                    },
                    C = function () {
                        if (i.charAt(l) !== t.charAt(s)) throw "Unexpected literal at position " + l;
                        l++
                    };
                for (s = 0; s < t.length; s++) if (b)"'" !== t.charAt(s) || w("'") ? C() : b = !1;
                else switch (t.charAt(s)) {
                        case "d":
                            v = _("d");
                            break;
                        case "D":
                            x("D", h, u);
                            break;
                        case "o":
                            y = _("o");
                            break;
                        case "m":
                            g = _("m");
                            break;
                        case "M":
                            g = x("M", p, f);
                            break;
                        case "y":
                            m = _("y");
                            break;
                        case "@":
                            r = new Date(_("@")),
                                m = r.getFullYear(),
                                g = r.getMonth() + 1,
                                v = r.getDate();
                            break;
                        case "!":
                            r = new Date((_("!") - this._ticksTo1970) / 1e4),
                                m = r.getFullYear(),
                                g = r.getMonth() + 1,
                                v = r.getDate();
                            break;
                        case "'":
                            w("'") ? C() : b = !0;
                            break;
                        default:
                            C()
                    }
                if (l < i.length && (o = i.substr(l), !/^\s+/.test(o))) throw "Extra/unparsed characters found in date: " + o;
                if (m === -1 ? m = (new Date).getFullYear() : m < 100 && (m += (new Date).getFullYear() - (new Date).getFullYear() % 100 + (m <= d ? 0 : -100)), y > -1) for (g = 1, v = y; ;) {
                    if (a = this._getDaysInMonth(m, g - 1), v <= a) break;
                    g++,
                        v -= a
                }
                if (r = this._daylightSavingAdjust(new Date(m, g - 1, v)), r.getFullYear() !== m || r.getMonth() + 1 !== g || r.getDate() !== v) throw "Invalid date";
                return r
            },
            ATOM: "yy-mm-dd",
            COOKIE: "D, dd M yy",
            ISO_8601: "yy-mm-dd",
            RFC_822: "D, d M y",
            RFC_850: "DD, dd-M-y",
            RFC_1036: "D, d M y",
            RFC_1123: "D, d M yy",
            RFC_2822: "D, d M yy",
            RSS: "D, d M y",
            TICKS: "!",
            TIMESTAMP: "@",
            W3C: "yy-mm-dd",
            _ticksTo1970: 24 * (718685 + Math.floor(492.5) - Math.floor(19.7) + Math.floor(4.925)) * 60 * 60 * 1e7,
            formatDate: function (e, t, i) {
                if (!t) return "";
                var n, s = (i ? i.dayNamesShort : null) || this._defaults.dayNamesShort,
                    a = (i ? i.dayNames : null) || this._defaults.dayNames,
                    o = (i ? i.monthNamesShort : null) || this._defaults.monthNamesShort,
                    r = (i ? i.monthNames : null) || this._defaults.monthNames,
                    l = function (t) {
                        var i = n + 1 < e.length && e.charAt(n + 1) === t;
                        return i && n++,
                            i
                    },
                    c = function (e, t, i) {
                        var n = "" + t;
                        if (l(e)) for (; n.length < i;) n = "0" + n;
                        return n
                    },
                    d = function (e, t, i, n) {
                        return l(e) ? n[t] : i[t]
                    },
                    h = "",
                    u = !1;
                if (t) for (n = 0; n < e.length; n++) if (u)"'" !== e.charAt(n) || l("'") ? h += e.charAt(n) : u = !1;
                else switch (e.charAt(n)) {
                        case "d":
                            h += c("d", t.getDate(), 2);
                            break;
                        case "D":
                            h += d("D", t.getDay(), s, a);
                            break;
                        case "o":
                            h += c("o", Math.round((new Date(t.getFullYear(), t.getMonth(), t.getDate()).getTime() - new Date(t.getFullYear(), 0, 0).getTime()) / 864e5), 3);
                            break;
                        case "m":
                            h += c("m", t.getMonth() + 1, 2);
                            break;
                        case "M":
                            h += d("M", t.getMonth(), o, r);
                            break;
                        case "y":
                            h += l("y") ? t.getFullYear() : (t.getYear() % 100 < 10 ? "0" : "") + t.getYear() % 100;
                            break;
                        case "@":
                            h += t.getTime();
                            break;
                        case "!":
                            h += 1e4 * t.getTime() + this._ticksTo1970;
                            break;
                        case "'":
                            l("'") ? h += "'" : u = !0;
                            break;
                        default:
                            h += e.charAt(n)
                    }
                return h
            },
            _possibleChars: function (e) {
                var t, i = "",
                    n = !1,
                    s = function (i) {
                        var n = t + 1 < e.length && e.charAt(t + 1) === i;
                        return n && t++,
                            n
                    };
                for (t = 0; t < e.length; t++) if (n)"'" !== e.charAt(t) || s("'") ? i += e.charAt(t) : n = !1;
                else switch (e.charAt(t)) {
                        case "d":
                        case "m":
                        case "y":
                        case "@":
                            i += "0123456789";
                            break;
                        case "D":
                        case "M":
                            return null;
                        case "'":
                            s("'") ? i += "'" : n = !0;
                            break;
                        default:
                            i += e.charAt(t)
                    }
                return i
            },
            _get: function (e, i) {
                return e.settings[i] !== t ? e.settings[i] : this._defaults[i]
            },
            _setDateFromField: function (e, t) {
                if (e.input.val() !== e.lastVal) {
                    var i = this._get(e, "dateFormat"),
                        n = e.lastVal = e.input ? e.input.val() : null,
                        s = this._getDefaultDate(e),
                        a = s,
                        o = this._getFormatConfig(e);
                    try {
                        a = this.parseDate(i, n, o) || s
                    } catch (r) {
                        n = t ? "" : n
                    }
                    e.selectedDay = a.getDate(),
                        e.drawMonth = e.selectedMonth = a.getMonth(),
                        e.drawYear = e.selectedYear = a.getFullYear(),
                        e.currentDay = n ? a.getDate() : 0,
                        e.currentMonth = n ? a.getMonth() : 0,
                        e.currentYear = n ? a.getFullYear() : 0,
                        this._adjustInstDate(e)
                }
            },
            _getDefaultDate: function (e) {
                return this._restrictMinMax(e, this._determineDate(e, this._get(e, "defaultDate"), new Date))
            },
            _determineDate: function (t, i, n) {
                var s = function (e) {
                        var t = new Date;
                        return t.setDate(t.getDate() + e),
                            t
                    },
                    a = function (i) {
                        try {
                            return e.datepicker.parseDate(e.datepicker._get(t, "dateFormat"), i, e.datepicker._getFormatConfig(t))
                        } catch (n) {
                        }
                        for (var s = (i.toLowerCase().match(/^c/) ? e.datepicker._getDate(t) : null) || new Date, a = s.getFullYear(), o = s.getMonth(), r = s.getDate(), l = /([+\-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g, c = l.exec(i); c;) {
                            switch (c[2] || "d") {
                                case "d":
                                case "D":
                                    r += parseInt(c[1], 10);
                                    break;
                                case "w":
                                case "W":
                                    r += 7 * parseInt(c[1], 10);
                                    break;
                                case "m":
                                case "M":
                                    o += parseInt(c[1], 10),
                                        r = Math.min(r, e.datepicker._getDaysInMonth(a, o));
                                    break;
                                case "y":
                                case "Y":
                                    a += parseInt(c[1], 10),
                                        r = Math.min(r, e.datepicker._getDaysInMonth(a, o))
                            }
                            c = l.exec(i)
                        }
                        return new Date(a, o, r)
                    },
                    o = null == i || "" === i ? n : "string" == typeof i ? a(i) : "number" == typeof i ? isNaN(i) ? n : s(i) : new Date(i.getTime());
                return o = o && "Invalid Date" === o.toString() ? n : o,
                o && (o.setHours(0), o.setMinutes(0), o.setSeconds(0), o.setMilliseconds(0)),
                    this._daylightSavingAdjust(o)
            },
            _daylightSavingAdjust: function (e) {
                return e ? (e.setHours(e.getHours() > 12 ? e.getHours() + 2 : 0), e) : null
            },
            _setDate: function (e, t, i) {
                var n = !t,
                    s = e.selectedMonth,
                    a = e.selectedYear,
                    o = this._restrictMinMax(e, this._determineDate(e, t, new Date));
                e.selectedDay = e.currentDay = o.getDate(),
                    e.drawMonth = e.selectedMonth = e.currentMonth = o.getMonth(),
                    e.drawYear = e.selectedYear = e.currentYear = o.getFullYear(),
                (s !== e.selectedMonth || a !== e.selectedYear) && !i && this._notifyChange(e),
                    this._adjustInstDate(e),
                e.input && e.input.val(n ? "" : this._formatDate(e))
            },
            _getDate: function (e) {
                var t = !e.currentYear || e.input && "" === e.input.val() ? null : this._daylightSavingAdjust(new Date(e.currentYear, e.currentMonth, e.currentDay));
                return t
            },
            _attachHandlers: function (t) {
                var i = this._get(t, "stepMonths"),
                    n = "#" + t.id.replace(/\\\\/g, "\\");
                t.dpDiv.find("[data-handler]").map(function () {
                    var t = {
                        prev: function () {
                            window["DP_jQuery_" + r].datepicker._adjustDate(n, -i, "M")
                        },
                        next: function () {
                            window["DP_jQuery_" + r].datepicker._adjustDate(n, +i, "M")
                        },
                        hide: function () {
                            window["DP_jQuery_" + r].datepicker._hideDatepicker()
                        },
                        today: function () {
                            window["DP_jQuery_" + r].datepicker._gotoToday(n)
                        },
                        selectDay: function () {
                            return window["DP_jQuery_" + r].datepicker._selectDay(n, +this.getAttribute("data-month"), +this.getAttribute("data-year"), this),
                                !1
                        },
                        selectMonth: function () {
                            return window["DP_jQuery_" + r].datepicker._selectMonthYear(n, this, "M"),
                                !1
                        },
                        selectYear: function () {
                            return window["DP_jQuery_" + r].datepicker._selectMonthYear(n, this, "Y"),
                                !1
                        }
                    };
                    e(this).bind(this.getAttribute("data-event"), t[this.getAttribute("data-handler")])
                })
            },
            _generateHTML: function (e) {
                var t, i, n, s, a, o, r, l, c, d, h, u, p, f, m, g, v, y, b, w, _, x, C, k, T, S, D, E, I, P, M, j, N, A, O, z, $, H, L, W = new Date,
                    R = this._daylightSavingAdjust(new Date(W.getFullYear(), W.getMonth(), W.getDate())),
                    F = this._get(e, "isRTL"),
                    B = this._get(e, "showButtonPanel"),
                    q = this._get(e, "hideIfNoPrevNext"),
                    V = this._get(e, "navigationAsDateFormat"),
                    Y = this._getNumberOfMonths(e),
                    U = this._get(e, "showCurrentAtPos"),
                    G = this._get(e, "stepMonths"),
                    X = 1 !== Y[0] || 1 !== Y[1],
                    K = this._daylightSavingAdjust(e.currentDay ? new Date(e.currentYear, e.currentMonth, e.currentDay) : new Date(9999, 9, 9)),
                    Q = this._getMinMaxDate(e, "min"),
                    J = this._getMinMaxDate(e, "max"),
                    Z = e.drawMonth - U,
                    ee = e.drawYear;
                if (Z < 0 && (Z += 12, ee--), J) for (t = this._daylightSavingAdjust(new Date(J.getFullYear(), J.getMonth() - Y[0] * Y[1] + 1, J.getDate())), t = Q && t < Q ? Q : t; this._daylightSavingAdjust(new Date(ee, Z, 1)) > t;) Z--,
                Z < 0 && (Z = 11, ee--);
                for (e.drawMonth = Z, e.drawYear = ee, i = this._get(e, "prevText"), i = V ? this.formatDate(i, this._daylightSavingAdjust(new Date(ee, Z - G, 1)), this._getFormatConfig(e)) : i, n = this._canAdjustMonth(e, -1, ee, Z) ? "<a class='ui-datepicker-prev ui-corner-all' data-handler='prev' data-event='click' title='" + i + "'><span class='ui-icon ui-icon-circle-triangle-" + (F ? "e" : "w") + "'>" + i + "</span></a>" : q ? "" : "<a class='ui-datepicker-prev ui-corner-all ui-state-disabled' title='" + i + "'><span class='ui-icon ui-icon-circle-triangle-" + (F ? "e" : "w") + "'>" + i + "</span></a>", s = this._get(e, "nextText"), s = V ? this.formatDate(s, this._daylightSavingAdjust(new Date(ee, Z + G, 1)), this._getFormatConfig(e)) : s, a = this._canAdjustMonth(e, 1, ee, Z) ? "<a class='ui-datepicker-next ui-corner-all' data-handler='next' data-event='click' title='" + s + "'><span class='ui-icon ui-icon-circle-triangle-" + (F ? "w" : "e") + "'>" + s + "</span></a>" : q ? "" : "<a class='ui-datepicker-next ui-corner-all ui-state-disabled' title='" + s + "'><span class='ui-icon ui-icon-circle-triangle-" + (F ? "w" : "e") + "'>" + s + "</span></a>", o = this._get(e, "currentText"), r = this._get(e, "gotoCurrent") && e.currentDay ? K : R, o = V ? this.formatDate(o, r, this._getFormatConfig(e)) : o, l = e.inline ? "" : "<button type='button' class='ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all' data-handler='hide' data-event='click'>" + this._get(e, "closeText") + "</button>", c = B ? "<div class='ui-datepicker-buttonpane ui-widget-content'>" + (F ? l : "") + (this._isInRange(e, r) ? "<button type='button' class='ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all' data-handler='today' data-event='click'>" + o + "</button>" : "") + (F ? "" : l) + "</div>" : "", d = parseInt(this._get(e, "firstDay"), 10), d = isNaN(d) ? 0 : d, h = this._get(e, "showWeek"), u = this._get(e, "dayNames"), p = this._get(e, "dayNamesMin"), f = this._get(e, "monthNames"), m = this._get(e, "monthNamesShort"), g = this._get(e, "beforeShowDay"), v = this._get(e, "showOtherMonths"), y = this._get(e, "selectOtherMonths"), b = this._getDefaultDate(e), w = "", _, x = 0; x < Y[0]; x++) {
                    for (C = "", this.maxRows = 4, k = 0; k < Y[1]; k++) {
                        if (T = this._daylightSavingAdjust(new Date(ee, Z, e.selectedDay)), S = " ui-corner-all", D = "", X) {
                            if (D += "<div class='ui-datepicker-group", Y[1] > 1) switch (k) {
                                case 0:
                                    D += " ui-datepicker-group-first",
                                        S = " ui-corner-" + (F ? "right" : "left");
                                    break;
                                case Y[1] - 1:
                                    D += " ui-datepicker-group-last",
                                        S = " ui-corner-" + (F ? "left" : "right");
                                    break;
                                default:
                                    D += " ui-datepicker-group-middle",
                                        S = ""
                            }
                            D += "'>"
                        }
                        for (D += "<div class='ui-datepicker-header ui-widget-header ui-helper-clearfix" + S + "'>" + (/all|left/.test(S) && 0 === x ? F ? a : n : "") + (/all|right/.test(S) && 0 === x ? F ? n : a : "") + this._generateMonthYearHeader(e, Z, ee, Q, J, x > 0 || k > 0, f, m) + "</div><table class='ui-datepicker-calendar'><thead><tr>", E = h ? "<th class='ui-datepicker-week-col'>" + this._get(e, "weekHeader") + "</th>" : "", _ = 0; _ < 7; _++) I = (_ + d) % 7,
                            E += "<th" + ((_ + d + 6) % 7 >= 5 ? " class='ui-datepicker-week-end'" : "") + "><span title='" + u[I] + "'>" + p[I] + "</span></th>";
                        for (D += E + "</tr></thead><tbody>", P = this._getDaysInMonth(ee, Z), ee === e.selectedYear && Z === e.selectedMonth && (e.selectedDay = Math.min(e.selectedDay, P)), M = (this._getFirstDayOfMonth(ee, Z) - d + 7) % 7, j = Math.ceil((M + P) / 7), N = X && this.maxRows > j ? this.maxRows : j, this.maxRows = N, A = this._daylightSavingAdjust(new Date(ee, Z, 1 - M)), O = 0; O < N; O++) {
                            for (D += "<tr>", z = h ? "<td class='ui-datepicker-week-col'>" + this._get(e, "calculateWeek")(A) + "</td>" : "", _ = 0; _ < 7; _++) $ = g ? g.apply(e.input ? e.input[0] : null, [A]) : [!0, ""],
                                H = A.getMonth() !== Z,
                                L = H && !y || !$[0] || Q && A < Q || J && A > J,
                                z += "<td class='" + ((_ + d + 6) % 7 >= 5 ? " ui-datepicker-week-end" : "") + (H ? " ui-datepicker-other-month" : "") + (A.getTime() === T.getTime() && Z === e.selectedMonth && e._keyEvent || b.getTime() === A.getTime() && b.getTime() === T.getTime() ? " " + this._dayOverClass : "") + (L ? " " + this._unselectableClass + " ui-state-disabled" : "") + (H && !v ? "" : " " + $[1] + (A.getTime() === K.getTime() ? " " + this._currentClass : "") + (A.getTime() === R.getTime() ? " ui-datepicker-today" : "")) + "'" + (H && !v || !$[2] ? "" : " title='" + $[2].replace(/'/g, "&#39;") + "'") + (L ? "" : " data-handler='selectDay' data-event='click' data-month='" + A.getMonth() + "' data-year='" + A.getFullYear() + "'") + ">" + (H && !v ? "&#xa0;" : L ? "<span class='ui-state-default'>" + A.getDate() + "</span>" : "<a class='ui-state-default" + (A.getTime() === R.getTime() ? " ui-state-highlight" : "") + (A.getTime() === K.getTime() ? " ui-state-active" : "") + (H ? " ui-priority-secondary" : "") + "' href='#'>" + A.getDate() + "</a>") + "</td>",
                                A.setDate(A.getDate() + 1),
                                A = this._daylightSavingAdjust(A);
                            D += z + "</tr>"
                        }
                        Z++,
                        Z > 11 && (Z = 0, ee++),
                            D += "</tbody></table>" + (X ? "</div>" + (Y[0] > 0 && k === Y[1] - 1 ? "<div class='ui-datepicker-row-break'></div>" : "") : ""),
                            C += D
                    }
                    w += C
                }
                return w += c,
                    e._keyEvent = !1,
                    w
            },
            _generateMonthYearHeader: function (e, t, i, n, s, a, o, r) {
                var l, c, d, h, u, p, f, m, g = this._get(e, "changeMonth"),
                    v = this._get(e, "changeYear"),
                    y = this._get(e, "showMonthAfterYear"),
                    b = "<div class='ui-datepicker-title'>",
                    w = "";
                if (a || !g) w += "<span class='ui-datepicker-month'>" + o[t] + "</span>";
                else {
                    for (l = n && n.getFullYear() === i, c = s && s.getFullYear() === i, w += "<select class='ui-datepicker-month' data-handler='selectMonth' data-event='change'>", d = 0; d < 12; d++)(!l || d >= n.getMonth()) && (!c || d <= s.getMonth()) && (w += "<option value='" + d + "'" + (d === t ? " selected='selected'" : "") + ">" + r[d] + "</option>");
                    w += "</select>"
                }
                if (y || (b += w + (!a && g && v ? "" : "&#xa0;")), !e.yearshtml) if (e.yearshtml = "", a || !v) b += "<span class='ui-datepicker-year'>" + i + "</span>";
                else {
                    for (h = this._get(e, "yearRange").split(":"), u = (new Date).getFullYear(), p = function (e) {
                        var t = e.match(/c[+\-].*/) ? i + parseInt(e.substring(1), 10) : e.match(/[+\-].*/) ? u + parseInt(e, 10) : parseInt(e, 10);
                        return isNaN(t) ? u : t
                    }, f = p(h[0]), m = Math.max(f, p(h[1] || "")), f = n ? Math.max(f, n.getFullYear()) : f, m = s ? Math.min(m, s.getFullYear()) : m, e.yearshtml += "<select class='ui-datepicker-year' data-handler='selectYear' data-event='change'>"; f <= m; f++) e.yearshtml += "<option value='" + f + "'" + (f === i ? " selected='selected'" : "") + ">" + f + "</option>";
                    e.yearshtml += "</select>",
                        b += e.yearshtml,
                        e.yearshtml = null
                }
                return b += this._get(e, "yearSuffix"),
                y && (b += (!a && g && v ? "" : "&#xa0;") + w),
                    b += "</div>"
            },
            _adjustInstDate: function (e, t, i) {
                var n = e.drawYear + ("Y" === i ? t : 0),
                    s = e.drawMonth + ("M" === i ? t : 0),
                    a = Math.min(e.selectedDay, this._getDaysInMonth(n, s)) + ("D" === i ? t : 0),
                    o = this._restrictMinMax(e, this._daylightSavingAdjust(new Date(n, s, a)));
                e.selectedDay = o.getDate(),
                    e.drawMonth = e.selectedMonth = o.getMonth(),
                    e.drawYear = e.selectedYear = o.getFullYear(),
                ("M" === i || "Y" === i) && this._notifyChange(e)
            },
            _restrictMinMax: function (e, t) {
                var i = this._getMinMaxDate(e, "min"),
                    n = this._getMinMaxDate(e, "max"),
                    s = i && t < i ? i : t;
                return n && s > n ? n : s
            },
            _notifyChange: function (e) {
                var t = this._get(e, "onChangeMonthYear");
                t && t.apply(e.input ? e.input[0] : null, [e.selectedYear, e.selectedMonth + 1, e])
            },
            _getNumberOfMonths: function (e) {
                var t = this._get(e, "numberOfMonths");
                return null == t ? [1, 1] : "number" == typeof t ? [1, t] : t
            },
            _getMinMaxDate: function (e, t) {
                return this._determineDate(e, this._get(e, t + "Date"), null)
            },
            _getDaysInMonth: function (e, t) {
                return 32 - this._daylightSavingAdjust(new Date(e, t, 32)).getDate()
            },
            _getFirstDayOfMonth: function (e, t) {
                return new Date(e, t, 1).getDay()
            },
            _canAdjustMonth: function (e, t, i, n) {
                var s = this._getNumberOfMonths(e),
                    a = this._daylightSavingAdjust(new Date(i, n + (t < 0 ? t : s[0] * s[1]), 1));
                return t < 0 && a.setDate(this._getDaysInMonth(a.getFullYear(), a.getMonth())),
                    this._isInRange(e, a)
            },
            _isInRange: function (e, t) {
                var i, n, s = this._getMinMaxDate(e, "min"),
                    a = this._getMinMaxDate(e, "max"),
                    o = null,
                    r = null,
                    l = this._get(e, "yearRange");
                return l && (i = l.split(":"), n = (new Date).getFullYear(), o = parseInt(i[0], 10), r = parseInt(i[1], 10), i[0].match(/[+\-].*/) && (o += n), i[1].match(/[+\-].*/) && (r += n)),
                (!s || t.getTime() >= s.getTime()) && (!a || t.getTime() <= a.getTime()) && (!o || t.getFullYear() >= o) && (!r || t.getFullYear() <= r)
            },
            _getFormatConfig: function (e) {
                var t = this._get(e, "shortYearCutoff");
                return t = "string" != typeof t ? t : (new Date).getFullYear() % 100 + parseInt(t, 10),
                {
                    shortYearCutoff: t,
                    dayNamesShort: this._get(e, "dayNamesShort"),
                    dayNames: this._get(e, "dayNames"),
                    monthNamesShort: this._get(e, "monthNamesShort"),
                    monthNames: this._get(e, "monthNames")
                }
            },
            _formatDate: function (e, t, i, n) {
                t || (e.currentDay = e.selectedDay, e.currentMonth = e.selectedMonth, e.currentYear = e.selectedYear);
                var s = t ? "object" == typeof t ? t : this._daylightSavingAdjust(new Date(n, i, t)) : this._daylightSavingAdjust(new Date(e.currentYear, e.currentMonth, e.currentDay));
                return this.formatDate(this._get(e, "dateFormat"), s, this._getFormatConfig(e))
            }
        }),
            e.fn.datepicker = function (t) {
                if (!this.length) return this;
                e.datepicker.initialized || (e(document).mousedown(e.datepicker._checkExternalClick), e.datepicker.initialized = !0),
                0 === e("#" + e.datepicker._mainDivId).length && e("body").append(e.datepicker.dpDiv);
                var i = Array.prototype.slice.call(arguments, 1);
                return "string" != typeof t || "isDisabled" !== t && "getDate" !== t && "widget" !== t ? "option" === t && 2 === arguments.length && "string" == typeof arguments[1] ? e.datepicker["_" + t + "Datepicker"].apply(e.datepicker, [this[0]].concat(i)) : this.each(function () {
                    "string" == typeof t ? e.datepicker["_" + t + "Datepicker"].apply(e.datepicker, [this].concat(i)) : e.datepicker._attachDatepicker(this, t)
                }) : e.datepicker["_" + t + "Datepicker"].apply(e.datepicker, [this[0]].concat(i))
            },
            e.datepicker = new i,
            e.datepicker.initialized = !1,
            e.datepicker.uuid = (new Date).getTime(),
            e.datepicker.version = "1.10.1",
            window["DP_jQuery_" + r] = e
    }(jQuery),


    function (e, t) {
        var i = {
                buttons: !0,
                height: !0,
                maxHeight: !0,
                maxWidth: !0,
                minHeight: !0,
                minWidth: !0,
                width: !0
            },
            n = {
                maxHeight: !0,
                maxWidth: !0,
                minHeight: !0,
                minWidth: !0
            };
        e.widget("ui.dialog", {
            version: "1.10.1",
            options: {
                appendTo: "body",
                autoOpen: !0,
                buttons: [],
                closeOnEscape: !0,
                closeText: "close",
                dialogClass: "",
                draggable: !0,
                hide: null,
                height: "auto",
                maxHeight: null,
                maxWidth: null,
                minHeight: 150,
                minWidth: 150,
                modal: !1,
                position: {
                    my: "center",
                    at: "center",
                    of: window,
                    collision: "fit",
                    using: function (t) {
                        var i = e(this).css(t).offset().top;
                        i < 0 && e(this).css("top", t.top - i)
                    }
                },
                resizable: !0,
                show: null,
                title: null,
                width: 300,
                beforeClose: null,
                close: null,
                drag: null,
                dragStart: null,
                dragStop: null,
                focus: null,
                open: null,
                resize: null,
                resizeStart: null,
                resizeStop: null
            },
            _create: function () {
                this.originalCss = {
                    display: this.element[0].style.display,
                    width: this.element[0].style.width,
                    minHeight: this.element[0].style.minHeight,
                    maxHeight: this.element[0].style.maxHeight,
                    height: this.element[0].style.height
                },
                    this.originalPosition = {
                        parent: this.element.parent(),
                        index: this.element.parent().children().index(this.element)
                    },
                    this.originalTitle = this.element.attr("title"),
                    this.options.title = this.options.title || this.originalTitle,
                    this._createWrapper(),
                    this.element.show().removeAttr("title").addClass("ui-dialog-content ui-widget-content").appendTo(this.uiDialog),
                    this._createTitlebar(),
                    this._createButtonPane(),
                this.options.draggable && e.fn.draggable && this._makeDraggable(),
                this.options.resizable && e.fn.resizable && this._makeResizable(),
                    this._isOpen = !1
            },
            _init: function () {
                this.options.autoOpen && this.open()
            },
            _appendTo: function () {
                var t = this.options.appendTo;
                return t && (t.jquery || t.nodeType) ? e(t) : this.document.find(t || "body").eq(0)
            },
            _destroy: function () {
                var e, t = this.originalPosition;
                this._destroyOverlay(),
                    this.element.removeUniqueId().removeClass("ui-dialog-content ui-widget-content").css(this.originalCss).detach(),
                    this.uiDialog.stop(!0, !0).remove(),
                this.originalTitle && this.element.attr("title", this.originalTitle),
                    e = t.parent.children().eq(t.index),
                    e.length && e[0] !== this.element[0] ? e.before(this.element) : t.parent.append(this.element)
            },
            widget: function () {
                return this.uiDialog
            },
            disable: e.noop,
            enable: e.noop,
            close: function (t) {
                var i = this;
                this._isOpen && this._trigger("beforeClose", t) !== !1 && (this._isOpen = !1, this._destroyOverlay(), this.opener.filter(":focusable").focus().length || e(this.document[0].activeElement).blur(), this._hide(this.uiDialog, this.options.hide, function () {
                    i._trigger("close", t)
                }))
            },
            isOpen: function () {
                return this._isOpen
            },
            moveToTop: function () {
                this._moveToTop()
            },
            _moveToTop: function (e, t) {
                var i = !!this.uiDialog.nextAll(":visible").insertBefore(this.uiDialog).length;
                return i && !t && this._trigger("focus", e),
                    i
            },
            open: function () {
                var t = this;
                return this._isOpen ? void(this._moveToTop() && this._focusTabbable()) : (this._isOpen = !0, this.opener = e(this.document[0].activeElement), this._size(), this._position(), this._createOverlay(), this._moveToTop(null, !0), this._show(this.uiDialog, this.options.show, function () {
                    t._focusTabbable(),
                        t._trigger("focus")
                }), this._trigger("open"), void 0)
            },
            _focusTabbable: function () {
                var e = this.element.find("[autofocus]");
                e.length || (e = this.element.find(":tabbable")),
                e.length || (e = this.uiDialogButtonPane.find(":tabbable")),
                e.length || (e = this.uiDialogTitlebarClose.filter(":tabbable")),
                e.length || (e = this.uiDialog),
                    e.eq(0).focus()
            },
            _keepFocus: function (t) {
                function i() {
                    var t = this.document[0].activeElement,
                        i = this.uiDialog[0] === t || e.contains(this.uiDialog[0], t);
                    i || this._focusTabbable()
                }

                t.preventDefault(),
                    i.call(this),
                    this._delay(i)
            },
            _createWrapper: function () {
                this.uiDialog = e("<div>").addClass("ui-dialog ui-widget ui-widget-content ui-corner-all ui-front " + this.options.dialogClass).hide().attr({
                    tabIndex: -1,
                    role: "dialog"
                }).appendTo(this._appendTo()),
                    this._on(this.uiDialog, {
                        keydown: function (t) {
                            if (this.options.closeOnEscape && !t.isDefaultPrevented() && t.keyCode && t.keyCode === e.ui.keyCode.ESCAPE) return t.preventDefault(),
                                void this.close(t);
                            if (t.keyCode === e.ui.keyCode.TAB) {
                                var i = this.uiDialog.find(":tabbable"),
                                    n = i.filter(":first"),
                                    s = i.filter(":last");
                                t.target !== s[0] && t.target !== this.uiDialog[0] || t.shiftKey ? (t.target === n[0] || t.target === this.uiDialog[0]) && t.shiftKey && (s.focus(1), t.preventDefault()) : (n.focus(1), t.preventDefault())
                            }
                        },
                        mousedown: function (e) {
                            this._moveToTop(e) && this._focusTabbable()
                        }
                    }),
                this.element.find("[aria-describedby]").length || this.uiDialog.attr({
                    "aria-describedby": this.element.uniqueId().attr("id")
                })
            },
            _createTitlebar: function () {
                var t;
                this.uiDialogTitlebar = e("<div>").addClass("ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix").prependTo(this.uiDialog),
                    this._on(this.uiDialogTitlebar, {
                        mousedown: function (t) {
                            e(t.target).closest(".ui-dialog-titlebar-close") || this.uiDialog.focus()
                        }
                    }),
                    this.uiDialogTitlebarClose = e("<button></button>").button({
                        label: this.options.closeText,
                        icons: {
                            primary: "ui-icon-closethick"
                        },
                        text: !1
                    }).addClass("ui-dialog-titlebar-close").appendTo(this.uiDialogTitlebar),
                    this._on(this.uiDialogTitlebarClose, {
                        click: function (e) {
                            e.preventDefault(),
                                this.close(e)
                        }
                    }),
                    t = e("<span>").uniqueId().addClass("ui-dialog-title").prependTo(this.uiDialogTitlebar),
                    this._title(t),
                    this.uiDialog.attr({
                        "aria-labelledby": t.attr("id")
                    })
            },
            _title: function (e) {
                this.options.title || e.html("&#160;"),
                    e.text(this.options.title)
            },
            _createButtonPane: function () {
                this.uiDialogButtonPane = e("<div>").addClass("ui-dialog-buttonpane ui-widget-content ui-helper-clearfix"),
                    this.uiButtonSet = e("<div>").addClass("ui-dialog-buttonset").appendTo(this.uiDialogButtonPane),
                    this._createButtons()
            },
            _createButtons: function () {
                var t = this,
                    i = this.options.buttons;
                return this.uiDialogButtonPane.remove(),
                    this.uiButtonSet.empty(),
                    e.isEmptyObject(i) || e.isArray(i) && !i.length ? void this.uiDialog.removeClass("ui-dialog-buttons") : (e.each(i, function (i, n) {
                        var s, a;
                        n = e.isFunction(n) ? {
                            click: n,
                            text: i
                        } : n,
                            n = e.extend({
                                type: "button"
                            }, n),
                            s = n.click,
                            n.click = function () {
                                s.apply(t.element[0], arguments)
                            },
                            a = {
                                icons: n.icons,
                                text: n.showText
                            },
                            delete n.icons,
                            delete n.showText,
                            e("<button></button>", n).button(a).appendTo(t.uiButtonSet)
                    }), this.uiDialog.addClass("ui-dialog-buttons"), this.uiDialogButtonPane.appendTo(this.uiDialog), void 0)
            },
            _makeDraggable: function () {
                function t(e) {
                    return {
                        position: e.position,
                        offset: e.offset
                    }
                }

                var i = this,
                    n = this.options;
                this.uiDialog.draggable({
                    cancel: ".ui-dialog-content, .ui-dialog-titlebar-close",
                    handle: ".ui-dialog-titlebar",
                    containment: "document",
                    start: function (n, s) {
                        e(this).addClass("ui-dialog-dragging"),
                            i._blockFrames(),
                            i._trigger("dragStart", n, t(s))
                    },
                    drag: function (e, n) {
                        i._trigger("drag", e, t(n))
                    },
                    stop: function (s, a) {
                        n.position = [a.position.left - i.document.scrollLeft(), a.position.top - i.document.scrollTop()],
                            e(this).removeClass("ui-dialog-dragging"),
                            i._unblockFrames(),
                            i._trigger("dragStop", s, t(a))
                    }
                })
            },
            _makeResizable: function () {
                function t(e) {
                    return {
                        originalPosition: e.originalPosition,
                        originalSize: e.originalSize,
                        position: e.position,
                        size: e.size
                    }
                }

                var i = this,
                    n = this.options,
                    s = n.resizable,
                    a = this.uiDialog.css("position"),
                    o = "string" == typeof s ? s : "n,e,s,w,se,sw,ne,nw";
                this.uiDialog.resizable({
                    cancel: ".ui-dialog-content",
                    containment: "document",
                    alsoResize: this.element,
                    maxWidth: n.maxWidth,
                    maxHeight: n.maxHeight,
                    minWidth: n.minWidth,
                    minHeight: this._minHeight(),
                    handles: o,
                    start: function (n, s) {
                        e(this).addClass("ui-dialog-resizing"),
                            i._blockFrames(),
                            i._trigger("resizeStart", n, t(s))
                    },
                    resize: function (e, n) {
                        i._trigger("resize", e, t(n))
                    },
                    stop: function (s, a) {
                        n.height = e(this).height(),
                            n.width = e(this).width(),
                            e(this).removeClass("ui-dialog-resizing"),
                            i._unblockFrames(),
                            i._trigger("resizeStop", s, t(a))
                    }
                }).css("position", a)
            },
            _minHeight: function () {
                var e = this.options;
                return "auto" === e.height ? e.minHeight : Math.min(e.minHeight, e.height)
            },
            _position: function () {
                var e = this.uiDialog.is(":visible");
                e || this.uiDialog.show(),
                    this.uiDialog.position(this.options.position),
                e || this.uiDialog.hide()
            },
            _setOptions: function (t) {
                var s = this,
                    a = !1,
                    o = {};
                e.each(t, function (e, t) {
                    s._setOption(e, t),
                    e in i && (a = !0),
                    e in n && (o[e] = t)
                }),
                a && (this._size(), this._position()),
                this.uiDialog.is(":data(ui-resizable)") && this.uiDialog.resizable("option", o)
            },
            _setOption: function (e, t) {
                var i, n, s = this.uiDialog;
                "dialogClass" === e && s.removeClass(this.options.dialogClass).addClass(t),
                "disabled" !== e && (this._super(e, t), "appendTo" === e && this.uiDialog.appendTo(this._appendTo()), "buttons" === e && this._createButtons(), "closeText" === e && this.uiDialogTitlebarClose.button({
                    label: "" + t
                }), "draggable" === e && (i = s.is(":data(ui-draggable)"), i && !t && s.draggable("destroy"), !i && t && this._makeDraggable()), "position" === e && this._position(), "resizable" === e && (n = s.is(":data(ui-resizable)"), n && !t && s.resizable("destroy"), n && "string" == typeof t && s.resizable("option", "handles", t), !n && t !== !1 && this._makeResizable()), "title" === e && this._title(this.uiDialogTitlebar.find(".ui-dialog-title")))
            },
            _size: function () {
                var e, t, i, n = this.options;
                this.element.show().css({
                    width: "auto",
                    minHeight: 0,
                    maxHeight: "none",
                    height: 0
                }),
                n.minWidth > n.width && (n.width = n.minWidth),
                    e = this.uiDialog.css({
                        height: "auto",
                        width: n.width
                    }).outerHeight(),
                    t = Math.max(0, n.minHeight - e),
                    i = "number" == typeof n.maxHeight ? Math.max(0, n.maxHeight - e) : "none",
                    "auto" === n.height ? this.element.css({
                        minHeight: t,
                        maxHeight: i,
                        height: "auto"
                    }) : this.element.height(Math.max(0, n.height - e)),
                this.uiDialog.is(":data(ui-resizable)") && this.uiDialog.resizable("option", "minHeight", this._minHeight())
            },
            _blockFrames: function () {
                this.iframeBlocks = this.document.find("iframe").map(function () {
                    var t = e(this);
                    return e("<div>").css({
                        position: "absolute",
                        width: t.outerWidth(),
                        height: t.outerHeight()
                    }).appendTo(t.parent()).offset(t.offset())[0]
                })
            },
            _unblockFrames: function () {
                this.iframeBlocks && (this.iframeBlocks.remove(), delete this.iframeBlocks)
            },
            _createOverlay: function () {
                this.options.modal && (e.ui.dialog.overlayInstances || this._delay(function () {
                    e.ui.dialog.overlayInstances && this.document.bind("focusin.dialog", function (t) {
                        !e(t.target).closest(".ui-dialog").length && !e(t.target).closest(".ui-datepicker").length && (t.preventDefault(), e(".ui-dialog:visible:last .ui-dialog-content").data("ui-dialog")._focusTabbable())
                    })
                }), this.overlay = e("<div>").addClass("ui-widget-overlay ui-front").appendTo(this._appendTo()), this._on(this.overlay, {
                    mousedown: "_keepFocus"
                }), e.ui.dialog.overlayInstances++)
            },
            _destroyOverlay: function () {
                this.options.modal && this.overlay && (e.ui.dialog.overlayInstances--, e.ui.dialog.overlayInstances || this.document.unbind("focusin.dialog"), this.overlay.remove(), this.overlay = null)
            }
        }),
            e.ui.dialog.overlayInstances = 0,
        e.uiBackCompat !== !1 && e.widget("ui.dialog", e.ui.dialog, {
            _position: function () {
                var t, i = this.options.position,
                    n = [],
                    s = [0, 0];
                i ? (("string" == typeof i || "object" == typeof i && "0" in i) && (n = i.split ? i.split(" ") : [i[0], i[1]], 1 === n.length && (n[1] = n[0]), e.each(["left", "top"], function (e, t) {
                    +n[e] === n[e] && (s[e] = n[e], n[e] = t)
                }), i = {
                    my: n[0] + (s[0] < 0 ? s[0] : "+" + s[0]) + " " + n[1] + (s[1] < 0 ? s[1] : "+" + s[1]),
                    at: n.join(" ")
                }), i = e.extend({}, e.ui.dialog.prototype.options.position, i)) : i = e.ui.dialog.prototype.options.position,
                    t = this.uiDialog.is(":visible"),
                t || this.uiDialog.show(),
                    this.uiDialog.position(i),
                t || this.uiDialog.hide()
            }
        })
    }(jQuery),


    function (e, t) {
        e.widget("ui.menu", {
            version: "1.10.1",
            defaultElement: "<ul>",
            delay: 300,
            options: {
                icons: {
                    submenu: "ui-icon-carat-1-e"
                },
                menus: "ul",
                position: {
                    my: "left top",
                    at: "right top"
                },
                role: "menu",
                blur: null,
                focus: null,
                select: null
            },
            _create: function () {
                this.activeMenu = this.element,
                    this.mouseHandled = !1,
                    this.element.uniqueId().addClass("ui-menu ui-widget ui-widget-content ui-corner-all").toggleClass("ui-menu-icons", !!this.element.find(".ui-icon").length).attr({
                        role: this.options.role,
                        tabIndex: 0
                    }).bind("click" + this.eventNamespace, e.proxy(function (e) {
                        this.options.disabled && e.preventDefault()
                    }, this)),
                this.options.disabled && this.element.addClass("ui-state-disabled").attr("aria-disabled", "true"),
                    this._on({
                        "mousedown .ui-menu-item > a": function (e) {
                            e.preventDefault()
                        },
                        "click .ui-state-disabled > a": function (e) {
                            e.preventDefault()
                        },
                        "click .ui-menu-item:has(a)": function (t) {
                            var i = e(t.target).closest(".ui-menu-item");
                            !this.mouseHandled && i.not(".ui-state-disabled").length && (this.mouseHandled = !0, this.select(t), i.has(".ui-menu").length ? this.expand(t) : this.element.is(":focus") || (this.element.trigger("focus", [!0]), this.active && 1 === this.active.parents(".ui-menu").length && clearTimeout(this.timer)))
                        },
                        "mouseenter .ui-menu-item": function (t) {
                            var i = e(t.currentTarget);
                            i.siblings().children(".ui-state-active").removeClass("ui-state-active"),
                                this.focus(t, i)
                        },
                        mouseleave: "collapseAll",
                        "mouseleave .ui-menu": "collapseAll",
                        focus: function (e, t) {
                            var i = this.active || this.element.children(".ui-menu-item").eq(0);
                            t || this.focus(e, i)
                        },
                        blur: function (t) {
                            this._delay(function () {
                                e.contains(this.element[0], this.document[0].activeElement) || this.collapseAll(t)
                            })
                        },
                        keydown: "_keydown"
                    }),
                    this.refresh(),
                    this._on(this.document, {
                        click: function (t) {
                            e(t.target).closest(".ui-menu").length || this.collapseAll(t),
                                this.mouseHandled = !1
                        }
                    })
            },
            _destroy: function () {
                this.element.removeAttr("aria-activedescendant").find(".ui-menu").addBack().removeClass("ui-menu ui-widget ui-widget-content ui-corner-all ui-menu-icons").removeAttr("role").removeAttr("tabIndex").removeAttr("aria-labelledby").removeAttr("aria-expanded").removeAttr("aria-hidden").removeAttr("aria-disabled").removeUniqueId().show(),
                    this.element.find(".ui-menu-item").removeClass("ui-menu-item").removeAttr("role").removeAttr("aria-disabled").children("a").removeUniqueId().removeClass("ui-corner-all ui-state-hover").removeAttr("tabIndex").removeAttr("role").removeAttr("aria-haspopup").children().each(function () {
                        var t = e(this);
                        t.data("ui-menu-submenu-carat") && t.remove()
                    }),
                    this.element.find(".ui-menu-divider").removeClass("ui-menu-divider ui-widget-content")
            },
            _keydown: function (t) {
                function i(e) {
                    return e.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&")
                }

                var n, s, a, o, r, l = !0;
                switch (t.keyCode) {
                    case e.ui.keyCode.PAGE_UP:
                        this.previousPage(t);
                        break;
                    case e.ui.keyCode.PAGE_DOWN:
                        this.nextPage(t);
                        break;
                    case e.ui.keyCode.HOME:
                        this._move("first", "first", t);
                        break;
                    case e.ui.keyCode.END:
                        this._move("last", "last", t);
                        break;
                    case e.ui.keyCode.UP:
                        this.previous(t);
                        break;
                    case e.ui.keyCode.DOWN:
                        this.next(t);
                        break;
                    case e.ui.keyCode.LEFT:
                        this.collapse(t);
                        break;
                    case e.ui.keyCode.RIGHT:
                        this.active && !this.active.is(".ui-state-disabled") && this.expand(t);
                        break;
                    case e.ui.keyCode.ENTER:
                    case e.ui.keyCode.SPACE:
                        this._activate(t);
                        break;
                    case e.ui.keyCode.ESCAPE:
                        this.collapse(t);
                        break;
                    default:
                        l = !1,
                            s = this.previousFilter || "",
                            a = String.fromCharCode(t.keyCode),
                            o = !1,
                            clearTimeout(this.filterTimer),
                            a === s ? o = !0 : a = s + a,
                            r = new RegExp("^" + i(a), "i"),
                            n = this.activeMenu.children(".ui-menu-item").filter(function () {
                                return r.test(e(this).children("a").text())
                            }),
                            n = o && n.index(this.active.next()) !== -1 ? this.active.nextAll(".ui-menu-item") : n,
                        n.length || (a = String.fromCharCode(t.keyCode), r = new RegExp("^" + i(a), "i"), n = this.activeMenu.children(".ui-menu-item").filter(function () {
                            return r.test(e(this).children("a").text())
                        })),
                            n.length ? (this.focus(t, n), n.length > 1 ? (this.previousFilter = a, this.filterTimer = this._delay(function () {
                                delete this.previousFilter
                            }, 1e3)) : delete this.previousFilter) : delete this.previousFilter
                }
                l && t.preventDefault()
            },
            _activate: function (e) {
                this.active.is(".ui-state-disabled") || (this.active.children("a[aria-haspopup='true']").length ? this.expand(e) : this.select(e))
            },
            refresh: function () {
                var t, i = this.options.icons.submenu,
                    n = this.element.find(this.options.menus);
                n.filter(":not(.ui-menu)").addClass("ui-menu ui-widget ui-widget-content ui-corner-all").hide().attr({
                    role: this.options.role,
                    "aria-hidden": "true",
                    "aria-expanded": "false"
                }).each(function () {
                    var t = e(this),
                        n = t.prev("a"),
                        s = e("<span>").addClass("ui-menu-icon ui-icon " + i).data("ui-menu-submenu-carat", !0);
                    n.attr("aria-haspopup", "true").prepend(s),
                        t.attr("aria-labelledby", n.attr("id"))
                }),
                    t = n.add(this.element),
                    t.children(":not(.ui-menu-item):has(a)").addClass("ui-menu-item").attr("role", "presentation").children("a").uniqueId().addClass("ui-corner-all").attr({
                        tabIndex: -1,
                        role: this._itemRole()
                    }),
                    t.children(":not(.ui-menu-item)").each(function () {
                        var t = e(this);
                        /[^\-\u2014\u2013\s]/.test(t.text()) || t.addClass("ui-widget-content ui-menu-divider")
                    }),
                    t.children(".ui-state-disabled").attr("aria-disabled", "true"),
                this.active && !e.contains(this.element[0], this.active[0]) && this.blur()
            },
            _itemRole: function () {
                return {
                    menu: "menuitem",
                    listbox: "option"
                }[this.options.role]
            },
            _setOption: function (e, t) {
                "icons" === e && this.element.find(".ui-menu-icon").removeClass(this.options.icons.submenu).addClass(t.submenu),
                    this._super(e, t)
            },
            focus: function (e, t) {
                var i, n;
                this.blur(e, e && "focus" === e.type),
                    this._scrollIntoView(t),
                    this.active = t.first(),
                    n = this.active.children("a").addClass("ui-state-focus"),
                this.options.role && this.element.attr("aria-activedescendant", n.attr("id")),
                    this.active.parent().closest(".ui-menu-item").children("a:first").addClass("ui-state-active"),
                    e && "keydown" === e.type ? this._close() : this.timer = this._delay(function () {
                        this._close()
                    }, this.delay),
                    i = t.children(".ui-menu"),
                i.length && /^mouse/.test(e.type) && this._startOpening(i),
                    this.activeMenu = t.parent(),
                    this._trigger("focus", e, {
                        item: t
                    })
            },
            _scrollIntoView: function (t) {
                var i, n, s, a, o, r;
                this._hasScroll() && (i = parseFloat(e.css(this.activeMenu[0], "borderTopWidth")) || 0, n = parseFloat(e.css(this.activeMenu[0], "paddingTop")) || 0, s = t.offset().top - this.activeMenu.offset().top - i - n, a = this.activeMenu.scrollTop(), o = this.activeMenu.height(), r = t.height(), s < 0 ? this.activeMenu.scrollTop(a + s) : s + r > o && this.activeMenu.scrollTop(a + s - o + r))
            },
            blur: function (e, t) {
                t || clearTimeout(this.timer),
                this.active && (this.active.children("a").removeClass("ui-state-focus"), this.active = null, this._trigger("blur", e, {
                    item: this.active
                }))
            },
            _startOpening: function (e) {
                clearTimeout(this.timer),
                "true" === e.attr("aria-hidden") && (this.timer = this._delay(function () {
                    this._close(),
                        this._open(e)
                }, this.delay))
            },
            _open: function (t) {
                var i = e.extend({
                    of: this.active
                }, this.options.position);
                clearTimeout(this.timer),
                    this.element.find(".ui-menu").not(t.parents(".ui-menu")).hide().attr("aria-hidden", "true"),
                    t.show().removeAttr("aria-hidden").attr("aria-expanded", "true").position(i)
            },
            collapseAll: function (t, i) {
                clearTimeout(this.timer),
                    this.timer = this._delay(function () {
                        var n = i ? this.element : e(t && t.target).closest(this.element.find(".ui-menu"));
                        n.length || (n = this.element),
                            this._close(n),
                            this.blur(t),
                            this.activeMenu = n
                    }, this.delay)
            },
            _close: function (e) {
                e || (e = this.active ? this.active.parent() : this.element),
                    e.find(".ui-menu").hide().attr("aria-hidden", "true").attr("aria-expanded", "false").end().find("a.ui-state-active").removeClass("ui-state-active")
            },
            collapse: function (e) {
                var t = this.active && this.active.parent().closest(".ui-menu-item", this.element);
                t && t.length && (this._close(), this.focus(e, t))
            },
            expand: function (e) {
                var t = this.active && this.active.children(".ui-menu ").children(".ui-menu-item").first();
                t && t.length && (this._open(t.parent()), this._delay(function () {
                    this.focus(e, t)
                }))
            },
            next: function (e) {
                this._move("next", "first", e)
            },
            previous: function (e) {
                this._move("prev", "last", e)
            },
            isFirstItem: function () {
                return this.active && !this.active.prevAll(".ui-menu-item").length
            },
            isLastItem: function () {
                return this.active && !this.active.nextAll(".ui-menu-item").length
            },
            _move: function (e, t, i) {
                var n;
                this.active && (n = "first" === e || "last" === e ? this.active["first" === e ? "prevAll" : "nextAll"](".ui-menu-item").eq(-1) : this.active[e + "All"](".ui-menu-item").eq(0)),
                n && n.length && this.active || (n = this.activeMenu.children(".ui-menu-item")[t]()),
                    this.focus(i, n)
            },
            nextPage: function (t) {
                var i, n, s;
                return this.active ? void(this.isLastItem() || (this._hasScroll() ? (n = this.active.offset().top, s = this.element.height(), this.active.nextAll(".ui-menu-item").each(function () {
                    return i = e(this),
                    i.offset().top - n - s < 0
                }), this.focus(t, i)) : this.focus(t, this.activeMenu.children(".ui-menu-item")[this.active ? "last" : "first"]()))) : void this.next(t)
            },
            previousPage: function (t) {
                var i, n, s;
                return this.active ? void(this.isFirstItem() || (this._hasScroll() ? (n = this.active.offset().top, s = this.element.height(), this.active.prevAll(".ui-menu-item").each(function () {
                    return i = e(this),
                    i.offset().top - n + s > 0
                }), this.focus(t, i)) : this.focus(t, this.activeMenu.children(".ui-menu-item").first()))) : void this.next(t)
            },
            _hasScroll: function () {
                return this.element.outerHeight() < this.element.prop("scrollHeight")
            },
            select: function (t) {
                this.active = this.active || e(t.target).closest(".ui-menu-item");
                var i = {
                    item: this.active
                };
                this.active.has(".ui-menu").length || this.collapseAll(t, !0),
                    this._trigger("select", t, i)
            }
        })
    }(jQuery),


    function (e, t) {
        e.widget("ui.progressbar", {
            version: "1.10.1",
            options: {
                max: 100,
                value: 0,
                change: null,
                complete: null
            },
            min: 0,
            _create: function () {
                this.oldValue = this.options.value = this._constrainedValue(),
                    this.element.addClass("ui-progressbar ui-widget ui-widget-content ui-corner-all").attr({
                        role: "progressbar",
                        "aria-valuemin": this.min
                    }),
                    this.valueDiv = e("<div class='ui-progressbar-value ui-widget-header ui-corner-left'></div>").appendTo(this.element),
                    this._refreshValue()
            },
            _destroy: function () {
                this.element.removeClass("ui-progressbar ui-widget ui-widget-content ui-corner-all").removeAttr("role").removeAttr("aria-valuemin").removeAttr("aria-valuemax").removeAttr("aria-valuenow"),
                    this.valueDiv.remove()
            },
            value: function (e) {
                return e === t ? this.options.value : (this.options.value = this._constrainedValue(e), void this._refreshValue())
            },
            _constrainedValue: function (e) {
                return e === t && (e = this.options.value),
                    this.indeterminate = e === !1,
                "number" != typeof e && (e = 0),
                !this.indeterminate && Math.min(this.options.max, Math.max(this.min, e))
            },
            _setOptions: function (e) {
                var t = e.value;
                delete e.value,
                    this._super(e),
                    this.options.value = this._constrainedValue(t),
                    this._refreshValue()
            },
            _setOption: function (e, t) {
                "max" === e && (t = Math.max(this.min, t)),
                    this._super(e, t)
            },
            _percentage: function () {
                return this.indeterminate ? 100 : 100 * (this.options.value - this.min) / (this.options.max - this.min)
            },
            _refreshValue: function () {
                var t = this.options.value,
                    i = this._percentage();
                this.valueDiv.toggle(this.indeterminate || t > this.min).toggleClass("ui-corner-right", t === this.options.max).width(i.toFixed(0) + "%"),
                    this.element.toggleClass("ui-progressbar-indeterminate", this.indeterminate),
                    this.indeterminate ? (this.element.removeAttr("aria-valuenow"), this.overlayDiv || (this.overlayDiv = e("<div class='ui-progressbar-overlay'></div>").appendTo(this.valueDiv))) : (this.element.attr({
                        "aria-valuemax": this.options.max,
                        "aria-valuenow": t
                    }), this.overlayDiv && (this.overlayDiv.remove(), this.overlayDiv = null)),
                this.oldValue !== t && (this.oldValue = t, this._trigger("change")),
                t === this.options.max && this._trigger("complete")
            }
        })
    }(jQuery),


    function (e, t) {
        var i = 5;
        e.widget("ui.slider", e.ui.mouse, {
            version: "1.10.1",
            widgetEventPrefix: "slide",
            options: {
                animate: !1,
                distance: 0,
                max: 100,
                min: 0,
                orientation: "horizontal",
                range: !1,
                step: 1,
                value: 0,
                values: null,
                change: null,
                slide: null,
                start: null,
                stop: null
            },
            _create: function () {
                this._keySliding = !1,
                    this._mouseSliding = !1,
                    this._animateOff = !0,
                    this._handleIndex = null,
                    this._detectOrientation(),
                    this._mouseInit(),
                    this.element.addClass("ui-slider ui-slider-" + this.orientation + " ui-widget ui-widget-content ui-corner-all"),
                    this._refresh(),
                    this._setOption("disabled", this.options.disabled),
                    this._animateOff = !1
            },
            _refresh: function () {
                this._createRange(),
                    this._createHandles(),
                    this._setupEvents(),
                    this._refreshValue()
            },
            _createHandles: function () {
                var t, i, n = this.options,
                    s = this.element.find(".ui-slider-handle").addClass("ui-state-default ui-corner-all"),
                    a = "<a class='ui-slider-handle ui-state-default ui-corner-all' href='#'></a>",
                    o = [];
                for (i = n.values && n.values.length || 1, s.length > i && (s.slice(i).remove(), s = s.slice(0, i)), t = s.length; t < i; t++) o.push(a);
                this.handles = s.add(e(o.join("")).appendTo(this.element)),
                    this.handle = this.handles.eq(0),
                    this.handles.each(function (t) {
                        e(this).data("ui-slider-handle-index", t)
                    })
            },
            _createRange: function () {
                var t = this.options,
                    i = "";
                t.range ? (t.range === !0 && (t.values ? t.values.length && 2 !== t.values.length ? t.values = [t.values[0], t.values[0]] : e.isArray(t.values) && (t.values = t.values.slice(0)) : t.values = [this._valueMin(), this._valueMin()]), this.range && this.range.length ? this.range.removeClass("ui-slider-range-min ui-slider-range-max").css({
                    left: "",
                    bottom: ""
                }) : (this.range = e("<div></div>").appendTo(this.element), i = "ui-slider-range ui-widget-header ui-corner-all"), this.range.addClass(i + ("min" === t.range || "max" === t.range ? " ui-slider-range-" + t.range : ""))) : this.range = e([])
            },
            _setupEvents: function () {
                var e = this.handles.add(this.range).filter("a");
                this._off(e),
                    this._on(e, this._handleEvents),
                    this._hoverable(e),
                    this._focusable(e)
            },
            _destroy: function () {
                this.handles.remove(),
                    this.range.remove(),
                    this.element.removeClass("ui-slider ui-slider-horizontal ui-slider-vertical ui-widget ui-widget-content ui-corner-all"),
                    this._mouseDestroy()
            },
            _mouseCapture: function (t) {
                var i, n, s, a, o, r, l, c, d = this,
                    h = this.options;
                return !h.disabled && (this.elementSize = {
                        width: this.element.outerWidth(),
                        height: this.element.outerHeight()
                    }, this.elementOffset = this.element.offset(), i = {
                        x: t.pageX,
                        y: t.pageY
                    }, n = this._normValueFromMouse(i), s = this._valueMax() - this._valueMin() + 1, this.handles.each(function (t) {
                        var i = Math.abs(n - d.values(t));
                        (s > i || s === i && (t === d._lastChangedValue || d.values(t) === h.min)) && (s = i, a = e(this), o = t)
                    }), r = this._start(t, o), r !== !1 && (this._mouseSliding = !0, this._handleIndex = o, a.addClass("ui-state-active").focus(), l = a.offset(), c = !e(t.target).parents().addBack().is(".ui-slider-handle"), this._clickOffset = c ? {
                        left: 0,
                        top: 0
                    } : {
                        left: t.pageX - l.left - a.width() / 2,
                        top: t.pageY - l.top - a.height() / 2 - (parseInt(a.css("borderTopWidth"), 10) || 0) - (parseInt(a.css("borderBottomWidth"), 10) || 0) + (parseInt(a.css("marginTop"), 10) || 0)
                    }, this.handles.hasClass("ui-state-hover") || this._slide(t, o, n), this._animateOff = !0, !0))
            },
            _mouseStart: function () {
                return !0
            },
            _mouseDrag: function (e) {
                var t = {
                        x: e.pageX,
                        y: e.pageY
                    },
                    i = this._normValueFromMouse(t);
                return this._slide(e, this._handleIndex, i),
                    !1
            },
            _mouseStop: function (e) {
                return this.handles.removeClass("ui-state-active"),
                    this._mouseSliding = !1,
                    this._stop(e, this._handleIndex),
                    this._change(e, this._handleIndex),
                    this._handleIndex = null,
                    this._clickOffset = null,
                    this._animateOff = !1,
                    !1
            },
            _detectOrientation: function () {
                this.orientation = "vertical" === this.options.orientation ? "vertical" : "horizontal"
            },
            _normValueFromMouse: function (e) {
                var t, i, n, s, a;
                return "horizontal" === this.orientation ? (t = this.elementSize.width, i = e.x - this.elementOffset.left - (this._clickOffset ? this._clickOffset.left : 0)) : (t = this.elementSize.height, i = e.y - this.elementOffset.top - (this._clickOffset ? this._clickOffset.top : 0)),
                    n = i / t,
                n > 1 && (n = 1),
                n < 0 && (n = 0),
                "vertical" === this.orientation && (n = 1 - n),
                    s = this._valueMax() - this._valueMin(),
                    a = this._valueMin() + n * s,
                    this._trimAlignValue(a)
            },
            _start: function (e, t) {
                var i = {
                    handle: this.handles[t],
                    value: this.value()
                };
                return this.options.values && this.options.values.length && (i.value = this.values(t), i.values = this.values()),
                    this._trigger("start", e, i)
            },
            _slide: function (e, t, i) {
                var n, s, a;
                this.options.values && this.options.values.length ? (n = this.values(t ? 0 : 1), 2 === this.options.values.length && this.options.range === !0 && (0 === t && i > n || 1 === t && i < n) && (i = n), i !== this.values(t) && (s = this.values(), s[t] = i, a = this._trigger("slide", e, {
                    handle: this.handles[t],
                    value: i,
                    values: s
                }), n = this.values(t ? 0 : 1), a !== !1 && this.values(t, i, !0))) : i !== this.value() && (a = this._trigger("slide", e, {
                    handle: this.handles[t],
                    value: i
                }), a !== !1 && this.value(i))
            },
            _stop: function (e, t) {
                var i = {
                    handle: this.handles[t],
                    value: this.value()
                };
                this.options.values && this.options.values.length && (i.value = this.values(t), i.values = this.values()),
                    this._trigger("stop", e, i)
            },
            _change: function (e, t) {
                if (!this._keySliding && !this._mouseSliding) {
                    var i = {
                        handle: this.handles[t],
                        value: this.value()
                    };
                    this.options.values && this.options.values.length && (i.value = this.values(t), i.values = this.values()),
                        this._lastChangedValue = t,
                        this._trigger("change", e, i)
                }
            },
            value: function (e) {
                return arguments.length ? (this.options.value = this._trimAlignValue(e), this._refreshValue(), this._change(null, 0), void 0) : this._value()
            },
            values: function (t, i) {
                var n, s, a;
                if (arguments.length > 1) return this.options.values[t] = this._trimAlignValue(i),
                    this._refreshValue(),
                    this._change(null, t),
                    void 0;
                if (!arguments.length) return this._values();
                if (!e.isArray(arguments[0])) return this.options.values && this.options.values.length ? this._values(t) : this.value();
                for (n = this.options.values, s = arguments[0], a = 0; a < n.length; a += 1) n[a] = this._trimAlignValue(s[a]),
                    this._change(null, a);
                this._refreshValue()
            },
            _setOption: function (t, i) {
                var n, s = 0;
                switch ("range" === t && this.options.range === !0 && ("min" === i ? (this.options.value = this._values(0), this.options.values = null) : "max" === i && (this.options.value = this._values(this.options.values.length - 1), this.options.values = null)), e.isArray(this.options.values) && (s = this.options.values.length), e.Widget.prototype._setOption.apply(this, arguments), t) {
                    case "orientation":
                        this._detectOrientation(),
                            this.element.removeClass("ui-slider-horizontal ui-slider-vertical").addClass("ui-slider-" + this.orientation),
                            this._refreshValue();
                        break;
                    case "value":
                        this._animateOff = !0,
                            this._refreshValue(),
                            this._change(null, 0),
                            this._animateOff = !1;
                        break;
                    case "values":
                        for (this._animateOff = !0, this._refreshValue(), n = 0; n < s; n += 1) this._change(null, n);
                        this._animateOff = !1;
                        break;
                    case "min":
                    case "max":
                        this._animateOff = !0,
                            this._refreshValue(),
                            this._animateOff = !1;
                        break;
                    case "range":
                        this._animateOff = !0,
                            this._refresh(),
                            this._animateOff = !1
                }
            },
            _value: function () {
                var e = this.options.value;
                return e = this._trimAlignValue(e)
            },
            _values: function (e) {
                var t, i, n;
                if (arguments.length) return t = this.options.values[e],
                    t = this._trimAlignValue(t);
                if (this.options.values && this.options.values.length) {
                    for (i = this.options.values.slice(), n = 0; n < i.length; n += 1) i[n] = this._trimAlignValue(i[n]);
                    return i
                }
                return []
            },
            _trimAlignValue: function (e) {
                if (e <= this._valueMin()) return this._valueMin();
                if (e >= this._valueMax()) return this._valueMax();
                var t = this.options.step > 0 ? this.options.step : 1,
                    i = (e - this._valueMin()) % t,
                    n = e - i;
                return 2 * Math.abs(i) >= t && (n += i > 0 ? t : -t),
                    parseFloat(n.toFixed(5))
            },
            _valueMin: function () {
                return this.options.min
            },
            _valueMax: function () {
                return this.options.max
            },
            _refreshValue: function () {
                var t, i, n, s, a, o = this.options.range,
                    r = this.options,
                    l = this,
                    c = !this._animateOff && r.animate,
                    d = {};
                this.options.values && this.options.values.length ? this.handles.each(function (n) {
                    i = (l.values(n) - l._valueMin()) / (l._valueMax() - l._valueMin()) * 100,
                        d["horizontal" === l.orientation ? "left" : "bottom"] = i + "%",
                        e(this).stop(1, 1)[c ? "animate" : "css"](d, r.animate),
                    l.options.range === !0 && ("horizontal" === l.orientation ? (0 === n && l.range.stop(1, 1)[c ? "animate" : "css"]({
                        left: i + "%"
                    }, r.animate), 1 === n && l.range[c ? "animate" : "css"]({
                        width: i - t + "%"
                    }, {
                        queue: !1,
                        duration: r.animate
                    })) : (0 === n && l.range.stop(1, 1)[c ? "animate" : "css"]({
                        bottom: i + "%"
                    }, r.animate), 1 === n && l.range[c ? "animate" : "css"]({
                        height: i - t + "%"
                    }, {
                        queue: !1,
                        duration: r.animate
                    }))),
                        t = i
                }) : (n = this.value(), s = this._valueMin(), a = this._valueMax(), i = a !== s ? (n - s) / (a - s) * 100 : 0, d["horizontal" === this.orientation ? "left" : "bottom"] = i + "%", this.handle.stop(1, 1)[c ? "animate" : "css"](d, r.animate), "min" === o && "horizontal" === this.orientation && this.range.stop(1, 1)[c ? "animate" : "css"]({
                    width: i + "%"
                }, r.animate), "max" === o && "horizontal" === this.orientation && this.range[c ? "animate" : "css"]({
                    width: 100 - i + "%"
                }, {
                    queue: !1,
                    duration: r.animate
                }), "min" === o && "vertical" === this.orientation && this.range.stop(1, 1)[c ? "animate" : "css"]({
                    height: i + "%"
                }, r.animate), "max" === o && "vertical" === this.orientation && this.range[c ? "animate" : "css"]({
                    height: 100 - i + "%"
                }, {
                    queue: !1,
                    duration: r.animate
                }))
            },
            _handleEvents: {
                keydown: function (t) {
                    var n, s, a, o, r = e(t.target).data("ui-slider-handle-index");
                    switch (t.keyCode) {
                        case e.ui.keyCode.HOME:
                        case e.ui.keyCode.END:
                        case e.ui.keyCode.PAGE_UP:
                        case e.ui.keyCode.PAGE_DOWN:
                        case e.ui.keyCode.UP:
                        case e.ui.keyCode.RIGHT:
                        case e.ui.keyCode.DOWN:
                        case e.ui.keyCode.LEFT:
                            if (t.preventDefault(), !this._keySliding && (this._keySliding = !0, e(t.target).addClass("ui-state-active"), n = this._start(t, r), n === !1)) return
                    }
                    switch (o = this.options.step, s = a = this.options.values && this.options.values.length ? this.values(r) : this.value(), t.keyCode) {
                        case e.ui.keyCode.HOME:
                            a = this._valueMin();
                            break;
                        case e.ui.keyCode.END:
                            a = this._valueMax();
                            break;
                        case e.ui.keyCode.PAGE_UP:
                            a = this._trimAlignValue(s + (this._valueMax() - this._valueMin()) / i);
                            break;
                        case e.ui.keyCode.PAGE_DOWN:
                            a = this._trimAlignValue(s - (this._valueMax() - this._valueMin()) / i);
                            break;
                        case e.ui.keyCode.UP:
                        case e.ui.keyCode.RIGHT:
                            if (s === this._valueMax()) return;
                            a = this._trimAlignValue(s + o);
                            break;
                        case e.ui.keyCode.DOWN:
                        case e.ui.keyCode.LEFT:
                            if (s === this._valueMin()) return;
                            a = this._trimAlignValue(s - o)
                    }
                    this._slide(t, r, a)
                },
                click: function (e) {
                    e.preventDefault()
                },
                keyup: function (t) {
                    var i = e(t.target).data("ui-slider-handle-index");
                    this._keySliding && (this._keySliding = !1, this._stop(t, i), this._change(t, i), e(t.target).removeClass("ui-state-active"))
                }
            }
        })
    }(jQuery),


    function (e) {
        function t(e) {
            return function () {
                var t = this.element.val();
                e.apply(this, arguments),
                    this._refresh(),
                t !== this.element.val() && this._trigger("change")
            }
        }

        e.widget("ui.spinner", {
            version: "1.10.1",
            defaultElement: "<input>",
            widgetEventPrefix: "spin",
            options: {
                culture: null,
                icons: {
                    down: "ui-icon-triangle-1-s",
                    up: "ui-icon-triangle-1-n"
                },
                incremental: !0,
                max: null,
                min: null,
                numberFormat: null,
                page: 10,
                step: 1,
                change: null,
                spin: null,
                start: null,
                stop: null
            },
            _create: function () {
                this._setOption("max", this.options.max),
                    this._setOption("min", this.options.min),
                    this._setOption("step", this.options.step),
                    this._value(this.element.val(), !0),
                    this._draw(),
                    this._on(this._events),
                    this._refresh(),
                    this._on(this.window, {
                        beforeunload: function () {
                            this.element.removeAttr("autocomplete")
                        }
                    })
            },
            _getCreateOptions: function () {
                var t = {},
                    i = this.element;
                return e.each(["min", "max", "step"], function (e, n) {
                    var s = i.attr(n);
                    void 0 !== s && s.length && (t[n] = s)
                }),
                    t
            },
            _events: {
                keydown: function (e) {
                    this._start(e) && this._keydown(e) && e.preventDefault()
                },
                keyup: "_stop",
                focus: function () {
                    this.previous = this.element.val()
                },
                blur: function (e) {
                    return this.cancelBlur ? void delete this.cancelBlur : (this._refresh(), void(this.previous !== this.element.val() && this._trigger("change", e)))
                },
                mousewheel: function (e, t) {
                    if (t) return !(!this.spinning && !this._start(e)) && (this._spin((t > 0 ? 1 : -1) * this.options.step, e), clearTimeout(this.mousewheelTimer), this.mousewheelTimer = this._delay(function () {
                            this.spinning && this._stop(e)
                        }, 100), e.preventDefault(), void 0)
                },
                "mousedown .ui-spinner-button": function (t) {
                    function i() {
                        var e = this.element[0] === this.document[0].activeElement;
                        e || (this.element.focus(), this.previous = n, this._delay(function () {
                            this.previous = n
                        }))
                    }

                    var n;
                    n = this.element[0] === this.document[0].activeElement ? this.previous : this.element.val(),
                        t.preventDefault(),
                        i.call(this),
                        this.cancelBlur = !0,
                        this._delay(function () {
                            delete this.cancelBlur,
                                i.call(this)
                        }),
                    this._start(t) !== !1 && this._repeat(null, e(t.currentTarget).hasClass("ui-spinner-up") ? 1 : -1, t)
                },
                "mouseup .ui-spinner-button": "_stop",
                "mouseenter .ui-spinner-button": function (t) {
                    if (e(t.currentTarget).hasClass("ui-state-active")) return this._start(t) !== !1 && void this._repeat(null, e(t.currentTarget).hasClass("ui-spinner-up") ? 1 : -1, t)
                },
                "mouseleave .ui-spinner-button": "_stop"
            },
            _draw: function () {
                var e = this.uiSpinner = this.element.addClass("ui-spinner-input").attr("autocomplete", "off").wrap(this._uiSpinnerHtml()).parent().append(this._buttonHtml());
                this.element.attr("role", "spinbutton"),
                    this.buttons = e.find(".ui-spinner-button").attr("tabIndex", -1).button().removeClass("ui-corner-all"),
                this.buttons.height() > Math.ceil(.5 * e.height()) && e.height() > 0 && e.height(e.height()),
                this.options.disabled && this.disable()
            },
            _keydown: function (t) {
                var i = this.options,
                    n = e.ui.keyCode;
                switch (t.keyCode) {
                    case n.UP:
                        return this._repeat(null, 1, t),
                            !0;
                    case n.DOWN:
                        return this._repeat(null, -1, t),
                            !0;
                    case n.PAGE_UP:
                        return this._repeat(null, i.page, t),
                            !0;
                    case n.PAGE_DOWN:
                        return this._repeat(null, -i.page, t),
                            !0
                }
                return !1
            },
            _uiSpinnerHtml: function () {
                return "<span class='ui-spinner ui-widget ui-widget-content ui-corner-all'></span>"
            },
            _buttonHtml: function () {
                return "<a class='ui-spinner-button ui-spinner-up ui-corner-tr'><span class='ui-icon " + this.options.icons.up + "'>&#9650;</span></a><a class='ui-spinner-button ui-spinner-down ui-corner-br'><span class='ui-icon " + this.options.icons.down + "'>&#9660;</span></a>"
            },
            _start: function (e) {
                return !(!this.spinning && this._trigger("start", e) === !1) && (this.counter || (this.counter = 1), this.spinning = !0, !0)
            },
            _repeat: function (e, t, i) {
                e = e || 500,
                    clearTimeout(this.timer),
                    this.timer = this._delay(function () {
                        this._repeat(40, t, i)
                    }, e),
                    this._spin(t * this.options.step, i)
            },
            _spin: function (e, t) {
                var i = this.value() || 0;
                this.counter || (this.counter = 1),
                    i = this._adjustValue(i + e * this._increment(this.counter)),
                this.spinning && this._trigger("spin", t, {
                    value: i
                }) === !1 || (this._value(i), this.counter++)
            },
            _increment: function (t) {
                var i = this.options.incremental;
                return i ? e.isFunction(i) ? i(t) : Math.floor(t * t * t / 5e4 - t * t / 500 + 17 * t / 200 + 1) : 1
            },
            _precision: function () {
                var e = this._precisionOf(this.options.step);
                return null !== this.options.min && (e = Math.max(e, this._precisionOf(this.options.min))),
                    e
            },
            _precisionOf: function (e) {
                var t = e.toString(),
                    i = t.indexOf(".");
                return i === -1 ? 0 : t.length - i - 1
            },
            _adjustValue: function (e) {
                var t, i, n = this.options;
                return t = null !== n.min ? n.min : 0,
                    i = e - t,
                    i = Math.round(i / n.step) * n.step,
                    e = t + i,
                    e = parseFloat(e.toFixed(this._precision())),
                    null !== n.max && e > n.max ? n.max : null !== n.min && e < n.min ? n.min : e
            },
            _stop: function (e) {
                this.spinning && (clearTimeout(this.timer), clearTimeout(this.mousewheelTimer), this.counter = 0, this.spinning = !1, this._trigger("stop", e))
            },
            _setOption: function (e, t) {
                if ("culture" === e || "numberFormat" === e) {
                    var i = this._parse(this.element.val());
                    return this.options[e] = t,
                        void this.element.val(this._format(i))
                }
                ("max" === e || "min" === e || "step" === e) && "string" == typeof t && (t = this._parse(t)),
                "icons" === e && (this.buttons.first().find(".ui-icon").removeClass(this.options.icons.up).addClass(t.up), this.buttons.last().find(".ui-icon").removeClass(this.options.icons.down).addClass(t.down)),
                    this._super(e, t),
                "disabled" === e && (t ? (this.element.prop("disabled", !0), this.buttons.button("disable")) : (this.element.prop("disabled", !1), this.buttons.button("enable")))
            },
            _setOptions: t(function (e) {
                this._super(e),
                    this._value(this.element.val())
            }),
            _parse: function (e) {
                return "string" == typeof e && "" !== e && (e = window.Globalize && this.options.numberFormat ? Globalize.parseFloat(e, 10, this.options.culture) : +e),
                    "" === e || isNaN(e) ? null : e
            },
            _format: function (e) {
                return "" === e ? "" : window.Globalize && this.options.numberFormat ? Globalize.format(e, this.options.numberFormat, this.options.culture) : e
            },
            _refresh: function () {
                this.element.attr({
                    "aria-valuemin": this.options.min,
                    "aria-valuemax": this.options.max,
                    "aria-valuenow": this._parse(this.element.val())
                })
            },
            _value: function (e, t) {
                var i;
                "" !== e && (i = this._parse(e), null !== i && (t || (i = this._adjustValue(i)), e = this._format(i))),
                    this.element.val(e),
                    this._refresh()
            },
            _destroy: function () {
                this.element.removeClass("ui-spinner-input").prop("disabled", !1).removeAttr("autocomplete").removeAttr("role").removeAttr("aria-valuemin").removeAttr("aria-valuemax").removeAttr("aria-valuenow"),
                    this.uiSpinner.replaceWith(this.element)
            },
            stepUp: t(function (e) {
                this._stepUp(e)
            }),
            _stepUp: function (e) {
                this._start() && (this._spin((e || 1) * this.options.step), this._stop())
            },
            stepDown: t(function (e) {
                this._stepDown(e)
            }),
            _stepDown: function (e) {
                this._start() && (this._spin((e || 1) * -this.options.step), this._stop())
            },
            pageUp: t(function (e) {
                this._stepUp((e || 1) * this.options.page)
            }),
            pageDown: t(function (e) {
                this._stepDown((e || 1) * this.options.page)
            }),
            value: function (e) {
                return arguments.length ? void t(this._value).call(this, e) : this._parse(this.element.val())
            },
            widget: function () {
                return this.uiSpinner
            }
        })
    }(jQuery),


    function (e, t) {
        function i() {
            return ++s
        }

        function n(e) {
            return e.hash.length > 1 && decodeURIComponent(e.href.replace(a, "")) === decodeURIComponent(location.href.replace(a, ""))
        }

        var s = 0,
            a = /#.*$/;
        e.widget("ui.tabs", {
            version: "1.10.1",
            delay: 300,
            options: {
                active: null,
                collapsible: !1,
                event: "click",
                heightStyle: "content",
                hide: null,
                show: null,
                activate: null,
                beforeActivate: null,
                beforeLoad: null,
                load: null
            },
            _create: function () {
                var t = this,
                    i = this.options;
                this.running = !1,
                    this.element.addClass("ui-tabs ui-widget ui-widget-content ui-corner-all").toggleClass("ui-tabs-collapsible", i.collapsible).delegate(".ui-tabs-nav > li", "mousedown" + this.eventNamespace, function (t) {
                        e(this).is(".ui-state-disabled") && t.preventDefault()
                    }).delegate(".ui-tabs-anchor", "focus" + this.eventNamespace, function () {
                        e(this).closest("li").is(".ui-state-disabled") && this.blur()
                    }),
                    this._processTabs(),
                    i.active = this._initialActive(),
                e.isArray(i.disabled) && (i.disabled = e.unique(i.disabled.concat(e.map(this.tabs.filter(".ui-state-disabled"), function (e) {
                    return t.tabs.index(e)
                }))).sort()),
                    this.options.active !== !1 && this.anchors.length ? this.active = this._findActive(i.active) : this.active = e(),
                    this._refresh(),
                this.active.length && this.load(i.active)
            },
            _initialActive: function () {
                var t = this.options.active,
                    i = this.options.collapsible,
                    n = location.hash.substring(1);
                return null === t && (n && this.tabs.each(function (i, s) {
                    if (e(s).attr("aria-controls") === n) return t = i,
                        !1
                }), null === t && (t = this.tabs.index(this.tabs.filter(".ui-tabs-active"))), null !== t && t !== -1 || (t = !!this.tabs.length && 0)),
                t !== !1 && (t = this.tabs.index(this.tabs.eq(t)), t === -1 && (t = !i && 0)),
                !i && t === !1 && this.anchors.length && (t = 0),
                    t
            },
            _getCreateEventData: function () {
                return {
                    tab: this.active,
                    panel: this.active.length ? this._getPanelForTab(this.active) : e()
                }
            },
            _tabKeydown: function (t) {
                var i = e(this.document[0].activeElement).closest("li"),
                    n = this.tabs.index(i),
                    s = !0;
                if (!this._handlePageNav(t)) {
                    switch (t.keyCode) {
                        case e.ui.keyCode.RIGHT:
                        case e.ui.keyCode.DOWN:
                            n++;
                            break;
                        case e.ui.keyCode.UP:
                        case e.ui.keyCode.LEFT:
                            s = !1,
                                n--;
                            break;
                        case e.ui.keyCode.END:
                            n = this.anchors.length - 1;
                            break;
                        case e.ui.keyCode.HOME:
                            n = 0;
                            break;
                        case e.ui.keyCode.SPACE:
                            return t.preventDefault(),
                                clearTimeout(this.activating),
                                this._activate(n),
                                void 0;
                        case e.ui.keyCode.ENTER:
                            return t.preventDefault(),
                                clearTimeout(this.activating),
                                this._activate(n !== this.options.active && n),
                                void 0;
                        default:
                            return
                    }
                    t.preventDefault(),
                        clearTimeout(this.activating),
                        n = this._focusNextTab(n, s),
                    t.ctrlKey || (i.attr("aria-selected", "false"), this.tabs.eq(n).attr("aria-selected", "true"), this.activating = this._delay(function () {
                        this.option("active", n)
                    }, this.delay))
                }
            },
            _panelKeydown: function (t) {
                this._handlePageNav(t) || t.ctrlKey && t.keyCode === e.ui.keyCode.UP && (t.preventDefault(), this.active.focus())
            },
            _handlePageNav: function (t) {
                return t.altKey && t.keyCode === e.ui.keyCode.PAGE_UP ? (this._activate(this._focusNextTab(this.options.active - 1, !1)), !0) : t.altKey && t.keyCode === e.ui.keyCode.PAGE_DOWN ? (this._activate(this._focusNextTab(this.options.active + 1, !0)), !0) : void 0
            },
            _findNextTab: function (t, i) {
                function n() {
                    return t > s && (t = 0),
                    t < 0 && (t = s),
                        t
                }

                for (var s = this.tabs.length - 1; e.inArray(n(), this.options.disabled) !== -1;) t = i ? t + 1 : t - 1;
                return t
            },
            _focusNextTab: function (e, t) {
                return e = this._findNextTab(e, t),
                    this.tabs.eq(e).focus(),
                    e
            },
            _setOption: function (e, t) {
                return "active" === e ? void this._activate(t) : "disabled" === e ? void this._setupDisabled(t) : (this._super(e, t), "collapsible" === e && (this.element.toggleClass("ui-tabs-collapsible", t), !t && this.options.active === !1 && this._activate(0)), "event" === e && this._setupEvents(t), "heightStyle" === e && this._setupHeightStyle(t), void 0)
            },
            _tabId: function (e) {
                return e.attr("aria-controls") || "ui-tabs-" + i()
            },
            _sanitizeSelector: function (e) {
                return e ? e.replace(/[!"$%&'()*+,.\/:;<=>?@\[\]\^`{|}~]/g, "\\$&") : ""
            },
            refresh: function () {
                var t = this.options,
                    i = this.tablist.children(":has(a[href])");
                t.disabled = e.map(i.filter(".ui-state-disabled"), function (e) {
                    return i.index(e)
                }),
                    this._processTabs(),
                    t.active !== !1 && this.anchors.length ? this.active.length && !e.contains(this.tablist[0], this.active[0]) ? this.tabs.length === t.disabled.length ? (t.active = !1, this.active = e()) : this._activate(this._findNextTab(Math.max(0, t.active - 1), !1)) : t.active = this.tabs.index(this.active) : (t.active = !1, this.active = e()),
                    this._refresh()
            },
            _refresh: function () {
                this._setupDisabled(this.options.disabled),
                    this._setupEvents(this.options.event),
                    this._setupHeightStyle(this.options.heightStyle),
                    this.tabs.not(this.active).attr({
                        "aria-selected": "false",
                        tabIndex: -1
                    }),
                    this.panels.not(this._getPanelForTab(this.active)).hide().attr({
                        "aria-expanded": "false",
                        "aria-hidden": "true"
                    }),
                    this.active.length ? (this.active.addClass("ui-tabs-active ui-state-active").attr({
                        "aria-selected": "true",
                        tabIndex: 0
                    }), this._getPanelForTab(this.active).show().attr({
                        "aria-expanded": "true",
                        "aria-hidden": "false"
                    })) : this.tabs.eq(0).attr("tabIndex", 0)
            },
            _processTabs: function () {
                var t = this;
                this.tablist = this._getList().addClass("ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all").attr("role", "tablist"),
                    this.tabs = this.tablist.find("> li:has(a[href])").addClass("ui-state-default ui-corner-top").attr({
                        role: "tab",
                        tabIndex: -1
                    }),
                    this.anchors = this.tabs.map(function () {
                        return e("a", this)[0]
                    }).addClass("ui-tabs-anchor").attr({
                        role: "presentation",
                        tabIndex: -1
                    }),
                    this.panels = e(),
                    this.anchors.each(function (i, s) {
                        var a, o, r, l = e(s).uniqueId().attr("id"),
                            c = e(s).closest("li"),
                            d = c.attr("aria-controls");
                        n(s) ? (a = s.hash, o = t.element.find(t._sanitizeSelector(a))) : (r = t._tabId(c), a = "#" + r, o = t.element.find(a), o.length || (o = t._createPanel(r), o.insertAfter(t.panels[i - 1] || t.tablist)), o.attr("aria-live", "polite")),
                        o.length && (t.panels = t.panels.add(o)),
                        d && c.data("ui-tabs-aria-controls", d),
                            c.attr({
                                "aria-controls": a.substring(1),
                                "aria-labelledby": l
                            }),
                            o.attr("aria-labelledby", l)
                    }),
                    this.panels.addClass("ui-tabs-panel ui-widget-content ui-corner-bottom").attr("role", "tabpanel")
            },
            _getList: function () {
                return this.element.find("ol,ul").eq(0)
            },
            _createPanel: function (t) {
                return e("<div>").attr("id", t).addClass("ui-tabs-panel ui-widget-content ui-corner-bottom").data("ui-tabs-destroy", !0)
            },
            _setupDisabled: function (t) {
                e.isArray(t) && (t.length ? t.length === this.anchors.length && (t = !0) : t = !1);
                for (var i, n = 0; i = this.tabs[n]; n++) t === !0 || e.inArray(n, t) !== -1 ? e(i).addClass("ui-state-disabled").attr("aria-disabled", "true") : e(i).removeClass("ui-state-disabled").removeAttr("aria-disabled");
                this.options.disabled = t
            },
            _setupEvents: function (t) {
                var i = {
                    click: function (e) {
                        e.preventDefault()
                    }
                };
                t && e.each(t.split(" "), function (e, t) {
                    i[t] = "_eventHandler"
                }),
                    this._off(this.anchors.add(this.tabs).add(this.panels)),
                    this._on(this.anchors, i),
                    this._on(this.tabs, {
                        keydown: "_tabKeydown"
                    }),
                    this._on(this.panels, {
                        keydown: "_panelKeydown"
                    }),
                    this._focusable(this.tabs),
                    this._hoverable(this.tabs)
            },
            _setupHeightStyle: function (t) {
                var i, n = this.element.parent();
                "fill" === t ? (i = n.height(), i -= this.element.outerHeight() - this.element.height(), this.element.siblings(":visible").each(function () {
                    var t = e(this),
                        n = t.css("position");
                    "absolute" !== n && "fixed" !== n && (i -= t.outerHeight(!0))
                }), this.element.children().not(this.panels).each(function () {
                    i -= e(this).outerHeight(!0)
                }), this.panels.each(function () {
                    e(this).height(Math.max(0, i - e(this).innerHeight() + e(this).height()))
                }).css("overflow", "auto")) : "auto" === t && (i = 0, this.panels.each(function () {
                    i = Math.max(i, e(this).height("").height())
                }).height(i))
            },
            _eventHandler: function (t) {
                var i = this.options,
                    n = this.active,
                    s = e(t.currentTarget),
                    a = s.closest("li"),
                    o = a[0] === n[0],
                    r = o && i.collapsible,
                    l = r ? e() : this._getPanelForTab(a),
                    c = n.length ? this._getPanelForTab(n) : e(),
                    d = {
                        oldTab: n,
                        oldPanel: c,
                        newTab: r ? e() : a,
                        newPanel: l
                    };
                t.preventDefault(),
                a.hasClass("ui-state-disabled") || a.hasClass("ui-tabs-loading") || this.running || o && !i.collapsible || this._trigger("beforeActivate", t, d) === !1 || (i.active = !r && this.tabs.index(a), this.active = o ? e() : a, this.xhr && this.xhr.abort(), !c.length && !l.length && e.error("jQuery UI Tabs: Mismatching fragment identifier."), l.length && this.load(this.tabs.index(a), t), this._toggle(t, d))
            },
            _toggle: function (t, i) {
                function n() {
                    a.running = !1,
                        a._trigger("activate", t, i)
                }

                function s() {
                    i.newTab.closest("li").addClass("ui-tabs-active ui-state-active"),
                        o.length && a.options.show ? a._show(o, a.options.show, n) : (o.show(), n())
                }

                var a = this,
                    o = i.newPanel,
                    r = i.oldPanel;
                this.running = !0,
                    r.length && this.options.hide ? this._hide(r, this.options.hide, function () {
                        i.oldTab.closest("li").removeClass("ui-tabs-active ui-state-active"),
                            s()
                    }) : (i.oldTab.closest("li").removeClass("ui-tabs-active ui-state-active"), r.hide(), s()),
                    r.attr({
                        "aria-expanded": "false",
                        "aria-hidden": "true"
                    }),
                    i.oldTab.attr("aria-selected", "false"),
                    o.length && r.length ? i.oldTab.attr("tabIndex", -1) : o.length && this.tabs.filter(function () {
                        return 0 === e(this).attr("tabIndex")
                    }).attr("tabIndex", -1),
                    o.attr({
                        "aria-expanded": "true",
                        "aria-hidden": "false"
                    }),
                    i.newTab.attr({
                        "aria-selected": "true",
                        tabIndex: 0
                    })
            },
            _activate: function (t) {
                var i, n = this._findActive(t);
                n[0] !== this.active[0] && (n.length || (n = this.active), i = n.find(".ui-tabs-anchor")[0], this._eventHandler({
                    target: i,
                    currentTarget: i,
                    preventDefault: e.noop
                }))
            },
            _findActive: function (t) {
                return t === !1 ? e() : this.tabs.eq(t)
            },
            _getIndex: function (e) {
                return "string" == typeof e && (e = this.anchors.index(this.anchors.filter("[href$='" + e + "']"))),
                    e
            },
            _destroy: function () {
                this.xhr && this.xhr.abort(),
                    this.element.removeClass("ui-tabs ui-widget ui-widget-content ui-corner-all ui-tabs-collapsible"),
                    this.tablist.removeClass("ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all").removeAttr("role"),
                    this.anchors.removeClass("ui-tabs-anchor").removeAttr("role").removeAttr("tabIndex").removeUniqueId(),
                    this.tabs.add(this.panels).each(function () {
                        e.data(this, "ui-tabs-destroy") ? e(this).remove() : e(this).removeClass("ui-state-default ui-state-active ui-state-disabled ui-corner-top ui-corner-bottom ui-widget-content ui-tabs-active ui-tabs-panel").removeAttr("tabIndex").removeAttr("aria-live").removeAttr("aria-busy").removeAttr("aria-selected").removeAttr("aria-labelledby").removeAttr("aria-hidden").removeAttr("aria-expanded").removeAttr("role")
                    }),
                    this.tabs.each(function () {
                        var t = e(this),
                            i = t.data("ui-tabs-aria-controls");
                        i ? t.attr("aria-controls", i).removeData("ui-tabs-aria-controls") : t.removeAttr("aria-controls")
                    }),
                    this.panels.show(),
                "content" !== this.options.heightStyle && this.panels.css("height", "")
            },
            enable: function (i) {
                var n = this.options.disabled;
                n !== !1 && (i === t ? n = !1 : (i = this._getIndex(i), n = e.isArray(n) ? e.map(n, function (e) {
                    return e !== i ? e : null
                }) : e.map(this.tabs, function (e, t) {
                    return t !== i ? t : null
                })), this._setupDisabled(n))
            },
            disable: function (i) {
                var n = this.options.disabled;
                if (n !== !0) {
                    if (i === t) n = !0;
                    else {
                        if (i = this._getIndex(i), e.inArray(i, n) !== -1) return;
                        n = e.isArray(n) ? e.merge([i], n).sort() : [i]
                    }
                    this._setupDisabled(n)
                }
            },
            load: function (t, i) {
                t = this._getIndex(t);
                var s = this,
                    a = this.tabs.eq(t),
                    o = a.find(".ui-tabs-anchor"),
                    r = this._getPanelForTab(a),
                    l = {
                        tab: a,
                        panel: r
                    };
                n(o[0]) || (this.xhr = e.ajax(this._ajaxSettings(o, i, l)), this.xhr && "canceled" !== this.xhr.statusText && (a.addClass("ui-tabs-loading"), r.attr("aria-busy", "true"), this.xhr.success(function (e) {
                    setTimeout(function () {
                        r.html(e),
                            s._trigger("load", i, l)
                    }, 1)
                }).complete(function (e, t) {
                    setTimeout(function () {
                        "abort" === t && s.panels.stop(!1, !0),
                            a.removeClass("ui-tabs-loading"),
                            r.removeAttr("aria-busy"),
                        e === s.xhr && delete s.xhr
                    }, 1)
                })))
            },
            _ajaxSettings: function (t, i, n) {
                var s = this;
                return {
                    url: t.attr("href"),
                    beforeSend: function (t, a) {
                        return s._trigger("beforeLoad", i, e.extend({
                            jqXHR: t,
                            ajaxSettings: a
                        }, n))
                    }
                }
            },
            _getPanelForTab: function (t) {
                var i = e(t).attr("aria-controls");
                return this.element.find(this._sanitizeSelector("#" + i))
            }
        })
    }(jQuery),


    function (e) {
        function t(t, i) {
            var n = (t.attr("aria-describedby") || "").split(/\s+/);
            n.push(i),
                t.data("ui-tooltip-id", i).attr("aria-describedby", e.trim(n.join(" ")))
        }

        function i(t) {
            var i = t.data("ui-tooltip-id"),
                n = (t.attr("aria-describedby") || "").split(/\s+/),
                s = e.inArray(i, n);
            s !== -1 && n.splice(s, 1),
                t.removeData("ui-tooltip-id"),
                n = e.trim(n.join(" ")),
                n ? t.attr("aria-describedby", n) : t.removeAttr("aria-describedby")
        }

        var n = 0;
        e.widget("ui.tooltip", {
            version: "1.10.1",
            options: {
                content: function () {
                    var t = e(this).attr("title") || "";
                    return e("<a>").text(t).html()
                },
                hide: !0,
                items: "[title]:not([disabled])",
                position: {
                    my: "left top+15",
                    at: "left bottom",
                    collision: "flipfit flip"
                },
                show: !0,
                tooltipClass: null,
                track: !1,
                close: null,
                open: null
            },
            _create: function () {
                this._on({
                    mouseover: "open",
                    focusin: "open"
                }),
                    this.tooltips = {},
                    this.parents = {},
                this.options.disabled && this._disable()
            },
            _setOption: function (t, i) {
                var n = this;
                return "disabled" === t ? (this[i ? "_disable" : "_enable"](), void(this.options[t] = i)) : (this._super(t, i), void("content" === t && e.each(this.tooltips, function (e, t) {
                    n._updateContent(t)
                })))
            },
            _disable: function () {
                var t = this;
                e.each(this.tooltips, function (i, n) {
                    var s = e.Event("blur");
                    s.target = s.currentTarget = n[0],
                        t.close(s, !0)
                }),
                    this.element.find(this.options.items).addBack().each(function () {
                        var t = e(this);
                        t.is("[title]") && t.data("ui-tooltip-title", t.attr("title")).attr("title", "")
                    })
            },
            _enable: function () {
                this.element.find(this.options.items).addBack().each(function () {
                    var t = e(this);
                    t.data("ui-tooltip-title") && t.attr("title", t.data("ui-tooltip-title"))
                })
            },
            open: function (t) {
                var i = this,
                    n = e(t ? t.target : this.element).closest(this.options.items);
                n.length && !n.data("ui-tooltip-id") && (n.attr("title") && n.data("ui-tooltip-title", n.attr("title")), n.data("ui-tooltip-open", !0), t && "mouseover" === t.type && n.parents().each(function () {
                    var t, n = e(this);
                    n.data("ui-tooltip-open") && (t = e.Event("blur"), t.target = t.currentTarget = this, i.close(t, !0)),
                    n.attr("title") && (n.uniqueId(), i.parents[this.id] = {
                        element: this,
                        title: n.attr("title")
                    }, n.attr("title", ""))
                }), this._updateContent(n, t))
            },
            _updateContent: function (e, t) {
                var i, n = this.options.content,
                    s = this,
                    a = t ? t.type : null;
                return "string" == typeof n ? this._open(t, e, n) : (i = n.call(e[0], function (i) {
                    e.data("ui-tooltip-open") && s._delay(function () {
                        t && (t.type = a),
                            this._open(t, e, i)
                    })
                }), void(i && this._open(t, e, i)))
            },
            _open: function (i, n, s) {
                function a(e) {
                    c.of = e,
                    o.is(":hidden") || o.position(c)
                }

                var o, r, l, c = e.extend({}, this.options.position);
                if (s) {
                    if (o = this._find(n), o.length) return void o.find(".ui-tooltip-content").html(s);
                    n.is("[title]") && (i && "mouseover" === i.type ? n.attr("title", "") : n.removeAttr("title")),
                        o = this._tooltip(n),
                        t(n, o.attr("id")),
                        o.find(".ui-tooltip-content").html(s),
                        this.options.track && i && /^mouse/.test(i.type) ? (this._on(this.document, {
                            mousemove: a
                        }), a(i)) : o.position(e.extend({
                            of: n
                        }, this.options.position)),
                        o.hide(),
                        this._show(o, this.options.show),
                    this.options.show && this.options.show.delay && (l = this.delayedShow = setInterval(function () {
                        o.is(":visible") && (a(c.of), clearInterval(l))
                    }, e.fx.interval)),
                        this._trigger("open", i, {
                            tooltip: o
                        }),
                        r = {
                            keyup: function (t) {
                                if (t.keyCode === e.ui.keyCode.ESCAPE) {
                                    var i = e.Event(t);
                                    i.currentTarget = n[0],
                                        this.close(i, !0)
                                }
                            },
                            remove: function () {
                                this._removeTooltip(o)
                            }
                        },
                    i && "mouseover" !== i.type || (r.mouseleave = "close"),
                    i && "focusin" !== i.type || (r.focusout = "close"),
                        this._on(!0, n, r)
                }
            },
            close: function (t) {
                var n = this,
                    s = e(t ? t.currentTarget : this.element),
                    a = this._find(s);
                this.closing || (clearInterval(this.delayedShow), s.data("ui-tooltip-title") && s.attr("title", s.data("ui-tooltip-title")), i(s), a.stop(!0), this._hide(a, this.options.hide, function () {
                    n._removeTooltip(e(this))
                }), s.removeData("ui-tooltip-open"), this._off(s, "mouseleave focusout keyup"), s[0] !== this.element[0] && this._off(s, "remove"), this._off(this.document, "mousemove"), t && "mouseleave" === t.type && e.each(this.parents, function (t, i) {
                    e(i.element).attr("title", i.title),
                        delete n.parents[t]
                }), this.closing = !0, this._trigger("close", t, {
                    tooltip: a
                }), this.closing = !1)
            },
            _tooltip: function (t) {
                var i = "ui-tooltip-" + n++,
                    s = e("<div>").attr({
                        id: i,
                        role: "tooltip"
                    }).addClass("ui-tooltip ui-widget ui-corner-all ui-widget-content " + (this.options.tooltipClass || ""));
                return e("<div>").addClass("ui-tooltip-content").appendTo(s),
                    s.appendTo(this.document[0].body),
                    this.tooltips[i] = t,
                    s
            },
            _find: function (t) {
                var i = t.data("ui-tooltip-id");
                return i ? e("#" + i) : e()
            },
            _removeTooltip: function (e) {
                e.remove(),
                    delete this.tooltips[e.attr("id")]
            },
            _destroy: function () {
                var t = this;
                e.each(this.tooltips, function (i, n) {
                    var s = e.Event("blur");
                    s.target = s.currentTarget = n[0],
                        t.close(s, !0),
                        e("#" + i).remove(),
                    n.data("ui-tooltip-title") && (n.attr("title", n.data("ui-tooltip-title")), n.removeData("ui-tooltip-title"))
                })
            }
        })
    }(jQuery),
jQuery.effects ||
function (e, t) {
    var i = "ui-effects-";
    e.effects = {
        effect: {}
    },


        function (e, t) {
            function i(e, t, i) {
                var n = h[t.type] || {};
                return null == e ? i || !t.def ? null : t.def : (e = n.floor ? ~~e : parseFloat(e), isNaN(e) ? t.def : n.mod ? (e + n.mod) % n.mod : 0 > e ? 0 : n.max < e ? n.max : e)
            }

            function n(t) {
                var i = c(),
                    n = i._rgba = [];
                return t = t.toLowerCase(),
                    f(l, function (e, s) {
                        var a, o = s.re.exec(t),
                            r = o && s.parse(o),
                            l = s.space || "rgba";
                        if (r) return a = i[l](r),
                            i[d[l].cache] = a[d[l].cache],
                            n = i._rgba = a._rgba,
                            !1
                    }),
                    n.length ? ("0,0,0,0" === n.join() && e.extend(n, a.transparent), i) : a[t]
            }

            function s(e, t, i) {
                return i = (i + 1) % 1,
                    6 * i < 1 ? e + (t - e) * i * 6 : 2 * i < 1 ? t : 3 * i < 2 ? e + (t - e) * (2 / 3 - i) * 6 : e
            }

            var a, o = "backgroundColor borderBottomColor borderLeftColor borderRightColor borderTopColor color columnRuleColor outlineColor textDecorationColor textEmphasisColor",
                r = /^([\-+])=\s*(\d+\.?\d*)/,
                l = [{
                    re: /rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,
                    parse: function (e) {
                        return [e[1], e[2], e[3], e[4]]
                    }
                },
                    {
                        re: /rgba?\(\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,
                        parse: function (e) {
                            return [2.55 * e[1], 2.55 * e[2], 2.55 * e[3], e[4]]
                        }
                    },
                    {
                        re: /#([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})/,
                        parse: function (e) {
                            return [parseInt(e[1], 16), parseInt(e[2], 16), parseInt(e[3], 16)]
                        }
                    },
                    {
                        re: /#([a-f0-9])([a-f0-9])([a-f0-9])/,
                        parse: function (e) {
                            return [parseInt(e[1] + e[1], 16), parseInt(e[2] + e[2], 16), parseInt(e[3] + e[3], 16)]
                        }
                    },
                    {
                        re: /hsla?\(\s*(\d+(?:\.\d+)?)\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,
                        space: "hsla",
                        parse: function (e) {
                            return [e[1], e[2] / 100, e[3] / 100, e[4]]
                        }
                    }],
                c = e.Color = function (t, i, n, s) {
                    return new e.Color.fn.parse(t, i, n, s)
                },
                d = {
                    rgba: {
                        props: {
                            red: {
                                idx: 0,
                                type: "byte"
                            },
                            green: {
                                idx: 1,
                                type: "byte"
                            },
                            blue: {
                                idx: 2,
                                type: "byte"
                            }
                        }
                    },
                    hsla: {
                        props: {
                            hue: {
                                idx: 0,
                                type: "degrees"
                            },
                            saturation: {
                                idx: 1,
                                type: "percent"
                            },
                            lightness: {
                                idx: 2,
                                type: "percent"
                            }
                        }
                    }
                },
                h = {
                    "byte": {
                        floor: !0,
                        max: 255
                    },
                    percent: {
                        max: 1
                    },
                    degrees: {
                        mod: 360,
                        floor: !0
                    }
                },
                u = c.support = {},
                p = e("<p>")[0],
                f = e.each;
            p.style.cssText = "background-color:rgba(1,1,1,.5)",
                u.rgba = p.style.backgroundColor.indexOf("rgba") > -1,
                f(d, function (e, t) {
                    t.cache = "_" + e,
                        t.props.alpha = {
                            idx: 3,
                            type: "percent",
                            def: 1
                        }
                }),
                c.fn = e.extend(c.prototype, {
                    parse: function (s, o, r, l) {
                        if (s === t) return this._rgba = [null, null, null, null],
                            this;
                        (s.jquery || s.nodeType) && (s = e(s).css(o), o = t);
                        var h = this,
                            u = e.type(s),
                            p = this._rgba = [];
                        return o !== t && (s = [s, o, r, l], u = "array"),
                            "string" === u ? this.parse(n(s) || a._default) : "array" === u ? (f(d.rgba.props, function (e, t) {
                                p[t.idx] = i(s[t.idx], t)
                            }), this) : "object" === u ? (s instanceof c ? f(d, function (e, t) {
                                s[t.cache] && (h[t.cache] = s[t.cache].slice())
                            }) : f(d, function (t, n) {
                                var a = n.cache;
                                f(n.props, function (e, t) {
                                    if (!h[a] && n.to) {
                                        if ("alpha" === e || null == s[e]) return;
                                        h[a] = n.to(h._rgba)
                                    }
                                    h[a][t.idx] = i(s[e], t, !0)
                                }),
                                h[a] && e.inArray(null, h[a].slice(0, 3)) < 0 && (h[a][3] = 1, n.from && (h._rgba = n.from(h[a])))
                            }), this) : void 0
                    },
                    is: function (e) {
                        var t = c(e),
                            i = !0,
                            n = this;
                        return f(d, function (e, s) {
                            var a, o = t[s.cache];
                            return o && (a = n[s.cache] || s.to && s.to(n._rgba) || [], f(s.props, function (e, t) {
                                if (null != o[t.idx]) return i = o[t.idx] === a[t.idx]
                            })),
                                i
                        }),
                            i
                    },
                    _space: function () {
                        var e = [],
                            t = this;
                        return f(d, function (i, n) {
                            t[n.cache] && e.push(i)
                        }),
                            e.pop()
                    },
                    transition: function (e, t) {
                        var n = c(e),
                            s = n._space(),
                            a = d[s],
                            o = 0 === this.alpha() ? c("transparent") : this,
                            r = o[a.cache] || a.to(o._rgba),
                            l = r.slice();
                        return n = n[a.cache],
                            f(a.props, function (e, s) {
                                var a = s.idx,
                                    o = r[a],
                                    c = n[a],
                                    d = h[s.type] || {};
                                null !== c && (null === o ? l[a] = c : (d.mod && (c - o > d.mod / 2 ? o += d.mod : o - c > d.mod / 2 && (o -= d.mod)), l[a] = i((c - o) * t + o, s)))
                            }),
                            this[s](l)
                    },
                    blend: function (t) {
                        if (1 === this._rgba[3]) return this;
                        var i = this._rgba.slice(),
                            n = i.pop(),
                            s = c(t)._rgba;
                        return c(e.map(i, function (e, t) {
                            return (1 - n) * s[t] + n * e
                        }))
                    },
                    toRgbaString: function () {
                        var t = "rgba(",
                            i = e.map(this._rgba, function (e, t) {
                                return null == e ? t > 2 ? 1 : 0 : e
                            });
                        return 1 === i[3] && (i.pop(), t = "rgb("),
                        t + i.join() + ")"
                    },
                    toHslaString: function () {
                        var t = "hsla(",
                            i = e.map(this.hsla(), function (e, t) {
                                return null == e && (e = t > 2 ? 1 : 0),
                                t && t < 3 && (e = Math.round(100 * e) + "%"),
                                    e
                            });
                        return 1 === i[3] && (i.pop(), t = "hsl("),
                        t + i.join() + ")"
                    },
                    toHexString: function (t) {
                        var i = this._rgba.slice(),
                            n = i.pop();
                        return t && i.push(~~(255 * n)),
                        "#" + e.map(i, function (e) {
                            return e = (e || 0).toString(16),
                                1 === e.length ? "0" + e : e
                        }).join("")
                    },
                    toString: function () {
                        return 0 === this._rgba[3] ? "transparent" : this.toRgbaString()
                    }
                }),
                c.fn.parse.prototype = c.fn,
                d.hsla.to = function (e) {
                    if (null == e[0] || null == e[1] || null == e[2]) return [null, null, null, e[3]];
                    var t, i, n = e[0] / 255,
                        s = e[1] / 255,
                        a = e[2] / 255,
                        o = e[3],
                        r = Math.max(n, s, a),
                        l = Math.min(n, s, a),
                        c = r - l,
                        d = r + l,
                        h = .5 * d;
                    return t = l === r ? 0 : n === r ? 60 * (s - a) / c + 360 : s === r ? 60 * (a - n) / c + 120 : 60 * (n - s) / c + 240,
                        i = 0 === c ? 0 : h <= .5 ? c / d : c / (2 - d),
                        [Math.round(t) % 360, i, h, null == o ? 1 : o]
                },
                d.hsla.from = function (e) {
                    if (null == e[0] || null == e[1] || null == e[2]) return [null, null, null, e[3]];
                    var t = e[0] / 360,
                        i = e[1],
                        n = e[2],
                        a = e[3],
                        o = n <= .5 ? n * (1 + i) : n + i - n * i,
                        r = 2 * n - o;
                    return [Math.round(255 * s(r, o, t + 1 / 3)), Math.round(255 * s(r, o, t)), Math.round(255 * s(r, o, t - 1 / 3)), a]
                },
                f(d, function (n, s) {
                    var a = s.props,
                        o = s.cache,
                        l = s.to,
                        d = s.from;
                    c.fn[n] = function (n) {
                        if (l && !this[o] && (this[o] = l(this._rgba)), n === t) return this[o].slice();
                        var s, r = e.type(n),
                            h = "array" === r || "object" === r ? n : arguments,
                            u = this[o].slice();
                        return f(a, function (e, t) {
                            var n = h["object" === r ? e : t.idx];
                            null == n && (n = u[t.idx]),
                                u[t.idx] = i(n, t)
                        }),
                            d ? (s = c(d(u)), s[o] = u, s) : c(u)
                    },
                        f(a, function (t, i) {
                            c.fn[t] || (c.fn[t] = function (s) {
                                var a, o = e.type(s),
                                    l = "alpha" === t ? this._hsla ? "hsla" : "rgba" : n,
                                    c = this[l](),
                                    d = c[i.idx];
                                return "undefined" === o ? d : ("function" === o && (s = s.call(this, d), o = e.type(s)), null == s && i.empty ? this : ("string" === o && (a = r.exec(s), a && (s = d + parseFloat(a[2]) * ("+" === a[1] ? 1 : -1))), c[i.idx] = s, this[l](c)))
                            })
                        })
                }),
                c.hook = function (t) {
                    var i = t.split(" ");
                    f(i, function (t, i) {
                        e.cssHooks[i] = {
                            set: function (t, s) {
                                var a, o, r = "";
                                if ("transparent" !== s && ("string" !== e.type(s) || (a = n(s)))) {
                                    if (s = c(a || s), !u.rgba && 1 !== s._rgba[3]) {
                                        for (o = "backgroundColor" === i ? t.parentNode : t;
                                             ("" === r || "transparent" === r) && o && o.style;) try {
                                            r = e.css(o, "backgroundColor"),
                                                o = o.parentNode
                                        } catch (l) {
                                        }
                                        s = s.blend(r && "transparent" !== r ? r : "_default")
                                    }
                                    s = s.toRgbaString()
                                }
                                try {
                                    t.style[i] = s
                                } catch (l) {
                                }
                            }
                        },
                            e.fx.step[i] = function (t) {
                                t.colorInit || (t.start = c(t.elem, i), t.end = c(t.end), t.colorInit = !0),
                                    e.cssHooks[i].set(t.elem, t.start.transition(t.end, t.pos))
                            }
                    })
                },
                c.hook(o),
                e.cssHooks.borderColor = {
                    expand: function (e) {
                        var t = {};
                        return f(["Top", "Right", "Bottom", "Left"], function (i, n) {
                            t["border" + n + "Color"] = e
                        }),
                            t
                    }
                },
                a = e.Color.names = {
                    aqua: "#00ffff",
                    black: "#000000",
                    blue: "#0000ff",
                    fuchsia: "#ff00ff",
                    gray: "#808080",
                    green: "#008000",
                    lime: "#00ff00",
                    maroon: "#800000",
                    navy: "#000080",
                    olive: "#808000",
                    purple: "#800080",
                    red: "#ff0000",
                    silver: "#c0c0c0",
                    teal: "#008080",
                    white: "#ffffff",
                    yellow: "#ffff00",
                    transparent: [null, null, null, 0],
                    _default: "#ffffff"
                }
        }(jQuery),


        function () {
            function i(t) {
                var i, n, s = t.ownerDocument.defaultView ? t.ownerDocument.defaultView.getComputedStyle(t, null) : t.currentStyle,
                    a = {};
                if (s && s.length && s[0] && s[s[0]]) for (n = s.length; n--;) i = s[n],
                "string" == typeof s[i] && (a[e.camelCase(i)] = s[i]);
                else for (i in s)"string" == typeof s[i] && (a[i] = s[i]);
                return a
            }

            function n(t, i) {
                var n, s, o = {};
                for (n in i) s = i[n],
                t[n] !== s && !a[n] && (e.fx.step[n] || !isNaN(parseFloat(s))) && (o[n] = s);
                return o
            }

            var s = ["add", "remove", "toggle"],
                a = {
                    border: 1,
                    borderBottom: 1,
                    borderColor: 1,
                    borderLeft: 1,
                    borderRight: 1,
                    borderTop: 1,
                    borderWidth: 1,
                    margin: 1,
                    padding: 1
                };
            e.each(["borderLeftStyle", "borderRightStyle", "borderBottomStyle", "borderTopStyle"], function (t, i) {
                e.fx.step[i] = function (e) {
                    ("none" !== e.end && !e.setAttr || 1 === e.pos && !e.setAttr) && (jQuery.style(e.elem, i, e.end), e.setAttr = !0)
                }
            }),
            e.fn.addBack || (e.fn.addBack = function (e) {
                return this.add(null == e ? this.prevObject : this.prevObject.filter(e))
            }),
                e.effects.animateClass = function (t, a, o, r) {
                    var l = e.speed(a, o, r);
                    return this.queue(function () {
                        var a, o = e(this),
                            r = o.attr("class") || "",
                            c = l.children ? o.find("*").addBack() : o;
                        c = c.map(function () {
                            var t = e(this);
                            return {
                                el: t,
                                start: i(this)
                            }
                        }),
                            a = function () {
                                e.each(s, function (e, i) {
                                    t[i] && o[i + "Class"](t[i])
                                })
                            },
                            a(),
                            c = c.map(function () {
                                return this.end = i(this.el[0]),
                                    this.diff = n(this.start, this.end),
                                    this
                            }),
                            o.attr("class", r),
                            c = c.map(function () {
                                var t = this,
                                    i = e.Deferred(),
                                    n = e.extend({}, l, {
                                        queue: !1,
                                        complete: function () {
                                            i.resolve(t)
                                        }
                                    });
                                return this.el.animate(this.diff, n),
                                    i.promise()
                            }),
                            e.when.apply(e, c.get()).done(function () {
                                a(),
                                    e.each(arguments, function () {
                                        var t = this.el;
                                        e.each(this.diff, function (e) {
                                            t.css(e, "")
                                        })
                                    }),
                                    l.complete.call(o[0])
                            })
                    })
                },
                e.fn.extend({
                    _addClass: e.fn.addClass,
                    addClass: function (t, i, n, s) {
                        return i ? e.effects.animateClass.call(this, {
                            add: t
                        }, i, n, s) : this._addClass(t)
                    },
                    _removeClass: e.fn.removeClass,
                    removeClass: function (t, i, n, s) {
                        return arguments.length > 1 ? e.effects.animateClass.call(this, {
                            remove: t
                        }, i, n, s) : this._removeClass.apply(this, arguments)
                    },
                    _toggleClass: e.fn.toggleClass,
                    toggleClass: function (i, n, s, a, o) {
                        return "boolean" == typeof n || n === t ? s ? e.effects.animateClass.call(this, n ? {
                            add: i
                        } : {
                            remove: i
                        }, s, a, o) : this._toggleClass(i, n) : e.effects.animateClass.call(this, {
                            toggle: i
                        }, n, s, a)
                    },
                    switchClass: function (t, i, n, s, a) {
                        return e.effects.animateClass.call(this, {
                            add: i,
                            remove: t
                        }, n, s, a)
                    }
                })
        }(),


        function () {
            function n(t, i, n, s) {
                return e.isPlainObject(t) && (i = t, t = t.effect),
                    t = {
                        effect: t
                    },
                null == i && (i = {}),
                e.isFunction(i) && (s = i, n = null, i = {}),
                ("number" == typeof i || e.fx.speeds[i]) && (s = n, n = i, i = {}),
                e.isFunction(n) && (s = n, n = null),
                i && e.extend(t, i),
                    n = n || i.duration,
                    t.duration = e.fx.off ? 0 : "number" == typeof n ? n : n in e.fx.speeds ? e.fx.speeds[n] : e.fx.speeds._default,
                    t.complete = s || i.complete,
                    t
            }

            function s(t) {
                return !(t && "number" != typeof t && !e.fx.speeds[t]) || "string" == typeof t && !e.effects.effect[t]
            }

            e.extend(e.effects, {
                version: "1.10.1",
                save: function (e, t) {
                    for (var n = 0; n < t.length; n++) null !== t[n] && e.data(i + t[n], e[0].style[t[n]])
                },
                restore: function (e, n) {
                    var s, a;
                    for (a = 0; a < n.length; a++) null !== n[a] && (s = e.data(i + n[a]), s === t && (s = ""), e.css(n[a], s))
                },
                setMode: function (e, t) {
                    return "toggle" === t && (t = e.is(":hidden") ? "show" : "hide"),
                        t
                },
                getBaseline: function (e, t) {
                    var i, n;
                    switch (e[0]) {
                        case "top":
                            i = 0;
                            break;
                        case "middle":
                            i = .5;
                            break;
                        case "bottom":
                            i = 1;
                            break;
                        default:
                            i = e[0] / t.height
                    }
                    switch (e[1]) {
                        case "left":
                            n = 0;
                            break;
                        case "center":
                            n = .5;
                            break;
                        case "right":
                            n = 1;
                            break;
                        default:
                            n = e[1] / t.width
                    }
                    return {
                        x: n,
                        y: i
                    }
                },
                createWrapper: function (t) {
                    if (t.parent().is(".ui-effects-wrapper")) return t.parent();
                    var i = {
                            width: t.outerWidth(!0),
                            height: t.outerHeight(!0),
                            "float": t.css("float")
                        },
                        n = e("<div></div>").addClass("ui-effects-wrapper").css({
                            fontSize: "100%",
                            background: "transparent",
                            border: "none",
                            margin: 0,
                            padding: 0
                        }),
                        s = {
                            width: t.width(),
                            height: t.height()
                        },
                        a = document.activeElement;
                    try {
                        a.id
                    } catch (o) {
                        a = document.body
                    }
                    return t.wrap(n),
                    (t[0] === a || e.contains(t[0], a)) && e(a).focus(),
                        n = t.parent(),
                        "static" === t.css("position") ? (n.css({
                            position: "relative"
                        }), t.css({
                            position: "relative"
                        })) : (e.extend(i, {
                            position: t.css("position"),
                            zIndex: t.css("z-index")
                        }), e.each(["top", "left", "bottom", "right"], function (e, n) {
                            i[n] = t.css(n),
                            isNaN(parseInt(i[n], 10)) && (i[n] = "auto")
                        }), t.css({
                            position: "relative",
                            top: 0,
                            left: 0,
                            right: "auto",
                            bottom: "auto"
                        })),
                        t.css(s),
                        n.css(i).show()
                },
                removeWrapper: function (t) {
                    var i = document.activeElement;
                    return t.parent().is(".ui-effects-wrapper") && (t.parent().replaceWith(t), (t[0] === i || e.contains(t[0], i)) && e(i).focus()),
                        t
                },
                setTransition: function (t, i, n, s) {
                    return s = s || {},
                        e.each(i, function (e, i) {
                            var a = t.cssUnit(i);
                            a[0] > 0 && (s[i] = a[0] * n + a[1])
                        }),
                        s
                }
            }),
                e.fn.extend({
                    effect: function () {
                        function t(t) {
                            function n() {
                                e.isFunction(a) && a.call(s[0]),
                                e.isFunction(t) && t()
                            }

                            var s = e(this),
                                a = i.complete,
                                r = i.mode;
                            (s.is(":hidden") ? "hide" === r : "show" === r) ? n() : o.call(s[0], i, n)
                        }

                        var i = n.apply(this, arguments),
                            s = i.mode,
                            a = i.queue,
                            o = e.effects.effect[i.effect];
                        return e.fx.off || !o ? s ? this[s](i.duration, i.complete) : this.each(function () {
                            i.complete && i.complete.call(this)
                        }) : a === !1 ? this.each(t) : this.queue(a || "fx", t)
                    },
                    _show: e.fn.show,
                    show: function (e) {
                        if (s(e)) return this._show.apply(this, arguments);
                        var t = n.apply(this, arguments);
                        return t.mode = "show",
                            this.effect.call(this, t)
                    },
                    _hide: e.fn.hide,
                    hide: function (e) {
                        if (s(e)) return this._hide.apply(this, arguments);
                        var t = n.apply(this, arguments);
                        return t.mode = "hide",
                            this.effect.call(this, t)
                    },
                    __toggle: e.fn.toggle,
                    toggle: function (t) {
                        if (s(t) || "boolean" == typeof t || e.isFunction(t)) return this.__toggle.apply(this, arguments);
                        var i = n.apply(this, arguments);
                        return i.mode = "toggle",
                            this.effect.call(this, i)
                    },
                    cssUnit: function (t) {
                        var i = this.css(t),
                            n = [];
                        return e.each(["em", "px", "%", "pt"], function (e, t) {
                            i.indexOf(t) > 0 && (n = [parseFloat(i), t])
                        }),
                            n
                    }
                })
        }(),


        function () {
            var t = {};
            e.each(["Quad", "Cubic", "Quart", "Quint", "Expo"], function (e, i) {
                t[i] = function (t) {
                    return Math.pow(t, e + 2)
                }
            }),
                e.extend(t, {
                    Sine: function (e) {
                        return 1 - Math.cos(e * Math.PI / 2)
                    },
                    Circ: function (e) {
                        return 1 - Math.sqrt(1 - e * e)
                    },
                    Elastic: function (e) {
                        return 0 === e || 1 === e ? e : -Math.pow(2, 8 * (e - 1)) * Math.sin((80 * (e - 1) - 7.5) * Math.PI / 15)
                    },
                    Back: function (e) {
                        return e * e * (3 * e - 2)
                    },
                    Bounce: function (e) {
                        for (var t, i = 4; e < ((t = Math.pow(2, --i)) - 1) / 11;);
                        return 1 / Math.pow(4, 3 - i) - 7.5625 * Math.pow((3 * t - 2) / 22 - e, 2)
                    }
                }),
                e.each(t, function (t, i) {
                    e.easing["easeIn" + t] = i,
                        e.easing["easeOut" + t] = function (e) {
                            return 1 - i(1 - e)
                        },
                        e.easing["easeInOut" + t] = function (e) {
                            return e < .5 ? i(2 * e) / 2 : 1 - i(e * -2 + 2) / 2
                        }
                })
        }()
}(jQuery),


    function (e, t) {
        var i = /up|down|vertical/,
            n = /up|left|vertical|horizontal/;
        e.effects.effect.blind = function (t, s) {
            var a, o, r, l = e(this),
                c = ["position", "top", "bottom", "left", "right", "height", "width"],
                d = e.effects.setMode(l, t.mode || "hide"),
                h = t.direction || "up",
                u = i.test(h),
                p = u ? "height" : "width",
                f = u ? "top" : "left",
                m = n.test(h),
                g = {},
                v = "show" === d;
            l.parent().is(".ui-effects-wrapper") ? e.effects.save(l.parent(), c) : e.effects.save(l, c),
                l.show(),
                a = e.effects.createWrapper(l).css({
                    overflow: "hidden"
                }),
                o = a[p](),
                r = parseFloat(a.css(f)) || 0,
                g[p] = v ? o : 0,
            m || (l.css(u ? "bottom" : "right", 0).css(u ? "top" : "left", "auto").css({
                position: "absolute"
            }), g[f] = v ? r : o + r),
            v && (a.css(p, 0), m || a.css(f, r + o)),
                a.animate(g, {
                    duration: t.duration,
                    easing: t.easing,
                    queue: !1,
                    complete: function () {
                        "hide" === d && l.hide(),
                            e.effects.restore(l, c),
                            e.effects.removeWrapper(l),
                            s()
                    }
                })
        }
    }(jQuery),


    function (e, t) {
        e.effects.effect.bounce = function (t, i) {
            var n, s, a, o = e(this),
                r = ["position", "top", "bottom", "left", "right", "height", "width"],
                l = e.effects.setMode(o, t.mode || "effect"),
                c = "hide" === l,
                d = "show" === l,
                h = t.direction || "up",
                u = t.distance,
                p = t.times || 5,
                f = 2 * p + (d || c ? 1 : 0),
                m = t.duration / f,
                g = t.easing,
                v = "up" === h || "down" === h ? "top" : "left",
                y = "up" === h || "left" === h,
                b = o.queue(),
                w = b.length;
            for ((d || c) && r.push("opacity"), e.effects.save(o, r), o.show(), e.effects.createWrapper(o), u || (u = o["top" === v ? "outerHeight" : "outerWidth"]() / 3), d && (a = {
                opacity: 1
            }, a[v] = 0, o.css("opacity", 0).css(v, y ? 2 * -u : 2 * u).animate(a, m, g)), c && (u /= Math.pow(2, p - 1)), a = {}, a[v] = 0, n = 0; n < p; n++) s = {},
                s[v] = (y ? "-=" : "+=") + u,
                o.animate(s, m, g).animate(a, m, g),
                u = c ? 2 * u : u / 2;
            c && (s = {
                opacity: 0
            }, s[v] = (y ? "-=" : "+=") + u, o.animate(s, m, g)),
                o.queue(function () {
                    c && o.hide(),
                        e.effects.restore(o, r),
                        e.effects.removeWrapper(o),
                        i()
                }),
            w > 1 && b.splice.apply(b, [1, 0].concat(b.splice(w, f + 1))),
                o.dequeue()
        }
    }(jQuery),


    function (e, t) {
        e.effects.effect.clip = function (t, i) {
            var n, s, a, o = e(this),
                r = ["position", "top", "bottom", "left", "right", "height", "width"],
                l = e.effects.setMode(o, t.mode || "hide"),
                c = "show" === l,
                d = t.direction || "vertical",
                h = "vertical" === d,
                u = h ? "height" : "width",
                p = h ? "top" : "left",
                f = {};
            e.effects.save(o, r),
                o.show(),
                n = e.effects.createWrapper(o).css({
                    overflow: "hidden"
                }),
                s = "IMG" === o[0].tagName ? n : o,
                a = s[u](),
            c && (s.css(u, 0), s.css(p, a / 2)),
                f[u] = c ? a : 0,
                f[p] = c ? 0 : a / 2,
                s.animate(f, {
                    queue: !1,
                    duration: t.duration,
                    easing: t.easing,
                    complete: function () {
                        c || o.hide(),
                            e.effects.restore(o, r),
                            e.effects.removeWrapper(o),
                            i()
                    }
                })
        }
    }(jQuery),


    function (e, t) {
        e.effects.effect.drop = function (t, i) {
            var n, s = e(this),
                a = ["position", "top", "bottom", "left", "right", "opacity", "height", "width"],
                o = e.effects.setMode(s, t.mode || "hide"),
                r = "show" === o,
                l = t.direction || "left",
                c = "up" === l || "down" === l ? "top" : "left",
                d = "up" === l || "left" === l ? "pos" : "neg",
                h = {
                    opacity: r ? 1 : 0
                };
            e.effects.save(s, a),
                s.show(),
                e.effects.createWrapper(s),
                n = t.distance || s["top" === c ? "outerHeight" : "outerWidth"](!0) / 2,
            r && s.css("opacity", 0).css(c, "pos" === d ? -n : n),
                h[c] = (r ? "pos" === d ? "+=" : "-=" : "pos" === d ? "-=" : "+=") + n,
                s.animate(h, {
                    queue: !1,
                    duration: t.duration,
                    easing: t.easing,
                    complete: function () {
                        "hide" === o && s.hide(),
                            e.effects.restore(s, a),
                            e.effects.removeWrapper(s),
                            i()
                    }
                })
        }
    }(jQuery),


    function (e, t) {
        e.effects.effect.explode = function (t, i) {
            function n() {
                b.push(this),
                b.length === h * u && s()
            }

            function s() {
                p.css({
                    visibility: "visible"
                }),
                    e(b).remove(),
                m || p.hide(),
                    i()
            }

            var a, o, r, l, c, d, h = t.pieces ? Math.round(Math.sqrt(t.pieces)) : 3,
                u = h,
                p = e(this),
                f = e.effects.setMode(p, t.mode || "hide"),
                m = "show" === f,
                g = p.show().css("visibility", "hidden").offset(),
                v = Math.ceil(p.outerWidth() / u),
                y = Math.ceil(p.outerHeight() / h),
                b = [];
            for (a = 0; a < h; a++) for (l = g.top + a * y, d = a - (h - 1) / 2, o = 0; o < u; o++) r = g.left + o * v,
                c = o - (u - 1) / 2,
                p.clone().appendTo("body").wrap("<div></div>").css({
                    position: "absolute",
                    visibility: "visible",
                    left: -o * v,
                    top: -a * y
                }).parent().addClass("ui-effects-explode").css({
                    position: "absolute",
                    overflow: "hidden",
                    width: v,
                    height: y,
                    left: r + (m ? c * v : 0),
                    top: l + (m ? d * y : 0),
                    opacity: m ? 0 : 1
                }).animate({
                    left: r + (m ? 0 : c * v),
                    top: l + (m ? 0 : d * y),
                    opacity: m ? 1 : 0
                }, t.duration || 500, t.easing, n)
        }
    }(jQuery),


    function (e, t) {
        e.effects.effect.fade = function (t, i) {
            var n = e(this),
                s = e.effects.setMode(n, t.mode || "toggle");
            n.animate({
                opacity: s
            }, {
                queue: !1,
                duration: t.duration,
                easing: t.easing,
                complete: i
            })
        }
    }(jQuery),


    function (e, t) {
        e.effects.effect.fold = function (t, i) {
            var n, s, a = e(this),
                o = ["position", "top", "bottom", "left", "right", "height", "width"],
                r = e.effects.setMode(a, t.mode || "hide"),
                l = "show" === r,
                c = "hide" === r,
                d = t.size || 15,
                h = /([0-9]+)%/.exec(d),
                u = !!t.horizFirst,
                p = l !== u,
                f = p ? ["width", "height"] : ["height", "width"],
                m = t.duration / 2,
                g = {},
                v = {};
            e.effects.save(a, o),
                a.show(),
                n = e.effects.createWrapper(a).css({
                    overflow: "hidden"
                }),
                s = p ? [n.width(), n.height()] : [n.height(), n.width()],
            h && (d = parseInt(h[1], 10) / 100 * s[c ? 0 : 1]),
            l && n.css(u ? {
                height: 0,
                width: d
            } : {
                height: d,
                width: 0
            }),
                g[f[0]] = l ? s[0] : d,
                v[f[1]] = l ? s[1] : 0,
                n.animate(g, m, t.easing).animate(v, m, t.easing, function () {
                    c && a.hide(),
                        e.effects.restore(a, o),
                        e.effects.removeWrapper(a),
                        i()
                })
        }
    }(jQuery),


    function (e, t) {
        e.effects.effect.highlight = function (t, i) {
            var n = e(this),
                s = ["backgroundImage", "backgroundColor", "opacity"],
                a = e.effects.setMode(n, t.mode || "show"),
                o = {
                    backgroundColor: n.css("backgroundColor")
                };
            "hide" === a && (o.opacity = 0),
                e.effects.save(n, s),
                n.show().css({
                    backgroundImage: "none",
                    backgroundColor: t.color || "#ffff99"
                }).animate(o, {
                    queue: !1,
                    duration: t.duration,
                    easing: t.easing,
                    complete: function () {
                        "hide" === a && n.hide(),
                            e.effects.restore(n, s),
                            i()
                    }
                })
        }
    }(jQuery),


    function (e, t) {
        e.effects.effect.pulsate = function (t, i) {
            var n, s = e(this),
                a = e.effects.setMode(s, t.mode || "show"),
                o = "show" === a,
                r = "hide" === a,
                l = o || "hide" === a,
                c = 2 * (t.times || 5) + (l ? 1 : 0),
                d = t.duration / c,
                h = 0,
                u = s.queue(),
                p = u.length;
            for (!o && s.is(":visible") || (s.css("opacity", 0).show(), h = 1), n = 1; n < c; n++) s.animate({
                opacity: h
            }, d, t.easing),
                h = 1 - h;
            s.animate({
                opacity: h
            }, d, t.easing),
                s.queue(function () {
                    r && s.hide(),
                        i()
                }),
            p > 1 && u.splice.apply(u, [1, 0].concat(u.splice(p, c + 1))),
                s.dequeue()
        }
    }(jQuery),


    function (e, t) {
        e.effects.effect.puff = function (t, i) {
            var n = e(this),
                s = e.effects.setMode(n, t.mode || "hide"),
                a = "hide" === s,
                o = parseInt(t.percent, 10) || 150,
                r = o / 100,
                l = {
                    height: n.height(),
                    width: n.width(),
                    outerHeight: n.outerHeight(),
                    outerWidth: n.outerWidth()
                };
            e.extend(t, {
                effect: "scale",
                queue: !1,
                fade: !0,
                mode: s,
                complete: i,
                percent: a ? o : 100,
                from: a ? l : {
                    height: l.height * r,
                    width: l.width * r,
                    outerHeight: l.outerHeight * r,
                    outerWidth: l.outerWidth * r
                }
            }),
                n.effect(t)
        },
            e.effects.effect.scale = function (t, i) {
                var n = e(this),
                    s = e.extend(!0, {}, t),
                    a = e.effects.setMode(n, t.mode || "effect"),
                    o = parseInt(t.percent, 10) || (0 === parseInt(t.percent, 10) ? 0 : "hide" === a ? 0 : 100),
                    r = t.direction || "both",
                    l = t.origin,
                    c = {
                        height: n.height(),
                        width: n.width(),
                        outerHeight: n.outerHeight(),
                        outerWidth: n.outerWidth()
                    },
                    d = {
                        y: "horizontal" !== r ? o / 100 : 1,
                        x: "vertical" !== r ? o / 100 : 1
                    };
                s.effect = "size",
                    s.queue = !1,
                    s.complete = i,
                "effect" !== a && (s.origin = l || ["middle", "center"], s.restore = !0),
                    s.from = t.from || ("show" === a ? {
                            height: 0,
                            width: 0,
                            outerHeight: 0,
                            outerWidth: 0
                        } : c),
                    s.to = {
                        height: c.height * d.y,
                        width: c.width * d.x,
                        outerHeight: c.outerHeight * d.y,
                        outerWidth: c.outerWidth * d.x
                    },
                s.fade && ("show" === a && (s.from.opacity = 0, s.to.opacity = 1), "hide" === a && (s.from.opacity = 1, s.to.opacity = 0)),
                    n.effect(s)
            },
            e.effects.effect.size = function (t, i) {
                var n, s, a, o = e(this),
                    r = ["position", "top", "bottom", "left", "right", "width", "height", "overflow", "opacity"],
                    l = ["position", "top", "bottom", "left", "right", "overflow", "opacity"],
                    c = ["width", "height", "overflow"],
                    d = ["fontSize"],
                    h = ["borderTopWidth", "borderBottomWidth", "paddingTop", "paddingBottom"],
                    u = ["borderLeftWidth", "borderRightWidth", "paddingLeft", "paddingRight"],
                    p = e.effects.setMode(o, t.mode || "effect"),
                    f = t.restore || "effect" !== p,
                    m = t.scale || "both",
                    g = t.origin || ["middle", "center"],
                    v = o.css("position"),
                    y = f ? r : l,
                    b = {
                        height: 0,
                        width: 0,
                        outerHeight: 0,
                        outerWidth: 0
                    };
                "show" === p && o.show(),
                    n = {
                        height: o.height(),
                        width: o.width(),
                        outerHeight: o.outerHeight(),
                        outerWidth: o.outerWidth()
                    },
                    "toggle" === t.mode && "show" === p ? (o.from = t.to || b, o.to = t.from || n) : (o.from = t.from || ("show" === p ? b : n), o.to = t.to || ("hide" === p ? b : n)),
                    a = {
                        from: {
                            y: o.from.height / n.height,
                            x: o.from.width / n.width
                        },
                        to: {
                            y: o.to.height / n.height,
                            x: o.to.width / n.width
                        }
                    },
                "box" !== m && "both" !== m || (a.from.y !== a.to.y && (y = y.concat(h), o.from = e.effects.setTransition(o, h, a.from.y, o.from), o.to = e.effects.setTransition(o, h, a.to.y, o.to)), a.from.x !== a.to.x && (y = y.concat(u), o.from = e.effects.setTransition(o, u, a.from.x, o.from), o.to = e.effects.setTransition(o, u, a.to.x, o.to))),
                ("content" === m || "both" === m) && a.from.y !== a.to.y && (y = y.concat(d).concat(c), o.from = e.effects.setTransition(o, d, a.from.y, o.from), o.to = e.effects.setTransition(o, d, a.to.y, o.to)),
                    e.effects.save(o, y),
                    o.show(),
                    e.effects.createWrapper(o),
                    o.css("overflow", "hidden").css(o.from),
                g && (s = e.effects.getBaseline(g, n), o.from.top = (n.outerHeight - o.outerHeight()) * s.y, o.from.left = (n.outerWidth - o.outerWidth()) * s.x, o.to.top = (n.outerHeight - o.to.outerHeight) * s.y, o.to.left = (n.outerWidth - o.to.outerWidth) * s.x),
                    o.css(o.from),
                "content" !== m && "both" !== m || (h = h.concat(["marginTop", "marginBottom"]).concat(d), u = u.concat(["marginLeft", "marginRight"]), c = r.concat(h).concat(u), o.find("*[width]").each(function () {
                    var i = e(this),
                        n = {
                            height: i.height(),
                            width: i.width(),
                            outerHeight: i.outerHeight(),
                            outerWidth: i.outerWidth()
                        };
                    f && e.effects.save(i, c),
                        i.from = {
                            height: n.height * a.from.y,
                            width: n.width * a.from.x,
                            outerHeight: n.outerHeight * a.from.y,
                            outerWidth: n.outerWidth * a.from.x
                        },
                        i.to = {
                            height: n.height * a.to.y,
                            width: n.width * a.to.x,
                            outerHeight: n.height * a.to.y,
                            outerWidth: n.width * a.to.x
                        },
                    a.from.y !== a.to.y && (i.from = e.effects.setTransition(i, h, a.from.y, i.from), i.to = e.effects.setTransition(i, h, a.to.y, i.to)),
                    a.from.x !== a.to.x && (i.from = e.effects.setTransition(i, u, a.from.x, i.from), i.to = e.effects.setTransition(i, u, a.to.x, i.to)),
                        i.css(i.from),
                        i.animate(i.to, t.duration, t.easing, function () {
                            f && e.effects.restore(i, c)
                        })
                })),
                    o.animate(o.to, {
                        queue: !1,
                        duration: t.duration,
                        easing: t.easing,
                        complete: function () {
                            0 === o.to.opacity && o.css("opacity", o.from.opacity),
                            "hide" === p && o.hide(),
                                e.effects.restore(o, y),
                            f || ("static" === v ? o.css({
                                position: "relative",
                                top: o.to.top,
                                left: o.to.left
                            }) : e.each(["top", "left"], function (e, t) {
                                o.css(t, function (t, i) {
                                    var n = parseInt(i, 10),
                                        s = e ? o.to.left : o.to.top;
                                    return "auto" === i ? s + "px" : n + s + "px"
                                })
                            })),
                                e.effects.removeWrapper(o),
                                i()
                        }
                    })
            }
    }(jQuery),


    function (e, t) {
        e.effects.effect.shake = function (t, i) {
            var n, s = e(this),
                a = ["position", "top", "bottom", "left", "right", "height", "width"],
                o = e.effects.setMode(s, t.mode || "effect"),
                r = t.direction || "left",
                l = t.distance || 20,
                c = t.times || 3,
                d = 2 * c + 1,
                h = Math.round(t.duration / d),
                u = "up" === r || "down" === r ? "top" : "left",
                p = "up" === r || "left" === r,
                f = {},
                m = {},
                g = {},
                v = s.queue(),
                y = v.length;
            for (e.effects.save(s, a), s.show(), e.effects.createWrapper(s), f[u] = (p ? "-=" : "+=") + l, m[u] = (p ? "+=" : "-=") + 2 * l, g[u] = (p ? "-=" : "+=") + 2 * l, s.animate(f, h, t.easing), n = 1; n < c; n++) s.animate(m, h, t.easing).animate(g, h, t.easing);
            s.animate(m, h, t.easing).animate(f, h / 2, t.easing).queue(function () {
                "hide" === o && s.hide(),
                    e.effects.restore(s, a),
                    e.effects.removeWrapper(s),
                    i()
            }),
            y > 1 && v.splice.apply(v, [1, 0].concat(v.splice(y, d + 1))),
                s.dequeue()
        }
    }(jQuery),


    function (e, t) {
        e.effects.effect.slide = function (t, i) {
            var n, s = e(this),
                a = ["position", "top", "bottom", "left", "right", "width", "height"],
                o = e.effects.setMode(s, t.mode || "show"),
                r = "show" === o,
                l = t.direction || "left",
                c = "up" === l || "down" === l ? "top" : "left",
                d = "up" === l || "left" === l,
                h = {};
            e.effects.save(s, a),
                s.show(),
                n = t.distance || s["top" === c ? "outerHeight" : "outerWidth"](!0),
                e.effects.createWrapper(s).css({
                    overflow: "hidden"
                }),
            r && s.css(c, d ? isNaN(n) ? "-" + n : -n : n),
                h[c] = (r ? d ? "+=" : "-=" : d ? "-=" : "+=") + n,
                s.animate(h, {
                    queue: !1,
                    duration: t.duration,
                    easing: t.easing,
                    complete: function () {
                        "hide" === o && s.hide(),
                            e.effects.restore(s, a),
                            e.effects.removeWrapper(s),
                            i()
                    }
                })
        }
    }(jQuery),


    function (e, t) {
        e.effects.effect.transfer = function (t, i) {
            var n = e(this),
                s = e(t.to),
                a = "fixed" === s.css("position"),
                o = e("body"),
                r = a ? o.scrollTop() : 0,
                l = a ? o.scrollLeft() : 0,
                c = s.offset(),
                d = {
                    top: c.top - r,
                    left: c.left - l,
                    height: s.innerHeight(),
                    width: s.innerWidth()
                },
                h = n.offset(),
                u = e("<div class='ui-effects-transfer'></div>").appendTo(document.body).addClass(t.className).css({
                    top: h.top - r,
                    left: h.left - l,
                    height: n.innerHeight(),
                    width: n.innerWidth(),
                    position: a ? "fixed" : "absolute"
                }).animate(d, t.duration, t.easing, function () {
                    u.remove(),
                        i()
                })
        }
    }(jQuery),


    function (e) {
        function t(e, t) {
            if (!(e.originalEvent.touches.length > 1)) {
                e.preventDefault();
                var i = e.originalEvent.changedTouches[0],
                    n = document.createEvent("MouseEvents");
                n.initMouseEvent(t, !0, !0, window, 1, i.screenX, i.screenY, i.clientX, i.clientY, !1, !1, !1, !1, 0, null),
                    e.target.dispatchEvent(n)
            }
        }

        if (e.support.touch = "ontouchend" in document, e.support.touch) {
            var i, n = e.ui.mouse.prototype,
                s = n._mouseInit;
            n._touchStart = function (e) {
                var n = this;
                !i && n._mouseCapture(e.originalEvent.changedTouches[0]) && (i = !0, n._touchMoved = !1, t(e, "mouseover"), t(e, "mousemove"), t(e, "mousedown"))
            },
                n._touchMove = function (e) {
                    i && (this._touchMoved = !0, t(e, "mousemove"))
                },
                n._touchEnd = function (e) {
                    i && (t(e, "mouseup"), t(e, "mouseout"), this._touchMoved || t(e, "click"), i = !1)
                },
                n._mouseInit = function () {
                    var t = this;
                    t.element.bind("touchstart", e.proxy(t, "_touchStart")).bind("touchmove", e.proxy(t, "_touchMove")).bind("touchend", e.proxy(t, "_touchEnd")),
                        s.call(t)
                }
        }
    }(jQuery),
    !
        function (e, t, i) {
            "use strict";
            e.fn.scrollUp = function (t) {
                e.data(i.body, "scrollUp") || (e.data(i.body, "scrollUp", !0), e.fn.scrollUp.init(t))
            },
                e.fn.scrollUp.init = function (n) {
                    var s, a, o, r, l, c, d, h = e.fn.scrollUp.settings = e.extend({}, e.fn.scrollUp.defaults, n),
                        u = !1;
                    switch (d = h.scrollTrigger ? e(h.scrollTrigger) : e("<a/>", {
                        id: h.scrollName,
                        href: "#top"
                    }), h.scrollTitle && d.attr("title", h.scrollTitle), d.appendTo("body"), h.scrollImg || h.scrollTrigger || d.html(h.scrollText), d.css({
                        display: "none",
                        position: "fixed",
                        zIndex: h.zIndex
                    }), h.activeOverlay && e("<div/>", {
                        id: h.scrollName + "-active"
                    }).css({
                        position: "absolute",
                        top: h.scrollDistance + "px",
                        width: "100%",
                        borderTop: "1px dotted" + h.activeOverlay,
                        zIndex: h.zIndex
                    }).appendTo("body"), h.animation) {
                        case "fade":
                            s = "fadeIn",
                                a = "fadeOut",
                                o = h.animationSpeed;
                            break;
                        case "slide":
                            s = "slideDown",
                                a = "slideUp",
                                o = h.animationSpeed;
                            break;
                        default:
                            s = "show",
                                a = "hide",
                                o = 0
                    }
                    r = "top" === h.scrollFrom ? h.scrollDistance : e(i).height() - e(t).height() - h.scrollDistance,
                        l = e(t).scroll(function () {
                            e(t).scrollTop() > r ? u || (d[s](o), u = !0) : u && (d[a](o), u = !1)
                        }),
                        h.scrollTarget ? "number" == typeof h.scrollTarget ? c = h.scrollTarget : "string" == typeof h.scrollTarget && (c = Math.floor(e(h.scrollTarget).offset().top)) : c = 0,
                        d.click(function (t) {
                            t.preventDefault(),
                                e("html, body").animate({
                                    scrollTop: c
                                }, h.scrollSpeed, h.easingType)
                        })
                },
                e.fn.scrollUp.defaults = {
                    scrollName: "scrollUp",
                    scrollDistance: 300,
                    scrollFrom: "top",
                    scrollSpeed: 300,
                    easingType: "linear",
                    animation: "fade",
                    animationSpeed: 200,
                    scrollTrigger: !1,
                    scrollTarget: !1,
                    scrollText: "Scroll to top",
                    scrollTitle: !1,
                    scrollImg: !1,
                    activeOverlay: !1,
                    zIndex: 2147483647
                },
                e.fn.scrollUp.destroy = function (n) {
                    e.removeData(i.body, "scrollUp"),
                        e("#" + e.fn.scrollUp.settings.scrollName).remove(),
                        e("#" + e.fn.scrollUp.settings.scrollName + "-active").remove(),
                        e.fn.jquery.split(".")[1] >= 7 ? e(t).off("scroll", n) : e(t).unbind("scroll", n)
                },
                e.scrollUp = e.fn.scrollUp
        }(jQuery, window, document);
var handler = function (e) {
    e.preventDefault()
};
$(function (e) {
    var t;
    e(".con-filter-div .swiper-scroll").css("max-height", e(window).height()),
    e(".swiper-scroll").hasClass("swiper-scroll") && swiper_scroll(),
        e(".ect-header-banner i.icon-guanbi1").click(function () {
            e(".ect-header-banner").hide()
        });
    var n = e(".j-input-text"),
        s = e(".j-text-all").find(".j-is-null"),
        a = e(".j-text-all").find(".j-yanjing");
    n.bind("focus", function () {
        s.removeClass("active"),
        "" != e(this).val() && e(this).siblings(".j-is-null").addClass("active")
    }),
        n.bind("input", function () {
            "" == e(this).val() ? e(this).siblings(".j-is-null").removeClass("active") : e(this).siblings(".j-is-null").addClass("active")
        }),
        s.click(function () {
            e(this).siblings(".j-input-text").val(""),
                e(this).siblings(".j-input-text").focus()
        }),
        a.click(function () {
            input_text_atr = e(this).siblings(".input-text").find(".j-input-text"),
                "password" == input_text_atr.attr("type") && e(this).hasClass("disabled") ? input_text_atr.attr("type", "text") : input_text_atr.attr("type", "password"),
                input_text_atr.focus(),
                e(this).toggleClass("disabled")
        });
    var o = ["icon-icon-square", "icon-pailie", "icon-viewlist"],
        r = ["product-list-big", "product-list-medium", "product-list-small"];
    e(".j-a-sequence").click(function () {
        var t = e(this).find("i").attr("data"),
            i = o.length,
            n = t;
        t++,
        t >= i && (t = 0),
            e(this).find(".iconfont").removeClass(o[n]).addClass(o[t]),
            e(this).find(".iconfont").attr("data", t),
            e(".j-product-list").removeClass(r[n]).addClass(r[t]),
            e(".j-product-list").attr("data", t)
    }),
        e(".j-search-check").click(function () {
            1 == e(this).attr("data") ? (e(this).attr("data", 2).find("span").html("商品"), e("input[name=type_select]").val(2)) : (e(this).attr("data", 1).find("span").html("店铺"), e("input[name=type_select]").val(1))
        }),
        e(".j-sub-menu").hide(),
        e(".j-get-city-one, .select-two").on("click", "a.j-menu-select", function () {
            e(this).next(".j-sub-menu").slideToggle().siblings(".j-sub-menu").slideUp(),
                e(this).toggleClass("active").siblings().removeClass("active");
            new Swiper(".swiper-scroll", {
                scrollbar: !1,
                direction: "vertical",
                slidesPerView: "auto",
                mousewheelControl: !0,
                freeMode: !0
            })
        });
    var l = !0,
        c = [];
    e(".j-get-limit .ect-select").not(".j-checkbox-all").click(function () {
        get_text = e(this).parents(".j-get-limit"),
            s_t_em_value = get_text.prev(".select-title").find(".t-jiantou em"),
            checked = e(this).find("label").hasClass("active"),
            l = e(this).parents(".j-get-limit").attr("data-istrue");
        var t = "",
            n = 0,
            s = "",
            a = get_text.prev(".j-menu-select").find(".j-t-jiantou");
        a.addClass("active"),
        get_text.find(".j-checkbox-all label").hasClass("active") && get_text.find(".j-checkbox-all label").removeClass("active"),
        "true" == l && e(this).find("label").toggleClass("active"),
        checked && (e(this).find("label").removeClass("active"), e(this).parents(".j-get-limit").attr("data-istrue", "true")),
        "false" == l && d_messages("筛选最多不能超过5个"),
            s_get_label = get_text.find("label.active"),
            n = s_get_label.length,
        n <= 0 && (a.removeClass("active"), e(".j-checkbox-all label").addClass("active"), t = e(this).siblings(".j-checkbox-all").find("label").text() + "、"),
            n >= 5 ? e(this).parents(".j-get-limit").attr("data-istrue", "false") : e(this).parents(".j-get-limit").attr("data-istrue", "true"),
            s_get_label.each(function () {
                t += e(this).text() + "、",
                e(this).parents("ul").hasClass("brand") && (s += e(this).parent("li").attr("data-brand") + ",")
            }),
            s_t_em_value.text(t.substring(0, t.length - 1)),
        "" != s && (s = s.substring(0, s.length - 1), e("input[name=brand]").val(s));
        var o = {};
        o.key = get_text.attr("data-key") || -1;
        var r = "";
        if (get_text.find("label").each(function () {
                e(this).parents("ul").hasClass("filter_attr") && e(this).hasClass("active") && (r += e(this).parent("li").attr("data-attr") + ",")
            }), o.val = r.substring(0, r.length - 1), c[o.key] = o, c) {
            var d = "";
            for (i in c) void 0 != typeof c[i].val && "" != c[i].val && (d += c[i].key + "-" + c[i].val + ".");
            d && (d = d.substring(0, d.length - 1)),
                e("input[name=filter_attr]").val(d)
        }
    }),
        e(".j-checkbox-all").click(function () {
            checkbox_all = e(this).find("label"),
                s_t_em_value = e(this).parent().prev(".select-title").find(".t-jiantou em"),
                checkbox_all_text = e(this).find("label").text(),
            checkbox_all.hasClass("active") || (e(this).find("label").addClass("active").parents(".ect-select").siblings().find("label").removeClass("active"), s_t_em_value.text(checkbox_all_text), e(this).parent(".j-get-limit").prev(".select-title").find(".t-jiantou").removeClass("active"), e(this).parents(".j-get-limit").attr("data-istrue", "true"));
            var t = {};
            t.key = e(this).parent().attr("data-key") || -1;
            var n = "";
            if (e(this).parent().find("label").each(function () {
                    e(this).parent("ul").hasClass("filter_attr") && e(this).hasClass("active") && (n += e(this).parent("li").attr("data-attr") + ",")
                }), t.val = n.substring(0, n.length - 1), c[t.key] = t, c) {
                var s = "";
                for (i in c) void 0 != typeof c[i].val && "" != c[i].val && (s += c[i].key + "-" + c[i].val + ".");
                s && (s = s.substring(0, s.length - 1)),
                    e("input[name=filter_attr]").val(s)
            }
            e("input[name=brand]").val(0)
        }),
        e(".j-filter-reset").click(function () {
            e(".con-filter-div label").removeClass("active"),
                e(".j-checkbox-all label").addClass("active"),
                e(".j-radio-switching").removeClass("active"),
                e(".j-menu-select .j-t-jiantou").removeClass("active"),
                e(".j-menu-select .j-t-jiantou em").text("全部"),
                e(".j-filter-city span.text-all-span").css("color", "#555"),
                e(".j-filter-city span.text-all-span").text("请选择"),
                e("#slider-range a:first").css("left", 0),
                e("#slider-range a:last").css("left", "100%"),
                e(".ui-widget-header").css({
                    left: 0,
                    width: "100%"
                });
            var t = e(".price-range-label").attr("data-min") + "~" + e(".price-range-label").attr("data-max");
            e("#slider-range-amount").text(t),
                e(this).parents(".j-get-limit").attr("data-istrue", !0),
                l = !0,
                e(".j-checkbox-all label").each(function () {
                    e(this).parents("ul").prev().find("span").removeClass("active"),
                        e(this).parents("ul").prev().find("span em").text(e(this).text())
                }),
                e("input[name=brand]").val(0),
                e("input[name=filter_attr]").val(0),
                e("input[name=isself]").val(0),
                e("input[name=price_min]").val(e(".price-range-label").attr("data-min")),
                e("input[name=price_max]").val(e(".price-range-label").attr("data-max")),
                e("input[name=isself]").val(0)
        }),
        e(".j-get-more .ect-select").click(function () {
            e(this).find("label").hasClass("active") ? (e(this).find("label").removeClass("active"), e(this).find("label").hasClass("label-all") && e(".j-select-all").find(".ect-select label").removeClass("active"), e(this).hasClass("list-select") && (e(this).hasClass("hasgoods") && e("input[name=hasgoods]").val(0), e(this).hasClass("promotion") && e("input[name=promotion]").val(0))) : (e(this).find("label").addClass("active"), e(this).find("label").hasClass("label-all") && e(".j-select-all").find(".ect-select label").addClass("active"), e(this).hasClass("list-select") && (e(this).hasClass("hasgoods") && e("input[name=hasgoods]").val(1), e(this).hasClass("promotion") && e("input[name=promotion]").val(1)))
        }),
        e(".j-get-i-more .j-select-btn").click(function () {
            if (e(this).parents(".ect-select").hasClass("j-flowcoupon-select-disab")) d_messages("同商家只能选择一个", 2);
            else {
                is_select_all = !0,
                e(this).parent("label").hasClass("label-this-all") && (e(this).parent("label").hasClass("active") ? e(this).parents(".j-get-i-more").find(".ect-select label").removeClass("active") : e(this).parents(".j-get-i-more").find(".ect-select label").addClass("active")),
                e(this).parent("label").hasClass("label-this-all") || e(this).parent("label").hasClass("label-all") || (e(this).parent("label").toggleClass("active"), is_select_this_all = !0, select_this_all = e(this).parents(".j-get-i-more").find(".ect-select label").not(".label-this-all"), select_this_all.each(function () {
                    if (!e(this).hasClass("active")) return is_select_this_all = !1,
                        !1
                }), is_select_this_all ? e(this).parents(".j-get-i-more").find(".label-this-all").addClass("active") : e(this).parents(".j-get-i-more").find(".label-this-all").removeClass("active"));
                var t = e(".j-select-all").find(".ect-select label");
                t.each(function () {
                    if (!e(this).hasClass("active")) return is_select_all = !1,
                        !1
                }),
                    is_select_all ? e(".label-all").addClass("active") : e(".label-all").removeClass("active")
            }
        }),
        e("body").on("click", ".j-get-one .ect-select", function () {
            if (get_tjiantou = e(this).parent(".j-get-one").prev(".select-title").find(".t-jiantou"), e(this).find("label").addClass("active").parent(".ect-select").siblings().find("label").removeClass("active"), get_tjiantou.find("em").text(e(this).find("label").text()), e(this).hasClass("j-checkbox-all") ? get_tjiantou.removeClass("active") : get_tjiantou.addClass("active"), e(this).parents("show-goods-attr")) {
                s_get_label = e(".show-goods-attr .s-g-attr-con").find("label.active");
                var t = "";
                s_get_label.each(function () {
                    t += e(this).text() + "、"
                });
                var i = e("#goods_number").val();
                i = parseInt(i) ? parseInt(i) : 1,
                    t = t + i + "个",
                    e(".j-goods-attr").find(".t-goods1").text(t)
            }
        }),
        e(".j-flow-site .ect-select").click(function () {
            site_h4_text = e(this).find("h4").text(),
                e(this).parents(".j-goods-site-li").find(".t-goods1 span").text(site_h4_text)
        }),
        e(".j-get-consignee-one label").click(function () {
            e(this).addClass("active").parents(".flow-checkout-adr").siblings().find("label").removeClass("active")
        }),
        e(".j-flow-get-consignee .flow-checkout-adr").click(function () {
            e(this).addClass("active").siblings(".flow-checkout-adr").removeClass("active")
        }),
        e(".j-get-city-one .ect-select").click(function () {
            city_span = e(".j-filter-city span.text-all-span"),
                city_txt = e(".j-city-left li.active").text() + " " + e(this).parents(".j-sub-menu").prev(".j-menu-select").find("label").text() + " " + e(this).find("label").text(),
                e(".j-get-city-one").find(".ect-select label").removeClass("active"),
                e(this).find("label").addClass("active"),
                city_span.text(city_txt),
            e(".j-filter-city span.text-all-span").hasClass("j-city-scolor") && e(".j-filter-city span.text-all-span").css("color", "#ec5151"),
                e("body").removeClass("show-city-div"),
                e("html,body").animate({
                    scrollTop: t
                }, 0)
        }),
        e(".j-get-depot-one .ect-select").click(function () {
            city_span = e(".j-filter-depot span.text-all-span"),
                city_txt = e(this).find("label").text(),
                e(".j-get-depot-one").find(".ect-select label").removeClass("active"),
                e(this).find("label").addClass("active"),
                city_span.text(city_txt),
            e(".j-filter-depot span.text-all-span").hasClass("j-city-scolor") && e(".j-filter-depot span.text-all-span").css("color", "#ec5151"),
                e("body").removeClass("show-depot-div"),
                e("html,body").animate({
                    scrollTop: t
                }, 0)
        }),
        e("#sidebar").on("click", "li", function () {
            e("#sidebar li").removeClass("active"),
                e(this).addClass("active")
        }),
        e(".s-g-list-con .j-get-one .ect-select").click(function () {
            dist_span = e(this).find("label>dd").html(),
                t_goods1 = e(this).parents(".j-show-get-val").find(".t-goods1"),
                t_goods1.html(dist_span)
        }),
        e(".flow-receipt .r-btn-submit").click(function () {
            if (e("body").hasClass("show-receipt-div")) return document.removeEventListener("touchmove", handler, !1),
                e("body").removeClass("show-receipt-div"),
                f_r_title = e(".flow-receipt-title .j-input-text").val(),
                f_r_cont = e(".flow-receipt-cont .active").text(),
                f_r_type = e(".flow-receipt-cont .active").attr("data-type"),
            "" == f_r_title && (f_r_title = "个人"),
                receipt_title = e(this).parents(".j-f-c-receipt").find(".receipt-title"),
                receipt_name = e(this).parents(".j-f-c-receipt").find(".receipt-name"),
                receipt_title.text(f_r_title),
                receipt_name.text(f_r_cont),
                f_r_title && "不开发票" != f_r_cont ? (e.get("index.php?r=flow/index/change_needinv", {
                    need_inv: 1,
                    inv_type: "",
                    inv_payee: f_r_title,
                    inv_content: f_r_cont
                }, function (t) {
                    e("#ECS_ORDERTOTAL").html(t.content),
                        e("#amount").html(t.amount)
                }, "json"), e("#inv_type").val(f_r_type), e("#ECS_NEEDINV").val(1)) : e("#ECS_NEEDINV").val(0),
                e("#ECS_INVPAYEE").val(f_r_title),
                e("#ECS_INVCONTENT").val(f_r_cont),
                !1
        }),
        e(".flow-coupon .c-btn-submit").click(function () {
            if (e("body").hasClass("show-coupon-div")) {
                if (document.removeEventListener("touchmove", handler, !1), e("body").removeClass("show-coupon-div"), coupon_list = e(this).parents(".flow-coupon").find(".ect-select label.active"), coupon_text = e(this).parents(".j-f-c-s-coupon").find(".t-goods1 .coupon-text"), coupon_price = e(this).parents(".j-f-c-s-coupon").find(".t-goods1 .coupon-price"), coupon_list.length > 1) return d_messages("一次只能使用一张红包"),
                    !1;
                if (coupon_list.length <= 0 && 0 == e("#ECS_BONUS").val()) return !1;
                var t = coupon_list.length <= 0 ? 0 : coupon_list.attr("data-bonus");
                return e.get("index.php?r=flow/index/change_bonus", {
                    bonus: t
                }, function (t) {
                    if (t.error) {
                        d_messages(obj.error);
                        try {
                            e("#ECS_BONUS").val(0)
                        } catch (i) {
                        }
                    } else t.bonus_id && e("#ECS_BONUS").val(t.bonus_id),
                    t.content && e("#ECS_ORDERTOTAL").html(t.content),
                    void 0 != t.amount && e("#amount").html(t.amount)
                }, "json"),
                    coupon_list.length <= 0 ? (coupon_text.text("不使用红包"), coupon_price.text("")) : (coupon_text.text("优惠金额"), coupon_price.text("?" + parseInt(coupon_list.attr("data-money")) + ".00")),
                    !1
            }
        }),
        e(".flow-coupon .cou-btn-submit").click(function () {
            if (e("body").hasClass("show-coupon-div-1")) {
                if (document.removeEventListener("touchmove", handler, !1), e("body").removeClass("show-coupon-div-1"), coupon_list = e(this).parents(".flow-coupon").find(".ect-select label.active"), coupon_text = e(this).parents(".j-f-c-s-coupon-1").find(".t-goods1 .coupon-text"), coupon_price = e(this).parents(".j-f-c-s-coupon-1").find(".t-goods1 .coupon-price"), coupon_list.length > 1) return d_messages("一次只能使用一张优惠券"),
                    !1;
                if (coupon_list.length <= 0 && 0 == e("#ECS_COUPONT").val()) return !1;
                var t = coupon_list.length <= 0 ? 0 : coupon_list.attr("data-coupont");
                return e.get("index.php?r=flow/index/change_coupont", {
                    cou_id: t
                }, function (t) {
                    if (t.error) {
                        d_messages(obj.error);
                        try {
                            e("#ECS_COUPONT").val(0)
                        } catch (i) {
                        }
                    } else t.cou_id && e("#ECS_COUPONT").val(t.cou_id),
                    t.content && e("#ECS_ORDERTOTAL").html(t.content),
                    void 0 != t.amount && e("#amount").html(t.amount)
                }, "json"),
                    coupon_list.length <= 0 ? (coupon_text.text("不使用优惠券"), coupon_price.text("")) : (coupon_text.text("优惠金额"), coupon_price.text("?" + parseInt(coupon_list.attr("data-money")) + ".00")),
                    !1
            }
        }),
        e(".j-search-input").click(function () {
            e(".j-input-text").val(""),
                e("input[name=type_select]").val(2),
                e("body").addClass("show-search-div")
        }),
        e(".j-close-search").click(function () {
            e("body").removeClass("show-search-div")
        }),
        e(".j-filter-city").click(function () {
            t = e(window).scrollTop(),
                e("body").addClass("show-city-div")
        }),
        e(".j-filter-depot").click(function () {
            t = e(window).scrollTop(),
                e("body").addClass("show-depot-div")
        }),
        e(".j-s-filter").click(function () {
            t = e(window).scrollTop(),
                e("body").addClass("show-filter-div")
        }),
        e(".j-close-filter-div").click(function () {
            return e(".filter-site-div").hasClass("show") ? (document.removeEventListener("touchmove", handler, !1), e(this).parent(".filter-site-div").removeClass("show"), !1) : e("body").hasClass("show-city-div") ? (e("body").removeClass("show-city-div"), e("html,body").animate({
                scrollTop: t
            }, 0), !1) : e("body").hasClass("show-filter-div") ? (e("body").removeClass("show-filter-div"), e("html,body").animate({
                scrollTop: t
            }, 0), !1) : e("body").hasClass("show-depot-div") ? (e("body").removeClass("show-depot-div"), e("html,body").animate({
                scrollTop: t
            }, 0), !1) : void 0
        }),
        e(".j-radio-switching").click(function () {
            e(this).hasClass("active") ? (e(this).removeClass("active"), e(this).attr("data", 0), e("input[name=isself]").val(0)) : (e(this).addClass("active"), e(this).attr("data", 1), e("input[name=isself]").val(1))
        }),
        e(".j-goods-site-li").click(function () {
            document.addEventListener("touchmove", handler, !1),
                e(this).find(".filter-site-div").addClass("show")
        }),
        e(".j-f-c-s-coupon").click(function () {
            e("body").addClass("show-coupon-div")
        }),
        e(".j-f-c-s-coupon-1").click(function () {
            document.addEventListener("touchmove", handler, !1),
                e("body").addClass("show-coupon-div-1")
        }),
        e(".j-f-c-receipt").click(function () {
            document.addEventListener("touchmove", handler, !1),
                e("body").addClass("show-receipt-div")
        }),
        e(".j-show-div").click(function () {
            document.addEventListener("touchmove", handler, !1),
                e(this).find(".j-filter-show-div").addClass("show"),
                e(".mask-filter-div").addClass("show")
        }),
        e(".j-evaluation-star .evaluation-star").click(function () {
            var t = e(this).index() + 1;
            e(".j-evaluation-star .evaluation-star").removeClass("active");
            for (var i = 0; i <= t; i++) e(".j-evaluation-star .evaluation-star").eq(i).addClass("active");
            e(".j-evaluation-value").val(t + 1)
        }),
        e(".mask-filter-div,.show-div-guanbi").click(function () {
            e(".j-filter-show-div").hasClass("show") && e(".j-filter-show-div").removeClass("show"),
            e(".j-filter-show-list").hasClass("show") && e(".j-filter-show-list").removeClass("show"),
            e(".shopping-menu").hasClass("nav-active") && e(".shopping-menu").removeClass("nav-active"),
            e(".shopping-menu").hasClass("position-active") && e(".shopping-menu").removeClass("position-active"),
                e(".mask-filter-div").removeClass("show"),
                document.removeEventListener("touchmove", handler, !1);
            var t = "";
            s_get_label = e(".show-goods-attr .s-g-attr-con").find("label.active"),
            s_get_label.length > 0 && s_get_label.each(function () {
                t += e(this).text() + "、"
            });
            var i = e("#goods_number").val();
            i = parseInt(i) ? parseInt(i) : 1,
                t = t + i + "个",
                e(".j-goods-attr").find(".t-goods1").text(t),
                event.stopPropagation()
        }),
        e(".j-show-list").click(function () {
            document.addEventListener("touchmove", handler, !1),
                e(".j-filter-show-list").addClass("show"),
                e(".mask-filter-div").addClass("show")
        }),
        e(".flow-have-cart .j-icon-show").click(function () {
            e(this).parents(".g-promotion-con").toggleClass("active")
        }),
        e(".f-cart-filter-btn .span-bianji").click(function () {
            e(".f-cart-filter-btn").addClass("active")
        }),
        e(".f-cart-filter-btn .j-btn-default").click(function () {
            e(".f-cart-filter-btn").removeClass("active")
        }),
        e(".div-num-disabled").find("input").attr("readonly", !0),
        e(".j-flow-checkout-pro span.t-jiantou").click(function () {
            e(this).parents(".flow-checkout-pro").toggleClass("active")
        }),
        e(".text-all-select .j-input-text").focus(function () {
            e(this).parents(".text-all-select").find(".text-all-select-div").show()
        }),
        e(".text-all-select-div li").click(function () {
            return text_select = e(this).text(),
                e(this).parents(".text-all-select").find(".j-input-text").val(text_select),
                e(this).parents(".text-all-select").find(".text-all-select-div").hide(),
                !1
        }),
        e(".filter-menu-title").click(function () {
            e(".filter-menu").toggleClass("active")
        }),
        e(".j-s-nav-select").click(function () {
            e(".shopping-menu").hasClass("nav-active") ? (e(".shopping-menu").removeClass("nav-active"), document.removeEventListener("touchmove", handler, !1), e(".mask-filter-div").removeClass("show")) : (e(this).addClass("active").siblings().removeClass("active"), document.addEventListener("touchmove", handler, !1), e(".shopping-menu").addClass("nav-active"), e(".shopping-menu").removeClass("position-active distance-active"), e(".mask-filter-div").addClass("show"))
        }),
        e(".j-s-position-select").click(function () {
            e(".shopping-menu").hasClass("position-active") ? (e(".shopping-menu").removeClass("position-active"), e(".mask-filter-div").removeClass("show")) : (e(this).addClass("active").siblings().removeClass("active"), e(".shopping-menu").addClass("position-active"), e(".shopping-menu").removeClass("nav-active distance-active"), e(".mask-filter-div").addClass("show"))
        }),
        e(".j-s-distance-select").click(function () {
            e(".mask-filter-div").hasClass("show") && e(".mask-filter-div").removeClass("show"),
                e(".shopping-menu").hasClass("distance-active") ? (e(".shopping-menu").removeClass("distance-active"), e("mask-filter-div").removeClass("show")) : (e(this).addClass("active").siblings().removeClass("active"), e(".shopping-menu").addClass("distance-active"), e(".shopping-menu").removeClass("position-active nav-active"), e("mask-filter-div").addClass("show"))
        }),
        e(".shopping-nav-con a").click(function () {
            e(this).addClass("active").siblings().removeClass("active"),
                e(".shopping-menu").removeClass("nav-active"),
                e(".j-s-nav-select").find("span").text(e(this).text())
        }),
    e(".j-shopping-pro-list").hasClass("j-shopping-pro-list") && e(window).scroll(function () {
        shopping_menu_h = e(".j-shopping-menu").outerHeight(),
            shopping_menu_t = e(".j-shopping-pro-list").offset().top - e(document).scrollTop(),
            shopping_menu_t <= shopping_menu_h ? e(".j-shopping-list").addClass("active") : e(".j-shopping-list").removeClass("active")
    }),
        e(".j-menu-fixed>ul>li").click(function () {
            e(this).hasClass("active") ? e(this).removeClass("active") : e(this).addClass("active").siblings().removeClass("active")
        }),
        e(".j-evaluation-star .evaluation-star").click(function () {
            var t = e(this).index();
            e(".j-evaluation-star .evaluation-star").removeClass("active");
            for (var i = 0; i <= t; i++) e(".j-evaluation-star .evaluation-star").eq(i).addClass("active");
            e(".j-evaluation-value").val(t + 1)
        }),
    e(".text-area1").hasClass("text-area1") && e(".text-area1").each(function () {
        e(this).find("span").text(e(this).find("textarea").attr("maxlength"))
    }),
        e(".text-area1 textarea").bind("input", function () {
            count_span = e(this).siblings("span"),
                max_length = e(this).attr("maxlength"),
                textarea_length = e(this).val().length,
                max_length - textarea_length < 0 ? count_span.text(0) : count_span.text(max_length - textarea_length)
        }),
        e(".filter-top").click(function () {
            e("html,body").animate({
                scrollTop: 0
            }, 200)
        }),
        e(window).scroll(function () {
            var t = 0,
                i = 0;
            i = e(window).scrollTop(),
                win_height = 2 * e(window).height(),
                i >= win_height ? e(".filter-top").stop().fadeIn(200) : e(".filter-top").stop().fadeOut(200),
                setTimeout(function () {
                    t = i
                }, 0)
        }),
        e("#loading").hide(),
        e(".my-com-nav1").click(function () {
            e(".my-com-nav1").siblings(".ect-select").find("label").removeClass("active"),
                e(this).siblings(".ect-select").find("label").addClass("active")
        }),
        e(function () {
            e(".oncle-color").click(function () {
                for (var t = 0; t < e(".oncle-color").size(); t++) this == e(".oncle-color").get(t) ? e(".oncle-color").eq(t).children("a").addClass("active") : e(".oncle-color").eq(t).children("a").removeClass("active")
            })
        })
}),
    $(function (e) {
        e(".my-com-nav2").click(function () {
            return my_com_nav2 = e(this).siblings(".ect-select").find("label"),
                j_open_two_select = e(".j-open-two-select").find(".ect-select label"),
                j_open_two_select_all = e(".j-open-two-select-all").find(".ect-select label"),
                my_com_nav2.hasClass("active") ? my_com_nav2.removeClass("active") : my_com_nav2.addClass("active"),
                j_open_two_select.each(function () {
                    return e(this).hasClass("active") ? void j_open_two_select_all.addClass("active") : (j_open_two_select_all.removeClass("active"), !1)
                }),
                !1
        }),
            e(".my-com-nav-one").click(function () {
                j_open_two_select_all = e(".j-open-two-select-all").find(".ect-select label"),
                    j_open_two_select = e(".j-open-two-select").find(".ect-select label"),
                    j_open_two_select_all.hasClass("active") ? (j_open_two_select_all.removeClass("active"), j_open_two_select.removeClass("active")) : (j_open_two_select_all.addClass("active"), j_open_two_select.addClass("active"))
            })
    }),
    $(function (e) {
        e(".user-shop-fx").click(function () {
            e(".shopping-prompt").addClass("active")
        }),
            e(".shopping-prompt").click(function () {
                e(".shopping-prompt").removeClass("active")
            }),
            e(".icon-13caidan,.j-search-input").click(function () {
                return e(".goods-scoll-bg").addClass("active"),
                    e(".goods-nav").hasClass("active") ? (e(".goods-nav").removeClass("active"), e(".goods-scoll-bg").removeClass("active"), !1) : (e(".goods-nav").addClass("active"), e(".goods-scoll-bg").addClass("active"), !1)
            }),
            e(".goods-scoll-bg").click(function () {
                e(".goods-scoll-bg").removeClass("active"),
                    e(".goods-nav").removeClass("active")
            }),
            e(window).scroll(function () {
                e(window).scrollTop() > 0 && (e(".goods-scoll-bg").removeClass("active"), e(".goods-nav").removeClass("active"))
            }),
            e(".j-goods-box").click(function () {
                document.addEventListener("touchmove", handler, !1),
                    e(".goods-banner").addClass("active"),
                    e(".goods-bg-box").addClass("active")
            }),
            e(".goods-bg-box").click(function () {
                document.removeEventListener("touchmove", handler, !1),
                    e(".goods-banner").removeClass("active"),
                    e(".goods-bg-box").removeClass("active")
            }),
            e(".j-search-address").click(function () {
                e(".ec-fresh-bg").addClass("active"),
                    e(".t-search-footer").addClass("active")
            }),
            e(".ec-fresh-bg").click(function () {
                e(".ec-fresh-bg").removeClass("active"),
                    e(".t-search-footer").removeClass("active")
            }),
            e(".n-goods-shop-list-nav li").on("click", function (t) {
                var i = e(this).attr("category"),
                    n = e(".n-goods-shop-list-nav li").index(this);
                e(this).siblings().removeClass("active"),
                    e(".shopping-abs .swiper-slide a").removeClass("active"),
                    e(".div" + i).addClass("active"),
                    swiper_nav.slideTo(n, 1e3, !1),
                    infinite.onload("where=" + i + "&type=1");
                new Swiper(".j-g-s-p-con", {
                    scrollbarHide: !0,
                    slidesPerView: "auto",
                    centeredSlides: !1,
                    grabCursor: !0
                })
            })
    });
var region = new Object;
region.isAdmin = !1,
    region.loadRegions = function (e, t, i, n) {
        $.get(region.getFileName(), {
            type: t,
            target: i,
            parent: e,
            user_id: n
        }, function (e) {
            region.response(e, "")
        }, "json")
    },
    region.loadProvinces = function (e, t) {
        var i = "undefined" == typeof t ? "selProvinces" : t;
        region.loadRegions(e, 1, i)
    },
    region.loadCities = function (e, t) {
        var i = "undefined" == typeof t ? "selCities" : t;
        region.loadRegions(e, 2, i)
    },
    region.loadDistricts = function (e, t) {
        var i = "undefined" == typeof t ? "selDistricts" : t;
        region.loadRegions(e, 3, i)
    },
    region.getRegion = function (e, t, i) {
        return $("#province_id").val(e),
            $.get(region.getFileName(), {
                parent: e,
                type: t,
                user_id: i
            }, function (e) {
                if (e.regions.length > 0) {
                    var t = "",
                        i = e.regions;
                    for (key in i) if (i[key].district.length > 0) {
                        if (t += '<a class="select-title padding-all j-menu-select" ><label class="fl">' + i[key].region_name + '</label><span class="fr t-jiantou j-t-jiantou" id="j-t-jiantou"><i class="iconfont icon-jiantou tf-180 ts-2"></i></span></a>', void 0 != typeof i[key].district) {
                            var n = i[key].district;
                            t += '<ul class="padding-all j-sub-menu" style="display:none;">';
                            for (k in n) t += ' <li class="ect-select"><label onclick="region.changedDis(' + n[k].region_id + ", " + i[key].region_id + ", " + e.user_id + ')" class="ts-1">' + n[k].region_name + '<i class="fr iconfont icon-gou ts-1"></i></label></li>';
                            t += "</ul>"
                        }
                    } else t += '<a class="select-title padding-all j-menu-select" onclick="region.changedDis(0, ' + i[key].region_id + ", " + e.user_id + ')"><label class="fl">' + i[key].region_name + '</label><span class="fr t-jiantou j-t-jiantou" id="j-t-jiantou"><i class="iconfont icon-jiantou tf-180 ts-2"></i></span></a>';
                    t && $(".j-city-right .j-get-city-one").html(t)
                }
            }, "json"),
            !1
    },
    region.getFileName = function () {
        return "index.php?r=region"
    },
    region.changedDis = function (e, t, i, n) {
        var s = document.getElementById("province_id").value,
            a = document.getElementById("good_id").value,
            o = "index.php?r=goods/index/in_stock";
        return !(t <= 0) && (n = 1 == n ? n : "", $("#city_id").val(t), $("#district_id").val(e), void $.get(o, {
                id: a,
                province: s,
                city: t,
                district: e,
                user_id: i,
                d_null: n
            }, function (e) {
                region.is_inStock(e)
            }, "json"))
    },
    region.is_inStock = function (e) {
        if (0 == e.isRegion) if (confirm(e.message)) {
            var t = document.getElementById("district_id");
            t.value = e.district,
                location.href = "index.php?r=user/index/address_list"
        } else location.reload();
        else location.reload();
        return !1
    },
    region.selectRegion = function (e, t, i) {
        return $.get("index.php?r=region/index/select_region_child", {
            raId: e,
            parent: t,
            type: i
        }, function (e) {
            if (e.regions.length > 0) {
                var t = "",
                    i = e.regions;
                for (key in i) if (i[key].district.length > 0) {
                    if (t += '<a class="select-title padding-all j-menu-select"><label class="fl">' + i[key].region_name + '</label><span class="fr t-jiantou j-t-jiantou" id="j-t-jiantou"><i class="iconfont icon-jiantou tf-180 ts-2"></i></span></a>', void 0 != typeof i[key].district) {
                        var n = i[key].district;
                        t += '<ul class="padding-all j-sub-menu" style="display:none;">';
                        for (k in n) t += ' <li class="ect-select"><label onclick="region.selectDis(' + n[k].region_id + ', 1)" class="ts-1">' + n[k].region_name + '<i class="fr iconfont icon-gou ts-1"></i></label></li>';
                        t += "</ul>"
                    }
                } else t += '<a class="select-title padding-all" onclick="region.selectDis(' + i[key].region_id + ', 0)"><label class="fl">' + i[key].region_name + '</label><span class="fr t-jiantou j-t-jiantou" id="j-t-jiantou"><i class="iconfont icon-jiantou tf-180 ts-2"></i></span></a>';
                t && $(".j-city-right .j-get-city-one").html(t)
            }
        }, "json"),
            !1
    },
    region.selectDis = function (e, t) {
        $.get("index.php?r=region/index/select_district_list", {
            region_id: e,
            type: t
        }, function (e) {
            0 == e.error && location.reload()
        }, "json")
    },
    region.cccDdd = function (e, t, i, n) {
        var s = document.getElementById("province_id").value;
        if (t <= 0) return !1;
        n = 1 == n ? n : "",
            $("#province_id").val(s),
            $("#city_id").val(t),
            $("#district_id").val(e),
            $(".show-city-div").removeClass("show-city-div");
        var a = $("input[name=province_region_id]").val(),
            o = $("input[name=city_region_id]").val(),
            r = $("input[name=district_region_id]").val();
        $.post("index.php?r=user/index/show_region_name", {
            province: a,
            city: o,
            district: r
        }, function (e) {
            e.district.region_name = e.district.region_name ? e.district.region_name : "",
                $(".show-region").text(e.province.region_name + e.city.region_name + e.district.region_name)
        }, "json")
    },
    region.getBbb = function (e, t, i) {
        return $("#province_id").val(e),
            $.get(region.getFileName(), {
                parent: e,
                type: t,
                user_id: i
            }, function (e) {
                if (e.regions.length > 0) {
                    var t = "",
                        i = e.regions;
                    for (key in i) if (i[key].district.length > 0) {
                        if (t += '<a class="select-title padding-all j-menu-select" ><label class="fl">' + i[key].region_name + '</label><span class="fr t-jiantou j-t-jiantou" id="j-t-jiantou"><i class="iconfont icon-jiantou tf-180 ts-2"></i></span></a>', void 0 != typeof i[key].district) {
                            var n = i[key].district;
                            t += '<ul class="padding-all j-sub-menu" style="display:none;">';
                            for (k in n) t += ' <li class="ect-select"><label onclick="region.cccDdd(' + n[k].region_id + ", " + i[key].region_id + ", " + e.user_id + ')" class="ts-1">' + n[k].region_name + '<i class="fr iconfont icon-gou ts-1"></i></label></li>';
                            t += "</ul>"
                        }
                    } else t += '<a class="select-title padding-all j-menu-select" onclick="region.cccDdd(0, ' + i[key].region_id + ", " + e.user_id + ')"><label class="fl">' + i[key].region_name + '</label><span class="fr t-jiantou j-t-jiantou" id="j-t-jiantou"><i class="iconfont icon-jiantou tf-180 ts-2"></i></span></a>';
                    t && $(".j-city-right .j-get-city-one").html(t)
                }
            }, "json"),
            !1
    };
var funParabola = function (e, t, i) {
    var n = {
            speed: 166.67,
            curvature: .001,
            progress: function () {
            },
            complete: function () {
            }
        },
        s = {};
    i = i || {};
    for (var a in n) s[a] = i[a] || n[a];
    var o = {
            mark: function () {
                return this
            },
            position: function () {
                return this
            },
            move: function () {
                return this
            },
            init: function () {
                return this
            }
        },
        r = "margin",
        l = document.createElement("div");
    "oninput" in l && ["", "ms", "webkit"].forEach(function (e) {
        var t = e + (e ? "T" : "t") + "ransform";
        t in l.style && (r = t)
    });
    var c = s.curvature,
        d = 0,
        h = !0;
    if (e && t && 1 == e.nodeType && 1 == t.nodeType) {
        var u = {},
            p = {},
            f = {},
            m = {},
            g = {},
            v = {};
        o.mark = function () {
            return 0 == h ? this : ("undefined" == typeof g.x && this.position(), e.setAttribute("data-center", [g.x, g.y].join()), t.setAttribute("data-center", [v.x, v.y].join()), this)
        },
            o.position = function () {
                if (0 == h) return this;
                var i = document.documentElement.scrollLeft || document.body.scrollLeft,
                    n = document.documentElement.scrollTop || document.body.scrollTop;
                return "margin" == r ? e.style.marginLeft = e.style.marginTop = "0px" : e.style[r] = "translate(0, 0)",
                    u = e.getBoundingClientRect(),
                    p = t.getBoundingClientRect(),
                    f = {
                        x: u.left + (u.right - u.left) / 2 + i,
                        y: u.top + (u.bottom - u.top) / 2 + n
                    },
                    m = {
                        x: p.left + (p.right - p.left) / 2 + i,
                        y: p.top + (p.bottom - p.top) / 2 + n
                    },
                    g = {
                        x: 0,
                        y: 0
                    },
                    v = {
                        x: -1 * (f.x - m.x),
                        y: -1 * (f.y - m.y)
                    },
                    d = (v.y - c * v.x * v.x) / v.x,
                    this
            },
            o.move = function () {
                if (0 == h) return this;
                var t = 0,
                    i = v.x > 0 ? 1 : -1,
                    n = function () {
                        var a = 2 * c * t + d;
                        t += i * Math.sqrt(s.speed / (a * a + 1)),
                        (1 == i && t > v.x || i == -1 && t < v.x) && (t = v.x);
                        var o = t,
                            l = c * o * o + d * o;
                        e.setAttribute("data-center", [Math.round(o), Math.round(l)].join()),
                            "margin" == r ? (e.style.marginLeft = o + "px", e.style.marginTop = l + "px") : e.style[r] = "translate(" + [o + "px", l + "px"].join() + ")",
                            t !== v.x ? (s.progress(o, l), window.requestAnimationFrame(n)) : (s.complete(), h = !0)
                    };
                return window.requestAnimationFrame(n),
                    h = !1,
                    this
            },
            o.init = function () {
                this.position().mark().move()
            }
    }
    return o
};