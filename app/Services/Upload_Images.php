<?php

namespace App\Services;



class Upload_Images
{

    public function storageFilepath($file, $disk = 'public_uploads')
    {
        if ($file != null && $file->isValid()) {
            $dir = date('Y/m/d');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs($dir, $fileName, $disk);
            return $dir . '/' . $fileName;
        }

        return null;
    }
}
