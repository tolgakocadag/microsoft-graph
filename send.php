<?php

require_once "./src.php"; // Microsoft Graph API

$microsoftOutlookGraph = new MicrosoftOutlookGraph();
$microsoftOutlookGraph->sendMail(
    $_POST["email_address"],
    $_POST["subject"],
    $_POST["body"],
    $_POST["to"]
);

