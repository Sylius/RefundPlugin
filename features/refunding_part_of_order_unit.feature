@refunds
Feature: Refunding a part of an order unit
    In order to give back part of the money to Customer for one of the bought products
    As an Administrator
    I want to be able to refund a single order unit

    Background:
        Given the store operates on a single green channel in "United States"
        And default tax zone is "US"
        And the store has "US VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And it belongs to "Clothes" tax category
        And the store has "Galaxy Post" shipping method with "$20.00" fee
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator
        And the order "#00000022" is already paid

    @ui @application
    Scenario: Refunding part of the order unit with tax applied
        When I want to refund some units of order "#00000022"
        And I decide to refund "$8.00" from 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment
        Then I should be notified that selected order units have been successfully refunded
        And this order refunded total should be "$8.00"
        And 1st "Mr. Meeseeks T-Shirt" product should have "$8.00" refunded

    @ui @application
    Scenario: Refunding the whole order unit price after partial refund
        Given the 1st "Mr. Meeseeks T-Shirt" product from order "#00000022" has a refund of "$5.00" with "Space money" payment
        When I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment
        Then I should be notified that selected order units have been successfully refunded
        And this order refunded total should be "$12.30"

    @ui @javascript
    Scenario: Refunding the whole order price after partial refund
        Given the 1st "Mr. Meeseeks T-Shirt" product from order "#00000022" has a refund of "$5.00" with "Space money" payment
        When I want to refund some units of order "#00000022"
        And I decide to refund all units of this order with "Space money" payment
        Then I should be notified that selected order units have been successfully refunded
        And this order refunded total should be "$44.60"

    @ui @application
    Scenario: Not being able to refund more money than order unit total
        Given the 1st "Mr. Meeseeks T-Shirt" product from order "#00000022" has a refund of "$5.00" with "Space money" payment
        When I want to refund some units of order "#00000022"
        And I decide to refund "$10.00" from 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment
        Then I should be notified that I cannot refund more money than the order unit total
        And this order refunded total should still be "$5.00"

    @ui @application
    Scenario: Not being able to refund less than allowed unit amount
        When I want to refund some units of order "#00000022"
        And I decide to refund "-$8.00" from 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment
        Then I should be notified that refunded amount should be greater than 0
        And this order refunded total should be "$0.00"
