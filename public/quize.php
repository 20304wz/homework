<?php
include 'get_questions.php';
?>

<?php
include 'get_multiple_questions.php'; // 包含题目和正确答案数据
?>
<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <title>选择题测试</title>
</head>
<body>
<form action="submit_quiz.php" method="POST">
  <?php foreach ($questions as $question): ?>
    <div>
      <!-- 显示题目 -->
      <p><?php echo htmlspecialchars($question["question"]); ?></p>
      <!-- 显示每个选项为单选框 -->
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
  <?php endforeach; ?>
  <h3>多选题</h3>
  <?php foreach ($multiple_questions as $question): ?>
    <div>
      <p><?php echo htmlspecialchars($question["name"]); ?></p>
      <label><input type="checkbox" name="question_<?php echo $question['id']; ?>[]" value="A"> <?php echo htmlspecialchars($question["A"]); ?></label><br>
      <label><input type="checkbox" name="question_<?php echo $question['id']; ?>[]" value="B"> <?php echo htmlspecialchars($question["B"]); ?></label><br>
      <label><input type="checkbox" name="question_<?php echo $question['id']; ?>[]" value="C"> <?php echo htmlspecialchars($question["C"]); ?></label><br>
      <label><input type="checkbox" name="question_<?php echo $question['id']; ?>[]" value="D"> <?php echo htmlspecialchars($question["D"]); ?></label><br>
    </div>
    <hr>
  <?php endforeach; ?>

  <input type="submit" value="提交答案">
</form>
</body>
</html>
