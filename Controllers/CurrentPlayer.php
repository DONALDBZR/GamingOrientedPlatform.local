<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/PlayerUnknownBattlegrounds.php";
$PlayerUnknownBattleGrounds = new PlayerUnknownBattleGrounds();
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
