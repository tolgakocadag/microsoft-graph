<?php

$mysql = [
    "host" => "127.0.0.1",
    "dbname" => "microsoft_graphs",
    "username" => "spechy",
    "password" => "spca1b2c3"
];

define("MYSQL", $mysql);

$application = [
    "tenant_id" => "3e3ada57-a982-4d21-a00d-b9c395cc31ce",
    "client_id"  => "79b70e6f-2084-41b0-896a-41b719c69277",
    "client_secret" => "PfY8Q~vpLEyegv2NZ5s4ntHI3qTmXIJ8nij3NanC",
    "scope" => "https://graph.microsoft.com/.default",
    "grant_type" => "client_credentials",
];

define("APPLICATION", $application);
