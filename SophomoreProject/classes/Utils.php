<?php
/**
 * Created By: William Watson
 * Description: Utilities class for UCCIBMWatson Capstone Project. Class contains general purpose functions for the project.
 */

class Utils
{
    //Debugging function that prints information on a variable to the browsers console.
    static function console_log($data){
        echo '<script>';
        echo 'console.log('. json_encode($data) .')';
        echo '</script>';
    }

    //Gets the size of a file directory in kb.
    static function getDirectorySizeInKb($directoryPath){
        $returnedSize = 0;
        $directory = new DirectoryIterator(realpath($directoryPath));

        foreach ($directory as $file) {
            if (!$file->isDot()) {
                $returnedSize += $file->getSize()/1000;
            }
        }

        return $returnedSize;
    }

    //Deletes all of the files in a given directory.
    static function deleteAllFilesIn($directoryPath){
        $directory = new DirectoryIterator(realpath($directoryPath));

        foreach ($directory as $file) {
            if (!$file->isDot()) {
                unlink($file->getRealPath());
            }
        }
    }

    // Zips all of the subdirectories of a given directory.
    static function zipAllDirectoriesIn($parentDirectoryPath){
        $directory = new DirectoryIterator(realPath($parentDirectoryPath));

        // For each folder in the directory a zip file is created and openened with the same name, and all of the files are copied to the zip.
        foreach($directory as $folder){
            // Makes sure the folder is a directory and isn't a dot.
            if($folder->isDir() && !$folder->isDot()){
                // A directory iterator is made for each folder to iterate through their files and add them to the zip.
                $folderIterator = new DirectoryIterator($folder->getRealPath());
                $zipFile = new ZipArchive();
                $zipFileFullName = $folderIterator->getRealPath() . '.zip';

                // If the zip file already exists we don't do anything.
                if(!is_file($zipFileFullName)){
                    $zipFile->open($zipFileFullName, ZipArchive::CREATE or ZipArchive::OVERWRITE);

                    foreach ($folderIterator as $file){
                        if(is_file($file->getRealPath())){
                            $filePath = $file->getRealPath();
                            $relativePath = $file->getFilename();

                            $zipFile->addFile($filePath, $relativePath);
                        }
                    }
                    $zipFile->close();
                }
            }
        }
    }

    // Returns an array of the names of the zipFiles from a given directory
    static function getAllZipFilesFrom($directoryPath){
        // Uses DirectoryIterator for the given directory
        $directory = new DirectoryIterator(realpath($directoryPath));
        $returnedArray = array();

        // Looks at every file in the DirectoryIterator and looks at their extensions, the ones with zip extensions are added to the returned array.
        foreach($directory as $file){
            if(strtolower($file->getExtension()) == 'zip'){
                array_push($returnedArray, $file->getRealPath());
            }
        }

        return $returnedArray;
    }


}
?>