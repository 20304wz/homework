<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Question Graphs</title>
  <style>
    /* 全局样式 */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
      color: #333;
      text-align: center;
    }

    h1 {
      margin-top: 20px;
      font-size: 24px;
      color: #555;
    }

    /* 按钮样式 */
    button {
      margin: 5px;
      padding: 12px 20px;
      font-size: 16px;
      color: #fff;
      background-color: #007BFF;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #0056b3;
    }

    button:disabled {
      background-color: #ccc;
      cursor: not-allowed;
    }

    /* 输入框样式 */
    input[type="number"] {
      padding: 10px;
      font-size: 16px;
      width: 200px;
      margin: 10px 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    input[type="number"]:focus {
      outline: none;
      border-color: #007BFF;
    }

    /* 图形容器样式 */
    .graph-container {
      width: 80%;
      max-width: 600px;
      height: 400px;
      margin: 20px auto;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: auto;
    }

    .graph-container img {
      max-width: 100%;
      max-height: 100%;
      border-radius: 5px;
    }

    .graph-container p {
      font-size: 16px;
      color: #888;
    }

    /* 按钮区域 */
    .button-container {
      margin: 20px 0;
    }
  </style>
</head>

<body>
<h1>Question Graphs Viewer</h1>

<!-- 按钮区域 -->
<div class="button-container">
  <input type="number" id="questionNumberInput" placeholder="请输入单选题题号">
  <button onclick="showGraph('single', getQuestionNumber())">显示对应题号单选题图形</button>
  <button onclick="showGraph('multi')">显示多选题图形</button>
  <button onclick="showGraph('single')">显示所有单选题图形</button>
  <button id="backButton" style="display: none;" onclick="resetPage()">返回</button>
</div>

<!-- 图形容器 -->
<div id="graph-container" class="graph-container">
  <p>选择一个操作以显示图形。</p>
</div>

<script>
  /**
   * 显示图形的函数
   * @param {string} type - 图形类型 ('single' 或 'multi')
   * @param {number} [questionNumber] - 单选题题号 (可选)
   */
  function showGraph(type, questionNumber = null) {
    const graphContainer = document.getElementById('graph-container');
    const backButton = document.getElementById('backButton');

    // 清空容器内容
    graphContainer.innerHTML = '<p>加载中，请稍候...</p>';

    // 构建请求 URL
    let url = '';
    if (type === 'multi') {
      url = 'view.php?graph=multi';
    } else if (type === 'single') {
      url = questionNumber !== null
        ? `view_d.php?questionNumber=${questionNumber}`
        : 'view.php?graph=single';
    }

    // 发起请求
    const xhttp = new XMLHttpRequest();
    xhttp.responseType = 'blob';
    xhttp.onload = function () {
      if (xhttp.status === 200) {
        const url = URL.createObjectURL(xhttp.response);
        graphContainer.innerHTML = ''; // 清空容器
        const img = document.createElement('img');
        img.src = url;
        graphContainer.appendChild(img);

        // 隐藏其他按钮，显示返回按钮
        toggleButtons(false);
        backButton.style.display = 'inline-block';
      } else {
        graphContainer.innerHTML = `<p>无法加载图形，请稍后重试。</p>`;
      }
    };

    xhttp.onerror = function () {
      graphContainer.innerHTML = `<p>发生网络错误，请检查连接。</p>`;
    };

    xhttp.open('GET', url, true);
    xhttp.send();
  }

  /**
   * 获取输入的题号
   * @returns {number|null} 输入的题号或 null
   */
  function getQuestionNumber() {
    const input = document.getElementById('questionNumberInput');
    const value = input.value.trim();
    if (!value) {
      alert('请输入题号！');
      return null;
    }
    return parseInt(value, 10);
  }

  /**
   * 重置页面状态
   */
  function resetPage() {
    const graphContainer = document.getElementById('graph-container');
    const backButton = document.getElementById('backButton');

    // 清空容器并显示默认消息
    graphContainer.innerHTML = '<p>选择一个操作以显示图形。</p>';

    // 恢复按钮状态
    toggleButtons(true);
    backButton.style.display = 'none';
  }

  /**
   * 切换按钮的显示状态
   * @param {boolean} isVisible - 是否显示操作按钮
   */
  function toggleButtons(isVisible) {
    const buttons = document.querySelectorAll('button:not(#backButton)');
    buttons.forEach(button => {
      button.style.display = isVisible ? 'inline-block' : 'none';
    });
  }
</script>
</body>

</html>
