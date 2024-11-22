<?php
// 数据库连接设置
include 'db_connection.php';

// 获取所有表名
$sql = "SHOW TABLES";
$result = $conn->query($sql);
$tables = [];
while ($row = $result->fetch_array()) {
  $tables[] = $row[0];
}

// 提示信息
$message = "";

// 关闭连接
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>支持排序的表格查看与插入</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }
    h1 {
      text-align: center;
    }
    form {
      margin-bottom: 20px;
    }
    input, select, button {
      margin: 10px 0;
      padding: 5px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #f4f4f4;
    }
    th a {
      text-decoration: none;
      color: #000;
    }
    .message {
      margin-top: 20px;
      color: green;
    }
  </style>
</head>
<body>
<h1>点击列头进行排序</h1>

<!-- 选择表格 -->
<form method="POST" id="add-form">
  <label for="table_name">选择表格：</label>
  <select name="table_name" id="table_name" required>
    <option value="">-- 请选择表格 --</option>
    <?php foreach ($tables as $table): ?>
      <option value="<?= $table ?>" <?= isset($_POST['table_name']) && $_POST['table_name'] === $table ? 'selected' : '' ?>><?= $table ?></option>
    <?php endforeach; ?>
  </select>
</form>

<!-- 表格内容 -->
<div id="table-content">
  <!-- 表格内容将在这里实时加载 -->
</div>

<script>
  const tableSelect = document.getElementById('table_name');
  const tableContentDiv = document.getElementById('table-content');

  // 加载选中表格的内容
  function loadTableContent(orderColumn = 'id', orderDirection = 'ASC') {
    const selectedTable = tableSelect.value;

    if (selectedTable) {
      // 发送AJAX请求获取表格内容
      fetch(`fetch_table_sorted.php?table=${selectedTable}&order_column=${orderColumn}&order_direction=${orderDirection}`)
        .then(response => response.text())
        .then(data => {
          tableContentDiv.innerHTML = data;

          // 绑定表头点击事件
          const thLinks = document.querySelectorAll('th a');
          thLinks.forEach(link => {
            link.addEventListener('click', (e) => {
              e.preventDefault();
              const url = new URL(link.href);
              const newOrderColumn = url.searchParams.get('order_column');
              const newOrderDirection = url.searchParams.get('order_direction');
              loadTableContent(newOrderColumn, newOrderDirection);
            });
          });
        })
        .catch(error => {
          console.error('Error:', error);
          tableContentDiv.innerHTML = '<p>加载表内容失败。</p>';
        });
    } else {
      tableContentDiv.innerHTML = '';
    }
  }

  // 初始化时加载选中的表格内容
  if (tableSelect.value) {
    loadTableContent();
  }

  // 切换表格时实时加载内容
  tableSelect.addEventListener('change', () => loadTableContent());
</script>
</body>
</html>
