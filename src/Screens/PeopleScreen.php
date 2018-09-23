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

`Всего в королевстве `*{$kingdom->getPeople()}* `людей, в час они съедают `*{$eatHourly}*` ед. еды 🍞`

`Уровень налогов `*{$taxLevel}*`, в час люди платят `*{$payHourly}*` ед. золота 💰 налогов`

`🏛️ Строителей - `*{$kingdom->getOnBuildings()}*` человек`

`🍞 Еда - добывают `*{$kingdom->getOnFood()}*`, в час `*{$foodHourly}*` ед.`
`🌲 Дерево - добывают `*{$kingdom->getOnWood()}*`, в час `*{$woodHourly}*` ед.`
`⛏ Камень - добывают `*{$kingdom->getOnStone()}*`, в час `*{$stoneHourly}*` ед.`
`🔨 Железо - добывают `*{$kingdom->getOnMetal()}*`, в час `*{$metalHourly}*` ед.`

`Свободно человек` - *{$free}*
TEXT;

        $pack = function ($name, $data) {
            $data['n'] = $name;
            return json_encode($data);
        };

        $inlineKeyboard = new InlineKeyboard(
            [
                ['text' => '📜 Налог', 'callback_data' => 'null'],
                ['text' => '⬇ Понизить', 'callback_data' => $pack(CallbackInterface::CALLBACK_UP_DOWN_TAX, ['v' => '-'])],
                ['text' => 'Увеличить ⬆', 'callback_data' => $pack(CallbackInterface::CALLBACK_UP_DOWN_TAX, ['v' => '+'])],
            ],
            [
                ['text' => '🏛️ Строители', 'callback_data' => 'null'],
                ['text' => '⬇ Уволить', 'callback_data' => $pack(CallbackInterface::CALLBACK_UP_DOWN_WORKER, ['t' => 'buildings', 'v' => '-'])],
                ['text' => 'Нанять ⬆', 'callback_data' => $pack(CallbackInterface::CALLBACK_UP_DOWN_WORKER, ['t' => 'buildings', 'v' => '+'])],
            ],
            [
                ['text' => '🍞 Еда', 'callback_data' => 'null'],
                ['text' => '⬇ Уволить', 'callback_data' => $pack(CallbackInterface::CALLBACK_UP_DOWN_WORKER, ['t' => 'food', 'v' => '-'])],
                ['text' => 'Нанять ⬆', 'callback_data' => $pack(CallbackInterface::CALLBACK_UP_DOWN_WORKER, ['t' => 'food', 'v' => '+'])],
            ],
            [
                ['text' => '🌲 Дерево', 'callback_data' => 'null'],
                ['text' => '⬇ Уволить', 'callback_data' => $pack(CallbackInterface::CALLBACK_UP_DOWN_WORKER, ['t' => 'wood', 'v' => '-'])],
                ['text' => 'Нанять ⬆', 'callback_data' => $pack(CallbackInterface::CALLBACK_UP_DOWN_WORKER, ['t' => 'wood', 'v' => '+'])],
            ],
            [
                ['text' => '⛏ Камень', 'callback_data' => 'null'],
                ['text' => '⬇ Уволить', 'callback_data' => $pack(CallbackInterface::CALLBACK_UP_DOWN_WORKER, ['t' => 'stone', 'v' => '-'])],
                ['text' => 'Нанять ⬆', 'callback_data' => $pack(CallbackInterface::CALLBACK_UP_DOWN_WORKER, ['t' => 'stone', 'v' => '+'])],
            ],
            [
                ['text' => '🔨 Железо', 'callback_data' => 'null'],
                ['text' => '⬇ Уволить', 'callback_data' => $pack(CallbackInterface::CALLBACK_UP_DOWN_WORKER, ['t' => 'metal', 'v' => '-'])],
                ['text' => 'Нанять ⬆', 'callback_data' => $pack(CallbackInterface::CALLBACK_UP_DOWN_WORKER, ['t' => 'metal', 'v' => '+'])],
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
