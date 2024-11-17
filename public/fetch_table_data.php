<?php
// 数据库连接设置
include 'db_connection.php';

// 获取表名
if (isset($_GET['table'])) {
  $table = $_GET['table'];
  $sql = "SELECT * FROM `$table`";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<thead><tr>";

    // 输出表头
    $field_info = $result->fetch_fields();
    foreach ($field_info as $field) {
      echo "<th>" . $field->name . "</th>";
    }

    echo "</tr></thead>";
    echo "<tbody>";

    // 输出表数据
    while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      foreach ($row as $cell) {
        echo "<td>" . htmlspecialchars($cell) . "</td>";
      }
      echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
  } else {
    echo "<p>表中无数据或表不存在。</p>";
  }
} else {
  echo "<p>未指定表名。</p>";
}

// 关闭连接
$conn->close();
?>
