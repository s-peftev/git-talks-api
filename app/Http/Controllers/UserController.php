<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    static function setUserPhotoURL($user)
    {
        if (Storage::disk('public')->exists($user->photo)) {
            $user->photo = asset('storage/' . $user->photo);
        } else {
            $user->photo = null;
        }
        return $user;
    }

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
            self::setUserPhotoURL($user);
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
        self::setUserPhotoURL($user);
        return $user;
    }

    public function updateUserPhoto(Request $request)
    {
        $uploadedPhoto = $request->photo;
        $newPhotoName = 'user_'
            . $request->user()->id . '_photo_'
            . date("Y_m_d_h_m_s.") . $uploadedPhoto->extension();

        if (Storage::disk('public')->put($newPhotoName, file_get_contents($uploadedPhoto))) {
            $currentUserPhoto = $request->user()->photo;
            if (Storage::disk('public')->exists($currentUserPhoto)) {
                Storage::disk('public')->delete($currentUserPhoto);
            }
            $user = User::findOrFail($request->user()->id);
            $user->photo = $newPhotoName;
            $user->save();
            return [
                'resultCode' => 0,
                'photo' => asset('storage/' . $user->photo),
            ];
        }

        return [
            'resultCode' => 1,
            'error' => 'Photo upload fail',
        ];
    }
}
