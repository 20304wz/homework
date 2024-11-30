<?php
$servername = "localhost"; // 数据库服务器
$username = "root"; // 数据库用户名
//$password = "zgy1356695061"; // 数据库密码
$password = "20030304Yjm."; // 数据库密码
$dbname = "questionnaire"; // 数据库名称

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
  die("连接失败: " . $conn->connect_error);
}

?>
