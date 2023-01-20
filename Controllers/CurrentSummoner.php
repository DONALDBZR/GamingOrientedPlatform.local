<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/LeagueOfLegends.php";
$LeagueOfLegends = new LeagueOfLegends();
if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Profiles/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json")) {
    $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Profiles/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json"));
    header('Content-Type: application/json', true, 200);
    echo json_encode($response);
} else {
    $LeagueOfLegends->getSummoner(rawurlencode($_SESSION['Account']['LeagueOfLegends']['gameName']), $_SESSION['Account']['LeagueOfLegends']['tagLine']);
}
