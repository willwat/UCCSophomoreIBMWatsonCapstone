<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
require('classes/IBMWatsonFunctionality.php');
require('templates/header.html');
?>
<?php
	//If user is an admin logged in then allow them to teach the AI
	if(isset($_SESSION['username'])){
	print'<form method="post" class="mb-2 pb-2 border">
			Search Phrase: <input type="text" class="btn border w-50" name="phrase"><br>
			<div class="w-100 mt-2 text-right"><input type="submit" class="btn-danger mt-4 w-50" value="Teach AI"></div>
		</form>';
	}
?>
	<br>
	<form method="post" class="mb-3 pb-2 border">
		<span class="h5">Image from:</span>
		<input type="radio" id="file" name="input" value="file" <?php if (isset($input) && $input=="file") echo "checked";?>>
		<label for="file">file</label>
		<input type="radio" id="url" name="input" value="url" <?php if (isset($input) && $input=="url") echo "checked";?>>
		<label for="url">url</label>
		<br>
		<div class="w-100 mt-2 text-right"><input type="submit" class="btn-danger mt-4 w-50" value="Select"></div>
	</form>
	<br>
	<form method="post" class="border" enctype="multipart/form-data">

		<?php
        // If the phrase is posted back and watson isn't training then we start the training
        if(isset($_POST["phrase"]) ){
            if(!IBMWatsonFunctionality::isWatsonTraining()){
                IBMWatsonFunctionality::trainWatson($_POST["phrase"]);
            }else{
                $trainingMessage = 'Watson is currently training, please wait until he is finished.';
            }
        }

        // Determines which way the user wishes to evaluate an image, then loads the proper controls.
		if(isset($_POST['input']) && $_POST['input']=="file"){
			print 'Input Image: <input type="file" class="btn border w-75" name="image"><br>';
			print '<div class="w-100 mt-2 text-right"><input type="submit" class="btn-danger w-50 mt-4 pl-1" value="Test AI"></div>';
		}else if(isset($_POST['input']) && $_POST['input']=="url"){
			print 'Input Image: <input type="text" class="btn border w-75" name="image"><br>';
			print '<div class="w-100 mt-2 text-right"><input type="submit" class="btn-danger w-50 mt-4 pl-1" value="Test AI"></div>';
		}else{
			if(!isset($_POST['image']) && !isset($_FILES['image'])){
				print '<span class="h5">Please choose one of the above options.</span><br>';
			}
		}

		// If the user enters an image URL and watson isn't training the image is evaluated
		if(isset($_POST['image'])){
            if(!IBMWatsonFunctionality::isWatsonTraining()){
                $image = $_POST['image'];
                print '<img alt="Image For Testing" style="width: 100%" src="' . $image . '"><br>';
                try{
                    print 'IBM Watson says this is a... ' . IBMWatsonFunctionality::classifyImage($image);
                } catch(Exception $e){
                    echo 'Please enter a valid image URL. If the URL you entered is an image the URL may be too large.';
                }
            }else{
                $trainingMessage = 'Watson is currently training, please wait until he is finished.';
            }

		}

		// If the user has uploaded an image file and watson isn't training the image is evaluated.
        if(isset($_FILES['image'])){
            if(!IBMWatsonFunctionality::isWatsonTraining()){
                $imageFileType = strtolower(pathinfo(basename($_FILES['image']['name']),PATHINFO_EXTENSION));

                if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif"){
                    if(Utils::getDirectorySizeInKb('uploads') > 50000){
                        Utils::deleteAllFilesIn('uploads');
                    }

                    $fileLocation = 'uploads/' . uniqid() . basename($_FILES['image']['name']);
                    if(!move_uploaded_file($_FILES["image"]["tmp_name"], $fileLocation)){
                        echo 'There was a problem uploading your image file.';
                    }

                    print '<img alt="Image For Testing" style="width: 100%" src="' . $fileLocation . '"><br>';
                    try{
                        print 'IBM Watson says this is a... ' . IBMWatsonFunctionality::classifyImage(realpath($fileLocation));
                    } catch(Exception $e){
                        echo 'There was an issue processing your image file.';
                    }
                }else{
                    echo 'Sorry, only .gif, .jpeg, .png, and .jpg files are accepted.';
                }
            }else{
                $trainingMessage = 'Watson is currently training, please wait until he is finished.';
            }
        }

        // If watson was training the trainingMessage will have been set, it will now be printed to let the user know watson is training.
        if(isset($trainingMessage)) print $trainingMessage;

		?>

	</form>
<?php
require('templates/footer.html');
?>
