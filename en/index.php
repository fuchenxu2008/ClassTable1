<?php
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
    <title>Add Your Class To Calendar</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/bootstrap3.3.7.min.css">
    <link href="../css/Source+Sans+Pro.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/swiper-3.4.2.min.css">
    <link rel="stylesheet" type="text/css" href="../css/iconfont.css">
    <link rel="stylesheet" type="text/css" href="../css/index.css">
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
                <a class="navbar-brand" href="#"><i class="fa fa-calendar" aria-hidden="true" style="padding-right:7px;"></i> Class Calendar</a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a data-toggle="modal" data-target="#manual" style="cursor:pointer;">Manual</a></li>
                    <li><a data-toggle="modal" data-target="#about" style="cursor:pointer;">About</a></li>
                    <li><a href="../" style="cursor:pointer;">中文版本</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>
    <div class="container" style="padding-left:40px; padding-right:40px;">
        <div class="swiper-container" style="margin-bottom:5px;">
            <div class="swiper-wrapper" style="margin-bottom:30px;">
                <div class="swiper-slide"><img src="../pic/cal1.jpg" style="width:100%;padding:0;border:none; border-radius:10px; box-shadow: 5px 5px 20px rgba(200, 200, 200, .7);
                    transform-style: preserve-3d; cursor:pointer;" data-toggle="modal" data-target="#manual"></div>
                    <div class="swiper-slide"><img src="../pic/about.jpg" style="width:100%; padding:0;border:none; border-radius:10px; box-shadow: 5px 5px 20px rgba(200, 200, 200, .7);
                        transform-style: preserve-3d;" data-toggle="modal" data-target="#about"></div>
                        <div class="swiper-slide"><img src="../pic/intro.jpg" style="width:100%; padding:0;border:none; border-radius:10px; box-shadow: 5px 5px 20px rgba(200, 200, 200, .7);
                            transform-style: preserve-3d;"></div>
                        </div>
                        <!-- 如果需要分页器 -->
                        <div class="swiper-pagination" style="margin-bottom:-7px;"></div>
                    </div>
                    <form action="../process.php" id="login" class="form-group" method="post" target="formsubmit">
                        <input type="text" class="form-control" name="username" placeholder="Username" required style="padding-left: 25px;margin-bottom: 20px;background-color: rgb(255, 255, 255); height: 60px; border: none; border-radius:30px; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                        transform-style: preserve-3d; font-size:20px;">
                        <input type="password" class="form-control" name="password" placeholder="Password" required style="padding-left: 25px;margin-bottom: 20px;background-color: rgb(255, 255, 255); height: 60px; border: none; border-radius:30px; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                        transform-style: preserve-3d; font-size:20px;">
                        <input id="go" type="submit" class="center-block btn" value="Go" style="font-weight:600; height: 60px; border: none;border-radius:30px; padding-left:60px; padding-right:60px; background-color:rgb(103,106,251); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                        transform-style: preserve-3d; font-size:20px;">
                    </form>
                    <iframe style="display: none;" name="formsubmit"></iframe>
                    <button id="loading" class="center-block" style="margin-bottom: 20px; height: 55px; background:transparent; border:none; display:none;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color:rgb(103,106,251);"></i></button>
                    <button id="ok" class="center-block btn" style="margin-bottom: 15px;font-weight:600; height: 60px; border: none;border-radius:30px; padding-left:45px; padding-right:45px; background-color:rgb(60,203,62); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                    transform-style: preserve-3d; font-size:20px;display:none;"><i class="fa fa-check" aria-hidden="true"></i> OK</button>
                    <button id="wrong" class="center-block btn" style="margin-bottom: 15px;font-weight:600; height: 60px; border: none;border-radius:30px; padding-left:45px; padding-right:45px; background-color:rgb(252,58,68); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                    transform-style: preserve-3d; font-size:20px;display:none;"><i class="fa fa-times" aria-hidden="true"></i> Wrong</button>
                    <div class="text-center" style="font-weight:300;">
                        Please read <a id="manualbtn" style="color:rgb(103,106,251);text-decoration:underline; cursor:pointer;">Manual</a> before pressing 'Go'<br />
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
                                    <h3>Get Started</h3>
                                    <ol>
                                        <li style="font-weight: 300;">Enter your <b style="color:rgb(103,106,251);">E-bridge</b> Username and Password to authenticate.</li>
                                        <li style="font-weight: 300;">Emmm...That's it. XD</li>
                                    </ol>
                                    <hr>
                                    <h3>Some Issues</h3>
                                    <ol>
                                        <li style="font-weight: 300;">You should use a <b style="color:rgb(103,106,251);">standard</b> browser like Safari, Chrome, UC. In-app browsers like <b style="color:rgb(103,106,251);">WeChat are not supported!</b></li>
                                        <li style="font-weight: 300;">The whole process will take <b style="color:rgb(103,106,251);">8-15 seconds</b>, hold still.</li>
                                        <li style="font-weight: 300;">If you are using Android, Mac and Windows, a '.ics' file will be offered which should be added to your default calendar on click.
                                            <li style="font-weight: 300;">Attention: Your password is only used to authenticate your identity in E-bridge.</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" class="center-block btn" data-dismiss="modal" value="Close" style="font-weight:600; height: 40px; border: none;border-radius:30px; padding-left:60px; padding-right:60px; background-color:rgb(103,106,251); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
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
                                <h2 class="modal-title text-center" style="color:rgb(103,106,251); font-weight:600;">About</h2>
                            </div>
                            <div class="modal-body">
                                <div class="container" style="width:100%;">
                                    <h3 style="margin-top:0;">Inspiration</h3>
                                    <p style="font-size:16px;font-weight: 300; padding-left:20px;padding-right:10px;">We used to make a sceenshot of our timetables on E-bridge, and either had to check it in the album now and then or even had to make it our lockscreen wallpaper to be kept updated what's next for class. Some might add their classes to calendar one by one
                                        to keep track, but man, that's simply painful.<br />Now with this handy tool, you can add all your classes to your calendar automatically once for all with one touch, and you can get to check your classes incredibly easily and in a more graceful way.</p>
                                        <hr>
                                        <h3 style="margin-top:0;">Contact</h3>
                                        <p style="font-size:16px; font-weight: 300;padding-left:20px;padding-right:10px;">Thank you for visiting my website, <span style="font-weight: 300;color:rgb(103,106,251);">please check twice after exporting your timetable to see if there's any error</span>. You should pay attention to the correctness of classes in corresponding weeks.</p>
                                        <p style="font-size:16px;font-weight: 300;padding-left:20px;padding-right:10px;">If you come across any issues or have any further need in my work, feel free to contact me~<br />And if you find this really useful, it would be so nice of you to share it around :)
                                            <div class="row" style="margin-left:15px;margin-right:15px;">
                                                <a class="col-xs-12 col-sm-6" type="button" href="mailto:fuchenxu2008@163.com" style="font-weight:600;border: none;border-radius:30px; padding-left:20px; padding-right:20px; padding-top:5px;padding-bottom:5px;background-color:rgb(180,180,180); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                                                transform-style: preserve-3d; font-size:16px; margin-top:10px;text-decoration:none;text-align:center;">Email:<br />fuchenxu2008@163.com</a>
                                                <button id="wechat" data-clipboard-action="copy" data-clipboard-text="553597230" class="col-xs-12 col-sm-5 col-sm-offset-1" type="button" style="font-weight:600;border: none;border-radius:30px; padding-left:20px; padding-right:20px; padding-top:5px;padding-bottom:5px; background-color:rgb(180,180,180); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                                                transform-style: preserve-3d; font-size:16px; margin-top:10px;">WeChat:<br />553597230</button>
                                            </div>
                                        </p>
                                        <hr style="margin-top:10px;margin-bottom:10px;">
                                        <h3 style="margin-top:0px;">Declaration</h3>
                                        <p style="font-size:16px; font-weight: 300;padding-left:20px;padding-right:10px;color:grey;">This site can be freely shared and used on any third-party platforms, but restricted to non-profit purpose only. Please contact the developer in advance if any wants to repost, thanks!

                                            <!-- <p>* This version is particularly optimised for the weird class timetable of EEE :)</p> -->
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="button" class="center-block btn" data-dismiss="modal" value="Close" style="font-weight:600; height: 40px; border: none;border-radius:30px; padding-left:60px; padding-right:60px; background-color:rgb(103,106,251); color:white; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                                        transform-style: preserve-3d; font-size:20px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="bulletin" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content" style='background-color: rgba(240, 242, 246, 1); border:none; box-shadow: 5px 5px 20px rgba(200, 200, 200, .5);
                                transform-style: preserve-3d;'>
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h2 class="modal-title text-center" style="color:rgb(103,106,251); font-weight:600;">Bulletin</h2>
                                </div>
                                <div class="modal-body">
                                    <div class="container" style="width:100%;">
                                        <!-- <h3 style="margin-top:0;">灵感来源</h3> -->
                                        <!-- <p style="font-size:16px;font-weight: 300; color:grey;">我们的服务器遇到了一些问题，正在抢修，如果您不能访问或无法抓取课表，请移步<a href="http://timetable.testfield.ml">timetable.testfield.ml</a>或<a href="http://stevesite.tk/timetable">stevesite.tk/timetable</a>如有疑问请联系我，在此带来的不便敬请谅解。</p> -->
                                        <p style="font-size:21px;font-weight: 300; color:grey;text-align:center;"><b>👨🏻‍💻NEW: Week 2-14 For Freshman!</b></p>
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
                <h4 style="float:left; margin-bottom:10px;"><i class="iconfont icon-View"></i> Views: <?php echo $_SESSION['views']; ?></h4>
                <h4 style="float:right; margin-bottom:10px;"><i class="iconfont icon-upload-demo"></i> Downloads: <?php echo $_SESSION['downloads']; ?></h4>
                <div id="wxhint">
                    <img id="arrow" src="../pic/arrow.svg" height="60px">
                    <img id="safarilogo" src="../pic/safari.svg" height="60px">
                    <div id="text" class="col-xs-10 col-xs-offset-1">
                        Open in Safari or other standard mobile browsers.
                    </div>
                </div>
                <script src="../js/jquery1.10.2.min.js"></script>
                <script src="../js/bootstrap.min.js"></script>
                <script src="../js/fontawesome.js"></script>
                <script src="../js/swiper-3.4.2.jquery.min.js"></script>
                <script src="../js/clipboard.min.js"></script>
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
