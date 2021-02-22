<?php

namespace Andriichello\types;

class Answer {
    protected int $id;
    protected int $mark;
    protected string $text;
    protected int $questionId;

    /**
     * Answer constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        if (empty($params)) {
            $this->id = -1;
            $this->mark = 0;
            $this->text = '';
            $this->questionId = -1;
        } else {
            $this->id = empty($params['id']) ? -1 : $params['id'];
            $this->mark = empty($params['mark']) ? 0 : $params['mark'];
            $this->text = empty($params['text']) ? '' : $params['text'];
            $this->questionId = empty($params['question_id']) ? -1 : $params['question_id'];
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
     * @return int
     */
    public function getMark(): int
    {
        return $this->mark;
    }

    /**
     * @param int $mark
     */
    public function setMark(int $mark): void
    {
        $this->mark = $mark;
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
    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    /**
     * @param int $questionId
     */
    public function setQuestionId(int $questionId): void
    {
        $this->questionId = $questionId;
    }


}