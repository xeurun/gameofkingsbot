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

    /**
     * HireOrFirePeopleCallback constructor.
     */
    public function __construct(
        BotManager $botManager,
        PeopleManager $peopleManager,
        WorkManager $workManager,
        PeopleScreen $peopleScreen
    ) {
        $this->peopleManager = $peopleManager;
        $this->workManager = $workManager;
        $this->peopleScreen = $peopleScreen;

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

        $error = null;
        if ($this->callbackData[2]) {
            if (0 === $this->workManager->free()) {
                $error = $this->botManager->getTranslator()->trans(
                    TranslatorInterface::TRANSLATOR_MESSAGE_NO_HIRED_PEOPLE,
                    [
                        '%gender%' => $this->botManager->getTranslator()->transChoice(
                            TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
                            User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
                            [],
                            TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                        ),
                    ],
                    TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
                );
            }
        } else {
            $kingdom = $this->botManager->getKingdom();
            $workerCount = $kingdom->getWorkerCount($this->callbackData[1]);
            if (0 === $workerCount) {
                $error = $this->botManager->getTranslator()->trans(
                    TranslatorInterface::TRANSLATOR_MESSAGE_NO_FIRED_PEOPLE,
                    [
                        '%gender%' => $this->botManager->getTranslator()->transChoice(
                            TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
                            User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
                            [],
                            TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                        ),
                    ],
                    TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
                );
            }
        }

        if (null === $error) {
            $this->hireOrFirePeople($stateName, $user);
            $response = Request::answerCallbackQuery([
                'callback_query_id' => $this->callbackQuery->getId(),
                'text' => 'Ваше слово - закон!',
                'show_alert' => false,
            ]);

            $stateStrategy = null;
            /** @var StateFactory $stateFactory */
            $stateFactory = $this->botManager->get(StateFactory::class);
            if ($stateFactory->isAvailable($stateName)) {
                $stateStrategy = $stateFactory->create($stateName);
            }

            if (null !== $stateStrategy) {
                $stateStrategy->preExecute();
            }
        } else {
            $response = Request::answerCallbackQuery([
                'callback_query_id' => $this->callbackQuery->getId(),
                'text' => $error,
                'show_alert' => false,
            ]);
        }

        return $response;
    }

    /**
     * @throws TelegramException
     */
    public function hireOrFirePeople(string $stateName, User $user)
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
