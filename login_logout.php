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
