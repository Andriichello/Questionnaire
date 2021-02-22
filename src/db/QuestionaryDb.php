<?php

namespace Andriichello\db;

use Andriichello\types\Answer;
use Andriichello\types\LearningStage;
use Andriichello\types\Question;
use http\Exception;

class QuestionaryDb extends Db
{
    // table names
    private const USER_TN = 'User';
    private const LEARNING_STAGE_TN = 'LearningStage';
    private const QUESTION_TN = 'Question';
    private const ANSWER_TN = 'Answer';

    public function __construct(string $dbUserName, string $dbUserPassword, string $dbName = 'questionarydb', string $dbServerName = '127.0.0.1')
    {
        parent::__construct($dbUserName, $dbUserPassword, $dbName, $dbServerName);
    }

    /**
     * @return ?array<int, LearningStage>
     */
    public function get_learning_stages(): ?array
    {
        if ($this->is_established()) {
            $query = "SELECT * FROM {$this->dbName}." . static::LEARNING_STAGE_TN;
            $answer = $this->connection->query($query);

            if (!$this->connection->error) {
                foreach ($answer->fetch_all(MYSQLI_ASSOC) as $params)
                    $stages[] = new LearningStage($params);
                return $stages;
            }
        }

        return null;
    }

    public function add_learning_stage(LearningStage $learningStage): bool
    {
        if ($this->is_established()) {
            $query = "INSERT INTO {$this->dbName}." . static::LEARNING_STAGE_TN;
            $query .= " (name, description) VALUES (?, ?)";

            $stmt = $this->connection->prepare($query);

            $name = $learningStage->getName();
            $description = $learningStage->getDescription();
            $stmt->bind_param("ss", $name, $description);

            $stmt->execute();

            if (!$this->connection->error)
                return true;
        }

        return false;
    }

    /**
     * @return ?array<int, Question>
     */
    public function get_questions(?int $learningStageId = null): ?array
    {
        if ($this->is_established()) {
            $query = "SELECT * FROM {$this->dbName}." . static::QUESTION_TN;
            if (!isset($learningStageId)) {
                $answer = $this->connection->query($query);
            } else {
                $query .= " WHERE learning_stage_id = ?";
                $stmt = $this->connection->prepare($query);
                $stmt->bind_param("i", $learningStageId);
                $stmt->execute();

                $answer = $stmt->get_result();
            }

            if (!$this->connection->error) {
                $questions = array();
                foreach ($answer->fetch_all(MYSQLI_ASSOC) as $params)
                    $questions[] = new Question($params);
                return $questions;
            }
        }

        return null;
    }

    public function add_question(Question $question): bool
    {
        if ($this->is_established()) {
            $query = "INSERT INTO {$this->dbName}." . static::QUESTION_TN;
            $query .= " (text, learning_stage_id) VALUES (?, ?)";

            $stmt = $this->connection->prepare($query);

            $text = $question->getText();
            $learningStageId = $question->getLearningStageId();
            $stmt->bind_param("si", $text, $learningStageId);

            $stmt->execute();

            if (!$this->connection->error)
                return true;
        }

        return false;
    }

    /**
     * @return ?array<int, Answer>
     */
    public function get_answers(?int $questionId = null): ?array
    {
        if ($this->is_established()) {
            $query = "SELECT * FROM {$this->dbName}." . static::ANSWER_TN;
            if (!isset($questionId)) {
                $answer = $this->connection->query($query);
            } else {
                $query .= " WHERE question_id = ?";
                $stmt = $this->connection->prepare($query);
                $stmt->bind_param("i", $questionId);
                $stmt->execute();

                $answer = $stmt->get_result();
            }

            if (!$this->connection->error) {
                $answers = array();
                foreach ($answer->fetch_all(MYSQLI_ASSOC) as $params)
                    $answers[] = new Answer($params);
                return $answers;
            }
        }

        return null;
    }

    public function add_answer(Answer $answer): bool
    {
        if ($this->is_established()) {
            $query = "INSERT INTO {$this->dbName}." . static::ANSWER_TN;
            $query .= " (text, mark, question_id) VALUES (?, ?, ?)";

            $stmt = $this->connection->prepare($query);

            $text = $answer->getText();
            $mark = $answer->getMark();
            $questionId = $answer->getQuestionId();
            $stmt->bind_param("sii", $text, $mark, $questionId);

            $stmt->execute();

            if (!$this->connection->error)
                return true;
        }

        return false;
    }

    public function init_learning_stages(): void
    {
        if (!$this->is_established())
            return;

        $learningStages = array(
            new LearningStage(["id" => -1, "name" => "Новачок", "description" => "Novice"]),
            new LearningStage(["id" => -1, "name" => "Твердий початківець", "description" => "Advanced beginner"]),
            new LearningStage(["id" => -1, "name" => "Досвідчений", "description" => "Proficient"]),
            new LearningStage(["id" => -1, "name" => "Компетентний", "description" => "Competent"]),
            new LearningStage(["id" => -1, "name" => "Експерт", "description" => "Expert"])
        );

        $this->connection->autocommit(false);
        $this->connection->begin_transaction();

        $isOK = true;
        foreach ($learningStages as $learningStage) {
            if (!$this->add_learning_stage($learningStage)) {
                $isOK = false;
                break;
            }
        }

        if ($isOK)
            $this->connection->commit();
        else {
            $this->connection->rollback();
            throw new \Exception("Error #" . $this->connection->errno . ": " . $this->connection->error);
        }
        $this->connection->autocommit(true);
    }

    public function init_questions(): void
    {
        if (!$this->is_established())
            return;


        $learningStages = $this->get_learning_stages();
        if (empty($learningStages))
            return;

        // LearningStage->getName() => array of questions
        $questions = array(
            "Новачок" => array(
                new Question(["id" => -1, "text" => "Переживаєте за успіх в роботі?", "learning_stage_id" => -1]),
                new Question(["id" => -1, "text" => "Прагнете досягти швидко результату?", "learning_stage_id" => -1]),
                new Question(["id" => -1, "text" => "Легко попадаєте в тупик при проблемах в роботі?", "learning_stage_id" => -1]),
                new Question(["id" => -1, "text" => "Чи потрібен чіткий алгоритм для вирішення задач?", "learning_stage_id" => -1])
            ),
            "Твердий початківець" => array(
                new Question(["id" => -1, "text" => "Чи використовуєте власний досвід при вирішенні задач?", "learning_stage_id" => -1]),
                new Question(["id" => -1, "text" => "Чи користуєтесь фіксованими правилами  для вирішення задач?", "learning_stage_id" => -1]),
                new Question(["id" => -1, "text" => "Чи відчуваєте ви загальний контекст вирішення задачі?", "learning_stage_id" => -1])
            ),
            "Досвідчений" => array(
                new Question(["id" => -1, "text" => "Чи можете ви побудувати модель вирішуваної задачі?", "learning_stage_id" => -1]),
                new Question(["id" => -1, "text" => "Чи вистачає вам ініціативи при вирішенні задач?", "learning_stage_id" => -1]),
                new Question(["id" => -1, "text" => "Чи можете вирішувати проблеми, з якими ще не стикались?", "learning_stage_id" => -1])
            ),
            "Компетентний" => array(
                new Question(["id" => -1, "text" => "Чи  необхідний вам весь контекст задачі?", "learning_stage_id" => -1]),
                new Question(["id" => -1, "text" => "Чи переглядаєте ви свої наміри до вирішення задачі?", "learning_stage_id" => -1]),
                new Question(["id" => -1, "text" => "Чи здатні  ви  навчатись у інших?", "learning_stage_id" => -1])
            ),
            "Експерт" => array(
                new Question(["id" => -1, "text" => "Чи обираєте ви нові методи своєї роботи?", "learning_stage_id" => -1]),
                new Question(["id" => -1, "text" => "Чи допомагає власна інтуїція при вирішенні задач?", "learning_stage_id" => -1]),
                new Question(["id" => -1, "text" => "Чи застовуєте рішення задач за аналогією?", "learning_stage_id" => -1])
            ),
        );

        $this->connection->autocommit(false);
        $this->connection->begin_transaction();

        $isOK = true;
        foreach ($questions as $stage => $stageQuestions) {
            foreach ($learningStages as $learningStage) {
                if ($stage == $learningStage->getName()) {
                    foreach ($stageQuestions as &$q) {
                        $q->setLearningStageId($learningStage->getId());
                        if (!$this->add_question($q)) {
                            $isOK = false;
                            break;
                        }
                    }
                    unset($q);
                }
            }
        }

        if ($isOK)
            $this->connection->commit();
        else {
            $this->connection->rollback();
            throw new \Exception("Error #" . $this->connection->errno . ": " . $this->connection->error);
        }
        $this->connection->autocommit(true);
    }

    public function init_answers(): void
    {
        if (!$this->is_established())
            return;

        $questions = $this->get_questions();
        if (empty($questions))
            return;

        echo "is inside..." . "<br>";
        // Question->getText() => array of answers
        $answers = array();
        foreach ($questions as $question) {
            $answers[$question->getText()] = match ($question->getText()) {
                // Новачок
                "Переживаєте за успіх в роботі?" => array(
                    new Answer(['text' => "сильно", 'mark' => 5, 'question_id' => $question->getId()]),
                    new Answer(['text' => "не дуже", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "спокійний", 'mark' => 2, 'question_id' => $question->getId()])),
                "Прагнете досягти швидко результату?" => array(
                    new Answer(['text' => "поступово", 'mark' => 2, 'question_id' => $question->getId()]),
                    new Answer(['text' => "якомога швидше", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "дуже", 'mark' => 5, 'question_id' => $question->getId()])),
                "Легко попадаєте в тупик при проблемах в роботі?" => array(
                    new Answer(['text' => "неодмінно", 'mark' => 5, 'question_id' => $question->getId()]),
                    new Answer(['text' => "поступово", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "зрідка", 'mark' => 2, 'question_id' => $question->getId()])),
                "Чи потрібен чіткий алгоритм для вирішення задач?" => array(
                    new Answer(['text' => "так", 'mark' => 5, 'question_id' => $question->getId()]),
                    new Answer(['text' => "в окремих випадках", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "не потрібен", 'mark' => 2, 'question_id' => $question->getId()])),

                // Твердий початківець
                "Чи використовуєте власний досвід при вирішенні задач?" => array(
                    new Answer(['text' => "зрідка", 'mark' => 5, 'question_id' => $question->getId()]),
                    new Answer(['text' => "частково", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "ні", 'mark' => 2, 'question_id' => $question->getId()])),
                "Чи користуєтесь фіксованими правилами  для вирішення задач?" => array(
                    new Answer(['text' => "так", 'mark' => 2, 'question_id' => $question->getId()]),
                    new Answer(['text' => "в окремих випадках", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "не потрібні", 'mark' => 5, 'question_id' => $question->getId()])),
                "Чи відчуваєте ви загальний контекст вирішення задачі?" => array(
                    new Answer(['text' => "так", 'mark' => 2, 'question_id' => $question->getId()]),
                    new Answer(['text' => "частково", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "в окремих випадках(", 'mark' => 5, 'question_id' => $question->getId()])),

                // Досвідчений
                "Чи можете ви побудувати модель вирішуваної задачі?" => array(
                    new Answer(['text' => "так", 'mark' => 5, 'question_id' => $question->getId()]),
                    new Answer(['text' => "не повністю", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "в окремих випадках", 'mark' => 2, 'question_id' => $question->getId()])),
                "Чи вистачає вам ініціативи при вирішенні задач?" => array(
                    new Answer(['text' => "так", 'mark' => 5, 'question_id' => $question->getId()]),
                    new Answer(['text' => "зрідка", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "отрібне натхнення", 'mark' => 2, 'question_id' => $question->getId()])),
                "Чи можете вирішувати проблеми, з якими ще не стикались?" => array(
                    new Answer(['text' => "так", 'mark' => 2, 'question_id' => $question->getId()]),
                    new Answer(['text' => "в окремих випадках", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "ні", 'mark' => 5, 'question_id' => $question->getId()])),

                // Компетентний
                "Чи  необхідний вам весь контекст задачі?" => array(
                    new Answer(['text' => "так", 'mark' => 5, 'question_id' => $question->getId()]),
                    new Answer(['text' => "в окремих деталях", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "в загальному", 'mark' => 2, 'question_id' => $question->getId()])),
                "Чи переглядаєте ви свої наміри до вирішення задачі?" => array(
                    new Answer(['text' => "так", 'mark' => 5, 'question_id' => $question->getId()]),
                    new Answer(['text' => "зрідка", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "коли є потреба", 'mark' => 2, 'question_id' => $question->getId()])),
                "Чи здатні  ви  навчатись у інших?" => array(
                    new Answer(['text' => "так", 'mark' => 5, 'question_id' => $question->getId()]),
                    new Answer(['text' => "зрідка", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "коли є потреба", 'mark' => 2, 'question_id' => $question->getId()])),

                // Експерт
                "Чи обираєте ви нові методи своєї роботи?" => array(
                    new Answer(['text' => "так", 'mark' => 5, 'question_id' => $question->getId()]),
                    new Answer(['text' => "вибірково", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "вистачає досвіду", 'mark' => 2, 'question_id' => $question->getId()])),
                "Чи допомагає власна інтуїція при вирішенні задач?" => array(
                    new Answer(['text' => "так", 'mark' => 5, 'question_id' => $question->getId()]),
                    new Answer(['text' => "частково", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "при емоційному напруженні", 'mark' => 2, 'question_id' => $question->getId()])),
                "Чи застовуєте рішення задач за аналогією?" => array(
                    new Answer(['text' => "часто", 'mark' => 5, 'question_id' => $question->getId()]),
                    new Answer(['text' => "зрідка", 'mark' => 3, 'question_id' => $question->getId()]),
                    new Answer(['text' => "ільки власний варіант", 'mark' => 2, 'question_id' => $question->getId()]))
            };
        }

        var_dump($answers);

        $this->connection->autocommit(false);
        $this->connection->begin_transaction();
        $isOK = true;
        foreach ($answers as $answerGroup) {
            foreach ($answerGroup as $a) {
                if (!$this->add_answer($a))
                {
                    $isOK = false;
                    break 2;
                }
            }
            unset($a);
        }
        unset($answerGroup);

        if ($isOK)
            $this->connection->commit();
        else {
            $this->connection->rollback();
            throw new \Exception("Error #" . $this->connection->errno . ": " . $this->connection->error);
        }

        $this->connection->autocommit(true);

    }

}