<?php
// 数据库连接配置
include 'db_connection.php';

// 查询单选题选项及其数量
$sql_single = "SELECT singleAnswer, COUNT(*) AS count FROM answer GROUP BY singleAnswer";
$result_single = $conn->query($sql_single);

$single_options = [];
$single_counts = [];
if ($result_single->num_rows > 0) {
  while ($row = $result_single->fetch_assoc()) {
    $single_options[] = $row['singleAnswer'];
    $single_counts[] = $row['count'];
  }
}

// 查询多选题分数及其数量
$sql_multi = "SELECT mulscore, COUNT(*) AS count FROM answer GROUP BY mulscore";
$result_multi = $conn->query($sql_multi);

$multi_scores = [];
$multi_counts = [];
if ($result_multi->num_rows > 0) {
  while ($row = $result_multi->fetch_assoc()) {
    $multi_scores[] = $row['mulscore'];
    $multi_counts[] = $row['count'];
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Answer Statistics</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
<canvas id="singleChart"></canvas>
<canvas id="multiChart"></canvas>

<script>
  // 绘制单选题柱形图
  const singleCtx = document.getElementById('singleChart').getContext('2d');
  new Chart(singleCtx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($single_options);?>,
      datasets: [{
        label: '单选题选项数量',
        data: <?php echo json_encode($single_counts);?>,
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
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

  // 绘制多选题分数柱形图
  const multiCtx = document.getElementById('multiChart').getContext('2d');
  new Chart(multiCtx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($multi_scores);?>,
      datasets: [{
        label: '多选题分数数量',
        data: <?php echo json_encode($multi_counts);?>,
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
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
