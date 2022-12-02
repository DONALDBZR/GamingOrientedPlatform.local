<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
class Account extends User
{
    private int $id;
    private string $leagueOfLegendsInGameName;
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
    public function getLeagueOfLegendsInGameName()
    {
        return $this->leagueOfLegendsInGameName;
    }
    public function setLeagueOfLegendsInGameName(int $league_of_legends_in_game_name)
    {
        $this->leagueOfLegendsInGameName = $league_of_legends_in_game_name;
    }
}
