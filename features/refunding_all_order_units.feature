@refunds
Feature: Refunding all order units
    In order to give back money for all of the bought products to Customer
    As an Administrator
    I want to be able to refund all order units

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store has "Galaxy Post" shipping method with "$10.00" fee
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 3 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator
        And the order "#00000022" is already paid

    @ui @javascript
    Scenario: Refunding all order units
        When I want to refund some units of order "#00000022"
        And I decide to refund all units of this order with "Space money" payment
        Then I should be notified that selected order units have been successfully refunded
        And I should not be able to refund anything
        And this order refunded total should be "$40.00"
        But I should not be able to refund 1st unit with product "Mr. Meeseeks T-Shirt"
        And I should not be able to refund 2nd unit with product "Mr. Meeseeks T-Shirt"
        And I should not be able to refund 3rd unit with product "Mr. Meeseeks T-Shirt"
        And I should not be able to refund order shipment
