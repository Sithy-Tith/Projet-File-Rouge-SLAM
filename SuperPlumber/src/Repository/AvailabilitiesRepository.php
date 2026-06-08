<?php

namespace App\Repository;

use App\Entity\Availabilities;
use App\Enum\Position;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Availabilities>
 */
class AvailabilitiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Availabilities::class);
    }

    //    /**
    //     * @return Availabilities[] Returns an array of Availabilities objects
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

    //    public function findOneBySomeField($value): ?Availabilities
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAvailablePlumbers(\DateTime $date, int $durationMinutes): array //Fonction pour filtrer les plombiers dispos
    {
        $dateStart = (clone $date)->setTime(0, 0);
        $dateEnd = (clone $date)->setTime(23, 59);

        $all = $this->createQueryBuilder('a')
            ->leftJoin('a.fkEmployee', 'e')
            ->addSelect('e')
            ->where('a.start >= :dateStart')
            ->andWhere('a.end <= :dateEnd')
            ->andWhere('e.position = :position')
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd)
            ->setParameter('position', Position::PLUMBER->value)
            ->orderBy('a.start', 'ASC')
            ->getQuery()
            ->getResult();

            //Filtre pour trouver les plombiers qui ont assez de temps disponibles
        return array_values(array_filter($all, function (Availabilities $a) use ($durationMinutes) {
            $diff = ($a->getEnd()->getTimestamp() - $a->getStart()->getTimestamp()) / 60;
            return $diff >= $durationMinutes;
        }));
    }
}
