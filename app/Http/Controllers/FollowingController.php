<?php

namespace App\Http\Controllers;

use App\Models\Following;
use Illuminate\Http\Request;

class FollowingController extends Controller
{
    public function follow(Request $request, $followId) {
        $subscriberId = $request->user()->id;
        $following = new Following();
        $following->subscriber_id = $subscriberId;
        $following->followed_id = $followId;

        if($following->save()) {
            return [
                'done'=> true,
            ];
        } else {
            return [
                'done'=> false,
            ];
        }

    }

    public function unfollow(Request $request, $unfollowId) {
        $subscriberId = $request->user()->id;
        $following = Following::query()->select('followings.*')
            ->where('subscriber_id', $subscriberId)
            ->where('followed_id', $unfollowId);
        if($following->delete()) {
            return [
                'done'=> true,
            ];
        } else {
            return [
                'done'=> false,
            ];
        }
    }
}
