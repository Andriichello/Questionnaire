<?php
namespace Andriichello\Types;

class Question implements IdentifiableDbModelInterface
{
    public int $id;
    public string $text;
    public int $learningStageID;

    public function identify()
    {
        return $this->id;
    }
}