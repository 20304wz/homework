<?php
require_once 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);

$table = $data['table'] ?? '';
$column = $data['column'] ?? '';
$id = $data['id'] ?? '';
$value = $data['value'] ?? '';

if (!$table || !$column || !$id) {
  echo json_encode(['success' => false, 'message' => '参数不完整。']);
  exit;
}

$updateQuery = "UPDATE `$table` SET `$column` = ? WHERE `ID` = ?";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param('si', $value, $id);

if ($stmt->execute()) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
?>
