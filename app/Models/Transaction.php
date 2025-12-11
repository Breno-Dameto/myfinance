<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'category_id', 'temp_category', 'amount', 'description', 'date', 'type'];
    protected $casts = ['date' => 'date'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    // Acessor inteligente: Retorna o nome da categoria ou o nome temporário
    public function getCategoryNameAttribute()
    {
        return $this->category ? $this->category->name : ($this->temp_category ?? 'Outros');
    }
}