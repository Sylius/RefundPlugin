@refunds
Feature: Viewing details of a credit memo with taxes excluded from price
    In order to be aware of what order units have been refunded
    As an Administrator
    I want to be able to view details of a credit memo

    Background:
        Given the store operates on a single green channel in "United States"
        And channel "United States" billing data is "Haas & Milan", "Pacific Coast Hwy", "90003" "Los Angeles", "United States" with "1100110011" tax ID
        And default tax zone is "US"
        And the store has "US VAT" tax rate of 10% for "Clothes" within the "US" zone
        And the store has "VAT" tax rate of 20% for "Mugs" within the "US" zone
        And the store has "SHIPPING_VAT" tax rate of 15% for "Shipments" within the "US" zone
        And the store has "Galaxy Post" shipping method with "$10.00" fee within the "US" zone
        And shipping method "Galaxy Post" belongs to "Shipments" tax category
        And the store allows paying with "Space money"
        And the store has a product "PHP T-Shirt" priced at "$10.00"
        And it belongs to "Clothes" tax category
        And the store has a product "Symfony Mug" priced at "$20.00"
        And it belongs to "Mugs" tax category
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought a "PHP T-Shirt" and a "Symfony Mug"
        And the customer "Rick Sanchez" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Galaxy Post" shipping method with "Space money" payment
        And the order "#00000022" is already paid
        And I am logged in as an administrator

    @ui @application
    Scenario: Viewing details of a credit memo issued for a full refund
        Given all units and shipment from the order "#00000022" are refunded with "Space money" payment
        When I browse the details of the only credit memo generated for order "#00000022"
        Then it should have sequential number generated from current date
        And it should be issued in "United States" channel
        And it should be issued to "Haas & Milan", "Pacific Coast Hwy", "90003" "Los Angeles" in the "United States" with "1100110011" tax ID
        And it should be issued from "Rick Sanchez", "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And it should contain 1 "PHP T-Shirt" product with "10.00" net value, "1.00" tax amount and "11.00" gross value in "USD" currency
        And it should contain 1 "Symfony Mug" product with "20.00" net value, "4.00" tax amount and "24.00" gross value in "USD" currency
        And it should contain 1 "Galaxy Post" shipment with "10.00" net value, "1.50" tax amount and "11.50" gross value in "USD" currency
        And it should contain a tax item "10%" with amount "1.00" in "USD" currency
        And it should contain a tax item "15%" with amount "1.50" in "USD" currency
        And it should contain a tax item "20%" with amount "4.00" in "USD" currency
        And its total should be "46.50" in "USD" currency
        And its net total should be "40.00"
        And its tax total should be "6.50"

    @ui @application
    Scenario: Viewing details of a credit memo issued for a partial refund
        Given the 1st "PHP T-Shirt" product from order "#00000022" has a refund of "$5.50" with "Space money" payment
        When I browse the details of the only credit memo generated for order "#00000022"
        Then it should have sequential number generated from current date
        And it should be issued in "United States" channel
        And it should contain 1 "PHP T-Shirt" product with "5.00" net value, "0.50" tax amount and "5.50" gross value in "USD" currency
        And it should contain a tax item "10%" with amount "0.50" in "USD" currency
        And its total should be "5.50" in "USD" currency
        And its net total should be "5.00"
        And its tax total should be "0.50"

    @ui @application
    Scenario: Viewing details of a credit memo issued for a partial shipping cost refund
        Given the "#00000022" order's shipping cost already has a refund of "$5.75" with "Space money" payment
        When I browse the details of the only credit memo generated for order "#00000022"
        Then it should have sequential number generated from current date
        And it should contain 1 "Galaxy Post" shipment with "5.00" net value, "0.75" tax amount and "5.75" gross value in "USD" currency
        And it should be issued in "United States" channel
        And its total should be "5.75" in "USD" currency
        And its net total should be "5.00"
        And its tax total should be "0.75"
