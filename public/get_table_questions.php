<?php
// 数据库连接
include 'db_connection.php';

// 查询 tablequestion 表获取行问题
$query = "SELECT * FROM tablequestion";
$result = $conn->query($query);

$table_questions = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $table_questions['Q1'] = $row['Q1'];
    $table_questions['Q2'] = $row['Q2'];
    $table_questions['Q3'] = $row['Q3'];
  }
}

// 关闭数据库连接
$conn->close();
?>
