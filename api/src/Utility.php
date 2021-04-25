<?php

namespace Src;

header('Content-Type: application/json');



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

    public static function isValidJSON($str)
    {
        json_decode($str);
        return json_last_error() == JSON_ERROR_NONE;
    }

    public static function checkHeaders($method)
    {
        if ($_SERVER["CONTENT_TYPE"] != "application/json") {
            echo json_encode(["error" => 400, "message" => "header Content-Type should be application/json"]);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] != $method) {
            echo json_encode(["error" => 400, "message" => "access denied bad request type"]);
            exit();
        }
    }

    public static function getHeaderData()
    {
        $json_params = file_get_contents("php://input");
        if (strlen($json_params) > 0 && utility::isValidJSON($json_params)) {
            return json_decode($json_params, true);
        } else {
            echo json_encode(["error" => 400, "message" => "bad body info"]);
            exit();
        }
    }
}
