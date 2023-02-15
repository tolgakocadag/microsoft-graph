<?php

$mysql = [
    "host" => "127.0.0.1",
    "dbname" => "microsoft_graphs",
    "username" => "spechy",
    "password" => "spca1b2c3"
];

define("MYSQL", $mysql);

$application = [
    "ogeturk" => [
        "tenant_id" => "3e3ada57-a982-4d21-a00d-b9c395cc31ce",
        "client_id"  => "79b70e6f-2084-41b0-896a-41b719c69277",
        "client_secret" => "PfY8Q~vpLEyegv2NZ5s4ntHI3qTmXIJ8nij3NanC",
        "redirect_url" => "https://callcenter.spechy.live/npbx/connect",
        "media_url" => "https://callcenter.spechy.live/npbx/saveMedia",
        "scope" => "https://graph.microsoft.com/.default offline_access",
        "grant_type" => "client_credentials",
        "database" => [
            "host" => "185.16.239.19",
            "dbname" => "spechycrm",
            "username" => "spechy",
            "password" => "spca1b2c3"
        ]
    ],
    "exxen" => [
        "tenant_id" => "fcd26ac6-2002-4c5f-a7a8-28a71f41014d",
        "client_id"  => "a24ab690-6c8d-4e45-a6d0-b8beb11a2795",
        "client_secret" => "gOi8Q~uVwfoHScxEDFgbidtOxmle~XlJXmdMVavt",
        "redirect_url" => "https://exxen.spechy.live/npbx/connect",
        "media_url" => "https://exxen.spechy.live/npbx/saveMedia",
        "scope" => "https://graph.microsoft.com/.default offline_access",
        "grant_type" => "client_credentials",
        "database" => [
            "host" => "141.98.204.51",
            "dbname" => "spechycrm",
            "username" => "spechy",
            "password" => "spca1b2c3"
        ]
    ]
];

define("APPLICATION", $application);
