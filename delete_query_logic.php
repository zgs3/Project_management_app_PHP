<?php

// EMPLOYEE DELETE LOGIC
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
  $sql = 'DELETE FROM Employees 
          WHERE id = ?';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('i', $_GET['id']);
  $res = $stmt->execute();
  $stmt->close();
  mysqli_close($conn);
  header("Location: " . "?page=" . $_GET['page']);
  exit();
}

// PROJECT DELETE LOGIC
if (isset($_GET['action']) && $_GET['action'] == 'deleteProject') {
  $sql = 'DELETE FROM projects 
          WHERE id = ?';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('i', $_GET['id']);
  $res = $stmt->execute();
  $stmt->close();
  mysqli_close($conn);
  header("Location: " . "?page=" . $_GET['page']);
  exit();
}
