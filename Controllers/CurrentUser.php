<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/{$_SESSION['User']['username']}.json")) {
    $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/{$_SESSION['User']['username']}.json"));
} else {
    header('Location: /Sign-Out');
}
header('Content-Type: application/json', true, 200);
echo json_encode($response);
