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
 * Checks if the table exists in the database
 * @param PDO $pdo object with database
 * @param mixed $table_name name of the table to check
 * 
 * @return true if table exists, false if not
 */
function checkDatabaseExisting(PDO $pdo, $table_name = 'spindles')
{
  try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $check_existing = "SHOW TABLES LIKE '$table_name'";
    $checkpdo = $pdo->prepare($check_existing);
    $checkpdo->execute();

    $result = $checkpdo->fetch(PDO::FETCH_ASSOC);
    if ($result) {
      return true;
    }
    return false;
  } catch (PDOException $e) {
    echo 'Error in DB execution: ' . $e->getMessage();
    error_log("Error in DB execution: " . $e->getMessage());
    return false;
  }
}


/**
 * CClears all data of a specific spindle ID
 * @param PDO $pdo object with database
 * @param mixed $spindle_id int value of spindle ID
 * 
 * @return 0 if successful, -1 if not
 */
function clearSpindleData(PDO $pdo, $spindle_id)
{
  try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $delete_data_stmt = "DELETE FROM spindle_data WHERE spindle_id = :spindle_id";
    $deletepdo = $pdo->prepare($delete_data_stmt);
    $deletepdo->bindParam(':spindle_id', $spindle_id, PDO::PARAM_INT);
    $deletepdo->execute();
    return 0;
  } catch (PDOException $e) {
    echo 'Error in DB execution: ' . $e->getMessage();
    error_log("Error in DB execution: " . $e->getMessage());
    return -1;
  }
} 


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
 * Changes the alias of an iSpindel
 * @param PDO $pdo object with database
 * @param int $spindle_id int value of spindle ID
 * @param string $new_name new alias of the spindle
 * 
 * @return 0 if successful
 */
function renameSpindle(PDO $pdo, $spindle_id, $new_name)
{
  try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $update_alias_stmt = "UPDATE spindles SET alias = :alias WHERE spindle_id = :spindle_id";
    $updatepdo = $pdo->prepare($update_alias_stmt);
    $updatepdo->bindParam(':alias', $new_name, PDO::PARAM_STR);
    $updatepdo->bindParam(':spindle_id', $spindle_id, PDO::PARAM_INT);
    $updatepdo->execute();
    return 0;
  } catch (PDOException $e) {
    echo 'Error in DB execution: ' . $e->getMessage();
    error_log("Error in DB execution: " . $e->getMessage());
    return -1;
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
function getSpindleMeasurements(PDO $pdo, $spindle_id, $time_perod = 7)
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

    $cutoff_date = date('Y-m-d', strtotime('-' . strval($time_perod) . ' days', strtotime($latest_date)));
    $sql = "SELECT angle, temperature, temp_units, battery, gravity, created_at FROM spindle_data WHERE spindle_id = :spindle_id AND created_at >= :cutoff_date ORDER BY created_at ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':spindle_id', $spindle_id, PDO::PARAM_INT);
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

    return $data_array;
  } catch (PDOException $e) {
    echo 'Error in DB execution: ' . $e->getMessage();
    error_log("Error in DB execution: " . $e->getMessage());
  }
}
