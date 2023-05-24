<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Environment.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/PDO.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Regions.php";
/**
 * The API which interacts with Riot Games API to take the data needed from Riot Games Data Center as well as the data model which will be used for data analysis.
 */
class LeagueOfLegends
{
    /**
     * The primary key of the player as well as identifier of the user
     * @var null|string  $playerUniversallyUniqueIdentifier
     */
    private ?string $playerUniversallyUniqueIdentifier;
    /**
     * The username of the player
     * @var string  $gameName
     */
    private string $gameName;
    /**
     * The region of the player
     * @var string  $tagLine
     */
    private string $tagLine;
    /**
     * Riot Games API Key
     * @var string  $apiKey
     */
    private string $apiKey;
    /**
     * PDO which will interact with the database server
     * @var PHPDataObject  $PDO
     */
    protected PHPDataObject $PDO;
    /**
     * Base API routing
     * @var array  $bases
     */
    private array $bases;
    /**
     * Regional API routing
     * @var array  $regions
     */
    private array $regions;
    /**
     * Client for Uniform Resource Locators
     * @var CurlHandle  $Curl
     */
    private CurlHandle $Curl;
    /**
     * Upon instantiation, its dependency is also instantiated as well as its API key is also set.
     */
    public function __construct()
    {
        $this->PDO = new PHPDataObject();
        $this->setApiKey(Environment::RiotAPIKey);
        $this->bases = Regions::baseUniformResourceLocators;
        $this->regions = Regions::regionalUniformResourceLocators;
    }
    public function getPlayerUniversallyUniqueIdentifier(): ?string
    {
        return $this->playerUniversallyUniqueIdentifier;
    }
    public function setPlayerUniversallyUniqueIdentifier(?string $player_universally_unique_identifier): void
    {
        $this->playerUniversallyUniqueIdentifier = $player_universally_unique_identifier;
    }
    public function getGameName(): string
    {
        return $this->gameName;
    }
    public function setGameName(string $game_name): void
    {
        $this->gameName = $game_name;
    }
    public function getTagLine(): string
    {
        return $this->tagLine;
    }
    public function setTagLine(string $tag_line): void
    {
        $this->tagLine = $tag_line;
    }
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
    public function setApiKey(string $api_key): void
    {
        $this->apiKey = $api_key;
    }
    /**
     * Re-routing the API to the correct route
     * @param   string  $tag_line   Regional server of the player
     * @link    https://github.com/TheDrone7/shieldbow
     * @return  string
     */
    public function getRegion(string $tag_line): string
    {
        $region = "";
        switch ($tag_line) {
            case 'na':
            case 'pbe':
            case 'br':
            case 'lan':
            case 'las':
                $region = "america";
                break;
            case 'oce':
            case 'ph':
            case 'sg':
            case 'th':
            case 'tw':
            case 'vn':
                $region = "south_east_asia";
                break;
            case 'kr':
            case 'jp':
                $region = "asia";
                break;
            case 'eune':
            case 'euw':
            case 'tr':
            case 'ru':
                $region = "europe";
                break;
        }
        return $region;
    }
    /**
     * Entry Point
     * @param   string  $tag_line   Regional server of the player
     * @link    https://github.com/TheDrone7/shieldbow
     * @return  string
     */
    public function getEntryPoint(string $tag_line): string
    {
        $entryPoint = "";
        switch ($tag_line) {
            case 'br':
            case 'eune':
            case 'euw':
            case 'jp':
            case 'lan':
            case 'na':
            case 'oce':
            case 'tr':
                $entryPoint = "{$tag_line}1";
                break;
            case 'kr':
            case 'ru':
                $entryPoint = $tag_line;
                break;
            case 'las':
            case 'ph':
            case 'sg':
            case 'th':
            case 'tw':
            case 'vn':
                $entryPoint = "{$tag_line}2";
                break;
            case 'pbe':
                $entryPoint = "na1";
        }
        return $entryPoint;
    }
    /**
     * Retrieving account's data
     * @param   string  $game_name  The username of the player
     * @param   string  $tag_line   The regional routing server of the player
     * @return  object
     */
    public function getAccount(string $game_name, string $tag_line): object
    {
        $this->setGameName($game_name);
        $this->setTagLine($tag_line);
        $request = "{$this->bases[$this->getTagLine()]}/lol/summoner/v4/summoners/by-name/{$this->getGameName()}";
        $this->Curl = curl_init();
        curl_setopt_array(
            $this->Curl,
            array(
                CURLOPT_URL => $request,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "X-Riot-Token: {$this->getApiKey()}"
                ),
            )
        );
        $riotSummonerApiResponse = json_decode(curl_exec($this->Curl));
        $riotSummonerApiResponseCode = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
        curl_close($this->Curl);
        if ($riotSummonerApiResponseCode == 200) {
            $this->setPlayerUniversallyUniqueIdentifier($riotSummonerApiResponse->puuid);
            $response = (object) array(
                "httpResponseCode" => $riotSummonerApiResponseCode,
                "playerUniversallyUniqueIdentifier" => $this->getPlayerUniversallyUniqueIdentifier(),
                "gameName" => $this->getGameName(),
                "tagLine" => $this->getTagLine()
            );
        } else {
            $response = (object) array(
                "httpResponseCode" => $riotSummonerApiResponseCode
            );
        }
        return $response;
    }
    /**
     * Accessing the summoner data
     * @param   string  $game_name  The username of the player
     * @param   string  $tag_line   The regional routing server of the player
     * @return  void
     */
    public function getSummoner(string $game_name, string $tag_line): void
    {
        $this->setGameName($game_name);
        $this->setTagLine($tag_line);
        if (str_contains($_SERVER['HTTP_REFERER'], "Home")) {
            if (isset($_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier'])) {
                $this->setPlayerUniversallyUniqueIdentifier($_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']);
            } else {
                $this->PDO->query("SELECT * FROM LeagueOfLegends WHERE LeagueOfLegendsGameName = :LeagueOfLegendsGameName AND LeagueOfLegendsTagLine = :LeagueOfLegendsTagLine");
                $this->PDO->bind(":LeagueOfLegendsGameName", $this->getGameName());
                $this->PDO->bind(":LeagueOfLegendsTagLine", $this->getTagLine());
                try {
                    $this->PDO->execute();
                    $this->setPlayerUniversallyUniqueIdentifier($this->PDO->resultSet()[0]["LeagueOfLegendsPlayerUniversallyUniqueIdentifier"]);
                } catch (PDOException $error) {
                    $response = (object) array(
                        "LeagueOfLegends" => 500,
                        "message" => $error->getMessage()
                    );
                }
            }
        } else {
            if (isset($_SESSION['Search']['LeagueOfLegends']['playerUniversallyUniqueIdentifier'])) {
                $this->setPlayerUniversallyUniqueIdentifier($_SESSION['Search']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']);
            } else {
                $this->PDO->query("SELECT * FROM LeagueOfLegends WHERE LeagueOfLegendsGameName = :LeagueOfLegendsGameName AND LeagueOfLegendsTagLine = :LeagueOfLegendsTagLine");
                $this->PDO->bind(":LeagueOfLegendsGameName", $this->getGameName());
                $this->PDO->bind(":LeagueOfLegendsTagLine", $this->getTagLine());
                try {
                    $this->PDO->execute();
                    $this->setPlayerUniversallyUniqueIdentifier($this->PDO->resultSet()[0]["LeagueOfLegendsPlayerUniversallyUniqueIdentifier"]);
                } catch (PDOException $error) {
                    $response = (object) array(
                        "LeagueOfLegends" => 500,
                        "message" => $error->getMessage()
                    );
                }
            }
        }
        $request = "{$this->bases[$this->getTagLine()]}/lol/summoner/v4/summoners/by-name/{$this->getGameName()}";
        $this->Curl = curl_init();
        curl_setopt_array(
            $this->Curl,
            array(
                CURLOPT_URL => $request,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "X-Riot-Token: {$this->getApiKey()}"
                ),
            )
        );
        $riotSummonerApiResponse = json_decode(curl_exec($this->Curl));
        $riotSummonerApiResponseCode = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
        curl_close($this->Curl);
        if ($riotSummonerApiResponseCode == 200) {
            $request = "{$this->bases[$this->getTagLine()]}/lol/league/v4/entries/by-summoner/{$riotSummonerApiResponse->id}";
            $this->Curl = curl_init();
            curl_setopt_array(
                $this->Curl,
                array(
                    CURLOPT_URL => $request,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        "X-Riot-Token: {$this->getApiKey()}"
                    ),
                )
            );
            $riotLeagueApiResponse = json_decode(curl_exec($this->Curl));
            $riotLeagueApiResponseCode = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
            curl_close($this->Curl);
            if ($riotLeagueApiResponseCode == 200) {
                if (str_contains($riotLeagueApiResponse[0]->queueType, "SOLO")) {
                    $soloDuoMatches = $riotLeagueApiResponse[0]->wins + $riotLeagueApiResponse[0]->losses;
                    $soloDuoTier = ucfirst(strtolower($riotLeagueApiResponse[0]->tier));
                    if ($soloDuoMatches == 0) {
                        $soloDuoWinRate = ($riotLeagueApiResponse[0]->wins / 1) * 100;
                    } else {
                        $soloDuoWinRate = ($riotLeagueApiResponse[0]->wins / $soloDuoMatches) * 100;
                    }
                    $soloDuoRank = $riotLeagueApiResponse[0]->rank;
                    $soloDuoLeaguePoints = $riotLeagueApiResponse[0]->leaguePoints;
                    $flexMatches = $riotLeagueApiResponse[1]->wins + $riotLeagueApiResponse[1]->losses;
                    $flexTier = ucfirst(strtolower($riotLeagueApiResponse[1]->tier));
                    if ($flexMatches == 0) {
                        $flexWinRate = ($riotLeagueApiResponse[1]->wins / 1) * 100;
                    } else {
                        $flexWinRate = ($riotLeagueApiResponse[1]->wins / $flexMatches) * 100;
                    }
                    $flexRank = $riotLeagueApiResponse[1]->rank;
                    $flexLeaguePoints = $riotLeagueApiResponse[1]->leaguePoints;
                } else {
                    $soloDuoMatches = $riotLeagueApiResponse[1]->wins + $riotLeagueApiResponse[1]->losses;
                    $soloDuoTier = ucfirst(strtolower($riotLeagueApiResponse[1]->tier));
                    if ($soloDuoMatches == 0) {
                        $soloDuoWinRate = ($riotLeagueApiResponse[1]->wins / 1) * 100;
                    } else {
                        $soloDuoWinRate = ($riotLeagueApiResponse[1]->wins / $soloDuoMatches) * 100;
                    }
                    $soloDuoRank = $riotLeagueApiResponse[1]->rank;
                    $soloDuoLeaguePoints = $riotLeagueApiResponse[1]->leaguePoints;
                    $flexMatches = $riotLeagueApiResponse[0]->wins + $riotLeagueApiResponse[0]->losses;
                    $flexTier = ucfirst(strtolower($riotLeagueApiResponse[0]->tier));
                    if ($flexMatches == 0) {
                        $flexWinRate = ($riotLeagueApiResponse[0]->wins / 1) * 100;
                    } else {
                        $flexWinRate = ($riotLeagueApiResponse[0]->wins / $flexMatches) * 100;
                    }
                    $flexRank = $riotLeagueApiResponse[0]->rank;
                    $flexLeaguePoints = $riotLeagueApiResponse[0]->leaguePoints;
                }
                $request = "{$this->regions[$this->getRegion($this->getTagLine())]}/lol/match/v5/matches/by-puuid/{$this->getPlayerUniversallyUniqueIdentifier()}/ids?start=0&count=20";
                $this->Curl = curl_init();
                curl_setopt_array(
                    $this->Curl,
                    array(
                        CURLOPT_URL => $request,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            "X-Riot-Token: {$this->getApiKey()}"
                        ),
                    )
                );
                $riotMatchApiResponse1 = json_decode(curl_exec($this->Curl));
                $riotMatchApiResponse1Code = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
                curl_close($this->Curl);
                if ($riotMatchApiResponse1Code == 200) {
                    $totalTimePlayed = 0;
                    $totalCreepScore = 0;
                    $kdaRatio = 0.0;
                    $totalVisionScore = 0;
                    for ($firstIndex = 0; $firstIndex < count($riotMatchApiResponse1); $firstIndex++) {
                        $request = "{$this->regions[$this->getRegion($this->getTagLine())]}/lol/match/v5/matches/{$riotMatchApiResponse1[$firstIndex]}";
                        $this->Curl = curl_init();
                        curl_setopt_array(
                            $this->Curl,
                            array(
                                CURLOPT_URL => $request,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'GET',
                                CURLOPT_HTTPHEADER => array(
                                    "X-Riot-Token: {$this->getApiKey()}"
                                ),
                            )
                        );
                        $riotMatchApiResponse2 = json_decode(curl_exec($this->Curl));
                        curl_close($this->Curl);
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
                        if (count($riotMatchApiResponse1) != 0) {
                            $amountOfMatches = count($riotMatchApiResponse1);
                        } else {
                            $amountOfMatches = 1;
                        }
                        if ($totalTimePlayed != 0) {
                            $totalTime = $totalTimePlayed;
                        } else {
                            $totalTime = 1;
                        }
                    }
                    $response = array(
                        "LeagueOfLegends" => 200,
                        "summoner" => $riotSummonerApiResponseCode,
                        "league" => $riotLeagueApiResponseCode,
                        "match_1" => $riotMatchApiResponse1Code,
                        "requestedDate" => date("Y/m/d H:i:s"),
                        "renewOn" => date("Y/m/d H:i:s", strtotime("+1 hours")),
                        "summonerLevel" => $riotSummonerApiResponse->summonerLevel,
                        "profileIconId" => $riotSummonerApiResponse->profileIconId,
                        "kda" => round($kdaRatio /= $amountOfMatches, 2),
                        "csMin" => round($totalCreepScore / ($totalTime /  60), 2),
                        "vsMin" => round($totalVisionScore / ($totalTime / 60), 2),
                        "SoloDuo" => (object) array(
                            "tier" => $soloDuoTier,
                            "division" => $soloDuoRank,
                            "leaguePoints" => $soloDuoLeaguePoints,
                            "winRate" => round($soloDuoWinRate, 2),
                        ),
                        "Flex5v5" => (object) array(
                            "tier" => $flexTier,
                            "division" => $flexRank,
                            "leaguePoints" => $flexLeaguePoints,
                            "winRate" => round($flexWinRate, 2),
                        ),
                    );
                    $cacheData = json_encode($response);
                    $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Profiles/{$this->getPlayerUniversallyUniqueIdentifier()}.json", "w");
                    fwrite($cache, $cacheData);
                    fclose($cache);
                } else {
                    $response = (object) array(
                        "LeagueOfLegends" => 200,
                        "summoner" => $riotSummonerApiResponseCode,
                        "league" => $riotLeagueApiResponseCode,
                        "match_1" => $riotMatchApiResponse1Code
                    );
                }
            } else {
                $response = (object) array(
                    "LeagueOfLegends" => 200,
                    "summoner" => $riotSummonerApiResponseCode,
                    "league" => $riotLeagueApiResponseCode
                );
            }
        } else {
            $response = (object) array(
                "LeagueOfLegends" => 200,
                "summoner" => $riotSummonerApiResponseCode
            );
        }
        header('Content-Type: application/json; X-XSS-Protection: 1; mode=block', true, 200);
        echo json_encode($response);
    }
    /**
     * Accessing the status of the game
     * @param   string  $tag_line   The regional routing of the server that the player uses to play the game
     * @return  void
     */
    public function getStatus(string $tag_line): void
    {
        $this->setTagLine($tag_line);
        $server = strtoupper($this->getTagLine());
        $request = "{$this->bases[$this->getTagLine()]}/lol/status/v4/platform-data";
        $this->Curl = curl_init();
        curl_setopt_array(
            $this->Curl,
            array(
                CURLOPT_URL => $request,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "X-Riot-Token: {$this->getApiKey()}"
                ),
            )
        );
        $riotStatusApiResponse = json_decode(curl_exec($this->Curl));
        $riotStatusApiResponseCode = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
        curl_close($this->Curl);
        if ($riotStatusApiResponseCode == 200) {
            $maintenances = array();
            $incidents = array();
            if (!empty($riotStatusApiResponse->maintenance)) {
                for ($index = 0; $index < count($riotStatusApiResponse->maintenance); $index++) {
                    array_push($maintenances, $riotStatusApiResponse->maintenance[$index]);
                }
            }
            if (!empty($riotStatusApiResponse->incidents)) {
                for ($firstIndex = 0; $firstIndex < count($riotStatusApiResponse->incidents); $firstIndex++) {
                    $title = "";
                    $content = "";
                    for ($secondIndex = 0; $secondIndex < count($riotStatusApiResponse->incidents[$firstIndex]->titles); $secondIndex++) {
                        if (str_contains($riotStatusApiResponse->incidents[$firstIndex]->titles[$secondIndex]->locale, "en_US")) {
                            $title = $riotStatusApiResponse->incidents[$firstIndex]->titles[$secondIndex]->content;
                        }
                    }
                    for ($secondIndex = 0; $secondIndex < count($riotStatusApiResponse->incidents[$firstIndex]->updates); $secondIndex++) {
                        for ($thirdIndex = 0; $thirdIndex < count($riotStatusApiResponse->incidents[$firstIndex]->updates[$secondIndex]->translations); $thirdIndex++) {
                            if (str_contains($riotStatusApiResponse->incidents[$firstIndex]->updates[$secondIndex]->translations[$thirdIndex]->locale, "en_US")) {
                                $content = $riotStatusApiResponse->incidents[$firstIndex]->updates[$secondIndex]->translations[$thirdIndex]->content;
                            }
                        }
                    }
                    $incident = array(
                        "title" => $title,
                        "content" => $content
                    );
                    array_push($incidents, $incident);
                }
            }
            $response = array(
                "status" => $riotStatusApiResponseCode,
                "requestedDate" => date("Y/m/d"),
                "renewOn" => date("Y/m/d", strtotime("+1 days")),
                "maintenances" => $maintenances,
                "incidents" => $incidents
            );
            $cacheData = json_encode($response);
            $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Platform/Status/{$server}.json", "w");
            fwrite($cache, $cacheData);
            fclose($cache);
        } else {
            $response = array(
                "status" => $riotStatusApiResponseCode
            );
        }
        header('Content-Type: application/json', true, $riotStatusApiResponseCode);
        echo json_encode($response);
    }
    /**
     * Accessing the match history of the player
     * @param   string  $game_name  The username of the player
     * @param   string  $tag_line   The regional routing server of the player
     * @return  void
     */
    public function getMatchHistory(string $game_name, string $tag_line): void
    {
        $this->setGameName($game_name);
        $this->setTagLine($tag_line);
        if (str_contains($_SERVER['HTTP_REFERER'], "Home")) {
            if (isset($_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier'])) {
                $this->setPlayerUniversallyUniqueIdentifier($_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']);
            } else {
                $this->PDO->query("SELECT * FROM LeagueOfLegends WHERE LeagueOfLegendsGameName = :LeagueOfLegendsGameName AND LeagueOfLegendsTagLine = :LeagueOfLegendsTagLine");
                $this->PDO->bind(":LeagueOfLegendsGameName", $this->getGameName());
                $this->PDO->bind(":LeagueOfLegendsTagLine", $this->getTagLine());
                try {
                    $this->PDO->execute();
                    $this->setPlayerUniversallyUniqueIdentifier($this->PDO->resultSet()[0]["LeagueOfLegendsPlayerUniversallyUniqueIdentifier"]);
                } catch (PDOException $error) {
                    $response = (object) array(
                        "LeagueOfLegends" => 500,
                        "message" => $error->getMessage()
                    );
                }
            }
        } else {
            if (isset($_SESSION['Search']['LeagueOfLegends']['playerUniversallyUniqueIdentifier'])) {
                $this->setPlayerUniversallyUniqueIdentifier($_SESSION['Search']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']);
            } else {
                $this->PDO->query("SELECT * FROM LeagueOfLegends WHERE LeagueOfLegendsGameName = :LeagueOfLegendsGameName AND LeagueOfLegendsTagLine = :LeagueOfLegendsTagLine");
                $this->PDO->bind(":LeagueOfLegendsGameName", $this->getGameName());
                $this->PDO->bind(":LeagueOfLegendsTagLine", $this->getTagLine());
                try {
                    $this->PDO->execute();
                    $this->setPlayerUniversallyUniqueIdentifier($this->PDO->resultSet()[0]["LeagueOfLegendsPlayerUniversallyUniqueIdentifier"]);
                } catch (PDOException $error) {
                    $response = (object) array(
                        "LeagueOfLegends" => 500,
                        "message" => $error->getMessage()
                    );
                }
            }
        }
        $request = "{$this->regions[$this->getRegion($this->getTagLine())]}/lol/match/v5/matches/by-puuid/{$this->getPlayerUniversallyUniqueIdentifier()}/ids?start=0&count=20";
        $this->Curl = curl_init();
        curl_setopt_array(
            $this->Curl,
            array(
                CURLOPT_URL => $request,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "X-Riot-Token: {$this->getApiKey()}"
                ),
            )
        );
        $riotMatchApiResponse1 = json_decode(curl_exec($this->Curl));
        $riotMatchApiResponse1Code = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
        curl_close($this->Curl);
        if ($riotMatchApiResponse1Code == 200) {
            $matchHistory = array();
            for ($firstIndex = 0; $firstIndex < count($riotMatchApiResponse1); $firstIndex++) {
                $request = "{$this->regions[$this->getRegion($this->getTagLine())]}/lol/match/v5/matches/{$riotMatchApiResponse1[$firstIndex]}";
                $this->Curl = curl_init();
                curl_setopt_array(
                    $this->Curl,
                    array(
                        CURLOPT_URL => $request,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            "X-Riot-Token: {$this->getApiKey()}"
                        ),
                    )
                );
                $riotMatchApiResponse2 = json_decode(curl_exec($this->Curl));
                curl_close($this->Curl);
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
                $match = (object) array(
                    "champion" => $riotMatchApiResponse2->info->participants[$puuidKey]->championName,
                    "kda" => round($kdaRatio, 2),
                    "creepScore" => $riotMatchApiResponse2->info->participants[$puuidKey]->neutralMinionsKilled + $riotMatchApiResponse2->info->participants[$puuidKey]->totalMinionsKilled,
                    "matchLength" => $riotMatchApiResponse2->info->gameDuration,
                    "length" => gmdate('H:i:s', $riotMatchApiResponse2->info->gameDuration),
                    "visualScore" => $riotMatchApiResponse2->info->participants[$puuidKey]->visionScore,
                    "win" => $riotMatchApiResponse2->info->participants[$puuidKey]->win,
                    "items" => array(
                        $riotMatchApiResponse2->info->participants[$puuidKey]->item0,
                        $riotMatchApiResponse2->info->participants[$puuidKey]->item1,
                        $riotMatchApiResponse2->info->participants[$puuidKey]->item2,
                        $riotMatchApiResponse2->info->participants[$puuidKey]->item3,
                        $riotMatchApiResponse2->info->participants[$puuidKey]->item4,
                        $riotMatchApiResponse2->info->participants[$puuidKey]->item5,
                        $riotMatchApiResponse2->info->participants[$puuidKey]->item6,
                    ),
                    "lane" => $riotMatchApiResponse2->info->participants[$puuidKey]->lane,
                    "kill" => $riotMatchApiResponse2->info->participants[$puuidKey]->kills,
                    "death" => $riotMatchApiResponse2->info->participants[$puuidKey]->deaths,
                    "assist" => $riotMatchApiResponse2->info->participants[$puuidKey]->assists,
                );
                array_push($matchHistory, $match);
                $response = (object) array(
                    "LeagueOfLegends" => 200,
                    "match_1" => $riotMatchApiResponse1Code,
                    "requestedDate" => date("Y/m/d H:i:s"),
                    "renewOn" => date("Y/m/d H:i:s", strtotime("+1 hours")),
                    "matchHistories" => $matchHistory
                );
                $cacheData = json_encode($response);
                $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Match Histories/{$this->getPlayerUniversallyUniqueIdentifier()}.json", "w");
                fwrite($cache, $cacheData);
                fclose($cache);
            }
        } else {
            $response = (object) array(
                "LeagueOfLegends" => 200,
                "match_1" => $riotMatchApiResponse1Code
            );
        }
        header('Content-Type: application/json; X-XSS-Protection: 1; mode=block', true, 200);
        echo json_encode($response);
    }
    /**
     * Searching for a player
     * @param   string  $game_name  The username of the player
     * @param   string  $tag_line   The regional routing server of the player
     * @return  void
     */
    public function search(string $game_name, string $tag_line): void
    {
        $PlayerData = $this->getAccount($game_name, $tag_line);
        $response = (object) array(
            "account" => $PlayerData->httpResponseCode,
            "playerUniversallyUniqueIdentifier" => $PlayerData->playerUniversallyUniqueIdentifier,
            "gameName" => $PlayerData->gameName,
            "tagLine" => $PlayerData->tagLine,
            "url" => "/LeagueOfLegends/Profile/$PlayerData->gameName"
        );
        $searchLoL = $response;
        $search = (object) array(
            "LeagueOfLegends" => $searchLoL
        );
        $session = array(
            "Client" => $_SESSION['Client'],
            "User" => $_SESSION['User'],
            "Account" => $_SESSION['Account'],
            "Search" => $search
        );
        $cacheData = json_encode($session);
        $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/{$_SESSION['User']['username']}.json", "w");
        fwrite($cache, $cacheData);
        fclose($cache);
        header('Content-Type: application/json; X-XSS-Protection: 1; mode=block', true, 200);
        echo json_encode($response);
    }
    /**
     * Deleting the data that is in the cache so that new data can be stored
     * @return  void
     */
    public function delete(): void
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
     * @param   string  $game_name  The username of the player
     * @param   string  $tag_line   The regional routing server of the player
     * @return  void
     */
    public function getChampionMastery(string $game_name, string $tag_line): void
    {
        $this->setGameName($game_name);
        $this->setTagLine($tag_line);
        if (str_contains($_SERVER['HTTP_REFERER'], "Home")) {
            if (isset($_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier'])) {
                $this->setPlayerUniversallyUniqueIdentifier($_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']);
            } else {
                $this->PDO->query("SELECT * FROM LeagueOfLegends WHERE LeagueOfLegendsGameName = :LeagueOfLegendsGameName AND LeagueOfLegendsTagLine = :LeagueOfLegendsTagLine");
                $this->PDO->bind(":LeagueOfLegendsGameName", $this->getGameName());
                $this->PDO->bind(":LeagueOfLegendsTagLine", $this->getTagLine());
                try {
                    $this->PDO->execute();
                    $this->setPlayerUniversallyUniqueIdentifier($this->PDO->resultSet()[0]["LeagueOfLegendsPlayerUniversallyUniqueIdentifier"]);
                } catch (PDOException $error) {
                    $response = (object) array(
                        "LeagueOfLegends" => 500,
                        "message" => $error->getMessage()
                    );
                }
            }
        } else {
            if (isset($_SESSION['Search']['LeagueOfLegends']['playerUniversallyUniqueIdentifier'])) {
                $this->setPlayerUniversallyUniqueIdentifier($_SESSION['Search']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']);
            } else {
                $this->PDO->query("SELECT * FROM LeagueOfLegends WHERE LeagueOfLegendsGameName = :LeagueOfLegendsGameName AND LeagueOfLegendsTagLine = :LeagueOfLegendsTagLine");
                $this->PDO->bind(":LeagueOfLegendsGameName", $this->getGameName());
                $this->PDO->bind(":LeagueOfLegendsTagLine", $this->getTagLine());
                try {
                    $this->PDO->execute();
                    $this->setPlayerUniversallyUniqueIdentifier($this->PDO->resultSet()[0]["LeagueOfLegendsPlayerUniversallyUniqueIdentifier"]);
                } catch (PDOException $error) {
                    $response = (object) array(
                        "LeagueOfLegends" => 500,
                        "message" => $error->getMessage()
                    );
                }
            }
        }
        $LeagueOfLegendsVersion = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Platform/Version.json"));
        $ddragonLeagueOfLegendsChampionsCDN = (array)json_decode(file_get_contents("http://ddragon.leagueoflegends.com/cdn/{$LeagueOfLegendsVersion->major}.{$LeagueOfLegendsVersion->minor}.{$LeagueOfLegendsVersion->patchNotes}/data/en_US/champion.json"))->data;
        $championIds = array_keys($ddragonLeagueOfLegendsChampionsCDN);
        $request = "{$this->bases[$this->getTagLine()]}/lol/summoner/v4/summoners/by-name/{$this->getGameName()}";
        $this->Curl = curl_init();
        curl_setopt_array(
            $this->Curl,
            array(
                CURLOPT_URL => $request,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "X-Riot-Token: {$this->getApiKey()}"
                ),
            )
        );
        $riotSummonerApiResponse = json_decode(curl_exec($this->Curl));
        $riotSummonerApiResponseCode = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
        curl_close($this->Curl);
        if ($riotSummonerApiResponseCode == 200) {
            $request = "{$this->bases[$this->getTagLine()]}/lol/champion-mastery/v4/champion-masteries/by-summoner/{$riotSummonerApiResponse->id}/top?count=3";
            $this->Curl = curl_init();
            curl_setopt_array(
                $this->Curl,
                array(
                    CURLOPT_URL => $request,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        "X-Riot-Token: {$this->getApiKey()}"
                    ),
                )
            );
            $riotChampionMasteryApiResponse = json_decode(curl_exec($this->Curl));
            $riotChampionMasteryApiResponseCode = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
            if ($riotChampionMasteryApiResponseCode == 200) {
                $championMastery = array();
                for ($firstIndex = 0; $firstIndex < count($riotChampionMasteryApiResponse); $firstIndex++) {
                    for ($secondIndex = 0; $secondIndex < count($championIds); $secondIndex++) {
                        if ($riotChampionMasteryApiResponse[$firstIndex]->championId == $ddragonLeagueOfLegendsChampionsCDN[$championIds[$secondIndex]]->key) {
                            $championName = $ddragonLeagueOfLegendsChampionsCDN[$championIds[$secondIndex]]->id;
                        }
                    }
                    $champion = (object) array(
                        "id" => $championName,
                        "level" => $riotChampionMasteryApiResponse[$firstIndex]->championLevel,
                        "points" => $riotChampionMasteryApiResponse[$firstIndex]->championPoints,
                    );
                    array_push($championMastery, $champion);
                }
                $response = (object) array(
                    "LeagueOfLegends" => 200,
                    "summoner" => $riotSummonerApiResponseCode,
                    "championMastery" => $riotChampionMasteryApiResponseCode,
                    "requestedDate" => date("Y/m/d H:i:s"),
                    "renewOn" => date("Y/m/d H:i:s", strtotime("+1 hours")),
                    "championMasteries" => $championMastery
                );
                $cacheData = json_encode($response);
                $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Champion Masteries/{$this->getPlayerUniversallyUniqueIdentifier()}.json", "w");
                fwrite($cache, $cacheData);
                fclose($cache);
            } else {
                $response = (object) array(
                    "LeagueOfLegends" => 200,
                    "summoner" => $riotSummonerApiResponseCode,
                    "championMastery" => $riotChampionMasteryApiResponseCode,
                );
            }
        } else {
            $response = (object) array(
                "LeagueOfLegends" => 200,
                "summoner" => $riotSummonerApiResponseCode
            );
        }
        header('Content-Type: application/json; X-XSS-Protection: 1; mode=block', true, 200);
        echo json_encode($response);
    }
    /**
     * Retrieving the patch notes of the game
     * @return  void
     */
    public function getPatchNotes(): void
    {
        $request = "https://ddragon.leagueoflegends.com/api/versions.json";
        $this->Curl = curl_init();
        curl_setopt_array(
            $this->Curl,
            array(
                CURLOPT_URL => $request,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            )
        );
        $dataDragonApiResponse = json_decode(curl_exec($this->Curl));
        $dataDragonApiResponseCode = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
        if ($dataDragonApiResponseCode == 200) {
            $latestVersion = $dataDragonApiResponse[0];
            $latestVersionArray = explode(".", $latestVersion);
            $response = (object) array(
                "httpResponseCode" => $dataDragonApiResponseCode,
                "requestedDate" => date("Y/m/d"),
                "renewOn" => date("Y/m/d", strtotime("+2 weeks")),
                "major" => (int)$latestVersionArray[0],
                "minor" => (int)$latestVersionArray[1],
                "patchNotes" => (int)$latestVersionArray[2]
            );
            $cacheData = json_encode($response);
            $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Platform/Version.json", "w");
            fwrite($cache, $cacheData);
            fclose($cache);
        } else {
            $response = (object) array(
                "httpResponseCode" => $dataDragonApiResponseCode
            );
        }
        header('Content-Type: application/json', true, $dataDragonApiResponseCode);
        echo json_encode($response);
    }
    /**
     * Adding League of Legends account in the database
     * @param   string  $game_name  The username of the player
     * @param   string  $tag_line   The regional routing server of the player
     * @return  object
     */
    public function addAccount(string $game_name, string $tagLine)
    {
        $Account = $this->getAccount(urldecode($game_name), $tagLine);
        if ($Account->httpResponseCode == 200) {
            $this->PDO->query("SELECT * FROM LeagueOfLegends WHERE LeagueOfLegendsPlayerUniversallyUniqueIdentifier = :LeagueOfLegendsPlayerUniversallyUniqueIdentifier");
            $this->PDO->bind(":LeagueOfLegendsPlayerUniversallyUniqueIdentifier", $this->getPlayerUniversallyUniqueIdentifier());
            try {
                $this->PDO->execute();
                if (empty($this->PDO->resultSet())) {
                    $this->PDO->query("INSERT INTO LeagueOfLegends(LeagueOfLegendsPlayerUniversallyUniqueIdentifier, LeagueOfLegendsGameName, LeagueOfLegendsTagLine) VALUES (:LeagueOfLegendsPlayerUniversallyUniqueIdentifier, :LeagueOfLegendsGameName, :LeagueOfLegendsTagLine)");
                    $this->PDO->bind(":LeagueOfLegendsPlayerUniversallyUniqueIdentifier", $this->getPlayerUniversallyUniqueIdentifier());
                    $this->PDO->bind(":LeagueOfLegendsGameName", $this->getGameName());
                    $this->PDO->bind(":LeagueOfLegendsTagLine", $this->getTagLine());
                    try {
                        $this->PDO->execute();
                        $leagueOfLegends = array(
                            "playerUniversallyUniqueIdentifier" => $this->getPlayerUniversallyUniqueIdentifier(),
                            "gameName" => $this->getGameName(),
                            "tagLine" => $this->getTagLine()
                        );
                        $_SESSION['LeagueOfLegends'] = $leagueOfLegends;
                        $response = (object) array(
                            "status" => 0,
                            "url" => $_SERVER["HTTP_REFERER"],
                            "RiotGamesSummonerAPI" => $Account->httpResponseCode,
                            "LeagueOfLegends" => 201
                        );
                    } catch (PDOException $error) {
                        $response = (object) array(
                            "status" => 6,
                            "url" => $_SERVER["HTTP_REFERER"],
                            "RiotGamesSummonerAPI" => $Account->httpResponseCode,
                            "LeagueOfLegends" => 500,
                            "message" => $error->getMessage()
                        );
                    }
                } else {
                    $response = (object) array(
                        "status" => 5,
                        "url" => $_SERVER["HTTP_REFERER"],
                        "RiotGamesSummonerAPI" => $Account->httpResponseCode,
                        "LeagueOfLegends" => 403,
                        "message" => "There is already an account with those details!"
                    );
                }
                return 0;
            } catch (PDOException $error) {
                $response = (object) array(
                    "status" => 4,
                    "url" => $_SERVER["HTTP_REFERER"],
                    "RiotGamesSummonerAPI" => $Account->httpResponseCode,
                    "LeagueOfLegends" => 500,
                    "message" => $error->getMessage()
                );
            }
        } else {
            $response = (object) array(
                "status" => 3,
                "url" => $_SERVER["HTTP_REFERER"],
                "RiotGamesSummonerAPI" => $Account->httpResponseCode,
                "message" => "Cannot find the account!",
                "LeagueOfLegends" => 404
            );
        }
        return $response;
    }
}
