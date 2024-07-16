<?php

session_start();

require "helpers.php";

$url = htmlspecialchars($_SERVER['REQUEST_URI']);

$parseURL = explode("/", $url);

if (array_key_exists(2, $parseURL)) {
    $_SESSION['feedback_url'] = $parseURL[2];
    header('Location:http://localhost:8000/feedback.php');
}
