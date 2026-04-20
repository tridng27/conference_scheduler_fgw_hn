<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Conference;
use App\Entity\Registration;
use App\Entity\Room;
use App\Entity\Session;
use App\Entity\Speaker;
use App\Entity\User;
use App\Entity\Venue;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed:test-data',
    description: 'Seed realistic conference scheduler test data for local development.',
)]
final class SeedTestDataCommand extends Command
{
    private const ADMIN_EMAIL = 'Admin@gmail.com';
    private const ADMIN_PASSWORD = 'admin@123';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('reset', null, InputOption::VALUE_NONE, 'Purge scheduler data before seeding')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ((bool) $input->getOption('reset')) {
            $this->purgeData();
            $io->note('Existing scheduler data was removed.');
        }

        $now = new \DateTimeImmutable();

        $admin = $this->upsertUser(
            self::ADMIN_EMAIL,
            'System',
            'Administrator',
            null,
            ['ROLE_USER', 'ROLE_ADMIN'],
            'ADMIN',
            self::ADMIN_PASSWORD,
            $now,
        );

        $normalUsers = [];
        $normalUserSpecs = [
            ['john.doe@example.com', 'John', 'Doe', '+1-202-555-0111'],
            ['jane.smith@example.com', 'Jane', 'Smith', '+1-202-555-0112'],
            ['michael.lee@example.com', 'Michael', 'Lee', '+1-202-555-0113'],
            ['sophia.kim@example.com', 'Sophia', 'Kim', '+1-202-555-0114'],
            ['david.tran@example.com', 'David', 'Tran', '+1-202-555-0115'],
            ['emma.nguyen@example.com', 'Emma', 'Nguyen', '+1-202-555-0116'],
            ['oliver.martin@example.com', 'Oliver', 'Martin', '+1-202-555-0117'],
            ['mia.wilson@example.com', 'Mia', 'Wilson', '+1-202-555-0118'],
        ];

        foreach ($normalUserSpecs as [$email, $firstName, $lastName, $phone]) {
            $normalUsers[$email] = $this->upsertUser(
                $email,
                $firstName,
                $lastName,
                $phone,
                ['ROLE_USER'],
                'USER',
                'user@123',
                $now,
            );
        }

        $speakerUsers = [];
        $speakerUserSpecs = [
            ['alex.chen.speaker@example.com', 'Alex', 'Chen', '+1-202-555-0151'],
            ['priya.patel.speaker@example.com', 'Priya', 'Patel', '+1-202-555-0152'],
            ['liam.garcia.speaker@example.com', 'Liam', 'Garcia', '+1-202-555-0153'],
            ['noah.brown.speaker@example.com', 'Noah', 'Brown', '+1-202-555-0154'],
        ];

        foreach ($speakerUserSpecs as [$email, $firstName, $lastName, $phone]) {
            $speakerUsers[$email] = $this->upsertUser(
                $email,
                $firstName,
                $lastName,
                $phone,
                ['ROLE_USER'],
                'USER',
                'speaker@123',
                $now,
            );
        }

        $this->entityManager->flush();

        $speakers = [
            'alex' => $this->upsertSpeaker(
                'Alex Chen',
                'alex.chen.speaker@example.com',
                'CloudNova',
                'Staff Engineer',
                'Distributed systems and Symfony performance specialist.',
                'Architecture',
                '@alexchen',
                $speakerUsers['alex.chen.speaker@example.com'],
            ),
            'priya' => $this->upsertSpeaker(
                'Priya Patel',
                'priya.patel.speaker@example.com',
                'DataForge',
                'Data Platform Lead',
                'Focuses on PostgreSQL optimization and data analytics at scale.',
                'Data Engineering',
                '@priyapatel',
                $speakerUsers['priya.patel.speaker@example.com'],
            ),
            'liam' => $this->upsertSpeaker(
                'Liam Garcia',
                'liam.garcia.speaker@example.com',
                'SecureStack',
                'Security Architect',
                'Builds secure auth and access-control systems for enterprise apps.',
                'Security',
                '@liamgarcia',
                $speakerUsers['liam.garcia.speaker@example.com'],
            ),
            'noah' => $this->upsertSpeaker(
                'Noah Brown',
                'noah.brown.speaker@example.com',
                'UXSphere',
                'Product Designer',
                'Design systems and UX workflows for B2B SaaS products.',
                'Design',
                '@noahbrown',
                $speakerUsers['noah.brown.speaker@example.com'],
            ),
        ];

        $venues = [
            'civic' => $this->upsertVenue('Civic Convention Center', '120 Main St', 'Seattle', 'WA', '98101', 1800, 'WiFi,Parking,Projectors'),
            'innovation' => $this->upsertVenue('Innovation Hall', '88 Lake Ave', 'Seattle', 'WA', '98104', 900, 'WiFi,Stage,LiveStream'),
            'techpark' => $this->upsertVenue('Tech Park Campus', '501 Pine St', 'Seattle', 'WA', '98109', 1200, 'WiFi,Parking,Recording'),
        ];

        $rooms = [
            'main_a' => $this->upsertRoom('Main Auditorium A', 450, 'Civic Building', '1', '4K Projector,PA System', [$venues['civic']]),
            'main_b' => $this->upsertRoom('Main Auditorium B', 320, 'Civic Building', '1', 'LED Wall,PA System', [$venues['civic']]),
            'workshop_1' => $this->upsertRoom('Workshop Room 1', 120, 'Innovation Tower', '2', 'Whiteboard,HDMI', [$venues['innovation']]),
            'workshop_2' => $this->upsertRoom('Workshop Room 2', 100, 'Innovation Tower', '2', 'Whiteboard,Recording Kit', [$venues['innovation']]),
            'lab_1' => $this->upsertRoom('Lab Room 1', 80, 'Tech Park North', '3', 'Mac Mini Stations', [$venues['techpark']]),
            'lab_2' => $this->upsertRoom('Lab Room 2', 70, 'Tech Park North', '3', 'Linux Workstations', [$venues['techpark']]),
        ];

        $conferenceDates = [
            'past' => [
                $now->modify('-35 days')->setTime(9, 0),
                $now->modify('-33 days')->setTime(18, 0),
                'Completed',
                false,
            ],
            'running' => [
                $now->modify('-1 day')->setTime(9, 0),
                $now->modify('+1 day')->setTime(18, 0),
                'Running',
                true,
            ],
            'upcoming_summer' => [
                $now->modify('+20 days')->setTime(9, 0),
                $now->modify('+22 days')->setTime(18, 0),
                'Scheduled',
                true,
            ],
            'upcoming_fall' => [
                $now->modify('+45 days')->setTime(9, 0),
                $now->modify('+47 days')->setTime(18, 0),
                'Scheduled',
                true,
            ],
        ];

        $conferences = [
            'past' => $this->upsertConference(
                'Cloud Engineering Summit 2026 (Past)',
                'Retrospective event to test historical reports and finished sessions.',
                'Seattle Downtown',
                $conferenceDates['past'][0],
                $conferenceDates['past'][1],
                $conferenceDates['past'][2],
                $conferenceDates['past'][3],
                1200,
                $admin,
            ),
            'running' => $this->upsertConference(
                'Conference Scheduler Live 2026',
                'Current live event used to test running dashboard widgets and schedules.',
                'Civic Convention Center',
                $conferenceDates['running'][0],
                $conferenceDates['running'][1],
                $conferenceDates['running'][2],
                $conferenceDates['running'][3],
                1500,
                $admin,
            ),
            'summer' => $this->upsertConference(
                'Future Tech Summit 2026',
                'Upcoming conference for testing registrations and future planning.',
                'Innovation Hall',
                $conferenceDates['upcoming_summer'][0],
                $conferenceDates['upcoming_summer'][1],
                $conferenceDates['upcoming_summer'][2],
                $conferenceDates['upcoming_summer'][3],
                900,
                $admin,
            ),
            'fall' => $this->upsertConference(
                'Product and Platform Expo 2026',
                'Upcoming cross-discipline conference with multiple tracks.',
                'Tech Park Campus',
                $conferenceDates['upcoming_fall'][0],
                $conferenceDates['upcoming_fall'][1],
                $conferenceDates['upcoming_fall'][2],
                $conferenceDates['upcoming_fall'][3],
                1000,
                $admin,
            ),
        ];

        $sessions = [
            [
                'title' => 'Past Keynote: Lessons from 2025',
                'conference' => $conferences['past'],
                'start' => $conferenceDates['past'][0]->setTime(10, 0),
                'end' => $conferenceDates['past'][0]->setTime(11, 0),
                'type' => 'Keynote',
                'status' => 'Completed',
                'track' => 'Strategy',
                'max' => 400,
                'capacity' => 400,
                'speakers' => [$speakers['alex']],
                'rooms' => [$rooms['main_a']],
            ],
            [
                'title' => 'Symfony 8 in Production',
                'conference' => $conferences['running'],
                'start' => $conferenceDates['running'][0]->setTime(9, 30),
                'end' => $conferenceDates['running'][0]->setTime(10, 30),
                'type' => 'Talk',
                'status' => 'Running',
                'track' => 'Backend',
                'max' => 300,
                'capacity' => 280,
                'speakers' => [$speakers['alex']],
                'rooms' => [$rooms['main_a']],
            ],
            [
                'title' => 'Scaling PostgreSQL for Registrations',
                'conference' => $conferences['running'],
                'start' => $conferenceDates['running'][0]->setTime(11, 0),
                'end' => $conferenceDates['running'][0]->setTime(12, 0),
                'type' => 'Workshop',
                'status' => 'Scheduled',
                'track' => 'Data',
                'max' => 120,
                'capacity' => 100,
                'speakers' => [$speakers['priya']],
                'rooms' => [$rooms['workshop_1']],
            ],
            [
                'title' => 'Secure Access Control Patterns',
                'conference' => $conferences['running'],
                'start' => $conferenceDates['running'][0]->setTime(13, 30),
                'end' => $conferenceDates['running'][0]->setTime(14, 30),
                'type' => 'Talk',
                'status' => 'Scheduled',
                'track' => 'Security',
                'max' => 220,
                'capacity' => 200,
                'speakers' => [$speakers['liam']],
                'rooms' => [$rooms['main_b']],
            ],
            [
                'title' => 'Designing Schedule UX that Works',
                'conference' => $conferences['summer'],
                'start' => $conferenceDates['upcoming_summer'][0]->setTime(10, 0),
                'end' => $conferenceDates['upcoming_summer'][0]->setTime(11, 0),
                'type' => 'Talk',
                'status' => 'Scheduled',
                'track' => 'Design',
                'max' => 160,
                'capacity' => 150,
                'speakers' => [$speakers['noah']],
                'rooms' => [$rooms['main_b']],
            ],
            [
                'title' => 'Hands-on Performance Tuning Lab',
                'conference' => $conferences['summer'],
                'start' => $conferenceDates['upcoming_summer'][0]->setTime(11, 30),
                'end' => $conferenceDates['upcoming_summer'][0]->setTime(12, 30),
                'type' => 'Lab',
                'status' => 'Scheduled',
                'track' => 'Performance',
                'max' => 90,
                'capacity' => 80,
                'speakers' => [$speakers['alex'], $speakers['priya']],
                'rooms' => [$rooms['lab_1']],
            ],
            [
                'title' => 'Platform Governance for Large Teams',
                'conference' => $conferences['fall'],
                'start' => $conferenceDates['upcoming_fall'][0]->setTime(9, 30),
                'end' => $conferenceDates['upcoming_fall'][0]->setTime(10, 30),
                'type' => 'Panel',
                'status' => 'Scheduled',
                'track' => 'Leadership',
                'max' => 260,
                'capacity' => 250,
                'speakers' => [$speakers['alex'], $speakers['liam'], $speakers['noah']],
                'rooms' => [$rooms['main_a']],
            ],
            [
                'title' => 'Production Incident Simulation',
                'conference' => $conferences['fall'],
                'start' => $conferenceDates['upcoming_fall'][0]->setTime(14, 0),
                'end' => $conferenceDates['upcoming_fall'][0]->setTime(15, 30),
                'type' => 'Workshop',
                'status' => 'Scheduled',
                'track' => 'Reliability',
                'max' => 100,
                'capacity' => 90,
                'speakers' => [$speakers['liam']],
                'rooms' => [$rooms['workshop_2']],
            ],
        ];

        foreach ($sessions as $sessionSpec) {
            $this->upsertSession(
                $sessionSpec['title'],
                $sessionSpec['conference'],
                $sessionSpec['start'],
                $sessionSpec['end'],
                $sessionSpec['type'],
                $sessionSpec['status'],
                $sessionSpec['track'],
                $sessionSpec['max'],
                $sessionSpec['capacity'],
                $sessionSpec['speakers'],
                $sessionSpec['rooms'],
            );
        }

        $this->entityManager->flush();

        $registrationPairs = [
            [$normalUsers['john.doe@example.com'], $conferences['running'], 'confirmed', 'Standard'],
            [$normalUsers['jane.smith@example.com'], $conferences['running'], 'confirmed', 'VIP'],
            [$normalUsers['michael.lee@example.com'], $conferences['running'], 'pending', 'Standard'],
            [$normalUsers['sophia.kim@example.com'], $conferences['summer'], 'confirmed', 'Early Bird'],
            [$normalUsers['david.tran@example.com'], $conferences['summer'], 'confirmed', 'Standard'],
            [$normalUsers['emma.nguyen@example.com'], $conferences['summer'], 'pending', 'Student'],
            [$normalUsers['oliver.martin@example.com'], $conferences['fall'], 'confirmed', 'Standard'],
            [$normalUsers['mia.wilson@example.com'], $conferences['fall'], 'cancelled', 'Standard'],
            [$normalUsers['john.doe@example.com'], $conferences['fall'], 'confirmed', 'VIP'],
            [$normalUsers['jane.smith@example.com'], $conferences['summer'], 'confirmed', 'Speaker Guest'],
        ];

        foreach ($registrationPairs as [$user, $conference, $status, $ticketType]) {
            $this->upsertRegistration($user, $conference, $status, $ticketType, $now);
        }

        $this->entityManager->flush();

        $io->success([
            'Seed data is ready.',
            'Admin login: Admin@gmail.com / admin@123',
            'Normal user sample login: john.doe@example.com / user@123',
            'Speaker user sample login: alex.chen.speaker@example.com / speaker@123',
            'Run with --reset to clean and regenerate all scheduler data.',
        ]);

        return Command::SUCCESS;
    }

    private function purgeData(): void
    {
        $connection = $this->entityManager->getConnection();
        $platformClass = strtolower($connection->getDatabasePlatform()::class);

        if (str_contains($platformClass, 'postgresql')) {
            $connection->executeStatement('TRUNCATE TABLE registration_user, registration_conference, session_speaker, session_room, room_venue, registration, session, speaker, room, venue, conference, "user" RESTART IDENTITY CASCADE');

            return;
        }

        $tables = [
            'registration_user',
            'registration_conference',
            'session_speaker',
            'session_room',
            'room_venue',
            'registration',
            'session',
            'speaker',
            'room',
            'venue',
            'conference',
            'user',
        ];

        if (str_contains($platformClass, 'mysql')) {
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        } elseif (str_contains($platformClass, 'sqlite')) {
            $connection->executeStatement('PRAGMA foreign_keys = OFF');
        }

        foreach ($tables as $table) {
            if ('user' === $table && !str_contains($platformClass, 'sqlite')) {
                $connection->executeStatement('DELETE FROM "user"');
                continue;
            }

            $connection->executeStatement(sprintf('DELETE FROM %s', $table));
        }

        if (str_contains($platformClass, 'mysql')) {
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
        } elseif (str_contains($platformClass, 'sqlite')) {
            $connection->executeStatement('PRAGMA foreign_keys = ON');
        }
    }

    private function upsertUser(
        string $email,
        string $firstName,
        string $lastName,
        ?string $phone,
        array $roles,
        string $roleLabel,
        string $plainPassword,
        \DateTimeImmutable $now,
    ): User {
        /** @var User|null $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user instanceof User) {
            $user = new User();
            $user->setEmail($email);
            $user->setCreatedAt($this->asMutableDateTime($now));
        }

        $user
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setPhone($phone)
            ->setRoles($roles)
            ->setRole($roleLabel)
            ->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        if (null === $user->getCreatedAt()) {
            $user->setCreatedAt($this->asMutableDateTime($now));
        }

        $this->entityManager->persist($user);

        return $user;
    }

    private function upsertSpeaker(
        string $name,
        string $email,
        string $company,
        ?string $jobTitle,
        ?string $bio,
        ?string $expertise,
        ?string $socialLinks,
        User $user,
    ): Speaker {
        /** @var Speaker|null $speaker */
        $speaker = $this->entityManager->getRepository(Speaker::class)->findOneBy(['email' => $email]);
        if (!$speaker instanceof Speaker) {
            $speaker = new Speaker();
            $speaker->setEmail($email);
        }

        $speaker
            ->setName($name)
            ->setCompany($company)
            ->setJobTitle($jobTitle)
            ->setBio($bio)
            ->setExpertise($expertise)
            ->setSocialLinks($socialLinks)
            ->setUser($user)
            ->setPhoto(null);

        $this->entityManager->persist($speaker);

        return $speaker;
    }

    private function upsertVenue(
        string $name,
        string $address,
        ?string $city,
        ?string $state,
        ?string $zipCode,
        ?int $capacity,
        ?string $facilities,
    ): Venue {
        /** @var Venue|null $venue */
        $venue = $this->entityManager->getRepository(Venue::class)->findOneBy(['name' => $name]);
        if (!$venue instanceof Venue) {
            $venue = new Venue();
            $venue->setName($name);
        }

        $venue
            ->setAddress($address)
            ->setCity($city)
            ->setState($state)
            ->setZipCode($zipCode)
            ->setCapacity($capacity)
            ->setFacilities($facilities);

        $this->entityManager->persist($venue);

        return $venue;
    }

    /**
     * @param list<Venue> $venues
     */
    private function upsertRoom(
        string $name,
        int $capacity,
        string $building,
        string $floor,
        ?string $equipment,
        array $venues,
    ): Room {
        /** @var Room|null $room */
        $room = $this->entityManager->getRepository(Room::class)->findOneBy(['name' => $name]);
        if (!$room instanceof Room) {
            $room = new Room();
            $room->setName($name);
        }

        $room
            ->setCapacity($capacity)
            ->setBuilding($building)
            ->setFloor($floor)
            ->setEquipment($equipment);

        foreach ($room->getVenues()->toArray() as $existingVenue) {
            $room->removeVenue($existingVenue);
        }

        foreach ($venues as $venue) {
            $room->addVenue($venue);
        }

        $this->entityManager->persist($room);

        return $room;
    }

    private function upsertConference(
        string $name,
        ?string $description,
        string $location,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate,
        string $status,
        bool $isActive,
        int $maxAttendees,
        User $organizer,
    ): Conference {
        /** @var Conference|null $conference */
        $conference = $this->entityManager->getRepository(Conference::class)->findOneBy(['name' => $name]);
        if (!$conference instanceof Conference) {
            $conference = new Conference();
            $conference->setName($name);
        }

        $conference
            ->setDescription($description)
            ->setLocation($location)
            ->setStartDate($this->asMutableDateTime($startDate))
            ->setEndDate($this->asMutableDateTime($endDate))
            ->setStatus($status)
            ->setCreateAt($this->asMutableDateTime($startDate->modify('-20 days')))
            ->setCreatedAt($this->asMutableDateTime($startDate->modify('-20 days')))
            ->setMaxAttendees($maxAttendees)
            ->setIsActive($isActive)
            ->setOrganizer($organizer);

        $this->entityManager->persist($conference);

        return $conference;
    }

    /**
     * @param list<Speaker> $speakers
     * @param list<Room> $rooms
     */
    private function upsertSession(
        string $title,
        Conference $conference,
        \DateTimeImmutable $startTime,
        \DateTimeImmutable $endTime,
        string $sessionType,
        string $status,
        ?string $track,
        int $maxAttendees,
        ?int $capacity,
        array $speakers,
        array $rooms,
    ): Session {
        /** @var Session|null $session */
        $session = $this->entityManager->getRepository(Session::class)->findOneBy(['title' => $title]);
        if (!$session instanceof Session) {
            $session = new Session();
            $session->setTitle($title);
        }

        $session
            ->setDescription(sprintf('%s session for %s', $sessionType, $conference->getName()))
            ->setConference($conference)
            ->setStartTime($this->asMutableDateTime($startTime))
            ->setEndTime($this->asMutableDateTime($endTime))
            ->setSessionType($sessionType)
            ->setStatus($status)
            ->setTrack($track)
            ->setMaxAttendees($maxAttendees)
            ->setCapacity($capacity);

        foreach ($session->getSpeakers()->toArray() as $existingSpeaker) {
            $session->removeSpeaker($existingSpeaker);
        }

        foreach ($speakers as $speaker) {
            $session->addSpeaker($speaker);
        }

        foreach ($session->getRooms()->toArray() as $existingRoom) {
            $session->removeRoom($existingRoom);
        }

        foreach ($rooms as $room) {
            $session->addRoom($room);
        }

        $this->entityManager->persist($session);

        return $session;
    }

    private function upsertRegistration(
        User $user,
        Conference $conference,
        string $status,
        ?string $ticketType,
        \DateTimeImmutable $now,
    ): Registration {
        $registration = $this->entityManager->getRepository(Registration::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.users', 'u')
            ->innerJoin('r.conferences', 'c')
            ->andWhere('u.id = :userId')
            ->andWhere('c.id = :conferenceId')
            ->setParameter('userId', $user->getId())
            ->setParameter('conferenceId', $conference->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$registration instanceof Registration) {
            $registration = new Registration();
            $registration
                ->setRegistrationDate($this->asMutableDateTime($now))
                ->addUser($user)
                ->addConference($conference);
        }

        $registration
            ->setStatus($status)
            ->setTicketType($ticketType);

        $this->entityManager->persist($registration);

        return $registration;
    }

    private function asMutableDateTime(\DateTimeImmutable $dateTime): \DateTime
    {
        return \DateTime::createFromImmutable($dateTime);
    }
}
