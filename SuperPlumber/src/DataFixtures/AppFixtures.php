<?php

namespace App\DataFixtures;

use App\Entity\Clients;
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

        for ($i = 0; $i < 10; $i++) {

            $clients = new Clients();
            $clients->setEmail($faker->unique()->email);
            $clients->setAddress($faker->address);
            $clients->setFirstName($faker->firstName);
            $clients->setLastName($faker->lastName);
            $clients->setPhone($faker->phoneNumber);

            $passwordClient = $this->hasher->hashPassword($clients, 'password123');
            $clients->setPassword($passwordClient);

            $employees = new Employees();
            $employees->setLastName($faker->lastName);
            $employees->setFirstName($faker->firstName);
            $employees->setPhone($faker->phoneNumber);
            $employees->setPosition($faker->randomElement(Position::cases()));
            $employees->setEmail($faker->unique()->email);

            $passwordEmployee = $this->hasher->hashPassword($employees, 'employee123');
            $employees->setPassword($passwordEmployee);

            $interventions = new Interventions();
            $interventions->setDate($faker->dateTime);
            $interventions->setDescription($faker->text);
            $interventions->setStatus($faker->randomElement(Status::cases()));
            $interventions->setDuration(mt_rand(1, 7));

            $pieces = new Pieces();
            $pieces->setName($faker->randomElement($piecesTypes));
            $pieces->setQuantity(mt_rand(0, 100));
            $pieces->setAlertTreshold(mt_rand(1, 10));
            $pieces->setSupplier($faker->company);

            $usedPieces = new UsedPieces();
            $usedPieces->setIsConsumable($faker->boolean());

            $manager->persist($clients);
            $manager->persist($employees);
            $manager->persist($interventions);
            $manager->persist($pieces);
            $manager->persist($usedPieces);
        }

        $manager->flush();
    }
}
