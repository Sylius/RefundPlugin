@refunds
Feature: Refunding an order shipping cost
    In order to give back money spent by Customer for order shipment
    As an Administrator
    I want to be able to refund an order shipping cost

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store has "Galaxy Post" shipping method with "$20.00" fee
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And the order "#00000022" is already paid
        And I am logged in as an administrator

    @ui @application
    Scenario: Refunding an order shipment
        When I want to refund some units of order "#00000022"
        And I decide to refund order shipment with "Space money" payment
        And this order refunded total should be "$20.00"
        And I should not be able to refund order shipment

    @ui @application
    Scenario: Refunding an order shipment along with order unit
        When I want to refund some units of order "#00000022"
        And I decide to refund order shipment and 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment
        Then I should be notified that selected order units have been successfully refunded
        And this order refunded total should be "$30.00"
        And I should not be able to refund order shipment
        And I should not be able to refund 1st unit with product "Mr. Meeseeks T-Shirt"
