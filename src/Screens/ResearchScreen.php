<?php

namespace App\Screens;

use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use App\Responses\BackResponse;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

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
        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_RESEACRH_SCREEN_MESSAGE,
            [
                '%title%' => $title
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $data = [
            'chat_id' => $kingdom->getUser()->getId(),
            'text' => $text,
            'parse_mode' => 'Markdown'
        ];

        return Request::sendMessage($data);
    }
}
