<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200310094633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Split sylius_refund_customer_billing_data customer_name into first_name and last_name';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_refund_customer_billing_data ADD last_name VARCHAR(255) NOT NULL, CHANGE customer_name first_name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_refund_customer_billing_data CHANGE first_name customer_name VARCHAR(255) NOT NULL, DROP last_name');
    }
}
