<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Router.php";
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        switch ($_SERVER['REQUEST_URI']) {
            case '/':
                $Router = new Router("GET", "/", "/Views/Homepage.php");
                break;
            case '/Login':
                $Router = new Router("GET", "/Login", "/Views/Login.php");
                break;
            case '/Register':
                $Router = new Router("GET", "/Register", "/Views/Register.php");
                break;
        }
        break;
    case 'POST':
        switch ($_SERVER['REQUEST_URI']) {
            case '/Register':
                $Router = new Router("POST", "/Register", "/Controllers/Register.php");
                break;
        }
}
