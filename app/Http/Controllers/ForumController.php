<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthUserTrait;
use App\Http\Resources\ForumResource;
use App\Http\Resources\ForumsResource;

class ForumController extends Controller
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
        return ForumsResource::collection(Forum::with('User')->withCount('comments')->paginate(3));
    }
   
    public function filterTag($tag)
    {
        return ForumResource::collection(Forum::with('User')->where('category', $tag)->paginate(3));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest();

        $user = $this->getAuthUser();

        // Simpan ke database
        $user->Forums()->create([
            'title' => request('title'),
            'slug' => Str::slug(request('title'), '-') . '-' . time(),
            'body' => request('body'),
            'category' => request('category'),
        ]);

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
        return new ForumResource(
            Forum::with('User', 'comments.User')->find($id)
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
        $this->validateRequest();

        $forum = Forum::find($id);

        // check ownership
        $this->checkOwnership($forum->user_id);

        // Simpan ke database
        $forum->update([
            'title' => request('title'),
            'slug' => Str::slug(request('title'), '-') . '-' . time(),
            'body' => request('body'),
            'category' => request('category'),
        ]);

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
        $forum = Forum::find($id);

        // check ownership
        $this->checkOwnership($forum->user_id);

        // Hapus di database
        $forum->delete();

        return response()->json(['message' => 'Successfully deleted']);
    }

    private function validateRequest()
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required|min:5',
            'body' => 'required|min:10',
            'category' => 'required'
        ]);

        if ($validator->fails()) {
            response()->json($validator->messages(), 422)->send();
            exit;
        }
    }
}
