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

// 预定义单选题和多选题的得分计算规则
$single_score_map = [
  'A' => 95,
  'B' => 75,
  'C' => 60,
  'D' => 35
];

$multi_correct_answers = [
  // 假设多选题的正确答案
  1 => ['A' => 1, 'B' => 0, 'C' => 1, 'D' => 0],
  // 添加更多题目的正确答案
];

// 初始化分数
$total_single_score = 0;
$total_multi_score = 0;

// 处理单选题答案
$single_answers = [];
foreach ($_POST as $key => $value) {
  if (strpos($key, 'single_') === 0) {
    $question_id = str_replace('single_', '', $key);
    $single_answers[] = $value;
    $total_single_score += $single_score_map[$value];
  }
}

// 处理多选题答案
$multi_answers = [];
foreach ($_POST as $key => $values) {
  if (strpos($key, 'multiple_') === 0) {
    $question_id = str_replace('multiple_', '', $key);

    // 获取用户选择的选项，并转换为布尔数组
    $user_answer = [
      'A' => in_array('A', $values) ? 1 : 0,
      'B' => in_array('B', $values) ? 1 : 0,
      'C' => in_array('C', $values) ? 1 : 0,
      'D' => in_array('D', $values) ? 1 : 0
    ];

    // 比较用户答案与正确答案
    if ($user_answer === $multi_correct_answers[$question_id]) {
      $total_multi_score += 10; // 假设每题10分
    }

    // 将用户答案保存为带题号的字符串，例如 "题号1:A,B"
    $multi_answers[] = "题号{$question_id}:" . implode(",", $values);
  }
}

// 将答案和得分插入数据库
$single_answers_string = implode(",", $single_answers);
$multi_answers_string = implode(";", $multi_answers); // 用分号分隔不同题目的多选答案

$sql = "INSERT INTO answer (singleAnswer, score, mulAnswer, mulscore) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sisi", $single_answers_string, $total_single_score, $multi_answers_string, $total_multi_score);

// 打印调试信息
echo "单选答案: " . $single_answers_string . "<br>";
echo "单选得分: " . $total_single_score . "<br>";
echo "多选答案: " . $multi_answers_string . "<br>";
echo "多选得分: " . $total_multi_score . "<br>";

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
