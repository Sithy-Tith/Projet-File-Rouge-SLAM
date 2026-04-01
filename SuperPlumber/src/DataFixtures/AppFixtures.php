<?php

namespace App\DataFixtures;

use App\Entity\Clients;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

    $faker = Factory::create('fr_FR');

        // $product = new Product();
        // $manager->persist($product);
        for ($i = 0; $i < 10; $i++){

            $clients = new Clients();
            $clients->setEmail($faker->email);
            $clients->setAddress($faker->address);
            $clients->setFirstName($faker->firstName);
            $clients->setLastName($faker->lastName);
            $clients->setPhone($faker->phoneNumber);
            $clients->setPassword($faker->);

            $manager->persist($clients);

        }
        $manager->flush();
    }
}
