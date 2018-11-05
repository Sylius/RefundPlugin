@refunds
Feature: Completing refund payment
    In order have consistent data on refund payments' status
    As an Administrator
    I want to complete refund payment

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
        And I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment

    @ui
    Scenario: Completing refund payment
        When I view the summary of the order "#00000022"
        And I complete the first refund payment
        Then I should be notified that refund payment has been successfully completed
        Then I should see 1 refund payment with status "Completed"

    @ui
    Scenario: Being unable to complete already completed payment
        When I view the summary of the order "#00000022"
        And I complete the first refund payment
        Then I should not be able to complete the first refund payment again
