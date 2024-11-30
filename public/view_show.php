<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>问卷数据展示</title>
  <script src="https://www.goat1000.com/tagcanvas.min.js"></script>
  <style>
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

    input[type="number"], input[type="text"] {
      padding: 10px;
      font-size: 16px;
      width: 200px;
      margin: 10px 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    input[type="number"]:focus, input[type="text"]:focus {
      outline: none;
      border-color: #007BFF;
    }

    .graph-container, .wordcloud-container {
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

    .hidden {
      display: none;
    }

    canvas {
      display: block;
      margin: 20px auto;
      border: 1px solid #ddd;
      border-radius: 10px;
      width: 800px;
      height: 600px;
    }

    #loading {
      font-size: 18px;
      color: #555;
    }

    #error {
      font-size: 16px;
      color: red;
    }

    #tags {
      display: none;
    }

    #subjectName {
      font-size: 18px;
      font-weight: bold;
      color: #333;
      margin-bottom: 10px;
    }

    #courseWrapper {
      display: none;
      margin-top: 20px;
    }
  </style>
</head>

<body>
<h1>问卷答题情况显示</h1>

<!-- 主选择区域 -->
<div class="button-container">
  <button onclick="showSection('wordcloud')">查看词云</button>
  <button onclick="showSection('bargraph')">查看柱状图</button>
  <a href="Ruler.php"><button>返回管理者界面</button></a>
</div>

<!-- 词云区域 -->
<div id="wordcloud-section" class="hidden">
  <button onclick="loadWordCloud('subAnswer')">查看主观题词云</button>
  <button onclick="loadWordCloud('tableAnswer')">查看表格题词云</button>
  <div id="courseWrapper">
    <p id="subjectName"></p>
    <input type="text" id="subjectInput" placeholder="输入科目号 (如 C1)">
    <button onclick="loadSubjectWordCloud()">按课程生成词云</button>
  </div>
  <p id="loading">请选择词云数据</p>
  <p id="error"></p>
  <canvas id="myCanvas" width="800" height="600"></canvas>
  <div id="tags"></div>
</div>

<!-- 柱状图区域 -->
<div id="bargraph-section" class="hidden">
  <input type="number" id="questionNumberInput" placeholder="请输入题号">
  <button onclick="showGraph('single', getQuestionNumber())">显示对应题号单选题图形</button>
  <button onclick="showGraph('multi_question', getQuestionNumber())">显示对应题号多选题图形</button>
  <button onclick="showGraph('single')">显示所有单选题图形</button>
  <button onclick="showGraph('multi')">显示多选题答案分布图</button>
  <button onclick="showGraph('multi_score')">显示多选题得分柱状图</button>
  <button id="backButton" style="display: none;" onclick="resetPage()">返回</button>
  <div id="graph-container" class="graph-container">
    <p>选择一个操作以显示图形。</p>
  </div>
</div>

<script>
  function showSection(section) {
    document.getElementById('wordcloud-section').classList.add('hidden');
    document.getElementById('bargraph-section').classList.add('hidden');

    if (section === 'wordcloud') {
      document.getElementById('wordcloud-section').classList.remove('hidden');
      document.getElementById('courseWrapper').style.display = 'block'; // 显示按课程词云的输入框
    } else if (section === 'bargraph') {
      document.getElementById('bargraph-section').classList.remove('hidden');
      document.getElementById('courseWrapper').style.display = 'none'; // 隐藏按课程词云的输入框
    }
  }

  function loadWordCloud(type) {
    document.getElementById('loading').textContent = '正在加载词云数据...';
    document.getElementById('error').textContent = '';
    const canvas = document.getElementById('myCanvas');
    const tagsContainer = document.getElementById('tags');

    tagsContainer.innerHTML = '';
    TagCanvas.Delete('myCanvas');

    fetch(`view_cloud.php?type=${type}`)
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP错误! 状态码: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success && data.words.length > 0) {
          tagsContainer.innerHTML = data.words
            .map(word => `<a href="#" style="font-size:${word.size}px;" data-weight="${word.size}">${word.text}</a>`)
            .join('');
          TagCanvas.Start('myCanvas', 'tags', {
            textColour: '#333',
            outlineColour: '#000',
            reverse: true,
            depth: 1.0,
            maxSpeed: 0.1,
            weight: true,
            weightMode: 'size',
            initial: [0.1, 0.1],
          });
          document.getElementById('loading').textContent = '';
        } else {
          throw new Error('词云数据为空或后端返回失败。');
        }
      })
      .catch(error => {
        document.getElementById('loading').textContent = '';
        document.getElementById('error').textContent = `加载失败: ${error.message}`;
      });
  }

  function loadSubjectWordCloud() {
    document.getElementById('loading').textContent = '正在加载词云数据...';
    document.getElementById('error').textContent = '';
    document.getElementById('subjectName').textContent = '';
    const canvas = document.getElementById('myCanvas');
    const tagsContainer = document.getElementById('tags');
    const subject = document.getElementById('subjectInput').value.trim();

    if (!subject) {
      document.getElementById('loading').textContent = '';
      document.getElementById('error').textContent = '请输入有效的科目号！';
      return;
    }

    tagsContainer.innerHTML = '';
    TagCanvas.Delete('myCanvas');

    fetch(`view_cloud.php?subject=${subject}`)
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP错误! 状态码: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success && data.words.length > 0) {
          if (data.subjectName) {
            document.getElementById('subjectName').textContent = `${data.subjectName}`;
          }
          tagsContainer.innerHTML = data.words
            .map(word => `<a href="#" style="font-size:${word.size}px;" data-weight="${word.size}">${word.text}</a>`)
            .join('');
          TagCanvas.Start('myCanvas', 'tags', {
            textColour: '#333',
            outlineColour: '#000',
            reverse: true,
            depth: 1.0,
            maxSpeed: 0.1,
            weight: true,
            weightMode: 'size',
            initial: [0.1, 0.1],
          });
          document.getElementById('loading').textContent = '';
        } else {
          throw new Error('词云数据为空或后端返回失败。');
        }
      })
      .catch(error => {
        document.getElementById('loading').textContent = '';
        document.getElementById('error').textContent = `加载失败: ${error.message}`;
      });
  }

  function showGraph(type, questionNumber = null) {
    const graphContainer = document.getElementById('graph-container');
    const backButton = document.getElementById('backButton');

    graphContainer.innerHTML = '<p>加载中，请稍候...</p>';
    let url = '';
    if (type === 'single') {
      url = questionNumber !== null ? `view_d.php?graph=single&questionNumber=${questionNumber}` : 'view.php?graph=single';
    } else if (type === 'multi_question') {
      url = questionNumber !== null ? `view_d.php?graph=multi_question&questionNumber=${questionNumber}` : null;
    } else if (type === 'multi') {
      url = 'view.php?graph=multi';
    } else if (type === 'multi_score') {
      url = 'view.php?graph=multi_score';
    }

    if (!url) {
      alert('请输入题号！');
      return;
    }

    const xhttp = new XMLHttpRequest();
    xhttp.responseType = 'blob';
    xhttp.onload = function () {
      if (xhttp.status === 200) {
        const imageUrl = URL.createObjectURL(xhttp.response);
        graphContainer.innerHTML = `<img src="${imageUrl}" alt="图形">`;
        backButton.style.display = 'inline-block';
      } else {
        graphContainer.innerHTML = '<p>无法加载图形，请稍后重试。</p>';
      }
    };

    xhttp.onerror = function () {
      graphContainer.innerHTML = '<p>发生网络错误，请检查连接。</p>';
    };

    xhttp.open('GET', url, true);
    xhttp.send();
  }

  function getQuestionNumber() {
    const input = document.getElementById('questionNumberInput');
    const value = input.value.trim();
    if (!value) {
      alert('请输入题号！');
      return null;
    }
    return parseInt(value, 10);
  }

  function resetPage() {
    const graphContainer = document.getElementById('graph-container');
    const backButton = document.getElementById('backButton');
    graphContainer.innerHTML = '<p>选择一个操作以显示图形。</p>';
    backButton.style.display = 'none';
  }
</script>
</body>

</html>
