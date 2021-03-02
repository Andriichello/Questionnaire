<?php
namespace Andriichello\Types;

class LearningStage implements IdentifiableDbModelInterface
{
    public int $id;
    public string $name;
    public ?string $description;

    public function identify()
    {
        return $this->id;
    }
}