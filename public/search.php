<?php
include 'db_connection.php'
?>
<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>查找问题</title>
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
      background-color: #17a2b8;
      color: white;
      cursor: pointer;
    }
  </style>
</head>
<body>
<h1>查找问题</h1>
<form method="GET" action="">
  <input type="text" name="keyword" placeholder="输入关键词" required>
  <br>
  <button type="submit">查找</button>
</form>
</body>
</html>

