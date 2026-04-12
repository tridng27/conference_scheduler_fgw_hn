<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260412103000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed default admin account';
    }

    public function up(Schema $schema): void
    {
        $email = 'Admin@gmail.com';
        $exists = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM "user" WHERE email = ?', [$email]);

        if (0 === $exists) {
            $passwordHash = password_hash('admin@123', PASSWORD_BCRYPT);

            $this->addSql(
                'INSERT INTO "user" (email, roles, password, first_name, last_name, phone, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
                [
                    $email,
                    json_encode(['ROLE_ADMIN']),
                    $passwordHash,
                    'System',
                    'Administrator',
                    null,
                    'ADMIN',
                ]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM "user" WHERE email = ?', ['Admin@gmail.com']);
    }
}
