<?php
session_start();
/**
 * The router that will route all the requests to the application.
 */
class Router
{
    /**
     * The request address
     * @var string $route
     */
    private string $route;
    /**
     * The server on which the application is being hosted
     * @var string $root
     */
    private string $root;
    /**
     * The path of the response
     * @var string $path
     */
    private string $path;
    /**
     * Creating the object which takes two parameters to verify the request method and session
     * @param   string  $route  The url of the view or controller
     */
    public function __construct(string $route)
    {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
        $this->setRoute($route);
        $this->verifySession();
    }
    public function getRoute(): string
    {
        return $this->route;
    }
    public function setRoute(string $route): void
    {
        $this->route = $route;
    }
    public function getPath(): string
    {
        return $this->path;
    }
    public function setPath(string $path): void
    {
        $this->path = $path;
    }
    public function getRoot(): string
    {
        return $this->root;
    }
    public function setRoot(string $root): void
    {
        $this->root = $root;
    }
    /**
     * Selecting data from the server
     * @param   string  $route  The url of the view or controller
     * @param   string  $path   The path of the view or controller
     * @return  void
     */
    public function get(string $route, string $path)
    {
        $this->setPath($path);
        if ($route != "/404") {
            require_once "{$this->getRoot()}{$this->getPath()}";
            http_response_code(200);
            exit();
        } else {
            require_once "{$this->getRoot()}/Views/HTTP404.php";
            http_response_code(404);
            exit();
        }
    }
    /**
     * Inserting data in the server
     * @param   string  $route  The url of the view or controller
     * @param   string  $path   The path of the view or controller
     * @return  void
     */
    public function post(string $route, string $path)
    {
        $this->setPath($path);
        if ($route != "/404") {
            $_POST[$route] = (object) json_decode(file_get_contents("php://input"));
            require_once "{$this->getRoot()}{$this->getPath()}";
            http_response_code(200);
            exit();
        } else {
            http_response_code(404);
            exit();
        }
    }
    /**
     * Creating Session
     * @return  void
     */
    public function createSession()
    {
        $data = array(
            "ip_address" => $_SERVER['REMOTE_ADDR'],
            "http_client_ip_address" => $_SERVER['HTTP_CLIENT_IP'],
            "proxy_ip_address" => $_SERVER['HTTP_X_FORWARDED_FOR'],
            "access_time" => time()
        );
        $_SESSION['Client'] = $data;
    }
    /**
     * Verifying that the session is not hijacked
     * @return  void
     */
    public function verifySession()
    {
        $directory = "{$_SERVER['DOCUMENT_ROOT']}/Cache/Session/Users/";
        $sessionFiles = array_values(array_diff(scandir($directory), array(".", "..")));
        for ($index = 0; $index < count($sessionFiles); $index++) {
            $session = json_decode(file_get_contents("{$directory}{$sessionFiles[$index]}"));
            $sessionData = $this->objectToArray($session);
            if ($_SESSION['Client']['ip_address'] == $session->Client->ip_address) {
                $_SESSION = $sessionData;
            }
        }
        if (isset($_SESSION['Client'])) {
            if ($_SERVER['REMOTE_ADDR'] == $_SESSION['Client']['ip_address']) {
                $_SESSION['Client']['access_time'] = time();
            } else {
                session_unset();
                session_destroy();
            }
        } else {
            $this->createSession();
        }
    }
    /**
     * Converting an object to an array
     * @param   mixed   $data   Data that is in the cache data
     * @return  array
     */
    public function objectToArray(mixed $data): array
    {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = (is_array($value) || is_object($value) ? $this->objectToArray($value) : $value);
            }
            return $result;
        }
        return $data;
    }
}
