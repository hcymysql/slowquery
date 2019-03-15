<html>
<head>
    <meta http-equiv="Content-Type"  content="text/html;  charset=UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>慢查询日志</title>

<body>
    
<style>  
    td{border:1px solid red;}  
</style>  

<table  style="table-layout:fixed;width:100%"  border="1" cellpadding="1" cellspacing="0">                                    
<thead>                                   
<tr>                                    
<th style="width:30%">抽象语句</th>                                        
<th style="width:10%">数据库</th>
<th style="width:10%">用户名</th>
<th style="width:10%">最近时间</th>
<th style="width:5%">次数</th>
<th style="width:5%">平均时间</th>
</tr>
</thead>
<tbody>

<?php
    require '../config.php';
    $sql = "SELECT r.checksum,r.fingerprint,h.db_max,h.user_max,r.last_seen,SUM(h.ts_cnt) AS ts_cnt,
ROUND(MIN(h.Query_time_min),3) AS Query_time_min,ROUND(MAX(h.Query_time_max),3) AS Query_time_max,
ROUND(SUM(h.Query_time_sum)/SUM(h.ts_cnt),3) AS Query_time_avg,r.sample
FROM mysql_slow_query_review AS r JOIN mysql_slow_query_review_history AS h
ON r.checksum=h.checksum
WHERE r.last_seen >= SUBDATE(NOW(),INTERVAL 1 DAY)
GROUP BY r.checksum
ORDER BY r.last_seen DESC,ts_cnt DESC LIMIT 100";

    $result = mysqli_query($con,$sql);
    while($row = mysqli_fetch_array($result)) 
    {
	echo "<tr>";
	echo "<td width='30%'>" .$row['1'] ."</td>";
	echo "<td align='center'>{$row['2']}</td>";
	echo "<td align='center'>{$row['3']}</td>";
	echo "<td align='center'>{$row['4']}</td>";
	echo "<td align='center'>{$row['5']}</td>";
	echo "<td align='center'>{$row['8']}</td>";
	//echo "<td>{$row['6']}</td>";
	//echo "<td>{$row['7']}</td>";
	echo "</tr>";
    }

    echo "</table>";
    echo "</div>";
    echo "</div>";
    echo "</div>";

?>

