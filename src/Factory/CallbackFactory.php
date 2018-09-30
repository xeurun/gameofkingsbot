<?php

namespace App\Factory;

use App\Callbacks\BaseCallback;
use App\Callbacks\ChangeStateCallback;
use App\Callbacks\EveryDayBonusCallback;
use App\Callbacks\GetInfoCallback;
use App\Callbacks\HireOrFirePeopleCallback;
use App\Callbacks\IncreaseStructureLevelCallback;
use App\Callbacks\MoveResourcesToWarehouseCallback;
use App\Callbacks\RiseOrLowerTaxesCallback;
use App\Callbacks\AdviserCallback;
use App\Interfaces\CallbackInterface;
use App\Manager\BotManager;
use Longman\TelegramBot\Entities\CallbackQuery;
use Psr\Log\InvalidArgumentException;

class CallbackFactory
{
    /**
     * @param string $callbackName
     * @param BotManager $botManager
     * @return BaseCallback
     */
    public function create(string $callbackName, BotManager $botManager): BaseCallback
    {
        switch ($callbackName) {
            case CallbackInterface::CALLBACK_EVERY_DAY_BONUS:
                $state = $botManager->get(EveryDayBonusCallback::class);
                break;
            case CallbackInterface::CALLBACK_RAISE_OR_LOWER_TAXES:
                $state = $botManager->get(RiseOrLowerTaxesCallback::class);
                break;
            case CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE:
                $state = $botManager->get(HireOrFirePeopleCallback::class);
                break;
            case CallbackInterface::CALLBACK_MOVE_RESOURCES_TO_WAREHOUSE:
                $state = $botManager->get(MoveResourcesToWarehouseCallback::class);
                break;
            case CallbackInterface::CALLBACK_INCREASE_STRUCTURE_LEVEL:
                $state = $botManager->get(IncreaseStructureLevelCallback::class);
                break;
            case CallbackInterface::CALLBACK_CHANGE_STATE:
                $state = $botManager->get(ChangeStateCallback::class);
                break;
            case CallbackInterface::CALLBACK_ADVISER:
                $state = $botManager->get(AdviserCallback::class);
                break;
            case CallbackInterface::CALLBACK_GET_INFO:
                $state = $botManager->get(GetInfoCallback::class);
                break;
            default:
                throw new InvalidArgumentException('Incorrect state name: ' . $callbackName);
        }

        return $state;
    }


    /**
     * @param mixed $args
     * @return false|string
     */
    public static function pack(...$args)
    {
        return '{' . implode('@', $args);
    }

    /**
     * @param CallbackQuery $callbackQuery
     * @return mixed|string
     */
    public static function getData(CallbackQuery $callbackQuery)
    {
        $callbackData = $callbackQuery->getData();
        if (strpos($callbackData, '{') === 0) {
            $callbackData = explode('@', mb_substr($callbackData, 1));
        } else {
            $callbackData = [
                $callbackData
            ];
        }

        return $callbackData;
    }

    /**
     * @param string $callbackName
     * @return bool
     */
    public function isAvailable(string $callbackName): bool
    {
        return \in_array($callbackName, $this->getAvailable(), true);
    }

    /**
     * @return array
     */
    protected function getAvailable(): array
    {
        return [
            CallbackInterface::CALLBACK_EVERY_DAY_BONUS,
            CallbackInterface::CALLBACK_RAISE_OR_LOWER_TAXES,
            CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE,
            CallbackInterface::CALLBACK_MOVE_RESOURCES_TO_WAREHOUSE,
            CallbackInterface::CALLBACK_CHANGE_STATE,
            CallbackInterface::CALLBACK_INCREASE_STRUCTURE_LEVEL,
            CallbackInterface::CALLBACK_ADVISER,
            CallbackInterface::CALLBACK_GET_INFO
        ];
    }
}
