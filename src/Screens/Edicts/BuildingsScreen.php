<?php

namespace App\Screens\Edicts;

use App\Factory\CallbackFactory;
use App\Helper\CurrencyHelper;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use App\Interfaces\TaxesInterface;
use App\Interfaces\TranslatorInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use App\Repository\StructureTypeRepository;
use App\Screens\BaseScreen;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class BuildingsScreen extends BaseScreen
{
    /** @var WorkManager  */
    protected $workManager;
    /** @var PeopleManager  */
    protected $peopleManager;
    /** @var KingdomManager  */
    protected $kingdomManager;
    /** @var StructureTypeRepository  */
    protected $buildTypeRepository;

    /**
     * @param BotManager $botManager
     * @param WorkManager $workManager
     * @param PeopleManager $peopleManager
     * @param KingdomManager $kingdomManager
     * @param StructureTypeRepository $buildTypeRepository
     */
    public function __construct(
        BotManager $botManager,
        WorkManager $workManager,
        PeopleManager $peopleManager,
        KingdomManager $kingdomManager,
        StructureTypeRepository $buildTypeRepository
    ) {
        $this->workManager = $workManager;
        $this->peopleManager = $peopleManager;
        $this->kingdomManager = $kingdomManager;
        $this->buildTypeRepository = $buildTypeRepository;

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
    public function getMessageData()
    {
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_BUILDINGS;

        $freeTerritorySize = $this->kingdomManager->getTerritorySize()
            - $this->kingdomManager->getStructureCount();

        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_BUILDINGS_SCREEN_MESSAGE,
            [
                '%title%' => $title,
                '%gold%' => CurrencyHelper::costFormat($kingdom->getGold()),
                '%wood%' => CurrencyHelper::costFormat($kingdom->getWood()),
                '%stone%' => CurrencyHelper::costFormat($kingdom->getStone()),
                '%iron%' => CurrencyHelper::costFormat($kingdom->getIron()),
                '%size%' => $freeTerritorySize
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $buildings = [];
        $buildTypes = $this->buildTypeRepository->findAll();
        foreach ($buildTypes as $buildType) {
            $build = $kingdom->getStructure($buildType->getCode());
            $level = 0;
            if ($build) {
                $level = $build->getLevel();
            }

            $cost = [];
            if ($buildType->getGoldCost() > 0) {
                $cost[] = CurrencyHelper::costFormat($buildType->getGoldCost()) . ' 💰';
            }
            if ($buildType->getWoodCost() > 0) {
                $cost[] = CurrencyHelper::costFormat($buildType->getWoodCost()) . ' 🌲';
            }
            if ($buildType->getStoneCost() > 0) {
                $cost[] = CurrencyHelper::costFormat($buildType->getStoneCost()) . ' ⛏';
            }
            if ($buildType->getIronCost() > 0) {
                $cost[] = CurrencyHelper::costFormat($buildType->getIronCost()) . ' 🔨';
            }

            $costText = implode(', ', $cost);
            $text .= $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_BUILDINGS_SCREEN_MESSAGE_STRUCTURE,
                [
                    '%structureName%' => $this->botManager->getTranslator()->trans(
                        $buildType->getCode(),
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    '%structureLevel%' => $level,
                    '%structureCost%' => $costText
                ],
                TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
            );

            $buildings[] = [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        $buildType->getCode(),
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_GET_INFO, $buildType->getCode())
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_BUY_STRUCTURE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_INCREASE_STRUCTURE_LEVEL, $buildType->getCode())
                ]
            ];
        }

        $text .= $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_BUILDINGS_SCREEN_MESSAGE_STRUCTURE_CHOOSE,
            [],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $inlineKeyboard = new InlineKeyboard(
            ...$buildings
        );

        return [
            'chat_id' => $kingdom->getUser()->getId(),
            'text' => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode' => 'Markdown',
        ];
    }
}
