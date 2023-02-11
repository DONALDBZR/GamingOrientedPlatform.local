<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Environment.php";
/**
 * The API which interacts with PUBG API to take the data needed from PUBG Data Center as well as the data model which will be used for data analysis.
 */
class PlayerUnknownBattleGrounds
{
    /**
     * The username of the player
     */
    private string $playerName;
    /**
     * The platform of the player
     */
    private string $platform;
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
}
