<?php

date_default_timezone_set('Europe/Istanbul');

require_once "./sql.php"; // SQL
require './vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class MicrosoftOutlookGraph {

    protected $account;
    protected $graph;

    /**
     * MicrosoftOutlookGraph constructor.
     * @param $account
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct()
    {
        //$this->account = $account;

        //$this->account["access_token"] = $this->getAccessToken();
        //$this->setGraph();
    }

    /**
     *
     */
    public function authorization(){
        global $sql;

        $provider = $this->authProvider();
        $authorizationUrl = $provider->getAuthorizationUrl();

        $sqlquery = "INSERT INTO accounts (state,created_at,updated_at) VALUES (?,?,?)";
        $sqlquery = $sql->prepare($sqlquery);
        $sqlquery->bindvalue(1, $provider->getState(), PDO::PARAM_STR);
        $sqlquery->bindvalue(2, Date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $sqlquery->bindvalue(3, Date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $sqlquery->execute();


        header('Location: ' . $authorizationUrl);
        exit();
    }

    private function authProvider(){
        return new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => APPLICATION["client_id"],
            'clientSecret'            => APPLICATION["client_secret"],
            'redirectUri'             => "https://callcenter.spechy.live/microsoft/exxen.php",
            'urlAuthorize'            => "https://login.microsoftonline.com/".APPLICATION["tenant_id"]."/oauth2/v2.0/authorize",
            'urlAccessToken'          => "https://login.microsoftonline.com/".APPLICATION["tenant_id"]."/oauth2/v2.0/token",
            'urlResourceOwnerDetails' => '',
            'scopes'                  => APPLICATION["scope"]
        ]);
    }

    public function refreshAccessToken(){
        global $sql;

        $sqlquery = $sql->prepare("SELECT * FROM accounts WHERE status = 1 AND access_token IS NULL");
        $sqlquery->execute();
        $accounts = $sqlquery->fetchAll(PDO::FETCH_OBJ);
        foreach ($accounts as $account) {
            $access_token = $this->getAccessToken($account->code);

            $sqlquery = "UPDATE accounts SET access_token = ?, updated_at = ? WHERE account_id = ?";
            $sqlquery = $sql->prepare($sqlquery);
            $sqlquery->bindvalue(1, $access_token, PDO::PARAM_STR);
            $sqlquery->bindvalue(2, $account->account_id, PDO::PARAM_STR);
            $sqlquery->bindvalue(3, Date("Y-m-d H:i:s"), PDO::PARAM_INT);
            $sqlquery->execute();
        }
    }

    /**
     * Generates an access token for the desired scope.
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getAccessToken($code){
        $provider = $this->authProvider();
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code'     => $code
        ]);
        return $accessToken->getToken();

    }

    /**
     * It is ready to authorize for requests to the microsoft graph library.
     */
    private function setGraph(){
        $this->graph = new Graph();
        $this->graph->setBaseUrl("https://graph.microsoft.com/")
            ->setApiVersion("beta")
            ->setAccessToken($this->account["access_token"]);
    }

    public function sendMail(string $subject,string $body,array $toRecipients){
        $mailBody = [
            "Message" => [
                "subject" => $this->lastVariableBender($subject),
                "body" => [
                    "contentType" => "html",
                    "content" => $this->lastVariableBender($body)
                ],
                "toRecipients" => []
            ]
        ];

        foreach ($toRecipients as $toRecipient) {
            $mailBody["Message"]["toRecipients"][] = [
                "address" => $this->lastVariableBender($toRecipient)
            ];
        }

        return $this->graph->createRequest("POST", "/me/sendMail")
            ->attachBody($mailBody)
            ->execute();
    }

    /**
     * Filters variables from harmful characters
     * @param $value
     * @return string
     */
    private function lastVariableBender($value){

        // Use htmlspecialchars() to convert special characters to HTML entities
        $filtered = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Use strip_tags() to remove HTML and PHP tags
        $filtered = strip_tags($value);

        // Use filter_var() with the FILTER_SANITIZE_STRING filter to remove tags and encode special characters
        $filtered = filter_var($value, FILTER_SANITIZE_STRING);

        //clear space
        $value = trim($value);

        return $value;
    }
}
