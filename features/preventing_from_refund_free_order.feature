@refunds
Feature: Preventing from refund a free order
    In order to give back money to the customer only if it is possible
    As an Administrator
    I want to not be able to generate refund for free order

    Background: Given the store operates on a single green channel in "United States"
        And the store has a free product "Witcher Sword"
        And the store allows shipping with "Galaxy Post"
        And the store ships everywhere for free for all channels
        And the store allows paying with "Space money"
        And there is a customer "geralt@netflix.com" that placed an order "#0000001"
        And the customer bought 3 "Witcher Sword" products
        And the customer chose "Free" shipping method to "United States" with "Space money" payment
        And this order is already paid
        And I am logged in as an administrator

        @ui
        Scenario: Not being able to refund free order
            When I am viewing the summary of the order "#0000001"
            Then I should not be able to see refunds button

        Scenario: Not being able to open refund page when order is free
            When I want to refund some units of order "#0000001"
            Then I should be redirected to the order "#0000001" show page
            And I should be notified that I cannot refund free order
