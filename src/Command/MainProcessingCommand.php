<?php

namespace App\Command;

use App\Entity\Kingdom;
use App\Entity\User;
use App\Helper\DateTimeHelper;
use App\Interfaces\ResourceInterface;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Repository\UserRepository;
use Longman\TelegramBot\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MainProcessingCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'main-processing';

    /** @var UserRepository */
    protected $userRepository;
    /** @var BotManager */
    protected $botManager;
    /** @var PeopleManager */
    protected $peopleManager;

    /**
     * MainProcessingCommand constructor.
     * @throws
     */
    public function __construct(
        UserRepository $userRepository,
        BotManager $botManager,
        PeopleManager $peopleManager,
        ?string $name = null
    ) {
        $this->botManager = $botManager;
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
        $qb->where(
            $qb->expr()->gte(
                'HOUR(TIMEDIFF(u.processDate, :now))',
                1
            )
        )->setParameter(':now', $now);

        $dataProvider = $qb->getQuery()->iterate();

        $batchSize = 100;
        $i = 0;
        while (($userRow = $dataProvider->next()) !== false) {
            $user = $userRow[0] ?? null;
            if ($user instanceof User) {
                $io->writeln( "\t{$user->getId()} processing...");

                $this->botManager->init($user);
                $processDate = $user->getProcessDate();

                $kingdom = $user->getKingdom();
                if ($kingdom instanceof Kingdom) {
                    $hourDiff = DateTimeHelper::hourBetween($now, $processDate);

                    $eat = $this->peopleManager->eat($hourDiff);
                    $newValue = $kingdom->getResource(ResourceInterface::RESOURCE_FOOD)
                        - $eat;

                    $io->writeln( "\tEat: {$eat}");
                    $kingdom->setResource(ResourceInterface::RESOURCE_FOOD, $newValue);

                    if (
                        $kingdom->getResource(ResourceInterface::RESOURCE_FOOD) < 0 &&
                        $kingdom->getResource(ResourceInterface::RESOURCE_PEOPLE) > ResourceInterface::MIN_ALIVE_PEOPLE
                    ) {
                        $kingdom->setResource(
                            ResourceInterface::RESOURCE_PEOPLE,
                            $kingdom->getResource(ResourceInterface::RESOURCE_PEOPLE) - $hourDiff
                        );

                        Request::sendMessage([
                            'chat_id' => $user->getId(),
                            'text' => '*Советник*: в королевстве умирают люди, срочно нужна еда!',
                            'parse_mode' => 'Markdown',
                        ]);
                    }
                }

                $io->write( "\tDone!");
                $user->setProcessDate($now);
            }

            if ($i % $batchSize === 0) {
                $this->botManager->getEntityManager()->flush();
                $this->botManager->getEntityManager()->clear();
            }

            ++$i;
        }
        $this->botManager->getEntityManager()->flush();

        $io->success('Done!');
    }
}
