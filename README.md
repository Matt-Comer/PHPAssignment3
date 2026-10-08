
# SportsPro Technical Support
### PHP & MySQL | Web Application Development

**Developer:** Matthew Comer  
**Institution:** triOS College  
**Program:** Mobile Web & App Development  
**Course:** MWD4A — PHP & MySQL  
**Project:** Assignment 3  
**Year:** 2026

---

## Project Overview

SportsPro Technical Support is a database-driven web application developed using PHP, MySQL, HTML, and CSS.

The application provides an administrative interface for managing sports software products, maintaining customer information, and recording technical support incidents.

The project demonstrates server-side development, relational database integration, form processing, SQL queries, data validation, and structured application design.

The interface features a responsive dark-themed design with consistent navigation, forms, tables, and feedback messages.

## Core Features

### 1. Product Management

The Product Manager provides functionality for maintaining software product records.

**Capabilities:**
- Display products retrieved from MySQL.
- Add products using a form.
- Delete existing products.
- Record product codes, names, versions, and release dates.
- Accept standard date input formats.
- Convert submitted dates for database storage.
- Display release dates in `n-j-Y` format.

### 2. Customer Management

The Customer Manager provides a searchable directory and customer maintenance interface.

**Capabilities:**
- Display all customers in alphabetical order.
- Search customers using partial last-name matches.
- Display customer names, email addresses, and cities.
- Select individual customer records.
- View and update customer information.
- Populate the country dropdown from the database.
- Automatically select the customer's saved country.
- Save customer updates using prepared SQL statements.
- Display confirmation after successful updates.

### 3. Technical Support Incident Management

The Incident Manager supports the creation of technical support records associated with registered customers.

**Workflow:**

1. Locate a customer using their email address.
2. Retrieve the customer's account information.
3. Identify products registered to that customer.
4. Populate a product dropdown using registered products only.
5. Enter an incident title and description.
6. Validate the submitted information.
7. Save the incident to MySQL.
8. Display a successful submission message.

**Validation:**
- Required fields must be completed.
- The customer must exist.
- The selected product must be registered to that customer.
- Invalid submissions are rejected.

## Technologies

| Technology | Purpose |
|---|---|
| PHP | Server-side application logic |
| MySQL / MariaDB | Relational database |
| PDO | Database connections and prepared statements |
| HTML5 | Application structure and forms |
| CSS3 | Responsive styling and interface design |
| Apache | Local web server |
| XAMPP | Local development environment |
| phpMyAdmin | Database administration and SQL exports |
| Git | Version control |
| GitHub | Source code hosting |

## Application Architecture

The project separates database operations, request handling, and shared presentation components.

**Controllers**

The Product Manager, Customer Manager, and Incident Manager process incoming requests and direct application workflows.

PHP `switch` statements route supported actions to the appropriate operations.

**Database Layer**

PDO provides database connectivity and supports parameterized queries.

Incident-specific database functions are maintained separately in `model/incident_db.php`.

**Presentation Layer**

Shared header and footer components provide a consistent interface across management pages.

The central stylesheet controls typography, spacing, colors, forms, buttons, tables, and responsive behavior.

## Project Structure

```text
PHPAssignment3/
├── README.md
├── tech_support.sql
└── project_start/
    └── tech_support/
        ├── index.php
        ├── main.css
        ├── tech_support.sql
        ├── under_construction.php
        ├── customer_manager/
        │   └── index.php
        ├── product_manager/
        │   └── index.php
        ├── incident_manager/
        │   └── index.php
        ├── model/
        │   ├── database.php
        │   └── incident_db.php
        ├── errors/
        │   ├── error.php
        │   └── database_error.php
        ├── view/
        │   ├── header.php
        │   └── footer.php
        └── nbproject/
```

## Database Design

The SportsPro database is named `tech_support`.

It contains the following tables:

| Table | Purpose |
|---|---|
| `customers` | Customer account information |
| `products` | Software product records |
| `registrations` | Relationships between customers and registered products |
| `incidents` | Technical support incidents |
| `countries` | Country reference information |
| `technicians` | Technical support personnel |
| `administrators` | Administrative account information |

### Relational Database Concepts

The application demonstrates relationships between customer accounts, software products, and technical support incidents.

The incident workflow uses database joins to retrieve products associated with a selected customer.

Prepared statements are used to safely execute queries containing submitted values.

## Installation and Setup

### Prerequisites

- PHP 8.x
- MySQL or MariaDB
- Apache
- XAMPP or a comparable PHP development environment
- A modern web browser

### Step 1 — Install the Project

Clone the repository into the XAMPP `htdocs` directory.

```bash
git clone https://github.com/YOUR-USERNAME/PHPAssignment3.git
```

### Step 2 — Start the Services

Open XAMPP and start:

- Apache
- MySQL

### Step 3 — Import the Database

Open phpMyAdmin:

`http://localhost/phpmyadmin`

Create a database named:

`tech_support`

Select the database, open **Import**, and import the root-level `tech_support.sql` file.

### Step 4 — Configure Database Access

Open:

`project_start/tech_support/model/database.php`

Confirm the database connection settings match your local MySQL installation.

The application expects the `tech_support` database.

### Step 5 — Launch the Application

Open the following address in your browser:

`http://localhost/PHPAssignment3/project_start/tech_support/index.php`

### Management Pages

**Product Manager**

`http://localhost/PHPAssignment3/project_start/tech_support/product_manager/`

**Customer Manager**

`http://localhost/PHPAssignment3/project_start/tech_support/customer_manager/`

**Incident Manager**

`http://localhost/PHPAssignment3/project_start/tech_support/incident_manager/`

## Assignment Requirements

This project addresses four PHP/MySQL development exercises.

| Exercise | Implementation |
|---|---|
| Project 6-5 | Create technical support incidents for registered customer products |
| Project 7-1 | Populate the customer country dropdown from MySQL |
| Project 8-1 | Implement switch-based controller routing |
| Project 10-1 | Process and format product release dates |

Additional work includes the customer directory, partial-name searching, improved customer lookup, form validation, and a redesigned application interface.

## Testing

The application has been manually tested in the local XAMPP environment.

**Completed checks:**
- Product creation and deletion.
- Product release-date formatting.
- Customer search and record retrieval.
- Customer information updates.
- Country dropdown population and selection.
- Customer directory with email addresses.
- Registered-product filtering.
- Incident form required-field validation.
- Successful technical support incident creation.

## Development Approach

The application was developed using a structured, incremental workflow.

Features were implemented and tested individually before integration.

The development process emphasizes:

- Separation of database logic and presentation.
- Readable and documented source code.
- Prepared SQL statements.
- Server-side input validation.
- Safe HTML output.
- Consistent user interface design.
- Functional testing against a local database.
- Git-based version control.

## Project Scope

SportsPro Technical Support is an academic application developed to demonstrate PHP and MySQL programming concepts.

The implemented management features are intended for local development and assessment rather than production deployment.

Additional administrative features may be expanded in future development.

## Author

**Matthew Comer**  
Mobile Web & App Development  
triOS College — 2026

*Developed as part of PHP/MySQL coursework, demonstrating practical full-stack web application development.*
