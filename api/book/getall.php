<?php
require '../vendor/autoload.php';

use Src\Api\Book;
use Src\Utility;


Utility::checkHeaders("GET");

$limit = $_GET['limit'] ?? 0;

echo Book::getall($limit);
exit();
