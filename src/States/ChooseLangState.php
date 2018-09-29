<?php

namespace App\States;

use App\Entity\Kingdom;
use App\Entity\StructureType;
use App\Entity\User;
use App\Factory\ScreenFactory;
use App\Factory\StateFactory;
use App\Interfaces\ScreenInterface;
use App\Interfaces\StateInterface;
use App\Interfaces\StructureInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Repository\StructureTypeRepository;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;

class ChooseLangState extends BaseState
{
    public function preExecute(): void
    {
        $message = $this->botManager->getMessage();
        $chatId = $message->getChat()->getId();

        $text = $this->botManager->getTranslator()->trans(
            \App\Interfaces\TranslatorInterface::TRANSLATOR_MESSAGE_CHOOSE_LANG,
            [],
            \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_STATE
        );

        $keyboard = new Keyboard(
            [
                $this->botManager->getTranslator()->trans(
                    User::AVAILABLE_LANG_RU,
                    [],
                    \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                )
            ]
        );

        $keyboard = $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);

        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => $keyboard,
            'parse_mode' => 'Markdown'
        ]);
    }

    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws ORMException
     */
    public function execute(): void
    {
        $message = $this->botManager->getMessage();

        $chosenLang = trim($message->getText(true));
        $langRu = $this->botManager->getTranslator()->trans(
            User::AVAILABLE_LANG_RU,
            [],
            \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
        );

        $lang = null;
        $user = $this->botManager->getUser();
        switch ($chosenLang) {
            case $langRu:
                $lang = User::AVAILABLE_LANG_RU;
                break;
            default:
                break;
        }

        if (null !== $lang) {
            $entityManager = $this->botManager->getEntityManager();
            $user->setState(StateInterface::STATE_WAIT_CHOOSE_GENDER);
            $user->setLang($lang);
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
                $state->preExecute();
            }
        } else {
            $this->preExecute();
        }
    }
}
