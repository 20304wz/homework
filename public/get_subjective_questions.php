<?php
// 显示 PHP 错误（调试用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 数据库连接配置
include 'db_connection.php';
// 从subjective表中获取主观题数据
$subjective_questions = [];
$sql = "SELECT ID, question FROM subjective";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $subjective_questions[] = $row;
  }
}

$conn->close();

