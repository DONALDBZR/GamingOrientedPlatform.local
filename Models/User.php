<?php
require_once "{$_SERVER["DOCUMENT_ROOT"]}/Models/Mail.php";
class User extends Password
{
    private string $username;
    private string $mailAddress;
    protected Mail $Mail;
    public function __construct()
    {
        $this->PDO = new PHPDataObject();
        $this->Mail = new Mail();
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function setUsername($username)
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
    public function register()
    {
        $request = json_decode(file_get_contents('php://input'));
        $this->setUsername($request->username);
        $this->setMailAddress($request->mailAddress);
        $this->PDO->query("SELECT * FROM Parkinston.Users WHERE UsersUsername = :UsersUsername AND UsersMailAddress = :UsersMailAddress");
        $this->PDO->bind(":UsersUsername", $this->getUsername());
        $this->PDO->bind(":UsersMailAddress", $this->getMailAddress());
        $this->PDO->execute();
        if (empty($this->PDO->resultSet())) {
            $this->PDO->query("SELECT * FROM Parkinston.Passwords ORDER BY PasswordsId DESC");
            $this->PDO->execute();
            if (empty($this->PDO->resultSet() || $this->PDO->resultSet()[0]['PasswordsId'] == null)) {
                $this->setPasswordId(1);
            } else {
                $this->setPasswordId($this->PDO->resultSet()[0]['PasswordsId'] + 1);
            }
            $this->setPassword($this->generatePassword());
            $this->Mail->send($this->getMailAddress(), "Registration Complete", "Your account with username, {$this->getUsername()} and password, {$this->getPassword()} has been created.  Please consider to change it after logging in!");
            $this->setSalt($this->generateSalt());
            $this->setPassword($this->getPassword() . $this->getSalt());
            $this->setHash(password_hash($this->getPassword(), PASSWORD_ARGON2I));
            $this->PDO->query("INSERT INTO Parkinston.Passwords(PasswordsSalt, PasswordsHash) VALUES (:PasswordsSalt, :PasswordsHash)");
            $this->PDO->bind(":PasswordsSalt", $this->getSalt());
            $this->PDO->bind(":PasswordsHash", $this->getHash());
            $this->PDO->execute();
            $this->PDO->query("INSERT INTO Parkinston.Users(UsersUsername, UsersMailAddress, UsersPassword) VALUES (:UsersUsername, :UsersMailAddress, :UsersPassword)");
            $this->PDO->bind(":UsersUsername", $this->getUsername());
            $this->PDO->bind(":UsersMailAddress", $this->getMailAddress());
            $this->PDO->bind(":UsersPassword", $this->getPasswordId());
            $this->PDO->execute();
            $response = array(
                "status" => 0,
                "url" => "{$this->domain}/Login",
                "message" => "Account created!  Please check your mail to obtain your password!"
            );
            header('Content-Type: application/json', true, 200);
            echo json_encode($response);
        } else {
            $response = array(
                "status" => 2,
                "url" => "{$this->domain}/Login",
                "message" => "Account exists!"
            );
            header('Content-Type: application/json', true, 300);
            echo json_encode($response);
        }
    }
    public function login()
    {
        $request = json_decode(file_get_contents("php://input"));
        $this->PDO->query("SELECT * FROM Parkinston.Users WHERE UsersUsername = :UsersUsername");
        $this->PDO->bind(":UsersUsername", $request->username);
        $this->PDO->execute();
        if (!empty($this->PDO->resultSet())) {
            $this->setUsername($this->PDO->resultSet()[0]['UsersUsername']);
            $this->setMailAddress($this->PDO->resultSet()[0]['UsersMailAddress']);
            $this->setPasswordId($this->PDO->resultSet()[0]['UsersPassword']);
            $this->setPassword($request->password);
            $this->PDO->query("SELECT * FROM Parkinston.Passwords WHERE PasswordsId = :PasswordsId");
            $this->PDO->bind(":PasswordsId", $this->getPasswordId());
            $this->PDO->execute();
            $this->setSalt($this->PDO->resultSet()[0]['PasswordsSalt']);
            $this->setHash($this->PDO->resultSet()[0]['PasswordsHash']);
            $this->setPassword($this->getPassword() . $this->getSalt());
            if (password_verify($this->getPassword(), $this->getHash())) {
                $this->setOtp($this->otpGenerate());
                $user = array(
                    "username" => $this->getUsername(),
                    "mailAddress" => $this->getMailAddress(),
                    "domain" => $this->domain,
                    "otp" => $this->getOtp()
                );
                $_SESSION['User'] = $user;
                $this->Mail->send($this->getMailAddress(), "Verification Needed!", "Your one-time password is {$this->getOtp()}.  Please use this password to complete the log in process on {$this->domain}/Login/Verification/{$this->getUsername()}");
                $response = array(
                    "status" => 0,
                    "url" => "{$this->domain}/Login/Verification/{$this->getUsername()}",
                    "message" => "You will be redirected to the verification process just to be sure and a password has been sent to you for that! 🙏"
                );
                header('Content-Type: application/json');
                echo json_encode($response);
            } else {
                $response = array(
                    "status" => 3,
                    "url" => "{$this->domain}/Login",
                    "message" => "Your password is incorrect!"
                );
                header('Content-Type: application/json');
                echo json_encode($response);
            }
        } else {
            $response = array(
                "status" => 4,
                "url" => "{$this->domain}",
                "message" => "This account does not exist!"
            );
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }
}
