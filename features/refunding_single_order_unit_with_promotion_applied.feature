@refunds
Feature: Refunding a single order unit with promotion applied
    In order to give back money to Customer for one of the bought products with discount applied
    As an Administrator
    I want to be able to refund a single order unit

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And there is a promotion "Anatomy Park Promotion"
        And this promotion gives "$1" off on every product with minimum price at "$5.00"
        And I am logged in as an administrator

    @ui @todo
    Scenario: Refunding one of the order unit with discount applied
        When I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product
        Then I should be notified that order unit has been successfully refunded
        And refunded total should be "$9"
