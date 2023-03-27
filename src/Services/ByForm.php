<?php
/**
* Author: Habib urRehman
* Email : chaudryhabib2@gmail.com 
*/
namespace OldRavian\FileUploader\Services;

use OldRavian\FileUploader\Abstracts\AbstractFileUploader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
class ByForm extends AbstractFileUploader
{
    protected function isValid($uploadedFile){
        if (!$uploadedFile->isValid($uploadedFile)) {
            $this->uploadError = $uploadedFile->getErrorMessage();
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
        return $uploadedFile->getClientOriginalExtension();
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
        return $uploadedFile->getSize();
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
        return $uploadedFile->getClientOriginalName();
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
        $uploadedFile->storeAs($settings['directory'], $name, $settings['disk']);

        $url = Storage::disk($settings['disk'])->url($storeLocation);

        return [
            'filename' => $name,
            'path' => $storeLocation,
            'url' => $url,
        ];
    }
}