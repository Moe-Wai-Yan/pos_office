<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PostController extends BaseController
{
    public function list(Request $request)
    {
        $request->validate([
            'page' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);

        $posts = Post::with('images')->latest()->paginate($request->limit);
        $totalPages = $posts->total();

        return response()->json([
            'success' => true,
            'total' => $totalPages,
            'can_load_more' => $posts->total() == 0 || $request->page >= $totalPages ? false : true,
            'data' => PostResource::collection($posts)
        ], 200);
    }

    public function detail(Post $post) {
        if($post) {
            return $this->sendResponse('success', new PostResource($post));
        }
        return $this->sendError(404, "There's no posts with this id!");
    }

    public function comments($id)
    {
        $post = Post::with('comments')->where('id', $id)->first();
        if ($post) {
            return $this->sendResponse('success', CommentResource::collection($post->comments));
        }
        return $this->sendError(404, "There's no posts with this id!");
    }

    public function commentStore(Request $request, $postId)
    {

        $comment = new Comment();
        $comment->user_id =Auth::guard('api')->user()->id;
        $comment->post_id = $postId;
        if ($request->parent_id) {
            $comment->parent_id = $request->parent_id;
        }
        $comment->body = $request->body;
        $comment->save();

        return $this->sendResponse('success', $comment);
    }

    public function commentDetail(Comment $comment) {
        if($comment) {
            return $this->sendResponse('success', new CommentResource($comment));
        } else {
            return $this->sendError(404, "There's no comments with this id!");
        }
    }

    public function commentDelete(Comment $comment) {
        if($comment) {
            $comment->delete();
            return $this->sendResponse('success', $comment);
        }
    }
}
