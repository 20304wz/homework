<?php
// 数据库连接配置
include 'db_connection.php';

// 引入JpGraph库文件
$jpgraphPath = 'G:\JpGraph\jpgraph\src\\';
$graphLibs = ['jpgraph.php', 'jpgraph_bar.php'];
foreach ($graphLibs as $lib) {
  $libPath = $jpgraphPath. $lib;
  if (!file_exists($libPath)) {
    die("文件 {$libPath} 不存在，无法加载JpGraph库");
  }
  require_once $libPath;
}


function getSingleAnswerData($questionNumber) {
  global $conn;
  // 假设答案数据存储在answer表的singleAnswer列中
  $sql = "SELECT singleAnswer FROM answer";
  $result = $conn->query($sql);
  $answerStats = [
    'A' => 0,
    'B' => 0,
    'C' => 0,
    'D' => 0
  ];
  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $answer = $row['singleAnswer'];
      // 按照格式解析答案，例如 "1.A,2.A,3.B"
      $parts = explode(',', $answer);
      foreach ($parts as $part) {
        $subParts = explode('.', $part);
        if (count($subParts) === 2) {
          list($num, $letter) = $subParts;
          if ($num === $questionNumber && isset($answerStats[$letter]) && ($letter === 'A' || $letter === 'B' || $letter === 'C' || $letter === 'D')) {
            $answerStats[$letter]++;
          }
        }
      }
    }
  }
  return $answerStats;
}


// 获取指定题号单选题处理后的数据
if (isset($_GET['questionNumber'])) {
  $questionNumber = $_GET['questionNumber'];
  $singleData = getSingleAnswerData($questionNumber);
  // 创建图形对象并设置相关属性（单选题柱状图）
  $graphSingle = new Graph(350, 250);
  $graphSingle->SetScale('textlin');
  $graphSingle->SetMargin(40, 30, 20, 40);
  // 根据处理后的数据创建柱状图对象并设置颜色等属性（单选题）
  $barplotSingle = new BarPlot([$singleData['A'], $singleData['B'], $singleData['C'], $singleData['D']]);
  $barplotSingle->SetFillColor('lightblue');
  // 设置X轴标签（单选题）
  $graphSingle->xaxis->SetTickLabels(['A', 'B', 'C', 'D']);
  // 将柱状图对象添加到图形对象中（单选题）
  $graphSingle->Add($barplotSingle);
  // 设置标题（单选题对应题号答案统计）
  $graphSingle->title->Set(''. $questionNumber. '.single');
  // 输出图形（单选题）
  try {
    $graphSingle->Stroke();
  } catch (Exception $e) {
    error_log("单选题第". $questionNumber. "题图形输出失败: ". $e->getMessage());
  }
} else {
  die('未接收到题号参数');
}
