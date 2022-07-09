<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $countPerPage = min((int)$request->count, 50);
        $totalCount = User::all()->count();
        $allUsers = User::simplePaginate($countPerPage);

        foreach($allUsers as $user) {
            $user->followed = false;
        }

        return [
            'items' => $allUsers,
            'totalCount' => $totalCount,
        ];
    }

    public function show($userId)
    {
        $user = User::findOrFail($userId);
        return $user;
    }
}
