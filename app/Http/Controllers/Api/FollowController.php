<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow()
    {
        return response()->json(['message' => 'follow']);
    }
}
