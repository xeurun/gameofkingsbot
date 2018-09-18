<?php

use Longman\TelegramBot\TelegramLog;

require_once __DIR__ . '/../src/bootstrap.php';

try {
    TelegramLog::initErrorLog('php://stderr');
    TelegramLog::initDebugLog('php://stdout');
    TelegramLog::initUpdateLog('php://stdout');

    $bot = new Bot();
    $text = $_GET['message'];
    $bot->getApp()->setCustomInput('{
        "update_id":119409284, 
        "message": {
            "message_id":84,
            "from":{
                "id":191000234,
                "is_bot":false,
                "first_name":"Alexey",
                "last_name":"Stepankov",
                "username":"alexeystepankov",
                "language_code":"en-US"
            },
            "chat":{
                "id":191000234,
                "first_name":"Alexey",
                "last_name":"Stepankov",
                "username":"alexeystepankov",
                "type":"private"
            },
            "date":1537300010,
            "text":"' . $text . '"
        }
    }');
    $bot->handle();
} catch (Longman\TelegramBot\Exception\TelegramException $ex) {
    // log telegram errors
    dump($ex);
} catch (\Throwable $ex) {
    // log telegram errors
    dump($ex);
}
