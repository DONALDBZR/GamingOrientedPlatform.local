<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/PlayerUnknownBattleGrounds.php";
$PlayerUnknownBattleGrounds = new PlayerUnknownBattleGrounds();
if (json_decode(file_get_contents("php://input")) != null) {
    if (!empty(json_decode(file_get_contents("php://input"))->pubgSearch)) {
        $PlayerUnknownBattleGrounds->search(rawurlencode(json_decode(file_get_contents("php://input"))->pubgSearch), $_SESSION['Account']['PlayerUnknownBattleGrounds']['platform']);
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
