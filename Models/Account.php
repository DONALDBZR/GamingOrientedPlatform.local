<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Environment.php";
class Account extends User
{
    private int $id;
    private string $leagueOfLegendsUsername;
    private string $leagueOfLegendsGameName;
    private string $leagueOfLegendsTagLine;
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
    public function getLeagueOfLegendsUsername()
    {
        return $this->leagueOfLegendsUsername;
    }
    public function setLeagueOfLegendsUsername(string $league_of_legends_username)
    {
        $this->leagueOfLegendsUsername = $league_of_legends_username;
    }
    public function getLeagueOfLegendsGameName()
    {
        return $this->leagueOfLegendsGameName;
    }
    public function setLeagueOfLegendsGameName(string $league_of_legends_game_name)
    {
        $this->leagueOfLegendsGameName = $league_of_legends_game_name;
    }
    public function getLeagueOfLegendsTagLine()
    {
        return $this->leagueOfLegendsTagLine;
    }
    public function setLeagueOfLegendsTagLine(string $league_of_legends_tag_line)
    {
        $this->leagueOfLegendsTagLine = $league_of_legends_tag_line;
    }
    public function add()
    {
        $request = json_decode(file_get_contents("php://input"));
        $this->setLeagueOfLegendsUsername($request->lolUsername);
        $riotAPIarray = explode("#", $this->getLeagueOfLegendsUsername());
        $this->setLeagueOfLegendsGameName($riotAPIarray[0]);
        $this->setLeagueOfLegendsTagLine($riotAPIarray[1]);
        $riotAPIRequest = json_decode(file_get_contents("https://europe.api.riotgames.com/riot/account/v1/accounts/by-riot-id/" . $this->getLeagueOfLegendsGameName() . "/" . $this->getLeagueOfLegendsTagLine() . "?api_key=" . Environment::RiotAPIKey));
        echo json_encode($riotAPIRequest);
    }
}
