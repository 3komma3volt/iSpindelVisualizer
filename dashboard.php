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

  $spindle_data = getSpindleMeasurements($pdo, $_SESSION['spindle_data']['spindle_id']);

  $data = [
    'spindle_id' => $_SESSION['spindle_data']['spindle_id'],
    'spindle_alias' => $_SESSION['spindle_data']['spindle_alias'],
    'timestamps' => "'" . implode("', '", $spindle_data['timestamp']) . "'",
    'angle_list' => "'" . implode("', '", $spindle_data['angle']) . "'",
    'temperature_list' => "'" . implode("', '", $spindle_data['temperature']) . "'",
    'battery_list' => "'" . implode("', '", $spindle_data['battery']) . "'",
    'gravity_list' => "'" . implode("', '", $spindle_data['gravity']) . "'"

  ];
  $template = 'templates/dashboard.php';
  $renderedContent = renderTemplate($template, $data);

  echo $renderedContent;
} catch (PDOException $e) {
  echo 'Error in DB execution: ' . $e->getMessage();
  error_log("Error in DB execution: " . $e->getMessage());
}
