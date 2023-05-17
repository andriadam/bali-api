<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessComment;
use App\Models\NewsComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsCommentController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $newsId)
    {
        $this->validateRequest();

        $user = $this->getAuthUser();

        // Simpan ke database
        // Tambahkan job ke dalam Redis queue
        ProcessComment::dispatch([
            'body' => request('body'),
            'user_id' => $user->id,
            'news_id' => $newsId,
        ]);

        return response()->json(['message' => 'Successfully comment posted']);
    }

    private function validateRequest()
    {
        $validator = Validator::make(request()->all(), [
            'body' => 'required|min:10'
        ]);

        if ($validator->fails()) {
            response()->json($validator->messages(), 422)->send();
            exit;
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $newsId, $commentId)
    {
        $this->validateRequest();

        $newsComment = NewsComment::find($commentId);

        // check ownership
        $this->checkOwnership($newsComment->user_id);

        // Simpan ke database
        $newsComment->update([
            'body' => request('body'),
        ]);

        return response()->json(['message' => 'Successfully comment updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($newsId, $commentId)
    {
        $newsComment = NewsComment::find($commentId);

        // check ownership
        $this->checkOwnership($newsComment->user_id);

        // Hapus di database
        $newsComment->delete();

        return response()->json(['message' => 'Successfully comment deleted']);
    }
}
