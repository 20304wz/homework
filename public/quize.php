<?php
include 'get_questions.php'; // 单选题数据
include 'get_multiple_questions.php'; // 多选题数据
include 'get_subjective_questions.php'; // 主观题数据
include 'get_table_questions.php'; // 表格列问题数据 (包含 Q1, Q2, Q3)
include 'get_table_columns.php'; // 表格行问题数据 (包含 id, name)
?>
<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <title>选择题测试</title>
</head>
<body>
<form action="submit_quiz.php" method="POST">g
    <!-- 表格行 -->
  </table>

  <!-- 单选题部分 -->
  <h3>单选题</h3>
  <?php $single_choice_count = 1; ?>
  <?php foreach ($questions as $question): ?>
    <div>
      <p><?php echo $single_choice_count . ". " . htmlspecialchars($question["question"]); ?></p>
      <label>
        <input type="radio" name="question_<?php echo $question['id']; ?>" value="A">
        <?php echo htmlspecialchars($question["A"]); ?>
      </label><br>
      <label>
        <input type="radio" name="question_<?php echo $question['id']; ?>" value="B">
        <?php echo htmlspecialchars($question["B"]); ?>
      </label><br>
      <label>
        <input type="radio" name="question_<?php echo $question['id']; ?>" value="C">
        <?php echo htmlspecialchars($question["C"]); ?>
      </label><br>
      <label>
        <input type="radio" name="question_<?php echo $question['id']; ?>" value="D">
        <?php echo htmlspecialchars($question["D"]); ?>
      </label><br>
      <label>
        <input type="radio" name="question_<?php echo $question['id']; ?>" value="E">
        <?php echo htmlspecialchars($question["E"]); ?>
      </label><br>
    </div>
    <hr>
    <?php $single_choice_count++; ?>
  <?php endforeach; ?>

  <!-- 多选题部分 -->
  <h3>多选题</h3>
  <?php $multiple_choice_count = 1; ?>
  <?php foreach ($multiple_questions as $question): ?>
    <div>
      <p><?php echo $multiple_choice_count . ". " . htmlspecialchars($question["name"]); ?></p>
      <label><input type="checkbox" name="question_<?php echo $question['id']; ?>[]" value="A"> <?php echo htmlspecialchars($question["A"]); ?></label><br>
      <label><input type="checkbox" name="question_<?php echo $question['id']; ?>[]" value="B"> <?php echo htmlspecialchars($question["B"]); ?></label><br>
      <label><input type="checkbox" name="question_<?php echo $question['id']; ?>[]" value="C"> <?php echo htmlspecialchars($question["C"]); ?></label><br>
      <label><input type="checkbox" name="question_<?php echo $question['id']; ?>[]" value="D"> <?php echo htmlspecialchars($question["D"]); ?></label><br>
    </div>
    <hr>
    <?php $multiple_choice_count++; ?>
  <?php endforeach; ?>

  <!-- 主观题部分 -->
  <h3>主观题</h3>
  <?php foreach ($subjective_questions as $question): ?>
    <div>
      <p><?php echo htmlspecialchars($question["question"]); ?></p>
      <textarea name="question_<?php echo $question['id']; ?>" rows="4" cols="50" placeholder="请输入您的答案"></textarea>
    </div>
    <hr>
  <?php endforeach; ?>

  <!-- 表格题部分 -->
  <h3>表格题</h3>
  <table border="1" width="40%"  cellspacing="0" cellpadding="5">
    <tr>
      <th>题目</th>
      <?php foreach (['Q1', 'Q2', 'Q3'] as $column_question): ?>
        <th><?php echo htmlspecialchars($table_questions[$column_question]); ?></th>
      <?php endforeach; ?>
    </tr>

    <!-- 生成表格的列问题作为行 -->
    <?php foreach ($table_columns as $column): ?>
      <tr>
        <td><?php echo htmlspecialchars($column["name"]); ?></td>
        <?php foreach (['Q1', 'Q2', 'Q3'] as $row_question): ?>
          <td>
            <input type="text" name="table_<?php echo $column['id']; ?>_<?php echo $row_question; ?>" placeholder="">
          </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </table>

  <input type="submit" value="提交答案">
</form>
</body>
</html>
