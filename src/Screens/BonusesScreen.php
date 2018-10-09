<?php

namespace App\Screens;

use App\Factory\CallbackFactory;
use App\Interfaces\AdviserInterface;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class BonusesScreen extends BaseScreen
{
    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    protected function sendAdvice(): bool
    {
        $inlineKeyboard = new InlineKeyboard([
            [
                'text' => '✅ Продолжить',
                'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_ADVISER, 1),
            ],
            [
                'text' => 'Достаточно ❌',
                'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_ADVISER, 0),
            ],
        ]);

        $data = [
            'chat_id' => $this->botManager->getUser()->getId(),
            'text' => ' _Нам нравится делать подарки своим игрокам, поэтому каждый день к вашим услугам доступен наш вам подарок в виде игровых бонусов, приходите и забирайте!_ ',
            'reply_markup' => $inlineKeyboard,
            'parse_mode' => 'Markdown',
        ];

        $response = Request::sendMessage($data);

        return $response->isOk();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): void
    {
        $this->sendMessage();

        if (AdviserInterface::ADVISER_SHOW_BONUSES_TUTORIAL === $this->botManager->getKingdom()->getAdviserState()) {
            $this->sendAdvice();
        }
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    protected function sendMessage(): bool
    {
        $kingdom = $this->botManager->getKingdom();

        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_BONUSES_SCREEN_MESSAGE,
            [
                '%title%' => ScreenInterface::SCREEN_BONUSES,
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $inlineKeyboard = new InlineKeyboard(
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_SUBSCRIBE_ON_CHANNEL_BUTTON,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_INLINE
                    ),
                    'url' => 'https://t.me/placeofkings',
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_ENTER_TO_GROUP_BUTTON,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_INLINE
                    ),
                    'url' => 'https://t.me/worldofkings',
                ],
            ], [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_RECEIVE_EVERY_DAY_BONUSES_BUTTON,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_INLINE
                    ),
                    'callback_data' => CallbackInterface::CALLBACK_EVERY_DAY_BONUS,
                ],
            ]
        );

        $data = [
            'chat_id' => $kingdom->getUser()->getId(),
            'text' => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode' => 'Markdown',
        ];

        $response = Request::sendMessage($data);

        return $response->isOk();
    }
}
