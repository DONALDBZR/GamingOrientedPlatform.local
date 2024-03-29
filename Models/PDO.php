<?php
require_once "{$_SERVER["DOCUMENT_ROOT"]}/Models/Environment.php";
/**
 * A simpler version of PDO for developers to understand
 */
class PHPDataObject
{
    /**
     * The ENV that will be used
     * @var Environment $Environment
     */
    public Environment $Environment;
    /**
     * The data source name which contains the database's name the IP address and the Port of the database server
     * @var string $dataSourceName
     */
    public string $dataSourceName = Environment::MySQLDataSourceName;
    /**
     * The username that is used to authenticate on MySQL server
     * @var string $username
     */
    public string $username = Environment::MySQLUsername;
    /**
     * The password of the username that is used to authenticate on MySQL server
     * @var string $password
     */
    private string $password = Environment::MySQLPassword;
    /**
     * The database handler that is being used for this application which is PHP Data Objects
     * @var PDO $databaseHandler
     */
    private PDO $databaseHandler;
    /**
     * The SQL query that is used to interact with the database server
     * @var PDOStatement|false $statement
     */
    private $statement;
    /**
     * Upon instantiation, it will be connected while ensuring that the connection is persistent
     */
    public function __construct()
    {
        $options = array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        try {
            $this->databaseHandler = new PDO($this->dataSourceName, $this->username, $this->password, $options);
        } catch (PDOException $error) {
            echo "Connection Failed: {$error->getMessage()}";
        }
    }
    /**
     * Sanitizing the data that is retrieved in order to prevent SQL injections
     * @param   string                  $parameter  Parameter to be used to bind the data
     * @param   int|bool|null|string    $value      The data to be bound
     * @return  void
     */
    public function bind($parameter, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }
        }
        $this->statement->bindValue($parameter, $value, $type);
    }
    /**
     * Preparing the SQL query that is going to be handled by the database handler
     * @param   string  $query  The SQL query
     * @return  void
     */
    public function query($query)
    {
        $this->statement = $this->databaseHandler->prepare($query);
    }
    /**
     * Executing the SQL query which will send a command to the database server
     * @return  bool
     */
    public function execute()
    {
        return $this->statement->execute();
    }
    /**
     * Fetching all the data that is requested from the command that was sent to the database server
     * @return  array|false
     */
    public function resultSet()
    {
        $this->execute();
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
