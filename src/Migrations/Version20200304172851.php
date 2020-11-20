<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200304172851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Updates sylius_refund_payment state values to new schema';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE sylius_refund_payment SET state = "new" WHERE state = "New"');
        $this->addSql('UPDATE sylius_refund_payment SET state = "completed" WHERE state = "Completed"');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE sylius_refund_payment SET state = "New" WHERE state = "new"');
        $this->addSql('UPDATE sylius_refund_payment SET state = "Completed" WHERE state = "completed"');
    }
}
