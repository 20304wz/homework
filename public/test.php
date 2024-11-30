<?php
$servername = "localhost";
$username = "root";
$password = "20030304Yjm.";
$dbname = "questionnaire";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
  die("连接失败: " . $conn->connect_error);
}

// 查询singleAnswer数据
$single_sql = "SELECT ID, singleAnswer FROM answer";
$single_result = $conn->query($single_sql);
$single_answers = [];
while ($row = $single_result->fetch_assoc()) {
  $id = $row['ID'];
  $answer = substr($row['singleAnswer'], 2); // 假设答案格式为 "1.A"，我们只取 ".A"
  $single_answers[$id] = $answer;
}

// 查询mulAnswer数据
$mul_sql = "SELECT ID, mulAnswer FROM answer";
$mul_result = $conn->query($mul_sql);
$mul_answers = [];
while ($row = $mul_result->fetch_assoc()) {
  $id = $row['ID'];
  $answer = $row['mulAnswer'];
  $mul_answers[$id] = $answer;
}

$conn->close();

// 将PHP数组转换为JavaScript对象
$single_json = json_encode($single_answers);
$mul_json = json_encode($mul_answers);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Chart Display</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<canvas id="singleChart" width="400" height="400"></canvas>
<canvas id="mulChart" width="400" height="400"></canvas>

<script>
  // 单选题图表
  var singleData = <?php echo $single_json; ?>;
  var singleLabels = Object.keys(singleData);
  var singleValues = Object.values(singleData);

  var ctx1 = document.getElementById('singleChart').getContext('2d');
  var singleChart = new Chart(ctx1, {
    type: 'bar',
    data: {
      labels: singleLabels,
      datasets: [{
        label: 'Single Choice Answers',
        data: singleValues,
        backgroundColor: 'rgba(54, 162, 235, 0.5)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // 多选题图表
  var mulData = <?php echo $mul_json; ?>;
  var mulLabels = Object.keys(mulData);
  var mulValues = Object.values(mulData);

  var ctx2 = document.getElementById('mulChart').getContext('2d');
  var mulChart = new Chart(ctx2, {
    type: 'bar',
    data: {
      labels: mulLabels,
      datasets: [{
        label: 'Multiple Choice Answers',
        data: mulValues,
        backgroundColor: 'rgba(255, 99, 132, 0.5)',
        borderColor: 'rgba(255, 99, 132, 1)',
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>

</body>
</html>
