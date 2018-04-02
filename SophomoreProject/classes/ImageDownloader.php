<?php
/**
 * Created By: Kellam Spencer
 * Description: Class downloading images from google and storing them in a directory.
 */

class ImageDownloader
{
    //TODO: Make sure images are valid before storing them
    //Takes the raw google image data and converts it to a readable format.
    private function format_object($object){
        $formatted_object = [];
        $formatted_object['image_format'] = $object['ity'];
        $formatted_object['image_height'] = $object['oh'];
        $formatted_object['image_width'] = $object['ow'];
        $formatted_object['image_link'] = $object['ou'];
        $formatted_object['image_description'] = $object['pt'];
        $formatted_object['image_host'] = $object['rh'];
        $formatted_object['image_source'] = $object['ru'];
        $formatted_object['image_thumbnail_url'] = $object['tu'];
        return $formatted_object;
    }
    /*
    * Takes the response from google and returns one an image in json format
    * Returns an array with the formatted object and the position the end of the object was at
    * Allows for looping through the result, just get the substr of $result past the end of the object
    */
    private function get_item($result){
        $start_line = strpos($result, 'class="rg_meta notranslate">');
        //json image data starts after this line
        $start_object = strpos($result,'{', $start_line + 1);
        //find first curly brace
        $end_object = strpos($result, '</div>', $start_object + 1);
        //find where the object ends, right before div
        $object_raw = substr($result, $start_object, $end_object-$start_object);
        echo $object_raw;
        //get the whole substring of the json data
        $decoded_object = (array)json_decode($object_raw);
        //convert json data to an array
        $formatted_object = $this->format_object($decoded_object);
        //format the object with descriptive tags
        return array ($formatted_object, $end_object);
        //return the object and the position at the end of the obj
    }
    public function get_all_items($limit, $search){
        $result = $this->get_google_response($search);
        //get response from google
        if(!is_dir('trainingFolders/' . $search)){
            //if the dir doesn't exist, make one
            mkdir('trainingFolders/' . $search, 0777, true);
        }
        $count = 0;
        //iterator
        while ($count < $limit){
            $item = $this->get_item($result);
            $object = $item[0];
            $end_object = $item[1];
            //get the image array object
            if($object['image_format'] = 'jpg'){
                //google search only searches for jpgs, if something else shows up, don't download it

                $success = $this->download_image($object['image_link'], $object['image_format'], $count, $search);
                //download the image at the source link specified from the json object
                //image_format and count are supplied to give it a name
                //search specifies where to store the image
                if($success)
                    $count++;
                //increase the count
            }
            $result = substr($result, $end_object, strlen($result)-$end_object);
            //cut down the result based on where the end of the last object was
        }
        return $count;
    }
    private function curl($url){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.27 Safari/537.17');
        //user agent makes sure that the response is suited for the program
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($ch);
    }
    private function get_google_response($search){
        $url = 'https://www.google.com/search?as_st=y&tbm=isch&as_epq=&as_oq=&as_eq=&cr=&as_sitesearch=&safe=images&tbs=isz:lt,islt:svga,itp:photo,ift:jpg&as_q=' . $search;
        return $this->curl($url);
    }
    //download the image at the source link specified from the json object
    //image_format and count are supplied to give it a name
    //search specifies where to store the image
    //returns true if image downloaded successfully
    private function download_image($image_link, $image_format, $count, $search){
        try{
            $result = $this->curl($image_link);
            //get image
            $finfo = new finfo();
            $image_info = $finfo->buffer($result);
            //make sure image is valid
            if(false!==strpos($image_info, 'JPEG')){
                file_put_contents('trainingFolders/'. $search . '/' . sha1($search . $count . '42') . '.' . $image_format, $result);
                //store the image in a folder, filename is an sha1 hash of the keyword, image number, and an arbitrary salt
                return true;
            }

        } catch(Exception $exception) {
            print('An error occured while downloading an image from ' . $image_link);
        }
        return false;
    }

}

?>