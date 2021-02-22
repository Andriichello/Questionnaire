<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../vendor/autoload.php";

use Andriichello\db\QuestionaryDb;
use Andriichello\types\Answer;
use Andriichello\types\LearningStage;
use Andriichello\types\Question;
use Andriichello\types\User;

$qdb = new QuestionaryDb('andrii', '131313');
if ($qdb->is_established()) {
    $learningStages = $qdb->get_learning_stages();
//    var_dump($learningStages);
    $questions = $qdb->get_questions();
//    var_dump($questions);
    $answers = $qdb->get_answers();
//    var_dump($answers);
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Questionary</title>
</head>

<body>

</body>

</html>
