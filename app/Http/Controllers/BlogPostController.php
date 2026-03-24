<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', BlogPost::class);
        return response()->json(BlogPost::with('author')->where('status', 'published')->paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', BlogPost::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', BlogPost::class);

        $validatedData = $request->validate([
            'title'  => 'required|string|max:255',
            'status' => 'required|in:draft,published',
        ]);

        $blogPost = BlogPost::create([
            ...$validatedData,
            'author_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Blog post created successfully',
            'post'    => $blogPost,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogPost $blogPost)
    {
        $this->authorize('view', $blogPost);
        return response()->json($blogPost->load('author'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogPost $blogPost)
    {
        $this->authorize('update', $blogPost);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogPost $blogPost)
    {
        $this->authorize('update', $blogPost);

        $validatedData = $request->validate([
            'title'  => 'sometimes|string|max:255',
            'status' => 'sometimes|in:draft,published',
        ]);

        $blogPost->update($validatedData);

        return response()->json([
            'message' => 'Blog post updated successfully',
            'post'    => $blogPost,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $blogPost)
    {
        $this->authorize('delete', $blogPost);
        $blogPost->delete();
        return response()->json(['message' => 'Blog post deleted successfully'], 204);
    }
}
