<?php
// 导入数据库连接文件
include  'db_connection.php'; // 确保 db_connection.php 文件路径正确

// 获取所有表名
$sql = "SHOW TABLES";
$result = $conn->query($sql);
$tables = [];
while ($row = $result->fetch_array()) {
  $tables[] = $row[0];
}

// 处理表单提交
$message = "";
$searchResults = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['table_name']) && !empty($_POST['search_column']) && isset($_POST['search_value'])) {
    $table_name = $_POST['table_name'];
    $search_column = $_POST['search_column'];
    $search_value = $_POST['search_value'];

    // 构建查询 SQL
    $sql = "SELECT * FROM `$table_name` WHERE `$search_column` LIKE '%$search_value%'";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $searchResults[] = $row;
      }
    } else {
      $message = "未找到匹配的数据。";
    }
  } else {
    $message = "请完整填写表名、搜索字段和搜索值！";
  }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>实时搜索表内容</title>
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
    .message {
      margin-top: 20px;
      color: green;
    }
  </style>
</head>
<body>
<h1>搜索表内容</h1>

<!-- 搜索表单 -->
<form method="POST">
  <label for="table_name">选择表格：</label>
  <select name="table_name" id="table_name" required>
    <option value="">-- 请选择表格 --</option>
    <?php foreach ($tables as $table): ?>
      <option value="<?= $table ?>" <?= isset($_POST['table_name']) && $_POST['table_name'] === $table ? 'selected' : '' ?>><?= $table ?></option>
    <?php endforeach; ?>
  </select>
  <br>

  <label for="search_column">搜索字段：</label>
  <input type="text" name="search_column" id="search_column" placeholder="输入字段名" value="<?= $_POST['search_column'] ?? '' ?>" required>
  <br>

  <label for="search_value">搜索值：</label>
  <input type="text" name="search_value" id="search_value" placeholder="输入搜索值" value="<?= $_POST['search_value'] ?? '' ?>" required>
  <br>

  <button type="submit">搜索</button>
</form>

<!-- 提示信息 -->
<?php if (!empty($message)): ?>
  <p class="message"><?= $message ?></p>
<?php endif; ?>

<!-- 搜索结果 -->
<?php if (!empty($searchResults)): ?>
  <table>
    <thead>
    <tr>
      <?php foreach (array_keys($searchResults[0]) as $header): ?>
        <th><?= $header ?></th>
      <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($searchResults as $row): ?>
      <tr>
        <?php foreach ($row as $cell): ?>
          <td><?= htmlspecialchars($cell) ?></td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($searchResults)): ?>
  <p>未找到匹配的数据。</p>
<?php endif; ?>
</body>
</html>
