<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;

class LikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $posts = Post::all();

        $likes = [
            // Likes untuk "Mobil Parkir Sembarangan di Depan Toko"
            [
                'post_id' => $posts->where('title', 'Mobil Parkir Sembarangan di Depan Toko')->first()->post_id,
                'user_id' => $users->where('username', 'budi')->first()->user_id,
            ],
            [
                'post_id' => $posts->where('title', 'Mobil Parkir Sembarangan di Depan Toko')->first()->post_id,
                'user_id' => $users->where('username', 'sari')->first()->user_id,
            ],
            [
                'post_id' => $posts->where('title', 'Mobil Parkir Sembarangan di Depan Toko')->first()->post_id,
                'user_id' => $users->where('username', 'reynald')->first()->user_id,
            ],

            // Likes untuk "Motor Parkir di Trotoar"
            [
                'post_id' => $posts->where('title', 'Motor Parkir di Trotoar')->first()->post_id,
                'user_id' => $users->where('username', 'reynald')->first()->user_id,
            ],
            [
                'post_id' => $posts->where('title', 'Motor Parkir di Trotoar')->first()->post_id,
                'user_id' => $users->where('username', 'budi')->first()->user_id,
            ],

            // Likes untuk "Parkir Ganda di Mall"
            [
                'post_id' => $posts->where('title', 'Parkir Ganda di Mall')->first()->post_id,
                'user_id' => $users->where('username', 'reynald')->first()->user_id,
            ],
            [
                'post_id' => $posts->where('title', 'Parkir Ganda di Mall')->first()->post_id,
                'user_id' => $users->where('username', 'budi')->first()->user_id,
            ],
            [
                'post_id' => $posts->where('title', 'Parkir Ganda di Mall')->first()->post_id,
                'user_id' => $users->where('username', 'sari')->first()->user_id,
            ],

            // Likes untuk "Parkir di Jalur Darurat"
            [
                'post_id' => $posts->where('title', 'Parkir di Jalur Darurat')->first()->post_id,
                'user_id' => $users->where('username', 'reynald')->first()->user_id,
            ],
            [
                'post_id' => $posts->where('title', 'Parkir di Jalur Darurat')->first()->post_id,
                'user_id' => $users->where('username', 'budi')->first()->user_id,
            ],
            [
                'post_id' => $posts->where('title', 'Parkir di Jalur Darurat')->first()->post_id,
                'user_id' => $users->where('username', 'sari')->first()->user_id,
            ],
        ];

        foreach ($likes as $like) {
            Like::create($like);
        }
    }
}
