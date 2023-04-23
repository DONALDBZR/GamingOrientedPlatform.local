<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/LeagueOfLegends.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/PlayerUnknownBattleGrounds.php";
$LeagueOfLegends = new LeagueOfLegends();
$PlayerUnknownBattleGrounds = new PlayerUnknownBattleGrounds();
if (str_contains($_SERVER['HTTP_REFERER'], "LeagueOfLegends")) {
    if (str_contains($_SERVER['HTTP_REFERER'], "Home")) {
        if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Match Histories/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json")) {
            $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Match Histories/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json"));
            if (date("Y/m/d") < $response->renewOn) {
                header('Content-Type: application/json', true, 200);
                echo json_encode($response);
            } else {
                unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Match Histories/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json");
                $LeagueOfLegends->getMatchHistory($_SESSION['Account']['LeagueOfLegends']['gameName'], $_SESSION['Account']['LeagueOfLegends']['tagLine']);
            }
        } else {
            $LeagueOfLegends->getMatchHistory($_SESSION['Account']['LeagueOfLegends']['gameName'], $_SESSION['Account']['LeagueOfLegends']['tagLine']);
        }
    } else {
        if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Match Histories/{$_SESSION['Search']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json")) {
            $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Match Histories/{$_SESSION['Search']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json"));
            if (date("Y/m/d") < $response->renewOn) {
                header('Content-Type: application/json', true, 200);
                echo json_encode($response);
            } else {
                unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Match Histories/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json");
                $LeagueOfLegends->getMatchHistory($_SESSION['Search']['LeagueOfLegends']['gameName'], $_SESSION['Search']['LeagueOfLegends']['tagLine']);
            }
        } else {
            $LeagueOfLegends->getMatchHistory($_SESSION['Search']['LeagueOfLegends']['gameName'], $_SESSION['Search']['LeagueOfLegends']['tagLine']);
        }
    }
} else {
    if (str_contains($_SERVER['HTTP_REFERER'], "Home")) {
        if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Match Histories/{$_SESSION['Account']['PlayerUnknownBattleGrounds']['identifier']}.json")) {
            $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Match Histories/{$_SESSION['Account']['PlayerUnknownBattleGrounds']['identifier']}.json"));
            if (date("Y/m/d") < $response->renewOn) {
                header('Content-Type: application/json', true, 200);
                echo json_encode($response);
            } else {
                unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Match Histories/{$_SESSION['Account']['PlayerUnknownBattleGrounds']['identifier']}.json");
                $PlayerUnknownBattleGrounds->getMatchHistory($_SESSION['Account']['PlayerUnknownBattleGrounds']['playerName'], $_SESSION['Account']['PlayerUnknownBattleGrounds']['platform']);
            }
        } else {
            $PlayerUnknownBattleGrounds->getMatchHistory($_SESSION['Account']['PlayerUnknownBattleGrounds']['playerName'], $_SESSION['Account']['PlayerUnknownBattleGrounds']['platform']);
        }
    } else {
        if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Match Histories/{$_SESSION['Search']['PlayerUnknownBattleGrounds']['identifier']}.json")) {
            $response = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Match Histories/{$_SESSION['Search']['PlayerUnknownBattleGrounds']['identifier']}.json"));
            if (date("Y/m/d") < $response->renewOn) {
                header('Content-Type: application/json', true, 200);
                echo json_encode($response);
            } else {
                unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Match Histories/{$_SESSION['Account']['PlayerUnknownBattleGrounds']['identifier']}.json");
                $PlayerUnknownBattleGrounds->getMatchHistory($_SESSION['Search']['PlayerUnknownBattleGrounds']['playerName'], $_SESSION['Search']['PlayerUnknownBattleGrounds']['platform']);
            }
        } else {
            $PlayerUnknownBattleGrounds->getMatchHistory($_SESSION['Search']['PlayerUnknownBattleGrounds']['playerName'], $_SESSION['Search']['PlayerUnknownBattleGrounds']['platform']);
        }
    }
}
