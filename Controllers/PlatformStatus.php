<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/LeagueOfLegends.php";
$LeagueOfLegends = new LeagueOfLegends();
$tagLine = strtoupper($_SESSION['Account']['LeagueOfLegends']['tagLine']);
if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Platform/Status/{$tagLine}.json")) {
    $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Platform/Status/{$tagLine}.json"));
    if (date("Y/m/d") < $response->renewOn) {
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    } else {
        $LeagueOfLegends->getStatus($_SESSION['Account']['LeagueOfLegends']['tagLine']);
    }
} else {
    $LeagueOfLegends->getStatus($_SESSION['Account']['LeagueOfLegends']['tagLine']);
}
