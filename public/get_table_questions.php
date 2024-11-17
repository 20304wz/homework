<?php
// 数据库连接
include 'db_connection.php';

// 查询 tablequestion 表获取行问题
$query = "SELECT * FROM tablequestion";
$result = $conn->query($query);

$table_questions = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $table_questions[] = [
      'ID' => $row['ID'],
      'row_question' => $row['row_question']
    ];
  }
}

// 关闭数据库连接
$conn->close();
?>
