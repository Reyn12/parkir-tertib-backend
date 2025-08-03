<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();

        $posts = [
            [
                'user_id' => $users->where('username', 'reynald')->first()->user_id,
                'title' => 'Mobil Parkir Sembarangan di Depan Toko',
                'description' => 'Mobil ini parkir sembarangan di depan toko, menghalangi akses masuk. Plakat B 1234 ABC',
                'category_id' => $categories->where('category_name', 'Parkir Sembarangan')->first()->category_id,
                'location_name' => 'Jl. Sudirman No. 45, Jakarta Pusat',
                'photo_url' => 'https://ik.imagekit.io/your_imagekit_id/Parkir%20Tertib/Postingan/sample1.jpg',
                'status' => 'approved',
                'likes_count' => 15,
                'comments_count' => 3,
                'hide_identity' => false,
                'privacy_type' => 'public',
            ],
            [
                'user_id' => $users->where('username', 'budi')->first()->user_id,
                'title' => 'Motor Parkir di Trotoar',
                'description' => 'Motor ini parkir di trotoar, mengganggu pejalan kaki. Plakat AB 5678 CD',
                'category_id' => $categories->where('category_name', 'Parkir di Trotoar')->first()->category_id,
                'location_name' => 'Jl. Thamrin No. 12, Jakarta Pusat',
                'photo_url' => 'https://ik.imagekit.io/your_imagekit_id/Parkir%20Tertib/Postingan/sample2.jpg',
                'status' => 'approved',
                'likes_count' => 8,
                'comments_count' => 1,
                'hide_identity' => true,
                'privacy_type' => 'public',
            ],
            [
                'user_id' => $users->where('username', 'sari')->first()->user_id,
                'title' => 'Parkir Ganda di Mall',
                'description' => 'Mobil ini parkir ganda di area parkir mall, menghalangi kendaraan lain. Plakat B 9999 XYZ',
                'category_id' => $categories->where('category_name', 'Parkir Ganda')->first()->category_id,
                'location_name' => 'Mall Taman Anggrek, Jakarta Barat',
                'photo_url' => 'https://ik.imagekit.io/your_imagekit_id/Parkir%20Tertib/Postingan/sample3.jpg',
                'status' => 'pending',
                'likes_count' => 5,
                'comments_count' => 2,
                'hide_identity' => false,
                'privacy_type' => 'public',
            ],
            [
                'user_id' => $users->where('username', 'reynald')->first()->user_id,
                'title' => 'Parkir di Zona Larangan',
                'description' => 'Mobil parkir di zona larangan parkir. Plakat B 1111 AAA',
                'category_id' => $categories->where('category_name', 'Parkir di Zona Larangan')->first()->category_id,
                'location_name' => 'Jl. Gatot Subroto No. 78, Jakarta Selatan',
                'photo_url' => 'https://ik.imagekit.io/your_imagekit_id/Parkir%20Tertib/Postingan/sample4.jpg',
                'status' => 'rejected',
                'rejection_reason' => 'Foto tidak jelas, tidak terlihat pelanggaran',
                'likes_count' => 0,
                'comments_count' => 0,
                'hide_identity' => true,
                'privacy_type' => 'public',
            ],
            [
                'user_id' => $users->where('username', 'budi')->first()->user_id,
                'title' => 'Parkir di Jalur Darurat',
                'description' => 'Mobil parkir di depan fire hydrant. Plakat B 2222 BBB',
                'category_id' => $categories->where('category_name', 'Parkir di Jalur Darurat')->first()->category_id,
                'location_name' => 'Apartemen Green Bay Pluit, Jakarta Utara',
                'photo_url' => 'https://ik.imagekit.io/your_imagekit_id/Parkir%20Tertib/Postingan/sample5.jpg',
                'status' => 'approved',
                'likes_count' => 25,
                'comments_count' => 7,
                'hide_identity' => false,
                'privacy_type' => 'public',
            ],
        ];

        foreach ($posts as $post) {
            Post::create($post);
        }
    }
}
