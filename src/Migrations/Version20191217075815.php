<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191217075815 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX IDX_5C4F3331989A8203 ON sylius_refund_credit_memo');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo CHANGE orderNumber order_number VARCHAR(255) NOT NULL, CHANGE currencyCode currency_code VARCHAR(255) NOT NULL, CHANGE localeCode locale_code VARCHAR(255) NOT NULL');
        $this->addSql('CREATE INDEX IDX_5C4F3331551F0F81 ON sylius_refund_credit_memo (order_number)');
        $this->addSql('ALTER TABLE sylius_refund_refund CHANGE ordernumber order_number VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX IDX_5C4F3331551F0F81 ON sylius_refund_credit_memo');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo CHANGE order_number orderNumber VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, CHANGE currency_code currencyCode VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, CHANGE locale_code localeCode VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('CREATE INDEX IDX_5C4F3331989A8203 ON sylius_refund_credit_memo (orderNumber)');
        $this->addSql('ALTER TABLE sylius_refund_refund CHANGE order_number orderNumber VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
