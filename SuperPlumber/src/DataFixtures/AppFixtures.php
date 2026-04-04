<?php

namespace App\DataFixtures;

use App\Entity\Pieces;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i<10 ; $i++){
            $piece = new Pieces();
            $piece->setName("Tuyau ".$i."mm");
            $piece->setQuantity(mt_rand(10, 100));
            $piece->setAlertTreshold(mt_rand(3, 10));
            $piece->setSupplier("Fournisseur n°".$i);

            $manager->persist($piece);
        }



        $manager->flush();
    }
}
