<?php   
     
//将里面的配置改成你的运维管理机MySQL的地址

     $con = mysqli_connect("192.168.148.9","admin","123456","sql_db","3306") or die("数据库链接错误".mysqli_connect_error());  
     mysqli_query($con,"set names utf8");  
    
?>  
