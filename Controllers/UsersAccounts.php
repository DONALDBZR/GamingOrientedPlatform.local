<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Password.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Account.php";
$Account = new Account();
$_POST[$_SERVER['REQUEST_URI']] = (object) json_decode(file_get_contents("php://input"));
$Account->manage($_POST[$_SERVER['REQUEST_URI']]);
