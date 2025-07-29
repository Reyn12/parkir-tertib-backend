<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $posts = Post::all();

        $comments = [
            [
                'post_id' => $posts->where('title', 'Mobil Parkir Sembarangan di Depan Toko')->first()->post_id,
                'user_id' => $users->where('username', 'budi')->first()->user_id,
                'comment_text' => 'Wah parah banget ini, parkir sembarangan gitu. Harusnya ada tilang!',
            ],
            [
                'post_id' => $posts->where('title', 'Mobil Parkir Sembarangan di Depan Toko')->first()->post_id,
                'user_id' => $users->where('username', 'sari')->first()->user_id,
                'comment_text' => 'Plakat B 1234 ABC ya? Aku juga sering liat mobil ini parkir sembarangan.',
            ],
            [
                'post_id' => $posts->where('title', 'Mobil Parkir Sembarangan di Depan Toko')->first()->post_id,
                'user_id' => $users->where('username', 'reynald')->first()->user_id,
                'comment_text' => 'Makasih udah share! Ini beneran mengganggu akses masuk toko.',
            ],
            [
                'post_id' => $posts->where('title', 'Motor Parkir di Trotoar')->first()->post_id,
                'user_id' => $users->where('username', 'reynald')->first()->user_id,
                'comment_text' => 'Motor ini sering banget parkir di trotoar, bikin pejalan kaki susah lewat.',
            ],
            [
                'post_id' => $posts->where('title', 'Parkir Ganda di Mall')->first()->post_id,
                'user_id' => $users->where('username', 'reynald')->first()->user_id,
                'comment_text' => 'Di mall gini masih aja parkir ganda, egois banget.',
            ],
            [
                'post_id' => $posts->where('title', 'Parkir Ganda di Mall')->first()->post_id,
                'user_id' => $users->where('username', 'budi')->first()->user_id,
                'comment_text' => 'Plakat B 9999 XYZ, harusnya ditilang biar kapok.',
            ],
            [
                'post_id' => $posts->where('title', 'Parkir di Jalur Darurat')->first()->post_id,
                'user_id' => $users->where('username', 'sari')->first()->user_id,
                'comment_text' => 'Ini yang paling parah! Parkir di depan fire hydrant bisa bahaya kalau ada kebakaran.',
            ],
            [
                'post_id' => $posts->where('title', 'Parkir di Jalur Darurat')->first()->post_id,
                'user_id' => $users->where('username', 'reynald')->first()->user_id,
                'comment_text' => 'Bener banget! Ini harus segera ditindak tegas.',
            ],
            [
                'post_id' => $posts->where('title', 'Parkir di Jalur Darurat')->first()->post_id,
                'user_id' => $users->where('username', 'budi')->first()->user_id,
                'comment_text' => 'Plakat B 2222 BBB, semoga cepat ditilang!',
            ],
            [
                'post_id' => $posts->where('title', 'Parkir di Jalur Darurat')->first()->post_id,
                'user_id' => $users->where('username', 'sari')->first()->user_id,
                'comment_text' => 'Aku udah lapor ke petugas mall, semoga cepat ditindak.',
            ],
            [
                'post_id' => $posts->where('title', 'Parkir di Jalur Darurat')->first()->post_id,
                'user_id' => $users->where('username', 'reynald')->first()->user_id,
                'comment_text' => 'Bagus! Kita harus kerja sama buat bikin parkir tertib.',
            ],
            [
                'post_id' => $posts->where('title', 'Parkir di Jalur Darurat')->first()->post_id,
                'user_id' => $users->where('username', 'budi')->first()->user_id,
                'comment_text' => 'Setuju! Aplikasi ini beneran membantu buat bikin Jakarta lebih tertib.',
            ],
        ];

        foreach ($comments as $comment) {
            Comment::create($comment);
        }
    }
}
