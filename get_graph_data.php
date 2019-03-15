<?php

    session_start();
    $dbname=$_SESSION['transmit_dbname'];

    require 'config.php';	
    $result_echarts = mysqli_query($con,"SELECT r.checksum,r.fingerprint,h.db_max,h.user_max,r.last_seen,SUM(h.ts_cnt) AS ts_cnt,
ROUND(MIN(h.Query_time_min),3) AS Query_time_min,ROUND(MAX(h.Query_time_max),3) AS Query_time_max,
ROUND(SUM(h.Query_time_sum)/SUM(h.ts_cnt),3) AS Query_time_avg,r.sample
FROM mysql_slow_query_review AS r JOIN mysql_slow_query_review_history AS h
ON r.checksum=h.checksum
WHERE db_max = '${dbname}' AND r.last_seen >= SUBDATE(NOW(),INTERVAL 14 DAY)
GROUP BY r.checksum
ORDER BY r.last_seen ASC,ts_cnt DESC");

    $data="";
    $array=array();

    class User{
    	public $last_seen;
    	public $Query_time_max;
    }

    while($row = mysqli_fetch_array($result_echarts,MYSQL_ASSOC)){
    	$user=new User();
    	$user->last_seen = $row['last_seen'];
    	$user->Query_time_max = $row['Query_time_max'];
    	$array[]=$user;
    }

    $data=json_encode($array);
    echo $data;

?>

