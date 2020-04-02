<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="format-detection" content="telephone=no,email=no,adress=no">
    <meta name="viewport" content="width=device-width,  initial-scale=1, maximum-scale=1, minimum-scale=1,  user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>新歌更新</title>

    <link rel="stylesheet" href="{{ URL::asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/index.css') }}">
    {{--<script src="js/jquery-1.11.1.js"></script>--}}
    <script src="{{ URL::asset('js/jquery-1.11.1.js') }}"></script>
    <script src="{{ URL::asset('js/base.js') }}"></script>
    <script src="{{ URL::asset('js/index.js') }}"></script>
</head>
<body>
<div class="index-container">
    <header class="index-header">新歌更新</header>
    <main class="index-main">
        <!--顶部导航-->
        <nav class="index-main-nav">
            @foreach ($createdDate as $value)
                <a href="javascript:;" id="{{$value->id}}">{{date("Y-m-d",strtotime($value->created_date))}}</a>
            @endforeach
        </nav>
        <!--新歌统计-->
        <div class="index-main-statistics">
            <header>新歌统计:</header>
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>新歌总数：<span id="songnum"></span>首</td>
                    <td></td>
                </tr>
                <tr>
                    <td>综艺歌曲数量：<span id=""></span>首</td>
                    <td>网络歌曲数量：<span id="netnum"></span>首</td>
                </tr>
                <tr>
                    <td>替换歌曲数量：<span id="replacenum"></span>首</td>
                    <td>热门新歌：<span id="hotnum"></span>首</td>
                </tr>
            </table>
        </div>
        <!--热门新歌推荐-->
        <div class="index-main-hot-recommend">
            <header>热门新歌推荐:</header>
            <table cellpadding="0" cellspacing="0" border="0" style="table-layout:fixed">
                <tr>
                    <th>歌星：</th>
                    <th>歌名：</th>
                    <th>语种：</th>
                    <th>专辑：</th>
                </tr>
                <tbody class="songList">

                </tbody>
            </table>
        </div>
    </main>
    <footer class="index-footer"></footer>
</div>
</body>
</html>

<script>
    var url = "{{$url}}";
    $(function () {
        var index1 = $(".index-main-nav").children("a:first-child").attr('id');
        var first = $(".index-main-nav").children("a:first-child");
        first.addClass("active").siblings().removeClass("active");
        ajax1(index1,url);

        tabSwitch(".index-main-nav a",function (index,url) {
            ajax1(index,url);
        })
    });

    var ajax1 = function(index,url){
        $.ajax({
            url: url,
            data: {index:index},
            method: 'get',
            dataType: 'json',
            success:function(data){
                var data = data.data;
                $('#songnum').html(data.songnum);
                $('#varietynum').html(data.varietynum);
                $('#netnum').html(data.netnum);
                $('#replacenum').html(data.replacenum);
                $('#hotnum').html(data.hotnum);
                var song = eval("("+data.song+")");
                var html="";

                for(var i in song){
                    var singername = typeof(song[i].singername)=='undefined'?'':song[i].singername;
                    var songname = typeof(song[i].songname)=='undefined'?'':song[i].songname;
                    var lan = typeof(song[i].lan)=='undefined'?'':song[i].lan;
                    var album = typeof(song[i].album)=='undefined'?'':song[i].album;
                    html+= "<tr>\n" +
                        "<td>"+singername+"</td>\n" +
                        "<td>"+songname+"</td>\n" +
                        "<td>"+lan+"</td>\n" +
                        "<td>"+album+"</td>\n" +
                        "<tr>\n"
                }
                $(".index-main-hot-recommend .songList").html(html);
            }
        });
    };
</script>