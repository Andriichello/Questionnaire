<?php
require_once "../vendor/autoload.php";

use \Andriichello\Types\LearningStage;
use \Andriichello\Types\Attempt;
use \Andriichello\Types\AttemptField;
use \Andriichello\Repo\QuestionRepository;
use \Andriichello\Repo\AnswerRepository;
use \Andriichello\Repo\LearningStageRepository;
use \Andriichello\Repo\AttemptRepository;
use \Andriichello\Repo\AttemptFieldsRepository;
use \Andriichello\Repo\UnitOfWork;

session_start();

if (empty($_SESSION['user'])) {
    header("Location: authorization.php");
    exit();
}
$user = $_SESSION['user'];

try {
    $unitOfWork = UnitOfWork::getInstance(
        AnswerRepository::class,
        QuestionRepository::class,
        AttemptRepository::class,
        AttemptFieldsRepository::class,
        LearningStageRepository::class
    );

    $answerRepo = $unitOfWork->findRepository(AnswerRepository::class);
    $questionRepo = $unitOfWork->findRepository(QuestionRepository::class);
    $attemptRepo = $unitOfWork->findRepository(AttemptRepository::class);
    $fieldsRepo = $unitOfWork->findRepository(AttemptFieldsRepository::class);
    $stagesRepo = $unitOfWork->findRepository(LearningStageRepository::class);

    $stages = $stagesRepo?->getAll();
    $answers = $answerRepo?->getAll();
    $attempts = $attemptRepo?->findByColumns([
        'userID' => $user->id,
    ]);
    if (!empty($attempts)) {
        // key (attemptID) value (array of attempt fields)
        $fields = [];
        foreach ($attempts as $attempt) {
            $fieldsForAttempt = $fieldsRepo?->findByColumns([
                'attemptID' => $attempt->id,
            ]);

            if (!empty($fieldsForAttempt))
                $fields[$attempt->id] = $fieldsForAttempt;
        }
    }

    if (
        !empty($_POST['clear'])
        && !empty($attempts)
    ) {
        foreach ($attempts as $attempt) {
            if (
                empty($fields[$attempt->id])
                || $fieldsRepo?->remove(...$fields[$attempt->id])
            ) {
                $attemptRepo?->remove($attempt);
            }
        }

        header('Refresh: 0');
    }
} catch (Exception $exception) {
    echo "Exception: #{$exception->getCode()}: {$exception->getMessage()}";
}

$dataPoints = [];
if (
    !empty($stages)
    && !empty($attempts)
    && !empty($fields)
    && !empty($answers)
) {

    foreach ($stages as $stage) {
        $accAttempts = 0;
        $accMark = 0;
        foreach ($attempts as $attempt) {
            if ($attempt->learningStageID == $stage->id) {
                if (empty($fields[$attempt->id]))
                    continue;

                foreach ($fields[$attempt->id] as $field) {
                    if (empty($answers[$field->questionID]))
                        continue;

                    foreach ($answers as $answer) {
                        if ($answer->id == $field->answerID)
                            $accMark += $answer->mark;
                    }
                }

                $accAttempts++;
            }
        }

        $dataPoints[] = [
            'y' => $accMark <= 0 ? 0 : $accMark / $accAttempts,
            'label' => "{$stage->name} ($accAttempts)",
        ];
    }
}
?>

<!doctype html>
<html lang=",
en">
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
                        text: "Stats"
                    },
                    data: [
                        {
                            type: "column",
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
    <span class="headerTitle">Results <?= empty($user) ?: " ({$user->login})"; ?></span>
</header>

<section class="container">

    <?php if (empty($attempts)): ?>
        <p class="errorMsg">There is no attempts...</p>
    <?php else: ?>
        <div id="chartContainer" class="chartContainer"
             style="visibility: <?= empty($attempts) ? 'hidden' : 'visible' ?>">
        </div>

        <form action="results.php" method="post">
            <input class="button" type="submit" name="clear" value="Clear all results">
        </form>
    <?php endif; ?>

    <a href="learningstages.php" class="myRef">Learning Stages</a>
</section>
</body>

</html>