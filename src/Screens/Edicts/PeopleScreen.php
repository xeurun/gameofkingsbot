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
use App\Interfaces\WorkInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use App\Screens\BaseScreen;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class PeopleScreen extends BaseScreen
{
    /** @var WorkManager */
    protected $workManager;
    /** @var KingdomManager */
    protected $kingdomManager;
    /** @var PeopleManager */
    protected $peopleManager;

    public function __construct(
        BotManager $botManager,
        KingdomManager $kingdomManager,
        WorkManager $workManager,
        PeopleManager $peopleManager
    ) {
        $this->workManager = $workManager;
        $this->kingdomManager = $kingdomManager;
        $this->peopleManager = $peopleManager;
        parent::__construct($botManager);
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    protected function sendAdvice(): bool
    {
        $inlineKeyboard = new InlineKeyboard([
            [
                'text' => '✅ Продолжить',
                'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_ADVISER, 1),
            ],
            [
                'text' => 'Достаточно ❌',
                'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_ADVISER, 0),
            ],
        ]);

        $user = $this->botManager->getUser();
        $gender = $this->botManager->getTranslator()->transChoice(
            TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
            User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
            [],
            TranslatorInterface::TRANSLATOR_DOMAIN_STATE
        );

        $name = ScreenInterface::SCREEN_PEOPLE;
        $data = [
            'chat_id' => $this->botManager->getUser()->getId(),
            'text' => '*Советник*: ' . $gender . " «{$name}» " . ' также не малозначимая часть вашего королевства, тут вы можете управлять налогами, а также нанимать и уволнять людей с различных видов работы
_(для более подробной информации о каждом типе работ нажмите на его название)_',
            'reply_markup' => $inlineKeyboard,
            'parse_mode' => 'Markdown',
        ];

        $response = Request::sendMessage($data);

        return $response->isOk();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): void
    {
        Request::sendMessage($this->getMessageData());
        if (AdviserInterface::ADVISER_SHOW_PEOPLE_TUTORIAL === $this->botManager->getKingdom()->getAdviserState()) {
            $this->sendAdvice();
        }
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function getMessageData(): array
    {
        $kingdom = $this->botManager->getKingdom();

        $title = ScreenInterface::SCREEN_PEOPLE;

        $eatHourly = $this->peopleManager->eat();
        $payHourly = $this->peopleManager->pay();

        $foodHourly = $this->workManager->getSalary(WorkInterface::WORK_TYPE_FOOD);
        $woodHourly = $this->workManager->getSalary(WorkInterface::WORK_TYPE_WOOD);
        $stoneHourly = $this->workManager->getSalary(WorkInterface::WORK_TYPE_STONE);
        $ironHourly = $this->workManager->getSalary(WorkInterface::WORK_TYPE_IRON);

        $maxOnArmy = $this->kingdomManager->getMaxOn(WorkInterface::WORK_TYPE_ARMY);
        $maxOnFood = $this->kingdomManager->getMaxOn(WorkInterface::WORK_TYPE_FOOD);
        $maxOnWood = $this->kingdomManager->getMaxOn(WorkInterface::WORK_TYPE_WOOD);
        $maxOnStone = $this->kingdomManager->getMaxOn(WorkInterface::WORK_TYPE_STONE);
        $maxOnIron = $this->kingdomManager->getMaxOn(WorkInterface::WORK_TYPE_IRON);

        $free = $this->workManager->free();
        $people = $this->kingdomManager->getPeople();

        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_PEOPLE_SCREEN_MESSAGE,
            [
                '%title%' => $title,
                '%people%' => $this->botManager->getTranslator()->transChoice(
                    TranslatorInterface::TRANSLATOR_MESSAGE_PEOPLES,
                    $people,
                    [
                        '%count%' => $people,
                    ],
                    TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                ),
                '%eatHourly%' => CurrencyHelper::costFormat($eatHourly),
                '%taxLevel%' => mb_strtolower(
                        $this->botManager->getTranslator()->transChoice(
                        TranslatorInterface::TRANSLATOR_MESSAGE_TAXES_LEVEL,
                        $kingdom->getTax(),
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
                    )
                ),
                '%payHourly%' => CurrencyHelper::costFormat($payHourly),
                '%onArmy%' => $kingdom->getWorkerCount(WorkInterface::WORK_TYPE_ARMY),
                '%onFood%' => $kingdom->getWorkerCount(WorkInterface::WORK_TYPE_FOOD),
                '%onWood%' => $kingdom->getWorkerCount(WorkInterface::WORK_TYPE_WOOD),
                '%onStone%' => $kingdom->getWorkerCount(WorkInterface::WORK_TYPE_STONE),
                '%onIron%' => $kingdom->getWorkerCount(WorkInterface::WORK_TYPE_IRON),
                '%maxOnArmy%' => $maxOnArmy,
                '%maxOnFood%' => $maxOnFood,
                '%maxOnWood%' => $maxOnWood,
                '%maxOnStone%' => $maxOnStone,
                '%maxOnIron%' => $maxOnIron,
                '%foodHourly%' => CurrencyHelper::costFormat($foodHourly),
                '%woodHourly%' => CurrencyHelper::costFormat($woodHourly),
                '%stoneHourly%' => CurrencyHelper::costFormat($stoneHourly),
                '%ironHourly%' => CurrencyHelper::costFormat($ironHourly),
                '%free%' => $this->botManager->getTranslator()->transChoice(
                    TranslatorInterface::TRANSLATOR_MESSAGE_PEOPLES,
                    $free,
                    [
                        '%count%' => $free,
                    ],
                    TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                ),
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $inlineKeyboard = new InlineKeyboard(
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TaxesInterface::TAXES,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_GET_INFO,
                        TaxesInterface::TAXES
                    ),
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_RAISE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_RAISE_OR_LOWER_TAXES,
                        1
                    ),
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_LOWER,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_RAISE_OR_LOWER_TAXES,
                        0
                    ),
                ],
            ],
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        ResourceInterface::RESOURCE_FOOD,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_GET_INFO,
                        WorkInterface::WORK_TYPE_FOOD
                    ),
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_FIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        WorkInterface::WORK_TYPE_FOOD,
                        0
                    ),
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_HIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        WorkInterface::WORK_TYPE_FOOD,
                        1
                    ),
                ],
            ],
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        ResourceInterface::RESOURCE_WOOD,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_GET_INFO,
                        WorkInterface::WORK_TYPE_WOOD
                    ),
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_FIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        WorkInterface::WORK_TYPE_WOOD,
                        0
                    ),
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_HIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        WorkInterface::WORK_TYPE_WOOD,
                        1
                    ),
                ],
            ],
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        ResourceInterface::RESOURCE_STONE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_GET_INFO,
                        WorkInterface::WORK_TYPE_STONE
                    ),
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_FIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        WorkInterface::WORK_TYPE_STONE,
                        0
                    ),
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_HIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        WorkInterface::WORK_TYPE_STONE,
                        1
                    ),
                ],
            ],
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        ResourceInterface::RESOURCE_IRON,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_GET_INFO,
                        WorkInterface::WORK_TYPE_IRON
                    ),
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_FIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        WorkInterface::WORK_TYPE_IRON,
                        0
                    ),
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_HIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        WorkInterface::WORK_TYPE_IRON,
                        1
                    ),
                ],
            ],
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        WorkInterface::WORK_TYPE_ARMY,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_GET_INFO,
                        WorkInterface::WORK_TYPE_ARMY
                    ),
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_FIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        WorkInterface::WORK_TYPE_ARMY,
                        0
                    ),
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_HIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => CallbackFactory::pack(
                        CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        WorkInterface::WORK_TYPE_ARMY,
                        1
                    ),
                ],
            ]
        );

        return [
            'chat_id' => $kingdom->getUser()->getId(),
            'text' => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode' => 'Markdown',
        ];
    }
}
