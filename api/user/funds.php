<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

require '../vendor/autoload.php';

use Src\Api\User;
use Src\Utility;

utility::checkHeaders("POST");

$_POST = Utility::getHeaderData();
$user_id = $_POST['user_id'];
$amount = $_POST['amount'];

//check if we have data
if (!utility::isempty($user_id, $amount)) {
    echo User::addFunds($user_id, $amount);
    exit();
}

echo json_encode(["error" => 400, "message" => "bad requst info"]);
