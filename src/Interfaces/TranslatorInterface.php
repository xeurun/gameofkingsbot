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
    public const TRANSLATOR_WORK_TYPE_PREFIX_TO = 'to_';
    /** @var string */
    public const TRANSLATOR_WORK_TYPE_PREFIX_FROM = 'from_';
    /** @var string */
    public const TRANSLATOR_MESSAGE_CHOOSE_LANG = 'choose_lang';
    /** @var string */
    public const TRANSLATOR_MESSAGE_CHOOSE_GENDER = 'choose_gender';
    /** @var string */
    public const TRANSLATOR_MESSAGE_CHOOSE_NAME = 'choose_name';
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
    public const TRANSLATOR_MESSAGE_WHAT_TAXES_LEVEL = 'what_taxes_level';
    /** @var string */
    public const TRANSLATOR_MESSAGE_HIRED_PEOPLE = 'hired_people';
    /** @var string */
    public const TRANSLATOR_MESSAGE_FIRED_PEOPLE = 'fired_people';
    /** @var string */
    public const TRANSLATOR_MESSAGE_NO_HIRED_PEOPLE = 'no_hired_people';
    /** @var string */
    public const TRANSLATOR_MESSAGE_NO_COUNT_FOR_FIRE_PEOPLE = 'no_count_for_fire_people';
    /** @var string */
    public const TRANSLATOR_MESSAGE_NO_COUNT_FOR_HIRE_PEOPLE = 'no_count_for_hire_people';
    /** @var string */
    public const TRANSLATOR_MESSAGE_HAS_NOT_FREE_SPACE = 'has_not_free_space';
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
    public const TRANSLATOR_MESSAGE_BUILDINGS_SCREEN_MESSAGE_STRUCTURE = 'buildings_screen_message_structure';
    /** @var string */
    public const TRANSLATOR_MESSAGE_BUILDINGS_SCREEN_MESSAGE_STRUCTURE_CHOOSE = 'buildings_screen_message_structure_choose';
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
    public const TRANSLATOR_MESSAGE_RESEACRH_SCREEN_MESSAGE_WITHOUT_LIBRARY = 'research_screen_message_without_library';
    /** @var string */
    public const TRANSLATOR_MESSAGE_WAREHOUSE_SCREEN_MESSAGE = 'warehouse_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_WAREHOUSE_ADDITIONAL_SCREEN_MESSAGE = 'warehouse_additional_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_WAREHOUSE_WITHOUT_ADDITIONAL_SCREEN_MESSAGE = 'warehouse_without_additional_screen_message';
    /** @var string */
    public const TRANSLATOR_MESSAGE_INPUT_STRUCTURE_COUNT_FOR_BUY = 'input_structure_count_for_buy';
    /** @var string */
    public const TRANSLATOR_MESSAGE_INPUT_PEOPLE_COUNT_FOR_HIRE_OR_FIRE = 'input_people_count_for_hire_or_fire';
    /** @var string */
    public const TRANSLATOR_MESSAGE_HIRE_OR_FIRE = 'hire_or_fire';
    /** @var string */
    public const TRANSLATOR_MESSAGE_FOR_HIRE_OR_FIRE = 'for_hire_or_fire';
    /** @var string */
    public const TRANSLATOR_MESSAGE_HOURS = 'hours';
    /** @var string */
    public const TRANSLATOR_MESSAGE_PEOPLES = 'peoples';
    /** @var string */
    public const TRANSLATOR_MESSAGE_PEOPLES_RAW = 'peoples_raw';
    /** @var string */
    public const TRANSLATOR_MESSAGE_HIRE = 'hire';
    /** @var string */
    public const TRANSLATOR_MESSAGE_FIRE = 'fire';
    /** @var string */
    public const TRANSLATOR_MESSAGE_RAISE = 'raise';
    /** @var string */
    public const TRANSLATOR_MESSAGE_LOWER = 'lower';
    /** @var string */
    public const TRANSLATOR_MESSAGE_BUY_STRUCTURE = 'buy_structure';
    /** @var string */
    public const TRANSLATOR_MESSAGE_GROUP_BUTTON = 'group_button';
    /** @var string */
    public const TRANSLATOR_MESSAGE_CHANNEL_BUTTON = 'channel_button';
    /** @var string */
    public const TRANSLATOR_MESSAGE_CHANGE_KINGDOM_NAME_BUTTON = 'change_kingdom_name_button';
    /** @var string */
    public const TRANSLATOR_MESSAGE_CHANGE_USER_NAME_BUTTON = 'change_user_name_button';
    /** @var string */
    public const TRANSLATOR_MESSAGE_ENTER_TO_GROUP_BUTTON = 'enter_to_group_button';
    /** @var string */
    public const TRANSLATOR_MESSAGE_SUBSCRIBE_ON_CHANNEL_BUTTON = 'subscribe_on_channel_button';
    /** @var string */
    public const TRANSLATOR_MESSAGE_RECEIVE_EVERY_DAY_BONUSES_BUTTON = 'receive_every_day_bonuses_button';
    /** @var string */
    public const TRANSLATOR_MESSAGE_MOVE_EXTRACTED_RESOURCES_TO_WAREHOUSE_BUTTON = 'move_extracted_resources_to_warehouse';
    /** @var string */
    public const TRANSLATOR_MESSAGE_MY_QUEEN = 'my_queen';
    /** @var string */
    public const TRANSLATOR_MESSAGE_MY_KING = 'my_king';
    /** @var string */
    public const TRANSLATOR_MESSAGE_SUPREME_GENDER = 'supreme_gender';
    /** @var string */
    public const TRANSLATOR_MESSAGE_GENDER = 'gender';
    /** @var string */
    public const TRANSLATOR_MESSAGE_REFER_KINGDOM = 'refer_kingdom';
    /** @var string */
    public const TRANSLATOR_MESSAGE_REFER_SUPREME_GENDER = 'refer_supreme_gender';
    /** @var string */
    public const TRANSLATOR_MESSAGE_CASTLE_LEVEL_UP = 'castle_level_up';
    /** @var string */
    public const TRANSLATOR_MESSAGE_TERRITORY_LEVEL_UP = 'territory_level_up';
    /** @var string */
    public const TRANSLATOR_MESSAGE_STRUCTURE_LEVEL_UP = 'structure_level_up';
    /** @var string */
    public const TRANSLATOR_MESSAGE_NO_HAVE_FREE_SPACE = 'no_have_free_space';
    /** @var string */
    public const TRANSLATOR_MESSAGE_NO_HAVE_FREE_SPACE_AND_AVAILABLE_RESOURCES = 'no_have_free_space_and_available_resources';
    /** @var string */
    public const TRANSLATOR_MESSAGE_NO_HAVE_AVAILABLE_RESOURCES = 'no_have_available_resources';
}
