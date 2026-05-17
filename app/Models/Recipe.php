<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'title', 'description',
        'image', 'video_url', 'cooking_time', 'servings', 'difficulty'
    ];

    // Relasi ke user (yang buat resep)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke bahan-bahan
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

    // Relasi ke langkah-langkah
    public function steps()
    {
        return $this->hasMany(Step::class)->orderBy('step_number');
    }

    // Relasi ke favorit
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // Relasi ke rating
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Average rating (computed)
    public function getAverageRatingAttribute()
    {
        return round($this->ratings()->avg('rating'), 1) ?? 0;
    }

    // Total favorit
    public function getFavoritesCountAttribute()
    {
        return $this->favorites()->count();
    }

    // Accessor untuk gambar agar menggunakan route proxy API jika diload dari emulator/HP
    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        // Jika URL-nya mengandung '/storage/recipes/', ubah menjadi '/api/recipes/image/'
        if (str_contains($value, '/storage/recipes/')) {
            return str_replace('/storage/recipes/', '/api/recipes/image/', $value);
        }

        return $value;
    }
}
