<?php

namespace App\Repository;

use App\Entity\Files;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Files|null find($id, $lockMode = null, $lockVersion = null)
 * @method Files|null findOneBy(array $criteria, array $orderBy = null)
 * @method Files[]    findAll()
 * @method Files[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Files::class);
    }

    /**
     * @param array $categories
     * @return Files[] Returns an array of Files objects
     */
    public function findByCategories(array $categories)
    {
        $qb = $this->createQueryBuilder('f')
        ->innerJoin('f.categories', 'c')
            ->orderBy('f.upload_date', 'DESC')
        ;
        $qb->orWhere('c.id IN (:categories)')->setParameter('categories', $categories);

        return $qb->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param array $categories
     * @return Query
     */
    public function findByCategoriesQuery(array $categories)
    {
        $qb = $this->createQueryBuilder('f')
        ->innerJoin('f.categories', 'c')
            ->orderBy('f.upload_date', 'DESC')
        ;
        $qb->andWhere('c.id IN (:categories)')->setParameter('categories', $categories);
        return $qb->getQuery();
    }

    /**
     * @param array $categories
     * @param string $search
     * @return Query
     */
    public function findByCategoriesSearchQuery(array $categories, string $search = ''): Query
    {
        $qb = $this->createQueryBuilder('f')
        ->innerJoin('f.categories', 'c')
        ;
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->like('f.name', ':search'),
            $qb->expr()->like('f.mime', ':search'),
            $qb->expr()->like('f.upload_date', ':search')
        ))->setParameter('search', "%{$search}%");
        $qb->andWhere('c.id IN (:categories)')->setParameter('categories', $categories);
        $qb->orderBy('f.upload_date', 'DESC');
        return $qb->getQuery();
    }

    // /**
    //  * @return Files[] Returns an array of Files objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Files
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
