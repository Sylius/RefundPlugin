@refunds
Feature: Viewing details of a credit memo with multiple shipments
    In order to be aware of what shipments have been refunded
    As an Administrator
    I want to be able to view details of a credit memo

    Background:
        Given the store operates on a single green channel in "United States"
        And channel "United States" billing data is "Haas & Milan", "Pacific Coast Hwy", "90003" "Los Angeles", "United States" with "1100110011" tax ID
        And default tax zone is "US"
        And the store has "US VAT" tax rate of 10% for "Clothes" within the "US" zone
        And the store has "VAT" tax rate of 23% for "Shipping Services" within the "US" zone
        And the store has "Galaxy Post" shipping method with "$20.00" fee
        And the store has "Space Pidgeons Post" shipping method with "$10.00" fee within the "US" zone
        And shipping method "Space Pidgeons Post" belongs to "Shipping Services" tax category
        And the store allows paying with "Space money"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And it belongs to "Clothes" tax category
        And I am logged in as an administrator

    @ui @application
    Scenario: Viewing details of a credit memo issued for a full refund with multiple shipments
        Given there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought a single "Mr. Meeseeks T-Shirt"
        And the customer chose "Galaxy Post" shipping method to "United States"
        And the customer bought another "Mr. Meeseeks T-Shirt" with separate "Space Pidgeons Post" shipment
        And the customer chose "Space money" payment
        And the order "#00000022" is already paid
        And all units and shipment from the order "#00000022" are refunded with "Space money" payment
        When I browse the details of the only credit memo generated for order "#00000022"
        Then it should have sequential number generated from current date
        And it should be issued in "United States" channel
        And it should be issued to "Haas & Milan", "Pacific Coast Hwy", "90003" "Los Angeles" in the "United States" with "1100110011" tax ID
        And it should contain 2 "Mr. Meeseeks T-Shirt" products with "20.00" net value, "2.00" tax amount and "22.00" gross value in "USD" currency
        And it should contain 1 "Galaxy Post" shipment with "20.00" net value, "0.00" tax amount and "20.00" gross value in "USD" currency
        And it should contain 1 "Space Pidgeons Post" shipment with "10.00" net value, "2.30" tax amount and "12.30" gross value in "USD" currency
        And it should contain a tax item "10%" with amount "2.00" in "USD" currency
        And it should contain a tax item "23%" with amount "2.30" in "USD" currency
        And its total should be "54.30" in "USD" currency
        And its net total should be "50.00"
        And its tax total should be "4.30"

    @ui @application
    Scenario: Viewing details of a credit memo issued for a full refund with multiple shipments and promotion applied
        Given there is a promotion "Multiple items promotion"
        And this promotion gives "$4.00" discount to every order with quantity at least 2
        And there is a promotion "Cheaper shipments promotion"
        And it gives "50%" discount on shipping to every order
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought a single "Mr. Meeseeks T-Shirt"
        And the customer chose "Galaxy Post" shipping method to "United States"
        And the customer bought another "Mr. Meeseeks T-Shirt" with separate "Space Pidgeons Post" shipment
        And the customer chose "Space money" payment
        And the order "#00000022" is already paid
        And all units and shipment from the order "#00000022" are refunded with "Space money" payment
        When I browse the details of the only credit memo generated for order "#00000022"
        Then it should have sequential number generated from current date
        And it should be issued in "United States" channel
        And it should be issued to "Haas & Milan", "Pacific Coast Hwy", "90003" "Los Angeles" in the "United States" with "1100110011" tax ID
        And it should contain 2 "Mr. Meeseeks T-Shirt" products with "16.00" net value, "1.60" tax amount and "17.60" gross value in "USD" currency
        And it should contain 1 "Galaxy Post" shipment with "10.00" net value, "0.00" tax amount and "10.00" gross value in "USD" currency
        And it should contain 1 "Space Pidgeons Post" shipment with "5.00" net value, "1.15" tax amount and "6.15" gross value in "USD" currency
        And it should contain a tax item "10%" with amount "1.60" in "USD" currency
        And it should contain a tax item "23%" with amount "1.15" in "USD" currency
        And its total should be "33.75" in "USD" currency
        And its net total should be "31.00"
        And its tax total should be "2.75"
