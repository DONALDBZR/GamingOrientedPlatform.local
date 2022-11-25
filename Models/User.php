<?php
// Importing Mail
require_once "{$_SERVER["DOCUMENT_ROOT"]}/Models/Mail.php";
/**
 * • The class that stores all the properties that are related to the user as well as all the actions that are going to be performed in the application by any user.
 * • The class variables are set the same way as the fields in the Users table.  In fact, the class represents a record.
 */
class User extends Password
{
    /**
     * The username of the username which is also the primary key
     */
    private string $username;
    /**
     * The mail address of the user
     */
    private string $mailAddress;
    /**
     * Mail which will interact with PHPMailer
     */
    protected Mail $Mail;
    // Constructor method
    public function __construct()
    {
        // Instantiating PDO
        $this->PDO = new PHPDataObject();
        // Instantiating Mail
        $this->Mail = new Mail();
    }
    // Username accessor method
    public function getUsername()
    {
        return $this->username;
    }
    // Username mutator method
    public function setUsername($username)
    {
        $this->username = $username;
    }
    // Mail Address accessor method
    public function getMailAddress()
    {
        return $this->mailAddress;
    }
    // Mail Address mutator method
    public function setMailAddress(string $mailAddress)
    {
        $this->mailAddress = $mailAddress;
    }
    // Password.ID accessor method
    public function getPasswordId()
    {
        $this->getId();
    }
    // Password.ID mutator method
    public function setPasswordId(int $Password_id)
    {
        $this->setId($Password_id);
    }
    /**
     * 1. Checking whether the mail address or username retrieved from the JSON exists in the database.
     * 2. In the condition that the mail address or username existed, verify that the passwords retrieved are the same.
     * 3. In the condition that the passwords are actually the same, an account will be created.
     * 4. A JSON will then be generated as a response which will be sent to the front-end.
     */
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
            $this->PDO->bind(":UsersPassword", $this->getPasswordID());
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
                "status" => 1,
                "url" => "{$this->domain}/Login",
                "message" => "Account exists!"
            );
            header('Content-Type: application/json', true, 300);
            echo json_encode($response);
        }
    }
}
