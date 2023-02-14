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
    protected $company;

    /**
     * MicrosoftOutlookGraph constructor.
     * @param $account
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct()
    {
        //$this->account = $account;

        //$this->setGraph();
    }

    /**
     *
     */
    public function authorization($company){
        global $sql;

        if (!in_array($this->lastVariableBender($company),array_keys(APPLICATION))){
            die("Invalid Company");
        }

        $this->company = $this->lastVariableBender($company);

        $provider = $this->authProvider();
        $authorizationUrl = $provider->getAuthorizationUrl();

        $sqlquery = "INSERT INTO accounts (company,state,created_at,updated_at) VALUES (?,?,?,?)";
        $sqlquery = $sql->prepare($sqlquery);
        $sqlquery->bindvalue(1, $this->lastVariableBender($company), PDO::PARAM_STR);
        $sqlquery->bindvalue(2, $provider->getState(), PDO::PARAM_STR);
        $sqlquery->bindvalue(3, Date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $sqlquery->bindvalue(4, Date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $sqlquery->execute();


        header('Location: ' . $authorizationUrl);
        exit();
    }

    /**
     * @return \League\OAuth2\Client\Provider\GenericProvider
     */
    private function authProvider(){
        return new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => APPLICATION[$this->company]["client_id"],
            'clientSecret'            => APPLICATION[$this->company]["client_secret"],
            'redirectUri'             => "https://callcenter.spechy.live/microsoft/exxen.php",
            'urlAuthorize'            => "https://login.microsoftonline.com/".APPLICATION[$this->company]["tenant_id"]."/oauth2/v2.0/authorize",
            'urlAccessToken'          => "https://login.microsoftonline.com/".APPLICATION[$this->company]["tenant_id"]."/oauth2/v2.0/token",
            'urlResourceOwnerDetails' => '',
            'scopes'                  => APPLICATION[$this->company]["scope"]
        ]);
    }

    /**
     * It is ready to authorize for requests to the microsoft graph library.
     */
    private function setGraph($access_token){
        $this->graph = new Graph();
        $this->graph->setBaseUrl("https://graph.microsoft.com/")
            ->setApiVersion("beta")
            ->setAccessToken($access_token);
    }

    /**
     *
     */
    public function setUserInfo(){
        global $sql;

        $sqlquery = $sql->prepare("SELECT * FROM accounts WHERE status = 1 AND email_address IS NULL");
        $sqlquery->execute();
        $accounts = $sqlquery->fetchAll(PDO::FETCH_OBJ);
        foreach ($accounts as $account) {
            $this->setGraph($account->access_token);
            $me = $this->graph->createRequest("GET", "/me")
                ->setReturnType(Model\User::class)
                ->execute();

            $sqlquery = "DELETE FROM accounts WHERE email_address = ?";
            $sqlquery = $sql->prepare($sqlquery);
            $sqlquery->bindvalue(1, $me->getMail(), PDO::PARAM_STR);
            $sqlquery->execute();

            $sqlquery = "UPDATE accounts SET email_address = ?, updated_at = ? WHERE account_id = ?";
            $sqlquery = $sql->prepare($sqlquery);
            $sqlquery->bindvalue(1, $me->getMail(), PDO::PARAM_STR);
            $sqlquery->bindvalue(2, Date("Y-m-d H:i:s"), PDO::PARAM_STR);
            $sqlquery->bindvalue(3, $account->account_id, PDO::PARAM_INT);
            $sqlquery->execute();
        }
    }

    public function sendMail(string $email_address,string $subject,string $body,$toRecipients){
        global $sql;

        $sqlquery = $sql->prepare("SELECT * FROM accounts WHERE email_address = ? AND status = 1");
        $sqlquery->bindvalue(1, $this->lastVariableBender($email_address), PDO::PARAM_STR);
        $sqlquery->execute();
        $account = $sqlquery->fetch(PDO::FETCH_OBJ);

        if (!empty($account->account_id)){
            $this->setGraph($account->access_token);

            $mailBody = [
                "Message" => [
                    "subject" => $this->lastVariableBender($subject),
                    "body" => [
                        "contentType" => "html",
                        "content" => $this->lastVariableBender($body)
                    ],
                    "toRecipients" => [],
                ]
            ];

            foreach ($toRecipients as $toRecipient) {
                $mailBody["Message"]["toRecipients"][] = [
                    "emailAddress" => [
                        "address" => $this->lastVariableBender($toRecipient)
                    ]
                ];
            }

            return $this->graph->createRequest("POST", "/me/sendMail")
                ->attachBody($mailBody)
                ->execute();
        }
    }

    public function getMail(string $email_address){
        global $sql;

        $sqlquery = $sql->prepare("SELECT * FROM accounts WHERE email_address = ? AND status = 1");
        $sqlquery->bindvalue(1, $this->lastVariableBender($email_address), PDO::PARAM_STR);
        $sqlquery->execute();
        $account = $sqlquery->fetch(PDO::FETCH_OBJ);

        if (!empty($account->account_id)){

            try {
                $sql2 = new PDO('mysql:dbname='.APPLICATION[$account->company]["database"]["dbname"].';host='.APPLICATION[$account->company]["database"]["host"].';charset=utf8',''.APPLICATION[$account->company]["database"]["username"].'',''.APPLICATION[$account->company]["database"]["password"].'',array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                $sql2->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                $sql2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $sql2->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                $sql2->bConnected = true;

                $sqlquery = $sql2->prepare("SELECT * FROM emailayarlari WHERE mailadresi = ? AND durum = 1 AND isews = 2");
                $sqlquery->bindvalue(1, $this->lastVariableBender($email_address), PDO::PARAM_STR);
                $sqlquery->execute();
                $mail_account = $sqlquery->fetch(PDO::FETCH_OBJ);
                if (!empty($mail_account->emailid)){
                    $this->setGraph($account->access_token);

                    $mails = $this->graph->createRequest("GET", '/me/messages?$search="received >= 2023-02-09"')
                        ->setReturnType(Model\Group::class)
                        ->execute();

                    foreach ($mails as $mail) {
                        $mail = $mail->getProperties();

                        $mail_id = $mail["id"];
                        $mail_send_date = Date("Y-m-d H:i:s",strtotime($mail["sentDateTime"]));
                        $mail_received_date = Date("Y-m-d H:i:s",strtotime($mail["receivedDateTime"]));

                        $subject = isset($mail["subject"]) ? $mail["subject"] : "";
                        $sender_name = isset($mail["sender"]["emailAddress"]["name"]) ? $mail["sender"]["emailAddress"]["name"] : "";
                        $sender = isset($mail["sender"]["emailAddress"]["address"]) ? $mail["sender"]["emailAddress"]["address"] : "";
                        $body = isset($mail["body"]["content"]) ? $mail["body"]["content"] : "";
                        $reply = isset($mail["replyTo"]["emailAddress"]["address"]) ? $mail["replyTo"]["emailAddress"]["address"] : $sender;


                        $to_recipients = "";
                        if(isset($mail["toRecipients"])){
                            foreach ($mail["toRecipients"] as $recipient) {
                                $to_recipients = empty($to_recipients) ? $recipient["emailAddress"]["address"] : $to_recipients . "," . $recipient["emailAddress"]["address"];
                            }
                        }

                        $cc_recipients = "";
                        if(isset($mail["ccRecipients"])){
                            foreach ($mail["ccRecipients"] as $recipient) {
                                $cc_recipients = empty($cc_recipients) ? $recipient["emailAddress"]["address"] : $cc_recipients . "," . $recipient["emailAddress"]["address"];
                            }
                        }

                        echo "MAIL ID : " . $mail_id . "<br>";
                        echo "SEND_DATE : " . $mail_send_date . "<br>";
                        echo "RECEIVED_DATE : " . $mail_received_date . "<br>";
                        echo "SENDER : " . $sender . "<br>";
                        echo "SENDERNAME : " . $sender_name . "<br>";
                        echo "SUBJECT : " . $subject . "<br>";
                        echo "TO_RECIPIENTS : " . $to_recipients . "<br>";
                        echo "CC_RECIPIENTS : " . $cc_recipients . "<br>";
                        echo "REPLY : " . $reply . "<br>";
                        echo "<hr>";

                        echo "<pre>";
                        print_r($mail);

                        //mail check
                        $remote_mail = $sql2->query("SELECT count(*) FROM gelenMailBilgiler
                            where (gonderilmeTrh = ? OR mgid = ?) and kime = ? and kimden = ?")->fetchColumn();
                        $remote_mail->bindvalue(1, $mail_send_date, PDO::PARAM_STR);
                        $remote_mail->bindvalue(2, $mail_id, PDO::PARAM_STR);
                        $remote_mail->bindvalue(3, $to_recipients, PDO::PARAM_STR);
                        $remote_mail->bindvalue(4, $sender, PDO::PARAM_STR);
                        $remote_mail->execute();
                        $remote_mail = $remote_mail->fetch(PDO::FETCH_OBJ);
                        if (empty($remote_mail->id)){

                            $transaction = "SPC" . mt_rand(1000000, 999999999);
                            $status = "";
                            $mailType = 1;
                            $is_seen = 0;

                            // //içerik ve ekler hariç diğer bilgiler kaydedilmesi için
                            $izStmt = $sql2->prepare("INSERT INTO gelenMailBilgiler(
                                siteAdi, kimden, kime, gonderilmeTrh, ulasmaTrh, email_id, goruldu, 
                                mgid, konu, CC, yanitAdresi, personal, transactionID,durum,mailType,visibility) 
                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                            $izStmt->bindParam(1, $mail_account->mailadresi, PDO::PARAM_STR);
                            $izStmt->bindParam(2, $sender, PDO::PARAM_STR);
                            $izStmt->bindParam(3, $to_recipients, PDO::PARAM_STR);
                            $izStmt->bindParam(4, $mail_send_date, PDO::PARAM_STR);
                            $izStmt->bindParam(5, $mail_received_date, PDO::PARAM_STR);
                            $izStmt->bindParam(6, $mail_account->emailid, PDO::PARAM_INT);
                            $izStmt->bindParam(7, $is_seen, PDO::PARAM_INT);
                            $izStmt->bindParam(8, $mail_id, PDO::PARAM_STR);
                            $izStmt->bindParam(9, $subject, PDO::PARAM_STR);
                            $izStmt->bindParam(10, $cc_recipients, PDO::PARAM_STR);
                            $izStmt->bindParam(11, $reply, PDO::PARAM_STR);
                            $izStmt->bindParam(12, $sender_name, PDO::PARAM_STR);
                            $izStmt->bindParam(13, $transaction, PDO::PARAM_STR);
                            $izStmt->bindParam(14, $status, PDO::PARAM_STR);
                            $izStmt->bindParam(15, $mailType, PDO::PARAM_INT);
                            $izStmt->bindParam(16, $mail_account->visibility, PDO::PARAM_STR);
                            $izStmt->execute();

                            $last_id = $sql2->lastInsertId();

                            $isMStmt = $sql2->prepare("INSERT INTO gelenMailIcerik(
                                mail_id, icerik, ekler, mgid, dosyaAdi) 
                                VALUES(?,?,?,?,?)");
                            $isMStmt->bindParam(1, $last_id, PDO::PARAM_INT);
                            $isMStmt->bindValue(2, base64_encode($body), PDO::PARAM_STR);
                            $isMStmt->bindValue(3, "", PDO::PARAM_STR);
                            $isMStmt->bindParam(4, $mail_id, PDO::PARAM_STR);
                            $isMStmt->bindValue(5, "", PDO::PARAM_STR);
                            $isMStmt->execute();

                            $data = array(
                                'type' => "3",
                                'sessionID' => $last_id,
                                'transactionID' => $transaction, // transaction id, bir nevi unique ID
                                'data' => array(
                                    'konu' => $subject, // mail konusu
                                    'email' => $sender, // maili gönderen kişinin maili
                                    'kime' => $to_recipients, // mail sahibi
                                    'islemtarih' => date("Y-m-d H:i:s"), // işlem tarihi
                                    'name' => $sender_name, // maili gönderen kişinin ismi
                                    'agent' => "" //
                                ),
                                'visibility' => $mail_account->visibility
                            );

                            $this->sendCurlRequest("https://callcenter.spechy.live/npbx/connect", $data);
                        }
                        exit();
                    }
                }

            } catch(PDOException $e) {
                echo $e->getMessage();
                die();
            }
            print_r($mails[0]);
        }
    }

    private function sendCurlRequest($url, $parameters){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',));

        $result = curl_exec($ch);
        // $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return $result;
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
        $filtered = filter_var($value, FILTER_UNSAFE_RAW);

        //clear space
        $value = trim($value);

        return $value;
    }
}
