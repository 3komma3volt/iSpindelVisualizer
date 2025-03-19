<?php

session_start();
include('db_functions.php');
include('render_template.php');
require_once('database.php');

header('Content-Type: application/json');

if (!isset($_SESSION['spindle_data']['spindle_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Not logged in'
    ]);
    exit;
  }

if(!isset($_POST['changed_view']) || !isset($_POST['changed_value'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No data provided'
    ]);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    $result =  setViewConfiguration($pdo, $_SESSION['spindle_data']['spindle_id'], $_POST['changed_view'], $_POST['changed_value']);
    if ($result === 0) {
        echo json_encode([
            'success' => true,
                ]);
        exit;
    }
        echo json_encode([
        'success' => false,
        'message' => "Error in DB execution"
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error in DB execution'
    ]);
}



?>