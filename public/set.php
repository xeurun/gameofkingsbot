<?php

require_once __DIR__ . '/../src/bootstrap.php';

try {
    $bot = new Bot();
    $bot->setWebHook();
} catch (Longman\TelegramBot\Exception\TelegramException $ex) {
    // log telegram errors
    dump($ex);
} catch (\Throwable $ex) {
    // log telegram errors
    dump($ex);
}
