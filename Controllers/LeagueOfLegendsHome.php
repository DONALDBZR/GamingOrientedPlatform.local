<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/LeagueOfLegends.php";
$LeagueOfLegends = new LeagueOfLegends();
if (!is_null($_POST[$_SERVER['REQUEST_URI']])) {
    if (!empty($_POST[$_SERVER['REQUEST_URI']]->lolSearch)) {
        $LeagueOfLegends->search(rawurlencode($_POST[$_SERVER['REQUEST_URI']]->lolSearch), $_SESSION['Account']['LeagueOfLegends']['tagLine']);
    } else {
        $response = array(
            "status" => 1,
            "url" => "/LeagueOfLegends/Home/" . rawurlencode($_SESSION['Account']['LeagueOfLegends']['username'])
        );
        header('Content-Type: application/json', true, 300);
        echo json_encode($response);
    }
} else {
    $response = array(
        "status" => 1,
        "url" => "/LeagueOfLegends/Home/" . rawurlencode($_SESSION['Account']['LeagueOfLegends']['username'])
    );
    header('Content-Type: application/json', true, 300);
    echo json_encode($response);
}
