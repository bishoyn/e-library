<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

require '../vendor/autoload.php';

use Src\Api\User;
use Src\Utility;

utility::checkHeaders("POST");

$_POST = Utility::getHeaderData();
$user_id = $_POST['user_id'];
$books = $_POST['books'];

//check if we have data
if (!utility::isempty($user_id, $books)) {
    echo User::addUserBooks($user_id, $books);
    exit();
}

echo json_encode(["error" => 400, "message" => "bad user id or books data"]);
