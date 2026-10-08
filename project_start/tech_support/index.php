
<?php
// Load the shared SportsPro header and page styling.
include 'view/header.php';
?>

<main>
    <!-- Main navigation for the SportsPro application. -->
    <nav>
        <!-- Administrative tools for managing business records. -->
        <h2>Administrators</h2>
        <ul>
            <!-- Open the product management controller. -->
            <li><a href="product_manager/">Manage Products</a></li>

            <!-- Technician management has not been implemented yet. -->
            <li><a href="under_construction.php">Manage Technicians</a></li>

            <!-- Open the customer management page and country drop-down. -->
            <li><a href="customer_manager/">Manage Customers</a></li>

            <!-- Open the completed Create Incident workflow. -->
            <li><a href="incident_manager/">Create Incident</a></li>

            <!-- Keep unfinished incident features on the placeholder page. -->
            <li><a href="under_construction.php">Assign Incident</a></li>
            <li><a href="under_construction.php">Display Incidents</a></li>
        </ul>

        <!-- Technician tools for working with assigned incidents. -->
        <h2>Technicians</h2>
        <ul>
            <li><a href="under_construction.php">Update Incident</a></li>
        </ul>

        <!-- Customer tools for registering purchased products. -->
        <h2>Customers</h2>
        <ul>
            <li><a href="under_construction.php">Register Product</a></li>
        </ul>
    </nav>
</main>

<?php
// Load the shared footer and close the HTML document.
include 'view/footer.php';
?>
