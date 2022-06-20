<?php

// UPDATE EMPLOYEE LOGIC
if (isset($_POST['editEmployee'])) {
  $sql = 'UPDATE employees 
          SET firstName = ?, lastName = ?, assignedProjectId = ? 
          WHERE id = ?';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('ssii', $_POST['updatedFirstName'], $_POST['updatedLastName'], $_POST['selectedProject'], $_POST['updateID']);
  $res = $stmt->execute();
  $stmt->close();
  mysqli_close($conn);
  header("Location: " . "?page=" . $_GET['page']);
  exit();
}

// UPDATE PROJECT LOGIC
if (isset($_POST['editProject'])) {
  $sql = 'UPDATE projects 
          SET projectName = ? 
          WHERE id = ?';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('si', $_POST['updatedProjectName'], $_POST['updateProjectID']);
  try {
    $res = $stmt->execute();
    $stmt->close();
    mysqli_close($conn);
    header("Location: " . "?page=" . $_GET['page']);
    exit();
  } catch (Exception $ex) {
    header("Location: " . "?page=" . $_GET['page'] . "&updateErr");
  }
}
