<?php
header('Access-Control-Allow-Origin: *');

require '../vendor/autoload.php';

use Src\Api\User;
use Src\Utility;



utility::checkHeaders("GET");

$user_id = $_GET['id'];

//check if we have data
if (!utility::isempty($user_id)) {
    echo User::getUserBooks($user_id);
    exit();
}

echo json_encode(["error" => 400, "message" => "bad user id"]);
