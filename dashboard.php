<?php

/**
 * dashboard.php
 *
 * Controller for preparing and displaying dashboard data
 *
 * @author BP (3komma3volt)
 * @license MIT License
 */


session_start();
include('db_functions.php');
include('render_template.php');
require_once('database.php');


if (!isset($_SESSION['spindle_data']['spindle_id'])) {
  header('Location: index.php');
}

try {
  $database = new Database();
  $pdo = $database->getConnection();
} catch (PDOException $e) {
  echo 'Error in DB execution: ' . $e->getMessage();
  error_log("Error in DB execution: " . $e->getMessage());
}


if (isset($_POST['chkClearData'])) {
  $error = clearSpindleData($pdo, $_SESSION['spindle_data']['spindle_id']);
  if ($error === 0) {
    $message = '<p>Data cleared sucessfully.</p>';
  } else {
    $message = '<p>Error clearing data.</p>';
  }
}

if (isset($_POST['formSpindleName'])) {
  $new_name = filter_input(INPUT_POST, 'formSpindleName', FILTER_SANITIZE_SPECIAL_CHARS);
  $error = renameSpindle($pdo, $_SESSION['spindle_data']['spindle_id'], $new_name);
  if ($error === 0) {
    $_SESSION['spindle_data']['spindle_alias'] = $new_name;
    $message = '<p>Name changed sucessfully.</p>';
  } else {
    $message = '<p>Error changing name.</p>';
  }
}

if (isset($_POST['timespanSelect'])) {
  $selected_timespan = intval(filter_input(INPUT_POST, 'timespanSelect', FILTER_SANITIZE_SPECIAL_CHARS));
  switch ($selected_timespan) {
    case 3:
      $sel3 = "selected";
      $timespan = 3;
      break;
    case 14:
      $sel14 = "selected";
      $timespan = 14;
      break;
    case 21:
      $sel21 = "selected";
      $timespan = 21;
      break;
    case 7:
    default:
      $sel7 = "selected";
      $timespan = 7;
      break;
  }
} else {
  $sel7 = "selected";
}

try {

  $spindle_data = getSpindleMeasurements($pdo, $_SESSION['spindle_data']['spindle_id'], $timespan ?? 7);
  $pdo = null;

  $data = [
    'spindle_id' => $_SESSION['spindle_data']['spindle_id'],
    'spindle_alias' => $_SESSION['spindle_data']['spindle_alias'],
    'select3' => $sel3 ?? '',
    'select7' => $sel7 ?? '',
    'select14' => $sel14 ?? '',
    'select21' => $sel21  ?? '',
    'message' => $message ?? ''
  ];

  if (empty($spindle_data)) {
    $data['message'] = '<p>No data available.</p>';
  }
  else {
    $readable_timestamps = [];
    foreach ($spindle_data['timestamp'] as $timestamp) {
      $readable_timestamps[] = date('m.d H:i', strtotime($timestamp));
    }

    $spindle_page_data= [
      'timestamps' => "'" . implode("', '", $readable_timestamps) . "'",
      'angle_list' => "'" . implode("', '", $spindle_data['angle']) . "'",
      'temperature_list' => "'" . implode("', '", $spindle_data['temperature']) . "'",
      'battery_list' => "'" . implode("', '", $spindle_data['battery']) . "'",
      'gravity_list' => "'" . implode("', '", $spindle_data['gravity']) . "'",  
    ];
    $data = array_merge($data, $spindle_page_data);
  }

 

  $template = 'templates/dashboard.template.php';
  $rendered_content = renderTemplate($template, $data);

  echo $rendered_content;
} catch (PDOException $e) {
  echo 'Error in DB execution: ' . $e->getMessage();
  error_log("Error in DB execution: " . $e->getMessage());
}
