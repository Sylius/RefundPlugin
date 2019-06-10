@refunds
Feature: Preselecting refund payment method based on order's payment
    As an Administrator
    In order to speed up refunding process
    I want to have refund payment method preselected

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
    Scenario: When refunding, the selected payment should be the first one of the corresponding order
        When I want to refund some units of order "#00000022"
        Then the selected refund payment method should be "Space money"
