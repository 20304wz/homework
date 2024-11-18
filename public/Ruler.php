<?php
// 数据库连接配置
include 'db_connection.php';
//include 'add.php';
//include 'delete.php';
//include 'search.php';
//include 'edit.php'
?>
<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>问卷管理员界面</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      margin: 50px;
    }
    h1 {
      font-size: 3em; /* 增大标题字体 */
      margin-bottom: 40px;
    }
    .button-container {
      display: flex;
      justify-content: center;
      gap: 30px; /* 增大按钮间距 */
    }
    .button-container a {
      text-decoration: none;
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
    button:hover {
      background-color: #0056b3;
      transform: scale(1.1); /* 鼠标悬停时放大 */
    }
  </style>
</head>
<body>
<h1>问卷管理员界面</h1>
<div class="button-container">
  <a href="add.php"><button>增加</button></a>
  <a href="delete.php"><button>删除</button></a>
  <a href="search.php"><button>查找</button></a>
  <a href="edit.php"><button>修改</button></a>
  <a href="Ruler_quize.php"><button>进入调查问卷</button></a>
</div>
</body>
</html>
