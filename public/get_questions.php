<?php
// 数据库连接配置
include 'db_connection.php';
// 查询题目和选项
$sql = "SELECT ID, question, A, B, C, D, E FROM singlechoice";
$result = $conn->query($sql);

$questions = []; // 初始化$questions变量
if ($result && $result->num_rows > 0) {
  $questions = $result->fetch_all(MYSQLI_ASSOC);
} else {
  echo "没有找到题目数据。";
}

// 不在这里关闭连接，以便在其他文件中继续使用 $conn
?>
