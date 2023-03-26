<?php
/**
* Author: Habib urRehman
* Email : chaudryhabib2@gmail.com 
*/
namespace OldRavian\FileUploader\Factories;

use OldRavian\FileUploader\Services\ByForm;
use OldRavian\FileUploader\Services\ByUrl;
use OldRavian\FileUploader\Services\ByBase64;
use Exception;

/**
 * Factory Pattern Implementation
*/
class FileUploaderFactory {

    public function build($type){
        switch($type){
            case "object":
                return new ByForm;
            break;
            case "url":
                return new ByUrl;
            break;
            case "base64":
                return new ByBase64;
            break;
            default:
            throw new Exception("Unsupported source");
            break;
        }
    }

}

?>