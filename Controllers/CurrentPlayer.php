<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/PlayerUnknownBattleGrounds.php";
$PlayerUnknownBattleGrounds = new PlayerUnknownBattleGrounds();
if (str_contains($_SERVER['HTTP_REFERER'], "Home")) {
    if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Profiles/{$_SESSION['Account']['PlayerUnknownBattleGrounds']['identifier']}.json")) {
        $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Profiles/{$_SESSION['Account']['PlayerUnknownBattleGrounds']['identifier']}.json"));
        if (date("Y/m/d") < $response->renewOn) {
            header('Content-Type: application/json', true, 200);
            echo json_encode($response);
        } else {
            unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Profiles/{$_SESSION['Account']['PlayerUnknownBattleGrounds']['identifier']}.json");
            $PlayerUnknownBattleGrounds->getPlayer($_SESSION['Account']['PlayerUnknownBattleGrounds']['playerName'], $_SESSION['Account']['PlayerUnknownBattleGrounds']['platform']);
        }
    } else {
        $PlayerUnknownBattleGrounds->getPlayer($_SESSION['Account']['PlayerUnknownBattleGrounds']['playerName'], $_SESSION['Account']['PlayerUnknownBattleGrounds']['platform']);
    }
} else {
    if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Profiles/{$_SESSION['Search']['PlayerUnknownBattleGrounds']['identifier']}.json")) {
        $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Profiles/{$_SESSION['Search']['PlayerUnknownBattleGrounds']['identifier']}.json"));
        if (date("Y/m/d") < $response->renewOn) {
            header('Content-Type: application/json', true, 200);
            echo json_encode($response);
        } else {
            unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Profiles/{$_SESSION['Search']['PlayerUnknownBattleGrounds']['identifier']}.json");
            $PlayerUnknownBattleGrounds->getPlayer($_SESSION['Search']['PlayerUnknownBattleGrounds']['playerName'], $_SESSION['Search']['PlayerUnknownBattleGrounds']['platform']);
        }
    } else {
        $PlayerUnknownBattleGrounds->getPlayer($_SESSION['Search']['PlayerUnknownBattleGrounds']['playerName'], $_SESSION['Search']['PlayerUnknownBattleGrounds']['platform']);
    }
}
