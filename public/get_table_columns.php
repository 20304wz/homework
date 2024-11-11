<?php
// 数据库连接
include 'db_connection.php';

// 查询 tableclass 表获取列问题
$query = "SELECT id, name FROM tableclass";
$result = $conn->query($query);

$table_columns = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $table_columns[] = [
      'id' => $row['id'],
      'name' => $row['name']
    ];
  }
}

// 关闭数据库连接
$conn->close();
?>
