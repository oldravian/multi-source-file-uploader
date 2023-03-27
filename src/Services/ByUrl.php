<?php
/**
* Author: Habib urRehman
* Email : chaudryhabib2@gmail.com 
*/
namespace OldRavian\FileUploader\Services;

use OldRavian\FileUploader\Abstracts\AbstractFileUploader;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ByUrl extends AbstractFileUploader
{
    private $fileSize;
    private $fileName;

    protected function isValid($uploadedFile){
        //https://stackoverflow.com/a/52368686/10029265
        $ch = curl_init($uploadedFile);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        $data = curl_exec($ch);
        $fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $info =  [
            'fileExists' => (int) $httpResponseCode == 200,
            'fileSize' => (int) $fileSize
        ];
        $this->fileSize = $info['fileSize'];

        if($info['fileExists']==false){
            $this->uploadError = "File doesn't exist on given url";
        }
        return $info['fileExists'];
    }

    function get_remote_file_info($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        $data = curl_exec($ch);
        $fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [
            'fileExists' => (int) $httpResponseCode == 200,
            'fileSize' => (int) $fileSize
        ];
    }

    /**
     * Return the file extension as extracted from the origin file name
     *
     * @param Illuminate\Http\UploadedFile|string $uploadedFile
     *
     * @return string
     */
    protected function getExtension($uploadedFile){
        $name = substr($uploadedFile, strrpos($uploadedFile, '/') + 1);
        $this->fileName = pathinfo($name, PATHINFO_FILENAME);
        $ext = pathinfo($name, PATHINFO_EXTENSION);
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
        return $this->fileSize;
    }

    /**
     * Return the original filename
     *
     * @param Illuminate\Http\UploadedFile|string $uploadedFile
     *
     * @return string
     */
    protected function getOriginName($uploadedFile)
    {
        return $this->fileName;
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
        Storage::disk($settings['disk'])->put($storeLocation, file_get_contents($uploadedFile));

        $url = Storage::disk($settings['disk'])->url($storeLocation);

        return [
            'filename' => $name,
            'path' => $storeLocation,
            'url' => $url,
        ];
    }
}