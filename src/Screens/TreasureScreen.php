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
        $response = [
            'chat_id'      => $kingdom->getUser()->getId(),
            'parse_mode'   => 'Markdown',
        ];

        $title = ScreenInterface::SCREEN_TREASURE;

        $newGold = $this->resourceManager->getStack(ResourceInterface::RESOURCE_GOLD);
        $newFood = $this->resourceManager->getStack(ResourceInterface::RESOURCE_FOOD);
        $newWood = $this->resourceManager->getStack(ResourceInterface::RESOURCE_WOOD);
        $newStone = $this->resourceManager->getStack(ResourceInterface::RESOURCE_STONE);
        $newMetal = $this->resourceManager->getStack(ResourceInterface::RESOURCE_METAL);

        $hours = $this->workManager->workedHours($kingdom);

        $text = <<<TEXT
*{$title}*

`💰 Золота - `*{$kingdom->getGold()}*` ед.`
`🍞 Еды    - `*{$kingdom->getFood()}*` ед.`
`🌲 Дерева - `*{$kingdom->getWood()}*` ед.`
`⛏ Камней - `*{$kingdom->getStone()}*` ед.`
`🔨 Железа - `*{$kingdom->getMetal()}*` ед.`

TEXT;

        if ($hours) {
            $text .= <<<TEXT
            
За {$hours} часов с последней проверки склада

`💰 Золота - `*{$newGold}*` ед.`
`🍞 Еды    - `*{$newFood}*` ед.`
`🌲 Дерева - `*{$newWood}*` ед.`
`⛏ Камней - `*{$newStone}*` ед.`
`🔨 Железа - `*{$newMetal}*` ед.`

необходимо перенести добытые ресурсы на склад королевства
TEXT;

            $inlineKeyboard = new InlineKeyboard(
                [
                    ['text' => 'Перенести добытые ресурсы на склад', 'callback_data' => CallbackInterface::CALLBACK_GRAB_RESOURCES],
                ]
            );

            $response['reply_markup'] = $inlineKeyboard;
        } else {
            $text .= <<<TEXT
            
С последней проверки склада прошло слишком мало времени, попробуйте проверить склад через час!
TEXT;
        }

        $response['text'] = $text;

        return $response;
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
