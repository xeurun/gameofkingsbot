<?php

namespace App\Screens;

use App\Entity\User;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

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

        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_SETTINGS_SCREEN_MESSAGE,
            [
                '%title%' => $title,
                '%count%' => $userRepository->count([]),
                '%name%' => $kingdom->getName(),
                '%admin%' => '@alexeystepankov'
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $inlineKeyboard = new InlineKeyboard(
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_CHANGE_KINGDOM_NAME_BUTTON,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_INLINE
                    ),
                    'callback_data' => CallbackInterface::CALLBACK_CHANGE_KINGDOM_NAME
                ],
            ]
        );

        $data = [
            'chat_id' => $kingdom->getUser()->getId(),
            'text' => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode' => 'Markdown',
        ];

        return Request::sendMessage($data);
    }
}
