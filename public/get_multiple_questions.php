<?php
// 显示 PHP 错误（调试用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 数据库连接配置
$servername = "localhost";
$username = "root";
$password = "20030304Yjm."; // 根据实际密码填写
$dbname = "questionnaire";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("连接失败: " . $conn->connect_error);
}

// 获取多选题内容
$multiple_questions = [];
$sql_questions = "SELECT id, name , A, B, C, D FROM mulquestion";
$result_questions = $conn->query($sql_questions);

if ($result_questions && $result_questions->num_rows > 0) {
  $multiple_questions = $result_questions->fetch_all(MYSQLI_ASSOC);
}

// 关闭连接
$conn->close();
?>
