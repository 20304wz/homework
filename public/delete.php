<?php
// 导入数据库连接文件
include 'db_connection.php'; // 确保 db_connection.php 文件路径正确

// 获取所有表名
$sql = "SHOW TABLES";
$result = $conn->query($sql);
$tables = [];
while ($row = $result->fetch_array()) {
  $tables[] = $row[0];
}

// 处理表单提交
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['table_name']) && !empty($_POST['id'])) {
    $table_name = $_POST['table_name'];
    $id = intval($_POST['id']);

    // 构建删除 SQL 查询
    $sql = "DELETE FROM `$table_name` WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
      $message = "ID $id 的数据已成功从表 $table_name 删除！";
    } else {
      $message = "删除失败: " . $conn->error;
    }
  } else {
    $message = "请完整填写表名和 ID！";
  }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>删除数据并实时查看</title>
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
    button {
      font-size: 1.2em; /* 增大按钮文字 */
      padding: 15px 25px; /* 增大按钮大小 */
      border: none;
      border-radius: 10px;
      background-color: #007BFF;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s;

    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #f4f4f4;
    }
    .message {
      margin-top: 20px;
      color: green;
    }
  </style>
</head>
<body>
<h1>删除数据并实时查看表内容</h1>

<!-- 选择表格 -->
<form method="POST" id="delete-form">
  <label for="table_name">选择表格：</label>
  <select name="table_name" id="table_name" required>
    <option value="">-- 请选择表格 --</option>
    <?php foreach ($tables as $table): ?>
      <option value="<?= $table ?>" <?= isset($_POST['table_name']) && $_POST['table_name'] === $table ? 'selected' : '' ?>><?= $table ?></option>
    <?php endforeach; ?>
  </select>
  <br>

  <!-- 输入需要删除的 ID -->
  <label for="id">删除的 ID：</label>
  <input type="text" name="id" id="id" placeholder="例如：1">
  <br>

  <button type="submit">删除数据</button>
</form>

<!-- 提示信息 -->
<?php if (!empty($message)): ?>
  <p class="message"><?= $message ?></p>
<?php endif; ?>

<!-- 表格内容 -->
<div id="table-content">
  <!-- 表格内容将在这里实时加载 -->
</div>

<script>
  const tableSelect = document.getElementById('table_name');
  const tableContentDiv = document.getElementById('table-content');

  // 加载选中表格的内容
  function loadTableContent() {
    const selectedTable = tableSelect.value;

    if (selectedTable) {
      // 发送AJAX请求获取表格内容
      fetch(`fetch_table_data.php?table=${selectedTable}`)
        .then(response => response.text())
        .then(data => {
          tableContentDiv.innerHTML = data;
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
  tableSelect.addEventListener('change', loadTableContent);
</script>
<div class="button-container">
  <a href="Ruler.php"><button>返回初始界面</button></a>
</div>
</body>
</html>
