<?php

namespace App\Screens;

use App\Entity\User;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use App\Responses\BackResponse;

class SettingsScreen extends BaseScreen
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $kingdom = $this->botManager->getKingdom();
        $userRepository = $this->botManager->getEntityManager()->getRepository(User::class);
        $title = ScreenInterface::SCREEN_SETTINGS;
        $text = <<<TEXT
*{$title}*

Название вашего королевства: *{$kingdom->getName()}*

Всего королевств: *{$userRepository->count([])}*

Администрация: 
TEXT;

        $inlineKeyboard = new InlineKeyboard(
            [
                ['text' => 'Изменить название королества', 'callback_data' => CallbackInterface::CALLBACK_CHANGE_KINGDOM_NAME],
            ]
        );

        $data = [
            'chat_id'      => $kingdom->getUser()->getId(),
            'text'         => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode'   => 'Markdown',
        ];

        return Request::sendMessage($data);
    }
}
