<?php

require_once "./src.php"; // Microsoft Graph API

$microsoftOutlookGraph = new MicrosoftOutlookGraph();
$microsoftOutlookGraph->getMail(
    $_POST["email_address"]
);

