<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $comments = Comment::where('post_id', $this->id)->whereNull('parent_id')->get();
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'poster' => $this->user->name ?? 'Admin',
            'description' => $this->description,
            'images' => $this->images,
            'comments' => CommentResource::collection($comments),
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
            'updated_at' => Carbon::parse($this->updated_at)->diffForHumans(),
        ];
    }
}
