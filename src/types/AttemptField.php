<?php
namespace Andriichello\Types;

class AttemptField implements IdentifiableDbModelInterface
{
    public int $id;
    public int $attemptID;
    public int $questionID;
    public int $answerID;

    public static function create(int $id, int $attemptId, int $questionId, $answerId): static {
        $obj = new AttemptField();

        $obj->id = $id;
        $obj->attemptID = $attemptId;
        $obj->questionID = $questionId;
        $obj->answerID = $answerId;

        return $obj;
    }

    public function identify()
    {
        return $this->id;
    }
}