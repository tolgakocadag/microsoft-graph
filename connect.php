<?php

require_once "./src.php"; // Microsoft Graph API

$microsoftOutlookGraph = new MicrosoftOutlookGraph();
$microsoftOutlookGraph->authorization($_GET["company"]);

