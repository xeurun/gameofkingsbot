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
     * @param BotManager $botManager
     * @param EntityManagerInterface $entityManager
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
                'allowed_updates' => ['message', 'inline_query', 'callback_query']
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
            $callbackName = $request->get('c', '{callback_get_info@structure_type_barn');

            $this->botManager->setCustomInput('{
                "update_id":119409613, 
                "callback_query":{
                    "id":"820339762695174636",
                    "from":{
                        "id":191000234,
                        "is_bot":false,
                        "first_name":"Alexey",
                        "last_name":"Stepankov",
                        "username":"alexeystepankov",
                        "language_code":"en-US"
                    },
                    "message":{
                        "message_id":676,
                        "from":{
                            "id":642701144,
                            "is_bot":true,
                            "first_name":"Game of Kings",
                            "username":"gameofkingsbot"
                        },
                        "chat":{
                            "id":191000234,
                            "first_name":"Alexey",
                            "last_name":"Stepankov",
                            "username":"alexeystepankov",
                            "type":"private"
                        },
                        "date":1537485979,
                        "text":"' . $m . '",
                        "entities":[
                            {"offset":0,"length":9,"type":"bold"}
                        ]
                    },
                    "chat_instance":"-1983157652211501556",
                    "data":"' . $callbackName . '"
                }
            }');

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

            $this->botManager->setCustomInput('{
                "update_id":119409820, 
                "message":{
                    "message_id":972,
                    "from": {
                        "id":191000234,
                        "is_bot":false,
                        "first_name":"Alexey",
                        "last_name":"Stepankov",
                        "username":"alexeystepankov",
                        "language_code":"en-US"
                    },
                    "chat": {
                        "id":191000234,
                        "first_name":"Alexey",
                        "last_name":"Stepankov",
                        "username":"alexeystepankov",
                        "type":"private"
                    },
                    "date":1537514492,
                    "text":"' . $c . '",
                    "entities":[
                        {
                            "offset":0,
                            "length":6,
                            "type":"' . $c . '"
                        }
                    ]
                }
            }');
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

            $this->botManager->setCustomInput('{
                "update_id":119409284, 
                "message": {
                    "message_id":84,
                    "from":{
                        "id":191000234,
                        "is_bot":false,
                        "first_name":"Alexey",
                        "last_name":"Stepankov",
                        "username":"alexeystepankov",
                        "language_code":"en-US"
                    },
                    "chat":{
                        "id":191000234,
                        "first_name":"Alexey",
                        "last_name":"Stepankov",
                        "username":"alexeystepankov",
                        "type":"private"
                    },
                    "date":1537300010,
                    "text":"' . $m . '"
                }
            }');
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

            $this->botManager->setCustomInput('{
                "update_id":284751997, 
                "inline_query":{
                    "id":"820339762720782670",
                    "from":{
                        "id":191000234,
                        "is_bot":false,
                        "first_name":"Alexey",
                        "last_name":"Stepankov",
                        "username":"alexeystepankov",
                        "language_code":"en-US"
                    },
                    "query":"' . $m . '","offset":""
                }
            }');
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
