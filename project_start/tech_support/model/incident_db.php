<?php
// Get a customer using their email address
function get_customer_by_email($email) {
    global $db;
    // Select the customer that matches the submitted email address
    $query = 'SELECT *
              FROM customers
              WHERE email = :email';
    // Prepare the SQL statement
    $statement = $db->prepare($query);
    // Bind the email address to the SQL parameter
    $statement->bindValue(':email', $email);
    // Execute the query
    $statement->execute();
    // Get the matching customer
    $customer = $statement->fetch();
    // Close the database cursor
    $statement->closeCursor();
    // Return the customer record
    return $customer;
}
// Get the products registered to a customer
function get_registered_products($customerID) {
    global $db;
    // Select the products registered to this customer
    $query = 'SELECT products.productCode, products.name
              FROM products
              INNER JOIN registrations
                  ON products.productCode = registrations.productCode
              WHERE registrations.customerID = :customerID';
    // Prepare the SQL statement
    $statement = $db->prepare($query);
    // Bind the customer ID to the SQL parameter
    $statement->bindValue(':customerID', $customerID);
    // Execute the query
    $statement->execute();
    // Get all registered products
    $products = $statement->fetchAll();
    // Close the database cursor
    $statement->closeCursor();
    // Return the registered products
    return $products;
}
// Add a new incident to the incidents table
function add_incident($customerID, $productCode, $title, $description) {
    global $db;
    // Create the SQL INSERT statement
    $query = 'INSERT INTO incidents
                 (customerID, productCode, dateOpened, title, description)
              VALUES
                 (:customerID, :productCode, NOW(), :title, :description)';
    // Prepare the SQL statement
    $statement = $db->prepare($query);
    // Bind the incident data to the SQL parameters
    $statement->bindValue(':customerID', $customerID);
    $statement->bindValue(':productCode', $productCode);
    $statement->bindValue(':title', $title);
    $statement->bindValue(':description', $description);
    // Execute the INSERT statement
    $statement->execute();
    // Close the database cursor
    $statement->closeCursor();
}
?>