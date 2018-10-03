<?php

namespace App\States;

use App\Entity\User;
use App\Factory\ScreenFactory;
use App\Factory\StateFactory;
use App\Interfaces\ScreenInterface;
use App\Interfaces\StateInterface;
use App\Interfaces\TranslatorInterface;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;

class ChooseNameState extends BaseState
{
    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function preExecute(): void
    {
        $user = $this->botManager->getUser();
        $chatId = $this->message->getChat()->getId();

        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_CHOOSE_NAME,
            [
                '%gender%' => $this->botManager->getTranslator()->transChoice(
                    TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
                    User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
                    [],
                    TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                ),
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_STATE
        );

        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => Keyboard::remove(),
            'parse_mode' => 'Markdown',
        ]);
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws ORMException
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     */
    public function execute(Message $message): void
    {
        $user = $this->botManager->getUser();
        $name = trim($message->getText(true));
        if (!empty($name)) {
            $entityManager = $this->botManager->getEntityManager();
            if (null === $user->getKingdom()) {
                $user->setState(StateInterface::STATE_WAIT_INPUT_KINGDOM_NAME);
            } else {
                $user->setState(null);
            }
            $user->setName($name);
            $entityManager->persist($user);
            $entityManager->flush();

            if (null === $user->getKingdom()) {
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
                $screen = null;
                /** @var ScreenFactory $screenFactory */
                $screenFactory = $this->botManager->get(ScreenFactory::class);
                if ($screenFactory->isAvailable(ScreenInterface::SCREEN_MAIN_MENU)) {
                    $screen = $screenFactory->create(ScreenInterface::SCREEN_MAIN_MENU);
                }

                if (null !== $screen) {
                    $screen->execute();
                }
            }
        } else {
            $this->preExecute();
        }
    }
}
