<?php

session_start();
// logout 
if (isset($_POST['logOut'])) {
  session_destroy();
  session_start();
  header('Location: ' . rtrim($_SERVER['PHP_SELF'], 'index.php'));
  exit;
}

// login 
$loginMsg = '';
if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {
  if ($_POST['username'] == 'Manager' && $_POST['password'] == '1234') {
    $_SESSION['logged_in'] = true;
    $_SESSION['timeout'] = time();
    $_SESSION['username'] = $_POST['username'];
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
  } else {
    $loginMsg = 'Wrong Username or Password.';
  }
}

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'projects_db';

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  die('Connection failed: ' . mysqli_connect_error());
}

$errMsg = '';
$updateErr = '';

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

if (isset($_GET['errMsg'])) {
  $errMsg = 'Project name already exist. Please choose another name.';
}

if (isset($_GET['updateErr'])) {
  $updateErr = 'Project name already exist. Please choose another name.';
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
  <script src="https://kit.fontawesome.com/8cc9ee3dc9.js" crossorigin="anonymous"></script>
</head>

<!-- Login form -->
<div id="loginForm" <?php isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true
                      ? print("style = \"display: none\"")
                      : print("style = \"display: block\"") ?>>
  <h2>Enter Username and Password</h2>
  <form action="" method="post" <?php isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true
                                  ? print("style = \"display: none\"")
                                  : print("style = \"display: block\"") ?>>
    <div>
      <input type="text" name="username" placeholder="Username = Manager" required autofocus></br>
      <input type="password" name="password" placeholder="Password = 1234" required>
    </div>
    <button type="submit" name="login">LOG IN</button>
    <h4><?php echo ($loginMsg); ?></h4>
  </form>
</div>
<div id="mainContainer" <?php isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true
                          ? print("style = \"display: block\"")
                          : print("style = \"display: none\"") ?>>

  <header>
    <nav>
      <?php
      $pages = array("Home", "Employees", "Projects");
      foreach ($pages as $page) {
        if (isset($_GET['page']) && $_GET['page'] == $page) {
          print '<a href="?page=' . $page . '" class="active"> ' . $page . '</a>';
        } else {
          print '<a href="?page=' . $page . '"> ' . $page . '</a>';
        }
      }
      ?>
    </nav>
    <div>
      <form action="" method="POST">
        <button type="submit" name="logOut" title="Log out" class="logOutBtn">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </button>
      </form>
    </div>
  </header>

  <body>
    <?php

    // TABLE DATA RENDERING
    if (isset($_GET['page']) && $_GET['page'] == 'Employees') {
      $sql = "SELECT employees.id, GROUP_CONCAT(CONCAT_WS(' ', employees.FirstName, employees.LastName) SEPARATOR ', ') AS 'Full name', 
              projects.ProjectName AS 'Assigned project' 
              FROM Employees
              LEFT JOIN projects ON employees.assignedProjectId = projects.id
              GROUP BY employees.id";
      $result = mysqli_query($conn, $sql);
    } else if (isset($_GET['page']) && $_GET['page'] == 'Projects') {
      $sql = "SELECT projects.id, projects.ProjectName, count(employees.FirstName) AS 'count', 
              GROUP_CONCAT(CONCAT_WS(' ', employees.FirstName, employees.LastName) SEPARATOR ', ') AS 'Full name' 
              FROM projects
              LEFT JOIN employees ON projects.id = employees.assignedProjectId
              WHERE projects.id != 0
              GROUP BY ProjectName
              ORDER BY id";
      $result = mysqli_query($conn, $sql);
    }

    if (isset($_GET['page']) && $_GET['page'] == 'Employees' && mysqli_num_rows($result) > 0) {
      print('<div class="tableDiv">');
      print('<table>');
      print('<thead>');
      print('<tr><th>ID.</th><th>Full name</th><th>Assigned project</th><th>Actions</th></tr>');
      print('</thead>');
      print('<tbody>');
      while ($row = mysqli_fetch_assoc($result)) {
        print('<tr>'
          . '<td>' . $row['id'] . '</td>'
          . '<td>' . $row['Full name'] . '</td>'
          . '<td>' . $row['Assigned project'] . '</td>'
          . '<td>' . '<a href="?page=Employees&action=delete&id=' . $row['id'] . '"><i class="fa-regular fa-trash-can" title="Delete employee"></i></a>'
          . '<a href="?page=Employees&action=updateEmployee&id='  . $row['id'] . '"><i class="fa-regular fa-pen-to-square" title="Update employee"></i></a></td>'
          . '</tr>');
      }
      print('</tbody>');
      print('</table>');
      print('<a href="?page=Employees&action=addEmployee"><button class="addItemBtn" title="Add new employee"><i class="fa-solid fa-plus"></i> New Employee</button></a>');
      print('</div>');
    } else if (isset($_GET['page']) && $_GET['page'] == 'Projects') {
      print('<div class="tableDiv">');
      print('<table>');
      print('<thead>');
      print('<tr><th>ID.</th><th>Project name</th><th>Assigned employees</th><th id="emplCount">Empl. count</th><th>Actions</th></tr>');
      print('</thead>');
      print('<tbody>');
      while ($row = mysqli_fetch_assoc($result)) {
        print('<tr>'
          . '<td>' . $row["id"] . '</td>'
          . '<td>' . $row["ProjectName"] . '</td>'
          . '<td>' . $row["Full name"] . '</td>'
          . '<td>' . $row["count"] . '</td>'
          . '<td><a href="?page=Projects&action=deleteProject&id='  . $row['id'] . '"><i class="fa-regular fa-trash-can" title="Delete project"></i></a>'
          . '<a href="?page=Projects&action=updateProject&id='  . $row['id'] . '"><i class="fa-regular fa-pen-to-square" title="Update project"></i></a></td>'
          . '</tr>');
      }
      print('</tbody>');
      print('</table>');
      print('<a href="?page=Projects&action=addProject"><button class="addItemBtn" title="Add new project"><i class="fa-solid fa-plus"></i> New Project</button></a>');
      print('<h4>' . $updateErr . '</h4>');
      print('</div>');
    } else {
      print('<div class="welcomeDiv">');
      print('<h3>Welcome back, ' . $_SESSION['username'] . '!</h3>');
      print('<p>Use navigation at the top of the page.</p>');
      print('<p>Have a productive day!</p>');
      print('</div>');
    }

    // CREATE EMPLOYEE/PROJECT FORMS
    if (isset($_GET['action']) && $_GET['action'] == 'addEmployee') {
      print('<div class="creationDiv">');
      print('<div>');
      print('<a href="?page=Employees" class="cancelBtn" title="Cancel" ><i class="fa-regular fa-rectangle-xmark"></i></a>');
      print('<h3>Add new employee</h3>');
      print('</div>');
      print('<form action="" method="POST" id=newEmployeeCreation>');
      print('<label for="newEmployeeFirstName">First name: </label>');
      print('<input type="text" name="newEmployeeFirstName">');
      print('<label for="newEmployeeLastName">Last name: </label>');
      print('<input type="text" name="newEmployeeLastName">');
      print('<label for="selectedProject">Assign to project: </label>');
      print('<select name="selectedProject" required >');
      print('<option value="0" disabled >Choose a project</option>');
      print('<option value="0" selected>--- No Project ---</option>');
      $sql = "SELECT projects.id, projects.ProjectName 
              FROM projects
              WHERE id != 0";
      $result = mysqli_query($conn, $sql);
      if (mysqli_num_rows($result) > 0)
      while ($row = mysqli_fetch_assoc($result)) {
        print('<option value="' . $row['id'] . '">' . $row['ProjectName'] . '</option>');
      };
      print('</select>');
      print('<button type="submit" name="createEmployee" title="Add new employee">Add</button>');
      print('</form>');
      print('</div>');
    } else if (isset($_GET['action']) && $_GET['action'] == 'addProject') {
      print('<div class="creationDiv">');
      print('<div>');
      print('<a href="?page=Projects" class="cancelBtn" title="Cancel" ><i class="fa-regular fa-rectangle-xmark"></i></a>');
      print('<h3>Add new project</h3>');
      print('</div>');
      print('<form action="" method="POST">');
      print('<label for="newProjectName">Project Name:</label>');
      print('<input type="text" name="newProjectName">');
      print('<button type="submit" name="createProject" title="Add new project">Add</button>');
      print('</form>');
      print('<h4>' . $errMsg . '</h4>');
      print('</div>');
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
      print('<div class="updateDiv">');
      print('<div>');
      print('<a href="?page=Employees" class="cancelBtn" title="Cancel" ><i class="fa-regular fa-rectangle-xmark"></i></a>');
      print('<h3>Update employee ID: ' . $_GET['id'] . '</h3>');
      print('</div>');
      print('<form action="" method="POST">');
      print('<input type="text" name="updateID" hidden value="' . $row['id'] . '"></input>');
      print('<label for="updatedFirstName">First name: </label>');
      print('<input type="text" name="updatedFirstName" value="' . $row['firstName'] . '">');
      print('<label for="updatedLastName">Last name: </label>');
      print('<input type="text" name="updatedLastName" value="' . $row['lastName'] . '">');
      print('<label for="selectedProject">Assign to: </label>');
      print('<select name="selectedProject">');
      print('<option value="0" disabled >Choose a project</option>');
      print('<option selected value="' . $row['projectId'] . '">' . $row['projectName'] . '</option>');
      $sqlProject = "SELECT projects.id, projects.ProjectName 
                    FROM projects 
                    WHERE id != 0 AND id !=" . $row['assignedProjectId'];
      $resultProject = mysqli_query($conn, $sqlProject);
      if (mysqli_num_rows($resultProject) > 0)
      while ($row = mysqli_fetch_assoc($resultProject)) {
        print('<option value="' . $row['id'] . '">' . $row['ProjectName'] . '</option>');
      };
      print('<option value="NULL">--- No Project ---</option></select>');
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
      print('<div class="updateDiv">');
      print('<div>');
      print('<a href="?page=Projects" class="cancelBtn" title="Cancel" ><i class="fa-regular fa-rectangle-xmark"></i></a>');
      print('<h3>UPDATE PROJECT ID: ' . $_GET['id'] . '</h3>');
      print('</div>');
      print('<form action="" method="POST">');
      print('<input type="text" name="updateProjectID" hidden value="' . $row['id'] . '"></input>');
      print('<label for="updatedProjectName">Project name: </label>');
      print('<input type="text" name="updatedProjectName" value="' . $row['ProjectName'] . '">');
      print('<button type="submit" name="editProject" >Update</button>');
      print('</form></div>');
    }

    mysqli_close($conn);
    ?>

    <footer>
      <span>Some cool company name 2022.</span>
    </footer>
</div>
</body>

</html>