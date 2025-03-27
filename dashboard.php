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
$viewConfiguration = getViewConfiguration($pdo, $_SESSION['spindle_data']['spindle_id']);

if (isset($_POST['timespanSelect'])) {
  $selected_timespan = intval(filter_input(INPUT_POST, 'timespanSelect', FILTER_SANITIZE_SPECIAL_CHARS));
  setViewDays($pdo, $_SESSION['spindle_data']['spindle_id'], $selected_timespan);
  
}
else {
  $selected_timespan = $viewConfiguration['view_days'] != 0 ? $viewConfiguration['view_days'] : 7;
} 
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
    'message' => $message ?? '',
    'gravity_visible' => $viewConfiguration['view_config']['gravity'] ?? 1,
    'temperature_visible' => $viewConfiguration['view_config']['temperature'] ?? 1,
    'battery_visible' => $viewConfiguration['view_config']['battery'] ?? 1,
    'angle_visible' => $viewConfiguration['view_config']['angle'] ?? 1,
  ];

  if (empty($spindle_data)) {
    $data['message'] = '<p>No data available.</p>';
  }
  else {
    $data_count = count($spindle_data['id']);
    if(REDUCED_DATA_POINTS > 0 && $data_count > REDUCED_DATA_POINTS) {
        $step_size = intval($data_count / REDUCED_DATA_POINTS);
        $reduced_spindle_data = array_map(function($arr) use ($step_size) {
          return array_filter($arr, function($key) use ($step_size) {
            return $key % $step_size == 0;
          }, ARRAY_FILTER_USE_KEY);
        }, $spindle_data);

        // Make sure that the last measurement is included
        if(end($reduced_spindle_data['id']) != end($spindle_data['id'])) {
          $reduced_spindle_data['id'][] = end($spindle_data['id']);
          $reduced_spindle_data['angle'][] = end($spindle_data['angle']);
          $reduced_spindle_data['temperature'][] = end($spindle_data['temperature']);
          $reduced_spindle_data['battery'][] = end($spindle_data['battery']);
          $reduced_spindle_data['gravity'][] = end($spindle_data['gravity']);
          $reduced_spindle_data['timestamp'][] = end($spindle_data['timestamp']);
        }
   
       $spindle_data = $reduced_spindle_data;
    }

    $readable_timestamps = [];
    foreach ($spindle_data['timestamp'] as $timestamp) {
      $readable_timestamps[] = date('d.m H:i', strtotime($timestamp));
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
