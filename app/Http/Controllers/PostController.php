<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by title or description
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Sort by latest first
        $posts = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Posts retrieved successfully',
            'data' => [
                'posts' => $posts->items(),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            ]
        ]);
    }

    public function show($id)
    {
        $post = Post::with(['user', 'category', 'comments.user', 'likes.user'])->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Post retrieved successfully',
            'data' => [
                'post' => [
                    'post_id' => $post->post_id,
                    'title' => $post->title,
                    'description' => $post->description,
                    'location_name' => $post->location_name,
                    'photo_url' => $post->photo_url,
                    'status' => $post->status,
                    'rejection_reason' => $post->rejection_reason,
                    'likes_count' => $post->likes_count,
                    'comments_count' => $post->comments_count,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'user' => [
                        'user_id' => $post->user->user_id,
                        'username' => $post->user->username,
                        'profile_picture' => $post->user->profile_picture,
                    ],
                    'category' => [
                        'category_id' => $post->category->category_id,
                        'category_name' => $post->category->category_name,
                    ],
                    'comments' => $post->comments->map(function($comment) {
                        return [
                            'comment_id' => $comment->comment_id,
                            'comment_text' => $comment->comment_text,
                            'created_at' => $comment->created_at,
                            'user' => [
                                'user_id' => $comment->user->user_id,
                                'username' => $comment->user->username,
                            ]
                        ];
                    }),
                    'likes' => $post->likes->map(function($like) {
                        return [
                            'like_id' => $like->like_id,
                            'created_at' => $like->created_at,
                            'user' => [
                                'user_id' => $like->user->user_id,
                                'username' => $like->user->username,
                            ]
                        ];
                    })
                ]
            ]
        ]);
    }

    public function getByUser($user_id)
    {
        $posts = Post::with(['user', 'category'])
            ->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'User posts retrieved successfully',
            'data' => [
                'posts' => $posts->items(),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            ]
        ]);
    }

    public function getByCategory($category_id)
    {
        $posts = Post::with(['user', 'category'])
            ->where('category_id', $category_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Category posts retrieved successfully',
            'data' => [
                'posts' => $posts->items(),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            ]
        ]);
    }
}
