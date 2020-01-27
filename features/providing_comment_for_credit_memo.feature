@refunds
Feature: Providing comment for credit memo
    In order to have more information or clarification provided with a credit memo
    As an Administrator
    I want to be able to add credit memo comment during order refund

    Background:
        Given the store operates on a single green channel in "United States"
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
        Then it should contain 1 "Mr. Meeseeks T-Shirt" product with "10.00" net value, "0.00" tax amount and "10.00" gross value in "USD" currency
        And its total should be "10.00" in "USD" currency
        And it should be commented with "Money for nothing"

    @ui
    Scenario: Providing comment for credit memo
        When I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment and a very long comment
        Then I should be notified that selected order units have been successfully refunded
        And I should have 1 credit memo generated for order "#00000022"
        And I should see 1 refund payment with status "New"
