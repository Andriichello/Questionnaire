<?php
namespace Andriichello\Repo;

use \Andriichello\Pdo\Db;
use \Andriichello\Types\Attempt;

class AttemptRepository extends Repository
{
    public function __construct(Db $db)
    {
        parent::__construct($db);
        $this->table = 'Attempt';
        $this->class = Attempt::class;
    }
}