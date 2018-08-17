@refunds
Feature: Having credit memo generated
    In order to have official confirmation of some order units refund
    As an Administrator
    I want to have credit memo generated for each order refund action

    Background:
        Given the store operates on a single channel in "United States"
        And default tax zone is "US"
        And the store has "US VAT" tax rate of 10% for "Clothes" within the "US" zone
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10"
        And it belongs to "Clothes" tax category
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a promotion "Anatomy Park Promotion"
        And this promotion gives "$1.00" off on every product with minimum price at "$5.00"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator
        And the order "#00000022" is already paid

    @ui @application
    Scenario: Having credit memo generated after refund process
        When I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment
        Then I should be notified that selected order units have been successfully refunded
        And I should have 1 credit memo generated for order "#00000022"

    @ui @application
    Scenario: Seeing the details of generated credit memo
        Given 1st "Mr. Meeseeks T-Shirt" product from order "#00000022" has already been refunded
        And I browse the details of the only credit memo generated for order "#00000022"
        And it should have sequential number generated from current date
        Then this credit memo should contain 1 "Mr. Meeseeks T-Shirt" product with "$0.90" tax applied
        And its total should be "$9.90"
