<?php

require_once "./src.php"; // Microsoft Graph API

$microsoftOutlookGraph = new MicrosoftOutlookGraph();
$microsoftOutlookGraph->response($_GET["code"],$_GET["state"],$_GET["session_state"]);



