<?php

// CREATE NEW EMPLOYEE LOGIC
if (isset($_POST['createEmployee'])) {
  $sql = 'INSERT INTO employees 
					(firstName, lastName, assignedProjectId)
					VALUES 
					(?, ?, ?)';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('ssi', $_POST['newEmployeeFirstName'], $_POST['newEmployeeLastName'], $_POST['selectedProject']);
  $res = $stmt->execute();
  $stmt->close();
  mysqli_close($conn);
  header("Location: " . "?page=" . $_GET['page']);
  exit();
}

// CREATE NEW PROJECT LOGIC
if (isset($_POST['createProject'])) {
  $sql = 'INSERT INTO projects 
					(ProjectName)
					VALUES 
					(?)';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('s', $_POST['newProjectName']);
  try {
    $res = $stmt->execute();
    $stmt->close();
    mysqli_close($conn);
    header("Location: " . "?page=" . $_GET['page']);
    exit();
  } catch (Exception $ex) {
    header("Location: " . "?page=" . $_GET['page'] . "&action=addProject&errMsg");
  }
}
