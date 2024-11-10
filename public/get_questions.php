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

// 查询题目和选项
$sql = "SELECT id, question, A, B, C, D, E FROM singlechoice";
$result = $conn->query($sql);

$questions = [];
if ($result && $result->num_rows > 0) {
  $questions = $result->fetch_all(MYSQLI_ASSOC);
} else {
  echo "没有找到题目数据。";
}

// 不在这里关闭连接，以便在其他文件中可以继续使用 $conn
// $conn->close();
?>
