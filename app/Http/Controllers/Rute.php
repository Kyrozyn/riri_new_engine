<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class Rute extends Controller
{
    public function index(Request $req, Response $res)
    {
        $httpClient = new CurlHTTPClient(env('channel_access_token'));
        $channel_secret = env('channel_secret');
        $bot = new LINEBot($httpClient, ['channelSecret' => $channel_secret]);

        $signature = $req->header(HTTPHeader::LINE_SIGNATURE);
        $result = null;
        if (empty($signature)) {
        }

        try {
            $events = $bot->parseEventRequest($req->getContent(), $signature[0]);
        } catch (Exception $e) {
            error_log('Exception didalam Parse Event ='.$e->getMessage());
            $events = null;
        }

        /** @var LINEBot\Event\BaseEvent $ev */
        foreach ($events as $ev) {
            $text = $ev->getText();

            try {
                $bot->replyText($ev->getReplyToken(), $text);
            } catch (\ReflectionException $e) {
                error_log($e->getMessage());
            }
        }
    }
}
