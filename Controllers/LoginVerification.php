<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Password.php";
$Password = new Password();
$Password->otpVerify($_POST[$_SERVER['REQUEST_URI']]);
