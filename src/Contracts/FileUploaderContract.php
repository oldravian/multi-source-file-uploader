<?php
/**
* Author: Habib urRehman
* Email : chaudryhabib2@gmail.com 
*/
namespace OldRavian\FileUploader\Contracts;

interface FileUploaderContract
{
    public function upload($target, array $settings = []);
}