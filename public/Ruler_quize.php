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
<style>
  button {
    font-size: 1.2em; /* 增大按钮文字 */
    padding: 15px 25px; /* 增大按钮大小 */
    border: none;
    border-radius: 10px;
    background-color: #007BFF;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;

  }
  button:hover {
    background-color: #0056b3;
    transform: scale(1.1); /* 鼠标悬停时放大 */
  }
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
<?php $single_choice_count = 1; ?>
<?php foreach ($questions as $question): ?>
  <div>
    <p><?php echo $single_choice_count . ". " . htmlspecialchars($question["question"]); ?></p>
    <p>A. <?php echo htmlspecialchars($question["A"]); ?></p>
    <p>B. <?php echo htmlspecialchars($question["B"]); ?></p>
    <p>C. <?php echo htmlspecialchars($question["C"]); ?></p>
    <p>D. <?php echo htmlspecialchars($question["D"]); ?></p>
    <p>E. <?php echo htmlspecialchars($question["E"]); ?></p>
  </div>
  <hr>
  <?php $single_choice_count++; ?>
<?php endforeach; ?>

<h3 id="mulchoice">2.多选题</h3>
<?php $multiple_choice_count = 1; ?>
<?php foreach ($multiple_questions as $multiple_question): ?>
  <div>
    <p><?php echo $multiple_choice_count . ". " . htmlspecialchars($multiple_question["name"]); ?></p>
    <p> <?php echo htmlspecialchars($multiple_question["A"]); ?></p>
    <p> <?php echo htmlspecialchars($multiple_question["B"]); ?></p>
    <p> <?php echo htmlspecialchars($multiple_question["C"]); ?></p>
    <p> <?php echo htmlspecialchars($multiple_question["D"]); ?></p>
  </div>
  <hr>
  <?php $multiple_choice_count++; ?>
<?php endforeach; ?>

<h3 id="subquestion">3.主观题</h3>
<?php foreach ($subjective_questions as $subjective_question): ?>
  <div>
    <p><?php echo htmlspecialchars($subjective_question["question"]); ?></p>
  </div>
  <hr>
<?php endforeach; ?>

<h3 id="tablequestion">4.表格题</h3>
<table border="1" width="40%" cellspacing="0" cellpadding="5">
  <tr>
    <th>题目</th>
    <?php foreach ($table_questions as $question): ?>
      <th><?php echo htmlspecialchars($question['row_question']); ?></th>
    <?php endforeach; ?>
  </tr>
  <?php foreach ($table_columns as $column): ?>
    <tr>
      <td><?php echo htmlspecialchars($column["name"]); ?></td>
      <?php foreach ($table_questions as $question): ?>
        <td></td>
      <?php endforeach; ?>
    </tr>
  <?php endforeach; ?>
</table>
<div class="button-container">
  <a href="Ruler.php"><button>返回管理者界面</button></a>
</div>
</body>
</html>
