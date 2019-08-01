<?php

namespace App\Repository;

use App\Entity\InitDataFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method InitDataFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method InitDataFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method InitDataFile[]    findAll()
 * @method InitDataFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InitDataFileRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InitDataFile::class);
    }

    // /**
    //  * @return InitDataFile[] Returns an array of InitDataFile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InitDataFile
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
