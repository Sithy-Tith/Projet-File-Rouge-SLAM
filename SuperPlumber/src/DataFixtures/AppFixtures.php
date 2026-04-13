<?php

namespace App\DataFixtures;

use App\Entity\Clients;
use App\Entity\Availabilities;
use App\Entity\Employees;
use App\Entity\Interventions;
use App\Entity\Pieces;
use App\Entity\UsedPieces;
use App\Enum\Position;
use App\Enum\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    // Pour le hashage
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $piecesTypes = ['Pipe', 'Elbow', 'Coupling', 'Reducer', 'Valve', 'Gasket', 'Robinet']; //Pieces types pour la plomberie pour test
        $employeesList=[];
        $clientsList=[];
        $interventionsList=[];
        $piecesList=[];
        for ($i = 0; $i < 10; $i++) {

            $client = new Clients();
            $client->setEmail($faker->unique()->email);
            $client->setAddress($faker->address);
            $client->setFirstName($faker->firstName);
            $client->setLastName($faker->lastName);
            $client->setPhone($faker->phoneNumber);

            $passwordClient = $this->hasher->hashPassword($client, 'password123');
            $client->setPassword($passwordClient);
            $clientsList[]=$client;

            $employee = new Employees();
            $employee->setLastName($faker->lastName);
            $employee->setFirstName($faker->firstName);
            $employee->setPhone($faker->phoneNumber);
            $employee->setPosition($faker->randomElement(Position::cases()));
            $employee->setEmail($faker->unique()->email);

            $passwordEmployee = $this->hasher->hashPassword($employee, 'employee123');
            $employee->setPassword($passwordEmployee);
            $employeesList[]=$employee;

            $intervention = new Interventions();
            $intervention->setDate($faker->dateTime);
            $intervention->setDescription($faker->text);
            $intervention->setStatus($faker->randomElement(Status::cases()));
            $intervention->setDuration(mt_rand(1, 7));
            if ($intervention->getStatus()!==Status::TO_PLAN){
                $intervention->setFkEmployee($faker->randomElement($employeesList));
            }
            $intervention->setFkClient($faker->randomElement($clientsList));
            $interventionsList[]=$intervention;


            $piece = new Pieces();
            $piece->setName($faker->randomElement($piecesTypes));
            $piece->setQuantity(mt_rand(0, 100));
            $piece->setAlertTreshold(mt_rand(1, 10));
            $piece->setSupplier($faker->company);
            $piecesList[]=$piece;


            $usedPiece = new UsedPieces();
            $usedPiece->setIsConsumable($faker->boolean());
            $usedPiece->setQuantity(mt_rand(1,8));
            $usedPiece->setFkPiece($faker->randomElement($piecesList));
            $usedPiece->setFkIntervention($faker->randomElement($interventionsList));


            $availability = new Availabilities();
            $availability->setAvailability(mt_rand(0,7));
            $availability->setDate($faker->dateTimeThisYear());
            $availability->setFkEmployee($faker->randomElement($employeesList));


            $manager->persist($client);
            $manager->persist($employee);
            $manager->persist($intervention);
            $manager->persist($piece);
            $manager->persist($usedPiece);
            $manager->persist($availability);
        }

        $manager->flush();
    }
}



