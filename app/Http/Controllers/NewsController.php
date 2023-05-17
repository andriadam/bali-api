<?php

namespace App\Http\Controllers;

use App\Events\NewsCreated;
use App\Events\NewsDeleted;
use App\Events\NewsUpdated;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthUserTrait;
use App\Http\Resources\NewResource;
use App\Http\Resources\NewsResource;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    use AuthUserTrait;

    public function __construct()
    {
        return auth()->shouldUse('api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return NewsResource::collection(News::with('User')->withCount('comments')->paginate(3));
    }
   
    public function filterTag($tag)
    {
        return NewResource::collection(News::with('User')->where('category', $tag)->paginate(3));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->checkAdmin();

        $this->validateRequest();

        $user = $this->getAuthUser();

        // Upload image
        if ($request->file('image')) {
            $images_name = $request->file('image')->store('news');
        }

        $news = News::create([
            'user_id' => $user->id,
            'title' => request('title'),
            'slug' => Str::slug(request('title'), '-') . '-' . time(),
            'category' => request('category'),
            'body' => request('body'),
            'image' => $images_name,
        ]);

        event(new NewsCreated($news));

        return response()->json(['message' => 'Successfully posted']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new NewResource(
            News::with('User', 'comments.User')->find($id)
        );
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
        $this->checkAdmin();
        
        $this->validateRequest();

        $news = News::find($id);

        // check ownership
        $this->checkOwnership($news->user_id);

        // Jika ada gambar baru dan mau ganti dengan gambar lama, maka hapus dlu gambar lama baru upload
        if ($request->file('image')) {
            if ($request->oldImage) {
                Storage::delete($request->oldImage);
            }
            $images_name = $request->file('image')->store('news');
        } else {
            $images_name = $news->image;
        }

        // Simpan ke database
        $news->update([
            'title' => request('title'),
            'slug' => Str::slug(request('title'), '-') . '-' . time(),
            'body' => request('body'),
            'category' => request('category'),
            'image' => $images_name,
        ]);

        event(new NewsUpdated($news));

        return response()->json(['message' => 'Successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->checkAdmin();
        
        $news = News::find($id);

        // check ownership
        $this->checkOwnership($news->user_id);

        // Delete image di storage
        if ($news->image != 'news/default.png') {
            Storage::delete($news->image);
        }

        event(new NewsDeleted($news));

        // Hapus di database
        $news->delete();


        return response()->json(['message' => 'Successfully deleted']);
    }

    private function validateRequest()
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required|min:5',
            'body' => 'required|min:10',
            'category' => 'required',
            'image' => 'image|file|max:2048'
        ]);

        if ($validator->fails()) {
            response()->json($validator->messages(), 422)->send();
            exit;
        }
    }
}
