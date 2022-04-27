@customer_credit_memos
Feature: Being unable to download a credit memo on customer order view
    In order not to generate PDF file for a credit memo
    As a Customer
    I want to be unable to download a credit memo as a PDF file on single order view

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a product "Angel T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And I placed an order "#00000666"
        And I bought a single "Angel T-Shirt"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And I chose "Free" shipping method with "Cash on Delivery" payment
        And this order is already paid
        And 1st "Angel T-Shirt" product from order "#00000666" has already been refunded with "Cash on Delivery" payment

    @ui
    Scenario: Being unable to download a credit memo on customer order view
        When I view the summary of the order "#00000666"
        Then I should not be able to download the first credit memo
