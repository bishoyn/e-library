<?php
require_once 'server.php';

$mysqli = new mysqli(Server::$servername, Server::$username, Server::$password, Server::$dbname);
// Check connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error . "\n";
    exit();
}
