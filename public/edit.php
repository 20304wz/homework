<?php
include 'db_connection.php'
?>

<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>修改问题</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      margin: 50px;
    }
    h1 {
      margin-bottom: 30px;
    }
    form {
      margin-top: 20px;
    }
    input[type="text"] {
      width: 300px;
      padding: 10px;
      font-size: 16px;
      margin-bottom: 20px;
    }
    button {
      font-size: 16px;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      background-color: #ffc107;
      color: white;
      cursor: pointer;
    }
  </style>
</head>
<body>
<h1>修改问题</h1>
<form method="POST" action="">
  <input type="text" name="question_id" placeholder="输入问题ID" required>
  <br>
  <input type="text" name="new_question" placeholder="输入新问题内容" required>
  <br>
  <button type="submit">保存修改</button>
</form>
</body>
</html>
