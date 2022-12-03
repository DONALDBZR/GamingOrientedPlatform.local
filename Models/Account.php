<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Environment.php";
class Account extends User
{
    private int $id;
    private string $leagueOfLegendsGameName;
    private string | array $leagueOfLegendsTagLine;
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
    public function getLeagueOfLegendsTagLine()
    {
        return $this->leagueOfLegendsTagLine;
    }
    public function setLeagueOfLegendsTagLine(string | array $league_of_legends_tag_line)
    {
        $this->leagueOfLegendsTagLine = $league_of_legends_tag_line;
    }
    public function getLeagueOfLegendsRegion()
    {
        return $this->leagueOfLegendsRegion;
    }
    public function setLeagueOfLegendsRegion(string $league_of_legends_region)
    {
        $this->leagueOfLegendsRegion = $league_of_legends_region;
    }
    public function add()
    {
        $request = json_decode(file_get_contents("php://input"));
        $this->setLeagueOfLegendsGameName($request->lolUsername);
        $this->setLeagueOfLegendsRegion($request->lolRegion);
        switch ($this->getLeagueOfLegendsRegion()) {
            case 'EUW':
                $this->setLeagueOfLegendsTagLine("#{$this->getLeagueOfLegendsRegion()}");
                break;
            case 'NA':
                $this->setLeagueOfLegendsTagLine(["#{$this->getLeagueOfLegendsRegion()}1", "#{$this->getLeagueOfLegendsRegion()}2"]);
                break;
        }
        $riotAPIRequest = json_decode(file_get_contents("https://europe.api.riotgames.com/riot/account/v1/accounts/by-riot-id/" . $this->getLeagueOfLegendsGameName() . "/" . $this->getLeagueOfLegendsTagLine() . "?api_key=" . Environment::RiotAPIKey));
        echo json_encode($riotAPIRequest);
    }
}
