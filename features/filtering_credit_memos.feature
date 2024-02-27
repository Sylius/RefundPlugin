@refunds
Feature: Filtering credit memos
    In order to browse credit memos more efficiently
    As an Administrator
    I want to be able to filter credit memos list

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency with green color
        And the store operates on a channel named "Web-UK" in "GBP" currency with green color
        And the store has country "United States"
        And the store has country "United Kingdom"
        And the store has a zone "United States + United Kingdom" with code "US + UK"
        And this zone has the "United States" country member
        And this zone has the "United Kingdom" country member
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00" available in channel "Web-US" and channel "Web-UK"
        And the store ships everywhere for free for all channels
        And the store allows paying offline for all channels
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000001"
        And this order has been placed in "Web-US" channel
        And the customer bought 2 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And the order "#00000001" is already paid
        And all units from the order "#00000001" are refunded with "Offline" payment
        And a customer "rick.sanchez@wubba-lubba-dub-dub.com" placed an order "#00000002"
        And this order has been placed in "Web-UK" channel
        And the customer bought 3 "Mr. Meeseeks T-Shirt" products
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And the order "#00000002" is already paid
        And all units from the order "#00000002" are refunded with "Offline" payment
        And I am logged in as an administrator

    @ui
    Scenario: Filtering credit memos by channel
        When I browse credit memos
        And I filter credit memos by "Web-US" channel
        Then there should be 1 credit memo generated
        And the only credit memo should be generated for order "#00000001"
