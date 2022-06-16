<?php
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'projects_db';

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  die('Connection failed: ' . mysqli_connect_error());
}

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
  $res = $stmt->execute();
  $stmt->close();
  mysqli_close($conn);
  header("Location: " . "?page=" . $_GET['page']);
  exit();
}

?>
<!DOCTYPE html>
<html lang='en'>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Management</title>
  <style>
    <?php require './styles/styles.css' ?>
  </style>
</head>

<header>
  <nav>
    <?php
    $pages = array("Employees", "Projects");
    foreach ($pages as $page) {
      if (isset($_GET['page']) && $_GET['page'] == $page) {
        print '<a href="?page=' . $page . '" class="active"> ' . $page . '</a>';
        $activePage = $page . ".php";
      } else {
        print '<a href="?page=' . $page . '"> ' . $page . '</a>';
      }
    }
    ?>
  </nav>
</header>

<body>
  <?php

  if (isset($_GET['page']) && $_GET['page'] == 'Employees') {
    $sql = "SELECT employees.id, GROUP_CONCAT(CONCAT_WS(' ', employees.FirstName, employees.LastName) SEPARATOR ', ') AS 'Full name', 
            projects.ProjectName AS 'Assigned project' 
            FROM Employees
            LEFT JOIN projects ON employees.assignedProjectId = projects.id
            GROUP BY employees.id";
    $result = mysqli_query($conn, $sql);
  } else if (isset($_GET['page']) && $_GET['page'] == 'Projects') {
    $sql = "SELECT projects.id, projects.ProjectName, GROUP_CONCAT(CONCAT_WS(' ', employees.FirstName, employees.LastName) SEPARATOR ', ') AS 'Full name' 
            FROM projects
            LEFT JOIN employees ON projects.id = employees.assignedProjectId
            WHERE projects.id != 0
            GROUP BY ProjectName
            ORDER BY id";
    $result = mysqli_query($conn, $sql);
  }

  if (isset($_GET['page']) && $_GET['page'] == 'Employees' && mysqli_num_rows($result) > 0) {
    print('<table>');
    print('<thead>');
    print('<tr><th>Id</th><th>Full name</th><th>Assigned project</th><th>Actions</th></tr>');
    print('</thead>');
    print('<tbody>');
    while ($row = mysqli_fetch_assoc($result)) {
      print('<tr>'
        . '<td>' . $row['id'] . '</td>'
        . '<td>' . $row['Full name'] . '</td>'
        . '<td>' . $row['Assigned project'] . '</td>'
        . '<td>' . '<a href="?page=Employees&action=delete&id=' . $row['id'] . '"><button>DELETE</button></a>'
        . '<a href="?page=Employees&action=updateEmployee&id='  . $row['id'] . '"><button>UPDATE</button></a></td>'
        . '</tr>');
    }
    print('</tbody>');
    print('</table>');
  } else if (isset($_GET['page']) && $_GET['page'] == 'Projects') {
    print('<table>');
    print('<thead>');
    print('<tr><th>Id</th><th>Project name</th><th>Employees working</th><th>Actions</th></tr>');
    print('</thead>');
    print('<tbody>');
    while ($row = mysqli_fetch_assoc($result)) {
      print('<tr>'
        . '<td>' . $row["id"] . '</td>'
        . '<td>' . $row["ProjectName"] . '</td>'
        . '<td>' . $row["Full name"] . '</td>'
        . '<td><a href="?page=Projects&action=deleteProject&id='  . $row['id'] . '"><button>DELETE</button><a>'
        . '<a href="?page=Projects&action=updateProject&id='  . $row['id'] . '"><button>UPDATE</button></a></td>'
        . '</tr>');
    }
    print('</tbody>');
    print('</table>');
  } else if (mysqli_num_rows($result) == 0) {
    print('<div>Error occured while loading data. Loaded 0 results</div>');
  } else {
    print('<div>Welcome to Project manager app.</div>');
  }

  // EMPLOYEE UPDATE FORM
  if (isset($_GET['action']) && $_GET['action'] == 'updateEmployee') {
    $sql = "SELECT employees.id, employees.firstName, employees.lastName, employees.assignedProjectId, 
						projects.id AS projectId, projects.projectName 
						FROM employees
						LEFT JOIN projects ON employees.assignedProjectId = projects.id
						WHERE employees.id =" . $_GET['id'];
    $result = mysqli_query($conn, $sql);
    mysqli_num_rows($result);
    $row = mysqli_fetch_assoc($result);
    print('<div class="updateDiv"><h3>UPDATE EMPLOYEE ID: ' . $_GET['id'] . '</h3>');
    print('<form action="" method="POST">');
    print('<input type="text" name="updateID" hidden value="' . $row['id'] . '"></input>');
    print('<label for="updatedFirstName">First name: </label>');
    print('<input type="text" name="updatedFirstName" value="' . $row['firstName'] . '">');
    print('<label for="updatedLastName">Last name: </label>');
    print('<input type="text" name="updatedLastName" value="' . $row['lastName'] . '">');
    print('<label for="selectedProject">Assign to: </label>');
    print('<select name="selectedProject">');
    print('<option value="' . $row['projectId'] . '">' . $row['projectName'] . '</option>');
    $sqlProject = "SELECT projects.id, projects.ProjectName 
                  FROM projects 
                  WHERE id != 0 AND id !=" . $row['assignedProjectId'];
    $resultProject = mysqli_query($conn, $sqlProject);
    if (mysqli_num_rows($resultProject) > 0)
      while ($row = mysqli_fetch_assoc($resultProject)) {
        print('<option value="' . $row['id'] . '">' . $row['ProjectName'] . '</option>');
      };
    print('<option value="NULL">---No Project---</option></select>');
    print('<button type="submit" name="editEmployee" >Update</button>');
    print('</form></div>');
  }

  // PROJECT UPDATE FORM
  if (isset($_GET['action']) && $_GET['action'] == 'updateProject') {
    $sql = "SELECT projects.id, projects.ProjectName 
            FROM projects
						WHERE id =" . $_GET['id'];
    $result = mysqli_query($conn, $sql);
    mysqli_num_rows($result);
    $row = mysqli_fetch_assoc($result);
    print('<div class="updateDiv"><h3>UPDATE PROJECT ID: ' . $_GET['id'] . '</h3>');
    print('<form action="" method="POST">');
    print('<input type="text" name="updateProjectID" hidden value="' . $row['id'] . '"></input>');
    print('<input type="text" name="updatedProjectName" value="' . $row['ProjectName'] . '">');
    print('<button type="submit" name="editProject" >Update</button>');
    print('</form></div>');
  }

  mysqli_close($conn);
  ?>

</body>

</html>