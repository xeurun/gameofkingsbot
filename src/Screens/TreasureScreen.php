<?php

namespace App\Screens;

use App\Factory\ScreenFactory;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ResourceInterface;
use App\Interfaces\ScreenInterface;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Manager\ResourceManager;
use App\Manager\WorkManager;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class TreasureScreen extends BaseScreen
{
    protected $workManager;
    protected $peopleManager;
    protected $resourceManager;

    public function __construct(BotManager $botManager, WorkManager $workManager, PeopleManager $peopleManager, ResourceManager $resourceManager)
    {
        $this->workManager = $workManager;
        $this->peopleManager = $peopleManager;
        $this->resourceManager = $resourceManager;
        parent::__construct($botManager);
    }


    /**
     * @return array
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function getMessageData(): array
    {
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_TREASURE;

        $newGold = $this->resourceManager->getStack(ResourceInterface::RESOURCE_GOLD);
        $newFood = $this->resourceManager->getStack(ResourceInterface::RESOURCE_FOOD);
        $newWood = $this->resourceManager->getStack(ResourceInterface::RESOURCE_WOOD);
        $newStone = $this->resourceManager->getStack(ResourceInterface::RESOURCE_STONE);
        $newMetal = $this->resourceManager->getStack(ResourceInterface::RESOURCE_METAL);

        $text = <<<TEXT
*{$title}*

Сейчас на складе

💰 Золота ({$kingdom->getGold()}ед.)
🍞 Еды ({$kingdom->getFood()}ед.)
🌲 Дерева ({$kingdom->getWood()}ед.)
⛏ Камней ({$kingdom->getStone()}ед.)
🔨 Железа ({$kingdom->getMetal()}ед.)

Прибыло на склад

💰 Золота ({$newGold})
🍞 Еды ({$newFood})
🌲 Дерева ({$newWood})
⛏ Камней ({$newStone})
🔨 Железа ({$newMetal})
TEXT;
        $inlineKeyboard = new InlineKeyboard(
            [
                ['text' => 'Загрузить пришедшее на склад', 'callback_data' => CallbackInterface::CALLBACK_GRAB_RESOURCES],
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
