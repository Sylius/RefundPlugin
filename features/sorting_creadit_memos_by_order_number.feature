@refunds
Feature: Sorting credit memos by order number
    In order to see
    As an Admin
    I want to have ability to sort credit memos by order number

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a product "Goredrinker" priced at "$10.00"
        And the store has a product "Doran's ring" priced at "$20.00"
        And the store allows shipping with "Recall post"
        And the store allows paying with "Gold"
        And there is a customer "garen@demacia.com" that placed an order "#00000021"
        And the customer bought a single "Goredrinker"
        And the customer bought a single "Doran's ring"
        And the customer chose "Recall post" shipping method to "United States" with "Gold" payment
        And the order "#00000021" is already paid
        And there is a customer "darius@noxus.com" that placed an order "#00000123"
        And the customer bought a single "Doran's ring"
        And the customer chose "Recall post" shipping method to "United States" with "Gold" payment
        And the order "#00000123" is already paid
        And 1st "Goredrinker" product from order "#00000021" has already been refunded with "Gold" payment
        And 1st "Doran's ring" product from order "#00000123" has already been refunded with "Gold" payment
        And I am logged in as an administrator

    @ui
    Scenario: Changing the credit memo sort by order number
        When I browse credit memos
        And I switch the way credit memos are sorted by order
        Then I should see 2 credit memos
        But the first credit memo should have order number "#00000021"

    @ui
    Scenario: Changing the credit memo sort by descending order number
        When I browse credit memos
        And I switch the way credit memos are sorted by order
        And I sort credit memos descending by order
        Then I should see 2 credit memos
        But the first credit memo should have order number "#00000123"

