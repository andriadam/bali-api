<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
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
        return Forum::with('User:id,username')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), $this->getRules());

        if ($validator->fails()) {
            return response()->json($validator->messages());
        }

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
        return Forum::with('User:id,username')->find($id);
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
        $validator = Validator::make(request()->all(), $this->getRules());

        if ($validator->fails()) {
            return response()->json($validator->messages());
        }

        // check ownership
            // authorized
        $this->getAuthUser();

        // Simpan ke database
        Forum::find($id)->update([
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
        //
    }

    private function getRules(){
        return [
            'title' => 'required|min:5',
            'body' => 'required|min:10',
            'category' => 'required'
        ];
    }

    private function getAuthUser()
    {
        try {
            return auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['message' => 'Not authenticated, you have to login first']);
        };
    }
}
