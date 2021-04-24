<?php
require_once 'db/dbconnect.php';

class Utility
{
    public static function isempty()
    {
        foreach (func_get_args() as $arg) {
            if (empty($arg) || !isset($arg))
                return true;
        }
        return false;
    }

    public static function isUserExists($email)
    {
        global $mysqli;
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

    public static function isValidJSON($str)
    {
        json_decode($str);
        return json_last_error() == JSON_ERROR_NONE;
    }
}
