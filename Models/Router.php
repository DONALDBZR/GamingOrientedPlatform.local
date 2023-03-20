<?php
session_start();
/**
 * The router that will route all the requests to the application.
 */
class Router
{
    /**
     * The request address
     */
    private string $route;
    /**
     * The server on which the application is being hosted
     */
    private string $root;
    /**
     * The path of the response
     */
    private string $path;
    /**
     * The method of the request
     */
    private string $requestMethod;
    public function __construct(string $request_method, string $route, string $path)
    {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
        $this->verifySession();
        $this->verifyRequestMethod($request_method, $route, $path);
    }
    public function getRoute(): string
    {
        return $this->route;
    }
    public function setRoute(string $route)
    {
        $this->route = $route;
    }
    public function getPath(): string
    {
        return $this->path;
    }
    public function setPath(string $path)
    {
        $this->path = $path;
    }
    public function getRoot(): string
    {
        return $this->root;
    }
    public function setRoot(string $root)
    {
        $this->root = $root;
    }
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }
    public function setRequestMethod(string $request_method)
    {
        $this->requestMethod = $request_method;
    }
    /**
     * Verifying the request method before setting the route of the request for generating the adequate response
     */
    public function verifyRequestMethod(string $requestMethod, string $route, string $path)
    {
        $this->setRequestMethod($requestMethod);
        $this->setRoute($route);
        $this->setPath($path);
        switch ($this->getRequestMethod()) {
            case 'GET':
                $this->get($this->getRoute(), $this->getPath());
            case 'POST':
                $this->post($this->getRoute(), $this->getPath());
            case 'PATCH':
                // $this->patch();
            case 'DELETE':
                // $this->delete();
            default:
                // $this->route($this->getRoute(), $this->getPath());
                break;
        }
    }
    /**
     * Selecting data from the server
     */
    public function get(string $route, string $path)
    {
        if ($route != "/404") {
            require_once "{$this->getRoot()}{$path}";
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
     */
    public function post(string $route, string $path)
    {
        if ($route != "/404") {
            $date = date('y-m-d h-i-s');
            $latestData = json_decode(file_get_contents("php://input"));
            $data = array(
                "requestMethod" => "POST",
                "route" => $route,
                "Data" => $latestData
            );
            $table = "";
            if (str_contains($route, "Users")) {
                $table = "Users";
                $cacheData = json_encode($data);
                $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$table}/{$date}.json", "w");
                fwrite($cache, $cacheData);
                fclose($cache);
                require_once "{$this->getRoot()}{$path}";
                http_response_code(200);
                exit();
            } else if (str_contains($route, "Passwords")) {
                $table = "Passwords";
                $cacheData = json_encode($data);
                $cache = fopen("{$_SERVER['DOCUMENT_ROOT']}/Cache/{$table}/{$date}.json", "w");
                fwrite($cache, $cacheData);
                fclose($cache);
                require_once "{$this->getRoot()}{$path}";
                http_response_code(200);
                exit();
            } else {
                require_once "{$this->getRoot()}/Views/HTTP503.php";
                http_response_code(503);
                exit();
            }
        } else {
            require_once "{$this->getRoot()}/Views/HTTP404.php";
            http_response_code(404);
            exit();
        }
    }
    /**
     * Updating data in the server
     */
    public function patch()
    {
    }
    /**
     * Deleting data from the server
     */
    public function delete()
    {
    }
    /**
     * Creating Session
     */
    public function createSession()
    {
        if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            $httpClientIP = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $proxyAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        $data = array(
            "ip_address" => $_SERVER['REMOTE_ADDR'],
            "http_client_ip_address" => $httpClientIP,
            "proxy_ip_address" => $proxyAddress,
            "access_time" => time()
        );
        $_SESSION['Client'] = $data;
    }
    /**
     * Verifying that the session is not hijacked
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
