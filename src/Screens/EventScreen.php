<?php

namespace App\Screens;

use App\Interfaces\ScreenInterface;
use App\Interfaces\TaxesInterface;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class EventScreen extends BaseScreen
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
        $title = ScreenInterface::SCREEN_EVENT;

        // Смотрим последнюю дату проверки событий и выводим
        $text = <<<TEXT
*{$title}*

За последние дни никто не умер...
TEXT;

        $data = [
            'chat_id'      => $kingdom->getUser()->getId(),
            'text'         => $text,
            'parse_mode'   => 'Markdown',
        ];

        return Request::sendMessage($data);
    }
}
