<?php
/**
 * Created By: William Watson
 * Description: Class automating IBMWatson Image Recognition functionality for UCC Capstone Project.
 */

require('ImageDownloader.php');
require('Utils.php');

class IBMWatsonFunctionality
{
	//POST Variables IBM Watson always needs.
    private static $urlClassifiersBase = "https://gateway-a.watsonplatform.net/visual-recognition/api/v3/classifiers?";
    private static $urlClassifyBase = "https://gateway-a.watsonplatform.net/visual-recognition/api/v3/classify?";
    private static $apiKey = "6ef1a6180f7dca04b4209c6134811502580db47c";
    private static $threshold = "0";
    private static $version = "2016-05-20";
    private static $trainingFolderFullPath = "trainingFolders";

	// Builds IBM Watson API link and feeds it into best match function, returns the best match to the imageURL parameter.
    static function classifyImage($imageURI){

        //Determines if the URI is a file or if it is a URL, then returns the result of the appropriate function.
        if(is_file(realpath($imageURI))){
           return IBMWatsonFunctionality::IBMWatsonBestMatchFromFile($imageURI);
        }elseif(filter_var($imageURI, FILTER_VALIDATE_URL)){
            return IBMWatsonFunctionality::IBMWatsonBestMatch(IBMWatsonFunctionality::$urlClassifyBase . "api_key=" . IBMWatsonFunctionality::$apiKey . "&url=$imageURI&" . "threshold=" . IBMWatsonFunctionality::$threshold . "&" . "classifier_ids=" . join(',' ,IBMWatsonFunctionality::getClassifierIDS()) . "&" . "version=" . IBMWatsonFunctionality::$version);
        }else{
            throw new Exception();
        }
    }

    // Finds the best matching class from IBMWatson image recognition URL
    static function IBMWatsonBestMatch($watsonApiUrl){

        // Get content from URL and store in contentFromURL using curl.
        $curlInit = curl_init($watsonApiUrl);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, 1);
        $contentFromURL = curl_exec($curlInit);
        curl_close($curlInit);


		// Throw exception if the request is bad.
		if(!isset($contentFromURL) || isset(json_decode($contentFromURL, true)["error"])){
			$error = 'Invalid image URL';
			throw new Exception($error);
		}

        // Parse the json into a associative array, without true it becomes a generic php object
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

    static function IBMWatsonBestMatchFromFile($imgFile){
        // Initialize the curl request passing URL base.
        $curlInit = curl_init(IBMWatsonFunctionality::$urlClassifyBase . 'api_key=' . IBMWatsonFunctionality::$apiKey . '&version=' . IBMWatsonFunctionality::$version);

        // Post variables for the CURL request.
        $postVariables = array(
            "images_file" => new CURLFile($imgFile),
            "threshold" => IBMWatsonFunctionality::$threshold,
            "classifier_ids" => join(', ' ,IBMWatsonFunctionality::getClassifierIDS())
        );

        // Setting options for the curl request.
        curl_setopt($curlInit, CURLOPT_POST,1);
        curl_setopt($curlInit, CURLOPT_POSTFIELDS, $postVariables);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER,1);

        // Storing the CURL requests response
        $APIResponse = curl_exec ($curlInit);

        // Close curl request.
        curl_close($curlInit);

        // Parse the json into a associative array, without true it becomes a generic php object
        $parsedJson = json_decode($APIResponse, true);

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

    // Returns array of all the classifier IDS in the watson instance
    static function getClassifierIDS(){
        $returnedClassifierIDsArray = array();

        // Setup CURL request, execute CURL request and store response, close CURL request.
        $curlInit = curl_init(IBMWatsonFunctionality::$urlClassifiersBase . "api_key=" . IBMWatsonFunctionality::$apiKey . "&version=" . IBMWatsonFunctionality::$version);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, 1);
        $contentFromURL = curl_exec($curlInit);
        curl_close($curlInit);

        // Parsed json from response into associative array.
        $parsedJson = json_decode($contentFromURL, true);

        // Grabs associative array of associative arrays holding each classifiers information
        $jsonClassifiers = $parsedJson["classifiers"];

        // Grabs the classifierID from each classifier and stores it in the returned array.
        foreach ($jsonClassifiers as $classifier){
            array_push($returnedClassifierIDsArray, $classifier["classifier_id"]);
        }
        return $returnedClassifierIDsArray;
    }

    // Deletes a single classifier ID of watson's.
	static function deleteClassifierID($classifierID){
        // Initialize curl request.
		$curlInit = curl_init();

		// Set CURL options and execute delete request on classifier ID then close the request.
		curl_setopt($curlInit, CURLOPT_URL, 'https://gateway-a.watsonplatform.net/visual-recognition/api/v3/classifiers/' . $classifierID . '?api_key=' . IBMWatsonFunctionality::$apiKey . '&version=' . IBMWatsonFunctionality::$version);
		curl_setopt($curlInit, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);
		curl_exec($curlInit);
		curl_close($curlInit);
	}

	// Function to tell if watson is training
	static function isWatsonTraining(){
        // Setup CURL request, execute CURL request and store response, close CURL request.
        $curlInit = curl_init(IBMWatsonFunctionality::$urlClassifiersBase . "api_key=" . IBMWatsonFunctionality::$apiKey . "&version=" . IBMWatsonFunctionality::$version);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, 1);
        $contentFromURL = curl_exec($curlInit);
        curl_close($curlInit);

        // Parsed json from response into associative array.
        $parsedJson = json_decode($contentFromURL, true);

        // Grabs associative array of associative arrays holding each classifiers information
        $jsonClassifiers = $parsedJson["classifiers"];

        // Checks if any statuses aren't ready and returns true if any aren't
        foreach ($jsonClassifiers as $classifier){
            if($classifier["status"] != 'ready') return true;
        }

        return false;
    }

    // Deletes all of the classifierIDs Watson has.
	static function deleteAllClassifierIDs(){
        $classifierIDS = IBMWatsonFunctionality::getClassifierIDS();

        foreach($classifierIDS as $classifierID){
            IBMWatsonFunctionality::deleteClassifierID($classifierID);
        }
    }

    // Takes a search phrase and trains watson on the first 20 images of a google search with that search phrase.
	static function trainWatson($imgSearchPhrase){

        //downloads the images to /trainingFolders/[search term]/[filename].jpg
        $search = htmlspecialchars($imgSearchPhrase);
        $ImageDownloader = new ImageDownloader();
        $ImageDownloader->get_all_items(20, $search);

        // If watson has classifierIDS we have to delete them for retraining on the free plan.
        if(count(IBMWatsonFunctionality::getClassifierIDS()) > 0){
            IBMWatsonFunctionality::deleteAllClassifierIDs();
            sleep(7);
        }

        // Zips all of the image folders for training.
        Utils::zipAllDirectoriesIn(IBMWatsonFunctionality::$trainingFolderFullPath);

        // Gets an array of the zip folder names for training with the curl request.
        $zipFoldersArray = Utils::getAllZipFilesFrom(IBMWatsonFunctionality::$trainingFolderFullPath);
		$postVariables = array();

		// Sets up the postVariables for the curl request.
		foreach ($zipFoldersArray as $zipFilePath){
		    $zipFileName = basename($zipFilePath);

		    $zipFileName = rtrim($zipFileName, '.ZIP');
            $zipFileName = rtrim($zipFileName, '.zip');

            $zipFileName = ucfirst(strtolower($zipFileName));

            $postVariables[$zipFileName . "_positive_examples"] = new CurlFile($zipFilePath);
        }

        // Sets classifierIDs name
        $postVariables['name'] = 'Everything';

        // Curl request options are set and the curl request is executed.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://gateway-a.watsonplatform.net/visual-recognition/api/v3/classifiers?api_key=6ef1a6180f7dca04b4209c6134811502580db47c&version=2016-05-20");
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postVariables);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

		$information = curl_getinfo($ch);
		$result= curl_exec($ch);
		curl_close ($ch);
	}
}
?>