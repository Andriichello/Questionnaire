<?php
require_once "../vendor/autoload.php";

use \Andriichello\Types\{
    LearningStage,
    Attempt,
    AttemptField,
};
use \Andriichello\Repo\{
    UnitOfWork,
    QuestionRepository,
    AnswerRepository,
    LearningStageRepository,
    AttemptRepository,
    AttemptFieldsRepository,
};

session_start();

if (empty($_SESSION['user'])) {
    header("Location: authorization.php");
    exit();
}
if (empty($_SESSION['learningStage'])) {
    header("Location: learningstages.php");
    exit();
}
$user = $_SESSION['user'];
$stage = $_SESSION['learningStage'];

try {
    $unitOfWork = UnitOfWork::getInstance(
        AnswerRepository::class,
        QuestionRepository::class,
        AttemptRepository::class,
        AttemptFieldsRepository::class,
    );

    $answerRepo = $unitOfWork->findRepository(AnswerRepository::class);
    $questionRepo = $unitOfWork->findRepository(QuestionRepository::class);
    $attemptRepo = $unitOfWork->findRepository(AttemptRepository::class);
    $attemptFieldsRepo = $unitOfWork->findRepository(AttemptFieldsRepository::class);

    $questions = $questionRepo?->findByColumns([
        'learningStageID' => $stage->id,
    ]);
    if (!empty($questions)) {
        // key (questionID) value (array of answers)
        $answers = [];
        foreach ($questions as $question) {
            $answersForQuestion = $answerRepo?->findByColumns([
                'questionID' => $question->id,
            ]);

            if (!empty($answersForQuestion))
                $answers[$question->id] = $answersForQuestion;
        }
    }

    $attempts = $attemptRepo?->findByColumns([
        'userID' => $user->id,
        'learningStageID' => $stage->id,
    ]);
    if (!empty($attempts)) {
        // key (attemptID) value (array of attempt fields)
        $fields = [];
        foreach ($attempts as $attempt) {
            $fieldsForAttempt = $attemptFieldsRepo?->findByColumns([
                'attemptID' => $attempt->id,
            ]);

            if (!empty($fieldsForAttempt))
                $fields[$attempt->id] = $fieldsForAttempt;
        }
    }

    if (
        !empty($_POST['submit'])
        && !empty($_POST['answers'])
    ) {
        $attempt = Attempt::create(0, $user->id, $stage->id, (new DateTime())->format('Y-m-d H:i:s'));
        $attemptID = $attemptRepo?->add($attempt);

        if (!empty($attemptID)) {
            $selectedFields = [];
            foreach ($_POST['answers'] as $questionID => $answerID)
                $selectedFields[] = AttemptField::create(0, $attemptID, $questionID, $answerID);

            $result = $attemptFieldsRepo?->add(...$selectedFields);
            if (empty($result)) {
                $msg = 'Error while saving answers';
            } else {
                header('Refresh: 0');
            }
        }
    }
} catch (Exception $exception) {
    echo "Exception: #{$exception->getCode()}: {$exception->getMessage()}";
}

// creating array of points for chart
$dataPoints = [];
if (
    !empty($attempts)
    && !empty($fields)
    && !empty($answers)
) {
    $c = 1;

    $attempt = $attempts[count($attempts) - 1];
    foreach ($fields[$attempt->id] as $field) {
        if (empty($answers[$field->questionID]))
            continue;

        foreach ($answers[$field->questionID] as $answer) {
            if ($answer->id == $field->answerID) {
                $dataPoints[] = [
                    'y' => $answer->mark,
                    'label' => ('Q' . $c++),
                ];
                break;
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <link rel="stylesheet" href="../styles/main_styles.css">
    <title>Questionnaire</title>

    <script type="text/javascript">
        window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer",
                {
                    title: {
                        text: "Marks"
                    },
                    data: [
                        {
                            type: "line",
                            dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                        }
                    ]
                });

            chart.render();
        }
    </script>
</head>

<body>
    <header>
        <span class="headerTitle">Questions (<?= $stage->name; ?>)</span>
    </header>

    <section class="container">
        <form action="questionnaire.php" method="post">
            <?php if (
                !empty($questions)
                && !empty($answers)
            ): ?>
                <?php foreach ($questions as $question): ?>
                    <div class="questionBlock">
                        <h4><?= $question->text; ?></h4>

                        <?php if (!empty($answers[$question->id])): ?>
                            <select name="answers[<?= $question->id; ?>]">
                                <?php foreach ($answers[$question->id] as $answer): ?>
                                    <option value="<?= $answer->id; ?>"
                                        <?php if (
                                            !empty($_POST[$question->id])
                                            && $_POST[$question->id] == $answer->id
                                        ): ?>
                                            selected
                                        <?php endif; ?>
                                    >
                                        <?= $answer->text; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <input class="button" type="submit" name="submit" value="Submit">
        </form>

        <a href="learningstages.php" class="myRef">Learning Stages</a>

        <div id="chartContainer" class="chartContainer"
             style="visibility: <?= empty($attempt) ? 'hidden' : 'visible' ?>">
        </div>

        <?php
        if (!empty($attempts)) {
            echo '<ul class="markHistoryUl">';
            foreach ($attempts as $attempt) {
                if (empty($fields[$attempt->id]))
                    continue;

                $maxMark = count($fields[$attempt->id]) * 5;
                $accMark = 0;
                foreach ($fields[$attempt->id] as $field) {
                    if (empty($answers[$field->questionID]))
                        continue;

                    foreach ($answers[$field->questionID] as $answer) {
                        if ($answer->id == $field->answerID) {
                            $accMark += $answer->mark;
                            break;
                        }
                    }
                }
                echo "<li class='markHistoryLi'>mark: {$accMark}/{$maxMark}, when: {$attempt->createdAt}</li>";
            }
        }
        echo '</ul>';
        ?>

    </section>

</body>

</html>