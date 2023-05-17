<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

trait AuthUserTrait {
    private function getAuthUser()
    {
        try {
            return auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            response()->json(['message' => 'Not Authenticated, you have to login first'])->send();
            exit;
        };
    }


    private function checkOwnership($owner)
    {
        $user = $this->getAuthUser();
        if ($user->id != $owner) {
            response()->json(['message' => 'Not Authorized'], 403)->send();
            exit;
        }
    }

    private function checkAdmin()
    {
        $user = $this->getAuthUser();
        if ($user->role != 'admin') {
            response()->json(['message' => 'Not Authorized'], 403)->send();
            exit;
        }
    }
}


?>