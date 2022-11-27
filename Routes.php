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
            case "/Login/Verification/{$_SESSION['User']['username']}":
                $Router = new Router("GET", "/Login/Verification/{$_SESSION['User']['username']}", "/Views/LoginVerification.php");
                break;
            case "/Users/Home/{$_SESSION['User']['username']}":
                $Router = new Router("GET", "/Users/Home/{$_SESSION['User']['username']}", "/Views/UsersHome.php");
                break;
            case '/Users/CurrentUser':
                $Router = new Router("GET", "/Users/CurrentUser", "/Controllers/CurrentUser.php");
                break;
        }
        break;
    case 'POST':
        switch ($_SERVER['REQUEST_URI']) {
            case '/Register':
                $Router = new Router("POST", "/Register", "/Controllers/Register.php");
                break;
            case '/Login':
                $Router = new Router("POST", "/Login", "/Controllers/Login.php");
                break;
            case "/Login/Verification/{$_SESSION['User']['username']}":
                $Router = new Router("POST", "/Login/Verification/{$_SESSION['User']['username']}", "/Controllers/LoginVerification.php");
                break;
        }
}
