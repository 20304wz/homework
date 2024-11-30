<?php
// 数据库连接配置
include 'db_connection.php';

// 开始事务
$conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

try {
  // 查询 answer 表中最大的 ID 值
  $sql = "SELECT MAX(ID) AS max_id FROM answer";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $next_id = $row['max_id'] + 1;
  } else {
    $next_id = 1;
  }

  // 单选题得分映射
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

  // 处理单选题答案
  $single_answers = [];
  foreach ($_POST as $key => $value) {
    if (strpos($key, 'question_') === 0 && !is_array($value)) {
      $single_answers[] = $value;
      if (isset($single_score_map[$value])) {
        $total_single_score += $single_score_map[$value];
      }
    }
  }

  // 构建单选题答案字符串
  $single_answers_string = '';
  foreach ($single_answers as $index => $answer) {
    $single_answers_string .= ($index + 1) . '.' . $answer . ',';
  }
  $single_answers_string = rtrim($single_answers_string, ',');

  // 计算单选题平均分
  $single_average_score = count($single_answers) > 0 ? (int) round($total_single_score / count($single_answers)) : 0;

  // 处理多选题答案
  $multi_answers = [];
  $multi_answer_desc = [];
  $question_count = 1;
  foreach ($_POST as $key => $value) {
    if (strpos($key, 'multiple_question_') === 0 && is_array($value)) {
      $answer = [];
      foreach ($value as $v) {
        $answer[] = $v;
      }
      sort($answer);
      $answer_str = implode("", $answer);
      $multi_answers[] = $answer_str;

      // 获取当前多选题的 ID
      $id = intval(str_replace('multiple_question_', '', $key));

      // 查询 mulanswer 表获取正确答案
      $sql_check = "SELECT answer FROM mulanswer WHERE id = ?";
      $stmt_check = $conn->prepare($sql_check);
      if ($stmt_check) {
        $stmt_check->bind_param('i', $id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows > 0) {
          $row_check = $result_check->fetch_assoc();
          $correct_answer = explode(',', $row_check['answer']);
          sort($correct_answer);
          $correct_answer_str = implode("", $correct_answer);

          // 检查答案是否包含错误选项
          $correct_parts = str_split($correct_answer_str); // 正确答案的选项
          $submitted_parts = str_split($answer_str); // 用户提交的选项

          $wrong_parts = array_diff($submitted_parts, $correct_parts); // 检查是否有错误选项
          if (count($wrong_parts) > 0) {
            // 如果存在错误选项，得 0 分
            $total_multi_score += 0;
          } elseif ($answer_str === $correct_answer_str) {
            // 完全正确，满分
            $total_multi_score += 10;
          } else {
            // 部分正确，按比例得分
            $correct_count = count(array_intersect($submitted_parts, $correct_parts)); // 正确选项数量
            $total_multi_score += round(($correct_count / count($correct_parts)) * 10, 2); // 按比例得分
          }
        } else {
          echo "<h4 style='color:red;'>多选题 ID {$id} 未找到对应的正确答案。</h4>";
        }
        $stmt_check->close();
      } else {
        echo "<h4 style='color:red;'>查询 mulanswer 表时发生错误: {$conn->error}</h4>";
      }

      $multi_answer_desc[] = "第{$question_count}题选择了{$answer_str}";
      $question_count++;
    }
  }

  // 构建多选题答案字符串
  $new_multi_answers_string = '';
  foreach ($multi_answers as $index => $answer) {
    $new_multi_answers_string .= ($index + 1) . '.' . $answer . ',';
  }
  $new_multi_answers_string = rtrim($new_multi_answers_string, ',');

  // 处理主观题答案
  $subjective_answers = [];
  foreach ($_POST as $key => $value) {
    if (strpos($key, 'subjective_') === 0) {
      $subjective_answers[] = $value;
    }
  }
  $subjective_answers_string = implode("#", $subjective_answers);

  // 处理表格题答案
  $table_answers = [];
  foreach ($_POST as $key => $value) {
    if (strpos($key, 'table_') === 0) {
      $parts = explode('_', $key);
      $column_id = $parts[1];
      $row_question = $parts[2];
      if (!isset($table_answers[$column_id])) {
        $table_answers[$column_id] = [];
      }
      $table_answers[$column_id][$row_question] = $value;
    }
  }

  // 构建表格题答案字符串
  $tableAnswerData = '';
  foreach ($table_answers as $column_id => $column_answer) {
    foreach ($column_answer as $row_question => $value) {
      $tableAnswerData .= "C{$column_id}.Q{$row_question}:{$value}#";
    }
  }
  $tableAnswerData = rtrim($tableAnswerData, ',');

  // 插入到 answer 表
  $sql = "INSERT INTO answer (ID, singleAnswer, score, mulAnswer, mulscore, subAnswer, tableAnswer) VALUES (?,?,?,?,?,?,?)";
  $stmt = $conn->prepare($sql);
  if ($stmt) {
    $stmt->bind_param(
      'issssss',
      $next_id,
      $single_answers_string,
      $single_average_score,
      $new_multi_answers_string,
      $total_multi_score,
      $subjective_answers_string,
      $tableAnswerData
    );
    if ($stmt->execute()) {
      echo "答案和分数提交成功！";
    } else {
      echo "答案提交失败：" . $stmt->error;
    }
    $stmt->close();
  } else {
    echo "SQL 语句准备失败：" . $conn->error;
  }

  // 提交事务
  $conn->commit();
} catch (Exception $e) {
  $conn->rollback();
  echo "<h3>发生异常：</h3>" . $e->getMessage();
}

// 关闭数据库连接
$conn->close();
?>
