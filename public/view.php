<?php
// 数据库连接配置
include 'db_connection.php';

// 引入JpGraph库文件
$jpgraphPath = 'G:\JpGraph\jpgraph\src\\'; // 修改为你本地的JpGraph路径
$graphLibs = ['jpgraph.php', 'jpgraph_bar.php'];
foreach ($graphLibs as $lib) {
  $libPath = $jpgraphPath . $lib;
  if (!file_exists($libPath)) {
    die("文件 {$libPath} 不存在，无法加载JpGraph库");
  }
  require_once $libPath;
}

// 获取单选题数据
function getSingleAnswerData() {
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
          if (isset($answerStats[$letter]) && in_array($letter, ['A', 'B', 'C', 'D', 'E'])) {
            $answerStats[$letter]++;
          }
        }
      }
    }
  }
  return $answerStats;
}

// 获取多选题答案分布数据
function getMultiAnswerDataGroupedByQuestion() {
  global $conn;
  $sql = "SELECT mulAnswer FROM answer";
  $result = $conn->query($sql);

  $data = []; // 初始化按题号分组的统计数据

  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $answers = $row['mulAnswer'];

      if (!empty($answers)) {
        $parts = explode(',', $answers); // 按逗号拆分每道题的数据
        foreach ($parts as $part) {
          $subParts = explode('.', $part); // 分割题号与选项
          if (count($subParts) === 2) {
            list($num, $letters) = $subParts; // 获取题号和选项
            if (!isset($data[$num])) {
              $data[$num] = [
                'A' => 0,
                'B' => 0,
                'C' => 0,
                'D' => 0 // 初始化去掉 E 选项
              ];
            }
            // 累加每个选项的选择次数
            for ($i = 0; $i < strlen($letters); $i++) {
              $letter = $letters[$i];
              if (isset($data[$num][$letter])) {
                $data[$num][$letter]++;
              }
            }
          }
        }
      }
    }
  }
  return $data;
}

// 获取多选题分数累加数据
function getMultiAnswerScores() {
  global $conn;

  // 查询正确答案
  $sql = "SELECT ID, answer FROM mulanswer"; // 假设mulanswer表存储正确答案
  $result = $conn->query($sql);

  $correctAnswers = [];
  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $correctAnswers[$row['ID']] = str_split($row['answer']); // 将每道题的正确答案分割为数组存储
    }
  }

  // 查询用户提交的答案
  $sql = "SELECT mulAnswer FROM answer"; // 获取用户多选题答案
  $result = $conn->query($sql);

  $scores = []; // 初始化分数结果数组
  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $userAnswers = $row['mulAnswer']; // 用户答案字符串

      if (!empty($userAnswers)) {
        $parts = explode(',', $userAnswers); // 拆分为每道题的数据
        foreach ($parts as $part) {
          $subParts = explode('.', $part); // 分割题号与用户选项
          if (count($subParts) === 2) {
            list($num, $userOptions) = $subParts; // 获取题号和用户选择的选项
            $userOptions = str_split($userOptions); // 转换为数组

            if (isset($correctAnswers[$num])) {
              $correctOptions = $correctAnswers[$num]; // 正确答案
              $intersection = array_intersect($userOptions, $correctOptions); // 用户选择的正确选项
              $wrongOptions = array_diff($userOptions, $correctOptions); // 用户选择的错误选项

              if (!isset($scores[$num])) {
                $scores[$num] = 0; // 初始化题号的分数
              }

              if (count($wrongOptions) > 0) {
                // 存在错误选项，分数为0
                $scores[$num] += 0;
              } elseif (count($intersection) === count($correctOptions)) {
                // 全对，满分10分
                $scores[$num] += 10;
              } else {
                // 部分对，按比例评分
                $scores[$num] += round((count($intersection) / count($correctOptions)) * 10, 2);
              }
            }
          }
        }
      }
    }
  }

  return $scores; // 返回每道题的累加分数
}

// 判断请求参数并生成相应图形
if (!isset($_GET['graph'])) {
  die('未指定图形类型');
}

$graphType = $_GET['graph'];

if ($graphType === 'single') {
  // 单选题柱状图
  $singleData = getSingleAnswerData();
  $graphSingle = new Graph(350, 250);
  $graphSingle->SetScale('textlin');
  $graphSingle->SetMargin(40, 30, 20, 40);
  $barplotSingle = new BarPlot([$singleData['A'], $singleData['B'], $singleData['C'], $singleData['D'], $singleData['E']]);
  $barplotSingle->SetFillColor('lightblue');
  $graphSingle->xaxis->SetTickLabels(['A', 'B', 'C', 'D', 'E']);
  $graphSingle->Add($barplotSingle);
  $graphSingle->title->Set('Single Answer Statistics');
  try {
    $graphSingle->Stroke();
  } catch (Exception $e) {
    error_log("单选题图形输出失败: " . $e->getMessage());
  }
} elseif ($graphType === 'multi') {
  // 多选题答案分布柱状图（横轴为题号，按分组选项显示）
  $multiData = getMultiAnswerDataGroupedByQuestion();

  // 提取题号和对应的选项统计
  // 提取题号和对应的选项统计
  $xLabels = array_keys($multiData); // 横轴为题号
  $yValues = [
    'A' => [],
    'B' => [],
    'C' => [],
    'D' => [] // 去掉 E
  ];

  foreach ($multiData as $num => $stats) {
    $yValues['A'][] = $stats['A'];
    $yValues['B'][] = $stats['B'];
    $yValues['C'][] = $stats['C'];
    $yValues['D'][] = $stats['D'];
  }

  $graphMulti = new Graph(700, 400);
  $graphMulti->SetScale('textlin'); // 设置纵轴为线性刻度
  $graphMulti->SetMargin(60, 30, 20, 50); // 设置边距
  $graphMulti->xaxis->SetTickLabels($xLabels); // 设置横轴题号
  $graphMulti->xaxis->SetTitle('Question ID', 'center');
  $graphMulti->yaxis->SetTitle('Selection Count', 'center');

// 创建每个选项的柱状图对象
  $barA = new BarPlot($yValues['A']);
  $barA->SetFillColor('lightblue');
  $barA->SetLegend('A');

  $barB = new BarPlot($yValues['B']);
  $barB->SetFillColor('lightgreen');
  $barB->SetLegend('B');

  $barC = new BarPlot($yValues['C']);
  $barC->SetFillColor('orange');
  $barC->SetLegend('C');

  $barD = new BarPlot($yValues['D']);
  $barD->SetFillColor('pink');
  $barD->SetLegend('D');

// 合并柱状图
  $groupBar = new GroupBarPlot([$barA, $barB, $barC, $barD]); // 去掉 E
  $graphMulti->Add($groupBar);

// 设置标题
  $graphMulti->title->Set('Multi-Answer Distribution by Question (No E)');


  // 输出图形
  try {
    $graphMulti->Stroke();
  } catch (Exception $e) {
    error_log("多选题答案分布图生成失败: " . $e->getMessage());
  }
} elseif ($graphType === 'multi_score') {
  // 多选题分数累加柱状图
  $multiScores = getMultiAnswerScores();
  $xLabels = array_keys($multiScores); // 横坐标为题号
  $yValues = array_values($multiScores); // 纵坐标为分数累加

  // 创建图形对象
  $graphMultiScore = new Graph(400, 300);
  $graphMultiScore->SetScale('textlin');
  $graphMultiScore->SetMargin(50, 30, 30, 50);

  // 创建柱状图对象
  $barplotMultiScore = new BarPlot($yValues);
  $barplotMultiScore->SetFillColor('lightblue');

  // 设置横轴标签为题号
  $graphMultiScore->xaxis->SetTickLabels($xLabels);
  $graphMultiScore->xaxis->SetTitle('Question ID', 'center');
  $graphMultiScore->yaxis->SetTitle('Score', 'center');

  $graphMultiScore->Add($barplotMultiScore);

  // 设置标题
  $graphMultiScore->title->Set('Multi-Answer Cumulative Scores by Question');

  try {
    $graphMultiScore->Stroke();
  } catch (Exception $e) {
    error_log("多选题分数累加图生成失败: " . $e->getMessage());
  }
} else {
  die('无效的图形类型请求');
}
?>
