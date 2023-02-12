<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Password.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Account.php";
$Account = new Account();
$Account->manage();
