<?php
// Include database class
include 'db.class.php';

// Define configuration
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "phppass4");
define("DB_NAME", "test");

// Instantiate database.
$database = new Database();

//new insert
$database->query('INSERT INTO mytable (FName, LName, Age, Gender) VALUES (:fname, :lname, :age, :gender)');
$database->bind(':fname', 'John');
$database->bind(':lname', 'Smith');
$database->bind(':age', '24');
$database->bind(':gender', 'male');
$database->execute();
//confirm insert
echo $database->lastInsertId();

//begin transaction (multiple dependent queries)
$database->beginTransaction();
$database->query('INSERT INTO mytable (FName, LName, Age, Gender) VALUES (:fname, :lname, :age, :gender)');
//first set of parameters
$database->bind(':fname', 'Jenny');
$database->bind(':lname', 'Smith');
$database->bind(':age', '23');
$database->bind(':gender', 'female');
$database->execute();
//second set of parameters
$database->bind(':fname', 'Jilly');
$database->bind(':lname', 'Smith');
$database->bind(':age', '25');
$database->bind(':gender', 'female');
$database->execute();
//confirm insert
echo $database->lastInsertId();
//end transaction
$database->endTransaction();

//select single row
$database->query('SELECT FName, LName, Age, Gender FROM mytable WHERE FName = :fname');
$database->bind(':fname', 'Jenny');
$row = $database->single();
//display result
echo "<pre>";
print_r($row);
echo "</pre>";

//select multiple rows
$database->query('SELECT FName, LName, Age, Gender FROM mytable WHERE LName = :lname');
$database->bind(':lname', 'Smith');
$rows = $database->resultset();
//display resultset
echo "<pre>";
print_r($rows);
echo "</pre>";
//display count of rows returned
echo $database->rowCount();