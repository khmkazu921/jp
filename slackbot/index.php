<?php 

$responseURL = 'https://slack.com/api/chat.postMessage';

class Response {
    public $response_type = "";
    public $text = "";
	public $token = "";
	public $callback_id = "";
	public $channel = "";
	public $username = "";
}

$r = new Response();
$r->response_type = "in_channel";
$r->callback_id = "hello";
$r->channel = "#general";
$r->text = "こんにちは";
$r->token = "xoxp-52472058662-52486210481-239744287415-7fcaf4b89a3cb2aa300b0005c5da202e";
$r->username = "ふるかわ";

header('Content-Type: application/json');

//Using CURL
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $responseURL,
    CURLOPT_POST => 1,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS =>http_build_query($r)
));

$resp = curl_exec($curl);
echo var_dump($resp);
curl_close($curl);

?>
