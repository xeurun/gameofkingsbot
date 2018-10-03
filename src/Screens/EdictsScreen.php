<?php

namespace App\Screens;

use App\Entity\User;
use App\Factory\CallbackFactory;
use App\Interfaces\AdviserInterface;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;

class EdictsScreen extends BaseScreen
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

        $user = $this->botManager->getUser();
        $gender = $this->botManager->getTranslator()->transChoice(
            TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
            User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
            [],
            TranslatorInterface::TRANSLATOR_DOMAIN_STATE
        );

        $name = ScreenInterface::SCREEN_EDICTS;
        $bName = ScreenInterface::SCREEN_BUILDINGS;
        $pName = ScreenInterface::SCREEN_PEOPLE;
        $data = [
            'chat_id' => $this->botManager->getUser()->getId(),
            'text' => '*Советник*: ' . $gender . " «{$name}» " . ' делятся на указы относящиеся к постройкам королества - ' . "«{$bName}»" . ' и указы относящиеся к людям королевства - ' . "«{$pName}»",
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
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_EDICTS;
        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_EDICTS_SCREEN_MESSAGE,
            [
                '%title%' => $title,
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $keyboard = new Keyboard(
            ['text' => ScreenInterface::SCREEN_BUILDINGS],
            ['text' => ScreenInterface::SCREEN_PEOPLE],
            ['text' => ScreenInterface::SCREEN_BACK]
        );

        //Return a random keyboard.
        $keyboard = $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);

        $data = [
            'chat_id' => $kingdom->getUser()->getId(),
            'text' => $text,
            'reply_markup' => $keyboard,
            'parse_mode' => 'Markdown',
        ];

        Request::sendMessage($data);

        if (AdviserInterface::ADVISER_SHOW_EDICTS_TUTORIAL === $this->botManager->getKingdom()->getAdviserState()) {
            $this->sendAdvice();
        }
    }
}
