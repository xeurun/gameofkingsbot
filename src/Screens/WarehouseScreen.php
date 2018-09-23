<?php

namespace App\Screens;

use App\Interfaces\CallbackInterface;
use App\Interfaces\ResourceInterface;
use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Manager\ResourceManager;
use App\Manager\WorkManager;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class WarehouseScreen extends BaseScreen
{
    protected $workManager;
    protected $peopleManager;
    protected $resourceManager;

    public function __construct(
        BotManager $botManager,
        WorkManager $workManager,
        PeopleManager $peopleManager,
        ResourceManager $resourceManager
    ) {
        $this->workManager = $workManager;
        $this->peopleManager = $peopleManager;
        $this->resourceManager = $resourceManager;
        parent::__construct($botManager);
    }

    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        return Request::sendMessage($this->getMessageData());
    }

    /**
     * @return array
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function getMessageData(): array
    {
        $kingdom = $this->botManager->getKingdom();
        $response = [
            'chat_id' => $kingdom->getUser()->getId(),
            'parse_mode' => 'Markdown',
        ];

        $title = ScreenInterface::SCREEN_TREASURE;

        $newGold = $this->resourceManager->getExtractedCountByResourceName(ResourceInterface::RESOURCE_GOLD);
        $newFood = $this->resourceManager->getExtractedCountByResourceName(ResourceInterface::RESOURCE_FOOD);
        $newWood = $this->resourceManager->getExtractedCountByResourceName(ResourceInterface::RESOURCE_WOOD);
        $newStone = $this->resourceManager->getExtractedCountByResourceName(ResourceInterface::RESOURCE_STONE);
        $newIron = $this->resourceManager->getExtractedCountByResourceName(ResourceInterface::RESOURCE_IRON);

        $hours = $this->workManager->workedHours($kingdom);

        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_WAREHOUSE_SCREEN_MESSAGE,
            [
                '%title%' => $title,
                '%gold%' => $kingdom->getGold(),
                '%food%' => $kingdom->getFood(),
                '%wood%' => $kingdom->getWood(),
                '%stone%' => $kingdom->getStone(),
                '%iron%' => $kingdom->getIron()
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        if ($hours) {
            $text .= $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_WAREHOUSE_ADDITIONAL_SCREEN_MESSAGE,
                [
                    '%hours%' => $this->botManager->getTranslator()->transChoice(
                        TranslatorInterface::TRANSLATOR_MESSAGE_HOURS,
                        $hours,
                        [
                            'count' => $hours
                        ],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    '%gold%' => $newGold,
                    '%food%' => $newFood,
                    '%wood%' => $newWood,
                    '%stone%' => $newStone,
                    '%iron%' => $newIron
                ],
                TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
            );

            $inlineKeyboard = new InlineKeyboard(
                [
                    [
                        'text' => $this->botManager->getTranslator()->trans(
                            TranslatorInterface::TRANSLATOR_MESSAGE_MOVE_EXTRACTED_RESOURCES_TO_WAREHOUSE_BUTTON,
                            [],
                            TranslatorInterface::TRANSLATOR_DOMAIN_INLINE
                        ),
                        'callback_data' => CallbackInterface::CALLBACK_MOVE_RESOURCES_TO_WAREHOUSE
                    ],
                ]
            );

            $response['reply_markup'] = $inlineKeyboard;
        } else {
            $text .= $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_WAREHOUSE_WITHOUT_ADDITIONAL_SCREEN_MESSAGE,
                [],
                TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
            );
        }

        $response['text'] = $text;

        return $response;
    }
}
