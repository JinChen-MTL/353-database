<?php
session_start();
$server = 'localhost3306.mysql.database.azure.com';
$username = 'localhost3306';
$password = 'zh102657.';
$database = '353projj';
 
$con = mysqli_init();
mysqli_ssl_set($con,NULL,NULL, "C:\MinGW\DigiCertGlobalRootCA.crt", NULL, NULL);
mysqli_real_connect($con, "localhost3306.mysql.database.azure.com", "localhost3306", "zh102657.", "353projj", 3306, MYSQLI_CLIENT_SSL);
// Check for errors
if(mysqli_connect_errno()){
 echo mysqli_connect_error();
}
// Execute a query to get sample data
$Employee = "SELECT * FROM employees";
$result = $con->query($Employee);
// Output the sample data
 
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row["MCN"]  . " - Name: " . $row["first_name"] . "<br>";
  }
} else {
  echo "0 results";
}
 
?>
