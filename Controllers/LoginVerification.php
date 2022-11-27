<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Password.php";
$Password = new Password();
if (json_decode(file_get_contents("php://input")) != null) {
    if (!empty(json_decode(file_get_contents("php://input"))->oneTimePpassword)) {
        $Password->otpVerify();
    } else {
        $response = array(
            "status" => 1,
            "url" => "{$Password->domain}/Login/Verification/{$_SESSION['User']['username']}",
            "message" => "The form must be completely filled!"
        );
        header('Content-Type: application/json', true, 300);
        echo json_encode($response);
    }
} else {
    $response = array(
        "status" => 1,
        "url" => "{$Password->domain}/Login/Verification/{$_SESSION['User']['username']}",
        "message" => "The form must be completely filled!"
    );
    header('Content-Type: application/json', true, 300);
    echo json_encode($response);
}
