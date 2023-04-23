<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/LeagueOfLegends.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/PlayerUnknownBattlegrounds.php";
$PlayerUnknownBattleGrounds = new PlayerUnknownBattleGrounds();
$LeagueOfLegends = new LeagueOfLegends();
if (str_contains($_SERVER['REQUEST_URI'], "LeagueOfLegends")) {
    if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Platform/Version.json")) {
        $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Platform/Version.json"));
        if (date("Y/m/d") < $response->renewOn) {
            header('Content-Type: application/json', true, 200);
            echo json_encode($response);
        } else {
            unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Platform/Version.json");
            $LeagueOfLegends->getPatchNotes();
        }
    } else {
        $LeagueOfLegends->getPatchNotes();
    }
} else {
    if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Platform/Version.json")) {
        $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Platform/Version.json"));
        if (date("Y/m/d") < $response->renewOn) {
            header('Content-Type: application/json', true, 200);
            echo json_encode($response);
        } else {
            unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Platform/Version.json");
            $PlayerUnknownBattleGrounds->getPatchNotes();
        }
    } else {
        $PlayerUnknownBattleGrounds->getPatchNotes();
    }
}
