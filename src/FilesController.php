<?php

namespace Trikey\FileUpload;

class FilesController
{
    public function upload()
    {
        $data = request()->all();
        if (request()->has('files'))
        {
            $data['files'] = collect(request('files'))->map(function($item) {
                $file = $item;
                $fileName = sha1(time().time()).".".$file->getClientOriginalExtension();
                $path = '/uploads/'.sha1(time()).'/';
                $file->move(public_path().$path, $fileName);
                return [
                    'path' => $path . $fileName,
                    'name' => $fileName
                ];
            });
        }
        return response()->json($data);
    }
    
    public function download($publicId) {
        
    }
}
