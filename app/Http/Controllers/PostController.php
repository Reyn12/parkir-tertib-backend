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

        // Sort based on tab selection
        if ($request->has('tab')) {
            if ($request->tab === 'popular') {
                // Sort by likes count + comments count (popularity)
                $query->orderByRaw('(likes_count + comments_count) DESC')
                      ->orderBy('created_at', 'desc');
            } else {
                // Default: sort by latest
                $query->orderBy('created_at', 'desc');
            }
        } else {
            // Default: sort by latest
            $query->orderBy('created_at', 'desc');
        }

        $posts = $query->paginate(10);

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

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location_name' => 'required|string|max:255',
            'photo_url' => 'required|string|max:500',
            'category_id' => 'required|exists:categories,category_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        $post = Post::create([
            'user_id' => $user->user_id,
            'title' => $request->title,
            'description' => $request->description,
            'location_name' => $request->location_name,
            'photo_url' => $request->photo_url,
            'category_id' => $request->category_id,
            'status' => 'pending', // Default status pending
            'likes_count' => 0,
            'comments_count' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => [
                'post' => [
                    'post_id' => $post->post_id,
                    'title' => $post->title,
                    'description' => $post->description,
                    'location_name' => $post->location_name,
                    'photo_url' => $post->photo_url,
                    'status' => $post->status,
                    'category_id' => $post->category_id,
                    'created_at' => $post->created_at,
                ]
            ]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        $user = $request->user();

        // Cek apakah user yang punya post
        if ($post->user_id !== $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to edit this post'
            ], 403);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'location_name' => 'sometimes|required|string|max:255',
            'photo_url' => 'sometimes|required|string|max:500',
            'category_id' => 'sometimes|required|exists:categories,category_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $post->update($request->only([
            'title', 'description', 'location_name', 'photo_url', 'category_id'
        ]));

        // Set status jadi pending lagi setelah edit dan clear rejection reason
        $post->update([
            'status' => 'pending',
            'rejection_reason' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => [
                'post' => [
                    'post_id' => $post->post_id,
                    'title' => $post->title,
                    'description' => $post->description,
                    'location_name' => $post->location_name,
                    'photo_url' => $post->photo_url,
                    'status' => $post->status,
                    'category_id' => $post->category_id,
                    'updated_at' => $post->updated_at,
                ]
            ]
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        $user = $request->user();

        // Cek apakah user yang punya post
        if ($post->user_id !== $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this post'
            ], 403);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ]);
    }
}
