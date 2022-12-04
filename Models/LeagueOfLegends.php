<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Environment.php";
class LeagueOfLegends
{
    private string | null $playerUniversallyUniqueIdentifier;
    private string $gameName;
    private string $tagLine;
    public function __construct()
    {
    }
    public function getPlayerUniversallyUniqueIdentifier()
    {
        return $this->playerUniversallyUniqueIdentifier;
    }
    public function setPlayerUniversallyUniqueIdentifier(string | null $player_universally_unique_identifier)
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
    public function retrieveData(string $game_name, string $tag_line)
    {
        $this->setGameName($game_name);
        $this->setTagLine($tag_line);
        $riotAccountApiRequest = "https://europe.api.riotgames.com/riot/account/v1/accounts/by-riot-id/" . $this->getGameName() . "/" . $this->getTagLine() . "?api_key=" . Environment::RiotAPIKey;
        if ($this->getHttpResponseCode($riotAccountApiRequest) == 200) {
            $riotAccountApiResponse = json_decode(file_get_contents($riotAccountApiRequest));
            $this->setPlayerUniversallyUniqueIdentifier($riotAccountApiResponse->puuid);
        }
        $response = array(
            "httpResponseCode" => $this->getHttpResponseCode($riotAccountApiRequest),
            "playerUniversallyUniqueIdentifier" => $this->getPlayerUniversallyUniqueIdentifier(),
            "gameName" => $this->getGameName(),
            "tagLine" => $this->getTagLine()
        );
        return json_encode($response);
    }
    public function getHttpResponseCode(string $request_uniform_resource_locator)
    {
        $headers = get_headers($request_uniform_resource_locator);
        return substr($headers[0], 9, 3);
    }
}
