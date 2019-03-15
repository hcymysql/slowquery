<?php

$get_mail_content = get_include_contents('get_top100_slowsql.php');

function get_include_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    return false;
}

    $smtpserver = "smtp.126.com";//SMTP服务器
    $smtpserverport = 25;//SMTP服务器端口
    $smtpusermail = "chunyang_he@126.com";//SMTP服务器的用户邮箱
    $smtpemailto = 'chunyang_he@126.com';//发送给谁
    $smtpuser = "chunyang_he@126.com";//SMTP服务器的用户帐号，注：部分邮箱只需@前面的用户名
    $smtppass = "123456";//SMTP服务器的授权码
    $mailtitle='【告警】慢查询报警推送TOP100条,请及时优化.';
    $mailcontent='下面的慢查询语句或许会影响到数据库的稳定性和健康性，请您在收到此邮件后及时优化语句或代码。数据库的稳定性需要大家的共同努力,感谢您的配合！<br><br>' .$get_mail_content .'<br><br>该邮件由slowquery系统自动发出，请勿回复，语句详细执行情况请登录<a href="http://xxxxx">slowquery系统查看.<br><br>';

system("./sendEmail -f $smtpusermail -t $smtpemailto  -s $smtpserver:$smtpserverport -u '$mailtitle' -o message-charset=utf8 -o message-content-type=html -m '$mailcontent' -xu $smtpusermail  -xp '$smtppass'");

?>
