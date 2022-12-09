<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Routes.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/LeagueOfLegends.php";
$LeagueOfLegends = new LeagueOfLegends();
if (json_decode(file_get_contents("php://input")) != null) {
    if (!empty(json_decode(file_get_contents("php://input"))->lolSearch)) {
        $LeagueOfLegends->search(json_decode(file_get_contents("php://input"))->lolSearch, $_SESSION['Account']['LeagueOfLegends']['tagLine']);
    } else {
        $response = array(
            "status" => 1,
            "url" => "/LeagueOfLegends/Home/{$_SESSION['Account']['LeagueOfLegends']['username']}",
            "message" => "The form must be completely filled!"
        );
        header('Content-Type: application/json', true, 300);
        echo json_encode($response);
    }
} else {
    $response = array(
        "status" => 1,
        "url" => "/LeagueOfLegends/Home/{$_SESSION['Account']['LeagueOfLegends']['username']}",
        "message" => "The form must be completely filled!"
    );
    header('Content-Type: application/json', true, 300);
    echo json_encode($response);
}
