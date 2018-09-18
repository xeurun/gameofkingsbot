<?php

namespace Factory;

use Interfaces\ScreenInterface;
use Psr\Log\InvalidArgumentException;
use Screens\AchivementsScreen;
use Screens\BaseScreen;
use Screens\BonusesScreen;
use Screens\DiplomacyScreen;
use Screens\EdictsScreen;
use Screens\KingdomScreen;
use Screens\MainMenuScreen;
use Screens\SettingsScreen;
use Screens\TODOScreen;
use Screens\TreasureScreen;

class ScreenFactory
{
    /**
     * @param int $chatId
     * @param string $screenName
     * @return MainMenuScreen
     */
    public function createScreen(int $chatId, string $screenName): BaseScreen
    {
        switch ($screenName) {
            case ScreenInterface::SCREEN_MAIN_MENU:
                $screen = new MainMenuScreen($chatId, $screenName);
                break;
            case ScreenInterface::SCREEN_BACK:
                // TODO: move to prev state
                $screen = new MainMenuScreen($chatId, ScreenInterface::SCREEN_MAIN_MENU);
                break;
            case ScreenInterface::SCREEN_KINGDOM:
                $screen = new KingdomScreen($chatId, $screenName);
                break;
            case ScreenInterface::SCREEN_EDICTS:
                $screen = new EdictsScreen($chatId, $screenName);
                break;
            case ScreenInterface::SCREEN_TREASURE:
                $screen = new TreasureScreen($chatId, $screenName);
                break;
            case ScreenInterface::SCREEN_DIPLOMACY:
                $screen = new DiplomacyScreen($chatId, $screenName);
                break;
            case ScreenInterface::SCREEN_TODO1:
            case ScreenInterface::SCREEN_TODO2:
                $screen = new TODOScreen($chatId, $screenName);
                break;
            case ScreenInterface::SCREEN_BONUSES:
                $screen = new BonusesScreen($chatId, $screenName);
                break;
            case ScreenInterface::SCREEN_ACHIEVEMENTS:
                $screen = new AchivementsScreen($chatId, $screenName);
                break;
            case ScreenInterface::SCREEN_SETTINGS:
                $screen = new SettingsScreen($chatId, $screenName);
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
    public function isAvailableScreen(string $screenName): bool
    {
        return \in_array($screenName, $this->getAvailableScreens(), true);
    }

    /**
     * @return array
     */
    protected function getAvailableScreens(): array
    {
        return [
            ScreenInterface::SCREEN_BACK,
            ScreenInterface::SCREEN_MAIN_MENU,
            ScreenInterface::SCREEN_KINGDOM,
            ScreenInterface::SCREEN_EDICTS,
            ScreenInterface::SCREEN_TREASURE,
            ScreenInterface::SCREEN_DIPLOMACY,
            ScreenInterface::SCREEN_TODO1,
            ScreenInterface::SCREEN_TODO2,
            ScreenInterface::SCREEN_BONUSES,
            ScreenInterface::SCREEN_ACHIEVEMENTS,
            ScreenInterface::SCREEN_SETTINGS,
        ];
    }
}
