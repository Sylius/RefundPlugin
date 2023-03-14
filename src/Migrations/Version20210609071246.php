<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210609071246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change order_number to order_id on sylius_refund_refund_payment';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_refund_refund_payment ADD order_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_refund_refund_payment ADD CONSTRAINT FK_EC283EA58D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)');
        $this->addSql('CREATE INDEX IDX_EC283EA58D9F6D38 ON sylius_refund_refund_payment (order_id)');

        $this->addSql('
            UPDATE sylius_refund_refund_payment
            SET sylius_refund_refund_payment.order_id = (
                SELECT sylius_order.id FROM sylius_order WHERE sylius_order.number = sylius_refund_refund_payment.order_number
            )
        ');

        $this->addSql('ALTER TABLE sylius_refund_refund_payment DROP order_number');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_refund_refund_payment DROP FOREIGN KEY FK_EC283EA58D9F6D38');
        $this->addSql('DROP INDEX IDX_EC283EA58D9F6D38 ON sylius_refund_refund_payment');
        $this->addSql('ALTER TABLE sylius_refund_refund_payment ADD order_number VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');

        $this->addSql(
            '
            UPDATE sylius_refund_refund_payment
            SET sylius_refund_refund_payment.order_number = (
                SELECT sylius_order.number FROM sylius_order WHERE sylius_order.id = sylius_refund_refund_payment.order_id
            )',
        );

        $this->addSql('ALTER TABLE sylius_refund_refund_payment DROP order_id');
    }
}
