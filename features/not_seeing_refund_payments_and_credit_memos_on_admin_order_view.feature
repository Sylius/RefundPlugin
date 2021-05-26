@refunds
Feature: Not seeing refund payments and credit memos on admin order view
    In order to have consistent documents and payment in a refunded order
    As an Administrator
    I don't want to have a credit memo or refund payment generated alone

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store has a product "Angel T-Shirt" priced at "$5.00"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "morty.smith@look-at-me.com" that placed an order "#00000023"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And the order "#00000023" is already paid
        And I am logged in as an administrator

    @ui
    Scenario: Not seeing credit memo and refund payment on order view if credit memo generation failed
        Given the credit memo generation is broken
        And I decided to refund 1st "Mr. Meeseeks T-Shirt" product of the order "00000023" with "Space money" payment
        When I view the summary of the order "#00000023"
        Then I should not see any refund payments
        And I should not see any credit memos

    @ui
    Scenario: Not seeing credit memo and refund payment on order view if refund generation failed
        Given the refund payment generation is broken
        And I decided to refund 1st "Mr. Meeseeks T-Shirt" product of the order "00000023" with "Space money" payment
        When I view the summary of the order "#00000023"
        Then I should not see any refund payments
        And I should not see any credit memos
