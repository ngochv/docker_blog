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
     * @param  boolean $resize
     * @return string
     */
    public function saveImage($folder, $fileImage, $resize = false)
    {
        $currentDate = Carbon::now()->toDateString();
        $imagename = $currentDate . '-' . uniqid() . '.' . $fileImage->getClientOriginalExtension();
        //            check category dir is exists
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }
        if ($resize) {
            //            resize image for category and upload
            $category = Image::make($fileImage)->resize(1600, 479)->save();
            Storage::disk('public')->put($folder . '/' . $imagename, $category);

            //            check category slider dir is exists
            if (!Storage::disk('public')->exists($folder . '/slider')) {
                Storage::disk('public')->makeDirectory($folder . '/slider');
            }
            //            resize image for category slider and upload
            $slider = Image::make($fileImage)->resize(500, 333)->save();
            Storage::disk('public')->put($folder . '/slider/' . $imagename, $slider);
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
     * @param  boolean $resize
     * @return string
     */
    public function updateImage($nameImgOld, $folder, $fileImage, $resize = false)
    {

        $this->deletedImage($nameImgOld, $folder);
        return $this->saveImage($folder, $fileImage, $resize);
    }

    /**
     * delted image by name image
     * @param  string  $nameImag
     * @param  string  $folder
     * @return void
     */
    public function deletedImage($nameImg, $folder)
    {
        if (Storage::disk('public')->exists($folder . '/' . $nameImg)) {
            Storage::disk('public')->delete($folder . '/' . $nameImg);
        }

        if (Storage::disk('public')->exists($folder . '/slider/' . $nameImg)) {
            Storage::disk('public')->delete($folder . '/slider/' . $nameImg);
        }
        return;
    }
}
