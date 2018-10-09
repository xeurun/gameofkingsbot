<?php

namespace App\Screens\Achivements;

use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use App\Screens\BaseScreen;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;

class TopScreen extends BaseScreen
{
    /**
     * {@inheritdoc}
     *
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): void
    {
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_TOP;
        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_ACHIVEMENTS_SCREEN_MESSAGE,
            [
                '%title%' => $title,
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $keyboard = new Keyboard(
            ['text' => ScreenInterface::SCREEN_TOP_PEOPLES],
            ['text' => ScreenInterface::SCREEN_TOP_RESOURCES],
            ['text' => ScreenInterface::SCREEN_TOP_STRUCTURES],
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
    }
}
