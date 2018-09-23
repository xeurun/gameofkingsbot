<?php

namespace App\Factory;

use App\Callbacks\BaseCallback;
use App\Callbacks\BuildLevelUpCallback;
use App\Callbacks\ChangeKingdomNameCallback;
use App\Callbacks\EveryDayBonusCallback;
use App\Callbacks\GrabResourcesCallback;
use App\Callbacks\UpDownTaxCallback;
use App\Callbacks\UpDownWorkerCallback;
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
    public function create(string $callbackName, BotManager $botManager): BaseCallback {
        switch ($callbackName) {
            case CallbackInterface::CALLBACK_EVERY_DAY_BONUS:
                $state = $botManager->get(EveryDayBonusCallback::class);
                break;
            case CallbackInterface::CALLBACK_UP_DOWN_TAX:
                $state = $botManager->get(UpDownTaxCallback::class);
                break;
            case CallbackInterface::CALLBACK_UP_DOWN_WORKER:
                $state = $botManager->get(UpDownWorkerCallback::class);
                break;
            case CallbackInterface::CALLBACK_GRAB_RESOURCES:
                $state = $botManager->get(GrabResourcesCallback::class);
                break;
            case CallbackInterface::CALLBACK_BUILD_LEVEL_UP:
                $state = $botManager->get(BuildLevelUpCallback::class);
                break;
            case CallbackInterface::CALLBACK_CHANGE_KINGDOM_NAME:
                $state = $botManager->get(ChangeKingdomNameCallback::class);
                break;
            default:
                throw new InvalidArgumentException('Incorrect state name: ' . $callbackName);
        }

        return $state;
    }

    /**
     * @param CallbackQuery $callbackQuery
     * @return mixed|string
     */
    public function getData(CallbackQuery $callbackQuery)
    {
        $callbackData = $callbackQuery->getData();
        if (strpos($callbackData, '{') === 0) {
            $callbackData = json_decode($callbackData, true);
        } else {
            $callbackData = [
                'n' => $callbackData
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
            CallbackInterface::CALLBACK_UP_DOWN_TAX,
            CallbackInterface::CALLBACK_UP_DOWN_WORKER,
            CallbackInterface::CALLBACK_GRAB_RESOURCES,
            CallbackInterface::CALLBACK_CHANGE_KINGDOM_NAME,
            CallbackInterface::CALLBACK_BUILD_LEVEL_UP
        ];
    }
}
