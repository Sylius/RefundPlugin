@refunds
Feature: Downloading credit memo as pdf file
    In order to save credit memo for further usage
    As an Administrator
    I want to download credit memo as a pdf file

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a product "Mr. Meeseeks T-Shirt" priced at "$10.00"
        And the store allows shipping with "Galaxy Post"
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
        And the customer bought a single "Mr. Meeseeks T-Shirt"
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And the order "#00000022" is already paid
        And 1st "Mr. Meeseeks T-Shirt" product from order "#00000022" has already been refunded with "Space money" payment
        And I am logged in as an administrator

    @ui
    Scenario: Downloading credit memo from credit memos index page
        When I browse credit memos
        And I download 1st credit memo
        Then a pdf file should be successfully downloaded

    @ui
    Scenario: Downloading credit memo from credit memo details page
        When I browse the details of the only credit memo generated for order "#00000022"
        And I download it
        Then a pdf file should be successfully downloaded

    @ui
    Scenario: Downloading credit memo from order show page
        When I view the summary of the order "#00000022"
        And I download 1st order's credit memo
        Then a pdf file should be successfully downloaded
