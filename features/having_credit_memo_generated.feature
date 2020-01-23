@refunds
Feature: Having credit memo generated
    In order to have official confirmation of some order units refund
    As an Administrator
    I want to have credit memo generated for each order refund action

    Background:
        Given the store operates on a single green channel in "United States"
        And channel "United States" billing data is "Haas & Milan", "Pacific Coast Hwy", "90003" "Los Angeles", "United States" with "1100110011" tax ID
        And the store has a product "PHP T-Shirt" priced at "$10.00"
        And the store has "Galaxy Post" shipping method with "$10.00" fee
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "PHP T-Shirt" products
        And the customer "Rick Sanchez" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Galaxy Post" shipping method with "Space money" payment
        And the order "#00000022" is already paid
        And I am logged in as an administrator

    @ui @application
    Scenario: Having credit memo generated after refund process
        When I want to refund some units of order "#00000022"
        And I decide to refund 1st "PHP T-Shirt" product with "Space money" payment
        Then I should be notified that selected order units have been successfully refunded
        And I should have 1 credit memo generated for order "#00000022"
