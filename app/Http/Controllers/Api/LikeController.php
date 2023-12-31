<?php

namespace App\Http\Controllers\Api;

use App\Actions\LikeActions\CommentActions\LikeCommentAction;
use App\Actions\LikeActions\CommentActions\UnlikeCommentAction;
use App\Actions\LikeActions\Post\LikePostAction;
use App\Actions\LikeActions\Post\UnlikePostAction;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\LikeResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class LikeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $likeType = Route::currentRouteName() === 'post.like' ? 'post' : 'comment';
        $tableName = $likeType === 'post' ? 'posts' : 'comments';
        $itemIdKey = "{$likeType}_id";

        $attributes = $request->validate([
            $itemIdKey => ['required', "exists:$tableName,id"],
        ]);

        return ($likeType === 'post') ? $this->likePost($attributes['post_id']) : $this->likeComment($attributes['comment_id']);

    }

    private function likePost($postId)
    {
        $post = Post::with('author', 'likes')->findOrFail($postId);
        $postLike = $post->likes()->where('parent_id', $post->id);

        if ($postLike->count() == 0) {
            $like = (new LikePostAction)->execute($post, Auth::user());

            return ApiResponse::send(JsonResponse::HTTP_CREATED, 'Post liked successfully .', new LikeResource($like));
        } else {
            (new UnlikePostAction)->execute($postLike, $post);

            return ApiResponse::send(JsonResponse::HTTP_OK, 'Post Unliked successfully .', ['is_liked' => false]);
        }
    }

    private function likeComment($commentId)
    {
        $comment = Comment::with('author', 'likes')->findOrFail($commentId);
        $commentLike = $comment->likes()->where('parent_id', $comment->id);

        if ($commentLike->count() == 0) {
            $like = (new LikeCommentAction)->execute($comment, Auth::user());

            return ApiResponse::send(JsonResponse::HTTP_CREATED, 'Comment liked successfully .', new LikeResource($like));
        } else {
            (new UnlikeCommentAction)->execute($commentLike, $comment);

            return ApiResponse::send(JsonResponse::HTTP_OK, 'Comment Unliked successfully .', ['is_liked' => false]);
        }
    }
}
