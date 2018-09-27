<?php

namespace App\States;

use App\Entity\User;
use App\Factory\StateFactory;
use App\Interfaces\StateInterface;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;

class ChooseGenderState extends BaseState
{
    public function getMessage(): void
    {
        $message = $this->botManager->getMessage();
        $chatId = $message->getChat()->getId();

        $text = $this->botManager->getTranslator()->trans(
            \App\Interfaces\TranslatorInterface::TRANSLATOR_MESSAGE_CHOOSE_GENDER,
            [],
            \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_STATE
        );

        $keyboard = new Keyboard(
            [
                $this->botManager->getTranslator()->trans(
                    User::AVAILABLE_GENDER_KING,
                    [],
                    \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                ),
                $this->botManager->getTranslator()->trans(
                    User::AVAILABLE_GENDER_QUEEN,
                    [],
                    \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                ),
            ]
        );

        $keyboard = $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);

        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => $keyboard,
            'parse_mode' => 'Markdown'
        ];

        Request::sendMessage($data);
    }

    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws ORMException
     */
    public function execute(): void
    {
        $message = $this->botManager->getMessage();

        $chosenGender = trim($message->getText(true));
        $genderKing = $this->botManager->getTranslator()->trans(
            User::AVAILABLE_GENDER_KING,
            [],
            \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
        );
        $genderQueen = $this->botManager->getTranslator()->trans(
            User::AVAILABLE_GENDER_QUEEN,
            [],
            \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
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
            $user->setState(StateInterface::STATE_WAIT_KINGDOM_NAME);
            $user->setGender($gender);
            $entityManager->persist($user);
            $entityManager->flush();

            $stateName = $user->getState();
            $state = null;
            /** @var StateFactory $stateFactory */
            $stateFactory = $this->botManager->get(StateFactory::class);
            if ($stateFactory->isAvailable($stateName)) {
                $state = $stateFactory->create(
                    $stateName,
                    $this->botManager
                );
            }

            if (null !== $state) {
                $state->getMessage();
            }
        } else {
            $this->getMessage();
        }
    }
}
