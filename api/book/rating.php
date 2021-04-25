<?php
require '../vendor/autoload.php';

use Src\Api\Book;
use Src\Utility;

Utility::checkHeaders("GET");

$book_id = $_GET['id'];

//check if we have data
if (!utility::isempty($book_id)) {
    echo Book::getrating($book_id);
    exit();
}

echo json_encode(["error" => 400, "message" => "book id required"]);
