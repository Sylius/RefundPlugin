@refunds
Feature: Providing comment for credit memo
    In order to have more information or clarification provided with a credit memo
    As an Administrator
    I want to be able to add credit memo comment during order refund

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator
        And the order "#00000022" is already paid

    @ui @application
    Scenario: Providing comment for credit memo
        When I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment and "Money for nothing" comment
        And I browse the details of the only credit memo generated for order "#00000022"
        Then this credit memo should contain 1 "Mr. Meeseeks T-Shirt" product with "$0.90" tax applied
        And its total should be "$9.90"
        And it should be commented with "Money for nothing"
