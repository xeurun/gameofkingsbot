<?php

namespace App\Screens;

use App\Interfaces\CallbackInterface;
use App\Interfaces\ResourceInterface;
use App\Interfaces\ScreenInterface;
use App\Interfaces\StructureInterface;
use App\Interfaces\TaxesInterface;
use App\Interfaces\TranslatorInterface;
use App\Interfaces\WorkInterface;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class PeopleScreen extends BaseScreen
{
    protected $workManager;
    protected $peopleManager;

    public function __construct(BotManager $botManager, WorkManager $workManager, PeopleManager $peopleManager)
    {
        $this->workManager = $workManager;
        $this->peopleManager = $peopleManager;
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

        $title = ScreenInterface::SCREEN_PEOPLE;

        $eatHourly = $this->peopleManager->eat($kingdom);
        $payHourly = $this->peopleManager->pay($kingdom);

        $foodHourly = $this->workManager->food($kingdom);
        $woodHourly = $this->workManager->wood($kingdom);
        $stoneHourly = $this->workManager->stone($kingdom);
        $ironHourly = $this->workManager->iron($kingdom);

        $free = $this->workManager->free($kingdom);

        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_PEOPLE_SCREEN_MESSAGE,
            [
                '%title%' => $title,
                '%people%' => $this->botManager->getTranslator()->transChoice(
                    TranslatorInterface::TRANSLATOR_MESSAGE_PEOPLES,
                    $kingdom->getPeople(),
                    [
                        '%count%' => $kingdom->getPeople()
                    ],
                    TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                ),
                '%eatHourly%' => $eatHourly,
                '%taxLevel%' => mb_strtolower(
                        $this->botManager->getTranslator()->transChoice(
                        TranslatorInterface::TRANSLATOR_MESSAGE_TAXES_LEVEL,
                        $kingdom->getTax(),
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
                    )
                ),
                '%payHourly%' => $payHourly,
                '%onStructure%' => $kingdom->getOnStructure(),
                '%onFood%' => $kingdom->getOnFood(),
                '%onWood%' => $kingdom->getOnWood(),
                '%onStone%' => $kingdom->getOnStone(),
                '%onIron%' => $kingdom->getOnIron(),
                '%foodHourly%' => $foodHourly,
                '%woodHourly%' => $woodHourly,
                '%stoneHourly%' => $stoneHourly,
                '%ironHourly%' => $ironHourly,
                '%free%' => $this->botManager->getTranslator()->transChoice(
                    TranslatorInterface::TRANSLATOR_MESSAGE_PEOPLES,
                    $free,
                    [
                        '%count%' => $free
                    ],
                    TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                )
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $pack = function ($name, $data) {
            $data['n'] = $name;
            return json_encode($data);
        };

        $inlineKeyboard = new InlineKeyboard(
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TaxesInterface::TAXES,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_GET_INFO, ['t' => TaxesInterface::TAXES])
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_LOWER,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_RAISE_OR_LOWER_TAXES, ['v' => '-'])
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_RAISE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_RAISE_OR_LOWER_TAXES, ['v' => '+'])
                ],
            ],
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        WorkInterface::WORK_TYPE_STRUCTURE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_GET_INFO, ['t' => WorkInterface::WORK_TYPE_STRUCTURE])
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_FIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        ['t' => 'buildings', 'v' => '-'])
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_HIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        ['t' => 'buildings', 'v' => '+'])
                ],
            ],
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        ResourceInterface::RESOURCE_FOOD,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_GET_INFO, ['t' => WorkInterface::WORK_TYPE_FOOD])
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_FIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        ['t' => 'food', 'v' => '-'])
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_HIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        ['t' => 'food', 'v' => '+'])
                ],
            ],
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        ResourceInterface::RESOURCE_WOOD,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_GET_INFO, ['t' => WorkInterface::WORK_TYPE_WOOD])
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_FIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        ['t' => 'wood', 'v' => '-'])
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_HIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        ['t' => 'wood', 'v' => '+'])
                ],
            ],
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        ResourceInterface::RESOURCE_STONE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_GET_INFO, ['t' => WorkInterface::WORK_TYPE_STONE])
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_FIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        ['t' => 'stone', 'v' => '-'])
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_HIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        ['t' => 'stone', 'v' => '+'])
                ],
            ],
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        ResourceInterface::RESOURCE_IRON,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_GET_INFO, ['t' => WorkInterface::WORK_TYPE_IRON])
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_FIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        ['t' => 'metal', 'v' => '-'])
                ],
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_HIRE,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
                        ['t' => 'metal', 'v' => '+'])
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
