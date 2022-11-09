@refunds
Feature: Validating refunding zero items
    In order not to make mistake during refunding
    As an Administrator
    I want not to be able to refund when none of items has been selected

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator

    @ui @email
    Scenario: Being unable to refund zero items
        Given the order "#00000022" is already paid
        When I want to refund some units of order "#00000022"
        And I refund zero items
        Then I should be notified that at least one unit should be selected to refund
        And the customer "rick.sanchez@wubba-lubba-dub-dub.com" should not receive an email that some units have been refunded
