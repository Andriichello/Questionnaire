<?php
namespace Andriichello\Repo;

use \Andriichello\Pdo\Db;
use \Andriichello\Types\IdentifiableDbModelInterface;
use \PDO;

abstract class Repository
{
    protected Db $db;
    protected string $table;
    protected string $class;

    public function __construct(Db $db)
    {
        $this->db = $db;
        $this->table = '';
        $this->class = '';

        if (!$db->isEstablished()) {
            $msg = "Instance of " . self::class . " can't be created with invalid Db.";
            $code = 1;

            throw new \Exception($msg, $code);
        }
    }

    public function __destruct()
    {
        unset($this->db);
    }

    public function getAll(): array
    {
        $query = "SELECT * FROM {$this->db->getDatabaseName()}.{$this->table}";
        $stmt = $this->db->getHandle()->query($query);

        $stmt->setFetchMode(PDO::FETCH_CLASS, $this->class);
        return $stmt->fetchAll();
    }

    public function add(IdentifiableDbModelInterface...$objs): int|false {
        $vars = get_object_vars($objs[0]);
        $columns = implode(',', array_keys($vars));
        $placeholders = implode(',', array_fill(0, count($vars), '?'));

        $query = "INSERT INTO {$this->db->getDatabaseName()}.{$this->table} ({$columns}) VALUES ({$placeholders})";

        $this->db->getHandle()->beginTransaction();
        $stmt = $this->db->getHandle()->prepare($query);

        foreach ($objs as $obj) {
            $vars = get_object_vars($obj);
            if (!$stmt->execute(array_values($vars))) {
                $this->db->getHandle()->rollBack();
                return false;
            }
        }
        $lastInsertedID = $this->db->getHandle()->lastInsertId();
        $this->db->getHandle()->commit();
        return $lastInsertedID;
    }

    public function remove(IdentifiableDbModelInterface...$objs): bool {
        $query = "DELETE FROM {$this->db->getDatabaseName()}.{$this->table} WHERE id = ?";

        $this->db->getHandle()->beginTransaction();
        $stmt = $this->db->getHandle()->prepare($query);

        foreach ($objs as $obj) {
            $id = $obj->identify();
            if (!$stmt->execute(array($id))) {
                $this->db->getHandle()->rollBack();
                return false;
            }
        }

        $this->db->getHandle()->commit();
        return true;
    }

    public function findByID($id): array {
        $query = "SELECT * FROM {$this->db->getDatabaseName()}.{$this->table} WHERE id = ?";
        $stmt = $this->db->getHandle()->prepare($query);
        $stmt->execute($id);

        $stmt->setFetchMode(PDO::FETCH_CLASS, $this->class);
        return $stmt->fetchAll();
    }

    public function findByColumns(array $arr, string $condition = 'and'): array {
        $conditions = '';
        $keys = array_keys($arr);
        for ($i = 0, $count = count($keys); $i < $count; $i++) {
            if ($i > 0)
                $conditions .= " {$condition} ";
            $conditions .= $keys[$i];
            $conditions .= ' = ?';
        }

        $query = "SELECT * FROM {$this->db->getDatabaseName()}.{$this->table} WHERE {$conditions}";
        $stmt = $this->db->getHandle()->prepare($query);
        $stmt->execute(array_values($arr));

        $stmt->setFetchMode(PDO::FETCH_CLASS, $this->class);
        return $stmt->fetchAll();
    }
}