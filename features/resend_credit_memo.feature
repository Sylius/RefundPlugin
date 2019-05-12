@refunds
Feature: Resend credit memo
    In order to inform customer about generate credit memo
    As an Administrator
    I want to be able to sent credit memo more that one time.

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store has a product "Portal Gun" priced at "$20.00"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought a single "Mr. Meeseeks T-Shirt"
        And the customer bought a single "Portal Gun"
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And the order "#00000022" is already paid
        And 1st "Mr. Meeseeks T-Shirt" product from order "#00000022" has already been refunded with "Space money" payment
        And 1st "Portal Gun" product from order "#00000022" has already been refunded with "Space money" payment
        And I am logged in as an administrator

    @ui
    Scenario: resend credit memo to a customer
        When I browse credit memos
        And I resend credit memo from order "#00000022"
        Then email to "rick.sanchez@wubba-lubba-dub-dub.com" with credit memo should be sent
