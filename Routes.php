<?php
// Importing the Router
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Router.php";
// Switch-statement to verify the request method
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Switch-statement to verify the url
        switch ($_SERVER['REQUEST_URI']) {
            case '/':
                $Router = new Router("GET", "/", "/Views/Homepage.php");
                break;
        }
        break;
}
