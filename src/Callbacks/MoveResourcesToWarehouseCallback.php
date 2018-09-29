<?php

namespace App\Callbacks;

use App\Helper\CurrencyHelper;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ResourceInterface;
use App\Manager\BotManager;
use App\Manager\ResourceManager;
use App\Screens\WarehouseScreen;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Symfony\Component\Translation\TranslatorInterface;

class MoveResourcesToWarehouseCallback extends BaseCallback
{
    /** @var ResourceManager */
    protected $resourceManager;
    /** @var WarehouseScreen */
    protected $warehouseScreen;

    public function __construct(
        BotManager $botManager,
        ResourceManager $resourceManager,
        WarehouseScreen $warehouseScreen
    ) {
        $this->resourceManager = $resourceManager;
        $this->warehouseScreen = $warehouseScreen;
        parent::__construct($botManager);
    }

    /**
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $data = $this->moveResourcesToWarehouse();
        return Request::answerCallbackQuery($data);
    }

    /**
     * @return array
     * @throws TelegramException
     */
    public function moveResourcesToWarehouse(): array
    {
        $kingdom = $this->botManager->getKingdom();

        $currentFood = $kingdom->getFood();
        $currentGold = $kingdom->getGold();
        $currentWood = $kingdom->getWood();
        $currentStone = $kingdom->getStone();
        $currentIron = $kingdom->getIron();

        $this->resourceManager->moveExtractedResourcesToWarehouse();

        $foodDiff = $kingdom->getFood() - $currentFood;
        $goldDiff = $kingdom->getGold() - $currentGold;
        $woodDiff = $kingdom->getWood() - $currentWood;
        $stoneDiff = $kingdom->getStone() - $currentStone;
        $ironDiff = $kingdom->getIron() - $currentIron;

        $subText = $this->botManager->getTranslator()->trans(
            CallbackInterface::CALLBACK_MOVE_RESOURCES_TO_WAREHOUSE,
            [
                '%gold%' => CurrencyHelper::costFormat($goldDiff),
                '%food%' => CurrencyHelper::costFormat($foodDiff),
                '%wood%' => CurrencyHelper::costFormat($woodDiff),
                '%stone%' => CurrencyHelper::costFormat($stoneDiff),
                '%iron%' => CurrencyHelper::costFormat($ironDiff)
            ],
            \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
        );

        Request::sendMessage([
            'chat_id' => $kingdom->getUser()->getId(),
            'text' => $subText,
            'parse_mode' => 'Markdown',
        ]);

        $text = $this->botManager->getTranslator()->trans(
            \App\Interfaces\TranslatorInterface::TRANSLATOR_MESSAGE_EXTRACTED_RESOURCES_RECEIVED,
            [],
            \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
        );

        $entityManager = $this->botManager->getEntityManager();
        $entityManager->persist($kingdom);
        $entityManager->flush();

        $message = $this->callbackQuery->getMessage();
        if ($message) {
            $data = $this->warehouseScreen->getMessageData();
            $data['message_id'] = $message->getMessageId();
            Request::editMessageText($data);
        }

        return [
            'callback_query_id' => $this->callbackQuery->getId(),
            'text' => $text,
            'show_alert' => false,
        ];
    }
}
