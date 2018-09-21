<?php

namespace App\Screens;

use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use App\Packages\UpDownCallbackDataPack;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class PeopleScreen extends BaseScreen
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
     * @return array
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function getMessageData(): array
    {
        $kingdom = $this->botManager->getKingdom();

        $title = ScreenInterface::SCREEN_PEOPLE;

        $taxLevel = $this->peopleManager->taxLevel($kingdom);
        $eatHourly = $this->peopleManager->eat($kingdom);
        $payHourly = $this->peopleManager->pay($kingdom);

        $foodHourly = $this->workManager->food($kingdom);
        $woodHourly = $this->workManager->wood($kingdom);
        $stoneHourly = $this->workManager->stone($kingdom);
        $metalHourly = $this->workManager->metal($kingdom);

        $free = $this->workManager->free($kingdom);

        $text = <<<TEXT
*{$title}*

Всего людей: {$kingdom->getPeople()} съедают {$eatHourly}ед. еды в час
Уровень налогов: {$taxLevel}, в час {$payHourly}ед. золота

Свободно людей: {$free}

{$kingdom->getOnFood()} людей заняты добычей еды, в час {$foodHourly}
{$kingdom->getOnWood()} людей заняты добычей древесины, в час {$woodHourly}
{$kingdom->getOnStone()} людей заняты добычей камней, в час {$stoneHourly}
{$kingdom->getOnMetal()} людей заняты добычей железа, в час {$metalHourly}
{$kingdom->getOnBuildings()} людей заняты постройкой
TEXT;

        $inlineKeyboard = new InlineKeyboard(
            [
                ['text' => '⬇️ Понизить налог', 'callback_data' => json_encode(['n' => CallbackInterface::CALLBACK_UP_DOWN_TAX, 'v' => '-'])],
                ['text' => 'Увеличить налог ⬆️', 'callback_data' => json_encode(['n' => CallbackInterface::CALLBACK_UP_DOWN_TAX, 'v' => '+'])],
            ],
            [
                ['text' => '⬇️ С добычи еды', 'callback_data' => json_encode(['n' => CallbackInterface::CALLBACK_UP_DOWN_WORKER, 't' => 'food', 'v' => '-'])],
                ['text' => 'На добычу еды ⬆', 'callback_data' => json_encode(['n' => CallbackInterface::CALLBACK_UP_DOWN_WORKER, 't' => 'food', 'v' => '+'])],
            ],
            [
                ['text' => '⬇️ С добычи древесины', 'callback_data' => json_encode(['n' => CallbackInterface::CALLBACK_UP_DOWN_WORKER, 't' => 'wood', 'v' => '-'])],
                ['text' => 'На добычу древесины ⬆', 'callback_data' => json_encode(['n' => CallbackInterface::CALLBACK_UP_DOWN_WORKER, 't' => 'wood', 'v' => '+'])],
            ],
            [
                ['text' => '⬇️ С добычи камней', 'callback_data' => json_encode(['n' => CallbackInterface::CALLBACK_UP_DOWN_WORKER, 't' => 'stone', 'v' => '-'])],
                ['text' => 'На добычу камней ⬆', 'callback_data' => json_encode(['n' => CallbackInterface::CALLBACK_UP_DOWN_WORKER, 't' => 'stone', 'v' => '+'])],
            ],
            [
                ['text' => '⬇️ С добычи железа', 'callback_data' => json_encode(['n' => CallbackInterface::CALLBACK_UP_DOWN_WORKER, 't' => 'metal', 'v' => '-'])],
                ['text' => 'На добычу железа ⬆', 'callback_data' => json_encode(['n' => CallbackInterface::CALLBACK_UP_DOWN_WORKER, 't' => 'metal', 'v' => '+'])],
            ],
            [
                ['text' => '⬇️ С построек', 'callback_data' => json_encode(['n' => CallbackInterface::CALLBACK_UP_DOWN_WORKER, 't' => 'buildings', 'v' => '-'])],
                ['text' => 'На постройки ⬆️', 'callback_data' => json_encode(['n' => CallbackInterface::CALLBACK_UP_DOWN_WORKER, 't' => 'buildings', 'v' => '+'])],
            ]
        );

        return [
            'chat_id'      => $kingdom->getUser()->getId(),
            'text'         => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode'   => 'Markdown',
        ];
    }

    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        return Request::sendMessage($this->getMessageData());
    }
}
