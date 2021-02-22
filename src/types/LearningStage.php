<?php

namespace Andriichello\types;

class LearningStage {
    protected int $id;
    protected string $name;
    protected ?string $description;

    /**
     * LearningStage constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        if (empty($params)) {
            $this->id = -1;
            $this->name = '';
            $this->description = null;
        } else {
            $this->id = empty($params['id']) ? -1 : $params['id'];
            $this->name = empty($params['name']) ? '' : $params['name'];
            $this->description = empty($params['description']) ? null : $params['description'];
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }


}