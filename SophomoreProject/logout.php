<?php
require('templates/header.html');
?>

<?php
//End the session to log out
$_SESSION = array();
session_destroy($_SESSION);
//redirect to home page
header("Location: index.php");
exit();
?>

<?php
require('templates/footer.html');
?>