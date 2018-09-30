<?php

namespace App\Screens;

use App\Entity\User;
use App\Factory\CallbackFactory;
use App\Helper\CurrencyHelper;
use App\Interfaces\AdviserInterface;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ResourceInterface;
use App\Interfaces\ScreenInterface;
use App\Interfaces\StructureInterface;
use App\Interfaces\TranslatorInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;

class MainMenuScreen extends BaseScreen
{
    /** @var WorkManager  */
    protected $workManager;
    /** @var PeopleManager  */
    protected $peopleManager;
    /** @var KingdomManager  */
    protected $kingdomManager;

    /**
     * @param BotManager $botManager
     * @param WorkManager $workManager
     * @param PeopleManager $peopleManager
     * @param KingdomManager $kingdomManager
     */
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
     * @inheritdoc
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): void
    {
        $this->sendTitle();
        $this->sendMessage();
        if ($this->botManager->getKingdom()->getAdviserState() === AdviserInterface::ADVISER_SHOW_INITIAL_TUTORIAL) {
            $this->sendAdvice();
        }
    }

    /**
     * @return bool
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    protected function sendAdvice(): bool
    {
        $inlineKeyboard = new InlineKeyboard([
            [
                'text' => '✅ Да',
                'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_ADVISER, 1)
            ],
            [
                'text' => 'Нет ❌',
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

        $data = [
            'chat_id' => $this->botManager->getUser()->getId(),
            'text' => '*Советник*: ' . $gender . ' хотите чтобы я подробно рассказал как управлять королевством?',
            'reply_markup' => $inlineKeyboard,
            'parse_mode' => 'Markdown',
        ];

        $response = Request::sendMessage($data);

        return $response->isOk();
    }

    /**
     * @return bool
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    protected function sendMessage(): bool
    {
        $response = Request::sendMessage($this->getMessageData());

        return $response->isOk();
    }

    /**
     * @return array
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function getMessageData(): array
    {
        $kingdom = $this->botManager->getKingdom();

        $eatHourly = $this->peopleManager->eat();
        $foodDay = round($kingdom->getResource(ResourceInterface::RESOURCE_FOOD) / $eatHourly);

        $level = $this->kingdomManager->level($kingdom, StructureInterface::STRUCTURE_TYPE_CASTLE);
        $territory = $this->kingdomManager->level($kingdom, StructureInterface::STRUCTURE_TYPE_TERRITORY);
        $people = $this->kingdomManager->getPeople();

        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_MAIN_MENU_SCREEN_MESSAGE,
            [
                '%level%' => $level,
                '%territory%' => $territory,
                '%territorySize%' => $this->kingdomManager->getTerritorySize(),
                '%people%' => $this->botManager->getTranslator()->transChoice(
                    TranslatorInterface::TRANSLATOR_MESSAGE_PEOPLES,
                    $people,
                    [
                        'count' => CurrencyHelper::costFormat($people)
                    ],
                    TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                ),
                '%tax%' => mb_strtolower(
                    $this->botManager->getTranslator()->transChoice(
                        TranslatorInterface::TRANSLATOR_MESSAGE_TAXES_LEVEL,
                        $kingdom->getTax(),
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
                    )
                ),
                '%gold%' => CurrencyHelper::costFormat(
                    $kingdom->getResource(ResourceInterface::RESOURCE_GOLD)
                ),
                '%food%' => CurrencyHelper::costFormat(
                    $kingdom->getResource(ResourceInterface::RESOURCE_FOOD)
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
                '%foodDay%' => $this->botManager->getTranslator()->transChoice(
                    TranslatorInterface::TRANSLATOR_MESSAGE_HOURS,
                    $foodDay,
                    [
                        '%count%' => $foodDay
                    ],
                    TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                )
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $inlineKeyboard = new InlineKeyboard([
            [
                'text' => $this->botManager->getTranslator()->trans(
                    TranslatorInterface::TRANSLATOR_MESSAGE_GROUP_BUTTON,
                    [
                        'name' => $kingdom->getName()
                    ],
                    TranslatorInterface::TRANSLATOR_DOMAIN_INLINE
                ),
                'url' => 'https://t.me/worldofkings'
            ],
            [
                'text' => $this->botManager->getTranslator()->trans(
                    TranslatorInterface::TRANSLATOR_MESSAGE_CHANNEL_BUTTON,
                    [
                        'name' => $kingdom->getName()
                    ],
                    TranslatorInterface::TRANSLATOR_DOMAIN_INLINE
                ),
                'url' => 'https://t.me/placeofkings'
            ],
        ]);

        return [
            'chat_id' => $kingdom->getUser()->getId(),
            'text' => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode' => 'Markdown',
        ];
    }

    /**
     * @return bool
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    protected function sendTitle(): bool
    {
        $user = $this->botManager->getUser();

        $keyboard = new Keyboard(
            [ScreenInterface::SCREEN_EVENT, ScreenInterface::SCREEN_TREASURE, ScreenInterface::SCREEN_EDICTS],
            [ScreenInterface::SCREEN_RESEARCH, ScreenInterface::SCREEN_DIPLOMACY],
            [ScreenInterface::SCREEN_BONUSES, ScreenInterface::SCREEN_ACHIEVEMENTS, ScreenInterface::SCREEN_SETTINGS]
        );

        //Return a random keyboard.
        $keyboard = $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);

        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_MAIN_MENU_SCREEN_TITLE,
            [
                '%kingdomName%' => $this->botManager->getKingdom()->getName(),
                '%name%' => $this->botManager->getUser()->getName(),
                '%supreme_gender%' => $this->botManager->getTranslator()->transChoice(
                    TranslatorInterface::TRANSLATOR_MESSAGE_SUPREME_GENDER,
                    $user->getGender() === User::AVAILABLE_GENDER_KING ? 1 : 0,
                    [],
                    TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                )
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $data = [
            'chat_id' => $user->getId(),
            'text' => $text,
            'reply_markup' => $keyboard,
            'parse_mode' => 'Markdown'
        ];

        $response = Request::sendMessage($data);

        return $response->isOk();
    }
}
