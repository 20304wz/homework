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
$tableResults = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['table_name'])) {
    $table_name = $_POST['table_name'];

    // 查询整个表的内容
    $sql = "SELECT * FROM `$table_name`";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $tableResults[] = $row;
      }
    } else {
      $message = "该表为空或未找到数据。";
    }
  } else {
    $message = "请选择一个表！";
  }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>查看表内容</title>
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
    button {
      font-size: 1.2em;
      padding: 15px 25px;
      border: none;
      border-radius: 10px;
      background-color: #007BFF;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s;
    }
    button:hover {
      background-color: #0056b3;
      transform: scale(1.05);
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
<h1>查看表内容</h1>

<!-- 表单选择 -->
<form method="POST">
  <label for="table_name">选择表格：</label>
  <select name="table_name" id="table_name" required>
    <option value="">-- 请选择表格 --</option>
    <?php foreach ($tables as $table): ?>
      <option value="<?= $table ?>" <?= isset($_POST['table_name']) && $_POST['table_name'] === $table ? 'selected' : '' ?>><?= $table ?></option>
    <?php endforeach; ?>
  </select>
  <br>

  <button type="submit">查看表内容</button>
</form>

<!-- 提示信息 -->
<?php if (!empty($message)): ?>
  <p class="message"><?= $message ?></p>
<?php endif; ?>

<!-- 表内容 -->
<?php if (!empty($tableResults)): ?>
  <table>
    <thead>
    <tr>
      <?php foreach (array_keys($tableResults[0]) as $header): ?>
        <th><?= $header ?></th>
      <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($tableResults as $row): ?>
      <tr>
        <?php foreach ($row as $cell): ?>
          <td><?= htmlspecialchars($cell) ?></td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($tableResults)): ?>
<?php endif; ?>

<div class="button-container">
  <a href="Ruler.php"><button>返回初始界面</button></a>
</div>
</body>
</html>
