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
            case '/Sign-Out':
                $Router = new Router("GET", "/Sign-Out", "/Views/SignOut.php");
                break;
            case '/LogOut':
                $Router = new Router("GET", "/LogOut", "/Controllers/SignOut.php");
                break;
            case '/ForgotPassword':
                $Router = new Router("GET", "/ForgotPassword", "/Views/ForgotPassword.php");
                break;
            case "/Users/Profile/{$_SESSION['User']['username']}":
                $Router = new Router("GET", "/Users/Profile/{$_SESSION['User']['username']}", "/Views/UsersProfile.php");
                break;
            case "/Users/Edit/Profile/{$_SESSION['User']['username']}":
                $Router = new Router("GET", "/Users/Edit/Profile/{$_SESSION['User']['username']}", "/Views/UsersEditProfile.php");
                break;
            case "/Users/Security/{$_SESSION['User']['username']}":
                $Router = new Router("GET", "/Users/Security/{$_SESSION['User']['username']}", "/Views/UsersSecurity.php");
                break;
            case "/Users/Accounts/{$_SESSION['User']['username']}":
                $Router = new Router("GET", "/Users/Accounts/{$_SESSION['User']['username']}", "/Views/UsersAccounts.php");
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
            case '/ForgotPassword':
                $Router = new Router("POST", "/ForgotPassword", "/Controllers/ForgotPassword.php");
                break;
            case "/Users/Edit/Profile/{$_SESSION['User']['username']}":
                $Router = new Router("POST", "/Users/Edit/Profile/{$_SESSION['User']['username']}", "/Controllers/UsersEditProfile.php");
                break;
            case "/Users/Security/{$_SESSION['User']['username']}":
                $Router = new Router("POST", "/Users/Security/{$_SESSION['User']['username']}", "/Controllers/UsersSecurity.php");
                break;
            case "/Users/Accounts/{$_SESSION['User']['username']}":
                $Router = new Router("POST", "/Users/Accounts/{$_SESSION['User']['username']}", "/Controllers/UsersAccounts.php");
                break;
        }
}
