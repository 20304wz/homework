<?php
// 数据库连接配置
include 'db_connection.php';

// 引入JpGraph库文件
$jpgraphPath = 'G:\JpGraph\jpgraph\src\\';
$graphLibs = ['jpgraph.php', 'jpgraph_bar.php'];
foreach ($graphLibs as $lib) {
  $libPath = $jpgraphPath . $lib;
  if (!file_exists($libPath)) {
    die("文件 {$libPath} 不存在，无法加载JpGraph库");
  }
  require_once $libPath;
}

// 获取单选题数据并统计
function getSingleAnswerData($questionNumber) {
  global $conn;
  $sql = "SELECT singleAnswer FROM answer";
  $result = $conn->query($sql);
  $answerStats = [
    'A' => 0,
    'B' => 0,
    'C' => 0,
    'D' => 0,
    'E' => 0
  ];
  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $answer = $row['singleAnswer'];
      // 假设答案格式为 "1.A,2.B"
      $parts = explode(',', $answer);
      foreach ($parts as $part) {
        $subParts = explode('.', $part);
        if (count($subParts) === 2) {
          list($num, $letter) = $subParts;
          if ($num == $questionNumber && isset($answerStats[$letter]) && in_array($letter, ['A', 'B', 'C', 'D', 'E'])) {
            $answerStats[$letter]++;
          }
        }
      }
    }
  }
  return $answerStats;
}

// 根据题号生成单选题柱状图
if (isset($_GET['questionNumber'])) {
  $questionNumber = $_GET['questionNumber'];
  $singleData = getSingleAnswerData($questionNumber);

  // 创建图形对象
  $graphSingle = new Graph(350, 250);
  $graphSingle->SetScale('textlin');
  $graphSingle->SetMargin(40, 30, 20, 40);

  // 创建柱状图对象
  $barplotSingle = new BarPlot([$singleData['A'], $singleData['B'], $singleData['C'], $singleData['D'], $singleData['E']]);
  $barplotSingle->SetFillColor('lightblue');

  // 设置X轴标签
  $graphSingle->xaxis->SetTickLabels(['A', 'B', 'C', 'D', 'E']);

  // 添加柱状图到图形对象
  $graphSingle->Add($barplotSingle);

  // 设置标题
  $graphSingle->title->Set('Question ' . $questionNumber . ' - Single Answer Statistics');

  // 输出图形
  try {
    $graphSingle->Stroke();
  } catch (Exception $e) {
    error_log("单选题第" . $questionNumber . "题图形输出失败: " . $e->getMessage());
  }
} else {
  die('未接收到题号参数');
}
?>
