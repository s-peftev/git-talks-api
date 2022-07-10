<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $authorizedUser = $request->user();
        $followedUsersId = $authorizedUser->subscriptions()
            ->map(function ($subscription) {
                return $subscription->followed_id;
            })->toArray();

        $countPerPage = min((int)$request->count, 50);

        $query = User::query()->select('users.*')
            ->where('id', '!=', $authorizedUser->id);
        $totalUsersCount = $query->get()->count();
        $allUsers = $query->simplePaginate($countPerPage);
        foreach ($allUsers as $user) {
            if(in_array($user->id, $followedUsersId)) {
                $user->followed = true;
            } else {
                $user->followed = false;
            }
        }

        return [
            'items' => $allUsers,
            'totalCount' => $totalUsersCount,
        ];
    }

    public function show($userId)
    {
        $user = User::findOrFail($userId);
        return $user;
    }
}
