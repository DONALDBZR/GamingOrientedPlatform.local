<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/LeagueOfLegends.php";
$LeagueOfLegends = new LeagueOfLegends();
if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json")) {
    $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['User']['username']}.matchHistory.json"));
    header('Content-Type: application/json', true, 200);
    echo json_encode($response);
} else {
    $LeagueOfLegends->getMatchHistory();
}
