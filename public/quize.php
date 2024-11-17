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
  <title>毕业生调查问卷</title>
</head>
<body>
<form action="submit_quiz.php" method="POST">
  <style>
    .question-links {
      font-size: 23px;
    }
    .red-text {
      color: #ff0000;
    }
  </style>
  <div class="question-links" style="text-align: center;">
    题目目录<br />
    <a href="#singlechoice">一.选择题</a>
    <a href="#mulchoice">二.多选题</a><br />
    <a href="#subquestion">三.主观题</a>
    <a href="#tablequestion">四.表格题</a>
  </div>
  <h3 id="singlechoice">1.单选题</h3>
  <!-- 单选题部分 -->
  <?php $single_choice_count = 1; ?>
  <?php foreach ($questions as $question): ?>
    <div>
      <p><?php echo $single_choice_count . ". " . htmlspecialchars($question["question"]); ?></p>
      <label>
        <input type="radio" name="question_<?php echo $question['ID']; ?>" value="A">
        <?php echo htmlspecialchars($question["A"]); ?>
      </label><br>
      <label>
        <input type="radio" name="question_<?php echo $question['ID']; ?>" value="B">
        <?php echo htmlspecialchars($question["B"]); ?>
      </label><br>
      <label>
        <input type="radio" name="question_<?php echo $question['ID']; ?>" value="C">
        <?php echo htmlspecialchars($question["C"]); ?>
      </label><br>
      <label>
        <input type="radio" name="question_<?php echo $question['ID']; ?>" value="D">
        <?php echo htmlspecialchars($question["D"]); ?>
      </label><br>
      <label>
        <input type="radio" name="question_<?php echo $question['ID']; ?>" value="E">
        <?php echo htmlspecialchars($question["E"]); ?>
      </label><br>
    </div>
    <hr>
    <?php $single_choice_count++; ?>
  <?php endforeach; ?>

  <!-- 多选题部分 -->
  <h3 id="mulchoice">2.多选题</h3>
  <?php $multiple_choice_count = 1; ?>
  <?php foreach ($multiple_questions as $multiple_question): ?>
    <div>
      <p><?php echo $multiple_choice_count . ". " . htmlspecialchars($multiple_question["name"]); ?></p>
      <label><input type="checkbox" name="multiple_question_<?php echo $multiple_question['ID']; ?>[]" value="A"> <?php echo htmlspecialchars($multiple_question["A"]); ?></label><br>
      <label><input type="checkbox" name="multiple_question_<?php echo $multiple_question['ID']; ?>[]" value="B"> <?php echo htmlspecialchars($multiple_question["B"]); ?></label><br>
      <label><input type="checkbox" name="multiple_question_<?php echo$multiple_question['ID']; ?>[]" value="C"> <?php echo htmlspecialchars($multiple_question["C"]); ?></label><br>
      <label><input type="checkbox" name="multiple_question_<?php echo $multiple_question['ID']; ?>[]" value="D"> <?php echo htmlspecialchars($multiple_question["D"]); ?></label><br>
    </div>
    <hr>
    <?php $multiple_choice_count++; ?>
  <?php endforeach; ?>


  <h3 id="subquestion">3.主观题</h3>
  <?php foreach ($subjective_questions as $subjective_question): ?>
    <div>
      <p><?php echo htmlspecialchars($subjective_question["question"]); ?></p>
      <textarea name="subjective_<?php echo $subjective_question['ID']; ?>" rows="4" cols="50" placeholder="请输入您的答案"></textarea>
    </div>
    <hr>
  <?php endforeach; ?>

  <!-- 表格题部分 -->
  <h3 id="tablequestion">4.表格题</h3>
  <table border="1" width="40%" cellspacing="0" cellpadding="5">
    <tr>
      <th>题目</th>
      <!-- 动态生成列标题 -->
      <?php foreach ($table_questions as $question): ?>
        <th><?php echo htmlspecialchars($question['row_question']); ?></th>
      <?php endforeach; ?>
    </tr>

    <!-- 假设 $table_columns 是已经定义的数组，包含列问题的数据 -->
    <?php foreach ($table_columns as $column): ?>
      <tr>
        <td><?php echo htmlspecialchars($column["name"]); ?></td>
        <!-- 动态生成每一列对应的输入框 -->
        <?php foreach ($table_questions as $question): ?>
          <td>
            <input type="text"
                   name="table_<?php echo htmlspecialchars($column['ID']); ?>_<?php echo htmlspecialchars($question['ID']); ?>"
                   placeholder="">
          </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </table>


  </table>

  <input type="submit" value="提交答案">
</form>
</body>
</html>
