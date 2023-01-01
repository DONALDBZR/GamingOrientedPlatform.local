<?php
// Importing Environment
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Environment.php";
/**
 * The API which interacts with Riot Games API to take the data needed from Riot Games Data Center as well as the data model which will be used for data analysis.
 */
class LeagueOfLegends
{
    /**
     * The primary key of the player as well as identifier of the user
     */
    private ?string $playerUniversallyUniqueIdentifier;
    /**
     * The username of the player
     */
    private string $gameName;
    /**
     * The region of the player
     */
    private string $tagLine;
    public function getPlayerUniversallyUniqueIdentifier()
    {
        return $this->playerUniversallyUniqueIdentifier;
    }
    public function setPlayerUniversallyUniqueIdentifier(?string $player_universally_unique_identifier)
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
    /**
     * Retrieving account's data
     * @param string $game_name
     * @param string $tag_line
     */
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
            "httpResponseCode" => intval($this->getHttpResponseCode($riotAccountApiRequest)),
            "playerUniversallyUniqueIdentifier" => $this->getPlayerUniversallyUniqueIdentifier(),
            "gameName" => $this->getGameName(),
            "tagLine" => $this->getTagLine()
        );
        return json_encode($response);
    }
    /**
     * Accessing the HTTP response code
     * @param string $request_uniform_resource_locator
     * @return string
     */
    public function getHttpResponseCode(string $request_uniform_resource_locator)
    {
        $headers = get_headers($request_uniform_resource_locator);
        return substr($headers[0], 9, 3);
    }
    /**
     * Acessing the summoner data
     * @param string $game_name
     * @param string $tag_line
     * @return JSON
     */
    public function getSummoner(string $game_name, string $tag_line)
    {
        if (json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode == 200) {
            $this->setGameName(json_decode($this->retrieveData($game_name, $tag_line))->gameName);
            $this->setTagLine(json_decode($this->retrieveData($game_name, $tag_line))->tagLine);
            $this->setPlayerUniversallyUniqueIdentifier(json_decode($this->retrieveData($game_name, $tag_line))->playerUniversallyUniqueIdentifier);
            $riotSummonerApiRequest = "https://{$this->getTagLine()}1.api.riotgames.com/lol/summoner/v4/summoners/by-name/{$this->getGameName()}?api_key=" . Environment::RiotAPIKey;
            if ($this->getHttpResponseCode($riotSummonerApiRequest) == 200) {
                $riotSummonerApiResponse = json_decode(file_get_contents($riotSummonerApiRequest));
                $riotLeagueApiRequest = "https://{$this->getTagLine()}1.api.riotgames.com/lol/league/v4/entries/by-summoner/{$riotSummonerApiResponse->id}?api_key=" . Environment::RiotAPIKey;
                if ($this->getHttpResponseCode($riotLeagueApiRequest) == 200) {
                    $riotLeagueApiResponse = json_decode(file_get_contents($riotLeagueApiRequest));
                    if (str_contains($riotLeagueApiResponse[0]->queueType, "SOLO") && str_contains($riotLeagueApiResponse[1]->queueType, "FLEX")) {
                        $soloDuoMatches = $riotLeagueApiResponse[0]->wins + $riotLeagueApiResponse[0]->losses;
                        $soloDuoTier = ucfirst(strtolower($riotLeagueApiResponse[0]->tier));
                        $soloDuoWinRate = ($riotLeagueApiResponse[0]->wins / $soloDuoMatches) * 100;
                        $soloDuoRank = $riotLeagueApiResponse[0]->rank;
                        $soloDuoLeaguePoints = $riotLeagueApiResponse[0]->leaguePoints;
                        $flexMatches = $riotLeagueApiResponse[1]->wins + $riotLeagueApiResponse[1]->losses;
                        $flexTier = ucfirst(strtolower($riotLeagueApiResponse[1]->tier));
                        $flexWinRate = ($riotLeagueApiResponse[1]->wins / $flexMatches) * 100;
                        $flexRank = $riotLeagueApiResponse[1]->rank;
                        $flexLeaguePoints = $riotLeagueApiResponse[1]->leaguePoints;
                    } else {
                        $soloDuoMatches = $riotLeagueApiResponse[1]->wins + $riotLeagueApiResponse[1]->losses;
                        $soloDuoTier = ucfirst(strtolower($riotLeagueApiResponse[1]->tier));
                        $soloDuoWinRate = ($riotLeagueApiResponse[1]->wins / $soloDuoMatches) * 100;
                        $soloDuoRank = $riotLeagueApiResponse[1]->rank;
                        $soloDuoLeaguePoints = $riotLeagueApiResponse[1]->leaguePoints;
                        $flexMatches = $riotLeagueApiResponse[0]->wins + $riotLeagueApiResponse[0]->losses;
                        $flexTier = ucfirst(strtolower($riotLeagueApiResponse[0]->tier));
                        $flexWinRate = ($riotLeagueApiResponse[0]->wins / $flexMatches) * 100;
                        $flexRank = $riotLeagueApiResponse[0]->rank;
                        $flexLeaguePoints = $riotLeagueApiResponse[0]->leaguePoints;
                    }
                    $riotMatchApiRequest1 = "https://europe.api.riotgames.com/lol/match/v5/matches/by-puuid/{$this->getPlayerUniversallyUniqueIdentifier()}/ids?start=0&count=20&api_key=" . Environment::RiotAPIKey;
                    if ($this->getHttpResponseCode($riotMatchApiRequest1) == 200) {
                        $riotMatchApiResponse1 = json_decode(file_get_contents($riotMatchApiRequest1));
                        $totalTimePlayed = 0;
                        $totalCreepScore = 0;
                        $kdaRatio = 0;
                        $totalVisionScore = 0;
                        for ($firstIndex = 0; $firstIndex < count($riotMatchApiResponse1); $firstIndex++) {
                            $riotMatchApiRequest2 = "https://europe.api.riotgames.com/lol/match/v5/matches/{$riotMatchApiResponse1[$firstIndex]}?api_key=" . Environment::RiotAPIKey;
                            $riotMatchApiResponse2 = json_decode(file_get_contents($riotMatchApiRequest2));
                            $puuidKey = 0;
                            for ($secondIndex = 0; $secondIndex < 10; $secondIndex++) {
                                if ($this->getPlayerUniversallyUniqueIdentifier() == $riotMatchApiResponse2->metadata->participants[$secondIndex]) {
                                    $puuidKey = $secondIndex;
                                }
                            }
                            if ($riotMatchApiResponse2->info->participants[$puuidKey]->deaths != 0) {
                                $kdaRatio += ($riotMatchApiResponse2->info->participants[$puuidKey]->kills + $riotMatchApiResponse2->info->participants[$puuidKey]->assists) / $riotMatchApiResponse2->info->participants[$puuidKey]->deaths;
                            } else {
                                $kdaRatio += ($riotMatchApiResponse2->info->participants[$puuidKey]->kills + $riotMatchApiResponse2->info->participants[$puuidKey]->assists) / 1;
                            }
                            $totalTimePlayed += $riotMatchApiResponse2->info->gameDuration;
                            $totalCreepScore += $riotMatchApiResponse2->info->participants[$puuidKey]->neutralMinionsKilled + $riotMatchApiResponse2->info->participants[$puuidKey]->totalMinionsKilled;
                            $totalVisionScore += $riotMatchApiResponse2->info->participants[$puuidKey]->visionScore;
                        }
                        $response = array(
                            "httpResponseCode_account" => json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode,
                            "httpResponseCode_summoner" => intval($this->getHttpResponseCode($riotSummonerApiRequest)),
                            "httpResponseCode_league" => intval($this->getHttpResponseCode($riotLeagueApiRequest)),
                            "httpResponseCode_match" => intval($this->getHttpResponseCode($riotMatchApiRequest1)),
                            "summonerLevel" => $riotSummonerApiResponse->summonerLevel,
                            "profileIconId" => $riotSummonerApiResponse->profileIconId,
                            "soloDuoTier" => $soloDuoTier,
                            "soloDuoRank" => $soloDuoRank,
                            "soloDuoLeaguePoints" => $soloDuoLeaguePoints,
                            "soloDuoWinRate" => round($soloDuoWinRate, 2),
                            "soloDuoMatches" => $soloDuoMatches,
                            "flexTier" => $flexTier,
                            "flexRank" => $flexRank,
                            "flexLeaguePoints" => $flexLeaguePoints,
                            "flexWinRate" => round($flexWinRate, 2),
                            "flexMatches" => $flexMatches,
                            "totalTimePlayed" => gmdate('H:i:s', $totalTimePlayed),
                            "kdaRatio" => round($kdaRatio /= count($riotMatchApiResponse1), 2),
                            "csMin" => round($totalCreepScore / ($totalTimePlayed /  60), 2),
                            "vsMin" => round($totalVisionScore / ($totalTimePlayed / 60), 2),
                            "gameName" => $this->getGameName()
                        );
                        $cacheData = json_encode($response);
                        $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Profiles/{$this->getPlayerUniversallyUniqueIdentifier()}.json", "w");
                        fwrite($cache, $cacheData);
                        fclose($cache);
                    } else {
                        $response = array(
                            "httpResponseCode_account" => json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode,
                            "httpResponseCode_summoner" => intval($this->getHttpResponseCode($riotSummonerApiRequest)),
                            "httpResponseCode_league" => intval($this->getHttpResponseCode($riotLeagueApiRequest)),
                            "httpResponseCode_match" => intval($this->getHttpResponseCode($riotMatchApiRequest1))
                        );
                    }
                } else {
                    $response = array(
                        "httpResponseCode_account" => json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode,
                        "httpResponseCode_summoner" => intval($this->getHttpResponseCode($riotSummonerApiRequest)),
                        "httpResponseCode_league" => intval($this->getHttpResponseCode($riotLeagueApiRequest))
                    );
                }
            } else {
                $response = array(
                    "httpResponseCode_account" => json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode,
                    "httpResponseCode_summoner" => intval($this->getHttpResponseCode($riotSummonerApiRequest))
                );
            }
        } else {
            $response = array(
                "httpResponseCode_account" => json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode
            );
        }
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
    /**
     * Accessing the match history of the player
     * @param string $game_name
     * @param string $tag_line
     * @return JSON
     */
    public function getMatchHistory(string $game_name, string $tag_line)
    {
        if (json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode == 200) {
            $this->setGameName(json_decode($this->retrieveData($game_name, $tag_line))->gameName);
            $this->setTagLine(json_decode($this->retrieveData($game_name, $tag_line))->tagLine);
            $this->setPlayerUniversallyUniqueIdentifier(json_decode($this->retrieveData($game_name, $tag_line))->playerUniversallyUniqueIdentifier);
            $riotMatchApiRequest1 = "https://europe.api.riotgames.com/lol/match/v5/matches/by-puuid/{$this->getPlayerUniversallyUniqueIdentifier()}/ids?start=0&count=20&api_key=" . Environment::RiotAPIKey;
            if ($this->getHttpResponseCode($riotMatchApiRequest1) == 200) {
                $riotMatchApiResponse1 = json_decode(file_get_contents($riotMatchApiRequest1));
                $matchHistory = array();
                for ($firstIndex = 0; $firstIndex < count($riotMatchApiResponse1); $firstIndex++) {
                    $riotMatchApiRequest2 = "https://europe.api.riotgames.com/lol/match/v5/matches/{$riotMatchApiResponse1[$firstIndex]}?api_key=" . Environment::RiotAPIKey;
                    $riotMatchApiResponse2 = json_decode(file_get_contents($riotMatchApiRequest2));
                    $puuidKey = 0;
                    for ($secondIndex = 0; $secondIndex < 10; $secondIndex++) {
                        if ($this->getPlayerUniversallyUniqueIdentifier() == $riotMatchApiResponse2->metadata->participants[$secondIndex]) {
                            $puuidKey = $secondIndex;
                        }
                    }
                    if ($riotMatchApiResponse2->info->participants[$puuidKey]->deaths != 0) {
                        $kdaRatio = ($riotMatchApiResponse2->info->participants[$puuidKey]->kills + $riotMatchApiResponse2->info->participants[$puuidKey]->assists) / $riotMatchApiResponse2->info->participants[$puuidKey]->deaths;
                    } else {
                        $kdaRatio = ($riotMatchApiResponse2->info->participants[$puuidKey]->kills + $riotMatchApiResponse2->info->participants[$puuidKey]->assists) / 1;
                    }
                    $match = array(
                        "champion" => $riotMatchApiResponse2->info->participants[$puuidKey]->championName,
                        "kda" => round($kdaRatio, 2),
                        "creepScore" => $riotMatchApiResponse2->info->participants[$puuidKey]->neutralMinionsKilled + $riotMatchApiResponse2->info->participants[$puuidKey]->totalMinionsKilled,
                        "matchLength" => $riotMatchApiResponse2->info->gameDuration,
                        "length" => gmdate('H:i:s', $riotMatchApiResponse2->info->gameDuration),
                        "visualScore" => $riotMatchApiResponse2->info->participants[$puuidKey]->visionScore,
                        "win" => $riotMatchApiResponse2->info->participants[$puuidKey]->win,
                        "item0" => $riotMatchApiResponse2->info->participants[$puuidKey]->item0,
                        "item1" => $riotMatchApiResponse2->info->participants[$puuidKey]->item1,
                        "item2" => $riotMatchApiResponse2->info->participants[$puuidKey]->item2,
                        "item3" => $riotMatchApiResponse2->info->participants[$puuidKey]->item3,
                        "item4" => $riotMatchApiResponse2->info->participants[$puuidKey]->item4,
                        "item5" => $riotMatchApiResponse2->info->participants[$puuidKey]->item5,
                        "item6" => $riotMatchApiResponse2->info->participants[$puuidKey]->item6,
                        "lane" => $riotMatchApiResponse2->info->participants[$puuidKey]->lane,
                        "kill" => $riotMatchApiResponse2->info->participants[$puuidKey]->kills,
                        "death" => $riotMatchApiResponse2->info->participants[$puuidKey]->deaths,
                        "assist" => $riotMatchApiResponse2->info->participants[$puuidKey]->assists,
                    );
                    array_push($matchHistory, $match);
                }
                $response = array(
                    "httpResponseCode_account" => json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode,
                    "httpResponseCode_match" => intval($this->getHttpResponseCode($riotMatchApiRequest1)),
                    "MatchHistory" => $matchHistory
                );
                $cacheData = json_encode($response);
                $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Match Histories/{$this->getPlayerUniversallyUniqueIdentifier()}.json", "w");
                fwrite($cache, $cacheData);
                fclose($cache);
            } else {
                $response = array(
                    "httpResponseCode_account" => json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode,
                    "httpResponseCode_match" => intval($this->getHttpResponseCode($riotMatchApiRequest1))
                );
            }
        } else {
            $response = array(
                "httpResponseCode_account" => json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode
            );
        }
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
    /**
     * Searching for a player
     * @param string $game_name
     * @param string $tag_line
     * @return JSON
     */
    public function search(string $game_name, string $tag_line)
    {
        $playerData = json_decode($this->retrieveData($game_name, $tag_line));
        $response = array(
            "httpResponseCode_account" => $playerData->httpResponseCode,
            "playerUniversallyUniqueIdentifier" => $playerData->playerUniversallyUniqueIdentifier,
            "gameName" => $playerData->gameName,
            "tagLine" => $playerData->tagLine,
            "url" => "/LeagueOfLegends/Profile/$playerData->gameName",
            "message" => "Player found!  Page loading soon...",
            "status" => 0
        );
        $search = $response;
        $_SESSION['Search']['LeagueOfLegends'] = $search;
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
    /**
     * Deleting the data that is in the cache so that new data can be stored
     * @return JSON
     */
    public function delete()
    {
        if (str_contains($_SERVER['HTTP_REFERER'], "Home")) {
            unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Profiles/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json");
            unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Match Histories/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json");
            unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Champion Masteries/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json");
        } else {
            unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Profiles/{$_SESSION['Search']['LeagueOfLegends']["playerUniversallyUniqueIdentifier"]}.json");
            unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Match Histories/{$_SESSION['Search']['LeagueOfLegends']["playerUniversallyUniqueIdentifier"]}.json");
            unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Champion Masteries/{$_SESSION['Search']['LeagueOfLegends']["playerUniversallyUniqueIdentifier"]}.json");
        }
        $response = array(
            "status" => 0,
            "url" => "{$_SERVER["HTTP_REFERER"]}"
        );
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
    /**
     * Accessing the champion mastery of the player
     * @param string $game_name
     * @param string $tag_line
     * @return JSON
     */
    public function getChampionMastery(string $game_name, string $tag_line)
    {
        if (json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode == 200) {
            $this->setGameName(json_decode($this->retrieveData($game_name, $tag_line))->gameName);
            $this->setTagLine(json_decode($this->retrieveData($game_name, $tag_line))->tagLine);
            $this->setPlayerUniversallyUniqueIdentifier(json_decode($this->retrieveData($game_name, $tag_line))->playerUniversallyUniqueIdentifier);
            $ddragonLeagueOfLegendsChampionsCDN = (array)json_decode(file_get_contents("http://ddragon.leagueoflegends.com/cdn/12.23.1/data/en_US/champion.json"))->data;
            $championIds = array_keys($ddragonLeagueOfLegendsChampionsCDN);
            $riotSummonerApiRequest = "https://{$this->getTagLine()}1.api.riotgames.com/lol/summoner/v4/summoners/by-name/{$this->getGameName()}?api_key=" . Environment::RiotAPIKey;
            if ($this->getHttpResponseCode($riotSummonerApiRequest) == 200) {
                $riotSummonerApiResponse = json_decode(file_get_contents($riotSummonerApiRequest));
                $riotChampionMasteryApiRequest = "https://{$this->getTagLine()}1.api.riotgames.com/lol/champion-mastery/v4/champion-masteries/by-summoner/{$riotSummonerApiResponse->id}/top?count=3&api_key=" . Environment::RiotAPIKey;
                if ($this->getHttpResponseCode($riotChampionMasteryApiRequest) == 200) {
                    $riotChampionMasteryApiResponse = json_decode(file_get_contents($riotChampionMasteryApiRequest));
                    $championMastery = array();
                    for ($firstIndex = 0; $firstIndex < count($riotChampionMasteryApiResponse); $firstIndex++) {
                        for ($secondIndex = 0; $secondIndex < count($championIds); $secondIndex++) {
                            if ($riotChampionMasteryApiResponse[$firstIndex]->championId == $ddragonLeagueOfLegendsChampionsCDN[$championIds[$secondIndex]]->key) {
                                $championName = $ddragonLeagueOfLegendsChampionsCDN[$championIds[$secondIndex]]->id;
                            }
                        }
                        $champion = array(
                            "championId" => $championName,
                            "championLevel" => $riotChampionMasteryApiResponse[$firstIndex]->championLevel,
                            "championPoints" => $riotChampionMasteryApiResponse[$firstIndex]->championPoints,
                        );
                        array_push($championMastery, $champion);
                    }
                    $response = array(
                        "httpResponseCode_account" => json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode,
                        "httpResponseCode_summoner" => intval($this->getHttpResponseCode($riotSummonerApiRequest)),
                        "httpResponseCode_championMastery" => intval($this->getHttpResponseCode($riotChampionMasteryApiRequest)),
                        "championMastery" => $championMastery
                    );
                    $cacheData = json_encode($response);
                    $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Champion Masteries/{$this->getPlayerUniversallyUniqueIdentifier()}.json", "w");
                    fwrite($cache, $cacheData);
                    fclose($cache);
                } else {
                    $response = array(
                        "httpResponseCode_account" => json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode,
                        "httpResponseCode_summoner" => intval($this->getHttpResponseCode($riotSummonerApiRequest)),
                        "httpResponseCode_championMastery" => intval($this->getHttpResponseCode($riotChampionMasteryApiRequest))
                    );
                }
            } else {
                $response = array(
                    "httpResponseCode_account" => json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode,
                    "httpResponseCode_summoner" => intval($this->getHttpResponseCode($riotSummonerApiRequest))
                );
            }
        } else {
            $response = array(
                "httpResponseCode_account" => json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode
            );
        }
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
}
