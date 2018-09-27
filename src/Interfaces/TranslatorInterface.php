<?php

namespace App\Interfaces;

interface TranslatorInterface
{
    /** @var string */
    public const TRANSLATOR_DOMAIN_STATE = 'state';
    /** @var string */
    public const TRANSLATOR_DOMAIN_CALLBACK = 'callback';
    /** @var string */
    public const TRANSLATOR_DOMAIN_INLINE = 'inline';
    /** @var string */
    public const TRANSLATOR_DOMAIN_SCREEN = 'screen';
    /** @var string */
    public const TRANSLATOR_DOMAIN_COMMON = 'common';
    /** @var string */
    public const TRANSLATOR_MESSAGE_CHOOSE_LANG = 'choose_lang';
    /** @var string */
    public const TRANSLATOR_MESSAGE_CHOOSE_GENDER = 'choose_gender';
    /** @var string */
    public const TRANSLATOR_MESSAGE_NEW_KING = 'new_king';
    /** @var string */
    public const TRANSLATOR_MESSAGE_NEW_KING_GENDER = 'new_king_gender';
    /** @var string */
    public const TRANSLATOR_MESSAGE_EVERY_DAY_BONUS_ALREADY_RECEIVED = 'every_day_bonus_already_received';
    /** @var string */
    public const TRANSLATOR_MESSAGE_EVERY_DAY_BONUS_RECEIVED = 'every_day_bonus_received';
    /** @var string */
    public const TRANSLATOR_MESSAGE_EXTRACTED_RESOURCES_RECEIVED = 'extracted_resources_received';
    /** @var string */
    public const TRANSLATOR_MESSAGE_RAISE_TAXES = 'raise_taxes';
    /** @var string */
    public const TRANSLATOR_MESSAGE_LOWER_TAXES = 'lower_taxes';
    /** @var string */
    public const TRANSLATOR_MESSAGE_TAXES_LEVEL = 'taxes_level';
    /** @var string */
    public const TRANSLATOR_MESSAGE_HIRED_PEOPLE = 'hired_people';
    /** @var string */
    public const TRANSLATOR_MESSAGE_FIRED_PEOPLE = 'fired_people';
    /** @var string */
    public const TRANSLATOR_MESSAGE_NO_HIRED_PEOPLE = 'no_hired_people';
    /** @var string */
    public const TRANSLATOR_MESSAGE_NO_FIRED_PEOPLE = 'no_fired_people';
    /** @var string */
    public const TRANSLATOR_MESSAGE_MAIN_MENU_SCREEN_TITLE = 'main_menu_screen_title';
    /** @var string */
    public const TRANSLATOR_MESSAGE_MAIN_MENU_SCREEN_MESSAGE = 'main_menu_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_SETTINGS_SCREEN_MESSAGE = 'settings_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_ACHIVEMENTS_SCREEN_MESSAGE = 'achivements_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_BONUSES_SCREEN_MESSAGE = 'bonuses_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_BUILDINGS_SCREEN_MESSAGE = 'buildings_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_DIPLOMACY_SCREEN_MESSAGE = 'diplomacy_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_EDICTS_SCREEN_MESSAGE = 'edicts_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_EVENT_SCREEN_MESSAGE = 'event_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_PEOPLE_SCREEN_MESSAGE = 'people_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_RESEACRH_SCREEN_MESSAGE = 'research_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_WAREHOUSE_SCREEN_MESSAGE = 'warehouse_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_WAREHOUSE_ADDITIONAL_SCREEN_MESSAGE = 'warehouse_additional_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_WAREHOUSE_WITHOUT_ADDITIONAL_SCREEN_MESSAGE = 'warehouse_without_additional_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_HOURS = 'hours';
    /** @var string */
    public const TRANSLATOR_MESSAGE_PEOPLES = 'peoples';
    /** @var string */
    public const TRANSLATOR_MESSAGE_HIRE = 'hire';
    /** @var string */
    public const TRANSLATOR_MESSAGE_FIRE = 'fire';
    /** @var string */
    public const TRANSLATOR_MESSAGE_RAISE = 'raise';
    /** @var string */
    public const TRANSLATOR_MESSAGE_LOWER = 'lower';
    /** @var string */
    public const TRANSLATOR_MESSAGE_GROUP_BUTTON = 'group_button';
    /** @var string */
    public const TRANSLATOR_MESSAGE_CHANNEL_BUTTON = 'channel_button';
    /** @var string */
    public const TRANSLATOR_MESSAGE_CHANGE_KINGDOM_NAME_BUTTON = 'change_kingdom_name_button';
    /** @var string */
    public const TRANSLATOR_MESSAGE_ENTER_TO_GROUP_BUTTON = 'enter_to_group_button';
    /** @var string */
    public const TRANSLATOR_MESSAGE_SUBSCRIBE_ON_CHANNEL_BUTTON = 'subscribe_on_channel_button';
    /** @var string */
    public const TRANSLATOR_MESSAGE_RECEIVE_EVERY_DAY_BONUSES_BUTTON = 'receive_every_day_bonuses_button';
    /** @var string */
    public const TRANSLATOR_MESSAGE_MOVE_EXTRACTED_RESOURCES_TO_WAREHOUSE_BUTTON = 'move_extracted_resources_to_warehouse';
}
