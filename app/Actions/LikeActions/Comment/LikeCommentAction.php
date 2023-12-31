<?php

namespace App\Actions\LikeActions\CommentActions;

use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use App\Notifications\CommentLoveNotification;
use Illuminate\Support\Facades\Notification;

class LikeCommentAction
{
    public function execute(Comment $comment, User $user)
    {
        $comment->increment('likes_num');
        $like = Like::create([
            'parent_type' => get_class($comment),
            'parent_id' => $comment->id,
            'user_id' => $user->id,
        ]);
        $like->likedAt = 'Comment';
        Notification::send($comment->author, new CommentLoveNotification($comment->id));

        return $like;
    }
}
