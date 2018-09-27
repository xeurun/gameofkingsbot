<?php

namespace App\Screens;

use App\Helper\CurrencyHelper;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use App\Interfaces\StructureInterface;
use App\Interfaces\TaxesInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use App\Repository\StructureTypeRepository;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class BuildingsScreen extends BaseScreen
{
    protected $workManager;
    protected $peopleManager;
    protected $kingdomManager;
    protected $buildTypeRepository;

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
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        return Request::sendMessage($this->getMessageData());
    }

    public function getMessageData()
    {
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_BUILDINGS;

        $free = $this->workManager->free($kingdom);
        $territorySize = $this->kingdomManager->getTerritorySize($kingdom);

        $text = <<<TEXT
*{$title}*

`💰 `*{$kingdom->getGold()}* | `🌲 `*{$kingdom->getWood()}*  | `⛏ `*{$kingdom->getStone()}*  | `🔨 `*{$kingdom->getIron()}*

`🏛️ Мест для постройки: `*{$territorySize}*


TEXT;

        $pack = function ($name, $data) {
            $data['n'] = $name;
            return json_encode($data);
        };

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
            $text .= <<<TEXT
`🏛 {$buildType->getName()} - `*{$level}*
`Стоимость $costText`
---

TEXT;

            $buildings[] = [
                [
                    'text' => '🏛 ' . $buildType->getName(),
                    'callback_data' => $pack(CallbackInterface::CALLBACK_GET_INFO, ['t' => TaxesInterface::TAXES])
                ],
                [
                    'text' => 'Купить 📝',
                    'callback_data' => $pack(CallbackInterface::CALLBACK_INCREASE_STRUCTURE_LEVEL, ['c' => $buildType->getCode()])
                ]
            ];
        }

        $text .= <<<TEXT
          
выберите здание для покупки или улучшения
TEXT;

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
