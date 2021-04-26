<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait ImageTrait
{
    /**
     * [saveImage description]
     * @param  string  $folder
     * @param  object  $fileImage
     * @param  array $resize
     * @return string
     */
    public function saveImage($folder, $fileImage, $resize = [])
    {
        $currentDate = Carbon::now()->toDateString();
        $extension = $fileImage->getClientOriginalExtension();
        $imageName = $currentDate . '-' . uniqid() . '.' . $extension;
        $fullPathOrg = $fileImage->getRealPath();

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'bmp'])) {
            foreach($resize as $size => $path) {
                if (!Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->makeDirectory($path, 0777);
                }
                $arrSize = explode(',', $size);
                Image::make($fullPathOrg)->resize($arrSize[0], $arrSize[1])->save(storage_path("app/public/$path/$imageName")); //The default value is 90
            }
        } else {
            if (!Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder, 0777);
            }
            Storage::disk('public')->put($folder . '/' . $imageName, file_get_contents($fullPathOrg));
        }

        return $imageName;
    }

    /**
     * [saveImage description]
     * @param  string  $nameImgOld
     * @param  string  $folder
     * @param  object  $fileImage
     * @param  array $resize
     * @return string
     */
    public function updateImage($nameImgOld, $folder, $fileImage, $resize = [])
    {
        if ($nameImgOld != 'default.png') {
            if ($resize) {
                $this->deletedImage($nameImgOld, $resize);
            } else {
                $this->deletedImage($nameImgOld, $folder);
            }
        }
        return $this->saveImage($folder, $fileImage, $resize);
    }

    /**
     * delted image by name image
     * @param  string  $nameImag
     * @param  string|array  $path
     * @return void
     */
    public function deletedImage($nameImg, $path)
    {
        if (is_array($path)) {
            foreach ($path as $key => $item) {
                if (Storage::disk('public')->exists($item . '/' . $nameImg)) {
                    Storage::disk('public')->delete($item . '/' . $nameImg);
                }
            }
        } else {
            if (Storage::disk('public')->exists($path . '/' . $nameImg)) {
                Storage::disk('public')->delete($path . '/' . $nameImg);
            }
        }
        return;
    }
}
