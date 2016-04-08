<?php

include_once dirname(__FILE__).'/../bootstrap.php';

use lib\KikApi;
use objects\Message;

$bot = new KikApi("botalias", "bot api key");

$inputJSON = file_get_contents('php://input');
$data = json_decode( $inputJSON, true );

foreach ($data['messages'] as $message) {

    switch($message['type']) {

        // When user start chat with bot
        case 'start-chatting':
            sendHelpMessage($bot, $message);
            break;

        // Receive message from user
        default:

            // Switch by text from user
            switch ($message['body'])
            {
                case 'All jobs':

                    // Send message to user
                    $bot->send(new Message([
                        'type' => Message::TYPE_TEXT,
                        'body' => "All jobs response",
                        'to' => $message['from'],
                        'chatId' => $message['chatId'],
                    ]));

                    break;

                case 'Web Development':

                    // Send message to user
                    $bot->send(new Message([
                        'type' => Message::TYPE_TEXT,
                        'body' => "Web development response",
                        'to' => $message['from'],
                        'chatId' => $message['chatId'],
                    ]));

                    break;

                default:

                    sendHelpMessage($bot, $message);
            }
    }
}

/**
 * Send help message to Userwith suggested items
 *
 * @param $bot
 * @param $message
 */
function sendHelpMessage($bot, $message)
{
    $bot->send(new Message([
        'type' => Message::TYPE_TEXT,
        'body' => "Please select suggested item.",
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
                    ]
                ]
            ]
        ]
    ]));
}