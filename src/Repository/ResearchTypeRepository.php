<?php

namespace App\Repository;

use App\Entity\ResearchType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ResearchType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResearchType|null findOneByCode(string $code, array $orderBy = null)
 * @method ResearchType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResearchType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResearchTypeRepository extends ServiceEntityRepository
{
    /**
     * ResearchTypeRepository constructor.
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ResearchType::class);
    }

    /**
     * {@inheritdoc}
     *
     * @return ResearchType[]
     */
    public function findAll(): array
    {
        return $this->findBy([], ['order' => 'ASC']);
    }

//    /**
//     * @return ResearchType[] Returns an array of ResearchType objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ResearchType
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
