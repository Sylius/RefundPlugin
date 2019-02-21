@refunds
Feature: Having order fully refunded
    In order to note that whole order total is refunded
    As an Administrator
    I want to have order fully refunded

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
        And all units and shipment from the order "#00000022" are refunded with "Space money" payment

    @ui
    Scenario: Having order fully refunded when both items and shipping are refunded
        When I browse orders
        Then the order "#00000022" should have order payment state "Refunded"
