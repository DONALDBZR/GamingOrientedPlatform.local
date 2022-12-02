<?php
require_once "{$_SERVER["DOCUMENT_ROOT"]}/Models/Mail.php";
class User extends Password
{
    private string $username;
    private string $mailAddress;
    protected Mail $Mail;
    private string|null $profilePicture;
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
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }
    public function setProfilePicture($profile_picture)
    {
        $this->profilePicture = $profile_picture;
    }
    public function register()
    {
        $request = json_decode(file_get_contents('php://input'));
        $this->setUsername($request->username);
        $this->setMailAddress($request->mailAddress);
        $this->PDO->query("SELECT * FROM Parkinston.Users WHERE UsersUsername = :UsersUsername OR UsersMailAddress = :UsersMailAddress");
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
            $this->setProfilePicture($this->PDO->resultSet()[0]['UsersProfilePicture']);
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
                    "profilePicture" => $this->getProfilePicture(),
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
    public function logOut()
    {
        unset($_SESSION);
        $response = array(
            "status" => 0,
            "url" => "{$this->domain}",
            "message" => "You have been successfully logged out!"
        );
        header('Content-Type: application/json', true, 200);
        echo json_encode($response);
    }
    public function forgotPassword()
    {
        $request = json_decode(file_get_contents('php://input'));
        $this->setMailAddress($request->mailAddress);
        $this->PDO->query("SELECT * FROM Parkinston.Users WHERE UsersMailAddress = :UsersMailAddress");
        $this->PDO->bind(":UsersMailAddress", $this->getMailAddress());
        $this->PDO->execute();
        if (!empty($this->PDO->resultSet())) {
            $this->setUsername($this->PDO->resultSet()[0]['UsersUsername']);
            $this->PDO->query("SELECT * FROM Parkinston.Passwords ORDER BY PasswordsId DESC");
            $this->PDO->execute();
            if (empty($this->PDO->resultSet() || $this->PDO->resultSet()[0]['PasswordsId'] == null)) {
                $this->setPasswordID(1);
            } else {
                $this->setPasswordID($this->PDO->resultSet()[0]['PasswordsId'] + 1);
            }
            $this->setPassword($this->generatePassword());
            $this->Mail->send($this->getMailAddress(), "Password Reset!", "Your new password for the account with username which is {$this->getUsername()}, is {$this->getPassword()} and please consider to change it after logging in!");
            $this->setSalt($this->generateSalt());
            $this->setPassword($this->getPassword() . $this->getSalt());
            $this->setHash(password_hash($this->getPassword(), PASSWORD_ARGON2I));
            $this->PDO->query("INSERT INTO Parkinston.Passwords(PasswordsSalt, PasswordsHash) VALUES (:PasswordsSalt, :PasswordsHash)");
            $this->PDO->bind(":PasswordsSalt", $this->getSalt());
            $this->PDO->bind(":PasswordsHash", $this->getHash());
            $this->PDO->execute();
            $this->PDO->query("UPDATE Parkinston.Users SET UsersPassword = :UsersPassword WHERE UsersMailAddress = :UsersMailAddress");
            $this->PDO->bind(":UsersPassword", $this->getPasswordID());
            $this->PDO->bind(":UsersMailAddress", $this->getMailAddress());
            $this->PDO->execute();
            $response = array(
                "status" => 0,
                "url" => "{$this->domain}/Login",
                "message" => "Password Reset!  Please check your mail to obtain your new password!"
            );
            header('Content-Type: application/json', true, 200);
            echo json_encode($response);
        } else {
            $response = array(
                "status" => 6,
                "url" => "{$this->domain}",
                "message" => "There is no account that is linked to this mail address!"
            );
            header('Content-Type: application/json', true, 300);
            echo json_encode($response);
        }
    }
    public function changeProfilePicture()
    {
        $this->setUsername($_SESSION['User']['username']);
        $imageDirectory = "/Public/Images/ProfilePictures/";
        $imageFile = $imageDirectory . $this->getUsername() . "." . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $uploadedPath = $_SERVER['DOCUMENT_ROOT'] . $imageFile;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadedPath)) {
            $this->setProfilePicture($imageFile);
            $this->PDO->query("UPDATE Parkinston.Users SET UsersProfilePicture = :UsersProfilePicture WHERE UsersUsername = :UsersUsername");
            $this->PDO->bind(":UsersProfilePicture", $this->getProfilePicture());
            $this->PDO->bind(":UsersUsername", $this->getUsername());
            $this->PDO->execute();
            $_SESSION['User']['profilePicture'] = $this->getProfilePicture();
            $response = array(
                "status" => 0,
                "url" => "{$this->domain}/Users/Profile/{$this->getUsername()}",
                "message" => "Your profile picture has been changed!"
            );
            header('Content-Type: application/json', true, 200);
            echo json_encode($response);
        }
    }
    public function changePassword()
    {
        $request = json_decode(file_get_contents("php://input"));
        $this->setUsername($_SESSION['User']['username']);
        $this->setMailAddress($_SESSION['User']['mailAddress']);
        $this->setPassword($request->oldPassword);
        $this->PDO->query("SELECT * FROM Parkinston.Users WHERE UsersUsername = :UsersUsername");
        $this->PDO->bind(":UsersUsername", $this->getUsername());
        $this->PDO->execute();
        $this->setPasswordId($this->PDO->resultSet()[0]['UsersPassword']);
        $this->PDO->query("SELECT * FROM Parkinston.Passwords WHERE PasswordsId = :PasswordsId");
        $this->PDO->bind(":PasswordsId", $this->getPasswordId());
        $this->PDO->execute();
        $this->setSalt($this->PDO->resultSet()[0]['PasswordsSalt']);
        $this->setHash($this->PDO->resultSet()[0]['PasswordsHash']);
        $this->setPassword($this->getPassword() . $this->getSalt());
        if (password_verify($this->getPassword(), $this->getHash())) {
            if ($request->newPassword == $request->confirmNewPassword) {
                $this->setPassword($request->newPassword);
                $this->Mail->send($this->getMailAddress(), "Password Changed!", "You have just changed your password and the new one is {$this->getPassword()}.  If, you have not made that change, consider into resetting the password on this link: {$this->domain}/ForgotPassword");
                $this->PDO->query("SELECT * FROM Parkinston.Passwords ORDER BY PasswordsId DESC");
                $this->PDO->execute();
                if (empty($this->PDO->resultSet()) || $this->PDO->resultSet()[0]['PasswordsId'] == null) {
                    $this->setPasswordId(1);
                } else {
                    $this->setPasswordId($this->PDO->resultSet()[0]['PasswordsId'] + 1);
                }
                $this->setSalt($this->generateSalt());
                $this->setPassword($this->getPassword() . $this->getSalt());
                $this->setHash(password_hash($this->getPassword(), PASSWORD_ARGON2I));
                $this->PDO->query("INSERT INTO Parkinston.Passwords(PasswordsSalt, PasswordsHash) VALUES (:PasswordsSalt, :PasswordsHash)");
                $this->PDO->bind(":PasswordsSalt", $this->getSalt());
                $this->PDO->bind(":PasswordsHash", $this->getHash());
                $this->PDO->execute();
                $this->PDO->query("UPDATE Parkinston.Users SET UsersPassword = :UsersPassword WHERE UsersUsername = :UsersUsername");
                $this->PDO->bind(":UsersPassword", $this->getPasswordId());
                $this->PDO->bind(":UsersUsername", $this->getUsername());
                $this->PDO->execute();
                $response = array(
                    "status" => 0,
                    "url" => "{$this->domain}/Sign-Out",
                    "message" => "Your password has been changed!  You will be logged out of your account to test the new password!"
                );
                header('Content-Type: application/json', true, 300);
                echo json_encode($response);
            } else {
                $response = array(
                    "status" => 8,
                    "url" => "{$this->domain}/Users/Security/{$this->getUsername()}",
                    "message" => "The passwords are not identical!"
                );
                header('Content-Type: application/json', true, 300);
                echo json_encode($response);
            }
        } else {
            $response = array(
                "status" => 7,
                "url" => "{$this->domain}/Users/Security/{$this->getUsername()}",
                "message" => "Incorrect Password!"
            );
            header('Content-Type: application/json', true, 300);
            echo json_encode($response);
        }
    }
    public function changeMailAddress()
    {
        $request = json_decode(file_get_contents("php://input"));
        $this->setUsername($_SESSION['User']['username']);
        $this->setMailAddress($_SESSION['User']['mailAddress']);
        if ($this->getMailAddress() != $request->mailAddress) {
            $this->Mail->send($this->getMailAddress(), "Mail Address Changed!", "You have just changed your mail address from {$this->getMailAddress()} to {$request->mailAddress}.  You will receive an update on you new mail address as well.  If, you have not made that change, consider into changing your mail address and password as soon as you logged in!");
            $this->setMailAddress($request->mailAddress);
            $this->PDO->query("UPDATE Parkinston.Users SET UsersMailAddress = :UsersMailAddress WHERE UsersUsername = :UsersUsername");
            $this->PDO->bind(":UsersMailAddress", $this->getMailAddress());
            $this->PDO->bind(":UsersUsername", $this->getUsername());
            $this->PDO->execute();
            $this->Mail->send($this->getMailAddress(), "Mail Address Changed!", "This mail is an update for your new mail address which username is {$this->getUsername()}.  If, you have not made that change, consider into changing your mail address and password as soon as you logged in!");
            $response = array(
                "status" => 0,
                "url" => "{$this->domain}/Sign-Out",
                "message" => "Your mail address has been changed!  You will be logged out of your account!"
            );
            header('Content-Type: application/json', true, 300);
            echo json_encode($response);
        }
    }
    public function changePasswordAndMailAddress()
    {
        $request = json_decode(file_get_contents("php://input"));
        $this->setUsername($_SESSION['User']['username']);
        $this->setMailAddress($_SESSION['User']['mailAddress']);
        if ($this->getMailAddress() != $request->mailAddress) {
            $this->Mail->send($this->getMailAddress(), "Mail Address Changed!", "You have just changed your mail address from {$this->getMailAddress()} to {$request->mailAddress}.  You will receive an update on you new mail address as well.  If, you have not made that change, consider into changing your mail address and password as soon as you logged in!");
            $this->setMailAddress($request->mailAddress);
            $this->PDO->query("UPDATE Parkinston.Users SET UsersMailAddress = :UsersMailAddress WHERE UsersUsername = :UsersUsername");
            $this->PDO->bind(":UsersMailAddress", $this->getMailAddress());
            $this->PDO->bind(":UsersUsername", $this->getUsername());
            $this->PDO->execute();
            $this->Mail->send($this->getMailAddress(), "Mail Address Changed!", "This mail is an update for your new mail address which username is {$this->getUsername()}.  If, you have not made that change, consider into changing your mail address and password as soon as you logged in!");
            $this->setPassword($request->oldPassword);
            $this->PDO->query("SELECT * FROM Parkinston.Users WHERE UsersUsername = :UsersUsername");
            $this->PDO->bind(":UsersUsername", $this->getUsername());
            $this->PDO->execute();
            $this->setPasswordId($this->PDO->resultSet()[0]['UsersPassword']);
            $this->PDO->query("SELECT * FROM Parkinston.Passwords WHERE PasswordsId = :PasswordsId");
            $this->PDO->bind(":PasswordsId", $this->getPasswordId());
            $this->PDO->execute();
            $this->setSalt($this->PDO->resultSet()[0]['PasswordsSalt']);
            $this->setHash($this->PDO->resultSet()[0]['PasswordsHash']);
            $this->setPassword($this->getPassword() . $this->getSalt());
            if (password_verify($this->getPassword(), $this->getHash())) {
                if ($request->newPassword == $request->confirmNewPassword) {
                    $this->setPassword($request->newPassword);
                    $this->Mail->send($this->getMailAddress(), "Password Changed!", "You have just changed your password and the new one is {$this->getPassword()}.  If, you have not made that change, consider into resetting the password on this link: {$this->domain}/ForgotPassword");
                    $this->PDO->query("SELECT * FROM Parkinston.Passwords ORDER BY PasswordsId DESC");
                    $this->PDO->execute();
                    if (empty($this->PDO->resultSet()) || $this->PDO->resultSet()[0]['PasswordsId'] == null) {
                        $this->setPasswordId(1);
                    } else {
                        $this->setPasswordId($this->PDO->resultSet()[0]['PasswordsId'] + 1);
                    }
                    $this->setSalt($this->generateSalt());
                    $this->setPassword($this->getPassword() . $this->getSalt());
                    $this->setHash(password_hash($this->getPassword(), PASSWORD_ARGON2I));
                    $this->PDO->query("INSERT INTO Parkinston.Passwords(PasswordsSalt, PasswordsHash) VALUES (:PasswordsSalt, :PasswordsHash)");
                    $this->PDO->bind(":PasswordsSalt", $this->getSalt());
                    $this->PDO->bind(":PasswordsHash", $this->getHash());
                    $this->PDO->execute();
                    $this->PDO->query("UPDATE Parkinston.Users SET UsersPassword = :UsersPassword WHERE UsersUsername = :UsersUsername");
                    $this->PDO->bind(":UsersPassword", $this->getPasswordId());
                    $this->PDO->bind(":UsersUsername", $this->getUsername());
                    $this->PDO->execute();
                    $response = array(
                        "status" => 0,
                        "url" => "{$this->domain}/Sign-Out",
                        "message" => "Your password has been changed!  You will be logged out of your account to test the new password!"
                    );
                    header('Content-Type: application/json', true, 300);
                    echo json_encode($response);
                } else {
                    $response = array(
                        "status" => 10,
                        "url" => "{$this->domain}/Sign-Out",
                        "message" => "The passwords are not identical!"
                    );
                    header('Content-Type: application/json', true, 300);
                    echo json_encode($response);
                }
            } else {
                $response = array(
                    "status" => 9,
                    "url" => "{$this->domain}/Sign-Out",
                    "message" => "Incorrect Password!"
                );
                header('Content-Type: application/json', true, 300);
                echo json_encode($response);
            }
        }
    }
}
