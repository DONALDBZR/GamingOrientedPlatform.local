<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['User']['username']}.json")) {
    $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['User']['username']}.json"));
} else {
    $response = array(
        "User" => $_SESSION['User'],
        "Account" => $_SESSION['Account']
    );
}
header('Content-Type: application/json', true, 200);
echo json_encode($response);
