<?php

/**
 * login.php
 *
 * Controller wich handles the login logic
 *
 * @author BP (3komma3volt)
 * @license MIT License
 */


include('render_template.php');
include('db_functions.php');
require_once('database.php');

$post_data = [];
$data = [
  'error_message' => '',
];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $post_data['spindle_id'] = filter_input(INPUT_POST, 'spindle_id', FILTER_SANITIZE_SPECIAL_CHARS);
  $post_data['spindle_key'] = filter_input(INPUT_POST, 'spindle_key', FILTER_SANITIZE_SPECIAL_CHARS);
}

if (!empty($post_data['spindle_id']) && !empty($post_data['spindle_key'])) {
  try {
    $database = new Database();
    $pdo = $database->getConnection();

    $spindle_credentials = checkSpindleKey($pdo, $post_data['spindle_id'], $post_data['spindle_key']);

    if (!$spindle_credentials['login']) {
      $data['error_message'] = "Wrong Spindle ID or Spindle Key";
    }
  } catch (PDOException $e) {
    echo 'Error in DB execution: ' . $e->getMessage();
    error_log("Error in DB execution: " . $e->getMessage());
  } catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    error_log("Error: " . $e->getMessage());
  }
}

if (isset($spindle_credentials) && $spindle_credentials['login']) {
  session_start();
  $_SESSION['spindle_data'] = [
    'spindle_id' => $post_data['spindle_id'],
    'spindle_alias' => $spindle_credentials['alias']
  ];
  header('Location: index.php?p=dash');
}

if ((isset($spindle_credentials) && !$spindle_credentials['login']) || empty($post_data['spindle_id']) || empty($post_data['spindle_key'])) {


  $template = 'templates/login.php';
  $renderedContent = renderTemplate($template, $data);

  echo $renderedContent;
}
