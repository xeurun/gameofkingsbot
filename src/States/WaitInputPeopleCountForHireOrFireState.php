<?php

namespace App\States;

use App\Entity\Kingdom;
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

        $kingdom = $this->botManager->getKingdom();

        $forTypePrefix = ($hire ? TranslatorInterface::TRANSLATOR_WORK_TYPE_PREFIX_TO
            : TranslatorInterface::TRANSLATOR_WORK_TYPE_PREFIX_FROM) . $workType;

        if ($this->checkReq($user, $kingdom, $workType, 1, $hire)) {
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
                        '%max%' => $hire
                            ? $this->workManager->maxHireSpace($workType)
                            : $kingdom->getWorkerCount($workType),
                        '%free%' => $this->workManager->free()
                    ],
                    TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                ),
                'parse_mode' => 'Markdown',
            ]);
        }
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(Message $message): void
    {
        $user = $this->botManager->getUser();
        $count = trim($message->getText(true));

        $entityManager = $this->botManager->getEntityManager();
        if (\is_numeric($count) && (int)$count > 0) {
            $count = (int)$count;

            $kingdom = $this->botManager->getKingdom();
            $state = $user->getState();
            $stateData = $state[User::STATE_DATA_KEY] ?? [];

            $messageId = $stateData['messageId'] ?? null;
            $workType = $stateData['workType'] ?? null;
            $hire = $stateData['hire'] ?? null;

            $forTypePrefix = ($hire ? TranslatorInterface::TRANSLATOR_WORK_TYPE_PREFIX_TO
                    : TranslatorInterface::TRANSLATOR_WORK_TYPE_PREFIX_FROM) . $workType;

            if ($this->checkReq($user, $kingdom, $workType, $count, $hire)) {
                if ($hire) {
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
                } else {
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
                }

                $entityManager->persist($kingdom);

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

                $user->setState(null);
                $entityManager->persist($user);
                $entityManager->flush();
            }
        } elseif (\is_numeric($count) && 0 === (int)$count) {
            $user->setState(null);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->peopleScreen->execute();
        } else {
            $this->preExecute();
        }
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function checkReq(User $user, Kingdom $kingdom, string $workType, int $count, bool $isHire): bool
    {
        $result = false;
        if ($isHire) {
            if (0 === $this->workManager->free()) {
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
            } else if (!$this->workManager->hasFreeSpaceFor($workType, $count)) {
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
            } else {
                $result = true;
            }
        } else {
            $workerCount = $kingdom->getWorkerCount($workType);
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
            } else if ($workerCount < $count) {
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
            } else {
                $result = true;
            }
        }

        if (!$result) {
            Request::sendMessage([
                'chat_id' => $user->getId(),
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);
        }

        return $result;
    }
}
