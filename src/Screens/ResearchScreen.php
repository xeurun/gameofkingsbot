<?php

namespace App\Screens;

use App\Interfaces\ScreenInterface;
use Longman\TelegramBot\Entities\ServerResponse;
use App\Responses\BackResponse;

class ResearchScreen extends BaseScreen
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_RESEARCH;
        $text = <<<TEXT
*{$title}*

Скоро вас ждет огромный выбор очень важных исследований
TEXT;

        return (new BackResponse($kingdom->getUser()->getId(), $text))->execute();
    }
}
