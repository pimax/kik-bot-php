<?php

include_once dirname(__FILE__).'/../bootstrap.php';

use lib\KikApi;
use objects\Message;

$bot = new KikApi(
    "job4joybot",
    "54aaa657-0457-40f2-8abc-fb5697b8bf3a"
//"https://job4joy.com/apps/kik/"
);

/*
$res = $bot->send(new Message([
    'type' => Message::TYPE_TEXT,
    'body' => "All message",
    'to' => "ipimax",
    'chatId' => "83fe4d03603ba009eb2e2872c7142d4004fd8d098743c95d6c8257aed47a3241",
    'keyboards' => [
        [
            'type' => 'suggested',
            'responses' => [
                [
                    'type' => 'text',
                    'body' => 'Good :)'
                ],
                [
                    'type' => 'text',
                    'body' => 'Not so good :('
                ]
            ]
        ]
    ]
]));
*/

// Access from KIK
if (empty($_REQUEST['config'])) {

    $inputJSON = file_get_contents('php://input');
    $data = json_decode( $inputJSON, true ); //convert JSON into array

    foreach ($data['messages'] as $message) {

        switch($message['type']) {
            case 'start-chatting':
                sendHelpMessage($bot, $message);
                break;

            default:
                switch ($message['body']) {
                    case 'All jobs':

                        getFeed('https://job4joy.com/marketplace/rss/', $bot, $message);
                        sendHelpMessage($bot, $message);

                        break;

                    // webdev feed
                    case 'Web Development':
                        getFeed('https://job4joy.com/marketplace/rss/?id=3', $bot, $message);
                        sendHelpMessage($bot, $message);
                        break;

                    // software feed
                    case 'Software Development & IT':
                        getFeed('https://job4joy.com/marketplace/rss/?id=5', $bot, $message);
                        sendHelpMessage($bot, $message);
                        break;

                    // design feed
                    case 'Design & Multimedia':
                        getFeed('https://job4joy.com/marketplace/rss/?id=2', $bot, $message);
                        sendHelpMessage($bot, $message);
                        break;

                    // mobile feed
                    case 'Mobile Application':
                        getFeed('https://job4joy.com/marketplace/rss/?id=7', $bot, $message);
                        sendHelpMessage($bot, $message);
                        break;

                    // server feed
                    case 'Host & Server Management':
                        getFeed('https://job4joy.com/marketplace/rss/?id=6', $bot, $message);
                        sendHelpMessage($bot, $message);
                        break;

                    // writing feed
                    case 'Writing':
                        getFeed('https://job4joy.com/marketplace/rss/?id=8', $bot, $message);
                        sendHelpMessage($bot, $message);
                        break;

                    // customer feed
                    case 'Customer Service':
                        getFeed('https://job4joy.com/marketplace/rss/?id=10', $bot, $message);
                        sendHelpMessage($bot, $message);
                        break;

                    // marketing feed
                    case 'Marketing':
                        getFeed('https://job4joy.com/marketplace/rss/?id=11', $bot, $message);
                        sendHelpMessage($bot, $message);
                        break;

                    // business feed
                    case 'Business Services':
                        getFeed('https://job4joy.com/marketplace/rss/?id=12', $bot, $message);
                        sendHelpMessage($bot, $message);
                        break;

                    // translation feed
                    case 'Translation & Languages':
                        getFeed('https://job4joy.com/marketplace/rss/?id=14', $bot, $message);
                        sendHelpMessage($bot, $message);
                        break;

                    default:

                        sendHelpMessage($bot, $message);
                }
        }

        /*$res = $bot->send(new Message([
            'type' => Message::TYPE_TEXT,
            'body' => "All message",
            'to' => $message['from'],
            'chatId' => $message['chatId'],
            'keyboards' => [
                [
                    'type' => 'suggested',
                    'responses' => [
                        [
                            'type' => 'text',
                            'body' => 'Good :)'
                        ],
                        [
                            'type' => 'text',
                            'body' => 'Not so good :('
                        ]
                    ]
                ]
            ]


            //'url' => 'http://yandex.ru'
        ]));*/

        /*$res = $bot->send(new Message([
            'type' => Message::TYPE_LINK,
            'text' => "All message",
            'title' => "Link title",
            'to' => $message['from'],
            'chatId' => $message['chatId'],
            'url' => 'http://yandex.ru'
        ]));*/

        //writeToLog($res);

    }

}


function getFeed($url, $bot, $message)
{
    global $googl;

    $arReport = array();

    try {

        //writeToLog($url, 'Feed');
        $reader = new Reader;

        //writeToLog($reader, 'Feed');


        $resource = $reader->download($url);

        //writeToLog($resource, 'Feed');

        $parser = $reader->getParser(
            $resource->getUrl(),
            $resource->getContent(),
            $resource->getEncoding()
        );

        $feed = $parser->execute();

        $items = array_reverse($feed->getItems());

        //echo '<pre>', print_r($items), '</pre>';

        //writeToLog($items, 'Feed');

        if (count($items)) {
            $arTasks = array();

            foreach ($items as $itm)
            {
                $url = $googl->shorten($itm->getUrl())->id;

                $message_text = substr(strip_tags($itm->getContent()), 0, 150);

                $res = $bot->send(new Message([
                    'type' => Message::TYPE_LINK,
                    'text' => $message_text,
                    'title' => $itm->getTitle(),
                    'to' => $message['from'],
                    'chatId' => $message['chatId'],
                    'url' => $url
                ]));
            }

        } else {

            $res = $bot->send(new Message([
                'type' => Message::TYPE_TEXT,
                'body' => "Not found a new projects in this section.",
                'to' => $message['from'],
                'chatId' => $message['chatId'],
            ]));
        }
    }
    catch (Exception $e) {
        // Do something...

        writeToLog($e->getMessage(), 'Exception');

    }

    return true;
}

function sendHelpMessage($bot, $message)
{
    $bot->send(new Message([
        'type' => Message::TYPE_TEXT,
        'body' => "Please select category.",
        'to' => $message['from'],
        'chatId' => $message['chatId'],
        'keyboards' => [
            [
                'type' => 'suggested',
                'responses' => [
                    [
                        'type' => 'text',
                        'body' => 'All jobs'
                    ],
                    [
                        'type' => 'text',
                        'body' => 'Web Development'
                    ],
                    [
                        'type' => 'text',
                        'body' => 'Software Development & IT'
                    ],
                    [
                        'type' => 'text',
                        'body' => 'Design & Multimedia'
                    ],
                    [
                        'type' => 'text',
                        'body' => 'Mobile Application'
                    ],
                    [
                        'type' => 'text',
                        'body' => 'Host & Server Management'
                    ],
                    [
                        'type' => 'text',
                        'body' => 'Writing'
                    ],
                    [
                        'type' => 'text',
                        'body' => 'Customer Service'
                    ],
                    [
                        'type' => 'text',
                        'body' => 'Marketing'
                    ],
                    [
                        'type' => 'text',
                        'body' => 'Business Services'
                    ],
                    [
                        'type' => 'text',
                        'body' => 'Translation & Languages'
                    ]
                ]
            ]
        ]
    ]));
}


function writeToLog($data, $title = '') {
    $log = "\n------------------------\n";
    $log .= date("Y.m.d G:i:s") . "\n";
    $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
    $log .= print_r($data, 1);
    $log .= "\n------------------------\n";
    file_put_contents(__DIR__ . '/imbot.log', $log, FILE_APPEND);
    return true;
}