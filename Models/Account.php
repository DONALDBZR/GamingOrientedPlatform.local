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
     * @var int $id
     */
    private int $id;
    /**
     * The API which interacts with Riot Games API to take the data needed from Riot Games Data Center as well as the data model which will be used for data analysis.
     * @var LeagueOfLegends $LeagueOfLegends
     */
    protected LeagueOfLegends $LeagueOfLegends;
    /**
     * The API which interacts with PUBG API to take the data needed from PUBG Data Center as well as the data model which will be used for data analysis.
     * @var PlayerUnknownBattleGrounds $PlayerUnknownBattleGrounds
     */
    protected PlayerUnknownBattleGrounds $PlayerUnknownBattleGrounds;
    /**
     * Ipon instantiation, its dependencies are also instantiated
     */
    public function __construct()
    {
        $this->PDO = new PHPDataObject();
        $this->LeagueOfLegends = new LeagueOfLegends();
        $this->PlayerUnknownBattleGrounds = new PlayerUnknownBattleGrounds();
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    /**
     * Managing accounts
     * @param   object  $request    JSON from the view
     * @return  void
     */
    public function manage(object $request): void
    {
        $this->setUsername($_SESSION["User"]["username"]);
        $this->PDO->query("SELECT * FROM Accounts WHERE AccountsUser = :AccountsUser");
        $this->PDO->bind(":AccountsUser", $this->getUsername());
        try {
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
            if ($this->getId() == 0) {
                $Response = $this->add($request);
            } else {
                $Response = $this->edit($request);
            }
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
                "Accounts" => $_SESSION['Account']
            );
            $cacheData = json_encode($data);
            $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/{$_SESSION['User']['username']}.json", "w");
            fwrite($cache, $cacheData);
            fclose($cache);
            $headers = $Response->headers;
            $response = $Response->response;
        } catch (PDOException $error) {
            $response = array(
                "status" => 1,
                "url" => "/Users/Accounts/{$_SESSION['User']['username']}",
                "message" => $error->getMessage(),
                "Accounts" => 500
            );
            $headers = array(
                "headers" => array(
                    "Content-Type" => "Content-Type: application/json",
                    "X-XSS-Protection" => "X-XSSProtection: 1",
                    "Mode" => "mode = block"
                ),
                "replace" => true,
                "responseCode" => $response["Accounts"]
            );
        }
        header($headers["headers"]["Content-Type"], $headers["replace"], $headers["responseCode"]);
        header($headers["headers"]["X-XSS-Protection"], $headers["replace"], $headers["responseCode"]);
        header($headers["headers"]["Mode"], $headers["replace"], $headers["responseCode"]);
        echo json_encode($response);
    }
    /**
     * Managing League of Legends accounts
     * @param   object  $form   Form data
     * @return  object
     */
    public function manageLeagueOfLegends(object $form): object
    {
        return $this->createLeagueOfLegendsAccount($form->gameName, $form->tagLine);
    }
    /**
     * Managing Player Unknown Battle Grounds accounts
     * @param   object  $form   Form data
     * @return  object
     */
    public function managePlayerUnknownBattleGrounds(object $form): object
    {
        return $this->createPlayerUnknownBattleGroundsAccount($form->playerName, $form->platform);
    }
    /**
     * Adding accounts
     * @param   object  $request    JSON from the view
     * @return  object
     */
    public function add(object $request): object
    {
        $processes = array(
            "LeagueOfLegends" => $this->manageLeagueOfLegends($request->LeagueOfLegends),
            "PlayerUnknownBattleGrounds" => $this->managePlayerUnknownBattleGrounds($request->PlayerUnknownBattleGrounds)
        );
        if ($processes["LeagueOfLegends"]->LeagueOfLegends == 201 && $processes["PlayerUnknownBattleGrounds"]->PlayerUnknownBattleGrounds == 201) {
            $response = array(
                "status" => 0,
                "url" => "/Users/Home/{$_SESSION['User']['username']}",
                "message" => "Your account has been added!",
                "LeagueOfLegends" => $processes["LeagueOfLegends"]->LeagueOfLegends,
                "PlayerUnknownBattleGrounds" => $processes["PlayerUnknownBattleGrounds"]->PlayerUnknownBattleGrounds
            );
            $headers = array(
                "headers" => array(
                    "Content-Type" => "Content-Type: application/json",
                    "X-XSS-Protection" => "X-XSSProtection: 1",
                    "Mode" => "mode = block"
                ),
                "replace" => true,
                "responseCode" => 201
            );
        } else {
            $message = "";
            $status = 0;
            $responseCode = 0;
            if ($processes["LeagueOfLegends"]->status > $processes["PlayerUnknownBattleGrounds"]->status) {
                $message = $processes["LeagueOfLegends"]->message;
                $status = $processes["LeagueOfLegends"]->status;
                $response = $processes["LeagueOfLegends"]->LeagueOfLegends;
            } else {
                $message = $processes["PlayerUnknownBattleGrounds"]->message;
                $status = $processes["PlayerUnknownBattleGrounds"]->status;
                $response = $processes["PlayerUnknownBattleGrounds"]->PlayerUnknownBattleGrounds;
            }
            $response = array(
                "status" => $status,
                "url" => $_SERVER["HTTP_REFERER"],
                "message" => $message,
                "LeagueOfLegends" => $processes["LeagueOfLegends"]->LeagueOfLegends,
                "PlayerUnknownBattleGrounds" => $processes["PlayerUnknownBattleGrounds"]->PlayerUnknownBattleGrounds
            );
            $headers = array(
                "headers" => array(
                    "Content-Type" => "Content-Type: application/json",
                    "X-XSS-Protection" => "X-XSSProtection: 1",
                    "Mode" => "mode = block"
                ),
                "replace" => true,
                "responseCode" => $response
            );
        }
        $this->PDO->query("INSERT INTO Accounts(AccountsLoL, AccountsUser, AccountsPUBG) VALUES (:AccountsLoL, :AccountsUser, :AccountsPUBG)");
        $this->PDO->bind(":AccountsLoL", $this->LeagueOfLegends->getPlayerUniversallyUniqueIdentifier());
        $this->PDO->bind(":AccountsUser", $this->getUsername());
        $this->PDO->bind(":AccountsPUBG", $this->PlayerUnknownBattleGrounds->getIdentifier());
        try {
            $this->PDO->execute();
            $response = array(
                "status" => 0,
                "url" => "/Users/Home/{$_SESSION['User']['username']}",
                "message" => "Your account has been added!",
                "LeagueOfLegends" => $processes["LeagueOfLegends"]->LeagueOfLegends,
                "PlayerUnknownBattleGrounds" => $processes["PlayerUnknownBattleGrounds"]->PlayerUnknownBattleGrounds,
                "Accounts" => 201
            );
            $headers = array(
                "headers" => array(
                    "Content-Type" => "Content-Type: application/json",
                    "X-XSS-Protection" => "X-XSSProtection: 1",
                    "Mode" => "mode = block"
                ),
                "replace" => true,
                "responseCode" => $response["Accounts"]
            );
        } catch (PDOException $error) {
            $response = array(
                "status" => 11,
                "url" => $_SERVER["HTTP_REFERER"],
                "message" => $error->getMessage(),
                "LeagueOfLegends" => $processes["LeagueOfLegends"]->LeagueOfLegends,
                "PlayerUnknownBattleGrounds" => $processes["PlayerUnknownBattleGrounds"]->PlayerUnknownBattleGrounds,
                "Accounts" => 500
            );
            $headers = array(
                "headers" => array(
                    "Content-Type" => "Content-Type: application/json",
                    "X-XSS-Protection" => "X-XSSProtection: 1",
                    "Mode" => "mode = block"
                ),
                "replace" => true,
                "responseCode" => $response["Accounts"]
            );
        }
        $Response = (object) array(
            "response" => $response,
            "headers" => $headers
        );
        return $Response;
    }
    /**
     * Editing the accounts of the user
     * @param   object  $request    Object containing data that needed to be changed into
     * @return  object
     */
    public function edit(object $request): object
    {
        $processes = array(
            "LeagueOfLegends" => $this->manageLeagueOfLegends($request->LeagueOfLegends),
            "PlayerUnknownBattleGrounds" => $this->managePlayerUnknownBattleGrounds($request->PlayerUnknownBattleGrounds)
        );
        if ($processes["LeagueOfLegends"]->LeagueOfLegends == 201 && $processes["PlayerUnknownBattleGrounds"]->PlayerUnknownBattleGrounds == 201) {
            $response = array(
                "status" => 0,
                "url" => "/Users/Home/{$_SESSION['User']['username']}",
                "message" => "Your account has been updated!",
                "LeagueOfLegends" => $processes["LeagueOfLegends"]->LeagueOfLegends,
                "PlayerUnknownBattleGrounds" => $processes["PlayerUnknownBattleGrounds"]->PlayerUnknownBattleGrounds
            );
            $headers = array(
                "headers" => array(
                    "Content-Type" => "Content-Type: application/json",
                    "X-XSS-Protection" => "X-XSSProtection: 1",
                    "Mode" => "mode = block"
                ),
                "replace" => true,
                "responseCode" => 201
            );
        } else {
            $message = "";
            $status = 0;
            $responseCode = 0;
            if ($processes["LeagueOfLegends"]->status > $processes["PlayerUnknownBattleGrounds"]->status) {
                $message = $processes["LeagueOfLegends"]->message;
                $status = $processes["LeagueOfLegends"]->status;
                $response = $processes["LeagueOfLegends"]->LeagueOfLegends;
            } else {
                $message = $processes["PlayerUnknownBattleGrounds"]->message;
                $status = $processes["PlayerUnknownBattleGrounds"]->status;
                $response = $processes["PlayerUnknownBattleGrounds"]->PlayerUnknownBattleGrounds;
            }
            $response = array(
                "status" => $status,
                "url" => $_SERVER["HTTP_REFERER"],
                "message" => $message,
                "LeagueOfLegends" => $processes["LeagueOfLegends"]->LeagueOfLegends,
                "PlayerUnknownBattleGrounds" => $processes["PlayerUnknownBattleGrounds"]->PlayerUnknownBattleGrounds
            );
            $headers = array(
                "headers" => array(
                    "Content-Type" => "Content-Type: application/json",
                    "X-XSS-Protection" => "X-XSSProtection: 1",
                    "Mode" => "mode = block"
                ),
                "replace" => true,
                "responseCode" => $response
            );
        }
        $this->PDO->query("UPDATE Accounts SET AccountsLoL = :AccountsLoL, AccountsPUBG = :AccountsPUBG WHERE AccountsUser = :AccountsUser");
        $this->PDO->bind(":AccountsLoL", $this->LeagueOfLegends->getPlayerUniversallyUniqueIdentifier());
        $this->PDO->bind(":AccountsUser", $this->getUsername());
        $this->PDO->bind(":AccountsPUBG", $this->PlayerUnknownBattleGrounds->getIdentifier());
        try {
            $this->PDO->execute();
            $response = array(
                "status" => 0,
                "url" => "/Users/Home/{$_SESSION['User']['username']}",
                "message" => "Your account has been updated!",
                "LeagueOfLegends" => $processes["LeagueOfLegends"]->LeagueOfLegends,
                "PlayerUnknownBattleGrounds" => $processes["PlayerUnknownBattleGrounds"]->PlayerUnknownBattleGrounds,
                "Accounts" => 201
            );
            $headers = array(
                "headers" => array(
                    "Content-Type" => "Content-Type: application/json",
                    "X-XSS-Protection" => "X-XSSProtection: 1",
                    "Mode" => "mode = block"
                ),
                "replace" => true,
                "responseCode" => $response["Accounts"]
            );
        } catch (PDOException $error) {
            $response = array(
                "status" => 12,
                "url" => $_SERVER["HTTP_REFERER"],
                "message" => $error->getMessage(),
                "LeagueOfLegends" => $processes["LeagueOfLegends"]->LeagueOfLegends,
                "PlayerUnknownBattleGrounds" => $processes["PlayerUnknownBattleGrounds"]->PlayerUnknownBattleGrounds,
                "Accounts" => 500
            );
            $headers = array(
                "headers" => array(
                    "Content-Type" => "Content-Type: application/json",
                    "X-XSS-Protection" => "X-XSSProtection: 1",
                    "Mode" => "mode = block"
                ),
                "replace" => true,
                "responseCode" => $response["Accounts"]
            );
        }
        $Response = (object) array(
            "response" => $response,
            "headers" => $headers
        );
        return $Response;
    }
    /**
     * Creating League Of Legends Accounts
     * @param   null|string $game_name  The username of the player
     * @param   null|string $tag_line   The regional routing server of the player
     * @return  object
     */
    public function createLeagueOfLegendsAccount(?string $game_name, ?string $tag_line): object
    {
        if (!is_null($game_name) && !is_null($tag_line)) {
            $LeagueOfLegendsResponse = $this->LeagueOfLegends->addAccount($game_name, strtolower($tag_line));
            if ($LeagueOfLegendsResponse->LeagueOfLegends == 201) {
                $response = (object) array(
                    "url" => $LeagueOfLegendsResponse->url,
                    "status" => $LeagueOfLegends->status,
                    "LeagueOfLegends" => $LeagueOfLegendsResponse->LeagueOfLegends,
                    "RiotGamesSummonerApi" => $LeagueOfLegendsResponse->RiotGamesSummonerApi
                );
            } else {
                $response = (object) array(
                    "url" => $LeagueOfLegendsResponse->url,
                    "status" => $LeagueOfLegends->status,
                    "LeagueOfLegends" => $LeagueOfLegendsResponse->LeagueOfLegends,
                    "RiotGamesSummonerApi" => $LeagueOfLegendsResponse->RiotGamesSummonerApi,
                    "message" => $LeagueOfLegendsResponse->message
                );
            }
        } else {
            $response = (object) array(
                "url" => $_SERVER['HTTP_REFERER'],
                "status" => 7,
                "LeagueOfLegends" => 403,
                "RiotGamesSummonerApi" => 403,
                "message" => "Incomplete Request!"
            );
        }
        return $response;
    }
    /**
     * Creating Player Unknown Battle Grounds Accounts
     * @param   null|string $player_name    Name of the player
     * @param   null|string $platform       Platform which the player uses tp play the game
     * @return  object
     */
    public function createPlayerUnknownBattleGroundsAccount(?string $player_name, ?string $platform): object
    {
        if (!is_null($player_name) && !is_null($platform)) {
            $PlayerUnknownBattleGroundsResponse = $this->PlayerUnknownBattleGrounds->addAccount($player_name, $platform);
            if ($PlayerUnknownBattleGroundsResponse->PlayerUnknownBattleGrounds == 201) {
                $response = (object) array(
                    "url" => $_SERVER['HTTP_REFERER'],
                    "status" => $PlayerUnknownBattleGroundsResponse->status,
                    "PlayerUnknownBattleGrounds" => $PlayerUnknownBattleGroundsResponse->PlayerUnknownBattleGrounds,
                    "PlayerUnknownBattleGroundsAccountAPI" => $PlayerUnknownBattleGroundsResponse->PlayerUnknownBattleGroundsAccountAPI
                );
            } else {
                $response = (object) array(
                    "url" => $_SERVER['HTTP_REFERER'],
                    "status" => $PlayerUnknownBattleGroundsResponse->status,
                    "PlayerUnknownBattleGrounds" => $PlayerUnknownBattleGroundsResponse->PlayerUnknownBattleGrounds,
                    "PlayerUnknownBattleGroundsAccountAPI" => $PlayerUnknownBattleGroundsResponse->PlayerUnknownBattleGroundsAccountAPI,
                    "message" => $PlayerUnknownBattleGroundsResponse->message
                );
            }
        } else {
            $response = (object) array(
                "url" => $_SERVER['HTTP_REFERER'],
                "status" => 10,
                "PlayerUnknownBattleGrounds" => 403,
                "PlayerUnknownBattleGroundsAccountAPI" => 403,
                "message" => "Incomplete Request!"
            );
        }
        return $response;
    }
}
