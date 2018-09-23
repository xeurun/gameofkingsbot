<?php

namespace App\Screens;

use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class BonusesScreen extends BaseScreen
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_BONUSES;
        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_BONUSES_SCREEN_MESSAGE,
            [
                '%title%' => $title
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );
        $inlineKeyboard = new InlineKeyboard(
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_ENTER_TO_GROUP_BUTTON,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_INLINE
                    ),
                    'url' => 'https://t.me/worldofkings'
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_SUBSCRIBE_ON_CHANNEL_BUTTON,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_INLINE
                    ),
                    'url' => 'https://t.me/placeofkings'
                ],
            ], [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_RECEIVE_EVERY_DAY_BONUSES_BUTTON,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_INLINE
                    ),
                    'callback_data' => CallbackInterface::CALLBACK_EVERY_DAY_BONUS
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
