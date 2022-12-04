<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/User.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/LeagueOfLegends.php";
class Account extends User
{
    private int $id;
    protected LeagueOfLegends $LeagueOfLegends;
    public function __construct()
    {
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
    public function add()
    {
        $request = json_decode(file_get_contents("php://input"));
        $requestArray = json_decode(file_get_contents("php://input"), true);
        $requestKeys = array_keys($requestArray);
        if (str_contains($requestKeys[0], "lol")) {
            $this->LeagueOfLegends->retrieveData($request->lolUsername, $request->lolRegion);
        } else {
            $response = array(
                "status" => 12,
                "url" => "{$this->domain}/Users/Accounts/{$_SESSION['User']['username']}",
                "message" => "There is an issue with the application.  Please try again later!"
            );
        }

        $this->setLeagueOfLegendsGameName($request->lolUsername);
        $this->setLeagueOfLegendsRegion($request->lolRegion);
        $riotAccountApiRequest = "https://europe.api.riotgames.com/riot/account/v1/accounts/by-riot-id/" . $this->getLeagueOfLegendsGameName() . "/" . $this->getLeagueOfLegendsRegion() . "?api_key=" . Environment::RiotAPIKey;
        if ($this->getHttpResponseCode($riotAccountApiRequest) == 200) {
            $this->setUsername($_SESSION["User"]["username"]);
            $this->PDO->query("INSERT INTO Parkinston.Accounts(AccountsLoL, AccountsUser) VALUES (:AccountsLoL, :AccountsUser)");
            $this->PDO->bind(":AccountsLoL", "{$this->getLeagueOfLegendsGameName()}#{$this->getLeagueOfLegendsRegion()}");
            $this->PDO->bind(":AccountsUser", $this->getUsername());
            $this->PDO->execute();
            $account = array(
                "leagueOfLegends" => "{$this->getLeagueOfLegendsGameName()}#{$this->getLeagueOfLegendsRegion()}"
            );
            $_SESSION['Account'] = $account;
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
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
    public function getHttpResponseCode(string $requestUniformResourceLocator)
    {
        $headers = get_headers($requestUniformResourceLocator);
        return substr($headers[0], 9, 3);
    }
}
