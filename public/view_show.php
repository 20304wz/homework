<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF - 8">
  <title>Question Graphs</title>
  <style>
    /* 容器样式，这里可以根据需要进行调整 */
    .graph - container {
               width: 400px;
               height: 300px;
               margin: 10px;
               padding: 5px;
               border: 1px solid #ccc;
             }
  </style>
</head>

<body>
<!-- 按钮区域 -->
<div>
  <input type="number" id="questionNumberInput" placeholder="请输入单选题题号">
  <button onclick="showSingleGraphById()">显示对应题号单选题图形</button>
  <button onclick="showMultiGraph()">显示多选题图形</button>
  <button onclick="showSingleGraph()">显示所有单选题图形</button>
</div>
<!-- 图形容器 -->
<div id="graph - container" class="graph - container"></div>
<!-- 返回按钮 -->
<button id="backButton" style="display: none;" onclick="location.reload()">返回</button>


<script>
  function showMultiGraph() {
    var xhttp = new XMLHttpRequest();
    xhttp.responseType = 'blob';
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        var url = URL.createObjectURL(this.response);
        var img = document.createElement('img');
        img.src = url;
        document.getElementById('graph - container').appendChild(img);
        // 隐藏显示多选题图形按钮
        document.querySelector('button[onclick="showMultiGraph()"]').style.display = 'none';
        // 显示返回按钮
        document.getElementById('backButton').style.display = 'inline - block';
      }
    };
    xhttp.open('GET', 'view.php?graph=multi', true);
    xhttp.send();
  }

  function showSingleGraph() {
    var xhttp = new XMLHttpRequest();
    xhttp.responseType = 'blob';
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        var url = URL.createObjectURL(this.response);
        var img = document.createElement('img');
        img.src = url;
        document.getElementById('graph - container').appendChild(img);
        // 隐藏显示单选题图形按钮
        document.querySelector('button[onclick="showSingleGraph()"]').style.display = 'none';
        // 显示返回按钮
        document.getElementById('backButton').style.display = 'inline - block';
      }
    };
    xhttp.open('GET', 'view.php?graph=single', true);
    xhttp.send();
  }

  function showSingleGraphById() {
    var questionNumberInput = document.getElementById('questionNumberInput');
    if (questionNumberInput) {
      var questionNumber = questionNumberInput.value;
      var xhttp = new XMLHttpRequest();
      xhttp.responseType = 'blob';
      xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          var url = URL.createObjectURL(this.response);
          var img = document.createElement('img');
          img.src = url;
          document.getElementById('graph - container').appendChild(img);
          // 隐藏显示对应题号单选题图形按钮
          document.querySelector('button[onclick="showSingleGraphById()"]').style.display = 'none';
          // 显示返回按钮
          var backButton = document.getElementById('backButton');
          if (backButton) {
            backButton.style.display = 'inline - block';
          }
        }
      };
      xhttp.open('GET', 'view_d.php?questionNumber=' + questionNumber, true);
      xhttp.send();
    }
  }
</script>
</body>

</html>
