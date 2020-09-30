@refunds
Feature: Having credit memo sent to customer
    In order to provide refund details to customer immediately after refund
    As an Administrator
    I want to be have credit memo file sent automatically to a customer

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator
        And the order "#00000022" is already paid

    @application @email
    Scenario: Having credit memo file sent to a customer
        When I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment
        Then I should be notified that selected order units have been successfully refunded
        And email to "rick.sanchez@wubba-lubba-dub-dub.com" with credit memo should be sent

    @ui @email
    Scenario: Not sending email with credit memo if the refund validation fails
        When I want to refund some units of order "#00000022"
        And I refund zero items
        Then I should be notified that at least one unit should be selected to refund
        And email to "rick.sanchez@wubba-lubba-dub-dub.com" with credit memo should not be sent
