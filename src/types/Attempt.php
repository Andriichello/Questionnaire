<?php
namespace Andriichello\Types;

class Attempt implements IdentifiableDbModelInterface
{
    public int $id;
    public string $createdAt;
    public int $userID;
    public int $learningStageID;

    public static function create(int $id, int $userId, int $learningStageId, string $createdAt): static {
        $obj = new Attempt();

        $obj->id = $id;
        $obj->userID = $userId;
        $obj->learningStageID = $learningStageId;
        $obj->createdAt = $createdAt;

        return $obj;
    }

    public function identify()
    {
        return $this->id;
    }
}