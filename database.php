<?php
session_start();
$server = 'localhost3306.mysql.database.azure.com';
$username = 'localhost3306';
$password = 'zh102657.';
$database = '353projj';
 
$conn = mysqli_init();
mysqli_ssl_set($conn,NULL,NULL, "C:\MinGW\DigiCertGlobalRootCA.crt", NULL, NULL);
mysqli_real_connect($conn, "localhost3306.mysql.database.azure.com", "localhost3306", "zh102657.", "353projj", 3306, MYSQLI_CLIENT_SSL);
// Check for errors
if(mysqli_connect_errno()){
 echo mysqli_connect_error();
}
?>