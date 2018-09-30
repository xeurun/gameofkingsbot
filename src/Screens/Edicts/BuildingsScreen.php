<?php

namespace App\Screens\Edicts;

use App\Entity\User;
use App\Factory\CallbackFactory;
use App\Helper\CurrencyHelper;
use App\Interfaces\AdviserInterface;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ResourceInterface;
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
     * @return bool
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    protected function sendAdvice(): bool
    {
        $inlineKeyboard = new InlineKeyboard([
            [
                'text' => '✅ Продолжить',
                'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_ADVISER, 1)
            ],
            [
                'text' => 'Достаточно ❌',
                'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_ADVISER, 0)
            ],
        ]);

        $user = $this->botManager->getUser();
        $gender = $this->botManager->getTranslator()->transChoice(
            TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
            $user->getGender() === User::AVAILABLE_GENDER_KING ? 1 : 0,
            [],
            TranslatorInterface::TRANSLATOR_DOMAIN_STATE
        );

        $name = ScreenInterface::SCREEN_BUILDINGS;
        $data = [
            'chat_id' => $this->botManager->getUser()->getId(),
            'text' => '*Советник*: ' . $gender . " «{$name}» " . ' очень важная часть управления королевством, они влияют на размер вашего королевства, его уровень, количество ресурсов которые вмещает склад. 
_(для более подробной информации о каждом строении нажмите на его название)_',
            'reply_markup' => $inlineKeyboard,
            'parse_mode' => 'Markdown',
        ];

        $response = Request::sendMessage($data);

        return $response->isOk();
    }

    /**
     * @inheritdoc
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): void
    {
        Request::sendMessage($this->getMessageData());
        if ($this->botManager->getKingdom()->getAdviserState() === AdviserInterface::ADVISER_SHOW_BUILDINGS_TUTORIAL) {
            $this->sendAdvice();
        }
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
                '%gold%' => CurrencyHelper::costFormat(
                    $kingdom->getResource(ResourceInterface::RESOURCE_GOLD)
                ),
                '%wood%' => CurrencyHelper::costFormat(
                    $kingdom->getResource(ResourceInterface::RESOURCE_WOOD)
                ),
                '%stone%' => CurrencyHelper::costFormat(
                    $kingdom->getResource(ResourceInterface::RESOURCE_STONE)
                ),
                '%iron%' => CurrencyHelper::costFormat(
                    $kingdom->getResource(ResourceInterface::RESOURCE_IRON)
                ),
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
            $goldCost = $buildType->getResourceCost(ResourceInterface::RESOURCE_GOLD);
            if ($goldCost > 0) {
                $cost[] = CurrencyHelper::costFormat($goldCost) . ' 💰';
            }
            $woodCost = $buildType->getResourceCost(ResourceInterface::RESOURCE_GOLD);
            if ($woodCost > 0) {
                $cost[] = CurrencyHelper::costFormat($woodCost) . ' 🌲';
            }
            $stoneCost = $buildType->getResourceCost(ResourceInterface::RESOURCE_GOLD);
            if ($stoneCost > 0) {
                $cost[] = CurrencyHelper::costFormat($stoneCost) . ' ⛏';
            }
            $ironCost = $buildType->getResourceCost(ResourceInterface::RESOURCE_GOLD);
            if ($ironCost > 0) {
                $cost[] = CurrencyHelper::costFormat($ironCost) . ' 🔨';
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
