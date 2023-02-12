<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Environment.php";
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
    private string $ApiKey = Environment::PubgAPIKey;
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
    /**
     * Retrievieng accounts's data
     * @param string $player_name
     * @param string $platform
     */
    public function retrieveData(string $player_name, $platform)
    {
        $this->setPlayerName($player_name);
        $this->setPlatform($platform);
        $pubgAccountApiRequest = "https://api.pubg.com/shards/{$this->getPlatform()}/players?filter[playerNames]={$this->getPlayerName()}";
        $headers = array(
            "Authorization" => "Bearer " . $this->ApiKey,
            "Accept" => "application/json"
        );
        $curl = curl_init($pubgAccountApiRequest);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if ($this->getHTTPResponseCode($curl) == 200) {
            $pubgAccountApiResponse = json_decode(curl_exec($curl));
            $this->setIdentifier($pubgAccountApiResponse->id);
            $response = array(
                "httpResponseCode" => intval($curl),
                "identifier" => $this->getIdentifier(),
                "playerName" => $this->getPlayerName(),
                "platform" => $this->getPlatform()
            );
        } else {
            $response = array(
                "httpResponseCode" => intval($this->getHTTPResponseCode($curl))
            );
        }
        return json_encode($response);
    }
    /**
     * Accessing the HTTP response code
     * @param CurlHandle $request_uniform_resource_locator
     * @return string
     */
    public function getHTTPResponseCode(CurlHandle $request_uniform_resource_locator)
    {
        curl_setopt($request_uniform_resource_locator, CURLOPT_HEADER, true);
        curl_setopt($request_uniform_resource_locator, CURLOPT_NOBODY, true);
        curl_setopt($request_uniform_resource_locator, CURLOPT_RETURNTRANSFER, 1);
        $headers = curl_exec($request_uniform_resource_locator);
        $httpResponseCode = curl_getinfo($request_uniform_resource_locator, CURLINFO_HTTP_CODE);
        curl_close($request_uniform_resource_locator);
        return $httpResponseCode;
    }
}
