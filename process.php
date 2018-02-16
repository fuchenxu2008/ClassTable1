<?php
// $handle = popen('/usr/bin/python3 -u program/main.py'.' '.$uname.' '.$psw,'r');
// while (!feof($handle)) {
    // echo str_repeat(" ",256);
    // $output = fgets($handle);
    // if ($output != '') {
        // echo "<script type='text/javascript'>document.body.innerHTML=''</script>";
        // echo "<script type='text/javascript'><link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,600' rel='stylesheet'></script><font style='color:red; font-family: 'Source Sans Pro', sans-serif;
        // font-weight: 600;'>".$output."</font>";
        // echo $output;
    // }
    // flush();
    // ob_flush();
// }
// pclose($handle);

$uname = trim($_POST['username']);
$psw = $_POST['password'];

// shell_exec("rm *.ics");
// echo "<script type='text/javascript'>alert('$uname');</script>";
// echo "<script type='text/javascript'>alert('$psw');</script>";
if (!empty($uname) && !empty($psw)) {
    if ($uname == 'dj'){
        daijin();
    }else {
        generate($uname, $psw);
    }
    // echo "<script type='text/javascript'>top.document.getElementById('loading').style.display='none';top.document.getElementById('go').style.display='block';</script>";
}
function generate($uname, $psw){
    $command = '/usr/bin/python3 program/main.py'.' '.$uname.' "'.$psw.'"';
    $output = exec($command);
    if ($output == 'Login Failed.') {
        // echo "<script type='text/javascript'>top.document.getElementById('loading').style.display='none';top.document.getElementById('go').style.display='block';</script>";
        // echo "<script type='text/javascript'>alert('Incorrect Username/Password...');</script>";
        echo "<script type='text/javascript'>top.document.getElementById('loading').style.display='none';top.document.getElementById('wrong').style.display='block';</script>";
        return;
    }
    if ($output == 'No class yet.') {
        echo "<script type='text/javascript'>top.document.getElementById('loading').style.display='none';top.document.getElementById('wrong').style.display='block';</script>";
        echo "<script>alert('Register at E-bridge first!')</script>";
        return;
    }

    $conn = new mysqli('localhost','root','Daohaolaji@','calendar');
    if ($conn) {
        $conn->query("set names 'utf8mb4'");
        $time=date("Y-m-d H:i:s");
        $agent=$_SERVER['HTTP_USER_AGENT'];
        preg_match("/\(\w*\;/",$agent,$device);
        $device=substr($device[0],1,-1);
        // echo "<script>alert('$device')</script>";
        $sql = "INSERT INTO download(User,Device,Time) VALUES('$uname','$device','$time')";
        $conn->query($sql);
        // return;
    }
    $conn->close();

    echo "<script type='text/javascript'>top.document.getElementById('loading').style.display='none';top.document.getElementById('ok').style.display='block';</script>";

    echo "<script>window.location.href='download.php?uname=$uname'</script>";
}

function daijin(){
    echo "<script type='text/javascript'>top.document.getElementById('loading').style.display='none';top.document.getElementById('go').style.display='none';top.document.getElementById('ok').style.display='block';</script>";
    echo "<script>window.location.href='download.php?uname=dj'</script>";
}
?>
