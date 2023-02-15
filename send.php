<?php

require_once "./src.php"; // Microsoft Graph API

$microsoftOutlookGraph = new MicrosoftOutlookGraph();


$result = $microsoftOutlookGraph->sendMail(
    $_POST["email_address"],
    $_POST["subject"],
    $_POST["body"],
    $_POST["to"],
    $_POST["cc"],
    $_POST["bcc"],
    $_POST["attachments"]
);

http_response_code($result->getStatus());

