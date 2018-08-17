@refunds
Feature: Marking refund payment as paid
    In order have consistent data on refund payments' status
    As an Administrator
    I want to mark refund payment as paid

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator
        And the order "#00000022" is already paid
        And I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment

    Scenario: Marking refund payment as paid
        Given I view the summary of the order "#00000022"
        When I mark the first refund payment as "Paid"
        Then I should see 1 refund payment with status "Paid"
