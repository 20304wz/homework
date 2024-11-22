<?php
// 引入数据库连接文件
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $stuID = $_POST['stuID'];
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirmPassword'];
  $type = $_POST['type'];

  // 检查两次密码是否一致
  if ($password !== $confirmPassword) {
    $message = "<p class='error'>密码不一致，请重新输入。</p>";
  } else {
    // 检查类型是否合法
    if (!in_array($type, ['administrator', 'stu21', 'stu22', 'stu23'])) {
      die("Invalid user type.");
    }

    // 检查学号是否已经存在
    $checkSql = "SELECT * FROM info WHERE stuID = ?";
    $checkStmt = $conn->prepare($checkSql);

    if ($checkStmt) {
      $checkStmt->bind_param("i", $stuID);
      $checkStmt->execute();
      $checkResult = $checkStmt->get_result();

      if ($checkResult->num_rows > 0) {
        $message = "<p class='error'>注册失败，该学号已经存在。</p>";
      } else {
        // 插入数据到数据库
        $sql = "INSERT INTO info (name, stuID, password, type) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
          $stmt->bind_param("siss", $name, $stuID, $password, $type);
          if ($stmt->execute()) {
            $message = "<p class='success'>注册成功！<a href='login.php'>点击这里登录</a></p>";
          } else {
            $message = "<p class='error'>错误: " . $stmt->error . "</p>";
          }
          $stmt->close();
        } else {
          $message = "<p class='error'>准备语句出错: " . $conn->error . "</p>";
        }
      }
    } else {
      $message = "<p class='error'>准备语句出错: " . $conn->error . "</p>";
    }
  }
}
?>



<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>注册</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background-color: #ffffff;
      padding: 20px 40px;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }
    h1 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin-bottom: 8px;
      color: #555;
      font-weight: bold;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }
    button {
      width: 100%;
      padding: 10px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background-color: #45a049;
    }
    .error {
      color: red;
      font-size: 14px;
      text-align: center;
    }
    .success {
      color: green;
      font-size: 14px;
      text-align: center;
    }
    a {
      color: #4CAF50;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
<div class="container">
  <h1>注册</h1>
  <?php if (isset($message)): ?>
    <?php echo $message; ?>
  <?php endif; ?>
  <form method="POST" action="register.php">
    <label>姓名:</label>
    <input type="text" name="name" required>

    <label>学号:</label>
    <input type="number" name="stuID" required>

    <label>密码:</label>
    <input type="password" name="password" required>

    <label>确认密码:</label>
    <input type="password" name="confirmPassword" required>

    <label>类型:</label>
    <select name="type" required>
      <option value="administrator">管理者</option>
      <option value="stu21">21级学生</option>
      <option value="stu22">22级学生</option>
      <option value="stu23">23级学生</option>
    </select>
    <button type="submit">提交</button>
    <a href="login.php"><button type="button">返回登录界面</button></a>
  </form>
</div>
</body>
</html>
