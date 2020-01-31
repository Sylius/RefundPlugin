@refunds
Feature: Refunding a single order unit
    In order to give back money for one of the bought products to Customer
    As an Administrator
    I want to be able to refund a single order unit

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator

    @ui
    Scenario: Seeing available order units to refund
        Given the order "#00000022" is already paid
        When I want to refund some units of order "#00000022"
        Then I should be able to refund 2 "Mr. Meeseeks T-Shirt" products
        And I should be able to go back to order details

    @ui @application
    Scenario: Refunding one of the order unit
        Given the order "#00000022" is already paid
        When I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment
        Then I should be notified that selected order units have been successfully refunded
        And this order refunded total should be "$10.00"
        And I should not be able to refund 1st unit with product "Mr. Meeseeks T-Shirt"
        But I should still be able to refund 2nd unit with product "Mr. Meeseeks T-Shirt" with "Space money" payment

    @application
    Scenario: Not being able to refund already refunded unit
        Given the order "#00000022" is already paid
        And 1st "Mr. Meeseeks T-Shirt" product from order "#00000022" has already been refunded with "Space money" payment
        When I want to refund some units of order "#00000022"
        And I should not be able to refund 1st unit with product "Mr. Meeseeks T-Shirt"

    @ui
    Scenario: Not being able to refund 0 units
        Given the order "#00000022" is already paid
        When I want to refund some units of order "#00000022"
        And I refund zero items
        Then I should be notified that at least one unit should be selected to refund

    @ui
    Scenario: Not being able to refund unit from an order that is unpaid
        Given I am viewing the summary of the order "#00000022"
        When I try to refund some units of order "#00000022"
        Then I should be notified that the order should be paid

    @ui
    Scenario: Not being able to see refunds button
        When I view the summary of the order "#00000022"
        Then I should not see refunds button

    @ui
    Scenario: Being able to choose only offline payment methods
        Given the order "#00000022" is already paid
        And the store allows paying with "Another offline payment method"
        And the store has a payment method "ElonPay" with a code "IN_THRUST_WE_TRUST" and Paypal Express Checkout gateway
        When I want to refund some units of order "#00000022"
        Then I should be able to choose refund payment method
        And there should be "Space money" payment method
        And there should be "Another offline payment method" payment method
        And there should not be "ElonPay" payment method

    @application
    Scenario: Not being able to refund unit from an order that is unpaid
        When I want to refund some units of order "#00000022"
        Then I should not be able to refund 1st unit with product "Mr. Meeseeks T-Shirt"
