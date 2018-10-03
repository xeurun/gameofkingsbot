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
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Exception\TelegramLogException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class BotManager extends Telegram
{
    /** @var string */
    protected const UPDATE_TYPE_MESSAGE = 'message';
    /** @var string */
    protected const UPDATE_TYPE_INLINE_QUERY = 'inline_query';
    /** @var string */
    protected const UPDATE_TYPE_CALLBCK_QUERY = 'callback_query';

    /** @var ContainerInterface */
    protected $container;
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var TranslatorInterface */
    protected $translator;
    /** @var User */
    protected $user;
    /** @var Message|null */
    protected $message;
    /** @var CallbackQuery|null */
    protected $callbackQuery;
    /** @var InlineQuery|null */
    protected $inlineQuery;

    /**
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
     * @throws TelegramException
     */
    public function handle(): void
    {
        $adminId = (int)getenv('ADMIN_ID');

        $this->enableAdmins([$adminId]);
        //$this->enableLimiter();
        $this->addCommandsPaths([
            __DIR__ . '/../Commands/System',
            __DIR__ . '/../Commands/User',
        ]);

        // Handle telegram webhook request
        parent::handle();
    }

    /**
     * {@inheritdoc}
     */
    public function processUpdate(Update $update)
    {
        $from = null;
        $updateType = $update->getUpdateType();

        if (self::UPDATE_TYPE_CALLBCK_QUERY === $updateType) {
            $this->setCallbackQuery($update->getCallbackQuery());
            $from = $update->getCallbackQuery()->getFrom();
        } else {
            if (self::UPDATE_TYPE_MESSAGE === $updateType) {
                $this->setMessage($update->getMessage());
                $from = $update->getMessage()->getFrom();
            } elseif (self::UPDATE_TYPE_INLINE_QUERY === $updateType) {
                $this->setInlineQuery($update->getInlineQuery());
                $from = $update->getInlineQuery()->getFrom();
            }
        }

        $response = Request::emptyResponse();

        if ($from) {
            $userRepository = $this->entityManager->getRepository(User::class);

            $fromUser = $userRepository->find($from->getId());
            if (!$fromUser) {
                $fromUser = new User($from);
            }

            if (null !== $this->getMessage()) {
                // Save if this a command
                $this->entityManager->persist($fromUser);
                $this->entityManager->flush();
            }

            $this->user = $fromUser;

            $response = parent::processUpdate($update);
        }

        return $response;
    }

    public function getCallbackQuery(): ?CallbackQuery
    {
        return $this->callbackQuery;
    }

    public function setCallbackQuery(?CallbackQuery $callbackQuery): self
    {
        $this->callbackQuery = $callbackQuery;

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $value): self
    {
        $this->message = $value;

        return $this;
    }

    public function getInlineQuery(): ?InlineQuery
    {
        return $this->inlineQuery;
    }

    public function setInlineQuery(?InlineQuery $inlineQuery): self
    {
        $this->inlineQuery = $inlineQuery;

        return $this;
    }

    /**
     * @param string $class
     */
    public function get($class)
    {
        return $this->container->get($class);
    }

    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function getKingdom(): Kingdom
    {
        if ($this->getUser()) {
            return $this->getUser()->getKingdom();
        }

        throw new \UnexpectedValueException('Not initialized kingdom');
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
