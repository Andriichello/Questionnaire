<?php
namespace Andriichello\Types;

class Answer implements IdentifiableDbModelInterface
{
    public int $id;
    public int $mark;
    public string $text;
    public int $questionID;

    public function identify()
    {
        return $this->id;
    }
}