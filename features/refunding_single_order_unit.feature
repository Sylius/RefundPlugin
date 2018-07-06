@refunds
Feature: Refunding a single order unit
    In order to give back money for one of the bought products to Customer
    As an Administrator
    I want to be able to refund a single order unit

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator

    @ui
    Scenario: Seeing available order units to refund
        When I want to refund some units of order "#00000022"
        Then I should be able to refund 2 "Mr. Meeseeks T-Shirt" products

    @ui @todo
    Scenario: Refunding one of the order unit
        When I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product
        Then I should be notified that order unit has been successfully refunded
        And refunded total should be "$10"
        And I should not be able to refund 1st "Mr. Meeseeks T-Shirt" product
        But I should still be able to refund 2nd "Mr. Meeseeks T-Shirt" product
