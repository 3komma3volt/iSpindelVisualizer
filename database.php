<?php
/**
 * database.php
 *
 * Provides connector class for database dependency injection
 *
 * @author BP (3komma3volt)
 * @license MIT License
 */

require_once ('configuration.php');

class Database {
    private $pdo;

    public function __construct() {
        try {
          $host = DATABASE_HOST;
          $dbname = DATABASE_NAME;
          $username = DATABASE_USERNAME;
          $password = DATABASE_PASSWORD;

          $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
          $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
          throw('Error connecting to database');
          error_log('Error connecting to database: ' . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>