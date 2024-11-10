<?php
$servername = "localhost"; // 数据库服务器
$username = "username";    // 数据库用户名
$password = "password";    // 数据库密码
$dbname = "database_name"; // 数据库名称

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
  die("连接失败: " . $conn->connect_error);
}

// 查询数据
$sql = "SELECT * FROM your_table";
$result = $conn->query($sql);

$data = array();
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $data[] = $row;
  }
}

// 以JSON格式返回数据
echo json_encode($data);

$conn->close();
?>
