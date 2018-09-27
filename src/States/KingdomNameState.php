<?php

namespace App\States;

use App\Entity\Kingdom;
use App\Entity\StructureType;
use App\Entity\User;
use App\Factory\ScreenFactory;
use App\Interfaces\ScreenInterface;
use App\Interfaces\StructureInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Repository\StructureTypeRepository;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;

class KingdomNameState extends BaseState
{
    protected $kingdomManager;

    public function __construct(BotManager $botManager, KingdomManager $kingdomManager)
    {
        $this->kingdomManager = $kingdomManager;
        parent::__construct($botManager);
    }

    public function getMessage(): void
    {
        $message = $this->botManager->getMessage();
        $user = $this->botManager->getUser();
        $chatId = $message->getChat()->getId();

        $text = $this->botManager->getTranslator()->trans(
            \App\Interfaces\TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING,
            [
                '%gender%' => $this->botManager->getTranslator()->transChoice(
                    \App\Interfaces\TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
                    $user->getGender() === User::AVAILABLE_GENDER_KING ? 1 : 0,
                    [],
                    \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                )
            ],
            \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_STATE
        );

        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => Keyboard::remove(),
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
        $user = $this->botManager->getUser();

        if (!$message instanceof Message || !$user instanceof User) {
            throw new \UnexpectedValueException('message or user empty');
        }

        $kingdomName = trim($message->getText(true));
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
                $screen = $screenFactory->create(ScreenInterface::SCREEN_MAIN_MENU, $this->botManager);
            }

            if (null !== $screen) {
                $screen->execute();
            }
        } else {
            $this->getMessage();
        }
    }
}
