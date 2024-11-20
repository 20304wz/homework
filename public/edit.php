<?php
// 导入数据库连接文件
include 'db_connection.php'; // 确保 db_connection.php 文件路径正确

// 获取所有表名
$sql = "SHOW TABLES";
$result = $conn->query($sql);
$tables = [];
while ($row = $result->fetch_array()) {
  $tables[] = $row[0];
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>实时编辑数据库表格</title>
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
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }
    h1 {
      text-align: center;
    }
    form {
      margin-bottom: 20px;
    }
    select, button {
      margin: 10px 0;
      padding: 5px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #f4f4f4;
    }
    td.editable {
      background-color: #f9f9f9;
      cursor: pointer;
    }
    .loading {
      text-align: center;
      font-size: 1.2em;
      color: #888;
    }
    .message {
      margin-top: 20px;
      color: green;
    }
  </style>
</head>
<body>
<h1>实时编辑数据库表格</h1>

<!-- 选择表格 -->
<form id="select-table-form">
  <label for="table_name">选择表格：</label>
  <select name="table_name" id="table_name" required>
    <option value="">-- 请选择表格 --</option>
    <?php foreach ($tables as $table): ?>
      <option value="<?= $table ?>"><?= $table ?></option>
    <?php endforeach; ?>
  </select>
</form>

<!-- 表格内容 -->
<div id="table-content">
  <p class="loading">请选择表格以加载内容。</p>
</div>

<script>
  const tableSelect = document.getElementById('table_name');
  const tableContentDiv = document.getElementById('table-content');

  function loadTableContent() {
    const selectedTable = tableSelect.value;

    if (selectedTable) {
      tableContentDiv.innerHTML = '<p class="loading">加载中...</p>';
      fetch(`fetch_table_editable.php?table=${encodeURIComponent(selectedTable)}`)
        .then(response => response.text())
        .then(data => {
          tableContentDiv.innerHTML = data;
          addEditListeners();
        })
        .catch(error => {
          console.error('Error:', error);
          tableContentDiv.innerHTML = '<p>加载表内容失败，请稍后再试。</p>';
        });
    } else {
      tableContentDiv.innerHTML = '<p class="loading">请选择表格以加载内容。</p>';
    }
  }

  function addEditListeners() {
    const editableCells = document.querySelectorAll('.editable');
    editableCells.forEach(cell => {
      cell.addEventListener('click', () => {
        const originalContent = cell.textContent.trim();
        if (cell.querySelector('input')) return;

        const input = document.createElement('input');
        input.type = 'text';
        input.value = originalContent;
        cell.innerHTML = '';
        cell.appendChild(input);
        input.focus();

        const saveEdit = () => {
          const newValue = input.value.trim();
          const column = cell.getAttribute('data-column');
          const id = cell.getAttribute('data-id');
          const table = tableSelect.value;

          if (newValue !== originalContent) {
            fetch('update_table_cell.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ table, column, id, value: newValue })
            })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  cell.textContent = newValue;
                } else {
                  cell.textContent = originalContent;
                  alert('更新失败: ' + data.message);
                }
              })
              .catch(error => {
                console.error('Error:', error);
                cell.textContent = originalContent;
                alert('更新失败，请稍后再试。');
              });
          } else {
            cell.textContent = originalContent;
          }
        };

        input.addEventListener('blur', saveEdit);
        input.addEventListener('keydown', (e) => {
          if (e.key === 'Enter') saveEdit();
          if (e.key === 'Escape') cell.textContent = originalContent;
        });
      });
    });
  }

  tableSelect.addEventListener('change', loadTableContent);
</script>
<div class="button-container">
  <a href="Ruler.php"><button>返回初始界面</button></a><br>
  <a href="Ruler_quize.php"><button>进入调查问卷</button></a>
</div>
</body>
</html>
