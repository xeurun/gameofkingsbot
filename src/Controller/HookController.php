<?php

namespace App\Controller;

use App\Manager\BotManager;
use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\TelegramLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HookController extends AbstractController
{
    /** @var BotManager */
    protected $botManager;
    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * HookController constructor.
     */
    public function __construct(BotManager $botManager, EntityManagerInterface $entityManager)
    {
        $this->botManager = $botManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/hook")
     */
    public function hook(): Response
    {
        try {
            $this->entityManager->beginTransaction();
            $this->botManager->handle();
            $this->entityManager->commit();
        } catch (TelegramException $ex) {
            // log telegram errors
            TelegramLog::error($ex->getMessage());
            dump($ex);
            $this->entityManager->rollback();
        } catch (\Throwable $ex) {
            // log telegram errors
            TelegramLog::error($ex->getMessage());
            dump($ex);
            $this->entityManager->rollback();
        }

        return new Response();
    }

    /**
     * @Route("/hook/set")
     */
    public function set(): Response
    {
        try {
            $hookUrl = getenv('HOOK_URL');
            $certPath = getenv('CERT_PATH');
            $data = [
                'max_connections' => 100,
                'allowed_updates' => ['message', 'inline_query', 'callback_query'],
            ];

            if (!empty($certPath)) {
                $data['certificate'] = $certPath;
            }

            $result = $this->botManager->setWebhook(
                $hookUrl,
                $data
            );
            if ($result->isOk()) {
                echo $result->getDescription();
            }
        } catch (TelegramException $ex) {
            // log telegram errors
            TelegramLog::error($ex->getMessage());
            dump($ex);
        } catch (\Throwable $ex) {
            // log telegram errors
            TelegramLog::error($ex->getMessage());
            dump($ex);
        }

        return new Response();
    }

    /**
     * @Route("/hook/fake/callback")
     */
    public function fake(Request $request): Response
    {
        try {
            $this->entityManager->beginTransaction();
            $m = $request->get('m', '/start');
            $callbackName = $request->get('c', '{callback_get_info@structure_type_barn}');

            $this->botManager->setCustomInput('');

            $this->botManager->handle();
            $this->entityManager->commit();
        } catch (TelegramException $ex) {
            // log telegram errors
            TelegramLog::error($ex->getMessage());
            dump($ex);
            $this->entityManager->rollback();
        } catch (\Throwable $ex) {
            // log telegram errors
            TelegramLog::error($ex->getMessage());
            dump($ex);
            $this->entityManager->rollback();
        }

        return new Response();
    }

    /**
     * @Route("/hook/fake/command")
     */
    public function fakeCommand(Request $request): Response
    {
        try {
            $this->entityManager->beginTransaction();
            $c = $request->get('с', '/start');

            $this->botManager->setCustomInput('');
            $this->botManager->handle();
            $this->entityManager->commit();
        } catch (TelegramException $ex) {
            // log telegram errors
            TelegramLog::error($ex->getMessage());
            dump($ex);
            $this->entityManager->rollback();
        } catch (\Throwable $ex) {
            // log telegram errors
            TelegramLog::error($ex->getMessage());
            dump($ex);
            $this->entityManager->rollback();
        }

        return new Response();
    }

    /**
     * @Route("/hook/fake/message")
     */
    public function fakeMessage(Request $request): Response
    {
        try {
            $this->entityManager->beginTransaction();
            $m = $request->get('m', '/start');

            $this->botManager->setCustomInput('');
            $this->botManager->handle();
            $this->entityManager->commit();
        } catch (TelegramException $ex) {
            // log telegram errors
            TelegramLog::error($ex->getMessage());
            dump($ex);
            $this->entityManager->rollback();
        } catch (\Throwable $ex) {
            // log telegram errors
            TelegramLog::error($ex->getMessage());
            dump($ex);
            $this->entityManager->rollback();
        }

        return new Response();
    }

    /**
     * @Route("/hook/fake/inline")
     */
    public function inlineMessage(Request $request): Response
    {
        try {
            $this->entityManager->beginTransaction();
            $m = $request->get('m', '/start');

            $this->botManager->setCustomInput('');
            $this->botManager->handle();
            $this->entityManager->commit();
        } catch (TelegramException $ex) {
            // log telegram errors
            TelegramLog::error($ex->getMessage());
            dump($ex);
            $this->entityManager->rollback();
        } catch (\Throwable $ex) {
            // log telegram errors
            TelegramLog::error($ex->getMessage());
            dump($ex);
            $this->entityManager->rollback();
        }

        return new Response();
    }
}
