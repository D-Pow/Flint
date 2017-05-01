<?php

function uploadMedia($uid) {
    try {
        //$_FILES organized by [varName][key][indexOfFileUpload]
        //shorten to $upfiles[key][index]
        $upfiles = $_FILES['upfiles'];
        $fileCount = count($upfiles['name']);

        //no files were uploaded
        if ($fileCount == 1 && $upfiles['name'][0] == '') {
            return true;
        }

        //process files
        for ($i = 0; $i < $fileCount; $i++) {
            //Undefined | Multiple Files | $_FILES Corruption Attack
            //If this request falls under any of them, treat it invalid
            if (!isset($upfiles['error'][$i]) 
                || is_array($upfiles['error'][$i])) {
                throw new RuntimeException('Invalid parameters.');
            }

            //Check $upfiles['error'][$i] value
            switch ($upfiles['error'][$i]) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('No file sent.');
                    break;
                case UPLOAD_ERR_INI_SIZE:
                    throw new RuntimeException('Exceeded filesize limit.');
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('Exceeded filesize limit.');
                    break;
                default:
                    throw new RuntimeException('Unknown error.');
                    break;
            }
    
            //Also check filesize here
            if ($upfiles['size'][$i] > 10400000) { //10 mb
                throw new RuntimeException('Exceeded filesize limit.');
            }

            //Get extension
            $t = explode('.', $upfiles['name'][$i]);
            $ext = $t[count($t)-1]; //get text after final dot
    
            //Name it uniquely
            $name = sprintf($_SERVER['DOCUMENT_ROOT'].'/Flint/uploads/%s.%s',
                        hash_file("sha256", $upfiles['tmp_name'][$i]), $ext
                    );

            //check if name in database already
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/db.php');
            $db = DB::getInstance();
            $results = $db->runSelect(
                "SELECT mid FROM Media WHERE filename=:f;",
                [':f' => $name]);
            //if the name exists, keep hashing it till it doesn't
            while ($results) {
                $name = sprintf($_SERVER['DOCUMENT_ROOT'].'/Flint/uploads/%s.%s',
                        hash("sha256", $name), $ext
                    );
                $results = $db->runSelect(
                    "SELECT mid FROM Media WHERE filename=:f;",
                    [':f' => $name]);
            }

            //move to directory, or error if failed
            if (!move_uploaded_file($upfiles['tmp_name'][$i], $name)) {
                throw new RuntimeException('Failed to move uploaded file.');
            }

            //update database
            $q = "INSERT INTO Media(filename) VALUES (:f);";
            $success = $db->runUpdate($q, [':f' => $name]);
            if (!$success) {
                return false;
            }
            $results = $db->runSelect("SELECT mid FROM Media WHERE filename=:f;",
                [':f' => $name]);
            if (!$success) {
                return false;
            }
            $mid = $results[0]['mid'];
            $q = "INSERT INTO Umedia VALUES (:u, :m);";
            $success = $db->runUpdate($q, [':u' => $uid, ':m' => $mid]);
            if (!$success) {
                return false;
            }
        }
        return true;
    } catch (RuntimeException $e) {
        echo $e->getMessage();
        return false;
    }
}

/**
 * Sorts the media to be displayed based on type
 */
function loadMedia($filenames) {
    $media = ['images' => [], 'sounds' => [], 'videos' => [], 'other' => []];
    foreach ($filenames as $file) {
        $mime = mime_content_type($file); //file type + extension
        $type = explode('/', $mime)[0];   //only the type
        if ($type == 'image') {
            $media['images'][] = $file;
        } else if ($mime == 'application/octet-stream') {
            $media['sounds'][] = $file;
        } else if ($type == 'video') {
            $media['videos'][] = $file;
        } else {
            $media['other'][] = $file;
        }
    }
    return $media;
}

?>