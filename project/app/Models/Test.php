<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'creator_id',
    ];

    public function questions()
    {
        return $this->hasMany(\App\Models\Question::class, 'test_id');
    }
}
