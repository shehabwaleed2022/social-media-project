<?php

namespace App\Http\Controllers\Api;

use App\Actions\FriendActions\AcceptFriendRequest;
use App\Actions\FriendActions\RejectFriendRequest;
use App\Actions\FriendActions\SendFriendRequest;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\UserFriendRequest;
use App\Models\User;
use App\Notifications\FriendRequestAccepted;
use App\Notifications\FriendRequestSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendRequestController extends Controller
{
    //! Refactor this Contorller
    public function send(Request $request , SendFriendRequest $SendFriendRequest)
    {
        $request->validate([
            'friend_id' => ['required'],
        ]);
        $result = $SendFriendRequest->execute($request->friend_id);
        return ($result['status'] === 'success')
            ?  ApiResponse::send('200', $result['message'], ['Friend Request Id' => $result['data']])
            :  ApiResponse::send('200', $result['message']);
    }
    public function accept(Request $request, AcceptFriendRequest $AcceptFriendRequest)
    {
        $request->validate([
            'request_id' => ['required', 'exists:user_friend_requests,id']
        ]);

        $result = $AcceptFriendRequest->execute($request->request_id);

        return ($result['status'] === 'success')
            ? ApiResponse::send('201', $result['message'], null)
            : ApiResponse::send('200', $result['message']);
    }
    public function reject(Request $request, RejectFriendRequest $RejectFriendRequest)
    {
        $request->validate([
            'friend_id' => ['required', 'exists:user_friend_requests,id']
        ]);
        $result = $RejectFriendRequest->execute($request->friend_id);
        return ($result['status'] === 'success')
            ? ApiResponse::send('200', $result['message'], null)
            : ApiResponse::send('200', $result['message']);
    }

}
