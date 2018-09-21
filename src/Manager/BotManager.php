<?php

namespace App\Manager;

use App\Commands\System\CallbackqueryCommand;
use App\Commands\System\GenericmessageCommand;
use App\Commands\System\StartCommand;
use App\Entity\Kingdom;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Exception\TelegramLogException;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\TelegramLog;
use Psr\Container\ContainerInterface;

class BotManager extends Telegram
{
    /** @var ContainerInterface */
    protected $container;
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var User */
    protected $user;
    /** @var Message */
    protected $message;
    /** @var CallbackQuery */
    protected $callbackQuery;

    /**
     * @param ContainerInterface $container
     * @throws TelegramException
     * @throws TelegramLogException
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;

        $apiKey = getenv('API_KEY');
        $botUsername = getenv('BOT_USERNAME');

        class_alias(
            StartCommand::class,
            \Longman\TelegramBot\Commands\SystemCommands\StartCommand::class
        );

        class_alias(
            GenericmessageCommand::class,
            \Longman\TelegramBot\Commands\SystemCommands\GenericmessageCommand::class
        );

        class_alias(
            CallbackqueryCommand::class,
            \Longman\TelegramBot\Commands\SystemCommands\CallbackqueryCommand::class
        );

        TelegramLog::initErrorLog('php://stderr');
        TelegramLog::initDebugLog('php://stdout');
        TelegramLog::initUpdateLog('php://stdout');

        parent::__construct($apiKey, $botUsername);
    }

    /**
     * @return void
     * @throws TelegramException
     */
    public function handle(): void
    {
        $adminId = (int)getenv('ADMIN_ID');

        $this->enableAdmins([$adminId]);
        //$this->app->enableLimiter();
        $this->addCommandsPaths([
            __DIR__ . '/../Commands/System',
            __DIR__ . '/../Commands/User'
        ]);

        // Handle telegram webhook request
        parent::handle();
    }

    /**
     * @param string $class
     * @return mixed
     */
    public function get($class)
    {
        return $this->container->get($class);
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $value
     * @return self
     */
    public function setUser(User $value): self
    {
        $this->user = $value;
        return $this;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    /**
     * @param Message $value
     * @return self
     */
    public function setMessage(Message $value): self
    {
        $this->message = $value;
        return $this;
    }

    /**
     * @return CallbackQuery
     */
    public function getCallbackQuery(): CallbackQuery
    {
        return $this->callbackQuery;
    }

    /**
     * @param CallbackQuery $callbackQuery
     * @return self
     */
    public function setCallbackQuery(CallbackQuery $callbackQuery): self
    {
        $this->callbackQuery = $callbackQuery;
        return $this;
    }

    /**
     * @return Kingdom
     */
    public function getKingdom(): Kingdom
    {
        if ($this->getUser()) {
            return $this->getUser()->getKingdom();
        }

        throw new \UnexpectedValueException('Not initialized kingdom');
    }
}
