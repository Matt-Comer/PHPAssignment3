
<?php
// Connect to the SportsPro MySQL database.
require_once('../model/database.php');
// Get the requested action from POST or GET.
$action = filter_input(INPUT_POST, 'action');
if ($action === null) {
    $action = filter_input(INPUT_GET, 'action');
}
 // Show  the product list when no action is provided.
if ($action === null || $action === '') {
    $action = 'list_products';
}
// Direct each  action to the appropriate product operation.
switch ($action) {
    case 'list_products':
        // Retrieve all products sorted alphabetically by name.
        $statement = $db->query(
            'SELECT productCode, name, version, releaseDate
             FROM products
             ORDER BY name'
        );
        $products = $statement->fetchAll(PDO::FETCH_ASSOC);
     // Display  the product list.
        include('../view/header.php');
        ?>
        <main>
            <h1>Product List</h1>
            <table>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Version</th>
                    <th>Release Date</th>
                    <th></th>
                </tr>
                <!-- Display each product returned from the database. -->
                <?php foreach ($products as $product) : ?>
                    <tr>
                        <td><?= htmlspecialchars($product['productCode']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['version']) ?></td>
                        <!-- Format the release date without leading zeros or time. -->
                        <td><?= date('n-j-Y', strtotime($product['releaseDate'])) ?></td>
                        <td>
                            <!-- Submit the selected product code for deletion. -->
                            <form action="index.php" method="post">
                                <input type="hidden" name="action" value="delete_product">
                                <input type="hidden" name="productCode"
                                       value="<?= htmlspecialchars($product['productCode']) ?>">
                                <input type="submit" value="Delete">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <p><a href="index.php?action=show_add_form">Add Product</a></p>
        </main>
        <?php
        include('../view/footer.php');
        break;
    case 'show_add_form':
        // Display the form used to add a new product.
        include('../view/header.php');
        ?>
        <main>
            <h1>Add Product</h1>
            <form action="index.php" method="post">
                <input type="hidden" name="action" value="add_product">
                <!-- Collect the new product's information. -->
                <label>Code:</label>
                <input type="text" name="productCode" required>
                <br>
                <label>Name:</label>
                <input type="text" name="name" required>
                <br>
                <label>Version:</label>
                <input type="text" name="version" required>
                <br>
                <!-- Accept a standard date entered by the user. -->
                <label>Release Date:</label>
                <input type="text" name="releaseDate"
                       placeholder="10/7/2026" required>
                <br>
                <input type="submit" value="Add Product">
            </form>
            <p><a href="index.php">View Product List</a></p>
        </main>
        <?php
        include('../view/footer.php');
        break;
    case 'add_product':
        // Read the submitted product information and remove surrounding spaces.
        $productCode = trim((string) filter_input(INPUT_POST, 'productCode'));
        $name = trim((string) filter_input(INPUT_POST, 'name'));
        $version = trim((string) filter_input(INPUT_POST, 'version'));
        $releaseDate = trim((string) filter_input(INPUT_POST, 'releaseDate'));
        // Convert the entered date into a timestamp for validation.
        $timestamp = strtotime($releaseDate);
        // Check that all required fields contain valid information.
        if ($productCode === '' || $name === '' ||
            $version === '' || $timestamp === false) {
            $error = 'Enter all required fields and a valid release date.';
            include('../errors/error.php');
            break;
        }
        // Convert the valid date into MySQL's YYYY-MM-DD format.
        $mysqlDate = date('Y-m-d', $timestamp);
        // Prepare the SQL statement to insert the new product.
        $statement = $db->prepare(
            'INSERT INTO products
                (productCode, name, version, releaseDate)
             VALUES
                (:productCode, :name, :version, :releaseDate)'
        );
        // Execute the prepared statement with the submitted product values.
        $statement->execute([
            ':productCode' => $productCode,
            ':name' => $name,
            ':version' => $version,
            ':releaseDate' => $mysqlDate
        ]);
        // Return to the product list after saving.
        header('Location: index.php');
        exit();
    case 'delete_product':
        // Retrieve the product code selected for deletion.
        $productCode = filter_input(INPUT_POST, 'productCode');
        // Prepare a SQL statement to delete the selected product.
        $statement = $db->prepare(
            'DELETE FROM products WHERE productCode = :productCode'
        );
        // Execute the deletion using the selected product code.
        $statement->execute([
            ':productCode' => $productCode
        ]);
        // Return to the updated product list.
        header('Location: index.php');
        exit();
    default:
        // Display an error when the requested action is not supported.
        $error = 'Invalid product action.';
        include('../errors/error.php');
        break;
}
