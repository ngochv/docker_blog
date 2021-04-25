<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Post;
use App\Tag;
use App\Http\Controllers\Controller;
use App\Http\Traits\ImageTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PostController extends Controller
{
    use ImageTrait;

    protected $folderImg = 'post';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::latest()->get();
        return view('admin.post.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.post.create', compact('categories', 'tags'));
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
            'title' => 'required',
            'image' => 'required',
            'categories' => 'required',
            'tags' => 'required',
            'body' => 'required',
        ]);
        $image = $request->file('image');
        $slug = str_slug($request->title);
        if (isset($image)) {
            $imageName = $this->saveImage($this->folderImg, $image, $this->getSizeImage());
        } else {
            $imageName = "default.png";
        }

        DB::beginTransaction();
        try {
            $post = new Post();
            $post->user_id = Auth::id();
            $post->title = $request->title;
            $post->slug = $slug;
            $post->image = $imageName;
            $post->body = $request->body;
            if (isset($request->status)) {
                $post->status = true;
            } else {
                $post->status = false;
            }
            $post->is_approved = true;
            $post->save();

            $post->categories()->attach($request->categories);
            $post->tags()->attach($request->tags);
            DB::commit();
            Toastr::success('Post Successfully Saved', 'Success');
            return redirect()->route('admin.post.index');
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollBack();
            Toastr::error('System error. Please try again later', 'error');
            return redirect()->route('admin.post.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('admin.post.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.post.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $this->validate($request, [
            'title' => 'required',
            'image' => 'image',
            'categories' => 'required',
            'tags' => 'required',
            'body' => 'required',
        ]);
        $image = $request->file('image');
        $slug = str_slug($request->title);

        if (isset($image)) {
            $imageName = $this->updateImage($post->image, $this->folderImg, $image, $this->getSizeImage());
            $post->image = $imageName;
        }

        DB::beginTransaction();
        try {
            $post->user_id = Auth::id();
            $post->title = $request->title;
            $post->slug = $slug;
            $post->body = $request->body;
            if (isset($request->status)) {
                $post->status = true;
            } else {
                $post->status = false;
            }
            $post->is_approved = true;
            $post->save();

            $post->categories()->sync($request->categories);
            $post->tags()->sync($request->tags);
            DB::commit();
            Toastr::success('Post Successfully Updated', 'Success');
            return redirect()->route('admin.post.index');
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollBack();
            Toastr::error('System error. Please try again later', 'error');
            return redirect()->route('admin.post.edit', $post->id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        DB::beginTransaction();
        try {
            if (isset($post->image)) {
                $this->deletedImage($post->image, $this->getSizeImage());
            }
            $post->categories()->detach();
            $post->tags()->detach();
            $post->delete();
            DB::commit();

            Toastr::success('Post Successfully Deleted', 'Success');
            return redirect()->back();
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollBack();
            Toastr::error('System error. Please try again later', 'error');
            return redirect()->back();
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
            '1600,1066' => $this->folderImg,
        ];
    }
}
