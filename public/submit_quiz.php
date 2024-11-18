<?php
// 数据库连接配置
include 'db_connection.php';

// 开始事务
$conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

try {
  // 查询answer表中最大的ID值
  $sql = "SELECT MAX(ID) AS max_id FROM answer";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $next_id = $row['max_id'] + 1;
  } else {
    $next_id = 1;
  }

  // 处理单选题部分（假设之前代码中的相关逻辑不变）
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
  if (count($single_answers) > 0) {
    $single_average_score = (int) round($total_single_score / count($single_answers));
  } else {
    $single_average_score = 0;
  }

  // 将单选题答案转换成字符串
  $single_answers_string = implode(",", $single_answers);


  // 处理多选题部分（假设之前代码中的相关逻辑不变）
  // 处理多选题答案并构建答案描述
  $multi_answers = [];
  $multi_answer_desc = [];
  $question_count = 1;
  // 处理多选题答案并根据multanswer表判断得分
  foreach ($_POST as $key => $value) {
    if (strpos($key,'multiple_question_') === 0 && is_array($value)) {
      $answer = [];
      foreach ($value as $v) {
        $answer[] = $v;
      }
      sort($answer);
      $answer_str = implode("", $answer);
      $multi_answers[] = $answer_str;

      // 获取当前多选题的id（假设这里的id获取方式与之前一致且正确）
      $id = intval(str_replace('multiple_question_', '', $key));

      // 使用参数化查询改进查询multanswer表获取正确答案
      $sql_check = "SELECT answer FROM multanswer WHERE id =?";
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

          // 检查答案是否完全正确
          if ($answer_str === $correct_answer_str) {
            $total_multi_score += 10;
          }
        }
        $stmt_check->close();
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


  // 处理主观题部分（假设之前代码中的相关逻辑不变）
  // 处理主观题答案并构建答案描述
  $subjective_answers = [];
  $question_count = 1;
  foreach ($_POST as $key => $value) {
    if (strpos($key,'subjective_') === 0) {
      $subjective_answers[] = $value;
      $question_count++;
    }
  }

  // 将主观题答案转换成字符串
  $subjective_answers_string = implode(",", $subjective_answers);


  // 处理表格题部分（假设之前代码中的相关逻辑不变）
  // 处理表格题答案并构建答案描述
  $table_answers = [];
  foreach ($_POST as $key => $value) {
    if (strpos($key, 'table_') === 0) {
      // 解析键名获取列id和行问题
      $parts = explode('_', $key);
      $column_id = $parts[1];
      $row_question = $parts[2];
      if (!isset($table_answers[$column_id])) {
        $table_answers[$column_id] = [];
      }
      $table_answers[$column_id][$row_question] = $value;
    }
  }

  // 将表格题答案转换为适合插入数据库的格式（直接使用表格题答案数组构建字符串）
  $tableAnswerData = '';
  foreach ($table_answers as $column_id => $column_answer) {
    foreach ($column_answer as $row_question => $value) {
      $tableAnswerData.= "C{$column_id}.Q{$row_question}:{$value},";
    }
  }
  $tableAnswerData = rtrim($tableAnswerData, ',');


  // 构建正确的SQL语句和绑定参数
  $param_types = 'ssssss';
  $bind_params = [$single_answers_string, $single_average_score, $new_multi_answers_string, $total_multi_score, $subjective_answers_string, $tableAnswerData];
  $sql = "INSERT INTO answer (ID, singleAnswer, score, mulAnswer, mulscore, subAnswer, tableAnswer) VALUES (?,?,?,?,?,?,?)";

  $stmt = $conn->prepare($sql);
  if ($stmt) {
    $stmt->bind_param('i'. $param_types, $next_id,...$bind_params);
    // 检查SQL语句执行情况
    if ($stmt->execute()) {
      echo "答案和分数提交成功！";
    } else {
      echo "答案提交失败：". $stmt->error;
    }
    // 关闭连接
    $stmt->close();
  } else {
    echo "SQL语句准备失败：". $conn->error;
  }

  // 提交事务
  $conn->commit();
} catch (Exception $e) {
  // 如果发生异常，回滚事务
  $conn->rollback();
  throw $e;
}

// 关闭数据库连接
$conn->close();
?>
