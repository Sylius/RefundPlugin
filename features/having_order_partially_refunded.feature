@refunds
Feature: Having order partially refunded
    In order to note that part of the order total is refunded
    As an Administrator
    I want to be aware of the order payment state being partially refunded

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

    @ui
    Scenario: Having order partially refunded when some items are refunded
        When 1st "Mr. Meeseeks T-Shirt" product from order "#00000022" has already been refunded with "Space money" payment
        Then this order's payment state should be "Partially refunded"

    @ui
    Scenario: Having order partially refunded when its shipping is refunded
        Given the "#00000022" order's shipping cost already has a refund of "$1.00" with "Space money" payment
        Then this order's payment state should be "Partially refunded"
