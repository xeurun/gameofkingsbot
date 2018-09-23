<?php

namespace App\Screens;

use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class MainMenuScreen extends BaseScreen
{
    protected $workManager;
    protected $peopleManager;
    protected $kingdomManager;

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
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $kingdom = $this->botManager->getKingdom();

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
                '%name%' => $kingdom->getName()
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $data = [
            'chat_id' => $kingdom->getUser()->getId(),
            'text' => $text,
            'reply_markup' => $keyboard,
            'parse_mode' => 'Markdown'
        ];

        Request::sendMessage($data);

        $eatHourly = $this->peopleManager->eat($kingdom);
        $foodDay = round($kingdom->getFood() / $eatHourly);

        $level = $this->kingdomManager->level($kingdom);
        $territory = $this->kingdomManager->level($kingdom);

        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_MAIN_MENU_SCREEN_MESSAGE,
            [
                '%level%' => $level,
                '%territory%' => $territory,
                '%territorySize%' => $this->kingdomManager->getTerritorySize($kingdom),
                '%people%' => $this->botManager->getTranslator()->transChoice(
                    TranslatorInterface::TRANSLATOR_MESSAGE_PEOPLES,
                    $kingdom->getPeople(),
                    [
                        '%count%' => $kingdom->getPeople()
                    ],
                    TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                ),
                '%tax%' => $this->botManager->getTranslator()->transChoice(
                    TranslatorInterface::TRANSLATOR_MESSAGE_TAXES_LEVEL,
                    $kingdom->getTax(),
                    [],
                    TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
                ),
                '%gold%' => $kingdom->getGold(),
                '%food%' => $kingdom->getFood(),
                '%wood%' => $kingdom->getWood(),
                '%stone%' => $kingdom->getStone(),
                '%iron%' => $kingdom->getIron(),
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

        $data = [
            'chat_id' => $kingdom->getUser()->getId(),
            'text' => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode' => 'Markdown',
        ];

        return Request::sendMessage($data);
    }
}
