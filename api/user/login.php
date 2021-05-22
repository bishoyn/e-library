<?php
header('Access-Control-Allow-Origin: *');

require '../vendor/autoload.php';

use Src\Api\User;
use Src\Utility;


Utility::checkHeaders("POST");

$_POST = Utility::getHeaderData();
$email = $_POST['email'];
$password = $_POST['password'];

//check if we have data then login
if (!utility::isempty($email, $password)) {
    echo User::login($email, $password);
    exit();
}

echo json_encode(["error" => 400, "message" => "bad login info"]);
