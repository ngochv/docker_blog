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
        $imagename = $currentDate . '-' . uniqid() . '.' . $fileImage->getClientOriginalExtension();
        //            check category dir is exists
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }
        if ($resize) {
            foreach($resize as $size => $path) {
                $arrSize = explode(',', $size);
                $category = Image::make($fileImage)->resize($arrSize[0], $arrSize[1])->save();
                Storage::disk('public')->put($path . '/' . $imagename, $category);
            }
        } else {
            $img = Image::make($fileImage)->save();
            Storage::disk('public')->put($folder . '/' . $imagename, $img);
        }
        return $imagename;
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
