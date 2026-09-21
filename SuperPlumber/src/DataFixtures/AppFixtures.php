<?php

namespace App\DataFixtures;

use App\Entity\Availabilities;
use App\Entity\Clients;
use App\Entity\Employees;
use App\Entity\Interventions;
use App\Entity\Pieces;
use App\Entity\UsedPieces;
use App\Enum\Position;
use App\Enum\Status;
use App\Enum\Type;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private const AVAILABILITY_START_HOUR = 8;
    private const AVAILABILITY_END_HOUR = 17;

    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $timezone = new DateTimeZone('Europe/Paris');
        $now = new DateTime('now', $timezone);

        [$plumbers, $workDays] = $this->createEmployees($manager);

        $clients = $this->createClients($manager, $faker);
        $pieces = $this->createPieces($manager);

        $availabilities = $this->createAvailabilities(
            $manager,
            $plumbers,
            $workDays,
            $now
        );

        $interventions = $this->createInterventions(
            $manager,
            $faker,
            $clients,
            $plumbers,
            $workDays,
            $availabilities,
            $now
        );

        $this->createUsedPieces(
            $manager,
            $interventions,
            $pieces
        );

        $manager->flush();
    }

    /**
     * Crée exactement :
     * - 2 administrateurs
     * - 6 plombiers
     *
     * Les admins restent utilisables normalement dans l'application,
     * mais ne seront jamais choisis par les fixtures pour une intervention.
     *
     * @return array{0: Employees[], 1: array<int, int[]>}
     */
    private function createEmployees(ObjectManager $manager): array
    {
        /*
         * ---------------------------------------------------------
         * Administrateurs
         * ---------------------------------------------------------
         */
        $adminsData = [
            [
                'Admin',
                'Test',
                'admin@test.com',
                '0600000000',
            ],
            [
                'Sophie',
                'Martin',
                'sophie.martin@superplumber.fr',
                '0600000001',
            ],
        ];

        foreach ($adminsData as [$firstName, $lastName, $email, $phone]) {
            $admin = new Employees();

            $admin->setFirstName($firstName);
            $admin->setLastName($lastName);
            $admin->setEmail($email);
            $admin->setPhone($phone);
            $admin->setPosition(Position::ADMINISTRATOR);

            $admin->setPassword(
                $this->hasher->hashPassword($admin, 'admin123')
            );

            $manager->persist($admin);
        }

        /*
         * ---------------------------------------------------------
         * Plombiers
         *
         * Les nombres correspondent aux jours ISO :
         * 1 = lundi
         * 2 = mardi
         * ...
         * 7 = dimanche
         *
         * Chaque plombier travaille toujours exactement 2 jours.
         * Tous les jours de la semaine sont couverts.
         * ---------------------------------------------------------
         */
        $plumbersData = [
            [
                'Jean',
                'Dupont',
                'plombier@test.com',
                '0611111111',
                [1, 2], // lundi + mardi
            ],
            [
                'Karim',
                'Benali',
                'karim.benali@superplumber.fr',
                '0611111112',
                [1, 4], // lundi + jeudi
            ],
            [
                'Lucas',
                'Bernard',
                'lucas.bernard@superplumber.fr',
                '0611111113',
                [1, 6], // lundi + samedi
            ],
            [
                'Thomas',
                'Robert',
                'thomas.robert@superplumber.fr',
                '0611111114',
                [2, 3], // mardi + mercredi
            ],
            [
                'Nicolas',
                'Leroy',
                'nicolas.leroy@superplumber.fr',
                '0611111115',
                [4, 6], // jeudi + samedi
            ],
            [
                'Hugo',
                'Morel',
                'hugo.morel@superplumber.fr',
                '0611111116',
                [5, 7], // vendredi + dimanche
            ],
        ];

        $plumbers = [];
        $workDays = [];

        foreach (
            $plumbersData as $index =>
            [$firstName, $lastName, $email, $phone, $days]
        ) {
            $plumber = new Employees();

            $plumber->setFirstName($firstName);
            $plumber->setLastName($lastName);
            $plumber->setEmail($email);
            $plumber->setPhone($phone);
            $plumber->setPosition(Position::PLUMBER);

            $plumber->setPassword(
                $this->hasher->hashPassword(
                    $plumber,
                    'plombier123'
                )
            );

            $manager->persist($plumber);

            $plumbers[$index] = $plumber;
            $workDays[$index] = $days;
        }

        return [$plumbers, $workDays];
    }

    /**
     * Crée un client fixe + 14 clients aléatoires.
     *
     * @return Clients[]
     */
    private function createClients(
        ObjectManager $manager,
        Generator $faker
    ): array {
        $clients = [];

        /*
         * ---------------------------------------------------------
         * Client de démonstration
         * ---------------------------------------------------------
         */
        $fixedClient = new Clients();

        $fixedClient->setEmail('client@test.com');
        $fixedClient->setAddress($faker->address());
        $fixedClient->setFirstName('Client');
        $fixedClient->setLastName('Fidèle');
        $fixedClient->setPhone('0622222222');

        $fixedClient->setPassword(
            $this->hasher->hashPassword(
                $fixedClient,
                'client123'
            )
        );

        $manager->persist($fixedClient);

        // Important : il fait aussi partie des clients disponibles
        // pour les interventions.
        $clients[] = $fixedClient;

        /*
         * ---------------------------------------------------------
         * Clients générés
         * ---------------------------------------------------------
         */
        for ($i = 1; $i <= 14; $i++) {
            $client = new Clients();

            $firstName = $faker->firstName();
            $lastName = $faker->lastName();

            $client->setFirstName($firstName);
            $client->setLastName($lastName);
            $client->setAddress($faker->address());
            $client->setPhone($faker->phoneNumber());

            /*
             * Le numéro final garantit l'unicité de l'adresse email
             * même si Faker génère deux fois le même nom.
             */
            $client->setEmail(
                sprintf(
                    '%s.%s.%d@demo-superplumber.fr',
                    $this->formatMail($firstName),
                    $this->formatMail($lastName),
                    $i
                )
            );

            $client->setPassword(
                $this->hasher->hashPassword(
                    $client,
                    'client123'
                )
            );

            $manager->persist($client);

            $clients[] = $client;
        }

        return $clients;
    }

    /**
     * Catalogue fixe et réaliste.
     *
     * Le stock ne change donc pas complètement à chaque reload.
     *
     * @return array<string, array{entity: Pieces, consumable: bool}>
     */
    private function createPieces(ObjectManager $manager): array
    {
        /*
         * Format :
         *
         * [
         *     nom,
         *     quantité,
         *     seuil d'alerte,
         *     fournisseur,
         *     consommable ?
         * ]
         */
        $catalog = [
            [
                'Joint fibre 12/17',
                120,
                25,
                'CEDEO',
                true,
            ],
            [
                'Joint fibre 15/21',
                100,
                25,
                'CEDEO',
                true,
            ],
            [
                'Joint fibre 20/27',
                80,
                20,
                'CEDEO',
                true,
            ],
            [
                'Ruban PTFE 12 mm',
                60,
                15,
                'Richardson',
                true,
            ],
            [
                'Raccord cuivre Ø12',
                40,
                8,
                'CEDEO',
                true,
            ],
            [
                'Raccord cuivre Ø14',
                35,
                8,
                'CEDEO',
                true,
            ],
            [
                'Raccord cuivre Ø16',
                35,
                8,
                'CEDEO',
                true,
            ],
            [
                'Coude cuivre Ø14 90°',
                30,
                8,
                'Richardson',
                true,
            ],
            [
                'Té cuivre Ø14',
                25,
                6,
                'Brossette',
                true,
            ],
            [
                'Tube cuivre Ø14 - 2 m',
                18,
                5,
                'Richardson',
                true,
            ],
            [
                'Tube PVC Ø32 - 2 m',
                25,
                6,
                'CEDEO',
                true,
            ],
            [
                'Tube PVC Ø40 - 2 m',
                25,
                6,
                'CEDEO',
                true,
            ],
            [
                'Coude PVC Ø40 87°',
                30,
                8,
                'CEDEO',
                true,
            ],
            [
                'Siphon lavabo',
                15,
                4,
                'Brossette',
                true,
            ],
            [
                'Flexible sanitaire 30 cm',
                25,
                6,
                'CEDEO',
                true,
            ],
            [
                'Flexible sanitaire 50 cm',
                25,
                6,
                'CEDEO',
                true,
            ],
            [
                "Robinet d'arrêt 12/17",
                18,
                5,
                'Richardson',
                true,
            ],
            [
                "Mécanisme de chasse d'eau",
                12,
                3,
                'Geberit',
                true,
            ],

            /*
             * Volontairement sous le seuil :
             * permet de démontrer les alertes de stock.
             */
            [
                'Groupe de sécurité chauffe-eau',
                2,
                3,
                'Atlantic',
                true,
            ],
            [
                'Cartouche mitigeur universelle',
                2,
                3,
                'Grohe',
                true,
            ],

            [
                'Clapet anti-retour 15/21',
                12,
                4,
                'CEDEO',
                true,
            ],

            /*
             * Matériel réutilisable.
             */
            [
                'Furet manuel 10 m',
                4,
                1,
                'Virax',
                false,
            ],
            [
                'Pompe déboucheur manuelle',
                3,
                1,
                'Virax',
                false,
            ],
        ];

        $pieces = [];

        foreach (
            $catalog as
            [$name, $quantity, $threshold, $supplier, $consumable]
        ) {
            $piece = new Pieces();

            $piece->setName($name);
            $piece->setQuantity($quantity);
            $piece->setAlertTreshold($threshold);
            $piece->setSupplier($supplier);

            $manager->persist($piece);

            /*
             * On garde l'information "consommable"
             * pour la génération de UsedPieces.
             *
             * Pieces ne possède pas directement cette propriété.
             */
            $pieces[$name] = [
                'entity' => $piece,
                'consumable' => $consumable,
            ];
        }

        return $pieces;
    }

    /**
     * Génère le planning fixe des plombiers.
     *
     * Fenêtre :
     * - 21 jours dans le passé
     * - aujourd'hui
     * - 42 jours dans le futur
     *
     * @return array<int, array<string, Availabilities>>
     */
    private function createAvailabilities(
        ObjectManager $manager,
        array $plumbers,
        array $workDays,
        DateTime $now
    ): array {
        $availabilities = [];

        $startDate = (clone $now)
            ->setTime(0, 0)
            ->modify('-21 days');

        $endDate = (clone $now)
            ->setTime(0, 0)
            ->modify('+42 days');

        for (
            $day = clone $startDate;
            $day <= $endDate;
            $day->modify('+1 day')
        ) {
            $isoDay = (int) $day->format('N');
            $dateKey = $day->format('Y-m-d');

            foreach ($plumbers as $index => $plumber) {
                /*
                 * Le plombier ne travaille que si le jour
                 * correspond à son planning fixe.
                 */
                if (!in_array(
                    $isoDay,
                    $workDays[$index],
                    true
                )) {
                    continue;
                }

                $availability = new Availabilities();

                $availability->setStart(
                    (clone $day)->setTime(
                        self::AVAILABILITY_START_HOUR,
                        0
                    )
                );

                $availability->setEnd(
                    (clone $day)->setTime(
                        self::AVAILABILITY_END_HOUR,
                        0
                    )
                );

                $availability->setFkEmployee($plumber);

                $manager->persist($availability);

                /*
                 * Exemple :
                 *
                 * $availabilities[0]['2026-09-21']
                 */
                $availabilities[$index][$dateKey] =
                    $availability;
            }
        }

        return $availabilities;
    }

    /**
     * @return Interventions[]
     */
    private function createInterventions(
        ObjectManager $manager,
        Generator $faker,
        array $clients,
        array $plumbers,
        array $workDays,
        array $availabilities,
        DateTime $now
    ): array {
        $interventions = [];

        /*
         * =========================================================
         * 1. INTERVENTIONS PASSÉES
         *
         * Seulement :
         * - FINISHED
         * - CANCELED
         * =========================================================
         */
        for ($i = 0; $i < 22; $i++) {
            $day = (clone $now)
                ->setTime(0, 0)
                ->modify(
                    '-' . random_int(1, 21) . ' days'
                );

            /*
             * Pour les fixtures, on choisit uniquement
             * un plombier qui travaille ce jour-là.
             */
            $workingIndexes =
                $this->getWorkingPlumberIndexes(
                    $day,
                    $workDays
                );

            $plumberIndex =
                $workingIndexes[array_rand($workingIndexes)];

            $availability =
                $availabilities[$plumberIndex][$day->format('Y-m-d')] ?? null;

            $start = clone $availability->getStart();

            $end = (clone $start)->modify(
                '+' . random_int(60, 180) . ' minutes'
            );

            /*
             * Environ :
             * 82 % terminées
             * 18 % annulées
             */
            $status =
                random_int(1, 100) <= 82
                ? Status::FINISHED
                : Status::CANCELED;

            $intervention =
                $this->newIntervention(
                    $faker,
                    $clients,
                    $start,
                    $end,
                    $status
                );

            /*
             * Ici, volontairement uniquement $plumbers.
             * Aucun admin n'est sélectionné.
             */
            $intervention->setFkEmployee(
                $plumbers[$plumberIndex]
            );

            $intervention->setFkAvailability(
                $availability
            );

            $manager->persist($intervention);

            $interventions[] = $intervention;
        }

        /*
         * =========================================================
         * 2. INTERVENTIONS EN COURS
         *
         * Toujours entre 1 et 3.
         *
         * On ne dépasse toutefois pas le nombre de plombiers
         * travaillant aujourd'hui afin d'éviter qu'un même
         * plombier ait plusieurs interventions simultanées.
         * =========================================================
         */
        $todayWorkingIndexes =
            $this->getWorkingPlumberIndexes(
                $now,
                $workDays
            );

        shuffle($todayWorkingIndexes);

        $ongoingCount = random_int(
            1,
            min(
                3,
                count($todayWorkingIndexes)
            )
        );

        for ($i = 0; $i < $ongoingCount; $i++) {
            $plumberIndex =
                $todayWorkingIndexes[$i];

            /*
             * L'intervention a commencé avant maintenant
             * et se termine après maintenant.
             */
            $start = (clone $now)->modify(
                '-' . random_int(15, 75) . ' minutes'
            );

            $end = (clone $now)->modify(
                '+' . random_int(45, 150) . ' minutes'
            );

            $intervention =
                $this->newIntervention(
                    $faker,
                    $clients,
                    $start,
                    $end,
                    Status::ONGOING
                );

            $intervention->setFkEmployee(
                $plumbers[$plumberIndex]
            );

            $intervention->setFkAvailability(
                $availabilities[$plumberIndex][$now->format('Y-m-d')] ?? null
            );

            $manager->persist($intervention);

            $interventions[] = $intervention;
        }

        /*
         * =========================================================
         * 3. INTERVENTIONS FUTURES PLANIFIÉES
         *
         * - date future
         * - plombier
         * - disponibilité
         * - statut PLANNED
         * =========================================================
         */
        $futureSlots = [];

        foreach (
            $availabilities as
            $plumberIndex => $byDate
        ) {
            foreach (
                $byDate as
                $dateKey => $availability
            ) {
                $date = new DateTime(
                    $dateKey,
                    $now->getTimezone()
                );

                /*
                 * On ignore aujourd'hui et le passé.
                 */
                if (
                    $date <=
                    (clone $now)->setTime(
                        23,
                        59,
                        59
                    )
                ) {
                    continue;
                }

                $futureSlots[] = [
                    'plumberIndex' =>
                    $plumberIndex,

                    'availability' =>
                    $availability,

                    'date' =>
                    $date,
                ];
            }
        }

        shuffle($futureSlots);

        /*
         * 10 interventions futures déjà planifiées.
         */
        foreach (
            array_slice($futureSlots, 0, 10)
            as $slot
        ) {
            /** @var DateTime $day */
            $day = $slot['date'];

            $start = (clone $day)->setTime(
                random_int(8, 14),
                random_int(0, 1) * 30
            );

            $end = (clone $start)->modify(
                '+' . random_int(60, 180) . ' minutes'
            );

            $intervention =
                $this->newIntervention(
                    $faker,
                    $clients,
                    $start,
                    $end,
                    Status::PLANNED
                );

            $intervention->setFkEmployee(
                $plumbers[$slot['plumberIndex']]
            );

            $intervention->setFkAvailability(
                $slot['availability']
            );

            $manager->persist($intervention);

            $interventions[] = $intervention;
        }

        /*
         * =========================================================
         * 4. INTERVENTIONS FUTURES À PLANIFIER
         *
         * Une date souhaitée existe,
         * mais aucun plombier n'est encore attribué.
         * =========================================================
         */
        for ($i = 0; $i < 6; $i++) {
            $requestedDate =
                (clone $now)
                ->setTime(
                    random_int(8, 17),
                    0
                )
                ->modify(
                    '+' .
                        random_int(1, 21) .
                        ' days'
                );

            $intervention =
                $this->newIntervention(
                    $faker,
                    $clients,
                    $requestedDate,
                    null,
                    Status::TO_PLAN
                );

            /*
             * Volontairement :
             *
             * fkEmployee = null
             * fkAvailability = null
             */
            $manager->persist($intervention);

            $interventions[] = $intervention;
        }

        return $interventions;
    }

    /**
     * Fabrique la partie commune d'une intervention.
     */
    private function newIntervention(
        Generator $faker,
        array $clients,
        ?DateTime $start,
        ?DateTime $end,
        Status $status
    ): Interventions {
        $type =
            $faker->randomElement(
                Type::cases()
            );

        /*
         * Descriptions liées au vrai type d'intervention
         * plutôt qu'un texte Faker sans rapport.
         */
        $descriptions = [
            Type::FUITE->name => [
                "Recherche et réparation d'une fuite sur alimentation d'eau.",
                "Fuite signalée sous évier avec contrôle des raccords et joints.",
                "Petite fuite sur canalisation, diagnostic puis remise en étanchéité.",
            ],

            Type::DEBOUCHAGE->name => [
                "Évacuation lente, débouchage et contrôle de l'écoulement.",
                "Canalisation obstruée dans la cuisine, intervention de débouchage.",
                "Débouchage d'une évacuation sanitaire et vérification après intervention.",
            ],

            Type::REPARATION->name => [
                "Diagnostic d'un chauffe-eau et remplacement des éléments défectueux si nécessaire.",
                "Contrôle d'une fuite sur chauffe-eau et remise en service.",
                "Réparation sur groupe de sécurité et contrôle de l'installation.",
            ],

            Type::INSTALLATION->name => [
                "Remplacement d'une robinetterie et contrôle de l'étanchéité.",
                "Installation d'un nouveau mitigeur avec raccordement des flexibles.",
                "Pose d'un robinet d'arrêt et vérification du réseau après remise en eau.",
            ],

            Type::AUTRE->name => [
                "Diagnostic plomberie demandé par le client.",
                "Intervention de contrôle et petite remise en état de l'installation.",
                "Vérification générale de l'installation suite à une anomalie signalée.",
            ],
        ];

        $intervention = new Interventions();

        $intervention->setType($type);
        $intervention->setStatus($status);

        $intervention->setFkClient(
            $clients[array_rand($clients)]
        );

        $intervention->setDescription(
            $faker->randomElement(
                $descriptions[$type->name]
            )
        );

        $intervention->setStartAt($start);
        $intervention->setEndAt($end);

        return $intervention;
    }

    /**
     * Les pièces sont utilisées UNIQUEMENT pour :
     *
     * - ONGOING
     * - FINISHED
     *
     * Et ces deux statuts ont TOUJOURS au moins
     * une pièce utilisée.
     */
    private function createUsedPieces(
        ObjectManager $manager,
        array $interventions,
        array $pieces
    ): void {
        /*
         * Les pièces proposées dépendent du type
         * d'intervention.
         */
        $piecePools = [
            Type::FUITE->name => [
                'Joint fibre 12/17',
                'Joint fibre 15/21',
                'Ruban PTFE 12 mm',
                'Raccord cuivre Ø12',
                'Raccord cuivre Ø14',
                "Robinet d'arrêt 12/17",
            ],

            Type::DEBOUCHAGE->name => [
                'Furet manuel 10 m',
                'Pompe déboucheur manuelle',
                'Siphon lavabo',
                'Tube PVC Ø32 - 2 m',
                'Tube PVC Ø40 - 2 m',
                'Coude PVC Ø40 87°',
            ],

            Type::REPARATION->name => [
                'Groupe de sécurité chauffe-eau',
                'Joint fibre 20/27',
                'Ruban PTFE 12 mm',
                'Raccord cuivre Ø14',
                'Clapet anti-retour 15/21',
            ],

            Type::INSTALLATION->name => [
                'Flexible sanitaire 30 cm',
                'Flexible sanitaire 50 cm',
                "Robinet d'arrêt 12/17",
                'Ruban PTFE 12 mm',
                'Cartouche mitigeur universelle',
                'Joint fibre 15/21',
            ],

            Type::AUTRE->name => [
                'Ruban PTFE 12 mm',
                'Joint fibre 12/17',
                'Joint fibre 15/21',
                'Raccord cuivre Ø14',
                'Clapet anti-retour 15/21',
            ],
        ];

        foreach ($interventions as $intervention) {
            /*
             * CANCELED / PLANNED / TO_PLAN :
             * aucune pièce utilisée.
             */
            if (
                !in_array(
                    $intervention->getStatus(),
                    [
                        Status::ONGOING,
                        Status::FINISHED,
                    ],
                    true
                )
            ) {
                continue;
            }

            $candidateNames =
                $piecePools[$intervention
                    ->getType()
                    ->name];

            shuffle($candidateNames);

            /*
             * Chaque intervention concernée utilise
             * entre 1 et 3 références.
             */
            $wantedCount =
                random_int(
                    1,
                    min(
                        3,
                        count($candidateNames)
                    )
                );

            $created = 0;

            foreach (
                $candidateNames as
                $pieceName
            ) {
                if (
                    $created >=
                    $wantedCount
                ) {
                    break;
                }

                $pieceData =
                    $pieces[$pieceName];

                /** @var Pieces $piece */
                $piece =
                    $pieceData['entity'];

                $isConsumable =
                    $pieceData['consumable'];

                /*
                 * Pas de stock négatif.
                 */
                if (
                    $isConsumable &&
                    $piece->getQuantity() <= 0
                ) {
                    continue;
                }

                $quantity =
                    $isConsumable
                    ? min(
                        random_int(1, 3),
                        $piece->getQuantity()
                    )
                    : 1;

                if ($quantity <= 0) {
                    continue;
                }

                $usedPiece =
                    new UsedPieces();

                $usedPiece->setFkIntervention(
                    $intervention
                );

                $usedPiece->setFkPiece(
                    $piece
                );

                $usedPiece->setIsConsumable(
                    $isConsumable
                );

                $usedPiece->setQuantity(
                    (float) $quantity
                );

                $manager->persist(
                    $usedPiece
                );

                /*
                 * Même logique que dans ton contrôleur :
                 * le stock diminue seulement si la pièce
                 * est consommable.
                 */
                if ($isConsumable) {
                    $piece->setQuantity(
                        $piece->getQuantity()
                            - $quantity
                    );
                }

                $created++;
            }

            /*
             * Sécurité absolue :
             * une intervention ONGOING ou FINISHED
             * doit toujours avoir au moins un matériel.
             */
            if ($created === 0) {
                $fallback =
                    $pieces['Furet manuel 10 m'];

                $usedPiece =
                    new UsedPieces();

                $usedPiece->setFkIntervention(
                    $intervention
                );

                $usedPiece->setFkPiece(
                    $fallback['entity']
                );

                $usedPiece->setIsConsumable(
                    false
                );

                $usedPiece->setQuantity(
                    1.0
                );

                $manager->persist(
                    $usedPiece
                );
            }
        }
    }

    /**
     * Retourne uniquement les index des plombiers
     * censés travailler à cette date.
     *
     * @return int[]
     */
    private function getWorkingPlumberIndexes(
        DateTime $date,
        array $workDays
    ): array {
        $isoDay =
            (int) $date->format('N');

        $indexes = [];

        foreach (
            $workDays as
            $index => $days
        ) {
            if (
                in_array(
                    $isoDay,
                    $days,
                    true
                )
            ) {
                $indexes[] = $index;
            }
        }

        return $indexes;
    }

    /**
     * Nettoyage pour générer une adresse email.
     */
    private function formatMail(
        string $string
    ): string {
        $string =
            iconv(
                'UTF-8',
                'ASCII//TRANSLIT',
                $string
            ) ?: $string;

        $string =
            preg_replace(
                '/[^a-zA-Z0-9]/',
                '',
                $string
            ) ?? $string;

        return strtolower($string);
    }
}
