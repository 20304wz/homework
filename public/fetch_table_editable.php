<?php
include 'db_connection.php';

$table = $_GET['table'] ?? '';
if (!$table) {
  echo '<p>未选择表格。</p>';
  exit;
}

$columnsQuery = "SHOW COLUMNS FROM `$table`";
$columnsResult = $conn->query($columnsQuery);
if (!$columnsResult) {
  echo "<p>无法获取表结构: " . $conn->error . "</p>";
  exit;
}

$columns = [];
$hasIdColumn = false;
while ($column = $columnsResult->fetch_assoc()) {
  $columns[] = $column['Field'];
  if (strtolower($column['Field']) === 'id') {
    $hasIdColumn = true;
  }
}

if (!$hasIdColumn) {
  echo "<p>表 `$table` 不包含 `ID` 列。请确认表结构。</p>";
  exit;
}

$dataQuery = "SELECT * FROM `$table`";
$dataResult = $conn->query($dataQuery);
if (!$dataResult) {
  echo "<p>无法加载表内容: " . $conn->error . "</p>";
  exit;
}

echo '<table>';
echo '<thead><tr>';
foreach ($columns as $column) {
  echo "<th>$column</th>";
}
echo '</tr></thead>';
echo '<tbody>';
while ($row = $dataResult->fetch_assoc()) {
  echo '<tr>';
  foreach ($columns as $column) {
    $editableClass = strtolower($column) !== 'id' ? 'editable' : '';
    $dataId = $row['ID'] ?? $row['id'] ?? '';
    echo "<td class='$editableClass' data-id='$dataId' data-column='$column'>{$row[$column]}</td>";
  }
  echo '</tr>';
}
echo '</tbody>';
echo '</table>';
?>
