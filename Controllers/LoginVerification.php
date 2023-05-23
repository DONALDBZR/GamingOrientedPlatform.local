<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Password.php";
$Password = new Password();
$_POST[$_SERVER['REQUEST_URI']] = (object) json_decode(file_get_contents("php://input"));
$Password->otpVerify($_POST[$_SERVER['REQUEST_URI']]);
