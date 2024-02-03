<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($slug)
    {
        $post = Post::where('slug', $slug)->first();

        if (!$post) {
            return response(
                [
                    'message' => 'Post not found'
                ],
                404
            );
        }


        return response([
            'comments' => $post->comments()->with('user')->get()
        ]);
    }

    public function store(Request $request, $slug)
    {
        $request->validate([
            'comment' => 'required'
        ]);

        $post = Post::where('slug', $slug)->first();

        if (!$post) {
            return response(
                [
                    'message' => 'Post not found'
                ],
                404
            );
        }

        if ($post->user_id != auth()->user()->id) {
            return response(
                [
                    'message' => 'Unauthorized'
                ],
                401
            );
        }

        Comment::create(
            [
                'comment' => $request->comment,
                'post_id' => $post->id,
                'user_id' => auth()->user()->id,
            ]
        );

        return response(
            [
                'message' => 'Comment created successfully'
            ],
            201
        );
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::where('id', $id)->first();

        if (!$comment) {
            return response(
                [
                    'message' => 'Post not found'
                ],
                404
            );
        }

        if ($comment->user_id != auth()->user()->id) {
            return response(
                [
                    'message' => 'Unauthorized'
                ],
                401
            );
        }

        $request->validate(
            [
                'comment' => 'required'
            ]
        );

        $comment->update([
            'comment' => $request->comment
        ]);

        return response(
            [
                'message' => 'Comment updated successfully'
            ],
            201
        );
    }

    public function destroy($id)
    {
        $comment = Comment::where('id', $id)->first();

        if (!$comment) {
            return response(
                [
                    'message' => 'Post not found'
                ],
                404
            );
        }

        if ($comment->user_id != auth()->user()->id) {
            return response(
                [
                    'message' => 'Unauthorized'
                ],
                401
            );
        }

        $comment->delete();

        return response(
            [
                'message' => 'Comment deleted successfully'
            ],
            201
        );
    }
}
