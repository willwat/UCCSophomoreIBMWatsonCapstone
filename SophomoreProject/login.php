<?php
require('classes/IBMWatsonFunctionality.php');
require('templates/header.html');
?>

<?php
if(isset($_POST['username']) && isset($_POST['password'])){
	//Variables for session variables
	$userName = $_POST['username'];
	$passWord = $_POST['password'];
	$success = false;	

	//If the submitted username and password match the ones in this line set success and stop going through the lines
	if(Utils::authenticateUser($userName, $passWord)){
		$success = true;
	}
	
	//If the previous part was succesful set the session variables and redirect to the home page
	if($success){
		$_SESSION['username'] = $userName;
		echo "<br> Hi $userName you have been logged in. <br>";
		header("Location: index.php");
		exit();
	}else{
		echo "<span class=\"text-danger\">Invalid Username or Password</span>";
	}

}

//The login form
print '<form action="login.php" method="post">
        <p>Username: <input type="username" class="btn border w-50" name="username" size = "19"/></p>
        <p>Password: <input type="password" class="btn border w-50" name="password" size = "19"/></p>
        <p><div class="w-100 text-right"><input type="submit" class="w-25 btn-danger" name="submit" value="Log in"/></div></p>
       </form>';
?>

<?php
require('templates/footer.html');
?>