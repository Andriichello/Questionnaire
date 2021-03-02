<?php
require_once "../vendor/autoload.php";

use \Andriichello\Types\User;
use \Andriichello\Repo\{
    UnitOfWork,
    UserRepository,
};

session_start();

try {
    $unitOfWork = UnitOfWork::getInstance(UserRepository::class);

    if (isset($_POST['authorize'])) {
         if (
             !empty($_POST['username'])
             && !empty($_POST['password'])
         ) {
             $username = $_POST['username'];
             $password = $_POST['password'];

             $userRepo = $unitOfWork->findRepository(UserRepository::class);
             $matchingUsers = $userRepo?->findByColumns([
                 'login' => $username,
                 'password' => $password
             ]);

             if (empty($matchingUsers)) {
                 // no users with such login exist
                 $msg = 'Wrong username or password.';
             } else {
                 // user exists
                 $_SESSION['user'] = $matchingUsers[0];
                 header('Location: learningstages.php');
                 exit();
             }
         }
    }
} catch (Exception $exception) {
    echo "Exception #{$exception->getCode()}: {$exception->getMessage()}";
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="../styles/main_styles.css">
    <title>Questionnaire</title>
</head>

<body>
    <header>
        <span class="headerTitle">Authorization</span>
    </header>

    <section class="container">
        <form  action="authorization.php" method="post">
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="username" value="<?= empty($_POST['username']) ? '' : $_POST['username']; ?>">
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="text" id="password" name="password" value="<?= empty($_POST['password']) ? '' : $_POST['password'];?>">
            </div>
            <input class="button" type="submit" name="authorize" value="Log in">
            <?php if (isset($msg)): ?>
                <p class="errorMsg"><?= $msg; ?></p>
            <?php endif; ?>
        </form>

        <a href="registration.php" class="myRef">Register</a>
    </section>
</body>

</html>