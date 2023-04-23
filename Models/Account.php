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
        header($headers["headers"]["Content-Type"], $headers["replace"], $headers["responseCode"]);
        header($headers["headers"]["X-XSS-Protection"], $headers["replace"], $headers["responseCode"]);
        header($headers["headers"]["Mode"], $headers["replace"], $headers["responseCode"]);
        echo json_encode($response);
    }
    /**
     * Managing League of Legends accounts
     * @param   object  $form   Form data
     * @return  int
     */
    public function manageLeagueOfLegends(object $form): int
    {
        return $this->createLeagueOfLegendsAccount($form->gameName, $form->tagLine);
    }
    /**
     * Managing Player Unknown Battle Grounds accounts
     * @param   object  $form   Form data
     * @return  int
     */
    public function managePlayerUnknownBattleGrounds(object $form): int
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
        if ($processes["LeagueOfLegends"] == 0 && $processes["PlayerUnknownBattleGrounds"] == 0) {
            $response = array(
                "status" => 0,
                "url" => "/Users/Home/{$_SESSION['User']['username']}",
                "message" => "Your account has been added!"
            );
            $headers = array(
                "headers" => array(
                    "Content-Type" => "Content-Type: application/json",
                    "X-XSS-Protection" => "X-XSSProtection: 1",
                    "Mode" => "mode = block"
                ),
                "replace" => true,
                "responseCode" => 200
            );
        } else {
            $response = array(
                "status" => 1,
                "url" => "/Users/Accounts/{$_SESSION['User']['username']}",
                "message" => "The form must be completely filled!"
            );
            $headers = array(
                "headers" => array(
                    "Content-Type" => "Content-Type: application/json",
                    "X-XSS-Protection" => "X-XSSProtection: 1",
                    "Mode" => "mode = block"
                ),
                "replace" => true,
                "responseCode" => 300
            );
        }
        $Response = (object) array(
            "response" => $response,
            "headers" => $headers
        );
        $this->PDO->query("INSERT INTO Accounts(AccountsLoL, AccountsUser, AccountsPUBG) VALUES (:AccountsLoL, :AccountsUser, :AccountsPUBG)");
        $this->PDO->bind(":AccountsLoL", $this->LeagueOfLegends->getPlayerUniversallyUniqueIdentifier());
        $this->PDO->bind(":AccountsUser", $this->getUsername());
        $this->PDO->bind(":AccountsPUBG", $this->PlayerUnknownBattleGrounds->getIdentifier());
        $this->PDO->execute();
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
        if ($processes["LeagueOfLegends"] == 0 && $processes["PlayerUnknownBattleGrounds"] == 0) {
            $response = array(
                "status" => 0,
                "url" => "/Users/Home/{$_SESSION['User']['username']}",
                "message" => "Your account has been added!"
            );
            $headers = array(
                "headers" => array(
                    "Content-Type" => "Content-Type: application/json",
                    "X-XSS-Protection" => "X-XSSProtection: 1",
                    "Mode" => "mode = block"
                ),
                "replace" => true,
                "responseCode" => 200
            );
        } else {
            $response = array(
                "status" => 1,
                "url" => "/Users/Accounts/{$_SESSION['User']['username']}",
                "message" => "The form must be completely filled!"
            );
            $headers = array(
                "headers" => array(
                    "Content-Type" => "Content-Type: application/json",
                    "X-XSS-Protection" => "X-XSSProtection: 1",
                    "Mode" => "mode = block"
                ),
                "replace" => true,
                "responseCode" => 300
            );
        }
        $Response = (object) array(
            "response" => $response,
            "headers" => $headers
        );
        $this->PDO->query("UPDATE Accounts SET AccountsLoL = :AccountsLoL, AccountsPUBG = :AccountsPUBG WHERE AccountsUser = :AccountsUser");
        $this->PDO->bind(":AccountsLoL", $this->LeagueOfLegends->getPlayerUniversallyUniqueIdentifier());
        $this->PDO->bind(":AccountsPUBG", $this->PlayerUnknownBattleGrounds->getIdentifier());
        $this->PDO->bind(":AccountsUser", $this->getUsername());
        $this->PDO->execute();
        return $Response;
    }
    /**
     * Creating League Of Legends Accounts
     * @param   null|string $game_name  The username of the player
     * @param   null|string $tag_line   The regional routing server of the player
     * @return  int
     */
    public function createLeagueOfLegendsAccount(?string $game_name, ?string $tag_line): int
    {
        if (!is_null($game_name) && !is_null($tag_line)) {
            if ($this->LeagueOfLegends->addAccount($game_name, strtolower($tag_line)) == 0) {
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
     * @param   null|string $player_name    Name of the player
     * @param   null|string $platform       Platform which the player uses tp play the game
     * @return  int
     */
    public function createPlayerUnknownBattleGroundsAccount(?string $player_name, ?string $platform): int
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
