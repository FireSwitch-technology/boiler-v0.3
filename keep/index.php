<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "boiler";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Path to your CSV file
$csvFilePath = 'shortlet.csv';

// Read the CSV file
$csvFile = fopen($csvFilePath, 'r');

// Check if the file is opened successfully
if ($csvFile !== FALSE) {
    // Read the header row (optional)
    $header = fgetcsv($csvFile);

    // Loop through the remaining rows
    while (($data = fgetcsv($csvFile)) !== FALSE) {
        // Build the SQL INSERT query
        $sql = "INSERT INTO shortlet (" . implode(', ', $header) . ") VALUES ('" . implode("', '", $data) . "')";

        // Execute the query
        if ($conn->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Close the CSV file
    fclose($csvFile);

    echo "Data imported successfully.";

} else {
    echo "Error opening the CSV file.";
}

// Close the database connection
$conn->close();

?>
