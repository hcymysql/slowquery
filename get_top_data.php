<?php

    require 'config.php';
    $result_echarts = mysqli_query($con,"SELECT db_max,user_max,SUM(ts_cnt) AS top_count FROM
(SELECT h.db_max,h.user_max,SUM(h.ts_cnt) AS ts_cnt
FROM mysql_slow_query_review AS r JOIN mysql_slow_query_review_history AS h
ON r.checksum=h.checksum
WHERE r.last_seen >= SUBDATE(NOW(),INTERVAL 14 DAY)
GROUP BY r.checksum) AS tmp
GROUP BY tmp.db_max");

    $top_data="";
    $array= array();

    class User{
    	public $db_max;
    	public $top_count;
    }

    while($row = mysqli_fetch_array($result_echarts,MYSQL_ASSOC)){
    	$user=new User();
    	$user->db_max = $row['db_max'];
    	$user->top_count = $row['top_count'];
    	$array[]=$user;
    }

    $top_data=json_encode($array);
    echo $top_data;

?>

