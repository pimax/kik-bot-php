<?php

include_once dirname(__FILE__).'/../bootstrap.php';

use lib\KikApi;

$bot = new KikApi("botalias", "bot api key");

$bot->setConfiguration("https://bothost/webhook/", [
    'manuallySendReadReceipts' => false,
    'receiveReadReceipts' => false,
    'receiveIsTyping' => false,
    'receiveDeliveryReceipts' => false
]);