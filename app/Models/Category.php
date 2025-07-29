<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'category_name',
        'description',
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'category_id';

    public $timestamps = true;

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id');
    }
}
