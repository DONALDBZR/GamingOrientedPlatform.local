<?php
// Importing User
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
// Importing League of Legends
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/LeagueOfLegends.php";
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
    public function __construct()
    {
        $this->domain = "http://{$_SERVER['HTTP_HOST']}/";
        $this->PDO = new PHPDataObject();
        $this->LeagueOfLegends = new LeagueOfLegends();
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
     * Adding accounts
     * @return JSON
     */
    public function add()
    {
        $request = json_decode(file_get_contents("php://input"));
        $requestArray = json_decode(file_get_contents("php://input"), true);
        $requestKeys = array_keys($requestArray);
        if (str_contains($requestKeys[0], "lol")) {
            $leagueOfLegends = json_decode($this->LeagueOfLegends->retrieveData($request->lolUsername, $request->lolRegion));
            if ($leagueOfLegends->httpResponseCode == 200) {
                $this->PDO->query("INSERT INTO Parkinston.LeagueOfLegends(LeagueOfLegendsPlayerUniversallyUniqueIdentifier, LeagueOfLegendsGameName, LeagueOfLegendsTagLine) VALUES (:LeagueOfLegendsPlayerUniversallyUniqueIdentifier, :LeagueOfLegendsGameName, :LeagueOfLegendsTagLine)");
                $this->PDO->bind(":LeagueOfLegendsPlayerUniversallyUniqueIdentifier", $this->LeagueOfLegends->getPlayerUniversallyUniqueIdentifier());
                $this->PDO->bind(":LeagueOfLegendsGameName", $this->LeagueOfLegends->getGameName());
                $this->PDO->bind(":LeagueOfLegendsTagLine", $this->LeagueOfLegends->getTagLine());
                $this->PDO->execute();
                $this->setUsername($_SESSION['User']['username']);
                $this->PDO->query("INSERT INTO Parkinston.Accounts(AccountsLoL, AccountsUser) VALUES (:AccountsLoL, :AccountsUser)");
                $this->PDO->bind(":AccountsLoL", $this->LeagueOfLegends->getPlayerUniversallyUniqueIdentifier());
                $this->PDO->bind(":AccountsUser", $this->getUsername());
                $this->PDO->execute();
                $leagueOfLegends = array(
                    "playerUniversallyUniqueIdentifier" => $this->LeagueOfLegends->getPlayerUniversallyUniqueIdentifier(),
                    "gameName" => $this->LeagueOfLegends->getGameName(),
                    "tagLine" => $this->LeagueOfLegends->getTagLine()
                );
                $_SESSION['LeagueOfLegends'] = $leagueOfLegends;
                $account = array(
                    "LeagueOfLegends" => $_SESSION['LeagueOfLegends']
                );
                $_SESSION['Account'] = $account;
                if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/{$this->getUsername()}.json")) {
                    file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/{$this->getUsername()}.json", "");
                }
                $data = array(
                    "User" => $_SESSION['User'],
                    "Account" => $_SESSION['Account']
                );
                $cacheData = json_encode($data);
                $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/{$_SESSION['User']['username']}.json", "w");
                fwrite($cache, $cacheData);
                fclose($cache);
                $response = array(
                    "status" => 0,
                    "url" => "{$this->domain}/Users/Home/{$_SESSION['User']['username']}",
                    "message" => "Your account has been added!"
                );
            } else {
                $response = array(
                    "status" => 11,
                    "url" => "{$this->domain}/Users/Accounts/{$_SESSION['User']['username']}",
                    "message" => "This League of Legends Username does not exist!"
                );
            }
        } else {
            $response = array(
                "status" => 12,
                "url" => "{$this->domain}/Users/Accounts/{$_SESSION['User']['username']}",
                "message" => "There is an issue with the application.  Please try again later!"
            );
        }
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
}
