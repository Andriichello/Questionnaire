<?php
require_once "../vendor/autoload.php";

session_start();
if (empty($_SESSION['user'])) {
    header('Location: authorization.php');
    exit();
} else {
    header('Location: learningstages.php');
    exit();
}