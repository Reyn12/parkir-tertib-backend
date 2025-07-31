<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,post_id',
            'comment_text' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Cek apakah post ada
        $post = Post::find($request->post_id);
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        $comment = Comment::create([
            'post_id' => $request->post_id,
            'user_id' => $user->user_id,
            'comment_text' => $request->comment_text,
        ]);

        // Update comments_count di post
        $post->increment('comments_count');

        // Load user data buat response
        $comment->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'data' => [
                'comment' => [
                    'comment_id' => $comment->comment_id,
                    'comment_text' => $comment->comment_text,
                    'created_at' => $comment->created_at,
                    'user' => [
                        'user_id' => $comment->user->user_id,
                        'username' => $comment->user->username,
                    ]
                ]
            ]
        ], 201);
    }

    public function destroy(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found'
            ], 404);
        }

        $user = $request->user();

        // Cek apakah user yang punya komentar
        if ($comment->user_id !== $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this comment'
            ], 403);
        }

        // Update comments_count di post
        $post = Post::find($comment->post_id);
        if ($post) {
            $post->decrement('comments_count');
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }

    public function getByPost($post_id)
    {
        $post = Post::find($post_id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        $comments = Comment::with('user')
            ->where('post_id', $post_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Comments retrieved successfully',
            'data' => [
                'comments' => $comments->items(),
                'pagination' => [
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                ]
            ]
        ]);
    }
} 