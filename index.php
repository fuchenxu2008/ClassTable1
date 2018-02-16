<?php
header('Location: https://class.kyrie.top');
// $agent=$_SERVER['HTTP_USER_AGENT'];
// echo "<script>alert('$agent')</script>";
session_start();
$conn = new mysqli('localhost','root','Daohaolaji@','calendar');
if ($conn) {
    $conn->query("set names 'utf8mb4'");
    if (!isset($_SESSION['views'])) {
        $time=date("Y-m-d H:i:s");
        $clientIP=$_SERVER[REMOTE_ADDR];
        // echo "<script>alert('$clientIP')</script>";
        function getcposition($ip){
            $res1 = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=$ip");
            $res1 = json_decode($res1,true);
            if ($res1["code"]==0){
                return $res1['data']["country"].$res1['data'][ "region"].$res1['data']["city"]."_".$res1['data'][ "isp"];
            }else{
                return "未知";
            }
        }
        $position=getcposition($clientIP);
        $agent=$_SERVER['HTTP_USER_AGENT'];
        preg_match("/\(\w*\;/",$agent,$device);
        $device=substr($device[0],1,-1);
        $human=($device!=''&&$device!='compatible'&& !preg_match('/阿里巴巴/',$position));
        $addsql = "INSERT INTO view(Human,IP,Position,Device,Time) VALUES('$human','$clientIP','$position','$device','$time')";
        $conn->query($addsql);
    }
    $viewsql = "SELECT * FROM view WHERE human=TRUE";
    $result = $conn->query($viewsql);
    if ($result) {
        $view_rows = $result->num_rows;
    }
    $downsql = "SELECT * FROM download";
    $result = $conn->query($downsql);
    if ($result) {
        $down_rows = $result->num_rows;
    }
}
$_SESSION['views'] = $view_rows;
$_SESSION['downloads'] = $down_rows;
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>把课表交给日历管理</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="css/bootstrap3.3.7.min.css">
    <link href="css/Source+Sans+Pro.css" rel="stylesheet">
    <link rel="stylesheet" href="css/swiper-3.4.2.min.css">
    <link rel="stylesheet" type="text/css" href="css/iconfont.css">
    <link rel="stylesheet" type="text/css" href="css/index.css">
</head>
<body>
    <nav class="navbar navbar-default" style="border:none; background-color: white;">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar" style="border:none;">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="en/"><i class="fa fa-calendar" aria-hidden="true" style="padding-right:7px;"></i> 课表日历</a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a data-toggle="modal" data-target="#manual" style="cursor:pointer;">使用说明</a></li>
                    <li><a data-toggle="modal" data-target="#about" style="cursor:pointer;">关于和支持</a></li>
                    <li><a href="en/" style="cursor:pointer;">English Version</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>
    <div class="container" style="padding-left:40px; padding-right:40px;">
        <div class="swiper-container" style="margin-bottom:5px;">
            <div class="swiper-wrapper" style="margin-bottom:30px;">
                <div class="swiper-slide"><img src="pic/cal1.jpg" style="width:100%;padding:0;border:none; border-radius:10px; box-shadow: 5px 5px 20px rgba(200, 200, 200, .7);
                    transform-style: preserve-3d; cursor:pointer;" data-toggle="modal" data-target="#manual"></div>
                    <div class="swiper-slide"><img src="pic/about.jpg" style="width:100%; padding:0;border:none; border-radius:10px; box-shadow: 5px 5px 20px rgba(200, 200, 200, .7);
                        transform-style: preserve-3d;" data-toggle="modal" data-target="#about"></div>
                        <div class="swiper-slide"><img src="pic/intro.jpg" style="width:100%; padding:0;border:none; border-radius:10px; box-shadow: 5px 5px 20px rgba(200, 200, 200, .7);
                            transform-style: preserve-3d;"></div>
                        </div>
                        <!-- 如果需要分页器 -->
                        <div class="swiper-pagination" style="margin-bottom:-7px;"></div>
                    </div>
                    <form action="process.php" id="login" class="form-group" method="post" target="formsubmit">
                        <input type="text" class="form-control" name="username" placeholder="E-bridge 用户名" required style="padding-left: 25px;margin-bottom: 20px;background-color: rgb(255, 255, 255); height: 60px; border: none; border-radius:30px; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                        transform-style: preserve-3d; font-size:20px;">
                        <input type="password" class="form-control" name="password" placeholder="密码" required style="padding-left: 25px;margin-bottom: 20px;background-color: rgb(255, 255, 255); height: 60px; border: none; border-radius:30px; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                        transform-style: preserve-3d; font-size:20px;">
                        <input id="go" type="submit" class="center-block btn" value="开始" style="font-weight:600; height: 60px; border: none;border-radius:30px; padding-left:60px; padding-right:60px; background-color:rgb(103,106,251); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                        transform-style: preserve-3d; font-size:20px;">
                    </form>
                    <iframe style="display: none;" name="formsubmit"></iframe>
                    <button id="loading" class="center-block" style="margin-bottom: 20px; height: 55px; background:transparent; border:none; display:none;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color:rgb(103,106,251);"></i></button>
                    <button id="ok" class="center-block btn" style="margin-bottom: 15px;font-weight:600; height: 60px; border: none;border-radius:30px; padding-left:45px; padding-right:45px; background-color:rgb(60,203,62); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                    transform-style: preserve-3d; font-size:20px;display:none;"><i class="fa fa-check" aria-hidden="true"></i> 成功</button>
                    <button id="wrong" class="center-block btn" style="margin-bottom: 15px;font-weight:600; height: 60px; border: none;border-radius:30px; padding-left:45px; padding-right:45px; background-color:rgb(252,58,68); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                    transform-style: preserve-3d; font-size:20px;display:none;"><i class="fa fa-times" aria-hidden="true"></i> 错误</button>
                    <div class="text-center" style="font-weight:300;">
                        请在开始前阅读<a id="manualbtn" style="color:rgb(103,106,251);text-decoration:underline; cursor:pointer;">使用说明</a><br />
                        Powered By <a id="aboutbtn" style="color:rgb(103,106,251);text-decoration:underline; cursor:pointer;">Chenxu.Fu15</a>
                    </div>
                    <div class="modal fade" id="manual" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content" style='background-color: rgba(240, 242, 246, 1); border:none; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                            transform-style: preserve-3d;'>
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h2 class="modal-title text-center" style="color:rgb(103,106,251); font-weight:600;">User Manual</h2>
                            </div>
                            <div class="modal-body">
                                <div class="container" style="width:100%;">
                                    <h3>让我们开始吧：</h3>
                                    <ol>
                                        <li style="font-weight: 300;">输入你的<b style="color:rgb(103,106,251);">E-bridge</b>用户名和密码来验证登录。</li>
                                        <li style="font-weight: 300;">等待8-15秒来让后台帮您生成日历。</li>
                                        <li style="font-weight: 300;">不要忘记核对一下与E-bridge上的官方课表是否一致～</li>
                                    </ol>
                                    <hr>
                                    <h3>注意事项</h3>
                                    <ol>
                                        <li style="font-weight: 300;">应当使用<b style="color:rgb(103,106,251);">标准</b>浏览器来访问本网页，比如Safari, Chrome, UC。<b style="color:rgb(103,106,251);">不支持微信一类的应用内置浏览器!</b></li>
                                        <li style="font-weight: 300;">整个生成过程会持续<b style="color:rgb(103,106,251);">8-15秒</b>, 请耐心等待。</li>
                                        <li style="font-weight: 300;">如果你使用的是iOS设备但无法添加日历，请确保<b style="color:rgb(103,106,251);">设置-iCloud-日历</b>已经开启。</li>
                                        <li style="font-weight: 300;">如果你使用的是非iOS设备，浏览器将下载一个.ics文件。打开后（安卓选择用日历📅打开）应自动提示添加至您的默认日历。
                                            <li style="font-weight: 300;">免责声明：您的密码将仅用于登录E-bridge验证身份，本站不会用于其他用途。</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" class="center-block btn" data-dismiss="modal" value="关闭" style="font-weight:600; height: 40px; border: none;border-radius:30px; padding-left:60px; padding-right:60px; background-color:rgb(103,106,251); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                                    transform-style: preserve-3d; font-size:20px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="about" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content" style='background-color: rgba(240, 242, 246, 1); border:none; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                            transform-style: preserve-3d;'>
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h2 class="modal-title text-center" style="color:rgb(103,106,251); font-weight:600;">关于和支持</h2>
                            </div>
                            <div class="modal-body">
                                <div class="container" style="width:100%;">
                                    <h3 style="margin-top:0;">灵感来源</h3>
                                    <p style="font-size:16px;font-weight: 300; padding-left:20px;padding-right:10px; color:grey;">在过去，新学期公布课表时，我们常常只能在E-bridge上把课表截图存在相册中, 然后不得不经常反复的去相册翻看自己的课表又或是把其设置为手机锁屏壁纸来获知接下来的课程安排。
                                        有些同学还会手动一条一条地把课表输进日历里去，但是那真的是太麻烦了。<br />现在通过这个实用的小工具，您可以一次性把自己所有的课程添加至日历，而操作过程仅仅是登录验证，然后你就可以轻松的通过手机通知中心，智能手表等其他工具快速的查看管理自己接下来的日程！不仅更方便，而且更加简单。</p>
                                        <hr>
                                        <h3 style="margin-top:0;">联系方式</h3>
                                        <p style="font-size:16px; font-weight: 300;padding-left:20px;padding-right:10px;color:grey;">感谢您访问本网站，<span style="font-weight: 300;color:rgb(103,106,251);">请在导出日历后仔细核对是否与官方课表一致</span>。（您应额外注意课程和其对应的教学周是否匹配）</p>
                                        <p style="font-size:16px;font-weight: 300;padding-left:20px;padding-right:10px;color:grey;">如果您遇到了任何问题，发现了错误或有其他需求，可以通过以下两种方式联系我~<br />如果您觉得本工具很好用，欢迎帮忙转发支持哈 :)
                                            <div class="row" style="margin-left:15px;margin-right:15px;">
                                                <a class="col-xs-12 col-sm-6" type="button" href="mailto:fuchenxu2008@163.com" style="font-weight:600;border: none;border-radius:30px; padding-left:20px; padding-right:20px; padding-top:5px;padding-bottom:5px;background-color:rgb(180,180,180); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                                                transform-style: preserve-3d; font-size:16px; margin-top:10px;text-decoration:none;text-align:center;">邮箱:<br />fuchenxu2008@163.com</a>
                                                <button id="wechat" data-clipboard-action="copy" data-clipboard-text="553597230" class="col-xs-12 col-sm-5 col-sm-offset-1" type="button" style="font-weight:600;border: none;border-radius:30px; padding-left:20px; padding-right:20px; padding-top:5px;padding-bottom:5px; background-color:rgb(180,180,180); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                                                transform-style: preserve-3d; font-size:16px; margin-top:10px;">微信:<br />fuchenxu2008</button>
                                            </div>
                                        </p>
                                        <hr style="margin-top:10px;margin-bottom:10px;">
                                        <h3 style="margin-top:0px;">其他声明</h3>
                                        <p style="font-size:16px; font-weight: 300;padding-left:20px;padding-right:10px;color:grey;">本站可在其他第三方平台自由转发使用，但仅限用于非盈利用途，转发使用前请联系开发者，谢谢！
                                            <!-- <p>* This version is particularly optimised for the weird class timetable of EEE :)</p> -->
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="button" class="center-block btn" data-dismiss="modal" value="关闭" style="font-weight:600; height: 40px; border: none;border-radius:30px; padding-left:60px; padding-right:60px; background-color:rgb(103,106,251); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                                        transform-style: preserve-3d; font-size:20px;">
                                    </div>
                                </div>
                            </div>
                        </div>
<a href="https://www.apple.com" hidden="true">Apple</a>
                        <div class="modal fade" id="bulletin" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content" style='background-color: rgba(240, 242, 246, 1); border:none; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                                transform-style: preserve-3d;'>
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h2 class="modal-title text-center" style="color:rgb(103,106,251); font-weight:600;">告示</h2>
                                </div>
                                <div class="modal-body">
                                    <div class="container" style="width:100%;">
                                        <!-- <h3 style="margin-top:0;">灵感来源</h3> -->
                                        <!-- <p style="font-size:16px;font-weight: 300; color:grey;">我们的服务器遇到了一些问题，正在抢修，如果您不能访问或无法抓取课表，请移步<a href="http://timetable.testfield.ml">timetable.testfield.ml</a>或<a href="http://stevesite.tk/timetable">stevesite.tk/timetable</a>如有疑问请联系我，在此带来的不便敬请谅解。</p> -->
                                        <p style="font-size:21px;font-weight: 300; color:grey;text-align:center;"><b>👨🏻‍💻NEW: 17届新生 2-14 周课表！</b></p>
                                        <!-- <hr> -->
                                        <!-- <h3 style="margin-top:0;">联系方式</h3> -->

                                        <!-- <a class="col-xs-12 col-sm-6" type="button" href="mailto:fuchenxu2008@163.com" style="font-weight:600;border: none;border-radius:30px; padding-left:20px; padding-right:20px; padding-top:5px;padding-bottom:5px;background-color:rgb(180,180,180); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                                        transform-style: preserve-3d; font-size:16px; margin-top:10px;text-decoration:none;text-align:center;">邮箱:<br />fuchenxu2008@163.com</a>
                                        <button id="wechat" data-clipboard-action="copy" data-clipboard-text="553597230" class="col-xs-12 col-sm-5 col-sm-offset-1" type="button" style="font-weight:600;border: none;border-radius:30px; padding-left:20px; padding-right:20px; padding-top:5px;padding-bottom:5px; background-color:rgb(180,180,180); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                                        transform-style: preserve-3d; font-size:16px; margin-top:10px;">微信:<br />553597230</button>
                                    </p> -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="button" class="center-block btn" data-dismiss="modal" value="关闭" style="font-weight:600; height: 40px; border: none;border-radius:30px; padding-left:60px; padding-right:60px; background-color:rgb(103,106,251); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                                transform-style: preserve-3d; font-size:20px;">
                            </div>
                        </div>
                    </div>
                </div>


                <h4 style="float:left; margin-bottom:10px;"><i class="iconfont icon-View"></i> 阅读: <?php echo $_SESSION['views']; ?></h4>
                <h4 style="float:right; margin-bottom:10px;"><i class="iconfont icon-upload-demo"></i> 下载: <?php echo $_SESSION['downloads']; ?></h4>
                <div id="wxhint">
                    <img id="arrow" src="pic/arrow.svg" height="60px">
                    <img id="safarilogo" src="pic/safari.svg" height="60px">
                    <div id="text" class="col-xs-10 col-xs-offset-1">
                        请使用Safari, Chrome或其他标准浏览器打开
                    </div>
                </div>
            </div>
            <script src="js/jquery1.10.2.min.js"></script>
            <script src="js/bootstrap.min.js"></script>
            <script src="js/fontawesome.js"></script>
            <script src="js/swiper-3.4.2.jquery.min.js"></script>
            <script src="js/clipboard.min.js"></script>
            <script type="text/javascript">
            downloaded()

            $('#bulletin').modal('show')

            function downloaded(){
                var ua = navigator.userAgent.toLowerCase();
                if(ua.match(/MicroMessenger/i)=="micromessenger") {
                    // alert("Please open in standard mobile browser.\n\n请在手机浏览器中打开。");
                    // window.location.href='com.baidu.tieba://'
                    $('#wxhint').fadeIn()
                }
            }
            var swiper = new Swiper('.swiper-container',{
                loop: true,
                // 如果需要分页器
                pagination: '.swiper-pagination',
                paginationClickable: true,
                spaceBetween: 30,
                centeredSlides: true,
                autoplay: 4500,
                autoplayDisableOnInteraction: false
            });
            $('#login').submit(
                function(){
                    $('#go').hide()
                    $('#ok').hide()
                    $('#wrong').hide()
                    $('#loading').show()
                }
            );
            $('#manualbtn').click(
                function() {
                    $('#manual').modal('show')
                }
            )
            $('#aboutbtn').click(
                function() {
                    $('#about').modal('show')
                }
            )
            $('#wechat').click(
                function() {
                    var clipboard = new Clipboard('#wechat');
                    clipboard.on('success', function(e) {
                        alert("WeChat ID copied.\n\n复制成功！")
                    });
                    clipboard.on('error', function(e) {
                        alert("WeChat ID cannot be copied. Please input manually.\n\n复制失败！请手动输入")
                    });
                    window.location.href = 'wechat://'
                }
            )
            $('#wxhint').click(
                function() {
                    $('#wxhint').fadeOut()
                }
            )
            $('#ok').click(
                function() {
                    $('#ok').hide();
                    $('#go').show();
                }
            )
            $('#wrong').click(
                function() {
                    $('#wrong').hide();
                    $('#go').show();
                }
            )
            </script>
        </body>
        </html>
