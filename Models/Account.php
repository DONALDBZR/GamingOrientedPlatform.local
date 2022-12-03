<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Environment.php";
class Account extends User
{
    private int $id;
    private string | array $leagueOfLegendsGameName;
    private string $leagueOfLegendsRegion;
    public function __construct()
    {
        $this->PDO = new PHPDataObject();
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId(int $id)
    {
        $this->id = $id;
    }
    public function getLeagueOfLegendsGameName()
    {
        return $this->leagueOfLegendsGameName;
    }
    public function setLeagueOfLegendsGameName(string $league_of_legends_game_name)
    {
        $this->leagueOfLegendsGameName = $league_of_legends_game_name;
    }
    public function getLeagueOfLegendsRegion()
    {
        return $this->leagueOfLegendsRegion;
    }
    public function setLeagueOfLegendsRegion(string | array $league_of_legends_region)
    {
        $this->leagueOfLegendsRegion = $league_of_legends_region;
    }
    public function add()
    {
        $request = json_decode(file_get_contents("php://input"));
        $this->setLeagueOfLegendsGameName($request->lolUsername);
        $this->setLeagueOfLegendsRegion($request->lolRegion);
        $riotAccountApiRequest = "https://europe.api.riotgames.com/riot/account/v1/accounts/by-riot-id/" . $this->getLeagueOfLegendsGameName() . "/" . $this->getLeagueOfLegendsRegion() . "?api_key=" . Environment::RiotAPIKey;
        if ($this->getHttpResponseCode($riotAccountApiRequest) == 200) {
            $response = json_decode(file_get_contents($riotAccountApiRequest));
        } else {
            $response = array(
                "status" => 11,
                "url" => "{$this->domain}/Users/Accounts/{$_SESSION['User']['username']}",
                "message" => "This League of Legends Username does not exist!"
            );
        }
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
    public function getHttpResponseCode(string $requestUniformResourceLocator)
    {
        $headers = get_headers($requestUniformResourceLocator);
        return substr($headers[0], 9, 3);
    }
}
