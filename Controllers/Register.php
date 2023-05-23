<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Password.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
$User = new User();
$_POST[$_SERVER['REQUEST_URI']] = (object) json_decode(file_get_contents("php://input"));
$User->register($_POST[$_SERVER['REQUEST_URI']]);
