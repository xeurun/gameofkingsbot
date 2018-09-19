<?php

namespace App\Screens;

use Longman\TelegramBot\Entities\ServerResponse;
use App\Responses\BackResponse;

class TODOScreen extends BaseScreen
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $text = <<<TEXT
*{$this->title}*

В разработке
TEXT;

        return (new BackResponse($this->chatId, $text))->execute();
    }
}
