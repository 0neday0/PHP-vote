<?php
header("content-type:text/html;charset=gb2312");
print<<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-PHP-exam>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>投票系统（防刷票）</title>
</head>

<body>
<form id="form1" name="form1" method="post" action="">
你觉得2020年谁最优秀？
  <p>
    <label>
      <input type="radio" name="dxz" value="张三" id="dxz_0" />
      张三</label>
    <br />
    <label>
      <input type="radio" name="dxz" value="李四" id="dxz_1" />
      李四</label>
    <br />
    <label>
      <input type="radio" name="dxz" value="王五" id="dxz_2" />
      王五</label>
    
    <br />
  </p>
  <input type="submit" name="button" id="button" value="投他一票" />
</form>
</body>
</html>
EOT;
	if(isset($_POST['button']))   //判断按下按钮
	{	$xz = $_POST['dxz'];		//获取选择项
		if(!$xz)					//判断是否为空选
			echo "<script>alert('未选择投票选项！');</script>";
		else{
			$ip = $_SERVER['REMOTE_ADDR'];		//获取IP地址
			$time = date('Y-m-d H:i:s',date('U'));//获取当前时间
			$time_old = date('Y-m-d H:i:s',strtotime("$time -1 minute"));		//获取60s前的时间
			//准备连接连接数据库部分
			//设置数据库变量
			$db_host = 'localhost';
			$db_user = 'root';
			$db_passw = 'root';
			$db_name = 'vote_exam';
			//连接数据库
			$conn = mysql_connect($db_host,$db_user,$db_passw) or die ('数据库连接失败！</br>错误原因：'.mysql_error());			 
			 //设置字符集
			 mysql_query("set names 'gb2312'");			
			//选定数据库
			mysql_select_db($db_name,$conn) or die ('数据库选定失败！</br>错误原因：'.mysql_error());
			//执行SQL语句
			//判断是否有效
			
	/*		echo "$time<br>";
			echo "$time_old<br>";	*/

			$result = mysql_query("select * from vote where user_ip = '$ip' and use_xz = '$xz' and user_time  > '$time_old'") or die ('语句执行失败!</br>失败原因：'.mysql_error());		//查询该IP 60s内给同一选项投票数
			//echo "$result<br>";
			$count = mysql_num_rows($result);
			//echo "$count<br>";
			if($count >= 3)			//若投票数已经=3则投票无效
				echo "<script>alert('来自 $ip 给 $xz 的投票在60s内超过3次，投票无效！');</script>";
			else{
				mysql_query("insert into vote (use_xz,user_ip,user_time) values('$xz','$ip','$time')") or die ('语句执行失败!</br>失败原因：'.mysql_error());		//将投票记录插入数据库
				echo "<script>alert('来自 $ip 给 $xz 的投票有效！');</script>";
				echo "目前张三的票数为：";
				$result_1 = mysql_query("select * from vote where use_xz = '张三'");
				$xz_1 = mysql_num_rows($result_1);
				echo "<font color='blue' size='6'>$xz_1</font><br>";
				echo "目前李四的票数为：";
				$result_2 = mysql_query("select * from vote where use_xz = '李四'");
				$xz_2 = mysql_num_rows($result_2);
				echo "<font color='blue' size='6'>$xz_2</font><br>";
				echo "目前王五的票数为：";
				$result_3 = mysql_query("select * from vote where use_xz = '王五'");
				$xz_3 = mysql_num_rows($result_3);
				echo "<font color='blue' size='6'>$xz_3</font><br>";
				echo "总票数为：";
				$result_all = mysql_query("select * from vote");
				$xz_all = mysql_num_rows($result_all);
				echo "<font color='red' size='10'> $xz_all </font>";
				}

			}
	}
?>
