<?php

\Route::post('files/upload', '\Trikey\FileUpload\FilesController@upload');
\Route::get('files/{public_id}', '\Trikey\FileUpload\FilesController@download');   
