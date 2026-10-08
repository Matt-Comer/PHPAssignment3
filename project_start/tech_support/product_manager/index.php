
<?php
// Connect to the SportsPro database.
require_once('../model/database.php');

// Get the controller action from the submitted form or URL.
$action = filter_input(INPUT_POST, 'action');

if ($action === null) {
    $action = filter_input(INPUT_GET, 'action');
}

// Display the product list when no action is specified.
if ($action === null || $action === '') {
    $action = 'list_products';
}

// Route requests to the appropriate product operation.
switch ($action) {
    case 'list_products':
        // Retrieve products in alphabetical order.
        $statement = $db->query(
            'SELECT productCode, name, version, releaseDate
             FROM products
             ORDER BY name'
        );
        $products = $statement->fetchAll(PDO::FETCH_ASSOC);

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

                <?php foreach ($products as $product) : ?>
                    <tr>
                        <td><?= htmlspecialchars($product['productCode']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['version']) ?></td>

                        <!-- Display month-day-year without leading zeros or time. -->
                        <td>
                            <?= date('n-j-Y', strtotime($product['releaseDate'])) ?>
                        </td>

                        <td>
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
        // Display the form for entering a new product.
        include('../view/header.php');
        ?>
        <main>
            <h1>Add Product</h1>

            <form action="index.php" method="post">
                <input type="hidden" name="action" value="add_product">

                <label>Code:</label>
                <input type="text" name="productCode" required>
                <br>

                <label>Name:</label>
                <input type="text" name="name" required>
                <br>

                <label>Version:</label>
                <input type="text" name="version" required>
                <br>

                <!-- Text input accepts standard date formats. -->
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
        // Read the submitted product information.
        $productCode = trim((string) filter_input(INPUT_POST, 'productCode'));
        $name = trim((string) filter_input(INPUT_POST, 'name'));
        $version = trim((string) filter_input(INPUT_POST, 'version'));
        $releaseDate = trim((string) filter_input(INPUT_POST, 'releaseDate'));

        // Convert a valid date to the format MySQL expects.
        $timestamp = strtotime($releaseDate);

        if ($productCode === '' || $name === '' ||
            $version === '' || $timestamp === false) {
            $error = 'Enter all required fields and a valid release date.';
            include('../errors/error.php');
            break;
        }

        $mysqlDate = date('Y-m-d', $timestamp);

        // Insert the product using a prepared SQL statement.
        $statement = $db->prepare(
            'INSERT INTO products
                (productCode, name, version, releaseDate)
             VALUES
                (:productCode, :name, :version, :releaseDate)'
        );

        $statement->execute([
            ':productCode' => $productCode,
            ':name' => $name,
            ':version' => $version,
            ':releaseDate' => $mysqlDate
        ]);

        // Return to the updated product list.
        header('Location: index.php');
        exit();

    case 'delete_product':
        // Identify the product selected for deletion.
        $productCode = filter_input(INPUT_POST, 'productCode');

        // Delete the selected product from MySQL.
        $statement = $db->prepare(
            'DELETE FROM products WHERE productCode = :productCode'
        );

        $statement->execute([
            ':productCode' => $productCode
        ]);

        header('Location: index.php');
        exit();

    default:
        // Display an error for unsupported controller actions.
        $error = 'Invalid product action.';
        include('../errors/error.php');
        break;
}
