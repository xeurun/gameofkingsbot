<?php

namespace App\States;

use App\Entity\User;
use App\Factory\StateFactory;
use App\Interfaces\StateInterface;
use App\Interfaces\TranslatorInterface;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;

class ChooseGenderState extends BaseState
{
    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function preExecute(): void
    {
        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_CHOOSE_GENDER,
            [],
            TranslatorInterface::TRANSLATOR_DOMAIN_STATE
        );

        $keyboard = new Keyboard(
            [
                $this->botManager->getTranslator()->trans(
                    TranslatorInterface::TRANSLATOR_MESSAGE_MY_KING,
                    [],
                    TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                ),
                $this->botManager->getTranslator()->trans(
                    TranslatorInterface::TRANSLATOR_MESSAGE_MY_QUEEN,
                    [],
                    TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                ),
            ]
        );

        $keyboard = $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);

        $data = [
            'chat_id' => $this->botManager->getUser()->getId(),
            'text' => $text,
            'reply_markup' => $keyboard,
            'parse_mode' => 'Markdown',
        ];

        Request::sendMessage($data);
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws ORMException
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     */
    public function execute(Message $message): void
    {
        $chosenGender = trim($message->getText(true));
        $genderKing = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_MY_KING,
            [],
            TranslatorInterface::TRANSLATOR_DOMAIN_STATE
        );
        $genderQueen = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_MY_QUEEN,
            [],
            TranslatorInterface::TRANSLATOR_DOMAIN_STATE
        );

        $gender = null;
        $user = $this->botManager->getUser();
        switch ($chosenGender) {
            case $genderKing:
                $gender = User::AVAILABLE_GENDER_KING;

                break;
            case $genderQueen:
                $gender = User::AVAILABLE_GENDER_QUEEN;

                break;
            default:
                break;
        }

        if (null !== $gender) {
            $entityManager = $this->botManager->getEntityManager();
            $user->setState(StateInterface::STATE_WAIT_INPUT_NAME);
            $user->setGender($gender);
            $entityManager->persist($user);
            $entityManager->flush();

            $state = $user->getState();
            $stateName = $state[User::STATE_NAME_KEY] ?? null;

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
            $this->preExecute();
        }
    }
}
