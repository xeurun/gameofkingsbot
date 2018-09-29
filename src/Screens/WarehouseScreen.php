<?php

namespace App\Screens;

use App\Helper\CurrencyHelper;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ResourceInterface;
use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Manager\PeopleManager;
use App\Manager\ResourceManager;
use App\Manager\WorkManager;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class WarehouseScreen extends BaseScreen
{
    /** @var KingdomManager */
    protected $kingdomManager;
    /** @var WorkManager  */
    protected $workManager;
    /** @var PeopleManager  */
    protected $peopleManager;
    /** @var ResourceManager  */
    protected $resourceManager;

    /**
     * @param BotManager $botManager
     * @param KingdomManager $kingdomManager
     * @param WorkManager $workManager
     * @param PeopleManager $peopleManager
     * @param ResourceManager $resourceManager
     */
    public function __construct(
        BotManager $botManager,
        KingdomManager $kingdomManager,
        WorkManager $workManager,
        PeopleManager $peopleManager,
        ResourceManager $resourceManager
    ) {
        $this->kingdomManager = $kingdomManager;
        $this->workManager = $workManager;
        $this->peopleManager = $peopleManager;
        $this->resourceManager = $resourceManager;
        parent::__construct($botManager);
    }

    /**
     * @inheritdoc
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): void
    {
        Request::sendMessage($this->getMessageData());
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

        $maxGold = $this->kingdomManager->getMax(ResourceInterface::RESOURCE_GOLD);
        $maxFood = $this->kingdomManager->getMax(ResourceInterface::RESOURCE_FOOD);
        $maxWood = $this->kingdomManager->getMax(ResourceInterface::RESOURCE_WOOD);
        $maxStone = $this->kingdomManager->getMax(ResourceInterface::RESOURCE_STONE);
        $maxIron = $this->kingdomManager->getMax(ResourceInterface::RESOURCE_IRON);

        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_WAREHOUSE_SCREEN_MESSAGE,
            [
                '%title%' => $title,
                '%gold%' => CurrencyHelper::costFormat($kingdom->getGold()),
                '%food%' => CurrencyHelper::costFormat($kingdom->getFood()),
                '%wood%' => CurrencyHelper::costFormat($kingdom->getWood()),
                '%stone%' => CurrencyHelper::costFormat($kingdom->getStone()),
                '%iron%' => CurrencyHelper::costFormat($kingdom->getIron()),
                '%maxGold%' => CurrencyHelper::costFormat($maxGold),
                '%maxFood%' => CurrencyHelper::costFormat($maxFood),
                '%maxWood%' => CurrencyHelper::costFormat($maxWood),
                '%maxStone%' => CurrencyHelper::costFormat($maxStone),
                '%maxIron%' => CurrencyHelper::costFormat($maxIron),
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
                    '%gold%' => CurrencyHelper::costFormat($newGold),
                    '%food%' => CurrencyHelper::costFormat($newFood),
                    '%wood%' => CurrencyHelper::costFormat($newWood),
                    '%stone%' => CurrencyHelper::costFormat($newStone),
                    '%iron%' => CurrencyHelper::costFormat($newIron),
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
