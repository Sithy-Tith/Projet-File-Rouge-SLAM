<?php

namespace App\Repository;

use App\Entity\Clients;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Clients>
 */
class ClientsRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clients::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Clients) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    // Retourne un array de clients qui ont une colonne contenant la chaîne $term
    public function searchByTerm(?string $term): array
    {

        return $this->createQueryBuilder('c')   // On crée une requête préparée avec l'alias 'c' pour l'entité 'clients'
            ->andWhere('c.email LIKE :term    
                            OR c.lastName LIKE :term
                            OR c.firstName LIKE :term
                            OR c.phone LIKE :term
                            OR c.address LIKE :term')   // Recherche multi-colonne qui sont comme le placeholder 'term'
            ->setParameter('term', '%' . $term . '%')  //  Définition du placeholder comme étant %$term% => qui contient la chaîne $term
            ->getQuery()
            ->getResult()   // Retourne le résultat (array de clients)
        ;
    }





    //    /**
    //     * @return Clients[] Returns an array of Clients objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        
    //    }

    //    public function findOneBySomeField($value): ?Clients
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
