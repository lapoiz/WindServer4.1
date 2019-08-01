<?php

namespace App\Repository;

use App\Entity\WindOrientation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WindOrientation|null find($id, $lockMode = null, $lockVersion = null)
 * @method WindOrientation|null findOneBy(array $criteria, array $orderBy = null)
 * @method WindOrientation[]    findAll()
 * @method WindOrientation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WindOrientationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WindOrientation::class);
    }

    // /**
    //  * @return WindOrientation[] Returns an array of WindOrientation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WindOrientation
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
