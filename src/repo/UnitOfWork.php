<?php
namespace Andriichello\Repo;

use \Andriichello\Pdo\Db;
use \PDO;


class UnitOfWork
{
    private static UnitOfWork $instance;

    public static function getInstance(string...$repoClassNames): static
    {
        if (empty(self::$instance) || !self::$instance->db->isEstablished()) {
            self::$instance = new UnitOfWork(
                UserRepository::class,
                LearningStageRepository::class,
                QuestionRepository::class,
                AnswerRepository::class,
                AttemptRepository::class,
                AttemptFieldsRepository::class
            );
        }

        foreach ($repoClassNames as $repoClassName)
            self::$instance->addRepository($repoClassName);

        return self::$instance;
    }

    // key (full class name) value (repository)
    protected array $repositories = array();
    protected Db $db;

    private function __construct(string...$repoClassNames)
    {
        $options = array(
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING
        );

        $this->db = new Db('andrii', '131313', "questionnairedb", "localhost", $options);

        if (!$this->db->isEstablished())
            throw new \Exception(self::class . " exception: Db connection is not established");

        foreach ($repoClassNames as $className)
            $this->addRepository($className);
    }

    public function getRepositories(): array
    {
        return $this->repositories;
    }

    public function findRepository(string $repoClassName): ?Repository
    {
        return $this->repositories[$repoClassName];
    }

    public function removeRepository(string $repoClassName): void
    {
        $this->repositories[$repoClassName] = null;
    }

    public function addRepository(string $repoClassName): bool
    {
        if (empty($this->repositories[$repoClassName])) {
            $this->repositories[$repoClassName] = new $repoClassName($this->db);
            return true;
        }

        return false;
    }
}
