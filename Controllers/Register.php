<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Password.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
$request = json_decode(file_get_contents("php://input"));
echo "TEST: <br />";
var_dump($request);
// $User = new User();
// $User->register();
