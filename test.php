<?php

require_once realpath(__DIR__ . '/vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


class Email
{

    protected string $api_key;

    protected string $template_id;

    protected string $from_email;

    protected string $from_name;

    public function __construct()
    {
        $this->api_key =  $_ENV['GO2_API_KEY'];
        $this->template_id = $_ENV['TEMPLATE_ID'];
        $this->from_email = $_ENV['FROM_EMAIL'];
        $this->from_name = $_ENV['FROM_NAME'];
    }

    function email_send(string $to_email, string $to_name = '')
    {

        $headers = array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-API-KEY' => $this->api_key,
        );

        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://go2.unisender.ru/ru/transactional/api/v1/'
        ]);

        $requestBody = [
            "message" => [
                "recipients" => [
                    [
                        "email" => $to_email,
                        "substitutions" => [
                            "to_name" => $to_name
                        ],
                    ]
                ],
                "template_id" => $this->template_id,
                "skip_unsubscribe" => 0,
                "global_language" => "ru",
                "template_engine" => "simple",
                "body" => [
                    "plaintext" => "Здравствуйте, {{to_name}}",
                ],
                "subject" => "Спасибо за пожертвование",
                "from_email" => $this->from_email,
                "from_name" => $this->from_name,
                "reply_to" => $this->from_email,
                "reply_to_name" => $this->from_name,
                "track_links" => 0,
                "track_read" => 0,
                "bypass_global" => 0,
                "bypass_unavailable" => 0,
                "bypass_unsubscribed" => 0,
                "bypass_complained" => 0,
            ]
        ];

        try {
            $response = $client->request(
                'POST',
                'email/send.json',
                array(
                    'headers' => $headers,
                    'json' => $requestBody,
                )
            );
            return $response->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            // handle exception or api errors.
            return $e->getMessage();
        }
    }
}

$email = new Email;

$result = $email->email_send($_ENV['EMAIL_TO'], $_ENV['NAME_TO']);

print_r($result);
