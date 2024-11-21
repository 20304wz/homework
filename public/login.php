<?php
// 引入数据库连接文件
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $stuID = $_POST['stuID'];
  $password = $_POST['password'];

  // 查询用户
  $sql = "SELECT * FROM info WHERE stuID = ? AND password = ?";
  $stmt = $conn->prepare($sql);

  if ($stmt) {
    $stmt->bind_param("is", $stuID, $password); // "is" 表示 int 和 string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();
      session_start();
      $_SESSION['user'] = $user;

      // 根据用户类型跳转
      switch ($user['type']) {
        case 'administrator':
          header("Location: Ruler.php");
          break;
        case 'stu21':
        case 'stu22':
        case 'stu23':
          header("Location: quize.php");
          break;
        default:
          $message = "<p class='error'>Invalid user type.</p>";
          break;
      }
      exit;
    } else {
      $message = "<p class='error'>Invalid Student ID or Password.</p>";
    }
    $stmt->close();
  } else {
    $message = "<p class='error'>Error preparing statement: " . $conn->error . "</p>";
  }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登录</title>
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
    input {
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
    p {
      text-align: center;
    }
  </style>
</head>
<body>
<div class="container">
  <h1>登录</h1>
  <?php if (isset($message)): ?>
    <?php echo $message; ?>
  <?php endif; ?>
  <form method="POST" action="login.php">
    <label>学号:</label>
    <input type="number" name="stuID" required>

    <label>密码:</label>
    <input type="password" name="password" required>

    <button type="submit">登录</button>
  </form>
  <p>没有账号？<a href="register.php">点击注册</a>.</p>
</div>
</body>
</html>
