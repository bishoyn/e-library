<?php
header('Access-Control-Allow-Origin: *');

require '../vendor/autoload.php';

use Src\Api\User;
use Src\Utility;

utility::checkHeaders("POST");

$_POST = Utility::getHeaderData();
$user_id = $_POST['user_id'];
$book_id = $_POST['book_id'];

//check if we have data
if (!utility::isempty($user_id, $book_id)) {
    echo User::addUserBooks($user_id, $book_id);
    exit();
}

echo json_encode(["error" => 400, "message" => "bad user id or book id"]);
