@refunds
Feature: Providing comment for credit memo
    In order to have more information or clarification provided with a credit memo
    As an Administrator
    I want to be able to add credit memo comment during order refund

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

    @ui @application
    Scenario: Providing comment for credit memo
        When I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment and "Money for nothing" comment
        And I browse the details of the only credit memo generated for order "#00000022"
        Then this credit memo should contain 1 "Mr. Meeseeks T-Shirt" product with "$0.00" tax applied
        And its total should be "$10.00"
        And it should be commented with "Money for nothing"

    @ui @application
    Scenario: Credit memo comment validation
        When I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment and very long comment
        Then I should be notified that credit memo comment is too long
        And there should be no refund payments for order "#00000022"
        And there should be no credit memos generated for order "#00000022"
