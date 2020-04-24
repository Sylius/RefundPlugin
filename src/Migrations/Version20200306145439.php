<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200306145439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Makes CreditMemo number unique and issued_at non nullable';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_refund_credit_memo CHANGE issued_at issued_at DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C4F333196901F54 ON sylius_refund_credit_memo (number)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_5C4F333196901F54 ON sylius_refund_credit_memo');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo CHANGE issued_at issued_at DATETIME DEFAULT NULL');
    }
}
