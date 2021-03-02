<?php
namespace Andriichello\Repo;

use \Andriichello\Pdo\Db;
use \Andriichello\Types\Attempt;
use \Andriichello\Types\AttemptField;

class AttemptFieldsRepository extends Repository
{
    public function __construct(Db $db)
    {
        parent::__construct($db);
        $this->table = 'AttemptField';
        $this->class = AttemptField::class;
    }
}