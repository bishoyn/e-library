<?php
header('Access-Control-Allow-Origin: *');

require '../vendor/autoload.php';

use Src\Api\User;
use Src\Utility;

utility::checkHeaders("POST");

$_POST = Utility::getHeaderData();
$user_id = $_POST['user_id'];
$book_id = $_POST['book_id'];
$rate = $_POST['rate'];

//check if we have data
if (!utility::isempty($user_id, $book_id, $rate)) {
    echo User::rateBook($user_id, $book_id, $rate);
    exit();
}

echo json_encode(["error" => 400, "message" => "bad request info"]);
