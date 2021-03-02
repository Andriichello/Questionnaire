<?php
require_once "../vendor/autoload.php";

use \Andriichello\Types\LearningStage;
use \Andriichello\Repo\{
    UnitOfWork,
    LearningStageRepository,
};

session_start();
if (empty($_SESSION['user'])) {
    header('Location: authorization.php');
    exit();
}

try {
    $unitOfWork = UnitOfWork::getInstance(LearningStageRepository::class);

    $stagesRepo = $unitOfWork->findRepository(LearningStageRepository::class);
    $stages = $stagesRepo?->getAll();

    if (
        !empty($_POST['learningStage'])
        && !empty($stages)
    ) {
        foreach ($stages as $stage) {
            if ($_POST['learningStage'] == $stage->name) {
                $_SESSION['learningStage'] = $stage;

                header("Location: questionnaire.php");
                exit();
            }
        }
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getCode() . ", " . $e->getMessage();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="../styles/main_styles.css" type="text/css">
    <title>Questionnaire</title>
</head>

<body>
    <header>
        <span class="headerTitle">Learning Stages</span>
    </header>

    <section class="container">
        <form  action="learningstages.php" method="post">
            <?php if (!empty($stages)): ?>
                <?php foreach ($stages as $stage): ?>
                    <input class="button" type="submit" name="learningStage" value="<?= $stage->name; ?>">
                <?php endforeach; ?>
            <?php endif; ?>
        </form>

        <a href="results.php" class="myRef">Results</a>
    </section>

</body>

</html>