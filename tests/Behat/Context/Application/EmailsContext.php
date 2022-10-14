<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Application;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\Checker\EmailCheckerInterface as BehatEmailCheckerInterface;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface as CoreEmailCheckerInterface;
use Webmozart\Assert\Assert;

final class EmailsContext implements Context
{
    public function __construct(private BehatEmailCheckerInterface|CoreEmailCheckerInterface $emailChecker)
    {
    }

    /**
     * @Then an email with credit memo should be sent again to :email
     */
    public function quantityOfEmailsForCustomerWithCreditMemo(string $email): void
    {
        Assert::same($this->emailChecker->countMessagesTo($email), 2);
        Assert::true(
            $this->emailChecker->hasMessageTo(
                'Some of the units from your order have been refunded.',
                $email
            ),
            'Some of the units from your order have been refunded.'
        );
    }
}
