<!--Author: W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<?php


?>
<!DOCTYPE HTML>
<html>
<head>
    <title>信息收集</title>
    <link href="{{URL::asset('css/collect.css')}}" rel="stylesheet" type="text/css" media="all"/>
    <link href="{{URL::asset('layui/src/css/layui.css')}}" rel="stylesheet" type="text/css" media="all"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="Temper Contact Form Responsive, Login form web template, Sign up Web Templates, Flat Web Templates, Login signup Responsive web template, Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
    <!--web-fonts-->
{{--    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'></head>--}}
{{--<link href="//fonts.googleapis.com/css?family=Arvo:400,400i,700,700i" rel="stylesheet">--}}
<!--web-fonts-->
<body>
<!---header--->
<div class="header">
    <h1>信息收集</h1>
</div>
<!---header--->
<!---main--->
<div class="main-content">
    <div class="contact-w3">
        <form action="collect/message" method="post">
            <label>姓名</label>
            <input type="text" name="name" placeholder="姓名" required>
            <div class="row">
                <div class="contact-right-w3l">
                    <label>手机号</label>
                    <input type="text" name="phone" placeholder="手机号" required>
                </div>
                <div class="contact-left-w3">
                    <label>地址</label>
                    <input type="text" name="address" placeholder="地址" required>
                </div>
                <div class="clear"></div>
            </div>

            <div class="row1">
                <label>备注</label>
                <textarea placeholder="备注" name="message"></textarea>
            </div>
            <input type="button" class="btn" value="保存" onclick="sub()">
        </form>
    </div>
</div>


<div class="footer-w3-agile">
    <p>&copy 2020 Temper Contact Form </p>
</div>

<!---main--->
</body>
</html>

<script src="{{URL::asset('js/jquery-1.11.1.js')}}"></script>
<script src="{{URL::asset('layui/src/layui.js')}}"></script>
<script>
$(function(){

})
function sub() {
    var name = $('input[name="name"]').val();
    var phone = $('input[name="phone"]').val();
    var address = $('input[name="address"]').val();
    var message = $('input[name="message"]').val();

    var nameval = document.querySelector('input[name="name"]');
    if (nameval.validity.valueMissing == true) {
        nameval.setCustomValidity('请填写姓名');
        nameval.reportValidity();
        return;
    }
    var phoneval = document.querySelector('input[name="phone"]');
    if (phoneval.validity.valueMissing == true) {
        phoneval.setCustomValidity('请填写手机号');
        phoneval.reportValidity();
        return;
    }
    var addressval = document.querySelector('input[name="address"]');
    if (addressval.validity.valueMissing == true) {
        addressval.setCustomValidity('请填写地址');
        addressval.reportValidity();
        return;
    }

    $.post('collect/message',{name:name,phone:phone,address:address,message:message},
       function(data){
           data = eval("("+data+")") ;
            if(data.code==200){
                layui.use('layer', function(){
                    layer.msg('保存成功', {
                        time: 2000, //20s后自动关闭,
                        area: 'auto',
                        maxWidth:150,
                    });
                });
                setTimeout(function () {
                    location.reload();
                },2000)
            }else{
                layui.use('layer', function(){
                    layer.msg('保存失败', {
                        time: 2000, //20s后自动关闭,
                        area: 'auto',
                        maxWidth:150,
                    });
                });
            }
       }
    );
}
</script>