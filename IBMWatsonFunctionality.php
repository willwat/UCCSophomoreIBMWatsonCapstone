<?php
/**
 * Author: William Watson
 *
 */

class IBMWatsonFunctionality
{
    private static $urlBase = "https://gateway-a.watsonplatform.net/visual-recognition/api/v3/classify?";
    private static $apiKey = "6ef1a6180f7dca04b4209c6134811502580db47c";
    private static $classifierIDS = "Everything_1037210565";
    private static $threshold = "0";
    private static $version = "2016-05-20";

    static function classifyImage($imageURL){
        return IBMWatsonFunctionality::IBMWatsonBestMatch(IBMWatsonFunctionality::$urlBase . "api_key=" . IBMWatsonFunctionality::$apiKey . "&url=$imageURL&" . "threshold=" . IBMWatsonFunctionality::$threshold . "&" . "classifier_ids=" . IBMWatsonFunctionality::$classifierIDS . "&" . "version=" . IBMWatsonFunctionality::$version);
    }

    // Finds the best matching class from IBMWatson image recognition URL
    static function IBMWatsonBestMatch($url){

        // Get content from URL and store in contentFromURL using curl.
        $curlInit = curl_init($url);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, 1);
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
}
?>