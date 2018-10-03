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
    public function create(string $callbackName): BaseCallback
    {
        switch ($callbackName) {
            case CallbackInterface::CALLBACK_EVERY_DAY_BONUS:
                $state = $this->botManager->get(EveryDayBonusCallback::class);

                break;
            case CallbackInterface::CALLBACK_RAISE_OR_LOWER_TAXES:
                $state = $this->botManager->get(RiseOrLowerTaxesCallback::class);

                break;
            case CallbackInterface::CALLBACK_HIRE_OR_FIRE_PEOPLE:
                $state = $this->botManager->get(HireOrFirePeopleCallback::class);

                break;
            case CallbackInterface::CALLBACK_MOVE_RESOURCES_TO_WAREHOUSE:
                $state = $this->botManager->get(MoveResourcesToWarehouseCallback::class);

                break;
            case CallbackInterface::CALLBACK_INCREASE_STRUCTURE_LEVEL:
                $state = $this->botManager->get(IncreaseStructureLevelCallback::class);

                break;
            case CallbackInterface::CALLBACK_CHANGE_STATE:
                $state = $this->botManager->get(ChangeStateCallback::class);

                break;
            case CallbackInterface::CALLBACK_ADVISER:
                $state = $this->botManager->get(AdviserCallback::class);

                break;
            case CallbackInterface::CALLBACK_GET_INFO:
                $state = $this->botManager->get(GetInfoCallback::class);

                break;
            default:
                throw new InvalidArgumentException('Incorrect state name: ' . $callbackName);
        }

        return $state;
    }

    /**
     * @param mixed $args
     *
     * @return false|string
     */
    public static function pack(...$args)
    {
        return '{' . implode('@', $args);
    }

    /**
     * @return mixed|string
     */
    public static function getData(CallbackQuery $callbackQuery)
    {
        $callbackData = $callbackQuery->getData();
        if (0 === strpos($callbackData, '{')) {
            $callbackData = explode('@', mb_substr($callbackData, 1));
        } else {
            $callbackData = [
                $callbackData,
            ];
        }

        return $callbackData;
    }

    /**
     * Check type is available.
     */
    public function isAvailable(string $callbackName): bool
    {
        return \in_array($callbackName, $this->getAvailable(), true);
    }

    /**
     * Get available type.
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
            CallbackInterface::CALLBACK_GET_INFO,
        ];
    }
}
