<?php
// 显示 PHP 错误（调试用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 数据库连接配置
$servername = "localhost";
$username = "root";
$password = "20030304Yjm."; // 根据实际密码填写
$dbname = "questionnaire";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("连接失败: " . $conn->connect_error);
}

// 预定义单选题得分映射
$single_score_map = [
  'A' => 95,
  'B' => 75,
  'C' => 60,
  'D' => 35,
  'E' => 15
];

// 初始化分数
$total_single_score = 0;
$total_multi_score = 0;

// 检查POST数据
echo "<pre>";
print_r($_POST);
echo "</pre>";

// 处理单选题答案
$single_answers = [];
foreach ($_POST as $key => $value) {
  if (strpos($key, 'single_') === 0) {
    $question_id = str_replace('single_', '', $key);
    $single_answers[] = $value;
    $total_single_score += $single_score_map[$value];
  }
}

// 将单选题答案转换成字符串
$single_answers_string = implode(",", $single_answers);

// 处理多选题答案
$multi_answers = [];
$correct_multi_answers = [];
$multi_score = 0;

// 从数据库获取多选题的正确答案
$multi_correct_query = "SELECT YorN1, YorN2, YorN3, YorN4 FROM mulanswer LIMIT 1";
$multi_correct_result = $conn->query($multi_correct_query);

if ($multi_correct_result->num_rows > 0) {
  $correct_row = $multi_correct_result->fetch_assoc();
  $correct_multi_answers = [
    $correct_row['YorN1'],
    $correct_row['YorN2'],
    $correct_row['YorN3'],
    $correct_row['YorN4']
  ];
} else {
  die("无法获取多选题的正确答案。");
}

// 计算多选题得分
foreach ($_POST as $key => $value) {
  if (strpos($key, 'multi_') === 0) {
    $question_id = str_replace('multi_', '', $key);
    $multi_answers[] = $value;
    $index = intval($question_id) - 1;
    if (isset($correct_multi_answers[$index]) && $value == $correct_multi_answers[$index]) {
      $total_multi_score += 1;
    }
  }
}

// 将多选题答案转换成字符串
$multi_answers_string = implode(",", $multi_answers);

// 调试输出
echo "单选答案字符串: " . $single_answers_string . "<br>";
echo "单选总分: " . $total_single_score . "<br>";
echo "多选答案字符串: " . $multi_answers_string . "<br>";
echo "多选总分: " . $total_multi_score . "<br>";

// 插入数据到 answer 表
$sql = "INSERT INTO answer (singleAnswer, score, mulAnswer, mulscore) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sisi", $single_answers_string, $total_single_score, $multi_answers_string, $total_multi_score);

// 检查 SQL 语句执行情况
if ($stmt->execute()) {
  echo "答案和分数提交成功！";
} else {
  echo "答案提交失败：" . $stmt->error;
}

// 关闭连接
$stmt->close();
$conn->close();
?>
