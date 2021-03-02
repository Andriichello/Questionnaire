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

    if (isset($_POST['register'])) {
        if (
            isset($_POST['username'])
            && isset($_POST['password'])
        ) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if (!preg_match('(^[_0-9a-zA-Z]{3,}$)', $username))
                $msg = 'Invalid username. Should be at least 3 characters long and should not contain spaces.';
            elseif (strlen($password) < 8)
                $msg = 'Invalid password. Should be at least 8 characters long.';
            else {
                // entered username and password are valid
                $userRepo = $unitOfWork->findRepository(UserRepository::class);
                $matchingUsers = $userRepo?->findByColumns([
                    'login' => $username
                ]);
                if (empty($matchingUsers)) {
                    // no users with such login exist
                    if ($userRepo->add(User::create(0, $username, $password))) {
                        // successfully registered
                        header('Location: authorization.php');
                        exit();
                    } else {
                        // failed to registered
                        $msg = 'Error while registering';
                    }
                } else {
                    // user with the same login already exists
                    $msg = "User with login '{$username}' already exists";
                }
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
        <span class="headerTitle">Registration</span>
    </header>

    <section class="container">
        <form  action="registration.php" method="post">
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="username" value="<?= empty($_POST['username']) ? ' ' : $_POST['username']; ?>">
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="text" id="password" name="password" value="<?= empty($_POST['password']) ? '' : $_POST['password']; ?>">
            </div>
            <input class="button" type="submit" name="register" value="Register">
            <?php if (isset($msg)): ?>
                <p class="errorMsg"><?= $msg; ?></p>
            <?php endif; ?>
        </form>

        <a href="authorization.php" class="myRef">Authorize</a>
    </section>
</body>

</html>