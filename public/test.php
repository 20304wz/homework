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
<form action="submit_quiz.php" method="POST">

  <!-- 单选题、多选题、主观题部分（省略，参考前面代码） -->

  <!-- 表格题部分 -->
  <h3>表格题</h3>
  <table border="1">
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
            <input type="text" name="table_<?php echo $column['id']; ?>_<?php echo $row_question; ?>" placeholder="请输入答案">
          </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </table>

  <input type="submit" value="提交答案">
</form>
</body>
</html>
