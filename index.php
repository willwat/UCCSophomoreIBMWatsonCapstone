<!DOCTYPE html>
<html lang="en">
<head>	
	<title>Watson AI</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<header>
		<h1>Watson AI</h1>
	</header>
	<main>	<form>
		Input Phrase: <input type="text" name="phrase"><br>
		<input type="submit" value="Teach AI">
	</form>
	<form method="post" action="index.php">
		Input Image: <input type="text" id="tbImage" name="tbImage"><br>
		<input type="submit" value="Test AI">
	</form>
	<?php
		
		function classifyImage($imageURL){
		 
		 $urlBase = "https://gateway-a.watsonplatform.net/visual-recognition/api/v3/classify?";
      $apiKey = "6ef1a6180f7dca04b4209c6134811502580db47c";
      $classifierIDS = "Everything_1037210565";
      $threshold = "0";
      $version = "2016-05-20";
		 
        return IBMWatsonBestMatch($urlBase . "api_key=" . $apiKey . "&url=$imageURL&" . "threshold=" . $threshold . "&" . "classifier_ids=" . $classifierIDS . "&" . "version=" . $version);
    }

    // Finds the best matching class from IBMWatson image recognition URL
     function IBMWatsonBestMatch($url){

        // Get content from URL and store in contentFromURL using curl.
        $curlInit = curl_init($url);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);
        $contentFromURL = curl_exec($curlInit);
        curl_close($curlInit);

        // Pare the json into a associative array, without true it becomes a generic php object
        $parsedJson = json_decode($contentFromURL, true);

        // Path to get to the classes/classifications we make for our watson api
        $jsonClassifications = $parsedJson["images"][0]["classifiers"][0]["classes"];

        // Setting up variables to store the highest store and the best match
        $highestScore = 0;
        $bestMatch = "";

        // $jsonClassifications is an array of associative arrays, each associative array contains a "class" which is what we called the classification,
        // and a score corresponding to how well the given images matches up with each class based on each classes training images.
        foreach($jsonClassifications as $classification){
            if($classification["score"] > $highestScore){
                $bestMatch = $classification["class"];
                $highestScore = $classification["score"];
            }
        }

        return $bestMatch;
    }
		
		//require('IBMWatsonFunctionality.php');
	 	if (isset($_POST['tbImage'])){
			$imgUrl = $_POST['tbImage'];
			$imgClassification = classifyImage($imgUrl);
			echo "IBMWatson thinks this is a... $imgClassification";
		}
	?>
	</main>
	<footer>
		<a href="mailto:powerslt@mail.uc.edu">powerslt@mail.uc.edu</a><br>
		This page was created by Liam Powers<br>
	</footer>
</body>
</html>
