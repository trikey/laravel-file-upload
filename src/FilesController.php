<?php

namespace Trikey\FileUpload;

class FilesController
{
    public function upload()
    {
        if (request()->has('files'))
        {
            $files = collect(request('files'))->map(function($file) {
                return $this->uploadFile($file);
            });
            return response()->json($files);
        }
        else if (request()->has('file')) {
            return $this->uploadFile(request('file'));
        }
        return response()->json([]);
    }

    protected function uploadFile($file) {

        $disk = config('file-upload.disk');
        $path = config('file-upload.path');
        $extension = $file->getClientOriginalExtension();

        $filePath = $this->write($file, $path, $disk);

        $publicId = str_random(16);
        while ($entry = \DB::table('file_upload_files')->where('public_id', $publicId)->first()) {
            $publicId = str_random(16);
        }

        $fileData = [
            'public_id' => $publicId,
            'format' => $extension,
            'mime_type' => $file->getClientMimeType(),
            'bytes' => $file->getSize(),
            'path' => $filePath,
            'disk' => $disk,
            'created_at' => \Carbon\Carbon::now()
        ];

        if (\DB::table('file_upload_files')->insert($fileData)) {
            unset($fileData['path']);
            $fileData['url'] = env('APP_URL') . "/files/{$publicId}";
            $fileData['secure_url'] = str_replace('http://', 'https://', $fileData['url']);
            return $fileData;
        }

        return [];
    }

    public function download($publicId) {

        $entry = \DB::table('file_upload_files')
            ->select('path', 'disk', 'mime_type', 'bytes')
            ->where('public_id', '=', $publicId)
            ->first();

        $file = $this->read($entry);

        return response($file)
            ->header('Content-Type', $entry->mime_type)
            ->header('Content-Length', $entry->bytes);
    }
    
    protected function write($file, $path, $disk) {
        return $file->store($path, $disk);
    }
    
    protected function read($entry) {
        return \Storage::disk($entry->disk)->get($entry->path);
    }
}
