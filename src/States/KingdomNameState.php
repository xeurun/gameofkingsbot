<?php

namespace App\States;

use App\Entity\User;
use App\Factory\ScreenFactory;
use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;

class KingdomNameState extends BaseState
{
    /** @var KingdomManager */
    protected $kingdomManager;

    public function __construct(BotManager $botManager, KingdomManager $kingdomManager)
    {
        $this->kingdomManager = $kingdomManager;
        parent::__construct($botManager);
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function preExecute(): void
    {
        $user = $this->botManager->getUser();

        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING,
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

        $data = [
            'chat_id' => $user->getId(),
            'text' => $text,
            'reply_markup' => Keyboard::remove(),
            'parse_mode' => 'Markdown',
        ];

        Request::sendMessage($data);
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(Message $message): void
    {
        $user = $this->botManager->getUser();
        $kingdomName = trim($this->message->getText(true));
        if (!empty($kingdomName)) {
            $entityManager = $this->botManager->getEntityManager();
            $kingdom = $user->getKingdom();
            if (!$kingdom) {
                $kingdom = $this->kingdomManager->createNewKingdom($kingdomName);
                $user->setKingdom($kingdom);
            } else {
                $kingdom->changeName($kingdomName);
            }
            $entityManager->persist($kingdom);
            $user->setState(null);
            $entityManager->persist($user);
            $entityManager->flush();

            $screen = null;
            /** @var ScreenFactory $screenFactory */
            $screenFactory = $this->botManager->get(ScreenFactory::class);
            if ($screenFactory->isAvailable(ScreenInterface::SCREEN_MAIN_MENU)) {
                $screen = $screenFactory->create(ScreenInterface::SCREEN_MAIN_MENU);
            }

            if (null !== $screen) {
                $screen->execute();
            }
        } else {
            $this->preExecute();
        }
    }
}
