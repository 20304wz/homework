<?php
// 假设 $conn 是在其他文件中已经创建的数据库连接
$sql = "SELECT COUNT(*) as total FROM singlechoice";
$result = $conn->query($sql);

$total_questions = 0;
if ($result) {
  $row = $result->fetch_assoc();
  $total_questions = $row['total'];
}
?>
