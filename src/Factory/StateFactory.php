<?php

namespace App\Factory;

use App\Interfaces\StateInterface;
use App\Manager\BotManager;
use App\States\BaseState;
use App\States\ChooseLangState;
use App\States\ChooseGenderState;
use App\States\ChooseNameState;
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
            case StateInterface::STATE_WAIT_CHOOSE_LANG:
                $state = $botManager->get(ChooseLangState::class);
                break;
            case StateInterface::STATE_WAIT_CHOOSE_GENDER:
                $state = $botManager->get(ChooseGenderState::class);
                break;
            case StateInterface::STATE_WAIT_INPUT_NAME:
                $state = $botManager->get(ChooseNameState::class);
                break;
            case StateInterface::STATE_WAIT_INPUT_KINGDOM_NAME:
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
            StateInterface::STATE_WAIT_CHOOSE_LANG,
            StateInterface::STATE_WAIT_CHOOSE_GENDER,
            StateInterface::STATE_WAIT_INPUT_NAME,
            StateInterface::STATE_WAIT_INPUT_KINGDOM_NAME,
        ];
    }
}
