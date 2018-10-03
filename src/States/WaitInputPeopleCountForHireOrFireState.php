<?php

namespace App\States;

use App\Entity\User;
use App\Helper\CurrencyHelper;
use App\Interfaces\TranslatorInterface;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use App\Screens\Edicts\PeopleScreen;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;

class WaitInputPeopleCountForHireOrFireState extends BaseState
{
    /** @var PeopleScreen */
    protected $peopleScreen;
    /** @var PeopleManager */
    protected $peopleManager;
    /** @var WorkManager */
    protected $workManager;

    /**
     * WaitInputPeopleCountForHireOrFireState constructor.
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
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function preExecute(): void
    {
        $user = $this->botManager->getUser();

        $state = $user->getState();
        $stateData = $state[User::STATE_DATA_KEY] ?? [];

        $workType = $stateData['workType'] ?? null;
        $hire = $stateData['hire'] ?? null;

        $forTypePrefix = ($hire ? TranslatorInterface::TRANSLATOR_WORK_TYPE_PREFIX_TO
            : TranslatorInterface::TRANSLATOR_WORK_TYPE_PREFIX_FROM) . $workType;

        Request::sendMessage([
            'chat_id' => $this->botManager->getUser()->getId(),
            'text' => $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_INPUT_PEOPLE_COUNT_FOR_HIRE_OR_FIRE,
                [
                    '%gender%' => $this->botManager->getTranslator()->transChoice(
                        TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
                        User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                    ),
                    '%type%' => $this->botManager->getTranslator()->transChoice(
                        TranslatorInterface::TRANSLATOR_MESSAGE_HIRE_OR_FIRE,
                        $hire,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                    '%workType%' => $this->botManager->getTranslator()->trans(
                        $forTypePrefix,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                    ),
                ],
                TranslatorInterface::TRANSLATOR_DOMAIN_STATE
            ),
            'parse_mode' => 'Markdown',
        ]);
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(Message $message): void
    {
        $count = trim($message->getText(true));

        if (\is_numeric($count) && (int)$count > 0) {
            $count = (int)$count;

            $kingdom = $this->botManager->getKingdom();
            $user = $this->botManager->getUser();
            $state = $user->getState();
            $stateData = $state[User::STATE_DATA_KEY] ?? [];

            $messageId = $stateData['messageId'] ?? null;
            $workType = $stateData['workType'] ?? null;
            $hire = $stateData['hire'] ?? null;

            $forTypePrefix = ($hire ? TranslatorInterface::TRANSLATOR_WORK_TYPE_PREFIX_TO
                    : TranslatorInterface::TRANSLATOR_WORK_TYPE_PREFIX_FROM) . $workType;

            if ($hire) {
                if ($this->workManager->free() > 0 && $this->workManager->hasFreeSpaceFor($workType, $count)) {
                    $text = $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_HIRED_PEOPLE,
                        [
                            '%gender%' => $this->botManager->getTranslator()->transChoice(
                                TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
                                User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
                                [],
                                TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                            ),
                            '%people%' => $this->botManager->getTranslator()->transChoice(
                                TranslatorInterface::TRANSLATOR_MESSAGE_PEOPLES_RAW,
                                $count,
                                [
                                    'count' => CurrencyHelper::costFormat($count),
                                ],
                                TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                            ),
                            '%workType%' => $this->botManager->getTranslator()->trans(
                                $forTypePrefix,
                                [],
                                TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                            ),
                        ],
                        TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
                    );
                    $kingdom->setWorkerCount(
                        $workType,
                        $kingdom->getWorkerCount($workType) + $count
                    );
                } elseif (0 === $this->workManager->free()) {
                    $text = $this->botManager->getTranslator()->trans(
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
                } elseif ($this->workManager->free() < $count) {
                    $text = $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_NO_COUNT_FOR_HIRE_PEOPLE,
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
                } else {
                    $text = $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_HAS_NOT_FREE_SPACE,
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
                $workerCount = $kingdom->getWorkerCount($workType);
                if ($workerCount >= $count) {
                    $text = $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_FIRED_PEOPLE,
                        [
                            '%gender%' => $this->botManager->getTranslator()->transChoice(
                                TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
                                User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
                                [],
                                TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                            ),
                            '%people%' => $this->botManager->getTranslator()->transChoice(
                                TranslatorInterface::TRANSLATOR_MESSAGE_PEOPLES_RAW,
                                $count,
                                [
                                    'count' => CurrencyHelper::costFormat($count),
                                ],
                                TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                            ),
                            '%workType%' => $this->botManager->getTranslator()->trans(
                                $forTypePrefix,
                                [],
                                TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                            ),
                        ],
                        TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
                    );
                    $kingdom->setWorkerCount(
                        $workType,
                        $kingdom->getWorkerCount($workType) - $count
                    );
                } else {
                    if (0 === $workerCount) {
                        $text = $this->botManager->getTranslator()->trans(
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
                    } else {
                        $text = $this->botManager->getTranslator()->trans(
                            TranslatorInterface::TRANSLATOR_MESSAGE_NO_COUNT_FOR_FIRE_PEOPLE,
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
            }

            $user->setState(null);

            $entityManager = $this->botManager->getEntityManager();
            $entityManager->persist($user);
            $entityManager->persist($kingdom);
            $entityManager->flush();

            if ($messageId) {
                $data = $this->peopleScreen->getMessageData();
                $data['message_id'] = $messageId;
                Request::editMessageText($data);
            }

            Request::sendMessage([
                'chat_id' => $user->getId(),
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);
        } else {
            $this->preExecute();
        }
    }
}
