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
    public function getSummoner(string $game_name, string $tag_line)
    {
        if (json_decode($this->retrieveData($game_name, $tag_line))->httpResponseCode == 200) {
            $this->setGameName(json_decode($this->retrieveData($game_name, $tag_line))->gameName);
            $this->setTagLine(json_decode($this->retrieveData($game_name, $tag_line))->tagLine);
            $this->setPlayerUniversallyUniqueIdentifier(json_decode($this->retrieveData($game_name, $tag_line))->playerUniversallyUniqueIdentifier);
            $riotSummonerApiRequest = "https://{$this->getTagLine()}1.api.riotgames.com/lol/summoner/v4/summoners/by-name/{$this->getGameName()}?api_key=" . Environment::RiotAPIKey;
            if ($this->getHttpResponseCode($riotSummonerApiRequest) == 200) {
                $riotSummonerApiResponse = json_decode(file_get_contents($riotSummonerApiRequest));
                $riotLeagueApiRequest = "https://{$this->getTagLine()}1.api.riotgames.com/lol/league/v4/entries/by-summoner/$riotSummonerApiResponse->id?api_key=" . Environment::RiotAPIKey;
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
                            $riotMatchApiRequest2 = "https://europe.api.riotgames.com/lol/match/v5/matches/$riotMatchApiResponse1[$firstIndex]?api_key=" . Environment::RiotAPIKey;
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
                            "vsMin" => round($totalVisionScore / ($totalTimePlayed / 60), 2)
                        );
                        $cacheData = json_encode($response);
                        $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$this->getPlayerUniversallyUniqueIdentifier()}.json", "w");
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
                    $riotMatchApiRequest2 = "https://europe.api.riotgames.com/lol/match/v5/matches/$riotMatchApiResponse1[$firstIndex]?api_key=" . Environment::RiotAPIKey;
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
                    "httpResponseCode" => intval($this->getHttpResponseCode($riotMatchApiRequest1)),
                    "MatchHistory" => $matchHistory
                );
                $cacheData = json_encode($response);
                $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$this->getPlayerUniversallyUniqueIdentifier()}.matchHistory.json", "w");
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
}
