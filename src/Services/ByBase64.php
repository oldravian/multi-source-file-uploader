<?php
/**
* Author: Habib urRehman
* Email : chaudryhabib2@gmail.com 
*/
namespace OldRavian\FileUploader\Services;

use OldRavian\FileUploader\Abstracts\AbstractFileUploader;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ByBase64 extends AbstractFileUploader
{
    private $base64Arr;

    protected function isValid($uploadedFile){
        // split the string on commas
        //$data[ 0 ] == "data:image/png;base64"
        //$data[ 1 ] == <actual base64 string>
        $this->base64Arr = explode( ',', $uploadedFile );
        if (count($this->base64Arr)<2) {
            $this->uploadError = "Unable to upload file from base64";
            return false;
        }
        return true;
    }

    /**
     * Return the file extension as extracted from the origin file name
     *
     * @param Illuminate\Http\UploadedFile|string $uploadedFile
     *
     * @return string
     */
    protected function getExtension($uploadedFile){
        $ext = substr($this->base64Arr[0], strpos($this->base64Arr[0], '/')+1);
        $ext = substr($ext, 0, strpos($ext, ';'));
        return $ext;
    }

    /**
     * Return the file size
     *
     * @param Illuminate\Http\UploadedFile|string $uploadedFile
     *
     * @return double
     */
    protected function getFileSize($uploadedFile)
    {
        //https://stackoverflow.com/a/5373559/10029265
        return strlen(base64_decode($uploadedFile));
    }

    /**
     * Physically store the  uploaded file
     *
     * @param Illuminate\Http\UploadedFile|string $uploadedFile
     * @param array $settings
     *
     * @return array
     */
    protected function storeFile($uploadedFile, $settings)
    {
        $name = $this->getUniqueName($uploadedFile);
        $storeLocation = $settings['directory'].DIRECTORY_SEPARATOR.$name;
        Storage::disk($settings['disk'])->put($storeLocation, base64_decode( $this->base64Arr[1] ));

        $url = Storage::disk($settings['disk'])->url($storeLocation);

        return [
            'filename' => $name,
            'path' => $storeLocation,
            'url' => $url,
        ];
    }
}