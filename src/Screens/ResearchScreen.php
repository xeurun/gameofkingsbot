<?php

namespace App\Screens;

use App\Factory\CallbackFactory;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use App\Interfaces\StructureInterface;
use App\Interfaces\TranslatorInterface;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class ResearchScreen extends BaseScreen
{
    /**
     * {@inheritdoc}
     *
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): void
    {
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_RESEARCH;

        $data = [
            'chat_id' => $kingdom->getUser()->getId(),
            'parse_mode' => 'Markdown',
        ];

        $library = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_LIBRARY);
        if (!$library) {
            $text = $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_RESEACRH_SCREEN_MESSAGE_WITHOUT_LIBRARY,
                [
                    '%title%' => $title,
                ],
                TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
            );
        } else {
            $text = $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_RESEACRH_SCREEN_MESSAGE,
                [
                    '%title%' => $title,
                ],
                TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
            );

            $researches = [
                [
                    [
                        'text' => 'Название',
                        'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_GET_INFO, 'name'),
                    ],
                    [
                        'text' => 'Изучить',
                        'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_INCREASE_STRUCTURE_LEVEL, 1),
                    ],
                ],
            ];

            $inlineKeyboard = new InlineKeyboard(
                ...$researches
            );

            $data['reply_markup'] = $inlineKeyboard;
        }

        $data['text'] = $text;

        Request::sendMessage($data);
    }
}
