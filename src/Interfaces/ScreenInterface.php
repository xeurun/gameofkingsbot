<?php

namespace App\Interfaces;

interface ScreenInterface
{
    /** @var string */
    public const SCREEN_BACK = '↩️ Назад';
    /** @var string */
    public const SCREEN_MAIN_MENU = '🤴 Главное меню 👸';
    /** @var string */
    public const SCREEN_EVENT = '🔜 📅 События';
    /** @var string */
    public const SCREEN_EDICTS = '📖 Указы';
    /** @var string */
    public const SCREEN_TREASURE = '🏚️ Склад';
    /** @var string */
    public const SCREEN_DIPLOMACY = '🔜 🤝 Дипломатия ⚔';
    /** @var string */
    public const SCREEN_RESEARCH = '🔜 💡 Исследования';
    /** @var string */
    public const SCREEN_BONUSES = '💎 Бонусы';
    /** @var string */
    public const SCREEN_ACHIEVEMENTS = '🔜 💪 Достижения';
    /** @var string */
    public const SCREEN_SETTINGS = '⚙️ Настройки';
    /** @var string */
    public const SCREEN_BUILDINGS = '🏛️ Постройки';
    /** @var string */
    public const SCREEN_PEOPLE = '👪 Люди';
}
