@refunds
Feature: Refunding multiple order units
    In order to give back money for some of the bought products to Customer
    As an Administrator
    I want to be able to refund multiple order units from multiple items

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store has a product "Portal gun" priced at "$100.00"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer bought 3 "Portal gun" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator

    @ui
    Scenario: Refunding multiple order units
        Given the order "#00000022" is already paid
        When I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" and 1st "Portal gun" products with "Space money" payment
        Then I should be notified that selected order units have been successfully refunded
        And this order refunded total should be "$110.00"
        And I should not be able to refund 1st unit with product "Mr. Meeseeks T-Shirt"
        And I should not be able to refund 1st unit with product "Portal gun"
