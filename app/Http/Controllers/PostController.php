<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PostController extends Controller
{
    public function welcome()
    {
        $posts = Post::with('user')->latest()->get();
        return view('welcome', compact('posts'));
    }

    public function index()
    {
        $posts = Post::with('user', 'comments')->latest()->get();     
        return view('dashboard', compact('posts'));
    }


    public function create()
    {
        // Return view for creating a new post
    }

    public function store(Request $request)
    {
        // Validate input
        $validatedData = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);
    
        // Create a new post instance
        $post = new Post();
        $post->title = $validatedData['title'];
        $post->body = $validatedData['body'];
        $post->published_at = now();
        
        // Assign the authenticated user's ID to the post
        $post->user_id = Auth::id();
        
        // Save the post
        $post->save();
    
        // Redirect to the dashboard with success message
        return redirect()->route('dashboard')->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {
        // Return view for editing the post
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $post->update([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return redirect()->route('dashboard')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        // Delete post
        $post->delete();

        return redirect()->route('dashboard')->with('success', 'Post deleted successfully.');
    }
}
