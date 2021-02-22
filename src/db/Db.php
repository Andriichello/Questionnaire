<?php

namespace Andriichello\db;

class Db {
    protected string $dbServerName;
    protected string $dbUserName;
    protected string $dbUserPassword;
    protected string $dbName;

    protected \mysqli $connection;

    /**
     * Db constructor.
     * @param string $dbServerName
     * @param string $dbUserName
     * @param string $dbUserPassword
     * @param string $dbName
     */
    public function __construct(string $dbUserName, string $dbUserPassword, string $dbName, string $dbServerName = '127.0.0.1')
    {
        $this->dbServerName = $dbServerName;
        $this->dbUserName = $dbUserName;
        $this->dbUserPassword = $dbUserPassword;
        $this->dbName = $dbName;

        $this->connection = new \mysqli($dbServerName, $dbUserName, $dbUserPassword, $dbName);
        if ($this->is_established()) {
            // by default all columns have string type
            // there is an option to convert integers and floats to numbers
            $this->connection->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
        }
    }

    public function __destruct()
    {
        if (!empty($this->connection)) {
            $this->connection->close();
        }
    }

    /**
     * @return false|\mysqli
     */
    public function get_connection(): bool|\mysqli
    {
        return $this->connection;
    }

    public function is_established(): bool {
        if (isset($this->connection))
            return !$this->connection->error;

        return false;
    }
}