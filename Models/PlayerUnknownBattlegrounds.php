<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Environment.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/PDO.php";
/**
 * The API which interacts with PUBG API to take the data needed from PUBG Data Center as well as the data model which will be used for data analysis.
 */
class PlayerUnknownBattleGrounds
{
    /**
     * The primary key of the player as well as the identifier of the player
     */
    private ?string $identifier;
    /**
     * The username of the player
     */
    private string $playerName;
    /**
     * The platform of the player
     */
    private string $platform;
    /**
     * PUBG API Key
     */
    private string $apiKey;
    /**
     * PDO which will interact with the database server
     */
    protected PHPDataObject $PDO;
    /**
     * Client for Uniform Resource Locators
     */
    private CurlHandle $Curl;
    /**
     * Hypertext Markup Language document parser
     */
    private DOMDocument $DOM;
    /**
     * PHP DOM selector
     */
    private DOMXPath $DOMXPath;
    /**
     * Upon instantiation, its dependencies are also instantiated
     */
    public function __construct()
    {
        $this->setApiKey(Environment::PubgAPIKey);
        $this->PDO = new PHPDataObject();
        $this->DOM = new DOMDocument();
    }
    public function getPlayerName(): string
    {
        return $this->playerName;
    }
    public function setPlayerName(string $player_name): void
    {
        $this->playerName = $player_name;
    }
    public function getPlatform(): string
    {
        return $this->platform;
    }
    public function setPlatform(string $platform): void
    {
        $this->platform = $platform;
    }
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }
    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
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
     * Retrieving accounts's data
     * @param   string  $player_name    Name of the player
     * @param   string  $platform       Platform at which the player plays the game
     * @return  object
     */
    public function getAccount(string $player_name, string $platform): object
    {
        $this->setPlayerName($player_name);
        $this->setPlatform($platform);
        $request = "https://api.pubg.com/shards/{$this->getPlatform()}/players?filter[playerNames]={$this->getPlayerName()}";
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
                    'Accept: application/vnd.api+json',
                    "Authorization: Bearer {$this->getApiKey()}"
                )
            )
        );
        $pubgAccountApiResponse = json_decode(curl_exec($this->Curl));
        $pubgAccountApiResponseCode = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
        curl_close($this->Curl);
        if ($pubgAccountApiResponseCode == 200) {
            $this->setIdentifier($pubgAccountApiResponse->data[0]->id);
            $response = (object) array(
                "account" => $pubgAccountApiResponseCode,
                "identifier" => $this->getIdentifier(),
                "playerName" => $this->getPlayerName(),
                "platform" => $this->getPlatform()
            );
        } else {
            $response = (object) array(
                "account" => $pubgAccountApiResponseCode
            );
        }
        return $response;
    }
    /**
     * Adding Player Unknown Battle Grounds account in the database
     * @param   string  $player_name    Name of the player
     * @param   string  $platform       Platform which the player uses to play the game
     * @return  int
     */
    public function addAccount(string $player_name, string $platform): int
    {
        $Account = $this->getAccount($player_name, $platform);
        if ($Account->account == 200) {
            $this->PDO->query("SELECT * FROM PlayerUnknownBattleGrounds WHERE PlayerUnknownBattleGroundsIdentifier = :PlayerUnknownBattleGroundsIdentifier");
            $this->PDO->bind(":PlayerUnknownBattleGroundsIdentifier", $this->getIdentifier());
            $this->PDO->execute();
            if (empty($this->PDO->resultSet())) {
                $this->PDO->query("INSERT INTO PlayerUnknownBattleGrounds (PlayerUnknownBattleGroundsIdentifier, PlayerUnknownBattleGroundsPlayerName, PlayerUnknownBattleGroundsPlatform) VALUES (:PlayerUnknownBattleGroundsIdentifier, :PlayerUnknownBattleGroundsPlayerName, :PlayerUnknownBattleGroundsPlatform)");
                $this->PDO->bind(":PlayerUnknownBattleGroundsIdentifier", $this->getIdentifier());
                $this->PDO->bind(":PlayerUnknownBattleGroundsPlayerName", $this->getPlayerName());
                $this->PDO->bind(":PlayerUnknownBattleGroundsPlatform", $this->getPlatform());
                $this->PDO->execute();
                $playerUnknownBattleGrounds = array(
                    "identifier" => $this->getIdentifier(),
                    "playerName" => $this->getPlayerName(),
                    "platform" => $this->getPlatform()
                );
                $_SESSION['PlayerUnknownBattleGrounds'] = $playerUnknownBattleGrounds;
            }
            $this->PDO->query("SELECT * FROM PlayerUnknownBattleGrounds");
            $this->PDO->execute();
            return 0;
        } else {
            return 1;
        }
    }
    /**
     * Accessing player data
     * @param   string  $player_name    Name of the player
     * @param   string  $platform       Platform that the player uses to play the game
     * @return  void
     */
    public function getPlayer(string $player_name, string $platform): void
    {
        $Account = $this->getAccount($player_name, $platform);
        if ($Account->account == 200) {
            $request = "https://api.pubg.com/shards/{$this->getPlatform()}/players/{$this->getIdentifier()}/seasons/lifetime";
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
                        'Accept: application/vnd.api+json',
                        "Authorization: Bearer {$this->getApiKey()}"
                    )
                )
            );
            $pubgLifetimeStatsApiResponse = json_decode(curl_exec($this->Curl));
            $pubgLifetimeStatsApiResponseCode = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
            curl_close($this->Curl);
            if ($pubgLifetimeStatsApiResponseCode == 200) {
                $kda = 0.0;
                $headshot = 0.0;
                $damagePerMatch = 0.0;
                $kdas = array(
                    (($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->kills + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->assists) / $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->losses),
                    (($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->kills + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->assists) / $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->losses),
                    (($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->kills + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->assists) / $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->losses)
                );
                $killStreaks = array(
                    $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->maxKillStreaks,
                    $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->maxKillStreaks,
                    $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->maxKillStreaks
                );
                $longestKills = array(
                    $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->longestKill,
                    $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->longestKill,
                    $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->longestKill
                );
                $headshots = array(
                    (($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->headshotKills / $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->kills) * 100),
                    (($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->headshotKills / $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->kills) * 100),
                    (($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->headshotKills / $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->kills) * 100)
                );
                $damagePerMatchs = array(
                    ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->damageDealt / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->losses)),
                    ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->damageDealt / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->losses)),
                    ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->damageDealt / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->losses))
                );
                $lengths = array(
                    count($kdas),
                    count($headshots),
                    count($damagePerMatchs)
                );
                for ($index = 0; $index < max($lengths); $index++) {
                    $kda += $kdas[$index];
                    $headshot += $headshots[$index];
                    $damagePerMatch += $damagePerMatchs[$index];
                }
                $duo = (object) array(
                    "winrate" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->wins / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->losses + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->top10s)) * 100), 2),
                    "top10Probability" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->top10s / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->losses + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->top10s)) * 100), 2)
                );
                $solo = (object) array(
                    "winrate" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->wins / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->losses + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->top10s)) * 100), 2),
                    "top10Probability" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->top10s / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->losses + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->top10s)) * 100), 2)
                );
                $squad = (object) array(
                    "winrate" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->wins / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->losses + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->top10s)) * 100), 2),
                    "top10Probability" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->top10s / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->losses + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->top10s)) * 100), 2)
                );
                $response = array(
                    "account" => $Account->account,
                    "lifetime" => $pubgLifetimeStatsApiResponseCode,
                    "requestedDate" => date("Y/m/d H:i:s"),
                    "renewOn" => date("Y/m/d H:i:s", strtotime("+1 hours")),
                    "kda" => round(($kda / max($lengths)), 2),
                    "killStreak" => max($killStreaks),
                    "longestKill" => round(max($longestKills), 2),
                    "headshot" => round(($headshot / max($lengths)), 2),
                    "damagePerMatch" => round(($damagePerMatch / max($lengths)), 2),
                    "duo" => $duo,
                    "solo" => $solo,
                    "squad" => $squad
                );
                $cacheData = json_encode($response);
                $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Profiles/{$this->getIdentifier()}.json", "w");
                fwrite($cache, $cacheData);
                fclose($cache);
            } else {
                $response = array(
                    "account" => $Account->account,
                    "lifetime" => $pubgLifetimeStatsApiResponseCode
                );
            }
        } else {
            $response = array(
                "account" => $Account->account
            );
        }
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
    /**
     * Retrieving the patch notes of the game
     * @return  void
     */
    public function getPatchNotes(): void
    {
        $request = "https://na.battlegrounds.pubg.com/patch-notes/";
        $patches = array();
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
                CURLOPT_CUSTOMREQUEST => 'GET'
            )
        );
        $page = curl_exec($this->Curl);
        $pageResponseCode = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
        curl_close($this->Curl);
        if ($pageResponseCode == 200) {
            $this->DOM->loadHTML($page);
            $this->DOMXPath = new DOMXPath($this->DOM);
            $patchDetails1 = $this->DOMXPath->query("//section[@id='news-list']//div[@class='top-section']//div[@class='news-list-first-column']//div[@class='content']//a[@class='title']//h2");
            $patchDetails2 = $this->DOMXPath->query("//section[@id='news-list']//div[@class='top-section']//div[@class='news-list-second-column']//div[@class='content']//a[@class='title']//h2");
            foreach ($patchDetails1 as $node) {
                array_push($patches, $node->nodeValue);
            }
            foreach ($patchDetails2 as $node) {
                array_push($patches, $node->nodeValue);
            }
            for ($index = 0; $index < count($patches); $index++) {
                $version = "";
                if (str_contains($patches[$index], "Patch Notes - Update ")) {
                    $version = str_replace("Patch Notes - Update ", "", $patches[$index]);
                } else if (str_contains($patches[$index], "Patch Notes â Update ")) {
                    $version = str_replace("Patch Notes â Update ", "", $patches[$index]);
                }
                $patches[$index] = $version;
            }
            rsort($patches);
            $latestVersion = $patches[0];
            $latestVersionArray = explode(".", $latestVersion);
            $response = (object) array(
                "patchNotes" => $pageResponseCode,
                "requestedDate" => date("Y/m/d"),
                "renewOn" => date("Y/m/d", strtotime("+2 weeks")),
                "major" => (int)$latestVersionArray[0],
                "minor" => (int)$latestVersionArray[1]
            );
            $cacheData = json_encode($response);
            $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Platform/Version.json", "w");
            fwrite($cache, $cacheData);
            fclose($cache);
        } else {
            $response = (object) array(
                "patchNotes" => $pageResponseCode
            );
        }
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
    /**
     * Retrieving player's current season stats
     * @param   string  $player_name    Name of the player
     * @param   string  $platform       Platform that the player uses to play the game
     * @return  void
     */
    public function getSeason(string $player_name, string $platform): void
    {
        $Account = $this->getAccount($player_name, $platform);
        if ($Account->account == 200) {
            $request = "https://api.pubg.com/shards/{$this->getPlatform()}/seasons";
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
                        'Accept: application/vnd.api+json',
                        "Authorization: Bearer {$this->getApiKey()}"
                    )
                )
            );
            $pubgSeasonsApiResponse1 = json_decode(curl_exec($this->Curl));
            $pubgSeasonsApiResponseCode1 = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
            if ($pubgSeasonsApiResponseCode1 == 200) {
                $currentSeason = "";
                for ($index = 0; $index < count($pubgSeasonsApiResponse1->data); $index++) {
                    if ($pubgSeasonsApiResponse1->data[$index]->attributes->isCurrentSeason == true) {
                        $currentSeason = $pubgSeasonsApiResponse1->data[$index]->id;
                    }
                }
                $request = "https://api.pubg.com/shards/{$this->getPlatform()}/players/{$this->getIdentifier()}/seasons/{$currentSeason}/ranked";
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
                            'Accept: application/vnd.api+json',
                            "Authorization: Bearer {$this->getApiKey()}"
                        )
                    )
                );
                $pubgSeasonsApiResponse2 = json_decode(curl_exec($this->Curl));
                $pubgSeasonsApiResponseCode2 = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
                if ($pubgSeasonsApiResponseCode2 == 200) {
                    if (!empty($pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats)) {
                        $rankedModes = array_keys((array) $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats);
                        if (count($rankedModes) == 1) {
                            switch ($rankedModes[0]) {
                                case 'solo':
                                    $Solo = (object) array(
                                        "tier" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->solo->currentTier->tier,
                                        "division" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->solo->currentTier->subTier,
                                        "rankPoint" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->solo->currentRankPoint,
                                        "winRate" => round(($pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->solo->winRatio * 100), 2),
                                        "top10Rate" => round(($pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->solo->top10Ratio * 100), 2)
                                    );
                                    break;
                                case 'duo':
                                    $Duo = (object) array(
                                        "tier" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->duo->currentTier->tier,
                                        "division" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->duo->currentTier->subTier,
                                        "rankPoint" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->duo->currentRankPoint,
                                        "winRate" => round(($pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->duo->winRatio * 100), 2),
                                        "top10Rate" => round(($pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->duo->top10Ratio * 100), 2)
                                    );
                                    break;
                                case 'squad':
                                    $Squad = (object) array(
                                        "tier" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->squad->currentTier->tier,
                                        "division" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->squad->currentTier->subTier,
                                        "rankPoint" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->squad->currentRankPoint,
                                        "winRate" => round(($pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->squad->winRatio * 100), 2),
                                        "top10Rate" => round(($pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->squad->top10Ratio * 100), 2)
                                    );
                                    break;
                            }
                        } else {
                            for ($index = 0; $index < count($rankedModes); $index++) {
                                switch ($rankedModes[$index]) {
                                    case 'solo':
                                        $Solo = (object) array(
                                            "tier" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->solo->currentTier->tier,
                                            "division" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->solo->currentTier->subTier,
                                            "rankPoint" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->solo->currentRankPoint,
                                            "winRate" => round(($pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->solo->winRatio * 100), 2),
                                            "top10Rate" => round(($pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->solo->top10Ratio * 100), 2)
                                        );
                                        break;
                                    case 'duo':
                                        $Duo = (object) array(
                                            "tier" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->duo->currentTier->tier,
                                            "division" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->duo->currentTier->subTier,
                                            "rankPoint" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->duo->currentRankPoint,
                                            "winRate" => round(($pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->duo->winRatio * 100), 2),
                                            "top10Rate" => round(($pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->duo->top10Ratio * 100), 2)
                                        );
                                        break;
                                    case 'squad':
                                        $Squad = (object) array(
                                            "tier" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->squad->currentTier->tier,
                                            "division" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->squad->currentTier->subTier,
                                            "rankPoint" => $pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->squad->currentRankPoint,
                                            "winRate" => round(($pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->squad->winRatio * 100), 2),
                                            "top10Rate" => round(($pubgSeasonsApiResponse2->data->attributes->rankedGameModeStats->squad->top10Ratio * 100), 2)
                                        );
                                        break;
                                }
                            }
                        }
                        if (is_null($Solo)) {
                            $Solo = (object) array(
                                "tier" => "Unranked",
                                "division" => 0,
                                "rankPoint" => 0,
                                "winRate" => round(0.0, 2),
                                "top10Rate" => round(0.0, 2)
                            );
                        }
                        if (is_null($Duo)) {
                            $Duo = (object) array(
                                "tier" => "Unranked",
                                "division" => 0,
                                "rankPoint" => 0,
                                "winRate" => round(0.0, 2),
                                "top10Rate" => round(0.0, 2)
                            );
                        }
                        if (is_null($Squad)) {
                            $Squad = (object) array(
                                "tier" => "Unranked",
                                "division" => 0,
                                "rankPoint" => 0,
                                "winRate" => round(0.0, 2),
                                "top10Rate" => round(0.0, 2)
                            );
                        }
                        $Season = (object) array(
                            "Solo" => $Solo,
                            "Duo" => $Duo,
                            "Squad" => $Squad
                        );
                        $response = (object) array(
                            "account" => $Account->account,
                            "season1" => $pubgSeasonsApiResponseCode1,
                            "currentSeason" => $currentSeason,
                            "season2" => $pubgSeasonsApiResponseCode2,
                            "Season" => $Season,
                            "requestedDate" => date("Y/m/d H:i:s"),
                            "renewOn" => date("Y/m/d H:i:s", strtotime("+1 hours"))
                        );
                        $cacheData = json_encode($response);
                        $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Seasons/{$this->getIdentifier()}.json", "w");
                        fwrite($cache, $cacheData);
                        fclose($cache);
                    } else {
                        $response = (object) array(
                            "account" => $Account->account,
                            "season1" => $pubgSeasonsApiResponseCode1,
                            "currentSeason" => $currentSeason,
                            "season2" => $pubgSeasonsApiResponseCode2,
                            "Season" => (object) array()
                        );
                    }
                } else {
                    $response = (object) array(
                        "account" => $Account->account,
                        "season1" => $pubgSeasonsApiResponseCode1,
                        "currentSeason" => $currentSeason,
                        "season2" => $pubgSeasonsApiResponseCode2,
                    );
                }
            } else {
                $response = (object) array(
                    "account" => $Account->account,
                    "season" => $pubgSeasonsApiResponseCode1
                );
            }
        } else {
            $response = (object) array(
                "account" => $Account->account
            );
        }
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
    /**
     * Accessing Match History
     * @param   string  $player_name    The name of the player
     * @param   string  $platform       The platform on which the player plays the game
     * @return  void
     */
    public function getMatchHistory(string $player_name, string $platform): void
    {
        $Account = $this->getAccount($player_name, $platform);
        if ($Account->account == 200) {
            $request = "https://api.pubg.com/shards/{$this->getPlatform()}/seasons";
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
                        'Accept: application/vnd.api+json',
                        "Authorization: Bearer {$this->getApiKey()}"
                    )
                )
            );
            $pubgSeasonsApiResponse1 = json_decode(curl_exec($this->Curl));
            $pubgSeasonsApiResponseCode1 = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
            if ($pubgSeasonsApiResponseCode1 == 200) {
                $currentSeason = "";
                for ($index = 0; $index < count($pubgSeasonsApiResponse1->data); $index++) {
                    if ($pubgSeasonsApiResponse1->data[$index]->attributes->isCurrentSeason == true) {
                        $currentSeason = $pubgSeasonsApiResponse1->data[$index]->id;
                    }
                }
                $request = "https://api.pubg.com/shards/{$this->getPlatform()}/players/{$this->getIdentifier()}/seasons/{$currentSeason}";
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
                            'Accept: application/vnd.api+json',
                            "Authorization: Bearer {$this->getApiKey()}"
                        )
                    )
                );
                $pubgSeasonsApiResponse2 = json_decode(curl_exec($this->Curl));
                $pubgSeasonsApiResponseCode2 = curl_getinfo($this->Curl, CURLINFO_HTTP_CODE);
                if ($pubgSeasonsApiResponseCode2 == 200) {
                    $matchIdentifiers = array();
                    if (!empty($pubgSeasonsApiResponse2->data->relationships->matchesSolo->data)) {
                        for ($index = 0; $index < count($pubgSeasonsApiResponse2->data->relationships->matchesSolo->data); $index++) {
                            array_push($matchIdentifiers, $pubgSeasonsApiResponse2->data->relationships->matchesSolo->data[$index]);
                        }
                    }
                    if (!empty($pubgSeasonsApiResponse2->data->relationships->matchesDuo->data)) {
                        for ($index = 0; $index < count($pubgSeasonsApiResponse2->data->relationships->matchesDuo->data); $index++) {
                            array_push($matchIdentifiers, $pubgSeasonsApiResponse2->data->relationships->matchesDuo->data[$index]);
                        }
                    }
                    if (!empty($pubgSeasonsApiResponse2->data->relationships->matchesSquad->data)) {
                        for ($index = 0; $index < count($pubgSeasonsApiResponse2->data->relationships->matchesSquad->data); $index++) {
                            array_push($matchIdentifiers, $pubgSeasonsApiResponse2->data->relationships->matchesSquad->data[$index]);
                        }
                    }
                    if (!empty($matchIdentifiers)) {
                        $matches = array();
                        for ($firstIndex = 0; $firstIndex < count($matchIdentifiers); $firstIndex++) {
                            $request = "https://api.pubg.com/shards/{$this->getPlatform()}/matches/{$matchIdentifiers[$firstIndex]->id}";
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
                                        'Accept: application/vnd.api+json',
                                        "Authorization: Bearer {$this->getApiKey()}"
                                    )
                                )
                            );
                            $pubgMatchesApiResponse = json_decode(curl_exec($this->Curl));
                            $match = array();
                            for ($secondIndex = 0; $secondIndex < count($pubgMatchesApiResponse->included); $secondIndex++) {
                                if ($pubgMatchesApiResponse->included[$secondIndex]->type == "participant" && $pubgMatchesApiResponse->included[$secondIndex]->attributes->stats->playerId == $this->getIdentifier()) {
                                    $distance = ($pubgMatchesApiResponse->included[$secondIndex]->attributes->stats->rideDistance + $pubgMatchesApiResponse->included[$secondIndex]->attributes->stats->swimDistance + $pubgMatchesApiResponse->included[$secondIndex]->attributes->stats->walkDistance) / 1000;
                                    $data = $pubgMatchesApiResponse->included[$secondIndex]->attributes->stats;
                                    $data = (object) array(
                                        "rank" => $pubgMatchesApiResponse->included[$secondIndex]->attributes->stats->winPlace,
                                        "kill" => $pubgMatchesApiResponse->included[$secondIndex]->attributes->stats->kills,
                                        "damage" => $pubgMatchesApiResponse->included[$secondIndex]->attributes->stats->damageDealt,
                                        "distance" => round($distance, 2)
                                    );
                                }
                                $match = $data;
                            }
                            array_push($matches, $match);
                        }
                        $response = (object) array(
                            "account" => $Account->account,
                            "season1" => $pubgSeasonsApiResponseCode1,
                            "season2" => $pubgSeasonsApiResponseCode2,
                            "matches" => $matches,
                            "dataset" => $pubgMatchesApiResponse,
                            "requestedDate" => date("Y/m/d H:i:s"),
                            "renewOn" => date("Y/m/d H:i:s", strtotime("+1 hours"))
                        );
                        $cacheData = json_encode($response);
                        $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/PUBG/Users/Seasons/{$this->getIdentifier()}.json", "w");
                        fwrite($cache, $cacheData);
                        fclose($cache);
                    } else {
                        $response = (object) array(
                            "account" => $Account->account,
                            "season1" => $pubgSeasonsApiResponseCode1,
                            "season2" => $pubgSeasonsApiResponseCode2,
                            "matches" => array()
                        );
                    }
                } else {
                    $response = (object) array(
                        "account" => $Account->account,
                        "season1" => $pubgSeasonsApiResponseCode1,
                        "season2" => $pubgSeasonsApiResponseCode2
                    );
                }
            } else {
                $response = (object) array(
                    "account" => $Account->account,
                    "season" => $pubgSeasonsApiResponseCode1
                );
            }
        } else {
            $response = (object) array(
                "account" => $Account->account
            );
        }
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
}
