
//系统参数设置显示密码
function show() {
    $(".fa-eye-slash").parent('span').css({"cursor":"pointer"});

    $("#SecretKey").prev('.input-group-addon').click(function(){
        if($('#SecretKey').attr('type')=='password'){
            $('#SecretKey').attr('type','text');
        }else if($('#SecretKey').attr('type')=='text'){
            $('#SecretKey').attr('type','password');
        }
    });
    $("#AccessKey").prev('.input-group-addon').click(function(){
        if($('#AccessKey').attr('type')=='password'){
            $('#AccessKey').attr('type','text');
        }else if($('#AccessKey').attr('type')=='text'){
            $('#AccessKey').attr('type','password');
        }
    });
}

function receivable() {
    $(function () {
        $(".column-key").parents('tbody').css('cursor','pointer');
        var url = new URL(location);
        var receivable_svrkey = url.searchParams.get('receivable_svrkey');
        if(receivable_svrkey!==null && receivable_svrkey.length>0){
            $(".column-key:contains('"+receivable_svrkey+"')").parent("tr").css('background','rgb(255, 255, 213)');
        }
    })

    var tabSwitch = function (element, fn) {
        $(element).dblclick(function () {
            var index = $.trim($(this).find('.column-key').html());
            $(this).addClass("active").siblings().removeClass("active");
            if (typeof fn === "function") {
                fn(index);
            }
        });
    };

    $elem = $(".column-key").parent("tr");

    tabSwitch($elem,function (index) {
        var url = new URL(location);

        url.searchParams.set('receivable_svrkey',index);

        $.pjax({container:'#pjax-container', url: url.toString()});
        //分页数据
    });
}

function receipt() {
    $(function () {
        $(".column-key").parents('tbody').css('cursor','pointer');

        var url = new URL(location);
        var receipt_svrkey = url.searchParams.get('receipt_svrkey');
        if(receipt_svrkey!==null && receipt_svrkey.length>0){
            $(".column-key:contains('"+receipt_svrkey+"')").parent("tr").css('background','rgb(255, 255, 213)');
        }
    })

    var tabSwitch = function (element, fn) {
        $(element).dblclick(function () {
            var index = $.trim($(this).find('.column-key').html());
            $(this).addClass("active").siblings().removeClass("active");
            if (typeof fn === "function") {
                fn(index);
            }
        });
    };

    $elem = $(".column-key").parent("tr");

    tabSwitch($elem,function (index) {
        var url = new URL(location);

        url.searchParams.set('receipt_svrkey',index);

        $.pjax({container:'#pjax-container', url: url.toString()});
        //分页数据
    });
}

function localMoney() {
    $(".iCheck-helper").click(function(){
        var owedPrice = 0;
        $(".column-__row_selector__").parents("tbody").find("tr").each(function(){
            var check = $(this).find(".icheckbox_minimal-blue").attr('aria-checked');

            if(check==="true"){
                var val = $(this).find(".column-owedPrice span").html();
                val = Number(val);
                if(val>0){
                    owedPrice = owedPrice+val;
                    owedPrice = Math.floor(owedPrice * 100) / 100;
                }
            }
        });
        $('#receivableMoney').val(owedPrice);
        $('#local_money').val(owedPrice);
    });
}

//开房时段
function openingTime(){
    $('select[name=province]').attr('required','required');
    $('select[name=city]').attr('required','required');
    $('select[name=placArea]').attr('required','required');

    // var input = document.getElementById('phone');
    // input.oninvalid = function(event) { event.target.setCustomValidity('手机号格式不正确'); }
    // input.onvalid = function(event) { event.target.setCustomValidity(''); }

    $(".time1,.time2,.time3,.time4").datetimepicker({"format":"HH:mm","locale":"zh-CN","allowInputToggle":true});

    var feesmode =$(".FeesMode option:selected").val();
    if(feesmode ==0){
        $('.feesmode').parents('.form-group').hide();
        $('.FeesScanMode').parents('.form-group').hide();
    }else if(feesmode ==1){
        $('.feesmode').parents('.form-group').show();
        $('.FeesScanMode').parents('.form-group').show();
    }
}

//订单
function order() {
    $(function () {
        $(".column-key").parents('tbody').css('cursor','pointer');

        var url = new URL(location);
        var ordersn_key = url.searchParams.get('ordersn_key');
        if(ordersn_key!==null && ordersn_key.length>0){
            $(".column-key:contains('"+ordersn_key+"')").parent("tr").css('background','rgb(255, 255, 213)');
        }
    })

    var tabSwitch = function (element, fn) {
        $(element).dblclick(function () {
            var index = $.trim($(this).find('.column-key').html());
            $(this).addClass("active").siblings().removeClass("active");
            if (typeof fn === "function") {
                fn(index);
            }
        });
    };

    $elem = $(".column-key").parent("tr");

    tabSwitch($elem,function (index) {
        var url = new URL(location);

        url.searchParams.set('ordersn_key',index);

        $.pjax({container:'#pjax-container', url: url.toString()});
        //分页数据
    });
}


//place
function place() {
    $(function () {
        $(".column-key").parents('tbody').css('cursor','pointer');
        var url = new URL(location);
        var settopbox_key = url.searchParams.get('settopbox_key');

        if(settopbox_key!==null && settopbox_key.length>0){
        $(".column-key:contains('"+settopbox_key+"')").parent("tr").css('background','rgb(255, 255, 213)');
        }
    })
    var tabSwitch = function (element, fn) {
        $(element).dblclick(function () {
            var index = $.trim($(this).find('.column-key').html());
            $(this).addClass("active").siblings().removeClass("active");
            if (typeof fn === "function") {
                fn(index);
            }
        });
    };

    $elem = $(".column-key").parent("tr");

    tabSwitch($elem,function (index) {
        var url = new URL(location);

        url.searchParams.set('settopbox_key',index);

        $.pjax({container:'#pjax-container', url: url.toString()});
        //分页数据
    });
}

//merchantset
function merchantset() {
    $(function () {
        $(".column-key").parents('tbody').css('cursor','pointer');
        var url = new URL(location);
        var merchantset_svrkey = url.searchParams.get('merchantset_svrkey');
        if(merchantset_svrkey!==null && merchantset_svrkey.length>0){
            $(".column-key:contains('"+merchantset_svrkey+"')").parent("tr").css('background','rgb(255, 255, 213)');
        }
    })
    var tabSwitch = function (element, fn) {
        $(element).dblclick(function () {
            var index = $.trim($(this).find('.column-key').html());
            $(this).addClass("active").siblings().removeClass("active");
            if (typeof fn === "function") {
                fn(index);
            }
        });
    };

    $elem = $(".column-key").parent("tr");

    tabSwitch($elem,function (index) {
        var url = new URL(location);

        url.searchParams.set('merchantset_svrkey',index);

        $.pjax({container:'#pjax-container', url: url.toString()});
        //分页数据
    });
}


