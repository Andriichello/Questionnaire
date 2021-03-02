<?php
namespace Andriichello\Repo;

use \Andriichello\Pdo\Db;
use \Andriichello\Types\LearningStage;

class LearningStageRepository extends Repository
{
    public function __construct(Db $db)
    {
        parent::__construct($db);
        $this->table = 'LearningStage';
        $this->class = LearningStage::class;
    }
}