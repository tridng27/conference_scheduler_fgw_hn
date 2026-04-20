<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260420120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed mock data for conferences, venues, rooms, speakers, sessions, and registrations';
    }

    public function up(Schema $schema): void
    {
        // Create mock users (organizers, speakers, attendees)
        $this->createMockUsers();
        
        // Create mock venues
        $this->createMockVenues();
        
        // Create mock rooms
        $this->createMockRooms();
        
        // Create mock conferences
        $this->createMockConferences();
        
        // Create mock sessions
        $this->createMockSessions();
        
        // Create mock speakers
        $this->createMockSpeakers();
        
        // Create mock registrations
        $this->createMockRegistrations();
    }

    public function down(Schema $schema): void
    {
        // Delete in reverse order to respect foreign key constraints
        $this->addSql('DELETE FROM registration_user');
        $this->addSql('DELETE FROM conference_registration');
        $this->addSql('DELETE FROM session_speaker');
        $this->addSql('DELETE FROM session');
        $this->addSql('DELETE FROM conference');
        $this->addSql('DELETE FROM room_venue');
        $this->addSql('DELETE FROM room');
        $this->addSql('DELETE FROM venue');
        $this->addSql('DELETE FROM speaker');
        $this->addSql('DELETE FROM "user" WHERE email NOT IN (\'Admin@gmail.com\')');
    }

    private function createMockUsers(): void
    {
        // Create organizers
        $organizers = [
            ['email' => 'alice.johnson@conference.com', 'firstName' => 'Alice', 'lastName' => 'Johnson', 'role' => 'ORGANIZER'],
            ['email' => 'bob.smith@conference.com', 'firstName' => 'Bob', 'lastName' => 'Smith', 'role' => 'ORGANIZER'],
            ['email' => 'carol.white@conference.com', 'firstName' => 'Carol', 'lastName' => 'White', 'role' => 'ORGANIZER'],
        ];

        foreach ($organizers as $organizer) {
            $exists = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM "user" WHERE email = ?', [$organizer['email']]);
            if (0 === $exists) {
                $passwordHash = password_hash('password123', PASSWORD_BCRYPT);
                $this->addSql(
                    'INSERT INTO "user" (email, roles, password, first_name, last_name, phone, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
                    [
                        $organizer['email'],
                        json_encode(['ROLE_ORGANIZER']),
                        $passwordHash,
                        $organizer['firstName'],
                        $organizer['lastName'],
                        null,
                        $organizer['role'],
                    ]
                );
            }
        }

        // Create speakers
        $speakers = [
            ['email' => 'david.brown@speaker.com', 'firstName' => 'David', 'lastName' => 'Brown'],
            ['email' => 'emma.davis@speaker.com', 'firstName' => 'Emma', 'lastName' => 'Davis'],
            ['email' => 'frank.miller@speaker.com', 'firstName' => 'Frank', 'lastName' => 'Miller'],
            ['email' => 'grace.lee@speaker.com', 'firstName' => 'Grace', 'lastName' => 'Lee'],
        ];

        foreach ($speakers as $speaker) {
            $exists = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM "user" WHERE email = ?', [$speaker['email']]);
            if (0 === $exists) {
                $passwordHash = password_hash('password123', PASSWORD_BCRYPT);
                $this->addSql(
                    'INSERT INTO "user" (email, roles, password, first_name, last_name, phone, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
                    [
                        $speaker['email'],
                        json_encode(['ROLE_SPEAKER']),
                        $passwordHash,
                        $speaker['firstName'],
                        $speaker['lastName'],
                        null,
                        'SPEAKER',
                    ]
                );
            }
        }

        // Create attendees
        $attendees = [
            ['email' => 'john.doe@attendee.com', 'firstName' => 'John', 'lastName' => 'Doe'],
            ['email' => 'jane.smith@attendee.com', 'firstName' => 'Jane', 'lastName' => 'Smith'],
            ['email' => 'michael.jones@attendee.com', 'firstName' => 'Michael', 'lastName' => 'Jones'],
            ['email' => 'sarah.wilson@attendee.com', 'firstName' => 'Sarah', 'lastName' => 'Wilson'],
            ['email' => 'robert.garcia@attendee.com', 'firstName' => 'Robert', 'lastName' => 'Garcia'],
        ];

        foreach ($attendees as $attendee) {
            $exists = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM "user" WHERE email = ?', [$attendee['email']]);
            if (0 === $exists) {
                $passwordHash = password_hash('password123', PASSWORD_BCRYPT);
                $this->addSql(
                    'INSERT INTO "user" (email, roles, password, first_name, last_name, phone, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
                    [
                        $attendee['email'],
                        json_encode(['ROLE_ATTENDEE']),
                        $passwordHash,
                        $attendee['firstName'],
                        $attendee['lastName'],
                        null,
                        'ATTENDEE',
                    ]
                );
            }
        }
    }

    private function createMockVenues(): void
    {
        $venues = [
            [
                'name' => 'Silicon Valley Convention Center',
                'address' => '123 Tech Boulevard',
                'city' => 'San Jose',
                'state' => 'CA',
                'zipCode' => '95110',
                'capacity' => 5000,
                'facilities' => 'WiFi, Parking, Catering',
            ],
            [
                'name' => 'New York Conference Hall',
                'address' => '456 Manhattan Avenue',
                'city' => 'New York',
                'state' => 'NY',
                'zipCode' => '10001',
                'capacity' => 3000,
                'facilities' => 'WiFi, Parking, Restaurant',
            ],
            [
                'name' => 'Chicago Innovation Center',
                'address' => '789 Innovation Drive',
                'city' => 'Chicago',
                'state' => 'IL',
                'zipCode' => '60601',
                'capacity' => 2500,
                'facilities' => 'WiFi, AV Equipment, Catering',
            ],
        ];

        foreach ($venues as $venue) {
            $exists = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM venue WHERE name = ?', [$venue['name']]);
            if (0 === $exists) {
                $this->addSql(
                    'INSERT INTO venue (name, address, city, state, zip_code, capacity, facilities) VALUES (?, ?, ?, ?, ?, ?, ?)',
                    [
                        $venue['name'],
                        $venue['address'],
                        $venue['city'],
                        $venue['state'],
                        $venue['zipCode'],
                        $venue['capacity'],
                        $venue['facilities'],
                    ]
                );
            }
        }
    }

    private function createMockRooms(): void
    {
        $rooms = [
            ['name' => 'Main Auditorium', 'capacity' => 2000, 'building' => 'A', 'floor' => '1', 'equipment' => 'Projector, Microphone, Sound System'],
            ['name' => 'Breakout Room A', 'capacity' => 500, 'building' => 'A', 'floor' => '2', 'equipment' => 'Projector, Whiteboard'],
            ['name' => 'Breakout Room B', 'capacity' => 500, 'building' => 'A', 'floor' => '2', 'equipment' => 'Projector, Whiteboard'],
            ['name' => 'Workshop Room', 'capacity' => 300, 'building' => 'B', 'floor' => '1', 'equipment' => 'Tables, WiFi'],
            ['name' => 'Networking Lounge', 'capacity' => 200, 'building' => 'B', 'floor' => '2', 'equipment' => 'Seating, Refreshments'],
        ];

        foreach ($rooms as $room) {
            $exists = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM room WHERE name = ?', [$room['name']]);
            if (0 === $exists) {
                $this->addSql(
                    'INSERT INTO room (name, capacity, building, floor, equipment) VALUES (?, ?, ?, ?, ?)',
                    [
                        $room['name'],
                        $room['capacity'],
                        $room['building'],
                        $room['floor'],
                        $room['equipment'],
                    ]
                );
            }
        }
    }

    private function createMockConferences(): void
    {
        // Get organizer IDs
        $organizers = $this->connection->fetchAllAssociative('SELECT id, first_name FROM "user" WHERE role = ? LIMIT 3', ['ORGANIZER']);

        $conferences = [
            [
                'name' => 'Tech Summit 2026',
                'description' => 'Annual technology conference featuring cutting-edge innovations',
                'location' => 'San Jose, CA',
                'startDate' => '2026-06-15',
                'endDate' => '2026-06-17',
                'status' => 'UPCOMING',
                'maxAttendees' => 5000,
                'isActive' => true,
                'organizerIndex' => 0,
            ],
            [
                'name' => 'AI & Machine Learning Conference',
                'description' => 'Deep dive into AI and ML trends for 2026',
                'location' => 'New York, NY',
                'startDate' => '2026-07-20',
                'endDate' => '2026-07-22',
                'status' => 'UPCOMING',
                'maxAttendees' => 3000,
                'isActive' => true,
                'organizerIndex' => 1,
            ],
            [
                'name' => 'Web Development Expo',
                'description' => 'Latest trends in web development and frontend frameworks',
                'location' => 'Chicago, IL',
                'startDate' => '2026-08-10',
                'endDate' => '2026-08-12',
                'status' => 'UPCOMING',
                'maxAttendees' => 2500,
                'isActive' => true,
                'organizerIndex' => 2,
            ],
        ];

        foreach ($conferences as $index => $conference) {
            $exists = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM conference WHERE name = ?', [$conference['name']]);
            if (0 === $exists) {
                $organizerId = $organizers[$conference['organizerIndex']]['id'] ?? 1;
                $this->addSql(
                    'INSERT INTO conference (name, description, location, start_date, end_date, status, create_at, max_attendees, is_active, created_at, organizer_id) 
                     VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, NOW(), ?)',
                    [
                        $conference['name'],
                        $conference['description'],
                        $conference['location'],
                        $conference['startDate'],
                        $conference['endDate'],
                        $conference['status'],
                        $conference['maxAttendees'],
                        $conference['isActive'] ? 1 : 0,
                        $organizerId,
                    ]
                );
            }
        }
    }

    private function createMockSessions(): void
    {
        $conferences = $this->connection->fetchAllAssociative('SELECT id, name FROM conference ORDER BY id');

        $sessions = [
            // Tech Summit sessions
            ['title' => 'Opening Keynote', 'description' => 'Welcome and future of tech', 'conferenceIndex' => 0, 'startTime' => '2026-06-15 09:00:00', 'endTime' => '2026-06-15 10:00:00'],
            ['title' => 'Cloud Computing Trends', 'description' => 'Exploring cloud infrastructure', 'conferenceIndex' => 0, 'startTime' => '2026-06-15 10:30:00', 'endTime' => '2026-06-15 11:30:00'],
            ['title' => 'DevOps Best Practices', 'description' => 'Modern DevOps strategies', 'conferenceIndex' => 0, 'startTime' => '2026-06-15 14:00:00', 'endTime' => '2026-06-15 15:00:00'],

            // AI Conference sessions
            ['title' => 'Introduction to AI', 'description' => 'AI fundamentals and concepts', 'conferenceIndex' => 1, 'startTime' => '2026-07-20 09:00:00', 'endTime' => '2026-07-20 10:00:00'],
            ['title' => 'Deep Learning Workshop', 'description' => 'Hands-on deep learning', 'conferenceIndex' => 1, 'startTime' => '2026-07-20 10:30:00', 'endTime' => '2026-07-20 12:00:00'],
            ['title' => 'NLP Applications', 'description' => 'Natural Language Processing use cases', 'conferenceIndex' => 1, 'startTime' => '2026-07-20 13:00:00', 'endTime' => '2026-07-20 14:00:00'],

            // Web Dev sessions
            ['title' => 'React 19 Features', 'description' => 'New React capabilities', 'conferenceIndex' => 2, 'startTime' => '2026-08-10 09:00:00', 'endTime' => '2026-08-10 10:00:00'],
            ['title' => 'TypeScript Mastery', 'description' => 'Advanced TypeScript patterns', 'conferenceIndex' => 2, 'startTime' => '2026-08-10 10:30:00', 'endTime' => '2026-08-10 11:30:00'],
            ['title' => 'Web Performance', 'description' => 'Optimizing web applications', 'conferenceIndex' => 2, 'startTime' => '2026-08-10 13:00:00', 'endTime' => '2026-08-10 14:00:00'],
        ];

        foreach ($sessions as $session) {
            $exists = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM session WHERE title = ?', [$session['title']]);
            if (0 === $exists) {
                $conferenceId = $conferences[$session['conferenceIndex']]['id'] ?? 1;
                $this->addSql(
                    'INSERT INTO session (title, description, conference_id, start_time, end_time, max_attendees, session_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                    [
                        $session['title'],
                        $session['description'],
                        $conferenceId,
                        $session['startTime'],
                        $session['endTime'],
                        100,
                        'PRESENTATION',
                        'SCHEDULED',
                    ]
                );
            }
        }
    }

    private function createMockSpeakers(): void
    {
        $speakers = $this->connection->fetchAllAssociative('SELECT id FROM "user" WHERE role = ?', ['SPEAKER']);
        $sessions = $this->connection->fetchAllAssociative('SELECT id FROM session ORDER BY id');

        if (empty($speakers) || empty($sessions)) {
            return;
        }

        // Assign speakers to sessions (2 speakers per session)
        foreach ($sessions as $index => $session) {
            $speakerIndex1 = $index % count($speakers);
            $speakerIndex2 = ($index + 1) % count($speakers);

            $existsCheck1 = (int) $this->connection->fetchOne(
                'SELECT COUNT(*) FROM session_speaker WHERE session_id = ? AND speaker_id = ?',
                [$session['id'], $speakers[$speakerIndex1]['id']]
            );

            if (0 === $existsCheck1) {
                $this->addSql(
                    'INSERT INTO session_speaker (session_id, speaker_id) VALUES (?, ?)',
                    [$session['id'], $speakers[$speakerIndex1]['id']]
                );
            }

            $existsCheck2 = (int) $this->connection->fetchOne(
                'SELECT COUNT(*) FROM session_speaker WHERE session_id = ? AND speaker_id = ?',
                [$session['id'], $speakers[$speakerIndex2]['id']]
            );

            if (0 === $existsCheck2) {
                $this->addSql(
                    'INSERT INTO session_speaker (session_id, speaker_id) VALUES (?, ?)',
                    [$session['id'], $speakers[$speakerIndex2]['id']]
                );
            }
        }
    }

    private function createMockRegistrations(): void
    {
        $conferences = $this->connection->fetchAllAssociative('SELECT id FROM conference ORDER BY id');
        $attendees = $this->connection->fetchAllAssociative('SELECT id FROM "user" WHERE role = ?', ['ATTENDEE']);

        if (empty($conferences) || empty($attendees)) {
            return;
        }

        // Create registrations
        foreach ($conferences as $conference) {
            // Create 3 registrations per conference
            foreach (range(0, 2) as $index) {
                $attendeeId = $attendees[$index % count($attendees)]['id'];
                $registrationStatus = ['PENDING', 'CONFIRMED', 'COMPLETED'][$index % 3];

                $exists = (int) $this->connection->fetchOne(
                    'SELECT COUNT(*) FROM registration WHERE conference_id = ? AND status = ?',
                    [$conference['id'], $registrationStatus]
                );

                if (0 === $exists) {
                    $this->addSql(
                        'INSERT INTO registration (conference_id, registration_date, status, ticket_type) VALUES (?, NOW(), ?, ?)',
                        [
                            $conference['id'],
                            $registrationStatus,
                            ['STANDARD', 'VIP', 'PREMIUM'][$index % 3],
                        ]
                    );

                    // Link attendee to the registration
                    $registrationId = $this->connection->lastInsertId();
                    $this->addSql(
                        'INSERT INTO registration_user (registration_id, user_id) VALUES (?, ?)',
                        [$registrationId, $attendeeId]
                    );
                }
            }
        }

        // Link registrations to conferences
        $registrations = $this->connection->fetchAllAssociative('SELECT id FROM registration ORDER BY id');
        foreach ($registrations as $index => $registration) {
            $conferenceId = $conferences[$index % count($conferences)]['id'];
            $exists = (int) $this->connection->fetchOne(
                'SELECT COUNT(*) FROM conference_registration WHERE conference_id = ? AND registration_id = ?',
                [$conferenceId, $registration['id']]
            );

            if (0 === $exists) {
                $this->addSql(
                    'INSERT INTO conference_registration (conference_id, registration_id) VALUES (?, ?)',
                    [$conferenceId, $registration['id']]
                );
            }
        }
    }
}
