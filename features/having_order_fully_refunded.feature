@refunds
Feature: Having order fully refunded
    In order to be aware of the order being fully refunded
    As an Administrator
    I want to see that the order is in Fully Refunded state

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store has "Galaxy Post" shipping method with "$10.00" fee
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 3 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator
        And the order "#00000022" is already paid
        And all units from the order "#00000022" are refunded
        And the shipping of the order "#00000022" is refunded

    @ui
    Scenario: Having order fully refunded when both items and shipping are refunded
        When I view the summary of the order "#00000022"
        Then its state should be "Fully refunded"
