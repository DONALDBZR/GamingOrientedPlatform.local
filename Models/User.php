<?php
// Importing Mail
require_once "{$_SERVER["DOCUMENT_ROOT"]}/Models/Mail.php";
/**
 * The API and data model for the users
 */
class User extends Password
{
    /**
     * Username of the user which is also the primary key
     */
    private string $username;
    /**
     * Mail address of the user which allows the application to communicate with the user
     */
    private string $mailAddress;
    /**
     * Profile picture of the user
     */
    private ?string $profilePicture;
    /**
     * Simplifying the use of PHPMailer
     */
    protected Mail $Mail;
    public function __construct()
    {
        $this->PDO = new PHPDataObject();
        $this->Mail = new Mail();
        $this->domain = $_SERVER['HTTP_HOST'];
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function setUsername(string $username)
    {
        $this->username = $username;
    }
    public function getMailAddress()
    {
        return $this->mailAddress;
    }
    public function setMailAddress(string $mailAddress)
    {
        $this->mailAddress = $mailAddress;
    }
    public function getPasswordId()
    {
        return $this->getId();
    }
    public function setPasswordId(int $Password_id)
    {
        $this->setId($Password_id);
    }
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }
    public function setProfilePicture(?string $profile_picture)
    {
        $this->profilePicture = $profile_picture;
    }
    /**
     * Registering the user
     * @return JSON
     */
    public function register()
    {
        $request = json_decode(file_get_contents('php://input'));
        $this->setUsername($request->username);
        $this->setMailAddress($request->mailAddress);
        if (!is_null($this->getUsername()) && !is_null($this->getMailAddress())) {
            $this->PDO->query("SELECT * FROM Users WHERE UsersUsername = :UsersUsername OR UsersMailAddress = :UsersMailAddress");
            $this->PDO->bind(":UsersUsername", $this->getUsername());
            $this->PDO->bind(":UsersMailAddress", $this->getMailAddress());
            $this->PDO->execute();
            if (empty($this->PDO->resultSet())) {
                $this->PDO->query("SELECT * FROM Passwords ORDER BY PasswordsId DESC");
                $this->PDO->execute();
                if (empty($this->PDO->resultSet() || $this->PDO->resultSet()[0]['PasswordsId'] == null)) {
                    $this->setPasswordId(1);
                } else {
                    $this->setPasswordId($this->PDO->resultSet()[0]['PasswordsId'] + 1);
                }
                $this->setPassword($this->generator("password"));
                $this->Mail->send($this->getMailAddress(), "Registration Complete", "Your account with username, {$this->getUsername()} and password, {$this->getPassword()} has been created.  Please consider to change it after logging in!");
                $this->setSalt($this->generator("salt"));
                $this->setPassword($this->getPassword() . $this->getSalt());
                $this->setHash(password_hash($this->getPassword(), PASSWORD_ARGON2I));
                $this->PDO->query("INSERT INTO Passwords(PasswordsSalt, PasswordsHash) VALUES (:PasswordsSalt, :PasswordsHash)");
                $this->PDO->bind(":PasswordsSalt", $this->getSalt());
                $this->PDO->bind(":PasswordsHash", $this->getHash());
                $this->PDO->execute();
                $this->PDO->query("INSERT INTO Users(UsersUsername, UsersMailAddress, UsersPassword) VALUES (:UsersUsername, :UsersMailAddress, :UsersPassword)");
                $this->PDO->bind(":UsersUsername", $this->getUsername());
                $this->PDO->bind(":UsersMailAddress", $this->getMailAddress());
                $this->PDO->bind(":UsersPassword", $this->getPasswordId());
                $this->PDO->execute();
                $response = array(
                    "status" => 0,
                    "url" => "/Login",
                    "message" => "Account created!  Please check your mail to obtain your password!"
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 200
                );
            } else {
                $response = array(
                    "status" => 2,
                    "url" => "/Login",
                    "message" => "Account exists!"
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 400
                );
            }
        } else {
            $response = array(
                "status" => 1,
                "url" => "/Register",
                "message" => "Invalid Form!"
            );
            $headers = array(
                "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                "replace" => true,
                "responseCode" => 400
            );
        }
        header($headers["headers"], $headers["replace"], $headers["responseCode"]);
        echo json_encode($response);
    }
    /**
     * Allow the user to have access to the application
     * @return JSON
     */
    public function login()
    {
        $request = json_decode(file_get_contents("php://input"));
        if (!is_null($request->username) && !is_null($request->password)) {
            $this->PDO->query("SELECT * FROM Users WHERE UsersUsername = :UsersUsername");
            $this->PDO->bind(":UsersUsername", $request->username);
            $this->PDO->execute();
            if (!empty($this->PDO->resultSet())) {
                $this->setUsername($this->PDO->resultSet()[0]['UsersUsername']);
                $this->setMailAddress($this->PDO->resultSet()[0]['UsersMailAddress']);
                $this->setProfilePicture($this->PDO->resultSet()[0]['UsersProfilePicture']);
                $this->setPasswordId($this->PDO->resultSet()[0]['UsersPassword']);
                $this->setPassword($request->password);
                $this->PDO->query("SELECT * FROM Passwords WHERE PasswordsId = :PasswordsId");
                $this->PDO->bind(":PasswordsId", $this->getPasswordId());
                $this->PDO->execute();
                $this->setSalt($this->PDO->resultSet()[0]['PasswordsSalt']);
                $this->setHash($this->PDO->resultSet()[0]['PasswordsHash']);
                $this->setPassword($this->getPassword() . $this->getSalt());
                if (password_verify($this->getPassword(), $this->getHash())) {
                    $this->setOtp($this->generator("otp"));
                    $user = array(
                        "username" => $this->getUsername(),
                        "mailAddress" => $this->getMailAddress(),
                        "profilePicture" => $this->getProfilePicture(),
                        "otp" => $this->getOtp()
                    );
                    $_SESSION['User'] = $user;
                    $this->PDO->query("SELECT * FROM LeagueOfLegends WHERE LeagueOfLegendsPlayerUniversallyUniqueIdentifier = (SELECT AccountsLoL FROM Accounts WHERE AccountsUser = :AccountsUser)");
                    $this->PDO->bind(":AccountsUser", $this->getUsername());
                    $this->PDO->execute();
                    if (empty($this->PDO->resultSet())) {
                        $leagueOfLegends = array(
                            "playerUniversallyUniqueIdentifier" => null,
                            "gameName" => null,
                            "tagLine" => null
                        );
                    } else {
                        $leagueOfLegends = array(
                            "playerUniversallyUniqueIdentifier" => $this->PDO->resultSet()[0]['LeagueOfLegendsPlayerUniversallyUniqueIdentifier'],
                            "gameName" => $this->PDO->resultSet()[0]['LeagueOfLegendsGameName'],
                            "tagLine" => $this->PDO->resultSet()[0]['LeagueOfLegendsTagLine']
                        );
                    }
                    $_SESSION['LeagueOfLegends'] = $leagueOfLegends;
                    $account = array(
                        "LeagueOfLegends" => $_SESSION['LeagueOfLegends']
                    );
                    $_SESSION['Account'] = $account;
                    $this->Mail->send($this->getMailAddress(), "Verification Needed!", "Your one-time password is {$this->getOtp()}.  Please use this password to complete the log in process on http://{$_SERVER['HTTP_HOST']}/Login/Verification/{$this->getUsername()}");
                    $data = array(
                        "Client" => $_SESSION['Client'],
                        "User" => $_SESSION['User'],
                        "Account" => $_SESSION['Account']
                    );
                    $cacheData = json_encode($data);
                    $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/{$this->getUsername()}.json", "w");
                    fwrite($cache, $cacheData);
                    fclose($cache);
                    $response = array(
                        "status" => 0,
                        "url" => "/Login/Verification/{$this->getUsername()}",
                        "message" => "You will be redirected to the verification process just to be sure and a password has been sent to you for that! 🙏"
                    );
                    $headers = array(
                        "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                        "replace" => true,
                        "responseCode" => 200
                    );
                } else {
                    $response = array(
                        "status" => 3,
                        "url" => "/Login",
                        "message" => "Your password is incorrect!"
                    );
                    $headers = array(
                        "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                        "replace" => true,
                        "responseCode" => 300
                    );
                }
            } else {
                $response = array(
                    "status" => 4,
                    "url" => $this->domain,
                    "message" => "This account does not exist!"
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 400
                );
            }
        } else {
            $response = array(
                "status" => 1,
                "url" => "/Login",
                "message" => "Invalid Form!"
            );
            $headers = array(
                "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                "replace" => true,
                "responseCode" => 300
            );
        }
        header($headers["headers"], $headers["replace"], $headers["responseCode"]);
        echo json_encode($response);
    }
    /**
     * Signing out the user and clearing server's cache data
     * @return JSON
     */
    public function logOut()
    {
        if (isset($_SESSION)) {
            if (isset($_SESSION['Account']['LeagueOfLegends']) && isset($_SESSION['User']) && file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Profiles/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json") || file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Match Histories/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json") || file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Champion Masteries/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json") || file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/{$_SESSION['User']['username']}.json")) {
                unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Profiles/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json");
                unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Match Histories/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json");
                unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Riot Games/Users/Champion Masteries/{$_SESSION['Account']['LeagueOfLegends']['playerUniversallyUniqueIdentifier']}.json");
                unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/{$_SESSION['User']['username']}.json");
                session_unset();
                session_destroy();
                $response = array(
                    "status" => 0,
                    "url" => "/",
                    "message" => "You have been successfully logged out!"
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 200
                );
            } else {
                session_unset();
                session_destroy();
                $response = array(
                    "status" => 13,
                    "url" => "/",
                    "message" => "You have been successfully logged out but the cache has not been cleared on the application server!"
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 300
                );
            }
        } else {
            $response = array(
                "status" => 13,
                "url" => $this->domain,
                "message" => "You have been successfully logged out but the cache has not been cleared on the application server!"
            );
            $headers = array(
                "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                "replace" => true,
                "responseCode" => 300
            );
        }
        header($headers["headers"], $headers["replace"], $headers["responseCode"]);
        echo json_encode($response);
    }
    /**
     * Resetting the password of the user
     * @return JSON
     */
    public function forgotPassword()
    {
        $request = json_decode(file_get_contents('php://input'));
        $this->setMailAddress($request->mailAddress);
        if (!is_null($this->getMailAddress())) {
            $this->PDO->query("SELECT * FROM Users WHERE UsersMailAddress = :UsersMailAddress");
            $this->PDO->bind(":UsersMailAddress", $this->getMailAddress());
            $this->PDO->execute();
            if (!empty($this->PDO->resultSet())) {
                $this->setUsername($this->PDO->resultSet()[0]['UsersUsername']);
                $this->PDO->query("SELECT * FROM Passwords ORDER BY PasswordsId DESC");
                $this->PDO->execute();
                if (empty($this->PDO->resultSet() || $this->PDO->resultSet()[0]['PasswordsId'] == null)) {
                    $this->setPasswordId(1);
                } else {
                    $this->setPasswordId($this->PDO->resultSet()[0]['PasswordsId'] + 1);
                }
                $this->setPassword($this->generator("password"));
                $this->Mail->send($this->getMailAddress(), "Password Reset!", "Your new password for the account with username which is {$this->getUsername()}, is {$this->getPassword()} and please consider to change it after logging in!");
                $this->setSalt($this->generator("salt"));
                $this->setPassword($this->getPassword() . $this->getSalt());
                $this->setHash(password_hash($this->getPassword(), PASSWORD_ARGON2I));
                $this->PDO->query("INSERT INTO Passwords(PasswordsSalt, PasswordsHash) VALUES (:PasswordsSalt, :PasswordsHash)");
                $this->PDO->bind(":PasswordsSalt", $this->getSalt());
                $this->PDO->bind(":PasswordsHash", $this->getHash());
                $this->PDO->execute();
                $this->PDO->query("UPDATE Users SET UsersPassword = :UsersPassword WHERE UsersMailAddress = :UsersMailAddress");
                $this->PDO->bind(":UsersPassword", $this->getPasswordID());
                $this->PDO->bind(":UsersMailAddress", $this->getMailAddress());
                $this->PDO->execute();
                $response = array(
                    "status" => 0,
                    "url" => "/Login",
                    "message" => "Password Reset!  Please check your mail to obtain your new password!"
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 200
                );
            } else {
                $response = array(
                    "status" => 6,
                    "url" => $this->domain,
                    "message" => "There is no account that is linked to this mail address!"
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 400
                );
            }
        } else {
            $response = array(
                "status" => 1,
                "url" => "/ForgotPassword",
                "message" => "Invalid Form!"
            );
            $headers = array(
                "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                "replace" => true,
                "responseCode" => 400
            );
        }
        header($headers["headers"], $headers["replace"], $headers["responseCode"]);
        echo json_encode($response);
    }
    /**
     * Changing the profile picture
     * @return JSON
     */
    public function changeProfilePicture()
    {
        $this->setUsername($_SESSION['User']['username']);
        $imageDirectory = "/Public/Images/ProfilePictures/";
        $imageFile = $imageDirectory . $this->getUsername() . "." . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $uploadedPath = $_SERVER['DOCUMENT_ROOT'] . $imageFile;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadedPath)) {
            $this->setProfilePicture($imageFile);
            $this->PDO->query("UPDATE Users SET UsersProfilePicture = :UsersProfilePicture WHERE UsersUsername = :UsersUsername");
            $this->PDO->bind(":UsersProfilePicture", $this->getProfilePicture());
            $this->PDO->bind(":UsersUsername", $this->getUsername());
            $this->PDO->execute();
            if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$this->getUsername()}.json")) {
                file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$this->getUsername()}.json", "");
            }
            $_SESSION['User']['profilePicture'] = $this->getProfilePicture();
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
                "url" => "http://{$_SERVER['HTTP_HOST']}/Users/Profile/{$this->getUsername()}",
                "message" => "Your profile picture has been changed!"
            );
            $headers = array(
                "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                "replace" => true,
                "responseCode" => 200
            );
            header($headers["headers"], $headers["replace"], $headers["responseCode"]);
            echo json_encode($response);
        }
    }
    /**
     * Changing both the mail address and the password
     * @return JSON
     */
    public function changePasswordAndMailAddress()
    {
        $request = json_decode(file_get_contents("php://input"));
        $this->setUsername($_SESSION['User']['username']);
        $this->setMailAddress($_SESSION['User']['mailAddress']);
        $this->setPassword($request->oldPassword);
        if (!empty($request)) {
            if (!is_null($request->mailAddress) && !is_null($request->oldPassword)) {
                if ($this->getMailAddress() != $request->mailAddress) {
                    $this->Mail->send($this->getMailAddress(), "Mail Address Changed!", "You have just changed your mail address from {$this->getMailAddress()} to {$request->mailAddress}.  You will receive an update on you new mail address as well.  If, you have not made that change, consider into changing your mail address and password as soon as you logged in!");
                    $this->setMailAddress($request->mailAddress);
                    $this->PDO->query("UPDATE Users SET UsersMailAddress = :UsersMailAddress WHERE UsersUsername = :UsersUsername");
                    $this->PDO->bind(":UsersMailAddress", $this->getMailAddress());
                    $this->PDO->bind(":UsersUsername", $this->getUsername());
                    $this->PDO->execute();
                    $this->Mail->send($this->getMailAddress(), "Mail Address Changed!", "This mail is an update for your new mail address which username is {$this->getUsername()}.  If, you have not made that change, consider into changing your mail address and password as soon as you logged in!");
                    $this->PDO->query("SELECT * FROM Passwords WHERE PasswordsId = SELECT UsersPassword FROM Users WHERE UsersUsername = :UsersUsername");
                    $this->PDO->bind(":UsersUsername", $this->getUsername());
                    $this->PDO->execute();
                    $this->setSalt($this->PDO->resultSet()[0]['PasswordsSalt']);
                    $this->setHash($this->PDO->resultSet()[0]['PasswordsHash']);
                    $this->setPassword($this->getPassword() . $this->getSalt());
                    if (password_verify($this->getPassword(), $this->getHash())) {
                        if ($request->newPassword == $request->confirmNewPassword) {
                            $this->setPassword($request->newPassword);
                            $this->Mail->send($this->getMailAddress(), "Password Changed!", "You have just changed your password and the new one is {$this->getPassword()}.  If, you have not made that change, consider into resetting the password on this link: http:///ForgotPassword");
                            $this->PDO->query("SELECT * FROM Passwords ORDER BY PasswordsId DESC");
                            $this->PDO->execute();
                            if (empty($this->PDO->resultSet()) || $this->PDO->resultSet()[0]['PasswordsId'] == null) {
                                $this->setPasswordId(1);
                            } else {
                                $this->setPasswordId($this->PDO->resultSet()[0]['PasswordsId'] + 1);
                            }
                            $this->setSalt($this->generator("salt"));
                            $this->setPassword($this->getPassword() . $this->getSalt());
                            $this->setHash(password_hash($this->getPassword(), PASSWORD_ARGON2I));
                            $this->PDO->query("INSERT INTO Passwords(PasswordsSalt, PasswordsHash) VALUES (:PasswordsSalt, :PasswordsHash)");
                            $this->PDO->bind(":PasswordsSalt", $this->getSalt());
                            $this->PDO->bind(":PasswordsHash", $this->getHash());
                            $this->PDO->execute();
                            $this->PDO->query("UPDATE Users SET UsersPassword = :UsersPassword WHERE UsersUsername = :UsersUsername");
                            $this->PDO->bind(":UsersPassword", $this->getPasswordId());
                            $this->PDO->bind(":UsersUsername", $this->getUsername());
                            $this->PDO->execute();
                            $response = array(
                                "status" => 0,
                                "url" => "/Sign-Out",
                                "message" => "Your mail address and password has been changed!  You will be logged out of your account to test the new password!"
                            );
                            $headers = array(
                                "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                                "replace" => true,
                                "responseCode" => 200
                            );
                        } else {
                            $response = array(
                                "status" => 9,
                                "url" => "/Users/Security/{$this->getUsername()}",
                                "message" => "The passwords are not identical!"
                            );
                            $headers = array(
                                "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                                "replace" => true,
                                "responseCode" => 300
                            );
                        }
                    } else {
                        $response = array(
                            "status" => 8,
                            "url" => "/Users/Security/{$this->getUsername()}",
                            "message" => "Incorrect Password!"
                        );
                        $headers = array(
                            "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                            "replace" => true,
                            "responseCode" => 300
                        );
                    }
                } else {
                    $response = array(
                        "status" => 7,
                        "url" => "/Users/Security/{$this->getUsername()}",
                        "message" => "You need a different mail address to change your mail address!"
                    );
                    $headers = array(
                        "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                        "replace" => true,
                        "responseCode" => 300
                    );
                }
            } else if (!is_null($request->mailAddress) && is_null($request->oldPassword)) {
                if ($this->getMailAddress() != $request->mailAddress) {
                    $this->Mail->send($this->getMailAddress(), "Mail Address Changed!", "You have just changed your mail address from {$this->getMailAddress()} to {$request->mailAddress}.  You will receive an update on you new mail address as well.  If, you have not made that change, consider into changing your mail address and password as soon as you logged in!");
                    $this->setMailAddress($request->mailAddress);
                    $this->PDO->query("UPDATE Users SET UsersMailAddress = :UsersMailAddress WHERE UsersUsername = :UsersUsername");
                    $this->PDO->bind(":UsersMailAddress", $this->getMailAddress());
                    $this->PDO->bind(":UsersUsername", $this->getUsername());
                    $this->PDO->execute();
                    $this->Mail->send($this->getMailAddress(), "Mail Address Changed!", "This mail is an update for your new mail address which username is {$this->getUsername()}.  If, you have not made that change, consider into changing your mail address and password as soon as you logged in!");
                    $response = array(
                        "status" => 0,
                        "url" => "/Sign-Out",
                        "message" => "Your mail address has been changed!  You will be logged out of your account!"
                    );
                    $headers = array(
                        "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                        "replace" => true,
                        "responseCode" => 200
                    );
                } else {
                    $response = array(
                        "status" => 7,
                        "url" => "/Users/Security/{$this->getUsername()}",
                        "message" => "You need a different mail address to change your mail address!"
                    );
                    $headers = array(
                        "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                        "replace" => true,
                        "responseCode" => 300
                    );
                }
            } else if (is_null($request->mailAddress) && !is_null($request->oldPassword)) {
                $this->PDO->query("SELECT * FROM Passwords WHERE PasswordsId = SELECT UsersPassword FROM Users WHERE UsersUsername = :UsersUsername");
                $this->PDO->bind(":UsersUsername", $this->getUsername());
                $this->PDO->execute();
                $this->setSalt($this->PDO->resultSet()[0]['PasswordsSalt']);
                $this->setHash($this->PDO->resultSet()[0]['PasswordsHash']);
                $this->setPassword($this->getPassword() . $this->getSalt());
                if (password_verify($this->getPassword(), $this->getHash())) {
                    if ($request->newPassword == $request->confirmNewPassword) {
                        $this->setPassword($request->newPassword);
                        $this->Mail->send($this->getMailAddress(), "Password Changed!", "You have just changed your password and the new one is {$this->getPassword()}.  If, you have not made that change, consider into resetting the password on this link: http:///ForgotPassword");
                        $this->PDO->query("SELECT * FROM Passwords ORDER BY PasswordsId DESC");
                        $this->PDO->execute();
                        if (empty($this->PDO->resultSet()) || $this->PDO->resultSet()[0]['PasswordsId'] == null) {
                            $this->setPasswordId(1);
                        } else {
                            $this->setPasswordId($this->PDO->resultSet()[0]['PasswordsId'] + 1);
                        }
                        $this->setSalt($this->generator("salt"));
                        $this->setPassword($this->getPassword() . $this->getSalt());
                        $this->setHash(password_hash($this->getPassword(), PASSWORD_ARGON2I));
                        $this->PDO->query("INSERT INTO Passwords(PasswordsSalt, PasswordsHash) VALUES (:PasswordsSalt, :PasswordsHash)");
                        $this->PDO->bind(":PasswordsSalt", $this->getSalt());
                        $this->PDO->bind(":PasswordsHash", $this->getHash());
                        $this->PDO->execute();
                        $this->PDO->query("UPDATE Users SET UsersPassword = :UsersPassword WHERE UsersUsername = :UsersUsername");
                        $this->PDO->bind(":UsersPassword", $this->getPasswordId());
                        $this->PDO->bind(":UsersUsername", $this->getUsername());
                        $this->PDO->execute();
                        $response = array(
                            "status" => 0,
                            "url" => "/Sign-Out",
                            "message" => "Your password has been changed!  You will be logged out of your account to test the new password!"
                        );
                        $headers = array(
                            "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                            "replace" => true,
                            "responseCode" => 200
                        );
                    } else {
                        $response = array(
                            "status" => 9,
                            "url" => "/Users/Security/{$this->getUsername()}",
                            "message" => "The passwords are not identical!"
                        );
                        $headers = array(
                            "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                            "replace" => true,
                            "responseCode" => 300
                        );
                    }
                } else {
                    $response = array(
                        "status" => 8,
                        "url" => "/Users/Security/{$this->getUsername()}",
                        "message" => "Incorrect Password!"
                    );
                    $headers = array(
                        "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                        "replace" => true,
                        "responseCode" => 300
                    );
                }
            } else {
                $response = array(
                    "status" => 1,
                    "url" => "/Users/Security/{$this->getUsername()}",
                    "message" => "Invalid Form!"
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 300
                );
            }
        } else {
            $response = array(
                "status" => 1,
                "url" => "/Users/Security/{$this->getUsername()}",
                "message" => "Invalid Form!"
            );
            $headers = array(
                "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                "replace" => true,
                "responseCode" => 300
            );
        }
        header($headers["headers"], $headers["replace"], $headers["responseCode"]);
        echo json_encode($response);
    }
}
