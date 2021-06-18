@refunds
Feature: Seeing an original payment method from completed payment on the refund page
    In order to choose proper payment method while refunding
    As an Administrator
    I want to see the original payment method of completed payment

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And the store also allows paying with "Mars money"
        And there is a customer "morty.smith@look-at-me.com" that placed an order "#00000023"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And the payment of order "#00000023" failed
        And the customer chose "Mars money" payment method
        And this payment has been paid
        And I am logged in as an administrator

    @ui
    Scenario: Seeing original payment method of completed payment
        When I want to refund some units of order "#00000023"
        Then I should see original payment method "Mars money"
