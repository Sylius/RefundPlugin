@refunds
Feature: Refunding a single order unit with taxes applied
    In order to give back money to Customer for one of the bought products with tax applied
    As an Administrator
    I want to be able to refund a single order unit

    Background:
        Given the store operates on a single green channel in "United States"
        And default tax zone is "US"
        And the store has "US VAT" tax rate of 10% for "Clothes" within the "US" zone
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And it belongs to "Clothes" tax category
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator
        And the order "#00000022" is already paid

    @ui @application
    Scenario: Refunding one of the order unit with tax applied
        When I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment
        Then I should be notified that selected order units have been successfully refunded
        And this order refunded total should be "$11.00"
