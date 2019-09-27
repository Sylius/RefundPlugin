@refunds
Feature: Refunding order units
    In order to give back money for returned products to the Customer
    As an Administrator
    I want to be able to prepare a refund from an accepted return request

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a product "Witcher Sword" priced at "$33.68"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "geralt@netflix.com" that placed an order "#0000001"
        And there is a promotion "Razor Promotion"
        And it gives "$1.89" discount to every order
        And the customer bought 3 "Witcher Sword" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And this order is already paid
        And I am logged in as an administrator

    @ui
    Scenario: Refund all items from order
        When I want to refund some units of order "#0000001"
        And I decide to refund 3 "Witcher Sword" products with "Space money" payment
        Then this order refunded total should be "$99.15"
