<?php

namespace Trikey\FileUploader;

use Trikey\FileUploader\Exceptions\FileUploadException;
use Trikey\FileUploader\Exceptions\MultipleFilesUploadException;

class FilesController
{
    public function upload()
    {
        \DB::beginTransaction();
        $result = [];
        try {
            if (request()->hasFile('files')) {
                $result = $this->uploadMultipleFiles(request()->file('files'));
            }
            else if (request()->hasFile('file')) {
                $result = $this->uploadFile(request()->file('file'));
            }
        }
        catch (MultipleFilesUploadException $e) {
            \DB::rollBack();
            return response()->json(['error' => $e->getMessages()], 400);
        }
        catch (FileUploadException $e) {
            \DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }

        \DB::commit();
        return response()->json($result);
    }

    public function download($publicId) {

        $entry = \DB::table('file_uploader_files')
            ->select('path', 'disk', 'mime_type', 'bytes')
            ->where('public_id', '=', $publicId)
            ->first();

        $file = $this->read($entry);

        return response($file)
            ->header('Content-Type', $entry->mime_type)
            ->header('Content-Length', $entry->bytes);
    }

    protected function uploadMultipleFiles($files) {
        $result = collect(request()->file('files'))->reduce(function($array, $file) {
            try {
                $array['files'][] = $this->uploadFile($file);
            }
            catch (FileUploadException $e) {
                $array['errors'][] = $e->getMessage();
            }

            return $array;
        }, ['files' => [], 'errors' => []]);

        extract($result);

        if ($errors) {
            $this->rollbackFiles($files);
            throw new MultipleFilesUploadException($errors);
        }

        return collect($files)->map(function ($file) {
            $file['created_at'] = $file['created_at']->format('Y-m-d H:i:s');
            return $file->except('path');
        });
    }

    protected function uploadFile($file) {

        $disk = config('file-uploader.disk');
        $path = config('file-uploader.path');
        $extension = $file->getClientOriginalExtension();

        $filePath = $this->write($file, $path, $disk);

        $publicId = str_random(16);
        while ($entry = \DB::table('file_uploader_files')->where('public_id', $publicId)->first()) {
            $publicId = str_random(16);
        }

        $fileData = [
            'public_id' => $publicId,
            'format' => $extension,
            'mime_type' => $file->getClientMimeType(),
            'bytes' => $file->getSize(),
            'path' => $filePath,
            'disk' => $disk,
            'created_at' => \Carbon\Carbon::now(),
        ];

        if (\DB::table('file_uploader_files')->insert($fileData)) {
            $fileData['url'] = "/files/{$publicId}";
            $fileData['secure_url'] = str_replace('http://', 'https://', $fileData['url']);
            return collect($fileData);
        }

        throw new FileUploadException("File {$file->getClientOriginalName()} was not uploaded");
    }

    protected function write($file, $path, $disk) {
        return $file->store($path, $disk);
    }

    protected function read($entry) {
        return \Storage::disk($entry->disk)->get($entry->path);
    }

    protected function rollbackFiles($files) {
        foreach ($files as $file) {
            $storage = \Storage::disk($file['disk']);
            if ($storage->exists($file['path'])) {
                $storage->delete($file['path']);
            }
        }
    }
}
