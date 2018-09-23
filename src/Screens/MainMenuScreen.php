<?php

namespace App\Screens;

use App\Interfaces\ScreenInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class MainMenuScreen extends BaseScreen
{
    protected $workManager;
    protected $peopleManager;
    protected $kingdomManager;

    public function __construct(
        BotManager $botManager,
        WorkManager $workManager,
        PeopleManager $peopleManager,
        KingdomManager $kingdomManager
    ) {
        $this->workManager = $workManager;
        $this->peopleManager = $peopleManager;
        $this->kingdomManager = $kingdomManager;

        parent::__construct($botManager);
    }

    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $kingdom = $this->botManager->getKingdom();

        $keyboard = new Keyboard(
            [ScreenInterface::SCREEN_EVENT, ScreenInterface::SCREEN_TREASURE, ScreenInterface::SCREEN_EDICTS],
            [ScreenInterface::SCREEN_RESEARCH,  ScreenInterface::SCREEN_DIPLOMACY],
            [ScreenInterface::SCREEN_BONUSES, ScreenInterface::SCREEN_ACHIEVEMENTS, ScreenInterface::SCREEN_SETTINGS]
        );

        //Return a random keyboard.
        $keyboard = $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);

        $text = <<<TEXT
*🤴 Королевство «{$kingdom->getName()}», вы верховный король 👸*
TEXT;

        $data    = [
            'chat_id'      => $kingdom->getUser()->getId(),
            'text'         => $text,
            'reply_markup' => $keyboard,
            'parse_mode'   => 'Markdown'
        ];

        Request::sendMessage($data);

        $taxLevel = $this->peopleManager->taxLevel($kingdom);

        $eatHourly = $this->peopleManager->eat($kingdom);
        $foodDay = round($kingdom->getFood() / $eatHourly);

        $level = $this->kingdomManager->level($kingdom);

        $text = <<<TEXT
`👪  В вашем королевстве `*{$level}*` уровня - `*{$kingdom->getPeople()}*` людей`

`📜  Уровень налогов: `*{$taxLevel}*

`💰 Золота - `*{$kingdom->getGold()}*` ед.`
`🍞 Еды    - `*{$kingdom->getFood()}*` ед.`
`🌲 Дерева - `*{$kingdom->getWood()}*` ед.`
`⛏ Камней - `*{$kingdom->getStone()}*` ед.`
`🔨 Железа - `*{$kingdom->getMetal()}*` ед.`

Еды на *{$foodDay}* часов

Проверьте склад!
TEXT;

        $inlineKeyboard = new InlineKeyboard([
            ['text' => 'Новости', 'url' => 'https://t.me/worldofkings'],
            ['text' => 'Группа для общения', 'url' => 'https://t.me/placeofkings'],
        ]);

        $data = [
            'chat_id'      => $kingdom->getUser()->getId(),
            'text'         => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode'   => 'Markdown',
        ];

        return Request::sendMessage($data);
    }
}
