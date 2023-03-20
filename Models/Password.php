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
    public function getId(): int
    {
        return $this->id;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function getSalt(): string
    {
        return $this->salt;
    }
    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
    public function getHash(): string
    {
        return $this->hash;
    }
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }
    public function getOtp(): string
    {
        return $this->otp;
    }
    public function setOtp(string $otp): void
    {
        $this->otp = $otp;
    }
    /**
     * Generating either a string or an integer
     */
    public function generator(string $parameter): string
    {
        $length = 0;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-*/.';
        $charactersLength = strlen($characters);
        $randomString = '';
        $response = "";
        switch ($parameter) {
            case 'salt':
                $length = 8;
                for ($index = 0; $index < $length; $index++) {
                    $randomString .= $characters[random_int(0, $charactersLength - 1)];
                }
                $response = $randomString;
                break;
            case 'password':
                $length = 16;
                for ($index = 0; $index < $length; $index++) {
                    $randomString .= $characters[random_int(0, $charactersLength - 1)];
                }
                $response = $randomString;
                break;
            case 'otp':
                $length = 6;
                $characters = '0123456789';
                for ($index = 0; $index < $length; $index++) {
                    $randomString .= $characters[random_int(0, strlen($characters) - 1)];
                }
                $response = $randomString;
                break;
        }
        return $response;
    }
    /**
     * Verifying the one-time password that was sent to the user
     */
    public function otpVerify(): void
    {
        $directory = "{$_SERVER['DOCUMENT_ROOT']}/Cache/Passwords/";
        $files = array_values(array_diff(scandir($directory), array(".", "..")));
        $otpVerifyFiles = array();
        for ($index = 0; $index < count($files); $index++) {
            $file = $files[$index];
            $fileData = json_decode(file_get_contents("{$directory}{$files[$index]}"));
            if ($fileData->requestMethod == "POST" && $fileData->route == "/Login/{$_SESSION['User']['username']}") {
                $otpVerifyFile = array(
                    "name" => $file,
                    "data" => $fileData
                );
                array_push($otpVerifyFiles, $otpVerifyFile);
            }
        }
        if (count($otpVerifyFiles) != 0) {
            if (count($otpVerifyFiles) == 1) {
                $file = $otpVerifyFiles[0];
                $name = $file["name"];
                $request = $file["data"]->Data;
            } else {
                rsort($otpVerifyFiles);
                $file = $otpVerifyFiles[0];
                $name = $file["name"];
                $request = $file["data"]->Data;
            }
        } else {
            $response = array(
                "status" => 3,
                "url" => "/Login/Verification/{$_SESSION['User']['username']}",
                "message" => "Form data not found"
            );
            $headers = array(
                "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                "replace" => true,
                "responseCode" => 404
            );
        }
        unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/Passwords/{$name}");
        if (!is_null($request->oneTimePassword)) {
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
                $response = array(
                    "status" => 0,
                    "url" => "/Users/Home/{$_SESSION['User']['username']}",
                    "message" => "You will be connected to the service as soon as possible..."
                );
                $headers = array(
                    "headers" => "Content-Type: application/json; X-XSS-Protection: 1; mode=block",
                    "replace" => true,
                    "responseCode" => 200
                );
            } else {
                if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['User']['username']}.json")) {
                    unlink("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$_SESSION['User']['username']}.json");
                }
                unset($_SESSION['User']);
                $response = array(
                    "status" => 5,
                    "url" => "/",
                    "message" => "The Password does not correspond to the one that was sent to you!"
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
                "url" => "/Login/Verification/{$_SESSION['User']['username']}",
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
}
