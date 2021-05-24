<?php

namespace Src\Api;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

class User
{

    private static function isUserExists($email)
    {
        require 'db/dbconnect.php';

        $sql = "SELECT * FROM users where email = '$email'";
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                return json_encode(["id" => $row["id"], "email" => $row["email"]]);
            }
        } else {
            return false;
        }

        return false;
    }

    private static function isUserHasBook($user_id, $book_id)
    {
        require 'db/dbconnect.php';


        //check if the user alreay has the book in his library
        $sql = "SELECT * FROM user_books where user_books.user_id = $user_id AND user_books.book_id = '$book_id'";
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {

            return true;
        }

        return false;
    }

    //register function
    public static function register($firstname, $lastname, $email, $password)
    {
        require 'db/dbconnect.php';

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $existence = self::isUserExists($email);
            if (!$existence) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (email, password)  VALUES ('$email', '$hashed_password')";
                $reg_result = $mysqli->query($sql);
                if ($reg_result === true) {
                    $user_id = $mysqli->insert_id;
                    $sql = "INSERT INTO user_data (user_id, first_name, last_name, balance)  VALUES ('$user_id', '$firstname', '$lastname', 0.00)";
                    $data_result = $mysqli->query($sql);
                    if ($data_result === true) {
                        return json_encode(["success" => true, "userdata" => [
                            "id" => $user_id,
                            "email" => $email,
                            "firstname" => $firstname,
                            "lastname" => $lastname,
                            "balance" => 0.00,
                        ]]);
                    }
                }

                return json_encode(["error" => 500, "message" => $mysqli->error]);
            }

            return json_encode(["error" => 400, "message" => "user already exist", "data" => json_decode($existence)]);
        }

        return json_encode(["error" => 401, "message" => "bad email format"]);
    }

    //login function
    public static function login($email, $password)
    {
        require 'db/dbconnect.php';

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $sql = "SELECT * FROM users INNER JOIN user_data ON users.id = user_data.user_id WHERE users.email = '$email' ";

            $result = $mysqli->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if (password_verify($password, $row["password"])) {
                        unset($row["password"]);
                        unset($row["user_id"]);
                        return json_encode(["success" => true, "userdata" => $row]);
                    }

                    return json_encode(["error" => 401, "message" => "invalid password"]);
                }
            }

            return json_encode(["error" => 404, "message" => "user not found"]);
        }

        return json_encode(["error" => 400, "message" => "bad email format"]);
    }

    //register a book to a user
    public static function addUserBooks($user_id, $book_id)
    {
        require 'db/dbconnect.php';

        if (self::isUserHasBook($user_id, $book_id)) {
            return json_encode(["error" => 422, "message" => "user already has this book in his library"]);
        }

        //get booke and user balance
        $sql = "SELECT books.price, user_data.balance FROM books, user_data WHERE user_data.user_id = $user_id AND books.id = '$book_id'";
        $balance_result = $mysqli->query($sql);
        if ($balance_result->num_rows > 0) {
            while ($row = $balance_result->fetch_assoc()) {
                $user_balance = $row['balance'];
                $book_price = $row['price'];

                //check if user have enough balance
                if ($user_balance >= $book_price) {
                    //register this book to the user
                    $new_user_balance = $user_balance - $book_price;
                    $sql = "INSERT INTO user_books (user_id, book_id) VALUES ($user_id, '$book_id');
                            UPDATE user_data SET balance = $new_user_balance WHERE user_id = $user_id";
                    //$purchase_result = $mysqli->query($sql);
                    $purchase_result = mysqli_multi_query($mysqli, $sql);

                    if ($purchase_result === true) {
                        return json_encode(["success" => true, "message" => "user successfully purchased book: $book_id"]);
                    }

                    return json_encode(["error" => 500, "message" => $mysqli->error]);
                }

                return json_encode(["error" => 422, "message" => "user doesn't have enough balance"]);
            }
        }

        return json_encode(["error" => 401, "message" => "user doesn't exist or book doesn't exist"]);
    }


    public static function addFunds($user_id, $amount)
    {
        require 'db/dbconnect.php';

        if (floatval($amount) <= 0) return json_encode(["error" => 422, "message" => "minimum amount of funds is $1"]);

        //get current balance
        $sql = "SELECT user_data.balance FROM user_data WHERE user_data.user_id = $user_id";
        $balance_result = $mysqli->query($sql);
        if ($balance_result->num_rows > 0) {
            while ($row = $balance_result->fetch_assoc()) {
                $user_balance = $row['balance'];
                $new_balance = floatval($user_balance) + floatval($amount);
                $sql = "UPDATE user_data SET balance = $new_balance WHERE user_id = $user_id";
                $result = $mysqli->query($sql);
                if ($result === true) {
                    return json_encode(["success" => true, "message" => "funds added successfully"]);
                }

                return json_encode(["error" => 500, "message" => $mysqli->error]);
            }
        }

        return json_encode(["error" => 404, "message" => "user doesn't exist"]);
    }


    //get user books function
    public static function getUserBooks($user_id)
    {
        require 'db/dbconnect.php';

        $sql = "SELECT * FROM user_books INNER JOIN books ON user_books.book_id = books.id WHERE user_books.user_id = $user_id";

        $result = $mysqli->query($sql);
        $books = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                unset($row["user_id"]);
                unset($row["id"]);

                array_push($books, $row);
            }

            return html_entity_decode(json_encode(["success" => true, "user_id" => $user_id, "books" => $books]));
        }

        return json_encode(["error" => 404, "message" => "no books found"]);
    }

    //add book rate
    public static function rateBook($user_id, $book_id, $rate)
    {
        require 'db/dbconnect.php';

        if ($rate <= 0 || $rate > 5) {
            return json_encode(["error" => 422, "message" => "rate must be between 1-5"]);
        }

        if (!self::isUserHasBook($user_id, $book_id)) {
            return json_encode(["error" => 422, "message" => "user doesn't have this book in his library"]);
        }

        //check if the user alreay has rated this book
        $sql = "SELECT * FROM rating where rating.user_id = $user_id AND rating.book_id = '$book_id'";
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            return json_encode(["error" => 422, "message" => "user already rated this book"]);
        }

        //rate the book
        $sql = "INSERT INTO rating (user_id, book_id, rate) VALUES ($user_id, '$book_id', $rate)";
        $rate_result = $mysqli->query($sql);

        if ($rate_result === true) {
            return json_encode(["success" => true, "message" => "user successfully rated book: $book_id"]);
        }

        return json_encode(["error" => 500, "message" => $mysqli->error]);

        return json_encode(["error" => 401, "message" => "user doesn't exist or book doesn't exist"]);
    }
}
