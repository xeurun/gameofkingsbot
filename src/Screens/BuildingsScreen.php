<?php

namespace App\Screens;

use App\Entity\BuildType;
use App\Interfaces\BuildInterface;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use App\Repository\BuildTypeRepository;
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
        BuildTypeRepository $buildTypeRepository
    ) {
        $this->workManager = $workManager;
        $this->peopleManager = $peopleManager;
        $this->kingdomManager = $kingdomManager;
        $this->buildTypeRepository = $buildTypeRepository;

        parent::__construct($botManager);
    }

    public function getMessageData()
    {
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_BUILDINGS;

        $free = $this->workManager->free($kingdom);
        $level = $this->kingdomManager->level($kingdom);

        $text = <<<TEXT
*{$title}*

| `💰 `*{$kingdom->getGold()}* | `🌲 `*{$kingdom->getWood()}*  | `⛏ `*{$kingdom->getStone()}*  | `🔨 `*{$kingdom->getMetal()}* |

`👪 Людей свободно: `*{$free}*
`🏛️ Строителей: `*{$kingdom->getOnBuildings()}*

`🏰 Уровень замка - `*{$level}*


TEXT;

        $pack = function ($name, $data) {
            $data['n'] = $name;
            return json_encode($data);
        };

        $buildings = [];
        $buildTypes = $this->buildTypeRepository->findAll();
        foreach ($buildTypes as $buildType) {
            if ($buildType->getCode() === BuildInterface::BUILD_TYPE_CASTLE) {
                $buildText = 'Улучшить ';
            } else {
                $buildText = 'Построить ';
                $build = $kingdom->getBuild($buildType->getCode());
                $level = 0;
                if ($build) {
                    $level = $build->getLevel();
                }
                $text .= <<<TEXT
`🏛 {$buildType->getName()} - `*{$level}*

TEXT;
            }

            $buildText .= mb_strtolower($buildType->getName());
            $buildText .= ' за (';

            $cost = [];
            if ($buildType->getGold() > 0) {
                $cost[] = $buildType->getGold() . ' 💰';
            }
            if ($buildType->getWood() > 0) {
                $cost[] = $buildType->getWood() . ' 🌲';
            }
            if ($buildType->getStone() > 0) {
                $cost[] = $buildType->getStone() . ' ⛏';
            }
            if ($buildType->getMetal() > 0) {
                $cost[] = $buildType->getMetal() . ' 🔨';
            }
            $buildText .= implode(', ', $cost) . ')';

            $buildings[] = [
                ['text' => $buildText, 'callback_data' => $pack(CallbackInterface::CALLBACK_BUILD_LEVEL_UP, ['c' => $buildType->getCode()])]
            ];
        }

        $inlineKeyboard = new InlineKeyboard(
            ...$buildings
        );

        return [
            'chat_id'      => $kingdom->getUser()->getId(),
            'text'         => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode'   => 'Markdown',
        ];
    }

    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        return Request::sendMessage($this->getMessageData());
    }
}
