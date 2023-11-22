<?php

final class TableCreator
{
    private $conn;

    // Constructor
    public function __construct($host, $user, $password, $dbname)
    {
        // Connect to the database
        $this->conn = new mysqli($host, $user, $password, $dbname);

        // Check the connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // Create and fill the table
        $this->create();
        $this->fill();
    }

    // Destructor
    public function __destruct()
    {
        // Close the database connection
        $this->conn->close();
    }

    // Method to create the table (accessible only within the class)
    private function create()
    {
        $query = "CREATE TABLE IF NOT EXISTS Test (
            id INT AUTO_INCREMENT PRIMARY KEY,
            script_name VARCHAR(25),
            start_time DATETIME,
            end_time DATETIME,
            result ENUM('normal', 'illegal', 'failed', 'success')
        )";

        $this->conn->query($query);
    }

    // Method to fill the table with random data (accessible only within the class)
    private function fill()
    {
        $scriptNames = ['ScriptA', 'ScriptB', 'ScriptC'];
        $results = ['normal', 'illegal', 'failed', 'success'];

        for ($i = 0; $i < 10; $i++) {
            $scriptName = $scriptNames[array_rand($scriptNames)];
            $startTime = date('Y-m-d H:i:s', mt_rand(1, time()));
            $endTime = date('Y-m-d H:i:s', mt_rand(strtotime($startTime), time()));
            $result = $results[array_rand($results)];

            $query = "INSERT INTO Test (script_name, start_time, end_time, result) VALUES ('$scriptName', '$startTime', '$endTime', '$result')";
            $this->conn->query($query);
        }
    }

    // Method to select data from the table Test according to the criterion (accessible from outside the class)
    public function get()
    {
        $query = "SELECT * FROM Test WHERE result IN ('normal', 'success')";
        $result = $this->conn->query($query);

        // Fetch the results
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
}

// Usage example:
$host = 'your_database_host';
$user = 'your_database_user';
$password = 'your_database_password';
$dbname = 'your_database_name';

$tableCreator = new TableCreator($host, $user, $password, $dbname);

// Access the get() method to fetch data
$data = $tableCreator->get();

// Display the fetched data
print_r($data);

?>

