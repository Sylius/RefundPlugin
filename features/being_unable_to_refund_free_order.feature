@refunds
Feature: Being unable to refund a free order
    In order to give back money to the customer only if it is possible
    As an Administrator
    I want not to be able to generate a refund for a free order

    Background:
        Given the store operates on a single green channel in "United States"
        And the store has a free product "Witcher Sword"
        And the store ships everywhere for free for all channels
        And the store allows paying with "Space money"
        And there is a customer "Lucifer Morningstar" that placed an order "#0000001"
        And the customer bought 3 "Witcher Sword" products
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Free" shipping method
        And this order is already paid
        And I am logged in as an administrator

        @ui
        Scenario: Being unable to refund a free order
            When I view the summary of the order "#0000001"
            Then I should not see refunds button

        @ui
        Scenario: Being unable to open refund page when order is free
            When I try to refund some units of order "#0000001"
            Then I should be redirected to the order "#0000001" show page
            And I should be notified that I cannot refund a free order
