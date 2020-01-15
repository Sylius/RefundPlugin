@refunds
Feature: Having credit memo generated
    In order to have official confirmation of some order units refund
    As an Administrator
    I want to have credit memo generated for each order refund action

    Background:
        Given the store operates on a single green channel in "United States"
        And channel "United States" billing data is "Haas & Milan", "Pacific Coast Hwy", "90003" "Los Angeles", "United States" with "1100110011" tax ID
        And default tax zone is "US"
        And the store has "US VAT" tax rate of 10% for "Clothes" within the "US" zone
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And it belongs to "Clothes" tax category
        And the store has "Galaxy Post" shipping method with "$10.00" fee
        And the store allows paying with "Space money"
        And there is a promotion "Anatomy Park Promotion"
        And this promotion gives "$1.00" off on every product with minimum price at "$5.00"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer "Rick Sanchez" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Galaxy Post" shipping method with "Space money" payment
        And there is a customer "morty.smith@look-at-me.com" that placed an order "#00000023"
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer "Morty Smith" addressed it to "Main St.", "90100" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Galaxy Post" shipping method with "Space money" payment
        And the order "#00000023" is already paid
        And I am logged in as an administrator
        And the order "#00000022" is already paid

    @ui @application
    Scenario: Having credit memo generated after refund process
        When I want to refund some units of order "#00000022"
        And I decide to refund 1st "Mr. Meeseeks T-Shirt" product with "Space money" payment
        Then I should be notified that selected order units have been successfully refunded
        And I should have 1 credit memo generated for order "#00000022"

    @ui @application
    Scenario: Seeing the details of generated credit memo
        Given all units from the order "#00000022" are refunded with "Space money" payment
        And I browse the details of the only credit memo generated for order "#00000022"
        And it should have sequential number generated from current date
        Then this credit memo should contain 2 "Mr. Meeseeks T-Shirt" products with "$0.90" tax applied
        And it should be issued in "United States" channel
        And it should be issued from "Rick Sanchez", "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And it should be issued to "Haas & Milan", "Pacific Coast Hwy", "90003" "Los Angeles" in the "United States" with "1100110011" tax ID
        And its subtotal should be "$18.00"
        And it should have a tax item "US VAT (10%)" with amount "$1.80"
        And its total should be "$19.80"

    @ui @application
    Scenario: Seeing the details of generated credit memo with partial price
        Given 1st "Mr. Meeseeks T-Shirt" product from order "#00000022" has already "$5.50" refunded with "Space money" payment
        When I browse the details of the only credit memo generated for order "#00000022"
        And it should have sequential number generated from current date
        Then this credit memo should contain 1 "Mr. Meeseeks T-Shirt" product with "$0.50" tax applied
        And it should be issued in "United States" channel
        And its subtotal should be "$5.00"
        And it should have a tax item "US VAT (10%)" with amount "$0.50"
        And its total should be "$5.50"

    @ui @application
    Scenario: Seeing the details of generated credit memo with partial shipment price
        Given shipment from order "#00000023" has already "$4.50" refunded with "Space money" payment
        When I browse the details of the only credit memo generated for order "#00000023"
        And it should have sequential number generated from current date
        Then this credit memo should contain 1 "Galaxy Post" shipment with "$4.50" total
        And it should be issued in "United States" channel
        And its total should be "$4.50"
