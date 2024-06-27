<?php

/**
 * logout.php
 *
 * Destroys the session
 *
 * @author BP (3komma3volt)
 * @license MIT License
 */

session_start();
session_destroy();
header("location: index.php");
exit;

?>