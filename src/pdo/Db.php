<?php
namespace Andriichello\Pdo;

class Db {
    protected ?string $dbUserName;
    protected ?string $dbUserPassword;
    protected ?string $dbServerName;
    protected ?string $dbDatabaseName;

    protected ?array $dbOptions;
    protected \PDO $dbHandle;

    public function __construct(?string $dbUserName, ?string $dbUserPassword, ?string $dbDatabaseName, ?string $dbServerName = 'localhost', ?array $dbOptions = null)
    {
        $this->dbUserName = $dbUserName;
        $this->dbUserPassword = $dbUserPassword;
        $this->dbServerName = $dbServerName;
        $this->dbDatabaseName = $dbDatabaseName;
        $this->dbOptions = $dbOptions;

        try {
            $this->dbHandle = new \PDO($this->getDSN(), $dbUserName, $dbUserPassword, $dbOptions);
        } catch (\PDOException $exception) {
            throw $exception;
        }
    }

    public function isEstablished(): bool {
        if (empty($this->dbHandle) || !$this->dbHandle)
            return false;

        return true;
    }

    public function getDSN(): string {
        return "mysql: host = {$this->dbServerName}; dbname = {$this->dbDatabaseName}; charset = UTF-8";
    }

    public function getUserName(): ?string
    {
        return $this->dbUserName;
    }

    public function getUserPassword(): ?string
    {
        return $this->dbUserPassword;
    }

    public function getDbServerName(): ?string
    {
        return $this->dbServerName;
    }

    public function getDatabaseName(): ?string
    {
        return $this->dbDatabaseName;
    }

    public function getOptions(): ?array
    {
        return $this->dbOptions;
    }

    public function getHandle(): \PDO
    {
        return $this->dbHandle;
    }
}