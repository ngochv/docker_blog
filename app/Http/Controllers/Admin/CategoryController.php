<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Traits\ImageTrait;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    use ImageTrait;

    protected $folderImgCate = 'category';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::latest()->with('posts')->get();
        return view('admin.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:categories',
            'image' => 'required|mimes:jpeg,bmp,png,jpg'
        ]);
        // get form image
        $image = $request->file('image');
        $slug = str_slug($request->name);

        DB::beginTransaction();
        try {
            if (isset($image)) {
                $imageName = $this->saveImage($this->folderImgCate, $image, $this->getSizeImage());
            } else {
                $imageName = "default.png";
            }
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $slug;
            $category->image = $imageName;
            $category->save();
            DB::commit();

            Toastr::success('Category Successfully Saved', 'Success');
            return redirect()->route('admin.category.index');
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollBack();
            Toastr::error('System error. Please try again later', 'error');
            return redirect()->route('admin.category.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id);
        if (!$category) {
            Toastr::error('Not exists category', 'error');
            return redirect()->route('admin.tag.index');
        }
        return view('admin.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $image = $request->file('image');
        $this->validate($request, [
            'name' => 'required|unique:categories,name,' . $id,
            'image' => 'mimes:jpeg,bmp,png,PNG,jpg'
        ]);
        // get form image
        $image = $request->file('image');
        $slug = str_slug($request->name);

        DB::beginTransaction();
        try {
            $category = Category::find($id);
            if (!$category) {
                Toastr::error('Not exists category', 'error');
                return redirect()->route('admin.category.index');
            }
            if (isset($image)) {
                $imageName = $this->updateImage($category->image, $this->folderImgCate, $image, $this->getSizeImage());
                $category->image = $imageName;
            }
            $category->name = $request->name;
            $category->slug = $slug;
            $category->save();
            DB::commit();

            Toastr::success('Category Successfully Update', 'Success');
            return redirect()->route('admin.category.index');
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollBack();
            Toastr::error('System error. Please try again later', 'error');
            return redirect()->route('admin.category.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $category = Category::find($id);
            if (!$category) {
                Toastr::error('Not exists category', 'error');
                return redirect()->route('admin.category.index');
            }
            if (isset($category->image)) {
                $this->deletedImage($category->image, $this->folderImgCate);
            }
            $category->delete();
            DB::commit();

            Toastr::success('Category Successfully Deleted', 'Success');
            return redirect()->route('admin.category.index');
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollBack();
            Toastr::error('System error. Please try again later', 'error');
            return redirect()->route('admin.category.index');
        }
    }

    /**
     * get resize for save image category
     *
     * @return array
     */
    public function getSizeImage()
    {
        return [
            '1600,479' => $this->folderImgCate,
            '500,333' => $this->folderImgCate . '/slider',
        ];
    }
}
