@refunds
Feature: Refunding a part of an shipment
    In order to give back part of the money to Customer for shipping fee
    As an Administrator
    I want to be able to refund an order shipment

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store has "Galaxy Post" shipping method with "$20.00" fee
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator
        And the order "#00000022" is already paid

    @ui @application
    Scenario: Refunding part of the order shipment
        When I want to refund some units of order "#00000022"
        And I decide to refund "$8.00" from order shipment with "Space money" payment
        Then I should be notified that selected order units have been successfully refunded
        And this order refunded total should be "$8.00"
        And I should still be able to refund order shipment with "Space money" payment

    @ui @application
    Scenario: Refunding the whole order unit price after partial refund
        Given the "#00000022" order's shipping cost already has a refund of "$5.00" with "Space money" payment
        When I want to refund some units of order "#00000022"
        And I decide to refund order shipment with "Space money" payment
        Then this order refunded total should be "$20.00"

    @ui @application
    Scenario: Not being able to refund more money than shipment total
        Given the "#00000022" order's shipping cost already has a refund of "$5.00" with "Space money" payment
        When I want to refund some units of order "#00000022"
        And I try to refund "$18.00" from order shipment with "Space money" payment
        Then I should be notified that I cannot refund more money than the shipment total
        And this order refunded total should still be "$5.00"

    @ui @application
    Scenario: Not being able to refund less than allowed shipment amount
        When I want to refund some units of order "#00000022"
        And I try to refund "-$18.00" from order shipment with "Space money" payment
        Then I should be notified that refunded amount should be greater than 0
        And this order refunded total should be "$0.00"
