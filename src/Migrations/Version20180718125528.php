<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180718125528 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_refund_refund ADD type VARCHAR(255) NOT NULL, CHANGE refundedunitid refunded_unit_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DEF86A0EE8F826668CDE5729 ON sylius_refund_refund (refunded_unit_id, type)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_DEF86A0EE8F826668CDE5729 ON sylius_refund_refund');
        $this->addSql('ALTER TABLE sylius_refund_refund DROP type, CHANGE refunded_unit_id refundedUnitId INT DEFAULT NULL');
    }
}
