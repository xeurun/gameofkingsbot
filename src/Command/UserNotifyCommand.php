<?php

namespace App\Command;

use App\Entity\Kingdom;
use App\Entity\User;
use App\Helper\DateTimeHelper;
use App\Interfaces\ResourceInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Manager\PeopleManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        while (($userRow = $dataProvider->next()) !== false) {
            $user = $userRow[0] ?? null;
            if ($user instanceof User) {
                $io->writeln( "\t{$user->getId()} processing...");

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
`Уважаемый игрок!
Сообщаем вам что с текущего момента, каждый час 
будет учитываться потребление еды вашими подданными,
не забывайте забирать ресурсы со склада и следите
чтобы у людей всегда была еда иначе у вас будет умирать
один человек в час, напоминаем вам что их у вас сейчас 
`*{$people}*` и им требуется `*{$eat}*` ед. еды в час, 
у вас на данный момент есть `*{$food}*` ед.`
TEXT
                        ,
                        'parse_mode' => 'Markdown',
                    ]);
                }

                $io->write( "\tDone!");
                $user->setProcessDate($now);
            }

            if ($i % $batchSize === 0) {
                $this->botManager->getEntityManager()->clear();
            }

            ++$i;
        }

        $io->success('Done!');
    }
}