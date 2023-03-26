<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/LeagueOfLegends.php";
$LeagueOfLegends = new LeagueOfLegends();
if (str_contains($_SERVER['HTTP_REFERER'], "Home")) {
    if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Champion Masteries/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json")) {
        $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Champion Masteries/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json"));
        if (date("Y/m/d") < $response->renewOn) {
            header('Content-Type: application/json', true, 200);
            echo json_encode($response);
        } else {
            unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Champion Masteries/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json");
            $LeagueOfLegends->getChampionMastery(rawurlencode($_SESSION['Account']['LeagueOfLegends']['gameName']), $_SESSION['Account']['LeagueOfLegends']['tagLine']);
        }
    } else {
        $LeagueOfLegends->getChampionMastery(rawurlencode($_SESSION['Account']['LeagueOfLegends']['gameName']), $_SESSION['Account']['LeagueOfLegends']['tagLine']);
    }
} else {
    if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Champion Masteries/{$_SESSION['Search']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json")) {
        $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Champion Masteries/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json"));
        if (date("Y/m/d") < $response->renewOn) {
            header('Content-Type: application/json', true, 200);
            echo json_encode($response);
        } else {
            unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Champion Masteries/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json");
            $LeagueOfLegends->getChampionMastery(rawurlencode($_SESSION['Account']['LeagueOfLegends']['gameName']), $_SESSION['Account']['LeagueOfLegends']['tagLine']);
        }
    } else {
        $LeagueOfLegends->getChampionMastery(rawurlencode($_SESSION['Account']['LeagueOfLegends']['gameName']), $_SESSION['Account']['LeagueOfLegends']['tagLine']);
    }
}
