<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class AdminBlogPostController extends Controller
{
    public function index(Request $request)
    {
        // Admins can see all blog posts, support filtering
        $query = BlogPost::with('author');
        
        if ($request->has('moderation_status')) {
            $query->where('moderation_status', '=', $request->query('moderation_status'));
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function moderate(Request $request, BlogPost $blogPost)
    {
        $validated = $request->validate([
            'moderation_status' => 'sometimes|in:approved,pending,rejected',
            'title' => 'sometimes|string|max:255',
            'body' => 'sometimes|nullable|string',
            'visibility' => 'sometimes|in:public,private,draft',
            'tags' => 'sometimes|nullable|array',
            'reason' => 'nullable|string'
        ]);

        $history = $blogPost->modification_history ?? [];
        $history[] = [
            'type' => 'admin_moderation',
            'timestamp' => now()->toIso8601String(),
            'admin_id' => $request->user()->id,
            'reason' => $validated['reason'] ?? null,
            'changes' => array_keys($validated)
        ];

        unset($validated['reason']); // Don't save reason to columns
        $validated['modification_history'] = $history;
        
        $blogPost->update($validated);

        return response()->json([
            'message' => 'Blog post moderated and updated successfully',
            'post' => $blogPost
        ]);
    }

    public function destroy(BlogPost $blogPost)
    {
        $blogPost->delete();
        return response()->json(['message' => 'Blog post deleted contextually by Admin'], 204);
    }
}
