<?php
// 数据库连接配置
include 'db_connection.php';


// 查询多选题数据
$sql = "SELECT ID, name, A, B, C, D FROM mulquestion";
$result = $conn->query($sql);

$multiple_questions = []; // 初始化 $multiple_questions
if ($result && $result->num_rows > 0) {
  $multiple_questions = $result->fetch_all(MYSQLI_ASSOC);
} else {
  echo "没有找到多选题数据。";
}
?>
