<?php
namespace Andriichello\Repo;

use \Andriichello\Pdo\Db;
use \Andriichello\Types\Question;
use \Andriichello\Types\User;

class UserRepository extends Repository
{
    public function __construct(Db $db)
    {
        parent::__construct($db);
        $this->table = 'User';
        $this->class = User::class;
    }
}