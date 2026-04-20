<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260420140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Assign speakers to all sessions';
    }

    public function up(Schema $schema): void
    {
        // Assign speakers to all sessions
        $speakers = $this->connection->fetchAllAssociative('SELECT id FROM speaker ORDER BY id');
        $sessions = $this->connection->fetchAllAssociative('SELECT id FROM session ORDER BY id');

        if (!empty($speakers) && !empty($sessions)) {
            foreach ($sessions as $index => $session) {
                // Check if session already has speakers
                $existing = $this->connection->fetchOne(
                    'SELECT COUNT(*) FROM session_speaker WHERE session_id = ?',
                    [$session['id']]
                );

                if ($existing == 0) {
                    // Assign 1-2 speakers to each session
                    $speakerIndex1 = $index % count($speakers);
                    $speakerIndex2 = ($index + 1) % count($speakers);

                    $this->addSql(
                        'INSERT INTO session_speaker (session_id, speaker_id) VALUES (?, ?)',
                        [$session['id'], $speakers[$speakerIndex1]['id']]
                    );

                    // Add second speaker for some sessions
                    if ($index % 2 == 0) {
                        $this->addSql(
                            'INSERT INTO session_speaker (session_id, speaker_id) VALUES (?, ?)',
                            [$session['id'], $speakers[$speakerIndex2]['id']]
                        );
                    }
                }
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM session_speaker');
    }
}
