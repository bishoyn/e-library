<?php

namespace Src\Api;

header('Content-Type: application/json');


class Book
{
    public static function getall($limit)
    {
        require 'db/dbconnect.php';

        $sql = "SELECT * FROM books";
        if ($limit != 0) $sql = $sql . " LIMIT $limit";

        $result = $mysqli->query($sql);
        $books = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                array_push($books, $row);
            }

            return html_entity_decode(json_encode(["success" => true, "books" => $books]));
        }

        return json_encode(["error" => 500, "message" => $mysqli->error]);
    }

    public static function getrating($book_id)
    {
        require 'db/dbconnect.php';

        $sql = "SELECT * FROM rating WHERE rating.book_id = '$book_id'";
        if ($limit != 0) $sql = $sql . " LIMIT $limit";

        $result = $mysqli->query($sql);
        $books = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($books, $row);
            }

            return html_entity_decode(json_encode(["success" => true, "books" => $books]));
        }

        return json_encode(["error" => 500, "message" => $mysqli->error]);
    }

    public static function getbook($book_id)
    {
        require 'db/dbconnect.php';

        $sql = "SELECT * FROM books WHERE books.id = '$book_id'";

        $result = $mysqli->query($sql);
        if ($row = $result->num_rows > 0) {
            return html_entity_decode(json_encode(["success" => true, "bookdata" => $row]));
        }

        return json_encode(["error" => 404, "message" => "book not found"]);
    }
}
