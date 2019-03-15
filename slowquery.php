<?php 
    session_start();

    if($_GET['action'] == "logout"){  
        unset($_SESSION['transmit_dbname']);  
	exit('<script>top.location.href="slowquery.php"</script>');
    }     
?>

<html>
<head>
    <meta http-equiv="Content-Type"  content="text/html;  charset=UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>慢查询日志</title>
    <link rel="stylesheet" href="./css/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="./css/font-awesome/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="./css/styles.css">

<script language="javascript">
function TestBlack(TagName){
 var obj = document.getElementById(TagName);
 if(obj.style.display=="block"){
  obj.style.display = "none";
 }else{
  obj.style.display = "block";
 }
}
</script>

<script>
function ss(){
var slt=document.getElementById("select");
if(slt.value==""){
        alert("请选择数据库!!!");
        return false;
}
return true;
}
</script>
</head>

<body>
<div class="card">
<div class="card-header bg-light">
    <h1><a href="slowquery.php?action=logout">MySQL 慢查询分析</a></h1>
</div>
      
<div class="card-body">
<div class="table-responsive">                  
<form action="" method="post" name="sql_statement" id="form1" onsubmit=" return ss()">
  <div>
    <tr>
        <td><select id="select" name="dbname">
	<option value="">选择你的数据库</option>
	<?php
        	require 'config.php';
		$result = mysqli_query($con,"SELECT dbname FROM dbinfo");
		while($row = mysqli_fetch_array($result)){
			echo "<option value=\"".$row[0]."\">".$row[0]."</option>"."<br>";
    		}
    	?>
        </select><td>
    </tr>
    <input name="submit" type="submit" class="STYLE3" value="搜索" />
    </label>
  </div>
</form>

<?php
    if(isset($_POST['submit'])){
        $dbname=$_POST['dbname'];
        session_start();
	$_SESSION['transmit_dbname']=$dbname;
        require 'show.html';
    } else {
		session_start();
	    $dbname=$_SESSION['transmit_dbname'];
		if(!empty($dbname)){
			require 'show.html';
		} else {
			require 'top.html';
		}
    }
?>

<table class="table table-hover">                                    
<thead>                                   
<tr>                                    
<th>抽象语句</th>                                        
<th>数据库</th>
<th>用户名</th>
<th>最近时间</th>
<th>次数</th>
<th>平均时间</th>
<th>最小时间</th>
<th>最大时间</th>
</tr>
</thead>
<tbody>

<?php
      session_start();
	$dbname=$_SESSION['transmit_dbname'];
    require 'config.php';
    $perNumber=50; //每页显示的记录数  
    $page=$_GET['page']; //获得当前的页面值  
    $count=mysqli_query($con,"select count(*) from mysql_slow_query_review"); //获得记录总数
    $rs=mysqli_fetch_array($count);   
    $totalNumber=$rs[0];  
    $totalPage=ceil($totalNumber/$perNumber); //计算出总页数  

    if (empty($page)) {  
    	$page=1;  
    } //如果没有值,则赋值1

    $startCount=($page-1)*$perNumber; //分页开始,根据此方法计算出开始的记录 

    if(!empty($dbname)){
	$sql = "SELECT r.checksum,r.fingerprint,h.db_max,h.user_max,r.last_seen,SUM(h.ts_cnt) AS ts_cnt,
ROUND(MIN(h.Query_time_min),3) AS Query_time_min,ROUND(MAX(h.Query_time_max),3) AS Query_time_max,
ROUND(SUM(h.Query_time_sum)/SUM(h.ts_cnt),3) AS Query_time_avg,r.sample
FROM mysql_slow_query_review AS r JOIN mysql_slow_query_review_history AS h
ON r.checksum=h.checksum
WHERE h.db_max = '${dbname}'
AND r.last_seen >= SUBDATE(NOW(),INTERVAL 31 DAY)
GROUP BY r.checksum
ORDER BY r.last_seen DESC,ts_cnt DESC LIMIT $startCount,$perNumber";
    } else {
        $sql = "SELECT r.checksum,r.fingerprint,h.db_max,h.user_max,r.last_seen,SUM(h.ts_cnt) AS ts_cnt,
ROUND(MIN(h.Query_time_min),3) AS Query_time_min,ROUND(MAX(h.Query_time_max),3) AS Query_time_max,
ROUND(SUM(h.Query_time_sum)/SUM(h.ts_cnt),3) AS Query_time_avg,r.sample
FROM mysql_slow_query_review AS r JOIN mysql_slow_query_review_history AS h
ON r.checksum=h.checksum
WHERE r.last_seen >= SUBDATE(NOW(),INTERVAL 31 DAY)
GROUP BY r.checksum
ORDER BY r.last_seen DESC,ts_cnt DESC LIMIT $startCount,$perNumber";
    }

    $result = mysqli_query($con,$sql);

    echo "慢查询日志agent采集阀值是每10分钟/次，SQL执行时间（单位：秒）</br>";

    require 'SqlFormatter.php';

    while($row = mysqli_fetch_array($result)) 
    {
    	echo "<tr>";
	echo "<td width='100px' onclick=\"TestBlack('${row['0']}')\">✚  &nbsp;" .substr("{$row['1']}",0,50)  
     ."<div id='${row['0']}' style='display:none;'><a href='slowquery_explain.php?checksum={$row['0']}'>" .SqlFormatter::format($row['1']) ."</br></div></a></td>";
	echo "<td>{$row['2']}</td>";
	echo "<td>{$row['3']}</td>";
	echo "<td>{$row['4']}</td>";
	echo "<td>{$row['5']}</td>";
	echo "<td>{$row['8']}</td>";
	echo "<td>{$row['6']}</td>";
	echo "<td>{$row['7']}</td>";
	echo "</tr>";
    }
//end while

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    echo "</div>";
    echo "</div>";

    $maxPageCount=10; 
    $buffCount=2;
    $startPage=1;
 
    if ($page< $buffCount){
    	$startPage=1;
	}else if($page>=$buffCount  and $page<$totalPage-$maxPageCount){
        	$startPage=$page-$buffCount+1;
	}else{
    		$startPage=$totalPage-$maxPageCount+1;
	}
 
	$endPage=$startPage+$maxPageCount-1;
 
	$htmlstr="";
 
	$htmlstr.="<table class='bordered' border='1' align='center'><tr>";
    	if ($page > 1){
        	$htmlstr.="<td> <a href='slowquery.php?dbname=$dbname&page=" . "1" . "'>第一页</a></td>";
        	$htmlstr.="<td> <a href='slowquery.php?dbname=$dbname&page=" . ($page-1) . "'>上一页</a></td>";
    	}	

    	$htmlstr.="<td> 总共${totalPage}页</td>";

    	for ($i=$startPage;$i<=$endPage; $i++){ 
        	$htmlstr.="<td><a href='slowquery.php?dbname=$dbname&page=" . $i . "'>" . $i . "</a></td>";
    	}
     
    	if ($page<$totalPage){
        	$htmlstr.="<td><a href='slowquery.php?dbname=$dbname&page=" . ($page+1) . "'>下一页</a></td>";
        	$htmlstr.="<td><a href='slowquery.php?dbname=$dbname&page=" . $totalPage . "'>最后页</a></td>";
 
    	}

	$htmlstr.="</tr></table>";

	echo $htmlstr;

?>

