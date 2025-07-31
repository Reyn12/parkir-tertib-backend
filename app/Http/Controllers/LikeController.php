<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Post;

class LikeController extends Controller
{
    public function toggle(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,post_id',
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

        // Cek apakah user sudah like post ini
        $existingLike = Like::where('post_id', $request->post_id)
                           ->where('user_id', $user->user_id)
                           ->first();

        if ($existingLike) {
            // Jika sudah like, hapus like (unlike)
            $existingLike->delete();
            $post->decrement('likes_count');

            return response()->json([
                'success' => true,
                'message' => 'Post unliked successfully',
                'data' => [
                    'is_liked' => false,
                    'likes_count' => $post->fresh()->likes_count
                ]
            ]);
        } else {
            // Jika belum like, tambah like
            Like::create([
                'post_id' => $request->post_id,
                'user_id' => $user->user_id,
            ]);

            $post->increment('likes_count');

            return response()->json([
                'success' => true,
                'message' => 'Post liked successfully',
                'data' => [
                    'is_liked' => true,
                    'likes_count' => $post->fresh()->likes_count
                ]
            ]);
        }
    }

    public function checkLike(Request $request, $post_id)
    {
        $user = $request->user();

        // Cek apakah post ada
        $post = Post::find($post_id);
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        // Cek apakah user sudah like post ini
        $isLiked = Like::where('post_id', $post_id)
                      ->where('user_id', $user->user_id)
                      ->exists();

        return response()->json([
            'success' => true,
            'message' => 'Like status retrieved successfully',
            'data' => [
                'is_liked' => $isLiked,
                'likes_count' => $post->likes_count
            ]
        ]);
    }

    public function getLikesByPost($post_id)
    {
        $post = Post::find($post_id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        $likes = Like::with('user')
            ->where('post_id', $post_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Likes retrieved successfully',
            'data' => [
                'likes' => $likes->items(),
                'pagination' => [
                    'current_page' => $likes->currentPage(),
                    'last_page' => $likes->lastPage(),
                    'per_page' => $likes->perPage(),
                    'total' => $likes->total(),
                ]
            ]
        ]);
    }
} 