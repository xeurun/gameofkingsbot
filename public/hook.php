<?php

use Longman\TelegramBot\TelegramLog;

require_once __DIR__ . '/../src/bootstrap.php';

try {
    TelegramLog::initErrorLog('php://stderr');
    TelegramLog::initDebugLog('php://stdout');
    TelegramLog::initUpdateLog('php://stdout');

    $bot = new Bot();
    $bot->handle();
} catch (Longman\TelegramBot\Exception\TelegramException $ex) {
    // log telegram errors
    dump($ex);
} catch (\Throwable $ex) {
    // log telegram errors
    dump($ex);
}
