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
use App\Enum\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $piecesTypes = ['Tuyau', 'Coude', 'Manchon', 'Reduction', 'Vanne', 'Joint', 'Robinet'];

        // -------------------------------------------------------
        // un admin fixe pour pouvoir se connecter
        // -------------------------------------------------------
        $admin = new Employees();
        $admin->setEmail('admin@test.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('Test');
        $admin->setPhone('0600000000');
        $admin->setPosition(Position::ADMINISTRATOR);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // -------------------------------------------------------
        // un plombier fixe aussi
        // -------------------------------------------------------
        $plumber = new Employees();
        $plumber->setEmail('plombier@test.com');
        $plumber->setFirstName('Jean');
        $plumber->setLastName('Dupont');
        $plumber->setPhone('0611111111');
        $plumber->setPosition(Position::PLUMBER);
        $plumber->setPassword($this->hasher->hashPassword($plumber, 'plombier123'));
        $manager->persist($plumber);

        // -------------------------------------------------------
        // Un client fixe
        // -------------------------------------------------------
        $client = new Clients();
        $client->setEmail('client@test.com');
        $client->setAddress($faker->address);
        $client->setFirstName('client');
        $client->setLastName('pigeon');
        $client->setPhone(0622222222);
        $client->setPassword($this->hasher->hashPassword($client, 'client123'));
        $manager->persist($client);

        // -------------------------------------------------------
        // création des clients et employés aléatoires
        // -------------------------------------------------------
        $clientsList = [];
        $employeesList = [$admin, $plumber]; // inclure les fixes

        for ($i = 0; $i < 10; $i++) {
            $client = new Clients();
            $client->setAddress($faker->address);
            $client->setFirstName($faker->firstName);
            $client->setLastName($faker->lastName);
            $firstName = $this->format_mail($client->getFirstName());
            $lastName = $this->format_mail($client->getLastName());
            $client->setEmail("$firstName.$lastName@gmail.com");
            $client->setPhone($faker->phoneNumber);
            $client->setPassword($this->hasher->hashPassword($client, 'password123'));
            $manager->persist($client);
            $clientsList[] = $client;

            $employee = new Employees();
            $employee->setLastName($faker->lastName);
            $employee->setFirstName($faker->firstName);
            $employee->setPhone($faker->phoneNumber);
            $employee->setPosition($faker->randomElement(Position::cases()));
            $first = $this->format_mail($employee->getFirstName());
            $last  = $this->format_mail($employee->getLastName());
            $employee->setEmail("$first.$last@gmail.com");
            $employee->setPassword($this->hasher->hashPassword($employee, 'employee123'));
            $manager->persist($employee);
            $employeesList[] = $employee;
        }

        // -------------------------------------------------------
        // création des pièces
        // -------------------------------------------------------
        $piecesList = [];

        for ($i = 0; $i < 10; $i++) {
            $piece = new Pieces();
            $piece->setName($faker->randomElement($piecesTypes));
            $piece->setQuantity(mt_rand(0, 100));
            $piece->setAlertTreshold(mt_rand(1, 10));
            $piece->setSupplier($faker->company);
            $manager->persist($piece);
            $piecesList[] = $piece;
        }

        // -------------------------------------------------------
        // création des interventions
        // -------------------------------------------------------
        $interventionsList = [];

        for ($i = 0; $i < 10; $i++) {
            $intervention = new Interventions();
            $intervention->setStartAt(
                $faker->dateTimeBetween('first day of this month', 'last day of this month')
            );
            // Fin de l'intervention quelques heures après le début
            $start = $intervention->getStartAt();
            $duration = mt_rand(1, 8);
            $intervention->setEndAt((clone $start)->modify("+{$duration} hours"));
            $intervention->setDescription($faker->text);
            $intervention->setType($faker->randomElement(Type::cases()));
            $intervention->setStatus($faker->randomElement(Status::cases()));
            $intervention->setFkClient($faker->randomElement($clientsList));

            // assigner un employé seulement si l'intervention n'est pas à planifier
            if ($intervention->getStatus() !== Status::TO_PLAN) {
                $intervention->setFkEmployee($faker->randomElement($employeesList));
            }

            $manager->persist($intervention);
            $interventionsList[] = $intervention;
        }

        // -------------------------------------------------------
        // créer les pièces utilisées et disponibilités
        // -------------------------------------------------------
        for ($i = 0; $i < 10; $i++) {
            $usedPiece = new UsedPieces();
            $usedPiece->setIsConsumable($faker->boolean());
            $usedPiece->setFkPiece($faker->randomElement($piecesList));
            $usedPiece->setFkIntervention($faker->randomElement($interventionsList));
            $usedPiece->setQuantity(mt_rand(1, 3));
            $manager->persist($usedPiece);

            $availability = new Availabilities();
            $start = $faker->dateTimeBetween('first day of this month', 'last day of this month');
            $end = (clone $start)->modify('+2 hours');
            $availability->setStart($start);
            $availability->setEnd($end);
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

    public function format_mail($string)
    {
        // Supprime les accents
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

        // Remplace tout ce qui n'est pas lettre ou chiffre par rien
        $string = preg_replace('/[^a-zA-Z0-9]/', '', $string);

        // Minuscule
        return strtolower($string);
    }
}
