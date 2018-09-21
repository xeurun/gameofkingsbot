<?php

namespace App\Callbacks;

use App\Interfaces\ResourceInterface;
use App\Manager\BotManager;
use App\Manager\ResourceManager;
use App\Screens\TreasureScreen;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class GrabResourcesCallback extends BaseCallback
{
    protected $treasureScreen;

    public function __construct(BotManager $botManager, TreasureScreen $treasureScreen)
    {
        $this->treasureScreen = $treasureScreen;
        parent::__construct($botManager);
    }

    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws ORMException
     */
    public function execute(): ServerResponse
    {
        /** @var ResourceManager $resourceManager */
        $resourceManager = $this->botManager->get(ResourceManager::class);
        $user = $this->botManager->getUser();
        $today = new \DateTime();

        $bonusGold = $resourceManager->getStack(ResourceInterface::RESOURCE_GOLD);
        $bonusFood = $resourceManager->getStack(ResourceInterface::RESOURCE_FOOD);
        $bonusWood = $resourceManager->getStack(ResourceInterface::RESOURCE_WOOD);
        $bonusStone = $resourceManager->getStack(ResourceInterface::RESOURCE_STONE);
        $bonusMetal = $resourceManager->getStack(ResourceInterface::RESOURCE_METAL);

        $text = <<<TEXT
Пополнение склада!

💰 Золота ({$bonusGold}ед.)
🍞 Еды ({$bonusFood}ед.)
🌲 Дерева ({$bonusWood}ед.)
⛏ Камней ({$bonusStone}ед.)
🔨 Железа ({$bonusMetal}ед.)
TEXT;

        $entityManager = $this->botManager->getEntityManager();
        $kingdom = $user->getKingdom();
        if ($kingdom) {
            $kingdom->setGold($kingdom->getGold() + $bonusGold);
            $kingdom->setFood($kingdom->getFood() + $bonusFood);
            $kingdom->setWood($kingdom->getWood() + $bonusWood);
            $kingdom->setStone($kingdom->getStone() + $bonusStone);
            $kingdom->setMetal($kingdom->getMetal() + $bonusMetal);
            $kingdom->setGrabResourcesDate($today);
        }
        $entityManager->persist($kingdom);
        $entityManager->flush();

        $callback = $this->botManager->getCallbackQuery();
        if ($callback->getMessage()) {
            $data = $this->treasureScreen->getMessageData();
            $data['message_id'] = $callback->getMessage()->getMessageId();
            Request::editMessageText($data);
        }

        $data = [
            'callback_query_id' => $this->botManager->getCallbackQuery()->getId(),
            'text'              => $text,
            'show_alert'        => true,
        ];

        return Request::answerCallbackQuery($data);
    }
}
