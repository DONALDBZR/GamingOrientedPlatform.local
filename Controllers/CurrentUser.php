<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
$response = array(
    "User" => $_SESSION['User'],
    "Account" => $_SESSION['Account']
);
header('Content-Type: application/json', true, 200);
echo json_encode($response);
