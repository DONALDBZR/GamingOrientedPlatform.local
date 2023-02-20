<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/LeagueOfLegends.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/PlayerUnknownBattleGrounds.php";
/**
 * The API and data model of the accounts that are linked to the user
 */
class Account extends User
{
    /**
     * Primary key of the account
     */
    private int $id;
    /**
     * The API which interacts with Riot Games API to take the data needed from Riot Games Data Center as well as the data model which will be used for data analysis.
     */
    protected LeagueOfLegends $LeagueOfLegends;
    /**
     * The API which interacts with PUBG API to take the data needed from PUBG Data Center as well as the data model which will be used for data analysis.
     */
    protected PlayerUnknownBattleGrounds $PlayerUnknownBattleGrounds;
    public function __construct()
    {
        $this->PDO = new PHPDataObject();
        $this->LeagueOfLegends = new LeagueOfLegends();
        $this->PlayerUnknownBattleGrounds = new PlayerUnknownBattleGrounds();
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId(int $id)
    {
        $this->id = $id;
    }
    /**
     * Managing accounts
     * @return object
     */
    public function manage()
    {
        $request = json_decode(file_get_contents("php://input"));
        $this->setUsername($_SESSION['User']['username']);
        $this->PDO->query("SELECT * FROM Accounts WHERE AccountsUser = :AccountsUser");
        $this->PDO->bind(":AccountsUser", $this->getUsername());
        $this->PDO->execute();
        if (!empty($this->PDO->resultSet())) {
            $this->setId($this->PDO->resultSet()[0]['AccountsId']);
            $this->LeagueOfLegends->setPlayerUniversallyUniqueIdentifier($this->PDO->resultSet()[0]['AccountsLoL']);
            $this->PlayerUnknownBattleGrounds->setIdentifier($this->PDO->resultSet()[0]['AccountsPUBG']);
        } else {
            $this->setId(0);
            $this->LeagueOfLegends->setPlayerUniversallyUniqueIdentifier(null);
            $this->PlayerUnknownBattleGrounds->setIdentifier(null);
        }
        if ($this->getId() < 1) {
            $Response = $this->add($request);
        } else {
            $Response = $this->edit($request);
        }
        echo "User.username: {$this->getUsername()}<br />Account.id: {$this->getId()}<br />LeagueOfLegends.puuid: {$this->LeagueOfLegends->getPlayerUniversallyUniqueIdentifier()}";
        $leagueOfLegends = array(
            "playerUniversallyUniqueIdentifier" => $this->LeagueOfLegends->getPlayerUniversallyUniqueIdentifier(),
            "gameName" => $this->LeagueOfLegends->getGameName(),
            "tagLine" => $this->LeagueOfLegends->getTagLine()
        );
        $playerUnknownBattleGrounds = array(
            "identifier" => $this->PlayerUnknownBattleGrounds->getIdentifier(),
            "playerName" => $this->PlayerUnknownBattleGrounds->getPlayerName(),
            "platform" => $this->PlayerUnknownBattleGrounds->getPlatform(),
        );
        $_SESSION['LeagueOfLegends'] = $leagueOfLegends;
        $_SESSION['PlayerUnknownBattleGrounds'] = $playerUnknownBattleGrounds;
        $account = array(
            "LeagueOfLegends" => $_SESSION['LeagueOfLegends'],
            "PlayerUnknownBattleGrounds" => $_SESSION['PlayerUnknownBattleGrounds']
        );
        $_SESSION['Account'] = $account;
        if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/{$this->getUsername()}.json")) {
            file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/{$this->getUsername()}.json", "");
        }
        $data = array(
            "Client" => $_SESSION['Client'],
            "User" => $_SESSION['User'],
            "Account" => $_SESSION['Account']
        );
        $cacheData = json_encode($data);
        $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/{$_SESSION['User']['username']}.json", "w");
        fwrite($cache, $cacheData);
        fclose($cache);
        $headers = $Response->headers;
        $response = $Response->response;
        header($headers["headers"], $headers["replace"], $headers["responseCode"]);
        echo json_encode($response);
    }
    /**
     * Managing League of Legends accounts
     * @param object $form
     * @return object
     */
    public function manageLeagueOfLegends(object $form)
    {
        $status = $this->createLeagueOfLegendsAccount($form->lolUsername, $form->lolRegion);
        switch ($status) {
            case 0:
                $response = array(
                    "status" => $status,
                    "url" => "/Users/Home/{$_SESSION['User']['username']}",
                    "message" => "Your account has been added!"
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 200
                );
                break;
            case 1:
                $response = array(
                    "status" => $status,
                    "url" => "/Users/Accounts/{$_SESSION['User']['username']}",
                    "message" => "The form must be completely filled!"
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 300
                );
                break;
            case 11:
                $response = array(
                    "status" => $status,
                    "url" => "/Users/Accounts/{$_SESSION['User']['username']}",
                    "message" => "This account does not exist!"
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 400
                );
                break;
        }
        $Response = (object) array(
            "response" => $response,
            "headers" => $headers
        );
        return $Response;
    }
    /**
     * Managing Player Unknown Battle Grounds accounts
     * @param object $form
     */
    public function managePlayerUnknownBattleGrounds(object $form)
    {
        $status = $this->createPlayerUnknownBattleGroundsAccount($form->pubgUsername, $form->pubgPlatform);
        switch ($status) {
            case 0:
                $response = array(
                    "status" => $status,
                    "url" => "/Users/Home/{$_SESSION['User']['username']}",
                    "message" => "Your account has been added!"
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 200
                );
                break;
            case 1:
                $response = array(
                    "status" => $status,
                    "url" => "/Users/Accounts/{$_SESSION['User']['username']}",
                    "message" => "The form must be completely filled!"
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 300
                );
                break;
            case 11:
                $response = array(
                    "status" => $status,
                    "url" => "/Users/Accounts/{$_SESSION['User']['username']}",
                    "message" => "This account does not exist!"
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 400
                );
                break;
        }
        $Response = (object) array(
            "response" => $response,
            "headers" => $headers
        );
        return $Response;
    }
    /**
     * Adding accounts
     * @param object $request
     * @return object
     */
    public function add(object $request)
    {
        if (!is_null($request->LeagueOfLegends->lolUsername) && !is_null($request->LeagueOfLegends->lolRegion)) {
            $Response = $this->manageLeagueOfLegends($request->LeagueOfLegends);
        } else if (!is_null($request->PlayerUnknownBattleGrounds->pubgUsername) && !is_null($request->PlayerUnknownBattleGrounds->pubgPlatform)) {
            $Response = $this->managePlayerUnknownBattleGrounds($request->PlayerUnknownBattleGrounds);
        } else {
            $response = array(
                "status" => 1,
                "url" => "/Users/Accounts/{$_SESSION['User']['username']}",
                "message" => "The form must be completely filled!"
            );
            $headers = array(
                "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                "replace" => true,
                "responseCode" => 300
            );
            $Response = (object) array(
                "response" => $response,
                "headers" => $headers
            );
        }
        $this->PDO->query("INSERT INTO Accounts(AccountsLoL, AccountsUser, AccountsPUBG) VALUES (:AccountsLoL, :AccountsUser, :AccountsPUBG)");
        $this->PDO->bind(":AccountsLoL", $this->LeagueOfLegends->getPlayerUniversallyUniqueIdentifier());
        $this->PDO->bind(":AccountsUser", $this->getUsername());
        $this->PDO->bind(":AccountsPUBG", $this->PlayerUnknownBattleGrounds->getIdentifier());
        $this->PDO->execute();
        return $Response;
    }
    /**
     * Editing the accounts of the user
     * @param object $request
     * @return object
     */
    public function edit(object $request)
    {
        if (!is_null($request->LeagueOfLegends->lolUsername) && !is_null($request->LeagueOfLegends->lolRegion)) {
            $Response = $this->manageLeagueOfLegends($request->LeagueOfLegends);
        } else if (!is_null($request->PlayerUnknownBattleGrounds->pubgUsername) && !is_null($request->PlayerUnknownBattleGrounds->pubgPlatform)) {
            $Response = $this->managePlayerUnknownBattleGrounds($request->PlayerUnknownBattleGrounds);
        } else {
            $response = array(
                "status" => 1,
                "url" => "/Users/Accounts/{$_SESSION['User']['username']}",
                "message" => "The form must be completely filled!"
            );
            $headers = array(
                "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                "replace" => true,
                "responseCode" => 300
            );
            $Response = (object) array(
                "response" => $response,
                "headers" => $headers
            );
        }
        $this->PDO->query("UPDATE Accounts SET AccountsLoL = :AccountsLoL, AccountsPUBG = :AccountsPUBG WHERE AccountsUser = :AccountsUser");
        $this->PDO->bind(":AccountsLoL", $this->LeagueOfLegends->getPlayerUniversallyUniqueIdentifier());
        $this->PDO->bind(":AccountsPUBG", $this->PlayerUnknownBattleGrounds->getIdentifier());
        $this->PDO->bind(":AccountsUser", $this->getUsername());
        $this->PDO->execute();
        return $Response;
    }
    /**
     * Creating League Of Legends Accounts
     * @param ?string $username
     * @param ?string $region
     * @return int
     */
    public function createLeagueOfLegendsAccount(?string $username, ?string $region)
    {
        if (!is_null($username) && !is_null($region)) {
            if ($this->LeagueOfLegends->addAccount($username, $region) == 0) {
                return 0;
            } else {
                return 11;
            }
        } else {
            return 1;
        }
    }
    /**
     * Creating Player Unknown Battle Grounds Accounts
     * @param ?string $player_name
     * @param ?string $platform
     * @return int
     */
    public function createPlayerUnknownBattleGroundsAccount(?string $player_name, ?string $platform)
    {
        if (!is_null($player_name) && !is_null($platform)) {
            if ($this->PlayerUnknownBattleGrounds->addAccount($player_name, $platform) == 0) {
                return 0;
            } else {
                return 11;
            }
        } else {
            return 1;
        }
    }
}
