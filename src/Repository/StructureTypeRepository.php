<?php

namespace App\Repository;

use App\Entity\StructureType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StructureType|null find($id, $lockMode = null, $lockVersion = null)
 * @method StructureType|null findOneByCode(string $code, array $orderBy = null)
 * @method StructureType|null findOneBy(array $criteria, array $orderBy = null)
 * @method StructureType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StructureTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StructureType::class);
    }

    /**
     * {@inheritdoc}
     *
     * @return StructureType[]
     */
    public function findAll(): array
    {
        return $this->findBy([], ['order' => 'ASC']);
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
