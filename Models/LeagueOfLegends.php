<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Environment.php";
class LeagueOfLegends
{
    private string $playerUniversallyUniqueIdentifier;
    private string $gameName;
    private string $tagLine;
    public function __construct()
    {
    }
    public function getPlayerUniversallyUniqueIdentifier()
    {
        return $this->playerUniversallyUniqueIdentifier;
    }
    public function setPlayerUniversallytUniqueIdentifier(string $player_universally_unique_identifier)
    {
        $this->playerUniversallyUniqueIdentifier = $player_universally_unique_identifier;
    }
    public function getGameName()
    {
        return $this->gameName;
    }
    public function setGameName(string $game_name)
    {
        $this->gameName = $game_name;
    }
    public function getTagLine()
    {
        return $this->tagLine;
    }
    public function setTagLine(string $tag_line)
    {
        $this->tagLine = $tag_line;
    }
    // public function retrieveData(string $game_name, string $tag_line)
    // {
    //     $this->setGameName($game_name);
    //     $this->setTagLine($tag_line);
    //     $riotAccountApiRequest = "https://europe.api.riotgames.com/riot/account/v1/accounts/by-riot-id/" . $this->getGameName() . "/" . $this->getTagLine() . "?api_key=" . Environment::RiotAPIKey;
    //     return $response;
    // }
}
