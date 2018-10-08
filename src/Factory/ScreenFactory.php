<?php

namespace App\Factory;

use App\Interfaces\ScreenInterface;
use App\Manager\BotManager;
use App\Screens\AchivementsScreen;
use App\Screens\BaseScreen;
use App\Screens\BonusesScreen;
use App\Screens\DiplomacyScreen;
use App\Screens\Edicts\BuildingsScreen;
use App\Screens\Edicts\PeopleScreen;
use App\Screens\EdictsScreen;
use App\Screens\EventScreen;
use App\Screens\MainMenuScreen;
use App\Screens\ResearchScreen;
use App\Screens\SettingsScreen;
use App\Screens\WarehouseScreen;
use Psr\Log\InvalidArgumentException;

class ScreenFactory
{
    /** @var BotManager */
    protected $botManager;

    /**
     * StateFactory constructor.
     */
    public function __construct(BotManager $botManager)
    {
        $this->botManager = $botManager;
    }

    /**
     * Create.
     */
    public function create(string $screenName): BaseScreen
    {
        switch ($screenName) {
            case ScreenInterface::SCREEN_MAIN_MENU:
            case ScreenInterface::SCREEN_KINGDOM_KING:
            case ScreenInterface::SCREEN_KINGDOM_QUEEN:
            case ScreenInterface::SCREEN_BACK:
                $screen = $this->botManager->get(MainMenuScreen::class);

                break;
            case ScreenInterface::SCREEN_EVENT:
                $screen = $this->botManager->get(EventScreen::class);

                break;
            case ScreenInterface::SCREEN_EDICTS:
                $screen = $this->botManager->get(EdictsScreen::class);

                break;
            case ScreenInterface::SCREEN_TREASURE:
                $screen = $this->botManager->get(WarehouseScreen::class);

                break;
            case ScreenInterface::SCREEN_DIPLOMACY:
                $screen = $this->botManager->get(DiplomacyScreen::class);

                break;
            case ScreenInterface::SCREEN_RESEARCH:
                $screen = $this->botManager->get(ResearchScreen::class);

                break;
            case ScreenInterface::SCREEN_BONUSES:
                $screen = $this->botManager->get(BonusesScreen::class);

                break;
            case ScreenInterface::SCREEN_ACHIEVEMENTS:
                $screen = $this->botManager->get(AchivementsScreen::class);

                break;
            case ScreenInterface::SCREEN_SETTINGS:
                $screen = $this->botManager->get(SettingsScreen::class);

                break;
            case ScreenInterface::SCREEN_BUILDINGS:
                $screen = $this->botManager->get(BuildingsScreen::class);

                break;
            case ScreenInterface::SCREEN_PEOPLE:
                $screen = $this->botManager->get(PeopleScreen::class);

                break;
            default:
                throw new InvalidArgumentException('Incorrect screen name: ' . $screenName);
        }

        return $screen;
    }

    /**
     * Check type is available.
     */
    public function isAvailable(string $screenName): bool
    {
        return \in_array($screenName, $this->getAvailable(), true);
    }

    /**
     * Get available type.
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
            ScreenInterface::SCREEN_PEOPLE,
        ];
    }
}
