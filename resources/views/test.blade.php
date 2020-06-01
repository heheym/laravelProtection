<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title></title>
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
<h3>WebSocket协议的客户端程序</h3>
{{--<button id="btConnect">连接到WS服务器</button>--}}
<p><input type-="text" id="subtext"><button id="btSendAndReceive">向WS服务器发消息并接收消息</button></p>
<button id="btClose">断开与WS服务器的连接</button>
<button id="postcast">广播</button>
<button id="remo">清空</button>
<div id="val"></div>
<script type="text/javascript">
    var wsClient=null;
    $(function () {
        wsClient=new WebSocket('ws://127.0.0.1:8081');
        console.log(wsClient)
        wsClient.onopen = function(){
            var uid = uuid(8,16);
            // 表名自己是uid1
            wsClient.send(uid);
            $('#val').append('<p>连接成功</p>');
        }
        wsClient.onmessage = function(e){
            console.log('ws客户端收到一个服务器消息：'+e.data);
            $('#val').append('<p>'+e.data+'</p>');
        }
    })
    btSendAndReceive.onclick = function(){
        var subtext = $('#subtext').val();
        wsClient.send(subtext);
        wsClient.onmessage = function(e){
            console.log('ws客户端收到一个服务器消息：'+e.data);
            $('#val').append('<p>'+e.data+'</p>');
        }
    }
    btClose.onclick = function(){
        wsClient.close();
        wsClient.onclose = function(){
            $('#val').append('<p>断开成功</p>');
        }
    }
    postcast.onclick = function(){
        wsClient.send('广播');
        wsClient.onmessage = function(e){
            console.log('ws客户端收到一个服务器消息：'+e.data);
            $('#val').append('<p>'+e.data+'</p>');
        }
    }

    function uuid(len, radix) {
        var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split('');
        var uuid = [], i;
        radix = radix || chars.length;

        if (len) {
            for (i = 0; i < len; i++) uuid[i] = chars[0 | Math.random() * radix];
        } else {
            var r;

            uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
            uuid[14] = '4';

            for (i = 0; i < 36; i++) {
                if (!uuid[i]) {
                    r = 0 | Math.random() * 16;
                    uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r];
                }
            }
        }

        return uuid.join('');
    }

    remo.onclick = function(){
        $('#val').empty();
    }
</script>
</body>
</html>