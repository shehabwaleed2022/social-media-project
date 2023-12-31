<?php

namespace App\Http\Controllers\Api;

use App\Actions\FriendActions\AcceptFriendRequest;
use App\Actions\FriendActions\RejectFriendRequest;
use App\Actions\FriendActions\SendFriendRequest;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserFriendRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendRequestController extends Controller
{
    public function send(Request $request, SendFriendRequest $SendFriendRequest)
    {
        $request->validate([
            'friend_id' => ['required'],
        ]);
        $friend = User::findOrFail($request->friend_id);

        $result = $SendFriendRequest->execute(Auth::user(), $friend);

        return ('success' === $result['status'])
        ? ApiResponse::send(JsonResponse::HTTP_OK, $result['message'], ['Friend Request Id' => $result['data']])
        : ApiResponse::send(JsonResponse::HTTP_OK, $result['message']);
    }

    public function accept(Request $request, AcceptFriendRequest $AcceptFriendRequest)
    {
        $request->validate([
            'request_id' => ['required', 'exists:user_friend_requests,id'],
        ]);
        $friendRequest = UserFriendRequest::findOrFail($request->request_id);

        $result = $AcceptFriendRequest->execute(Auth::user(), $friendRequest);

        return ('success' === $result['status'])
        ? ApiResponse::send(JsonResponse::HTTP_OK, $result['message'], null)
        : ApiResponse::send(JsonResponse::HTTP_CREATED, $result['message']);
    }

    public function reject(Request $request, RejectFriendRequest $RejectFriendRequest)
    {
        $request->validate([
            'friend_id' => ['required', 'exists:user_friend_requests,id'],
        ]);
        $friendRequest = UserFriendRequest::findOrFail($request->friend_id);
        $result = $RejectFriendRequest->execute(Auth::user(), $friendRequest);

        return ('success' === $result['status'])
        ? ApiResponse::send(JsonResponse::HTTP_OK, $result['message'], null)
        : ApiResponse::send(JsonResponse::HTTP_OK, $result['message']);
    }
}
