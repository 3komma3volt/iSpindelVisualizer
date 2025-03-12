<?PHP

/**
 * install.php
 *
 * Helper for installing the databse
 *
 * @author BP (3komma3volt)
 * @license MIT License
 */


include('db_functions.php');
require_once('database.php');

echo 'Connecting to database<br>';

$database = new Database();
$pdo = $database->getConnection();

echo 'Checking for existing tables or creating tables<br>';

if(!checkDatabaseExisting($pdo, 'spindles')) {
  $create_table = "CREATE TABLE spindles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    spindle_id INT NOT NULL UNIQUE,
    spindle_key VARCHAR(255) NOT NULL,
    alias VARCHAR(255) NOT NULL
  )";

  try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec($create_table);
    echo "Table spindles created successfully<br>";
  } catch (PDOException $e) {
    echo 'Error creating table: ' . $e->getMessage();
    error_log("Error creating table: " . $e->getMessage());
  }
} else {
echo "Table spindles already existsy<br>";
}


if(!checkDatabaseExisting($pdo, 'spindle_data')) {
  $create_table = "CREATE TABLE spindle_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    spindle_id INT NOT NULL,
    angle FLOAT NOT NULL,
    temperature FLOAT NOT NULL,
    temp_units CHAR(1) NOT NULL,
    battery FLOAT NOT NULL,
    gravity FLOAT NOT NULL,
    update_interval INT NOT NULL,
    rssi INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  )";

  try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec($create_table);
    echo "Table spindle_data created successfully<br>";
  } catch (PDOException $e) {
    echo 'Error creating table: ' . $e->getMessage();
    error_log("Error creating table: " . $e->getMessage());
  }
} else {
  echo "Table spindle_data already exists<br>";
}

echo 'Database installation complete<br>';
?>