<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Poppler-utils binary path
    |--------------------------------------------------------------------------
    |
    | This option define the binary path where is located poppler utils binaries.
    | pdftotext, pdfimages, pdftohtml, pdftops, pdfinfo, pdffonts
    |
    */

    'binary_path' => env('POPPLER_PATH', '/usr/bin'),

    /*
    |--------------------------------------------------------------------------
    | Temporary directory
    |--------------------------------------------------------------------------
    |
    | This option define the temporary directory where images are extracted
    |
    */

    'tmp_dir' => env('PDFUTILS_TMP', storage_path('tmp')),

];
