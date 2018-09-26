<?php

namespace App\Manager;

use App\Commands\System\CallbackqueryCommand;
use App\Commands\System\GenericmessageCommand;
use App\Commands\System\InlinequeryCommand;
use App\Commands\System\StartCommand;
use App\Commands\User\HelpCommand;
use App\Entity\Kingdom;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Entities\InlineQuery;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Exception\TelegramLogException;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class BotManager extends Telegram
{
    /** @var ContainerInterface */
    protected $container;
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var TranslatorInterface */
    protected $translator;
    /** @var User */
    protected $user;
    /** @var Message */
    protected $message;
    /** @var CallbackQuery */
    protected $callbackQuery;
    /** @var InlineQuery */
    protected $inlineQuery;

    /**
     * @param ContainerInterface $container
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @throws TelegramException
     * @throws TelegramLogException
     */
    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ) {
        $this->container = $container;
        $this->translator = $translator;
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

        class_alias(
            InlinequeryCommand::class,
            \Longman\TelegramBot\Commands\SystemCommands\InlinequeryCommand::class
        );

        class_alias(
            HelpCommand::class,
            \Longman\TelegramBot\Commands\UserCommands\HelpCommand::class
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
     * @return TranslatorInterface
     */
    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
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

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $value
     * @return self
     */
    public function setUser(?User $value): self
    {
        $this->user = $value;
        return $this;
    }

    /**
     * @return Message|null
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }

    /**
     * @param Message|null $value
     * @return self
     */
    public function setMessage(?Message $value): self
    {
        $this->message = $value;
        return $this;
    }

    /**
     * @return CallbackQuery|null
     */
    public function getCallbackQuery(): ?CallbackQuery
    {
        return $this->callbackQuery;
    }

    /**
     * @param CallbackQuery|null $callbackQuery
     * @return self
     */
    public function setCallbackQuery(?CallbackQuery $callbackQuery): self
    {
        $this->callbackQuery = $callbackQuery;
        return $this;
    }

    /**
     * @return InlineQuery|null
     */
    public function getInlineQuery(): ?InlineQuery
    {
        return $this->inlineQuery;
    }

    /**
     * @param InlineQuery|null $inlineQuery
     * @return self
     */
    public function setInlineQuery(?InlineQuery $inlineQuery): self
    {
        $this->inlineQuery = $inlineQuery;
        return $this;
    }
}
