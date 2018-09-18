<?php

namespace App\Command;

use App\Entity\Kingdom;
use App\Entity\User;
use App\Interfaces\ResourceInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Manager\PeopleManager;
use App\Repository\UserRepository;
use Longman\TelegramBot\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserNotifyCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'user-notify';
    /** @var UserRepository */
    protected $userRepository;
    /** @var BotManager */
    protected $botManager;
    /** @var PeopleManager */
    protected $peopleManager;
    /** @var KingdomManager */
    protected $kingdomManager;

    /**
     * MainProcessingCommand constructor.
     *
     * @throws
     */
    public function __construct(
        UserRepository $userRepository,
        BotManager $botManager,
        PeopleManager $peopleManager,
        KingdomManager $kingdomManager,
        ?string $name = null
    ) {
        $this->botManager = $botManager;
        $this->kingdomManager = $kingdomManager;
        $this->peopleManager = $peopleManager;
        $this->userRepository = $userRepository;
        parent::__construct($name);
        Request::initialize($botManager);
    }

    /**
     * Configure.
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Add a short description for your command');
    }

    /**
     * @throws
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $now = new \DateTime();
        $qb = $this->userRepository->createQueryBuilder('u');

        $dataProvider = $qb->getQuery()->iterate();

        $batchSize = 100;
        $i = 0;
        while (false !== ($userRow = $dataProvider->next())) {
            $user = $userRow[0] ?? null;
            if ($user instanceof User) {
                $io->writeln("\t{$user->getId()} processing...");

                $this->botManager->init($user);

                $kingdom = $user->getKingdom();
                if ($kingdom instanceof Kingdom) {
                    $people = $this->kingdomManager->getPeople();
                    $eat = $this->peopleManager->eat();
                    $food = $kingdom->getResource(ResourceInterface::RESOURCE_FOOD);

                    Request::sendMessage([
                        'chat_id' => $user->getId(),
                        'text' => <<<TEXT
*Системное сообщение*
Всем спасибо, расходимся - @BastionSiegeBot
TEXT
                        ,
                        'parse_mode' => 'Markdown',
                    ]);
                }

                $io->write("\tDone!");
                $user->setProcessDate($now);
            }

            if (0 === $i % $batchSize) {
                $this->botManager->getEntityManager()->clear();
            }

            ++$i;
        }

        $io->success('Done!');
    }
}
