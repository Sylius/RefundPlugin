@refunds
Feature: Seeing credit memos in admin order view
  In order to see every credit memos related to one order
  As an Administrator
  I want to be able to see credit memos in admin order view

  Background:
    Given the store operates on a single green channel in "United States"
    And the store has "Galaxy Post" shipping method with "$10.00" fee
    And the store allows paying with "Space money"
    And the store has a product "PHP T-Shirt" priced at "$10.00"
    And the store has a product "Symfony Mug" priced at "$21.00"
    And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000022"
    And the customer bought a "PHP T-Shirt" and a "Symfony Mug"
    And the customer "Rick Sanchez" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
    And the customer chose "Galaxy Post" shipping method with "Space money" payment
    And the order "#00000022" is already paid
    And I am logged in as an administrator
    
  @ui
  Scenario: Having all credit memos listed on the order page in ascending order
    Given the "#00000022" order's shipping cost already has a refund of "$4.50" with "Space money" payment
    And the 1st "PHP T-Shirt" product from order "#00000022" has a refund of "$5.50" with "Space money" payment
    When I view the summary of the order "#00000022"
    Then I should see the credit memo with "$4.50" total as 1st in the list
    And I should see the credit memo with "$5.50" total as 2nd in the list
