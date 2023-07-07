<?php

namespace App\Repository;

use App\classe\Search;
use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 *
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function save(Activity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAll():array
    {
        return $this->findBy(array(), array('startingTime' => 'ASC'));
    }

    public function remove(Activity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findWithSearch(Search $search)
    {
        $query = $this
            ->createQueryBuilder('a')
            ->select('a', 'c')
            ->join('a.campus', 'c');


        if(!empty($search->campus)) {
            $query = $query
                ->andWhere('c.id IN (:campus)')
                ->setParameter('campus', $search->campus);
        }

        if (!empty($search->string)) {
            $query = $query
                ->andWhere('a.name LIKE :string')
                ->setParameter('string', "%{$search->string}%");
        }

        if (!empty($search->startDate)) {
            $query = $query
                ->andWhere('a.startingTime >= :startDate')
                ->setParameter('startDate' , $search->startDate);
        }

        if (!empty($search->endDate)) {
            $query = $query
                ->andWhere('a.startingTime <= :endDate')
                ->setParameter('endDate', $search->endDate );
        }

//        if (!empty($search->startDate and $search->endDate)) {
//            $query = $query
//                ->andWhere('a.startingTime between :startDate and :endDate')
//                ->setParameter('startDate' and 'endDate', $search->startDate and $search->endDate );
//        }

        $query = $query
            ->addOrderBy('a.startingTime','ASC');


        return $query->getQuery()->getResult();

    }

//    /**
//     * @return Activity[] Returns an array of Activity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Activity
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
