<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', BlogPost::class);
        $query = BlogPost::with('author');

        if ($request->boolean('mine')) {
            $query->where('author_id', '=', $request->user()->id);
        } else {
            $query->where('visibility', '=', 'public')
                  ->where('moderation_status', '=', 'approved');
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function store(Request $request)
    {
        $this->authorize('create', BlogPost::class);

        $validatedData = $request->validate([
            'title'      => 'required|string|max:255',
            'body'       => 'nullable|string',
            'visibility' => 'required|in:public,private,draft',
            'tags'       => 'nullable|array',
            'status'     => 'sometimes|in:draft,published', // backwards compat
        ]);

        if (empty($validatedData['visibility']) && isset($validatedData['status'])) {
            $validatedData['visibility'] = $validatedData['status'] === 'draft' ? 'draft' : 'public';
        }

        if (empty($validatedData['status']) && isset($validatedData['visibility'])) {
            $validatedData['status'] = $validatedData['visibility'] === 'draft' ? 'draft' : 'published';
        }

        $blogPost = BlogPost::create([
            ...$validatedData,
            'author_id' => $request->user()->id,
            'moderation_status' => 'approved', 
        ]);

        return response()->json([
            'message' => 'Blog post created successfully',
            'post'    => $blogPost,
        ], 201);
    }

    public function show(BlogPost $blogPost)
    {
        $this->authorize('view', $blogPost);
        return response()->json($blogPost->load('author'));
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $this->authorize('update', $blogPost);

        $validatedData = $request->validate([
            'title'      => 'sometimes|string|max:255',
            'body'       => 'sometimes|nullable|string',
            'visibility' => 'sometimes|in:public,private,draft',
            'tags'       => 'sometimes|nullable|array',
            'status'     => 'sometimes|in:draft,published',
        ]);

        if (isset($validatedData['status']) && !isset($validatedData['visibility'])) {
            $validatedData['visibility'] = $validatedData['status'] === 'draft' ? 'draft' : 'public';
        }

        if (isset($validatedData['visibility']) && !isset($validatedData['status'])) {
            $validatedData['status'] = $validatedData['visibility'] === 'draft' ? 'draft' : 'published';
        }

        // Add history for modification
        $history = $blogPost->modification_history ?? [];
        $history[] = [
            'type' => 'user_edit',
            'timestamp' => now()->toIso8601String(),
        ];
        $validatedData['modification_history'] = $history;

        $blogPost->update($validatedData);

        return response()->json([
            'message' => 'Blog post updated successfully',
            'post'    => $blogPost,
        ]);
    }

    public function destroy(BlogPost $blogPost)
    {
        $this->authorize('delete', $blogPost);
        $blogPost->delete();
        return response()->json(['message' => 'Blog post deleted successfully'], 204);
    }
}
