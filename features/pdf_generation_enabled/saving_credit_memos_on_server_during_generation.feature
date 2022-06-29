@refunds
Feature: Saving credit memos on server during generation
    In order to keep credit memos immutable for further usage
    As a Store Owner
    I want the credit memos to be saved on a server during generation

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$10.00"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And the order "#00000022" is already paid
        And I am logged in as an administrator

    @application
    Scenario: Having credit memo saved on the server after the refund is made
        When I refund all units of "#00000022" order with "Space money" payment method
        Then the credit memo for "#00000022" order should be saved on the server
