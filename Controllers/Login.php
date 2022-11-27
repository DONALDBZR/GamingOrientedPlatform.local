<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Password.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
$User = new User();
if (json_decode(file_get_contents("php://input")) != null) {
    if (!empty(json_decode(file_get_contents("php://input"))->username) && !empty(json_decode(file_get_contents("php://input"))->password)) {
        $User->login();
    } else {
        $response = array(
            "status" => 1,
            "url" => $User->domain . "/Login",
            "message" => "The form must be completely filled!"
        );
        header('Content-Type: application/json', true, 300);
        echo json_encode($response);
    }
} else {
    $response = array(
        "status" => 1,
        "url" => $User->domain . "/Login",
        "message" => "The form must be completely filled!"
    );
    header('Content-Type: application/json', true, 300);
    echo json_encode($response);
}
