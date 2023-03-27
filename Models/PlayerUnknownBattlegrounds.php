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
     */
    public function getAccount(string $player_name, string $platform): object
    {
        $this->setPlayerName($player_name);
        $this->setPlatform($platform);
        $request = "https://api.pubg.com/shards/{$this->getPlatform()}/players?filter[playerNames]={$this->getPlayerName()}";
        $curl = curl_init();
        curl_setopt_array(
            $curl,
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
        $pubgAccountApiResponse = json_decode(curl_exec($curl));
        $pubgAccountApiResponseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
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
     */
    public function addAccount(string $player_name, string $platform): int
    {
        $Account = $this->getAccount($player_name, $platform);
        if ($Account->httpResponseCode == 200) {
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
                $duo = array(
                    "winrate" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->wins / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->losses)) * 100), 2),
                    "top10Probability" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->top10s / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->duo->losses)) * 100), 2)
                );
                $solo = array(
                    "winrate" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->wins / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->losses)) * 100), 2),
                    "top10Probability" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->top10s / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->solo->losses)) * 100), 2)
                );
                $squad = array(
                    "winrate" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->wins / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->losses)) * 100), 2),
                    "top10Probability" => round((($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->top10s / ($pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->wins + $pubgLifetimeStatsApiResponse->data->attributes->gameModeStats->squad->losses)) * 100), 2)
                );
                $response = array(
                    "httpResponseCode_account" => $pubgAccountApiResponse->httpResponseCode,
                    "httpResponseCode_lifetime" => $pubgLifetimeStatsApiResponseCode,
                    "duo" => $duo,
                    "solo" => $solo,
                    "squad" => $squad,
                    "kda" => round(($kda / max($lengths)), 2),
                    "killStreak" => max($killStreaks),
                    "longestKill" => round(max($longestKills), 2),
                    "headshot" => round(($headshot / max($lengths)), 2),
                    "damagePerMatch" => round(($damagePerMatch / max($lengths)), 2)
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
