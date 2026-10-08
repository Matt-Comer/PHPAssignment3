
<?php
// Load the database connection used to communicate with MySQL.
require_once('../model/database.php');
// Load the functions responsible for customer lookup, registered products, and incidents.
require_once('../model/incident_db.php');
// Read the action submitted by the form using the POST method.
$action = filter_input(INPUT_POST, 'action');
// Set the default action when the page is opened without a form submission.
if ($action === null || $action === '') {
    $action = 'show_form';
}
// Use a switch statement to direct requests to the correct operation.
switch ($action) {
    case 'show_form':
        // Load the shared page header.
        include('../view/header.php');
        ?>
        <main>
            <!-- Display the page heading. -->
            <h1>Create Incident</h1>
            <!-- Submit the customer's email address to the same controller. -->
            <form action="index.php" method="post">
                <!-- Tell the controller to search for a customer. -->
                <input type="hidden" name="action" value="get_customer">
                <!-- Require an email address before allowing submission. -->
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <!-- Submit the email address for database lookup. -->
                <input type="submit" value="Get Customer">
            </form>
        </main>
        <?php
        // Load the shared page footer.
        include('../view/footer.php');
        // Stop processing this switch case.
        break;
    case 'get_customer':
        // Retrieve the email address submitted by the customer search form.
        $email = trim((string) filter_input(INPUT_POST, 'email'));
        // Search the customers table for the submitted email address.
        $customer = get_customer_by_email($email);
        // Check whether the database returned a matching customer.
        if (!$customer) {
            // Display the shared header for the customer-not-found page.
            include('../view/header.php');
            ?>
            <main>
                <!-- Identify the operation that could not be completed. -->
                <h1>Create Incident</h1>
                <!-- Explain that the submitted email did not match a customer. -->
                <p>No customer was found with that email address.</p>
                <!-- Allow the user to return to the customer lookup form. -->
                <p><a href="index.php">Search Again</a></p>
            </main>
            <?php
            // Display the shared footer before ending this operation.
            include('../view/footer.php');
            // Stop processing because no customer was found.
            break;
        }
        // Convert the matching customer's ID into an integer.
        $customerID = (int) $customer['customerID'];
        // Retrieve only the products registered to the matching customer.
        $products = get_registered_products($customerID);
        // Load the shared header before displaying customer information.
        include('../view/header.php');
        ?>
        <main>
            <!-- Display the incident creation heading. -->
            <h1>Create Incident</h1>
            <!-- Display the customer's name to confirm the selected account. -->
            <p>
                Customer:
                <!-- Escape the first name to prevent unsafe HTML output. -->
                <?= htmlspecialchars($customer['firstName'], ENT_QUOTES, 'UTF-8') ?>
                <!-- Escape the last name before displaying it. -->
                <?= htmlspecialchars($customer['lastName'], ENT_QUOTES, 'UTF-8') ?>
            </p>
            <!-- Check whether the customer has any registered products. -->
            <?php if (empty($products)) : ?>
                <!-- Explain why an incident cannot be created for this customer. -->
                <p>This customer has no registered products.</p>
            <?php else : ?>
                <!-- Display the incident form when registered products exist. -->
                <form action="index.php" method="post">
                    <!-- Tell the controller to insert a new incident. -->
                    <input type="hidden" name="action" value="add_incident">
                    <!-- Preserve the customer ID when the incident form is submitted. -->
                    <input
                        type="hidden"
                        name="customerID"
                        value="<?= $customerID ?>"
                    >
                    <!-- Display the registered-product selection field. -->
                    <p>
                        <label for="productCode">Product:</label>
                        <!-- Require the user to select a registered product. -->
                        <select id="productCode" name="productCode" required>
                            <!-- Provide a placeholder that cannot be submitted as a product. -->
                            <option value="">Select a product</option>
                            <!-- Generate one option for each registered product. -->
                            <?php foreach ($products as $product) : ?>
                                <!-- Use the product code as the database identifier. -->
                                <option
                                    value="<?= htmlspecialchars($product['productCode'], ENT_QUOTES, 'UTF-8') ?>"
                                >
                                    <!-- Display the product name safely. -->
                                    <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <!-- Collect a short title describing the support incident. -->
                    <p>
                        <label for="title">Title:</label>
                        <!-- Require a title before submitting the form. -->
                        <input
                            type="text"
                            id="title"
                            name="title"
                            required
                        >
                    </p>
                    <!-- Collect a detailed explanation of the reported issue. -->
                    <p>
                        <label for="description">Description:</label>
                        <!-- Require a description before submitting the form. -->
                        <textarea
                            id="description"
                            name="description"
                            required
                        ></textarea>
                    </p>
                    <!-- Submit the incident information to the controller. -->
                    <input type="submit" value="Create Incident">
                </form>
            <?php endif; ?>
            <!-- Allow another customer to be searched without creating an incident. -->
            <p><a href="index.php">Search Again</a></p>
        </main>
        <?php
        // Load the shared footer after the incident form.
        include('../view/footer.php');
        // End the customer lookup operation.
        break;
    case 'add_incident':
        // Retrieve and validate the submitted customer ID as an integer.
        $customerID = filter_input(
            INPUT_POST,
            'customerID',
            FILTER_VALIDATE_INT
        );
        // Retrieve the product code selected from the registered-product dropdown.
        $productCode = trim((string) filter_input(INPUT_POST, 'productCode'));
        // Retrieve the incident title and remove unnecessary surrounding whitespace.
        $title = trim((string) filter_input(INPUT_POST, 'title'));
        // Retrieve the incident description and remove surrounding whitespace.
        $description = trim((string) filter_input(INPUT_POST, 'description'));
        // Reject the submission if the customer ID or any required field is missing.
        if (!$customerID || $productCode === '' ||
            $title === '' || $description === '') {
            // Prepare an error message explaining the missing information.
            $error = 'Please complete all incident fields.';
            // Display the application's shared error page.
            include('../errors/error.php');
            // Stop processing the invalid submission.
            break;
        }
        // Retrieve registered products again to verify the submitted product.
        $registeredProducts = get_registered_products($customerID);
        // Assume the submitted product is invalid until a match is found.
        $validProduct = false;
        // Compare the submitted product code against registered product codes.
        foreach ($registeredProducts as $product) {
            // Check whether the submitted code belongs to this customer's registrations.
            if ($product['productCode'] === $productCode) {
                // Mark the product as valid when a registered match is found.
                $validProduct = true;
                // Stop searching because the matching product has been identified.
                break;
            }
        }
        // Prevent an incident from being created for an unregistered product.
        if (!$validProduct) {
            // Prepare an error message describing the invalid product selection.
            $error = 'The selected product is not registered to this customer.';
            // Display the application's shared error page.
            include('../errors/error.php');
            // Stop processing before inserting any database record.
            break;
        }
        // Insert the validated incident into the incidents table.
        add_incident($customerID, $productCode, $title, $description);
        // Load the shared header for the confirmation page.
        include('../view/header.php');
        ?>
        <main>
            <!-- Display the incident creation heading. -->
            <h1>Create Incident</h1>
            <!-- Confirm that the database insertion completed successfully. -->
            <p>The incident was successfully added to the database.</p>
            <!-- Provide a link to begin another incident. -->
            <p><a href="index.php">Create Another Incident</a></p>
        </main>
        <?php
        // Load the shared footer for the confirmation page.
        include('../view/footer.php');
        // End the incident creation operation.
        break;
    default:
        // Handle an action that is not recognized by this controller.
        $error = 'Invalid incident action.';
        // Display the shared error page for unsupported actions.
        include('../errors/error.php');
        // Stop processing the unsupported action.
        break;
}
