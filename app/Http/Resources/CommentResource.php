<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (!$this->parent_id) {
            return [
                "id" => $this->id,
                "user_id" => $this->user_id,
                "poster" => $this->customer->name ?? 'Deleted User',
                "image" => $this->customer ? $this->customer->photo : asset('images/customer.png'),
                "post_id" => $this->post_id,
                "parent_id" => $this->parent_id,
                "body" => $this->body,
                "created_at" => Carbon::parse($this->created_at)->diffForHumans(),
                "updated_at" => Carbon::parse($this->updated_at)->diffForHumans(),
                "replies" => CommentReplyResource::collection($this->replies),
            ];
        }
    }
}
