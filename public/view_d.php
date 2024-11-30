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
      $parts = explode(',', $answer);
      foreach ($parts as $part) {
        $subParts = explode('.', $part);
        if (count($subParts) === 2) {
          list($num, $letter) = $subParts;
          if ($num == $questionNumber && isset($answerStats[$letter])) {
            $answerStats[$letter]++;
          }
        }
      }
    }
  }
  return $answerStats;
}

// 获取多选题单题答案分布数据
function getMultiAnswerDataByQuestion($questionNumber) {
  global $conn;
  $sql = "SELECT mulAnswer FROM answer";
  $result = $conn->query($sql);
  $answerStats = [
    'A' => 0,
    'B' => 0,
    'C' => 0,
    'D' => 0
    // 'E' removed
  ];
  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $answers = $row['mulAnswer'];
      $parts = explode(',', $answers);
      foreach ($parts as $part) {
        $subParts = explode('.', $part);
        if (count($subParts) === 2) {
          list($num, $letters) = $subParts;
          if ($num == $questionNumber) {
            foreach (str_split($letters) as $letter) {
              if (isset($answerStats[$letter])) {
                $answerStats[$letter]++;
              }
            }
          }
        }
      }
    }
  }
  return $answerStats;
}

if (isset($_GET['graph'])) {
  $graphType = $_GET['graph'];

  if ($graphType === 'single' && isset($_GET['questionNumber'])) {
    $questionNumber = $_GET['questionNumber'];
    $singleData = getSingleAnswerData($questionNumber);
    $graph = new Graph(400, 300);
    $graph->SetScale('textlin');
    $barplot = new BarPlot(array_values($singleData));
    $barplot->SetFillColor('lightblue');
    $graph->xaxis->SetTickLabels(array_keys($singleData));
    $graph->Add($barplot);
    $graph->title->Set("Question $questionNumber - Single Answer Statistics");
    $graph->Stroke();
  } elseif ($graphType === 'multi_question' && isset($_GET['questionNumber'])) {
    $questionNumber = $_GET['questionNumber'];
    $multiData = getMultiAnswerDataByQuestion($questionNumber);
    $graph = new Graph(400, 300);
    $graph->SetScale('textlin');
    $barplot = new BarPlot(array_values($multiData));
    $barplot->SetFillColor('lightblue');
    $graph->xaxis->SetTickLabels(array_keys($multiData));
    $graph->Add($barplot);
    $graph->title->Set("Question $questionNumber - Multi-Answer Distribution");
    $graph->Stroke();
  } else {
    die('无效的图形类型请求或缺少题号参数');
  }
}

?>
