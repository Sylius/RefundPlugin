<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190928200659 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_refund_shop_billing_data (id INT AUTO_INCREMENT NOT NULL, company VARCHAR(255) DEFAULT NULL, tax_id VARCHAR(255) DEFAULT NULL, country_code VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, postcode VARCHAR(255) DEFAULT NULL, id_credit_memo VARCHAR(255) NOT NULL,PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_refund_customer_billing_data (id INT AUTO_INCREMENT NOT NULL, customer_name VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, postcode VARCHAR(255) NOT NULL, country_code VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL, province_name VARCHAR(255) DEFAULT NULL, province_code VARCHAR(255) DEFAULT NULL, id_credit_memo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE sylius_refund_embeddable_backup (id INT AUTO_INCREMENT NOT NULL, id_credit_memo VARCHAR(255) NOT NULL, channel_code VARCHAR(255) NOT NULL, from_customer_name VARCHAR(255) NOT NULL, from_street VARCHAR(255) NOT NULL, from_postcode VARCHAR(255) NOT NULL, from_country_code VARCHAR(255) NOT NULL, from_city VARCHAR(255) NOT NULL, from_company VARCHAR(255) DEFAULT NULL, from_province_name VARCHAR(255) DEFAULT NULL, from_province_code VARCHAR(255) DEFAULT NULL, to_company VARCHAR(255) DEFAULT NULL, to_tax_id VARCHAR(255) DEFAULT NULL, to_country_code VARCHAR(255) DEFAULT NULL, to_street VARCHAR(255) DEFAULT NULL, to_city VARCHAR(255) DEFAULT NULL, to_postcode VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('
            INSERT INTO sylius_refund_embeddable_backup (id_credit_memo, channel_code, from_customer_name, from_street, from_postcode, from_country_code, from_city, from_company, from_province_name, from_province_code, to_company, to_tax_id, to_country_code, to_street, to_city, to_postcode)
            SELECT id, channel_code, from_customerName, from_street, from_postcode, from_countryCode, from_city, from_company, from_provinceName, from_provinceCode, to_company, to_taxId, to_countryCode, to_street, to_city, to_postcode
            FROM sylius_refund_credit_memo
        ');

        $this->addSql('ALTER TABLE sylius_refund_credit_memo ADD from_id INT DEFAULT NULL, ADD to_id INT DEFAULT NULL, ADD channel_id INT DEFAULT NULL, DROP channel_code, DROP channel_name, DROP channel_color, DROP from_customerName, DROP from_street, DROP from_postcode, DROP from_countryCode, DROP from_city, DROP from_company, DROP from_provinceName, DROP from_provinceCode, DROP to_company, DROP to_taxId, DROP to_countryCode, DROP to_street, DROP to_city, DROP to_postcode');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo ADD CONSTRAINT FK_5C4F333178CED90B FOREIGN KEY (from_id) REFERENCES sylius_refund_customer_billing_data (id)');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo ADD CONSTRAINT FK_5C4F333130354A65 FOREIGN KEY (to_id) REFERENCES sylius_refund_shop_billing_data (id)');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo ADD CONSTRAINT FK_5C4F333172F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C4F333178CED90B ON sylius_refund_credit_memo (from_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C4F333130354A65 ON sylius_refund_credit_memo (to_id)');
        $this->addSql('CREATE INDEX IDX_5C4F333172F5A1AA ON sylius_refund_credit_memo (channel_id)');

        $this->addSql('
            UPDATE sylius_refund_credit_memo
            INNER JOIN sylius_channel
            INNER JOIN sylius_refund_embeddable_backup
            SET sylius_refund_credit_memo.channel_id = sylius_channel.id
            WHERE sylius_channel.code = sylius_refund_embeddable_backup.channel_code
            AND sylius_refund_credit_memo.id = sylius_refund_embeddable_backup.id_credit_memo
        ');

        $this->addSql('
            INSERT INTO sylius_refund_shop_billing_data (company, tax_id, country_code, street, city, postcode, id_credit_memo)
            SELECT to_company, to_tax_id, to_country_code, to_street, to_city, to_postcode, id_credit_memo
            FROM sylius_refund_embeddable_backup
        ');
        $this->addSql('
            UPDATE sylius_refund_credit_memo
            INNER JOIN sylius_refund_shop_billing_data
            SET sylius_refund_credit_memo.to_id = sylius_refund_shop_billing_data.id
            WHERE sylius_refund_credit_memo.id = sylius_refund_shop_billing_data.id_credit_memo
        ');
        $this->addSql('ALTER TABLE sylius_refund_shop_billing_data DROP COLUMN id_credit_memo');

        $this->addSql('
            INSERT INTO sylius_refund_customer_billing_data (customer_name, street, postcode, country_code, city, company, province_name, province_code, id_credit_memo)
            SELECT from_customer_name, from_street, from_postcode, from_country_code, from_city, from_company, from_province_name, from_province_code, id_credit_memo
            FROM sylius_refund_embeddable_backup
        ');
        $this->addSql('
            UPDATE sylius_refund_credit_memo
            INNER JOIN sylius_refund_customer_billing_data
            SET sylius_refund_credit_memo.from_id = sylius_refund_customer_billing_data.id
            WHERE sylius_refund_credit_memo.id = sylius_refund_customer_billing_data.id_credit_memo
        ');
        $this->addSql('ALTER TABLE sylius_refund_customer_billing_data DROP COLUMN id_credit_memo');

        $this->addSql('DROP TABLE sylius_refund_embeddable_backup');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_refund_credit_memo DROP FOREIGN KEY FK_5C4F333130354A65');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo DROP FOREIGN KEY FK_5C4F333178CED90B');
        $this->addSql('DROP TABLE sylius_refund_shop_billing_data');
        $this->addSql('DROP TABLE sylius_refund_customer_billing_data');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo DROP FOREIGN KEY FK_5C4F333172F5A1AA');
        $this->addSql('DROP INDEX UNIQ_5C4F333178CED90B ON sylius_refund_credit_memo');
        $this->addSql('DROP INDEX UNIQ_5C4F333130354A65 ON sylius_refund_credit_memo');
        $this->addSql('DROP INDEX IDX_5C4F333172F5A1AA ON sylius_refund_credit_memo');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo ADD channel_code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD channel_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD channel_color VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD from_customerName VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD from_street VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD from_postcode VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD from_countryCode VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD from_city VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD from_company VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD from_provinceName VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD from_provinceCode VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD to_company VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD to_taxId VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD to_countryCode VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD to_street VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD to_city VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD to_postcode VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP from_id, DROP to_id, DROP channel_id');
    }
}
