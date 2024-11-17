<?php
// 数据库连接配置
include 'db_connection.php';

// 预定义单选题得分映射
$single_score_map = [
  'A' => 95,
  'B' => 75,
  'C' => 60,
  'D' => 35,
  'E' => 15
];
//test
// 初始化分数
$total_single_score = 0;
$total_multi_score = 0;

// 处理单选题答案并构建答案描述
$single_answers = [];
$single_answer_desc = [];
$question_count = 1;
foreach ($_POST as $key => $value) {
  if (strpos($key, 'question_') === 0 &&!is_array($value)) {
    $single_answers[] = $value;
    if (isset($single_score_map[$value])) {
      // 根据答案获取对应的分数并累加
      $total_single_score += $single_score_map[$value];
    }
    $single_answer_desc[] = "第{$question_count}题选择了{$value}";
    $question_count++;
  }
}

// 计算单选题平均分并转换为整数
$single_average_score = (int)round($total_single_score / count($single_answers));

// 将单选题答案转换成字符串
$single_answers_string = implode(",", $single_answers);
echo "单选题答案描述：<br>";
foreach ($single_answer_desc as $desc) {
  echo $desc. "<br>";
}

// 处理多选题答案并构建答案描述
$multi_answers = [];
$correct_multi_answers = [];
$multi_score = 0;
$multi_answer_desc = [];
$question_count = 1;
// 从数据库获取多选题的正确答案（这里省略数据库查询部分，假设已经正确获取）
//...
foreach ($_POST as $key => $value) {
  if (strpos($key, 'multiple_question_') === 0 && is_array($value)) {
    $answer = [];
    foreach ($value as $v) {
      $answer[] = $v;
    }
    sort($answer);
    $answer_str = implode("", $answer);
    $multi_answers[] = $answer_str;
    $index = intval(str_replace('multiple_question_', '', $key)) - 1;
    if (isset($correct_multi_answers[$index]) && in_array($correct_multi_answers[$index], $value)) {
      $total_multi_score += 1;
    }
    $answer_str_no_comma = str_replace(',', '', $answer_str);
    $multi_answer_desc[] = "第{$question_count}题选择了{$answer_str_no_comma}";
    $question_count++;
  }
}

// 重新构建多选题答案字符串（只保留字母）
$new_multi_answers_string = '';
foreach ($multi_answers as $answer) {
  $new_multi_answers_string.= $answer. ',';
}
$new_multi_answers_string = rtrim($new_multi_answers_string, ',');

// 将多选题答案转换成字符串
echo "多选题答案描述：<br>";
foreach ($multi_answer_desc as $desc) {
  echo $desc. "<br>";
}

// 调试输出
echo "单选答案字符串: ". $single_answers_string. "<br>";
echo "单选平均分: ". $single_average_score. "<br>";
echo "单选总分: ". $total_single_score. "<br>";
echo "多选答案字符串: ". $new_multi_answers_string. "<br>";
echo "多选总分: ". $total_multi_score. "<br>";

// 插入数据到 answer 表
$sql = "INSERT INTO answer (singleAnswer, score, mulAnswer, mulscore) VALUES (?,?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sisi", $single_answers_string, $single_average_score, $new_multi_answers_string, $total_multi_score);

// 检查 SQL 语句执行情况
if ($stmt->execute()) {
  echo "答案和分数提交成功！";
} else {
  echo "答案提交失败：". $stmt->error;
}

// 关闭连接
$stmt->close();
$conn->close();
?>
