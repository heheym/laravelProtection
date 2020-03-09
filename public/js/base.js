var baseAlert = function (tip, ms) {
    var ms = ms || 5000;
    var baseAlertTip = document.querySelectorAll(".base-alert-tip");
    if (baseAlertTip.length) {
        baseAlertTip[baseAlertTip.length - 1].style.display = "none";
    }
    var div = document.createElement("div");
    var p = document.createElement("p");
    var i = document.createElement("i");
    var span = document.createElement("span");
    div.classList.add("base-alert-tip");
    div.style.position = "fixed";
    div.style.width = "600px";
    div.style.top = "100px";
    div.style.left = "50%";
    div.style.marginLeft = "-300px";
    div.style.textAlign = "center";
    div.style.lineHeight = "1";
    div.style.zIndex = "10000";
    div.style.transition = "top 1s";
    p.style.position = "relative";
    p.style.display = "inline";
    p.style.padding = "2px 8px 2px 28px";
    p.style.borderRadius = "5px";
    p.style.backgroundColor = "#333";
    p.style.lineHeight = "1.428";
    p.style.fontSize = "14px";
    p.style.color = "#fff";
    i.style.position = "absolute";
    i.style.left = "6px";
    i.style.top = "3px";
    i.style.width = "16px";
    i.style.height = "16px";
    i.style.lineHeight = "16px";
    i.style.borderRadius = "50%";
    i.style.fontSize = "12px";
    i.style.backgroundColor = "#fff";
    i.style.color = "#f90";
    i.style.fontStyle = "normal";
    i.innerHTML = "!";
    span.innerHTML = tip;
    p.appendChild(i);
    p.appendChild(span);
    div.appendChild(p);
    document.body.appendChild(div);
    setTimeout(function () {
        div.style.top = "70px";
    }, 10);
    if (ms !== 0) {
        setTimeout(function () {
            div.parentNode.removeChild(div)
        }, ms);
    }
    return div;
};
var tabSwitch = function (element, fn) {
    $(element).click(function () {
        var index = $(this).attr('id');
        $(this).addClass("active").siblings().removeClass("active");
        if (typeof fn === "function") {
            fn(index,url);
        }
    });
};

