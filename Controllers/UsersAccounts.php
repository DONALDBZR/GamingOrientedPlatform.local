<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Password.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Account.php";
$Account = new Account();
if (json_decode(file_get_contents("php://input")) != null) {
    if (!empty(json_decode(file_get_contents("php://input"))->lolUsername)) {
        $Account->add();
    } else {
        $response = array(
            "status" => 1,
            "url" => "{$Account->domain}/Users/Accounts/{$_SESSION['User']['username']}",
            "message" => "The form must be completely filled!"
        );
        header('Content-Type: application/json', true, 300);
        echo json_encode($response);
    }
} else {
    $response = array(
        "status" => 1,
        "url" => "{$Account->domain}/Users/Accounts/{$_SESSION['User']['username']}",
        "message" => "The form must be completely filled!"
    );
    header('Content-Type: application/json', true, 300);
    echo json_encode($response);
}
