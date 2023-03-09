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
    public function __construct()
    {
        $this->setApiKey(Environment::PubgAPIKey);
        $this->PDO = new PHPDataObject();
    }
    public function getPlayerName()
    {
        return $this->playerName;
    }
    public function setPlayerName(string $player_name)
    {
        $this->playerName = $player_name;
    }
    public function getPlatform()
    {
        return $this->platform;
    }
    public function setPlatform(string $platform)
    {
        $this->platform = $platform;
    }
    public function getIdentifier()
    {
        return $this->identifier;
    }
    public function setIdentifier(?string $identifier)
    {
        $this->identifier = $identifier;
    }
    public function getApiKey()
    {
        return $this->apiKey;
    }
    public function setApiKey(string $api_key)
    {
        $this->apiKey = $api_key;
    }
    /**
     * Retrieving accounts's data
     * @param string $player_name
     * @param string $platform
     */
    public function retrieveData(string $player_name, $platform)
    {
        $this->setPlayerName($player_name);
        $this->setPlatform($platform);
        $pubgAccountApiRequest = "https://api.pubg.com/shards/{$this->getPlatform()}/players?filter[playerNames]={$this->getPlayerName()}";
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $pubgAccountApiRequest,
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
        $pubgAccountApiResponse = json_decode(curl_exec($curl));
        $pubgAccountApiResponseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($pubgAccountApiResponseCode == 200) {
            $this->setIdentifier($pubgAccountApiResponse->data[0]->id);
            $response = array(
                "httpResponseCode" => $pubgAccountApiResponseCode,
                "identifier" => $this->getIdentifier(),
                "playerName" => $this->getPlayerName(),
                "platform" => $this->getPlatform()
            );
        } else {
            $response = array(
                "httpResponseCode" => $pubgAccountApiResponseCode
            );
        }
        return json_encode($response);
    }
    /**
     * Adding Player Unknown Battle Grounds account in the database
     * @param string $player_name
     * @param string $platform
     * @return int
     */
    public function addAccount(string $player_name, string $platform)
    {
        $pubgAccountApiResponse = json_decode($this->retrieveData($player_name, $platform));
        if ($pubgAccountApiResponse->httpResponseCode == 200) {
            $this->PDO->query("SELECT * FROM PlayerUnknownBattleGrounds WHERE PlayerUnknownBattleGroundsIdentifier = :PlayerUnknownBattleGroundsIdentifier");
            $this->PDO->bind(":PlayerUnknownBattleGroundsIdentifier", $this->getIdentifier());
            $this->PDO->execute();
            if (empty($this->PDO->resultSet())) {
                $this->PDO->query("INSERT INTO PlayerUnknownBattleGrounds (PlayerUnknownBattleGroundsIdentifier, PlayerUnknownBattleGroundsPlayerName, PlayerUnknownBattleGroundsPlatform) VALUES (:PlayerUnknownBattleGroundsIdentifier, :PlayerUnknownBattleGroundsPlayerName, :PlayerUnknownBattleGroundsPlatform)");
                $this->PDO->bind(":PlayerUnknownBattleGroundsIdentifier", $this->getIdentifier());
                $this->PDO->bind(":PlayerUnknownBattleGroundsPlayerName", $this->getPlayerName());
                $this->PDO->bind(":PlayerUnknownBattleGroundsPlatform", $this->getPlatform());
                $this->PDO->execute();
            }
            $playerUnknownBattleGrounds = array(
                "identifier" => $this->getIdentifier(),
                "playerName" => $this->getPlayerName(),
                "platform" => $this->getPlatform()
            );
            $_SESSION['PlayerUnknownBattleGrounds'] = $playerUnknownBattleGrounds;
            return 0;
        } else {
            return 1;
        }
    }
    /**
     * Accessing account data
     * @param string $player_name
     * @param string $platform
     */
    public function getAccount(string $player_name, string $platform)
    {
        $pubgAccountApiResponse = json_decode($this->retrieveData($player_name, $platform));
        if (json_decode($pubgAccountApiResponse->httpResponseCode == 200)) {
            $pubgLifetimeStatsApiRequest = "https://api.pubg.com/shards/{$this->getPlatform()}/players/{$this->getIdentifier()}/seasons/lifetime";
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $pubgLifetimeStatsApiRequest,
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
            $pubgLifetimeStatsApiResponse = json_decode(curl_exec($curl));
            $pubgLifetimeStatsApiResponseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($pubgLifetimeStatsApiResponseCode == 200) {
                $duo = array(
                    "winrate" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->wins / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->losses)) * 100), 2),
                    "top10Probability" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->top10s / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->losses)) * 100), 2),
                    "kda" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->kills + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->assists) / $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->losses), 2),
                    'damagePerMatch' => round(($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->damageDealt / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->losses)), 2),
                    "longestKill" => round($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->longestKill, 2),
                    "headshot" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->headshotKills / $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->kills) * 100), 2),
                    "killStreak" => $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->maxKillStreaks
                );
                $solo = array(
                    "winrate" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->wins / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->losses)) * 100), 2),
                    "top10Probability" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->top10s / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->losses)) * 100), 2),
                    "kda" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->kills + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->assists) / $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->losses), 2),
                    'damagePerMatch' => round(($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->damageDealt / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->losses)), 2),
                    "longestKill" => round($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->longestKill, 2),
                    "headshot" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->headshotKills / $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->kills) * 100), 2),
                    "killStreak" => $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->maxKillStreaks
                );
                $squad = array(
                    "winrate" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->wins / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->losses)) * 100), 2),
                    "top10Probability" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->top10s / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->losses)) * 100), 2),
                    "kda" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->kills + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->assists) / $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->losses), 2),
                    'damagePerMatch' => round(($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->damageDealt / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->losses)), 2),
                    "longestKill" => round($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->longestKill, 2),
                    "headshot" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->headshotKills / $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->kills) * 100), 2),
                    "killStreak" => $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->maxKillStreaks
                );
                $response = array(
                    "httpResponseCode_account" => $pubgAccountApiResponse->httpResponseCode,
                    "httpResponseCode_lifetime" => $pubgLifetimeStatsApiResponseCode,
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
                    "httpResponseCode_account" => $pubgAccountApiResponse->httpResponseCode,
                    "httpResponseCode_lifetime" => $pubgLifetimeStatsApiResponseCode
                );
            }
        } else {
            $response = array(
                "httpResponseCode_account" => $pubgAccountApiResponse->httpResponseCode
            );
        }
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
}
