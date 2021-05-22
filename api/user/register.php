<?php
ini_set('display_errors', 1);
header('Access-Control-Allow-Origin: *');



require '../vendor/autoload.php';

use Src\Api\User;
use Src\Utility;


Utility::checkHeaders("POST");


$_POST = Utility::getHeaderData();
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$password = $_POST['password'];

//check if we have data then register
if (!Utility::isempty($firstname, $lastname, $email, $password)) {
    echo User::register($firstname, $lastname, $email, $password);
    exit();
}

echo json_encode(["error" => 400, "message" => "bad register info"]);
