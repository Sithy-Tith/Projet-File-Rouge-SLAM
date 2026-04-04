<?php

namespace App\DataFixtures;

use App\Entity\Availabilities;
use App\Entity\Clients ;
use App\Entity\Employees;
use App\Entity\Interventions;
use App\Entity\Pieces;
use App\Entity\UsedPieces;
use App\Enum\Position;
use App\Enum\Status;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        //Employees
        $employees=[];
        for ($i=0; $i<10; $i++){
            $employee = new Employees();
            $employee->setEmail($faker->email());
            $employee->setPassword($faker->password());
            $employee->setLastName($faker->lastName());
            $employee->setFirstName($faker->firstName());
            $employee->setPhone($faker->phoneNumber());
            //Attribue une position aléatoire parmi celles présentes dans l'Enum Positions
            $employee->setPosition($faker->randomElement(Position::cases()));
            //On stocke les employees créés poir les utiliser dans Availability et Intervention comme FK
            $employees[] = $employee;
            $manager->persist($employee);
        }

        // Availabilities
        for ($i=0; $i<10; $i++){
            $availability = new Availabilities();
            $availability->setAvailability(mt_rand(0,8));
            $availability->setDate($faker->dateTimeThisYear());
            $availability->setFkEmployee($faker->randomElement($employees)); //fkEmployee doit être un objet Employees entier, pas juste un int

            $manager->persist($availability);
        }

        // Clients
        $clients = []; //On stocke les clients pour les attribuer en tant que FK
        for ($i=0; $i<10 ; $i++){
            $client = new Clients();
            $client->setEmail($faker->email());
            $client->setPassword($faker->password());
            $client->setLastName($faker->lastName());
            $client->setFirstName($faker->firstName());
            $client->setPhone($faker->phoneNumber());
            $client->setAddress($faker->address());

            $clients[] = $client;
            $manager->persist($client);
        }

        //Interventions
        $interventions=[];
        for ($i=0 ; $i<10;$i++){
            $intervention = new Interventions();
            $intervention->setDate($faker->dateTimeThisYear());
            $intervention->setDescription($faker->paragraph());
            //Attribue un statut au hasard parmi ceux existants dans l'enum Status
            $intervention->setStatus($faker->randomElement(Status::cases())) ;
            $intervention->setDuration(mt_rand(1,5));
            if ($intervention->getStatus()!==Status::TO_PLAN){
                $intervention->setFkEmployee($faker->randomElement($employees));
            }
            $intervention->setFkClient($faker->randomElement($clients));

            $interventions[] = $intervention;
            $manager->persist($intervention);
        }


        //Pieces
        //On stocke les pieces créées pour les réutiliser en tant que FK
        $pieces=[];
        for ($i = 0; $i<10 ; $i++){
            $piece = new Pieces();
            $piece->setName($faker->word());
            $piece->setQuantity(mt_rand(10, 100));
            $piece->setAlertTreshold(mt_rand(3, 10));
            $piece->setSupplier("Fournisseur n°".$i);

            $pieces[]=$piece;
            $manager->persist($piece);
        }

        $manager->flush();
    }
}
