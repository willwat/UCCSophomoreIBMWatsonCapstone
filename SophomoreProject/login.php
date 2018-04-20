<?php
require('templates/header.html');
?>

<?php
//Variables for session variables
$userName = $_POST['username'];
$passWord = $_POST['password'];
$adminList = file('admins.txt');
$success = false;

//For each line in the document of admins
foreach($adminList as $user){
	//Separate the username and password
	$adminDetails = explode(',', $user);
	
	//If the submitted username and password match the ones in this line set success and stop going through the lines
	if($adminDetails[0] == $userName && $adminDetails[1] == $passWord){
		$success = true;
		break;
	}
}

//If the previous part was succesful set the session variables and redirect to the home page
if($success){
	$_SESSION['username'] = $userName;
	echo "<br> Hi $userName you have been logged in. <br>";
	header("Location: index.php");
	exit();
}

//The login form
print '<form action="login.php" method="post">
        <p>Username: <input type="username" name="username" size = "20"/></p>
        <p>Password: <input type="password" name="password" size = "20"/></p>
        <p>Submit: <input type="submit" name="submit" value="Log in"/></p>
       </form>';
?>

<?php
require('templates/footer.html');
?>