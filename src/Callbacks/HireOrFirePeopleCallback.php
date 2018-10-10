<?php

namespace App\Callbacks;

use App\Entity\User;
use App\Factory\CallbackFactory;
use App\Factory\StateFactory;
use App\Interfaces\StateInterface;
use App\Interfaces\TranslatorInterface;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use App\Screens\Edicts\PeopleScreen;
use App\States\WaitInputPeopleCountForHireOrFireState;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class HireOrFirePeopleCallback extends BaseCallback
{
    /** @var PeopleScreen */
    protected $peopleScreen;
    /** @var PeopleManager */
    protected $peopleManager;
    /** @var WorkManager */
    protected $workManager;
    /** @var array */
    protected $callbackData;
    /** @var WaitInputPeopleCountForHireOrFireState */
    protected $waitInputPeopleCountForHireOrFireState;

    /**
     * HireOrFirePeopleCallback constructor.
     */
    public function __construct(
        BotManager $botManager,
        PeopleManager $peopleManager,
        WorkManager $workManager,
        WaitInputPeopleCountForHireOrFireState $waitInputPeopleCountForHireOrFireState,
        PeopleScreen $peopleScreen
    ) {
        $this->peopleManager = $peopleManager;
        $this->workManager = $workManager;
        $this->peopleScreen = $peopleScreen;
        $this->waitInputPeopleCountForHireOrFireState = $waitInputPeopleCountForHireOrFireState;

        parent::__construct($botManager);

        $this->callbackData = CallbackFactory::getData($this->callbackQuery);
    }

    /**
     * @throws TelegramException
     * @throws ORMException
     */
    public function execute(): ServerResponse
    {
        $user = $this->botManager->getUser();
        $stateName = StateInterface::STATE_WAIT_INPUT_PEOPLE_COUNT_FOR_HIRE_OR_FIRE;

        $response = Request::answerCallbackQuery([
            'callback_query_id' => $this->callbackQuery->getId(),
            'text' => 'Ваше слово - закон!',
            'show_alert' => false,
        ]);

        $kingdom = $this->botManager->getKingdom();

        if (
            $this->waitInputPeopleCountForHireOrFireState->checkReq(
                $user,
                $kingdom,
                $this->callbackData[1],
                1,
                $this->callbackData[2])
        ) {
            $this->hireOrFirePeople($stateName, $user);

            $stateStrategy = null;
            /** @var StateFactory $stateFactory */
            $stateFactory = $this->botManager->get(StateFactory::class);
            if ($stateFactory->isAvailable($stateName)) {
                $stateStrategy = $stateFactory->create($stateName);
            }

            if (null !== $stateStrategy) {
                $stateStrategy->preExecute();
            }
        }

        return $response;
    }

    /**
     * @throws TelegramException
     */
    public function hireOrFirePeople(string $stateName, User $user): void
    {
        $user->setState(
            $stateName,
            [
                'messageId' => $this->message->getMessageId(),
                'workType' => $this->callbackData[1] ?? null,
                'hire' => $this->callbackData[2] ?? null,
            ]
        );

        $entityManager = $this->botManager->getEntityManager();
        $entityManager->persist($user);
        $entityManager->flush();
    }
}
