<?php

namespace App\Callbacks;

use App\Factory\CallbackFactory;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use App\Screens\Edicts\PeopleScreen;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Symfony\Component\Translation\TranslatorInterface;

class HireOrFirePeopleCallback extends BaseCallback
{
    /** @var PeopleScreen */
    protected $peopleScreen;
    /** @var PeopleManager */
    protected $peopleManager;
    /** @var WorkManager */
    protected $workManager;

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
     * @return ServerResponse
     * @throws TelegramException
     * @throws ORMException
     */
    public function execute(): ServerResponse
    {
        $data = $this->hireOrFirePeople();
        return Request::answerCallbackQuery($data);
    }

    /**
     * @return array
     * @throws TelegramException
     */
    public function hireOrFirePeople(): array
    {
        $kingdom = $this->botManager->getKingdom();

        $callbackData = CallbackFactory::getData($this->callbackQuery);
        $workType = $callbackData[1];
        if ($callbackData[2] === '1') {
            if ($this->workManager->free() > 0 && $this->workManager->checkLimit($workType)) {
                $text = $this->botManager->getTranslator()->trans(
                    \App\Interfaces\TranslatorInterface::TRANSLATOR_MESSAGE_HIRED_PEOPLE,
                    [],
                    \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
                );
                $this->workManager->hire($kingdom, $workType);
            } else {
                $text = $this->botManager->getTranslator()->trans(
                    \App\Interfaces\TranslatorInterface::TRANSLATOR_MESSAGE_NO_HIRED_PEOPLE,
                    [],
                    \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
                );
            }
        } else {
            $workerCount = $this->workManager->workerCount($kingdom, $workType);
            if ($workerCount > 0) {
                $text = $this->botManager->getTranslator()->trans(
                    \App\Interfaces\TranslatorInterface::TRANSLATOR_MESSAGE_FIRED_PEOPLE,
                    [],
                    \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
                );
                $this->workManager->fire($kingdom, $workType);
            } else {
                $text = $this->botManager->getTranslator()->trans(
                    \App\Interfaces\TranslatorInterface::TRANSLATOR_MESSAGE_NO_FIRED_PEOPLE,
                    [],
                    \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
                );
            }
        }

        $entityManager = $this->botManager->getEntityManager();
        $entityManager->persist($kingdom);
        $entityManager->flush();

        $message = $this->callbackQuery->getMessage();
        if ($message) {
            $data = $this->peopleScreen->getMessageData();
            $data['message_id'] = $message->getMessageId();
            Request::editMessageText($data);
        }

        return [
            'callback_query_id' => $this->callbackQuery->getId(),
            'text' => $text,
            'show_alert' => false,
        ];
    }
}
