<!DOCTYPE html>
<html>
<head>

</head>
<body>
<?php
require("ImageDownloader.php");
//includes ImageDownloader class file

ini_set('display_errors', 1);
//Turn error reporting off for release
error_reporting(E_ALL | E_STRICT);

//set this to whatever number is necessary
$count = 20;

//if values have been posted, set the values of the search term
if(isset($_POST['search'])){
    $search = htmlspecialchars($_POST['search']);

    $ImageDownloader = new ImageDownloader();
    $images_downloaded = $ImageDownloader->get_all_items($count, $search);
    //download the images to /images/[keyword name/search term]/[filename].jpg
    
    print('<p>' . $images_downloaded . ' images downloaded!</p>');
    
}


?>

<form action="HandleForm.php" method="post">
    Search Term: <input type="text" name="search"><br>
    <input type="submit" value="Submit">
</form>


</body>
</html>