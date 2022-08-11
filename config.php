<?php 

$server = "localhost";
$user = "root";
$pass = "";
$database = "login_register_db";    /* <------- NOME DEL MIO DATABASE SU MYSQL */

$conn = mysqli_connect($server, $user, $pass, $database);

if (!$conn) {
    die("<script>alert('Connessione Fallita.')</script>");
}

?>