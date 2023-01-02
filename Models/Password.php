<?php
// Importing PDO
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/PDO.php";
// Importing Environment
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Environment.php";
/**
 * The API and data model for the Passwords which interacts with the application's rainbow table
 */
class Password
{
    /**
     * The ID of the record
     */
    private int $id;
    /**
     * The salt of the password
     */
    private string $salt;
    /**
     * The plain text of the password
     */
    private string $password;
    /**
     * The hash of the password
     */
    private string $hash;
    /**
     * PDO which will interact with the database server
     */
    protected PHPDataObject $PDO;
    /**
     * The one-time password needed for the user to complete the login process
     */
    private string $otp;
    /**
     * The domain of the application
     */
    public string $domain;
    public function __construct()
    {
        $this->domain = "http://{$_SERVER['HTTP_HOST']}";
        $this->PDO = new PHPDataObject();
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId(int $id)
    {
        $this->id = $id;
    }
    public function getSalt()
    {
        return $this->salt;
    }
    public function setSalt(string $salt)
    {
        $this->salt = $salt;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function setPassword(string $password)
    {
        $this->password = $password;
    }
    public function getHash()
    {
        return $this->hash;
    }
    public function setHash(string $hash)
    {
        $this->hash = $hash;
    }
    public function getOtp()
    {
        return $this->otp;
    }
    public function setOtp(string $otp)
    {
        $this->otp = $otp;
    }
    /**
     * Generating the salt that will be appended the password in its plain form before inserting it in the database
     * @return string
     */
    public function generateSalt()
    {
        $length = 8;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-*/.';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($index = 0; $index < $length; $index++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /**
     * Generating a password for the user
     * @return string
     */
    public function generatePassword()
    {
        $length = 16;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-*/.';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($index = 0; $index < $length; $index++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /**
     * Generating an one-time password for the user
     * @return string
     */
    public function otpGenerate()
    {
        $length = 6;
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($index = 0; $index < $length; $index++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /**
     * Verifying the one-time password that was sent to the user
     * @return JSON
     */
    public function otpVerify()
    {
        $request = json_decode(file_get_contents('php://input'));
        if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['User']['username']}.json")) {
            $cache = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['User']['username']}.json"));
            $this->setOtp($cache->User->otp);
            file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['User']['username']}.json", "");
        } else {
            $this->setOtp($_SESSION['User']['otp']);
        }
        if ($request->oneTimePassword == $this->getOtp()) {
            unset($_SESSION['User']['otp']);
            $data = array(
                "Client" => $_SESSION['Client'],
                "User" => $_SESSION['User'],
                "Account" => $_SESSION['Account']
            );
            $cacheData = json_encode($data);
            $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/{$_SESSION['User']['username']}.json", "w");
            fwrite($cache, $cacheData);
            fclose($cache);
            $reponse = array(
                "status" => 0,
                "url" => "{$this->domain}/Users/Home/{$_SESSION['User']['username']}",
                "message" => "You will be connected to the service as soon as possible..."
            );
        } else {
            if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['User']['username']}.json")) {
                unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['User']['username']}.json");
            }
            unset($_SESSION['User']);
            $reponse = array(
                "status" => 5,
                "url" => "{$this->domain}/",
                "message" => "The Password does not correspond to the one that was sent to you!"
            );
        }
        header('Content-Type: application/json', true, 200);
        echo json_encode($reponse);
    }
}
