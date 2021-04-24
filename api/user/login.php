<?php
require_once '../db/dbconnect.php';
require_once '../utility.php';

utility::checkHeaders("POST");

$_POST = Utility::getHeaderData();
$email = $_POST['email'];
$password = $_POST['password'];

//check if we have data then login
if (!utility::isempty($email, $password)) {
    echo login();
    exit();
}

//login function
function login()
{
    global $email, $password, $mysqli;

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

        return json_encode(["error" => 401, "message" => "user not found"]);
    }

    return json_encode(["error" => 400, "message" => "bad email format"]);
}
