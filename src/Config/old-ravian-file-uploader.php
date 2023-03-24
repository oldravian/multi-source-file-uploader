<?php

return [
    'disk' => env('OLDRAVIAN_File_UPLOADER_DISK', 'public'),
    'directory' => env('OLDRAVIAN_File_UPLOADER_DIRECTORY', 'media'),
    'max_file_size' => env('OLDRAVIAN_File_UPLOADER_MAX_FILE_SIZE', 52428800), // 50 MB
    'allowed_extensions' => [
        'png','jpg','jpeg','mp4','doc','docx','ppt','pptx','xls','xlsx','txt','pdf'
    ],
];