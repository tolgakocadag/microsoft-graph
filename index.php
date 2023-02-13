<?php
require_once "./src.php";

foreach (ACCOUNTS as $ACCOUNT) {
    $microsoftOutlookGraph = new MicrosoftOutlookGraph($ACCOUNT);

//    echo $microsoftOutlookGraph->sendMail("DENEME MAİLİ","deneme içeriği",["tlgkcdg@gmail.com"]);
}