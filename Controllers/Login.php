<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Password.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
$request = (object) json_decode(file_get_contents("php://input"));
$User = new User();
$User->login($request);
