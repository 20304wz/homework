<?php
// 数据库连接配置
include 'db_connection.php';

// 引入JpGraph库文件
$jpgraphPath = 'D:\JpGraph\src\\';
$graphLibs = ['jpgraph.php', 'jpgraph_bar.php'];
foreach ($graphLibs as $lib) {
  $libPath = $jpgraphPath. $lib;
  if (!file_exists($libPath)) {
    die("文件 {$libPath} 不存在，无法加载JpGraph库");
  }
  require_once $libPath;
}

// 定义函数获取单选题数据并处理
function getSingleAnswerData()
{
  global $conn;
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
      $parts = explode(',', $answer);
      foreach ($parts as $part) {
        $subParts = explode('.', $part);
        if (count($subParts) === 2) {
          list($num, $letter) = $subParts;
          if (isset($answerStats[$letter]) && ($letter === 'A' || $letter === 'B' || $letter === 'C' || $letter === 'D')) {
            $answerStats[$letter]++;
          }
        }
      }
    }
  }
  return $answerStats;
}

// 定义函数获取多选题数据并处理
function getMultiAnswerData()
{
  global $conn;
  $sql = "SELECT mulAnswer FROM answer";
  $result = $conn->query($sql);
  $answerStats = [
    'A' => 0,
    'B' => 0,
    'C' => 0,
    'D' => 0
  ];
  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $answer = $row['mulAnswer'];
      if (!empty($answer)) {
        for ($i = 0; $i < strlen($answer); $i++) {
          $letter = $answer[$i];
          if ($letter === 'A' || $letter === 'B' || $letter === 'C' || $letter === 'D') {
            if (isset($answerStats[$letter])) {
              $answerStats[$letter]++;
            }
          }
        }
      }
    }
  }
  return $answerStats;
}

if (isset($_GET['graph'])) {
  if ($_GET['graph'] ==='multi') {
    // 输出多选题图形相关代码
    // 获取多选题处理后的数据
    $multiData = getMultiAnswerData();
    // 创建图形对象并设置相关属性（多选题柱状图）
    $graphMulti = new Graph(350, 250);
    $graphMulti->SetScale('textlin');
    $graphMulti->SetMargin(50, 30, 20, 50);
    // 根据处理后的数据创建柱状图对象并设置颜色等属性（多选题）
    $barplotMulti = new BarPlot([$multiData['A'], $multiData['B'], $multiData['C'], $multiData['D']]);
    $barplotMulti->SetFillColor('lightblue');
    // 设置X轴标签（多选题）
    $graphMulti->xaxis->SetTickLabels(['A', 'B', 'C', 'D']);
    // 将柱状图对象添加到图形对象中（多选题）
    $graphMulti->Add($barplotMulti);
    // 设置标题（多选题）
    $graphMulti->title->Set('mulAnswer');
    // 输出图形（多选题）
    try {
      $graphMulti->Stroke();
    } catch (Exception $e) {
      error_log("多选题图形输出失败: ". $e->getMessage());
    }
  } elseif ($_GET['graph'] ==='single') {
    // 输出单选题图形相关代码
    // 获取单选题处理后的数据
    $singleData = getSingleAnswerData();
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
    // 设置标题（单选题）
    $graphSingle->title->Set('singleAnswer');
    // 输出图形（单选题）
    try {
      $graphSingle->Stroke();
    } catch (Exception $e) {
      error_log("单选题图形输出失败: ". $e->getMessage());
    }
  } else {
    die('无效的图形类型请求');
  }
} else {
  die('请指定要显示的图形类型（single或multi）');
}
