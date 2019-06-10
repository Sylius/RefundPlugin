<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Application;

use Behat\Behat\Context\Context;
use Tests\Sylius\RefundPlugin\Behat\Services\Provider\MessagesProvider;
use Webmozart\Assert\Assert;

final class EmailsContext implements Context
{
    /** @var MessagesProvider */
    private $messagesProvider;

    public function __construct(MessagesProvider $messagesProvider)
    {
        $this->messagesProvider = $messagesProvider;
    }

    /**
     * @Then an email with credit memo should be sent again to :email
     */
    public function quantityOfEmailsForCustomerWithCreditMemo(string $email): void
    {
        Assert::same($this->countMessagesToCustomer($email, 'Some of the units from your order have been refunded.'), 2);
        Assert::true($this->lastEmailBodyContains('Some of the units from your order have been refunded.'));
    }

    private function countMessagesToCustomer(string $email, string $messageBody): int
    {
        $counter = 0;
        /** @var \Swift_Message $message */
        foreach ($this->messagesProvider->getMessages() as $message) {
            if (array_key_exists($email, $message->getTo()) && false !== strpos($message->getBody(), $messageBody )) {
                $counter++;
            }
        }

        return $counter;
    }

    private function lastEmailBodyContains(string $message): bool
    {
        /** @var \Swift_Message|array $messages */
        $messages = $this->messagesProvider->getMessages();

        return false !== strpos(end($messages)->getBody(), $message);
    }
}
