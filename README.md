# Multi Source File Uploader
> A Laravel package for handling file upload from multiple sources like file object, url or base64 encoded data

## Current Features

- file uploading from multiple sources like file object, url or base64 encoded data
- file validation (based on extension and size)
- support for handling multiple files

## Upcoming Features

- file compression
- support for file deletion

## Requirements  

It is recommended to install this package with **PHP version 7.2+** and **Laravel Framework version 5.5+**   

## Installation

```
composer require oldravian/multi-source-file-uploader
```

## Configuration

Copy configuration to your project:

```
php artisan vendor:publish --provider="OldRavian\FileUploader\FileUploaderServiceProvider"
```

By executing above command the package configuration will be published to **config/old-ravian-file-uploader.php**


## Usage

Firstly, you need to initialize the specific FileUploader instance using ```FileUploaderFactory```  
```php
$file_uploader_factory = new \OldRavian\FileUploader\Factories\FileUploaderFactory();
$file_uploader = $file_uploader_factory->build(here you need to provide the file source name, it could be "object", "url" or "base64");
```


## Upload File From Object
In your controller method:

```php

public function uploadFile(Request $request)
{
  $uploadSettings = ["directory"=>"mention directory", "disk"=>"mention disk", "maxFileSize"=>"mention size in bytes", "allowedExtensions"=>[mention extensions]];
  $file_uploader_factory = new \OldRavian\FileUploader\Factories\FileUploaderFactory();
  $file_uploader = $file_uploader_factory->build("object");
   
  //first parameter should be an instance of \Illuminate\Http\UploadedFile
  //second parameter is optional, if you leave that parameter then default settings will be used
  $data = $file_uploader->upload($request->file, $uploadSettings); //it will return an array
}
``` 

**$uploadSettings** is an asociative array with the following possible keys:   
- ```directory```: the root directory containing your uploaded files  
- ```disk```: the storage disk for file upload. Check Laravel official documentation for more details, e.g: ```public```, ```s3```  
- ```maxFileSize``` (in bytes): the maximum size of an uploaded file 
- ```allowedExtensions```: array of acceptable file extensions, e.g: ```['jpg', 'png', 'pdf']```  

The backend default settings are as follows:  
```
- 'directory': 'media'
- 'disk': 'public'
- 'maxFileSize':  50 MB
- 'allowedExtensions': 'png','jpg','jpeg','mp4','doc','docx','ppt','pptx','xls','xlsx','txt','pdf'
```

You can change these default settings by using the following environment variables in .env:  
- ```OLDRAVIAN_File_UPLOADER_DEFAULT_DISK```
- ```OLDRAVIAN_File_UPLOADER_DEFAULT_DIRECTORY```
- ```OLDRAVIAN_File_UPLOADER_DEFAULT_MAX_FILE_SIZE``` (in bytes)

If the upload succeeds,```$file_uploader->upload($request->file, $uploadSettings)``` will return the following data for being further persisted to database:  

```php
[
    'filename' => 'uploaded file name',
    'path' => 'path to file location relative to the disk storage',
    'url' => 'public url to access the file in browser'
]
```

If the uploaded file is not valid, then ```false``` will be returned and an error message will be set for ```$file_uploader->uploadError```  

## Upload File From URL

```php

$file_uploader_factory = new \OldRavian\FileUploader\Factories\FileUploaderFactory();
$file_uploader = $file_uploader_factory->build("url");

//first parameter should be a string url
//second parameter is optional, if you leave that parameter then default settings will be used
$data = $file_uploader->upload($request->url, $uploadSettings);
``` 


## Upload File From Base64 Encoded data

```php

$file_uploader_factory = new \OldRavian\FileUploader\Factories\FileUploaderFactory();
$file_uploader = $file_uploader_factory->build("base64");

//first parameter should be a string (base64 encoded string)
//second parameter is optional, if you leave that parameter then default settings will be used
$data = $file_uploader->upload($request->base64_str, $uploadSettings);
``` 



## Handling Multiple File Uploads

```php

$file_uploader_factory = new \OldRavian\FileUploader\Factories\FileUploaderFactory();
$file_uploader = $file_uploader_factory->build("object or url or base64");

$urls_array = ["first file url", "second file url", "third file url"];
//first parameter should be an array (it could be an array of objects or urls or base64 encoded strings array based on what you mentioned in the above line)
//second parameter is optional, if you leave that parameter then default settings will be used
$data_array = $file_uploader->uploadMany($urls_array, $uploadSettings); //$data_array is a 2d array
``` 

