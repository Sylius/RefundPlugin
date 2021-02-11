@refunds
Feature: Refunding an order shipping cost
    In order to give back money spent by Customer for order shipment
    As an Administrator
    I want to be able to refund an order shipping cost

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has "VAT" tax rate of 23% for "Pidgeons Services" within the "US" zone
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store has "Galaxy Post" shipping method with "$20.00" fee
        And the store has "Space Pidgeons Post" shipping method with "$10.00" fee within the "US" zone
        And shipping method "Space Pidgeons Post" belongs to "Pidgeons Services" tax category
        And there is a promotion "50% shipping discount"
        And it gives "50%" discount on shipping to every order
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And the order "#00000022" is already paid
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000023"
        And the customer bought a single "Mr. Meeseeks T-Shirt"
        And the customer chose "Space Pidgeons Post" shipping method to "United States" with "Space money" payment
        And the order "#00000023" is already paid
        And I am logged in as an administrator

    @ui @application @todo
    Scenario: Refunding an order shipping cost with a promotion applied
        When I want to refund some units of order "#00000022"
        And I decide to refund order shipment with "Space money" payment
        Then this order refunded total should be "$10.00"
        And I should not be able to refund order shipment

    @ui @application @todo
    Scenario: Refunding an order shipping cost with taxes and a promotion applied
        When I want to refund some units of order "#00000023"
        And I decide to refund order shipment with "Space money" payment
        Then this order refunded total should be "$6.15"
        And I should not be able to refund order shipment
