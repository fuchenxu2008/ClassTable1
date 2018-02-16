<?php
$uname=$_GET['uname'];
session_write_close(); //关闭session，允许用户进行其他操作
set_time_limit(0); //关闭php执行超时
$filepath = './'.$uname.'.ics'; //文件路径
$filename = $uname.'.ics'; //显示给用户的名称
$encoded_filename = rawurlencode($filename); //支持中文字符
ob_clean(); //清除缓存区，防止header already send的错误

// 构造头部
header('Content-Description: File Transfer');
header("Content-Type: application/octet-stream"); //文件类型
header("Content-Length: ".filesize($filepath)); //文件大小
header('Expires: 0');

// 检查IE浏览器
if (preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false)) {
 header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
} else {
 header('Content-Disposition: attachment; filename="' . $filename . '"');
}
@readfile($filepath);

unlink($filename);

?>
