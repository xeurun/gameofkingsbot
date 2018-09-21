<?php

namespace App\Screens;

use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class BuildingsScreen extends BaseScreen
{
    protected $workManager;
    protected $peopleManager;

    public function __construct(BotManager $botManager, WorkManager $workManager, PeopleManager $peopleManager)
    {
        $this->workManager = $workManager;
        $this->peopleManager = $peopleManager;
        parent::__construct($botManager);
    }

    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_BUILDINGS;

        $free = $this->workManager->free($kingdom);

        $text = <<<TEXT
*{$title}*

Свободно людей: {$free}
Людей на постройках: {$kingdom->getOnBuildings()}

Построено жилых домов: 0
Построено хранилищ еды: 0
Построено лесопилок: 0
Построено каменоломен: 0
Построено плавильни: 0

TEXT;
        $inlineKeyboard = new InlineKeyboard(
            [
                ['text' => 'Построить жилой дом', 'callback_data' => CallbackInterface::CALLBACK_MOCK],
            ],
            [
                ['text' => 'Пестроить хранилище еды', 'callback_data' => CallbackInterface::CALLBACK_MOCK],
            ],
            [
                ['text' => 'Построить лесопилку', 'callback_data' => CallbackInterface::CALLBACK_MOCK],
            ],
            [
                ['text' => 'Построить каменоломню', 'callback_data' => CallbackInterface::CALLBACK_MOCK],
            ],
            [
                ['text' => 'Построить плавильню', 'callback_data' => CallbackInterface::CALLBACK_MOCK],
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
