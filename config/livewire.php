<?php

/*
|--------------------------------------------------------------------------
| Livewire overrides (merged with the package defaults)
|--------------------------------------------------------------------------
| Published only to raise the temporary file upload limits so book PDFs
| (up to 100MB) can be uploaded from the admin Books page. Everything
| not listed here keeps Livewire's default value.
*/

return [

    'temporary_file_upload' => [
        'disk' => null,
        'rules' => ['required', 'file', 'max:102400'], // 100MB (Livewire default is 12MB)
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 30, // minutes — large PDFs on slow connections
        'cleanup' => true,
    ],

];
