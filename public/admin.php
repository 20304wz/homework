<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['type'] != 'admin') {
  header("Location: login.php");
  exit;
}

echo "<h1>Welcome Admin, " . htmlspecialchars($_SESSION['user']['name']) . "</h1>";
echo "<a href='logout.php'>Logout</a>";
?>
