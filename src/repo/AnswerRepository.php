<?php
namespace Andriichello\Repo;

use \Andriichello\Pdo\Db;
use \Andriichello\Types\Answer;

class AnswerRepository extends Repository
{
    public function __construct(Db $db)
    {
        parent::__construct($db);
        $this->table = 'Answer';
        $this->class = Answer::class;
    }
}