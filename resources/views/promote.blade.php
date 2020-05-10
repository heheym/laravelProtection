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
            <header><img src="img/xingetongji.png" height="26" width="107"/></header>
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td><div class="statistics-content-box" style="margin-top: 0;"><span class="color-f6ff00">综艺</span>歌曲数量：<span class="little-font" id="varietynum"></span></div></td>
                    <td><div class="statistics-content-box" style="margin-top: 0;"><span class="color-f6ff00">网络</span>歌曲数量：<span class="little-font" id="netnum"></span></div></td>
                </tr>
                <tr>
                    <td><div class="statistics-content-box"><span class="color-f6ff00">替换</span>歌曲数量：<span class="little-font" id="replacenum"></span></div></td>
                    <td><div class="statistics-content-box"><span class="color-f6ff00">热门歌曲：</span><span class="little-font" id="hotnum"></span></div></td>
                </tr>
            </table>
        </div>
        <!--热门新歌推荐-->
        <div class="index-main-hot-recommend">
            <header><img src="img/hotSong.png" height="26" width="162"/></header>
            <ul class="songList">
                {{--<li>--}}
                    {{--<dl>--}}
                        {{--<dd>1</dd>--}}
                        {{--<dd>林俊杰</dd>--}}
                        {{--<dd>背对背拥抱</dd>--}}
                        {{--<dd>国语</dd>--}}
                        {{--<dd>JJ陆</dd>--}}
                    {{--</dl>--}}
                {{--</li>--}}
            </ul>
            <!--<table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <th>
                        <dl></dl>
                    </th>
                    <th>歌星：</th>
                    <th>歌名：</th>
                    <th>语种：</th>
                    <th>专辑：</th>
                </tr>
                <tr>
                    <td>刘德华</td>
                    <td>中国人</td>
                    <td>国语</td>
                    <td>《中国人》</td>
                </tr>
                <tr>
                    <td>刘德华2</td>
                    <td>中国人2</td>
                    <td>国语2</td>
                    <td>《中国人2》</td>
                </tr>
                <tr>
                    <td>刘德华22</td>
                    <td>中国人22</td>
                    <td>国语22</td>
                    <td>《中国人22》</td>
                </tr>
                <tr>
                    <td>刘德华222</td>
                    <td>中国人222</td>
                    <td>国语222</td>
                    <td>《中国人222》</td>
                </tr>
                <tr>
                    <td>刘德华22222</td>
                    <td>中国人22222</td>
                    <td>国语22222</td>
                    <td>《中国人22222》</td>
                </tr>
                <tr>
                    <td>刘德华2222222222</td>
                    <td>中国人中国人中国人中国人中国人中国人中国人中国人中国人中国人中国人中国人中国人中国人</td>
                    <td>国语2222222222</td>
                    <td>《中国人2222222222》</td>
                </tr>
            </table>-->
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
                $('#songnum').html(data.songnum+"首");
                $('#varietynum').html(data.varietynum+"首");
                $('#netnum').html(data.netnum+"首");
                $('#replacenum').html(data.replacenum+"首");
                $('#hotnum').html(data.hotnum+"首");
                var song = eval("("+data.song+")");
                var html="";

                for(var i in song){
                    var singername = typeof(song[i].singername)=='undefined'?' ':song[i].singername;
                    var songname = typeof(song[i].songname)=='undefined'?' ':song[i].songname;
                    var lan = typeof(song[i].lan)=='undefined'?' ':song[i].lan;
                    var album = typeof(song[i].album)=='undefined'?' ':song[i].album;
                    i++;
                    html+= "<li>\n" +
                        "<dl>\n" +
                        "<dd>"+i+"</dd>\n" +
                        "<dd>"+singername+"</dd>\n" +
                        "<dd>"+songname+"</dd>\n" +
                        "<dd>"+lan+"</dd>\n" +
                        "<dd>"+album+"</dd>\n" +
                        "</dl>\n" +
                        "</li>";
                }
                $(".index-main-hot-recommend .songList").html(html);
            }
        });
    };
</script>