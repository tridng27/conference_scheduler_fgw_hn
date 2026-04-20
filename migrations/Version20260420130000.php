<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260420130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix speaker and room data relationships';
    }

    public function up(Schema $schema): void
    {
        // Clean up existing incorrect data
        $this->addSql('DELETE FROM session_speaker');
        $this->addSql('DELETE FROM session_room');
        $this->addSql('DELETE FROM speaker');

        // Create speaker entities for speaker users
        $speakerUsers = $this->connection->fetchAllAssociative('SELECT id, first_name, last_name, email FROM "user" WHERE role = ?', ['SPEAKER']);

        $companies = ['TechCorp', 'InnovateLabs', 'DataDynamics', 'CloudSolutions'];
        $jobTitles = ['Senior Developer', 'Tech Lead', 'Principal Engineer', 'Software Architect'];
        $expertise = ['JavaScript', 'Python', 'AI/ML', 'Cloud Computing', 'DevOps', 'React', 'Node.js'];

        foreach ($speakerUsers as $index => $user) {
            $this->addSql(
                'INSERT INTO speaker (name, email, company, job_title, bio, usser_id, expertise) VALUES (?, ?, ?, ?, ?, ?, ?)',
                [
                    $user['first_name'] . ' ' . $user['last_name'],
                    $user['email'],
                    $companies[$index % count($companies)],
                    $jobTitles[$index % count($jobTitles)],
                    'Experienced professional with expertise in ' . $expertise[$index % count($expertise)] . '. Passionate about sharing knowledge and mentoring others.',
                    $user['id'],
                    $expertise[$index % count($expertise)],
                ]
            );
        }

        // Assign speakers to sessions
        $speakers = $this->connection->fetchAllAssociative('SELECT id FROM speaker ORDER BY id');
        $sessions = $this->connection->fetchAllAssociative('SELECT id FROM session ORDER BY id');

        if (!empty($speakers) && !empty($sessions)) {
            foreach ($sessions as $index => $session) {
                $speakerIndex1 = $index % count($speakers);
                $speakerIndex2 = ($index + 1) % count($speakers);

                $this->addSql(
                    'INSERT INTO session_speaker (session_id, speaker_id) VALUES (?, ?)',
                    [$session['id'], $speakers[$speakerIndex1]['id']]
                );

                $this->addSql(
                    'INSERT INTO session_speaker (session_id, speaker_id) VALUES (?, ?)',
                    [$session['id'], $speakers[$speakerIndex2]['id']]
                );
            }
        }

        // Assign rooms to sessions
        $rooms = $this->connection->fetchAllAssociative('SELECT id FROM room ORDER BY id');

        if (!empty($rooms) && !empty($sessions)) {
            foreach ($sessions as $index => $session) {
                $roomIndex = $index % count($rooms);

                $this->addSql(
                    'INSERT INTO session_room (session_id, room_id) VALUES (?, ?)',
                    [$session['id'], $rooms[$roomIndex]['id']]
                );
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM session_speaker');
        $this->addSql('DELETE FROM session_room');
        $this->addSql('DELETE FROM speaker');
    }
}
