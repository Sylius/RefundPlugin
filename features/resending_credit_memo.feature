@refunds
Feature: Resending credit memo
    In order to provide a generated credit memo to the customer
    As an Administrator
    I want to be able to send credit memo more than once

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store has a product "Portal Gun" priced at "$20.00"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought a single "Mr. Meeseeks T-Shirt"
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And the order "#00000022" is already paid
        And 1st "Mr. Meeseeks T-Shirt" product from order "#00000022" has already been refunded with "Space money" payment
        And I am logged in as an administrator

    @ui @email
    Scenario: Resending credit memo to a customer
        When I browse credit memos
        And I resend credit memo from order "#00000022"
        Then an email with credit memo should be sent again to "rick.sanchez@wubba-lubba-dub-dub.com"
