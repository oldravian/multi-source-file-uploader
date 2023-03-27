<?php
/**
* Author: Habib urRehman
* Email : chaudryhabib2@gmail.com 
*/
namespace OldRavian\FileUploader\Abstracts;

use OldRavian\FileUploader\Contracts\FileUploaderContract;
use Illuminate\Http\UploadedFile;
abstract class AbstractFileUploader implements FileUploaderContract
{
    /**
    * @var $uploadError
    */
    public $uploadError;
    
    /**
    * Default settings
    *
    */
    protected $settings;

    public function __construct()
    {
        $this->initializeDefaults();
    }

    /**
    * Initialize default settings
    *
    * @return void
    */
    protected function initializeDefaults()
    {
        $this->settings = config('old-ravian-file-uploader');
    }

    /**
    * Handle the uploaded file
    *
    * @param Illuminate\Http\UploadedFile|string $target
    * @param array $settings
    *
    * @return array|false
    */
    public function upload($target, array $settings = [])
    {        
        $settings = array_merge($this->settings, $settings);

        if ($this->uploadValidate($target, $settings)) {
            return $this->storeFile($target, $settings);
        }

        return false;
    }

    /** 
     * @param Illuminate\Http\UploadedFile|string $target
     * @param array $settings
     * @return array|false
     */
    public function uploadMany(array $targets, array $settings = []){
        $arrs=[];
        foreach($targets as $target){
            $res = $this->upload($target, $settings);
            if($res===false){
                return false;
            }
            $arrs[] = $res;
        }
        return $arrs;
    }

    /**
     * Validate the uploaded file for extension & size
     *
     * @return bool
     */
    protected function uploadValidate($uploadedFile, $settings){
        if (!$this->isValid($uploadedFile)) {
            return false;
        }

        if (!in_array($this->getExtension($uploadedFile), $settings['allowed_extensions'])) {
            $this->uploadError = "Extension {$this->getExtension($uploadedFile)} is not allowed";
            return false;
        }

        if ($this->getFileSize($uploadedFile) > $settings['max_file_size']) {
            $this->uploadError = "Input file cannot exceed {$settings['max_file_size']} bytes";
            return false;
        }

        return true;
    }

    protected function isValid($uploadedFile){
        return true;
    }
    
    /**
     * Return the file extension as extracted from the origin file name
     *
     * @param Illuminate\Http\UploadedFile|string $uploadedFile
     *
     * @return string
     */
    abstract protected function getExtension($uploadedFile);

    /**
     * Return the file size
     *
     * @param Illuminate\Http\UploadedFile|string $uploadedFile
     *
     * @return double
     */
    abstract protected function getFileSize($uploadedFile);

    /**
     * Generate an unique name for storing file
     *
     * @param Illuminate\Http\UploadedFile|string $uploadedFile
     *
     * @return string
     */
    protected function getUniqueName($uploadedFile){
        $originName = $this->getOriginName($uploadedFile);
        $uniqueString = uniqid(rand(), true)."_".$originName."_".getmypid()."_".gethostname()."_".time();
        return md5($uniqueString).".".$this->getExtension($uploadedFile);
    }

    /**
     * Return the original filename
     *
     * @param Illuminate\Http\UploadedFile|string $uploadedFile
     *
     * @return string
     */
    protected function getOriginName($uploadedFile){
        return "".$this->getExtension($uploadedFile);
    }
    

    /**
     * Physically store the  uploaded file
     *
     * @param Illuminate\Http\UploadedFile|string $uploadedFile
     * @param array $settings
     *
     * @return array
     */
    abstract protected function storeFile($uploadedFile, $settings);
}