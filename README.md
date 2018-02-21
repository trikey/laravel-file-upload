# laravel-file-uploader

## Installation

    composer require trikey/laravel-file-uploader

If you are on laravel 5.5+, then you are ready to go.

Those who are on older versions of laravel , add this to `providers` in `config/app.php`:

    \Trikey\FileUploader\FileUploaderServiceProvider::class,

This package also adds tabke to keep files in, so run:

    php artisan migrate

You can set these params in env:

    FILE_UPLOADER_DISK to specify filesystem disk which will be used to store files, defaults to local

    FILE_UPLOADER_PATH is a path on a disk where files will be stores, defaults to uploads
