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
    public function getSummoner()
    {
        $this->setGameName($_SESSION['Account']['LeagueOfLegends']['gameName']);
        $this->setTagLine($_SESSION['Account']['LeagueOfLegends']['tagLine']);
        $this->setPlayerUniversallyUniqueIdentifier($_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']);
        $riotSummonerApiRequest = "https://" . $this->getTagLine() .  "1.api.riotgames.com/lol/summoner/v4/summoners/by-name/" . $this->getGameName() . "?api_key=" . Environment::RiotAPIKey;
        if ($this->getHttpResponseCode($riotSummonerApiRequest) == 200) {
            $riotSummonerApiResponse = json_decode(file_get_contents($riotSummonerApiRequest));
            $riotLeagueApiRequest = "https://" . $this->getTagLine() .  "1.api.riotgames.com/lol/league/v4/entries/by-summoner/" . $riotSummonerApiResponse->id . "?api_key=" . Environment::RiotAPIKey;
            if ($this->getHttpResponseCode($riotLeagueApiRequest) == 200) {
                $riotLeagueApiResponse = json_decode(file_get_contents($riotLeagueApiRequest));
                $soloDuoMatches = $riotLeagueApiResponse[0]->wins + $riotLeagueApiResponse[0]->losses;
                $soloDuoWinRate = ($riotLeagueApiResponse[0]->wins / $soloDuoMatches) * 100;
                $flexMatches = $riotLeagueApiResponse[1]->wins + $riotLeagueApiResponse[1]->losses;
                $flexWinRate = ($riotLeagueApiResponse[1]->wins / $flexMatches) * 100;
                $riotMatchApiRequest = "https://europe.api.riotgames.com/lol/match/v5/matches/by-puuid/" . $this->getPlayerUniversallyUniqueIdentifier() . "/ids?start=0&count=100&api_key=" . Environment::RiotAPIKey;
                if ($this->getHttpResponseCode($riotMatchApiRequest)) {
                    $riotMatchApiResponse = json_decode(file_get_contents($riotMatchApiRequest));
                    $response = array(
                        "httpResponseCode" => intval($this->getHttpResponseCode($riotSummonerApiRequest)),
                        "httpResponseCode" => intval($this->getHttpResponseCode($riotLeagueApiRequest)),
                        "httpResponseCode" => intval($this->getHttpResponseCode($riotMatchApiRequest)),
                        "summonerLevel" => $riotSummonerApiResponse->summonerLevel,
                        "profileIconId" => $riotSummonerApiResponse->profileIconId,
                        "soloDuoTier" => ucfirst(strtolower($riotLeagueApiResponse[0]->tier)),
                        "soloDuoRank" => $riotLeagueApiResponse[0]->rank,
                        "soloDuoLeaguePoints" => $riotLeagueApiResponse[0]->leaguePoints,
                        "soloDuoWinRate" => round($soloDuoWinRate, 2),
                        "soloDuoMatches" => $soloDuoMatches,
                        "flexTier" => ucfirst(strtolower($riotLeagueApiResponse[1]->tier)),
                        "flexRank" => $riotLeagueApiResponse[1]->rank,
                        "flexLeaguePoints" => $riotLeagueApiResponse[1]->leaguePoints,
                        "flexWinRate" => round($flexWinRate, 2),
                        "flexMatches" => $flexMatches,
                        "matches" => count($riotMatchApiResponse)
                    );
                } else {
                    $response = array(
                        "httpResponseCode" => intval($this->getHttpResponseCode($riotSummonerApiRequest)),
                        "httpResponseCode" => intval($this->getHttpResponseCode($riotLeagueApiRequest)),
                        "httpResponseCode" => intval($this->getHttpResponseCode($riotMatchApiRequest))
                    );
                }
            } else {
                $response = array(
                    "httpResponseCode" => intval($this->getHttpResponseCode($riotSummonerApiRequest)),
                    "httpResponseCode" => intval($this->getHttpResponseCode($riotLeagueApiRequest))
                );
            }
        } else {
            $response = array(
                "httpResponseCode" => intval($this->getHttpResponseCode($riotSummonerApiRequest))
            );
        }
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
}
