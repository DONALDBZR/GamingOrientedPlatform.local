<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/PlayerUnknownBattleGrounds.php";
$PlayerUnknownBattleGrounds = new PlayerUnknownBattleGrounds();
if (!is_null($_POST[$_SERVER['REQUEST_URI']])) {
    if (!empty($_POST[$_SERVER['REQUEST_URI']]->pubgSearch)) {
        $PlayerUnknownBattleGrounds->search(rawurlencode($_POST[$_SERVER['REQUEST_URI']]->pubgSearch), $_SESSION['Account']['PlayerUnknownBattleGrounds']['platform']);
    } else {
        $response = array(
            "status" => 1,
            "url" => "/PlayerUnknownBattleGrounds/Home/" . rawurlencode($_SESSION['Account']['PlayerUnknownBattleGrounds']['username'])
        );
        header('Content-Type: application/json', true, 300);
        echo json_encode($response);
    }
} else {
    $response = array(
        "status" => 1,
        "url" => "/PlayerUnknownBattleGrounds/Home/" . rawurlencode($_SESSION['Account']['PlayerUnknownBattleGrounds']['username'])
    );
    header('Content-Type: application/json', true, 300);
    echo json_encode($response);
}
