<?php

namespace App\Screens;

use App\Interfaces\ScreenInterface;
use App\Interfaces\TaxesInterface;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class KingdomScreen extends BaseScreen
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
        $kingdom = $this->botManager->getKingdom();
        $user = $this->botManager->getKingdom()->getUser();
        $eatHourly = $this->peopleManager->eat($kingdom);
        $payHourly = $this->peopleManager->pay($kingdom);
        $taxLevel = $this->peopleManager->taxLevel($kingdom);

        $foodHourly = $this->workManager->food($kingdom);
        $woodHourly = $this->workManager->wood($kingdom);
        $stoneHourly = $this->workManager->stone($kingdom);
        $metalHourly = $this->workManager->metal($kingdom);

        $title = ScreenInterface::SCREEN_KINGDOM;
        $text = <<<TEXT
*{$title} - {$kingdom->getName()}*

*{$user->getFirstName()} {$user->getLastName()} вы верховный король*

Люди съедают {$eatHourly}ед. еды в час и плотят {$payHourly}ед. золота налогов

{$kingdom->getOnFood()} людей заняты добычей еды, они добывают {$foodHourly}ед. в час
{$kingdom->getOnWood()} людей заняты добычей древесины, они добывают {$woodHourly}ед. в час
{$kingdom->getOnStone()} людей заняты добычей камней, они добывают {$stoneHourly}ед. в час
{$kingdom->getOnMetal()} людей заняты добычей железа, они добывают {$metalHourly}ед. в час
{$kingdom->getOnBuildings()} людей заняты постройкой

Уровень налогов: {$taxLevel}

За последние дни никто не умер...
TEXT;
        $data = [
            'chat_id'      => $kingdom->getUser()->getId(),
            'text'         => $text,
            'parse_mode'   => 'Markdown',
        ];

        return Request::sendMessage($data);
    }
}
