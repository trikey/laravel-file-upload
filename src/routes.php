<?php

\Route::post('files/upload', '\Trikey\FileUploader\FilesController@upload');
\Route::get('files/{public_id}', '\Trikey\FileUploader\FilesController@download');
