<?php

namespace App\Factory;

use App\Interfaces\StateInterface;
use App\Manager\BotManager;
use App\States\BaseState;
use App\States\ChooseLangState;
use App\States\ChooseGenderState;
use App\States\ChooseNameState;
use App\States\KingdomNameState;
use App\States\WaitInputPeopleCountForHireOrFireState;
use App\States\WaitInputStructureCountForBuyState;
use Psr\Log\InvalidArgumentException;

class StateFactory
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
    public function create(string $stateName): BaseState
    {
        switch ($stateName) {
            case StateInterface::STATE_WAIT_CHOOSE_LANG:
                $state = $this->botManager->get(ChooseLangState::class);

                break;
            case StateInterface::STATE_WAIT_CHOOSE_GENDER:
                $state = $this->botManager->get(ChooseGenderState::class);

                break;
            case StateInterface::STATE_WAIT_INPUT_NAME:
                $state = $this->botManager->get(ChooseNameState::class);

                break;
            case StateInterface::STATE_WAIT_INPUT_KINGDOM_NAME:
                $state = $this->botManager->get(KingdomNameState::class);

                break;
            case StateInterface::STATE_WAIT_INPUT_PEOPLE_COUNT_FOR_HIRE_OR_FIRE:
                $state = $this->botManager->get(WaitInputPeopleCountForHireOrFireState::class);

                break;
            case StateInterface::STATE_WAIT_INPUT_STRUCTURE_COUNT_FOR_BUY:
                $state = $this->botManager->get(WaitInputStructureCountForBuyState::class);

                break;
            default:
                throw new InvalidArgumentException('Incorrect state name: ' . $stateName);
        }

        return $state;
    }

    /**
     * Check type is available.
     */
    public function isAvailable(string $stateName): bool
    {
        return \in_array($stateName, $this->getAvailable(), true);
    }

    /**
     * Get available type.
     */
    protected function getAvailable(): array
    {
        return [
            StateInterface::STATE_WAIT_CHOOSE_LANG,
            StateInterface::STATE_WAIT_CHOOSE_GENDER,
            StateInterface::STATE_WAIT_INPUT_NAME,
            StateInterface::STATE_WAIT_INPUT_KINGDOM_NAME,
            StateInterface::STATE_WAIT_INPUT_PEOPLE_COUNT_FOR_HIRE_OR_FIRE,
            StateInterface::STATE_WAIT_INPUT_STRUCTURE_COUNT_FOR_BUY,
        ];
    }
}
