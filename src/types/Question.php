<?php

namespace Andriichello\types;

class Question {
    protected int $id;
    protected string $text;
    protected int $learningStageId;

    /**
     * Question constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        if (empty($params)) {
            $this->id = -1;
            $this->text = '';
            $this->learningStageId = -1;
        } else {
            $this->id = empty($params['id']) ? -1 : $params['id'];
            $this->text = empty($params['text']) ? '': $params['text'];
            $this->learningStageId = empty($params['learning_stage_id']) ? -1 : $params['learning_stage_id'];
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
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getLearningStageId(): int
    {
        return $this->learningStageId;
    }

    /**
     * @param int $learningStageId
     */
    public function setLearningStageId(int $learningStageId): void
    {
        $this->learningStageId = $learningStageId;
    }



}