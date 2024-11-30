<?php
// 数据库连接
include 'db_connection.php';

// 检查连接是否成功
if ($conn->connect_error) {
  die(json_encode(['success' => false, 'error' => '数据库连接失败: ' . $conn->connect_error]));
}

// 中文分词工具函数
function extractWords($text)
{
  $text = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text); // 去掉标点符号
  $text = trim($text);
  $words = explode(' ', $text);

  // 停用词过滤
  $stopWords = ['的', '和', '了', '是', '在', '对', '与', '以及', '等', '有', '为', '不', '就', '但', '这', '那', '可以', '我们', '你', '我', '他们', '她们', '它们'];
  $filteredWords = array_filter($words, function ($word) use ($stopWords) {
    return !in_array($word, $stopWords)
      && mb_strlen($word, 'UTF-8') > 1
      && !preg_match('/^Q\d+$/', $word)
      && !preg_match('/^C\d+$/', $word);
  });

  return $filteredWords;
}

// 获取特定科目数据
function getSubjectWords($conn, $subjectCode)
{
  $sql = "SELECT tableAnswer FROM answer WHERE tableAnswer LIKE '%$subjectCode%'";
  $result = $conn->query($sql);

  $words = [];
  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      // 提取特定科目内容
      $text = $row['tableAnswer'];
      preg_match_all("/$subjectCode(.*?)#/", $text, $matches);

      // 合并所有匹配到的内容
      if (!empty($matches[1])) {
        foreach ($matches[1] as $match) {
          $subjectWords = extractWords($match);
          foreach ($subjectWords as $word) {
            if (isset($words[$word])) {
              $words[$word]++;
            } else {
              $words[$word] = 1;
            }
          }
        }
      }
    }
  }

  // 转换为词云格式
  $wordCloud = [];
  foreach ($words as $text => $size) {
    $wordCloud[] = ['text' => $text, 'size' => $size * 10];
  }

  return $wordCloud;
}

// 获取词云数据（新的功能）
function getWordCloudData($conn, $type)
{
  $sql = "SELECT $type FROM answer";
  $result = $conn->query($sql);
  $words = [];

  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $answerWords = extractWords($row[$type]);

      foreach ($answerWords as $word) {
        $word = trim($word);
        if (!empty($word)) {
          if (isset($words[$word])) {
            $words[$word]++;
          } else {
            $words[$word] = 1;
          }
        }
      }
    }
  }

  // 转换为词云格式
  $wordCloud = [];
  foreach ($words as $text => $size) {
    $wordCloud[] = ['text' => $text, 'size' => $size * 10];
  }

  return $wordCloud;
}

// 获取课程名称
function getSubjectName($conn, $subjectCode)
{
  $sql = "SELECT name FROM tableclass WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $subjectCode);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    return $row['name'];
  }
  return null;
}

// 返回 JSON 响应
header('Content-Type: application/json');
try {
  // 检查请求的功能
  $type = isset($_GET['type']) ? $_GET['type'] : null;
  $subjectCode = isset($_GET['subject']) ? $_GET['subject'] : null;

  if ($type && in_array($type, ['subAnswer', 'tableAnswer'])) {
    // 获取词云数据（新功能）
    $words = getWordCloudData($conn, $type);
  } elseif ($subjectCode) {
    // 获取特定科目数据（原有功能）
    $words = getSubjectWords($conn, $subjectCode);
    $subjectName = getSubjectName($conn, $subjectCode);
  } else {
    throw new Exception('无效的请求参数。');
  }

  if (empty($words)) {
    throw new Exception('没有可用的词语数据生成词云。');
  }

  // 返回 JSON 数据
  $response = ['success' => true, 'words' => $words];
  if (isset($subjectName)) {
    $response['subjectName'] = $subjectName;
  }
  echo json_encode($response);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
