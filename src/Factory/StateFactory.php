<?php

namespace App\Factory;

use App\Interfaces\StateInterface;
use App\Manager\BotManager;
use App\States\BaseState;
use App\States\KingdomNameState;
use Psr\Log\InvalidArgumentException;

class StateFactory
{
    /**
     * @param string $stateName
     * @param BotManager $botManager
     * @return BaseState
     */
    public function create(string $stateName, BotManager $botManager): BaseState
    {
        switch ($stateName) {
            case StateInterface::STATE_WAIT_KINGDOM_NAME:
                $state = $botManager->get(KingdomNameState::class);
                break;
            default:
                throw new InvalidArgumentException('Incorrect state name: ' . $stateName);
        }

        return $state;
    }

    /**
     * @param string $stateName
     * @return bool
     */
    public function isAvailable(string $stateName): bool
    {
        return \in_array($stateName, $this->getAvailable(), true);
    }

    /**
     * @return array
     */
    protected function getAvailable(): array
    {
        return [
            StateInterface::STATE_NEW_PLAYER,
            StateInterface::STATE_WAIT_KINGDOM_NAME
        ];
    }
}
