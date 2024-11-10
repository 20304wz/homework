<!--<?php
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

// 包含获取题目数据和总题目数的文件
include 'get_questions.php';
include 'get_total_questions.php';

// 预定义每个选项的分数
$score_map = [
  'A' => 95,
  'B' => 75,
  'C' => 60,
  'D' => 35,
  'E' => 15
];

// 获取提交的答案数据
$answers = [];
$total_score = 0; // 用于累加所有题目的分数
foreach ($_POST as $key => $value) {
  if (strpos($key, 'question_') === 0) {
    $answers[] = $value; // 记录用户选择的答案（如 "A", "B", "C", "D", "E"）
    $total_score += $score_map[$value]; // 根据选项映射累加分数
  }
}

// 计算平均分（使用总题目数）
if ($total_questions > 0) {
  $average_score = intval($total_score / $total_questions); // 求平均分并取整
} else {
  $average_score = 0; // 如果没有题目，设置默认分数为 0 或其他适当的值
}

// 将答案数组转为字符串格式，例如 "A,B,C,D,E"
$answers_string = implode(",", $answers);

// 准备 SQL 插入语句，将答案和平均分一同插入
$sql = "INSERT INTO answer (singleAnswer, score) VALUES (?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
  die("准备语句失败: " . $conn->error);
}

// 绑定参数并执行插入
$stmt->bind_param("si", $answers_string, $average_score);

if ($stmt->execute()) {
  echo "答案和分数提交成功！";
} else {
  echo "答案提交失败：" . $stmt->error;
}

// 关闭语句和数据库连接
$stmt->close();
$conn->close();
?>
