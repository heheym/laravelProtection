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
    var tabSwitch = function (element, fn) {
        $(element).click(function () {
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
    var tabSwitch = function (element, fn) {
        $(element).click(function () {
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



