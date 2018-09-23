<?php

namespace App\Repository;

use App\Entity\BuildType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BuildType|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuildType|null findOneByCode(string $code, array $orderBy = null)
 * @method BuildType|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuildType[]    findAll()
 * @method BuildType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuildTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BuildType::class);
    }

//    /**
//     * @return BuildType[] Returns an array of BuildType objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BuildType
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
