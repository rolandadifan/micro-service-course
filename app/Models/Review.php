<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    protected $fillable = [
        'course_id', 'user_id', 'rating', 'note'
    ];

    public function course()
    {
        return $this->belongsTo('App\Models\Course');
    }
}
