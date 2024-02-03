<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function likeOrUnlike(Request $request)
    {
        $post = Post::where('slug', $request->slug)->first();

        if (!$post) {
            return response(
                [
                    'message' => 'Post not found'
                ],
                404
            );
        }

        $like = Like::where('post_id', $post->id)->where('user_id', $request->user_id)->first();
        if (!$like) {


            $like = Like::create(
                [
                    'post_id' => $post->id,
                    'user_id' => auth()->user()->id
                ]
            );

            return response(
                [
                    'message' => 'Post liked'
                ],
                200
            );
        } else {
            $like->delete();

            return response(
                [
                    'message' => 'Post disliked'
                ],
                200
            );
        }
    }
}
