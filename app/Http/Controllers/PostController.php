<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->with(['user', 'likes'])->withCount('comments', 'likes')->get();
        return response(
            [
                'posts' => $posts
            ],
            200
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Add validation for image file
        ]);
        
        if ($request->hasFile('image')) {
            $src = $request->file('image');
            $path = 'posts'; // 
            $image = $this->saveImage($src, $path);
        }
        
        dd($request->all());
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => auth()->user()->id,
            'image' => $request->hasFile('image') ? $image : null,
            'slug' => Str::slug($request->title),
        ]);
        
        return response([
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->withCount('comments', 'likes')->get();
        return response(
            [
                'message' => 'Post retrieved successfully',
                'post' => $post
            ],
            200
        );
    }

    public function update(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Add validation for image file
        ]);

        $post = Post::where('slug', $slug)->first();
        if (!$post) {
            return response([
                'message' => 'Post not found'
            ], 404);
        }

        if ($post->user_id != auth()->user()->id) {
            return response([
                'message' => 'Unauthorized'
            ], 401);
        }

        if ($request->hasFile('image')) {
            $src = $request->file('image');
            $path = 'posts'; // 

            $image = $this->saveImage($src, $path);

            // Delete old image if it exists
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
        }

        $post->update([
            'title' => $request->title,
            'body' => $request->body,
            'image' => $request->hasFile('image') ? $image : $post->image,
            'slug' => Str::slug($request->title),
        ]);

        return response([
            'message' => 'Post updated successfully',
            'post' => $post
        ], 200);
    }

    public function destroy($slug)
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

        if ($post->user_id != auth()->user()->id) {
            return response(
                [
                    'message' => 'Unauthorized'
                ],
                401
            );
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response(
            [
                'message' => 'Post deleted successfully',
            ],
            200
        );
    }
}
