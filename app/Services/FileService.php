<?php

namespace App\Services;
use Illuminate\Support\Facades\Storage;

class FileService
{
    /**
     * Store an uploaded file and return its path.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string
     */
    public function storeImage($file, $filePath = 'uploads')
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs($filePath, $fileName, 'public'); // if you set 'public', no need to use pulic dir to display the file
        return $filePath . '/' . $fileName;
    }


    /**
     * Delete an image from the specified filePath.
     *
     * @param string $filePath The relative path to the file within the 'public' disk (e.g., 'uploads/filename.jpg').
     * @return void
     */
    public function deleteImage($filePath) {
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }
}
