
<?php
// Connect to the SportsPro database.
require_once('../model/database.php');

// Read the requested action from POST or GET.
$action = filter_input(INPUT_POST, 'action');

if ($action === null) {
    $action = filter_input(INPUT_GET, 'action');
}

// Display the customer directory by default.
if ($action === null || $action === '') {
    $action = 'search_customers';
}

// Escape database values before displaying them in HTML.
function escape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Route requests to the correct customer operation.
switch ($action) {
    case 'search_customers':
        // Read the optional customer last-name search.
        $lastName = trim((string) (
            filter_input(INPUT_POST, 'lastName') ??
            filter_input(INPUT_GET, 'lastName') ??
            ''
        ));

        // Display every customer when no last name is entered.
        if ($lastName === '') {
            $statement = $db->query(
                'SELECT customerID, firstName, lastName, email, city
                 FROM customers
                 ORDER BY lastName, firstName'
            );
        } else {
            // Filter customers using a partial last-name match.
            $statement = $db->prepare(
                'SELECT customerID, firstName, lastName, email, city
                 FROM customers
                 WHERE lastName LIKE :lastName
                 ORDER BY lastName, firstName'
            );

            // Execute the customer search safely.
            $statement->execute([
                ':lastName' => '%' . $lastName . '%'
            ]);
        }

        // Retrieve customer records for the directory.
        $customers = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Display the shared application header.
        include('../view/header.php');
        ?>
        <main>
            <h1>Customer Directory</h1>

            <p>Browse customers or search by last name.</p>

            <!-- Search customers without requiring a last name. -->
            <form action="index.php" method="get">
                <input
                    type="hidden"
                    name="action"
                    value="search_customers"
                >

                <!-- Enter all or part of a customer's last name. -->
                <label for="lastName">Last Name:</label>
                <input
                    type="text"
                    id="lastName"
                    name="lastName"
                    value="<?= escape($lastName) ?>"
                >

                <!-- Submit the optional customer search. -->
                <button type="submit">Search</button>
            </form>

            <!-- Display the customer directory or search results. -->
            <h2>
                <?= $lastName === '' ? 'All Customers' : 'Search Results' ?>
            </h2>

            <?php if (count($customers) > 0) : ?>
                <!-- Show customer names, emails, and cities. -->
                <table>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email Address</th>
                        <th>City</th>
                        <th>Action</th>
                    </tr>

                    <!-- Display each customer returned from MySQL. -->
                    <?php foreach ($customers as $customer) : ?>
                        <tr>
                            <td>
                                <?= escape($customer['firstName']) ?>
                            </td>

                            <td>
                                <?= escape($customer['lastName']) ?>
                            </td>

                            <td>
                                <?= escape($customer['email']) ?>
                            </td>

                            <td>
                                <?= escape($customer['city']) ?>
                            </td>

                            <!-- Open the selected customer's record. -->
                            <td>
                                <a href="index.php?action=view_customer&amp;customerID=<?= (int) $customer['customerID'] ?>">
                                    Select
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else : ?>
                <!-- Display feedback when no customers match. -->
                <p>No customers found.</p>
            <?php endif; ?>

            <!-- Return to the complete customer directory. -->
            <?php if ($lastName !== '') : ?>
                <p>
                    <a href="index.php">Show All Customers</a>
                </p>
            <?php endif; ?>
        </main>
        <?php
        // Close the page using the shared footer.
        include('../view/footer.php');
        break;

    case 'view_customer':
        // Read the selected customer ID from the URL.
        $customerID = filter_input(
            INPUT_GET,
            'customerID',
            FILTER_VALIDATE_INT
        );

        // Retrieve the selected customer's complete record.
        $statement = $db->prepare(
            'SELECT *
             FROM customers
             WHERE customerID = :customerID'
        );

        // Execute the customer lookup.
        $statement->execute([
            ':customerID' => $customerID
        ]);

        // Fetch the customer record.
        $customer = $statement->fetch(PDO::FETCH_ASSOC);

        // Stop if the requested customer does not exist.
        if (!$customer) {
            $error = 'Customer not found.';
            include('../errors/error.php');
            break;
        }

        // Load countries for the country dropdown.
        $countries = $db->query(
            'SELECT countryCode, countryName
             FROM countries
             ORDER BY countryName'
        )->fetchAll(PDO::FETCH_ASSOC);

        // Display the selected customer's information.
        include('../view/header.php');
        ?>
        <main>
            <h2>View/Update Customer</h2>

            <!-- Confirm successful customer updates. -->
            <?php if (isset($_GET['updated'])) : ?>
                <p class="message">
                    Customer updated successfully.
                </p>
            <?php endif; ?>

            <!-- Submit edited customer information to MySQL. -->
            <form action="index.php" method="post">
                <input
                    type="hidden"
                    name="action"
                    value="update_customer"
                >

                <!-- Identify the customer being updated. -->
                <input
                    type="hidden"
                    name="customerID"
                    value="<?= (int) $customer['customerID'] ?>"
                >

                <?php
                // Define the standard editable customer fields.
                $fields = [
                    'firstName' => 'First Name',
                    'lastName' => 'Last Name',
                    'address' => 'Address',
                    'city' => 'City',
                    'state' => 'State',
                    'postalCode' => 'Postal Code'
                ];
                ?>

                <!-- Display customer fields with saved values. -->
                <?php foreach ($fields as $field => $label) : ?>
                    <p>
                        <label for="<?= escape($field) ?>">
                            <?= escape($label) ?>:
                        </label>

                        <input
                            type="text"
                            id="<?= escape($field) ?>"
                            name="<?= escape($field) ?>"
                            value="<?= escape($customer[$field]) ?>"
                        >
                    </p>
                <?php endforeach; ?>

                <!-- Select a country from the countries table. -->
                <p>
                    <label for="countryCode">Country:</label>

                    <select
                        id="countryCode"
                        name="countryCode"
                        required
                    >
                        <!-- Preselect the customer's saved country. -->
                        <?php foreach ($countries as $country) : ?>
                            <option
                                value="<?= escape($country['countryCode']) ?>"
                                <?= $customer['countryCode'] === $country['countryCode']
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= escape($country['countryName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <!-- Display the customer's phone number. -->
                <p>
                    <label for="phone">Phone:</label>
                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="<?= escape($customer['phone']) ?>"
                    >
                </p>

                <!-- Display the customer's email address. -->
                <p>
                    <label for="email">Email:</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= escape($customer['email']) ?>"
                    >
                </p>

                <!-- Display the customer's existing password field. -->
                <p>
                    <label for="password">Password:</label>
                    <input
                        type="text"
                        id="password"
                        name="password"
                        value="<?= escape($customer['password']) ?>"
                    >
                </p>

                <!-- Save the edited customer information. -->
                <button type="submit">Update Customer</button>
            </form>

            <!-- Return to the complete customer directory. -->
            <p>
                <a href="index.php">All Customers</a>
            </p>
        </main>
        <?php
        // Close the customer details page.
        include('../view/footer.php');
        break;

    case 'update_customer':
        // Validate the customer ID submitted by the form.
        $customerID = filter_input(
            INPUT_POST,
            'customerID',
            FILTER_VALIDATE_INT
        );

        // Read the selected country code.
        $countryCode = trim((string) (
            filter_input(INPUT_POST, 'countryCode') ?? ''
        ));

        // Confirm that the country exists in the database.
        $statement = $db->prepare(
            'SELECT COUNT(*)
             FROM countries
             WHERE countryCode = :countryCode'
        );

        // Check the selected country code.
        $statement->execute([
            ':countryCode' => $countryCode
        ]);

        // Reject an invalid customer or country.
        if (!$customerID || !$statement->fetchColumn()) {
            $error = 'Invalid customer or country.';
            include('../errors/error.php');
            break;
        }

        // Define the customer fields to update.
        $fields = [
            'firstName',
            'lastName',
            'address',
            'city',
            'state',
            'postalCode',
            'phone',
            'email',
            'password'
        ];

        // Prepare the submitted customer information.
        $data = [];

        // Read each editable field from the form.
        foreach ($fields as $field) {
            $data[$field] = trim((string) ($_POST[$field] ?? ''));
        }

        // Update the customer's complete database record.
        $statement = $db->prepare(
            'UPDATE customers
             SET firstName = :firstName,
                 lastName = :lastName,
                 address = :address,
                 city = :city,
                 state = :state,
                 postalCode = :postalCode,
                 countryCode = :countryCode,
                 phone = :phone,
                 email = :email,
                 password = :password
             WHERE customerID = :customerID'
        );

        // Bind customer information to the SQL parameters.
        $statement->execute([
            ':firstName' => $data['firstName'],
            ':lastName' => $data['lastName'],
            ':address' => $data['address'],
            ':city' => $data['city'],
            ':state' => $data['state'],
            ':postalCode' => $data['postalCode'],
            ':countryCode' => $countryCode,
            ':phone' => $data['phone'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':customerID' => $customerID
        ]);

        // Return to the updated customer record.
        header(
            'Location: index.php?action=view_customer&customerID=' .
            $customerID . '&updated=1'
        );
        exit();

    default:
        // Handle unsupported customer-management actions.
        $error = 'Invalid customer action.';
        include('../errors/error.php');
        break;
}
