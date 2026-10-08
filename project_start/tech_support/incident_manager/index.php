
<?php
// Connect to MySQL and load the incident database functions.
require_once('../model/database.php');
require_once('../model/incident_db.php');

// Read the requested action from the form.
$action = filter_input(INPUT_POST, 'action');

// Display the customer lookup when no action is submitted.
if ($action === null || $action === '') {
    $action = 'show_form';
}

// Route each request to the correct incident operation.
switch ($action) {
    case 'show_form':
        // Display the customer email search form.
        include('../view/header.php');
        ?>
        <main>
            <h1>Create Incident</h1>

            <form action="index.php" method="post">
                <input type="hidden" name="action" value="get_customer">

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <input type="submit" value="Get Customer">
            </form>
        </main>
        <?php
        include('../view/footer.php');
        break;

    case 'get_customer':
        // Find the customer using the submitted email address.
        $email = trim((string) filter_input(INPUT_POST, 'email'));
        $customer = get_customer_by_email($email);

        // Display a message if the customer does not exist.
        if (!$customer) {
            include('../view/header.php');
            ?>
            <main>
                <h1>Create Incident</h1>
                <p>No customer was found with that email address.</p>
                <p><a href="index.php">Search Again</a></p>
            </main>
            <?php
            include('../view/footer.php');
            break;
        }

        // Retrieve products registered to this specific customer.
        $customerID = (int) $customer['customerID'];
        $products = get_registered_products($customerID);

        // Display the customer and their registered products.
        include('../view/header.php');
        ?>
        <main>
            <h1>Create Incident</h1>

            <p>
                Customer:
                <?= htmlspecialchars($customer['firstName'], ENT_QUOTES, 'UTF-8') ?>
                <?= htmlspecialchars($customer['lastName'], ENT_QUOTES, 'UTF-8') ?>
            </p>

                     <?php if (empty($products)) : ?>
               <!-- Explain why the product drop-down is unavailable. -->
                <p>This customer has no registered products.</p>
                          <?php else : ?>

                <!-- Display only products registered to this customer. -->
                <form action="index.php" method="post">
                    <input type="hidden" name="action" value="add_incident">

                    <input
                        type="hidden"
                        name="customerID"
                        value="<?= $customerID ?>"
                    >

                    <p>
                        <label for="productCode">Product:</label>

                        <select id="productCode" name="productCode" required>
                            <option value="">Select a product</option>

                            <?php foreach ($products as $product) : ?>
                                <option
                                    value="<?= htmlspecialchars($product['productCode'], ENT_QUOTES, 'UTF-8') ?>"
                                >
                                    <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <!-- Collect the incident title. -->
                    <p>
                        <label for="title">Title:</label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            required
                        >
                    </p>

                    <!-- Collect the incident description. -->
                    <p>
                        <label for="description">Description:</label>
                        <textarea
                            id="description"
                            name="description"
                            required
                        ></textarea>
                    </p>

                    <input type="submit" value="Create Incident">
                </form>
            <?php endif; ?>

            <p><a href="index.php">Search Again</a></p>
        </main>
        <?php
        include('../view/footer.php');
        break;

    case 'add_incident':
        // Read the submitted incident details.
        $customerID = filter_input(
            INPUT_POST,
            'customerID',
            FILTER_VALIDATE_INT
        );

        $productCode = trim((string) filter_input(INPUT_POST, 'productCode'));
        $title = trim((string) filter_input(INPUT_POST, 'title'));
        $description = trim((string) filter_input(INPUT_POST, 'description'));

        // Confirm that all required fields were submitted.
        if (!$customerID || $productCode === '' ||
            $title === '' || $description === '') {
            $error = 'Please complete all incident fields.';
            include('../errors/error.php');
            break;
        }

        // Confirm that the product is registered to this customer.
        $registeredProducts = get_registered_products($customerID);
        $validProduct = false;

        foreach ($registeredProducts as $product) {
            if ($product['productCode'] === $productCode) {
                $validProduct = true;
                break;
            }
        }

        // Reject products that do not belong to this customer.
        if (!$validProduct) {
            $error = 'The selected product is not registered to this customer.';
            include('../errors/error.php');
            break;
        }

        // Save the new incident to the database.
        add_incident($customerID, $productCode, $title, $description);

        // Display confirmation after the database insert succeeds.
        include('../view/header.php');
        ?>
        <main>
            <h1>Create Incident</h1>

            <p>The incident was successfully added to the database.</p>

            <p><a href="index.php">Create Another Incident</a></p>
        </main>
        <?php
        include('../view/footer.php');
        break;

    default:
        // Handle unsupported controller actions.
        $error = 'Invalid incident action.';
        include('../errors/error.php');
        break;
}

