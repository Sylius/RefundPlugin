@refunds
Feature: Refunding an order shipping cost
    In order to give back money spent by Customer for order shipment
    As an Administrator
    I want to be able to refund an order shipping cost

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store has "Galaxy Post" shipping method with "$20.00" fee
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator

    @ui @application
    Scenario: Refunding an order shipment
        When I want to refund some units of order "#00000022"
        And I decide to refund order shipment
        Then I should be notified that order shipment has been successfully refunded
        And this order refunded total should be "$20.00"
        And I should not be able to refund order shipment

    @ui @application
    Scenario: Refunding and order shipment along with order unit
        When I want to refund some units of order "#00000022"
        And I decide to refund order shipment and 1st "Mr. Meeseeks T-Shirt" product
        Then I should be notified that order shippment has been successfully refunded
        Then I should be notified that selected order units have been successfully refunded
        And this order refunded total should be "$30.00"
        And I should not be able to refund order shipment
        And I should not be able to refund 1st unit with product "Mr. Meeseeks T-Shirt"
