<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\StoreProductRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public $postImageArray = [];
    /**
     * post listing view
     *
     * @return void
     */
    public function index()
    {
        return view('backend.posts.index');
    }

    /**
     * Create Form
     *
     * @return void
     */
    public function create()
    {
        return view('backend.posts.create');
    }

    /**
     * Store Post
     *
     * @param Request $request
     * @return void
     */
    public function store(StorePostRequest $request)
    {
        $post = new Post();
        $post->user_id = Auth::id();
        $post->description = $request->description;
        $post->save();

        if ($request->hasFile('images')) {
            $this->_createPostImages($post->id, $request);
        }

        return redirect()->route('post')->with('created', 'Post created Successfully');
    }

    private function _createPostImages($postId, $data)
    {
        $files = $data->images;
        foreach ($files as $key => $image) {
            $this->postImageArray[] = [
                'post_id'      => $postId,
                'path'           => $image->store('post_images'),
                'description'    => $data->contents[$key],
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        PostImage::insert($this->postImageArray);
    }

    /**
     * Post Edit
     *
     * @param [type] $id
     * @return void
     */
    public function edit(Post $post)
    {
        return view('backend.posts.edit', compact('post'));
    }

    /**
     * Post date
     *
     * @param Reqeuest $reqeuest
     * @param [type] $id
     * @return void
     */
    public function update(StorePostRequest $request, Post $post)
    {
        $post->description = $request->description;
        if ($request->has('old')) {
            $files = $post->images()->whereNotIn('id', $request->old)->get();
            if (count($files) > 0) {
                foreach ($files as $file) {
                    $oldPath = $file->getRawOriginal('path') ?? '';
                    Storage::delete($oldPath);
                }

                $post->images()->whereNotIn('id', $request->old)->delete();
            }
        } elseif(!$request->old || !$request->content) {
            $post->images()->delete();
        }

        if ($request->hasFile('images')) {
            $this->_createPostImages($post->id, $request);
        }
        if($request->contents && $request->old) {
            foreach($request->old as $key=>$old) {
                $postImage = PostImage::find($old);
                $postImage->description = $request->contents[$key];
                $postImage->update();
            }
        }
        $post->update();

        return redirect()->route('post')->with('updated', 'Post Updated Successfully');
    }


    /**
     * delete Category
     *
     * @return void
     */
    public function destroy(Post $post)
    {
        $oldImage = $post->getRawOriginal('image') ?? '';
        Storage::delete($oldImage);

        $comments = $post->comments();
        foreach($comments as $cmt) {
            $cmt->delete();
        }

        $post->delete();

        return 'success';
    }

    /**
     * ServerSide
     *
     * @return void
     */
    public function serverSide()
    {
        $posts = Post::withCount('user')->orderBy('id', 'desc')->get();
        return datatables($posts)
            ->addColumn('poster', function ($each) {
                return $each->user->name ?? '---';
            })
            ->addColumn('description', function ($each) {
                return Str::limit($each->description, 80) ?? '---';
            })
            ->addColumn('action', function ($each) {
                $comment_icon = '<button class="btn btn-sm btn-info mr-3 edit_btn" onclick="fetchComments(' . $each->id . ')" data-bs-toggle="modal" data-bs-target="#commentModal"><i class="ri-chat-2-line"></i></button>';
                $edit_icon = '<a href="' . route('post.edit', $each->id) . '" class="btn btn-sm btn-success mr-3 edit_btn"><i class="mdi mdi-square-edit-outline btn_icon_size"></i></a>';
                $delete_icon = '<a href="#" class="btn btn-sm btn-danger delete_btn" data-id="' . $each->id . '"><i class="mdi mdi-trash-can-outline btn_icon_size"></i></a>';

                return '<div class="action_icon">' . $comment_icon . $edit_icon . $delete_icon . '</div>';
            })
            ->rawColumns(['poster', 'description', 'action'])
            ->toJson();
    }

    public function images(Post $post)
    {
        $oldImages = [];
        foreach ($post->images as $img) {
            $oldImages[] = [
                'id'  => $img->id,
                'src' => $img->path,
            ];
        }
        return response()->json($oldImages);
    }

    public function comments(Post $post)
    {
        return response()->json($post->comments);
    }

    public function deleteComment($id)
    {
        $comment = Comment::find($id);
        if ($comment) {
            foreach ($comment->replies as $reply) {
                $reply->delete();
            }
            $comment->delete();
            return 'success';
        } else {
            return 'error';
        }
    }
}
