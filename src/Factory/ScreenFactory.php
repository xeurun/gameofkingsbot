<?php

namespace App\Factory;

use App\Interfaces\ScreenInterface;
use App\Manager\BotManager;
use App\Screens\BuildingsScreen;
use App\Screens\PeopleScreen;
use App\Screens\ResearchScreen;
use Psr\Log\InvalidArgumentException;
use App\Screens\AchivementsScreen;
use App\Screens\BaseScreen;
use App\Screens\BonusesScreen;
use App\Screens\DiplomacyScreen;
use App\Screens\EdictsScreen;
use App\Screens\EventScreen;
use App\Screens\MainMenuScreen;
use App\Screens\SettingsScreen;
use App\Screens\TODOScreen;
use App\Screens\TreasureScreen;

class ScreenFactory
{
    /**
     * @param string $screenName
     * @param BotManager $botManager
     * @return BaseScreen
     */
    public function create(string $screenName, BotManager $botManager): BaseScreen
    {
        switch ($screenName) {
            case ScreenInterface::SCREEN_MAIN_MENU:
            case ScreenInterface::SCREEN_BACK:
                $screen = $botManager->get(MainMenuScreen::class);
                break;
            case ScreenInterface::SCREEN_EVENT:
                $screen = $botManager->get(EventScreen::class);
                break;
            case ScreenInterface::SCREEN_EDICTS:
                $screen = $botManager->get(EdictsScreen::class);
                break;
            case ScreenInterface::SCREEN_TREASURE:
                $screen = $botManager->get(TreasureScreen::class);
                break;
            case ScreenInterface::SCREEN_DIPLOMACY:
                $screen = $botManager->get(DiplomacyScreen::class);
                break;
            case ScreenInterface::SCREEN_RESEARCH:
                $screen = $botManager->get(ResearchScreen::class);
                break;
            case ScreenInterface::SCREEN_BONUSES:
                $screen = $botManager->get(BonusesScreen::class);
                break;
            case ScreenInterface::SCREEN_ACHIEVEMENTS:
                $screen = $botManager->get(AchivementsScreen::class);
                break;
            case ScreenInterface::SCREEN_SETTINGS:
                $screen = $botManager->get(SettingsScreen::class);
                break;
            case ScreenInterface::SCREEN_BUILDINGS:
                $screen = $botManager->get(BuildingsScreen::class);
                break;
            case ScreenInterface::SCREEN_PEOPLE:
                $screen = $botManager->get(PeopleScreen::class);
                break;
            default:
                throw new InvalidArgumentException('Incorrect screen name: ' . $screenName);
        }

        return $screen;
    }

    /**
     * @param string $screenName
     * @return bool
     */
    public function isAvailable(string $screenName): bool
    {
        return \in_array($screenName, $this->getAvailable(), true);
    }

    /**
     * @return array
     */
    protected function getAvailable(): array
    {
        return [
            ScreenInterface::SCREEN_BACK,
            ScreenInterface::SCREEN_MAIN_MENU,
            ScreenInterface::SCREEN_EVENT,
            ScreenInterface::SCREEN_EDICTS,
            ScreenInterface::SCREEN_TREASURE,
            ScreenInterface::SCREEN_DIPLOMACY,
            ScreenInterface::SCREEN_RESEARCH,
            ScreenInterface::SCREEN_BONUSES,
            ScreenInterface::SCREEN_ACHIEVEMENTS,
            ScreenInterface::SCREEN_SETTINGS,
            ScreenInterface::SCREEN_BUILDINGS,
            ScreenInterface::SCREEN_PEOPLE
        ];
    }
}
