@refunds
Feature: Viewing details of a credit memo for order with a promotion applied
    In order to be aware of what order units have been refunded
    As an Administrator
    I want to be able to view details of a credit memo

    Background:
        Given the store operates on a single green channel in "United States"
        And channel "United States" billing data is "Haas & Milan", "Pacific Coast Hwy", "90003" "Los Angeles", "United States" with "1100110011" tax ID
        And default tax zone is "US"
        And the store has "US VAT" tax rate of 10% for "Clothes" within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$10.00"
        And it belongs to "Clothes" tax category
        And the store has included in price "SHIPPING_VAT" tax rate of 15% for "Shipments" within the "US" zone
        And the store has "Galaxy Post" shipping method with "$11.50" fee within the "US" zone
        And shipping method "Galaxy Post" belongs to "Shipments" tax category
        And the store allows paying with "Space money"
        And there is a promotion "Anatomy Park Promotion"
        And this promotion gives "$3.99" discount to every order with quantity at least 4
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And there is a promotion "Cheaper shipments"
        And it gives "50%" discount on shipping to every order
        And the customer bought 4 "PHP T-Shirt" products
        And the customer "Rick Sanchez" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Galaxy Post" shipping method with "Space money" payment
        And the order "#00000022" is already paid
        And I am logged in as an administrator

    @ui @application
    Scenario: Viewing details of a credit memo issued for an order with a promotion applied
        Given all units and shipment from the order "#00000022" are refunded with "Space money" payment
        When I browse the details of the only credit memo generated for order "#00000022"
        Then it should have sequential number generated from current date
        And it should be issued in "United States" channel
        And it should be issued to "Haas & Milan", "Pacific Coast Hwy", "90003" "Los Angeles" in the "United States" with "1100110011" tax ID
        And it should be issued from "Rick Sanchez", "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And it should contain 3 "PHP T-Shirt" products with "27.00" net value, "2.70" tax amount and "29.70" gross value in "USD" currency
        And it should contain 1 "PHP T-Shirt" product with "9.01" net value, "0.90" tax amount and "9.91" gross value in "USD" currency
        And it should contain 1 "Galaxy Post" shipment with "5.00" net value, "0.75" tax amount and "5.75" gross value in "USD" currency
        And it should contain a tax item "10%" with amount "3.60" in "USD" currency
        And it should contain a tax item "15%" with amount "0.75" in "USD" currency
        And its total should be "45.36" in "USD" currency
