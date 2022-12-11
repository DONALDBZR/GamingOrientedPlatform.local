<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/LeagueOfLegends.php";
$LeagueOfLegends = new LeagueOfLegends();
if (str_contains($_SERVER['HTTP_REFERER'], "Home")) {
    if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.matchHistory.json")) {
        $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.matchHistory.json"));
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    } else {
        $LeagueOfLegends->getMatchHistory($_SESSION['Account']['LeagueOfLegends']['gameName'], $_SESSION['Account']['LeagueOfLegends']['tagLine']);
    }
} else {
    if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['Search']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.matchHistory.json")) {
        $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['Search']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.matchHistory.json"));
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    } else {
        $LeagueOfLegends->getMatchHistory($_SESSION['Search']['LeagueOfLegends']['gameName'], $_SESSION['Search']['LeagueOfLegends']['tagLine']);
    }
}
