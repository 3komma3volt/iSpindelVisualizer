<?php

/**
 * update.php
 *
 * Updates spindle data or creates a new device, if not already existing
 *
 * @author BP (3komma3volt)
 * @license MIT License
 */

require_once("database.php");

if (!isset($_GET['key']) || empty($_GET['key'])) {
    echo 'Missing or empty secret key for your iSpindel!';
    exit();
}

$spindle_key_raw = filter_input(INPUT_GET, 'key', FILTER_SANITIZE_SPECIAL_CHARS);

try {
    $post_data = file_get_contents("php://input");
    $data = json_decode($post_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo 'JSON file format mismatch';
        error_log('JSON file format mismatch');
        exit();
    }
    $json_fields = ['ID', 'name', 'angle', 'temperature', 'temp_units', 'gravity', 'interval', 'RSSI'];
    foreach ($json_fields as $field) {
        if(!isset($data[$field])) {
            echo 'Missing required data field: ' . $field; 
            exit();
        }
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    error_log('Error in processing data: ' . $e->getMessage());
}

try {
    $database = new Database();
    $pdo = $database->getConnection();

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $check_existing = "SELECT spindle_key FROM spindles WHERE spindle_id = :spindle_id";
    $checkpdo = $pdo->prepare($check_existing);
    $checkpdo->bindParam(':spindle_id', $data['ID'], PDO::PARAM_INT);
    $checkpdo->execute();
    $result = $checkpdo->fetch(PDO::FETCH_ASSOC);
    // Checking if spindle already exists in database and check its key if existing
    if ($result) {
       if(!password_verify($spindle_key_raw, $result['spindle_key'])) {
            echo 'Wrong iSpindel key provided';
            exit();
        }
    } else {
        // Insert the new spindle with provided key
        $spindle_key = password_hash($spindle_key_raw, PASSWORD_DEFAULT);
        $insert = "INSERT INTO spindles (spindle_id, spindle_key, alias) VALUES (:spindle_id, :spindle_key, :alias)";
        $insertpdo = $pdo->prepare($insert);
        $insertpdo->bindParam(':spindle_id', $data['ID'], PDO::PARAM_INT);
        $insertpdo->bindParam(':spindle_key', $spindle_key, PDO::PARAM_STR);
        $insertpdo->bindParam(':alias', $data['name'], PDO::PARAM_STR);
        $insertpdo->execute();
        if (isset($_SERVER['HTTPS'])) {
            echo 'New iSpindel added with key: ' . $spindle_key_raw .'<br>';
        }
        else {
            echo 'New iSpindel added with desired key<br>';
        }
        
    }

    $insert = "INSERT INTO spindle_data (name, spindle_id, angle, temperature, temp_units, battery, gravity, update_interval, rssi) 
                      VALUES (:name, :spindle_id, :angle, :temperature, :temp_units, :battery, :gravity, :update_interval, :rssi)";

    $insertpdo = $pdo->prepare($insert);
    $insertpdo->bindParam(':name', $data['name'], PDO::PARAM_STR);
    $insertpdo->bindParam(':spindle_id', $data['ID'], PDO::PARAM_INT);
    $insertpdo->bindParam(':angle', $data['angle'], PDO::PARAM_STR);
    $insertpdo->bindParam(':temperature', $data['temperature'], PDO::PARAM_STR);
    $insertpdo->bindParam(':temp_units', $data['temp_units'], PDO::PARAM_STR);
    $insertpdo->bindParam(':battery', $data['battery'], PDO::PARAM_STR);
    $insertpdo->bindParam(':gravity', $data['gravity'], PDO::PARAM_STR);
    $insertpdo->bindParam(':update_interval', $data['interval'], PDO::PARAM_INT);
    $insertpdo->bindParam(':rssi', $data['RSSI'], PDO::PARAM_INT);
    $insertpdo->execute();

    echo 'Data inserted successfully.';
} catch (PDOException $e) {
    echo 'Error in DB execution: ' . $e->getMessage();
    error_log('Error in DB execution: ' . $e->getMessage());
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    error_log('Error: "' . $e->getMessage());
}

$pdo = null;

?>