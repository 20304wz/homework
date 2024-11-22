<?php
include 'db_connection.php';

$table = $_GET['table'] ?? ''; // 获取表名
$order_column = $_GET['order_column'] ?? 'id'; // 默认排序列为 'id'
$order_direction = $_GET['order_direction'] ?? 'ASC'; // 默认排序方向为升序

// 验证表名和排序列
if (!$table) {
  echo '<p>未选择表格。</p>';
  exit;
}

// 获取列信息
$columnsQuery = "SHOW COLUMNS FROM `$table`";
$columnsResult = $conn->query($columnsQuery);

if (!$columnsResult) {
  echo "<p>无法获取表结构: " . $conn->error . "</p>";
  exit;
}

$columns = [];
while ($column = $columnsResult->fetch_assoc()) {
  $columns[] = $column['Field'];
}

// 验证排序列是否有效
if (!in_array($order_column, $columns)) {
  $order_column = 'id';
}

// 验证排序方向
$order_direction = strtoupper($order_direction) === 'DESC' ? 'DESC' : 'ASC';

// 查询数据
$dataQuery = "SELECT * FROM `$table` ORDER BY `$order_column` $order_direction";
$dataResult = $conn->query($dataQuery);

if (!$dataResult) {
  echo "<p>无法加载表内容: " . $conn->error . "</p>";
  exit;
}

// 输出 HTML 表格
echo '<table>';
echo '<thead><tr>';
foreach ($columns as $column) {
  $new_direction = ($order_column === $column && $order_direction === 'ASC') ? 'DESC' : 'ASC';
  echo "<th><a href='?table=$table&order_column=$column&order_direction=$new_direction'>$column</a></th>";
}
echo '</tr></thead>';
echo '<tbody>';
while ($row = $dataResult->fetch_assoc()) {
  echo '<tr>';
  foreach ($columns as $column) {
    echo "<td>{$row[$column]}</td>";
  }
  echo '</tr>';
}
echo '</tbody>';
echo '</table>';

// 关闭连接
$conn->close();
?>
