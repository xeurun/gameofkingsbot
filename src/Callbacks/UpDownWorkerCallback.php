<?php

namespace App\Callbacks;

use App\Factory\CallbackFactory;
use App\Interfaces\CallbackInterface;
use App\Interfaces\TaxesInterface;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use App\Screens\PeopleScreen;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class UpDownWorkerCallback extends BaseCallback
{
    protected $callbackFactory;
    protected $peopleScreen;
    protected $peopleManager;
    protected $workManager;

    public function __construct(
        BotManager $botManager,
        PeopleManager $peopleManager,
        WorkManager $workManager,
        PeopleScreen $peopleScreen,
        CallbackFactory $callbackFactory
    ) {
        $this->callbackFactory = $callbackFactory;
        $this->peopleManager = $peopleManager;
        $this->workManager = $workManager;
        $this->peopleScreen = $peopleScreen;
        parent::__construct($botManager);
    }

    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws ORMException
     */
    public function execute(): ServerResponse
    {
        $callback = $this->botManager->getCallbackQuery();
        $user = $this->botManager->getUser();

        $entityManager = $this->botManager->getEntityManager();
        $kingdom = $user->getKingdom();
        if ($kingdom) {
            $callbackData = $this->callbackFactory->getData($callback);
            $getter = 'getOn' . ucfirst($callbackData['t']);
            $setter = 'setOn' . ucfirst($callbackData['t']);
            if ($callbackData['v'] === '+') {
                if ($this->workManager->free($kingdom) > 0) {
                    $type = 'Добавлен рабочий';
                    $kingdom->$setter($kingdom->$getter() + 1);
                } else {
                    $type = 'Нет свободных рабочих';
                }
            } else {
                if ($kingdom->$getter() > 0) {
                    $type = 'Убран рабочий';
                    $kingdom->$setter($kingdom->$getter() - 1);
                } else {
                    $type = 'Некого убирать';
                }
            }
            $entityManager->persist($kingdom);
            $entityManager->flush();
        }

        if ($callback->getMessage()) {
            $data = $this->peopleScreen->getMessageData();
            $data['message_id'] = $callback->getMessage()->getMessageId();
            Request::editMessageText($data);
        }

        $text = <<<TEXT
{$type}
TEXT;
        $data = [
            'callback_query_id' => $this->botManager->getCallbackQuery()->getId(),
            'text'              => $text,
            'show_alert'        => false,
        ];

        return Request::answerCallbackQuery($data);
    }
}
