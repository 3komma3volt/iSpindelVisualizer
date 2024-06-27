<?php

/**
 * db_functions.php
 *
 * Logic for database actions
 *
 * @author BP (3komma3volt)
 * @license MIT License
 */


/**
 * Checks if the key of a specific spindle ID is correct
 * @param PDO $pdo object with database
 * @param mixed $spindle_id int value of spindle ID
 * @param mixed $spindle_key key to check
 * 
 * @return [true/false, spindle alias]
 */
function checkSpindleKey(PDO $pdo, $spindle_id, $spindle_key)
{
  try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $check_existing = "SELECT spindle_key, alias FROM spindles WHERE spindle_id = :spindle_id";
    $checkpdo = $pdo->prepare($check_existing);
    $checkpdo->bindParam(':spindle_id', $spindle_id, PDO::PARAM_INT);
    $checkpdo->execute();

    $result = $checkpdo->fetch(PDO::FETCH_ASSOC);

    if (!empty($result['spindle_key'])) {
        if (password_verify($spindle_key, $result['spindle_key'])) {
        return ['login' => true, 'alias' => $result['alias']];
      }
    }
    return ['login' => false, 'alias' => ''];
  } catch (PDOException $e) {
    echo 'Error in DB execution: ' . $e->getMessage();
    error_log("Error in DB execution: " . $e->getMessage());
  }
}


/**
 * Returns the measurement values as array
 * @param PDO $pdo object with database
 * @param mixed $spindle_id int value of spindle ID
 * @param int $time_perod get data in this period in days
 * 
 * @return [spindle data as array]
 */
function getSpindleMeasurements(PDO $pdo, $spindle_id, $time_perod = 14)
{
  try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT MAX(created_at) AS latest_date FROM spindle_data";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
  
    $latest_date_result = $stmt->fetch(PDO::FETCH_ASSOC);
  
    if (!$latest_date_result || !isset($latest_date_result['latest_date'])) {
      return []; 
    }
  
    $latest_date = $latest_date_result['latest_date'];
  
    $cutoff_date = date('Y-m-d', strtotime('-'.strval($time_perod).' days', strtotime($latest_date)));
  
    $sql = "SELECT angle, temperature, temp_units, battery, gravity, created_at FROM spindle_data WHERE created_at >= :cutoff_date";
  
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':cutoff_date', $cutoff_date, PDO::PARAM_STR);
    $stmt->execute();
    
    $data_array = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $data_array['angle'][] = $row['angle'];
      $data_array['temperature'][] = $row['temperature'];
      $data_array['temp_units'][] = $row['temp_units'];
      $data_array['battery'][] = $row['battery'];
      $data_array['gravity'][] = $row['gravity'];
      $data_array['timestamp'][] = $row['created_at'];    
    }
    if($data_array != null) {
      if ($data_array['angle'] != null) $data_array['angle'] = array_reverse($data_array['angle']);
      if ($data_array['temperature'] != null) $data_array['temperature'] = array_reverse($data_array['temperature']);      
      if ($data_array['temp_units'] != null) $data_array['temp_units'] = array_reverse($data_array['temp_units']);
      if ($data_array['battery'] != null) $data_array['battery'] = array_reverse($data_array['battery']);
      if ($data_array['gravity'] != null) $data_array['gravity'] = array_reverse($data_array['gravity']);
      if ($data_array['timestamp'] != null) $data_array['timestamp'] = array_reverse($data_array['timestamp']);
    }
    
    return $data_array;
  } catch (PDOException $e) {
    echo 'Error in DB execution: ' . $e->getMessage();
    error_log("Error in DB execution: " . $e->getMessage());
  }
}
