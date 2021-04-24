<?php
require_once '../db/dbconnect.php';
require_once '../utility.php';



if ($_SERVER["CONTENT_TYPE"] != "application/json") {
    echo json_encode(["error" => 400, "message" => "header Content-Type should be application/json"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(["error" => 400, "message" => "access denied bad request type"]);
    exit();
}

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && utility::isValidJSON($json_params)) {
    $_POST = json_decode($json_params, true);
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    //check if we have data then register
    if (!utility::isempty($firstname, $lastname, $email, $password)) {
        echo register();
        exit();
    }
}

echo json_encode(["error" => 400, "message" => "bad register info"]);
exit();




//register function
function register()
{
    global $firstname, $lastname, $email, $password, $mysqli;

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $existence = utility::isUserExists($email);
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

            return json_encode(["code" => 500, "error" => "database error", "message" => $mysqli->error]);
        }

        return json_encode(["error" => 400, "message" => "user already exist", "data" => json_decode($existence)]);
    }

    return json_encode(["error" => 400, "message" => "bad email format"]);
}
