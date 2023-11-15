<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'body',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voices()
    {
        return $this->hasMany(Voice::class);
    }

    public function votes(): Attribute
    {
        return Attribute::make(
            get: function () {
                $upvotes = $this->voices->where('value', true)->count();
                $downvotes = $this->voices->where('value', false)->count();

                $totalVotes = $upvotes - $downvotes;

                return $totalVotes;
            }
        );
    }

    protected $appends = ['votes'];
}
