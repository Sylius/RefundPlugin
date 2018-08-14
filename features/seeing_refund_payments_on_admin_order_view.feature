@refunds
Feature: Seeing refund payments on admin order view
    In order to see every payment related to user's credit memos
    As an Administrator
    I want to be able to browse them on order view

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
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product

    Scenario: Seeing refund payment on order view
        When I view the summary of the order "#00000022"
        Then I should see 1 refund payment with status "New"
