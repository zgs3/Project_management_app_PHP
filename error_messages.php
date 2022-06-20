<?php

$errMsg = '';
$updateErr = '';

if (isset($_GET['errMsg'])) {
  $errMsg = 'Project name already exist. Please choose another name.';
}

if (isset($_GET['updateErr'])) {
  $updateErr = 'Project name already exist. Please choose another name.';
}
