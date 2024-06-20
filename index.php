<?php

/**
 * index.php
 *
 * Main page selecting logic
 *
 * @author BP (3komma3volt)
 * @license MIT License
 */

session_start();
include('db_functions.php');
require_once('database.php');

$page = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_SPECIAL_CHARS);

switch ($page) {

  case 'dash':
    header('Location: dashboard.php');
    break;

  case 'logout':
    header('Location: logout.php');
    break;

  case 'login':
    header('Location: login.php');
  default:
    if (isset($_SESSION['spindle_data']['spindle_id'])) {
      header('Location: dashboard.php');
    } else {
      header('Location: login.php');
    }

    break;
}
